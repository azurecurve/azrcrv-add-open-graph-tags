<?php
/*
	other plugins tab on settings page
*/

/**
 * Declare the Namespace.
 */
namespace azurecurve\AddOpenGraphTags;

/**
 * Instructions tab.
 */

$tab_instructions_label = esc_html__( 'Instructions', 'azrcrv-aogt' );
$tab_instructions       = '
<table class="form-table azrcrv-settings">

	<tr>
	
		<th scope="row" colspan=2 class="azrcrv-settings-section-heading">
			
				<h2 class="azrcrv-settings-section-heading">' . esc_html__( 'Add Open Graph Tags', 'azrcrv-aogt' ) . '</h2>
			
		</th>

	</tr>

	<tr>
	
		<td scope="row" colspan=2>
		
			<p>' .
				esc_html__( 'Open Graph tags control how URLs are displayed when shared on social media.', 'azrcrv-aogt' ) . '
				' .
				esc_html__( ' They are part of Facebook\'s Open Graph protocol, which are also used by other social media sites, including LinkedIn and Twitter (if Twitter Cards are not present).', 'azrcrv-aogt' ) . '
					
			</p>
		
			<p>' .
				sprintf( esc_html__( 'This %s plugin will add open graph tags to every page using an image from the page content, featured image or a fall back image specified on the Settings tab.', 'azrcrv-aogt' ), '<strong>' . PLUGIN_NAME . '</strong>' ) . '
					
			</p>
		
		</td>
	
	</tr>
	
</table>';
