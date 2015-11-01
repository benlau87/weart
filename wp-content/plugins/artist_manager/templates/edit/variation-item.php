<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

		foreach ( $parent_data['attributes'] as $attribute ) {

				// Only deal with attributes that are variations
				if ( ! $attribute['is_variation'] ) {
						continue;
				}

				// Get current value for variation (if set)
				$variation_selected_value = isset( $variation_data[ 'attribute_' . sanitize_title( $attribute['name'] ) ][0] ) ? $variation_data[ 'attribute_' . sanitize_title( $attribute['name'] ) ][0] : '';

				// Name will be something like attribute_pa_color
				echo '<td><select name="attribute_' . sanitize_title( $attribute['name'] ) . '[' . $loop . ']" class="waa-w3 waa-form-control"><option value="">' . __( 'Any', 'waa' ) . ' ' . esc_html( wc_attribute_label( $attribute['name'] ) ) . '&hellip;</option>';

				// Get terms for attribute taxonomy or value if its a custom attribute
				if ( $attribute['is_taxonomy'] ) {
						$post_terms = wp_get_post_terms( $parent_data['id'], $attribute['name'] );
						foreach ( $post_terms as $term ) {
								echo '<option ' . selected( $variation_selected_value, $term->slug, false ) . ' value="' . esc_attr( $term->slug ) . '">' . apply_filters( 'woocommerce_variation_option_name', esc_html( $term->name ) ) . '</option>';
						}

				} else {

						$options = array_map( 'trim', explode( WC_DELIMITER, $attribute['value'] ) );

						foreach ( $options as $option ) {
								echo '<option ' . selected( sanitize_title( $variation_selected_value ), sanitize_title( $option ), false ) . ' value="' . esc_attr( sanitize_title( $option ) ) . '">' . esc_html( apply_filters( 'woocommerce_variation_option_name', $option ) ) . '</option>';
						}

				}

				echo '</select></td>';
		}
?>

    <input type="hidden" name="variable_post_id[<?php echo $loop; ?>]" value="<?php echo esc_attr( $variation_id ); ?>" />
		<input type="hidden" name="variable_enabled[<?php echo $loop; ?>]" value="yes">
    <input type="hidden" class="variation_menu_order" name="variation_menu_order[<?php echo $loop; ?>]" value="<?php echo $loop; ?>" />
		<input type="hidden" name="variable_sku[<?php echo $loop; ?>]" value="<?php if ( isset( $_sku ) ) echo esc_attr( $_sku ); ?>" />
</td>
<td class="waa-input-group" >
		<span class="waa-input-group-addon">€</span>
    <input type="number" min="0" step="any" size="5" name="variable_regular_price[<?php echo $loop; ?>]" value="<?php if ( isset( $_regular_price ) ) echo esc_attr( $_regular_price ); ?>" class="waa-form-control" placeholder="<?php _e( '0.00', 'waa' ); ?>" size="10"/>
</td>

        

</td>
<td style="width:25% !important;">
    <!-- <a href="#variation-edit-popup" class="waa-btn waa-btn-theme edit_variation"><i class="fa fa-pencil"></i></a> -->
    <a class="waa-btn waa-btn-theme btn-remove-print" data-variation_id=<?php echo $variation_id; ?>><i class="fa fa-trash-o"></i></a>
</td>














