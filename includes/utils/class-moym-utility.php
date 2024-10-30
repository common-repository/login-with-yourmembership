<?php
/**
 * This file contains all utility functions.
 *
 * @package login-with-yourmembership\includes\utils
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Check if a attribute is empty or null.
 *
 * @param mixed $value The value to check.
 * @return bool True if the value is empty or null, false otherwise.
 */
function moym_check_empty_or_null( $value ) {
	if ( ! isset( $value ) || empty( $value ) ) {
		return true;
	}
	return false;
}

/**
 * Wrapper function to display the success message.
 */
function moym_show_success_message() {
	remove_action( 'admin_notices', 'moym_success_message' );
	add_action( 'admin_notices', 'moym_error_message' );
}

/**
 * Wrapper function to display the error message.
 */
function moym_show_error_message() {
	remove_action( 'admin_notices', 'moym_error_message' );
	add_action( 'admin_notices', 'moym_success_message' );
}

/**
 * Prints the error message to be displayed in the admin notice.
 */
function moym_success_message() {
	$message = get_option( 'moym_message' ); ?>		
		<script> 	
		jQuery(document).ready(function() {	
			var message = "<?php echo esc_attr( $message ); ?>";
			jQuery('#moym_msgs').append("<div class='error notice is-dismissible moym_error_container'> <p class='moym_msgs'>" + message + "</p></div>");
		});
		</script>
		<?php
}

/**
 * Prints the success message to be displayed in the admin notice.
 */
function moym_error_message() {
	$message = get_option( 'moym_message' );
	?>
		<script> 
		jQuery(document).ready(function() {
			var message = "<?php echo esc_attr( $message ); ?>";
			jQuery('#moym_msgs').append("<div class='updated notice is-dismissible moym_success_container'> <p class='moym_msgs'>" + message + "</p></div>");
		});
		</script>
		<?php
}

/**
 * Check if the customer is registered.
 *
 * @return int Returns 1 if the customer is registered, 0 otherwise.
 */
function moym_is_customer_registered() {
	$email        = get_option( 'moym_admin_email' );
	$customer_key = get_option( 'moym_admin_customer_key' );
	if ( ! $email || ! $customer_key || ! is_numeric( trim( $customer_key ) ) ) {
		return 0;
	} else {
		return 1;
	}
}

/**
 * Check if cURL is installed.
 *
 * @return int Returns 1 if cURL is installed, 0 otherwise.
 */
function moym_is_curl_installed() {
	if ( in_array( 'curl', get_loaded_extensions(), true ) ) {
		return 1;
	} else {
		return 0;
	}
}

/**
 * Checks if the mentioned extension is installed.
 *
 * @param string $name The name of the extension to check.
 * @return bool Returns true if the extension is installed, false otherwise.
 */
function moym_is_extension_installed( $name ) {
	if ( in_array( $name, get_loaded_extensions(), true ) ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Starts the session if not already started.
 */
function moym_start_session() {
	if ( ! session_id() ) {
		session_start();
	}
}

/**
 * Check if a password matches the pattern during miniOrange account login.
 *
 * @param string $password The password to check.
 * @return bool Returns true if the password does not match the pattern, false otherwise.
 */
function moym_check_password_pattern( $password ) {
	$pattern = '/^[(\w)*(\!\@\#\$\%\^\&\*\.\-\_)*]+$/';
	return ! preg_match( $pattern, $password );
}

/**
 * Sends POST request to the mentioned URL with provided arguments.
 *
 * @param string $url  The URL to which the request is being made.
 * @param array  $args Optional. Array of args needed for sending request to API endpoint.
 */
function moym_wp_remote_post( $url, $args = array() ) {
	$response = wp_remote_post( $url, $args );

	if ( ! is_wp_error( $response ) ) {
		return $response;
	} else {
		update_option( 'moym_message', 'Unable to connect to the Internet. Please try again.' );
		moym_show_error_message();
	}
}

/**
 * Sends POST request to the URL with request body, and Content-Type header as application/x-www-form-urlencoded.
 *
 * @param string $data JSON encoded parameters' string.
 * @param string $url  The URL to which the request is being made.
 * @return string|false Response of the API call.
 */
function moym_remote_post_urlencoded( $data, $url ) {
	$field_string = $data;
	$headers      = array(
		'Content-Type' => 'application/x-www-form-urlencoded',
	);
	$args         = array(
		'method'      => 'POST',
		'body'        => $field_string,
		'redirection' => '10',
		'httpversion' => '1.0',
		'blocking'    => true,
		'sslverify'   => false,
		'data_format' => 'body',
		'headers'     => $headers,
	);
	$response     = wp_remote_post( $url, $args );
	if ( is_wp_error( $response ) ) {
		return false;
	} else {
		return $response['body'];
	}
}

/**
 * Get the current page URL.
 *
 * @return string The current page URL.
 */
function moym_get_current_page_url() {
	$http_host = isset( $_SERVER['HTTP_HOST'] ) ? esc_url_raw( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';
	if ( substr( $http_host, -1 ) === '/' ) {
		$http_host = substr( $http_host, 0, -1 );
	}

	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	if ( substr( $request_uri, 0, 1 ) === '/' ) {
		$request_uri = substr( $request_uri, 1 );
	}

	if ( strpos( $request_uri, '?option=moymsso' ) !== false ) {
		return strtok( $request_uri, '?' );
	}

	$is_https    = isset( $_SERVER['HTTPS'] ) && strcasecmp( sanitize_text_field( wp_unslash( $_SERVER['HTTPS'] ) ), 'on' ) === 0;
	$relay_state = 'http' . ( $is_https ? 's' : '' ) . '://' . $http_host . '/' . $request_uri;

	return $relay_state;
}

/**
 * Sends GET/POST request to the mentioned URL with provided arguments.
 *
 * @param string $url     The URL to send the request to.
 * @param array  $args    Optional. Request arguments (default: array()).
 * @param bool   $is_get  Optional. Whether the request is a GET request (default: false).
 *
 * @return string|void The response body on success, or void if an error occurred.
 */
function moym_wp_remote_call( $url, $args = array(), $is_get = false ) {
	if ( ! $is_get ) {
		$response = wp_remote_post( $url, $args );
	} else {
		$response = wp_remote_get( $url, $args );
	}

	if ( ! is_wp_error( $response ) ) {
		return $response['body'];
	} else {
		echo 'Unable to connect to the Internet. Please try again.';
		exit;
	}
}
