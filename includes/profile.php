<?php

class RS_Utility_Blocks_Profile {
	
	/**
	 * Store errors to display on the form
	 * @var array
	 */
	private static $errors = array();
	
	/**
	 * Initialized when the plugin is loaded
	 *
	 * @return void
	 */
	public static function init() {
		
		// Save edit profile form on submit
		add_action( 'init', array( __CLASS__, 'save_edit_profile' ) );
		
	}
	
	/**
	 * Check if on using the block editor (or on the backend in some other way)
	 *
	 * @return true
	 */
	public static function is_block_editor() {
		return is_admin() || acf_is_block_editor();
	}
	
	/**
	 * Display the edit profile form
	 *
	 * @param array $block  The block settings and attributes, optional.
	 * @param int $post_id  The post ID currently being displayed
	 *
	 * @return void
	 */
	public static function display_edit_profile_form( $block, $post_id ) {
		$user_id = get_current_user_id();
		
		// Get block fields
		$fields = get_field( 'fields', $block['id'] ); // [ name, email, password, bio, website ]
		$show_confirm_email = get_field( 'show_confirm_email', $block['id'] );
		$show_confirm_password = get_field( 'show_confirm_password', $block['id'] );

		// Get form ID
		$form_id = 'rs_ub_edit_profile';
		if ( !empty($block['anchor']) ) $form_id = $block['anchor'];

		// Get submitted form data
		$data = isset($_POST['rs_ub']) ? stripslashes_deep($_POST['rs_ub']) : array();
		$field_values = isset($data['field']) ? $data['field'] : array();
		
		// Display a message on success
		if ( isset($_GET['rs_ub_success']) ) {
			?>
			<div class="rs-notice rs-notice--success">Your profile has been updated.</div>
			<?php
		}
		
		// Check if any validation errors occurred during submission
		if ( !empty( self::$errors ) ) {
			?>
			<div class="rs-notice rs-notice--error">
				<p>Please correct the following:</p>
				<ul class="rs-validation-error-list">
					<?php
					foreach( self::$errors as $error ) {
						echo '<li>'. $error .'</li>';
					}
					?>
				</ul>
			</div>
			<?php
		}
		
		?>
		<form action="" method="POST" id="<?php echo esc_attr($form_id); ?>" class="rs-edit-profile">
			
			<input type="hidden" name="rs_ub[action]" value="edit-profile">
			<input type="hidden" name="rs_ub[nonce]" value="<?php echo wp_create_nonce('edit-profile'); ?>">
			
			<div class="rs-edit-profile__fields">
				
				<?php
				do_action( 'rs_utility_blocks/before_edit_profile_fields', $user_id, $post_id, $block );
				?>
		
				<?php
				foreach( $fields as $field_type ) {
					if ( ! $field_type ) continue;
					
					$field_class = array( 'rs-field' );
					$field_class[] = 'rs-field--'. $field_type;
					
					switch( $field_type ) {
						case 'name':
							$field_name = 'first_name';
							$field_label = 'Name:';
							$field_value = get_the_author_meta( 'first_name', $user_id );
							if ( ! $field_value ) $field_value = get_the_author_meta( 'display_name', $user_id );
							$input_type = 'text';
							$required = 'required';
							$placeholder = 'First Name';
							break;
						case 'email':
							$field_name = 'user_email';
							$field_label = 'Email:';
							$field_value = get_the_author_meta( 'user_email', $user_id );
							$input_type = 'email';
							$required = 'required';
							$placeholder = 'Email Address';
							break;
						case 'password':
							$field_name = 'user_pass';
							$field_label = 'Password:';
							$field_value = '';
							$input_type = 'password';
							$required = '';
							$placeholder = 'Password';
							break;
						case 'bio':
							$field_name = 'description';
							$field_label = 'Biographical Info:';
							$field_value = get_the_author_meta( 'description', $user_id );
							$input_type = 'textarea';
							$required = '';
							$placeholder = 'Enter your bio';
							break;
						case 'website':
							$field_name = 'user_url';
							$field_label = 'Website:';
							$field_value = get_the_author_meta( 'user_url', $user_id );
							$input_type = 'url';
							$required = '';
							$placeholder = 'Website';
							break;
						default:
							continue 2;
					}
					
					// If user tried saving a value, use that instead
					if ( isset($field_values[$field_name]) ) {
						$field_value = $field_values[$field_name];
					}
					
					// Output field
					?>
					<div class="<?php echo esc_attr(implode(' ', $field_class)); ?>">
						
						<label class="rs-field-label" for="rs-<?php echo $field_name; ?>"><?php echo $field_label; ?></label>
						
						<div class="rs-field-input">
							<?php
							
							// Display the input
							self::display_field( $input_type, $field_name, $field_value, $required, $placeholder );
							
							// Display advanced inputs
							if ( $field_type == 'name' ) {
								$placeholder = 'Last Name';
								$confirm_label = 'Last Name';
								$confirm_name = 'last_name';
								$confirm_value = $field_values['last_name'] ?? '';
								if ( ! $confirm_value ) $confirm_value = get_the_author_meta( 'last_name', $user_id );
								?>
								<label class="screen-reader-text" for="rs-<?php echo $confirm_name; ?>"><?php echo $confirm_label; ?></label>
								<?php
								self::display_field( $input_type, $confirm_name, $confirm_value, $required, $placeholder );
							}
							
							if ( $field_type == 'password' && $show_confirm_password ) {
								$placeholder = 'Confirm Password';
								$confirm_label = 'Confirm Password';
								$confirm_name = 'confirm_password';
								$confirm_value = $field_values['confirm_password'] ?? '';
								if ( self::is_block_editor() ) $confirm_value = '';
								?>
								<label class="screen-reader-text" for="rs-<?php echo $confirm_name; ?>"><?php echo $confirm_label; ?></label>
								<?php
								self::display_field( $input_type, $confirm_name, $confirm_value, $required, $placeholder );
							}
							
							if ( $field_type == 'email' && $show_confirm_email ) {
								$placeholder = 'Confirm Email';
								$confirm_label = 'Confirm Email';
								$confirm_name = 'confirm_email';
								$confirm_value = $field_values['confirm_email'] ?? '';
								?>
								<label class="screen-reader-text" for="rs-<?php echo $confirm_name; ?>"><?php echo $confirm_label; ?></label>
								<?php
								self::display_field( $input_type, $confirm_name, $confirm_value, $required, $placeholder );
							}
							?>
						</div>
					
					</div>
					<?php
					
				}
				?>
				
				<?php
				do_action( 'rs_utility_blocks/after_edit_profile_fields', $user_id, $post_id, $block );
				?>
				
			</div>
			
			<div class="rs-submit wp-block-buttons">
				
				<div class="wp-block-button">
					<?php /* Show a disabled button on the block editor */ ?>
					<?php if ( self::is_block_editor() ) { ?>
						<input type="button" class="wp-block-button__link wp-element-button disabled" value="Update Profile" disabled="disabled">
					<?php }else{ ?>
						<input type="submit" class="wp-block-button__link wp-element-button" value="Update Profile">
					<?php } ?>
				</div>
				
				<?php
				do_action( 'rs_utility_blocks/after_submit_button', $user_id, $post_id, $block );
				?>
				
			</div>
		
		</form>

		<?php
		
	}
	
	/**
	 * Display a field in the edit profile form
	 *
	 * @param string $input_type   The type of input: text, email, password, textarea, url
	 * @param string $field_name   The name of the field
	 * @param string $field_value  The value of the field
	 * @param string $required     Whether the field is required
	 * @param string $placeholder  The placeholder text
	 *
	 * @return void
	 */
	private static function display_field( $input_type, $field_name, $field_value, $required, $placeholder ) {
		$atts = array(
			'type' => $input_type,
			'id' => 'rs-'. $field_name,
			'name' => 'rs_ub[field]['. $field_name .']',
			'placeholder' => $placeholder,
		);
		
		// Only add required attribute if required (do not use an empty string as the value)
		if ( $required ) {
			$atts['required'] = 'required';
		}
		
		// Text areas are formatted slightly differently
		if ( $input_type == 'textarea' ) {
			unset($atts['type']);
			$atts['rows'] = 4;
			$atts['cols'] = 60;
		}
		
		// Block editor disables the form fields
		if ( self::is_block_editor() ) {
			unset($atts['name']);
			$atts['disabled'] = 'disabled';
			$atts['class'] = 'disabled';
			$field_value = '';
			if ( $input_type == 'password' ) $atts['type'] = 'text';
		}
		
		// Allow plugins to filter each field
		$input_type = apply_filters( 'rs_utility_blocks/edit_profile/input_type', $input_type, $field_name, $field_value, $input_type );
		$field_value = apply_filters( 'rs_utility_blocks/edit_profile/field_value', $field_value, $field_name, $field_value, $input_type );
		$atts = apply_filters( 'rs_utility_blocks/edit_profile/field_attributes', $atts, $field_name, $field_value, $input_type );
		
		// Combine attributes to a string
		$atts_str = '';
		foreach( $atts as $key => $value ) {
			$atts_str .= ' '. $key .'="'. esc_attr($value) .'"';
		}
		
		
		if ( $input_type == 'textarea' ) {
			?>
			<textarea <?php echo $atts_str; ?>><?php echo esc_textarea($field_value); ?></textarea>
			<?php
		}else{
			?>
			<input <?php echo $atts_str; ?> value="<?php echo esc_attr($field_value) ?>">
			<?php
		}
	}
	
	/**
	 * Save the edit profile form
	 *
	 * @return void
	 */
	public static function save_edit_profile() {
		if ( ! isset($_POST['rs_ub']) ) return;
		
		// Verify nonce
		if ( ! wp_verify_nonce($_POST['rs_ub']['nonce'], 'edit-profile') ) return;
		
		// Get user ID
		$user_id = get_current_user_id();
		
		// Get submitted data
		$data = stripslashes_deep($_POST['rs_ub']);
		$fields = $data['field'];
		
		// Validate fields
		$errors = array();
		
		// Name
		if ( isset($fields['first_name']) ) {
			$first_name = sanitize_text_field($fields['first_name']);
			$last_name = sanitize_text_field($fields['last_name']);
			if ( ! $first_name && ! $last_name ) {
				$errors['first_name'] = 'Please enter your name.';
			}else if ( ! $first_name ) {
				$errors['first_name'] = 'Please enter your first name.';
			}else if ( ! $last_name ) {
				$errors['last_name'] = 'Please enter your last name.';
			}
		}
		
		// Email
		if ( isset($fields['user_email']) ) {
			$email = sanitize_email($fields['user_email']);
			if ( ! is_email($email) ) {
				$errors['user_email'] = 'Please enter a valid email address.';
			}
		}
		
		// Password
		// Only required if changing password
		if ( isset($fields['user_pass']) ) {
			$password = $fields['user_pass'];
			
			// No confirm password, just one input
			if ( ! $password ) {
				// No password change
				$password = '';
			}else if ( mb_strlen($password) < 6 ) {
				$errors['password'] = 'Password is too short (minimum 6 characters).';
			}
			
			// (Optional) Require confirmation password
			if ( isset($fields['confirm_password']) ) {
				$confirm_password = $fields['confirm_password'];
				if ( $password !== $confirm_password ) {
					$errors['confirm_password'] = 'Passwords do not match.';
				}
			}
		}
		
		// Email
		if ( isset($fields['user_email']) ) {
			$email = sanitize_email($fields['user_email']);
			
			if ( ! is_email($email) ) {
				$errors['user_email'] = 'Please enter a valid email address.';
			}
			
			// (Optional) Require confirmation email
			if ( isset($fields['confirm_email'] ) ) {
				$confirm_email = sanitize_email($fields['confirm_email']);
				if ( $email !== $confirm_email ) {
					$errors['confirm_email'] = 'Email addresses do not match.';
				}
			}
		}
		
		// Bio
		if ( isset($fields['description']) ) {
			// Optional, can be left blank
			$bio = sanitize_textarea_field($fields['description']);
		}
		
		// Website
		if ( isset($fields['user_url']) ) {
			// Optional, can be left blank
			$website = esc_url($fields['user_url']);
		}
		
		// Allow plugins to add custom validation
		$update_data = array();
		
		// Store each field to update
		if ( isset($first_name) ) {
			$update_data['first_name'] = $first_name;
		}
		
		if ( isset($last_name) ) {
			$update_data['last_name'] = $last_name;
		}
		
		if ( isset($email) ) {
			$update_data['user_email'] = $email;
		}
		
		if ( isset($password) ) {
			// Optional password change, only update if non-empty
			if ( ! empty($password) ) {
				$update_data['user_pass'] = $password;
			}
		}
		
		if ( isset($bio) ) {
			$update_data['description'] = $bio;
		}
		
		if ( isset($website) ) {
			$update_data['user_url'] = $website;
		}
		
		// If no fields to update, abort with a message, unless a custom filter is used during save
		if ( count($update_data) < 1 && ! has_filter('rs_utility_blocks/save_profile') ) {
			$errors['no_changes'] = 'No changes were made.';
			return;
		}
		
		// Allow plugins to add validation or save other fields, returning any errors as a string list
		$errors = apply_filters( 'rs_utility_blocks/save_profile', $errors, $fields, $user_id );
		
		// Check for errors, if found, store them to display above the form later and abort
		if ( ! empty($errors) ) {
			self::$errors = $errors;
			return;
		}
		
		// If fields to update on the user account, apply them
		if ( count($update_data) > 0 ) {
			// Add the user ID to the args before updating
			$update_data['ID'] = $user_id;
			
			// Update user data
			$result = wp_update_user( $update_data );
			
			// Check for errors
			if ( is_wp_error($result) ) {
				self::$errors[] = 'wp_update_user() -> ' . $result->get_error_message();
				return;
			}
		}
		
		// Redirect back to the form
		$redirect = add_query_arg( 'rs_ub_success', '1', wp_get_referer() );
		$redirect = apply_filters( 'rs_utility_blocks/edit_profile/redirect', $redirect, $user_id );
		wp_safe_redirect($redirect);
		exit;
		
	}
	
}

RS_Utility_Blocks_Profile::init();