<?php
/**
 * This file handles integration of YourMembership Single Sign-On with WordPress.
 *
 * @package login-with-yourmembership
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require 'includes/classes/class-moym-login-widget.php';
require 'includes/tmpl/moym-feedback.php';

/**
 *
 * This class handles the integration of YourMembership Single Sign-On with WordPress.
 */
class MoYm_Sso {

	/**
	 * Stores the YM API domain.
	 *
	 * @var string
	 */
	private $url;

	/**
	 * Stores the version of plugin.
	 *
	 * @var string
	 */
	private $version;

	/**
	 *
	 * Initialize default values and hooks into WordPress actions.
	 */
	public function __construct() {

		$this->url     = 'https://api.yourmembership.com';
		$this->version = '2.30';

		add_action( 'init', array( $this, 'moym_validate' ) );
		add_action( 'admin_footer', array( $this, 'moym_feedback' ) );
		add_action(
			'widgets_init',
			function() {
				register_widget( 'MoYm_Login_Widget' );
			}
		);
		add_action( 'login_form', array( $this, 'moym_modify_login_form' ) );
		if ( ! session_id() || session_id() === '' || ! isset( $_SESSION ) ) {
			session_start();
		}
	}
	/**
	 * This function is responsible for adding the SSO button on the WordPress login page.
	 *
	 * @return void
	 */
	public function moym_modify_login_form() {
		if ( get_option( 'moym_add_sso_button' ) === 'true' ) {
			$this->moym_add_sso_button();
		}
	}
	/**
	 * Renders the SSO button.
	 *
	 * @return void
	 */
	public function moym_add_sso_button() {
		if ( ! is_user_logged_in() ) {
			echo '
			<input id="moym_user_login_input" type="hidden" name="option" value="" />
			<div id="moym_button">
				<div id="moym_login_sso_button" onclick="loginWithSSOButton(this.id)" class="button button-primary">
				<img class="moym_login_lockicon" src="' . esc_url( plugin_dir_url( __FILE__ ) ) . 'includes/images/lock-icon.webp"> Login with YourMembership
				</div>
				<div class="moym_login_button"><b>OR</b></div>
			</div>';

		}
	}
	/**
	 * Initiates the SSO and consumes the authorization code.
	 */
	public function moym_validate() {
		$redirect_to = '';

		//phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Ignore the nonce verification as this is a GET param.
		$option = ! empty( $_REQUEST['option'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['option'] ) ) : '';
		if ( isset( $option ) && strpos( $option, 'moymsso' ) !== false ) {

			//phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Ignore the nonce verification as this is a GET param.
			if ( isset( $_REQUEST['testconfig'] ) && 'true' === $_REQUEST['testconfig'] ) {
				$_SESSION['moymtest'] = true;
			}
			$this->moym_authorize_user();
		} elseif ( isset( $_REQUEST['option'] ) && strpos( $option, 'show_attr' ) !== false ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Ignore the nonce verification as this is a GET param.
			$this->moym_display_test_attributes();
			exit;
		} elseif ( isset( $_REQUEST['code'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Ignore the nonce verification as this is a GET param.
			//phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Ignore the nonce verification as this is a GET param.
			$moym_code = sanitize_text_field( wp_unslash( $_REQUEST['code'] ) );
			$this->moym_user_auth( $moym_code );
		}
	}
	/**
	 * Displays the test config window.
	 */
	public function moym_display_test_attributes() {
		global $moym_plugin_dir;
		$basic_profile = get_option( 'moym_basic_profile' );
		$email = "";
		if ( isset( $basic_profile->email ) ) {
			$email = $basic_profile->email;
		}
		echo '
			<style>
				.test_window{
					color: #3c763d;
					background-color: #dff0d8; 
					padding:2%;
					margin-bottom:20px;
					text-align:center; 
					border:1px solid #AEDB9A; 
					font-size:18pt;
				}
			</style>
			<div class = "test_window">TEST SUCCESSFUL</div>
			<div style="display:block;text-align:center;margin-bottom:4%;"><img style="width:15%;" src="' . esc_url( $moym_plugin_dir ) . 'includes/images/green_check.png"/></div>';

		echo '<span style="font-size:14pt;"><b>Hello</b>, ' . esc_html( $email ) . '</span>';

		echo '<br/><p style="font-weight:bold;font-size:14pt;margin-left:1%;">ATTRIBUTES RECEIVED:</p>
				<table style="border-collapse:collapse;border-spacing:0; display:table;width:100%; font-size:14pt;background-color:#EDEDED;">
				<tr style="text-align:center;"><td style="font-weight:bold;border:2px solid #949090;padding:2%;">ATTRIBUTE NAME</td><td style="font-weight:bold;padding:2%;border:2px solid #949090; word-wrap:break-word;">ATTRIBUTE VALUE</td></tr>';

		foreach ( $basic_profile as $key => $value ) {
			echo '<tr>';
			echo "<td style='font-weight:bold;border:2px solid #949090;padding:2%;'>" . esc_html( $key ) . "</td>\n<td style='padding:2%;border:2px solid #949090; word-wrap:break-word;'>" . esc_html( $value ) . '</td>';
			echo '</tr>';

		}
		echo '</table></div>';
		echo '<div style="margin:3%;display:block;text-align:center;">
			<input style="padding:1%;width:100px;background: #0091CD none repeat scroll 0% 0%;cursor: pointer;font-size:15px;border-width: 1px;border-style: solid;border-radius: 3px;white-space: nowrap;box-sizing: border-box;border-color: #0073AA;box-shadow: 0px 1px 0px rgba(120, 200, 230, 0.6) inset;color: #FFF;
			"type="button" value="Done" onClick="self.close();"></div>';

		session_destroy();

	}
	/**
	 * Constructs the authorization URL and redirects towards it.
	 */
	public function moym_authorize_user() {
		$auth_url          = get_option( 'moym_authURL' );
		$app_id            = get_option( 'moym_appID' );
		$callback_url      = get_option( 'moym_callback' );
		$authorization_url = $auth_url . '?app_id=' . $app_id . '&redirect_uri=' . $callback_url . '/&scope=basic_profile';

		header( 'Location: ' . $authorization_url );
		exit;
	}
	/**
	 * Performs all further operations till user login after receiving the auth code.
	 *
	 * @param string $moym_code The authorization code.
	 * @return void
	 */
	public function moym_user_auth( $moym_code ) {
		$moym_appid      = get_option( 'moym_appID' );
		$moym_app_secret = get_option( 'moym_app_secret' );
		$moym_clientid   = get_option( 'moym_clientID' );
		$moym_user_type  = 'Member';

		if ( empty( $moym_code ) ) {
			echo 'ERROR: No Authorization Code found';
			exit;
		}

		$access_token_response = json_decode( $this->moym_get_access_token( $moym_appid, $moym_app_secret, $moym_code ) );
		$moym_access_token     = $access_token_response->AccessToken;
		if ( empty( $moym_access_token ) ) {
			echo 'ERROR: Access Token not found';
			exit;
		}

		$moym_session_value = json_decode( $this->moym_authenticate_user( $moym_appid, $moym_app_secret, $moym_access_token, $moym_clientid, $moym_user_type ) );
		$moym_sessionid     = $moym_session_value->SessionId;
		$moym_memberid      = $moym_session_value->MemberID;

		if ( empty( $moym_sessionid ) || empty( $moym_memberid ) ) {
			echo 'ERROR: Session information not found';
			exit;
		}

		$basic_profile = json_decode( $this->moym_get_user_basic_profile( $moym_sessionid, $moym_memberid, $moym_clientid ) );

		update_option( 'moym_basic_profile', $basic_profile );

		$first_name = '';
		$last_name  = '';
		$email      = '';
		if ( isset( $basic_profile->FirstName ) ) {
			$first_name = $basic_profile->FirstName;
		}
		if ( isset( $basic_profile->LastName ) ) {
			$last_name = $basic_profile->LastName;
		}
		if ( isset( $basic_profile->Email ) ) {
			$email = $basic_profile->Email;
		}
		if ( isset( $_SESSION['moymtest'] ) && $_SESSION['moymtest'] ) {
			
			$redirect_url = site_url();
			wp_safe_redirect( $redirect_url . '?option=show_attr' );
			exit;
		}

		$this->moym_new_user_login( $email, $first_name, $last_name, $moym_sessionid );
	}

	/**
	 * Creates/updates user's WP profile.
	 *
	 * @param string $email_addr     Email address of the user.
	 * @param string $first_name     First name of the user.
	 * @param string $last_name      Last name of the user.
	 * @param string $moym_sessionid Session ID received from YM.
	 * @return void
	 */
	public function moym_new_user_login( $email_addr, $first_name, $last_name, $moym_sessionid ) {

		$user_name    = $email_addr;
		$email        = $email_addr;
		$display_name = $first_name . ' ' . $last_name;
		$new_user     = 'false';

		if ( username_exists( $user_name ) || email_exists( $email ) ) {    // user is a member.

			$user_id = '';
			if ( username_exists( $user_name ) ) {
				$user    = get_user_by( 'login', $user_name );
				$user_id = $user->ID;
				if ( ! empty( $email ) ) {
					wp_update_user(
						array(
							'ID'         => $user_id,
							'user_email' => $email,
						)
					);
				}
			} elseif ( email_exists( $email ) ) {
				$user    = get_user_by( 'email', $email );
				$user_id = $user->ID;
			}
		} else {                                                                                  // this user is a new user.
			$random_password = wp_generate_password( 10, false );
			$new_user        = 'true';

			if ( ! empty( $user_name ) ) {
				$user_id = wp_create_user( $user_name, $random_password, $email );
			} else {
				wp_die( 'We could not sign you in. Please contact your administrator.', 'No Username received.' );
				exit();
			}
		}

		$this->moym_map_basic_attributes( $user_id, $first_name, $last_name, $display_name );
		$this->moym_create_auth_token( $user_name, $new_user );

		$redirect_url = site_url();
		wp_safe_redirect( $redirect_url );
		exit;
	}
	/**
	 * Updates basic WP profile attributes.
	 *
	 * @param int    $user_id      WP User ID of the user.
	 * @param string $first_name   First name of the user.
	 * @param string $last_name    Last name of the user.
	 * @param string $display_name Display name of the user.
	 * @return void
	 */
	public function moym_map_basic_attributes( $user_id, $first_name, $last_name, $display_name ) {

		if ( ! empty( $first_name ) ) {
				wp_update_user(
					array(
						'ID'         => $user_id,
						'first_name' => $first_name,
					)
				);
		}
		if ( ! empty( $last_name ) ) {
			wp_update_user(
				array(
					'ID'        => $user_id,
					'last_name' => $last_name,
				)
			);
		}
		if ( ! empty( $display_name ) ) {
			wp_update_user(
				array(
					'ID'           => $user_id,
					'display_name' => $display_name,
				)
			);
		}

	}
	/**
	 * Creates user session.
	 *
	 * @param string  $username Username of the user.
	 * @param boolean $new_user Whether the user is new or not.
	 * @return void
	 */
	public function moym_create_auth_token( $username, $new_user ) {
		$user = get_user_by( 'login', $username);
		if ( ! $user ) {
			$user = get_user_by( 'email', $username );
		}

		if ( $new_user ) {
			do_action( 'user_register', $user->ID );
		}

		do_action( 'wp_login', $user->user_login, $user );
		wp_set_auth_cookie( $user->ID, true );
	}
	/**
	 * Fetches YM access token.
	 *
	 * @param string $app_id     YM application ID.
	 * @param string $app_secret YM application secret.
	 * @param string $code       Authorization code issued by YM.
	 * @return string
	 */
	public function moym_get_access_token( $app_id, $app_secret, $code ) {

		global $moym_api_host;
		$url    = $moym_api_host . '/OAuth/GetAccessToken';
		$fields = array(
			'AppID'     => $app_id,
			'AppSecert' => $app_secret,
			'GrantType' => 'Code',
			'Code'      => $code,
		);

		$headers  = array(
			'Accept' => 'application/json',
			'Cookie' => 'ss-id=41POeyfOt1rfuE3zudex; ss-pid=uXiKrjunYce4x2A66jq4; X-UAId=ae1c6f0b-7413-40fe-b650-5e6d9da36859; .Stackify.Rum=930e9203-2700-4c03-a65d-9e80cd148840',
		);
		$args     = $this->moym_get_arguments( $fields, $headers );
		$response = moym_wp_remote_call( $url, $args );
		return $response;
	}
	/**
	 * Authenticates user on YM and fetches the session information.
	 *
	 * @param string $moym_app_id        YM application ID.
	 * @param string $moym_app_secret    YM application secret.
	 * @param string $moym_access_token  YM access token.
	 * @param string $moym_client_id     YM client ID.
	 * @param string $moym_user_type     YM user type.
	 */
	public function moym_authenticate_user( $moym_app_id, $moym_app_secret, $moym_access_token, $moym_client_id, $moym_user_type ) {

		global $moym_api_host;
		$url    = $moym_api_host . '/Ams/Authenticate';
		$fields = array(
			'ConsumerKey'    => $moym_app_id,
			'ConsumerSecret' => $moym_app_secret,
			'AccessToken'    => $moym_access_token,
			'ClientID'       => $moym_client_id,
			'UserType'       => $moym_user_type,
		);

		$headers  = array(
			'Accept' => 'application/json',
			'Cookie' => 'ss-pid=uXiKrjunYce4x2A66jq4',
		);
		$args     = $this->moym_get_arguments( $fields, $headers );
		$response = moym_wp_remote_call( $url, $args );
		return $response;
	}
	/**
	 * Creates arguments list required for API call.
	 *
	 * @param array $fields  Parameters to be sent in the post body.
	 * @param array $headers Headers to be sent with the request.
	 * @return array
	 */
	public function moym_get_arguments( $fields, $headers ) {
		$args = array(
			'method'      => 'POST',
			'body'        => $fields,
			'timeout'     => '5',
			'redirection' => '10',
			'httpversion' => '1.1',
			'blocking'    => true,
			'headers'     => $headers,
		);
		return $args;
	}
	/**
	 * Fetches the basic profile information from YM.
	 *
	 * @param string $moym_sessionid YM session ID of the user.
	 * @param string $moym_memberid  YM Member ID of the user.
	 * @param string $moym_clientid  YM client ID.
	 * @return string
	 */
	public function moym_get_user_basic_profile( $moym_sessionid, $moym_memberid, $moym_clientid ) {

		global $moym_api_host;
		$url = $moym_api_host . '/Ams/' . $moym_clientid . '/Member/' . $moym_memberid . '/BasicMemberProfile';

		$args = array(
			'headers' => array(
				'Content-Type' => 'application/json',
				'X-SS-ID'      => $moym_sessionid,
			),
		);

		$response = moym_wp_remote_call( $url, $args, true );
		return $response;
	}
	/**
	 * Display the feedback form.
	 */
	public function moym_feedback() {
		moym_feedback_form();
	}

}
