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

$appearance = get_field( 'appearance', $block['id'] );                   // icon, text, icontext

$icons = array(
	'open' => array(
		'text' => get_field( 'open_text', $block['id'] ),                    // appearance = text, icontext
		'icon_type' => get_field( 'open_icon_type', $block['id'] ),          // appearance = icon, icontext
		'custom_icon' => get_field( 'custom_open_icon', $block['id'] ),      // appearance = icon, icontext
		'symbol' => '☰',
	),
	'close' => array(
		'text' => get_field( 'close_text', $block['id'] ),                   // appearance = text, icontext
		'icon_type' => get_field( 'close_icon_type', $block['id'] ),         // appearance = icon, icontext
		'custom_icon' => get_field( 'custom_close_icon', $block['id'] ),     // appearance = icon, icontext
		'symbol' => '×',
	),
);

$icon_text_placement = get_field( 'icon_text_placement', $block['id'] ); // icon-first, text-first; appearance = icontext

$menu_slug = get_field( 'menu_slug', $block['id'] ); // "menu-slug" (should match a mobile menu container block)
if ( ! $menu_slug ) $menu_slug = 'default';

// Add additional classes
$classes = array();
if ( $appearance == 'icontext' && $icon_text_placement == 'icon-first' ) $classes[] = 'icon-first';

// Start output
$html_element = 'div';

$atts = array(
	'class' => implode(' ', $classes),
);

if ( !empty($block['anchor']) ) $atts['id'] = $block['anchor'];

$atts = get_block_wrapper_attributes( $atts );

echo '<'. $html_element .' '. $atts .'>';

echo '<button type="button" class="rs-mobile-menu-button closed" data-target="#rs-menu--'. esc_attr($menu_slug) .'" aria-label="Toggle Mobile Menu">';

foreach( $icons as $state => $i ) {
	
	// $state == 'open' or 'close'
	$text = $i['text'];
	$icon_type = $i['icon_type'];
	$custom_icon = $i['custom_icon'];
	
	$html = array();
	
	// 1. Get the text
	if ( $appearance == 'text' || $appearance == 'icontext' ) {
		$html[] = '<span class="rs--text">'. $text .'</span>';
	}
	
	// 2. Get the icon
	if ( $appearance == 'icon' || $appearance == 'icontext' ) {
		$icon = '';
		
		if ( $icon_type == 'custom' ) {
			
			// Using a custom icon from the media library
			// attachment id, get as image element
			$image_id = (int) $custom_icon;
			
			// Get attached file path
			$file = get_attached_file( $image_id );
			
			// Check if SVG. Embed SVG directly, use <img> element otherwise
			if ( pathinfo( $file, PATHINFO_EXTENSION ) == 'svg' ) {
				$icon = file_get_contents( $file );
			}else{
				$icon = wp_get_attachment_image( $custom_icon, 'full' );
			}
			
		}else if ( str_starts_with( $icon_type, 'dashicons-' ) ) {
			
			// Dashicons use an SVG file included with this plugin. It does not load the dashicons font.
			// examples: dashicons-menu, dashicons-no
			$file = RS_Utility_Blocks_PATH . '/assets/icons/' . $icon_type . '.svg';
			if ( file_exists($file) ) $icon = file_get_contents($file);
			
		}
		
		if ( ! $icon ) {
			// As a fallback, use the appropriate symbol as plain text
			$icon = $i['symbol'];
		}
		
		$html[] = '<span class="rs--icon">'. $icon .'</span>';
	}
	
	// If icon-text placement is "icon-first", reverse the order
	if ( $appearance == 'icontext' && $icon_text_placement == 'icon-first' ) {
		$html = array_reverse( $html );
	}
	
	// Output the HTML
	// .rs-mobile-menu-button--open, .rs-mobile-menu-button--close
	echo '<span class="rs--state rs--state-'. $state .'">'. implode( '', $html ) .'</span>';
	
}

echo '</button>';

echo '</'. $html_element .'>';