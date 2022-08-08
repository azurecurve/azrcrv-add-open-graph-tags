<?php
/*
	other plugins tab on settings page
*/

/**
 * Declare the Namespace.
 */
namespace azurecurve\AddOpenGraphTags;

/**
 * Settings tab.
 */

$tab_settings_label = PLUGIN_NAME . ' ' . esc_html__( 'Settings', 'azrcrv-aogt' );
$tab_settings       = '
<table class="form-table azrcrv-settings">
		
	<tr>
	
		<th scope="row" colspan="2">
		
			<label for="explanation">
				' . sprintf( esc_html__( '%s adds header tags which allow you to control how your URLs are displayed when shared on social media (such as Facebook and LinkedIn).', 'azrcrv-aogt' ), PLUGIN_NAME ) . '
			</label>
			
		</th>
		
	</tr>

	<tr>
	
		<th scope="row">
		
			' . esc_html__( 'Use featured image?', 'azrcrv-aogt' ) . '
			
		</th>
		
		<td>
			
			<legend class="screen-reader-text">
					' . esc_html__( 'Use featured image?', 'azrcrv-aogt' ) . '
			</legend>

			<input name="use_featured_image" type="checkbox" id="use_featured_image" value="1" ' . checked( '1', $options['use_featured_image'], false ) . ' />
			<label for="use_featured_image">
				<span class="description">
					' . esc_html__( 'Use featured image set on the post.', 'azrcrv-aogt' ) . '
				</span>
			</label>
			
		</td>
		
	</tr>

	<tr>
	
		<th scope="row">
		
			' . esc_html__( 'Use floating featured image?', 'azrcrv-aogt' ) . '
			
		</th>
		
		<td>
			
			<legend class="screen-reader-text">
					' . esc_html__( 'Use floating featured image?', 'azrcrv-aogt' ) . '
			</legend>';

if ( check_is_plugin_active( 'azrcrv-floating-featured-image/azrcrv-floating-featured-image.php' ) ) {

	$tab_settings .= '
					<input name="use_ffi" type="checkbox" id="use_ffi" value="1" ' . checked( '1', $options['use_ffi'], false ) . ' />
			<label for="use_ffi">
				<span class="description">
					' . esc_html__( 'Use floating featured image if available in the post.', 'azrcrv-aogt' ) . '
				</span>
			</label>';

} else {

	$tab_settings .= sprintf( esc_html__( '%1$s from %2$s is not installed', 'azrcrv-aogt' ), '<a href="' . DEVELOPER_URL_RAW . '/floating-featured-image/">Floating Featured Image</a>', DEVELOPER_URL );

}

		$tab_settings .= '</td>
		
	</tr>

	<tr>
		<th scope="row">
		
			' . esc_html__( 'Minimum Dimensions', 'azrcrv-aogt' ) . '
			
		</th>
		
		<td>
		
			<input type="number" name="dimensions-width" value="' . esc_html( $options['dimensions']['width'] ) . '" class="small-text" />&nbsp;x&nbsp;<input type="number" name="dimensions-height" value="' . esc_html( $options['dimensions']['height'] ) . '" class="small-text" />
			
			<p class="description">
				' . esc_html__( 'Specify minimum dimensions (width and height).', 'azrcrv-aogt' ) . '
			</p>
		
		</td>
	
	</tr>

	<tr>
		<th scope="row">
		
			' . esc_html__( 'Fallback Image', 'azrcrv-aogt' ) . '
			
		</th>
		
		<td>

			<p>
			
				<img src="' . esc_url_raw( $options['fallback_image'] ) . '" id="' . PLUGIN_HYPHEN . '-fallback-image" style="width: 300px;"><br />
				
				<!-- Outputs the text field and displays the URL of the image retrieved by the media uploader -->
				<input type="hidden" name="fallback_image" id="fallback_image" value="' . esc_url_raw( $options['fallback_image'] ) . '" class="regular-text" />
				
				<input type="button" id="' . PLUGIN_HYPHEN . '-upload-image" class="button upload" value="' . esc_html__( 'Upload image', 'azrcrv-aogt' ) . '" />
				<input type="button" id="' . PLUGIN_HYPHEN . '-remove-image" class="button remove" value="' . esc_html__( 'Remove image', 'azrcrv-aogt' ) . '" />
				
			</p>
			
			<p class="description">
				' . esc_html__( 'Upload, choose or remove your fallback image; recommended open graph image is 1200px by 627px.', 'azrcrv-aogt' ) . '
			</p>
	
		</td>
		
	</tr>

</table>';
