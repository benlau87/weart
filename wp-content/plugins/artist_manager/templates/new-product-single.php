<?php

global $post;

$from_shortcode = false;

if( isset( $post->ID ) && $post->ID && $post->post_type == 'product' ) {
    $post_id = $post->ID;
    $post_title = $post->post_title;
    $post_content = $post->post_content;
    $post_excerpt = $post->post_excerpt;
    $post_status = $post->post_status;
} else {
    $post_id = NULL;
    $post_title = '';
    $post_content = '';
    $post_excerpt = '';
    $post_status = 'publish';
    $from_shortcode = true;

}

if ( isset( $_GET['product_id'] ) ) {
    $post_id        = intval( $_GET['product_id'] );
    $post           = get_post( $post_id );
    $post_title     = $post->post_title;
    $post_content   = $post->post_content;
    $post_excerpt   = $post->post_excerpt;
    $post_status    = $post->post_status;
    $product        = get_product( $post_id );
    $from_shortcode = true;
}

$_regular_price         = get_post_meta( $post_id, '_regular_price', true );
$_sale_price            = get_post_meta( $post_id, '_sale_price', true );
$is_discount            = !empty( $_sale_price ) ? true : false;
$_sale_price_dates_from = get_post_meta( $post_id, '_sale_price_dates_from', true );
$_sale_price_dates_to   = get_post_meta( $post_id, '_sale_price_dates_to', true );

$_sale_price_dates_from = !empty( $_sale_price_dates_from ) ? date_i18n( 'Y-m-d', $_sale_price_dates_from ) : '';
$_sale_price_dates_to   = !empty( $_sale_price_dates_to ) ? date_i18n( 'Y-m-d', $_sale_price_dates_to ) : '';
$show_schedule          = false;

if ( !empty( $_sale_price_dates_from ) && !empty( $_sale_price_dates_to ) ) {
    $show_schedule = true;
}

$_featured          = get_post_meta( $post_id, '_featured', true );
$_weight            = get_post_meta( $post_id, '_weight', true );
$_length            = get_post_meta( $post_id, '_length', true );
$_width             = get_post_meta( $post_id, '_width', true );
$_height            = get_post_meta( $post_id, '_height', true );
$_downloadable      = get_post_meta( $post_id, '_downloadable', true );
$_stock             = get_post_meta( $post_id, '_stock', true );
$_stock_status      = get_post_meta( $post_id, '_stock_status', true );
$_visibility        = get_post_meta( $post_id, '_visibility', true );
$_enable_reviews    = $post->comment_status;
$_required_tax      = get_post_meta( $post_id, '_required_tax', true );
$_has_attribute     = get_post_meta( $post_id, '_has_attribute', true );
$_create_variations = get_post_meta( $post_id, '_create_variation', true );


/* Art specific fields */
$waa_date_created = get_post_meta( $post_id, 'waa_date_created', true );




$processing_time         = waa_get_shipping_processing_times();
$user_id                 = get_current_user_id();
$_disable_shipping       = ( get_post_meta( $post_id, '_disable_shipping', true ) ) ? get_post_meta( $post_id, '_disable_shipping', true ) : 'no';
$_additional_price       = get_post_meta( $post_id, '_additional_price', true );
$_additional_qty         = get_post_meta( $post_id, '_additional_qty', true );
$_processing_time        = get_post_meta( $post_id, '_dps_processing_time', true );
$dps_shipping_type_price = get_user_meta( $user_id, '_dps_shipping_type_price', true );
$dps_additional_qty      = get_user_meta( $user_id, '_dps_additional_qty', true );
$dps_pt                  = get_user_meta( $user_id, '_dps_pt', true );

$porduct_shipping_pt = ( $_processing_time ) ? $_processing_time : $dps_pt;
$attribute_taxonomies = wc_get_attribute_taxonomies();

$product_attributes = get_post_meta( $post_id, '_product_attributes', true );

$processing_time = waa_get_shipping_processing_times();
$tax_classes = array_filter( array_map( 'trim', explode( "\n", get_option( 'woocommerce_tax_classes' ) ) ) );
$classes_options = array();
$classes_options[''] = __( 'Standard', 'waa' );

if ( $tax_classes ) {

    foreach ( $tax_classes as $class ) {
        $classes_options[ sanitize_title( $class ) ] = esc_html( $class );
    }
}

if ( ! $from_shortcode ) {
    get_header();
}
?>
<section id="content" role="main">
	<div class="container">
		<div class="row">
			<div class="col-md-12" role="main">	
				<div class="waa-dashboard-wrap">
						<?php waa_get_template( 'dashboard-nav.php', array( 'active_menu' => 'product' ) ); ?>

						<div class="waa-dashboard-content waa-product-edit">
								<header class="waa-dashboard-header waa-clearfix">
										<h1 class="entry-title">
												<?php if ( !$post_id ): ?>
														<?php _e( 'Add New Product', 'waa' ); ?>
												<?php else: ?>
														<?php _e( 'Edit Product', 'waa' ); ?>
														<span class="waa-label <?php echo waa_get_post_status_label_class( $post->post_status ); ?> waa-product-status-label">
																<?php echo waa_get_post_status( $post->post_status ); ?>
														</span>

														<?php if ( $post->post_status == 'publish' ) { ?>
																<span class="waa-right">
																		<a class="view-product waa-btn waa-btn-sm" href="<?php echo get_permalink( $post->ID ); ?>" target="_blank"><?php _e( 'View Product', 'waa' ); ?></a>
																</span>
														<?php } ?>

														<?php if ( $_visibility == 'hidden' ) { ?>
																<span class="waa-right waa-label waa-label-default waa-product-hidden-label"><i class="fa fa-eye-slash"></i> <?php _e( 'Hidden', 'waa' ); ?></span>
														<?php } ?>

												<?php endif ?>
										</h1>
								</header><!-- .entry-header -->

								<div class="product-edit-new-container">
										<?php if ( waa_Template_Shortcodes::$errors ) { ?>
												<div class="waa-alert waa-alert-danger">
														<a class="waa-close" data-dismiss="alert">&times;</a>

														<?php foreach ( waa_Template_Shortcodes::$errors as $error) { ?>

																<strong><?php _e( 'Error!', 'waa' ); ?></strong> <?php echo $error ?>.<br>

														<?php } ?>
												</div>
										<?php } ?>

										<?php if ( isset( $_GET['message'] ) && $_GET['message'] == 'success') { ?>
												<div class="waa-message">
														<button type="button" class="waa-close" data-dismiss="alert">&times;</button>
														<strong><?php _e( 'Success!', 'waa' ); ?></strong> <?php _e( 'The product has been saved successfully.', 'waa' ); ?>

														<?php if ( $post->post_status == 'publish' ) { ?>
																<a href="<?php echo get_permalink( $post_id ); ?>" target="_blank"><?php _e( 'View Product &rarr;', 'waa' ); ?></a>
														<?php } ?>
												</div>
										<?php } ?>

										<?php
										$can_sell = apply_filters( 'waa_can_post', true );

										if ( $can_sell ) {

												if ( waa_is_seller_enabled( get_current_user_id() ) ) { ?>

														<form class="waa-product-edit-form" role="form" method="post">

																<?php if ( $post_id ): ?>
																		<?php do_action( 'waa_product_data_panel_tabs' ); ?>
																<?php endif; ?>
																<?php do_action( 'waa_product_edit_before_main' ); ?>

																<div class="waa-form-top-area">

																		<div class="content-half-part">

																				<div class="waa-form-group">
																						<input type="hidden" name="waa_product_id" value="<?php echo $post_id; ?>">

																						<label for="post_title" class="form-label"><?php _e( 'Title', 'waa' ); ?></label>
																						<?php waa_post_input_box( $post_id, 'post_title', array( 'placeholder' => __( 'Product name..', 'waa' ), 'value' => $post_title, 'required' => true ) ); ?>
																				</div>

																				<div class="hide_if_variation waa-clearfix">

																						<div class="waa-form-group waa-clearfix waa-price-container">

																								<div class="regular-price">
																										<label for="_regular_price" class="form-label"><?php _e( 'Price', 'waa' ); ?></label>

																										<div class="waa-input-group">
																												<span class="waa-input-group-addon"><?php echo get_woocommerce_currency_symbol(); ?></span>
																												<?php waa_post_input_box( $post_id, '_regular_price', array( 'placeholder' => __( '0.00', 'waa' ) ), 'number' ); ?>
																										</div>
																								</div>
																						</div>
																				</div>

																				<?php if ( waa_get_option( 'product_category_style', 'waa_selling', 'single' ) == 'single' ): ?>
																						<div class="waa-form-group">
																								<label for="product_cat" class="form-label"><?php _e( 'Category', 'waa' ); ?></label>
																								<?php
																								$product_cat = -1;
																								$term = array();
																								$term = wp_get_post_terms( $post_id, 'product_cat', array( 'fields' => 'ids') );

																								if ( $term ) {
																										$product_cat = reset( $term );
																								}

																								wp_dropdown_categories( array(
																										'show_option_none' => __( '- Select a category -', 'waa' ),
																										'hierarchical'     => 1,
																										'hide_empty'       => 0,
																										'name'             => 'product_cat',
																										'id'               => 'product_cat',
																										'taxonomy'         => 'product_cat',
																										'title_li'         => '',
																										'class'            => 'product_cat waa-form-control chosen',
																										'exclude'          => '',
																										'selected'         => $product_cat,
																								) );
																								?>
																						</div>
																				<?php elseif ( waa_get_option( 'product_category_style', 'waa_selling', 'single' ) == 'multiple' ): ?>
																						<div class="waa-form-group waa-list-category-box">
																								<h5><?php _e( 'Choose a category', 'waa' );  ?></h5>
																								<ul class="waa-checkbox-cat">
																										<?php
																										$term = array();
																										$term = wp_get_post_terms( $post_id, 'product_cat', array( 'fields' => 'ids') );

																										include_once waa_LIB_DIR.'/class.category-walker.php';
																										wp_list_categories(array(
																												'walker'       => new waaCategoryWalker(),
																												'title_li'     => '',
																												'id'           => 'product_cat',
																												'hide_empty'   => 0,
																												'taxonomy'     => 'product_cat',
																												'hierarchical' => 1,
																												'selected'     => $term
																										));
																										?>
																								</ul>
																						</div>
																				<?php endif; ?>
																				
																				<div class="waa-form-group">
																								<label for="pa_stil" class="form-label"><?php _e( 'Style', 'waa' ); ?></label>
																								<input type="hidden" name="waa_stil_attr" value="pa_stil">
																								<?php
																								$pa_stil = -1;
																								$term = array();
																								$term = wp_get_post_terms( $post_id, 'pa_stil', array( 'fields' => 'names') );

																								if ( $term ) {
																										$pa_stil = reset( $term );
																								}

																								wp_dropdown_categories( array(
																										'show_option_none' => __( '- Select a style -', 'waa' ),
																										'hierarchical'     => 1,
																										'hide_empty'       => 0,
																										'name'             => 'waa_stil_val',
																										'id'               => 'pa_stil',
																										'taxonomy'         => 'pa_stil',
																										'title_li'         => '',
																										'class'            => 'pa_stil waa-form-control chosen',
																										'exclude'          => '',
																										'selected'         => $pa_stil,
																										'value_field'	     => 'name'
																								) );
																								?>
																								<?php 
																									$current_user = get_current_user_id();
																									$profile_info = waa_get_store_info( $current_user );
																									$city_term = get_term_by('slug', sanitize_title($profile_info['region']), 'pa_stadt');
																									#print_r($city_term );
																								?>	
																								<input type="hidden" name="_has_attribute" value="yes">
																								<input type="hidden" name="waa_region_attr" value="pa_stadt">
																								<input type="hidden" name="waa_region_val" value="<?= $city_term->name; ?>">		
																						</div>

																				
																				<div class="waa-clearfix waa-form-group">
																									<label for="waa_date_created" class="form-label"><?php _e( 'Date created', 'waa' ); ?></label>
																										<div class="waa-form-group">
																												<input type="text" name="waa_date_created" class="waa-form-control datepicker" value="<?php echo esc_attr( $waa_date_created ); ?>" maxlength="10" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01]).(0[1-9]|1[012]).[0-9]{4}" placeholder="TT.MM.JJJJ" required>
																										</div>
																						</div>
																		</div><!-- .content-half-part -->

																		<div class="content-half-part featured-image">

																				<div class="waa-feat-image-upload">
																						<?php
																						$wrap_class        = ' waa-hide';
																						$instruction_class = '';
																						$feat_image_id     = 0;

																						if ( has_post_thumbnail( $post_id ) ) {
																								$wrap_class        = '';
																								$instruction_class = ' waa-hide';
																								$feat_image_id     = get_post_thumbnail_id( $post_id );
																						}
																						?>

																						<div class="instruction-inside<?php echo $instruction_class; ?>">
																								<input type="hidden" name="feat_image_id" class="waa-feat-image-id" value="<?php echo $feat_image_id; ?>">

																								<i class="fa fa-cloud-upload"></i>
																								<a href="#" class="waa-feat-image-btn btn btn-sm"><?php _e( 'Upload a product cover image', 'waa' ); ?></a>
																						</div>

																						<div class="image-wrap<?php echo $wrap_class; ?>">
																								<a class="close waa-remove-feat-image">&times;</a>
																								<?php if ( $feat_image_id ) { ?>
																										<?php echo get_the_post_thumbnail( $post_id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ), array( 'height' => '', 'width' => '' ) ); ?>
																								<?php } else { ?>
																										<img height="" width="" src="" alt="">
																								<?php } ?>
																						</div>
																				</div><!-- .waa-feat-image-upload -->

																				<div class="waa-product-gallery">
																						<div class="waa-side-body" id="waa-product-images">
																								<div id="product_images_container">
																										<ul class="product_images waa-clearfix">
																												<?php
																												$product_images = get_post_meta( $post_id, '_product_image_gallery', true );
																												$gallery = explode( ',', $product_images );

																												if ( $gallery ) {
																														foreach ($gallery as $image_id) {
																																if ( empty( $image_id ) ) {
																																		continue;
																																}

																																$attachment_image = wp_get_attachment_image_src( $image_id, 'thumbnail' );
																																?>
																																<li class="image" data-attachment_id="<?php echo $image_id; ?>">
																																		<img src="<?php echo $attachment_image[0]; ?>" alt="">
																																		<a href="#" class="action-delete" title="<?php esc_attr_e( 'Delete image', 'waa' ); ?>">&times;</a>
																																</li>
																																<?php
																														}
																												}
																												?>
																										</ul>

																										<input type="hidden" id="product_image_gallery" name="product_image_gallery" value="<?php echo esc_attr( $product_images ); ?>">
																								</div>
																						</div>
																				</div> <!-- .product-gallery -->
																		</div><!-- .content-half-part -->
																</div><!-- .waa-form-top-area -->

																<div class="waa-product-description">
																		<label for="post_content" class="form-label"><?php _e( 'Description', 'waa' ); ?></label>
																		<?php wp_editor( wpautop( $post_content ), 'post_content', array('editor_height' => 50, 'quicktags' => false, 'media_buttons' => false, 'teeny' => true, 'editor_class' => 'post_content') ); ?>
																</div>																
																				
																<div class="waa-form-group">
																		<label for="product_tag" class="form-label"><?php _e( 'Tags', 'waa' ); ?></label>
																		<?php
																		require_once waa_LIB_DIR.'/class.tag-walker.php';
																		$term = wp_get_post_terms( $post_id, 'product_tag', array( 'fields' => 'ids') );
																		$selected = ( $term ) ? $term : array();
																		$drop_down_tags = wp_dropdown_categories( array(
																				'show_option_none' => __( '', 'waa' ),
																				'hierarchical'     => 1,
																				'hide_empty'       => 0,
																				'name'             => 'product_tag[]',
																				'id'               => 'product_tag',
																				'taxonomy'         => 'product_tag',
																				'title_li'         => '',
																				'class'            => 'product_tags waa-form-control chosen',
																				'exclude'          => '',
																				'selected'         => $selected,
																				'echo'             => 0,
																				'walker'           => new waa_Walker_Tag_Multi()
																		) );

																		echo str_replace( '<select', '<select data-placeholder="'.__( 'Select product tags','waa' ).'" multiple="multiple" ', $drop_down_tags );

																		?>
																</div>

																
																<?php do_action( 'waa_new_product_form' ); ?>
																<?php if ( $post_id ): ?>
																		<?php do_action( 'waa_product_edit_after_main' ); ?>
																<?php endif; ?>
																<div class="waa-product-inventory waa-edit-row waa-clearfix">
																		<div class="waa-side-left">
																				<h2><?php _e( 'Inventory & Variants', 'waa' ); ?></h2>

																				<p>
																						<?php _e( 'Manage inventory, and configure the options for selling this product.', 'waa' ); ?>
																				</p>
																		</div>

																		<div class="waa-side-right">
																				<div class="waa-form-group hide_if_variation" style="width: 50%;">
																						<label for="_sku" class="form-label"><?php _e( 'SKU', 'waa' ); ?> <span><?php _e( '(Stock Keeping Unit)', 'waa' ); ?></span></label>
																						<?php waa_post_input_box( $post_id, '_sku' ); ?>
																				</div>

																				<div class="waa-form-group hide_if_variation">
																						<?php waa_post_input_box( $post_id, '_manage_stock', array( 'label' => __( 'Enable product stock management', 'waa' ) ), 'checkbox' ); ?>
																				</div>

																				<div class="show_if_stock waa-stock-management-wrapper waa-form-group waa-clearfix">

																						<div class="waa-w3 hide_if_variation">
																								<label for="_stock" class="waa-form-label"><?php _e( 'Quantity', 'waa' ); ?></label>
																								<input type="number" name="_stock" placeholder="<?php __( '1', 'waa' ); ?>" value="<?php echo wc_stock_amount( $_stock ); ?>" min="0" step="1">
																						</div>

																						<div class="waa-w3 hide_if_variation">
																								<label for="_stock_status" class="waa-form-label"><?php _e( 'Stock Status', 'waa' ); ?></label>

																								<?php waa_post_input_box( $post_id, '_stock_status', array( 'options' => array(
																										'instock'     => __( 'In Stock', 'waa' ),
																										'outofstock' => __( 'Out of Stock', 'waa' ),
																								) ), 'select' ); ?>
																						</div>

																						<div class="waa-w3 hide_if_variation">
																								<label for="_backorders" class="waa-form-label"><?php _e( 'Allow Backorders', 'waa' ); ?></label>

																								<?php waa_post_input_box( $post_id, '_backorders', array( 'options' => array(
																										'no'     => __( 'Do not allow', 'waa' ),
																										'notify' => __( 'Allow but notify customer', 'waa' ),
																										'yes'    => __( 'Allow', 'waa' )
																								) ), 'select' ); ?>
																						</div>
																				</div><!-- .show_if_stock -->

																			<!--	<div class="waa-form-group"> -->
																						<?php #waa_post_input_box( $post_id, '_sold_individually', array('label' => __( 'Allow only one quantity of this product to be bought in a single order', 'waa' ) ), 'checkbox' ); ?>
																			<!-- 	</div> -->
																				<input type="hidden" name="_sold_individually" value="yes" />

																				<?php if ( $post_id ): ?>
																						<?php do_action( 'waa_product_edit_after_inventory' ); ?>
																				<?php endif; ?>

																				

																				<?php if ( $post_id ): ?>
																						<?php do_action( 'waa_product_edit_after_downloadable' ); ?>
																				<?php endif; ?>
																				<?php if ( $post_id ): ?>
																						<?php do_action( 'waa_product_edit_after_sidebar' ); ?>
																				<?php endif; ?>
																				<!-- <div class="waa-divider-top"></div> -->
																	</div><!-- .waa-side-right -->
																</div><!-- .waa-product-inventory -->

																<?php if ( $post_id ): ?>
																		<?php do_action( 'waa_product_options_shipping_before' ); ?>
																<?php endif; ?>

																<?php if ( 'yes' == get_option( 'woocommerce_calc_shipping' ) || 'yes' == get_option( 'woocommerce_calc_taxes' ) ): ?>
																<div class="waa-product-shipping-tax waa-edit-row waa-clearfix <?php echo ( 'no' == get_option('woocommerce_calc_shipping') ) ? 'woocommerce-no-shipping' : '' ?> <?php echo ( 'no' == get_option('woocommerce_calc_taxes') ) ? 'woocommerce-no-tax' : '' ?>">
																		<div class="waa-side-left">
																				<h2><?php _e( 'Shipping & Tax', 'waa' ); ?></h2>

																				<p>
																						<?php _e( 'Manage shipping and tax for this product', 'waa' ); ?>
																				</p>
																		</div>

																		<div class="waa-side-right">
																				<?php if( 'yes' == get_option('woocommerce_calc_shipping') ): ?>
																						<div class="waa-clearfix hide_if_downloadable waa-shipping-container">
																								<input type="hidden" name="product_shipping_class" value="0">
																								<div class="waa-form-group">
																												<input type="checkbox" id="_disable_shipping" name="_disable_shipping" <?php checked( $_disable_shipping, 'no' ); ?>>
																								</div>
																								<div class="show_if_needs_shipping waa-shipping-dimention-options">
																										<?php waa_post_input_box( $post_id, '_weight', array( 'class' => '', 'placeholder' => __( 'Gewicht (' . esc_html( get_option( 'woocommerce_weight_unit' ) ) . ')', 'waa' ) ), 'number' ); ?>
																										<?php waa_post_input_box( $post_id, '_length', array( 'class' => '', 'placeholder' => __( 'L&auml;nge (' . esc_html( get_option( 'woocommerce_dimension_unit' ) ) . ')', 'waa' ) ), 'number' ); ?>
																										<?php waa_post_input_box( $post_id, '_width', array( 'class' => '', 'placeholder' => __( 'Breite (' . esc_html( get_option( 'woocommerce_dimension_unit' ) ) . ')', 'waa' ) ), 'number' ); ?>
																										<?php waa_post_input_box( $post_id, '_height', array( 'class' => '', 'placeholder' => __( 'H&ouml;he (' . esc_html( get_option( 'woocommerce_dimension_unit' ) ) . ')', 'waa' ) ), 'number' ); ?>
																										<div class="waa-clearfix"></div>
																								</div>

																								<?php if ( $post_id ): ?>
																										<?php do_action( 'waa_product_options_shipping' ); ?>
																								<?php endif; ?>
																								<div class="show_if_needs_shipping waa-form-group">
																										<label class="control-label" for="product_shipping_class"><?php _e( 'Shipping Class', 'waa' ); ?></label>
																										<div class="waa-text-left">
																												<?php
																												// Shipping Class
																												$classes = get_the_terms( $post->ID, 'product_shipping_class' );
																												if ( $classes && ! is_wp_error( $classes ) ) {
																														$current_shipping_class = current($classes)->term_id;
																												} else {
																														$current_shipping_class = '';
																												}

																												$args = array(
																														'taxonomy'          => 'product_shipping_class',
																														'hide_empty'        => 0,
																														'show_option_none'  => __( 'No shipping class', 'waa' ),
																														'name'              => 'product_shipping_class',
																														'id'                => 'product_shipping_class',
																														'selected'          => $current_shipping_class,
																														'class'             => 'waa-form-control'
																												);
																												?>

																												<?php wp_dropdown_categories( $args ); ?>
																												<p class="help-block"><?php _e( 'Shipping classes are used by certain shipping methods to group similar products.', 'waa' ); ?></p>
																										</div>
																								</div>

																								<div class="show_if_needs_shipping waa-shipping-product-options">

																										<div class="waa-form-group">
																												<?php waa_post_input_box( $post_id, '_overwrite_shipping', array( 'label' => __( 'Override default shipping cost for this product', 'waa' ) ), 'checkbox' ); ?>
																										</div>

																										<div class="waa-form-group show_if_override">
																												<label class="waa-control-label" for="_additional_product_price"><?php _e( 'Additional cost', 'waa' ); ?></label>
																												<input id="_additional_product_price" value="<?php echo $_additional_price; ?>" name="_additional_price" placeholder="z.B. 9,99" class="waa-form-control" type="number" step="any">
																										</div>

																										<div class="waa-form-group show_if_override">
																												<label class="waa-control-label" for="dps_additional_qty"><?php _e( 'Per Qty Additional Price', 'waa' ); ?></label>
																												<input id="additional_qty" value="<?php echo ( $_additional_qty ) ? $_additional_qty : $dps_additional_qty; ?>" name="_additional_qty" placeholder="z.B. 1,99" class="waa-form-control" type="number" step="any">
																										</div>

																										<div class="waa-form-group show_if_override">
																												<label class="waa-control-label" for="dps_additional_qty"><?php _e( 'Processing Time', 'waa' ); ?></label>
																												<select name="_dps_processing_time" id="_dps_processing_time" class="waa-form-control">
																														<?php foreach ( $processing_time as $processing_key => $processing_value ): ?>
																																	<option value="<?php echo $processing_key; ?>" <?php selected( $porduct_shipping_pt, $processing_key ); ?>><?php echo $processing_value; ?></option>
																														<?php endforeach ?>
																												</select>
																										</div>
																								</div>
																						</div>
																				<?php endif; ?>

																				<?php if ( 'yes' == get_option('woocommerce_calc_shipping') && 'yes' == get_option( 'woocommerce_calc_taxes' ) ): ?>
																						<div class="waa-divider-top hide_if_downloadable"></div>
																				<?php endif ?>

																		</div><!-- .waa-side-right -->
																</div><!-- .waa-product-inventory -->
																<?php endif; ?>

																<?php if ( $post_id ): ?>
																		<?php do_action( 'waa_product_edit_after_shipping' ); ?>
																<?php endif; ?>
																<div class="waa-other-options waa-edit-row waa-clearfix">
																		<div class="waa-side-left">
																				<h2><?php _e( 'Other Options', 'waa' ); ?></h2>
																		</div>

																		<div class="waa-side-right">
																				<?php if ( $post_id ): ?>
																						<div class="waa-form-group">
																								<label for="post_status" class="form-label"><?php _e( 'Product Status', 'waa' ); ?></label>
																								<?php if ( $post_status != 'pending' ) { ?>
																										<?php $post_statuses = apply_filters( 'waa_post_status', array(
																												'publish' => __( 'Online', 'waa' ),
																												'draft'   => __( 'Draft', 'waa' )
																										), $post ); ?>

																										<select id="post_status" class="waa-form-control" name="post_status">
																												<?php foreach ( $post_statuses as $status => $label ) { ?>
																														<option value="<?php echo $status; ?>"<?php selected( $post_status, $status ); ?>><?php echo $label; ?></option>
																												<?php } ?>
																										</select>
																								<?php } else { ?>
																										<?php $pending_class = $post_status == 'pending' ? '  waa-label waa-label-warning': ''; ?>
																										<span class="waa-toggle-selected-display<?php echo $pending_class; ?>"><?php echo waa_get_post_status( $post_status ); ?></span>
																								<?php } ?>
																						</div>
																				<?php endif ?>

																				<input type="hidden" name="_visibility" value="visible" />
																			

																				<div class="waa-form-group">
																						<label for="_purchase_note" class="form-label"><?php _e( 'Purchase Note', 'waa' ); ?></label>
																						<?php waa_post_input_box( $post_id, '_purchase_note', array( 'placeholder' => __( 'Customer will get this info in their order email', 'waa' ) ), 'textarea' ); ?>
																				</div>
																		</div>
																</div><!-- .waa-other-options -->

																<?php if ( $post_id ): ?>
																		<?php do_action( 'waa_product_edit_after_options' ); ?>
																<?php endif; ?>

																<?php wp_nonce_field( 'waa_add_new_product', 'waa_add_new_product_nonce' ); ?>
																<input type="submit" name="waa_add_product" class="waa-btn waa-btn-theme waa-btn-lg btn-block" value="<?php esc_attr_e( 'Save Product', 'waa' ); ?>"/>

														</form>

												<?php } else { ?>

														<?php waa_seller_not_enabled_notice(); ?>

												<?php } ?>

										<?php } else { ?>

												<?php do_action( 'waa_can_post_notice' ); ?>

										<?php } ?>
								</div> <!-- #primary .content-area -->
						</div>
				</div><!-- .waa-dashboard-wrap -->
				<div class="waa-clearfix"></div>

				<?php
				if( $post_id ) {
						?>
						<div class="variation-single-content">
							 <?php include_once 'edit/vatiation-popup.php'; ?>
						</div>
						<?php
				}
				?>
			</div>
		</div>
	</div>
<?php

wp_reset_postdata();

if ( ! $from_shortcode ) {
    get_footer();
}
?>

