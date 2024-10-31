<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @since      1.0.0
 *
 * @package    PostViews_Insights
 * @subpackage PostViews_Insights/admin/partials
 */

// Get all public and queryable post types
$post_types = get_post_types( array(
    'public'   => true, // Include only public post types
), 'objects' );
$wp_roles = wp_roles()->roles;

$enable_views = get_option('pvin-enable-post-views');
$allowed_types = get_option('pvin-allowed-post-types') ? get_option('pvin-allowed-post-types') : [];
$enable_views_column = get_option('pvin-enable-views-column');
$excludeRoles = get_option('pvin-exclude-roles') ? get_option('pvin-exclude-roles') : [];

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="pvi-container">
	<div class="pvi-row">
		<h1>Post Views Insights</h1>
		<div class="pvi-form-box">	
			<div class="pvi-form-field"> 
				<form method="post" class="piv-settings-form">
					<?php wp_nonce_field('pvin_nonce_action', 'pvin_nonce_security'); ?>
					<div class="pvi-settings-container">
						<table class="piv-settings-table table-left">
							<tbody>
								<tr>
									<th scope="row">
										<label><?php echo esc_attr(__('Enable Post Views', 'postviews-insights')); ?></label>
									</th>
									<td><input type="checkbox" name="pvin-enable-post-views" value="1" <?php echo $enable_views ? "checked" : ""; ?> /></td>
								</tr>
								<tr>
									<th scope="row">
										<label><?php echo esc_attr(__('Post Types', 'postviews-insights')); ?> 
											<div class="piv-tooltip"><img src="<?php echo esc_url(plugin_dir_url(__DIR__).'images/icon-information.png'); ?>">
											  <span class="tooltiptext">These are the types of posts for which you want to enable Views Tracking.</span>
											</div>
										</label>
									</th>
									<td>
										<?php  foreach ($post_types as $key => $post_type) { 
	                                		if($key == 'attachment') continue; //skip attachments post type
	                                		?>
	                                      
	                                          <input type="checkbox" id="cpt_<?php echo esc_attr($key); ?>" name="pvin-allowed-post-types[]" class="sc-gJwTLC ikxBAC" value="<?php echo esc_attr($key); ?>" <?php if(isset($allowed_types) && in_array($key, $allowed_types)){ echo "checked"; } ?> /> 
	                                          <label for="cpt_<?php echo esc_attr($key); ?>" ><?php echo esc_attr($post_type->label); ?></label><br>
	                                	<?php } ?>
									</td>
								</tr>
								<tr>
									<th scope="row">
										<label><?php echo esc_attr(__('Enable Views Column', 'postviews-insights')); ?> 
											<div class="piv-tooltip"><img src="<?php echo esc_url(plugin_dir_url(__DIR__).'images/icon-information.png'); ?>">
										  		<span class="tooltiptext">This setting will enable Views Column in post list table.</span>
											</div>
										</label>
									</th>
									<td><input type="checkbox" name="pvin-enable-views-column" value="1" <?php echo $enable_views_column ? "checked": ""; ?>></td>
								</tr>
								<tr>
									<th scope="row">
										<label><?php echo esc_attr(__('Exclude specific user roles', 'postviews-insights')); ?> 
										</label>
									</th>
									<td style="width: 300px">
										<select multiple="multiple" name="pvin-exclude-roles[]" class="piv-select">
											<?php foreach ($wp_roles as $key => $role) { ?>
												<option value="<?php echo esc_attr($key); ?>" <?php echo in_array($key, $excludeRoles) ? 'selected="selected"' : ""; ?>><?php echo esc_attr($role['name']); ?></option>
											<?php } ?>
										</select>
									</td>
								</tr>
								<tr>
									<td colspan="2" style="padding-left: 0;">
										<button type="submit" class="button button-primary pvi-submit-buttton" name="pvin-submit"><?php echo esc_attr(__('Save Changes', 'postviews-insights')); ?></button>	
									</td>
								</tr>
							</tbody>
						</table>
						<div class="pvi-technichal-guide">
					        <h1>Technichal/Developer Guide</h1>
					        <h2>Using meta post_views_count</h2>
					        <p>You can use the meta key <code>post_views_count</code> to get the number of views for a post. Here's an example:</p>
					        <pre class="language-php">
				        	<code class="language-php">
&lt;?php 
	$post_id = get_the_ID(); // or any post ID
	$views = get_post_meta($post_id, 'post_views_count', true);
	echo 'This post has been viewed ' . $views . ' times.';
?&gt;
					        </code>
					    	</pre>
					        
					        <h2>Shortcode to List Posts</h2>
					        <p>You can use the following shortcode to list posts with a specific post type, ordered by the post_views_count meta key:</p>
					        <pre class="language-php"><code class="language-php">
[post_view_list post_type="your_post_type" order="ASC" display="5"]
					        </code></pre>
				          	<h2>Customizing the Shortcode HTML Output</h2>
    						<p>You can customize the HTML output of the shortcode by using the <code>pvin_custom_html</code> filter. Here’s an example of how you can do this in your theme's <code>functions.php</code> file:</p>
        <pre class="language-php"><code class="language-php">
add_filter('pvin_custom_html', 'custom_pvi_html_output', 10, 2);

function custom_pvi_html_output($output, $atts) {
    // Custom HTML structure
    $custom_output = '&lt;div class="custom-post-list"&gt;' . $output . '&lt;/div&gt;';
    return $custom_output;
}
        </code></pre>
					    </div>
					</div>
					
				</form>
			</div>
		</div>
	</div>
</div>
