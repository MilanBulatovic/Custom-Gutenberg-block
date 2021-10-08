<?php
/**
 * Blocks Initializer
 *
 * Enqueue CSS/JS of all the blocks.
 *
 * @since   1.0.0
 * @package CGB
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue Gutenberg block assets for both frontend + backend.
 *
 * Assets enqueued:
 * 1. blocks.style.build.css - Frontend + Backend.
 * 2. blocks.build.js - Backend.
 * 3. blocks.editor.build.css - Backend.
 *
 * @uses {wp-blocks} for block type registration & related functions.
 * @uses {wp-element} for WP Element abstraction — structure of blocks.
 * @uses {wp-i18n} to internationalize the block's text.
 * @uses {wp-editor} for WP editor styles.
 * @since 1.0.0
 */
function post_loop_cgb_block_assets() { // phpcs:ignore
	// Register block styles for both frontend + backend.
	wp_register_style(
		'post_loop-cgb-style-css', // Handle.
		plugins_url( 'dist/blocks.style.build.css', dirname( __FILE__ ) ), // Block style CSS.
		is_admin() ? array( 'wp-editor' ) : null, // Dependency to include the CSS after it.
		null // filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.style.build.css' ) // Version: File modification time.
	);

	// Register block editor script for backend.
	wp_register_script(
		'post_loop-cgb-block-js', // Handle.
		plugins_url( '/dist/blocks.build.js', dirname( __FILE__ ) ), // Block.build.js: We register the block here. Built with Webpack.
		array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ), // Dependencies, defined above.
		null, // filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.build.js' ), // Version: filemtime — Gets file modification time.
		true // Enqueue the script in the footer.
	);

	// Register block editor styles for backend.
	wp_register_style(
		'post_loop-cgb-block-editor-css', // Handle.
		plugins_url( 'dist/blocks.editor.build.css', dirname( __FILE__ ) ), // Block editor CSS.
		array( 'wp-edit-blocks' ), // Dependency to include the CSS after it.
		null // filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.editor.build.css' ) // Version: File modification time.
	);

	// WP Localized globals. Use dynamic PHP stuff in JavaScript via `cgbGlobal` object.
	wp_localize_script(
		'post_loop-cgb-block-js',
		'cgbGlobal', // Array containing dynamic data for a JS Global.
		[
			'pluginDirPath' => plugin_dir_path( __DIR__ ),
			'pluginDirUrl'  => plugin_dir_url( __DIR__ ),
			// Add more data here that you want to access from `cgbGlobal` object.
		]
	);

	/**
	 * Register Gutenberg block on server-side.
	 *
	 * Register the block on server-side to ensure that the block
	 * scripts and styles for both frontend and backend are
	 * enqueued when the editor loads.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/blocks/writing-your-first-block-type#enqueuing-block-scripts
	 * @since 1.16.0
	 */
	register_block_type(
		'cgb/block-post-loop', array(
			// Enqueue blocks.style.build.css on both frontend & backend.
			'style'         => 'post_loop-cgb-style-css',
			// Enqueue blocks.build.js in the editor only.
			'editor_script' => 'post_loop-cgb-block-js',
			// Enqueue blocks.editor.build.css in the editor only.
			'editor_style'  => 'post_loop-cgb-block-editor-css',
			//Rendering function
			'render_callback' => 'display_post_block'
		)
	);
}

//Function for displaying block
function display_post_block( $attributes ){
	$posts = get_posts([
		'category' => $attributes['selectedCategory'],
		'posts_per_page' => $attributes['postPerPage'],
		'meta_key'=>'post_views_count',
		'orderby' => 'meta_value_num'
	]);

	ob_start();

	foreach( $posts as $post ){
		echo '<div class="post-sidebar-content">';
		echo '<a href="' . get_permalink($post->ID) . '">' . get_the_post_thumbnail($post->ID, 'sidebar') . '</a>';
		echo '<div class="post-title-date-wrapper">';
		echo '<h5 class="sidebar-post-title">' . '<a href="' . get_permalink($post->ID) . '">' . wp_trim_words($post->post_title, 6) . '</a>' . '</h5>';
		//echo '<p class="sidebar-post-date">' . get_the_date() . '">' .  '</p>';
		echo '</div>';
		echo '</div>';
		echo '<hr>';
		//echo '<p class="sidebar-post-date">' . '<i class="bi bi-chat-right-text-fill"></i>' . get_comments_number() . '</p>';
	}

	return ob_get_clean();
}

// Hook: Block assets.
add_action( 'init', 'post_loop_cgb_block_assets' );
