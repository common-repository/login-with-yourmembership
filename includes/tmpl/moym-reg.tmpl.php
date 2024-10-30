<?php
/** This file contains the function to display the registration page.
 *
 * @package     login-with-yourmembership\tmpl
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Displays the registration form to create the new account.
 */
function moym_show_new_registration_page() {
	update_option( 'moym_new_registration', 'true' );
	?>
	<!--Register with miniOrange-->
	<form name="f" method="post" action="" id="register-form">
		<?php wp_nonce_field( 'moym_register_customer' ); ?>
		<input type="hidden" name="option" value="moym_register_customer" />
		<div class="moym_table_layout">
			<h3>Register with miniOrange</h3>
			<p style="font-size:14px;"><b>Why should I register? </b></p>
			<div style="background: aliceblue; padding: 10px 10px 10px 10px; border-radius: 10px;">
				You should register so that in case you need help, we can help you with step by step instructions. <b>You will also need a miniOrange account to upgrade to the premium version of the plugin.</b> We do not store any information except the email that you will use to register with us.
			</div>
			<br>

			<table class="moym_settings_table">
				<tr>
					<td><b><font color="#FF0000">*</font>Email:</b></td>
					<td><input class="moym_table_textbox" type="email" name="email"
						required placeholder="person@example.com"
						value="<?php echo esc_attr( get_option( 'moym_admin_email' ) ); ?>" /></td>
				</tr>
				<tr>
					<td><b><font color="#FF0000">*</font>Password:</b></td>
					<td><input class="moym_table_textbox" required type="password"
							name="password" placeholder="Choose your password (Min. length 6)" /></td>
				</tr>
				<tr>
					<td><b><font color="#FF0000">*</font>Confirm Password:</b></td>
					<td><input class="moym_table_textbox" required type="password"
							name="confirmPassword" placeholder="Confirm your password" /></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td><br>
						<input type="submit" name="submit" value="Register" style="width:100px;"
							class="button button-primary button-large" />
						<input type="button" name="moym_goto_login" id="moym_goto_login" value="Already have an account?"
							class="button button-primary button-large"/>
					</td>
				</tr>
			</table>
			<br/>
		</div>
	</form>
	<form name="f1" method="post" action="" id="moym_goto_login_form">
		<?php wp_nonce_field( 'moym_goto_login' ); ?>
		<input type="hidden" name="option" value="moym_goto_login"/>
	</form>
	<script>
		var text = "&nbsp;&nbsp;We will call only if you need support."
		jQuery('.intl-number-input').append(text);

		jQuery('#moym_goto_login').click(function () {
			jQuery('#moym_goto_login_form').submit();
		});
	</script>

	<?php
}

?>
