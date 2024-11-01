<?php
function wpsts_menu_item() {
	global $wpsts_settings_page_hook;
    $wpsts_settings_page_hook = add_submenu_page( 'edit.php?post_type=testimonial', 'Testimonial Settings', 'Settings', 'administrator', 'wpsts_settings', 'wpsts_render_settings_page' );
}
add_action( 'admin_menu', 'wpsts_menu_item' );

function wpsts_scripts_styles($hook) {
	global $wpsts_settings_page_hook;
	if( $wpsts_settings_page_hook != $hook )
		return;
	wp_enqueue_style("wpsts-options-page", plugins_url( "css/settings.css" , __FILE__ ), false, "1.0", "all");
	wp_enqueue_script("wpsts-options-page", plugins_url( "js/settings.js" , __FILE__ ), false, "1.0");
	wp_enqueue_script('common');
	wp_enqueue_script('wp-lists');
	wp_enqueue_script('postbox');
}
add_action( 'admin_enqueue_scripts', 'wpsts_scripts_styles' );

function wpsts_render_settings_page() {
?>
<div class="wrap">
<div id="icon-options-general" class="icon32"></div>
<h2>Testimonial Settings</h2>
	<?php settings_errors(); ?>
	<div class="clearfix paddingtop20">
		<div class="first ninecol">
			<form method="post" action="options.php">
				<?php settings_fields( 'wpsts_settings' ); ?>
				<?php do_meta_boxes('wpsts_metaboxes','advanced',null); ?>
				<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
				<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>
			</form>
		</div>
		<div class="last threecol">
			<div class="side-block">
				Like the plugin? Don't forget to give it a good rating on WordPress.org.
			</div>
		</div>
	</div>
</div>
<?php }

function wpsts_create_options() { 
	
	add_settings_section( 'wpsts_appearence_section', null, null, 'wpsts_settings' );
	add_settings_section( 'wpsts_slider_section', null, null, 'wpsts_settings' );
	add_settings_section( 'wpsts_post_type_section', null, null, 'wpsts_settings' );

	add_settings_field(
        'bubble_color', '', 'wpsts_render_settings_field', 'wpsts_settings', 'wpsts_appearence_section',
		array(
			'title' => 'Bubble Color',
			'desc' => 'Color of the speech bubble (e.g. #3399FF) in the slider',
			'id' => 'bubble_color',
			'type' => 'text',
			'group' => 'wpsts_appearence_settings'
		)
    );
    add_settings_field(
        'bubble_text_color', '', 'wpsts_render_settings_field', 'wpsts_settings', 'wpsts_appearence_section',
		array(
			'title' => 'Bubble Text Color',
			'desc' => 'Color of the speech bubble text (e.g. #000)',
			'id' => 'bubble_text_color',
			'type' => 'text',
			'group' => 'wpsts_appearence_settings'
		)
    );
   	add_settings_field(
        'min_bubble_height', '', 'wpsts_render_settings_field', 'wpsts_settings', 'wpsts_appearence_section',
		array(
			'title' => 'Minimum Bubble Height',
			'desc' => 'Minimum height of the speech bubble in pixels (e.g. 100). This will help you make all your testimonials equal in height',
			'id' => 'min_bubble_height',
			'type' => 'text',
			'group' => 'wpsts_appearence_settings'
		)
    );
    add_settings_field(
        'display_website_url', '', 'wpsts_render_settings_field', 'wpsts_settings', 'wpsts_appearence_section',
		array(
			'title' => 'Display website url',
			'desc' => 'If checked, the user\'s website url will be shown under his or her name in the slider',
			'id' => 'display_website_url',
			'type' => 'checkbox',
			'group' => 'wpsts_appearence_settings'
		)
    );
    add_settings_field(
        'display_full', '', 'wpsts_render_settings_field', 'wpsts_settings', 'wpsts_appearence_section',
		array(
			'title' => 'Display full content in bubble',
			'desc' => 'If checked, the full testimonial content will be displayed in the speech bubble. Otherwise just the excerpt will be displayed',
			'id' => 'display_full',
			'type' => 'checkbox',
			'group' => 'wpsts_appearence_settings'
		)
    );
    add_settings_field(
        'items', '', 'wpsts_render_settings_field', 'wpsts_settings', 'wpsts_slider_section',
		array(
			'title' => 'Item Count',
			'desc' => 'Number of items to show on the widest screen',
			'id' => 'items',
			'type' => 'text',
			'group' => 'wpsts_slider_settings'
		)
    );
    add_settings_field(
        'single_item', '', 'wpsts_render_settings_field', 'wpsts_settings', 'wpsts_slider_section',
		array(
			'title' => 'Show Single Item',
			'desc' => 'If checked only a single item will be displayed no matter what the screen size is',
			'id' => 'single_item',
			'type' => 'checkbox',
			'group' => 'wpsts_slider_settings'
		)
    );
    add_settings_field(
        'slide_speed', '', 'wpsts_render_settings_field', 'wpsts_settings', 'wpsts_slider_section',
		array(
			'title' => 'Slider Speed',
			'desc' => 'Animation speed of the slider in milliseconds',
			'id' => 'slide_speed',
			'type' => 'text',
			'group' => 'wpsts_slider_settings'
		)
    );
    add_settings_field(
        'pagination_speed', '', 'wpsts_render_settings_field', 'wpsts_settings', 'wpsts_slider_section',
		array(
			'title' => 'Pagination Speed',
			'desc' => 'Pagination speed of the slider in milliseconds',
			'id' => 'pagination_speed',
			'type' => 'text',
			'group' => 'wpsts_slider_settings'
		)
    );
    add_settings_field(
        'rewind_speed', '', 'wpsts_render_settings_field', 'wpsts_settings', 'wpsts_slider_section',
		array(
			'title' => 'Rewind Speed',
			'desc' => 'Rewind speed of the slider in milliseconds',
			'id' => 'rewind_speed',
			'type' => 'text',
			'group' => 'wpsts_slider_settings'
		)
    );
	add_settings_field(
        'auto_play', '', 'wpsts_render_settings_field', 'wpsts_settings', 'wpsts_slider_section',
		array(
			'title' => 'Auto Play Slider',
			'desc' => 'If checked the slider will start to animate automatically on page load',
			'id' => 'auto_play',
			'type' => 'checkbox',
			'group' => 'wpsts_slider_settings'
		)
    );
	add_settings_field(
        'stop_on_hover', '', 'wpsts_render_settings_field', 'wpsts_settings', 'wpsts_slider_section',
		array(
			'title' => 'Stop on hover',
			'desc' => 'If checked the animation will stop on hover',
			'id' => 'stop_on_hover',
			'type' => 'checkbox',
			'group' => 'wpsts_slider_settings'
		)
    );
	add_settings_field(
        'navigation', '', 'wpsts_render_settings_field', 'wpsts_settings', 'wpsts_slider_section',
		array(
			'title' => 'Display Navigation',
			'desc' => 'If checked, next and previous links will be displayed',
			'id' => 'navigation',
			'type' => 'checkbox',
			'group' => 'wpsts_slider_settings'
		)
    );
	add_settings_field(
        'pagination', '', 'wpsts_render_settings_field', 'wpsts_settings', 'wpsts_slider_section',
		array(
			'title' => 'Display Pagination',
			'desc' => 'If checked the slider will be paginated',
			'id' => 'pagination',
			'type' => 'checkbox',
			'group' => 'wpsts_slider_settings'
		)
    );
	add_settings_field(
        'responsive', '', 'wpsts_render_settings_field', 'wpsts_settings', 'wpsts_slider_section',
		array(
			'title' => 'Responsive',
			'desc' => 'If checked the slider will automatically adapt to screen size',
			'id' => 'responsive',
			'type' => 'checkbox',
			'group' => 'wpsts_slider_settings'
		)
    );
    add_settings_field(
        'public_post_type', '', 'wpsts_render_settings_field', 'wpsts_settings', 'wpsts_post_type_section',
		array(
			'title' => 'Make Post Type Public',
			'desc' => 'If checked the testimonial post type will become public and your visitors will be able to see each testimonial at http://yourdomain.com/testimonial/some-testimonial-slug/',
			'id' => 'public_post_type',
			'type' => 'checkbox',
			'group' => 'wpsts_post_type_settings'
		)
    );
	add_settings_field(
        'public_taxonomy', '', 'wpsts_render_settings_field', 'wpsts_settings', 'wpsts_post_type_section',
		array(
			'title' => 'Make Taxonomy Public',
			'desc' => 'Check to make the testimonial_cat taxonomy public',
			'id' => 'public_taxonomy',
			'type' => 'checkbox',
			'group' => 'wpsts_post_type_settings'
		)
    );
    // Finally, we register the fields with WordPress 
	register_setting('wpsts_settings', 'wpsts_appearence_settings', 'wpsts_settings_validation');
	register_setting('wpsts_settings', 'wpsts_slider_settings', 'wpsts_settings_validation');
	register_setting('wpsts_settings', 'wpsts_post_type_settings', 'wpsts_settings_validation');
	
} // end sandbox_initialize_theme_options 
add_action('admin_init', 'wpsts_create_options');

function wpsts_settings_validation($input){
	return $input;
}

function wpsts_add_meta_boxes(){
	add_meta_box("wpsts_appearence_settings_metabox", 'Appearence Settings', "wpsts_metaboxes_callback", "wpsts_metaboxes", 'advanced', 'default', array('settings_section'=>'wpsts_appearence_section'));
	add_meta_box("wpsts_slider_settings_metabox", 'Slider Settings', "wpsts_metaboxes_callback", "wpsts_metaboxes", 'advanced', 'default', array('settings_section'=>'wpsts_slider_section'));
	add_meta_box("wpsts_post_type_settings_metabox", 'Post Type & Taxonomy Settings', "wpsts_metaboxes_callback", "wpsts_metaboxes", 'advanced', 'default', array('settings_section'=>'wpsts_post_type_section'));
}
add_action( 'admin_init', 'wpsts_add_meta_boxes' );

function wpsts_metaboxes_callback($post, $args){
	do_settings_fields( "wpsts_settings", $args['args']['settings_section'] );
	submit_button('Save Changes', 'secondary');
}

function wpsts_render_settings_field($args){
	$option_value = get_option($args['group']);
?>
	<div class="row clearfix">
		<div class="col colone"><?php echo $args['title']; ?></div>
		<div class="col coltwo">
	<?php if($args['type'] == 'text'): ?>
		<input type="text" id="<?php echo $args['id'] ?>" name="<?php echo $args['group'].'['.$args['id'].']'; ?>" value="<?php echo (isset($option_value[$args['id']]))?esc_attr($option_value[$args['id']]):''; ?>">
	<?php elseif ($args['type'] == 'select'): ?>
		<select name="<?php echo $args['group'].'['.$args['id'].']'; ?>" id="<?php echo $args['id']; ?>">
			<?php foreach ($args['options'] as $key=>$option) { ?>
				<option <?php if(isset($option_value[$args['id']])) selected($option_value[$args['id']], $key); echo 'value="'.$key.'"'; ?>><?php echo $option; ?></option><?php } ?>
		</select>
	<?php elseif($args['type'] == 'checkbox'): ?>
		<input type="hidden" name="<?php echo $args['group'].'['.$args['id'].']'; ?>" value="0" />
		<input type="checkbox" name="<?php echo $args['group'].'['.$args['id'].']'; ?>" id="<?php echo $args['id']; ?>" value="1" <?php if(isset($option_value[$args['id']])) checked($option_value[$args['id']], '1'); ?> />
	<?php elseif($args['type'] == 'textarea'): ?>
		<textarea name="<?php echo $args['group'].'['.$args['id'].']'; ?>" type="<?php echo $args['type']; ?>" cols="" rows=""><?php echo isset($option_value[$args['id']])?stripslashes(esc_textarea($option_value[$args['id']]) ):''; ?></textarea>
	<?php elseif($args['type'] == 'multicheckbox'):
		foreach ($args['items'] as $key => $checkboxitem ):
	?>
		<input type="hidden" name="<?php echo $args['group'].'['.$args['id'].']['.$key.']'; ?>" value="0" />
		<label for="<?php echo $args['group'].'['.$args['id'].']['.$key.']'; ?>"><?php echo $checkboxitem; ?></label> <input type="checkbox" name="<?php echo $args['group'].'['.$args['id'].']['.$key.']'; ?>" id="<?php echo $args['group'].'['.$args['id'].']['.$key.']'; ?>" value="1" 
		<?php if($key=='reason'){ ?>checked="checked" disabled="disabled"<?php }else{ checked($option_value[$args['id']][$key]); } ?> />
	<?php endforeach; ?>
	<?php endif; ?>
		</div>
		<div class="col colthree"><small><?php echo $args['desc'] ?></small></div>
	</div>
<?php
}

?>