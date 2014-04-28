<?php

/**
 * Lightbox functions
 * @package package Sell Media
 * @since Sell Media 1.9.3
 */

/*// Add AJAX actions
add_action('wp_ajax_sell_media_lightbox_ajax', 'sell_media_add_to_lightbox');
add_action('wp_ajax_nopriv_sell_media_lightbox_ajax', 'sell_media_add_to_lightbox');

function sell_media_add_to_lightbox() {
	$blogname = get_site_url();
	$blogname = str_replace( array(':','.','/','-'), '', $blogname);

	// get post ID
	$id = $_POST['id'];
	$cookie_name = '_stockphotography_lightbox_' . $blogname;
	if( isset($_COOKIE[$cookie_name]) ) {

		$cookie=  $_COOKIE[$cookie_name];
		$lightbox=explode(',',$cookie);

		if(!in_array($id,$lightbox)) {
			$cookie=$cookie.','.$id;
			$status = true;
		} else {
		// not in array
		// text will not update when clicked
		}

	} else {
		$cookie=$id;
		$status = true;
    }
	// set lightbox cookie
	setcookie($cookie_name, $cookie ,time()+3600*24*365,'/');

	// generate the response
	$response = json_encode(
		array(
			'success' => $status,
			'postID' => $id
		)
	);
	// JSON header
	header('Content-type: application/json');
	echo $response;
	die();
}

// Add AJAX actions
add_action('wp_ajax_sell_media_lightbox_remove_ajax', 'sell_media_remove_from_lightbox');
add_action('wp_ajax_nopriv_sell_media_lightbox_remove_ajax', 'sell_media_remove_from_lightbox');

function sell_media_remove_from_lightbox() {
	$blogname = get_site_url();
	$blogname = str_replace( array(':','.','/','-'), '', $blogname);

	// get post ID
	$id = $_POST['id'];
	$cookie_name = '_stockphotography_lightbox_' . $blogname;

	if( isset($_COOKIE[$cookie_name]) ) {

		$cookie=  $_COOKIE[$cookie_name];
		$lightbox=explode(',', $cookie);

		// remove post ID from lightbox cookie
		unset($lightbox[array_search($id, $lightbox)]);
		$lightbox = implode(',', $lightbox);
		$cookie = $lightbox;
		$status = true;

    }
	// set lightbox cookie
	setcookie($cookie_name, $cookie ,time()+3600*24*365,'/');

	// generate the response
	$response = json_encode(
		array(
			'success' => $status,
			'postID' => $id
		)
	);
	// JSON header
	header('Content-type: application/json');
	echo $response;
	die();
}*/

function sell_media_in_lightbox() {
	$blogname = get_site_url();
	$blogname = str_replace( array(':','.','/','-'), '', $blogname);

	global $post;
	$cookie_name = '_stockphotography_lightbox_' . $blogname;
	$lightbox=array();
	if(isset($_COOKIE[$cookie_name])) {
		$cookie= $_COOKIE[$cookie_name];
		$lightbox=explode(',', $cookie);
	}
	if(in_array($post->ID,$lightbox))
		return true;
}

/**
 * Adds the 'sell_media_lightbox' short code to the editor. [sell_media_lightbox]
 *
 * @since 1.9.3
 */
function sell_media_lightbox_shortcode() { ?>

	<?php wp_enqueue_script( 'sellMediaLightbox', SELL_MEDIA_PLUGIN_URL . 'js/sell_media_lightbox.js', array( 'jquery' ), SELL_MEDIA_VERSION ); ?>
	<div id="lightbox_content"></div>
<?php
}
add_shortcode( 'sell_media_lightbox', 'sell_media_lightbox_shortcode' );

/**
 * Global $discount_code_id;
 * ajax calculation of total
 */
function sell_media_lightbox_generator() {
    $posts = explode( ",", $_POST['lightbox_ids'] );

	$lightbox_content = '<div id="lightbox">';

		// Query posts
		$args = array(
				'posts_per_page' => -1,
				'post_type' => 'sell_media_item',
				'post__in' => $posts
		        );
		$posts = New WP_Query( $args );
		if ( $posts->posts ) {

			$lightbox_content .= '<div class="sell-media-grid-container">';

			foreach( $posts->posts as $post ) {
				$lightbox_content .= '<div class="sell-media-grid">
				                        <div class="item-inner">
				                            <a href="'. the_permalink().'">'.sell_media_item_icon( $post->ID ).'</a>
				                        </div>
				                    </div>';
								 }
				$lightbox_content .= '</div>';
			}
		$lightbox_content .= '</div>';
		echo $lightbox_content;

    die;


}
add_action( 'wp_ajax_sell_media_lightbox', 'sell_media_lightbox_generator' );
add_action( 'wp_ajax_nopriv_sell_media_lightbox', 'sell_media_lightbox_generator' );