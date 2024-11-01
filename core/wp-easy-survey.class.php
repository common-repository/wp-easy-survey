<?php
/**
 * File defining class for plugin initialisation
 *
 * @author Eoxia developpement team <dev@eoxia.com>
 * @version 0.1
 * @package wp_easy_survey
 * @subpackage librairies
 */

/** Check if the plugin version is defined. If not defined script will be stopped here */
if ( !defined( 'WP_EASY_SURVEY' ) ) {
	die( __("You are not allowed to use this service.", 'wp_easy_survey') );
}

/**
 * Plugin initialisation class
 *
 *
 * @author Eoxia developpement team <dev@eoxia.com>
 * @version 0.1
 * @package wp_easy_survey
 * @subpackage librairies
 */
class wpes_easy_survey {

	var $template_dir = WPES_TPL_DIR;

	/**
	 * Create an instance for survey
	 */
	function __construct() {

		/**	Load plugin translation	*/
		load_plugin_textdomain( 'wp_easy_survey', false, WP_EASY_SURVEY_DIR . '/languages/');

		/**	Call administration style definition	*/
		add_action( 'admin_enqueue_scripts', array(&$this, 'admin_css') );

		/**	Add a custom class to the admin body	*/
		add_filter( 'admin_body_class', array(&$this, 'admin_body_class') );

		/**	Call administration javascript utilities	*/
		add_action( 'admin_enqueue_scripts', array(&$this, 'admin_js') );
		add_action( 'admin_print_scripts', array(&$this, 'admin_printed_js') );
	}

	/**
	 * Check and get the template file path to use for a given display part
	 *
	 * @uses locate_template()
	 * @uses get_template_part()
	 *
	 * @param string $side The website part were the template will be displayed. Backend or frontend
	 * @param string $slug The slug name for the generic template.
	 * @param string $name The name of the specialised template.
	 *
	 * @return string The template file path to use
	 */
	function get_template_part( $side, $slug, $name=null ) {

		switch ( $side ) {
			case "backend":
			case "common":
				$path = '';

				$templates = array();
				$name = (string)$name;
				if ( '' !== $name )
					$templates[] = "{$side}/{$slug}-{$name}.php";
				$templates[] = "{$side}/{$slug}.php";

				$path = locate_template( $templates, false );

				if ( empty( $path ) ) {
					foreach ( (array) $templates as $template_name ) {
						if ( !$template_name )
							continue;
						if ( file_exists($this->template_dir . $template_name)) {
							$path = $this->template_dir . $template_name;
							break;
						}
					}
				}

				return $path;
				break;

			case "frontend":
				get_template_part( $slug, $name );
				break;
		}

	}

	/**
	 * Load the different javascript librairies
	 */
	function admin_js() {
		add_thickbox();
		wp_enqueue_script( 'wpes_common_js', WPEASYSURVEY_COMMON_JS_URL . 'functions.js', '', WP_EASY_SURVEY, true);
		wp_enqueue_script( 'wpes_backend_js', WPEASYSURVEY_BACKEND_TPL_URL . 'scripts.js', array( 'wp-color-picker' ), WP_EASY_SURVEY, true);
		wp_enqueue_script( 'wpes_nestable_js', WPEASYSURVEY_COMMON_JS_URL . 'jquery.nestable.js', '', WP_EASY_SURVEY, true);
		wp_enqueue_script( 'wpes_chosen_js', WPEASYSURVEY_COMMON_JS_URL . 'jquery.chosen.js', '', WP_EASY_SURVEY, true);
		wp_enqueue_script( 'wpes_charts_js', WPEASYSURVEY_COMMON_JS_URL . 'chart.min.js', '', WP_EASY_SURVEY, true);

		wp_enqueue_script( 'jquery-form' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'jquery-ui-datepicker' );
	}

	/**
	 * Print javascript (dynamic js content) instruction into html code head.
	 */
	function admin_printed_js() {
		/**	Disable autosave on given custom post type	*/
		global $post;
		if ( $post ) {
			$current_post_type = get_post_type($post->ID);
			if ( in_array( $current_post_type, array( 'wpes_survey' ) ) ) {
				wp_dequeue_script('autosave');
			}
		}

		require_once( $this->get_template_part( 'backend', "header.js" ) );
	}

	/**
	 * Add custom classes in admin body
	 *
	 * @param string $classes Current classes for admin body
	 * @return string The new classes to add to admin body
	 */
	function admin_body_class( $classes ) {
		global $post;

		if ( !empty($post->ID) ) {
			$post_type = get_post_type( $post->ID );
			if ( is_admin() && in_array( $post_type, array( 'wpes_survey', 'wpes_issue' ) ) ) {
				$classes .= ' wpes-admin-body wpes-admin-post-type-' . $post_type;
			}
		}

		return $classes;
	}

	/**
	 * Load the different css librairies
	 */
	function admin_css() {
		wp_register_style('wpes_adminstyles_css', WPEASYSURVEY_BACKEND_TPL_URL . 'wpes_admin.css', '', WP_EASY_SURVEY);
		wp_enqueue_style('wpes_adminstyles_css');

		wp_register_style('wpes_common_css', WPEASYSURVEY_COMMON_CSS_URL . 'common.css', '', WP_EASY_SURVEY);
		wp_enqueue_style('wpes_common_css');

		wp_register_style('wpes_backend_css', WPEASYSURVEY_BACKEND_TPL_URL . 'style.css', '', WP_EASY_SURVEY);
		wp_enqueue_style('wpes_backend_css');

		wp_register_style('wpes_nestable_css', WPEASYSURVEY_COMMON_CSS_URL . 'nestable.css', '', WP_EASY_SURVEY);
		wp_enqueue_style( 'wpes_nestable_css' );

		wp_register_style('wpes_chosen_css', WPEASYSURVEY_COMMON_CSS_URL . 'chosen.css', '', WP_EASY_SURVEY);
		wp_enqueue_style( 'wpes_chosen_css' );

		wp_enqueue_style( 'wp-color-picker' );
	}

}
