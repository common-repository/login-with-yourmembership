<?php
/**
 * Creates WP widget.
 *
 * @package login-with-yourmembership\classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to create WordPress widget
 */
class MoYm_Login_Widget extends WP_Widget {

	/**
	 * Initialize mo_login_widget
	 */
	public function __construct() {
		parent::__construct(
			'MoYm_Login_Widget',
			'Login with Your Membership',
			array(
				'description'                 => __( 'This is a miniOrange YM login widget.', 'moymsso' ),
				'customize_selective_refresh' => true,
			)
		);
	}

	/**
	 * Widget UI
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance The settings for the particular instance of the widget.
	 *
	 * @return void
	 */
	public function widget( $args, $instance ) {
		$wid_title = apply_filters( 'widget_title', $instance['wid_title'] );
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $args['before_widget'] is html that needs to render on dom escaping will not render html.
		echo $args['before_widget'];
		if ( ! empty( $wid_title ) ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $args['before_widget'] and $args['after_title'] is html that needs to render on dom escaping will not render html.
			echo $args['before_title'] . esc_html( $wid_title ) . $args['after_title'];
		}
		$this->loginForm();
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $args['after_widget'] is html that needs to render on dom escaping will not render html.
		echo $args['after_widget'];
	}

	/**
	 * MiniOrange method to override parent method
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Settings to save or bool false to cancel saving.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance              = array();
		$instance['wid_title'] = sanitize_text_field( $new_instance['wid_title'] );
		return $instance;
	}

	/**
	 * Outputs the settings update form.
	 *
	 * @param array $instance Current settings.
	 * @return void
	 */
	public function form( $instance ) {
		$wid_title = '';
		if ( array_key_exists( 'wid_title', $instance ) ) {
			$wid_title = $instance['wid_title'];
		}
		?>
		<p><label for="<?php echo esc_attr( $this->get_field_id( 'wid_title' ) ); ?>"><?php esc_html_e( 'Title:' ); ?> </label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'wid_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'wid_title' ) ); ?>" type="text" value="<?php echo esc_attr( $wid_title ); ?>" />
		</p>
		<?php
	}

	/**
	 * Outputs SSO Login & Logout Buttons in form of a WordPress Widget.
	 *
	 * @return void
	 */
	public function loginForm() {
		if ( ! is_user_logged_in() ) {
			echo '
            <script>
                function submitMoYMForm(){ document.getElementById("miniorange-ym-sso-login-form").submit(); }
            </script>
            <form name="miniorange-ym-sso-login-form" id="miniorange-ym-sso-login-form" method="post" action="">';
				wp_nonce_field( 'moymsso' );
			echo '   
                <input type="hidden" name="option" value="moymsso" />
                <font size="+1" style="vertical-align:top;"> </font>';

			if ( ! empty( get_option( 'moym_app_secret' ) ) ) {
				echo '<a onClick="submitMoYMForm()">Login with Your Membership </a>';
			} else {
				echo 'Please configure the miniOrange YM Plugin first.';
			}

			echo '</form>';
		} else {
			$current_user       = wp_get_current_user();
			$link_with_username = __( 'Hello, ', 'moymsso' ) . $current_user->display_name;
			echo esc_html( $link_with_username );
			echo '| <a href="' . esc_url( wp_logout_url( moym_get_current_page_url() ) ) . '>Logout</a>';
		}
	}
}
