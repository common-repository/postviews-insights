<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    PostViews_Insights
 * @subpackage PostViews_Insights/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    PostViews_Insights
 * @subpackage PostViews_Insights/public
 * @author     Sunny Thakur <tsunny923@gmail.com>
 */
class PostViews_Insights_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in PostViews_Insights_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The PostViews_Insights_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/post-views-insights-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in PostViews_Insights_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The PostViews_Insights_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/post-views-insights-public.js', array( 'jquery' ), $this->version, false );

	}
	/**
	 * Increment the post view count.
	 *
	 * @param int $post_id The ID of the post being viewed.
	 */
	public function pvi_set_post_views($post_id) {
	    $count_key = 'post_views_count';
	    $count = get_post_meta($post_id, $count_key, true) ? get_post_meta($post_id, $count_key, true) : 0;
	    
        $count++;
        update_post_meta($post_id, $count_key, $count);
	}

	/**
	 * Hook into 'wp_head' to increment the post view count.
	 */
	public function pvi_track_post_views(){
		$excludeRoles = get_option('pvin-exclude-roles') ? get_option('pvin-exclude-roles') : [];
		if(is_user_logged_in()){
			$user_id = get_current_user_id();
			$user = new WP_User( $user_id );

			if ( !empty( $user->roles ) && is_array( $user->roles ) ) {

			    $result = array_intersect($user->roles, $excludeRoles); 
			}
		    //check if current user roles excluded then do not count view
			if(!empty($result)){
				return;
			}
		}
		$allowed_post_types = get_option('pvin-allowed-post-types') ? get_option('pvin-allowed-post-types') : [];
		if(!empty($allowed_post_types)){
			if(is_singular($allowed_post_types)){
				$post_id = get_the_ID();
	        	$this->pvi_set_post_views($post_id);
			}
		}

	}

	public function pvi_post_view_list_shortcode($atts) {
	    // Extract shortcode attributes
	    $atts = shortcode_atts(array(
	        'post_type' => 'post',
	        'order' => 'DESC',
	        'display' => 5,
	    ), $atts, 'post_view_list');

	    // Query arguments
	    $args = array(
	        'post_type' => $atts['post_type'],
	        'meta_key' => 'post_views_count',
	        'orderby' => 'meta_value_num',
	        'posts_per_page' => $atts['display'],
	        'order' => $atts['order']
	    );

	    // WP Query
	    $query = new WP_Query($args);

	    // Output
	    $output = '<ul class="pvi-posts-list">';
	    if ($query->have_posts()) {
	        while ($query->have_posts()) {
	            $query->the_post();
	            $output .= '<li>
	            	
	            	<a href="' .esc_url(get_permalink()) . '">'.get_the_post_thumbnail(get_the_ID(), "thumbnail").'<h5>'. esc_attr(get_the_title()) . '</h5></a></li>';
	        }
	    } else {
	        $output .= '<li>No posts found</li>';
	    }
	    $output .= '</ul>';

      	// Apply filter for custom HTML
    	$output = apply_filters('pvin_custom_html', $output, $atts);

	    // Restore original Post Data
	    wp_reset_postdata();

	    return $output;
	}
}
