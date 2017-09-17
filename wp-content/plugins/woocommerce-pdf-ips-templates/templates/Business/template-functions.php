<?php
/**
 * Use this file for all your template filters and actions.
 * Requires WooCommerce PDF Invoices & Packing Slips 1.4.13 or higher
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Add extra setting for the footer height to the template settings
 */
add_action( 'admin_init', 'wpo_wcpdf_footer_height_setting', 5 );
function wpo_wcpdf_footer_height_setting () {
	global $wpo_wcpdf;
	add_settings_field(
		'footer_height',
		__( 'Footer height', 'wpo_wcpdf' ),
		array( $wpo_wcpdf->settings, 'text_element_callback' ),
		'wpo_wcpdf_template_settings',
		'extra_template_fields',
		array(
			'menu'			=> 'wpo_wcpdf_template_settings',
			'id'			=> 'footer_height',
			'size'			=> '5',
			'default'		=> '2cm',
			'description'	=> __( 'Enter the total height of the footer in mm, cm or in and use a dot for decimals.<br/>For example: 1.25in or 82mm', 'wpo_wcpdf' )
		)
	);
}

add_filter( 'wpo_wcpdf_template_editor_defaults', 'wpo_wcpdf_business_template_defaults', 9, 3 );
add_filter( 'wpo_wcpdf_template_editor_settings', 'wpo_wcpdf_business_template_defaults', 9, 3 );
function wpo_wcpdf_business_template_defaults ( $settings, $template_type, $settings_name ) {
	$editor_settings = get_option('wpo_wcpdf_editor_settings');

	if (isset($editor_settings['settings_saved'])) {
		return $settings;
	}

	// only packing slip is different
	if ( $template_type == 'packing-slip' ) {
		switch ($settings_name) {
			case 'columns':
				$settings = array (
					1 => array (
						'type'			=> 'sku',
					),
					2 => array (
						'type'			=> 'description',
						'show_meta'		=> 1,
					),
					3 => array (
						'type'			=> 'quantity',
					),
				);
				break;
			case 'totals':
				$settings = array();
				break;
		}
	} else {
		switch ($settings_name) {
			case 'columns':
				$settings = array (
					1 => array (
						'type'			=> 'sku',
					),
					2 => array (
						'type'			=> 'description',
						'show_meta'		=> 1,
					),
					3 => array (
						'type'			=> 'quantity',
					),
					4 => array (
						'type'			=> 'price',
						'price_type'	=> 'single',
						'tax'			=> 'excl',
						'discount'		=> 'before',
					),
					5 => array (
						'type'			=> 'tax_rate',
					),
					6 => array (
						'type'			=> 'price',
						'price_type'	=> 'total',
						'tax'			=> 'excl',
						'discount'		=> 'before',
					),
				);
				break;
			case 'totals':
				$settings = array(
					1 => array (
						'type'			=> 'subtotal',
						'tax'			=> 'excl',
						'discount'		=> 'before',
					),
					2 => array (
						'type'			=> 'discount',
						'tax'			=> 'excl',
					),
					3 => array (
						'type'			=> 'shipping',
						'tax'			=> 'excl',
					),
					4 => array (
						'type'			=> 'fees',
						'tax'			=> 'excl',
					),
					5 => array (
						'type'			=> 'vat',
					),
					6 => array (
						'type'			=> 'total',
						'tax'			=> 'incl',
					),
				);
				break;
		}
	}

	return $settings;
}
