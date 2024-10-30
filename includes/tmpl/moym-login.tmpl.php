<?php
/** This file contains the function to display the login page.
 *
 * @package     login-with-yourmembership\tmpl
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Displays the miniOrange login form for user to login.
 */
function moym_show_verify_password_page() {
	echo ' <!--Verify password with miniOrange-->
			<form name="f" method="post" action="">';
				wp_nonce_field( 'moym_connect_verify_customer' );
	echo '
				<input type="hidden" name="option" value="moym_connect_verify_customer" />
				<div class="moym_table_layout">
				    <h3>Login with miniOrange</h3>
					<p><b>It seems you already have an account with miniOrange. Please enter your miniOrange email and password. <a href="https://login.xecurify.com/moas/idp/resetpassword">Click here if you forgot your password?</a></b></p>
					<table class="moym_settings_table">
						<tr>
							<td><b><font color="#FF0000">*</font>Email:</b></td>
							<td><input class="moym_table_textbox" id="email" type="email" name="email"
								required placeholder="person@example.com"
								value="' . esc_attr( get_option( 'moym_admin_email' ) ) . '" /></td>
						</tr>
						<td><b><font color="#FF0000">*</font>Password:</b></td>
						<td><input class="moym_table_textbox" required type="password"
							name="password" placeholder="Choose your password" /></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>
							    <input type="submit" name="submit" class="button button-primary button-large" />
							    <input type="button" value="Register?" id="moym_go_back" class="button button-primary button-large" />
							</td>
						</tr>
					</table>
				</div>
			</form>
			<form name="f" method="post" action="" id="ymgobackform">';
				wp_nonce_field( 'moym_go_back' );
	echo '		<input type="hidden" name="option" value="moym_go_back"/>
			</form>
			<form name="forgotpassword" method="post" action="" id="ymforgotpasswordform">';
				wp_nonce_field( 'moym_forgot_password' );
		echo '	<input type="hidden" name="option" value="moym_forgot_password"/>
				<input type="hidden" id="forgot_pass_email" name="email" value=""/>
			</form>
			<script>
				jQuery(\'#moym_go_back\').click(function() {
					jQuery(\'#ymgobackform\').submit();
				});
				jQuery(\'a[href="#forgot_password"]\').click(function(){
					jQuery(\'#forgot_pass_email\').val(jQuery(\'#email\').val());
					jQuery(\'#ymforgotpasswordform\').submit();
				});
			</script>';
}

/**
 * Displays the registered customer details.
 */
function moym_show_customer_details() {
	?>
	<div style="display:block;margin-top:1px;background-color:rgba(255, 255, 255, 255);padding-left:10px;border:solid 1px rgba(255, 255, 255, 255);border:solid 1px  #CCCCCC"; >
		<h2>Thank you for registering with miniOrange.</h2>
		<div style="padding: 10px;">
			<table border="1" style="background-color:#FFFFFF; border:1px solid #CCCCCC; border-collapse: collapse; margin-bottom:15px; width:85%">
				<tr>
					<td style="width:45%; padding: 10px;">miniOrange Account Email</td>
					<td style="width:55%; padding: 10px;"><?php echo esc_html( get_option( 'moym_admin_email' ) ); ?></td>
				</tr>
				<tr>
					<td style="width:45%; padding: 10px;">Customer ID</td>
					<td style="width:55%; padding: 10px;"><?php echo esc_html( get_option( 'moym_admin_customer_key' ) ); ?></td>
				</tr>
			</table>

			<table>
				<tr>
					<td>
						<form name="f1" method="post" action="" id="moym_goto_login_form">
							<?php wp_nonce_field( 'moym_go_back' ); ?>
							<input type="hidden" value="moym_go_back" name="option"/>
							<input type="submit" value="Change Email Address" class="button button-primary button-large"/>
						</form>
					</td>
					<td>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=moym_settings&tab=pricing' ) ); ?>"><input type="button" class="button button-primary button-large" value="Check Premium Plans"/></a>
					</td>
				</tr>
			</table>
		</div>
		<br>
	</div>

	<?php
}


?>
