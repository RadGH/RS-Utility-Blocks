/*
jQuery(document).on('ajaxSend', function(event, xhr, settings) {
	// Check if the request is for fetching an ACF block
	if (settings.data.indexOf('acf%2Fajax%2Ffetch-block') !== -1) { // acf/ajax/fetch-block
		// Add your custom data
		settings.data += '&rsub_post_type=' + 'something';
		console.log('settings data added');
	}else{ console.log('settings not added :('); }
});
*/

import domReady from "@wordpress/dom-ready";

domReady(() => {

	/**
	 * Get the post type of the current post
	 * If editing a singular page template, return the post type that it represents
	 */
	const get_template_post_type = function() {
		let core_editor = wp.data.select('core/editor');
		if ( ! core_editor ) return false;

		let post_type = core_editor.getEditedPostAttribute('type');

		if ( post_type === 'wp_template' ) {

			let templateSlug = core_editor.getEditedPostAttribute('slug');
			// templateSlug = 'single-organization'

			// Use a wildcard approach to match singular or archive templates
			if ( templateSlug.startsWith('single-')  ) {
				post_type = templateSlug.replace('single-', '');
			}else if ( templateSlug.startsWith('archive-')  ) {
				post_type = templateSlug.replace('archive-', '');
			}

		}

		return post_type;
	};

	/**
	 * Add the post type of the current post to the ACF block fetch request for RS Utility Blocks
	 */
	jQuery(document).on('ajaxSend', function(event, xhr, settings) {
		let data = settings.data || '';

		// Check if acf/ajax/fetch-block
		let is_acf_ajax = data && data.indexOf('acf%2Fajax%2Ffetch-block') !== -1;
		if ( ! is_acf_ajax ) return;

		// Check if rs-utility-blocks
		let is_rs_utility_blocks = data && data.indexOf('block=%7B%22name%22%3A%22rs-utility-blocks') !== -1;
		if ( ! is_rs_utility_blocks ) return;

		// Add post type
		let post_type = get_template_post_type();

		settings.data += '&rsub_template_post_type=' + post_type;
	});
});