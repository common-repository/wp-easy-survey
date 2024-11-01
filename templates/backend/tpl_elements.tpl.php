<?php
/**
 * File defining template element for backend element
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

$tpl_element = array();


/**	Issue association form */
ob_start();
?><form id="wpes-survey-item-edition-form" action="<?php echo admin_url('admin-ajax.php'); ?>" method="POST" >
	<input type="hidden" name="action" value="wpes-ajax-survey-item-{WPES_TPL_ITEM_SAVE_ACTION}" />
	<input type="hidden" name="wpes_survey_item[ID]" value="{WPES_TPL_ITEM_POST_ID}" />
	<input type="hidden" name="wpes_survey_item_parent_ID" value="{WPES_TPL_ITEM_PARENT_ID}" />
	<input type="hidden" name="wpes_survey_item[post_type]" value="{WPES_TPL_ITEM_POST_TYPE}" />
	<input type="hidden" name="wpes_survey_item_localisation" value="{WPES_TPL_ITEM_NODE_LOCALISATION}" />
	<input type="hidden" name="wpes-ajax-survey-item-save-nonce" value="{WPES_TPL_ITEM_SAVE_NONCE}" />
{WPES_TPL_ITEM_FORM_CONTENT}
	<input type="submit" id="wpes-survey-item-edition-form-submit" class="button button-primary" value="<?php _e('Save', 'wp_easy_survey'); ?>" />
	<div class="alignright" id="wpes-survey-item-edition-form-save-in-progress" >
		<img class="wpes-hide" src="<?php echo admin_url('images/loading.gif'); ?>" alt="<?php _e('save in progress', 'wp_easy_survey'); ?>" />
		<div class="wpes-hide wpes-survey-item-edition-form-message updated" ></div>
	</div>
</form>
<script type="text/javascript" >
	wp_easy_survey(document).ready(function(){
		jQuery("#wpes-survey-item-edition-form").ajaxForm({
	        dataType: 'json',
	        beforeSubmit: function () {
		        /**	Display loading picture	*/
		        jQuery("#wpes-survey-item-edition-form-save-in-progress img").show();
	        },
	        success: wpes_action_after_survey_item_operation,
		});
		jQuery(".wpes-existing-issue-list").chosen({allow_single_deselect:true});
	});
</script><?php
$tpl_element['survey_item_form']['tpl'] = ob_get_contents();
ob_end_clean();

/**	Issue edition form */
ob_start();
?>	<table class="wpes-issue-edition-form" >
		<tr>
			<td>
				<?php _e('Title', 'wp_easy_survey'); ?>
				<input style="width: 100%;" type="text" name="wpes_survey_item[post_title]" value="{WPES_TPL_ITEM_POST_TITLE}" placeholder="<?php _e('Enter title here', 'wp_easy_survey'); ?>" />
			</td>
		</tr>
		<tr>
			<td>
				<?php _e('Description', 'wp_easy_survey'); ?>
				<textarea id="wpes-issue-custom-editor" name="wpes_survey_item[post_content]" >{WPES_TPL_ITEM_POST_CONTENT_DATA}</textarea>
			</td>
		</tr>
		{WPES_TPL_ISSUE_ASSOCIATED_ANSWERS_CONTAINER}
	</table><?php
$tpl_element['survey_edition_form_content']['tpl'] = ob_get_contents();
ob_end_clean();

ob_start();
?>		<tr>
			<td>
				<?php _e('Available answers', 'wp_easy_survey'); ?> <span class="wpes-issue-edition-helper" ><?php _e('You can chose different answers that will be available for this issue in final survey', 'wp_easy_survey'); ?></span><br/>
				<span class="wpes-mass-action-on-answer-for-issue wpes-check-all-available-answer-in-issue" ><?php _e('Check all', 'wp_easy_survey'); ?></span> / <span class="wpes-mass-action-on-answer-for-issue wpes-uncheck-all-available-answer-in-issue" ><?php _e('Uncheck all', 'wp_easy_survey'); ?></span>
				<ul class="wpes-issue-edition-form-answers" >{WPES_TPL_ISSUE_ASSOCIATED_ANSWERS}</ul>
			</td>
		</tr><?php
$tpl_element['survey_edition_form_content_answer_list']['tpl'] = ob_get_contents();
ob_end_clean();

/**	List registered post type in order to associate survey to those post type -	Item */
ob_start();
?><li><label class="selectit" ><input type="checkbox" name="wpes_issue_associated_answers[]" class="wpes-issue-associated-answers" id="wpes-issue-associated-answers-{WPES_TPL_ANSWER_ID}" value="{WPES_TPL_ANSWER_ID}"{WPES_TPL_ANSWER_FOR_ISSUE_STATE} /> {WPES_TPL_ANSWER_POST_TITLE}</label></li><?php
$tpl_element['wpes_answer_item_for_issue_association']['tpl'] = ob_get_contents();
ob_end_clean();


/**	List registered post type in order to associate survey to those post type - Container	*/
ob_start();
?><ul>{WPES_TPL_REGISTERED_POST_TYPE_LIST}</ul><?php
$tpl_element['wpes_survey_post_type_association_list_container']['tpl'] = ob_get_contents();
ob_end_clean();

/**	List registered post type in order to associate survey to those post type -	Item */
ob_start();
?><li><label class="selectit" ><input type="checkbox" name="wpes-survey-association[]" value="{WPES_TPL_SURVEY_ASSOCIATION_POST_TYPE}" id="wpes-existing-post-type-{WPES_TPL_SURVEY_ASSOCIATION_POST_TYPE}"{WPES_TPL_SURVEY_ASSOCAITION_CHECKBOX_STATE} /> {WPES_TPL_SURVEY_ASSOCIATION_POST_TYPE_NAME}</label></li><?php
$tpl_element['wpes_survey_post_type_association_item']['tpl'] = ob_get_contents();
ob_end_clean();




/**	Javascript being printed into admin header	*/
ob_start();
?><select name="wpes-issue-to-associate[]" class="wpes-existing-issue-list" style="width: 350px;" data-placeholder="<?php _e('Select issues to associate to current selected element', 'wp_easy_survey'); ?>" multiple="multiple" >{WPES_TPL_ITEM_LIST_FOR_ASSOCIATION}</select><?php
$tpl_element['wpes_item_association_select']['tpl'] = ob_get_contents();
ob_end_clean();


/**	Survey item container for final display in associated post type	*/
ob_start();
?><ol class="wpes-survey-issue-list-for-answer" >{WPES_TPL_ISSUES_LIST}</ol><?php
$tpl_element['wpes_survey_issue_display_in_associate_element_container']['tpl'] = ob_get_contents();
ob_end_clean();

/**	Survey item for final display in associated post type */
ob_start();
?><li id="wpes-final-survey-issue-{WPES_TPL_ISSUE_ID}" ><span class="wpes-final-survey-issue-title wpes-final-survey-issue-title-{WPES_TPL_ISSUE_ID}" >{WPES_TPL_ISSUE_TITLE}<span class="wpes-final-survey-issue-save-state-msg" ></span></span><div class="wpes-final-survey-issue-content wpes-final-survey-issue-content-{WPES_TPL_ISSUE_ID}" >{WPES_TPL_ISSUE_SUBLIST}</div>{WPES_TPL_ISSUE_GIVEN_ANSWERS}</li><?php
$tpl_element['wpes_survey_issue_display_in_associate_element']['tpl'] = ob_get_contents();
ob_end_clean();

/**	Survey issue answer form */
ob_start();
?><table class="wpes-survey-issue-response-table" id="wpes-survey-issue-response-table-{WPES_TPL_ISSUE_ID}" >
	<tr>
		<td>{WPES_TPL_AVAILABLE_ANSWERS}</td>
	</tr>
	<tr class="wpes-survey-issue-response-table-details-{WPES_TPL_ISSUE_ID}{WPES_TPL_FINAL_ISSUE_ANSWER_DETAILS_CONTAINER_CLASS}" >
		<td>
			<?php _e('Notes', 'wp_easy_survey'); ?>
			<textarea id="wpes-issue-answer-notes-{WPES_TPL_ISSUE_ID}" name="wpes-issue-final-answer[{WPES_TPL_ISSUE_ID}][notes]" class="wpes-issue-answer-input wpes-issue-answer-notes" >{WPES_TPL_ISSUE_CURRENT_ANSWER_NOTES}</textarea>
		</td>
	</tr>
	<tr class="wpes-survey-issue-response-table-details-{WPES_TPL_ISSUE_ID}{WPES_TPL_FINAL_ISSUE_ANSWER_DETAILS_CONTAINER_CLASS}" >
		<!-- <td>
			<?php _e('Next verification date', 'wp_easy_survey'); ?><br/>
			<input type="text" id="wpes-issue-answer-expiration-date-{WPES_TPL_ISSUE_ID}" name="wpes-issue-final-answer[{WPES_TPL_ISSUE_ID}][expiration-date]" class="wpes-issue-answer-input wpes-issue-answer-expiration-date" value="{WPES_TPL_ISSUE_CURRENT_ANSWER_EXPIRATION_DATE}" />
		</td> -->
		<td>
			<br/>
			<button id="wpes-answer-validation-button-{WPES_TPL_ISSUE_ID}" class="alignright wpes-answer_final-action-button wpes-answer-validation-button button button-primary" ><?php _e('Save answer', 'wp_easy_survey'); ?></button>
			<button id="wpes-answer-cancel-button-{WPES_TPL_ISSUE_ID}" class="alignright wpes-answer_final-action-button wpes-answer-cancel-button button button-secondary" ><?php _e('Cancel', 'wp_easy_survey'); ?></button>
			<img class="wpes-final-survey-issue-save-in-progress alignright" src="<?php echo admin_url('images/loading.gif'); ?>" alt="<?php _e('save in progress', 'wp_easy_survey'); ?>" />
			<input type="hidden" class="wpes-issue-final-answer-{WPES_TPL_ISSUE_ID}-answer-state" name="wpes-issue-final-current-answer-state" value="{WPES_TPL_CURRENT_ANSWER_STATE}" />
			<input type="hidden" class="wpes-issue-final-answer-{WPES_TPL_ISSUE_ID}-survey-id" name="wpes-issue-final-answer[{WPES_TPL_ISSUE_ID}][survey_id]" value="{WPES_TPL_SURVEY_ID}" />
		</td>
	</tr>
</table>
<!--
<script type="text/javascript" >
	jQuery(document).ready(function(){
		/**	Change an input into a datepicker when having given class */
		jQuery(".wpes-issue-answer-expiration-date").datepicker({
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
			changeYear: true
		});
		jQuery('#ui-datepicker-div').addClass( 'wpes-issue-answer-input' );
	});
</script>
--><?php
$tpl_element['wpes_survey_issue_answer_form']['tpl'] = ob_get_contents();
ob_end_clean();

/**	Final answers' output container	*/
ob_start();
?><ul class="wpes-final-survey-answer-container" >{WPES_TPL_ASSOCIATED_ANSWERS_LIST}</ul><?php
$tpl_element['wpes_survey_associated_answers_list_container']['tpl'] = ob_get_contents();
ob_end_clean();

/**	Final answers' output item - multiple (checkboxes)	*/
ob_start();
?><li class="wpes-final-answer-display-{WPES_TPL_ANSWER_OUTPUT_TYPE}" ><label class="selectit" ><input type="{WPES_TPL_ANSWER_INPUT_TYPE}" class="wpes-issue-answers" name="wpes-issue-final-answer[{WPES_TPL_ISSUE_ID}][answers][][main_choice]" value="{WPES_TPL_ANSWER_ID}" id="wpes-issue-final-answer-{WPES_TPL_ISSUE_ID}-{WPES_TPL_ANSWER_ID}" /> {WPES_TPL_ANSWER_LABEL}</label>{WPES_TPL_ANSWER_DETAIL} {WPES_TPL_FINAL_ANSWER_UNIT}</li><?php
$tpl_element['wpes_survey_issue_answer_input']['tpl'] = ob_get_contents();
ob_end_clean();

/**	Final answers' output - percentage	*/
ob_start();
?><input type="text" value="" name="wpes-issue-final-answer[{WPES_TPL_ISSUE_ID}][answers_detail][{WPES_TPL_ANSWER_ID}][details]" id="wpes-issue-final-answer-{WPES_TPL_ISSUE_ID}-{WPES_TPL_ANSWER_ID}-details" class="wpes-issue-answer-detail-input wpes-issue-answer-slider-final-value" /><?php
$tpl_element['wpes_survey_issue_answer_input_percentage']['tpl'] = ob_get_contents();
ob_end_clean();
/**	Final answers' output - short text	*/
ob_start();
?> <input type="text" value="" name="wpes-issue-final-answer[{WPES_TPL_ISSUE_ID}][answers_detail][{WPES_TPL_ANSWER_ID}][details]" id="wpes-issue-final-answer-{WPES_TPL_ISSUE_ID}-{WPES_TPL_ANSWER_ID}-details" class="wpes-issue-answer-detail-input wpes-issue-answer-short-test-final-value" /><?php
$tpl_element['wpes_survey_issue_answer_input_short_text']['tpl'] = ob_get_contents();
ob_end_clean();
/**	Final answers' output - long text	*/
ob_start();
?> <textarea name="wpes-issue-final-answer[{WPES_TPL_ISSUE_ID}][answers_detail][{WPES_TPL_ANSWER_ID}][details]" id="wpes-issue-final-answer-{WPES_TPL_ISSUE_ID}-{WPES_TPL_ANSWER_ID}-details" class="wpes-issue-answer-detail-input wpes-issue-answer-long-text-final-value" ></textarea><?php
$tpl_element['wpes_survey_issue_answer_input_long_text']['tpl'] = ob_get_contents();
ob_end_clean();
/**	Final answers' output - select list	*/
ob_start();
?> <select name="wpes-issue-final-answer[{WPES_TPL_ISSUE_ID}][answers_detail][{WPES_TPL_ANSWER_ID}][details]"{WPES_TPL_ANSWER_SELECT_LIST_STATE} id="wpes-issue-final-answer-{WPES_TPL_ISSUE_ID}-{WPES_TPL_ANSWER_ID}-details" class="wpes-issue-answer-detail-input wpes-issue-answer-select-list-final-value">{WPES_TPL_ANSWER_FINAL_SELECT_LIST_ITEMS}</select><?php
$tpl_element['wpes_survey_issue_answer_input_select_list']['tpl'] = ob_get_contents();
ob_end_clean();
/**	Final answers' output - select list	item */
ob_start();
?><option value="{WPES_TPL_ANSWER_SELECT_LIST_ITEM_VALUE}" >{WPES_TPL_ANSWER_SELECT_LIST_ITEM_LABEL}</option><?php
$tpl_element['wpes_survey_issue_answer_input_select_list_item']['tpl'] = ob_get_contents();
ob_end_clean();

/**	Final answers' output no answers	*/
ob_start();
?><span><?php _e('There are no answers for this issue', 'wp_easy_survey'); ?></span><?php
$tpl_element['wpes_no_answers_defined_for_issue']['tpl'] = ob_get_contents();
ob_end_clean();


/**
 *
 *
 * ANSWERS MANAGEMENT
 *
 *
 */
/**	Answer type list container	*/
ob_start();
?><ul class="wpes-answer-type-choice-container" >{WPES_TPL_ANSWER_TYPE_LIST_CONTAINER}</ul><?php
$tpl_element['wpes_answer_management_type']['tpl'] = ob_get_contents();
ob_end_clean();

/**	Answer type list item	*/
ob_start();
?><li><label class="selectit" ><input type="radio" id="wpes-answer-type-{WPES_TPL_ANSWER_TYPE}" name="wpes_answer_meta[type_def][type]" class="wpes-answer-type{WPES_TPL_ANSWER_TYPE_MORE_CLASS}" value="{WPES_TPL_ANSWER_TYPE}"{WPES_TPL_ANSWER_TYPE_STATE} /> {WPES_TPL_ANSWER_TYPE_LABEL}</label>{WPES_TPL_ANSWER_TYPE_OPTIONS_CONTAINER}</li><?php
$tpl_element['wpes_answer_management_type_item']['tpl'] = ob_get_contents();
ob_end_clean();

/**	Answer type item more configuration container	*/
ob_start();
?><div class="wpes-answer-type-options{WPES_TPL_ANSWER_TYPE_OPTIONS_STATE}" id="wpes-answer-type-{WPES_TPL_ANSWER_TYPE}-options" >{WPES_TPL_ANSWER_TYPE_OPTIONS}</div><?php
$tpl_element['wpes_answer_management_type_item_options']['tpl'] = ob_get_contents();
ob_end_clean();

/**	Answer options management	*/
ob_start();
?><!-- <span><?php _e('Choose a color for this answer. This will help when viewing answer repartition in surveys\' statistics area', 'wp_easy_survey'); ?></span><br/><input type="hidden" name="wpes_answer_meta[options][default_color]" value="#{WPES_TPL_ANSWER_MNGT_OPTIONS_COLOR}" /><input type="text" class="wpes-color-field" name="wpes_answer_meta[options][color]" value="#{WPES_TPL_ANSWER_MNGT_OPTIONS_COLOR}" /> -->
<br/><span><?php _e('Set a unit for this answer', 'wp_easy_survey'); ?></span><br/><input type="text" name="wpes_answer_meta[options][unit]" value="{WPES_TPL_ANSWER_MNGT_OPTIONS_UNIT}" />
<!-- <br/><span><?php _e('Define final answer state', 'wp_easy_survey'); ?></span><div class="wpes-answer-final-state-container" >{WPES_TPL_ANSWER_FINAL_STATE_DEFINITION}</div> --><?php
$tpl_element['wpes_answer_management_options']['tpl'] = ob_get_contents();
ob_end_clean();

/**	Answer options management - short-text	*/
ob_start();
?><label class="selectit" ><input type="checkbox" name="wpes_answer_meta[{WPES_TPL_ANSWER_TYPE}][type_def][options][use_content]" value="yes" class="wpes-answer-options-parameters" id="wpes-answer-options-parameters-{WPES_TPL_ANSWER_TYPE}"{WPES_TPL_ANSWER_TYPE_OPTIONS_CONTENT_STATE} /> <?php _e('Add a text before answer input', 'wp_easy_survey'); ?></label><br/><input type="text" name="wpes_answer_meta[{WPES_TPL_ANSWER_TYPE}][type_def][options][content]" value="{WPES_TPL_ANSWER_TYPE_OPTIONS_CONTENT}" class="wpes-answer-type-options-content{WPES_TPL_ANSWER_TYPE_OPTIONS_CONTENT_CLASS}" id="wpes-answer-options-parameters-{WPES_TPL_ANSWER_TYPE}-content" /><?php
$tpl_element['wpes_answer_options_management_type_short-text']['tpl'] = ob_get_contents();
ob_end_clean();

/**	Answer options management - long-text	*/
ob_start();
?><label class="selectit" ><input type="checkbox" name="wpes_answer_meta[{WPES_TPL_ANSWER_TYPE}][type_def][options][use_content]" value="yes" class="wpes-answer-options-parameters" id="wpes-answer-options-parameters-{WPES_TPL_ANSWER_TYPE}"{WPES_TPL_ANSWER_TYPE_OPTIONS_CONTENT_STATE} /> <?php _e('Add a text before answer input', 'wp_easy_survey'); ?></label><br/><input type="text" name="wpes_answer_meta[{WPES_TPL_ANSWER_TYPE}][type_def][options][content]" value="{WPES_TPL_ANSWER_TYPE_OPTIONS_CONTENT}" class="wpes-answer-type-options-content{WPES_TPL_ANSWER_TYPE_OPTIONS_CONTENT_CLASS}" id="wpes-answer-options-parameters-{WPES_TPL_ANSWER_TYPE}-content" /><?php
$tpl_element['wpes_answer_options_management_type_long-text']['tpl'] = ob_get_contents();
ob_end_clean();

/**	Answer options management - select-list	*/
ob_start();
?><label class="selectit" ><input type="checkbox" name="wpes_answer_meta[{WPES_TPL_ANSWER_TYPE}][type_def][options][content_options][subtype]" class="wpes-answer-options-parameters"{WPES_TPL_ANSWER_TYPE_LIST_MULTIPLE_STATE} id="wpes-answer-options-parameters-{WPES_TPL_ANSWER_TYPE}-use-multiple-input" value="multiple" /> <?php _e('User can choose multiple values', 'wp_easy_survey'); ?></label>
<div class="wpes-answer-select-list-type-empty-value-for-first{WPES_TPL_ANSWER_TYPE_LIST_EMPTY_VALUE_CLASS}" >
	<label class="selectit" ><input type="checkbox" class="wpes-answer-options-parameters"{WPES_TPL_ANSWER_TYPE_LIST_EMPTY_VALUE_STATE} id="wpes-answer-options-parameters-{WPES_TPL_ANSWER_TYPE}" value="yes" /> <?php _e('Create an empty item at top of list', 'wp_easy_survey'); ?></label>
	<br/>
	<input type="text" placeholder="<?php _e('Add a text for the first value of select list', 'wp_easy_survey'); ?>" name="wpes_answer_meta[{WPES_TPL_ANSWER_TYPE}][type_def][options][content_options][empty_value]" value="{WPES_TPL_ANSWER_TYPE_OPTIONS_CONTENT}" class="wpes-answer-type-options-content{WPES_TPL_ANSWER_TYPE_OPTIONS_CONTENT_CLASS}" id="wpes-answer-options-parameters-{WPES_TPL_ANSWER_TYPE}-content" />
</div>

<ul class="wpes-answer-select-list" >{WPES_TPL_SELECT_LIST_ITEM_CONTAINER}</ul>
<script type="text/javascript" >
jQuery(document).ready(function(){
	jQuery(".wpes-answer-select-list").sortable();
});
</script><?php
$tpl_element['wpes_answer_options_management_type_select-list']['tpl'] = ob_get_contents();
ob_end_clean();
/**	Answer options management - select-list item	*/
ob_start();
?><li class="wpes-custom-tree3-content" ><div class="wpes-answer-type-select-list-handle"></div>{WPES_TPL_ANSWER_TYPE_SELECT_LIST_INPUT}<span class="wpes-answer-type-selectlist-item-remover" ></span></li><?php
$tpl_element['wpes_answer_options_management_type_select-list-item']['tpl'] = ob_get_contents();
ob_end_clean();
/**	Answer options management - select-list item actions add	*/
ob_start();
?><input type="text" name="wpes_answer_meta[{WPES_TPL_ANSWER_TYPE}][type_def][options][content][]" value="{WPES_TPL_SELECT_LIST_ITEM_LABEL}" placeholder="<?php _e('Enter a new value for select list', 'wp_easy_survey'); ?>" /><?php
$tpl_element['wpes_answer_options_management_type_select-list-item-new']['tpl'] = ob_get_contents();
ob_end_clean();
/**	Answer options management - select-list item actions add	*/
ob_start();
?><div class="wpes-answer-new-item-into-select-list" >{WPES_TPL_ANSWER_TYPE_SELECT_LIST_INPUT}<button><?php _e('Add to list', 'wp_easy_survey'); ?></button><ul class="wpes-hide" id="wpes-answer-new-item-for-select-list-empty-tpl-container" >{WPES_TPL_NEW_ITEM_FOR_SELECT_LIST_TPL}</ul></div><?php
$tpl_element['wpes_answer_options_management_type_select-list-item-actions-add']['tpl'] = ob_get_contents();
ob_end_clean();

