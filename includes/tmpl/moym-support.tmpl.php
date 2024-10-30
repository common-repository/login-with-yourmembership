<?php
/**
 * This file takes care of rendering the support form.
 *
 * @package login-with-yourmembership\tmpl
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * The function displays the support form in the plugin.
 */
function moym_support() {
	$moym_admin_email = get_option( 'moym_admin_email' ) ? get_option( 'moym_admin_email' ) : get_option( 'admin_email' );
	echo '
        <div class="moym_support_layout">
			<h3>Support</h3>
			<p>Need any help? Just send us a query so we can help you.</p>
			<form method="post" action="">';
			wp_nonce_field( 'moym_contact_us_query_option' );
	echo '
				<input type="hidden" name="option" value="moym_contact_us_query_option" />
				<table class="moym_settings_table">
					<tr>
						<td><input type="email" class="moym_table_contact" required placeholder="Enter your Email" 
							name="moym_contact_us_email" value="' . esc_attr( $moym_admin_email ) . '"></td>
					</tr>
					<tr>
					    <td>
                        	<input type="tel" id="moym_contact_us_phone"
                                   pattern="[\+]\d{11,14}|[\+]\d{1,4}[\s]\d{9,10}"
                                   class="moym_table_contact"
                                   name="moym_contact_us_phone"
                                   value="' . esc_attr( get_option( 'moym_admin_phone' ) ) . '"
                                   placeholder="Enter your phone">
                        </td>
                    </tr>
					<tr>
						<td><textarea class="moym_table_contact" placeholder="Write your query here"
							required name="moym_contact_us_query" rows="4" style="resize: vertical;"></textarea></td>
					</tr>
				</table>
				<br>
			<input type="submit" name="submit" value="Submit Query" style="width:110px;" class="button button-primary button-large" />

			</form>
			<p>If you want custom features in the plugin, just drop an email at <a href="mailto:info@xecurify.com">info@xecurify.com</a>.</p>
		</div>
		</div>
		</div>
		</div>
		<script>
		jQuery(document).ready(function() {	
			jQuery("#moym_contact_us_phone").intlTelInput();
		});
		</script>';
}
