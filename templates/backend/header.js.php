<script type="text/javascript" >
	var WPES_JS_VAR_FINAL_SURVEY_ISSUE_AJAX_NONCE = "<?php echo wp_create_nonce( 'wpes-issue-history-load' ); ?>";
	var WPES_JS_VAR_FINAL_SURVEY_EVALUATION_CLOSE_AJAX_NONCE = "<?php echo wp_create_nonce( 'wpes-ajax-close-evaluation' ); ?>";
	var WPES_JS_VAR_FINAL_SURVEY_NEW_EVALUATION_START_AJAX_NONCE = "<?php echo wp_create_nonce( 'wpes-new-evaluation-start' ); ?>";
	var WPES_JS_VAR_ANSWER_REUSE_AJAX_NONCE = "<?php echo wp_create_nonce( 'wpes-answer-reuse' ); ?>";
	var WPES_JS_VAR_ANSWER_EDIT_AJAX_NONCE = "<?php echo wp_create_nonce( 'wpes-answer-edit' ); ?>";
	var WPES_JS_VAR_ANSWER_SAVE_AJAX_NONCE = "<?php echo wp_create_nonce( 'wpes-answer-save' ); ?>";
	var WPES_JS_VAR_ORDER_SURVEY_AJAX_NONCE = "<?php echo wp_create_nonce( 'wpes-survey-order-save' ); ?>";
	var WPES_JS_VAR_DISPLAY_SURVEY_AJAX_NONCE = "<?php echo wp_create_nonce( 'wpes-display-survey' ); ?>";
	var WPES_JS_VAR_DELETE_ISSUE_AJAX_NONCE = "<?php echo wp_create_nonce( 'wpes-issue-delete' ) ?>";
	var WPES_JS_VAR_DISSOCIATE_ISSUE_AJAX_NONCE = "<?php echo wp_create_nonce( 'wpes-issue-dissociate' ); ?>";

	var WPES_JS_MSG_CHOOSE_AT_LEAST_ONE_ANSWER_BEFORE_SAVING = "<?php _e('You have to chose at least one answer before saving', 'wp_easy_survey'); ?>";
	var WPES_JS_MSG_FILL_ANSWER_DETAIL_BEFORE_SAVING = "<?php _e('The answer you choose need some specification. Please fill corresponding field before saving', 'wp_easy_survey'); ?>";
	var WPES_JS_MSG_SURE_TO_DELETE_ISSUE = "<?php _e('Are you sure you want to delete this element?', 'wp_easy_survey'); ?>";
	var WPES_JS_MSG_SURE_TO_DELETE_ISSUE_CONTAINING_OTHERS = "<?php _e('Are you sure you want to delete this element?\r\nThere are some issues contained that will be unavailable after this action.', 'wp_easy_survey'); ?>";
	var WPES_JS_MSG_SURE_TO_DISSOCIATE_ISSUE = "<?php _e('Are you sure you want to dissociate this issue?', 'wp_easy_survey'); ?>";
	var WPES_JS_MSG_SURE_TO_DISSOCIATE_ISSUE_CONTAINING_OTHERS = "<?php _e('Are you sure you want to dissociate this issue?\r\nIssues contained into this one your are deleting won\'t be associated to this survey after this action', 'wp_easy_survey'); ?>";
	var WPES_JS_MSG_SURE_TO_DELETE_ANSWER_POSSIBILITY = "<?php _e('Are you sure you want to delete this answer possibility?', 'wp_easy_survey'); ?>";
	var WPES_JS_MSG_FILL_IN_NEW_ITEM_VALUE_FOR_ANSWER_SELECTLIST = "<?php _e('Please fill in new value field before clicking on addition button', 'wp_easy_survey'); ?>";
	var WPES_JS_VAR_FINAL_SURVEY_EVALUATION_CLOSE_ALL_ANSWER = "<?php _e('Are you sure you want to close this evaluation?\r\nAfter closing an evaluation no changes would be possible', 'wp_easy_survey'); ?>";
	var WPES_JS_VAR_FINAL_SURVEY_EVALUATION_CLOSE_NOT_ALL_ANSWER = "<?php _e('Are you sure you want to close this evaluation while not all issues have an answer?\r\nAfter closing an evaluation no changes would be possible', 'wp_easy_survey'); ?>";
	var WPES_JS_VAR_NESTABLE_BUTTON_COLLAPSER = '<button type="button" style="display: none;" class="wpes-nestable-button collapser" data-action="collapse"><?php _e('Collapse', 'wp_easy_survey'); ?></button>';
	var WPES_JS_VAR_NESTABLE_BUTTON_EXPANDER = '<button type="button" style="display: none;" class="wpes-nestable-button expander" data-action="expand"><?php _e('Expand', 'wp_easy_survey'); ?></button>';
</script>