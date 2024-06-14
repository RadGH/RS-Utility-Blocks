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

$separator = get_field( 'separator', $block['id'] );
$link_current_page = get_field( 'link_current_page', $block['id'] );
$show_home = get_field( 'show_home', $block['id'] );

// In the editor, if no post is defined then look up the latest post with a matching post type
if ( ! $post_id && $is_preview ) {
	$post_id = RS_Utility_Blocks_Functions::get_post_id_for_block_editor( $block );
}

// Get breadcrumb trail as array
$args = array(
	'link_current_page' => $link_current_page,
	'home' => $show_home,
);
$breadcrumbs = RS_Utility_Blocks_Functions::get_breadcrumbs( $post_id, $args );

// Get current page url
$current_url = get_permalink();

// Add additional classes
$classes = array(); // wp-block-rs-utility-blocks-breadcrumbs

// Start output
$atts = array(
	'class' => implode(' ', $classes),
);

$html_element = 'div';

if ( !empty($block['anchor']) ) $atts['id'] = $block['anchor'];

$atts = get_block_wrapper_attributes( $atts );

if ( ! $is_preview ) echo '<'. $html_element .' '. $atts .'>';

foreach( $breadcrumbs as $i => $crumb ) {
	$c_post_id = $crumb['post_id'];
	$c_title = $crumb['title'];
	$c_url = $crumb['url'];
	
	// Disable links on the editor
	if ( $is_preview ) {
		$c_url = 'javascript:void();';
	}
	
	if ( $c_post_id ) {
		$is_active = $c_post_id == $post_id;
	}else{
		$is_active = RS_Utility_Blocks_Functions::compare_urls( $c_url, $current_url );
	}
	
	?>
	<div class="rs-breadcrumbs--item">
		
		<?php if ( $c_url ) { ?>
			<a href="<?php echo esc_attr( $c_url ); ?>" class="rs-breadcrumbs--item__content">
		<?php }else{ ?>
			<span class="rs-breadcrumbs--item__content no-link">
		<?php } ?>

		<span class="rs-breadcrumbs--item__label"><?php echo esc_html( $c_title ); ?></span>
		
		<?php if ( ! $c_url ) { ?>
			</span>
		<?php }else{ ?>
			</a>
		<?php } ?>
	</div>
	<?php
	
	// Unless this is the last item, add a separator item
	if ( $separator && $i < count($breadcrumbs) - 1 ) {
		?>
		<div class="rs-breadcrumbs--item separator">
			<span class="rs-breadcrumbs--item__content">
				<span class="rs-breadcrumbs--item__label"><?php echo esc_html($separator); ?></span>
			</span>
		</div>
		<?php
	}
}

if ( ! $is_preview ) echo '</'. $html_element .'>';