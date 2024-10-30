<?php
/**
 * This file contains functions to render the plugin tabs.
 *
 * @package login-with-yourmembership
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
require 'includes/tmpl/moym-config.tmpl.php';
require 'includes/tmpl/moym-license.tmpl.php';
require 'includes/tmpl/moym-login.tmpl.php';
require 'includes/tmpl/moym-reg.tmpl.php';
require 'includes/tmpl/moym-support.tmpl.php';
require 'includes/tmpl/moym-attr.tmpl.php';

/**
 * This function checks the 'tab' parameter in the URL to determine the active tab.
 *
 * @return void
 */
function moym_show_page() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- reading tab name
	if ( isset( $_GET['tab'] ) ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- reading tab name
		$active_tab = sanitize_key( $_GET['tab'] );
	} else {
		$active_tab = 'config';
	}

	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	if ( moym_is_curl_installed() === 0 ) {
		echo '<p style="color:red;">(Warning: <a href="http://php.net/manual/en/curl.installation.php" target="_blank">PHP CURL extension</a> is not installed or disabled) Please go to Troubleshooting for steps to enable cURL.</p>';
	}

	echo '
        <div style="margin-top: 20px; margin-bottom: 10px; display: flex;">
            <div style="float:left; width:35%; font-size: 22px; padding-top: 10px;"> miniOrange YourMembership Single Sign-On </div>
            <div style="float:left; width:30%;">
                <button class="button button-large button-primary" onclick="window.open(\'https://faq.miniorange.com/\', \'_blank\');">FAQs</a></button>
                &nbsp;&nbsp;&nbsp;<button class="button button-large button-secondary" onclick="window.open(\'https://forum.miniorange.com/\', \'_blank\');">Ask questions on our forum</button>
            </div>
        </div>';
	echo '
        <div id="tab">
            <h2 class="nav-tab-wrapper">
                <a class="nav-tab ' . ( 'config' === $active_tab ? 'nav-tab-active' : '' ) . '" href="' . esc_url( add_query_arg( array( 'tab' => 'config' ), $request_uri ) ) . '">SSO Settings</a>
                <a class="nav-tab ' . ( 'attr' === $active_tab ? 'nav-tab-active' : '' ) . '" href="' . esc_url( add_query_arg( array( 'tab' => 'attr' ), $request_uri ) ) . '">Attribute/Role Mapping</a>
                <a class="nav-tab ' . ( 'pricing' === $active_tab ? 'nav-tab-active' : '' ) . '" href=" ' . esc_url( add_query_arg( array( 'tab' => 'pricing' ), $request_uri ) ) . '">Licensing Plans</a>
                <a class="nav-tab ' . ( 'register' === $active_tab ? 'nav-tab-active' : '' ) . '" href="' . esc_url( add_query_arg( array( 'tab' => 'register' ), $request_uri ) ) . '">Account Setup</a>
            </h2>
	    </div>

	    <div id="moym_settings">
		    <div class="moym_container">
			    <div id="moym_msgs"></div>
			    <table style="width:100%;">
				<tr>
			    <td style="vertical-align:top;width:65%;">';
	if ( 'config' === $active_tab ) {
		moym_show_settings_page();
	} elseif ( 'attr' === $active_tab ) {
		moym_attr_mapping();
	} elseif ( 'pricing' === $active_tab ) {
		moym_pricing_info();
	} elseif ( 'register' === $active_tab ) {
		if ( moym_is_customer_registered() ) {
			moym_show_customer_details();
		} else {
			if ( get_option( 'moym_verify_customer' ) === 'true' ) {
				moym_show_verify_password_page();
			} elseif ( trim( get_option( 'moym_admin_email' ) ) !== '' && trim( get_option( 'moym_admin_api_key' ) ) === '' && get_option( 'moym_new_registration' ) !== 'true' ) {
				moym_show_verify_password_page();
			} elseif ( ! moym_is_customer_registered() ) {
				delete_option( 'password_mismatch' );
				moym_show_new_registration_page();
			}
		}
	}
				echo '      </td>
			    <td style="vertical-align:top;padding-left:1%;">';
				moym_support();
				echo '      </td>
				</tr>
			    </table>
		    </div>
		</div>';
}



