<?php

add_action( 'acf/include_fields', function() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}
	
	acf_add_local_field_group( array(
		'key' => 'group_65f2e54f471c2',
		'title' => 'Login Form',
		'fields' => array(
			array(
				'key' => 'field_65f2e54f00886',
				'label' => 'Settings',
				'name' => 'settings',
				'aria-label' => '',
				'type' => 'checkbox',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'choices' => array(
					'show_forgot_password' => 'Show forgot password',
					'show_logout_link' => 'Show logout link (if logged in)',
					'show_register_link' => 'Show create account link',
					'disable_custom_style' => 'Disable custom styles',
				),
				'default_value' => array(
				),
				'return_format' => 'value',
				'allow_custom' => 0,
				'layout' => 'vertical',
				'toggle' => 0,
				'save_custom' => 0,
				'custom_choice_button_text' => 'Add new choice',
			),
			array(
				'key' => 'field_65f2e57f00887',
				'label' => 'Create Account URL',
				'name' => 'create_account_url',
				'aria-label' => '',
				'type' => 'text',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_65f2e54f00886',
							'operator' => '==',
							'value' => 'show_register_link',
						),
					),
				),
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'maxlength' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
			),
			array(
				'key' => 'field_65f2ef6272a08',
				'label' => 'Login Redirect',
				'name' => 'login_redirect',
				'aria-label' => '',
				'type' => 'text',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'maxlength' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'block',
					'operator' => '==',
					'value' => 'rs-utility-blocks/login-form',
				),
			),
		),
		'menu_order' => 0,
		'position' => 'normal',
		'style' => 'default',
		'label_placement' => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen' => '',
		'active' => true,
		'description' => 'ACF Block',
		'show_in_rest' => 0,
	) );
	
	acf_add_local_field_group( array(
		'key' => 'group_65f2327831337',
		'title' => 'Post Field',
		'fields' => array(
			array(
				'key' => 'field_65f23278347e0',
				'label' => 'Post Field',
				'name' => 'post_display_field',
				'aria-label' => '',
				'type' => 'select',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'choices' => array(
					'ID' => 'ID',
					'post_author' => 'Author',
					'post_date' => 'Publish Date',
					'post_date_gmt' => 'Publish Date (GMT)',
					'post_content' => 'Content',
					'post_title' => 'Title',
					'post_excerpt' => 'Excerpt',
					'post_status' => 'Post Status',
					'comment_status' => 'Comment Status',
					'post_name' => 'Name',
					'post_modified' => 'Modified',
					'post_modified_gmt' => 'Modified (GMT)',
					'guid' => 'GUID',
					'post_type' => 'Post Type',
					'comment_count' => 'Comment Count',
					'custom_field' => 'Custom Field',
				),
				'default_value' => false,
				'return_format' => 'value',
				'multiple' => 0,
				'allow_null' => 1,
				'ui' => 0,
				'ajax' => 0,
				'placeholder' => '',
			),
			array(
				'key' => 'field_65f23278383c1',
				'label' => 'Custom Field Key',
				'name' => 'custom_field_key',
				'aria-label' => '',
				'type' => 'text',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_65f23278347e0',
							'operator' => '==',
							'value' => 'custom_field',
						),
					),
				),
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'maxlength' => '',
				'placeholder' => 'first_name',
				'prepend' => '',
				'append' => '',
			),
			array(
				'key' => 'field_65f232783bd00',
				'label' => 'Create Link',
				'name' => 'create_link',
				'aria-label' => '',
				'type' => 'true_false',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => 'acf-hide-label',
					'id' => '',
				),
				'message' => 'Link to this field',
				'default_value' => 0,
				'ui' => 0,
				'ui_on_text' => '',
				'ui_off_text' => '',
			),
			array(
				'key' => 'field_65f2327843203',
				'label' => 'Link Type',
				'name' => 'link_type',
				'aria-label' => '',
				'type' => 'select',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_65f232783bd00',
							'operator' => '==',
							'value' => '1',
						),
					),
				),
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'choices' => array(
					'post' => 'Permalink',
					'archive' => 'Post Type Archive',
					'parent' => 'Parent',
					'edit' => 'Edit Post URL',
					'custom' => 'Custom URL',
				),
				'default_value' => false,
				'return_format' => 'value',
				'multiple' => 0,
				'allow_null' => 1,
				'ui' => 0,
				'ajax' => 0,
				'placeholder' => '',
			),
			array(
				'key' => 'field_65f2327846ea4',
				'label' => 'Custom URL',
				'name' => 'custom_url',
				'aria-label' => '',
				'type' => 'text',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_65f232783bd00',
							'operator' => '==',
							'value' => '1',
						),
						array(
							'field' => 'field_65f2327843203',
							'operator' => '==',
							'value' => 'custom',
						),
					),
				),
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'maxlength' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
			),
			array(
				'key' => 'field_65f232783f7ae',
				'label' => 'New Tab',
				'name' => 'new_tab',
				'aria-label' => '',
				'type' => 'true_false',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_65f232783bd00',
							'operator' => '==',
							'value' => '1',
						),
					),
				),
				'wrapper' => array(
					'width' => '',
					'class' => 'acf-hide-label',
					'id' => '',
				),
				'message' => 'Open link in new tab',
				'default_value' => 0,
				'ui' => 0,
				'ui_on_text' => '',
				'ui_off_text' => '',
			),
			array(
				'key' => 'field_65f232784a86e',
				'label' => 'Apply Formatting',
				'name' => 'apply_formatting',
				'aria-label' => '',
				'type' => 'true_false',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => 'acf-hide-label',
					'id' => '',
				),
				'message' => 'Apply paragraph formatting',
				'default_value' => 0,
				'ui' => 0,
				'ui_on_text' => '',
				'ui_off_text' => '',
			),
			array(
				'key' => 'field_65f232784e3d4',
				'label' => 'Show filter (for developers)',
				'name' => 'show_filter_instructions_for_developers',
				'aria-label' => '',
				'type' => 'true_false',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => 'acf-hide-label',
					'id' => '',
				),
				'message' => 'Show filter (for developers)',
				'default_value' => 0,
				'ui' => 0,
				'ui_on_text' => '',
				'ui_off_text' => '',
			),
			array(
				'key' => 'field_65f2327852088',
				'label' => 'Filter Usage',
				'name' => '',
				'aria-label' => '',
				'type' => 'message',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_65f232784e3d4',
							'operator' => '==',
							'value' => '1',
						),
					),
				),
				'wrapper' => array(
					'width' => '',
					'class' => 'acf-hide-label',
					'id' => '',
				),
				'message' => '<p>Developers can change the value of this block using a filter, for example:</p>

<pre class="code" style="overflow: auto; padding: 5px; background: #00000010;">add_filter( \'rs/post_field/custom_field\', function( $value, $post, $display_field, $custom_field_key ) {
		// Example values:
		// $value = null
		// $post = WP_Post object
		// $display_field = "custom_field"
		// $custom_field_key = "birthday"

		// [Example] Apply date formatting to a date field
		if ( $display_field == \'custom_field\' && $field_key == \'expiration_date\' ) {
				$value = get_post_meta( $post->ID, \'expiration_date\', true );
				if ( $value ) $value = date( \'F j, Y\', strtotime($value) );
		}

		return $value;
}, 10, 4);</pre>',
				'new_lines' => 'wpautop',
				'esc_html' => 0,
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'block',
					'operator' => '==',
					'value' => 'rs-utility-blocks/post-field',
				),
			),
		),
		'menu_order' => 0,
		'position' => 'normal',
		'style' => 'default',
		'label_placement' => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen' => '',
		'active' => true,
		'description' => 'ACF Block',
		'show_in_rest' => 0,
	) );
	
	acf_add_local_field_group( array(
		'key' => 'group_65ee276b37a9f',
		'title' => 'User Field',
		'fields' => array(
			array(
				'key' => 'field_65ee276b1579b',
				'label' => 'User Profile Field',
				'name' => 'user_profile_display_field',
				'aria-label' => '',
				'type' => 'select',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'choices' => array(
					'ID' => 'ID',
					'user_login' => 'Username',
					'user_nicename' => 'Nicename',
					'user_email' => 'Email',
					'user_url' => 'URL',
					'display_name' => 'Display Name',
					'first_name' => 'First Name',
					'last_name' => 'Last Name',
					'description' => 'Description',
					'nickname' => 'Nickname',
					'role' => 'Role',
					'avatar' => 'Avatar',
					'logout' => 'Sign Out',
					'custom_field' => 'Custom Field',
				),
				'default_value' => false,
				'return_format' => 'value',
				'multiple' => 0,
				'allow_null' => 1,
				'ui' => 0,
				'ajax' => 0,
				'placeholder' => '',
			),
			array(
				'key' => 'field_65f2168a6b8da',
				'label' => 'Custom Field Key',
				'name' => 'custom_field_key',
				'aria-label' => '',
				'type' => 'text',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_65ee276b1579b',
							'operator' => '==',
							'value' => 'custom_field',
						),
					),
				),
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'maxlength' => '',
				'placeholder' => 'first_name',
				'prepend' => '',
				'append' => '',
			),
			array(
				'key' => 'field_65ee2ca65d13d',
				'label' => 'Create Link',
				'name' => 'create_link',
				'aria-label' => '',
				'type' => 'true_false',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => 'acf-hide-label',
					'id' => '',
				),
				'message' => 'Link to this field',
				'default_value' => 0,
				'ui' => 0,
				'ui_on_text' => '',
				'ui_off_text' => '',
			),
			array(
				'key' => 'field_65ee2cd392752',
				'label' => 'Link Type',
				'name' => 'link_type',
				'aria-label' => '',
				'type' => 'select',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_65ee2ca65d13d',
							'operator' => '==',
							'value' => '1',
						),
					),
				),
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'choices' => array(
					'profile' => 'Edit Profile',
					'author_url' => 'Author URL',
					'user_url' => 'User Website',
					'logout' => 'Sign Out',
					'custom' => 'Custom URL',
				),
				'default_value' => false,
				'return_format' => 'value',
				'multiple' => 0,
				'allow_null' => 1,
				'ui' => 0,
				'ajax' => 0,
				'placeholder' => '',
			),
			array(
				'key' => 'field_65ee2e1892753',
				'label' => 'Custom URL',
				'name' => 'custom_url',
				'aria-label' => '',
				'type' => 'text',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_65ee2ca65d13d',
							'operator' => '==',
							'value' => '1',
						),
						array(
							'field' => 'field_65ee2cd392752',
							'operator' => '==',
							'value' => 'custom',
						),
					),
				),
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'maxlength' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
			),
			array(
				'key' => 'field_65ee3410e76cf',
				'label' => 'New Tab',
				'name' => 'new_tab',
				'aria-label' => '',
				'type' => 'true_false',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_65ee2ca65d13d',
							'operator' => '==',
							'value' => '1',
						),
					),
				),
				'wrapper' => array(
					'width' => '',
					'class' => 'acf-hide-label',
					'id' => '',
				),
				'message' => 'Open link in new tab',
				'default_value' => 0,
				'ui' => 0,
				'ui_on_text' => '',
				'ui_off_text' => '',
			),
			array(
				'key' => 'field_65f2213285afc',
				'label' => 'Apply Formatting',
				'name' => 'apply_formatting',
				'aria-label' => '',
				'type' => 'true_false',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => 'acf-hide-label',
					'id' => '',
				),
				'message' => 'Apply paragraph formatting',
				'default_value' => 0,
				'ui' => 0,
				'ui_on_text' => '',
				'ui_off_text' => '',
			),
			array(
				'key' => 'field_65f21b106b8dd',
				'label' => 'Show filter (for developers)',
				'name' => 'show_filter_instructions_for_developers',
				'aria-label' => '',
				'type' => 'true_false',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => 'acf-hide-label',
					'id' => '',
				),
				'message' => 'Show filter (for developers)',
				'default_value' => 0,
				'ui' => 0,
				'ui_on_text' => '',
				'ui_off_text' => '',
			),
			array(
				'key' => 'field_65f218f96b8dc',
				'label' => 'Filter Usage',
				'name' => '',
				'aria-label' => '',
				'type' => 'message',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_65f21b106b8dd',
							'operator' => '==',
							'value' => '1',
						),
					),
				),
				'wrapper' => array(
					'width' => '',
					'class' => 'acf-hide-label',
					'id' => '',
				),
				'message' => '<p>Developers can change the value of this block using a filter, for example:</p>

<pre class="code" style="overflow: auto; padding: 5px; background: #00000010;">add_filter( \'rs/user_field/custom_field\', function( $value, $user_id, $display_field, $custom_field_key ) {
		// Example values:
		// $value = null
		// $user_id = int(1)
		// $display_field = "custom_field"
		// $custom_field_key = "birthday"

		// [Example] Apply date formatting to a date field
		if ( $display_field == \'custom_field\' && $field_key == \'birthday\' ) {
				$value = get_user_meta( $user_id, \'birthday\', true );
				if ( $value ) $value = date( \'F j, Y\', strtotime($value) );
		}

		return $value;
}, 10, 4);</pre>',
				'new_lines' => 'wpautop',
				'esc_html' => 0,
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'block',
					'operator' => '==',
					'value' => 'rs-utility-blocks/user-field',
				),
			),
		),
		'menu_order' => 0,
		'position' => 'normal',
		'style' => 'default',
		'label_placement' => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen' => '',
		'active' => true,
		'description' => 'ACF Block',
		'show_in_rest' => 0,
	) );
} );