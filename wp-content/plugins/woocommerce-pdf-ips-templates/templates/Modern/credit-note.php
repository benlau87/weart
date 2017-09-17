<?php global $wpo_wcpdf, $wpo_wcpdf_pro, $wpo_wcpdf_templates; ?>
<?php do_action( 'wpo_wcpdf_before_document', $wpo_wcpdf->export->template_type, $wpo_wcpdf->export->order ); ?>
<table class="head container">
	<tr class="underline">
		<td class="header">
			<div class="header-stretcher">
			<?php
			if ($wpo_wcpdf->get_header_logo_id()) {
				$wpo_wcpdf->header_logo();
			} else {
				$wpo_wcpdf->shop_name();
			}
			?>
			</div>
		</td>
		<td class="shop-info">
			<div class="shop-address"><?php $wpo_wcpdf->shop_address(); ?></div>
		</td>
	</tr>
</table>

<table class="order-data-addresses">
	<tr>
		<td class="address billing-address">
			<h3>&nbsp;<!-- empty spacer to keep adjecent cell content aligned --></h3>
			<?php $wpo_wcpdf->billing_address(); ?>
			<?php if ( isset($wpo_wcpdf->settings->template_settings['invoice_email']) ) { ?>
			<div class="billing-email"><?php $wpo_wcpdf->billing_email(); ?></div>
			<?php } ?>
			<?php if ( isset($wpo_wcpdf->settings->template_settings['invoice_phone']) ) { ?>
			<div class="billing-phone"><?php $wpo_wcpdf->billing_phone(); ?></div>
			<?php } ?>
		</td>
		<td class="address shipping-address">
			<?php if ( isset($wpo_wcpdf->settings->template_settings['invoice_shipping_address']) && $wpo_wcpdf->ships_to_different_address()) { ?>
			<h3><?php _e( 'Ship To:', 'wpo_wcpdf' ); ?></h3>
			<?php $wpo_wcpdf->shipping_address(); ?>
			<?php } ?>
		</td>
		<td class="order-data">
			<h3 class="document-type-label">
			<?php echo apply_filters( 'wpo_wcpdf_credit_note_title', __( 'Credit Note', 'wpo_wcpdf_pro' ) ); ?>
			</h3>
			<?php do_action( 'wpo_wcpdf_after_document_label', $wpo_wcpdf->export->template_type, $wpo_wcpdf->export->order ); ?>
			<table>
				<?php do_action( 'wpo_wcpdf_before_order_data', $wpo_wcpdf->export->template_type, $wpo_wcpdf->export->order ); ?>
				<tr class="credit-note-number">
					<th><?php _e( 'Credit Note Number:', 'wpo_wcpdf_pro' ); ?></th>
					<td><?php $wpo_wcpdf_pro->number('credit-note'); ?></td>
				</tr>
				<?php if ( isset($wpo_wcpdf_pro->settings->pro_settings['credit_note_date']) ) { ?>
				<tr class="credit-note-date">
					<th><?php _e( 'Credit Note Date:', 'wpo_wcpdf_pro' ); ?></th>
					<td><?php $wpo_wcpdf_pro->date('credit-note'); ?></td>
				</tr>
				<?php } ?>
				<tr class="order-number">
					<th><?php _e( 'Order Number:', 'wpo_wcpdf' ); ?></th>
					<td><?php $wpo_wcpdf->order_number(); ?></td>
				</tr>
				<tr class="order-date">
					<th><?php _e( 'Order Date:', 'wpo_wcpdf' ); ?></th>
					<td><?php $wpo_wcpdf->order_date(); ?></td>
				</tr>
				<?php do_action( 'wpo_wcpdf_after_order_data', $wpo_wcpdf->export->template_type, $wpo_wcpdf->export->order ); ?>
			</table>
		</td>
	</tr>
</table><!-- head container -->

<?php do_action( 'wpo_wcpdf_before_order_details', $wpo_wcpdf->export->template_type, $wpo_wcpdf->export->order ); ?>

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
</table><!-- order-details -->

<?php do_action( 'wpo_wcpdf_after_order_details', $wpo_wcpdf->export->template_type, $wpo_wcpdf->export->order ); ?>

<?php do_action( 'wpo_wcpdf_before_customer_notes', $wpo_wcpdf->export->template_type, $wpo_wcpdf->export->order ); ?>

<?php if ( $wpo_wcpdf->get_shipping_notes() ) : ?>
<div class="notes customer-notes reason-for-refund">
	<h3><?php _e( 'Reason for refund', 'wpo_wcpdf_pro' ); ?></h3>
	<?php $wpo_wcpdf->shipping_notes(); ?>
</div>
<?php endif; ?>

<?php do_action( 'wpo_wcpdf_after_customer_notes', $wpo_wcpdf->export->template_type, $wpo_wcpdf->export->order ); ?>

<div class="foot">
	<table class="footer container">
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>
				<table class="totals">
					<tfoot>
						<?php
						$totals = $wpo_wcpdf_templates->get_totals( $wpo_wcpdf->export->template_type );
						if( sizeof( $totals ) > 0 ) {
							foreach( $totals as $total_key => $total_data ) {
								?>
								<tr class="<?php echo $total_data['class']; ?>">
									<th class="description"><span><?php echo $total_data['label']; ?></span></th>
									<td class="price"><span class="totals-price"><?php echo $total_data['value']; ?></span></td>
								</tr>
								<?php
							}
						}
						?>
					</tfoot>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="3" class="bluebox">
				<span class="shipping-method-label"><?php _e( 'Shipping method', 'wpo_wcpdf' ); ?>:</span><span class="shipping-method"><?php $wpo_wcpdf->shipping_method(); ?></span><br/>
			</td>
		</tr>
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
<?php do_action( 'wpo_wcpdf_after_document', $wpo_wcpdf->export->template_type, $wpo_wcpdf->export->order ); ?>