<?php

class RS_Utility_Blocks_Setup {
	
	/**
	 * Initialized when the plugin is loaded
	 *
	 * @return void
	 */
	public static function init() {
		
		// Register (but do not enqueue) CSS and JS files
		add_action( 'init', array( __CLASS__, 'register_all_assets' ) );
		
		// Enqueue assets on the dashboard.
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_assets' ) );
		
		// Enqueue assets on the front-end.
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_public_assets' ) );
		
		// Enqueue scripts for the block editor
		add_action( 'enqueue_block_assets', array( __CLASS__, 'enqueue_block_assets' ) );
		
		// Add body classes for the theme.
		add_filter( 'body_class', array( __CLASS__, 'add_body_classes' ) );
		
		// Register custom block types
		add_action( 'init', array( __CLASS__, 'register_blocks' ) );
		
		// Add choices to the field: User Field block -> User Profile Field
		add_filter( 'acf/load_field/key=field_65ee276b1579b', array( __CLASS__, 'add_user_profile_field_choices' ) );
		
		// Add choices to the field: Post Field block -> Post Field
		add_filter( 'acf/load_field/key=field_65f23278347e0', array( __CLASS__, 'add_post_field_choices' ) );
		
		// Add choices to the field: Post Field block -> Image Size
		add_filter( 'acf/load_field/key=field_663e5b96ab779', array( __CLASS__, 'add_image_size_choices' ) );
		
		// Add choices to the field: Post Field block -> Date Format
		add_filter( 'acf/load_field/key=field_663e5c09260ad', array( __CLASS__, 'add_date_format_choices' ) );
		
	}
	
	/**
	 * Enqueue public scripts (theme/front-end)
	 *
	 * @return void
	 */
	public static function register_all_assets() {
		
		// Block editor CSS
		wp_register_style( 'rs-utility-blocks', RS_Utility_Blocks_URL . 'assets/rs-utility-blocks.css', array(), RS_Utility_Blocks_VERSION );
		
		// Block editor JS (admin-only)
		// - compiled using "npm run build", see readme.md for details.
		$asset = require( RS_Utility_Blocks_PATH . '/assets/scripts/dist/rs-utility-block-editor.asset.php' );
		wp_register_script( 'rs-utility-blocks-block-editor', RS_Utility_Blocks_URL . 'assets/scripts/dist/rs-utility-block-editor.js', $asset['dependencies'], $asset['version'] );
		
	}
	
	/**
	 * Enqueue assets on the wordpress dashboard (backend).
	 *
	 * @return void
	 */
	public static function enqueue_admin_assets() {
		
		wp_enqueue_style( 'rs-utility-blocks' );
		
	}
	
	/**
	 * Enqueue assets on the front-end.
	 *
	 * @return void
	 */
	public static function enqueue_public_assets() {
		
		wp_enqueue_style( 'rs-utility-blocks' );
		
	}
	
	/**
	 * Enqueue block editor assets, wherever blocks are used
	 *
	 * @return void
	 */
	public static function enqueue_block_assets() {
		
		wp_enqueue_style( 'rs-utility-blocks' );
		
		wp_enqueue_script( 'rs-utility-blocks-block-editor' );
		
	}
	
	/**
	 * Add body classes for the theme.
	 *
	 * @param array $classes
	 *
	 * @return array
	 */
	public static function add_body_classes( $classes ) {
		
		// Add classes that may toggle block visibility, see scripts/src/settings/visibility.js
		if ( ! is_admin() ) $classes[] = 'front-end';
		
		$classes[] = is_user_logged_in() ? 'user-is-logged-in' : 'user-not-logged-in';
		
		$classes[] = current_user_can('manage_options') ? 'user-is-admin' : 'user-not-admin';
		
		return $classes;
	}
	
	/**
	 * Register custom block types
	 */
	public static function register_blocks( $classes ) {
		
		register_block_type( RS_Utility_Blocks_PATH . '/blocks/breadcrumbs/block.json');
		
		register_block_type( RS_Utility_Blocks_PATH . '/blocks/edit-profile/block.json');
		
		register_block_type( RS_Utility_Blocks_PATH . '/blocks/link-block/block.json');
		
		register_block_type( RS_Utility_Blocks_PATH . '/blocks/login-form/block.json');
		
		register_block_type( RS_Utility_Blocks_PATH . '/blocks/mobile-menu-button/block.json');
		
		register_block_type( RS_Utility_Blocks_PATH . '/blocks/mobile-menu-container/block.json');
		
		register_block_type( RS_Utility_Blocks_PATH . '/blocks/post-field/block.json');
		
		register_block_type( RS_Utility_Blocks_PATH . '/blocks/user-field/block.json');
		
		
	}
	
	/**
	 * Add choices to the User Field block -> User Profile Field
	 *
	 * @param array $field
	 *
	 * @return array
	 */
	public static function add_user_profile_field_choices( $field ) {
		// Do not apply to the field group editor
		if ( acf_is_screen('edit-field-group') ) return $field;
		
		$field['choices'] = array();
		
		foreach( self::get_user_display_fields() as $key => $f ) {
			$field['choices'][ $key ] = $f['title'] ;
		}
		
		return $field;
	}
	
	/**
	 * Add choices to the Post Field block -> Post Field
	 *
	 * @param array $field
	 *
	 * @return array
	 */
	public static function add_post_field_choices( $field ) {
		// Do not apply to the field group editor
		if ( acf_is_screen('edit-field-group') ) return $field;
		
		$field['choices'] = array();
		
		foreach( self::get_post_display_fields() as $key => $f ) {
			$field['choices'][ $key ] = $f['title'] ;
		}
		
		return $field;
	}
	
	/**
	 * Add choices to the Post Field block -> Image Size
	 *
	 * @param array $field
	 *
	 * @return array
	 */
	public static function add_image_size_choices( $field ) {
		// Do not apply to the field group editor
		if ( acf_is_screen('edit-field-group') ) return $field;
		
		$field['choices'] = array();

		// Get all registered image sizes and use them as choices
		foreach( self::get_image_sizes() as $name => $s ) {
			$width = $s['width'];
			$height = $s['height'];
			$crop = $s['crop'];
			
			$label = ucwords( str_replace( '_', ' ', $name ) );
			$label .= ' ('. $width .'x'. $height;
			if ( $crop ) $label .= ', cropped';
			$label .= ')';
			
			$field['choices'][ $name ] = $label;
		}
		
		return $field;
	}
	
	/**
	 * Add choices to the Post Field block -> Date Format
	 *
	 * @param array $field
	 *
	 * @return array
	 */
	public static function add_date_format_choices( $field ) {
		// Do not apply to the field group editor
		if ( acf_is_screen('edit-field-group') ) return $field;
		
		$ts = current_time('timestamp');
		$default = get_option( 'date_format' );
		$default_time = get_option( 'time_format' );
		
		$suggestions = array(
			'F j',
			'F j, Y',
			'F j, Y g:i a',
			'Y-m-d',
			'Y-m-d H:i:s',
			'm/d/Y',
			'm/d/Y g:i a',
			'd/m/Y',
			'd/m/Y g:i a',
		);
		
		$field['choices'] = array();
		
		$field['choices'][$default] = date($default, $ts) . ' (Site Default)';
		$field['choices'][$default . ' ' . $default_time] = date($default . ' ' . $default_time, $ts) . ' (Site Default)';
		
		foreach( $suggestions as $format ) {
			if ( ! isset($field['choices'][$format]) ) {
				$field['choices'][$format] = date($format, $ts);
			}
		}
		
		$field['choices']['relative'] = 'Relative';
		$field['choices']['custom'] = 'Custom';
		
		return $field;
	}
	
	/**
	 * Get an array image sizes and crop info
	 *
	 * @return array[] $size {
	 *     @type int $width
	 *     @type int $height
	 *     @type bool $crop
	 * }
	 */
	public static function get_image_sizes() {
		
		// Use previous results if cache exists
		$sizes = get_transient( 'rs_cached_image_sizes' );
		if ( $sizes ) return $sizes;
		
		$sizes = array();
		
		$wp_additional_image_sizes = wp_get_additional_image_sizes();
		$get_intermediate_image_sizes = get_intermediate_image_sizes();
		
		// Create the full array with sizes and crop info
		foreach( $get_intermediate_image_sizes as $_size ) {
			if ( in_array( $_size, array( 'thumbnail', 'medium', 'medium_large', 'large' ) ) ) {
				$sizes[ $_size ]['width'] = get_option( $_size . '_size_w' );
				$sizes[ $_size ]['height'] = get_option( $_size . '_size_h' );
				$sizes[ $_size ]['crop'] = (bool) get_option( $_size . '_crop' );
			} elseif ( isset( $wp_additional_image_sizes[ $_size ] ) ) {
				$sizes[ $_size ] = array(
					'width' => $wp_additional_image_sizes[ $_size ]['width'],
					'height' => $wp_additional_image_sizes[ $_size ]['height'],
					'crop' =>  $wp_additional_image_sizes[ $_size ]['crop']
				);
			}
		}
		
		// Cache results for an hour
		set_transient( 'rs_cached_image_sizes', $sizes, HOUR_IN_SECONDS );
		
		return $sizes;
	}
	
	/**
	 * Get a WP_User object by ID
	 *
	 * @param $user_id
	 *
	 * @return false|WP_User
	 */
	public static function get_user_by_id( $user_id = null ) {
		if ( $user_id === null ) $user_id = get_current_user_id();
		return get_user_by( 'ID', $user_id );
	}
	
	/**
	 * Get the value of a field from the user's profile.
	 *
	 * @param int         $user_id           The user ID to get the field from
	 * @param string      $display_field     The profile field to display, for options
	 *                                       @see self::get_user_display_fields()
	 * @param string|null $custom_field_key  If using a custom field, specify the user meta key to retrieve
	 *
	 * @return string|false
	 */
	public static function get_user_field( $user_id, $display_field, $custom_field_key = null ) {
		if ( $user_id < 1 ) return false;
		
		$fields = self::get_user_display_fields();
		$field = isset($fields[ $display_field ]) ? $fields[ $display_field ] : false;
		if ( ! $field ) return false;
		
		// Settings to get the value are stored in the "output" field such as:
		// - array( 'user', 'ID' )
		// - array( 'user', 'user_login' )
		// Which refers to:
		// $user = wp_get_current_user();
		// $user->ID
		// $user->user_login
		$output = $field['output'];
		
		// Allow a filter to use a custom value
		if ( has_filter('rs/user_field') ) {
			$value = apply_filters( 'rs/user_field', null, $user_id, $display_field, $custom_field_key );
			if ( $value !== null ) return $value;
		}
		
		// Allow a filter to use a custom value, including field key in the filter
		if ( has_filter('rs/user_field/' . $display_field) ) {
			$value = apply_filters( 'rs/user_field/' . $display_field, null, $user_id, $display_field, $custom_field_key );
			if ( $value !== null ) return $value;
		}
		
		switch ( $output[0] ) {
			case 'user':
				$user = self::get_user_by_id( $user_id );
				$profile_key = $output[1];
				if ( $user ) {
					$value = $user->get($profile_key);
				}
				break;
				
			case 'user_meta':
				$value = $custom_field_key ? get_user_meta( $user_id, $custom_field_key, true ) : false;
				break;
				
			case 'logout':
				$value = 'Sign Out';
				break;
		}
		
		// Expand shortcodes in the result
		if ( $value ) $value = do_shortcode( $value );
		
		return $value;
	}
	
	/**
	 * Get a link based on the selected user
	 *
	 * @param int $user_id        The user ID
	 * @param string $key         The key of the link to get: profile, author_url, user_url, logout, custom
	 * @param string $custom_url  The custom URL if "custom" is selected
	 *
	 * @return array|false {
	 *     @type string $url The URL
	 *     @type bool $new_tab Whether to open the link in a new tab
	 * }
	 */
	public static function get_user_link( $user_id, $key, $custom_url = '' ) {
		if ( $user_id < 1 ) return false;
		
		switch( $key ) {
			case 'profile':
				return get_edit_user_link( $user_id );
				
			case 'author_url':
				return get_author_posts_url( $user_id );
				
			case 'user_url':
				$user = self::get_user_by_id( $user_id );
				return $user->get('user_url');
				
			case 'logout':
				return wp_logout_url();
				
			case 'custom':
				return $custom_url;
		}
		
		return false;
	}
	
	
	/**
	 * Get the value of a field from the post's profile.
	 *
	 * @param WP_Post  $post              The post object to get the field from
	 * @param string   $display_field     The profile field to display, for options
	 *                                    @see self::get_post_display_fields()
	 * @param string   $custom_field_key  If using a custom field, specify the post meta key to retrieve
	 * @param array    $block             The block settings
	 *
	 * @return string|false
	 */
	public static function get_post_field( $post, $display_field, $custom_field_key, $block ) {
		$post_id = $post instanceof WP_Post ? $post->ID : false;
		
		$fields = self::get_post_display_fields();
		$field = isset($fields[ $display_field ]) ? $fields[ $display_field ] : false;
		if ( ! $field ) return false;
		
		// Settings to get the value are stored in the "output" field such as:
		// 'post'         -> $post->{ $display_field } -> $post->post_title
		// 'custom_field' -> get_post_meta( $post_id, $custom_field_key, true )
		// 'function'     -> specific function below
		$output = $field['output'];
		
		// Allow a filter to use a custom value
		if ( has_filter('rs/post_field') ) {
			$value = apply_filters( 'rs/post_field', null, $post_id, $display_field, $custom_field_key, $block );
			if ( $value !== null ) return $value;
		}
		
		// Allow a filter to use a custom value, including field key in the filter
		if ( has_filter('rs/post_field/' . $display_field) ) {
			$value = apply_filters( 'rs/post_field/' . $display_field, null, $post_id, $display_field, $custom_field_key, $block );
			if ( $value !== null ) return $value;
		}
		
		// Allow a filter to use a custom value, including field key in the filter
		if ( $output == 'custom_field' && has_filter('rs/post_field/custom_field/' . $custom_field_key) ) {
			$value = apply_filters( 'rs/post_field/' . $custom_field_key, null, $post_id, $display_field, $custom_field_key, $block );
			if ( $value !== null ) return $value;
		}
		
		if ( $output == 'post' ) {
			// Get the value of the post field
			$value = $post ? $post->{ $display_field } : false;
			
			switch( $display_field ) {
				
				// If using post author, get the post author's display name
				case 'post_author':
					$user = self::get_user_by_id( $value );
					$value = ($user && $user->ID > 0) ? $user->get('display_name') : false;
					break;
				
				// Format dates
				case 'post_date':
				case 'post_date_gmt':
				case 'post_modified':
				case 'post_modified_gmt':
					$format = get_field( 'date_format', $block['id'] );
					if ( $format == 'custom' ) $format = get_field( 'date_format_custom', $block['id'] );
					if ( ! $format ) $format = get_option( 'date_format' );
					
					if ( $format == 'relative' ) {
						$value = human_time_diff( strtotime( $value ) );
					}else{
						$format = apply_filters( 'rs/date_format', $format, 'post', $post_id, $display_field, $custom_field_key, $block );
						$value = date_i18n( $format, strtotime( $value ) );
					}
					
					break;
					
				// Comment Count default
				case 'comment_count':
					if ( ! $value ) $value = '0';
					break;
				
					
			}
			
		}else if ( $output == 'custom_field' ) {
			
			// Get the value of a custom field
			$value = $custom_field_key ? get_post_meta( $post_id, $custom_field_key, true ) : false;
			
		}else if ( $output == 'function' ) {
			
			// Get the value using a specific function
			switch( $display_field ) {
				
				case 'permalink':
					$value = get_permalink( $post_id );
					break;
					
				case 'featured_image':
					$image_size = get_field( 'image_size', $block['id'] );
					$value = get_the_post_thumbnail( $post_id, $image_size );
					break;
				
			}
			
		}
		
		// Expand shortcodes in the result
		if ( $value ) $value = do_shortcode( $value );
		
		return $value;
	}
	
	/**
	 * Get a link based on the selected post
	 *
	 * @param WP_Post $post        The post object
	 * @param string  $key         The key of the link to get: profile, author_url, post_url, logout, custom
	 * @param string  $custom_url  The custom URL if "custom" is selected
	 *
	 * @return array|false {
	 *     @type string $url The URL
	 *     @type bool $new_tab Whether to open the link in a new tab
	 * }
	 */
	public static function get_post_link( $post, $key, $custom_url = '' ) {
		if ( ! $post ) return false;
		
		switch( $key ) {
			case 'post':
				return get_permalink( $post->ID );
				
			case 'archive':
				return get_post_type_archive_link( $post->ID );
				
			case 'parent':
				$parent_id = $post->post_parent;
				return $parent_id ? get_permalink( $parent_id ) : false;
			
			case 'author':
				$post_author = $post->post_author;
				return get_author_posts_url( $post_author );
				
			case 'edit':
				return get_edit_post_link( $post->ID );
				
			case 'custom':
				return $custom_url;
		}
		
		return false;
	}
	
	/**
	 * Checks if a given URL is external (links to a different website and should be opened in a new tab)
	 *
	 * @param $url
	 *
	 * @return bool
	 */
	public static function is_link_external( $url ) {
		// Relative links
		if ( str_starts_with( $url, '/' ) ) return false;
		if ( str_starts_with( $url, '#' ) ) return false;
		if ( str_starts_with( $url, 'javascript:' ) ) return false;
		
		// Links to the same host name
		$link_host = parse_url( $url, PHP_URL_HOST );
		$site_host = parse_url( home_url(), PHP_URL_HOST );
		if ( $link_host == $site_host ) return true;
		
		// Link to other website
		return false;
	}
	
	/**
	 * Get an array of user profile fields that can be displayed in the User Field block.
	 *
	 * Choices include:
	 * - ID
	 * - user_login
	 * - user_nicename
	 * - user_email
	 * - user_url
	 * - display_name
	 * - first_name
	 * - last_name
	 * - description
	 * - nickname
	 * - role
	 * - avatar
	 * - custom_field
	 *
	 * @return array[]
	 */
	public static function get_user_display_fields() {
		return array(
			'ID' => array(
				'title' => 'ID',
				'output' => array( 'user', 'ID' ),
			),
			'user_login' => array(
				'title' => 'Username',
				'output' => array( 'user', 'user_login' ),
			),
			'user_nicename' => array(
				'title' => 'Nicename',
				'output' => array( 'user', 'user_nicename' ),
			),
			'user_email' => array(
				'title' => 'Email',
				'output' => array( 'user', 'user_email' ),
			),
			'user_url' => array(
				'title' => 'URL',
				'output' => array( 'user', 'user_url' ),
			),
			'display_name' => array(
				'title' => 'Display Name',
				'output' => array( 'user', 'display_name' ),
			),
			'first_name' => array(
				'title' => 'First Name',
				'output' => array( 'user', 'first_name' ),
			),
			'last_name' => array(
				'title' => 'Last Name',
				'output' => array( 'user', 'last_name' ),
			),
			'description' => array(
				'title' => 'Description',
				'output' => array( 'user', 'description' ),
			),
			'nickname' => array(
				'title' => 'Nickname',
				'output' => array( 'user', 'nickname' ),
			),
			'role' => array(
				'title' => 'Role',
				'output' => array( 'user', 'role' ),
			),
			'avatar' => array(
				'title' => 'Avatar',
				'output' => array( 'user', 'avatar' ),
			),
			'logout' => array(
				'title' => 'Sign Out',
				'output' => array( 'logout' ),
			),
			'custom_field' => array(
				'title' => 'Custom Field',
				'output' => array( 'user_meta' ),
			),
		);
	}
	
	/**
	 * Get an array of post fields that can be displayed in the Post Field block.
	 *
	 * Choices include:
	 * - ID
	 * - post_author
	 * - post_date
	 * - post_date_gmt
	 * - post_content
	 * - post_title
	 * - post_excerpt
	 * - post_status
	 * - comment_status
	 * - post_name
	 * - post_modified
	 * - post_modified_gmt
	 * - guid
	 * - post_type
	 * - comment_count
	 * - custom_field
	 *
	 * @return array[]
	 */
	public static function get_post_display_fields() {
		return array(
			'ID' => array(
				'title' => 'ID',
				'output' => 'post',
			),
			'post_author' => array(
				'title' => 'Author',
				'output' => 'post',
			),
			'post_date' => array(
				'title' => 'Publish Date',
				'output' => 'post',
			),
			'post_date_gmt' => array(
				'title' => 'Publish Date (GMT)',
				'output' => 'post',
			),
			'post_modified' => array(
				'title' => 'Modified Date',
				'output' => 'post',
			),
			'post_modified_gmt' => array(
				'title' => 'Modified Date (GMT)',
				'output' => 'post',
			),
			'post_content' => array(
				'title' => 'Content',
				'output' => 'post',
			),
			'post_title' => array(
				'title' => 'Title',
				'output' => 'post',
			),
			'post_excerpt' => array(
				'title' => 'Excerpt',
				'output' => 'post',
			),
			'post_status' => array(
				'title' => 'Post Status',
				'output' => 'post',
			),
			'comment_status' => array(
				'title' => 'Comment Status',
				'output' => 'post',
			),
			/*
			'ping_status' => array(
				'title' => 'Ping Status',
				'output' => 'post',
			),
			'post_password' => array(
				'title' => 'Password',
				'output' => 'post',
			),
			*/
			'post_name' => array(
				'title' => 'Post Name (Slug)',
				'output' => 'post',
			),
			'permalink' => array(
				'title' => 'Permalink',
				'output' => 'function',
			),
			/*
			'to_ping' => array(
				'title' => 'To Ping',
				'output' => 'post',
			),
			'pinged' => array(
				'title' => 'Pinged',
				'output' => 'post',
			),
			*/
			/*
			'post_content_filtered' => array(
				'title' => 'Content Filtered',
				'output' => 'post',
			),
			'post_parent' => array(
				'title' => 'Parent',
				'output' => 'post',
			),
			*/
			'guid' => array(
				'title' => 'GUID',
				'output' => 'post',
			),
			/*
			'menu_order' => array(
				'title' => 'Menu Order',
				'output' => 'post',
			),
			*/
			'post_type' => array(
				'title' => 'Post Type',
				'output' => 'post',
			),
			/*
			'post_mime_type' => array(
				'title' => 'MIME Type',
				'output' => 'post',
			),
			*/
			'comment_count' => array(
				'title' => 'Comment Count',
				'output' => 'post',
			),
			/*
			'filter' => array(
				'title' => 'Filter',
				'output' => 'post',
			),
			*/
			'featured_image' => array(
				'title' => 'Featured Image',
				'output' => 'function',
			),
			'custom_field' => array(
				'title' => 'Custom Field',
				'output' => 'custom_field',
			),
		);
	}
	
	/**
	 * Checks if a field is empty. A zero is not empty in this situation, but false, null, or empty strings or arrays are.
	 *
	 * @param $value
	 *
	 * @return bool
	 */
	public static function is_field_empty( $value ) {
		if ( $value === null ) return true;
		if ( $value === '' ) return true;
		if ( $value === false ) return true;
		if ( is_array($value) && empty($value) ) return true;
		return false;
	}
	
}

RS_Utility_Blocks_Setup::init();