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

$menu_slug = get_field( 'menu_slug', $block['id'] ); // "menu-slug" (should match a mobile menu button block)
if ( ! $menu_slug ) $menu_slug = 'default';

// Template to use by default
// [0] = Block name
// [1] = Block settings
// [2] = Array of child blocks (same format)
$template = array(
	array(
		'core/group',
		array(
			'layout' => 'constrained',
		),
		array(
			
			array(
				'core/paragraph',
				array(
					'content' => 'Add content to the mobile menu container here.',
				),
				array(),
			),
			
		),
	),
);

// HTML element to use for the wrapper
$html_element = 'div';

// Add additional classes
$classes = array();
$classes[] = 'rs-mobile-menu--container';

// Start output
$atts = array(
	'id' => 'rs-menu--'. esc_attr($menu_slug),
	'class' => implode(' ', $classes),
);

if ( !empty($block['anchor']) ) $atts['id'] = $block['anchor'];

$atts = get_block_wrapper_attributes( $atts );

echo '<'. $html_element .' '. $atts .'>';

echo '<InnerBlocks template="'. esc_attr( wp_json_encode( $template ) ) .'" class="rs-mobile-menu--inner" />';

echo '</'. $html_element .'>';