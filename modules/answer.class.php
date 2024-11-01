<?php
/**
 * File containing component definition for answer management
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
 * Define the component to manage answers
 *
 * @author Eoxia developpement team <dev@eoxia.com>
 * @version 0.1
 * @package wp_easy_survey
 * @subpackage core
 */
class wpes_answer extends wpes_display {

	/**	Define the custom post type for answers	*/
	public $post_type = 'wpes_answers';

	/**	Define the custom post type prefix for answers	*/
	public $post_type_prefix = 'A';

	/**	Define the different types available for answers */
	private $answer_types = array();

	/**	Define default color for different answer state*/
	private $default_color = 'C0C0C0';

	/**
	 *	Instanciate answer management component
	 */
	function __construct( ) {
		/**	Initialise display component	*/
		parent::__construct();

		/**	Call the main definition for custom post type	*/
		$this->definition();
		$this->set_answer_types();

		/**	Call the different metaboxes allowing to configure each custom post type 	*/
		add_action( 'add_meta_boxes', array(&$this, 'meta_boxes_caller') );

		/**	Make specific saving when saving cutom post type element	*/
		add_action( 'save_post', array(&$this, 'save_post') );
		add_filter( 'wp_insert_post_data', array(&$this, 'create_post'), 10, 1 );

		/**	Add different support for ajax	*/
		add_action('wp_ajax_wpes-ajax-save-answer', array( &$this, 'ajax_save_answer') );
		add_action('wp_ajax_wpes-ajax-survey-view-issue-history', array( &$this, 'ajax_load_issue_answer_history') );
		add_action('wp_ajax_wpes-ajax-reuse-answer', array( &$this, 'ajax_reuse_answer') );
	}

	/**
	 * Define and create the custom taxonomy for answers management
	 *
	 * @see register_taxonomy()
	 */
	function definition() {
		$labels = array(
			'name'                	=> _x( 'Answers', 'Post Type General Name', 'wp_easy_survey' ),
			'singular_name'       	=> _x( 'Answer', 'Post Type Singular Name', 'wp_easy_survey' ),
			'menu_name'           	=> __( 'Answer', 'wp_easy_survey' ),
			'parent_item_colon'   	=> __( 'Parent Answer:', 'wp_easy_survey' ),
			'all_items'           	=> __( 'Answers', 'wp_easy_survey' ),
			'view_item'           	=> __( 'View Answer', 'wp_easy_survey' ),
			'add_new_item'        	=> __( 'Add New Answer', 'wp_easy_survey' ),
			'add_new'             	=> __( 'New Answer', 'wp_easy_survey' ),
			'edit_item'           	=> __( 'Edit Answer', 'wp_easy_survey' ),
			'update_item'         	=> __( 'Update Answer', 'wp_easy_survey' ),
			'search_items'        	=> __( 'Search Answers', 'wp_easy_survey' ),
			'not_found'           	=> __( 'No answers found', 'wp_easy_survey' ),
			'not_found_in_trash'  	=> __( 'No answers found in Trash', 'wp_easy_survey' ),
		);
		$args = array(
			'label'               	=> __( 'Answers', 'wp_easy_survey' ),
			'description'         	=> __( 'Answers management', 'wp_easy_survey' ),
			'labels'              	=> $labels,
			'supports'            	=> array( 'title' ),
			'hierarchical'        	=> false,
			'public'              	=> false,
			'show_ui'             	=> true,
			'show_in_menu'        	=> false, //'edit.php?post_type=wpes_survey',
			'show_in_nav_menus'   	=> false,
			'show_in_admin_bar'   	=> false,
			'can_export'          	=> true,
			'has_archive'         	=> true,
			'exclude_from_search' 	=> true,
			'publicly_queryable'  	=> false,
			'query_var'           	=> 'answer',
			'capability_type'     	=> 'post',
			'rewrite'     		  	=> false,
		);
		register_post_type( $this->post_type, $args );
	}

	/**
	 * Post save hook function. When saving a post, this function is called for saving custom informations about current edited post
	 *
	 * @param integer $post_ID
	 */
	function save_post( $post_ID ) {

		/**	When saving an answer make special case	*/
		if ( !empty($_POST) && !empty($_POST['post_type']) && ($_POST['post_type'] == $this->post_type) && !empty($_POST['wpes_answer_meta']) ) {

			/**	Save answer options	*/
			if ( !empty($_POST['wpes_answer_meta']['options']) ) {
				$_POST['wpes_answer_meta']['options']['color'] = str_replace( '#', '', empty($_POST['wpes_answer_meta']['options']['color']) ? (!empty($_POST['wpes_answer_meta']['options']['default_color']) ? $_POST['wpes_answer_meta']['options']['default_color'] : $this->default_color) : $_POST['wpes_answer_meta']['options']['color'] );
				if ( !empty($_POST['wpes_answer_meta']['options']['default_color']) ) {
					unset( $_POST['wpes_answer_meta']['options']['default_color'] );
				}
				update_post_meta( $post_ID, '_wpes_answer_options', $_POST['wpes_answer_meta']['options'] );
			}

			/**	Save answer type definition */
			if ( !empty($_POST['wpes_answer_meta']['type_def']) && !empty($_POST['wpes_answer_meta']['type_def']['type']) ) {
				if ( !empty($_POST['wpes_answer_meta'][$_POST['wpes_answer_meta']['type_def']['type']]['type_def']) ) {
					if ( !empty($_POST['wpes_answer_meta'][$_POST['wpes_answer_meta']['type_def']['type']]['type_def']['options'])
							&& !empty($_POST['wpes_answer_meta'][$_POST['wpes_answer_meta']['type_def']['type']]['type_def']['options']['content'])
							&& !empty($_POST['wpes_answer_meta'][$_POST['wpes_answer_meta']['type_def']['type']]['type_def']['options']['use_content']) ) { }
					else if ( $_POST['wpes_answer_meta']['type_def']['type'] != 'select-list' ) {
						unset( $_POST['wpes_answer_meta'][$_POST['wpes_answer_meta']['type_def']['type']]['type_def']['options']['content'] );
					}
					unset($_POST['wpes_answer_meta'][$_POST['wpes_answer_meta']['type_def']['type']]['type_def']['options']['use_content']);
					$_POST['wpes_answer_meta']['type_def']['options'] = $_POST['wpes_answer_meta'][$_POST['wpes_answer_meta']['type_def']['type']]['type_def']['options'];
				}

				update_post_meta( $post_ID, '_wpes_answer_type_def', $_POST['wpes_answer_meta']['type_def']);
			}
		}

		/**	When saving any existing element and that there is a survey with answer to save	*/
		if ( !empty( $_POST['wpes-issue-final-answer'] ) ) {
			foreach ( $_POST['wpes-issue-final-answer'] as $issue_ID => $answer_details ) {
				if ( !empty( $answer_details['answers'] ) ) {
					$answers_to_save = $answer_details;
					$stored_answers = null;
					$i = 0;
					foreach ( $answer_details['answers'] as $answers ) {
						foreach ( $answers as $key => $value ) {
							$stored_answers[ $i ][ $key ] = $value;
							if ( !empty( $answers_to_save['answers_detail'] ) && array_key_exists($value, $answers_to_save['answers_detail']) ) {
								$stored_answers[ $i ][ 'details' ] = $answers_to_save['answers_detail'][ $value ][ 'details' ];
							}
							$i++;
						}
					}
					unset( $answer_details['answers'] );
					unset( $answer_details['answers_detail'] );
					$answer_details['answers'] = $stored_answers;
					if ( !empty( $answer_details['answers'] ) ) {
						$this->save_the_answer( $post_ID, $issue_ID, $answer_details, $answer_details['survey_id'] );
					}
				}
			}
		}

	}

	/**
	 * When a new answer is created automatically add parameters to this answers (menu_order)
	 *
	 * @param array $data The current post datas automatically created
	 *
	 * @return array Additionnal parameters for post creation
	 */
	function create_post( $data ) {

		if ( $data[ 'post_type' ] == $this->post_type && $data[ 'post_status' ] == 'auto-draft' ) {
			global $wpdb;
			$data[ 'menu_order' ] = $wpdb->get_var( "SELECT MAX(menu_order)+1 AS menu_order FROM {$wpdb->posts} WHERE post_type='{$this->post_type}'" );
		}

		return $data;
	}

	/**
	 * Call the different metaboxes for answers admin edition page
	 */
	function meta_boxes_caller() {
		/**	Define metabox allowing to manage answer type	*/
		add_meta_box( $this->post_type . '-type-options', __('Answer type', 'wp_easy_survey'), array(&$this, 'answer_options_type'), $this->post_type, 'normal' );

		/**	Define metabox allowing to manage answer options	*/
		add_meta_box( $this->post_type . '-main-options', __('Answer options', 'wp_easy_survey'), array(&$this, 'answer_options'), $this->post_type, 'normal' );
	}

	/**
	 * Define the different type existing for answers
	 */
	function set_answer_types() {
		$this->answer_types = array(
			'text-display' => array(
				'name' => __('Text display', 'wp_easy_survey'),
				'has_conf' => false,
			),
			'percentage' => array(
				'name' => __('Percentage', 'wp_easy_survey'),
				'has_conf' => false,
			),
			'short-text' => array(
				'name' => __('Short text', 'wp_easy_survey'),
				'has_conf' => true,
			),
			'long-text' => array(
				'name' => __('Long text', 'wp_easy_survey'),
				'has_conf' => true,
			),
			'select-list' => array(
				'name' => __('Select list', 'wp_easy_survey'),
				'has_conf' => true,
			),
		);
	}

	/**
	 * Display the metabox content allowing to manage answer type
	 *
	 * @param object $post The complete definition of current edited post
	 */
	function answer_options_type( $post ) {
		$answer_meta_type_def = get_post_meta( $post->ID, '_wpes_answer_type_def', true);

		$answer_type_list = '';
		foreach ( $this->answer_types as $type => $type_def ) {
			$content = '';
			switch ( $type ) {
				case 'select-list':
					$answer_content = '';
					if ( !empty($answer_meta_type_def['options']['content']) ) {
						foreach ( $answer_meta_type_def['options']['content'] as $index => $value ) {
							if ( !empty($value) ) {
								$answer_content .= $this->display( 'wpes_answer_options_management_type_select-list-item', array(
									'ANSWER_TYPE_SELECT_LIST_INPUT' => $this->display( 'wpes_answer_options_management_type_select-list-item-new', array(
										'SELECT_LIST_ITEM_LABEL' => $value,
									) ),
								) );
							}
						}
					}

					$is_multiple = !empty($answer_meta_type_def['options']['content_options']) && !empty($answer_meta_type_def['options']['content_options']['subtype']) && ($answer_meta_type_def['options']['content_options']['subtype'] == 'multiple') ? true : false;
					$use_empty_value = !empty($answer_meta_type_def['options']['content_options']) && !empty($answer_meta_type_def['options']['content_options']['empty_value']) ? true : false;
					$content .= $this->display( 'wpes_answer_options_management_type_' . $type, array(
						'SELECT_LIST_ITEM_CONTAINER' 		=> $answer_content,
						'ANSWER_TYPE' 						=> $type,
						'ANSWER_TYPE_LIST_MULTIPLE_STATE' 	=> $is_multiple ? ' checked="checked"' : '',
						'ANSWER_TYPE_LIST_EMPTY_VALUE_STATE' 	=> $use_empty_value ? ' checked="checked"' : '',
						'ANSWER_TYPE_LIST_EMPTY_VALUE_CLASS' 	=> $is_multiple ? ' wpes-hide' : '',
						'ANSWER_TYPE_OPTIONS_CONTENT_CLASS' 	=> !$use_empty_value ? ' wpes-hide' : '',
						'ANSWER_TYPE_OPTIONS_CONTENT' 	=> $use_empty_value ? $answer_meta_type_def['options']['content_options']['empty_value'] : '',
					) );

					$content .= $this->display( 'wpes_answer_options_management_type_select-list-item-actions-add', array(
						'ANSWER_TYPE_SELECT_LIST_INPUT' => $this->display( 'wpes_answer_options_management_type_select-list-item-new', array(
							'SELECT_LIST_ITEM_LABEL' => '',
							'ANSWER_TYPE' 			=> $type,
						) ),
						'NEW_ITEM_FOR_SELECT_LIST_TPL' => $this->display( 'wpes_answer_options_management_type_select-list-item', array(
							'ANSWER_TYPE_SELECT_LIST_INPUT' => $this->display( 'wpes_answer_options_management_type_select-list-item-new', array(
								'SELECT_LIST_ITEM_LABEL' => '',
							) ),
							'ANSWER_TYPE' 					=> $type,
						) ),
					) );
					break;
				default:
					$content = $this->display( 'wpes_answer_options_management_type_' . $type, array(
						'ANSWER_TYPE_OPTIONS_CONTENT_STATE' => !empty($answer_meta_type_def) && ( $type == $answer_meta_type_def['type'] ) && !empty($answer_meta_type_def['options']) && !empty($answer_meta_type_def['options']['content']) ? ' checked="checked"' : '',
						'ANSWER_TYPE_OPTIONS_CONTENT_CLASS' => !empty($answer_meta_type_def) && ( $type == $answer_meta_type_def['type'] ) && !empty($answer_meta_type_def['options']) && !empty($answer_meta_type_def['options']['content']) ? '' : ' wpes-hide',
						'ANSWER_TYPE_OPTIONS_CONTENT'		=> !empty($answer_meta_type_def) && ( $type == $answer_meta_type_def['type'] ) && !empty($answer_meta_type_def['options']) && !empty($answer_meta_type_def['options']['content']) ? $answer_meta_type_def['options']['content'] : '',
						'ANSWER_TYPE' 						=> $type,
					) );
					break;
			}

			$answer_type_list .= $this->display( 'wpes_answer_management_type_item', array(
				'ANSWER_TYPE' 					=> $type,
				'ANSWER_TYPE_LABEL' 			=> $type_def['name'],
				'ANSWER_TYPE_STATE' 			=> !empty($answer_meta_type_def) && ( $type == $answer_meta_type_def['type'] ) ? ' checked="checked"' : '',
				'ANSWER_TYPE_MORE_CLASS' 		=> $type_def['has_conf'] ? ' wpes-answer-type-has-conf' : '',
				'ANSWER_TYPE_OPTIONS_CONTAINER' => $type_def['has_conf'] ? $this->display( 'wpes_answer_management_type_item_options', array(
					'ANSWER_TYPE_OPTIONS_STATE'		=> !empty($answer_meta_type_def) && ( $type == $answer_meta_type_def['type'] ) ? '' : ' wpes-hide',
					'ANSWER_TYPE'					=> $type,
					'ANSWER_TYPE_OPTIONS'	 		=> $content,
				) ) : '',
			) );
		}

		echo $this->display( 'wpes_answer_management_type', array( 'ANSWER_TYPE_LIST_CONTAINER' => $answer_type_list ) );
	}

	/**
	 * Display the metabox content allowing to manage options for an answer
	 *
	 * @param object $post The complete definition of current edited post
	 */
	function answer_options( $post ) {
		$answer_meta_options = get_post_meta( $post->ID, '_wpes_answer_options', true);



		$tpl_component = array(
// 			'ANSWER_FINAL_STATE_DEFINITION' => '',
			'ANSWER_MNGT_OPTIONS_UNIT' => !empty($answer_meta_options) && !empty($answer_meta_options['unit']) ? $answer_meta_options['unit'] : '',
			'ANSWER_MNGT_OPTIONS_COLOR' => !empty($answer_meta_options) && !empty($answer_meta_options['color']) ? $answer_meta_options['color'] : $this->default_color,
		);

		echo $this->display( 'wpes_answer_management_options', $tpl_component );
	}

	/**
	 * Insert the default answers on plugin activation
	 */
	function create_default_answers() {
		global $wpdb;

		$wpes_options = get_option( 'wpes_options' );
		if ( empty($wpes_options) || empty($wpes_options['default_answer_created']) ) {
			$default_answers = array();
			$default_answers[] = array(
				'main' => array( 'ID' => null , 'post_title' => __('Yes', 'wp_easy_survey'), 'post_type' => $this->post_type, 'post_status' => 'publish', 'menu_order' => 1, ),
				'type_def' => array( 'type' => 'text-display' ),
				'options' => array( 'color' => '0000FF' ),
			);
			$default_answers[] = array(
				'main' => array( 'ID' => null , 'post_title' => __('No', 'wp_easy_survey'), 'post_type' => $this->post_type, 'post_status' => 'publish', 'menu_order' => 2, ),
				'type_def' => array( 'type' => 'text-display' ),
				'options' => array( 'color' => '990000' ),
			);
			$default_answers[] = array(
				'main' => array( 'ID' => null , 'post_title' => __('NA', 'wp_easy_survey'), 'post_type' => $this->post_type, 'post_status' => 'publish', 'menu_order' => 3, ),
				'type_def' => array( 'type' => 'text-display' ),
				'options' => array( 'color' => 'FFFF00' ),
			);
			$default_answers[] = array(
				'main' => array( 'ID' => null , 'post_title' => __('NC', 'wp_easy_survey'), 'post_type' => $this->post_type, 'post_status' => 'publish', 'menu_order' => 4, ),
				'type_def' => array( 'type' => 'text-display' ),
				'options' => array( 'color' => '993300' ),
			);
			$default_answers[] = array(
				'main' => array( 'ID' => null , 'post_title' => __('Percentage', 'wp_easy_survey'), 'post_type' => $this->post_type, 'post_status' => 'publish', 'menu_order' => 5, ),
				'type_def' => array( 'type' => 'percentage' ),
				'options' => array( 'color' => 'C0C0C0', 'unit' => '%', /*'final_state' => array(
					'not_answered' => array(
						'=' => array(
							'0',
						),
					),
					'in_progress' => array(
						'>=' => array(
							'1',
						),
						'<=' => array(
							'99',
						),
					),
					'answered' => array(
						'=' => array(
							'100',
						),
					),
				)*/ ),
			);

			foreach ( $default_answers as $answer ) {
				$answer_id = wp_insert_post( $answer['main'] );
				unset($answer['main']);
				foreach ( $answer as $meta_key => $meta_value ) {
					add_post_meta( $answer_id, '_wpes_answer_' . $meta_key, $meta_value );
				}
			}

			$wpes_options['default_answer_created'] = current_time('mysql', 0);
			update_option( 'wpes_options', $wpes_options);
		}

		/**	Transfert answer from element to issue	*/
		$query = $wpdb->prepare( "SELECT * FROM {$wpdb->postmeta} WHERE meta_key LIKE ( '%%%s%%' )", array( '_wpes_survey_answers_survey-' ) );
		$saved_answers = $wpdb->get_results( $query );
		if ( !empty( $saved_answers ) ) {
			foreach ( $saved_answers as $answer ) {
				$answer_def = explode( "_issue-", $answer->meta_key );
				update_post_meta( $answer_def[ 1 ], '_wpes_issue_final_answer', unserialize( $answer->meta_value ) );
				delete_post_meta( $answer->post_id, $answer->meta_key  );
			}
		}
	}


	/**
	 * Display answers from a given list
	 *
	 * @param object $answers_list Answers to display
	 * @param array $issue_args Different parameters to customize answers' output for each issue if specific parameters have been defined
	 *
	 * @return string The html output with the difeerent answers and
	 */
	function display_available_answers( $answers_list, $issue_args = array() ) {
		$output = '';

		foreach ( $answers_list as $answer_id => $answer) {
			$multiple = false;
			$tpl_component = array(
				'ISSUE_ID' => $issue_args['id'],
				'ANSWER_ID' => $answer_id,
				'ANSWER_INPUT_TYPE' => !empty($multiple) ? 'checkbox' : 'radio',
			);

			$answer_meta_def = $answer['options']['type'];
			switch ( $answer_meta_def['type'] ) {
				case 'percentage':
					$label = '';
					$detail = $this->display( 'wpes_survey_issue_answer_input_percentage', $tpl_component );
				break;

				case 'short-text':
					$label = !empty($answer_meta_def['options']) && !empty($answer_meta_def['options']['content']) ? $answer_meta_def['options']['content'] : '';
					$detail = $this->display( 'wpes_survey_issue_answer_input_short_text', $tpl_component );
				break;

				case 'long-text':
					$label = !empty($answer_meta_def['options']) && !empty($answer_meta_def['options']['content']) ? $answer_meta_def['options']['content'] : '';
					$detail = $this->display( 'wpes_survey_issue_answer_input_long_text', $tpl_component );
				break;

				case 'select-list':
					$label = '';
					if ( !empty($answer_meta_def['options']) && !empty($answer_meta_def['options']['content']) ) {
						$select_list_detail = '';
						if ( !empty($answer_meta_def['options']['content_options']) && !empty($answer_meta_def['options']['content_options']['empty_value']) && empty($answer_meta_def['options']['content_options']['subtype']) ) {
							$select_list_detail = $select_list_detail .= $this->display( 'wpes_survey_issue_answer_input_select_list_item', array(
								'ANSWER_SELECT_LIST_ITEM_VALUE' => '',
								'ANSWER_SELECT_LIST_ITEM_LABEL' => $answer_meta_def['options']['content_options']['empty_value'],
							) );
						}
						foreach ( $answer_meta_def['options']['content'] as $index => $value ) {
							if ( !empty($value) ) {
								$select_list_detail .= $this->display( 'wpes_survey_issue_answer_input_select_list_item', array(
									'ANSWER_SELECT_LIST_ITEM_VALUE' => $value,
									'ANSWER_SELECT_LIST_ITEM_LABEL' => $value,
								) );
							}
						}
						$detail = $this->display( 'wpes_survey_issue_answer_input_select_list', array(
							'ANSWER_FINAL_SELECT_LIST_ITEMS' => $select_list_detail,
							'ANSWER_SELECT_LIST_STATE' => !empty($answer_meta_def['options']['content_options']) && !empty($answer_meta_def['options']['content_options']['subtype']) && ($answer_meta_def['options']['content_options']['subtype'] == 'multiple') ? ' multiple="multiple"' : '',
						) );
					}
				break;

				default:
					$label = $answer['name'];
					$detail = '';
				break;
			}

			$output .= $this->display( 'wpes_survey_issue_answer_input', array_merge( array(
				'ANSWER_LABEL' => $label,
				'ANSWER_DETAIL' => $detail,
				'FINAL_ANSWER_UNIT' => !empty($answer['options']['options']) && !empty($answer['options']['options']['unit']) ? $answer['options']['options']['unit'] : '',
				'ANSWER_OUTPUT_TYPE' => $answer_meta_def['type'],
			), $tpl_component ) );
		}

		return $output;
	}


	/**
	 * Get the last or a list of last answers for a given element where a survey is present
	 *
	 * @param integer $post_ID The element identifier that the survey is associated to
	 * @param integer $issue_ID Optionnal. An issue identifier if we want to get the last answer for a given issue
	 *
	 * @return array
	 */
	function get_last_answer_for_issue( $issue_ID ) {
		global $wpdb;

		$the_tab = array();

		$query = $wpdb->prepare( "SELECT * FROM {$wpdb->postmeta} WHERE meta_key = %s AND post_id = %d ORDER BY meta_id", array( '_wpes_issue_final_answer', $issue_ID ) );
		$answers = $wpdb->get_results( $query );
		if ( !empty( $answers ) ) {
			foreach ( $answers as $answer ) {
				$the_tab[ $answer->post_id ][ 'count' ] = count( $answers );
				$the_tab[ $answer->post_id ][ 'content' ] = unserialize( $answer->meta_value );
			}
		}

		return $the_tab;
	}

	/**
	 * Display the last answer for a given issue in a survey
	 *
	 * @param integer $post_ID The element that issue and answer are associated to
	 * @param integer $issue_ID The issue identifier to get answer for
	 *
	 * @return string The html output for issue's answer
	 */
	function display_final_survey_last_answer( $post_ID, $survey_id, $issue_ID ) {
		$output = '';

		$current_answer = $this->get_last_answer_for_issue( $issue_ID );
		if ( !empty($current_answer) ) {
			$output .= $this->display_an_answer( $current_answer[ $issue_ID ], $issue_ID, $survey_id, $post_ID );
		}

		return $output;
	}

	/**
	 * Display a given answer
	 *
	 * @param integer $post_ID The element identifier that the answer is associated to
	 * @param array $answer_detail The details of given nswer to output
	 * @param string $from Optionnal A location form where the function is called. Allow to avoid some display in someplaces
	 *
	 * @return string A complete html output for the asked answer
	 */
	function display_an_answer( $given_answer_content, $issue_ID, $survey_id, $element_id, $from = '' ) {
		$response = '';

		/**	Get the different answers' state definition	*/
		$answers_type = unserialize( WPES_ANSWER_TYPE );

		$the_answer = '   ';
		$answer_def_for_issue = get_post_meta( $issue_ID, '_wpes_issue_final_args', true);
		foreach ( $given_answer_content['content']['answers'] as $answer ) {
			$answer_def = !empty($answer_def_for_issue[ 'answers' ]) && !empty($answer_def_for_issue[ 'answers' ][ $answer['main_choice'] ]) ? $answer_def_for_issue[ 'answers' ][ $answer['main_choice'] ] : null;
			if ( !empty($answer_def) ) {
				$the_answer .= ((!empty($answer['details']) || (isset($answer['details']) && ($answer['details'] == '0'))) ? $answer['details'] : $answer_def[ 'name' ]) . (!empty($answer_def['options']['options']) && !empty($answer_def['options']['options']['unit']) ? $answer_def['options']['options']['unit'] : '') . ' / ';
			}
		}
		$user_data = get_userdata( $given_answer_content['content']['answer_user'] );
		$issue_history_load_nonce = wp_create_nonce( 'wpes-issue-history-load' );
		$answer_state = $this->check_answer_state( $issue_ID, $given_answer_content['content']['answers'] );

		ob_start();
		require( $this->get_template_part( "backend", "final-survey/answer", "given") );
		$response .= ob_get_contents();
		ob_end_clean();

		return $response;
	}

	/**
	 * Check and return the state of an answer - Allows to know if the answer is completly answered or not
	 *
	 * @param integer $issue_ID The issue identifier to get available answers definition
	 * @param array $given_answers The different given answers
	 *
	 * @return string The state of current answer for given issue
	 */
	function check_answer_state( $issue_ID, $given_answers ) {
		/**	Get issue complete list of available answers	*/
		$answer_def_for_issue = get_post_meta( $issue_ID, '_wpes_issue_final_args', true);

		$given_answers = $given_answers[0];

		$current_state = 'answered';
		/**	Define answer state for specific issues	*/
		if ( (!empty( $given_answers['details'] ) || ( isset( $given_answers['details'] ) && ( $given_answers['details'] == 0 )) ) && !empty($answer_def_for_issue[ 'answers' ][ $given_answers['main_choice'] ][ 'options' ][ 'type' ]) && !empty($answer_def_for_issue[ 'answers' ][ $given_answers['main_choice'] ][ 'options' ][ 'type' ][ 'type' ])
				&& ($answer_def_for_issue[ 'answers' ][ $given_answers['main_choice'] ][ 'options' ][ 'type' ][ 'type' ] == 'percentage') ) {
			if ( $given_answers['details'] == '0' ) {
				$current_state = 'not_answered';
			}
			else if ( ( $given_answers['details'] >= '1' ) && ( $given_answers['details'] <= '99' )) {
				$current_state = 'in_progress';
			}
		}

		return $current_state;
	}


	/**
	 * AJAX - Load the complete answer's history for a given issue
	 */
	function ajax_load_issue_answer_history() {
		check_ajax_referer( 'wpes-issue-history-load', 'wpes-ajax-issue-history-view-nonce' );
		global $wpdb;
		$output = '';

		$query = $wpdb->prepare( "SELECT * FROM {$wpdb->postmeta} WHERE meta_key = %s AND post_id = %d ORDER BY meta_id DESC", array( '_wpes_issue_final_answer', $_REQUEST['issue_id'] ) );
		$answers = $wpdb->get_results( $query );
		if ( !empty( $answers ) ) {
			foreach ( $answers as $answer_index => $answer ) {
				if ( $answer_index > 0) {
					$output .= $this->display_an_answer( array(
						'count' => 0,
						'content' => unserialize( $answer->meta_value ),
						'meta_id' => $answer->meta_id
					), $_REQUEST['issue_id'], $_REQUEST['survey_id'], $_REQUEST['post_ID'], 'history' );
				}
			}
		}

		echo $output;
		die();
	}

	/**
	 * AJAX - Take an existing answer and restore it
	 */
	function ajax_reuse_answer() {
		check_ajax_referer( 'wpes-answer-reuse', 'wpes-ajax-answer-reuse-nonce' );
		$output = '';

		$meta_to_copy = get_post_meta_by_id( $_POST['issue_ID'] );
		$survey_and_issue = explode('_issue-', str_replace('_wpes_survey_answers_survey-', '', $meta_to_copy->meta_key));
		$survey_id = $survey_and_issue[0];
		$issue_id = $survey_and_issue[1];

		/**	Store automatic answer detail	*/
		$meta_to_copy->meta_value['answer_user'] = get_current_user_id();
		$meta_to_copy->meta_value['answer_date'] = current_time( 'mysql', 0);
		add_post_meta($meta_to_copy->post_id, $meta_to_copy->meta_key, $meta_to_copy->meta_value);

		echo json_encode( array( 'output' => $this->display_final_survey_last_answer( $meta_to_copy->post_id, $survey_id, $issue_id ), 'issue_id' => $issue_id, 'state_message' => __('Selected answer is used again', 'wp_easy_survey'), ) );
		die();
	}

	/**
	 * AJAX - Save answers for an issue from final associated element
	 */
	function ajax_save_answer() {
		check_ajax_referer( 'wpes-answer-save', 'wpes-ajax-answer-save-nonce' );

		global $wpes_survey;

		$response = array(
			'status' => true,
			'state_message' => __('You\'re answer have been saved succesfully', 'wp_easy_survey'),
			'output' => '',
			'output_stats' => '',
			'issue_id' => $_POST['issue_ID'],
			'survey_ID' => $_POST['survey_ID'],
		);

		$expiration = '';
		$answer_details = array();

		/**	Get answer definition	*/
		if ( is_array($_POST['answers']) ) {
			$index = 0;
			foreach ( $_POST['answers'] as $answer ) {
				$answer_details[ 'answers' ][ $index ][ 'main_choice' ] = $answer[0];
				$answer_details[ 'answers' ][ $index ][ 'details' ] = !empty($answer[1]) || (isset($answer[1]) && ($answer[1] == '0')) ? $answer[1] : '';

				$index++;
			}
		}

		/**	Store user defined answer detail	*/
		if ( !empty( $_POST['answer_details'] ) ) {
			foreach ( $_POST['answer_details'] as $detail ) {
				$answer_details[ $detail[0] ] = $detail[1];

				if ( $detail[0] == 'expiration-date' ) {
					$expiration = $detail[1];
				}
			}
		}

		/**	Save user answer	*/
		$this->save_the_answer( $_POST['post_ID'], $_POST['issue_ID'], $answer_details, $_POST['survey_ID'] );

		/** In case expiration date is filled : save a custon meta for current element with different expiratin date and issues' answer */
		$current_expiration = get_post_meta( $_POST['post_ID'], '_wpes_survey_expiration_survey', true);
		if ( !empty( $expiration ) ) {
			$current_expiration[ $expiration ][] = $_POST['issue_ID'];
			foreach ( $current_expiration as $expiration_date => $issues_list_for_date ) {
				if ( in_array( $_POST['issue_ID'], $issues_list_for_date ) ) {
					unset( $current_expiration[ $expiration_date ][ key($_POST['issue_ID']) ] );
				}
			}
		}
		else {
			if ( !empty($current_expiration) && is_array($current_expiration) ) {
				foreach ( $current_expiration as $expiration_date => $issues_list_for_date ) {
					foreach ( $issues_list_for_date as $issue_key => $issue_id ) {
						if ( $issue_id == $_POST['issue_ID'] ) {
							unset( $current_expiration[ $expiration_date ][ $issue_key ] );
						}
					}
					if ( count( $current_expiration[ $expiration_date ] ) <= 0 ) {
						unset( $current_expiration[ $expiration_date ] );
					}
				}
			}
		}
		update_post_meta( $_POST['post_ID'], '_wpes_survey_expiration_survey', $current_expiration);

		$response['output'] = $this->display_final_survey_last_answer( $_POST['post_ID'], $_POST['survey_ID'], $_POST['issue_ID'] );

		$response['output_stats'] = $wpes_survey->display_final_survey_statistics_content( $_POST[ 'survey_ID' ], $_POST[ 'answer_total_number' ], unserialize( $_POST[ 'issues_with_answer' ] ) );

		echo json_encode( $response );
		die();
	}


	/**
	 * Save the user answer with datas not send with the form
	 *
	 * @param integer $element_ID The current element identifier to save answer for
	 * @param integer $issue_ID The issue identifier user has answered
	 * @param integer $answer The given answer
	 */
	function save_the_answer( $element_ID, $issue_ID, $answer, $survey_id ) {
		/**	Store automatic answer detail	*/
		$answer['answer_user'] = get_current_user_id();
		$answer['answer_date'] = current_time( 'mysql', 0);

		/**	Save user answer	*/
		add_post_meta( $issue_ID, '_wpes_issue_final_answer', $answer );
	}

}

?>