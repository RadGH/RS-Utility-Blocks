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

?>
<div <?php echo get_block_wrapper_attributes(); ?>>
	
	<?php
	RS_Utility_Blocks_Profile::display_edit_profile_form( $block, $post_id );
	?>
	
</div>