<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Get Started - step 0.
 *
 * This is the initial step of the artsits' setup process. It basically is a
 * explanatory page.
 *
 * @author		Benedikt Laufer
 * @package		WAA Get Started
 * @version		1.0.0
 */
require_once(waa_DIR . '/classes/get-started-modal.php');														
$getStarted = new waa_GetStarted();
?>
<div class='waa-settings-content waa-settings-content-step-0'>

	
	<h3><?= __('Willkommen! Gleich kann es losgehen...', 'waa'); ?></h3>
	<p><?= __('Um direkt mit dem Verkauf von Kunstwerken zu beginnen, benötigen wir noch ein paar Informationen von dir.', 'waa'); ?></p>

	
	<?php if ( isset($_POST['waa_address'])) : ?>
  <div class="waa-alert waa-alert-danger waa-panel-alert"><?php echo $getStarted->steps[$getStarted->current_step()]['error']; ?></div>
	<?php endif; ?>
	<div class="waa-page-help">
		<p><?= __('Bitte gebe hier die Adresse ein...Hier ein Text warum diese benötigt wird (Rechnungen/Versandadresse)', 'waa'); ?></p>
	</div>
	
	<form id="waa-address-handler-form" action="<?php echo esc_url( add_query_arg( 'step', 1 ) ); ?>" method="post" class="waa-form-horizontal waa-settings-area waa-get-started">
			<?php waa_seller_address_fields(false, true, isset ($_POST['waa_address']) ? $_POST['waa_address'] : ''); ?>
	
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
				<li class="step step-0 current"><a href='<?php echo esc_url( remove_query_arg( 'step' ) ); ?>'><?= __('Adresse', 'waa'); ?></a></li>
				<li class="step step-1"><a href="#"><?= __('Auszahlung', 'waa'); ?></a></li>
				<li class="step step-2" ><a href="#"><?= __('Versand', 'waa'); ?></a></li>
				<li class="step step-3"><a href="#"><?= __('Los geht`s!', 'waa'); ?></a></li>
			</ol>
	</nav>

</div>