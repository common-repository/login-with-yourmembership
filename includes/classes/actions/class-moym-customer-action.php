<?php
/**
 * Handles all the customer account related actions.
 *
 * @package login-with-yourmembership\classes\actions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles all the customer account related actions.
 */
class MoYm_Customer_Action {

	/**
	 * Wrapper function that creates the customer account in miniOrange after validating input data.
	 *
	 * @param array $post The posted data containing customer credentials.
	 *
	 * @return void
	 */
	public function register( $post ) {

		$email            = '';
		$password         = '';
		$confirm_password = '';
		$illegal          = "#$%^*()+=[]';,/{}|:<>?~";
		$illegal          = $illegal . '"';
		if ( moym_check_empty_or_null( $post['email'] ) || moym_check_empty_or_null( $post['password'] ) || moym_check_empty_or_null( $post['confirmPassword'] ) ) {
			update_option( 'moym_message', 'All the fields are required. Please enter valid entries.' );
			moym_show_error_message();
			return;
		} elseif ( ( strlen( $post['password'] ) < 6 ) || ( strlen( $post['confirmPassword'] ) < 6 ) ) {    // check password is of minimum length 6.
			update_option( 'moym_message', 'Choose a password with minimum length 6.' );
			moym_show_error_message();
			return;
		} elseif ( strpbrk( $post['email'], $illegal ) ) {
			update_option( 'moym_message', 'Please match the format of Email. No special characters are allowed.' );
			moym_show_error_message();
			return;
		} else {
			$email            = sanitize_email( $post['email'] );
			$password         = sanitize_text_field( stripslashes( $post['password'] ) );
			$confirm_password = sanitize_text_field( stripslashes( $post['confirmPassword'] ) );
		}
		update_option( 'moym_admin_email', $email );

		if ( strcmp( $password, $confirm_password ) === 0 ) {

			update_option( 'moym_admin_password', $password );
			$customer = new MoYm_Customer();
			$content  = json_decode( $customer->check_customer(), true );

			if ( ! is_null( $content ) ) {

				if ( strcasecmp( $content['status'], 'CUSTOMER_NOT_FOUND' ) === 0 ) {

					$response = $this->moym_create_customer();

					if ( is_array( $response ) && array_key_exists( 'status', $response ) && 'success' === $response['status'] ) {
						update_option( 'moym_message', 'Customer registered successfully.' );
						moym_show_success_message();
					} else {
						update_option( 'moym_message', 'This is not a valid email. Please enter a valid email.' );
						moym_show_error_message();
					}
				} elseif ( strcasecmp( $content['status'], 'INVALID_EMAIL' ) === 0 ) {
					update_option( 'moym_message', 'This is not a valid email. Please enter a valid email.' );
					moym_show_error_message();
				} else {
					$response = $this->moym_get_current_customer();
					if ( is_array( $response ) && array_key_exists( 'status', $response ) && 'success' === $response['status'] ) {
						update_option( 'moym_message', 'Customer Retrieved Successfully.' );
						moym_show_success_message();
					}
				}
			}
		} else {
			update_option( 'moym_message', 'Passwords do not match.' );
			delete_option( 'moym_verify_customer' );
			delete_option( 'moym_admin_customer_key' );
			moym_show_error_message();
		}
	}
	/**
	 * Validates the user credentials from miniOrange.
	 *
	 * @param array $post The posted data containing customer credentials.
	 *
	 * @return void
	 */
	public function login( $post ) {
		// validation and sanitization.
		$email    = '';
		$password = '';
		$illegal  = "#$%^*()+=[]';,/{}|:<>?~";
		$illegal  = $illegal . '"';
		if ( moym_check_empty_or_null( $post['email'] ) || moym_check_empty_or_null( $post['password'] ) ) {
			update_option( 'moym_message', 'All the fields are required. Please enter valid entries.' );
			moym_show_error_message();
			return;
		} elseif ( strpbrk( $post['email'], $illegal ) ) {
			update_option( 'moym_message', 'Please match the format of Email. No special characters are allowed.' );
			moym_show_error_message();
			return;
		} elseif ( moym_check_password_pattern( $post['password'] ) ) {
			update_option( 'moym_message', 'Minimum 6 characters should be present. Maximum 15 characters should be present. Only following symbols (!@#.$%^&*-_) should be present.' );
			moym_show_error_message();
			return;
		} else {
			$email    = sanitize_email( $post['email'] );
			$password = sanitize_text_field( stripslashes( $post['password'] ) );
		}

		update_option( 'moym_admin_email', $email );
		update_option( 'moym_admin_password', $password );
		$customer = new MoYm_Customer();
		$content  = $customer->get_customer_key();

		if ( ! is_null( $content ) ) {
			$customer_key = json_decode( $content, true );
			if ( json_last_error() === JSON_ERROR_NONE ) {
				update_option( 'moym_admin_customer_key', $customer_key['id'] );
				update_option( 'moym_admin_api_key', $customer_key['apiKey'] );
				update_option( 'moym_customer_token', $customer_key['token'] );
				update_option( 'moym_admin_password', '' );
				update_option( 'moym_message', 'Your account has been retrieved successfully.' );
				delete_option( 'moym_verify_customer' );
				moym_show_success_message();
			} else {
				delete_option( 'moym_admin_customer_key' );
				update_option( 'moym_message', 'Invalid username or password. Please try again.' );
				moym_show_error_message();
			}
		}
		update_option( 'moym_admin_password', '' );
	}
	/**
	 * Handles the forgot password flow of miniOrange.
	 *
	 * @param array $post The posted data.
	 *
	 * @return void
	 */
	public function forgot_password( $post ) {
		$email = get_option( 'moym_admin_email' );
		if ( moym_check_empty_or_null( $email ) ) {
			if ( moym_check_empty_or_null( $post['email'] ) ) {
				update_option( 'moym_message', 'No email provided. Please enter your email below to reset password.' );
				moym_show_error_message();
				return;
			} else {
				$email = sanitize_email( $post['email'] );
			}
		}
		$customer = new MoYm_Customer();
		$content  = json_decode( $customer->forgot_password( $email ), true );
		if ( strcasecmp( $content['status'], 'SUCCESS' ) === 0 ) {
			update_option( 'moym_message', 'You password has been reset successfully. Please enter the new password sent to your registered mail here.' );
			moym_show_success_message();
		} else {
			update_option( 'moym_message', 'An error occured while processing your request. Please make sure you are registered with miniOrange with the given email address.' );
			moym_show_error_message();
		}
	}
	/**
	 * Sends support query.
	 *
	 * @param array $post The posted data.
	 *
	 * @return void
	 */
	public function contact_us( $post ) {
		// Contact Us query.
		$email    = sanitize_email( $post['moym_contact_us_email'] );
		$phone    = sanitize_text_field( $post['moym_contact_us_phone'] );
		$query    = sanitize_text_field( $post['moym_contact_us_query'] );
		$customer = new MoYm_Customer();
		if ( moym_check_empty_or_null( $email ) || moym_check_empty_or_null( $query ) ) {
			update_option( 'moym_message', 'Please fill up Email and Query fields to submit your query.' );
			moym_show_error_message();
		} else {
			$submited = $customer->submit_contact_us( $email, $phone, $query );
			if ( false === $submited ) {
				update_option( 'moym_message', 'Your query could not be submitted. Please try again.' );
				moym_show_error_message();
			} else {
				update_option( 'moym_message', 'Thanks for getting in touch! We shall get back to you shortly.' );
				moym_show_success_message();
			}
		}
	}
	/**
	 * Go back to registration page from login page.
	 *
	 * @param array $post The posted data.
	 *
	 * @return void
	 */
	public function go_back_registration( $post ) {
		update_option( 'moym_registration_status', '' );
		delete_option( 'moym_new_registration' );
		delete_option( 'moym_admin_email' );
		delete_option( 'moym_verify_customer' );
	}
	/**
	 * Go to login page from registration page.
	 *
	 * @param array $post The posted data.
	 *
	 * @return void
	 */
	public function go_back_login( $post ) {
		delete_option( 'moym_new_registration' );
		update_option( 'moym_verify_customer', 'true' );
	}

	/**
	 * Creates the customer account in miniOrange.
	 *
	 * @return array The response status indicating success or error.
	 */
	public function moym_create_customer() {
		$customer     = new MoYm_Customer();
		$customer_key = json_decode( $customer->create_customer(), true );
		if ( ! is_null( $customer_key ) ) {
			$response = array();
			if ( strcasecmp( $customer_key['status'], 'CUSTOMER_USERNAME_ALREADY_EXISTS' ) === 0 ) {
				$api_response = $this->moym_get_current_customer();
				if ( $api_response ) {
					$response['status'] = 'success';
				} else {
					$response['status'] = 'error';
				}
			} elseif ( strcasecmp( $customer_key['status'], 'SUCCESS' ) === 0 ) {
				update_option( 'moym_admin_customer_key', $customer_key['id'] );
				update_option( 'moym_admin_api_key', $customer_key['apiKey'] );
				update_option( 'moym_customer_token', $customer_key['token'] );
				update_option( 'moym_admin_password', '' );
				update_option( 'moym_message', 'Registration complete!' );
				update_option( 'moym_registration_status', 'mo_ym_REGISTRATION_COMPLETE' );
				delete_option( 'moym_verify_customer' );
				delete_option( 'moym_new_registration' );
				moym_show_success_message();
				$response['status'] = 'success';
				return $response;
			}
			update_option( 'moym_admin_password', '' );
			return $response;
		}
	}
	/**
	 * Retrieves the current customer details.
	 *
	 * @return array The response status indicating success or error.
	 */
	public function moym_get_current_customer() {
		$customer     = new MoYm_Customer();
		$content      = $customer->get_customer_key();
		$customer_key = json_decode( $content, true );

		if ( isset( $customer_key ) ) {
			update_option( 'moym_admin_customer_key', $customer_key['id'] );
			update_option( 'moym_admin_api_key', $customer_key['apiKey'] );
			update_option( 'moym_customer_token', $customer_key['token'] );
			update_option( 'moym_admin_password', '' );
			update_option( 'moym_message', 'Your account has been retrieved successfully.' );
			delete_option( 'moym_verify_customer' );
			delete_option( 'moym_new_registration' );
			moym_show_success_message();
			$response['status'] = 'success';
			return $response;
		} else {
			update_option( 'moym_message', 'You already have an account with miniOrange. Please enter a valid password.' );
			update_option( 'moym_verify_customer', 'true' );
			delete_option( 'moym_new_registration' );
			delete_option( 'moym_admin_customer_key' );
			moym_show_error_message();
			$response['status'] = 'error';
			return $response;
		}

	}
	/**
	 * Redirects to the Installed Plugin page with the correct message after the plugin is deactivated.
	 *
	 * @return void
	 */
	public function skip_feedback() {
		global $moym_plugin_path;
		deactivate_plugins( $moym_plugin_path );
		wp_safe_redirect( self_admin_url( 'plugins.php?deactivate=true' ) );
		exit;
	}

	/**
	 * Sends feedback via email after plugin deactivation.
	 *
	 * @param array $post The posted data containing feedback.
	 *
	 * @return void
	 */
	public function send_feedback( $post ) {

		global $moym_plugin_path;
		$user                      = wp_get_current_user();
		$message                   = 'Plugin Deactivated';
		$deactivate_reason_message = array_key_exists( 'query_feedback', $post ) ? sanitize_text_field( $post['query_feedback'] ) : false;

		if ( is_multisite() ) {
			$multisite_enabled = 'True';
		} else {
			$multisite_enabled = 'False';
		}

		$message .= ', [Multisite enabled: ' . $multisite_enabled . ']';
		$message .= ', Feedback : ' . $deactivate_reason_message . '';

		if ( isset( $post['rate'] ) ) {
			$rate_value = sanitize_text_field( $post['rate'] );
		}

		$message .= ', [Rating :' . $rate_value . ']';

		$email = $post['query_mail'];
		if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
			$email = get_option( 'moym_admin_email' );
			if ( empty( $email ) ) {
				$email = $user->user_email;
			}
		}
		$phone            = get_option( 'moym_admin_phone' );
		$feedback_reasons = new MoYm_Customer();
		if ( ! is_null( $feedback_reasons ) ) {
			$submited = json_decode( $feedback_reasons->send_email_alert( $email, $phone, $message ), true );
			if ( json_last_error() === JSON_ERROR_NONE ) {
				if ( is_array( $submited ) && array_key_exists( 'status', $submited ) && 'ERROR' === $submited['status'] ) {
					update_option( 'moym_message', $submited['message'] );
					moym_show_success_message();
				} else {
					if ( false === $submited ) {
						update_option( 'moym_message', 'Error while submitting the query.' );
						moym_show_error_message();
					}
				}
			}
		}
		deactivate_plugins( $moym_plugin_path );
		wp_safe_redirect( self_admin_url( 'plugins.php?deactivate=true' ) );
		exit;
	}

	/**
	 * Adds the YM SSO button.
	 *
	 * @param array $post The posted data.
	 *
	 * @return void
	 */
	public function moym_add_sso_button( $post ) {
		$add_button = 'false';
		if ( array_key_exists( 'moym_add_sso_button', $post ) ) {
			$add_button = htmlspecialchars( $post['moym_add_sso_button'] );
		}

		update_option( 'moym_add_sso_button', $add_button );
		update_option( 'moym_message', 'Sign in option updated.' );
		moym_show_success_message();
	}

}


