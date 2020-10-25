<?php
/**
 * ------------------------------------------------------------------------------
 * Plugin Name: Add Open Graph Tags
 * Description: Add Open Graph Tags to attach rich photos to social media posts, helping to drive traffic to your website.
 * Version: 1.2.1
 * Author: azurecurve
 * Author URI: https://development.azurecurve.co.uk/classicpress-plugins/
 * Plugin URI: https://development.azurecurve.co.uk/classicpress-plugins/add-open-graph-tags/
 * Text Domain: add-open-graph-tags
 * Domain Path: /languages
 * ------------------------------------------------------------------------------
 * This is free software released under the terms of the General Public License,
 * version 2, or later. It is distributed WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Full
 * text of the license is available at https://www.gnu.org/licenses/gpl-2.0.html.
 * ------------------------------------------------------------------------------
 */

// Prevent direct access.
if (!defined('ABSPATH')){
	die();
}

// include plugin menu
require_once(dirname(__FILE__).'/pluginmenu/menu.php');
add_action('admin_init', 'azrcrv_create_plugin_menu_aogt');

// include update client
require_once(dirname(__FILE__).'/libraries/updateclient/UpdateClient.class.php');

/**
 * Setup registration activation hook, actions, filters and shortcodes.
 *
 * @since 1.0.0
 *
 */
add_action('admin_init', 'azrcrv_aogt_set_default_options');

// add actions
add_action('admin_menu', 'azrcrv_aogt_create_admin_menu');
add_action('admin_post_azrcrv_aogt_save_options', 'azrcrv_aogt_save_options');
add_action('admin_enqueue_scripts', 'azrcrv_aogt_load_jquery');
add_action('admin_enqueue_scripts', 'azrcrv_aogt_media_uploader');
add_action( 'wp_head', 'azrcrv_aogt_insert_opengraph_tags', 0 );
add_action('plugins_loaded', 'azrcrv_aogt_load_languages');

// add filters
add_filter('plugin_action_links', 'azrcrv_aogt_add_plugin_action_link', 10, 2);
add_filter('codepotent_update_manager_image_path', 'azrcrv_aogt_custom_image_path');
add_filter('codepotent_update_manager_image_url', 'azrcrv_aogt_custom_image_url');

/**
 * Load language files.
 *
 * @since 1.0.0
 *
 */
function azrcrv_aogt_load_languages() {
    $plugin_rel_path = basename(dirname(__FILE__)).'/languages';
    load_plugin_textdomain('add-open-graph-tags', false, $plugin_rel_path);
}

/**
 * Media Uploader.
 *
 * @since 1.0.0
 *
 */
function azrcrv_aogt_media_uploader() {
    global $post_type;
        if(function_exists('wp_enqueue_media')) {
            wp_enqueue_media();
        }
        else {
            wp_enqueue_script('media-upload');
            wp_enqueue_script('thickbox');
            wp_enqueue_style('thickbox');
        }
}

/**
 * Load JQuery.
 *
 * @since 1.0.0
 *
 */
function azrcrv_aogt_load_jquery($hook){
	wp_enqueue_script( 'azrcrv-aogt', plugins_url('assets/jquery/jquery.js',__FILE__));
}

/**
 * Custom plugin image path.
 *
 * @since 1.2.0
 *
 */
function azrcrv_aogt_custom_image_path($path){
    if (strpos($path, 'azrcrv-add-open-graph-tags') !== false){
        $path = plugin_dir_path(__FILE__).'assets/pluginimages';
    }
    return $path;
}

/**
 * Custom plugin image url.
 *
 * @since 1.2.0
 *
 */
function azrcrv_aogt_custom_image_url($url){
    if (strpos($url, 'azrcrv-add-open-graph-tags') !== false){
        $url = plugin_dir_url(__FILE__).'assets/pluginimages';
    }
    return $url;
}

/**
 * Set default options for plugin.
 *
 * @since 1.0.0
 *
 */
function azrcrv_aogt_set_default_options($networkwide){
	
	$option_name = 'azrcrv-aogt';
	
	$new_options = array(
						'use_ffi' => 0,
						'fallback_image' => '',
						'dimensions' => array(
													'width' => 100,
													'height' => 100,
												),
						'updated' => strtotime('2020-10-25'),
			);
	
	// set defaults for multi-site
	if (function_exists('is_multisite') && is_multisite()){
		// check if it is a network activation - if so, run the activation function for each blog id
		if ($networkwide){
			global $wpdb;

			$blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
			$original_blog_id = get_current_blog_id();

			foreach ($blog_ids as $blog_id){
				switch_to_blog($blog_id);
				
				azrcrv_aogt_update_options($option_name, $new_options, false);
			}

			switch_to_blog($original_blog_id);
		}else{
			azrcrv_aogt_update_options( $option_name, $new_options, false);
		}
		if (get_site_option($option_name) === false){
			azrcrv_aogt_update_options($option_name, $new_options, true);
		}
	}
	//set defaults for single site
	else{
		azrcrv_aogt_update_options($option_name, $new_options, false);
	}
}

/**
 * Update options.
 *
 * @since 1.1.3
 *
 */
function azrcrv_aogt_update_options($option_name, $new_options, $is_network_site){
	if ($is_network_site == true){
		if (get_site_option($option_name) === false){
			add_site_option($option_name, $new_options);
		}else{
			$options = get_site_option($option_name);
			if (!isset($options['updated']) OR $options['updated'] < $new_options['updated'] ){
				$options['updated'] = $new_options['updated'];
				update_site_option($option_name, azrcrv_aogt_update_default_options($options, $new_options));
			}
		}
	}else{
		if (get_option($option_name) === false){
			add_option($option_name, $new_options);
		}else{
			$options = get_option($option_name);
			if (!isset($options['updated']) OR $options['updated'] < $new_options['updated'] ){
				$options['updated'] = $new_options['updated'];
				update_option($option_name, azrcrv_aogt_update_default_options($options, $new_options));
			}
		}
	}
}


/**
 * Add default options to existing options.
 *
 * @since 1.1.3
 *
 */
function azrcrv_aogt_update_default_options( &$default_options, $current_options ) {
    $default_options = (array) $default_options;
    $current_options = (array) $current_options;
    $updated_options = $current_options;
    foreach ($default_options as $key => &$value) {
        if (is_array( $value) && isset( $updated_options[$key])){
            $updated_options[$key] = azrcrv_aogt_update_default_options($value, $updated_options[$key]);
        } else {
			$updated_options[$key] = $value;
        }
    }
    return $updated_options;
}

/**
 * Add pluginnameazrcrv-aogt action link on plugins page.
 *
 * @since 1.0.0
 *
 */
function azrcrv_aogt_add_plugin_action_link($links, $file){
	static $this_plugin;

	if (!$this_plugin){
		$this_plugin = plugin_basename(__FILE__);
	}

	if ($file == $this_plugin){
		$settings_link = '<a href="'.get_bloginfo('wpurl').'/wp-admin/admin.php?page=azrcrv-aogt"><img src="'.plugins_url('/pluginmenu/images/Favicon-16x16.png', __FILE__).'" style="padding-top: 2px; margin-right: -5px; height: 16px; width: 16px;" alt="azurecurve" />'.esc_html__('Settings' ,'add-open-graph-tags').'</a>';
		array_unshift($links, $settings_link);
	}

	return $links;
}

/**
 * Add to menu.
 *
 * @since 1.0.0
 *
 */
function azrcrv_aogt_create_admin_menu(){
	//global $admin_page_hooks;
	
	add_submenu_page("azrcrv-plugin-menu"
						,esc_html__("Add Open Graph Tags Settings", 'add-open-graph-tags')
						,esc_html__("Add Open Graph Tags", 'add-open-graph-tags')
						,'manage_options'
						,'azrcrv-aogt'
						,'azrcrv_aogt_display_options');
}

/**
 * Display Settings page.
 *
 * @since 1.0.0
 *
 */
function azrcrv_aogt_display_options(){
	if (!current_user_can('manage_options')){
        wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'add-open-graph-tags'));
    }
	
	// Retrieve plugin configuration options from database
	$options = get_option('azrcrv-aogt');
	?>
	<div id="azrcrv-aogt-general" class="wrap">
		<fieldset>
			<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
			<?php if(isset($_GET['settings-updated'])){ ?>
				<div class="notice notice-success is-dismissible">
					<p><strong><?php esc_html_e('Settings have been saved.', 'add-open-graph-tags'); ?></strong></p>
				</div>
			<?php } ?>
			<form method="post" action="admin-post.php">
				<input type="hidden" name="action" value="azrcrv_aogt_save_options" />
				<input name="page_options" type="hidden" value="use_ffi,fallback_image" />
				
				<!-- Adding security through hidden referrer field -->
				<?php wp_nonce_field('azrcrv-aogt', 'azrcrv-aogt-nonce'); ?>
				<table class="form-table">
				
				<tr><th scope="row"><?php esc_html_e('Use floating featured image?', 'add-open-graph-tags'); ?></th><td>
					<fieldset><legend class="screen-reader-text"><span>Use floating featured image</span></legend>
					<label for="use_ffi"><input name="use_ffi" type="checkbox" id="use_ffi" value="1" <?php checked( '1', $options['use_ffi'] ); ?> /><?php esc_html_e('Use floating featured image in Twitter card?', 'add-open-graph-tags'); ?></label>
					</fieldset>
				</td></tr>
				
				<tr><th scope="row"><label for="dimensions"><?php esc_html_e('Minimum Dimensions', 'add-open-graph-tags'); ?></label></th><td>
					<input type="number" name="dimensions-width" value="<?php echo esc_html(stripslashes($options['dimensions']['width'])); ?>" class="small-text" />&nbsp;x&nbsp;<input type="number" name="dimensions-height" value="<?php echo esc_html(stripslashes($options['dimensions']['height'])); ?>" class="small-text" />
					<p class="description"><?php esc_html_e('Specify minimum dimensions (width and height).', 'add-open-graph-tags'); ?></p>
				</td></tr>
				
				<tr><th scope="row"><label for="fallback_image"><?php esc_html_e('Fallback Image', 'add-open-graph-tags'); ?></label></th><td>
					<img src="<?php echo esc_url($options['fallback_image']); ?>" id="azrcrv-aogt-fallback-image" style="width: 300px;"><br />
					<!-- Outputs the text field and displays the URL of the image retrieved by the media uploader -->
					<input type="hidden" name="fallback_image" id="fallback_image" value="<?php echo esc_url($options['fallback_image']); ?>" class="regular-text" />
					
					<input type='button' id="azrcrv-aogt-upload-image" class="button upload" value="<?php esc_html_e( 'Upload image', 'add-open-graph-tags' ); ?>" />
					<input type='button' id="azrcrv-aogt-remove-image" class="button remove" value="<?php esc_html_e( 'Remove image', 'add-open-graph-tags' ); ?>" /><br />
					<span class="description"><?php esc_html_e( 'Upload, choose or remove your fallback image; recommended open graph image is 1200px by 627px.', 'add-open-graph-tags' ); ?></span>
				</td></tr>
				
				</table>
				<input type="submit" value="Save Changes" class="button-primary"/>
			</form>
		</fieldset>
	</div>
	<?php
}

/**
 * Save settings.
 *
 * @since 1.0.0
 *
 */
function azrcrv_aogt_save_options(){
	// Check that user has proper security level
	if (!current_user_can('manage_options')){
		wp_die(esc_html__('You do not have permissions to perform this action', 'add-open-graph-tags'));
	}
	// Check that nonce field created in configuration form is present
	if (! empty($_POST) && check_admin_referer('azrcrv-aogt', 'azrcrv-aogt-nonce')){
	
		// Retrieve original plugin options array
		$options = get_option('azrcrv-aogt');
		
		$option_name = 'use_ffi';
		if (isset($_POST[$option_name])){
			$options[$option_name] = 1;
		}else{
			$options[$option_name] = 0;
		}
		
		$option_name = 'dimensions-width';
		if (isset($_POST[$option_name])){
			$options['dimensions']['width'] = sanitize_text_field($_POST[$option_name]);
		}
		$option_name = 'dimensions-height';
		if (isset($_POST[$option_name])){
			$options['dimensions']['height'] = sanitize_text_field($_POST[$option_name]);
		}
		
		$option_name = 'fallback_image';
		if (isset($_POST[$option_name])){
			$options[$option_name] = sanitize_text_field($_POST[$option_name]);
		}
		
		// Store updated options array to database
		update_option('azrcrv-aogt', $options);
		
		// Redirect the page to the configuration form that was processed
		wp_redirect(add_query_arg('page', 'azrcrv-aogt&settings-updated', admin_url('admin.php')));
		exit;
	}
}

/**
 * Check if function active (included due to standard function failing due to order of load).
 *
 * @since 1.0.0
 *
 */
function azrcrv_aogt_is_plugin_active($plugin){
    return in_array($plugin, (array) get_option('active_plugins', array()));
}

/**
 * Insert Open Graph Tags into post and page.
 *
 * @since 1.0.0
 *
 */
function azrcrv_aogt_insert_opengraph_tags() {
	
	// Bring $post object into scope.
	global $post;
	// Set defaults for Twitter shares.
	$url   = get_bloginfo( 'url' );
	$title = get_bloginfo( 'name' );
	$desc  = get_bloginfo( 'description' );
	
	$options = get_option('azrcrv-aogt');
	
	$image_count = 0;
	$imagetouse = '';
	if (!is_singular()){
		$imagetouse = $options['fallback_image'];
		$image_count = 0;
	/*}elseif ($options['use_thumbnail'] == 1 AND has_post_thumbnail()){
		$image_properties = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ) , 'medium_large' );
		$imagetouse = $image_properties[0];*/
	}elseif (azrcrv_aogt_is_plugin_active('azrcrv-floating-featured-image/azrcrv-floating-featured-image.php') AND $options['use_ffi'] == 1){
		$image_count = 1;
	}elseif (azrcrv_aogt_is_plugin_active('azrcrv-floating-featured-image/azrcrv-floating-featured-image.php') AND $options['use_ffi'] == 0 AND strpos($post->post_content, 'featured-image') == true){
		$image_count = 2;
	}elseif ($options['use_ffi'] == 0 AND strpos($post->post_content, 'featured-image') == false){
		$image_count = 1;
	}else{
		$image_count = 1;
	}
	
	if ($image_count > 0){
		$counter = 0;
		if ( preg_match_all( '/<img(.*?)src=("|\'|)(.*?)("|\'| )(.*?)>/s',  do_shortcode($post->post_content), $matches ) ) {
			$_matches = reset( $matches );
			foreach ( $_matches as $image ) {
				$counter += 1;
				if ($counter == $image_count){
					if ( preg_match( '`src=(["\'])(.*?)\1`', $image, $_match ) ) {
						list($width, $height) = getimagesize($_match[2]);
						if ($width >= $options['dimensions']['width'] || $height >= $options['dimensions']['height']){
							$imagetouse = $_match[2];
							break;
						}
					}
				}
			}
		}
	}
	
	if (STRLEN($imagetouse) == 0){
		$imagetouse = $options['fallback_image'];
	}
	
	list($width, $height) = getimagesize($imagetouse); 
	if ($width < 100 || $height < 100){
		$imagetouse = $options['fallback_image'];
	}
	
	// If on a post or page, reset defaults.
	if( is_single() || is_page() ) {
		// Update URL to current post/page.
		$url = get_permalink();
		// Update title only if $post has non-empty title.
		$title = (get_the_title() ) ? get_the_title() : $title;
		// Update description only if $post has non-empty excerpt.
		if (!empty( $post->post_excerpt)){
			$desc = $post->post_excerpt;
		}else{
			$desc = trim(strip_tags(do_shortcode($post->post_content)));
			if (strlen($desc) > 200){
				$desc = substr($desc, 0 , 199).'‎&hellip;';
			}
		}
		
	}
	// Assemble the meta tag markup.
	$markup = '<meta prefix="og: http://ogp.me/ns#" property="og:type" content="website" />'."\n";
	$markup .= '<meta prefix="og: http://ogp.me/ns#" property="og:title" content="'.$title.'" />'."\n";
	$markup .= '<meta prefix="og: http://ogp.me/ns#" property="og:description" content="'.$desc.'" />'."\n";
	$markup .= '<meta prefix="og: http://ogp.me/ns#" property="og:image" content="'.$imagetouse.'" />'."\n";
	$markup .= '<meta prefix="og: http://ogp.me/ns#" property="og:url" content="'.$url.'" />'."\n";
	// Print the tags.
	echo $markup;
}

?>