<?php
/**
 * Plugin Name: WooCommerce PDF Invoices & Packing Slips Premium Templates
 * Plugin URI: http://www.wpovernight.com
 * Description: Premium templates for the WooCommerce PDF Invoices & Packing Slips extension
 * Version: 2.2.1
 * Author: Ewout Fernhout
 * Author URI: http://www.wpovernight.com
 * License: GPLv2 or later
 * License URI: http://www.opensource.org/licenses/gpl-license.php
 * Text Domain: wpo_wcpdf_templates
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'WooCommerce_PDF_IPS_Templates' ) ) {

	class WooCommerce_PDF_IPS_Templates {

		public static $plugin_url;
		public static $version;

		public $settings;
		public $functions;

		/**
		 * Constructor
		 */
		public function __construct() {
			self::$plugin_url = plugin_dir_url(plugin_basename(__FILE__));
			self::$version = '2.2.1';

			add_action( 'admin_notices', array( $this, 'free_version_check' ) );

			// Init updater data
			$this->item_name	= 'WooCommerce PDF Invoices & Packing Slips Premium Templates';
			$this->file			= __FILE__;
			$this->license_slug	= 'wpo_wcpdf_templates_license';
			$this->author		= 'Ewout Fernhout';

			// Load the updater
			add_action( 'init', array( $this, 'load_updater' ), 0 );

			// Add premium templates to settings page listing
			add_filter( 'wpo_wcpdf_template_paths', array( $this, 'add_templates' ), 1, 1 );

			// Load custom styles from settings
			add_action( 'wpo_wcpdf_custom_styles', array( $this, 'custom_template_styles' ) );

			// load the localisation & classes
			add_action( 'plugins_loaded', array( $this, 'translations' ) ); // or use init?
			add_action( 'init', array( $this, 'load_classes' ) );

			// run lifecycle methods
			if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
				add_action( 'wp_loaded', array( $this, 'do_install' ) );
			}
		}

		/**
		 * Load the translation / textdomain files
		 * 
		 * Note: the first-loaded translation file overrides any following ones if the same translation is present
		 */
		public function translations() {
			$locale = apply_filters( 'plugin_locale', get_locale(), 'wpo_wcpdf_templates' );
			$dir    = trailingslashit( WP_LANG_DIR );

			/**
			 * Frontend/global Locale. Looks in:
			 *
			 * 		- WP_LANG_DIR/woocommerce-pdf-ips-templates/wpo_wcpdf_templates-LOCALE.mo
			 * 	 	- WP_LANG_DIR/plugins/wpo_wcpdf_templates-LOCALE.mo
			 * 	 	- woocommerce-pdf-ips-templates/languages/wpo_wcpdf_templates-LOCALE.mo (which if not found falls back to:)
			 * 	 	- WP_LANG_DIR/plugins/wpo_wcpdf_templates-LOCALE.mo
			 */
			load_textdomain( 'wpo_wcpdf_templates', $dir . 'woocommerce-pdf-ips-templates/wpo_wcpdf_templates-' . $locale . '.mo' );
			load_textdomain( 'wpo_wcpdf_templates', $dir . 'plugins/wpo_wcpdf_templates-' . $locale . '.mo' );
			load_plugin_textdomain( 'wpo_wcpdf_templates', false, dirname( plugin_basename(__FILE__) ) . '/languages' );
		}

		/**
		 * Load the main plugin classes and functions
		 */
		public function includes() {
			include_once( 'includes/wcpdf-template-settings.php' );
			include_once( 'includes/wcpdf-template-functions.php' );
		}		

		/**
		 * Instantiate classes when woocommerce is activated
		 */
		public function load_classes() {
			if ( $this->is_woocommerce_activated() ) {
				$this->includes();
				$this->settings = new WooCommerce_PDF_IPS_Templates_Settings();
				$this->functions = new WooCommerce_PDF_IPS_Templates_Functions();
			} else {
				// display notice instead
				add_action( 'admin_notices', array ( $this, 'need_woocommerce' ) );
			}
		}

		/**
		 * Check if woocommerce is activated
		 */
		public function is_woocommerce_activated() {
			$blog_plugins = get_option( 'active_plugins', array() );
			$site_plugins = get_site_option( 'active_sitewide_plugins', array() );

			if ( in_array( 'woocommerce/woocommerce.php', $blog_plugins ) || isset( $site_plugins['woocommerce/woocommerce.php'] ) ) {
				return true;
			} else {
				return false;
			}
		}
		
		/**
		 * WooCommerce not active notice.
		 *
		 * @return string Fallack notice.
		 */
		 
		public function need_woocommerce() {
			$error = sprintf( __( 'WooCommerce PDF Invoices & Packing Slips requires %sWooCommerce%s to be installed & activated!' , 'wpo_wcpdf' ), '<a href="http://wordpress.org/extend/plugins/woocommerce/">', '</a>' );
			
			$message = '<div class="error"><p>' . $error . '</p></div>';
		
			echo $message;
		}

		/**
		 * Check if free version is installed
		 */
		public function free_version_check () {
			if ( !class_exists( 'WooCommerce_PDF_Invoices' ) ) {
				?>
				<div class="error">
					<p>
					<?php printf( __( 'WooCommerce PDF Invoices & Packing Slips Premium Templates requires the %sbase plugin (free)%s to be installed & activated!' , 'wpo_wcpdf_pro' ), '<a href="https://wordpress.org/plugins/woocommerce-pdf-invoices-packing-slips/">', '</a>' ); ?> <br />
					</p>
				</div>
				<?php
			}
		}

		/** Lifecycle methods *******************************************************
		 * Because register_activation_hook only runs when the plugin is manually
		 * activated by the user, we're checking the current version against the
		 * version stored in the database
		****************************************************************************/

		/**
		 * Handles version checking
		 */
		public function do_install() {
			// only install when woocommerce is active
			if ( !$this->is_woocommerce_activated() ) {
				return;
			}

			$version_setting = 'wpo_wcpdf_templates_version';
			$installed_version = get_option( $version_setting );

			// installed version lower than plugin version?
			if ( version_compare( $installed_version, self::$version, '<' ) ) {

				if ( ! $installed_version ) {
					$this->install();
				} else {
					$this->upgrade( $installed_version );
				}

				// new version number
				update_option( $version_setting, self::$version );
			}
		}


		/**
		 * Plugin install method. Perform any installation tasks here
		 */
		protected function install() {
			// check if old (1.3.2) template paths are used. If so - upgrade to new /templates folder
			$template_settings = get_option('wpo_wcpdf_template_settings');
			if ( isset( $template_settings['template_path'] ) ) {
				// plugin folder:
				$plugin_folder = trailingslashit(basename(__DIR__));
				// replace old paths with new
				$template_settings['template_path'] = str_replace('woocommerce-pdf-ips-templates/Business', $plugin_folder.'templates/Business', $template_settings['template_path']);
				$template_settings['template_path'] = str_replace('woocommerce-pdf-ips-templates/Modern', $plugin_folder.'templates/Modern', $template_settings['template_path']);

				// automatically switch to simple premium
				$template_settings['template_path'] = str_replace('woocommerce-pdf-invoices-packing-slips/templates/pdf/Simple', $plugin_folder.'templates/Simple Premium', $template_settings['template_path']);

				update_option( 'wpo_wcpdf_template_settings', $template_settings );
			}

		}


		/**
		 * Plugin upgrade method. Perform any required upgrades here
		 *
		 * @param string $installed_version the currently installed version
		 */
		protected function upgrade( $installed_version ) {
			// 2.1.5 Upgrade: set default footer height for Simple Premium (2cm)
			if ( version_compare( $installed_version, '2.1.5', '<' ) ) {
				$template_settings = get_option('wpo_wcpdf_template_settings');
				if (isset($template_settings['template_path']) && strpos($template_settings['template_path'],'Simple Premium') !== false ) {
					$template_settings['footer_height'] = '2cm';
					update_option( 'wpo_wcpdf_template_settings', $template_settings );
				}
			}

			// 2.1.7 Upgrade: set show meta as default in product block
			if ( version_compare( $installed_version, '2.1.7', '<' ) ) {
				$editor_settings = get_option('wpo_wcpdf_editor_settings');
				$documents = array('invoice','packing-slip','proforma','credit-note');
				foreach ($documents as $document) {
					if (isset($editor_settings['fields_'.$document.'_columns'])) {
						foreach ($editor_settings['fields_'.$document.'_columns'] as $key => $column) {

							if (isset($column['type']) && $column['type'] == 'description') {
								$column['show_meta'] = 1;
							}
							$editor_settings['fields_'.$document.'_columns'][$key] = $column;
						}
					}
				}
				update_option('wpo_wcpdf_editor_settings', $editor_settings);
			}
		}		

		/**
		 * Run the updater scripts from the WPO Sidekick
		 * @return void
		 */
		public function load_updater() {
			// Check if sidekick is loaded
			if (class_exists('WPO_Updater')) {
				$this->updater = new WPO_Updater( $this->item_name, $this->file, $this->license_slug, self::$version, $this->author );
			}
		}

		/**
		 * Add premium templates to settings page listing
		 */
		public function add_templates( $template_paths ) {
			$template_paths['premium_plugin'] = plugin_dir_path( __FILE__ ) . 'templates/';
			return $template_paths;
		}

		/**
		 * Load custom styles from settings
		 */
		public function custom_template_styles ( $template_type ) {
			$editor_settings = get_option('wpo_wcpdf_editor_settings');
			if (isset($editor_settings['custom_styles'])) {
				echo $editor_settings['custom_styles'];
			}
		}

		/***********************************************************************/
		/********************** PREMIUM TEMPLATE FUNCTIONS *********************/
		/***********************************************************************/

		public function get_table_headers ( $template_type ) {
			global $wpo_wcpdf;
			$column_settings = $this->settings->get_settings( $template_type, 'columns' );
			$order_discount = $wpo_wcpdf->get_order_discount( 'total', 'incl' );

			// mark first and last column
			end($column_settings);
			$column_settings[key($column_settings)]['position'] = 'last';
			reset($column_settings);
			$column_settings[key($column_settings)]['position'] = 'first';

			foreach ( $column_settings as $column_key => $column_setting) {
				if ( !$order_discount && isset($column_setting['only_discounted'])) {
					continue;
				}
				$headers[$column_key] = $column_setting + $this->functions->get_order_details_header( $column_setting );
			}

			return apply_filters( 'wpo_wcpdf_templates_table_headers', $headers, $template_type );
		}

		public function get_table_body ( $template_type ) {
			global $wpo_wcpdf;
			$column_settings = $this->settings->get_settings( $template_type, 'columns' );
			$order_discount = $wpo_wcpdf->get_order_discount( 'total', 'incl' );

			// mark first and last column
			end($column_settings);
			$column_settings[key($column_settings)]['position'] = 'last';
			reset($column_settings);
			$column_settings[key($column_settings)]['position'] = 'first';

			$body = array();
			foreach ($column_settings as $column_key => $column_setting) {
				$items = $wpo_wcpdf->get_order_items();
				if( sizeof( $items ) > 0 ) {
					$line_number = 1;
					foreach( $items as $item_id => $item ) {
						if ( !$order_discount && isset($column_setting['only_discounted'])) {
							continue;
						}

						$column_setting['line_number'] = $line_number;
						$body[$item_id][$column_key] = $column_setting + $this->functions->get_order_details_data( $column_setting, $item );
						$line_number++;
					}
				}
			}

			return apply_filters( 'wpo_wcpdf_templates_table_body', $body, $template_type );
		}

		public function get_totals ( $template_type ) {
			$total_settings = $this->settings->get_settings( $template_type, 'totals' );
			$totals_data = $this->functions->get_totals_table_data( $total_settings );

			return apply_filters( 'wpo_wcpdf_templates_totals', $totals_data, $template_type );
		}

		public function get_footer_height_page_bottom ( $default_height = '5cm' ) {
			global $wpo_wcpdf;
			if ( isset($wpo_wcpdf->settings->template_settings['footer_height']) && !empty($wpo_wcpdf->settings->template_settings['footer_height']) ) {
				$footer_height = $wpo_wcpdf->settings->template_settings['footer_height'];
			} else {
				$footer_height = $default_height;
			}

			// calculate bottom page margin
			$page_bottom = floatval($footer_height);

			// convert to cm
			if (strpos($footer_height,'in') !== false) {
				$page_bottom = $page_bottom * 2.54;
			} elseif (strpos($footer_height,'mm') !== false) {
				$page_bottom = $page_bottom / 10;
			}

			// add 1 + cm
			$page_bottom = ($page_bottom + 1).'cm';

			return compact( 'footer_height', 'page_bottom' );
		}


	} // end class
} // end class_exists

$wpo_wcpdf_templates = new WooCommerce_PDF_IPS_Templates();

/**
 * WPOvernight updater admin notice
 */
if ( ! class_exists( 'WPO_Updater' ) && ! function_exists( 'wpo_updater_notice' ) ) {

	if ( ! empty( $_GET['hide_wpo_updater_notice'] ) ) {
		update_option( 'wpo_updater_notice', 'hide' );
	}

	/**
	 * Display a notice if the "WP Overnight Sidekick" plugin hasn't been installed.
	 * @return void
	 */
	function wpo_updater_notice() {
		$wpo_updater_notice = get_option( 'wpo_updater_notice' );

		$blog_plugins = get_option( 'active_plugins', array() );
		$site_plugins = get_site_option( 'active_sitewide_plugins', array() );
		$plugin = 'wpovernight-sidekick/wpovernight-sidekick.php';

		if ( in_array( $plugin, $blog_plugins ) || isset( $site_plugins[$plugin] ) || $wpo_updater_notice == 'hide' ) {
			return;
		}

		echo '<div class="updated fade"><p>Install the <strong>WP Overnight Sidekick</strong> plugin to receive updates for your WP Overnight plugins - check your order confirmation email for more information. <a href="'.add_query_arg( 'hide_wpo_updater_notice', 'true' ).'">Hide this notice</a></p></div>' . "\n";
	}

	add_action( 'admin_notices', 'wpo_updater_notice' );
}
