
/**
*	Allows to convert html special chars to normal chars in javascript messages
*
*	@param string text The text we want to change html special chars into normal chars
*
*/
function wpes_convert_html_accent(text) {
	text = text.replace(/&Agrave;/g, "\300");
	text = text.replace(/&Aacute;/g, "\301");
	text = text.replace(/&Acirc;/g, "\302");
	text = text.replace(/&Atilde;/g, "\303");
	text = text.replace(/&Auml;/g, "\304");
	text = text.replace(/&Aring;/g, "\305");
	text = text.replace(/&AElig;/g, "\306");
	text = text.replace(/&Ccedil;/g, "\307");
	text = text.replace(/&Egrave;/g, "\310");
	text = text.replace(/&Eacute;/g, "\311");
	text = text.replace(/&Ecirc;/g, "\312");
	text = text.replace(/&Euml;/g, "\313");
	text = text.replace(/&Igrave;/g, "\314");
	text = text.replace(/&Iacute;/g, "\315");
	text = text.replace(/&Icirc;/g, "\316");
	text = text.replace(/&Iuml;/g, "\317");
	text = text.replace(/&Eth;/g, "\320");
	text = text.replace(/&Ntilde;/g, "\321");
	text = text.replace(/&Ograve;/g, "\322");
	text = text.replace(/&Oacute;/g, "\323");
	text = text.replace(/&Ocirc;/g, "\324");
	text = text.replace(/&Otilde;/g, "\325");
	text = text.replace(/&Ouml;/g, "\326");
	text = text.replace(/&Oslash;/g, "\330");
	text = text.replace(/&Ugrave;/g, "\331");
	text = text.replace(/&Uacute;/g, "\332");
	text = text.replace(/&Ucirc;/g, "\333");
	text = text.replace(/&Uuml;/g, "\334");
	text = text.replace(/&Yacute;/g, "\335");
	text = text.replace(/&THORN;/g, "\336");
	text = text.replace(/&Yuml;/g, "\570");
	text = text.replace(/&szlig;/g, "\337");
	text = text.replace(/&agrave;/g, "\340");
	text = text.replace(/&aacute;/g, "\341");
	text = text.replace(/&acirc;/g, "\342");
	text = text.replace(/&atilde;/g, "\343");
	text = text.replace(/&auml;/g, "\344");
	text = text.replace(/&aring;/g, "\345");
	text = text.replace(/&aelig;/g, "\346");
	text = text.replace(/&ccedil;/g, "\347");
	text = text.replace(/&egrave;/g, "\350");
	text = text.replace(/&eacute;/g, "\351");
	text = text.replace(/&ecirc;/g, "\352");
	text = text.replace(/&euml;/g, "\353");
	text = text.replace(/&igrave;/g, "\354");
	text = text.replace(/&iacute;/g, "\355");
	text = text.replace(/&icirc;/g, "\356");
	text = text.replace(/&iuml;/g, "\357");
	text = text.replace(/&eth;/g, "\360");
	text = text.replace(/&ntilde;/g, "\361");
	text = text.replace(/&ograve;/g, "\362");
	text = text.replace(/&oacute;/g, "\363");
	text = text.replace(/&ocirc;/g, "\364");
	text = text.replace(/&otilde;/g, "\365");
	text = text.replace(/&ouml;/g, "\366");
	text = text.replace(/&oslash;/g, "\370");
	text = text.replace(/&ugrave;/g, "\371");
	text = text.replace(/&uacute;/g, "\372");
	text = text.replace(/&ucirc;/g, "\373");
	text = text.replace(/&uuml;/g, "\374");
	text = text.replace(/&yacute;/g, "\375");
	text = text.replace(/&thorn;/g, "\376");
	text = text.replace(/&yuml;/g, "\377");
	text = text.replace(/&oelig;/g, "\523");
	text = text.replace(/&OElig;/g, "\522");
	return text;
}

/**
 * Check if there are element into current survey when deleting or disassociating an issue from a survey. If there are no element anymore then reload with ajax the container
 */
function wpes_check_element_nb_for_display() {
	var children_nb = 0;
	jQuery(".wpes-custom-tree-list li").each(function() {
		children_nb += 1;
	});
	if ( 0 == children_nb ) {
		jQuery.post(ajaxurl, {action: "wpes-ajax-survey-content-output", post_ID: jQuery("#post_ID").val(), "wpes-ajax-display-survey-metabox": WPES_JS_VAR_DISPLAY_SURVEY_AJAX_NONCE, }, function(response){
			jQuery("#wpes_survey-issues .inside").html( response );
	    });
	}
}

/**
 * Get nestable order and save it into database on the fly
 *
 * @param list The nestable element to get order for
 */
function update_wpes_survey_order( list ) {
    jQuery.post(ajaxurl, {action: "wpes-ajax-survey-content-order", post_ID: jQuery("#post_ID").val(), "wpes-ajax-survey-order": WPES_JS_VAR_ORDER_SURVEY_AJAX_NONCE, "wpes-survey-new-order": list.nestable('serialize'), } );
}

/**
 * Initialise the nestable tree
 */
function initialize_nestable( collapse_all ) {
	jQuery(".wpes-custom-tree").nestable({
		maxDepth:        10,
		rootClass: 		 "wpes-custom-tree",
		listClass: 		 "wpes-custom-tree-list",
		itemClass: 		 "wpes-custom-tree-item",
		dragClass: 		 "wpes-custom-tree-dragel",
		handleClass: 	 "wpes-custom-tree-handle",
		collapsedClass:  "wpes-custom-tree-collapsed",
		placeClass: 	 "wpes-custom-tree-placeholder",
		emptyClass: 	 "wpes-custom-tree-empty",
		expandBtnHTML: 	 WPES_JS_VAR_NESTABLE_BUTTON_COLLAPSER,
		collapseBtnHTML: WPES_JS_VAR_NESTABLE_BUTTON_EXPANDER,
	}).on('change', function (event) {
		var list = event.length ? event : jQuery( event.target );
		update_wpes_survey_order( list );
	});
	if ( collapse_all ) {
		jQuery(".wpes-custom-tree").nestable( 'collapseAll' );
	}
}


/**
 * Launch ajax action for saving tree order
 *
 * @param response The response sent by ajax action
 */
function wpes_action_after_survey_item_operation(response) {
    /**	Display message and hide loading picture	*/
    jQuery(".wpes-survey-item-edition-form-message").html( response['status_message'] );
    jQuery(".wpes-survey-item-edition-form-message").show();
    setTimeout(function() {
		jQuery(".wpes-survey-item-edition-form-message").hide().html("");
		jQuery("#TB_closeWindowButton").click();
    }, "2000");
    jQuery("#wpes-survey-item-edition-form-save-in-progress img").hide();

    if ( (response['status'] == true) && (response['operation'] != 'undefined') ) {
        /**	Check returned action in order to change display	*/
        if ( response['operation'] == 'edit-issue' ) {
	        jQuery("#wpes-survey-node-" + response['item-id']).html( response['output'] );
        }
        else if ( (response['operation'] == 'add-issue') || (response['operation'] == 'item_association') ) {
	        if ( response['location'] == 'empty_root' ) {
		        jQuery.post(ajaxurl, {action: "wpes-ajax-survey-content-output", post_ID: jQuery("#post_ID").val(), "wpes-ajax-display-survey-metabox": WPES_JS_VAR_DISPLAY_SURVEY_AJAX_NONCE, }, function(response){
					jQuery("#wpes_survey-issues .inside").html( response );
			    });
	        }
	        else if ( response['location'] == 'root' ) {
		        jQuery(".wpes-custom-tree-root-node .wpes-custom-tree-list:first-child").append( response['output'] );
	        }
	        else if ( response['location'] == 'tree' ) {
	        	jQuery(".wpes-custom-tree-item-" + response['item-parent-id'] ).removeClass( 'wpes-custom-tree-collapsed' );
	        	if ( response['item-parent-children'] > 0 ) {
		       		jQuery(".wpes-custom-tree-item-" + response['item-parent-id'] + " ol.wpes-custom-tree-list").append( response['output'] );
		        }
		        else {
		        	jQuery(".wpes-custom-tree-item-" + response['item-parent-id'] + " .wpes-custom-tree-handle" ).before( WPES_JS_VAR_NESTABLE_BUTTON_EXPANDER + WPES_JS_VAR_NESTABLE_BUTTON_COLLAPSER );
		        	jQuery(".wpes-custom-tree-item-" + response['item-parent-id'] + " button.collapser" ).show();
		        	jQuery(".wpes-custom-tree-item-" + response['item-parent-id'] ).append( response['output'] );
		        }
	        }
        }

    }
}