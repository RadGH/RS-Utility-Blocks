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

$url = get_field( 'url', $block['id'] );
$new_tab = get_field( 'new_tab', $block['id'] );
$title = get_field( 'title', $block['id'] );

// [0] = Block name
// [1] = Block settings
// [2] = Array of child blocks (same format)
$template = array(
	array(
		'core/paragraph',
        array(
	        'content' => 'Add content here.',
	    ),
        array(),
    ),
);

// Add additional classes
$classes = array();

// Start output
$atts = array(
	'class' => implode(' ', $classes),
);

if ( $is_preview ) {
	
	// On previews, just show as a <div> without the link effects
	$html_element = 'div';
	$url = false;
	
}else{
	
	// On the front end, show as a link and add extra attributes
	$html_element = 'a';
	$atts['href'] = $url;
	if ( $new_tab ) $atts['target'] = '_blank';
	if ( $title ) $atts['title'] = $title;
	
}

if ( !empty($block['anchor']) ) $atts['id'] = $block['anchor'];

$atts = get_block_wrapper_attributes( $atts );

if ( ! $is_preview ) echo '<'. $html_element .' '. $atts .'>';

echo '<InnerBlocks template="'. esc_attr( wp_json_encode( $template ) ) .'" />';

if ( ! $is_preview ) echo '</'. $html_element .'>';