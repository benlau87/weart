<?php global $wpo_wcpdf, $wpo_wcpdf_templates; ?>
		<table class="head container">
				<tr>
						<td class="header">
								<?php
								if ($wpo_wcpdf->get_header_logo_id()) {
																		$wpo_wcpdf->header_logo();
								} else {
										echo apply_filters('wpo_wcpdf_invoice_title', __('Invoice', 'wpo_wcpdf'));
								}
								?>
						</td>
						<td class="shop-info">
								<table border="0" width="100%">
										<tr>
												<td width="50%">
														WeArt Zon/Wellner KG
														Stadtbachstrasse 40<br>
														3012 Bern<br>
														Schweiz<br>
														<strong>Tel.:</strong> +41 (0)79 / 2847998<br>
														<strong>E-Mail:</strong> support@weare-art.com
												</td>
												<td>Bankverbindung<br>
														<strong>Bank:</strong> PostFinance AG<br>
														<strong>Ktonr.:</strong> 000612441833<br>
														<strong>Blz.:</strong> 9000<br>
														<strong>IBAN:</strong> CH71 0900 0000 6124 4183 3<br>
														<strong>BIC:</strong> POFICHBEXXX
												</td>
										</tr>
								</table>

						</td>
				</tr>
		</table>

		<h1 class="document-type-label">
				<?php if ($wpo_wcpdf->get_header_logo_id()) echo apply_filters('wpo_wcpdf_invoice_title', __('Invoice', 'wpo_wcpdf')); ?>
		</h1>

<?php do_action('wpo_wcpdf_after_document_label', $wpo_wcpdf->export->template_type, $wpo_wcpdf->export->order); ?>

		<table class="order-data-addresses">
				<tr>
						<td class="address billing-address">
								<h3>Rechnungsadresse</h3>
								<br>
								<?php $wpo_wcpdf->billing_address(); ?>
								<?php if (isset($wpo_wcpdf->settings->template_settings['invoice_email'])) { ?>
										<div class="billing-email"><?php $wpo_wcpdf->billing_email(); ?></div>
								<?php } ?>
								<?php if (isset($wpo_wcpdf->settings->template_settings['invoice_phone'])) { ?>
										<div class="billing-phone"><?php $wpo_wcpdf->billing_phone(); ?></div>
								<?php } ?>
						</td>
						<td class="address shipping-address">
								<?php if (isset($wpo_wcpdf->settings->template_settings['invoice_shipping_address']) /*&& $wpo_wcpdf->ships_to_different_address()*/) { ?>
										<h3>Lieferadresse</h3>
										<br>
										<?php $wpo_wcpdf->shipping_address(); ?>
								<?php } ?>
						</td>
						<td class="order-data">
								<table>
										<tr class="customer-number">
												<th>Kundennummer:</th>
												<td><?php echo $wpo_wcpdf->export->order->post->post_author; ?></td>
										</tr>
										<?php do_action('wpo_wcpdf_before_order_data', $wpo_wcpdf->export->template_type, $wpo_wcpdf->export->order); ?>
										<?php if (isset($wpo_wcpdf->settings->template_settings['display_number']) && $wpo_wcpdf->settings->template_settings['display_number'] == 'invoice_number') { ?>
												<tr class="invoice-number">
														<th><?php _e('Invoice Number:', 'wpo_wcpdf'); ?></th>
														<td><?php $wpo_wcpdf->invoice_number(); ?></td>
												</tr>
										<?php } ?>
										<?php if (isset($wpo_wcpdf->settings->template_settings['display_date']) && $wpo_wcpdf->settings->template_settings['display_date'] == 'invoice_date') { ?>
												<tr class="invoice-date">
														<th><?php _e('Invoice Date:', 'wpo_wcpdf'); ?></th>
														<td><?php $wpo_wcpdf->invoice_date(); ?></td>
												</tr>
										<?php } ?>
										<tr class="order-number">
												<th><?php _e('Order Number:', 'wpo_wcpdf'); ?></th>
												<td><?php $wpo_wcpdf->order_number(); ?></td>
										</tr>
										<tr class="order-date">
												<th><?php _e('Order Date:', 'wpo_wcpdf'); ?></th>
												<td><?php $wpo_wcpdf->order_date(); ?></td>
										</tr>
										<tr class="payment-method">
												<th><?php _e('Payment Method:', 'wpo_wcpdf'); ?></th>
												<td><?php $wpo_wcpdf->payment_method(); ?></td>
										</tr>
										<?php do_action('wpo_wcpdf_after_order_data', $wpo_wcpdf->export->template_type, $wpo_wcpdf->export->order); ?>
								</table>
						</td>
				</tr>
		</table>

<?php do_action('wpo_wcpdf_before_order_details', $wpo_wcpdf->export->template_type, $wpo_wcpdf->export->order); ?>

		<table class="order-details">
				<thead>
				<tr>
										<?php
										foreach ( $wpo_wcpdf_templates->get_table_headers( $wpo_wcpdf->export->template_type ) as $column_key => $header_data ) {
												printf('<th class="%s"><span>%s</span></th>', $header_data['class'], $header_data['title']);
										}
										?>
				</tr>
				</thead>
				<tbody>
								<?php
								$tbody = $wpo_wcpdf_templates->get_table_body( $wpo_wcpdf->export->template_type );
								if( sizeof( $tbody ) > 0 ) {
										foreach( $tbody as $item_id => $item_columns ) {
												$row_class = apply_filters( 'wpo_wcpdf_item_row_class', $item_id, $wpo_wcpdf->export->template_type, $wpo_wcpdf->export->order, $item_id );
												printf('<tr class="%s">', $row_class);
												foreach ($item_columns as $column_key => $column_data) {
														printf('<td class="%s"><span>%s</span></td>', $column_data['class'], $column_data['data']);
												}
												echo '</tr>';
										}
								}
								?>
				</tbody>
				<tfoot>
				<tr class="no-borders">
						<td colspan="3" class="no-borders">
								<div class="customer-notes">
										<?php if ($wpo_wcpdf->get_shipping_notes()) : ?>
												<h3><?php _e('Customer Notes', 'wpo_wcpdf'); ?></h3>
												<?php $wpo_wcpdf->shipping_notes(); ?>
										<?php endif; ?>
								</div>
						</td>
						<td class="no-borders" colspan="2">
								<table class="totals">
										<tfoot>
																				<?php
																				$totals = $wpo_wcpdf_templates->get_totals( $wpo_wcpdf->export->template_type );
																				if( sizeof( $totals ) > 0 ) {
																						foreach( $totals as $total_key => $total_data ) {
																								?>
												<tr class="<?php echo $total_data['class']; ?>">
													<th class="description"><span><?= $total_data['label'] == 'Mehrwertsteuer 19 %' ? 'MwSt. 19%' : $total_data['label']; ?></span></th>
													<td style="text-align:right" class="price"><span class="totals-price"><?php echo $total_data['value']; ?></span></td>
												</tr>
																								<?php
																						}
																				}
																				?>
										</tfoot>
								</table>
						</td>
				</tr>
				</tfoot>
		</table>

<?php do_action('wpo_wcpdf_after_order_details', $wpo_wcpdf->export->template_type, $wpo_wcpdf->export->order); ?>


<div class="foot" style="position:absolute; bottom:0; left:0;">
	<table class="footer container">

		<tr>
			<td class="footer-column-1">
				<div class="wrapper"><?php $wpo_wcpdf->extra_1(); ?></div>
			</td>
			<td class="footer-column-2">
				<div class="wrapper"><?php $wpo_wcpdf->extra_2(); ?></div>
			</td>
			<td class="footer-column-3">
				<div class="wrapper"><?php $wpo_wcpdf->extra_3(); ?></div>
			</td>
		</tr>
		<tr>
			<td colspan="3" class="footer-wide-row">
								<?php $wpo_wcpdf->footer(); ?>
			</td>
		</tr>
	</table>
</div><!-- #letter-footer -->