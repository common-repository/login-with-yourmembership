<?php
/**
 * This file runs automatically when the user deletes the plugin in order to clear out any plugin options and/or settings specific to the plugin.
 *
 * @package login-with-yourmembership
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}
	// delete all stored key-value pairs.

	delete_option( 'moym_host_name' );
	delete_option( 'moym_enable_redirect_on_login' );
	delete_option( 'moym_message' );
	delete_option( 'moym_admin_company_name' );
	delete_option( 'moym_admin_first_name' );
	delete_option( 'moym_admin_last_name' );
	delete_option( 'moym_admin_email' );
	delete_option( 'moym_admin_phone' );
	delete_option( 'moym_admin_password' );
	delete_option( 'moym_email_otp_count' );
	delete_option( 'moym_transactionId' );
	delete_option( 'moym_registration_status' );
	delete_option( 'moym_verify_customer' );
	delete_option( 'moym_admin_customer_key' );
	delete_option( 'moym_admin_api_key' );
	delete_option( 'moym_customer_token' );
	delete_option( 'moym_cust' );
	delete_option( 'moym_new_registration' );


