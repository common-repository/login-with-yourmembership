<?php
/**
 * This file will display the feedback form when the customer deactivates the plugin.
 *
 * @package login-with-yourmembership\tmpl
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Displays the feedback form upon plugin deactivation.
 *
 * @return void
 */
function moym_feedback_form() {
	global $moym_plugin_dir;
	if ( isset( $_SERVER['PHP_SELF'] ) && 'plugins.php' !== basename( sanitize_text_field( wp_unslash( $_SERVER['PHP_SELF'] ) ) ) ) {
		return;
	}
	?>

	<div id="moym_feedback_modal" class="moym-modal" style="width:90%; margin-left:12%; margin-top:5%; text-align:center;">
		<div class="moym-modal-content" style="width:50%;">
			<h3 style="margin: 2%; text-align:center;">
				<b>Your feedback</b>
			</h3>
			<hr style="width:75%;">
			<form name="f" method="post" action="" id="moym_feedback">
				<?php wp_nonce_field( 'moym_feedback' ); ?>
				<input type="hidden" name="option" value="moym_feedback"/>
				<div>
					<p style="margin:2%">
					<h4 style="margin: 2%; text-align:center;">Please help us improve our plugin by giving your opinion.<br></h4>

					<div id="moym_rate" style="text-align:center">
						<input type="radio" name="rate" id="angry" value="1"/>
						<label for="angry"><img class="moym-rate-img" src="<?php echo esc_url( $moym_plugin_dir ) . 'includes/images/angry.png'; ?>" />
						</label>

						<input type="radio" name="rate" id="sad" value="2"/>
						<label for="sad"><img class="moym-rate-img" src="<?php echo esc_url( $moym_plugin_dir ) . 'includes/images/sad.png'; ?>" />
						</label>

						<input type="radio" name="rate" id="neutral" value="3"/>
						<label for="neutral"><img class="moym-rate-img" src="<?php echo esc_url( $moym_plugin_dir ) . 'includes/images/normal.png'; ?>" />
						</label>

						<input type="radio" name="rate" id="smile" value="4"/>
						<label for="smile"><img class="moym-rate-img" src="<?php echo esc_url( $moym_plugin_dir ) . 'includes/images/smile.png'; ?>" />
						</label>

						<input type="radio" name="rate" id="happy" value="5" checked/>
						<label for="happy"><img class="moym-rate-img" src="<?php echo esc_url( $moym_plugin_dir ) . 'includes/images/happy.png'; ?>" />
						</label>

						<div id="outer" style="visibility:visible"><span id="result">Thank you for appreciating our work</span></div>
					</div><br>
					<hr style="width:75%;">
					<?php
					$email = get_option( 'moym_admin_email' );
					if ( empty( $email ) ) {
						$user  = wp_get_current_user();
						$email = $user->user_email;
					}
					?>
					<div style="text-align:center;">
						<div style="display:inline-block; width:60%;">
							<input type="email" id="moym_query_mail" name="query_mail" style="text-align:center; border:0px solid black; border-style:solid; background:#f0f3f7; width:20vw;border-radius: 6px;"
								placeholder="your email address" required value="<?php echo esc_attr( $email ); ?>" readonly="readonly"/>
							<input type="radio" name="edit" id="edit" onclick="moym_editName()" value=""/>
							<label for="edit"><img class="moym-edit" src="<?php echo esc_url( $moym_plugin_dir ) . 'includes/images/edit.png'; ?>" />
							</label>
						</div>
						<br><br>
						<textarea id="query_feedback" name="query_feedback" rows="4" style="width: 60%"
								placeholder="Tell us what happened!"></textarea>
						<br><br>
					</div>
					<br>

					<div class="moym-modal-footer" style="text-align: center;margin-bottom: 2%">
						<input type="submit" name="miniorange_feedback_submit"
							class="button button-primary button-large" value="Send"/>
						<span width="30%">&nbsp;&nbsp;</span>
						<input type="button" name="miniorange_skip_feedback"
							class="button button-primary button-large" value="Skip" onclick="document.getElementById('moym_feedback_form_close').submit();"/>
					</div>
				</div>
			</form>
			<form name="f" method="post" action="" id="moym_feedback_form_close">
				<?php wp_nonce_field( 'moym_skip_feedback' ); ?>
				<input type="hidden" name="option" value="moym_skip_feedback"/>
			</form>
		</div>
	</div>

	<script>

		jQuery('a[aria-label="Deactivate Login with YourMembership - YM SSO Login"]').click(function () {
			var mo_modal = document.getElementById('moym_feedback_modal');
			mo_modal.style.display = "block";
			document.querySelector("#query_feedback").focus();
			window.onclick = function (event) {
				if (event.target === mo_modal) {
					mo_modal.style.display = "none";
				}
			};
			return false;
		});
		INPUTS = document.querySelectorAll('#moym_rate input');
		INPUTS.forEach(el => el.addEventListener('click', (e) => updateValue(e)));

		function moym_editName(){
			document.querySelector('#moym_query_mail').removeAttribute('readonly');
			document.querySelector('#moym_query_mail').focus();
			return false;
		}

		function updateValue(e) {
			document.querySelector('#outer').style.visibility="visible";
			var result = 'Thank you for appreciating our work';
			switch(e.target.value){
				case '1':	result = 'Not happy with our plugin ? Let us know what went wrong';
					break;
				case '2':	result = 'Found any issues? Let us know and we\'ll fix it ASAP';
					break;
				case '3':	result = 'Let us know if you need any help';
					break;
				case '4':	result = 'We\'re glad that you are happy with our plugin';
					break;
				case '5':	result = 'Thank you for appreciating our work';
					break;
			}
			document.querySelector('#result').innerHTML = result;
		}
	</script><?php
}
?>
