=== PostViews Insights ===
Contributors: sunny923
Tags: views, analytics, shortcode, postviews, insights
Requires at least: 5.0
Tested up to: 6.6.2
Stable tag: 1.0.7
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Display post view insights and provide a shortcode to list posts based on view count.

== Description ==

A plugin to display post view insights and provide a shortcode to list posts based on view count.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/postviews-insights` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the `[post_view_list post_type="your_post_type" order="ASC" display="5"]` shortcode in your posts or pages.



== Frequently Asked Questions ==

= How do I customize the HTML output? =

You can use the `pvin_custom_html` filter to customize the HTML output. Here’s an example:

`
add_filter('pvin_custom_html', 'custom_pvi_html_output', 10, 2);

function custom_pvi_html_output($output, $atts) {
    $custom_output = '<div class="custom-post-list">' . $output . '</div>';
    return $custom_output;
}
`


== Screenshots ==
1. Screenshot 1 is admin settings page where you can manage you settings
2. Secreenshot 2 displays the Views of posts in admin post table