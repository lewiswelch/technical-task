<?php
/**
 * Plugin Name: WooCommerce Test
 *
 * @package WooCommerce Test
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once 'woo-includes/woo-functions.php';
}
/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), '55d9643a241ecf5ad501808c0787483f', '122144' );

if ( ! class_exists( 'WooCommerce_Test_Plugin' ) ) {
	/**
	 * Activate when WC starts
	 *
	 * Only start us up if WC is running & declare HPOS compatibility
	 */
	if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ||
		( is_array( get_site_option( 'active_sitewide_plugins' ) ) && array_key_exists( 'woocommerce/woocommerce.php', get_site_option( 'active_sitewide_plugins' ) ) ) ) {
		add_action( 'plugins_loaded', 'WooCommerce_Test_Plugin::instance' );
		add_action( 'before_woocommerce_init', function() {
			if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
			}
		} );
	} else {
		add_action( 'admin_notices', array( 'WooCommerce_Test_Plugin', 'output_not_active_notice' ) );
	}

	/**
	 * Namespace class for functions non-specific to any object within the plugin
	 *
	 * @package  WooCommerce Test
	 */
	class WooCommerce_Test_Plugin {

		/**
		 * Main plugin class instance
		 *
		 * @var object
		 */
		protected static $instance;
		/**
		 * Path to plugin directory
		 *
		 * @var string
		 */
		public static $path;

		/**
		 * WooCommerce_Test_Plugin constructor
		 */
		public function __construct() {
			self::$path = plugin_dir_path( __FILE__ );
			require_once self::$path . '/definitions.php';
			if ( ! $this->minimum_woocommerce_version_is_loaded() ) {
				return;
			}
			$this->load_hooks();
		}

		/**
		 * Check users version of WooCommerce is high enough for our plugin
		 *
		 * @return bool
		 */
		public function minimum_woocommerce_version_is_loaded() {
			global $woocommerce;
			if ( ! version_compare( $woocommerce->version, '3.0', '<' ) ) {
				return true;
			}
			add_action( 'admin_notices', array( __CLASS__, 'output_not_active_notice' ) );

			return false;
		}

		/**
		 * Display an admin notice notifying users their version of WooCommerce is too low
		 *
		 * @return void
		 */
		public static function output_not_active_notice() {
			?>
			<div class="error">
				<p><?php esc_html_e( 'WooCommerce Test is active but is not functional. Is WooCommerce installed and up to date (version 3.0 or higher)?', 'woocommerce-test' ); ?></p>
			</div>
			<?php
		}

		/**
		 * All other hooks pertinent to the main plugin class
		 *
		 * @todo factor out hooks into appropriate classes
		 */
		public function load_hooks() {
			// Initialisation.
			add_action( 'admin_init', array( $this, 'version_check' ) );
			add_action( 'init', array( $this, 'set_default_localization_directory' ) );
		}

		/**
		 * Setup localization for plugin
		 *
		 * @access public
		 * @return void
		 */
		public function set_default_localization_directory() {
			load_plugin_textdomain( 'woocommerce-test', false, plugin_basename( dirname( __FILE__ ) ) . '/assets/languages/' );
		}

		/**
		 * Check plugin version in DB and call any required upgrade functions
		 *
		 * @hooked action admin_init
		 * @access public
		 * @return void
		 * @since  1.0.1
		 */
		public function version_check() {

			$options = get_option( WCT_SLUG );
			if ( ! isset( $options['version'] ) ) {
				$this->set_default_options();
			} else {
				if ( version_compare( $options['version'], '1.7.0' ) < 0 ) {
					update_option( 'woocommerce_queue_flush_rewrite_rules', 'true' );
				}
				if ( version_compare( $options['version'], '2.0.0' ) >= 0 && ! get_option( '_' . WCT_SLUG . '_version_2_warning' ) ) {
					update_option( '_' . WCT_SLUG . '_version_2_warning', true );
				}
			}

			$options['version'] = WCT_VERSION;
			update_option( WCT_SLUG, $options );
		}

		/**
		 * Set default test options
		 */
		protected function set_default_options() {
			update_option( 'woocommerce_queue_flush_rewrite_rules', 'true' );
			update_option( '_' . WCT_SLUG . '_metadata_updated', true );
			update_option( '_' . WCT_SLUG . '_version_2_warning', true );

		}

		/**
		 * Test main instance, ensures only one instance is loaded
		 *
		 * @since 1.5.0
		 * @return WooCommerce_Test_Plugin
		 */
		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

	}
}
