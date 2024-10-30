<?php
/**
 * File to define the global variables.
 *
 * @package login-with-yourmembership\includes\utils
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Defines the global variables.
 */
class MoYm_Global_Variables {
	/**
	 * Constructor for defining the global variables.
	 */
	public function __construct() {
		global $moym_customer_action,$moym_customer,$moym_config_action,$moym_sso_action,$moym_api_host,$moym_plugin_path;
		$moym_api_host        = 'https://ws.yourmembership.com';
		$moym_customer_action = new MoYm_Customer_Action();
		$moym_customer        = new MoYm_Customer();
		$moym_config_action   = new MoYm_Config_Action();
		$moym_sso_action      = new MoYm_Sso();
	}
}

