<?php
/**
 * Main plugin configuration file
 *
 * @author Eoxia developpement team <dev@eoxia.com>
 * @version 0.1
 * @package wp_easy_survey
 * @subpackage librairies
 */

/** Check if the plugin version is defined. If not defined script will be stopped here	*/
if ( !defined( 'WP_EASY_SURVEY' ) ) {
	die( __("You are not allowed to use this service.", 'wp_easy_survey') );
}


/** Define librairies directory */
DEFINE( 'WPEASYSURVEY_LIBS_DIR', WP_PLUGIN_DIR . '/' . WP_EASY_SURVEY_DIR . '/');
DEFINE( 'WPEASYSURVEY_CORELIB_DIR', WPEASYSURVEY_LIBS_DIR . 'core/');
DEFINE( 'WPEASYSURVEY_MODULELIB_DIR', WPEASYSURVEY_LIBS_DIR . 'modules/');

/** Define languages directory */
DEFINE( 'WPEASYSURVEY_LANG_DIR', WP_EASY_SURVEY_DIR . '/languages/');

/** Define template directory */
DEFINE( 'WPES_TPL_DIR', WP_PLUGIN_DIR . '/' . WP_EASY_SURVEY_DIR . '/templates/');
DEFINE( 'WPES_TPL_URL', WP_PLUGIN_URL . '/' . WP_EASY_SURVEY_DIR . '/templates/');

/** Define template element directory for backend */
DEFINE( 'WPEASYSURVEY_BACKEND_TPL_DIR', WPES_TPL_DIR . 'backend/');
DEFINE( 'WPEASYSURVEY_BACKEND_TPL_URL', WPES_TPL_URL . 'backend/');
DEFINE( 'WPEASYSURVEY_BACKEND_CSS_URL', WPEASYSURVEY_BACKEND_TPL_URL . 'css/');
DEFINE( 'WPEASYSURVEY_BACKEND_JS_URL', WPEASYSURVEY_BACKEND_TPL_URL . 'js/');

/* * Define template element directory for frontend */
DEFINE( 'WPEASYSURVEY_FRONTEND_TPL_DIR', WPES_TPL_DIR . 'frontend/');
DEFINE( 'WPEASYSURVEY_FRONTEND_TPL_URL', WPES_TPL_URL . 'frontend/');
DEFINE( 'WPEASYSURVEY_FRONTEND_CSS_URL', WPEASYSURVEY_BACKEND_TPL_DIR . 'css/');
DEFINE( 'WPEASYSURVEY_FRONTEND_JS_URL', WPEASYSURVEY_BACKEND_TPL_URL . 'js/');

/** Define common template element directory	*/
DEFINE( 'WPEASYSURVEY_COMMON_TPL_DIR', WPES_TPL_DIR . 'common/');
DEFINE( 'WPEASYSURVEY_COMMON_TPL_URL', WPES_TPL_URL . 'common/');
DEFINE( 'WPEASYSURVEY_COMMON_CSS_URL', WPEASYSURVEY_COMMON_TPL_URL . 'css/');
DEFINE( 'WPEASYSURVEY_COMMON_JS_URL', WPEASYSURVEY_COMMON_TPL_URL . 'js/');
DEFINE( 'WPEASYSURVEY_COMMON_MEDIAS_URL', WPEASYSURVEY_COMMON_TPL_URL . 'medias/');
DEFINE( 'WPEASYSURVEY_COMMON_MEDIAS_DIR', WPEASYSURVEY_COMMON_TPL_DIR . 'medias/');


/**	Define debug vars	*/
/**	Default vars	*/
$default_options_definition = array();
$default_options_definition['wpesurvey_debug_mode'] = true;
$default_options_definition['wpesurvey_debug_allowed_ip'] = array( '127.0.0.1', );
/**	Check if there are options corresponding to this debug vars	*/
$wpee_extra_options = get_option('wpesurvey_extra_options', $default_options_definition);
/**	Define all var as global for use in all plugin	*/
foreach ( $wpee_extra_options as $options_key => $options_value ) {
	if ( is_array($options_value) ) {
		$options_value= serialize($options_value);
	}
	DEFINE( strtoupper($options_key), $options_value );
}

add_image_size( 'wpes-issues-picture-xsmall', 16, 16, true );
add_image_size( 'wpes-issues-picture-small', 36, 34, true );

/**	Define the different answers' type for chart display	*/
$stats_answers_type = array(
		'not_answered' => array(
			'name' => __('Not answered', 'wp_easy_survey'),
			'color' => '990000',
		),
		'in_progress' => array(
			'name' => __('In progress', 'wp_easy_survey'),
			'color' => 'C0C0C0',
		),
		'answered' => array(
			'name' => __('Answered', 'wp_easy_survey'),
			'color' => '009900',
		),
);
DEFINE( "WPES_ANSWER_TYPE", serialize( $stats_answers_type ) );

DEFINE( "WPES_LIST_ALL", true );