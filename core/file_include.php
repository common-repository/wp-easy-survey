<?php
/**
 * Main file for including librairies once in plugin.
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

/**	Include display component	*/
require_once(WPEASYSURVEY_CORELIB_DIR . 'display.class.php' );

/**	Include general initialisation class	*/
require_once(WPEASYSURVEY_CORELIB_DIR . 'wp-easy-survey.class.php' );

/**	Include general survey class	*/
require_once(WPEASYSURVEY_MODULELIB_DIR . 'survey.class.php' );
require_once(WPEASYSURVEY_CORELIB_DIR . 'survey.action.php' );

/**	Include general issue management class	*/
require_once(WPEASYSURVEY_MODULELIB_DIR . 'issue.class.php' );

/**	Include general answer management class	*/
require_once(WPEASYSURVEY_MODULELIB_DIR . 'answer.class.php' );
