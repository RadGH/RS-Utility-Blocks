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
		
		// Enqueue assets on the front-end.
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_public_assets' ) );
		
		// Enqueue scripts for the block editor
		add_action( 'enqueue_block_assets', array( __CLASS__, 'enqueue_block_assets' ) );
		
		// Add body classes for the theme.
		add_filter( 'body_class', array( __CLASS__, 'add_body_classes' ) );
		
		// Register custom block types
		add_action( 'init', array( __CLASS__, 'register_blocks' ) );
		
		// Add choices to the field: User Profile -> Display Data
		add_filter( 'acf/load_field/key=field_65ee276b1579b', array( __CLASS__, 'add_user_profile_field_choices' ) );
		
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
		
		register_block_type( RS_Utility_Blocks_PATH . '/blocks/user-field/block.json', array(
			'render_callback' => array( __CLASS__, 'render_block' ),
		));
		
	}
	
	/**
	 * Add choices to the field: User Profile -> Display Data
	 *
	 * @param array $field
	 *
	 * @return array
	 */
	public static function add_user_profile_field_choices( $field ) {
		// Do not apply to the field group editor
		if ( acf_is_screen('edit-field-group') ) return $field;
		
		$field['choices'] = array();
		
		foreach( self::get_user_fields() as $key => $f ) {
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
	 * @see self::get_user_fields()
	 *
	 * @param int    $user_id    The user ID to get the field from
	 * @param string $field_key  The key of the field to get, see get_user_fields()
	 *
	 * @return string|false
	 */
	public static function get_user_field( $user_id, $field_key ) {
		if ( $user_id < 1 ) return false;
		
		$fields = self::get_user_fields();
		$field = isset($fields[ $field_key ]) ? $fields[ $field_key ] : false;
		if ( ! $field ) return false;
		
		// Settings to get the value are stored in the "output" field such as:
		// - array( 'user', 'ID' )
		// - array( 'user', 'user_login' )
		// Which refers to:
		// $user = wp_get_current_user();
		// $user->ID
		// $user->user_login
		$output = $field['output'];
		
		switch ( $output[0] ) {
			case 'user':
				$user = self::get_user_by_id( $user_id );
				$profile_key = $output[1];
				if ( $user ) {
					$value = $user->get($profile_key);
				}
				break;
				
			case 'user_meta':
				$meta_key = $output[1];
				$value = get_user_meta( $user_id, $meta_key, true );
				break;
				
			case 'logout':
				$value = 'Sign Out';
				break;
		}
		
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
	 * Get an array of user profile fields that can be displayed in the User Profile block type.
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
	 *
	 * @return array[]
	 */
	public static function get_user_fields() {
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
		);
	}
	
}