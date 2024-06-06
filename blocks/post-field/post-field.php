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

// In the editor, if no post is defined then look up the latest post with a matching post type
if ( ! $post_id && $is_preview ) {
	$post_id = RS_Utility_Blocks_Functions::get_post_id_for_block_editor( $block );
}

// The target post is the one which the field is based off.
// It may not match the queried post being displayed on the page.
$target_post = null; // WP_Post

// Get fields: post
$post_type = get_field( 'post_type', $block['id'] );
$selection = get_field( 'selection', $block['id'] );

// Get fields: custom field
$display_field = get_field( 'post_display_field', $block['id'] );
$custom_field_key = get_field( 'custom_field_key', $block['id'] ); // if $display_field = "custom_field"
$default_value = get_field( 'default_value', $block['id'] );

// Get fields: link
$create_link = get_field( 'create_link', $block['id'] );
$new_tab = get_field( 'new_tab', $block['id'] );
$link_type = get_field( 'link_type', $block['id'] );
$custom_url = get_field( 'custom_url', $block['id'] );

// Get fields: formatting
$apply_formatting = get_field( 'apply_paragraph_formatting', $block['id'] );
$apply_formatting = get_field( 'apply_formatting', $block['id'] );

// Get the post to use
switch( $selection ) {
	
	case 'post_parent':
		$p = get_post($post_id);
		$target_post = $p && $p->post_parent ? get_post($p->post_parent) : false;
		break;
	
	case 'specific_post':
		$p = get_field( 'specific_post', $block['id'] );
		$target_post = $p ? get_post( $p ) : false;
		break;
	
	case 'post':
	default:
		$target_post = get_post($post_id);
		break;
	
}

// Get the field value
$value = RS_Utility_Blocks_Setup::get_post_field( $target_post, $display_field, $custom_field_key, $block );
if ( $default_value && RS_Utility_Blocks_Setup::is_field_empty($value) ) $value = $default_value;

// Check if the field is empty
$is_empty = RS_Utility_Blocks_Setup::is_field_empty($value);

// Get the URL
$url = $create_link ? RS_Utility_Blocks_Setup::get_post_link( $target_post, $link_type, $custom_url ) : false;

// Use a <div> if paragraph formatting is applied. Allow filtering.
$html_element = ( $apply_formatting ) ? 'div' : 'span';
$html_element = apply_filters( 'rs/post_field_element', $html_element, $post_id, $display_field, $custom_field_key, $value, $url, $block );

// Add additional classes
$classes = array();

if ( $display_field ) {
	$classes[] = 'display-field--' . $display_field;
}

if ( $display_field === 'custom_field' && $custom_field_key ) {
	$classes[] = 'custom-field-key--' . $custom_field_key;
}

$classes[] = ! $is_empty ? 'has-value' : 'no-value';

$classes[] = $url ? 'has-link' : 'no-link';

// Start output
$atts = array(
	'class' => implode(' ', $classes),
);

if ( !empty($block['anchor']) ) $atts['id'] = $block['anchor'];

$atts = get_block_wrapper_attributes( $atts );

echo '<'. $html_element .' '. $atts .'>';

if ( ! $is_empty ) {
	
	if ( $url ) {
		
		// On the block editor, show it as a link, but remove the href property
		$href = 'href="'. esc_attr( $url ) .'"';
		if ( is_admin() || acf_is_block_editor() ) $href = '';
		
		$target = $new_tab ? 'target="_blank"' : '';
		
		echo "<a $href $target >";
	}
	
	if ( $apply_formatting ) {
		echo wpautop($value);
	}else{
		echo $value;
	}
	
	if ( $url ) {
		echo '</a>';
	}
	
}else if ( $is_preview ) {
	
	if ( ! $display_field ) {
		echo '<em>Select a post field to display</em>';
	}else{
		if ( $display_field === 'custom_field' ) {
			if ( ! $custom_field_key ) {
				echo '<em>Enter a custom field key</em>';
			}else{
				echo '<em>Custom field is empty ('. $custom_field_key .')</em>';
			}
		}else{
			echo '<em>Post field is empty ('. $display_field .')</em>';
		}
	}
	
}

echo '</'. $html_element .'>';