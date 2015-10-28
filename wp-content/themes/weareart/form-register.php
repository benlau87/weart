<?php
function app_output_buffer() {
  ob_start();
} // soi_output_buffer
add_action('init', 'app_output_buffer');

// user registration login form
function waa_registration_form( $atts ) {
	
	// get atts 
	$atts = shortcode_atts(
		array(
			'type' => 'user',
		), $atts, 'register_form' );
 
	// only show the registration form to non-logged-in members
	if(!is_user_logged_in()) {
 
		global $waa_load_css;
 
		// set this to true so the CSS is loaded
		$waa_load_css = true;
 
		// check to make sure user registration is enabled
		$registration_enabled = get_option('users_can_register');
 
		// only show the registration form if allowed
		if($registration_enabled) {
			if($atts['type'] == "artist") {
				$output = waa_registration_form_fields('artist');
			} else {
				$output = waa_registration_form_fields('user');
			}
		} else {
			$output = __('Die Registrierung ist momentan geschlossen.', 'waa');
		}
		return $output;
	} else {
		wp_redirect( home_url() );
		exit;
	}
}
add_shortcode('register_form', 'waa_registration_form');

// user login form
function waa_login_form() {
 
	if(!is_user_logged_in()) {
 
		global $waa_load_css;
 
		// set this to true so the CSS is loaded
		$waa_load_css = true;
 
		$output = waa_login_form_fields();
	} else {
		// could show some logged in user info here
		// $output = 'user info here';
	}
	return $output;
}
add_shortcode('login_form', 'waa_login_form');


// registration form fields
function waa_registration_form_fields($type) {
 
	ob_start();
	// show any error messages after form submission
	waa_show_error_messages(); ?>
 
		<h3 class="waa_header"><?= ($type == "artist" ? __('Kostenlos als Künstler registrieren', 'waa') : __('Kostenloses Konto erstellen', 'waa')); ?></h3>
		
		<div class="info-box danger waa_js_errors">
			<h4><?= __('Bitte füllen Sie alle Felder korrekt aus.', 'waa') . '</h4>'; ?>
			<span class="error"><strong><?= __('Fehler', 'waa'); ?></strong>: <span class="error-msg"></span></span><br/>
		</div>
		
		<form id="waa_registration_form" class="waa_form" action="" method="POST">
			<fieldset>
				<?php if ($type == "artist" ) : ?>
				<input type="hidden" name="registration_type" value="artist" />
				<div class="form-group">
					<label for="waa_user_first"><?php _e('Vorname', 'waa'); ?></label>
					<input name="waa_user_first" id="waa_user_first" class="required form-control" type="text" value="<?= $_POST['waa_user_first']; ?>" />
				</div>
				<div class="form-group">
					<label for="waa_user_last"><?php _e('Nachname', 'waa'); ?></label>
					<input name="waa_user_last" id="waa_user_last" class="required form-control" type="text" value="<?= $_POST['waa_user_last']; ?>" />
				</div>
				<?php endif; ?>
				<div class="form-group">
					<label for="waa_user_email"><?php _e('E-Mail', 'waa'); ?></label>
					<input name="<?= ($type == "artist" ? 'waa_user_email' : 'waa_user_login'); ?>" id="waa_user_email" class="required form-control" type="email" value="
					<?= ($type == "artist" ? $_POST['waa_user_email'] : $_POST['waa_user_login']); ?>" />
				</div>
				<?php if ($type == "artist" ) : ?>
				<div class="form-group">
					<label for="waa_user_month"><?php _e('Geburtsdatum', 'waa'); ?></label>
					<div class="row">
						<div class="col-md-4">
							<select id="waa_user_month" name="waa_user_month" class="form-control required">
								<option><?php _e('Monat', 'waa'); ?></option>
								<option value="01"<?= ($_POST['waa_user_month'] == '01' ? ' selected' : '') ?>><?php _e('Januar', 'waa'); ?></option>
								<option value="02"<?= ($_POST['waa_user_month'] == '02' ? ' selected' : '') ?>><?php _e('Februar', 'waa'); ?></option>
								<option value="03"<?= ($_POST['waa_user_month'] == '03' ? ' selected' : '') ?>><?php _e('März', 'waa'); ?></option>
								<option value="04"<?= ($_POST['waa_user_month'] == '04' ? ' selected' : '') ?>><?php _e('April', 'waa'); ?></option>
								<option value="05"<?= ($_POST['waa_user_month'] == '05' ? ' selected' : '') ?>><?php _e('Mai', 'waa'); ?></option>
								<option value="06"<?= ($_POST['waa_user_month'] == '06' ? ' selected' : '') ?>><?php _e('Juni', 'waa'); ?></option>
								<option value="07"<?= ($_POST['waa_user_month'] == '07' ? ' selected' : '') ?>><?php _e('Juli', 'waa'); ?></option>
								<option value="08"<?= ($_POST['waa_user_month'] == '08' ? ' selected' : '') ?>><?php _e('August', 'waa'); ?></option>
								<option value="09"<?= ($_POST['waa_user_month'] == '09' ? ' selected' : '') ?>><?php _e('September', 'waa'); ?></option>
								<option value="10"<?= ($_POST['waa_user_month'] == '10' ? ' selected' : '') ?>><?php _e('Oktober', 'waa'); ?></option>
								<option value="11"<?= ($_POST['waa_user_month'] == '11' ? ' selected' : '') ?>><?php _e('November', 'waa'); ?></option>
								<option value="12"<?= ($_POST['waa_user_month'] == '12' ? ' selected' : '') ?>><?php _e('Dezember', 'waa'); ?></option>
							</select>
						</div>
						<div class="col-md-4">
							<select id="waa_user_day" name="waa_user_day" class="form-control required" autocomplete="off">
								<option><?php _e('Tag', 'waa'); ?></option>
								<option value="01"<?= ($_POST['waa_user_month'] == '01' ? ' selected' : '') ?>>1</option><option value="02"<?= ($_POST['waa_user_month'] == '02' ? ' selected' : '') ?>>2</option><option value="03"<?= ($_POST['waa_user_month'] == '03' ? ' selected' : '') ?>>3</option><option value="04"<?= ($_POST['waa_user_month'] == '04' ? ' selected' : '') ?>>4</option><option value="05"<?= ($_POST['waa_user_month'] == '05' ? ' selected' : '') ?>>5</option><option value="06"<?= ($_POST['waa_user_month'] == '06' ? ' selected' : '') ?>>6</option><option value="07"<?= ($_POST['waa_user_month'] == '07' ? ' selected' : '') ?>>7</option><option value="08"<?= ($_POST['waa_user_month'] == '08' ? ' selected' : '') ?>>8</option><option value="09"<?= ($_POST['waa_user_month'] == '09' ? ' selected' : '') ?>>9</option><option value="10"<?= ($_POST['waa_user_month'] == '10' ? ' selected' : '') ?>>10</option><option value="11"<?= ($_POST['waa_user_month'] == '11' ? ' selected' : '') ?>>11</option><option value="12"<?= ($_POST['waa_user_month'] == '12' ? ' selected' : '') ?>>12</option><option value="13"<?= ($_POST['waa_user_month'] == '13' ? ' selected' : '') ?>>13</option><option value="14"<?= ($_POST['waa_user_month'] == '14' ? ' selected' : '') ?>>14</option><option value="15"<?= ($_POST['waa_user_month'] == '15' ? ' selected' : '') ?>>15</option><option value="16"<?= ($_POST['waa_user_month'] == '16' ? ' selected' : '') ?>>16</option><option value="17"<?= ($_POST['waa_user_month'] == '17' ? ' selected' : '') ?>>17</option><option value="18"<?= ($_POST['waa_user_month'] == '18' ? ' selected' : '') ?>>18</option><option value="19"<?= ($_POST['waa_user_month'] == '19' ? ' selected' : '') ?>>19</option><option value="20"<?= ($_POST['waa_user_month'] == '20' ? ' selected' : '') ?>>20</option><option value="21<?= ($_POST['waa_user_month'] == '21' ? ' selected' : '') ?>">21</option><option value="22"<?= ($_POST['waa_user_month'] == '22' ? ' selected' : '') ?>>22</option><option value="23"<?= ($_POST['waa_user_month'] == '23' ? ' selected' : '') ?>>23</option><option value="24"<?= ($_POST['waa_user_month'] == '24' ? ' selected' : '') ?>>24</option><option value="25"<?= ($_POST['waa_user_month'] == '25' ? ' selected' : '') ?>>25</option><option value="26"<?= ($_POST['waa_user_month'] == '26' ? ' selected' : '') ?>>26</option><option value="27"<?= ($_POST['waa_user_month'] == '27' ? ' selected' : '') ?>>27</option><option value="28"<?= ($_POST['waa_user_month'] == '28' ? ' selected' : '') ?>>28</option><option value="29"<?= ($_POST['waa_user_month'] == '29' ? ' selected' : '') ?>>29</option><option value="30"<?= ($_POST['waa_user_month'] == '30' ? ' selected' : '') ?>>30</option><option value="31"<?= ($_POST['waa_user_month'] == '31' ? ' selected' : '') ?>>31</option>
							</select>
						</div>
						<div class="col-md-4">
								<input name="waa_user_year" id="waa_user_year" placeholder="Year" class="required form-control" type="text" value="<?= $_POST['waa_user_year']; ?>" />
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="waa_user_country"><?php _e('Land', 'waa'); ?></label>
					<?php							
						global $woocommerce;
						$countries_obj   = new WC_Countries();
						$countries   = $countries_obj->get_allowed_countries();
					
						woocommerce_form_field('waa_user_country', array(
						'type'       => 'select',
						'input_class'      => array( 'required form-control' ),
						'options'    => $countries
						)
						);
					?>
				</div>
				<div class="form-group" id="region-select-container">
					<label for="waa_user_region"><?php _e('Region wählen', 'waa'); ?></label>
					<p class="form-row" id="waa_user_region_field"></p>
				</div>
				<div class="form-group register_form_user">
					<label for="waa_user_login"><?php _e('Benutzername', 'waa'); ?></label>
					<input name="waa_user_login" id="waa_user_login" class="required form-control" type="text" value="<?= $_POST['waa_user_login']; ?>"  data-toggle="tooltip" data-placement="top" title="keine Sonderzeichen oder Leerzeichen erlaubt" />
					<div class="help-block text-muted"><?php _e('Wichtig: Der Benutzername darf keine Sonderzeichen oder Leerzeichen beinhalten. Ihr Benutzername wir für Ihr Benutzerprofil genutzt, z.B. www.weart.com/kuenstler/ihr-name', 'waa'); ?></div>
				</div>
				<?php endif; ?>
				<div class="form-group">
					<label for="waa_user_pass"><?php _e('Passwort', 'waa'); ?></label>
					<input name="waa_user_pass" id="waa_user_pass" class="required form-control" type="password" value="<?= $_POST['waa_user_pass']; ?>"/>
					<div class="help-block text-muted"><?= __('Muss aus min. 6 Zeichen bestehen.', 'waa'); ?></div>
				</div>
				<div class="form-group">
					<label for="waa_user_pass_confirm"><?php _e('Passwort wiederholen', 'waa'); ?></label>
					<input name="waa_user_pass_confirm" id="waa_user_pass_confirm" class="required form-control" type="password"  value="<?= $_POST['waa_user_pass_confirm']; ?>" />
				</div>
				<div class="checkbox">
					<label>
						<input name="waa_user_legal" id="waa_user_legal" type="checkbox" value="1" data-error-msg="<?php _e('Bitte akzeptieren Sie unsere Allgemeinen Geschäftsbedingungen.', 'waa') ?>" class="required"<?= ($_POST['waa_user_legal'] == '1' ? ' checked="checked"' : '') ?>> <?php _e('AGBs akzeptieren.', 'waa'); ?>
					</label>
				</div>
				<div class="form-group">
					<input type="hidden" name="waa_register_nonce" value="<?php echo wp_create_nonce('waa-register-nonce'); ?>"/>
					<input type="submit" class="btn btn-default" value="<?php _e('Kostenlos registrieren', 'waa'); ?>"/>
				</div>
			</fieldset>
		</form>
		
		<script>
		jQuery(document).ready(function($) {
			$('#waa_user_login').bind('keypress', function (event) {
					var regex = new RegExp("^[a-zA-Z0-9_-]+$");
					var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
					if (!regex.test(key)) {
						 event.preventDefault();
						 $('.tooltip .tooltip-inner').css('background-color', 'red');
						 $('.tooltip.top .tooltip-arrow').css('border-top-color', 'red');
						 return false;
					}
			});

			$('#region-select-container').hide();
			$('#waa_user_country').change(function() {
				var selected_country = $(this).val();
				
				if(selected_country) {
					// get regions for selected country
					if(selected_country == "CH")
						$('#region-select-container > label').html('Kanton wählen');
					$.ajax({
						url: "<?= get_template_directory_uri(); ?>/js/"+selected_country+".regions.json",
						dataType: 'json',
						success: function( data ) {

						var items = [];
						$.each( data, function( code, region ) {
							items.push( "<option value='" + region + "'>" + region + "</option>" );
						});
					 
						var select = $( "<select/>", {
							"id": "waa_user_region",
							"name": "waa_user_region",
							html: items.join( "" )
						});
						$( "#waa_user_region_field" ).html(select);
						
						// show regions-select 
						$('#region-select-container').show();
						},
						error: function ( data ) {
							$('#region-select-container').hide();
							alert('Noch keine Regionen für '+selected_country+' hinterlegt.');
						}
					});
				}					
			});
			$('[data-toggle="tooltip"]').tooltip();
			$('#waa_registration_form input[type="submit"]').on('click', function(e) {
				$('#waa_registration_form .required' ).each(function() {
					if(!$(this).val()) {
						$(this).parent().addClass("has-error");
						e.preventDefault();
						window.scrollTo(0,0);
						if($(this).attr('data-error-msg'))
							$('.waa_js_errors .error-msg').text($(this).attr('data-error-msg'));
						else 
							$('.waa_js_errors .error-msg').text('<?php _e('Bitte füllen Sie alle markierten Felder korrekt aus.', 'waa'); ?>');
						$('.waa_js_errors').show();
					}
					$(this).on('change keyup', function () {
						if($(this).val()) {
							$(this).parent().removeClass("has-error");
						}
					});
				});
				$('#waa_registration_form input[type="checkbox"].required').each(function() {
					if(!$(this).is(':checked')) {
						$(this).parent().parent().addClass("has-error");
						e.preventDefault();
						window.scrollTo(0,0);
					}
					$(this).on('change', function () {
						if($(this).is(':checked')) {
							$(this).parent().parent().removeClass("has-error");
						}
					});
				});	
			});
			$('#password, #password_again').on('change keyup', function () {
				if($('#password').val() === $('#password_again').val() && $('#password').val().length > 5) {
						$("#waa_registration_form input[type='submit']").removeAttr("disabled").removeClass('disabled');	
						$("#waa_registration_form input[type='submit']").removeAttr("data-toggle");					
						$("#waa_registration_form input[type='submit']").removeAttr("title");					
				} else {
						$("#waa_registration_form input[type='submit']").attr("disabled", "disabled").addClass('disabled');	
						$("#waa_registration_form input[type='submit']").attr({"data-toggle": "tooltip", "data-placement": "top", "title": "<?php _e('Bitte stellen Sie sicher, dass die eingegebenen Passwörter übereinstimmen und min. 6 Zeichen lang sind.', 'waa'); ?>" });	
					
				}
			});
		});
		</script>
	<?php
	return ob_get_clean();
}

// login form fields
function waa_login_form_fields() {
 
	ob_start(); ?>
		<h3 class="waa_header"><?php _e('Login'); ?></h3>
 
		<?php
		// show any error messages after form submission
		waa_show_error_messages(); ?>
 
		<form id="waa_login_form"  class="waa_form"action="" method="post">
			<fieldset>
				<p>
					<label for="waa_user_Login">Username</label>
					<input name="waa_user_login" id="waa_user_login" class="required" type="text" value="<?= $_POST['waa_user_login']; ?>" />
				</p>
				<p>
					<label for="waa_user_pass">Password</label>
					<input name="waa_user_pass" id="waa_user_pass" class="required" type="password" value="<?= $_POST['waa_user_pass']; ?>" />
				</p>
				<p>
					<input type="hidden" name="waa_login_nonce" value="<?php echo wp_create_nonce('waa-login-nonce'); ?>"/>
					<input id="waa_login_submit" type="submit" value="Login"/>
				</p>
			</fieldset>
		</form>
	<?php
	return ob_get_clean();
}

// logs a member in after submitting a form
function waa_login_member() {
 
	if(isset($_POST['waa_user_login']) && wp_verify_nonce($_POST['waa_login_nonce'], 'waa-login-nonce')) {
 
		// this returns the user ID and other info from the user name
		$user = get_userdatabylogin($_POST['waa_user_login']);
 
		if(!$user) {
			// if the user name doesn't exist
			waa_errors()->add('empty_username', __('Invalid username'));
		}
 
		if(!isset($_POST['waa_user_pass']) || $_POST['waa_user_pass'] == '') {
			// if no password was entered
			waa_errors()->add('empty_password', __('Please enter a password'));
		}
 
		// check the user's login with their password
		if(!wp_check_password($_POST['waa_user_pass'], $user->user_pass, $user->ID)) {
			// if the password is incorrect for the specified user
			waa_errors()->add('empty_password', __('Incorrect password'));
		}
 
		// retrieve all error messages
		$errors = waa_errors()->get_error_messages();
 
		// only log the user in if there are no errors
		if(empty($errors)) {
 
			wp_setcookie($_POST['waa_user_login'], $_POST['waa_user_pass'], true);
			wp_set_current_user($user->ID, $_POST['waa_user_login']);	
			do_action('wp_login', $_POST['waa_user_login']);
 
			wp_redirect(get_permalink( get_option('woocommerce_myaccount_page_id') )); exit;
		}
	}
}
add_action('init', 'waa_login_member');

// register a new user
function waa_add_new_member() {
  	if (isset( $_POST["waa_user_login"] ) && wp_verify_nonce($_POST['waa_register_nonce'], 'waa-register-nonce')) {
		$user_login		= $_POST["waa_user_login"];	
		$user_pass		= $_POST["waa_user_pass"];
		$pass_confirm 	= $_POST["waa_user_pass_confirm"];
		$user_legal 	= $_POST["waa_user_legal"];
		$registration_type = $_POST['registration_type'];
		if($registration_type == 'artist') {
			$user_email		= $_POST["waa_user_email"];
			$user_first 	= $_POST["waa_user_first"];
			$user_last	 	= $_POST["waa_user_last"];
			$user_birthday= $_POST["waa_user_year"] . '-' . $_POST["waa_user_month"] . '-' . $_POST["waa_user_day"];
			$user_country = $_POST["waa_user_country"];
			$user_region = $_POST["waa_user_region"];
		} else {
			$user_email		= $_POST["waa_user_login"];
		}
 
		// this is required for username checks
		require_once(ABSPATH . WPINC . '/registration.php');
 
		if(username_exists($user_login)) {
			// Username already registered
			waa_errors()->add('username_unavailable', __('Benutzername ist bereits vergeben.', 'waa'));
		}
		if(!validate_username($user_login)) {
			// invalid username
			waa_errors()->add('username_invalid', __('Ungültiger Benutzername.', 'waa'));
		}
		if($user_login == '') {
			// empty username
			waa_errors()->add('username_empty', __('Bitte geben Sie einen Benutzernamen ein.', 'waa'));
		}
		
		if($registration_type == 'artist') {
			if($user_first == '') {
				// empty firstname
				waa_errors()->add('firstname_empty', __('Bitte geben Sie Ihren Vornamen ein.', 'waa'));
			}
			if($user_last == '') {
				// empty lastname
				waa_errors()->add('lastname_empty', __('Bitte geben Sie Ihren Nachnamen ein.', 'waa'));
			}
			if($user_birthday == '') {
				// empty birthday
				waa_errors()->add('birthdate_empty', __('Bitte geben Sie Ihr Geburtsdatum ein.', 'waa'));
			}
			if($user_country == '') {
				// empty country
				waa_errors()->add('country_empty', __('Bitte wählen Sie ein Land aus.', 'waa'));
			}
			if($user_region == '') {
				// empty region
				waa_errors()->add('region_empty', __('Bitte wählen Sie eine Region aus.', 'waa'));
			}
			if(!is_email($user_email)) {
				//invalid email
				waa_errors()->add('email_invalid', __('Bitte geben Sie eine gültige E-Mail-Adresse ein..', 'waa'));
			}
			if(email_exists($user_email)) {
				//Email address already registered
				waa_errors()->add('email_used', __('Die angegebene E-Mail-Adresse wird bereits verwendet.', 'waa'));
			}
		}
		if($user_pass == '') {
			// passwords do not match
			waa_errors()->add('password_empty', __('Bitte geben Sie ein Passwort ein.', 'waa'));
		}
		if($user_pass != $pass_confirm) {
			// passwords do not match
			waa_errors()->add('password_mismatch', __('Bitte stellen Sie sicher, dass die eingegebenen Passwörter übereinstimmen und min. 6 Zeichen lang sind.', 'waa'));
		}
		if(!isset($user_legal)) {
			// passwords do not match
			waa_errors()->add('legal_empty', __('Bitte akzeptieren Sie die Allgemeinen Geschäftsbedingungen.'));
		}
 
		$errors = waa_errors()->get_error_messages();
 
		// only create the user in if there are no errors
		if(empty($errors)) {
 
			$new_user_id = wp_insert_user(array(
					'user_login'		=> $user_login,
					'user_pass'	 		=> $user_pass,
					'user_email'		=> $user_email,
					'first_name'		=> $user_first,
					'last_name'			=> $user_last,
					'user_registered'	=> date('Y-m-d H:i:s'),
					'role'				=> ($registration_type == 'artist' ? 'seller' : 'customer')
				)
			);
			if($new_user_id) {

				if($registration_type == 'artist') {
					
					add_user_meta( $new_user_id, 'artist_birthday', $user_birthday );
					add_user_meta( $new_user_id, 'artist_country', $user_country );
					add_user_meta( $new_user_id, 'artist_region', $user_region );
					
					// create region category, if it doesnt exist yet					
					if (!get_term_by('name', $user_region, 'product_cat')) {
					#	wp_insert_term(__($user_region, 'waa'), 'product_cat', array('description' => $user_country));
					}
					
					// send an email to the admin alerting them of the registration of an artist
					wp_new_user_notification($new_user_id, 'artist');
					
					// send the newly created artist to confirmation page
					wp_redirect(get_permalink( 171 ) ); exit;
					
				} else {
					
					// approve customers by default
					update_user_meta( $new_user_id, 'wp-approve-user-mail-sent', true );
					update_user_meta( $new_user_id, 'wp-approve-user', true );
					
					// log the new user in
					wp_setcookie($user_login, $user_pass, true);
					wp_set_current_user($new_user_id, $user_login);	
					do_action('wp_login', $user_login);
					// send confirmation email to the customer
					// @todo
					
					// send the newly created user to the my-account page after logging them in
					wp_redirect(get_permalink( get_option('woocommerce_myaccount_page_id') )); exit;
					
				}		
 
				
			}
 
		}
 
	}
}
add_action('init', 'waa_add_new_member');

// used for tracking error messages
function waa_errors(){
    static $wp_error; // Will hold global variable safely
    return isset($wp_error) ? $wp_error : ($wp_error = new WP_Error(null, null, null));
}

// displays error messages from form submissions
function waa_show_error_messages() {
	if($codes = waa_errors()->get_error_codes()) {
		echo '<div class="info-box danger waa_errors"><h4>'. __('Please enter all required field correctly.') .'</h4>';
		    // Loop error codes and display errors
		   foreach($codes as $code){
		        $message = waa_errors()->get_error_message($code);
		        echo '<span class="error"><strong>' . __('Error') . '</strong>: ' . $message . '</span><br/>';
		    }
		echo '</div>';
	}	
}
?>