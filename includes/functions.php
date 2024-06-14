<?php

class RS_Utility_Blocks_Functions {
	
	public static function get_breadcrumbs( $post_id, $settings = array() ) {
		$settings = shortcode_atts(array(
			'link_current_page' => false,
			'home' => true,
			'home_text' => 'Home',
			'archive' => true,
		), $settings);
		
		$breadcrumbs = array();
		
		// Get the post type
		$post_type = $post_id ? get_post_type( $post_id ) : false;
		
		// Get the post's ancestors
		$ancestors = $post_id ? get_post_ancestors( $post_id ) : false;
		
		// Add the post itself to the breadcrumbs
		if ( $post_id ) {
			$breadcrumbs[] = array(
				'post_id' => $post_id,
				'title' => get_the_title( $post_id ),
				'url' => $settings['link_current_page'] ? get_permalink( $post_id ) : false,
			);
		}
		
		// Add the post's ancestors to the breadcrumbs
		if ( $ancestors ) {
			$ancestors = array_reverse( $ancestors );
			foreach( $ancestors as $ancestor_id ) {
				$breadcrumbs[] = array(
					'post_id' => $ancestor_id,
					'title' => get_the_title( $ancestor_id ),
					'url' => get_permalink( $ancestor_id ),
				);
			}
		}
		
		// Add the post type archive page to the breadcrumbs, if enabled
		if ( $settings['archive'] ) {
			$archive_page = get_post_type_archive_link( $post_type );
			if ( $archive_page ) {
				$breadcrumbs[] = array(
					'post_id' => null,
					'title' => get_post_type_object( $post_type )->labels->name,
					'url' => $archive_page,
				);
			}
		}
		
		// Add the home page to the breadcrumbs, if enabled
		if ( $settings['home'] ) {
			$breadcrumbs[] = array(
				'post_id' => get_option( 'page_on_front' ) ?: null,
				'title' => $settings['home_text'] ?: 'Home',
				'url' => home_url(),
			);
		}
		
		// Reverse the breadcrumbs so they are in the correct order
		$breadcrumbs = array_reverse( $breadcrumbs );
		
		// Allow plugins to filter the breadcrumbs
		$breadcrumbs = apply_filters( 'rs_utility_blocks/breadcrumbs', $breadcrumbs, $post_id, $settings );
		
		return $breadcrumbs;
	}
	
	/**
	 * Compare two URLs to see if they point to the same page.
	 * If using a relative URL, the host will be ignored.
	 *
	 * @param string $url1
	 * @param string $url2
	 * @param bool $compare_query    Default false. Whether to compare the query string: ?foo=bar
	 * @param bool $compare_fragment Default false. Whether to compare the fragment:     #section
	 *
	 * @return bool
	 */
	public static function compare_urls( $url1, $url2, $compare_query = false, $compare_fragment = false ) {
		if ( ! $url1 || ! $url2 ) return false;
		
		$url1 = parse_url( $url1 );
		$url2 = parse_url( $url2 );
		
		// Compare the host
		if ( !empty($url1['host']) && !empty($url2['host']) && $url1['host'] !== $url2['host'] ) return false;
		
		// Compare the path
		if ( !empty($url1['path']) && !empty($url2['path']) && $url1['path'] !== $url2['path'] ) return false;
		
		// Compare the query string
		if ( $compare_query && !empty($url1['query']) && !empty($url2['query']) && $url1['query'] !== $url2['query'] ) return false;
		
		// Compare the fragment
		if ( $compare_fragment && !empty($url1['fragment']) && !empty($url2['fragment']) && $url1['fragment'] !== $url2['fragment'] ) return false;
		
		return true;
	}
	
	/**
	 * Get the latest post of the given post type for the block editor.
	 * This should only be used on the block editor.
	 *
	 * @param array $block The block data
	 *
	 * @return int|false
	 */
	public static function get_post_id_for_block_editor( $block ) {
		$post_type = get_post_type();
		
		if ( ! $post_type ) {
			if ( isset($_POST['rsub_template_post_type']) ) {
				$post_type = stripslashes( $_POST['rsub_template_post_type'] );
			}
		}
		
		// Get the latest post of the given post type
		if ( $post_type ) {
			$p = get_posts(array(
				'post_type' => $post_type,
				'posts_per_page' => 1,
				'orderby' => 'date',
				'order' => 'DESC',
			));
			
			if ( $p ) return $p[0]->ID;
		}
		
		// No post found
		return false;
	}
	
}