<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Get Started - step 1.
 *
 * acquire address information of artist
 *
 * @author		Benedikt Laufer
 * @package		WAA Get Started
 * @version		1.0.0
 */
require_once(waa_DIR . '/classes/get-started-modal.php');														
$getStarted = new waa_GetStarted();
?>
<div class='waa-settings-content waa-settings-content-step-1'>

<?php if ( isset($_POST['settings'])) : ?>
  <div class="waa-alert waa-alert-danger waa-panel-alert"><?php echo $getStarted->steps[$getStarted->current_step()]['error']; ?></div>
<?php endif; ?>
	<div class="waa-page-help">
		<p><?= __('Hier kannst du einstellen, auf welchem Weg du Auszahlungen von uns erhalten möchtest.', 'waa'); ?></p>
	</div>

	<form id="waa-address-handler-form" action="<?php echo esc_url( add_query_arg( 'step', 2 ) ); ?>" method="post" class="waa-settings-area waa-get-started">
			
		 <?php $methods = waa_withdraw_get_active_methods(); ?>
		 <label for="payment_method"><?= __('Zahlungsart auswählen:', 'waa'); ?> *</label>
		 
		 <script>
			 jQuery(document).ready(function($) {
					$('#payment_method').on('change', function() {
						$('.payment-field').hide();
						$('#payment-field-'+$(this).val()).show();
					});
			 });
		 </script>
						<select name="settings[method]" id="payment_method">
							<option><?= __('bevorzugte Zahlungsart wählen', 'waa'); ?></option>
                <?php foreach ( $methods as $method_key ) {
                    $method = waa_withdraw_get_method( $method_key );
                    ?>										
											<option value="<?php echo $method_key; ?>"><?php echo $method['title'] ?></option>											
								<?php } ?>
						</select>
						<br><br>
						  <?php foreach ( $methods as $method_key ) {
                    $method = waa_withdraw_get_method( $method_key );
                    ?>
										
                    <fieldset class="payment-field" style="display:none" id="payment-field-<?php echo $method_key; ?>">
                        <div class="waa-form-group">
                            <label class="waa-control-label" for="waa_setting"><?php echo $method['title'] ?> *</label><br>
                            
                                <?php if ( is_callable( $method['callback'] ) ) {
																		global $current_user;
																		$profile_info   = waa_get_store_info( $current_user->ID );
                                    call_user_func( $method['callback'], $profile_info );
                                } ?>
                      
                        </div>
                    </fieldset>
                <?php } ?>
								
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
				<li class="step step-1 current"><a href="#"><?= __('Auszahlung', 'waa'); ?></a></li>
				<li class="step step-2" ><a href="#"><?= __('Versand', 'waa'); ?></a></li>
				<li class="step step-3"><a href="#"><?= __('Los geht`s!', 'waa'); ?></a></li>
			</ol>
	</nav>

</div>