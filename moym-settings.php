<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- cannot change main file's name
/**
 * Plugin Name: Login with YourMembership - YM SSO Login
 * Plugin URI: https://miniorange.com
 * Description: YM SSO Plugin allows your users to SSO login into your WordPress site using their YourMembership credentials.
 * Version: 1.1.6
 * Author: miniOrange
 * Author URI: https://miniorange.com
 * License: GPL2
 *
 * @package login-with-yourmembership
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require 'includes/classes/actions/class-moym-config-action.php';
require 'includes/classes/actions/class-moym-customer-action.php';
require 'includes/classes/class-moym-customer.php';
require 'includes/utils/class-moym-utility.php';
require 'includes/utils/class-moym-global-variables.php';
require 'moym-page.php';
require 'class-moym-sso.php';

/**
 * The Main class of the Login with YourMembership Plugin.
 */
class MoYm_Login {

	/**
	 * The Constructor that takes care of initializing the hooks used by the plugin.
	 */
	public function __construct() {
		define('MOYM_VERSION', '1.1.6');
		global $moym_plugin_dir;
		global $moym_plugin_path;
		$moym_plugin_dir  = plugin_dir_url( __FILE__ );
		$moym_plugin_path = plugin_basename( __FILE__ );
		new MoYm_Global_Variables();

		add_action( 'admin_menu', array( $this, 'moym_add_to_menu' ) );
		add_action( 'admin_init', array( $this, 'moym_save_config_settings' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'moym_register_plugin_style' ) );
		add_action( 'login_enqueue_scripts', array( $this, 'moym_register_login_plugin_style' ) );
		add_action( 'login_enqueue_scripts', array( $this, 'moym_register_login_plugin_script' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'moym_register_plugin_script' ) );

		register_deactivation_hook( __FILE__, array( $this, 'moym_deactivate' ) );
	}
	/**
	 * Wrapper function to show the plugin settings page.
	 */
	public function moym_page_options() {
		update_option( 'moym_host_name', 'https://login.xecurify.com' );
		moym_show_page();
	}
	/**
	 * Wrapper function to handle the save action of every form in the plugin settings.
	 */
	public function moym_save_config_settings() {
		global $moym_customer_action, $moym_config_action;

		//phpcs:ignore WordPress.Security.NonceVerification.Missing -- NonceVerification is not required here.		
		$post_array  = sanitize_post( $_POST );
		$post_option = ! empty( $post_array['option'] ) ? sanitize_text_field( wp_unslash( $post_array['option'] ) ) : '';
		if ( current_user_can( 'manage_options' ) && ! empty( $post_option ) && check_admin_referer( $post_option ) ) {
			switch ( $post_option ) {
				case 'moym_register_customer':
					$moym_customer_action->register( $post_array );
					break;
				case 'moym_connect_verify_customer':
					$moym_customer_action->login( $post_array );
					break;
				case 'moym_forgot_password':
					$moym_customer_action->forgot_password( $post_array );
					break;
				case 'moym_contact_us_query_option':
					$moym_customer_action->contact_us( $post_array );
					break;
				case 'moym_go_back':
					$moym_customer_action->go_back_registration( $post_array );
					break;
				case 'moym_goto_login':
					$moym_customer_action->go_back_login( $post_array );
					break;
				case 'moym_save_app_config':
					$moym_config_action->save_app_config( $post_array );
					break;
				case 'moym_skip_feedback':
					$moym_customer_action->skip_feedback();
					break;
				case 'moym_feedback':
					$moym_customer_action->send_feedback( $post_array );
					break;
				case 'moym_add_sso_button':
					$moym_customer_action->moym_add_sso_button( $post_array );
					break;
			}
		}
	}
	/**
	 * Deactivates the plugin and deletes options.
	 *
	 * @return void
	 */
	public function moym_deactivate() {
		wp_redirect('plugins.php');
		delete_option( 'moym_host_name' );
		delete_option( 'moym_admin_first_name' );
		delete_option( 'moym_admin_last_name' );
		delete_option( 'moym_admin_phone' );
		delete_option( 'moym_transactionId' );
		delete_option( 'moym_registration_status' );
		delete_option( 'moym_verify_customer' );
		delete_option( 'moym_admin_customer_key' );
		delete_option( 'moym_admin_api_key' );
		delete_option( 'moym_customer_token' );
		delete_option( 'moym_new_registration' );
	}

	/**
	 * Adds the menu and submenu for the Login With YourMembership plugin.
	 *
	 * @return void
	 */
	public function moym_add_to_menu() {

		$page = add_menu_page(
			'YourMembership SSO',
			'YourMembership SSO',
			'administrator',
			'moym_settings',
			array( $this, 'moym_page_options' ),
			plugin_dir_url( __FILE__ ) . 'includes/images/miniorange_icon.png'
		);
	}
	/**
	 * Enqueues the login plugin style.
	 *
	 * @return void
	 */
	public function moym_register_login_plugin_style() {
		wp_enqueue_style( 'moym_login_style', plugins_url( 'includes/css/moym_style.min.css', __FILE__ ), array(), MOYM_VERSION );
	}

	/**
	 * Enqueues all the css files required by the plugin.
	 *
	 * @return void
	 */
	public function moym_register_plugin_style() {
		wp_enqueue_style( 'moym_admin_style', plugins_url( 'includes/css/moym_style.min.css', __FILE__ ), array(), MOYM_VERSION );
		wp_enqueue_style( 'moym_admin_phone_style', plugins_url( 'includes/css/phone.css', __FILE__ ), array(), MOYM_VERSION );
	}
	/**
	 * Enqueues all the js files required by the plugin.
	 *
	 * @return void
	 */
	public function moym_register_plugin_script() {
		wp_enqueue_script( 'moym_admin_phone_script', plugins_url( 'includes/js/phone.js', __FILE__ ), array(), MOYM_VERSION, true );

	}

	/**
	 * Enqueues the login plugin scripts.
	 *
	 * @return void
	 */
	public function moym_register_login_plugin_script() {
		wp_enqueue_script( 'moym_login_script', plugins_url( 'includes/js/settings.js', __FILE__ ), array( 'jquery' ), MOYM_VERSION, true );
	}
}

new MoYm_Login();


