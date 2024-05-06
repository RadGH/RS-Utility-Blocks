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
		
		register_block_type( RS_Utility_Blocks_PATH . '/blocks/edit-profile/block.json');
		
		register_block_type( RS_Utility_Blocks_PATH . '/blocks/user-field/block.json');
		
		register_block_type( RS_Utility_Blocks_PATH . '/blocks/post-field/block.json');
		
		register_block_type( RS_Utility_Blocks_PATH . '/blocks/login-form/block.json');
		
		register_block_type( RS_Utility_Blocks_PATH . '/blocks/link-block/block.json');
		
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
	 * @param WP_Post     $post              The post object to get the field from
	 * @param string      $display_field     The profile field to display, for options
	 *                                       @see self::get_post_display_fields()
	 * @param string|null $custom_field_key  If using a custom field, specify the post meta key to retrieve
	 *
	 * @return string|false
	 */
	public static function get_post_field( $post, $display_field, $custom_field_key = null ) {
		if ( ! $post ) return false;
	
		$post_id = $post->ID;
		
		$fields = self::get_post_display_fields();
		$field = isset($fields[ $display_field ]) ? $fields[ $display_field ] : false;
		if ( ! $field ) return false;
		
		// Settings to get the value are stored in the "output" field such as:
		// - array( 'post', 'ID' )
		// - array( 'post', 'post_login' )
		// Which refers to:
		// $post = wp_get_current_post();
		// $post->ID
		// $post->post_login
		$output = $field['output'];
		
		// Allow a filter to use a custom value
		if ( has_filter('rs/post_field') ) {
			$value = apply_filters( 'rs/post_field', null, $post_id, $display_field, $custom_field_key );
			if ( $value !== null ) return $value;
		}
		
		// Allow a filter to use a custom value, including field key in the filter
		if ( has_filter('rs/post_field/' . $display_field) ) {
			$value = apply_filters( 'rs/post_field/' . $display_field, null, $post_id, $display_field, $custom_field_key );
			if ( $value !== null ) return $value;
		}
		
		switch ( $output[0] ) {
			case 'post':
				$post_field = $output[1];
				$value = $post->{$post_field};
				
				// If using post author, get the post author's display name
				if ( $post_field === 'post_author' ) {
					$user = self::get_user_by_id( $value );
					$value = $user ? $user->get('display_name') : 'User #' . $value;
				}
				
				break;
				
			case 'post_meta':
				$value = $custom_field_key ? get_post_meta( $post_id, $custom_field_key, true ) : false;
				break;
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
				'output' => array( 'post', 'ID' ),
			),
			'post_author' => array(
				'title' => 'Author',
				'output' => array( 'post', 'post_author' ),
			),
			'post_date' => array(
				'title' => 'Publish Date',
				'output' => array( 'post', 'post_date' ),
			),
			'post_date_gmt' => array(
				'title' => 'Publish Date (GMT)',
				'output' => array( 'post', 'post_date_gmt' ),
			),
			'post_content' => array(
				'title' => 'Content',
				'output' => array( 'post', 'post_content' ),
			),
			'post_title' => array(
				'title' => 'Title',
				'output' => array( 'post', 'post_title' ),
			),
			'post_excerpt' => array(
				'title' => 'Excerpt',
				'output' => array( 'post', 'post_excerpt' ),
			),
			'post_status' => array(
				'title' => 'Post Status',
				'output' => array( 'post', 'post_status' ),
			),
			'comment_status' => array(
				'title' => 'Comment Status',
				'output' => array( 'post', 'comment_status' ),
			),
			/*
			'ping_status' => array(
				'title' => 'Ping Status',
				'output' => array( 'post', 'ping_status' ),
			),
			'post_password' => array(
				'title' => 'Password',
				'output' => array( 'post', 'post_password' ),
			),
			*/
			'post_name' => array(
				'title' => 'Name',
				'output' => array( 'post', 'post_name' ),
			),
			/*
			'to_ping' => array(
				'title' => 'To Ping',
				'output' => array( 'post', 'to_ping' ),
			),
			'pinged' => array(
				'title' => 'Pinged',
				'output' => array( 'post', 'pinged' ),
			),
			*/
			'post_modified' => array(
				'title' => 'Modified',
				'output' => array( 'post', 'post_modified' ),
			),
			'post_modified_gmt' => array(
				'title' => 'Modified (GMT)',
				'output' => array( 'post', 'post_modified_gmt' ),
			),
			/*
			'post_content_filtered' => array(
				'title' => 'Content Filtered',
				'output' => array( 'post', 'post_content_filtered' ),
			),
			'post_parent' => array(
				'title' => 'Parent',
				'output' => array( 'post', 'post_parent' ),
			),
			*/
			'guid' => array(
				'title' => 'GUID',
				'output' => array( 'post', 'guid' ),
			),
			/*
			'menu_order' => array(
				'title' => 'Menu Order',
				'output' => array( 'post', 'menu_order' ),
			),
			*/
			'post_type' => array(
				'title' => 'Post Type',
				'output' => array( 'post', 'post_type' ),
			),
			/*
			'post_mime_type' => array(
				'title' => 'MIME Type',
				'output' => array( 'post', 'post_mime_type' ),
			),
			*/
			'comment_count' => array(
				'title' => 'Comment Count',
				'output' => array( 'post', 'comment_count' ),
			),
			/*
			'filter' => array(
				'title' => 'Filter',
				'output' => array( 'post', 'filter' ),
			),
			*/
			'custom_field' => array(
				'title' => 'Custom Field',
				'output' => array( 'post_meta' ),
			),
		);
	}
	
}

RS_Utility_Blocks_Setup::init();