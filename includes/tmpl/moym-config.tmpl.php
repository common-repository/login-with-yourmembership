<?php
/**
 * File to displays the SSO settings tab.
 *
 * @package login-with-yourmembership\tmpl
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Function to displays the SSO settings tab.
 *
 * @return void
 */
function moym_show_settings_page() {
	global $moym_plugin_dir;
	echo '
        <script>
            function showTestWindow() {
                var myWindow = window.open("' . esc_url( site_url() ) . '/?option=moymsso&testconfig=true", "TEST YOURMEMBERSHIP SSO", "scrollbars=1 width=800, height=600");
            }
        </script>
        <div class="moym_table_layout">
        	<h2>Configure YourMembership Settings <font size="2px"></font></h2>
            <br>
		    <form name="moym_app_config_form" method="post" action="" id="moym_app_config_form">';
				wp_nonce_field( 'moym_save_app_config' );
	echo '	
				<input type="hidden" name="option" value="moym_save_app_config">
			    <table class="moym_settings_table">
			        <tr>
			            <td><b>Application: </b></td>
			            <td><div class="ym_logo" style="border:1px solid #7e8993;border-radius: 4px;">
                        <img src="' . esc_url( $moym_plugin_dir ) . 'includes/images/ym_image.png"/>
                        </div>  <a href="https://plugins.miniorange.com/setup-single-sign-on-sso-for-wordpress-using-yourmembership" target="_blank" rel="noopener noreferrer" class="button button-primary" style="margin-top:7%; margin-left: 5%;">Click here to open setup guide</a></td>
					</tr>
                    <tr>
                        <td></td>
                        <td><h4>YourMembership Application</h4></td>
                    </tr>
			        <tr>
			            <td><b>Redirect URL: </b></td>
			            <td> <input type="text" class="moym_table_textbox" name="moym_callback" readonly="true" value="' . esc_url( site_url() ) . '"></td>
					</tr>
					<tr>
			            <td><b>Client ID<span style="color:red;">*</span>: </b></td>
			            <td> <input type="text" class="moym_table_textbox" name="moym_clientID" pattern="[a-zA-Z0-9]+" required value="' . esc_attr( get_option( 'moym_clientID' ) ) . '"></td>
					</tr>
					<tr>
			            <td><b>App ID<span style="color:red;">*</span>: </b></td>
			            <td> <input type="text" class="moym_table_textbox" name="moym_appID" required value="' . esc_attr( get_option( 'moym_appID' ) ) . '"></td>
					</tr>
					<tr>
			            <td><b>App Secret<span style="color:red;">*</span>: </b></td>
			            <td> <input type="password" class="moym_table_textbox" name="moym_app_secret" required value="' . esc_attr( get_option( 'moym_app_secret' ) ) . '"></td>
					</tr>
					<tr>
			            <td><b>Scope: </b></td>
			            <td> <input type="text" class="moym_table_textbox" name="moym_scope" readonly="true" value="basic_profile"></td>
					</tr>
                    <tr>
                        <td></td>
                        <td><b><span style="color:red;">Note:</span> full_profile scope is available in <a href="' . esc_url( admin_url( 'admin.php?page=moym_settings&tab=pricing' ) ) . '">Premium and All-Inclusive</a> plans.</b></td>
                    </tr>
					<tr>
			            <td><b>Authorize Endpoint<span style="color:red;">*</span>: </b></td>
			            <td> <input type="text" class="moym_table_textbox" name="moym_authURL" required value="' . esc_attr( get_option( 'moym_authURL' ) ) . '"></td>
					</tr>
					<tr>
			            <td></td>
			            <td><p>
			            <input type="submit" name="submit" value="Save" class="button button-primary button-large">
			            <input type="button" name="test" title="You can test your Configuration only after configuring the app" onclick="showTestWindow();" ';
	if ( moym_check_empty_or_null( get_option( 'moym_clientID' ) ) || moym_check_empty_or_null( get_option( 'moym_appID' ) ) ) {
						echo 'disabled';
	}
				echo '
                        value="Test configuration" class="button button-primary button-large" style="margin-left: 3%;"/></p></td>
					</tr>
				</table>
		    </form>     
        </div>

        <div class="moym_table_layout" id="miniorange-use-sso-button">

            <h3>
                <b>Option 1: Use a Single Sign-On button</b>
                <sup style="font-size: 12px;">[Available in current version of the plugin]</sup>
            </h3>
            <hr>
            <div style="margin:2% 0 2% 17px;">
                <form id="moym_add_sso_button_wp_form" method="post" action="">';
					wp_nonce_field( 'moym_add_sso_button' );
					echo '<input type="hidden" name="option" value="moym_add_sso_button"/>
                    <p><label class="switch">
                        <input type="checkbox" name="moym_add_sso_button" value="true"';
	if ( moym_check_empty_or_null( get_option( 'moym_clientID' ) ) || moym_check_empty_or_null( get_option( 'moym_appID' ) ) ) {
		echo 'disabled';
	}
						checked( get_option( 'moym_add_sso_button' ) === 'true' );
						echo ' onchange="document.getElementById(\'moym_add_sso_button_wp_form\').submit();"/> 
                        <span class="slider round" title="You can add the button only after configuring the app"></span></label>
                        <span style="padding-left:5px"><b>Add a Single Sign on button on the WordPress login page</b></span>
                    </p>
                </form>
            
        </div>
        </br>


        <div style="background-color:#FFFFFF;position: relative" id="minorange-use-widget">
            <h3>
                <b>Option 2: Use a Widget</b>
                <sup style="font-size: 12px;"> [Available in current version of the plugin]</sup>
            </h3>
            <hr>
            <div style="margin:2% 0 2% 17px;">
                <p>Add the SSO Widget by following the instructions below. This will add the SSO link on your site.</p>
                <div id="moym_add_widget_steps">
                    <ol>
                        <li>Go to Appearances > <a href=" ' . esc_url( get_admin_url() ) . 'widgets.php">Widgets</a></li>
                        <li>Select "Login with YourMembership". Drag and drop to your favourite location and save.</li>
                    </ol>
                </div>
            </div>
            <br>
       
            <div style="background-color:#FFFFFF;position: relative" id="moym_short_code">
                <h3>
                    Option 3: Use a ShortCode
                    <sup style="font-size: 12px;">[Available in <a href="' . esc_url( admin_url( 'admin.php?page=moym_settings&tab=pricing' ) ) . '">Premium and All-Inclusive</a> plans]</sup>
                </h3>
                <hr>
                <div style="margin:2% 0 2% 17px;">
                    <label class="switch">
                      <input type="checkbox" disabled>
                      <span class="slider round"></span>
                    </label>

                    <span style="padding-left:5px">
                        <b><span style="color: red">*</span>
                            Check this option if you want to add a shortcode to your page
                        </b>
                    </span>
                </div>
                <br/>
            </div>
		
		    <h3>
                <b>Option 4: Auto Redirection</b>
                <sup style="font-size: 12px;">[Available in <a href="' . esc_url( admin_url( 'admin.php?page=moym_settings&tab=pricing' ) ) . '">Premium and All-Inclusive</a> plans]</sup>
            </h3>
            <hr>
            Enable the following option if you want to restrict your site to only logged in users. Any un-authenticated user trying to access your site will be redirected to YourMembership login.<br/><br/>
            <div style="margin:0 0 0 17px;">
                <input type="checkbox" name="moym_enable_auto_redirect" value="true" disabled>
                &nbsp;&nbsp; <strong><span style="color: red">*</span> Enable Auto Redirect to YM</strong>
            </div>
            <br><br>

            Enable the following option if you want the users visiting any of the following URLs to get redirected to YourMembership login page for authentication:
            <br><code><b>' . esc_url( wp_login_url() ) . '</b></code> or <code><b> ' . esc_url( admin_url() ) . '</b></code><br><br>
            <div style="margin:0 0 0 17px;">
                <input type="checkbox" name="moym_wp_redirect" value="true" disabled>
                &nbsp; &nbsp;<strong><span style="color: red">*</span> Enable Auto-redirect from WP Login</strong>
            </div>
            <br><br>
		
		    Enable this option to create a backdoor, using which you can login to your website using WordPress credentials, incase you get locked out of your YM account.
			<br><br>
			<input type="checkbox" name="moym_enable_backdoor" value="true" disabled>
			&nbsp;&nbsp;<strong><span style="color: red">*</span> Enable Backdoor Login</strong>
			<br><br>

            <i>
                <table width="100%">
                    <tr>
                        <td style="display:block;"><b>Backdoor URL:</b><br/>(Please note it down) </td>
                        <td>
                            <div style="background-color:#ededed;padding:1%;display:block;"><b>
                                ' . esc_url( site_url() ) . '/wp-login.php?ym_sso=
                                <input style="width:150px" type="text" id="backdoor_url" name="moym_backdoor_url" disabled value="false">
                            </b></div>
                        </td>
                    </tr>
                </table>
				<div style="display:block;text-align:center; margin:2%;">
				<button class="button button-primary button-large" disabled>Update</button>
				</div>
			</i>
			<div style="background-color:#CBCBCB;padding:1%;">
				<span style="color:#FF0000;">WARNING:</span>
                Checking the above option will <b>open a security hole</b>.
                Anybody knowing the above URL will be able to login to your website using WordPress Credentials.
                <b>Please do not share this URL.</b>
			</div>
			<br/><br>
		
            <span style="color:red;">*</span>
            These options are configurable in the <a href="' . esc_url( admin_url( 'admin.php?page=moym_settings&tab=pricing' ) ) . '"><b>Premium and All-Inclusive</b></a> version of the plugin.
            <br/>
        </div>';

}
