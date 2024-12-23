<?php
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 *
 * @package MPCL_Multisite_Post_Cloner
 */

if ( ! class_exists( 'MPCL_Multisite_Post_Cloner' ) ) {

	/**
	 * The main class for the Multisite Post Cloner plugin.
	 */
	class MPCL_Multisite_Post_Cloner {

		/**
		 * The instance of the class.
		 *
		 * @var ?MPCL_Multisite_Post_Cloner
		 */
		private static ?MPCL_Multisite_Post_Cloner $instance = null;

		/**
		 * Constructor.
		 */
		private function __construct() {
			$this->load_dependencies();
			$this->define_admin_hooks();
		}

		/**
		 * Get the instance of the class.
		 *
		 * @return MPCL_Multisite_Post_Cloner
		 */
		public static function get_instance(): MPCL_Multisite_Post_Cloner {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Load the required dependencies for this plugin.
		 *
		 * @return void
		 */
		private function load_dependencies(): void {
			require_once plugin_dir_path( __FILE__ ) . 'class-mpcl-multisite-post-cloner-admin.php';
			require_once plugin_dir_path( __FILE__ ) . 'class-mpcl-multisite-post-cloner-actions.php';
		}

		/**
		 * Define the hooks related to the admin area.
		 *
		 * @return void
		 */
		private function define_admin_hooks(): void {
			$plugin_admin = new MPCL_Multisite_Post_Cloner_Admin();
			add_action( 'admin_menu', array( $plugin_admin, 'add_settings_page' ) );
			add_action( 'admin_init', array( $plugin_admin, 'register_settings' ) );

			$selected_types = get_option( 'mpcl_multisite_post_cloner_post_types', array( 'post', 'page' ) );

			add_action( 'admin_notices', array( $plugin_admin, 'bulk_multisite_notices' ) );

			$plugin_actions = new MPCL_Multisite_Post_Cloner_Actions();

			foreach ( $selected_types as $post_type ) {
				add_filter( "bulk_actions-edit-{$post_type}", array( $plugin_admin, 'bulk_multisite_actions' ) );
				add_filter( "handle_bulk_actions-edit-{$post_type}", array( $plugin_actions, 'bulk_action_multisite_handler' ), 10, 3 );
			}
		}
	}
}
