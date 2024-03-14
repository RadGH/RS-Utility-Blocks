<?php

/**
 * @global   array $block The block settings and attributes.
 * @global   string $content The block inner HTML (empty).
 * @global   bool $is_preview True during backend preview render.
 * @global   int $post_id The post ID the block is rendering content against.
 *           This is either the post ID currently being displayed inside a query loop,
 *           or the post ID of the post hosting this block.
 * @global   array $context The context provided to the block by the post or it's parent block.
 */

// Custom Fields
$settings = get_field( 'settings', $block['id'] );

$show_forgot_password = in_array('show_forgot_password', $settings);
$show_logout_link = in_array('show_logout_link', $settings);
$show_register_link = in_array('show_register_link', $settings);
$disable_custom_style = in_array('disable_custom_style', $settings);

$create_account_url = get_field( 'create_account_url', $block['id'] );

$login_redirect = get_field( 'login_redirect', $block['id'] );

// Get action
$action = isset( $_REQUEST['action'] ) ? stripslashes($_REQUEST['action']) : 'login';
$redirect_to = ! empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '';

if ( $login_redirect && ! $redirect_to ) {
	$redirect_to = $login_redirect;
}

// Other Variables
$classes = array( 'rs-utility-login-form' );

$login_text = 'Already have your password? <a href="%1$s">Sign In</a>';
$login_url = add_query_arg(array('action' => 'login'));

$create_account_text = 'Don’t have an account? <a href="%1$s">Register Now</a>';

$logout_text = 'You are already signed in. Would you like to <a href="%1$s">sign out</a>?';
$logout_url = wp_logout_url( apply_filters( 'lostpassword_redirect', $redirect_to ) );

$forgot_password_text = 'Forgot your password?';
$forgot_password_url = add_query_arg(array('action' => 'lostpassword'));

// Prepare other links
$login_link_html = false;
$forgot_password_html = false;
$create_account_html = false;

if ( $show_logout_link && $logout_text ) {
	$login_link_html = sprintf(
		'<p class="login-logout-link">'. $logout_text .'</p>',
		esc_attr($logout_url)
	);
}

if ( $show_forgot_password && $forgot_password_text && $forgot_password_url ) {
	$forgot_password_html = sprintf( '<p class="login-forgot-password"><span><a href="%s">%s</a></span></p>',
		esc_attr($forgot_password_url),
		esc_html($forgot_password_text)
	);
}

if ( $show_register_link && $create_account_text && $create_account_url ) {
	$create_account_html = sprintf(
		'<p class="login-register-link"><span>'. $create_account_text .'</span></p>',
		esc_attr($create_account_url)
	);
}


// Manage block editor appearance
if ( is_user_logged_in() && ( ! is_admin() && ! acf_is_block_editor() ) ) {
	// If the user is logged in, but not in the admin or block editor, show a link to sign out
	$action = 'logout';
}else if ( is_admin() || acf_is_block_editor() ) {
	// If the user is in the admin or block editor, show the login form normally
	$action = 'login';
	$classes[] = 'disable-form';
}

/**
 * (From wp-login.php)
 *
 * Fires before a specified login form action.
 *
 * Possible hook names include:
 *
 *  - `login_form_login`
 *  - `login_form_logout`
 *  - `login_form_lostpassword`
 */
do_action( "login_form_{$action}" );

// Add additional classes
$classes[] = 'action-' . strtolower($action);

if ( ! $disable_custom_style ) {
	$classes[] = 'apply-styles';
}

// Start output
$atts = array(
	'class' => implode(' ', $classes),
);

if ( !empty($block['anchor']) ) $atts['id'] = $block['anchor'];

echo '<div '. get_block_wrapper_attributes( $atts ) .'>';

switch( $action ) {
	
	case 'login':
		$args = array(
			'echo' => false,
			'redirect' => $redirect_to,
			'form_id' => 'loginform',
			'label_username' => __( 'Username' ),
			'label_password' => __( 'Password' ),
			'label_remember' => __( 'Remember Me' ),
			'label_log_in' => __( 'Log In' ),
			'id_username' => 'user_login',
			'id_password' => 'user_pass',
			'id_remember' => 'rememberme',
			'id_submit' => 'wp-submit',
			'remember' => true,
			'value_username' => NULL,
			'value_remember' => false,
		);
		
		$extra_html = '';
		
		if ( $forgot_password_html ) {
			$extra_html .= "\n" . $forgot_password_html;
		}
		
		if ( $create_account_html ) {
			$extra_html .= "\n" . $create_account_html;
		}
		
		$html = wp_login_form($args);
		
		$html = str_replace('</form>', $extra_html . "\n\n" . '</form>', $html );
		
		// Use a block button
		$html = str_replace('class="login-submit"', 'class="submit login-submit wp-block-button"', $html);
		$html = str_replace('button button-primary', 'button button-primary wp-block-button__link wp-element-button', $html);
	
		echo $html;
		
		break;
	
		
		
	case 'lostpassword':
		$redirect_to = apply_filters( 'lostpassword_redirect', $redirect_to );
		
		$user_login = '';
		
		if ( isset( $_POST['user_login'] ) && is_string( $_POST['user_login'] ) ) {
			$user_login = wp_unslash( $_POST['user_login'] );
		}
		?>
		<form name="lostpasswordform" id="lostpasswordform" action="<?php echo esc_url( network_site_url( 'wp-login.php?action=lostpassword', 'login_post' ) ); ?>" method="post">
			
			<p>Please enter your username or email address. You will receive an email message with instructions on how to reset your password.</p>
			
			<p class="login-username">
				<label for="user_login"><?php _e( 'Username or Email Address' ); ?></label>
				<input type="text" name="user_login" id="user_login" class="input" value="<?php echo esc_attr( $user_login ); ?>" size="20" autocapitalize="off" autocomplete="username" required="required" />
			</p>
			<?php
			
			/**
			 * Fires inside the lostpassword form tags, before the hidden fields.
			 *
			 * @since 2.1.0
			 */
			do_action( 'lostpassword_form' );
			
			?>
			<input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>" />
			
			<p class="submit wp-block-button">
				<input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large wp-block-button__link wp-element-button" value="<?php esc_attr_e( 'Get New Password' ); ?>" />
			</p>
			
			<?php
			echo sprintf(
				'<p class="login-sign-in"><span>'. $login_text .'</span></p>',
				esc_attr($login_url)
			);
			?>
		</form>
		<?php
		
		break;
		
		
		
	case 'logout':
		if ( $login_link_html ) {
			echo $login_link_html;
		}
	
		break;
	
}

echo '</div>';