<?php
/**
 * Handles the save action of YM app configuration.
 *
 * @package login-with-yourmembership\classes\actions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This function handles the save action of YM app configuration.
 */
class MoYm_Config_Action {

	/**
	 * Saves the app configuration settings based on the posted data.
	 *
	 * @param array $post The posted data.
	 *
	 * @return void
	 */
	public function save_app_config( $post ) {

		if ( moym_check_empty_or_null( $post['moym_clientID'] ) || moym_check_empty_or_null( $post['moym_appID'] ) || moym_check_empty_or_null( $post['moym_app_secret'] ) ) {
			update_option( 'moym_message', 'Please enter valid Client ID, App ID and App Secret' );
			moym_show_error_message();
			return;
		}
		if ( moym_check_empty_or_null( $post['moym_clientID'] ) ) {
			update_option( 'moym_message', 'Please enter a valid Client ID' );
			moym_show_error_message();
			return;
		}
		if ( moym_check_empty_or_null( $post['moym_authURL'] ) ) {
			update_option( 'moym_message', 'Please enter valid Authorization Endpoint' );
			moym_show_error_message();
			return;
		}

		$callback_url = site_url();

		$client_id  = sanitize_text_field( stripslashes( trim( $post['moym_clientID'] ) ) );
		$app_id     = sanitize_text_field( stripslashes( trim( $post['moym_appID'] ) ) );
		$app_secret = sanitize_text_field( stripslashes( trim( $post['moym_app_secret'] ) ) );
		$auth_url   = esc_url( stripslashes( $post['moym_authURL'] ) );

		$scope = isset( $post['moym_scope'] ) ? sanitize_text_field( stripslashes( $post['moym_scope'] ) ) : '';

		update_option( 'moym_callback', $callback_url );
		update_option( 'moym_clientID', $client_id );
		update_option( 'moym_appID', $app_id );
		update_option( 'moym_app_secret', $app_secret );
		update_option( 'moym_scope', $scope );
		update_option( 'moym_authURL', $auth_url );

		update_option( 'moym_message', 'App settings saved successfully.' );
		moym_show_success_message();
	}

}


