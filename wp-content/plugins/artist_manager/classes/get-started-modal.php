<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

error_reporting(E_ALL);
ini_set('display_errors', 'On');
/**
 * Class waa_GetStarted.
 *
 * Initial 3-step-setup for artists after creating an account.
 *
 * @class		waa_GetStarted
 * @author		Benedikt Laufer
 * @version		1.0.0
 */
class waa_GetStarted {

	/**
	 * Steps.
	 *
	 * @since 1.0.0
	 * @var array $steps List of steps in the generator.
	 */
	public $steps = array();


	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->steps = array(
		
			'0'	=> array(
				'name'    => __( 'Adresse', 'waa' ),
				'handler' => 'address_handler',
				'error' 		=> 'Bitte fülle alle mit einem Sternchen * markierten Felder aus.',
			),
			'1'	=> array(
				'name'    => __( 'Zahlungsdaten', 'waa' ),
				'handler' => 'payment_handler',
				'error' 		=> 'Bitte fülle alle mit einem Sternchen * markierten Felder aus.',
			),
			'2'	=> array(
				'name'    => __( 'Versand', 'waa' ),
				'handler' => 'shipping_handler',
				'error' 		=> 'Bitte fülle alle mit einem Sternchen * markierten Felder aus.',
			),
			'3'	=> array(
				'name'    => __( 'Glückwunsch!', 'waa' ),
				'handler' => 'finish_handler',
				'error'		 => 'Bitte fülle alle mit einem Sternchen * markierten Felder aus.',
			),
		);

	}


	/**
	 * Current step.
	 *
	 * Get the current step number.
	 *
	 * @since 1.0.0
	 *
	 * @return	int	Step number.
	 */
	public function current_step() {

		$step = 0;

		if ( isset( $_REQUEST['step'] ) ) :
			$step = absint( $_REQUEST['step'] );
		endif;

		return $step;

	}


	/**
	 * Output step.
	 *
	 * Output a step of the generator. Uses the step
	 * handler as a callback.
	 *
	 * @since 1.0.0
	 *
	 * @param	int	$step	Step to output. Leave empty to use the current step.
	 * @param bool $error
	 */
	public function output_step( $step = null, $error = false ) {

		if ( null == $step ) :
			$step = $this->current_step();
		endif;

		// Fallback to first step
		if ( ! isset( $this->steps[ $step ] ) ) :
			$step = 0;
		endif;

		if ( ! is_array( $this->steps ) || ! isset( $this->steps[ $step ]['handler'] ) ) :
			wp_die( __( 'I\'m trying to load a step but couldn\'t find it! Please go back and try again.', 'waa' ), __( 'Could not find step', 'waa' ) );
		endif;

		if ( ! is_callable( array( $this, $this->steps[ $step ]['handler'] ) ) && ! is_callable( $this->steps[ $step ]['handler'] ) ) :
			wp_die( __( 'I\'m trying to load a generator step but couldn\'t find the right callback! Please go back and try again.', 'waa' ), __( 'Could not find step', 'waa' ) );
		endif;

		$handler = $this->steps[ $step ]['handler'];
		
		if ( $error )
			$error = $this->steps [ $step ]['error'];		

		if ( is_callable( array( $this, $handler ) ) ) :
			call_user_func( array( $this, $handler ) );
		endif;

	}


	/**
	 * Coupon options handler.
	 *
	 * Handler to output the coupon options page (step 0).
	 *
	 * @since 1.0.0
	 */
	public function address_handler() {
	
		require_once waa_DIR . '/templates/get-started/step-0.php';

	}


	/**
	 * Generator options handler.
	 *
	 * Handler to output the coupon generator options (step 1).
	 *
	 * @since 1.0.0
	 */
	public function payment_handler() {
		
		// Make sure values from the previous step are present
		if ( ! isset( $_POST['waa_address'] ) || empty ( $_POST['waa_address']['street_1'] )  ||  empty ( $_POST['waa_address']['city'] )  ||  empty ( $_POST['waa_address']['zip'] ) ||  empty ( $_POST['waa_address']['country'] ) ) :
			return $this->output_step( 0, true );
		endif;
			
		require_once waa_DIR . '/templates/get-started/step-1.php';

	}


	/**
	 * Generate coupon handler.
	 *
	 * Handler to output the coupon generator page (step 2).
	 *
	 * @since 1.0.0
	 */
	public function shipping_handler() {

		if ( ! isset( $_POST['waa_address'] ) || empty ( $_POST['waa_address']['street_1'] )  ||  empty ( $_POST['waa_address']['city'] )  ||  empty ( $_POST['waa_address']['zip'] ) ||  empty ( $_POST['waa_address']['country'] ) || empty ( $_POST['settings']['method'] ) ) :
			return $this->output_step( 1, true );
		endif;
		
		if ( !empty ( $_POST['settings']['method'] ) && $_POST['settings']['method'] == 'paypal' ) :
				if ( empty ( $_POST['settings']['paypal']['email'] ) ) :
					return $this->output_step( 1, true );
				endif;
			elseif ( !empty ( $_POST['settings']['method'] ) &&  $_POST['settings']['method'] == 'bank') :
				if ( empty ($_POST['settings']['bank']['ac_name']) || empty ($_POST['settings']['bank']['ac_iban']) || empty ($_POST['settings']['bank']['bank_name']) ) :
					return $this->output_step( 1, true );
				endif;
			endif;					
			
		require_once waa_DIR . '/templates/get-started/step-2.php';

	}
	
		/**
	 * Finish handler.
	 *
	 * Handler to output the finish page (step 3).
	 *
	 * @since 1.0.0
	 */
	public function finish_handler() {

		require_once waa_DIR . '/templates/get-started/step-3.php';

	}


}
