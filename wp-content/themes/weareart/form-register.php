<?php
function app_output_buffer() {
  ob_start();
} // soi_output_buffer
add_action('init', 'app_output_buffer');

// user registration login form
function waa_registration_form() {
 
	// only show the registration form to non-logged-in members
	if(!is_user_logged_in()) {
 
		global $waa_load_css;
 
		// set this to true so the CSS is loaded
		$waa_load_css = true;
 
		// check to make sure user registration is enabled
		$registration_enabled = get_option('users_can_register');
 
		// only show the registration form if allowed
		if($registration_enabled) {
			$output = waa_registration_form_fields();
		} else {
			$output = __('User registration is not enabled');
		}
		return $output;
	} else {
		wp_redirect( home_url('/') . '/wp-admin' );
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
function waa_registration_form_fields() {
 
	ob_start();
	// show any error messages after form submission
	waa_show_error_messages(); ?>
 
		<h3 class="waa_header"><?php _e('Register New Account'); ?></h3>
		
		<div class="info-box danger waa_errors">
			<h4><?= __('Please enter all required field correctly.') . '</h4>'; ?>
			<span class="error"><strong><?= __('Error'); ?></strong>: <span class="error-msg"></span></span><br/>
		</div>
		
		<form id="waa_registration_form" class="waa_form" action="" method="POST">
			<fieldset>
				<div class="form-group">
					<label for="waa_user_first"><?php _e('First Name'); ?></label>
					<input name="waa_user_first" id="waa_user_first" class="required form-control" type="text"/>
				</div>
				<div class="form-group">
					<label for="waa_user_last"><?php _e('Last Name'); ?></label>
					<input name="waa_user_last" id="waa_user_last" class="required form-control" type="text"/>
				</div>
				<div class="form-group">
					<label for="waa_user_email"><?php _e('Email'); ?></label>
					<input name="waa_user_email" id="waa_user_email" class="required form-control" type="email"/>
				</div>
				<div class="form-group">
					<label for="waa_user_month"><?php _e('Birthday'); ?></label>
					<div class="row">
						<div class="col-md-4">
							<select id="waa_user_month" name="waa_user_month" class="form-control required">
								<option>Month</option>
								<option value="01">January</option>
								<option value="02">February</option>
								<option value="03">March</option>
								<option value="04">April</option>
								<option value="05">May</option>
								<option value="06">June</option>
								<option value="07">July</option>
								<option value="08">August</option>
								<option value="09">September</option>
								<option value="10">October</option>
								<option value="11">November</option>
								<option value="12">December</option>
							</select>
						</div>
						<div class="col-md-4">
							<select id="waa_user_day" name="waa_user_day" class="form-control required" autocomplete="off">
								<option>Day</option>
								<option value="01">1</option><option value="02">2</option><option value="03">3</option><option value="04">4</option><option value="05">5</option><option value="06">6</option><option value="07">7</option><option value="08">8</option><option value="09">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option>
							</select>
						</div>
						<div class="col-md-4">
								<input name="waa_user_year" id="waa_user_year" placeholder="Year" class="required form-control" type="text"/>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="waa_user_country"><?php _e('Country'); ?></label>
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
						
						// note: create city select with allowed cities
					?>
				</div>
				<div class="form-group">
					<label for="waa_user_login"><?php _e('Username'); ?></label>
					<input name="waa_user_login" id="waa_user_login" class="required form-control" type="text"/>
					<div class="help-block text-muted"><?= __('Important: The username should not contain any special characters. It will be used for your artist profile page, e.g. www.weart.com/artist/your-name'); ?></div>
				</div>
				<div class="form-group">
					<label for="password"><?php _e('Password'); ?></label>
					<input name="waa_user_pass" id="password" class="required form-control" type="password"/>
					<div class="help-block text-muted"><?= __('Must be at least 6 characters.'); ?></div>
				</div>
				<div class="form-group">
					<label for="password_again"><?php _e('Password Again'); ?></label>
					<input name="waa_user_pass_confirm" id="password_again" class="required form-control" type="password"/>
				</div>
				<div class="checkbox">
					<label>
						<input name="waa_user_legal" id="waa_user_legal" type="checkbox" class="required"> Check me out
					</label>
				</div>
				<div class="form-group">
					<input type="hidden" name="waa_register_nonce" value="<?php echo wp_create_nonce('waa-register-nonce'); ?>"/>
					<input type="submit" class="btn btn-default" value="<?php _e('Register Your Account'); ?>"/>
				</div>
			</fieldset>
		</form>
		
		<script>
		jQuery(document).ready(function($) {
			$('[data-toggle="tooltip"]').tooltip();
			$('#waa_registration_form input[type="submit"]').on('click', function(e) {
				$('#waa_registration_form .required' ).each(function() {
					if(!$(this).val()) {
						$(this).parent().addClass("has-error");
						e.preventDefault();
						window.scrollTo(0,0);
						$('.waa_errors .error-msg').text('Please enter all required fields. The required fields are highlighted.');
						$('.waa_errors').show();
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
						$("#waa_registration_form input[type='submit']").attr({"data-toggle": "tooltip", "data-placement": "top", "title": "Please reassure that the passwords match and are at least 6 characters." });	
					
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
					<input name="waa_user_login" id="waa_user_login" class="required" type="text"/>
				</p>
				<p>
					<label for="waa_user_pass">Password</label>
					<input name="waa_user_pass" id="waa_user_pass" class="required" type="password"/>
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
 
			wp_redirect(home_url()); exit;
		}
	}
}
add_action('init', 'waa_login_member');

// register a new user
function waa_add_new_member() {
  	if (isset( $_POST["waa_user_login"] ) && wp_verify_nonce($_POST['waa_register_nonce'], 'waa-register-nonce')) {
		$user_login		= $_POST["waa_user_login"];	
		$user_email		= $_POST["waa_user_email"];
		$user_first 	= $_POST["waa_user_first"];
		$user_last	 	= $_POST["waa_user_last"];
		$user_birthday= $_POST["waa_user_year"] . '-' . $_POST["waa_user_month"] . '-' . $_POST["waa_user_day"];
		$user_country = $_POST["waa_user_country"];
		$user_pass		= $_POST["waa_user_pass"];
		$pass_confirm 	= $_POST["waa_user_pass_confirm"];
		$user_legal 	= $_POST["waa_user_legal"];
 
		// this is required for username checks
		require_once(ABSPATH . WPINC . '/registration.php');
 
		if(username_exists($user_login)) {
			// Username already registered
			waa_errors()->add('username_unavailable', __('Username already taken.'));
		}
		if(!validate_username($user_login)) {
			// invalid username
			waa_errors()->add('username_invalid', __('Invalid username.'));
		}
		if($user_login == '') {
			// empty username
			waa_errors()->add('username_empty', __('Please enter a username.'));
		}
		if($user_first == '') {
			// empty firstname
			waa_errors()->add('firstname_empty', __('Please enter your First Name.'));
		}
		if($user_last == '') {
			// empty lastname
			waa_errors()->add('lastname_empty', __('Please enter your Last Name.'));
		}
		if($user_birthday == '') {
			// empty birthday
			waa_errors()->add('birthdate_empty', __('Please enter your birthday.'));
		}
		if($user_country == '') {
			// empty country
			waa_errors()->add('country_empty', __('Please select your country.'));
		}
		if(!is_email($user_email)) {
			//invalid email
			waa_errors()->add('email_invalid', __('Invalid email.'));
		}
		if(email_exists($user_email)) {
			//Email address already registered
			waa_errors()->add('email_used', __('Email already registered.'));
		}
		if($user_pass == '') {
			// passwords do not match
			waa_errors()->add('password_empty', __('Please enter a password.'));
		}
		if($user_pass != $pass_confirm) {
			// passwords do not match
			waa_errors()->add('password_mismatch', __('Passwords do not match.'));
		}
		if(!isset($user_legal)) {
			// passwords do not match
			waa_errors()->add('legal_empty', __('Please accept the terms and conditions.'));
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
					'role'				=> 'artist'
				)
			);
			if($new_user_id) {
				wp_insert_term(
				$user_first . ' ' . $user_last, // the term 
				'product_cat', // the taxonomy
					array(
						'slug' => $user_login
					)
				);
				add_user_meta( $new_user_id, 'artist_birthday', $user_birthday );
				add_user_meta( $new_user_id, 'artist_country', $user_country );
				
				// send an email to the admin alerting them of the registration
				wp_new_user_notification($new_user_id);
 
				// log the new user in
				wp_setcookie($user_login, $user_pass, true);
				wp_set_current_user($new_user_id, $user_login);	
				do_action('wp_login', $user_login);
 
				// send the newly created user to the home page after logging them in
				wp_redirect(home_url()); exit;
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