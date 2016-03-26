<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Get Started - step 2.
 *
 * acquire payment information of artist
 *
 * @author		Benedikt Laufer
 * @package		WAA Get Started
 * @version		1.0.0
 */
require_once(waa_DIR . '/classes/get-started-modal.php');														
$getStarted = new waa_GetStarted();
$user_id = get_current_user_id();
$dps_pt = isset( $_POST['dps_pt'] ) ? $_POST['dps_pt'] : '';
$dps_enable_pickup = isset( $_POST['dps_enable_pickup'] ) ? $_POST['dps_enable_pickup'] : '';
$dps_home_country = isset( $_POST['dps_home_country'] ) ? $_POST['dps_home_country'] : '';
$dps_eu_countries = isset( $_POST['dps_eu_countries'] ) ? $_POST['dps_eu_countries'] : '';
$dps_switzerland = isset( $_POST['dps_switzerland'] ) ? $_POST['dps_switzerland'] : '';
?>


<div class='waa-settings-content waa-settings-content-step-2'>

<?php if ( isset($_POST['waa_payment'])) : ?>
  <div class="waa-alert waa-alert-danger waa-panel-alert"><?php echo $getStarted->steps[$getStarted->current_step()]['error']; ?></div>
<?php endif; ?>

	<div class="waa-page-help">
			<p><?= __('Hier kannst du deine Liefer- und Versandbedingungen- und Kosten, sowie dein Rückgaberecht einstellen.', 'waa'); ?></p>
	</div>

	<form id="waa-address-handler-form" action="<?php echo esc_url( add_query_arg( 'step', 3 ) ); ?>" method="post" class="waa-settings-area waa-get-started">
<div class="waa-form-group">
		<label for="dps_enable_pickup"><?php _e('Selbstabholung erlauben', 'waa'); ?> <span class="waa-tooltips-help tips"
						title="<?php esc_attr_e('Biete deinen Kunden an, das Kunstwerk bei dir persönlich abzuholen.', 'waa'); ?>">
					<i class="ui ui-question-circle"></i>
			</span>
         </label>
				 <br>
		 <input type="checkbox" id="dps_enable_pickup" name="dps_enable_pickup"
										 value="yes" <?php checked('yes', $dps_enable_pickup, true); ?>> <?php _e('Selbstabholung erlauben', 'waa'); ?>
		</div> 
         
	 
		<div class="waa-form-group">
				<label for="dps_pt">
						<?php _e('Processing Time', 'waa'); ?> *
						<span class="waa-tooltips-help tips"
									title="<?php esc_attr_e('The time required before sending the product for delivery', 'waa'); ?>">
								<i class="ui ui-question-circle"></i>
						</span>
				</label>
				<br>
						<select name="dps_pt" id="dps_pt" class="waa-form-control" required>
								<?php
								$processing_time = waa_get_shipping_processing_times();
								foreach ($processing_time as $processing_key => $processing_value): ?>
										<option
												value="<?php echo $processing_key; ?>" <?php selected($dps_pt, $processing_key); ?>><?php echo $processing_value; ?></option>
								<?php endforeach ?>
						</select>
		</div>

	<div class="waa-form-group">
			<label for="_dps_ship_policy">
					<?php _e('Shipping Policy', 'waa'); ?>
					<span class="waa-tooltips-help tips"
								title="<?php _e('Write your terms, conditions and instructions about shipping', 'waa'); ?>">
							<i class="ui ui-question-circle"></i>
					</span>
			</label>
			<br>
					<textarea name="dps_ship_policy" id=""
										class="waa-form-control"><? isset( $_POST['dps_ship_policy'] ) ? $_POST['dps_ship_policy'] : ''; ?></textarea>
	</div>

	<div class="waa-form-group">
			<label for="_dps_refund_policy">
					<?php _e('Refund Policy', 'waa'); ?>
					<span class="waa-tooltips-help tips"
								title="<?php _e('Write your terms, conditions and instructions about refund', 'waa'); ?>">
							<i class="ui ui-question-circle"></i>
					</span>
			</label>
			<br>
					<textarea name="dps_refund_policy" id=""
										class="waa-form-control"><? isset( $_POST['dps_refund_policy'] ) ? $_POST['dps_refund_policy'] : ''; ?></textarea>
	</div>

	<div class="waa-form-group">
			<label for="dps_form_location">
					<?php _e('Ships from:', 'waa'); ?> *
					<span class="waa-tooltips-help tips"
								title="<?php _e('The place you send the products for delivery. Most of the time it as store location', 'waa'); ?>">
							<i class="ui ui-question-circle"></i>
					</span>
			</label>
			<br>
					<select name="dps_form_location" class="waa-form-control" required>
							<?php 
							$country_obj = new WC_Countries();
							$countries = $country_obj->countries;
							$dps_form_location = get_user_meta($user_id, '_dps_form_location', true);
							$dps_form_location = $dps_form_location ? $dps_form_location : $address_country;
							waa_country_dropdown($countries, $dps_form_location); ?>
					</select>
	</div>
	

	<div class="waa-form-group">

			<div class="waa-w12 dps-main-wrapper">
					<div class="waa-shipping-location-wrapper">

							<p class="waa-page-help"><?php _e('Add the countries you deliver your products to. You can specify states as well. If the shipping price is same except some countries/states, there is an option <strong>Everywhere Else</strong>, you can use that.', 'waa') ?></p>
							
							<div class="dps-shipping-location-content">
									<table class="dps-shipping-table">
											<tbody>
											<tr class="dps-shipping-location">
													<td colspan="3" width="100%" class="text-center">
															<?php _e('Ship to', 'waa'); ?> *
																			<span class="waa-tooltips-help tips"
																						title="<?php _e('The country you ship to', 'waa'); ?>">
																	<i class="ui ui-question-circle"></i></span></label>
													</td>
											</tr>
											<tr>
													<td class="col-md-4 text-center">
															<div class="checkbox">
																	<label for="home_country">
																			<input type="checkbox" name="dps_country_to[]" value="<?= $_POST['waa_address']['country'] ?>" id="home_country"
																						 class="waa-form-control dps_country_selection"
																						 id="home_country" <?php checked('yes', $dps_home_country, true);  ?>>
																			<?= __('Heimatland', 'waa'); ?> (<?= $_POST['waa_address']['country'] ?>)
																	</label>
															</div>
													</td>
													<td class="col-md-4">
															<div class="checkbox">
																	<label for="eu_countries">
																			<input type="checkbox" name="dps_country_to[]" value="everywhere" id="eu_countries"
																						 class="waa-form-control dps_country_selection" <?php checked('yes', $dps_eu_countries, true); ?>>
																			<?= __('EU', 'waa'); ?>
																	</label>
															</div>

													</td>
													<?php if(get_user_meta($user_id, '_dps_form_location', true) != 'CH') : ?>
													<td class="col-md-4">
															<div class="checkbox">
																	<label for="switzerland">
																			<input type="checkbox" name="dps_country_to[]" value="CH" id="switzerland"
																						 class="waa-form-control dps_country_selection" <?php checked('yes', $dps_switzerland, true); ?>>
																			<?= __('Schweiz', 'waa'); ?>
																	</label>
															</div>
													</td>
													<?php endif; ?>
											</tr>
											</tbody>
									</table>
							</div>
					</div>
			</div>
	</div>
	
		<br>
	<input type="submit" class="waa-btn waa-right waa-btn-danger waa-btn-theme" value="<?= __('weiter', 'waa'); ?>">
	
	
	
	<?php
			// Keep existing post values
			foreach ( $_POST as $key => $val ) :
				if ( is_array( $val ) ) :
					foreach( $val as $inner_key => $inner_val ) :
						if ( is_array( $inner_val ) ) :
							foreach( $inner_val as $inner_inner_key => $inner_inner_val ) :
								if ( !empty ( $inner_inner_val ) ) :
						?><input type="hidden" name="<?php echo esc_attr( $key ); ?>[<?php echo esc_attr( $inner_key); ?>][<?php echo esc_attr( $inner_inner_key ); ?>]" value="<?php echo esc_attr( $inner_inner_val ); ?>" /><?php
								endif;
							endforeach;
					else:
					?><input type="hidden" name="<?php echo esc_attr( $key ); ?>[<?php echo esc_attr( $inner_key); ?>]" value="<?php echo esc_attr( $inner_val ); ?>" /><?php
					endif;
					endforeach;
				else :
					?><input type="hidden" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $val ); ?>" /><?php
				endif;

			endforeach;
		?>
	</form>

	<div class="clear"></div>
		<nav>
			<ol class="cd-multi-steps text-bottom count">
				<li class="step step-0 visited"><a href='<?php echo esc_url( remove_query_arg( 'step' ) ); ?>'><?= __('Adresse', 'waa'); ?></a></li>
				<li class="step step-1 visited"><a href="<?php echo esc_url( add_query_arg( 'step', 1 ) ); ?>"><?= __('Auszahlung', 'waa'); ?></a></li>
				<li class="step step-2 current"><a href="#"><?= __('Versand', 'waa'); ?></a></li>
				<li class="step step-3"><a href="#"><?= __('Los geht`s!', 'waa'); ?></a></li>
			</ol>
	</nav>

</div>