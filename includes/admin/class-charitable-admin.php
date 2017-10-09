<?php
/**
 * Class that sets up the Charitable Admin functionality.
 *
 * @package     Charitable/Classes/Charitable_Admin
 * @version     1.0.0
 * @author      Eric Daams
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'Charitable_Admin' ) ) :

	/**
	 * Charitable_Admin
	 *
	 * @final
	 * @since  1.0.0
	 */
	final class Charitable_Admin {

		/**
		 * The single instance of this class.
		 *
		 * @var Charitable_Admin|null
		 */
		private static $instance = null;

		/**
		 * Donation actions class.
		 *
		 * @var Charitable_Donation_Admin_Actions
		 */
		private $donation_actions;

		/**
		 * Set up the class.
		 *
		 * Note that the only way to instantiate an object is with the charitable_start method,
		 * which can only be called during the start phase. In other words, don't try
		 * to instantiate this object.
		 *
		 * @since  1.0.0
		 */
		protected function __construct() {
			$this->load_dependencies();

			$this->donation_actions = new Charitable_Donation_Admin_Actions;

			do_action( 'charitable_admin_loaded' );
		}

		/**
		 * Returns and/or create the single instance of this class.
		 *
		 * @since  1.2.0
		 *
		 * @return Charitable_Admin
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Include admin-only files.
		 *
		 * @since  1.0.0
		 *
		 * @return void
		 */
		private function load_dependencies() {
			$includes_dir = charitable()->get_path( 'includes' );
			$admin_dir    = $includes_dir . 'admin/';

			/* Interfaces */
			require_once( $includes_dir . 'interfaces/interface-charitable-admin-actions.php' );

			/* Abstracts */
			require_once( $includes_dir . 'abstracts/abstract-class-charitable-admin-actions.php' );

			/* Core */
			require_once( $admin_dir . 'charitable-core-admin-functions.php' );
			require_once( $admin_dir . 'class-charitable-meta-box-helper.php' );
			require_once( $admin_dir . 'class-charitable-admin-pages.php' );
			require_once( $admin_dir . 'class-charitable-admin-notices.php' );

			/* Admin Actions */
			require_once( $admin_dir . 'actions/class-charitable-donation-admin-actions.php' );

			/* Campaigns */
			require_once( $admin_dir . 'campaigns/class-charitable-campaign-meta-boxes.php' );
			require_once( $admin_dir . 'campaigns/class-charitable-campaign-list-table.php' );
			require_once( $admin_dir . 'campaigns/charitable-admin-campaign-hooks.php' );//

			/* Dashboard widgets */
			require_once( $admin_dir . 'dashboard-widgets/class-charitable-donations-dashboard-widget.php' );
			require_once( $admin_dir . 'dashboard-widgets/charitable-dashboard-widgets-hooks.php' );

			/* Donations */
			require_once( $admin_dir . 'donations/class-charitable-donation-metaboxes.php' );
			require_once( $admin_dir . 'donations/class-charitable-donation-list-table.php' );
			require_once( $admin_dir . 'donations/charitable-admin-donation-hooks.php' );

			/* Forms */
			require_once( $admin_dir . 'forms/views/class-charitable-admin-form-view.php' );
			require_once( $admin_dir . 'forms/class-charitable-admin-form.php' );
			require_once( $admin_dir . 'forms/class-charitable-admin-donation-form.php' );

			/* Settings */
			require_once( $admin_dir . 'settings/class-charitable-settings.php' );
			require_once( $admin_dir . 'settings/class-charitable-general-settings.php' );
			require_once( $admin_dir . 'settings/class-charitable-email-settings.php' );
			require_once( $admin_dir . 'settings/class-charitable-gateway-settings.php' );
			require_once( $admin_dir . 'settings/class-charitable-licenses-settings.php' );
			require_once( $admin_dir . 'settings/class-charitable-advanced-settings.php' );
			require_once( $admin_dir . 'settings/charitable-settings-admin-hooks.php' );

			/* Upgrades */
			require_once( $admin_dir . 'upgrades/class-charitable-upgrade.php' );
			require_once( $admin_dir . 'upgrades/class-charitable-upgrade-page.php' );
			require_once( $admin_dir . 'upgrades/charitable-upgrade-hooks.php' );
		}

		/**
		 * Get Charitable_Donation_Admin_Actions class.
		 *
		 * @since  1.5.0
		 *
		 * @return Charitable_Donation_Admin_Actions
		 */
		public function get_donation_actions() {
			return $this->donation_actions;
		}

		/**
		 * Loads admin-only scripts and stylesheets.
		 *
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function admin_enqueue_scripts() {
			if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
				$suffix  = '';
				$version = '';
			} else {
				$suffix  = '.min';
				$version = charitable()->get_version();
			}

			$assets_path = charitable()->get_path( 'assets', false );

			/* Menu styles are loaded everywhere in the WordPress dashboard. */
			wp_register_style(
				'charitable-admin-menu',
				$assets_path . 'css/charitable-admin-menu' . $suffix . '.css',
				array(),
				$version
			);

			wp_enqueue_style( 'charitable-admin-menu' );

			/* Admin page styles are registered but only enqueued when necessary. */
			wp_register_style(
				'charitable-admin-pages',
				$assets_path . 'css/charitable-admin-pages' . $suffix . '.css',
				array(),
				$version
			);

			/* The following styles are only loaded on Charitable screens. */
			$screen = get_current_screen();

			if ( ! is_null( $screen ) && in_array( $screen->id, $this->get_charitable_screens() ) ) {

				wp_register_style(
					'charitable-admin',
					$assets_path . 'css/charitable-admin' . $suffix . '.css',
					array(),
					$version
				);

				wp_enqueue_style( 'charitable-admin' );

				wp_register_script(
					'charitable-admin',
					$assets_path . 'js/charitable-admin' . $suffix . '.js',
					array( 'jquery-ui-datepicker', 'jquery-ui-tabs', 'jquery-ui-sortable' ),
					$version,
					false
				);

				wp_enqueue_script( 'charitable-admin' );

				$localized_vars = apply_filters( 'charitable_localized_javascript_vars', array(
					'suggested_amount_description_placeholder' => __( 'Optional Description', 'charitable' ),
					'suggested_amount_placeholder'             => __( 'Amount', 'charitable' ),
				) );

				wp_localize_script( 'charitable-admin', 'CHARITABLE', $localized_vars );

			}//end if

			wp_register_script(
				'charitable-admin-notice',
				$assets_path . 'js/charitable-admin-notice' . $suffix . '.js',
				array( 'jquery-core' ),
				$version,
				false
			);

			wp_register_script(
				'charitable-admin-media',
				$assets_path . 'js/charitable-admin-media' . $suffix . '.js',
				array( 'jquery-core' ),
				$version,
				false
			);
		}

		/**
		 * Set admin body classes.
		 *
		 * @since  1.5.0
		 *
		 * @param  string $classes Existing list of classes.
		 * @return string
		 */
		public function set_body_class( $classes ) {
			$screen = get_current_screen();

			if ( 'donation' == $screen->post_type && ( 'add' == $screen->action || isset( $_GET['show_form'] ) ) ) {
				$classes .= ' charitable-admin-donation-form';
			}

			return $classes;
		}

		/**
		 * Add notices to the dashboard.
		 *
		 * @since  1.4.0
		 *
		 * @return void
		 */
		public function add_notices() {

			/* Get any version update notices first. */
			$this->add_version_update_notices();

			/* Also pick up any settings notices. */
			// $this->add_settings_update_notices();

			/* Render notices. */
			charitable_get_admin_notices()->render();

		}

		/**
		 * Add version update notices to the dashboard.
		 *
		 * @since  1.4.6
		 *
		 * @return void
		 */
		public function add_version_update_notices() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			$notices = array();

			$notices['release-140'] = sprintf( __( "Thanks for upgrading to Charitable 1.4. <a href='%s'>Find out what's new in this release</a>.", 'charitable' ),
				'https://www.wpcharitable.com/charitable-1-4-features-responsive-campaign-grids-a-new-shortcode?utm_source=notice&utm_medium=wordpress-dashboard&utm_campaign=release-notes&utm_content=release-140'
			);

			$notices['release-142'] = sprintf( __( "In Charitable 1.4.2, we have improved the login and registration forms. <a href='%s'>Find out how</a>.", 'charitable' ),
				'https://www.wpcharitable.com/how-we-improved-logins-and-registrations-in-charitable/?utm_source=notice&utm_medium=wordpress-dashboard&utm_campaign=release-notes&utm_content=release-142'
			);

			if ( Charitable_Gateways::get_instance()->is_active_gateway( 'paypal' ) ) {
				$notices['release-143-paypal'] = sprintf( __( "PayPal is upgrading its SSL certificates. <a href='%s'>Test your integration now to avoid disruption.</a>", 'charitable' ),
					esc_url( add_query_arg( array(
		                'page'         => 'charitable-settings',
		                'tab'          => 'gateways',
		                'group'        => 'gateways_paypal',
		            ), admin_url( 'admin.php#paypal-sandbox-test' ) ) )
		        );
			} else {
				delete_transient( 'charitable_release-143-paypal_notice' );
			}

			$notices['release-1410-recurring-donations'] = sprintf( __( "<strong>NEW:</strong> Supercharge your online fundraising with Recurring Donations. <a href='%s'>Read more</a>", 'charitable' ),
				'https://www.wpcharitable.com/supercharge-your-online-fundraising-in-2017-with-recurring-donations/?utm_source=notices&utm_medium=wordpress-dashboard&utm_campaign=recurring-donations-release-post&utm_content=release-1410'
			);

			$helper = charitable_get_admin_notices();

			foreach ( $notices as $notice => $message ) {
				if ( ! get_transient( 'charitable_' . $notice . '_notice' ) ) {
					continue;
				}

				$helper->add_version_update( $message, $notice );
			}
		}

		/**
		 * Dismiss a notice.
		 *
		 * @since  1.4.0
		 *
		 * @return void
		 */
		public function dismiss_notice() {
			if ( ! isset( $_POST['notice'] ) ) {
				wp_send_json_error();
			}

			$ret = delete_transient( 'charitable_' . $_POST['notice'] . '_notice', true );

			if ( ! $ret ) {
				wp_send_json_error( $ret );
			}

			wp_send_json_success();
		}

		/**
		 * Adds one or more classes to the body tag in the dashboard.
		 *
		 * @since  1.0.0
		 *
		 * @param  string $classes Current body classes.
		 * @return string Altered body classes.
		 */
		public function add_admin_body_class( $classes ) {
			$screen = get_current_screen();

			if ( Charitable::DONATION_POST_TYPE == $screen->post_type ) {
				$classes .= ' post-type-charitable';
			}

			return $classes;
		}

		/**
		 * Add custom links to the plugin actions.
		 *
		 * @since  1.0.0
		 *
		 * @param  string[] $links Plugin action links.
		 * @return string[]
		 */
		public function add_plugin_action_links( $links ) {
			$links[] = '<a href="' . admin_url( 'admin.php?page=charitable-settings' ) . '">' . __( 'Settings', 'charitable' ) . '</a>';
			return $links;
		}

		/**
		 * Add Extensions link to the plugin row meta.
		 *
		 * @since  1.2.0
		 *
		 * @param  string[] $links Plugin action links.
		 * @param  string   $file  The plugin file.
		 * @return string[] $links
		 */
		public function add_plugin_row_meta( $links, $file ) {
			if ( plugin_basename( charitable()->get_path() ) != $file ) {
				return $links;
			}

			$extensions_link = esc_url( add_query_arg( array(
				'utm_source'   => 'plugins-page',
				'utm_medium'   => 'plugin-row',
				'utm_campaign' => 'admin',
				),
				'https://wpcharitable.com/extensions/'
			) );

			$links[] = '<a href="' . $extensions_link . '">' . __( 'Extensions', 'charitable' ) . '</a>';

			return $links;
		}

		/**
		 * Remove the jQuery UI styles added by Ninja Forms.
		 *
		 * @since  1.2.0
		 *
		 * @return void
		 */
		public function remove_jquery_ui_styles_nf( $context ) {
			wp_dequeue_style( 'jquery-smoothness' );
			return $context;
		}

		/**
		 * Export donations.
		 *
		 * @since  1.3.0
		 *
		 * @return void
		 */
		public function export_donations() {
			if ( ! wp_verify_nonce( $_GET['_charitable_export_nonce'], 'charitable_export_donations' ) ) {
				return false;
			}

			require_once( charitable()->get_path( 'admin' ) . 'reports/class-charitable-export-donations.php' );

			$report_type = $_GET['report_type'];

			$export_args = apply_filters( 'charitable_donations_export_args', array(
				'start_date'    => $_GET['start_date'],
				'end_date'      => $_GET['end_date'],
				'status'        => $_GET['post_status'],
				'campaign_id'   => $_GET['campaign_id'],
				'report_type'   => $report_type,
			) );

			$export_class = apply_filters( 'charitable_donations_export_class', 'Charitable_Export_Donations', $report_type, $export_args );

			new $export_class( $export_args );

			exit();
		}

		/**
		 * Returns an array of screen IDs where the Charitable scripts should be loaded.
		 *
		 * @uses   charitable_admin_screens
		 *
		 * @since  1.0.0
		 *
		 * @return array
		 */
		public function get_charitable_screens() {
			/**
			 * Filter admin screens where Charitable styles & scripts should be loaded.
			 *
			 * @since 1.0.0
			 *
			 * @param string[] $screens List of screen ids.
			 */
			return apply_filters( 'charitable_admin_screens', array(
				'campaign',
				'donation',
				'charitable_page_charitable-settings',
				'edit-campaign',
				'edit-donation',
				'dashboard',
			) );
		}
	}

endif;
