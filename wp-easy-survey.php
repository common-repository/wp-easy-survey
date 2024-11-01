<?php
/*
Plugin Name: Wp Easy Survey
Description: Create question and group question into survey. Each survey would be affectable to a post type in wordpress. / Gerez vos formulaires associable aux post type de wordpress
Version: 2.2
Author: Eoxia
*/

/**
 * File defining class for custom post type management
 *
 * @author Eoxia developpement team <dev@eoxia.com>
 * @version 2.0
 */

/**
 * Define the current version for the plugin. Interresting for clear cache for plugin style and script
 * @var string Plugin current version number
 */
DEFINE('WP_EASY_SURVEY', '2.2');

/**
 * Get the plugin main dirname. Allows to avoid writing path directly into code
 * @var string Dirname of the plugin
 */
DEFINE('WP_EASY_SURVEY_DIR', basename(dirname(__FILE__)));

/** Include core config file	*/
require_once(WP_PLUGIN_DIR . '/' . WP_EASY_SURVEY_DIR . '/core/config.php' );

/**	Allows php notice/fatal errors debugging	*/
if ( WPESURVEY_DEBUG_MODE && in_array(long2ip(ip2long($_SERVER['REMOTE_ADDR'])), unserialize(WPESURVEY_DEBUG_ALLOWED_IP)) ) {
	ini_set( 'display_errors', true );
	error_reporting( E_ALL );
}

/** Include all librairies on plugin load */
require_once( WPEASYSURVEY_CORELIB_DIR . 'file_include.php' );

/** Plugin initialisation */
new wpes_easy_survey();
$wpes_survey = new wpes_survey();
$wpes_issue = new wpes_issue();
$wpes_survey->issues = $wpes_issue;
$wpes_answers = new wpes_answer();
$wpes_issue->answers = $wpes_answers;
$wpes_survey->answers = $wpes_answers;


/**	On plugin activation create the default parameters */
register_activation_hook( __FILE__ , array(&$wpes_answers, 'create_default_answers') );

?>
