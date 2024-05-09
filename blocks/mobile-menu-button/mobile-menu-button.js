if ( typeof window.rs_mobile_menu === 'undefined' ) window.rs_mobile_menu = new function() {
	let m = this;

	m.is_block_editor = window.location.href.indexOf('/wp-admin/') !== -1;

	m.init = function() {

		// Register event binding for mobile buttons
		document.addEventListener( 'click', function(e) {

			// Check if we clicked a menu button or a child of a menu button
			let mobile_button = e.target.closest('.rs-mobile-menu-button');

			if ( mobile_button ) {
				// Toggle the menu button
				m.toggle_menu_button( mobile_button );

				// Ignore default behavior
				e.preventDefault();
				e.stopPropagation();
			}

		});

		// EXAMPLE: How to use the custom hook to apply effects when the menu is opened or closed.
		/*
		// Register event binding for "rs_mobile_menu_toggled" to display debug information about the menu opening
		document.addEventListener( 'rs_mobile_menu_toggled', function( e ) {
			console.log( 'Mobile menu toggled', e );
		});
		*/

	};

	// Toggle a menu open or closed
	m.toggle_menu_button = function( menu_button, make_open = null ) {
		// Toggle the menu if make_open was not defined
		if ( make_open === null ) {
			make_open = ! menu_button.classList.contains('open');
		}

		// Toggle classes
		menu_button.classList.toggle('open', make_open);
		menu_button.classList.toggle('closed', !make_open);

		// Get target element from data-target attribute
		let menu_selector = menu_button.getAttribute('data-target');
		let menu_container = document.querySelector( menu_selector );
		
		if ( menu_container ) {

			// If found, toggle classes on the menu_container element as well
			menu_container.classList.toggle('open', make_open);
			menu_container.classList.toggle('closed', !make_open);

		}


		// Trigger a custom event. Custom event data is stored in the "detail" property.
		document.dispatchEvent( new CustomEvent('rs_mobile_menu_toggled', {
			detail: {
				open: make_open,
				slug: menu_selector,
				button: menu_button,
				container: menu_container,
			}
		}) );

	};

	m.init();

	return m;
};