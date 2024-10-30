<?php
/** This file takes care of making API requests for interacting with the customer’s miniOrange account.
 *
 * @package     login-with-yourmembership\classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This class MoYm_Customer contains functions to handle all the customer related functionalities like sending support query.
 */
class MoYm_Customer {

	/**
	 * Customer Email.
	 *
	 * @access   public
	 * @var      string $email Customer's Email.
	 */
	public $email;

	/**
	 * Customer Key.
	 * Initial values are hardcoded to support the miniOrange framework to generate OTP for email.
	 * We need the default value for creating the first time,
	 * As we don't have the Default keys available before registering the user to our server.
	 * This default values are only required for sending an One Time Passcode at the user provided email address.
	 *
	 * @access   private
	 * @var      string $default_customer_key Customer's Customer Key.
	 */
	private $default_customer_key = '16555';

	/**
	 * Customer API Key.
	 * Initial values are hardcoded to support the miniOrange framework to generate OTP for email.
	 * We need the default value for creating the first time,
	 * As we don't have the Default keys available before registering the user to our server.
	 * This default values are only required for sending an One Time Passcode at the user provided email address.
	 *
	 * @access   private
	 * @var      string $default_api_key Customer's Customer Key.
	 */
	private $default_api_key = 'fFd2XcvTGDemZvbw1bcUesNJWEqKbbUq';

	/**
	 * This function is used for creating customer by making a call to the /rest/customer/add endpoint.
	 *
	 * @return string $response Response body of the API call for creating Customer.
	 */
	public function create_customer() {
		$url = get_option( 'moym_host_name' ) . '/moas/rest/customer/add';

		$this->email = get_option( 'moym_admin_email' );
		$password    = get_option( 'moym_admin_password' );

		$fields       = array(
			'areaOfInterest' => 'WP YourMembership SSO Plugin',
			'email'          => $this->email,
			'password'       => $password,
		);
		$field_string = wp_json_encode( $fields );
		$headers      = array(
			'Content-Type' => 'application/json',
		);
		$args         = array(
			'method'      => 'POST',
			'body'        => $field_string,
			'timeout'     => '5',
			'redirection' => '5',
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => $headers,
		);

		$response = moym_wp_remote_post( $url, $args );
		return $response['body'];
	}

	/**
	 * This function is used for getting customer key.
	 *
	 * @return string $response Response body of the API call for fetching Customer key by making a call to the /rest/customer/key endpoint.
	 */
	public function get_customer_key() {
		$url   = get_option( 'moym_host_name' ) . '/moas/rest/customer/key';
		$email = get_option( 'moym_admin_email' );

		$password = get_option( 'moym_admin_password' );

		$fields = array(
			'email'    => $email,
			'password' => $password,
		);

		$field_string = wp_json_encode( $fields );
		$headers      = array(
			'Content-Type' => 'application/json',
		);
		$args         = array(
			'method'      => 'POST',
			'body'        => $field_string,
			'timeout'     => '5',
			'redirection' => '5',
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => $headers,
		);
		$response     = moym_wp_remote_post( $url, $args );
		return $response['body'];
	}

	/**
	 * This function is used for checking if customer exists by making a call to the /rest/customer/check-if-exists endpoint.
	 *
	 * @return string $response Response body of the API call for customer validity.
	 */
	public function check_customer() {
		$url   = get_option( 'moym_host_name' ) . '/moas/rest/customer/check-if-exists';
		$email = get_option( 'moym_admin_email' );

		$fields       = array(
			'email' => $email,
		);
		$field_string = wp_json_encode( $fields );
		$headers      = array(
			'Content-Type' => 'application/json',
		);
		$args         = array(
			'method'      => 'POST',
			'body'        => $field_string,
			'timeout'     => '5',
			'redirection' => '5',
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => $headers,
		);

		$response = moym_wp_remote_post( $url, $args );
		return $response['body'];
	}

	/**
	 * This function is used for sending support query from plugin by making a call to the rest/customer/contact-us endpoint.
	 *
	 * @param string $email       Customer's Email.
	 * @param string $phone       Customer's Phone.
	 * @param string $query       Customer's Query.
	 *
	 * @return array $response    Response body of the API call for call request.
	 */
	public function submit_contact_us( $email, $phone, $query ) {
		$company    = ! empty( get_option( 'moym_admin_company_name' ) ) ? get_option( 'moym_admin_company_name' ) : '';
		$first_name = ! empty( get_option( 'moym_admin_first_name' ) ) ? get_option( 'moym_admin_first_name' ) : '';
		$last_name  = ! empty( get_option( 'moym_admin_last_name' ) ) ? get_option( 'moym_admin_last_name' ) : '';
		$query      = '[WP YourMembership SSO Plugin] ' . $query;
		$url        = get_option( 'moym_host_name' ) . '/moas/rest/customer/contact-us';
		$fields     = array(
			'firstName' => $first_name,
			'lastName'  => $last_name,
			'company'   => $company,
			'email'     => $email,
			'ccEmail'   => 'samlsupport@xecurify.com',
			'phone'     => $phone,
			'query'     => $query,
		);

		$field_string = wp_json_encode( $fields );

		$headers  = array(
			'Content-Type' => 'application/json',
		);
		$args     = array(
			'method'      => 'POST',
			'body'        => $field_string,
			'timeout'     => '10',
			'redirection' => '5',
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => $headers,
		);
		$response = moym_wp_remote_post( $url, $args );
		return $response['body'];
	}

	/**
	 * This function is used for resetting the account password by making a call to the /rest/customer/password-reset endpoint.
	 *
	 * @param  string $email    Customer's Email.
	 *
	 * @return string  $response Response body of the API call for resetting account's password.
	 */
	public function forgot_password( $email ) {

		$url = get_option( 'moym_host_name' ) . '/moas/rest/customer/password-reset';

		/* The customer Key provided to you */
		$customer_key = get_option( 'moym_admin_customer_key' );

		/* The customer API Key provided to you */
		$api_key = get_option( 'moym_admin_api_key' );

		/* Current time in milliseconds since midnight, January 1, 1970 UTC. */
		$current_time_in_millis = round( microtime( true ) * 1000 );

		/* Creating the Hash using SHA-512 algorithm */
		$string_to_hash = $customer_key . number_format( $current_time_in_millis, 0, '', '' ) . $api_key;
		$hash_value     = hash( 'sha512', $string_to_hash );

		$fields = '';

		// *check for otp over sms/email
		$fields       = array(
			'email' => $email,
		);
		$field_string = wp_json_encode( $fields );

		$headers = array(
			'Content-Type'  => 'application/json',
			'Customer-Key'  => $customer_key,
			'Timestamp'     => $current_time_in_millis,
			'Authorization' => $hash_value,
		);
		$args    = array(
			'method'      => 'POST',
			'body'        => $field_string,
			'timeout'     => '5',
			'redirection' => '5',
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => $headers,
		);

		$response = moym_wp_remote_post( $url, $args );
		return $response['body'];
	}

	/**
	 * This function is used for sending the query for demo requests and feedback for the plugin by making a call to the /api/notify/send endpoint.
	 *
	 * @param string $email        Customer's Email.
	 * @param string $phone        Customer's Phone.
	 * @param string $message      Customer's Message.
	 *
	 * @return string $response     Response body of the API call for demo request and feedback.
	 */
	public function send_email_alert( $email, $phone, $message ) {

		$url                    = get_option( 'moym_host_name' ) . '/moas/api/notify/send';
		$customer_key           = $this->default_customer_key;
		$api_key                = $this->default_api_key;
		$current_time_in_millis = round( microtime( true ) * 1000 );
		$string_to_hash         = $customer_key . $current_time_in_millis . $api_key;
		$hash_value             = hash( 'sha512', $string_to_hash );
		$from_email             = 'no-reply@xecurify.com';
		$subject                = 'Feedback: WordPress YourMembership SSO Plugin';
		$server_name            = isset( $_SERVER['SERVER_NAME'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_NAME'] ) ) : '';

		global $user;
		$user    = wp_get_current_user();
		$query   = '[WordPress YourMembership SSO Plugin: ]: ' . $message;
		$content = '<div >Hello, <br><br>First Name :' . $user->user_firstname . '<br><br>Last  Name :' . $user->user_lastname . '   <br><br>Company :<a href="' . $server_name . '" target="_blank" >' . $server_name . '</a><br><br>Phone Number :' . $phone . '<br><br>Email :<a href="mailto:' . $email . '" target="_blank">' . $email . '</a><br><br>Query :' . $query . '</div>';

		$fields       = array(
			'customerKey' => $customer_key,
			'sendEmail'   => true,
			'email'       => array(
				'customerKey' => $customer_key,
				'fromEmail'   => $from_email,
				'bccEmail'    => $from_email,
				'fromName'    => 'Xecurify',
				'toEmail'     => 'info@xecurify.com',
				'toName'      => 'samlsupport@xecurify.com',
				'bccEmail'    => 'samlsupport@xecurify.com',
				'subject'     => $subject,
				'content'     => $content,
			),
		);
		$field_string = wp_json_encode( $fields );
		$headers      = array(
			'Content-Type'  => 'application/json',
			'Customer-Key'  => $customer_key,
			'Timestamp'     => $current_time_in_millis,
			'Authorization' => $hash_value,
		);
		$args         = array(
			'method'      => 'POST',
			'body'        => $field_string,
			'timeout'     => '5',
			'redirection' => '5',
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => $headers,
		);

		$response = moym_wp_remote_post( $url, $args );
		return $response['body'];
	}

}


