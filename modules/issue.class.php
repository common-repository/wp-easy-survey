<?php
/**
 * File containing component definition for issues management
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
 * Define the component to manage issues
 *
 * @author Eoxia developpement team <dev@eoxia.com>
 * @version 0.1
 * @package wp_easy_survey
 * @subpackage core
 */
class wpes_issue extends wpes_display {

	var $template_dir = WPES_TPL_DIR;

	/**	Define the custom post type for issues	*/
	public $post_type = 'wpes_issues';
	public $post_type_final = 'wpes_final_issues';

	/**	Define the custom post type prefix for issues	*/
	public $post_type_prefix = 'Q';

	/**	Define a variable for answer management	*/
	public $answers;

	/**
	 *	Instanciate issue management component
	 */
	function __construct() {
		/**	Initialise display component	*/
		parent::__construct();

		/**	Call the main definition for custom post type	*/
		$this->definition();

		/**	Add different support for ajax	*/
		add_action('wp_ajax_wpes-ajax-survey-element-edit', array( &$this, 'get_element_to_edit') );
		add_action('wp_ajax_wpes-ajax-survey-item-save', array( &$this, 'save') );
		add_action('wp_ajax_wpes-ajax-survey-item-association', array( &$this, 'save_association') );
		add_action('wp_ajax_wpes-ajax-survey-item-add', array( &$this, 'add') );
		add_action('wp_ajax_wpes-ajax-survey-item-delete', array( &$this, 'delete') );
		add_action('wp_ajax_wpes-ajax-survey-item-dissociate', array( &$this, 'dissociate') );
		add_action('wp_ajax_wpes-ajax-survey-item-associate', array( &$this, 'associate') );
		add_action('wp_ajax_wpes-ajax-load-children', array( &$this, 'load_children') );
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
	 * Define and create the custom post type for issues management
	 *
	 * @see register_post_type()
	 * @see register_taxonomy()
	 */
	function definition() {
		$labels = array(
			'name'                => _x( 'Issues', 'Post Type General Name', 'wp_easy_survey' ),
			'singular_name'       => _x( 'Issue', 'Post Type Singular Name', 'wp_easy_survey' ),
			'menu_name'           => __( 'Issue', 'wp_easy_survey' ),
			'parent_item_colon'   => __( 'Parent Issue:', 'wp_easy_survey' ),
			'all_items'           => __( 'All Issues', 'wp_easy_survey' ),
			'view_item'           => __( 'View Issue', 'wp_easy_survey' ),
			'add_new_item'        => __( 'Add New Issue', 'wp_easy_survey' ),
			'add_new'             => __( 'New Issue', 'wp_easy_survey' ),
			'edit_item'           => __( 'Edit Issue', 'wp_easy_survey' ),
			'update_item'         => __( 'Update Issue', 'wp_easy_survey' ),
			'search_items'        => __( 'Search issues', 'wp_easy_survey' ),
			'not_found'           => __( 'No issues found', 'wp_easy_survey' ),
			'not_found_in_trash'  => __( 'No issues found in Trash', 'wp_easy_survey' ),
		);
		$args = array(
			'label'               	=> __( 'issues', 'wp_easy_survey' ),
			'description'         	=> __( 'Issues management', 'wp_easy_survey' ),
			'labels'              	=> $labels,
			'supports'            	=> array( 'title', 'thumbnail'),
			'hierarchical'        	=> true,
			'public'              	=> false,
			'show_ui'             	=> true,
			'show_in_menu'        	=> false,
			'show_in_nav_menus'   	=> false,
			'show_in_admin_bar'   	=> false,
			'menu_position'       	=> 5,
			'can_export'          	=> true,
			'has_archive'         	=> true,
			'exclude_from_search' 	=> true,
			'publicly_queryable'  	=> false,
			'query_var'           	=> 'issue',
			'capability_type'     	=> 'post',
			'rewrite'     		  	=> false,
		);
		register_post_type( $this->post_type, $args );

		$labels = array(
			'name'                => _x( 'Final Issues', 'Post Type General Name', 'wp_easy_survey' ),
			'singular_name'       => _x( 'Final Issue', 'Post Type Singular Name', 'wp_easy_survey' ),
			'menu_name'           => __( 'Final Issue', 'wp_easy_survey' ),
			'parent_item_colon'   => __( 'Parent Final Issue:', 'wp_easy_survey' ),
			'all_items'           => __( 'All Final Issues', 'wp_easy_survey' ),
			'view_item'           => __( 'View Final Issue', 'wp_easy_survey' ),
			'add_new_item'        => __( 'Add New Final Issue', 'wp_easy_survey' ),
			'add_new'             => __( 'New Final Issue', 'wp_easy_survey' ),
			'edit_item'           => __( 'Edit Final Issue', 'wp_easy_survey' ),
			'update_item'         => __( 'Update Final Issue', 'wp_easy_survey' ),
			'search_items'        => __( 'Search Final issues', 'wp_easy_survey' ),
			'not_found'           => __( 'No Final issues found', 'wp_easy_survey' ),
			'not_found_in_trash'  => __( 'No Final issues found in Trash', 'wp_easy_survey' ),
		);
		$args = array(
			'label'               	=> __( 'Final issues', 'wp_easy_survey' ),
			'description'         	=> __( 'Final Issues management', 'wp_easy_survey' ),
			'labels'              	=> $labels,
			'supports'            	=> array( 'title' ),
			'hierarchical'        	=> true,
			'public'              	=> false,
			'show_ui'             	=> true,
			'show_in_menu'        	=> false,
			'show_in_nav_menus'   	=> false,
			'show_in_admin_bar'   	=> false,
			'menu_position'       	=> 5,
			'can_export'          	=> true,
			'has_archive'         	=> true,
			'exclude_from_search' 	=> true,
			'publicly_queryable'  	=> false,
			'query_var'           	=> 'final_issue',
			'capability_type'     	=> 'post',
			'rewrite'     		  	=> false,
		);
		register_post_type( $this->post_type_final, $args );
	}

	/**
	 * Retrieve existing issues for a given element
	 *
	 * @param integer $parent_id Element we have to get children issues' for
	 *
	 * @return WP_Query Issues' list for the given element
	 */
	function get_issues( $parent_id, $meta_compare = '', $preview = true ) {
		$associated_item = null;
		$query_post_args = array(
			'post_type' 		=> $preview ? $this->post_type : $this->post_type_final,
			'posts_per_page' 	=> -1,
			'orderby' 			=> 'menu_order',
			'order' 			=> 'ASC',
			'post_status'		=> array( 'publish'/* , 'pending', 'draft', 'future', */ ),
		);

		if ( !empty( $parent_id ) ) {
		 	$query_post_args['meta_key']	= '_wpes_node_parent_id';
			$query_post_args['meta_value'] 	= $parent_id;
			if ( !empty($meta_compare) ) {
				$query_post_args['meta_compare'] = $meta_compare;
			}
		}

		$associated_item = new WP_Query( $query_post_args );

		return $associated_item;
	}

	/**
	 * Define the form ouput allowing to edit an issue
	 *
	 * @param integer $post_ID The current issue to edit
	 *
	 * @return string The complete form html output
	 */
	function display_item_edition_form( $post_ID = null ) {
		global $wpdb;

		$tpl_component = array();
		$tpl_component['ITEM_SAVE_ACTION'] = 'save';
		$tpl_component['ITEM_SAVE_NONCE'] = wp_create_nonce( 'wpes-survey-node-save' );
		$tpl_component['ITEM_POST_ID'] = $post_ID;
		$tpl_component['ITEM_NODE_LOCALISATION'] = (!empty($_GET) && !empty($_GET['post_parent_localisation'])) ? $_GET['post_parent_localisation'] : '';

		$current_edited_post = null;
		if ( !empty($post_ID) ) {
			$current_edited_post = get_post( $post_ID );
		}
		else {
			$query = $wpdb->prepare( "SHOW COLUMNS FROM {$wpdb->posts}", array() );
			$columns = $wpdb->get_results( $query );
			foreach ( $columns as $column_detail ) {
				$current_edited_post[$column_detail->Field] = '';
			}
			$current_edited_post['post_type'] = $this->post_type;
		}

		$tpl_component['ITEM_PARENT_ID'] = (!empty($_GET) && !empty($_GET['post_parent'])) ? $_GET['post_parent'] : '';

		if ( !empty( $current_edited_post ) ) {
			foreach ( $current_edited_post as $post_field_name => $post_field_value ) {
				$tpl_component['ITEM_' . strtoupper( $post_field_name )] = $post_field_value;

				if ( $post_field_name == 'post_content' ) {
					$tpl_component['ITEM_' . strtoupper( $post_field_name ) . '_DATA'] = $post_field_value;
					ob_start();
					wp_editor( $post_field_value, 'wpes-issue-custom-editor', array('media_buttons' => false, 'textarea_name' => 'wpes_issue[post_content]') );
					$tpl_component['ITEM_' . strtoupper( $post_field_name )] = ob_get_contents();
					ob_end_clean();
				}
			}
		}

		$issue_final_args = get_post_meta( $post_ID, '_wpes_issue_final_args', true );
		$tpl_component['ISSUE_ASSOCIATED_ANSWERS_CONTAINER'] = '';
		$query = $wpdb->prepare( "SELECT COUNT(post_id) FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = %d", '_wpes_node_parent_id', $post_ID );
		$number_of_children = $wpdb->get_var( $query );
		if ( $number_of_children <= 0 ) {
			$tpl_component['ISSUE_ASSOCIATED_ANSWERS'] = '';
			$query_post_args = array(
				'post_type' 		=> $this->answers->post_type,
				'posts_per_page' 	=> -1,
				'orderby' 			=> 'menu_order',
				'order' 			=> 'ASC',
				'post_status'		=> array( 'publish'/* , 'pending', 'draft', 'future', */ ),
			);
			$available_issues = new WP_Query( $query_post_args );
			if ( $available_issues->have_posts() ) {
				foreach ( $available_issues->posts as $answers ) {
					$sub_tpl_component = array();
					foreach ( $answers as $answer_def_key => $answer_def_value ) {
						$sub_tpl_component['ANSWER_' . strtoupper( $answer_def_key )] = $answer_def_value;
					}
					$sub_tpl_component['ANSWER_FOR_ISSUE_STATE'] = '';
					if ( empty($issue_final_args) || ( !empty($issue_final_args['answers']) && array_key_exists($answers->ID, $issue_final_args['answers']) ) ) {
						$sub_tpl_component['ANSWER_FOR_ISSUE_STATE'] = ' checked="checked"';
					}
					$tpl_component['ISSUE_ASSOCIATED_ANSWERS'] .= $this->display( 'wpes_answer_item_for_issue_association', $sub_tpl_component);
				}
			}
			wp_reset_query();
			$tpl_component['ISSUE_ASSOCIATED_ANSWERS_CONTAINER'] = $this->display( 'survey_edition_form_content_answer_list', $tpl_component);
		}

		return $this->display( 'survey_item_form', array_merge( $tpl_component, array( 'ITEM_FORM_CONTENT' => $this->display( 'survey_edition_form_content', $tpl_component ) ) ) );
	}

	/**
	 * Retrieve the survey component list, and display them into a box with possibilities to drag and drop for ordering
	 *
	 * @param integer $post_parent The current element id (Can be a survey or an issue) we have to get data for
	 * @return string Datas contained into current element (survey or issue)
	 */
	function read_survey_node_list( $survey_items_list, $parent_id = '', $list_children = true ) {
		$output = '';

		if ( !empty($survey_items_list) ) {
			ob_start();
			require( $this->get_template_part( "backend", "nestable_tree/node_container" ) ) ;
			$output = ob_get_contents();
			ob_end_clean();
		}

		return $output;
	}

	/**
	 * Display a node in a tree
	 *
	 * @param object $node The node definition we want to display
	 *
	 * @return string The complete html output for node display
	 */
	function display_survey_node( $node ) {
		$output = '';

		$prefix = "#";
		$node_parent_id = get_post_meta( $node->ID, '_wpes_node_parent_id', true );

		$node_adition_nonce = wp_create_nonce( 'wpes-survey-node-adition' );
		$node_edition_nonce = wp_create_nonce( 'wpes-item-edition' );
		$node_name = $prefix . $node->ID . ' - ' . $node->post_title;

		ob_start();
		require( $this->get_template_part( "backend", "nestable_tree/node_content" ) );
		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}

	/**
	 * Ajax action - Get an issue form for edition
	 */
	function get_element_to_edit() {
		check_ajax_referer( 'wpes-item-edition', 'wpes-ajax-survey-item-edition-nonce' );

		echo $this->display_item_edition_form( !empty($_GET) && !empty($_GET['post_id']) ? $_GET['post_id'] : null );
		die();
	}

	/**
	 * Ajax action - Add a new issue. Adding the new issue is done automatically and then display the edition form for the created issue
	 */
	function add() {
		check_ajax_referer( 'wpes-survey-node-adition', 'wpes-ajax-survey-item-addition-nonce' );

		echo $this->display_item_edition_form( );
		die();
	}

	/**
	 * Ajax action - Save information send with the issue's edition form
	 */
	function save() {
		check_ajax_referer( 'wpes-survey-node-save', 'wpes-ajax-survey-item-save-nonce' );
		global $wp_error,
			   $wpdb;

		/**	Get current item parent in order to have information about element already existing	*/
		$parent_issue_list = $this->get_issues( $_POST['wpes_survey_item_parent_ID'] );

		$ajax_response = array(
			'status'               => false,
			'status_message'       => __('An error occured while attempting to save item.', 'wp_easy_survey'),
			'output'               => '',
			'operation'            => '',
			'item-id'              => '',
			'item-parent-id'       => $_POST['wpes_survey_item_parent_ID'],
			'item-parent-children' => count( $parent_issue_list->posts ),
			'location'             => $_POST['wpes_survey_item_localisation'],
		);

		$new_post = $_POST['wpes_survey_item'];
		$new_post['post_status'] = 'publish';
		if ( !empty( $_POST['wpes_survey_item']['ID'] ) ) {
			$new_post_id = wp_update_post( $new_post, $wp_error );
		}
		else {
			$query = $wpdb->prepare( "SELECT (MAX( P.menu_order ) + 1 ) AS MAX_MENU_ORDER FROM {$wpdb->posts} AS P INNER JOIN {$wpdb->postmeta} AS PM ON (PM.post_id = P.ID) WHERE PM.meta_key = %s AND PM.meta_value = %d", '_wpes_node_parent_id', $_POST['wpes_survey_item_parent_ID'] );
			$new_post['menu_order'] = $wpdb->get_var( $query );
			$new_post_id = wp_insert_post( $new_post, $wp_error );
		}

		if ( is_int($new_post_id) ) {
			update_post_meta( $new_post_id, '_wpes_node_parent_id', $_POST['wpes_survey_item_parent_ID']);
			$associated_answers = array();
			if ( !empty($_POST['wpes_issue_associated_answers']) ) {
				foreach ( $_POST['wpes_issue_associated_answers'] as $answer_id ) {
					$answer_main_def = get_post( $answer_id );
					$answer_type_def = get_post_meta( $answer_id, '_wpes_answer_type_def', true );
					$answer_options_def = get_post_meta( $answer_id, '_wpes_answer_options', true );
					$associated_answers[$answer_id]['name'] = $answer_main_def->post_title;
					$associated_answers[$answer_id]['options'] = array(
						'type' => $answer_type_def,
						'options' => $answer_options_def,
					);
				}
			}
			update_post_meta( $new_post_id, '_wpes_issue_final_args', array(
				'answers' => $associated_answers,
			));
			$ajax_response['status'] = true;
			$current_element = get_post( $new_post_id );
			if ( !empty( $_POST['wpes_survey_item']['ID'] ) ) {
				$ajax_response['operation'] = 'edit-issue';
				$ajax_response['output'] = $this->display_survey_node( $current_element );
			}
			else {
				$ajax_response['operation'] = 'add-issue';

				if ( in_array( $ajax_response['location'], array( 'root', 'tree' ) ) ) {

					if ( 0 == count( $parent_issue_list->posts ) ) {
						$parent_issue_list = $this->get_issues( $_POST['wpes_survey_item_parent_ID'] );
						$ajax_response['output'] = $this->read_survey_node_list( $parent_issue_list->posts, $_POST['wpes_survey_item_parent_ID'] );
					}
					else {
						$item = $current_element;
						ob_start();
						require( $this->get_template_part( "backend", "nestable_tree/node" ) );
						$ajax_response['output'] = ob_get_contents();
						ob_end_clean();
					}
				}
			}
			$ajax_response['item-id'] = $new_post_id;
			$ajax_response['status_message'] = __('You\'re item has been saved succefully', 'wp_easy_survey');
		}

		echo json_encode( $ajax_response );
		die();
	}

	/**
	 * Ajax action - Delete (Only set post_status to "Trash") an issue
	 */
	function delete() {
		check_ajax_referer( 'wpes-issue-delete', 'wpes-ajax-survey-issue-delete-nonce' );
		global $wp_error;

		$new_post = array( 'ID' => $_POST['post_ID'], 'post_status' => 'trash' );
		$new_post_id = wp_update_post( $new_post, $wp_error );

		$response['status'] = false;
		$response['error'] = $wp_error;
		if ( is_int($new_post_id) ) {
			$response['status'] = true;
			unset($response['error']);
		}

		echo json_encode( $response );
		die();
	}

	/**
	 * Ajax action - Dissociate an issue from another issue or survey
	 */
	function dissociate() {
		check_ajax_referer( 'wpes-issue-dissociate', 'wpes-ajax-survey-issue-dissociate-nonce' );
		$response['status'] = delete_post_meta( $_POST['post_ID'], '_wpes_node_parent_id');

		echo json_encode( $response );
		die();
	}

	/**
	 * Ajax action - Save issue association
	 */
	function save_association() {
		check_ajax_referer( 'wpes-survey-node-save-association', 'wpes-ajax-survey-item-save-nonce' );
		$ajax_response = array(
			'status'         => true,
			'status_message' => __('Selected items has been associated succesfully', 'wp_easy_survey'),
			'output'         => '',
			'operation'      => 'item_association',
			'item-id'        => '',
			'item-parent-id' => $_POST['wpes_survey_item_parent_ID'],
			'location'       => $_POST['wpes_survey_item_localisation'],
		);

		if ( !empty( $_POST['wpes-issue-to-associate'] ) && !empty( $_POST['wpes_survey_item_parent_ID'] ) ) {
			foreach ( $_POST['wpes-issue-to-associate'] as $issue_ID ) {
				$ajax_response['status'] = update_post_meta( $issue_ID, '_wpes_node_parent_id', $_POST['wpes_survey_item_parent_ID']);
			}
		}

		if ( false === $ajax_response['status'] ) {
			$ajax_response['status_message'] = __('An error occured while attempting to save item association', 'wp_easy_survey');
		}

		echo json_encode( $ajax_response );
		die();
	}

	/**
	 * Ajax action - Display form allowing to associate an issue to another issue or survey
	 */
	function associate() {
		check_ajax_referer( 'wpes-survey-node-association', 'wpes-ajax-survey-issue-association-nonce' );
		$output = '';

		$tpl_component = array();
		$tpl_component['ITEM_SAVE_ACTION'] = 'association';
		$tpl_component['ITEM_SAVE_NONCE'] = wp_create_nonce( 'wpes-survey-node-save-association' );
		$tpl_component['ITEM_POST_ID'] = '';
		$tpl_component['ITEM_POST_TYPE'] = $this->post_type;
		$tpl_component['ITEM_NODE_LOCALISATION'] = (!empty($_GET) && !empty($_GET['post_parent_localisation'])) ? $_GET['post_parent_localisation'] : '';

		$tpl_component['ITEM_PARENT_ID'] = (!empty($_GET) && !empty($_GET['post_parent'])) ? $_GET['post_parent'] : '';

		$list = '';
		$orphelan_issue = $this->get_issues( $tpl_component['ITEM_PARENT_ID'], '!=' );
		if ( !empty($orphelan_issue) && $orphelan_issue->have_posts() ) {
			foreach ( $orphelan_issue->posts as $issue) {
				$list .= '<option value="' . $issue->ID . '" >#' . $issue->ID . ' - ' . $issue->post_title . '</option>';
			}
		}

		if ( !empty($list) ) {
			$output = $this->display( 'wpes_item_association_select', array('ITEM_LIST_FOR_ASSOCIATION' => $list));
		}

		echo $this->display( 'survey_item_form', array_merge( $tpl_component, array( 'ITEM_FORM_CONTENT' => $output) ) );
		die();
	}

	/**
	 * Ajax action - Display children nodes for a given node
	 */
	function load_children() {
		$children_list = $this->get_issues( $_POST[ 'node_id' ] );

		$children_output = "";
		if ( !empty($children_list) && $children_list->have_posts() ) {
			foreach ( $children_list->posts as $item ) {
				ob_start();
				require( $this->get_template_part( "backend", "nestable_tree/node" ) );
				$children_output .= ob_get_contents();
				ob_end_clean();
			}
		}

		echo json_encode( array( 'output' => $children_output/*  . '<script type="text/javascript" >initialize_nestable( false );</script>' */, 'item_id' => $_POST[ 'node_id' ], ) );
		die();
	}

}