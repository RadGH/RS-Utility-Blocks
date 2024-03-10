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

$classes = array(
	'rs-utility-blocks--user-field',
);

$user_id = get_current_user_id();

$display_field = get_field( 'user_profile_display_field', $block['id'] );
$create_link = get_field( 'create_link', $block['id'] );
$new_tab = get_field( 'new_tab', $block['id'] );
$link_type = get_field( 'link_type', $block['id'] );
$custom_url = get_field( 'custom_url', $block['id'] );

echo '<span class="' . esc_attr( implode( ' ', $classes ) ) . '">';

if ( $user_id ) {

	$value = RS_Utility_Blocks_Setup::get_user_field( $user_id, $display_field );
	$url = $create_link ? RS_Utility_Blocks_Setup::get_user_link( $user_id, $link_type, $custom_url, $new_tab ) : false;
	
	if ( $url ) {
		
		// On the block editor, show it as a link, but remove the href property
		$href = 'href="'. esc_attr( $url ) .'"';
		if ( is_admin() || acf_is_block_editor() ) $href = '';
		
		$target = $new_tab ? 'target="_blank"' : '';
		
		echo "<a $href $target >";
	}
	
	if ( $value ) echo $value;
	
	if ( $url ) {
		echo '</a>';
	}
	
}

echo '</span>';