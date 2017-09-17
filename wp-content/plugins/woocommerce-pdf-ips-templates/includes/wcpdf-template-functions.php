<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WooCommerce_PDF_IPS_Templates_Functions' ) ) {

	class WooCommerce_PDF_IPS_Templates_Functions {
		public function __construct() {
			// hook custom blocks to template actions
			add_action( 'wpo_wcpdf_after_document_label', array( $this, 'custom_blocks_data' ), 10, 2 );
			add_action( 'wpo_wcpdf_before_order_data', array( $this, 'custom_blocks_data' ), 10, 2 );
			add_action( 'wpo_wcpdf_after_order_data', array( $this, 'custom_blocks_data' ), 10, 2 );
			add_action( 'wpo_wcpdf_before_customer_notes', array( $this, 'custom_blocks_data' ), 10, 2 );
			add_action( 'wpo_wcpdf_after_customer_notes', array( $this, 'custom_blocks_data' ), 10, 2 );
			add_action( 'wpo_wcpdf_before_order_details', array( $this, 'custom_blocks_data' ), 10, 2 );
			add_action( 'wpo_wcpdf_after_order_details', array( $this, 'custom_blocks_data' ), 10, 2 );

			// make replacements in template settings fields
			add_action( 'wpo_wcpdf_footer', array( $this, 'settings_fields_replacements' ), 999 );
			add_action( 'wpo_wcpdf_extra_1', array( $this, 'settings_fields_replacements' ), 999 );
			add_action( 'wpo_wcpdf_extra_2', array( $this, 'settings_fields_replacements' ), 999 );
			add_action( 'wpo_wcpdf_extra_3', array( $this, 'settings_fields_replacements' ), 999 );
			add_action( 'wpo_wcpdf_shop_name', array( $this, 'settings_fields_replacements' ), 999 );
			add_action( 'wpo_wcpdf_shop_address', array( $this, 'settings_fields_replacements' ), 999 );

			// store regular price in item meta
			add_action( 'woocommerce_checkout_order_processed', array( $this, 'save_regular_item_price' ), 10, 2 );
			add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'hide_regular_price_itemmeta' ) );
		}

		public function get_totals_table_data ($total_settings) {
			global $wpo_wcpdf;

			$totals_table_data = array();
			foreach ($total_settings as $total_key => $total_setting) {
				// reset possibly absent vars
				$method = $percent = $base = $show_unit = $only_discounted = NULL;
				// extract vars
				extract($total_setting);

				// remove label if empty!
				if( empty($total_setting['label']) ) {
					unset($total_setting['label']);
				} elseif ( !in_array( $type, array( 'fees', 'vat' ) ) ) {
					$total_setting['label'] = __( $total_setting['label'], 'wpo_wcpdf' ); // not proper gettext, but it makes it possible to reuse po translations!
				}

				switch ($type) {
					case 'subtotal':
						// $tax, $discount, $only_discounted
						$order_discount = $wpo_wcpdf->get_order_discount( 'total', 'incl' );
						if ( !$order_discount && isset($only_discounted) ) {
							continue;
						}
						switch ($discount) {
							case 'before':

								$totals_table_data[$total_key] = (array) $total_setting + $wpo_wcpdf->get_order_subtotal( $tax );
								break;

							case 'after':
								$subtotal_value = 0;
								$items = $wpo_wcpdf->export->order->get_items();
								if( sizeof( $items ) > 0 ) {
									foreach( $items as $item ) {
										$subtotal_value += $item['line_total'];
										if ( $tax == 'incl' ) {
											$subtotal_value += $item['line_tax'];
										}
									}
								}
								$subtotal_data = array(
									'label'	=> __('Subtotal', 'wpo_wcpdf'),
									'value'	=> $wpo_wcpdf->export->wc_price( $subtotal_value ),
								);
								$totals_table_data[$total_key] = (array) $total_setting + $subtotal_data;
								break;
						}
						break;
					case 'discount':
						// $tax, $show_codes, $show_percentage
						if ( $discount = $wpo_wcpdf->get_order_discount( 'total', $tax ) ) {
							if (isset($discount['raw_value'])) {
								// support for positive discount (=more expensive/price corrections)
								$discount['value'] = $wpo_wcpdf->export->wc_price( $discount['raw_value'] * -1 );
							} else {
								$discount['value'] = '-'.$discount['value'];
							}

							$discount_percentage = $this->get_discount_percentage( $wpo_wcpdf->export->order );
							if (isset($show_percentage) && $discount_percentage) {
								$discount['label'] = "{$discount['label']} ({$discount_percentage}%)";
							}

							$used_coupons = implode(', ', $wpo_wcpdf->export->order->get_used_coupons() );
							if (isset($show_codes) && !empty($used_coupons)) {
								$discount['label'] = "{$discount['label']} ({$used_coupons})";
							}

							$totals_table_data[$total_key] = (array) $total_setting + $discount;
						}
						break;
					case 'shipping':
						// $tax, $method, $hide_free
						$shipping_cost = $wpo_wcpdf->export->order->order_shipping;
						if ( !(round( $shipping_cost, 3 ) == 0 && isset($hide_free)) ) {
							$totals_table_data[$total_key] = (array) $total_setting + $wpo_wcpdf->get_order_shipping( $tax );
							if (!empty($method)) {
								$totals_table_data[$total_key]['value'] = $wpo_wcpdf->export->order->get_shipping_method();
							}
						}
						break;
					case 'fees':
						// $tax
						if ( $fees = $wpo_wcpdf->get_order_fees( $tax ) ) {

							// WooCommerce Checkout Add-Ons compatibility
							if ( function_exists('wc_checkout_add_ons') && is_object(wc_checkout_add_ons()->frontend) && method_exists(wc_checkout_add_ons()->frontend, 'append_order_add_on_fee_meta') ) {
								// we're adding a 'fee_' prefix because that's what woocommerce does in its
								// order total keys and wc_checkout_add_ons uses this to determine the total type (fee)
								$fees = $this->array_keys_prefix($fees, 'fee_', 'add');
								$fees = wc_checkout_add_ons()->frontend->append_order_add_on_fee_meta( $fees, $wpo_wcpdf->export->order );
								$fees = $this->array_keys_prefix($fees, 'fee_', 'remove');
							}

							reset($fees);
							$first = key($fees);
							end($fees);
							$last = key($fees);
							
							foreach( $fees as $fee_key => $fee ) {
								$class = 'fee-line';
								if ($fee_key == $first) $class .= ' first';
								if ($fee_key == $last) $class .= ' last';

								$totals_table_data[$total_key.$fee_key] = (array) $total_setting + $fee;
								$totals_table_data[$total_key.$fee_key]['class'] = $class;
							}
						}
						break;
					case 'vat':
						// $percent, $base
						if ($taxes = $wpo_wcpdf->get_order_taxes()){
							$taxes = $this->add_tax_base( $taxes, $wpo_wcpdf->export->order );

							reset($taxes);
							$first = key($taxes);
							end($taxes);
							$last = key($taxes);
							
							foreach( $taxes as $tax_key => $tax ) {
								$class = 'tax-line';
								if ($tax_key == $first) $class .= ' first';
								if ($tax_key == $last) $class .= ' last';

								// prepare label format based on settings
								$label_format = '{{label}}';
								if (isset($percent)) $label_format .= ' {{rate}}';
								if (isset($base) && !empty($tax['base'])) $label_format .= ' ({{base}})';
								$label_format = apply_filters( 'wpo_wcpdf_templates_tax_total_label_format', $label_format );

								// prevent errors if base not set
								if ( empty( $tax['base'] ) ) $tax['base'] = 0;

								$tax['label'] = str_replace( array( '{{label}}', '{{rate}}', '{{base}}' ) , array( $tax['label'], $tax['rate'], $wpo_wcpdf->export->wc_price( $tax['base'] ) ), $label_format );

								$totals_table_data[$total_key.$tax_key] = (array) $total_setting + $tax;
								$totals_table_data[$total_key.$tax_key]['class'] = $class;
							}
						}
						break;
					case 'vat_base':
						// $percent
						if ($taxes = $wpo_wcpdf->get_order_taxes()){
							$taxes = $this->add_tax_base( $taxes, $wpo_wcpdf->export->order );

							reset($taxes);
							$first = key($taxes);
							end($taxes);
							$last = key($taxes);

							if (empty($total_setting['label'])) {
								$total_setting['label'] = $label = __( 'Total ex. VAT', 'wpo_wcpdf' );
							}

							foreach( $taxes as $tax_key => $tax ) {
								// prevent errors if base not set
								if ( empty( $tax['base'] ) ) continue;

								$class = 'tax-base-line';
								if ($tax_key == $first) $class .= ' first';
								if ($tax_key == $last) $class .= ' last';

								// prepare label format based on settings
								$label_format = '{{label}}';
								if (isset($percent)) $label_format .= ' ({{rate}})';
								$label_format = apply_filters( 'wpo_wcpdf_templates_tax_base_total_label_format', $label_format );

								$tax['value'] = $wpo_wcpdf->export->wc_price( $tax['base'] );

								$total_setting['label'] = str_replace( array( '{{label}}', '{{rate}}' ) , array( $label, $tax['rate'] ), $label_format );

								$totals_table_data[$total_key.$tax_key] = (array) $total_setting + $tax;
								$totals_table_data[$total_key.$tax_key]['class'] = $class;
							}
						}
						break;
					case 'total':
						// $tax
						$totals_table_data[$total_key] = (array) $total_setting + $wpo_wcpdf->get_order_grand_total( $tax);
						if ( $tax == 'incl') {
							$totals_table_data[$total_key]['class'] = 'total grand-total';
						}
						break;
					case 'order_weight':
						// $show_unit
						$items = $wpo_wcpdf->export->order->get_items();
						$weight = 0;
						if( sizeof( $items ) > 0 ) {
							foreach( $items as $item ) {
								$product = $wpo_wcpdf->export->order->get_product_from_item( $item );
								if ( $product ) {
									$weight += $product->get_weight() * $item['qty'];
								}
							}
						}
						if ( isset($show_unit) && $weight > 0 ) {
							$weight .= get_option('woocommerce_weight_unit');
						}

						$order_weight = array (
							'label'	=> __( 'Total weight', 'wpo_wcpdf_templates' ),
							'value'	=> $weight,
						);

						$totals_table_data[$total_key] = (array) $total_setting + $order_weight;
						break;
					case 'total_qty':
						$items = $wpo_wcpdf->export->order->get_items();
						$total_qty = 0;
						if( sizeof( $items ) > 0 ) {
							foreach( $items as $item ) {
								$total_qty += $item['qty'];
							}
						}

						$total_qty_total = array (
							'label'	=> __( 'Total quantity', 'wpo_wcpdf_templates' ),
							'value'	=> $total_qty,
						);

						$totals_table_data[$total_key] = (array) $total_setting + $total_qty_total;
						break;
					default:
						break;
				}

				// set class if not set. note that fees and taxes have modified keys!
				if (isset($totals_table_data[$total_key]) && !isset($totals_table_data[$total_key]['class'])) {
					$totals_table_data[$total_key]['class'] = $type;
				}
			}

			return $totals_table_data;
		}


		public function get_order_details_header ($column_setting) {
			extract($column_setting);

			if (!empty($label)) {
				$header['title'] = __( $label, 'wpo_wcpdf' ); // not proper gettext, but it makes it possible to reuse po translations!
			} else {
				switch ($type) {
					case 'position':
						$header['title'] = '';
						break;
					case 'sku':
						$header['title'] = __( 'SKU', 'wpo_wcpdf' );
						break;
					case 'thumbnail':
						$header['title'] = '';
						break;
					case 'description':
						$header['title'] = __( 'Product', 'wpo_wcpdf' );
						break;
					case 'quantity':
						$header['title'] = __( 'Quantity', 'wpo_wcpdf' );
						break;
					case 'price':
						switch ($price_type) {
							case 'single':
								$header['title'] = __( 'Price', 'wpo_wcpdf' );
								$header['class'] = 'price';
								break;
							case 'total':
								$header['title'] = __( 'Total', 'wpo_wcpdf' );
								$header['class'] = 'total';
								break;
						}
						break;
					case 'regular_price':
						$header['title'] = __( 'Regular price', 'wpo_wcpdf_templates' );
						break;
					case 'discount':
						$header['title'] = __( 'Discount', 'wpo_wcpdf' );
						break;
					case 'vat':
						$header['title'] = __( 'VAT', 'wpo_wcpdf' );
						break;
					case 'tax_rate':
						$header['title'] = __( 'Tax rate', 'wpo_wcpdf' );
						break;
					case 'weight':
						$header['title'] = __( 'Weight', 'wpo_wcpdf' );
						break;
					case 'product_attribute':
						$header['title'] = '';
						break;
					case 'product_custom':
						$header['title'] = '';
						break;
					case 'product_description':
						$header['title'] = __( 'Product description', 'wpo_wcpdf_templates' );
						break;
					case 'product_categories':
						$header['title'] = __( 'Categories', 'wpo_wcpdf_templates' );
						break;
					case 'all_meta':
						$header['title'] = __( 'Variation', 'wpo_wcpdf_templates' );
						break;
					case 'item_meta':
						$header['title'] = $meta_key;
						break;
					case 'cb':
						$header['title'] = '';
						break;
					case 'static_text':
						$header['title'] = '';
						break;
					default:
						$header['title'] = $type;
						break;
				}
			}


			// set class if not set;
			if (!isset($header['class'])) {
				$header['class'] = $type;
			}

			// mark first and last column
			if (isset($position)) {
				$header['class'] .= " {$position}-column";
			}

			return $header;
		}

		public function get_order_details_data ($column_setting, $item) {
			global $wpo_wcpdf;
			extract($column_setting);

			switch ($type) {
				case 'position':
					$column['data'] = $line_number;
					break;
				case 'sku':
					$column['data'] = isset($item['sku']) ? $item['sku'] : '';
					break;
				case 'thumbnail':
					$column['data'] = isset($item['thumbnail']) ? $item['thumbnail'] : '';
					break;
				case 'description':
					// $show_sku, $show_weight
					ob_start();
					?>
					<span class="item-name"><?php echo $item['name']; ?></span>
					<?php do_action( 'wpo_wcpdf_before_item_meta', $wpo_wcpdf->export->template_type, $item, $wpo_wcpdf->export->order  ); ?>
					<?php if ( isset($show_meta) ) : ?>
					<span class="item-meta"><?php echo $item['meta']; ?></span>
					<?php endif; ?>
					<?php if ( isset($show_sku) || isset($show_weight) ) : ?>
					<dl class="meta">
						<?php $description_label = __( 'SKU', 'wpo_wcpdf' ); // registering alternate label translation ?>
						<?php if( !empty( $item['sku'] ) && isset($show_sku) ) : ?><dt class="sku"><?php _e( 'SKU:', 'wpo_wcpdf' ); ?></dt><dd class="sku"><?php echo $item['sku']; ?></dd><?php endif; ?>
						<?php if( !empty( $item['weight'] ) && isset($show_weight) ) : ?><dt class="weight"><?php _e( 'Weight:', 'wpo_wcpdf' ); ?></dt><dd class="weight"><?php echo $item['weight']; ?><?php echo get_option('woocommerce_weight_unit'); ?></dd><?php endif; ?>
					</dl>
					<?php endif; ?>
					<?php do_action( 'wpo_wcpdf_after_item_meta', $wpo_wcpdf->export->template_type, $item, $wpo_wcpdf->export->order  ); ?>
					<?php
					$column['data'] = ob_get_clean();
					break;
				case 'quantity':
					$column['data'] = $item['quantity'];
					break;
				case 'price':
					// $price_type, $tax, $discount
					// using a combined value to make this more readable...
					$price_type_full = "{$price_type}_{$tax}_{$discount}";
					switch ($price_type_full) {
						// before discount
						case 'single_incl_before':
							$column['data'] = $item['single_price'];
							break;
						case 'single_excl_before':
							$column['data'] = $item['ex_single_price'];
							break;
						case 'total_incl_before':
							$column['data'] = $item['price'];
							break;
						case 'total_excl_before':
							$column['data'] = $item['ex_price'];
							break;

						// after discount
						case 'single_incl_after':
							$price = ( $item['item']['line_total'] + $item['item']['line_tax'] ) / max( 1, $item['quantity'] );
							$column['data'] = $wpo_wcpdf->export->wc_price( $price );
							break;
						case 'single_excl_after':
							$column['data'] = $item['single_line_total'];
							break;
						case 'total_incl_after':
							$price = $item['item']['line_total'] + $item['item']['line_tax'];
							$column['data'] = $wpo_wcpdf->export->wc_price( $price );
							break;
						case 'total_excl_after':
							$column['data'] = $item['line_total'];
							break;
					}

					if ($price_type == 'total') {
						$column['class'] = 'total';
					}
					break;
				case 'regular_price':
					// $price_type, $tax, $only_sale
					$regular_prices = $this->get_regular_item_price( $item['item'], $item['item_id'], $wpo_wcpdf->export->order );

					// check if item price is different from sale price
					$single_item_price = ( $item['item']['line_subtotal'] + $item['item']['line_subtotal_tax'] ) / max( 1, $item['quantity'] );
					if ( isset($only_sale) && round( $single_item_price, 2 ) == round( $regular_prices['incl'], 2 ) ) {
						$column['data'] = '';
					} else {
						// get including or excluding tax
						$regular_price = $regular_prices[$tax];
						// single or total
						if ($price_type == 'total') {
							$regular_price = $regular_price * $item['quantity'];
						}
						$column['data'] = $wpo_wcpdf->export->wc_price( $regular_price );
					}
					break;
				case 'discount':
					// $price_type, $tax
					if ($price_type == 'percent') {
						$discount = ( ($item['item']['line_subtotal'] + $item['item']['line_subtotal_tax']) - ( $item['item']['line_total'] + $item['item']['line_tax'] ) );
						$percent = round( ( $discount / ( $item['item']['line_subtotal'] + $item['item']['line_subtotal_tax'] ) ) * 100 );
						$column['data'] = "{$percent}%";
						break;
					}
					
					$price_type = "{$price_type}_{$tax}";
					switch ($price_type) {
						// before discount
						case 'single_incl':
							$price = ( ($item['item']['line_subtotal'] + $item['item']['line_subtotal_tax']) - ( $item['item']['line_total'] + $item['item']['line_tax'] ) ) / max( 1, $item['quantity'] );
							$column['data'] = $wpo_wcpdf->export->wc_price( $price );
							break;
						case 'single_excl':
							$price = ( $item['item']['line_subtotal'] - $item['item']['line_total'] ) / max( 1, $item['quantity'] );
							$column['data'] = $wpo_wcpdf->export->wc_price( $price );
							break;
						case 'total_incl':
							$price = ($item['item']['line_subtotal'] + $item['item']['line_subtotal_tax']) - ( $item['item']['line_total'] + $item['item']['line_tax'] );
							$column['data'] = $wpo_wcpdf->export->wc_price( $price );
							break;
						case 'total_excl':
							$price = $item['item']['line_subtotal'] - $item['item']['line_total'];
							$column['data'] = $wpo_wcpdf->export->wc_price( $price );
							break;
					}
					break;
				case 'vat':
					// $price_type, $discount
					$price_type = "{$price_type}_{$discount}";
					switch ($price_type) {
						// before discount
						case 'single_before':
							$price = ( $item['item']['line_subtotal_tax'] ) / max( 1, $item['quantity'] );
							$column['data'] = $wpo_wcpdf->export->wc_price( $price );
							break;
						case 'single_after':
							$price = ( $item['item']['line_tax'] ) / max( 1, $item['quantity'] );
							$column['data'] = $wpo_wcpdf->export->wc_price( $price );
							break;
						case 'total_before':
							$column['data'] = $item['line_subtotal_tax'];
							break;
						case 'total_after':
							$column['data'] = $item['line_tax'];
							break;
					}
					break;
				case 'tax_rate':
					$column['data'] = $item['tax_rates'];
					break;
				case 'weight':
					if ( !isset($qty) ) {
						$qty = 'single';
					}

					switch ($qty) {
						case 'single':
							$column['data'] = !empty($item['weight']) ? $item['weight'] : '';
							break;
						case 'total':
							$column['data'] = !empty($item['weight']) ? $item['weight'] * $item['quantity'] : '';
							break;
					}
					if (isset($show_unit) && !empty($item['weight'])) {
						$column['data'] .= get_option('woocommerce_weight_unit');
					}
					break;
				case 'product_attribute':
					if (isset($item['product'])) {
						$column['data'] = $wpo_wcpdf->get_product_attribute( $attribute_name, $item['product'] );
					} else {
						$column['data'] = '';
					}
					break;
				case 'product_custom':
					if (isset($item['product']) && !empty($field_name)) {
						$column['data'] = get_post_meta( $item['product_id'], $field_name, true );
					} else {
						$column['data'] = '';
					}
					break;
				case 'product_description':
					if (isset($item['product'])) {
						if ( isset( $use_variation_description ) && isset( $item['variation_id'] ) && $item['variation_id'] != 0 && version_compare( WOOCOMMERCE_VERSION, '2.4', '>=' ) ) {
							$column['data'] = $item['product']->get_variation_description();
						} else {
							switch ($description_type) {
								case 'short':
									$column['data'] = $item['product']->post->post_excerpt;
									break;
								case 'long':
									$column['data'] = $item['product']->post->post_content;
									break;
							}
						}
					} else {
						$column['data'] = '';
					}
					break;
				case 'product_categories':
					if (isset($item['product'])) {
						$column['data'] = strip_tags( $item['product']->get_categories() );
					} else {
						$column['data'] = '';
					}
					break;
				case 'all_meta':
					// $product_fallback
					// For an order added through the admin) we can display
					// the formatted variation data (if fallback enabled)
					if ( isset($product_fallback) && empty($item['meta']) && isset($item['product']) && function_exists('wc_get_formatted_variation') ) {
						$item['meta'] = wc_get_formatted_variation($item['product']->variation_data, true);
					}
					$column['data'] = '<span class="item-meta">'.$item['meta'].'</span>';
					break;
				case 'item_meta':
					// $field_name
					if ( !empty($field_name) ) {
						$column['data'] = wc_get_order_item_meta( $item['item_id'], $field_name, true );
					} else {
						$column['data'] = '';
					}
					break;
				case 'cb':
					$column['data'] = '<span class="checkbox"></span>';
					break;
				case 'static_text':
					// $text
					$column['data'] = !empty( $text ) ? $text : '';
					break;

				default:
					$column['data'] = '';
					break;
			}

			// set class if not set;
			if (!isset($column['class'])) {
				$column['class'] = $type;
			}

			// mark first and last column
			if (isset($position)) {
				$column['class'] .= " {$position}-column";
			}

			return apply_filters( 'wpo_wcpdf_templates_item_column_data', $column, $column_setting, $item );
		}

		/**
		 * Output custom blocks (if set for template)
		 */
		public function custom_blocks_data( $template_type, $order ) {
			$editor_settings = get_option('wpo_wcpdf_editor_settings');
			if (!empty($editor_settings["fields_{$template_type}_custom"])) {
				foreach ($editor_settings["fields_{$template_type}_custom"] as $key => $custom_block) {
					// echo "<pre>";var_dump($custom_block);echo "</pre>";die();
					if ( current_filter() != $custom_block['position']) {
						continue;
					}

					// only process blocks with input
					if ( $custom_block['type'] == 'custom_field' && empty( $custom_block['meta_key'] ) ) {
						continue;
					} elseif ( $custom_block['type'] == 'text' && empty( $custom_block['text'] ) ) {
						continue;
					}

					switch ($custom_block['type']) {
						case 'custom_field':
							$data = get_post_meta( $order->id, $custom_block['meta_key'], true);
							$class = $custom_block['meta_key'];
							break;
						case 'text':
							$formatted_text = $this->make_replacements( $custom_block['text'], $order );
							$data =  nl2br( wptexturize( $formatted_text ) );
							$class = 'custom-block-text';
							break;						
					}

					// Hide if empty option
					if ( $custom_block['type'] == 'custom_field' && isset($custom_block['hide_if_empty']) && empty( $data ) ) {
						continue;
					}


					// output table rows if in order data table
					if ( in_array( current_filter(), array( 'wpo_wcpdf_before_order_data', 'wpo_wcpdf_after_order_data') ) ) {
						printf('<tr class="%s"><th>%s</th><td>%s</td></tr>', $class, $custom_block['label'], $data );
					} else {
						if (!empty($custom_block['label'])) {
							printf('<h3>%s</h3>', $custom_block['label'] );
						}
						printf('<div>%s</div>', $data );
					}
				};
			}
		}

		public function settings_fields_replacements( $text ) {
			global $wpo_wcpdf;
			// make replacements if placeholders present
			if ( strpos( $text, '{{' ) !== false ) {
				$text = $this->make_replacements( $text, $wpo_wcpdf->export->order );
			}

			return $text;
		}

		public function make_replacements ( $text, $order ) {
			global $wpo_wcpdf;	
			$order_meta = get_post_meta( $order->id );

			// flatten order meta array
			foreach ($order_meta as $key => &$value) {
				$value = $value[0];
			}
			// remove reference!
			unset($value);

			if ( get_post_type( $order->id ) == 'shop_order_refund' && $parent_order_id = wp_get_post_parent_id( $order->id ) ) {
				$parent_meta = get_post_meta( $parent_order_id );
				// flatten parent meta array
				foreach ($parent_meta as $key => &$value) {
					$value = $value[0];
				}
				// remove reference!
				unset($value);
			}

			// get full countries & states
			$countries = new WC_Countries;
			if ( get_post_type( $order->id ) == 'shop_order_refund' && $parent_order_id = wp_get_post_parent_id( $order->id ) ) {
				$shipping_country	= isset($parent_meta['_shipping_country']) ? $parent_meta['_shipping_country'] : '';
				$billing_country	= isset($parent_meta['_billing_country']) ? $parent_meta['_billing_country'] : '';
				$shipping_state		= isset($parent_meta['_shipping_state']) ? $parent_meta['_shipping_state'] : '';
				$billing_state		= isset($parent_meta['_billing_state']) ? $parent_meta['_billing_state'] : '';
			} else {
				$shipping_country	= isset($order_meta['_shipping_country']) ? $order_meta['_shipping_country'] : '';
				$billing_country	= isset($order_meta['_billing_country']) ? $order_meta['_billing_country'] : '';
				$shipping_state		= isset($order_meta['_shipping_state']) ? $order_meta['_shipping_state'] : '';
				$billing_state		= isset($order_meta['_billing_state']) ? $order_meta['_billing_state'] : '';
			}

			$shipping_state_full	= ( $shipping_country && $shipping_state && isset( $countries->states[ $shipping_country ][ $shipping_state ] ) ) ? $countries->states[ $shipping_country ][ $shipping_state ] : $shipping_state;
			$billing_state_full		= ( $billing_country && $billing_state && isset( $countries->states[ $billing_country ][ $billing_state ] ) ) ? $countries->states[ $billing_country ][ $billing_state ] : $billing_state;
			$shipping_country_full	= ( $shipping_country && isset( $countries->countries[ $shipping_country ] ) ) ? $countries->countries[ $shipping_country ] : $shipping_country;
			$billing_country_full	= ( $billing_country && isset( $countries->countries[ $billing_country ] ) ) ? $countries->countries[ $billing_country ] : $billing_country;
			unset($countries);

			// add 'missing meta'
			$order_meta['shipping_address']			= $order->get_formatted_shipping_address();
			$order_meta['shipping_country_code']	= $shipping_country;
			$order_meta['shipping_state_code']		= $shipping_state;
			$order_meta['_shipping_country']		= $shipping_country_full;
			$order_meta['_shipping_state']			= $shipping_state_full;

			$order_meta['billing_address']			= $order->get_formatted_billing_address();
			$order_meta['billing_country_code']		= $billing_country;
			$order_meta['billing_state_code']		= $billing_state;
			$order_meta['_billing_country']			= $billing_country_full;
			$order_meta['_billing_state']			= $billing_state_full;

			$order_meta['site_title']				= get_bloginfo();
			$order_meta['shipping_method']			= $order->get_shipping_method();
			$order_meta['shipping_notes']			= wpautop( wptexturize( $order->customer_note ) );
			$order_meta['customer_note']			= $order_meta['shipping_notes'];
			$order_meta['order_notes']				= $this->get_order_notes( $order );
			$order_meta['private_order_notes']		= $this->get_order_notes( $order, 'private' );
			$order_meta['order_number']				= ltrim($order->get_order_number(), '#');
			$order_meta['order_date']				= date_i18n( get_option( 'date_format' ), strtotime( $order->order_date ) );
			$order_meta['order_time']				= date_i18n( get_option( 'time_format' ), strtotime( $order->order_date ) );
			$order_meta['used_coupons']				= implode(', ', $order->get_used_coupons() );

			// formatted prices including tax
			$grand_total 							= $wpo_wcpdf->get_order_grand_total('incl');
			$order_meta['formatted_order_total']	= $grand_total['value'];
			$subtotal 								= $wpo_wcpdf->get_order_subtotal('incl');
			$order_meta['formatted_subtotal']		= $subtotal['value'];
			$discount 								= $wpo_wcpdf->get_order_discount('total', 'incl');
			$order_meta['formatted_discount']		= isset($discount['value']) ? $discount['value'] : '';
			$shipping 								= $wpo_wcpdf->get_order_shipping('incl');
			$order_meta['formatted_shipping']		= $shipping['value'];

			// formatted prices excluding tax
			$grand_total							= $wpo_wcpdf->get_order_grand_total('excl');
			$order_meta['formatted_order_total_ex']	= $grand_total['value'];
			$subtotal								= $wpo_wcpdf->get_order_subtotal('excl');
			$order_meta['formatted_subtotal_ex']	= $subtotal['value'];
			$shipping								= $wpo_wcpdf->get_order_shipping('excl');
			$order_meta['formatted_shipping_ex']	= $shipping['value'];
			$discount								= $wpo_wcpdf->get_order_discount('total', 'excl');
			$order_meta['formatted_discount_ex']	= isset($discount['value']) ? $discount['value'] : '';

			// invoice data
			$order_meta['invoice_number']			= $wpo_wcpdf->get_invoice_number();
			$invoice_date = get_post_meta($wpo_wcpdf->export->order->id,'_wcpdf_invoice_date',true);
			// prevent creating invoice date when not already set
			if (!empty($invoice_date)) {
				$order_meta['invoice_date']			= $wpo_wcpdf->get_invoice_date();
			}

			// create placeholders list
			foreach ($order_meta as $key => $value) {
				// strip leading underscores, add brackets
				$placeholders[$key] = '{{'.ltrim($key,'_').'}}';
			}

			// make an index of placeholders
			preg_match_all('/\{\{.*?\}\}/', $text, $placeholders_used);
			$placeholders_used = array_shift($placeholders_used); // we only need the first match set

			// unset empty order_meta and remove corresponding placeholder
			foreach ($order_meta as $key => $value) {
				if (empty($value)) {
					unset($order_meta[$key]);
					unset($placeholders[$key]);
				}
			}

			// make replacements
			$formatted_text = str_replace($placeholders, $order_meta, $text);

			// remove leftover placeholders, but not special ones :)
			$dont_remove = array( '{{PAGE_NUM}}', '{{PAGE_COUNT}}' );
			$formatted_text = str_replace( array_diff( $placeholders_used, $dont_remove ), '', $formatted_text );

			return $formatted_text;
		}

		public function get_order_notes( $order, $filter = 'customer' ) {
			if ( get_post_type( $order->id ) == 'shop_order_refund' && $parent_order_id = wp_get_post_parent_id( $order->id ) ) {
				$post_id = $parent_order_id;
			} else {
				$post_id = $order->id;
			}

			$args = array(
				'post_id' 	=> $post_id,
				'approve' 	=> 'approve',
				'type' 		=> 'order_note'
			);

			remove_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10, 1 );

			$notes = get_comments( $args );

			add_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10, 1 );

			if ( $notes ) {
				$formatted_notes = array();
				foreach( $notes as $key => $note ) {
					if ( $filter == 'customer' && !get_comment_meta( $note->comment_ID, 'is_customer_note', true ) ) {
						unset($notes[$key]);
						continue;
					}
					if ( $filter == 'private' && get_comment_meta( $note->comment_ID, 'is_customer_note', true ) ) {
						unset($notes[$key]);
						continue;
					}

					$formatted_notes[$key] = '<div class="note_content">'. wpautop( wptexturize( wp_kses_post( $note->comment_content ) ) ) . '</div>';
				}
				return implode("\n", $formatted_notes);
			} else {
				return false;
			}
		}

		public function add_tax_base( $taxes, $order) {
			$tax_rates_base = $this->get_tax_rates_base( $order );
			foreach ($taxes as $key => $tax) {
				if ( isset( $tax_rates_base[$tax['rate_id']] ) ) {
					$taxes[$key]['base'] = $tax_rates_base[$tax['rate_id']]->base;
				}
			}

			return $taxes;
		}

		public function get_tax_rates_base( $order ) {
			// only works in WC2.2+
			if ( version_compare( WOOCOMMERCE_VERSION, '2.2', '<' ) ) {
				return $taxes;
			}

			// get taxes from WC
			$tax_totals = $order->get_tax_totals();
			// put taxes in new array with tax_id as key
			$taxes = array();
			foreach ($tax_totals as $code => $tax) {
				$tax->code = $code;
				$tax->base = 0;
				$taxes[$tax->rate_id] = $tax;
			}

			// get subtotals from regular line items and fees
			$items = $order->get_items( array( 'fee', 'line_item', 'shipping' ) );
			foreach ($items as $item_id => $item) {
				// get tax data
				if ( $item['type'] == 'shipping' ) {
					$line_taxes = maybe_unserialize( $item['taxes'] );
				} else {
					$line_tax_data = maybe_unserialize( $item['line_tax_data'] );
					$line_taxes = $line_tax_data['total'];
				}

				foreach ( $line_taxes as $rate_id => $tax ) {
					if ( isset( $taxes[$rate_id] ) && $tax != 0 ) {
						$taxes[$rate_id]->base += ($item['type'] == 'shipping') ? $item['cost'] : $item['line_total'];
					}
				}
			}

			return $taxes;
		}

		public function save_regular_item_price( $order_id, $posted = array() ) {
			if ( $order = wc_get_order( $order_id ) ) {
				$items = $order->get_items();
				if (empty($items)) {
					return;
				}

				foreach ($items as $item_id => $item) {
					// this function will directly store the item price
					$regular_price = $this->get_regular_item_price( $item, $item_id, $order );
				}
			}
		}

		// get regular price from item - query product when not stored in item yet
		public function get_regular_item_price( $item, $item_id, $order ) {
			// first check if we alreay have stored the regular price of this item
			$regular_price = wc_get_order_item_meta( $item_id, '_wcpdf_regular_price', true );
			if (!empty($regular_price)) {
				return $regular_price;
			}

			$product = $order->get_product_from_item( $item );
			if ($product) {
				$product_regular_price = $product->regular_price;
				// get different incarnations
				$regular_price = array(
					'incl'	=> $product->get_price_including_tax( 1, $product_regular_price ),
					'excl'	=> $product->get_price_excluding_tax( 1, $product_regular_price ),
				);
			} else {
				// fallback to item price
				$regular_price = array(
					'incl'	=> $order->get_line_subtotal( $item, true /* $inc_tax */, false ),
					'excl'	=> $order->get_line_subtotal( $item, false /* $inc_tax */, false ),
				);
			}

			wc_update_order_item_meta( $item_id, '_wcpdf_regular_price', $regular_price );
			return $regular_price;
		}

		public function get_discount_percentage( $order ) {
			if (method_exists($order, 'get_total_discount')) {
				// WC2.3 introduced an $ex_tax parameter
				$ex_tax = false;
				$discount = $order->get_total_discount( $ex_tax );
			} elseif (method_exists($order, 'get_discount_total')) {
				// was this ever included in a release?
				$discount = $order->get_discount_total();
			} else {
				return false;
			}

			$order_total = $order->get_total();

			// shipping and fees are not discounted
			$shipping_total = $order->get_total_shipping() + $order->get_shipping_tax();
			$fee_total = 0;
			if (method_exists($order, 'get_fees')) { // old versions of WC don't support fees
				foreach ( $order->get_fees() as $fees ) {
					$fee_total += $fees['line_total'] + $fees['line_tax'];
				}
			}

			$percentage = ( $discount / ( $order_total + $discount - $shipping_total - $fee_total) ) * 100;

			return round($percentage);
		}

		// hide regular price item eta
		public function hide_regular_price_itemmeta( $hidden_keys ) {
			$hidden_keys[] = '_wcpdf_regular_price';
			return $hidden_keys;
		}

		public function array_keys_prefix( $array, $prefix, $add_or_remove = 'add' ) {
			if (empty($array) || !is_array($array) ) {
				return $array;
			}

			foreach ($array as $key => $value) {
				if ( $add_or_remove == 'add' ) {
					$array[$prefix.$key] = $value;
					unset($array[$key]);
				} else { // remove
					$new_key = str_replace($prefix, '', $key);
					$array[$new_key] = $value;
					unset($array[$key]);
				}
			}

			return $array;

		}

	} // end class
} // end class_exists