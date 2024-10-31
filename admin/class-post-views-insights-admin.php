<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    PostViews_Insights
 * @subpackage PostViews_Insights/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    PostViews_Insights
 * @subpackage PostViews_Insights/admin
 * @author     Sunny Thakur <tsunny923@gmail.com>
 */
class PostViews_Insights_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles($hook) {

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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/post-views-insights-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'select2-min', plugin_dir_url( __FILE__ ) . 'css/select2.min.css', array(), $this->version, 'all' );
		if ($hook != 'toplevel_page_postviews-insights') {
	        return;
	    }
		wp_enqueue_style( 'prism', plugin_dir_url( __FILE__ ) . 'css/prism.min.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts($hook) {

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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/post-views-insights-admin.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'select2-min', plugin_dir_url( __FILE__ ) . 'js/select2.min.js', array( 'jquery' ), $this->version, false );
	  	if ($hook != 'toplevel_page_postviews-insights') {
	        return;
	    }
		wp_enqueue_script( 'prism', plugin_dir_url( __FILE__ ) . 'js/prism.min.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Register a admin settings page.
	 */
	public function postviews_insights_settings_menu_page() {
		add_menu_page(
			__( 'PostViews Insights', 'postviews-insights' ),
			'PostViews Insights',
			'manage_options',
			'postviews-insights',
			[$this, 'postviews_insights_settings_ui_callback'],
			esc_url( plugins_url( 'images/icon-post-views.png', __FILE__ )),
			10
		);
	}

	public function postviews_insights_settings_ui_callback(){
		try{
			// Save the OpenAI Secret Key when the form is submitted
		    if (isset($_POST['pvin-submit'])) {
		   		
				$pvi_nonce_security = isset($_POST['pvin_nonce_security']) ? sanitize_text_field(wp_unslash($_POST['pvin_nonce_security'])) : "";
				
				// Verify the nonce
				if (!wp_verify_nonce($pvi_nonce_security, 'pvin_nonce_action')) {

	                // Nonce verification failed
	                wp_die(esc_attr(__('Nonce verification failed', 'postviews-insights')));
	            }
				
				$allowed_post_types = isset($_POST['pvin-allowed-post-types']) ? array_map('sanitize_text_field',  wp_unslash($_POST['pvin-allowed-post-types'])) : [];
				$pvi_exclude_roles = isset($_POST['pvin-exclude-roles']) ? array_map('sanitize_text_field', wp_unslash($_POST['pvin-exclude-roles'])) : [];

		    	if(isset($_POST['pvin-enable-post-views'])){
		    		update_option('pvin-enable-post-views', 1);
		    	}else{
		    		update_option('pvin-enable-post-views', 0);
		    	}	
			    if (isset($allowed_post_types)) {

		    		update_option('pvin-allowed-post-types', $allowed_post_types);
			    }
		   		if(isset($_POST['pvin-enable-views-column'])){
		    		update_option('pvin-enable-views-column', 1);
		    	}else{
		    		update_option('pvin-enable-views-column', 0);
		    	}	
			    if (isset($pvi_exclude_roles)) {

		    		update_option('pvin-exclude-roles', $pvi_exclude_roles);
			    }
		    }
	    	
		    require_once(plugin_dir_path(__FILE__).'partials/post-views-insights-admin-display.php');

		} catch (Exception $e) {
	        // Handle any exceptions that might occur during the execution
	        echo esc_attr($e->getMessage());
	    }

		
	}
	/**
	 * Add a 'Views' column to the posts list table.
	 *
	 * @param array $columns The existing columns.
	 * @return array The modified columns.
	 */
	public function postviews_insights_add_views_column( $columns ){
		$columns['piv_views'] = __('Views', 'postviews-insights');
    	return $columns;
	}

	/**
	 * Output the content for the 'Views' column.
	 *
	 * @param string $column_name The name of the column to output.
	 * @param int    $post_id     The ID of the current post.
	 */

	public function postviews_insights_views_column_callback( $column_name, $post_id ){
		if ($column_name == 'piv_views') {
			$piv_views = get_post_meta($post_id, 'post_views_count', true);
			echo $piv_views ? esc_attr($piv_views) : 0;
		}
	}

	/**
	 * Make 'Views' column sortable.
	 *
	 * @param array $columns The existing sortable columns.
	 * @return array The modified sortable columns.
	 */
	public function postviews_insights_sortable_views_column($columns) {
	    $columns['piv_views'] = 'piv_views';
	    return $columns;
	}

	/**
	 * Handle sorting by 'Views' column.
	 *
	 * @param WP_Query $query The WP_Query instance (passed by reference).
	 */
	public function postviews_insights_sort_views_column($query) {
	    if (!is_admin()) {
	        return;
	    }

	    $orderby = $query->get('orderby');
	    if ('piv_views' == $orderby) {
	        $query->set('meta_key', 'post_views_count');
	        $query->set('orderby', 'meta_value_num');
	    }
	}



}
