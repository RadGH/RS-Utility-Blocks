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

$post_id = get_the_ID();

$post = get_post($post_id);

$display_field = get_field( 'post_display_field', $block['id'] );
$create_link = get_field( 'create_link', $block['id'] );
$new_tab = get_field( 'new_tab', $block['id'] );
$link_type = get_field( 'link_type', $block['id'] );
$custom_url = get_field( 'custom_url', $block['id'] );
$apply_formatting = get_field( 'apply_formatting', $block['id'] );

// Custom field key is only used if display_field is set to "custom_field"
$custom_field_key = get_field( 'custom_field_key', $block['id'] );

$value = RS_Utility_Blocks_Setup::get_post_field( $post, $display_field, $custom_field_key );

$url = $create_link ? RS_Utility_Blocks_Setup::get_post_link( $post, $link_type, $custom_url, $new_tab ) : false;

// Use a <div> if paragraph formatting is applied. Allow filtering.
$html_element = ( $apply_formatting ) ? 'div' : 'span';
$html_element = apply_filters( 'rs/post_field_element', $html_element, $post_id, $display_field, $custom_field_key, $value, $url, $block );

// Add additional classes
$classes = array();
$classes[] = $value ? 'has-value' : 'no-value';
$classes[] = $url ? 'has-link' : 'no-link';

if ( $display_field ) {
	$classes[] = 'display-field--' . $display_field;
}

if ( $display_field === 'custom_field' && $custom_field_key ) {
	$classes[] = 'custom-field-key--' . $custom_field_key;
}

// Start output
$atts = array(
	'class' => implode(' ', $classes),
);

if ( !empty($block['anchor']) ) $atts['id'] = $block['anchor'];

$atts = get_block_wrapper_attributes( $atts );

echo '<'. $html_element .' '. $atts .'>';

if ( $post_id && $value ) {
	
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
	
}else{
	
	if ( is_admin() || acf_is_block_editor() ) {
		if ( ! $display_field ) {
			echo '<em>Select a profile field to display</em>';
		}else if ( ! $value ) {
			if( $display_field === 'custom_field' ) {
				if ( ! $custom_field_key ) {
					echo '<em>Enter a custom field key</em>';
				}else{
					if ( $value === null ) {
						echo '<em>Custom field is empty ('. $custom_field_key .')</em>';
					}
				}
			}else{
				echo '<em>Profile field is empty ('. $display_field .')</em>';
			}
		}
	}
	
}

echo '</'. $html_element .'>';