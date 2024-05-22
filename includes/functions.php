<?php

class RS_Utility_Blocks_Functions {
	
	public static function get_breadcrumbs( $post_id, $settings = array() ) {
		$settings = shortcode_atts(array(
			'home' => true,
			'home_text' => 'Home',
			'archive' => true,
		), $settings);
		
		$breadcrumbs = array();
		
		// Get the post type
		$post_type = get_post_type( $post_id );
		
		// Get the post's ancestors
		$ancestors = get_post_ancestors( $post_id );
		
		// Add the post itself to the breadcrumbs
		$breadcrumbs[] = array(
			'post_id' => $post_id,
			'title' => get_the_title( $post_id ),
			'url' => get_permalink( $post_id ),
		);
		
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
		$url1 = parse_url( $url1 );
		$url2 = parse_url( $url2 );
		
		// Compare the host
		if ( $url1['host'] && $url2['host'] && $url1['host'] !== $url2['host'] ) { return false; }
		
		// Compare the path
		if ( $url1['path'] !== $url2['path'] ) return false;
		
		// Compare the query string
		if ( $compare_query && $url1['query'] !== $url2['query'] ) return false;
		
		// Compare the fragment
		if ( $compare_fragment && $url1['fragment'] !== $url2['fragment'] ) return false;
		
		return true;
	}
	
}