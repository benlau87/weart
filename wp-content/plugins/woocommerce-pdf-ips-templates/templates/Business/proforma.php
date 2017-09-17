<?php global $wpo_wcpdf, $wpo_wcpdf_pro, $wpo_wcpdf_templates; ?>
<?php do_action( 'wpo_wcpdf_before_document', $wpo_wcpdf->export->template_type, $wpo_wcpdf->export->order ); ?>
<table class="head container">
	<tr class="underline">
		<td class="shop-info">
			<div class="shop-address">
				<?php $wpo_wcpdf->shop_address(); ?>
			</div>
		</td>
		<td class="shop-info">
			<div class="shop-contact">
				<?php $wpo_wcpdf->extra_1(); ?>
			</div>
		</td>
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
	</tr>
</table>

<h1 class="document-type-label">
<?php if( $wpo_wcpdf->get_header_logo_id() ) echo apply_filters( 'wpo_wcpdf_proforma_title', __( 'Proforma Invoice', 'wpo_wcpdf_pro' ) ); ?>
</h1>

<?php do_action( 'wpo_wcpdf_after_document_label', $wpo_wcpdf->export->template_type, $wpo_wcpdf->export->order ); ?>

<table class="addresses">
	<tr>
		<td class="address billing-address">
			<!-- <h3><?php _e( 'Billing Address:', 'wpo_wcpdf' ); ?></h3> -->
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
	</tr>
</table>

<table class="order-data">
	<tr>
		<td class="numbers">
			<table>
				<?php do_action( 'wpo_wcpdf_before_order_data', $wpo_wcpdf->export->template_type, $wpo_wcpdf->export->order ); ?>
				<tr class="proforma-number">
					<th><?php _e( 'Proforma Invoice Number:', 'wpo_wcpdf_pro' ); ?></th>
					<td><?php $wpo_wcpdf_pro->number('proforma'); ?></td>
				</tr>
				<tr class="order-number">
					<th><?php _e( 'Order Number:', 'wpo_wcpdf' ); ?></th>
					<td><?php $wpo_wcpdf->order_number(); ?></td>
				</tr>
				<?php do_action( 'wpo_wcpdf_after_order_data', $wpo_wcpdf->export->template_type, $wpo_wcpdf->export->order ); ?>
			</table>
		</td>
		<td class="dates">
			<table>
				<?php if ( isset($wpo_wcpdf_pro->settings->pro_settings['proforma_date']) ) { ?>
				<tr class="proforma-date">
					<th><?php _e( 'Proforma Invoice Date:', 'wpo_wcpdf_pro' ); ?></th>
					<td><?php $wpo_wcpdf_pro->date('proforma'); ?></td>
				</tr>
				<?php } ?>
				<tr class="order-date">
					<th><?php _e( 'Order Date:', 'wpo_wcpdf' ); ?></th>
					<td><?php $wpo_wcpdf->order_date(); ?></td>
				</tr>
			</table>
		</td>
	</tr>
</table>

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
</table>

<table class="notes-totals">
	<tr>
		<td class="notes">
			<?php do_action( 'wpo_wcpdf_after_order_details', $wpo_wcpdf->export->template_type, $wpo_wcpdf->export->order ); ?>
			<?php do_action( 'wpo_wcpdf_before_customer_notes', $wpo_wcpdf->export->template_type, $wpo_wcpdf->export->order ); ?>
			<div class="customer-notes">
				<?php if ( $wpo_wcpdf->get_shipping_notes() ) : ?>
					<h3><?php _e( 'Customer Notes', 'wpo_wcpdf' ); ?></h3>
					<?php $wpo_wcpdf->shipping_notes(); ?>
				<?php endif; ?>
			</div>
			<?php do_action( 'wpo_wcpdf_after_customer_notes', $wpo_wcpdf->export->template_type, $wpo_wcpdf->export->order ); ?>
		</td>
		<td class="totals">
			<table class="totals-table">
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
			</table>
		</td>
	</tr>
</table>

<?php if ( $wpo_wcpdf->get_footer() ): ?>
<div id="footer">
	<?php $wpo_wcpdf->footer(); ?>
</div>
<?php endif; ?>
<?php do_action( 'wpo_wcpdf_after_document', $wpo_wcpdf->export->template_type, $wpo_wcpdf->export->order ); ?>