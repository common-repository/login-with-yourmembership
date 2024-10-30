<?php
/**
 * Show Pricing Details for premium services.
 *
 * @package     login-with-yourmembership\tmpl
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Show Pricing Details for premium services.
 */
function moym_pricing_info() {
	$hostname    = get_option( 'moym_host_name' );
	$login_url   = $hostname . '/moas/login';
	$username    = get_option( 'moym_admin_email' );
	$payment_url = $hostname . '/moas/initializepayment';

	echo '
        <div class="moym_table_layout">
            <input type="hidden" value="' . esc_attr( moym_is_customer_registered() ) . '" id="moym_customer_registered">
	        <form style="display:none;" id="moym_loginform" action="' . esc_attr( $login_url ) . '" target="_blank" method="post">
				<input type="email" name="username" value="' . esc_attr( $username ) . '" />
				<input type="text" name="redirectUrl" value="' . esc_attr( $payment_url ) . '" />
				<input type="text" name="requestOrigin" id="requestOrigin"  />
			</form>
			<a id="moym_backto_accountsetup" style="display:none;" href="' . esc_url( admin_url( 'admin.php?page=moym_settings&tab=register' ) ) . '">Back</a>
			
			<table class="moym_pricing_table">
                <h2>LICENSING PLANS - <font color="cadetblue">You are currently on the FREE Version of the plugin</font></h2>
                <hr>
                <div style="padding:0px">
                <div class="moym_plan_col">
                    <div class="moym_plan_header">
                        <p class="moym_plan_heading" >Premium</p>
                        <div class="moym_plan_price" >
                            <span class="moym_plan_currency">$</span>
                            <span class="moym_plan_value">449&nbsp;</span></span>
                        </div>
                    </div>
                    <div class="moym_plan_footer" onclick="window.open(\'https://www.miniorange.com/contact\', \'_blank\');">
                        <a href="#" class="moym_plan_select" >Upgrade Now</a>
                    </div>
                    <br>
                    <div class="moym_plan_body" style="text-align: center;">
                        <b style="font-weight: 600; font-size: 18px;">Premium Features</b>
                        <ul class="moym_plan_features">
                            <li>Redirect to YM Login Page for Authentication</li>
                            <li>Unlimited Authentications</li>
                            <li>Widget, Shortcode for SSO</li>
                            <li>SSO Login Link</li>
                            <li>Basic Attribute Mapping</li>
                            <li>Default Role Mapping</li>
                            <li>Auto-Redirect to YourMembership</li>
                            <li>Protect Complete Site</li>
                            <li>&nbsp;</li>
                            <li>&nbsp;</li>
                            <li>&nbsp;</li>
                        </ul>
                        <b>Add-ons</b><br>
                        <ul>
                            <li>Purchase Separately</li>
                            <li><a style="color:blue; font-size:14px;" href="https://www.miniorange.com/contact" target="_blank"><b> Contact us</b></a></li>
                            <li> &nbsp;</li>
                            <li> &nbsp;</li>
                            <li> &nbsp;</li> 
                        </ul>
                        <a><b></b></a>
                        <br><br>
                    </div>
                </div>
                
                <div class="moym_plan_col">
                    <div class="moym_plan_header">
                        <p class="moym_plan_heading" >All-Inclusive</p>
                        <div class="moym_plan_price" >
                            <span class="moym_plan_currency">$</span>
                            <span class="moym_plan_value">649&nbsp;</span></span>
                        </div>
                    </div>
                    <div class="moym_plan_footer" onclick="window.open(\'https://www.miniorange.com/contact\', \'_blank\');">
                        <a href="#" class="moym_plan_select">Upgrade Now</a>
                    </div>
                    <br>
                    <div class="moym_plan_body" style="text-align: center;">
                        <b style="font-weight: 600; font-size: 18px;">All-Inclusive Features</b>
                        <ul class="moym_plan_features">
                            <li>Unlimited Authentications</li>
                            <li>Widget, Shortcode for SSO</li>
                            <li>SSO Login Link</li>
                            <li>Basic Attribute Mapping</li>
                            <li>Default Role Mapping</li>
                            <li>Auto-Redirect to YourMembership</li>
                            <li>Protect Complete Site</li>
                            <li>Custom Attribute Mapping</li>
                            <li>Advanced Role Mapping</li>
                            <li>Multisite network support</li>
                            <li>One time setup support</li>
                        </ul>
                        <b>Add-ons</b><br>
                        <ul>
                            <li>Page Restriction</li>
                            <li>Attribute-based Restriction/Redirection</li>
                            <li>Media Restriction</li>
                            <li>SSO Login Audit</li>
                            <li>WP User Role Editor</li> 
                        </ul>
                        <a style="color:blue; font-size:14px;" href="https://www.miniorange.com/contact" target="_blank"><b>Contact us</b></a>
                        <br><br>
                    </div>
                </div>
                </div>
			
			</table>
			<div id="disclaimer" style="margin-bottom:15px;">
				<div style="text-align:left; font-size:12px;  padding-right:30px;">
                <br><br>
                *If you have any doubts regarding the licensing plans, you can mail us at <a href="mailto: samlsupport@xecurify.com"> samlsupport@xecurify.com</a> or submit a query using the <b>support form</b> on right.
                <br><br>
                    <h3>Steps to Upgrade to Premium Plugin -</h3>
                    <p>1. Click on Upgrade now button of the required licensing plan. You will be redirected to miniOrange Login Console. Enter your password with which you created an account
                        with us. After that you will be redirected to payment page.</p>
                    <p>2. Enter your card details and complete the payment. On successful payment completion, you will see the link
                        to download the premium plugin.</p>
                    <p>3. To install the premium plugin, first deactivate and delete the free version of the plugin. Enable the "Keep Configuration Intact" checkbox before deactivating and deleting the plugin. By doing so, your saved configurations of the plugin will not get lost.</p>
                    <p>4. From this point on, do not update the premium plugin from the WordPress store.</p>
        
                    <h3>* Cost applicable for one instance only. Licenses are subscription-based and the Support Plan includes 12 months of maintenance (support and version updates). You can renew maintenance after 12 months at 50% of the current license cost.</h3>
                    <br/>
                    <li class="bottom-heading"> MultiSite Network Support -<br></li>
                    <p style="padding-left:14px"><b>*</b> There is an additional cost for the number of subsites in Multisite Network.</p>
                    <br/>
                    <p>
                        <strong>Note :</strong> miniOrange does not store or transfer any data which is coming from the Identity Provider to the WordPress. All the data remains within your premises / server. We do not provide the developer license for our paid plugins and the source code is protected. It is strictly prohibited to make any changes in the code without having written permission from miniOrange. There are hooks provided in the plugin which can be used by the developers to extend the plugin\'s functionality.
                    </p>
        
                    <h3>10 Days Return Policy -</h3>
                    At miniOrange, we want to ensure you are 100% happy with your purchase. If the premium plugin you purchased is
                    not working as advertised and you\'ve attempted to resolve any issues with our support team, which couldn\'t get
                    resolved. We will refund the whole amount within 10 days of the purchase. Please email us at <b><a href="mailto:info@xecurify.com">info@xecurify.com</a></b>
                    for any queries regarding the return policy.

                </div>
			</div>
		</div>';
}
