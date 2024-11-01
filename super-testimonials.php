<?php
/*
Plugin Name: Super Testimonials
Plugin URI: http://geekhippo.com/
Description: Creates a post type for testimonials and allows you to display your testimonials in a beautiful, responsive slider with the help of a shortcode.
Version: 1.0
Author: Bilal Khan
Author URI: http://geekhippo.com/
License: GPL2
*/

/*
* Initialize the plugin options on first run
*/

function wpsts_initialize(){
	$not_fresh_install = get_option('wpsts_post_type_settings');
	if( $not_fresh_install ) return;
	
	$appearence_settings = array( 'bubble_color' => '#3399FF', 'bubble_text_color' => '#FFFFFF', 'min_bubble_height' => '200', 'display_website_url' => '1', 'display_full' => '1' );
	$slider_settings = array( 'items' => '3', 'single_item' => '0', 'slide_speed' => '500', 'pagination_speed' => '500', 'rewind_speed' => '500', 'auto_play' => '1', 'stop_on_hover' => '1', 'navigation' => '0', 'pagination' => '1', 'responsive' => '1' );
	$type_settings = array( 'public_post_type' => '0', 'public_taxonomy' => '0' );

	update_option('wpsts_appearence_settings', $appearence_settings);
	update_option('wpsts_slider_settings', $slider_settings);
	update_option('wpsts_post_type_settings', $type_settings);
}
register_activation_hook(__FILE__, 'wpsts_initialize');

/*
* Delete the plugin options on uninstall
*/

function wpsts_remove_options(){
	delete_option('wpsts_appearence_settings');
	delete_option('wpsts_slider_settings');
	delete_option('wpsts_post_type_settings');
}
register_uninstall_hook(__FILE__, 'wpsts_remove_options');

/*
* Enqueue scripts and stylesheets on the pages where the shortcode has been used
*/

function wpsts_enqueue_shortcode_files($posts) {
    if ( empty($posts) )
        return $posts;
 
    $found_slider = false;
    foreach ($posts as $post) {
        if ( has_shortcode($post->post_content, 'wpsts_testimonial_slider') ){
        	$found_slider = true;
        	break;
        }
    }
 
    if ($found_slider){
        wp_enqueue_style( 'wpsts-testimonial-slider', plugins_url('css/testimonial-slider.css', __FILE__), array(), '1.0', 'all' );
        wp_enqueue_script( "wpsts-testimonial-slider", plugins_url('js/testimonial-slider.js', __FILE__ ), array('jquery') );
        $slider_settings = get_option('wpsts_slider_settings');
        wp_localize_script( 'wpsts-testimonial-slider', 'wpsts', $slider_settings);
    }
    return $posts;
}
add_action('the_posts', 'wpsts_enqueue_shortcode_files');

/*
* If the function has_sortcode() is not defined, define it
*/

if(!function_exists('has_shortcode')){
	function has_shortcode( $content, $tag ) {
		if(stripos($content, '['.$tag.']') !== false)
			return true;
		return false;
	}
}

/*
* Setup the metabox for testimonial post type
*/

add_action( 'load-post.php', 'wpsts_customer_details_metabox_setup' );
add_action( 'load-post-new.php', 'wpsts_customer_details_metabox_setup' );

function wpsts_customer_details_metabox_setup() {
	add_action( 'add_meta_boxes', 'wpsts_add_customer_details_metabox' );
	add_action( 'save_post', 'wpsts_save_customer_details_meta', 10, 2 );
}

function wpsts_add_customer_details_metabox() {
	add_meta_box(
		'wpsts_customer_details',
		'Customer Details',
		'wpsts_render_customer_details_metabox',
		'testimonial',
		'normal',
		'core'
	);
}

function wpsts_render_customer_details_metabox( $post, $box ) { 
	wp_nonce_field( basename( __FILE__ ), 'wpsts_customer_details_metabox_nonce' ); 
	$curr_values = get_post_meta( $post->ID, 'customer_details', true );
?>	
	<style>
		#wpsts-user-details-metabox label{
			display: table;
			clear: both;
			margin: 10px 0;
		}
		#wpsts-user-details-metabox input{
			padding: 10px;
		}
	</style>
	<div id="wpsts-user-details-metabox">
		<label for="wpsts_customer_details[name]">Name:</label>
		<input type="text" id="wpsts_customer_details[name]" name="wpsts_customer_details[name]" value="<?=isset($curr_values['name'])?$curr_values['name']:''?>"/>
		<label for="wpsts_customer_details[email]">Email:</label>
		<input type="text" id="wpsts_customer_details[email]" name="wpsts_customer_details[email]" value="<?=isset($curr_values['email'])?$curr_values['email']:''?>"/>
		<label for="wpsts_customer_details[website]">Website:</label>
		<input type="text" id="wpsts_customer_details[website]" name="wpsts_customer_details[website]" value="<?=isset($curr_values['website'])?$curr_values['website']:''?>"/>
	</div>
<?php
}

function wpsts_save_customer_details_meta( $post_id, $post ) {

	/* Verify the nonce before proceeding. */
	if ( !isset( $_POST['wpsts_customer_details_metabox_nonce'] ) || !wp_verify_nonce( $_POST['wpsts_customer_details_metabox_nonce'], basename( __FILE__ ) ) )
		return $post_id;

	/* Get the post type object. */
	$post_type = get_post_type_object( $post->post_type );

	/* Check if the current user has permission to edit the post. */
	if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
		return $post_id;

	/* Get the posted data and sanitize it for use as an HTML class. */
	$form_data = ( isset( $_POST['wpsts_customer_details'] ) ?  $_POST['wpsts_customer_details'] : false );

	$new_meta_value = $form_data;

	/* Get the meta value of the custom field key. */
	$curr_meta_value = get_post_meta( $post_id, 'customer_details', true );

	/* If a new meta value was added and there was no previous value, add it. */
	if ( $new_meta_value )
		update_post_meta( $post_id, 'customer_details', $new_meta_value );
	elseif ( $curr_meta_value )
		delete_post_meta( $post_id, 'customer_details' );
}

/*
* Register the custom post type and taxonomy
*/

function wpsts_register_type_taxonomy() {
	$settings = get_option('wpsts_post_type_settings');
	$is_type_public = (isset($settings['public_post_type']))?(boolean) $settings['public_post_type']:false;
	$is_tax_public = (isset($settings['public_taxonomy']))?(boolean) $settings['public_taxonomy']:false;
	register_post_type( 'testimonial',
		array(
			'labels' => array(
				'name' 					=> 'Testimonials', 
				'singular_name' 		=> 'Testimonial',
				'all_items' 			=> 'All Testimonials',
				'add_new' 				=> 'Add New',
				'add_new_item' 			=> 'Add New Testimonial',
				'edit' 					=>  'Edit',
				'edit_item' 			=> 'Edit Testimonial',
				'new_item' 				=> 'New Testimonial',
				'view_item' 			=> 'View Testimonial',
				'search_items' 			=> 'Search Testimonials', 
				'not_found' 			=>  'Nothing found in the Database.',
				'not_found_in_trash' 	=> 'Nothing found in Trash',
				'parent_item_colon' 	=> ''
			),
			'description' 				=>  'This is the Testimonial post type',
			'public' 					=> $is_type_public,
			'publicly_queryable' 		=> $is_type_public,
			'exclude_from_search' 		=> !$is_type_public,
			'show_ui' 					=> true,
			'query_var' 				=> $is_type_public,
			'menu_position' 			=> 8,
			'rewrite'					=> array( 'slug' => 'testimonial', 'with_front' => true ),
			'taxonomies' 				=> array( 'testimonial_cat' ),
			'has_archive' 				=> false,
			'capability_type' 			=> 'post',
			'hierarchical' 				=> false,
			'supports' 					=> array( 'title', 'editor', 'thumbnail', 'excerpt' )
	 	)
	);
	register_taxonomy(
		'testimonial_cat',
		'testimonial',
		array(
			'hierarchical' 			=> true,
			'labels' 				=> array(
				'name' 				=> 'Category',
				'singular_name' 	=> 'Categories',
				'search_items' 		=> 'Search Category',
				'all_items' 		=> 'All Category',
				'parent_item' 		=> 'Parent Categories',
				'parent_item_colon' => 'Parent Categories:',
				'edit_item' 		=> 'Edit Categories',
				'update_item' 		=> 'Update Categories',
				'add_new_item' 		=> 'Add New Categories',
				'new_item_name' 	=> 'New Categories Name'
			),
			'public' 				=> $is_tax_public,
			'hierarchical' 			=> true,
			'show_admin_column' 	=> true,
			'show_ui' 				=> true,
			'query_var' 			=> $is_tax_public,
			'rewrite' 				=> array( 'slug' => 'testimonial-category', 'hierarchical' => true )
		)
	);
	$flushed = get_option('wpsts_flush_done');
	if(!$flushed){
		flush_rewrite_rules(true);
		update_option('wpsts_flush_done', true);
	}
}
add_action( 'init', 'wpsts_register_type_taxonomy');

/*
* Setup the shortcode
*/

function wpsts_testimonial_slider_callback( $atts ) {
    $a = shortcode_atts( array(
        'count' => -1,
        'category' => false,
    ), $atts );
	$args = array('post_type' => 'testimonial', 'post_status' => 'publish', 'posts_per_page'   => $a['count']);
	if($a['category']) $args['tax_query'] = array( array('taxonomy' => 'testimonial_cat', 'field' => 'slug', 'terms' => $a['category']));
	global $post;
	$testimonials = get_posts( $args );
	$settings = get_option('wpsts_appearence_settings');

	ob_start();
?>
	<div id="wpsts-testimonial-slider" class="owl-carousel">
		<?php foreach ( $testimonials as $post ) : setup_postdata( $post ); ?>
			<div class="testimonial-container">
				<div class="testimonial" style="
					<?=!empty($settings['bubble_color'])?'background:'.$settings['bubble_color'].';':''?>
					<?=(!empty($settings['min_bubble_height']) && is_numeric($settings['min_bubble_height']))?'min-height:'.$settings['min_bubble_height'].'px;':''?>
					<?=!empty($settings['bubble_text_color'])?'color:'.$settings['bubble_text_color'].';':''?>
				">
					<h3><?php the_title(); ?></h3>
					<?php (!empty($settings['display_full']) && $settings['display_full'])?the_content():the_excerpt(); ?>
					<?php $customer_details = get_post_meta(get_the_ID(), 'customer_details', true); ?>
					<div class="customer-details">
						<span class="customer-name"><?=isset($customer_details['name'])?$customer_details['name']:''?></span><br/>
						<?php if(!empty($settings['display_website_url']) && $settings['display_website_url']): ?>
							<span class="customer-website"><?=isset($customer_details['website'])?$customer_details['website']:''?></span>
						<?php endif; ?>
					</div>
				</div>
				<span class="icon-arrow-down" style="<?=!empty($settings['bubble_color'])?'color:'.$settings['bubble_color'].';':''?>"></span>
				<?php the_post_thumbnail(array(64,64)); ?>
			</div>
		<?php endforeach; wp_reset_postdata(); ?>
	</div>
<?php
	return ob_get_clean();
}
add_shortcode( 'wpsts_testimonial_slider', 'wpsts_testimonial_slider_callback' );

/*
* Change excerpt length for the testimonial post type
*/

function wpsts_testimonial_excerpt_length($length) {
    global $post;
    if ($post->post_type == 'testimonial')
		return 40;
    return $length;
}
add_filter('excerpt_length', 'wpsts_testimonial_excerpt_length');

/*
* Include the settings page
*/

include_once('settings.php');

?>