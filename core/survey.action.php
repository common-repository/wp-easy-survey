<?php
/**
 * File containing component definition for surveys management
 *
 * @author Eoxia developpement team <dev@eoxia.com>
 * @version 0.1
 * @package wp_easy_survey
 */

/** Check if the plugin version is defined. If not defined script will be stopped here	*/
if ( !defined( 'WP_EASY_SURVEY' ) ) {
	die( __("You are not allowed to use this service.", 'wp_easy_survey') );
}

/**
 * Define the component to manage surveys
 *
 * @author Eoxia developpement team <dev@eoxia.com>
 * @version 0.1
 * @package wp_easy_survey
 * @subpackage core
 */
class wpes_survey_ajax extends wpes_display {

	function __construct(){
		add_action( 'wp_ajax_display_full_survey', array( $this, 'ajax_display_survey' ) );
		add_action( 'wp_ajax_wpes_survey_list', array( $this, 'wpes_survey_list' ) );
	}

	function ajax_display_survey() {
		check_ajax_referer( 'wpes-survey-selection' );

		new wpes_easy_survey();
		$wpes_survey = new wpes_survey();
		$wpes_issue = new wpes_issue();
		$wpes_survey->issues = $wpes_issue;
		$wpes_answers = new wpes_answer();
		$wpes_issue->answers = $wpes_answers;
		$wpes_survey->answers = $wpes_answers;

		$final_survey = $wpes_survey->final_survey_display( $_POST[ 'post_ID' ], $_POST[ 'survey_id' ] );

		wp_send_json_success( array( 'output' => $final_survey[ 'content' ], ) );
	}

	function wpes_survey_list() {
		check_ajax_referer( 'wpes-survey-list' );
		$wpes_survey = new wpes_survey();

		$current_post = get_post( $_POST[ 'post_ID' ] );

		ob_start();
		$wpes_survey->display_all_surveys_metabox( $current_post );
		$output = ob_get_clean();

		wp_send_json_success( array( 'output' => $output, ) );
	}

}

new wpes_survey_ajax();
