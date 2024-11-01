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
class wpes_survey extends wpes_display {

	/**	Define the custom post type for surveys	*/
	public $post_type = 'wpes_survey';
	public $post_type_final = 'wpes_final_survey';

	/**	Define the custom post type prefix for surveys	*/
	public $post_type_prefix = 'S';

	/**	Add a variable with a instance of issue, allowing to manage display of issue into survey	*/
	public $issues;

	/**	Define a variable for answer management	*/
	public $answers;

	/**	Define the total number of real issue with answer to give	*/
	private $total_issue_number = 0;

	/**	Define the total number of real issue with answer to give	*/
	private $issues_with_answers;

	/**	Define the way where the export file will be saved	*/
	public $export_directory;
	public $export_directory_url;
	public $export_file_name = '%1$s_C%2$s-%3$s_S%4$s_E%5$s_V%6$s';

	/**
	 * Create an instance for survey
	 */
	function __construct() {
		/**	Initialise display component	*/
		parent::__construct();

		/**	Get current wordpress uploads directory	*/
		$wp_upload_dir = wp_upload_dir();
		$this->export_directory = $wp_upload_dir[ 'basedir' ] . '/wpes-export/';
		$this->export_directory_url = $wp_upload_dir[ 'baseurl' ] . '/wpes-export/';
		wp_mkdir_p( $this->export_directory );

		/**	Call the main definition for custom post type	*/
		$this->definition();

		/**	Call the different metaboxes allowing to configure each custom post type 	*/
		add_action( 'add_meta_boxes', array(&$this, 'meta_boxes_caller') );

		/**	Make specific saving when saving cutom post type element	*/
		add_action( 'save_post', array(&$this, 'save_post') );

		/**	Add different support for ajax	*/
		add_action('wp_ajax_wpes-ajax-survey-content-output', array( &$this, 'ajax_display_survey_content') );
		add_action('wp_ajax_wpes-ajax-survey-content-order', array( &$this, 'ajax_survey_order_save') );
		add_action('wp_ajax_wpes-ajax-edit-answer', array( &$this, 'ajax_load_answer_for_edition') );
		add_action('wp_ajax_wpes-start-new-evaluation', array( &$this, 'ajax_start_new_evaluation') );
		add_action('wp_ajax_wpes-close-evaluation', array( &$this, 'ajax_close_evaluation') );
		add_action('wp_ajax_wpes-ajax-final-survey-evaluation-result-view', array( &$this, 'ajax_evaluation_answers_view') );
		add_action('wp_ajax_wpes-ajax-final-survey-evaluation-result-export', array( &$this, 'ajax_evaluation_export_file') );
	}

	/**
	 * Define and create the custom post type for surveys management
	 *
	 * @see register_post_type()
	 */
	function definition() {
		$labels = array(
			'name'                	=> __( 'Surveys', 'wp_easy_survey' ),
			'singular_name'       	=> __( 'Survey', 'wp_easy_survey' ),
			'menu_name'           	=> __( 'Survey', 'wp_easy_survey' ),
			'parent_item_colon'   	=> __( 'Parent Survey:', 'wp_easy_survey' ),
			'all_items'           	=> __( 'Surveys', 'wp_easy_survey' ),
			'view_item'           	=> __( 'View Survey', 'wp_easy_survey' ),
			'add_new_item'        	=> __( 'Add New Survey', 'wp_easy_survey' ),
			'add_new'             	=> __( 'New Survey', 'wp_easy_survey' ),
			'edit_item'           	=> __( 'Edit Survey', 'wp_easy_survey' ),
			'update_item'         	=> __( 'Update Survey', 'wp_easy_survey' ),
			'search_items'        	=> __( 'Search Surveys', 'wp_easy_survey' ),
			'not_found'           	=> __( 'No surveys found', 'wp_easy_survey' ),
			'not_found_in_trash'  	=> __( 'No surveys found in Trash', 'wp_easy_survey' ),
		);
		$args = array(
			'label'               	=> __( 'Surveys', 'wp_easy_survey' ),
			'description'         	=> __( 'Surveys management', 'wp_easy_survey' ),
			'labels'              	=> $labels,
			'supports'            	=> array( 'title' ),
			'hierarchical'        	=> false,
			'public'              	=> false,
			'show_ui'             	=> true,
			'show_in_menu'        	=> true,
			'show_in_nav_menus'   	=> false,
			'show_in_admin_bar'   	=> false,
			'can_export'          	=> true,
			'has_archive'         	=> true,
			'exclude_from_search' 	=> true,
			'publicly_queryable'  	=> false,
			'query_var'           	=> 'survey',
			'capability_type'     	=> 'post',
			'rewrite'     		  	=> false,
			'menu_icon' 			=> 'dashicons-forms',
		);
		register_post_type( $this->post_type, $args );

		$labels = array(
			'name'                	=> _x( 'Final Surveys', 'Post Type General Name', 'wp_easy_survey' ),
			'singular_name'       	=> _x( 'Final Survey', 'Post Type Singular Name', 'wp_easy_survey' ),
			'menu_name'           	=> __( 'Final Survey', 'wp_easy_survey' ),
			'parent_item_colon'   	=> __( 'Parent Survey:', 'wp_easy_survey' ),
			'all_items'           	=> __( 'Surveys', 'wp_easy_survey' ),
			'view_item'           	=> __( 'View Final Survey', 'wp_easy_survey' ),
			'add_new_item'        	=> __( 'Add New Final Survey', 'wp_easy_survey' ),
			'add_new'             	=> __( 'New Final Survey', 'wp_easy_survey' ),
			'edit_item'           	=> __( 'Edit Final Survey', 'wp_easy_survey' ),
			'update_item'         	=> __( 'Update Final Survey', 'wp_easy_survey' ),
			'search_items'        	=> __( 'Search Final Surveys', 'wp_easy_survey' ),
			'not_found'           	=> __( 'No Final surveys found', 'wp_easy_survey' ),
			'not_found_in_trash'  	=> __( 'No Final surveys found in Trash', 'wp_easy_survey' ),
		);
		$args = array(
			'label'               	=> __( 'Final Surveys', 'wp_easy_survey' ),
			'description'         	=> __( 'Final Surveys management', 'wp_easy_survey' ),
			'labels'              	=> $labels,
			'supports'            	=> array( 'title' ),
			'hierarchical'        	=> false,
			'public'              	=> false,
			'show_ui'             	=> false,
			'show_in_menu'        	=> false,
			'show_in_nav_menus'   	=> false,
			'show_in_admin_bar'   	=> false,
			'can_export'          	=> true,
			'has_archive'         	=> true,
			'exclude_from_search' 	=> true,
			'publicly_queryable'  	=> false,
			'query_var'           	=> 'final_survey',
			'capability_type'     	=> 'post',
			'rewrite'     		  	=> false,
		);
		register_post_type( $this->post_type_final, $args );
	}

	/**
	 * Post save hook function. When saving a post, this function is called for saving custom informations about current edited post
	 *
	 * @param integer $post_ID
	 */
	function save_post( $post_ID ) {
		if ( !empty($_POST) && !empty($_POST['post_type']) && ($_POST['post_type'] == $this->post_type) ) {
			/**	Save current survey association to existing post types	*/
			if ( !empty( $_POST['wpes-survey-association'] ) ) {
				update_post_meta( $post_ID, '_wpes_survey_association', $_POST['wpes-survey-association'] );
			}
			else {
				delete_post_meta( $post_ID, '_wpes_survey_association' );
			}

			/**	Save current survey issues' order	*/
			if ( !empty( $_POST['wpes-survey-order'] ) ) {
				update_post_meta( $post_ID, '_wpes_survey_order', $_POST['wpes-survey-order'] );
			}
			else {
				delete_post_meta( $post_ID, '_wpes_survey_order' );
			}
		}
	}

	/**
	 * Call the different metaboxes for surveys admin edition page
	 */
	function meta_boxes_caller() {
		/**	Define metabox allowing to manage survey component	*/
		add_meta_box( $this->post_type . '-issues', __('Survey details', 'wp_easy_survey'), array(&$this, 'survey_content_management'), $this->post_type, 'normal' );

		/**	Define metabox allowing to associate survey to existing custom post type */
		add_meta_box( $this->post_type . '-survey-association', __('Survey association', 'wp_easy_survey'), array(&$this, 'survey_association'), $this->post_type, 'side' );

		/**	When editing a post, check if this post have one or many survey associated in order to display corresponding boxes	*/
		global $post;
		if ( !empty($post) && ( $post->post_status != 'auto-draft' ) ) {
			add_meta_box( $post->post_type . '-survey-association', __( 'Survey association', 'wp_easy_survey'), array( $this, 'display_all_surveys_metabox' ), $post->post_type, 'advanced', 'default' );

			// global $wpdb;
			// $query = $wpdb->prepare( "SELECT PM.post_id FROM {$wpdb->postmeta} AS PM INNER JOIN {$wpdb->posts} AS P ON (P.ID = PM.post_id) WHERE P.post_status = 'publish' AND PM.meta_key = %s AND PM.meta_value LIKE ('%%%s%%') AND P.post_type = %s", '_wpes_survey_association', $post->post_type, $this->post_type);
			// $associated_surveys_list = $wpdb->get_results( $query );
			//
			// if ( !empty($associated_surveys_list) ) {
			// 	foreach ( $associated_surveys_list as $survey ) {
			// 		/**	Define metabox allowing to associate survey to existing custom post type */
			// 		add_meta_box( $post->post_type . '-survey-association-' . $survey->post_id, sprintf( _x('%s', 'Title for metabox displaying survey in associated post type', 'wp_easy_survey'), get_the_title( $survey->post_id ) ), array(&$this, 'survey_metabox_display'), $post->post_type, 'advanced', 'default', array( 'parent_element_id' => $survey->post_id, 'parent_element_type' => 'survey' ) );
			// 	}
			// }
		}
	}

	/**
	 * Define the box allowgin to affecte questions and questions' group to a survey
	 *
	 * @param object $post The current survey (post) complete definition
	 */
	function survey_content_management( $post ) {
		$wpes_survey_node_addition_nonce = wp_create_nonce( 'wpes-survey-node-adition' );
		$wpes_survey_node_association_nonce = wp_create_nonce( 'wpes-survey-node-association' );

		/**	Check and get existing issues into current survey	*/
		$node_list = null;
		$children_issues_list = $this->issues->get_issues( $post->ID );
		if ( !empty($children_issues_list) && ($children_issues_list->have_posts()) ) {
			$node_list = $this->issues->read_survey_node_list( $children_issues_list->posts, $post->ID );
		}
		wp_reset_query();

		require_once( $this->get_template_part( "backend", "nestable_tree/survey" ) );
	}

	/**
	 * GETTER - Get the survey total number of real issue to answer
	 *
	 * @return number The total issue number having answers
	 */
	function get_total_number_of_issue() {
		return $this->total_issue_number;
	}

	/**
	 * SETTER - Update the survey issues' order
	 *
	 * @param array $element_list The list of element to save new order for
	 * @param integer $survey_id The main survey id
	 *
	 */
	function survey_issues_order( $element_list, $survey_id ) {
		$order = 1;
		foreach ( $element_list as $survey_order ) {
			update_post_meta( $survey_order['id'], '_wpes_node_parent_id', $survey_id );
			wp_update_post( array( 'ID' => $survey_order['id'], 'menu_order' => $order, ) );
			$order++;

			if ( !empty( $survey_order['children'] ) ) {
				foreach ( $survey_order['children'] as $children ) {
					$this->survey_issues_order( array( $children ), $survey_order['id'] );
				}
			}
		}
	}


	/**
	 * Create post revision a post with new parameters
	 *
	 * @param integer $post_ID The current survey to create revision for
	 *
	 * @return mixed <integer, WP_Error> If new post revision has been succesfull then return an integer representing the new post identifier or return a wp_error
	 */
	function create_post_revision( $post_ID ) {
		global $wpdb,
		$wp_error;

		/**	Get current post information	*/
		$post_infos = get_post( $post_ID );
		/**	Set new information for post that will be created	*/
		$new_post_infos = array();
		$new_post_infos['post_status'] = 'publish';
		$new_post_infos['post_title'] = $post_infos->post_title;
		$new_post_infos['post_content'] = $post_infos->post_content;
		$new_post_infos['menu_order'] = $post_infos->menu_order;
		$new_post_infos['post_parent'] = $post_ID;
		$new_post_infos['post_type'] = ($post_infos->post_type == $this->post_type) ? $this->post_type_final : $this->issues->post_type_final;
		wp_reset_query();

		/**	Insert the new post	*/
		$last_post = wp_insert_post( $new_post_infos, $wp_error );
		if ( is_int( $last_post ) ) {
			$current_post_metas = get_post_meta( $post_ID );
			if ( !empty( $current_post_metas ) ) {
				foreach ( $current_post_metas as $meta_key => $meta_values ) {
					foreach ( $meta_values as $meta_value ) {
						if ( (substr($meta_key, 0, 6) == '_wpes_') && ($meta_key != '_wpes_node_parent_id') ) {
							add_metadata('post', $last_post, $meta_key, unserialize( $meta_value ), false);
						}
					}
				}
			}
		}

		return $last_post;
	}

	/**
	 * Get children for a given element for duplicate it into the final element type
	 *
	 * @param integer $current_item The current element to get children for in order to duplicate them
	 * @param integer $new_item The new item to associate duplicated element to
	 */
	function create_final_survey( $current_item, $new_item ) {
		$associated_item = $this->issues->get_issues( $current_item );

		if ( $associated_item->have_posts() ) {
			foreach ( $associated_item->posts as $sub_item ) {
				$new_issue_id = $this->create_post_revision( $sub_item->ID );
				add_metadata('post', $new_issue_id, '_wpes_node_parent_id', $new_item, false);
				$this->create_final_survey( $sub_item->ID, $new_issue_id );
			}
		}
		return;
	}


	/**
	 * Define the metabox allowing to associate current survey to other post type defined into current wordpress installation
	 *
	 * @param object $post The current survey (post) complete definition
	 */
	function survey_association( $post ) {
		$output = $sub_output = '';
		$current_association = get_post_meta( $post->ID, '_wpes_survey_association', true);

		$post_types = get_post_types();
		foreach ( $post_types as $post_type ) {
			if ( !in_array($post_type, array($this->post_type, $this->issues->post_type, $this->answers->post_type)) ) {
				$post_type_object = get_post_type_object( $post_type );

				if ( $post_type_object->show_in_menu || $post_type_object->show_in_nav_menus || $post_type_object->show_in_admin_bar ) {
					$sub_output .= $this->display( 'wpes_survey_post_type_association_item', array( 'SURVEY_ASSOCIATION_POST_TYPE' => $post_type, 'SURVEY_ASSOCIATION_POST_TYPE_NAME' => $post_type_object->labels->name, 'SURVEY_ASSOCAITION_CHECKBOX_STATE' => (!empty($current_association) && is_array($current_association) && in_array($post_type, $current_association) ? ' checked="checked"' : '')) );
				}
			}
		}

		/**	Add a filter to allow any plugin to add support for surveys	*/
		$sub_output = apply_filters( 'wpes_survey_association' , $sub_output, $current_association );

		if ( !empty($sub_output) ) {
			$output = $this->display( 'wpes_survey_post_type_association_list_container', array( 'REGISTERED_POST_TYPE_LIST' => $sub_output,) );
		}

		echo $output;
	}



	/**
	 * FINAL ASSOCIATED ELEMENT OUTPUT - Display the complete statistic for the current element and survey
	 *
	 * @param integer $survey_issue_number The total number of issues into current survey
	 * @param integer $survey_id The current survey identifier
	 *
	 * @return string The complete html output for current survey statistics
	 */
	function display_final_survey_statistics( $survey_issue_number, $survey_id, $parent_survey_id = 0, $associated_element_id = 0, $associated_element_type = '' ) {
		$output = '';

		$issues_with_answers = $this->issues_with_answers;

		ob_start();
		require( $this->get_template_part( "backend", "final-survey-result/stats") );
		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}

	/**
	 * FINAL ASSOCIATED ELEMENT OUTPUT - Display the statistic content for a given survey
	 *
	 * @param integer $survey_id The survey identifier to display stats for
	 * @param integer $survey_issue_number The total number of issue having answers for the survey
	 * @param array $issues_with_answers The list of issues' identifier that have answer
	 *
	 * @return string The html code for stats
	 */
	function display_final_survey_statistics_content( $survey_id, $survey_issue_number, $issues_with_answers, $from = "" ) {
		$output = '';

		ob_start();
		require( $this->get_template_part( "backend", "final-survey-result/stats", "detail" ) );
		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}

	/**
	 * FINAL ASSOCIATED ELEMENT OUTPUT - Define the metabox that display survey in associated post type
	 *
	 * @param object $post The current post complete definition
	 * @param array $args Additionnal parameters to spot exact element to display
	 *
	 */
	function survey_metabox_display( $post, $args ) {
		$final_survey = $this->final_survey_display( $post->ID, $args['args']['parent_element_id'] );

		echo $final_survey['content'];
	}

	/**
	 * Display all surveys into only one boxx, allowing to save space without lossing functionnalities
	 *
	 * @param  WP_Post $post Current post we are on
	 */
	function display_all_surveys_metabox( $post ) {
		global $wpdb;

		/** Get existing survey list */
		$query = $wpdb->prepare(
			"SELECT PM.post_id
			FROM {$wpdb->postmeta} AS PM
				INNER JOIN {$wpdb->posts} AS P ON (P.ID = PM.post_id)
			WHERE P.post_status = 'publish'
				AND PM.meta_key = %s
				AND PM.meta_value LIKE ('%%%s%%')
				AND P.post_type = %s",
			'_wpes_survey_association', $post->post_type, $this->post_type);
		$associated_surveys_list = $wpdb->get_results( $query );

		$already_started_survey_list = $not_started_survey = array();
		foreach ( $associated_surveys_list as $survey ) {
			$current_element_evaluation = get_post_meta( $post->ID, '_wpes_audit_' . $survey->post_id, true );
			if ( !empty( $current_element_evaluation ) ) {
				$already_started_survey_list[ $survey->post_id ][ 'survey' ] = $survey;
				$already_started_survey_list[ $survey->post_id ][ 'audit' ] = $current_element_evaluation;
			}

			$not_started_survey[] = $survey;
		}

		$ajax_action = "wpes-ajax-final-survey-evaluation-result-view";
		$associated_element_id = $post->ID;

		require_once( $this->get_template_part( "backend", "metabox", "association" ) );
	}

	/**
	 * FINAL ASSOCIATED ELEMENT OUTPUT - Output
	 *
	 * @param integer $associated_element_id The current associated element identifier
	 * @param integer $survey_id The survey to display
	 * @param boolean $preview Define if we have to output complete form or just a preview
	 *
	 * @return string The complete html output for the survey
	 */
	function final_survey_display( $associated_element_id, $survey_id, $external_evaluation = array() ) {
		$sub_output = '';

		$preview = true;
		$current_evaluation = null;
		$parent_survey_id = $survey_id;
		if ( !empty( $external_evaluation ) ) {
			$current_element_evaluation = $external_evaluation;
			if ( !empty($current_element_evaluation) && !empty($current_element_evaluation['in_progress'])) {
				$preview = false;
				$current_evaluation = 1;
				$survey_id = $current_element_evaluation['in_progress']['survey_id'];

				$evaluation_infos = $current_element_evaluation[ 'in_progress' ];
			}

			$ajax_action = $external_evaluation[ 'ajax_action' ];
		}
		else {
			$current_element_evaluation = get_post_meta( $associated_element_id, '_wpes_audit_' . $survey_id, true);

			if ( !empty($current_element_evaluation) && !empty($current_element_evaluation['in_progress'])) {
				if ( count($current_element_evaluation['in_progress']) == 1 ) {
					$preview = false;
					$current_evaluation = 1;
					$survey_id = $current_element_evaluation['in_progress'][1]['survey_id'];

					$evaluation_infos = $current_element_evaluation[ 'in_progress' ][1];
				}
			}
			if ( !empty( $current_element_evaluation[ 'closed' ] ) )
				krsort( $current_element_evaluation[ 'closed' ] );

			$ajax_action = "wpes-ajax-final-survey-evaluation-result-view";
		}

		/**	Get all issues for current element survey	*/
		$associated_item = $this->issues->get_issues( $survey_id, '', $preview );

		/**	Display state box	*/
		ob_start();
		require( $this->get_template_part( "backend", "final-survey/evaluation", "informations") );
		$main_evaluation_informations = ob_get_contents();
		ob_end_clean();

		/**	Output final survey for current element	*/
		$output = '';
		ob_start();
		require( $this->get_template_part( "backend", "final-survey/survey" ) );
		$output['content'] = ob_get_contents();
		ob_end_clean();

		return $output;
	}

	/**
	 * FINAL ASSOCIATED ELEMENT OUTPUT - Display an issue in associated element in order to give user to answer the different issue
	 *
	 * @param object $issue A wordpres database object representing an issue
	 * @param unknown_type $issue
	 * @param unknown_type $current_post
	 * @param unknown_type $preview
	 * @param unknown_type $survey_id
	 *
	 * @return Ambigous <string, mixed, NULL, unknown>
	 */
	function display_issue( $issue, $current_post, $preview, $survey_id ) {
		$output = '';

		$sub_output = '';
		$given_answer = '';
		$associated_item = $this->issues->get_issues( $issue->ID, '', $preview );
		if ( $associated_item->have_posts() ) {
			$issues_list = $associated_item->posts;
			$issue_childrens = '';
			foreach ( $issues_list as $item ) {
				$issue_childrens .= $this->display_issue( $item, $current_post, $preview, $survey_id );
			}
			$sub_output .= $this->display( 'wpes_survey_issue_display_in_associate_element_container', array( 'ISSUES_LIST' => $issue_childrens ) );
		}
		else if ( !$preview ) {
			$current_answer = $this->answers->get_last_answer_for_issue( $issue->ID );
			if ( !empty($current_answer) ) {
				$given_answer .= $this->answers->display_an_answer( $current_answer[ $issue->ID ], $issue->ID, $survey_id, $current_post );

				$this->total_issue_number++;
				$this->issues_with_answers[] = $issue->ID;
			}
			else {
				$sub_output .= $this->display_answer_form( $issue, $survey_id );
			}
		}
		else {
			$issue_associated_answers = get_post_meta( $issue->ID, '_wpes_issue_final_args', true);
			if ( !empty($issue_associated_answers) && !empty($issue_associated_answers['answers']) ) {
				$sub_output = '';
				foreach ( $issue_associated_answers['answers'] as $answer_id => $answer_def ) {
					$sub_output .= $answer_def['name'] . ', ';
				}
				$sub_output = __('Available answers', 'wp_easy_survey') . ' : ' . substr($sub_output, 0, -2);
			}
			else {
				$sub_output = __('No answer are associated to this issue for the moment', 'wp_easy_survey');
			}
		}

		$output = $this->display( 'wpes_survey_issue_display_in_associate_element', array( 'ISSUE_ID' => $issue->ID, 'ISSUE_TITLE' => $issue->post_title, 'ISSUE_SUBLIST' => $sub_output, 'ISSUE_GIVEN_ANSWERS' => $given_answer ) );

		return $output;
	}

	/**
	 * FINAL ASSOCIATED ELEMENT OUTPUT - The final answer
	 *
	 * @param unknown_type $issue
	 * @param unknown_type $current_post
	 * @param unknown_type $survey_id
	 *
	 * @return string The issue's html output
	 */
	function display_final_issue( $issue, $current_post, $survey_id, $from = '' ) {
		$output = '';
		$preview = false;

		/**	Get the different answers' state definition	*/
		$answers_type = unserialize( WPES_ANSWER_TYPE );

		$sub_output = '';
		$given_answer = '';
		$associated_item = $this->issues->get_issues( $issue->ID, '', $preview );
		if ( $associated_item->have_posts() ) {
			$issues_list = $associated_item->posts;
			$issue_childrens = '';
			foreach ( $issues_list as $item ) {
				$issue_childrens .= $this->display_final_issue( $item, $current_post, $survey_id, $from );
			}
			$sub_output .= $this->display( 'wpes_survey_issue_display_in_associate_element_container', array( 'ISSUES_LIST' => $issue_childrens ) );
		}
		else {
			$current_answer = $this->answers->get_last_answer_for_issue( $issue->ID );
			if ( !empty($current_answer) ) {
				foreach ( $current_answer as $answer_id => $answer_detail ) {
					$sub_output .= $this->answers->display_an_answer( $current_answer[ $issue->ID ], $issue->ID, $survey_id, $current_post, $from);

					$this->issues_with_answers[] = $issue->ID;
				}
			}
			else {
				ob_start();
				require( $this->get_template_part( "backend", "final-survey/answer", "waiting") );
				$sub_output .= ob_get_contents();
				ob_end_clean();
			}
			$this->total_issue_number++;
		}

		$output = $this->display( 'wpes_survey_issue_display_in_associate_element', array( 'ISSUE_ID' => $issue->ID, 'ISSUE_TITLE' => $issue->post_title, 'ISSUE_SUBLIST' => $sub_output, 'ISSUE_GIVEN_ANSWERS' => $given_answer ) );

		return $output;
	}

	/**
	 * FINAL ASSOCIATED ELEMENT OUTPUT - Display the form allowing to give an answer to a given issue
	 *
	 * @param object $issue The complete definition of an issue
	 *
	 * @return string The html output for the answer
	 */
	function display_answer_form( $issue, $survey_id, $from = '' ) {
		$current_answer = null;
		if ( !empty($from) && ($from == 'edition') ) {
			$answer = $this->answers->get_last_answer_for_issue( $issue->ID );
			foreach ( $answer as $answer_content ) {
				$current_answer['notes'] = $answer_content['content']['notes'];
				$current_answer['expiration-date'] = !empty($answer_content['content']['expiration-date']) ? $answer_content['content']['expiration-date'] : '';
			}
		}

		$issue_final_args = get_post_meta( $issue->ID, '_wpes_issue_final_args', true );
		if ( !empty($issue_final_args) && !empty($issue_final_args['answers']) ) {
			$display_issue_args = array(
				'id' => $issue->ID,
			);
			$answers_output = $this->display( 'wpes_survey_associated_answers_list_container', array(
				'ASSOCIATED_ANSWERS_LIST' => $this->answers->display_available_answers( $issue_final_args['answers'], $display_issue_args ),
			) );
			$this->total_issue_number++;
			$this->issues_with_answers[] = $issue->ID;
		}
		else {
			$answers_output = $this->display( 'wpes_no_answers_defined_for_issue', array() );
		}

		return $this->display( 'wpes_survey_issue_answer_form', array(
			'SURVEY_ID' 		 => $survey_id,
			'ISSUE_ID' 			 => $issue->ID,
			'ISSUE_CURRENT_ANSWER_NOTES' => '', //!empty($current_answer) && !empty($current_answer['notes']) ? $current_answer['notes'] : '',
			'ISSUE_CURRENT_ANSWER_EXPIRATION_DATE' => '', //!empty($current_answer) && !empty($current_answer['expiration-date']) ? $current_answer['expiration-date'] : '',
			'CURRENT_ANSWER_STATE' => $from,
			'FINAL_ISSUE_ANSWER_DETAILS_CONTAINER_CLASS' => !empty($from) && ($from == 'edition') ? '' : ' wpes-hide',
			'AVAILABLE_ANSWERS'  => $answers_output
		) );
	}

	/**
	 * FINAL ASSOCIATED ELEMENT OUTPUT - Display the box with the result for a given evaluation
	 *
	 * @param array $evaluation The evaluation information to get result for
	 *
	 * @return string The html output for evaluation result
	 */
	function display_final_survey_evaluation_result( $evaluation, $content, $from = "" ) {
		$output = '';

		$user_data = get_userdata( $evaluation[ 'user' ] );
		$user_closed_data = get_userdata( $evaluation[ 'user_closed' ] );
		$evaluation_to_display = $evaluation;
		ob_start();
		require_once( $this->get_template_part( "backend", "final-survey-result/view") );
		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}


	/**
	 * AJAX - Display the complete survey management interface
	 */
	function ajax_display_survey_content() {
		check_ajax_referer( 'wpes-display-survey', 'wpes-ajax-display-survey-metabox' );

		$this->survey_content_management( get_post( $_POST['post_ID'] )  );
		die();
	}

	/**
	 * AJAX - Load a given issue's answer for edition
	 */
	function ajax_load_answer_for_edition() {
		check_ajax_referer( 'wpes-answer-edit', 'wpes-ajax-answer-edit-nonce' );
		$response = array(
			'output' => '',
			'issue_id' => $_POST['issue_ID'],
		);

		$issue = get_post( $_POST['issue_ID'] );
		$response['output'] = $this->display_answer_form( $issue, $_POST['survey_ID'], 'edition' );

		echo json_encode( $response );
		die();
	}

	/**
	 * AJAX - Save survey's issues' order on the fly when user move element into tree
	 */
	function ajax_survey_order_save() {
		check_ajax_referer( 'wpes-survey-order-save', 'wpes-ajax-survey-order' );

		if ( !empty( $_POST['wpes-survey-new-order'] ) ) {
			$this->survey_issues_order( $_POST['wpes-survey-new-order'], $_POST['post_ID'] );
		}

		die();
	}

	/**
	 * AJAX - Save current survey composition
	 */
	function ajax_start_new_evaluation() {
		check_ajax_referer( 'wpes-new-evaluation-start', 'wpes-ajax-new-evaluation-start' );
		$response = array(
			'status'    => false,
			'survey_id' => $_POST['survey_id'],
			'post_ID'   => $_POST['post_ID'],
			'output'    => '',
		);
		/**	Get existing evaluation	*/
		$current_element_evaluation = get_post_meta( $_POST['post_ID'], '_wpes_audit_' . $_POST['survey_id'], true);
		$started_evaluation = !empty($current_element_evaluation) && !empty($current_element_evaluation['in_progress']) ? $current_element_evaluation['in_progress'] : null;
		$next_evaluation_number = count($started_evaluation) + 1;

		/**	Save the survey content for the current evaluation */
		$new_survey_id = $this->create_post_revision( $_POST['survey_id'] );

		if ( is_int($new_survey_id) ) {
			$this->create_final_survey( $_POST['survey_id'], $new_survey_id );

			/**	Save new evaluation informations */
			$current_element_evaluation['in_progress'][$next_evaluation_number] = array(
				'date_started' => current_time('mysql', 0),
				'date_closed'  => '',
				'state'        => 'started',
				'user'         => get_current_user_id(),
				'user_closed'  => '',
				'survey_id'    => $new_survey_id,
			);
			update_post_meta( $_POST['post_ID'], '_wpes_audit_' . $_POST['survey_id'], $current_element_evaluation);

			$final_survey = $this->final_survey_display( $_POST['post_ID'], $_POST['survey_id'] );
			$response['status'] = true;
			$response['output'] = $final_survey[ 'content' ];
		}
		else {}

		echo json_encode( $response );
		die();
	}

	/**
	 * AJAX - Save current survey composition
	 */
	function ajax_close_evaluation() {
		check_ajax_referer( 'wpes-ajax-close-evaluation', 'wpes-ajax-close-evaluation' );
		$ancestors = get_post_ancestors( $_POST['survey_id'] );
		$response = array(
			'status'    => false,
			'survey_id' => $ancestors[0],
			'post_ID'   => $_POST['post_ID'],
			'output'    => '',
		);
		$current_element_evaluation = get_post_meta( $_POST['post_ID'], '_wpes_audit_' . $ancestors[0], true);
		$current_element_evaluation[ 'in_progress' ][ 1 ][ 'date_closed' ] = current_time( 'mysql', 0 );
		$current_element_evaluation[ 'in_progress' ][ 1 ][ 'user_closed' ] = get_current_user_id();
		$current_element_evaluation[ 'closed' ][ ] = $current_element_evaluation[ 'in_progress' ][ 1 ];
		unset( $current_element_evaluation[ 'in_progress' ][ 1 ] );
		update_post_meta( $_POST['post_ID'], '_wpes_audit_' . $ancestors[0], $current_element_evaluation);

		$response['status'] = true;
		ob_start();
		$this->survey_metabox_display( get_post( $_POST['post_ID'] ), array( 'args' => array( 'parent_element_id' => $ancestors[0] ) ) );
		$response['output'] = ob_get_contents();
		ob_end_clean();

		wp_die( json_encode( $response ) );
	}

	/**
	 * AJAX - Display a form revision with the different given answers
	 */
	function ajax_evaluation_answers_view() {
		check_ajax_referer( 'wpes-ajax-view-survey-results', 'wpes-ajax-survey-final-result-view-nonce' );

		$output = $this->display_the_survey( $_REQUEST[ 'post_id' ], $_REQUEST[ 'survey_id' ], $_REQUEST[ 'evaluation_id' ], $_REQUEST[ 'final_survey_id' ] );

		wp_die( $output );
	}

	/**
	 * AJAX - Save a file with an export of the survey audit by file format
	 */
	function ajax_evaluation_export_file() {
ini_set("display_errors", true);
error_reporting(E_ALL);

		$response = array(
			'status' => true,
		);

		$export_type = !empty( $_POST[ 'export_type' ] ) ? $_POST[ 'export_type' ] : null;
		$element_id = !empty( $_POST[ 'element_id' ] ) ? $_POST[ 'element_id' ] : null;
		$survey_id = !empty( $_POST[ 'survey_id' ] ) ? $_POST[ 'survey_id' ] : null;
		$evaluation_state = isset( $_POST[ 'evaluation_state' ] ) ? $_POST[ 'evaluation_state' ] : 'closed';
		$evaluation_id = isset( $_POST[ 'evaluation_id' ] ) && ( 0 <= $_POST[ 'evaluation_id' ] ) ? $_POST[ 'evaluation_id' ] : null;
		$final_survey_id = !empty( $_POST[ 'final_survey_id' ] ) ? $_POST[ 'final_survey_id' ] : null;

		if ( !empty( $element_id ) && !empty( $survey_id ) && !empty( $final_survey_id ) ) {
			/**	Get survey informations	*/
			$survey = get_post( $survey_id );

			/** Get the element associated to the survey */
			$element = get_post( $element_id );

			/**	Get the current element survey print history for the current survey	*/
			$survey_print_history = get_post_meta( $element_id, '_wpes_survey_print_history_' . $survey_id, true );

			/**	Define the filename	*/
			$version_number = !empty( $survey_print_history ) && !empty( $survey_print_history[ mysql2date( 'Ymd', current_time( 'mysql', 0 ), true ) ] ) && !empty( $survey_print_history[ mysql2date( 'Ymd', current_time( 'mysql', 0 ), true ) ][ $final_survey_id ] ) ? ( count( $survey_print_history[ mysql2date( 'Ymd', current_time( 'mysql', 0 ), true ) ][ $final_survey_id ] ) + 1 ) : 1;
			$filename = sprintf( $this->export_file_name, mysql2date( 'Ymd', current_time( 'mysql', 0 ), true ), $element_id, sanitize_title( $element->post_title ), $survey_id, $final_survey_id, $version_number );

			switch ( $export_type ) {
				case 'odt':
// 					if ( !class_exists( 'Odf' ) ) {

// 					}
					$survey_result = get_post_meta( $element_id, '_wpes_audit_' . $survey_id, true);
					$response = $this->save_odt_file( $element_id, $survey_id, $final_survey_id, $survey_result[ $evaluation_state ], $filename . '.odt' );
					break;
				case 'pdf':
				default:
					/**	Include html to pdf class */
					require_once(WPEASYSURVEY_MODULELIB_DIR . 'html2pdf/html2pdf.class.php' );

					$response = $this->save_pdf_file( $element_id, $survey_id, $final_survey_id, $evaluation_id, $filename . '.pdf'  );
					break;
			}

			/**	Add file print into history	*/
			if ( !empty( $response ) && !empty( $response[ 'status' ] ) ) {
				$survey_print_history[ mysql2date( 'Ymd', current_time( 'mysql', 0 ), true ) ][ $final_survey_id ][] = array( 'file' => $filename . '.' . $export_type, 'user' => get_current_user_id(), 'date' => current_time( 'mysql', 0 ) );
			}

			/**	Save the new file print history	*/
			update_post_meta( $element_id, '_wpes_survey_print_history_' . $survey_id, $survey_print_history );

			$response[ 'output' ] = $this->display_export_list( $element_id, $survey_id, $final_survey_id );
		}
		else {
			$response[ 'status' ] = false;
			$response[ 'message' ] = sprintf( __( 'One of those information is missing: Element id.%s / Survey id.%s / Final survey id.%s', 'wp_easy_survey' ), $element_id, $survey_id, $final_survey_id );
		}

		$response[ 'final_survey_id' ] = $final_survey_id;

		wp_die( json_encode( $response ) );
	}

	/**
	 *	Save a file under pdf format
	 *
	 * @param integer $element_id The current element on witch we want to get an audit result
	 * @param integer $survey_id The survey id defining the issue structure
	 * @param integer $final_survey_id The final survey id that has been used for the audit
	 * @param integer $evaluation_id The evaluation identifier to specify informations to get
	 * @param string $filename The output filename
	 *
	 * @return array The export status
	 */
	function save_pdf_file( $element_id, $survey_id, $final_survey_id, $evaluation_id, $filename ) {
		$response = array(
			'status' => true,
			'message' => __( 'The file have been created succesfully', 'wp_easy_survey' ),
		);

		ob_start();
		require( $this->get_template_part( "backend", "final-survey-result/html2pdf", "styles.css" ) );
		echo $this->display_the_survey( $element_id, $survey_id, $evaluation_id, $final_survey_id, 'export' );
		$survey_output = ob_get_contents();
		ob_end_clean();

		try {
			$html_content = '<page>' . $survey_output . '</page>';

			$html2pdf = new HTML2PDF( 'P', 'A4', 'fr' );
			$html2pdf->setDefaultFont( 'Arial' );
			$html2pdf->writeHTML( $html_content );

			/**	Save the file	*/
			$directory_to_use = $this->export_directory . $element_id . '/' . $survey_id . '/';
			wp_mkdir_p( $directory_to_use );
			$html2pdf->Output( $directory_to_use . $filename, 'F');
		}
		catch (HTML2PDF_exception $e) {
			$response[ 'status' ] = false;
			$response[ 'message' ] = '<div id="wpes-final-survey-evaluation-export-message" >' . sprintf( __( 'An exception (%s) has been thrown for Element id.%s / Survey id.%s / Final survey id.%s', 'wp_easy_survey' ), $e, $element_id, $survey_id, $final_survey_id ) . '</div>';
		}

		return $response;
	}

	/**
	 *	Save a file under odt format
	 *
	 * @param integer $element_id The current element on witch we want to get an audit result
	 * @param integer $survey_id The survey id defining the issue structure
	 * @param integer $final_survey_id The final survey id that has been used for the audit
	 * @param string $filename The output filename
	 *
	 * @return array The export status
	 */
	function save_odt_file( $element_id, $survey_id, $final_survey_id, $audit_informations, $filename, $specified_element = null ) {
		$response = array(
			'status' => true,
			'message' => __( 'The file have been created succesfully', 'wp_easy_survey' ),
		);

		if ( !empty( $audit_informations ) ) {

			/**	Read closed evaluation list in order to define the one to use	*/
			$current_evaluation = array();
			foreach ( $audit_informations as $audit ) {
				if( array_key_exists( 'survey_id', $audit ) && ( $final_survey_id == $audit['survey_id'] ) ) {
					$current_evaluation = $audit;
					continue;
				}
			}

			if ( !empty( $current_evaluation ) ) {
				/**	Include php odt class */
				require_once(WPEASYSURVEY_MODULELIB_DIR . 'odtphp/odf.php' );

				/**	Create an instance for php odt with the model	*/
				$odf = new WPES_Odf( WPEASYSURVEY_BACKEND_TPL_DIR . "final-survey-result/final-survey-audit.odt" );

				/**	Get survey informations	*/
				$survey = get_post( $survey_id );

				/** Get the element associated to the survey */
				$element = empty( $specified_element ) ? get_post( $element_id, ARRAY_A ) : $specified_element;

				/**	Get informations about users that made the audit	*/
				$opener_user = $closer_user = get_userdata( $current_evaluation[ 'user' ] );
				if ( !empty( $current_evaluation[ 'user_closed' ] ) && ( $current_evaluation[ 'user_closed' ] != $current_evaluation[ 'user' ] ) ) {
					$closer_user = get_userdata( $current_evaluation[ 'user_closed' ] );
				}

				/**	Assign the value to generate odt file	*/
				/**	Survey	*/
				$odf->setVars( 'NomFormulaire', $survey->post_title );
				/**	Element */
				$odf->setVars( 'NomDuClient', $element[ 'post_title' ] );
				/**	User	*/
				$odf->setVars( 'NomAuditeurDebut', $opener_user->display_name );
				$odf->setVars( 'NomAuditeurFin', $closer_user->display_name );
				/**	Date	*/
				$odf->setVars( 'DateDebutAudit', mysql2date( get_option( 'date_format' ), $current_evaluation[ 'date_started' ], true) );
				$odf->setVars( 'HeureDebutAudit', mysql2date( get_option( 'time_format' ), $current_evaluation[ 'date_started' ], true) );
				$odf->setVars( 'DateFinAudit', mysql2date( get_option( 'date_format' ), $current_evaluation[ 'date_closed' ], true) );
				$odf->setVars( 'HeureFinAudit', mysql2date( get_option( 'time_format' ), $current_evaluation[ 'date_closed' ], true) );

				/**	Read the questions	*/
				/**	Instanciate the loop for answer adding into odt final file	*/
				$main_questions = $odf->setSegment( 'main_questions' );
				$survey = $this->build_survey( $final_survey_id );
				foreach ( $survey as $issue ) {
					try {
						$main_questions->setVars( 'NumeroQuestion', str_replace('-', ' ', $issue[ 'issue_key' ] ), true, 'UTF-8' );
					} catch ( Exception $e ) {}

					try {
						$main_questions->setVars( 'IntituleQuestion', $issue[ 'issue_title' ], true, 'UTF-8' );
					} catch ( Exception $e ) {}

					try {
						$main_questions->setVars( 'Reponse', !empty( $issue[ 'issue_detail' ] ) ? "
    " . $issue[ 'issue_detail' ] : '', true, 'UTF-8' );
					} catch ( Exception $e ) {}

					try {
						$main_questions->setVars( 'CommentaireReponse', !empty( $issue[ 'issue_comment' ] ) ? "
        " . $issue[ 'issue_comment' ] : '', true, 'UTF-8' );
					} catch ( Exception $e ) {}

					try {
						$main_questions->setVars( 'ReponseSeule', !empty( $issue[ 'issue_answer' ] ) ? "
    " . $issue[ 'issue_answer' ] : '', true, 'UTF-8' );
					} catch ( Exception $e ) {}

					try {
						$main_questions->setVars( 'InfosReponse', !empty( $issue[ 'issue_answer_infos' ] ) ? "
    " . $issue[ 'issue_answer_infos' ] : '', true, 'UTF-8' );
					} catch ( Exception $e ) {}

					$main_questions->merge();
				}
				$odf->mergeSegment( $main_questions );

				/**	Stats	*/
				$odf->setVars( 'NombreQuestions', $this->total_issue_number );
				$answered_answers = !empty( $this->issues_with_answers[ 'answered' ] ) ? $this->issues_with_answers[ 'answered' ] : 0;
				$inprogress_answers = !empty( $this->issues_with_answers[ 'in_progress' ] ) ? $this->issues_with_answers[ 'in_progress' ] : 0;
				$not_answered_answers = !empty( $this->issues_with_answers[ 'not_answered' ] ) ? $this->issues_with_answers[ 'not_answered' ] : 0;
				$nb_of_answer_given = $answered_answers + $inprogress_answers;
				$odf->setVars( 'NombreReponse', $nb_of_answer_given);
				$final_survey_progression = number_format( ($nb_of_answer_given * 100 ) / $this->total_issue_number, 1 );
				$odf->setVars( 'PourcentageReponse', substr( $final_survey_progression, -2 ) == '.0' ? substr( $final_survey_progression, 0, -2 ) : $final_survey_progression );
				$odf->setVars( 'NombreQuestionsNonRepondues', $not_answered_answers );
				$odf->setVars( 'NombreQuestionsEnCours', $inprogress_answers );
				$odf->setVars( 'NombreQuestionsRepondues', $answered_answers );

				/**	Save the file	*/
				$directory_to_use = $this->export_directory . $element_id . '/' . $survey_id . '/';
				wp_mkdir_p( $directory_to_use );
				$odf->saveToDisk( $directory_to_use . $filename );
			}
			else {
				$response[ 'status' ] = false;
				$response[ 'message' ] = sprintf( __( 'The final survey audit has not been found: Element id.%s / Survey id.%s / Final survey id.%s', 'wp_easy_survey' ), $element_id, $survey_id, $final_survey_id );
			}
		}
		else {
			$response[ 'status' ] = false;
			$response[ 'message' ] = sprintf( __( 'There are no closed evaluation for current element id.%s for survey id.%s', 'wp_easy_survey' ), $element_id, $survey_id );
		}

		return $response;
	}

	/**
	 * Read all issue from survey and build an array with all of them in order to print a document with the complete audit result
	 *
	 * @param integer $final_survey_id The survey id to get issue for
	 * @param string $current_index The current index under string format to do a "manual" tree
	 *
	 * @return array THe complete survey on a single level
	 */
	function build_survey( $final_survey_id, $current_index = '') {
		$survey_definition = array();

		$associated_item = $this->issues->get_issues( $final_survey_id, '', false );
		if ( $associated_item->have_posts() ) {

			/**	Add each issue to display	*/
			$i = 1;
			foreach ( $associated_item->posts as $item ) {
				$survey_definition[ $item->ID ][ 'issue_key' ] = $current_index . $i;
				$survey_definition[ $item->ID ][ 'issue_title' ] = $item->post_title;

				$sub_associated_item = $this->issues->get_issues( $item->ID, '', false );
				if ( $sub_associated_item->have_posts() ) {
					$survey_definition[ $item->ID ][ 'issue_detail' ] = '';
					$survey_definition = array_merge( $survey_definition, $this->build_survey( $item->ID, '--' . $current_index . $i . '.' ) );
				}
				else {
					$current_answer = $this->answers->get_last_answer_for_issue( $item->ID );

					$the_answer = '   ';

					if ( !empty( $current_answer ) && !empty( $current_answer[ $item->ID ] ) ) {
						$answer_def_for_issue = get_post_meta( $item->ID, '_wpes_issue_final_args', true);
						foreach ( $current_answer[ $item->ID ][ 'content' ][ 'answers' ] as $answer ) {
							$answer_def = !empty($answer_def_for_issue[ 'answers' ]) && !empty($answer_def_for_issue[ 'answers' ][ $answer['main_choice'] ]) ? $answer_def_for_issue[ 'answers' ][ $answer['main_choice'] ] : null;
							if ( !empty($answer_def) ) {
								$the_answer .= ((!empty($answer['details']) || (isset($answer['details']) && ($answer['details'] == '0'))) ? $answer['details'] : $answer_def[ 'name' ]) . (!empty($answer_def['options']['options']) && !empty($answer_def['options']['options']['unit']) ? $answer_def['options']['options']['unit'] : '') . ' / ';
							}
						}
						$user_data = get_userdata( $current_answer[ $item->ID ][ 'content' ]['answer_user'] );
						$answer_state = $this->answers->check_answer_state( $item->ID, $current_answer[ $item->ID ]['content']['answers'] );
						if ( empty($this->issues_with_answers[$answer_state]) ) {
							$this->issues_with_answers[$answer_state] = 1;
						}
						else {
							$this->issues_with_answers[$answer_state]++;
						}

						$survey_definition[ $item->ID ][ 'issue_answer' ] = trim( substr( $the_answer, 0, -3 ) );
						$survey_definition[ $item->ID ][ 'issue_answer_infos' ] = sprintf( __('The %1$s %2$s answered', 'wp_easy_survey'), mysql2date( sprintf( __( '%1$s \a\t %2$s', 'wp_easy_survey' ), get_option( 'date_format' ), get_option( 'time_format' ) ), $current_answer[ $item->ID ]['content']['answer_date'], true), $user_data->display_name );
						$the_answer = $survey_definition[ $item->ID ][ 'issue_answer_infos' ] . ' ' . $survey_definition[ $item->ID ][ 'issue_answer' ];
					}
					else {
						if ( empty($this->issues_with_answers['not_answered']) ) {
							$this->issues_with_answers['not_answered'] = 1;
						}
						else {
							$this->issues_with_answers['not_answered']++;
						}
						$the_answer = __( 'No answer were given to this issue' , 'wp_easy_survey' );
					}

					$survey_definition[ $item->ID ][ 'issue_detail' ] = $the_answer;
					$survey_definition[ $item->ID ][ 'issue_comment' ] = !empty( $current_answer[ $item->ID ]['content'][ 'notes' ] ) ? $current_answer[ $item->ID ]['content'][ 'notes' ] : '';
					$this->total_issue_number++;
				}

				$i++;
			}
		}

		return $survey_definition;
	}

	/**
	 * Build the survey output
	 *
	 * @param integer $element_id The current element identifier to display survey for
	 * @param integer $survey_id The current survey identifier we want to display
	 * @param integer $evaluation_id The evaluation identifier to get result for
	 * @param integer $final_survey_id The final survey identifier that have been used for audit
	 * @param string $from Allow to specify the
	 *
	 * @return string
	 */
	function display_the_survey( $element_id, $survey_id, $evaluation_id, $final_survey_id, $from = 'final_display' ) {
		$output = '';

		$element_evaluation = get_post_meta( $element_id, '_wpes_audit_' . $survey_id, true);
		if ( !empty($element_evaluation) && !empty($element_evaluation[ 'closed' ]) && !empty($element_evaluation[ 'closed' ][ $evaluation_id ]) ) {
			$preview = false;

			$associated_item = $this->issues->get_issues( $final_survey_id, '', $preview );
			if ( $associated_item->have_posts() ) {
				/**	Add each issue to display	*/
				$sub_output = '';
				foreach ( $associated_item->posts as $item ) {
					$sub_output .= $this->display_final_issue( $item, $element_id, $survey_id, $from);
				}
			}

			if ( !empty($sub_output) ) {
				$output = $this->display_final_survey_evaluation_result( $element_evaluation[ 'closed' ][ $evaluation_id ], $sub_output, $from );
			}
			else {
				$output = sprintf( __('There are no issues in this survey for the moment. %s', 'wp_easy_survey'), '<a href="' . admin_url('post.php') . '?post=' . $survey_id . '&amp;action=edit" >' . __('Edit survey', 'wp_easy_survey') . '</a>' );
			}
		}

		return $output;
	}

	/**
	 * DISPLAY - Output the list of existing document for a given survey
	 *
	 * @param integer $element_id The eleent identifier to get survey export for
	 * @param integer $survey_id The final survey identifier to get different document for
	 *
	 * @return string
	 */
	function display_export_list( $element_id, $survey_id, $final_survey_id, $element_type = '' ) {
		$output = '';

		$current_element_survey_directory = $this->export_directory . $element_id . '/' . $survey_id;

		/**	Get the current element survey print history for the current survey	*/
		$survey_print_history = apply_filters( 'wpes-final-survey-export-file-list-filter', get_post_meta( $element_id, '_wpes_survey_print_history_' . $survey_id, true ), $element_id, $survey_id, $final_survey_id, $element_type );

		if ( !empty( $survey_print_history ) ) {
			krsort( $survey_print_history );
			foreach ( $survey_print_history as $date => $printed_file ) {
				if ( !empty( $printed_file ) && !empty( $printed_file[ $final_survey_id ] ) && is_array( $printed_file[ $final_survey_id ] ) ) {
					krsort( $printed_file[ $final_survey_id ] );

					foreach ( $printed_file[ $final_survey_id ] as $file_infos ) {
						if ( !empty( $file_infos ) && !empty( $file_infos[ 'file' ] ) && is_file( $current_element_survey_directory . '/' . $file_infos[ 'file' ] ) ) {
							$output .= "<a target='wpes-expoort' href='" . $this->export_directory_url . $element_id . '/' . $survey_id . "/" . $file_infos[ 'file' ] . "' >" . $file_infos[ 'file' ] . "</a><br/>";
						}
					}
				}
			}
		}

		return $output;
	}

}
