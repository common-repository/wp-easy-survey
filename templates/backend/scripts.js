/**	Define a no conflict var for our plugin. Avoid conflict with other plugin using jQuery and wordpress	*/
var wp_easy_survey = jQuery.noConflict();

/**	On document load call the different javascript utilities	*/
wp_easy_survey(document).ready(function(){

	/**	Add action listener on delete issue button	*/
	jQuery(".wpes-custom-tree").on("click", ".wpes-delete-issue", function(){
		var current_issue_id = jQuery(this).attr("id").replace("wpes-delete-issue-", "");
		var children_number = 0;
		jQuery( ".wpes-custom-tree-item-" + current_issue_id + " ol ").each(function(){
			children_number += 1;
		});

		if ( ((children_number == 0) && confirm( wpes_convert_html_accent( WPES_JS_MSG_SURE_TO_DELETE_ISSUE) )) || ((children_number > 0) && confirm( wpes_convert_html_accent( WPES_JS_MSG_SURE_TO_DELETE_ISSUE_CONTAINING_OTHERS ) )) ) {
			jQuery.post( ajaxurl, { action: "wpes-ajax-survey-item-delete", post_ID: current_issue_id, "wpes-ajax-survey-issue-delete-nonce": WPES_JS_VAR_DELETE_ISSUE_AJAX_NONCE, }, function(response) {
				if ( response['status'] == true ) {
					jQuery( ".wpes-custom-tree-item-" + current_issue_id ).remove();
					wpes_check_element_nb_for_display();
				}
			}, "json");
		}

		return false;
	});

	/**	Add action listener on dissociate issue button	*/
	jQuery(".wpes-custom-tree").on("click", ".wpes-dissociate-issue", function(){
		var current_issue_id = jQuery(this).attr("id").replace("wpes-dissociate-issue-", "");
		var children_number = 0;
		jQuery( ".wpes-custom-tree-item-" + current_issue_id + " ol ").each(function(){
			children_number += 1;
		});

		if ( ((children_number == 0) && confirm( wpes_convert_html_accent( WPES_JS_MSG_SURE_TO_DISSOCIATE_ISSUE) )) || ((children_number > 0) && confirm( wpes_convert_html_accent( WPES_JS_MSG_SURE_TO_DISSOCIATE_ISSUE_CONTAINING_OTHERS ) )) ) {
			jQuery.post( ajaxurl, { action: "wpes-ajax-survey-item-dissociate", post_ID: current_issue_id, "wpes-ajax-survey-issue-dissociate-nonce": WPES_JS_VAR_DISSOCIATE_ISSUE_AJAX_NONCE, }, function(response) {
				if ( response['status'] == true ) {
					jQuery( ".wpes-custom-tree-item-" + current_issue_id ).remove();
					wpes_check_element_nb_for_display();
				}
			}, "json");
		}

		return false;
	});

	/**	Add action listener on save answer button into associated element	*/
	jQuery( document ).on("click", "button.wpes-answer-validation-button", function(){
		var current_issue = jQuery(this).attr("id").replace("wpes-answer-validation-button-", "");
		jQuery(this).toggleClass("button-primary button-secondary");

		/**	Display loading button	*/
		jQuery("#wpes-survey-issue-response-table-" + current_issue + " img.wpes-final-survey-issue-save-in-progress").show();

		/**	Build an array with user's answer	*/
		var answer_details = [ ];
		jQuery("#wpes-survey-issue-response-table-" + current_issue + " .wpes-issue-answer-input").each(function(){
			var name = jQuery(this).attr("name").replace("wpes-issue-final-answer[" + current_issue + "][", "").replace("]", "");
			answer_details.push( [name, jQuery(this).val()] );
		});
		var answers = [ ];
		var detail_is_empty = false;
		jQuery("#wpes-survey-issue-response-table-" + current_issue + " input[type=radio].wpes-issue-answers, #wpes-survey-issue-response-table-" + current_issue + " input[type=checkbox].wpes-issue-answers").each(function(){
			if ( jQuery(this).is(":checked") ) {
				var detail = '';
				var answer_detail_input_name = jQuery(this).attr("id") + "-details";
				if ( "" == jQuery( "#" + answer_detail_input_name ).val() ) {
					detail_is_empty = true;
				}
				else if ( undefined != jQuery( "#" + answer_detail_input_name ).val() ) {
					detail = jQuery( "#" + answer_detail_input_name ).val();
				}
				answers.push( [jQuery(this).val(), detail] );
			}
		});

		if ( answers.length > 0 ) {
			if ( !detail_is_empty ) {

				var parent_survey_id = jQuery( this ).closest( "div.postbox" ).attr( "id" ).replace( jQuery( "#post_type" ).val() + "-survey-association-", "" );
				/**	Define the different variable to save answer	*/
				var data = {
					action: "wpes-ajax-save-answer",
					"wpes-ajax-answer-save-nonce": WPES_JS_VAR_ANSWER_SAVE_AJAX_NONCE,
					post_ID: jQuery("#post_ID").val(),
					issue_ID: current_issue,
					survey_ID: jQuery( ".wpes-issue-final-answer-" + current_issue + "-survey-id" ).val(),
					answers: answers,
					answer_details: answer_details,
					answer_total_number: jQuery("#wpes-final-survey-total-answer-to-give-for-survey-" + parent_survey_id ).val(),
					issues_with_answer: jQuery("#wpes-final-survey-issues-with-answers-for-survey-" + parent_survey_id ).val(),
				};

				/**	Launch ajax request */
				jQuery.post( ajaxurl, data, function(response) {
					if ( true == response['status'] ) {
				    	jQuery(".wpes-final-survey-issue-title-" + response['issue_id'] + " .wpes-final-survey-issue-save-state-msg").html( response['state_message'] ).addClass('wpes-msg-udpdate').show();
					    setTimeout(function() {
					    	jQuery(".wpes-final-survey-issue-title-" + response['issue_id'] + " .wpes-final-survey-issue-save-state-msg").hide().html("").removeClass('wpes-msg-udpdate');
					    }, "3000");
					    jQuery("#wpes-survey-issue-response-table-" + response['issue_id'] + " img.wpes-final-survey-issue-save-in-progress").hide();

					    jQuery("#wpes-final-survey-issue-" + response['issue_id'] + " table.wpes-final-survey-last-answer-detail-display").remove();
						jQuery("#wpes-final-survey-issue-" + response['issue_id'] ).append( response['output'] );
					    jQuery(".wpes-final-survey-issue-content-" + response['issue_id']).html( "" );

						jQuery("#wpes-survey-current-state-" + response['survey_ID'] + " .wpes-final-survey-stats-container .inside div.wpes-audit-the-stats").html( response['output_stats'] );
					}
				}, 'json');

			}
			else {
				alert( WPES_JS_MSG_FILL_ANSWER_DETAIL_BEFORE_SAVING );
				jQuery("#wpes-survey-issue-response-table-" + current_issue + " img.wpes-final-survey-issue-save-in-progress").hide();
			}
		}
		else {
			alert( WPES_JS_MSG_CHOOSE_AT_LEAST_ONE_ANSWER_BEFORE_SAVING );
			jQuery("#wpes-survey-issue-response-table-" + current_issue + " img.wpes-final-survey-issue-save-in-progress").hide();
		}

		return false;
	});

	/**	Add action for check/uncheck all answer in idduse edition form	*/
	jQuery( document ).on("click", ".wpes-mass-action-on-answer-for-issue", function(){
		var state = true;
		if ( jQuery(this).hasClass("wpes-check-all-available-answer-in-issue") ) {
			state = true;
		}
		else if ( jQuery(this).hasClass("wpes-uncheck-all-available-answer-in-issue") ) {
			state = false;
		}
		jQuery(".wpes-issue-associated-answers").each(function(){
			jQuery(this).prop( "checked", state );
		});
	});

	/** Add action listener on answer edition button */
	jQuery( document ).on("click", ".wpes-final-survey-issue-edit-answer", function(){
		var issue_to_edit = jQuery(this).attr("id").replace("wpes-final-survey-issue-edit-answer-", "");
		var survey_id = jQuery( this ).closest( "div.postbox" ).attr( "id" ).replace( jQuery( "#post_type" ).val() + "-survey-association-", "" );
		jQuery( "#wpes-final-survey-issue-edit-answer-" + issue_to_edit ).hide();
		jQuery( "#wpes-final-survey-issue-" + issue_to_edit + " table.wpes-final-survey-last-answer-detail-display" ).before( jQuery(".wpes-final-survey-loader-picto").html() );

		/**	Define the different variable to save answer	*/
		var data = {
			action: "wpes-ajax-edit-answer",
			"wpes-ajax-answer-edit-nonce": WPES_JS_VAR_ANSWER_EDIT_AJAX_NONCE,
			post_ID: jQuery("#post_ID").val(),
			answer_total_number: jQuery("#wpes-final-survey-total-answer-to-give-for-survey-" + survey_id ).val(),
			issues_with_answer: jQuery("#wpes-final-survey-issues-with-answers-for-survey-" + survey_id ).val(),
			issue_ID: issue_to_edit,
			survey_ID: jQuery( "#wpes-final-survey-identifier-for-survey-" + survey_id ) .val(),
		};

		/**	Launch ajax request */
		jQuery.post( ajaxurl, data, function(response) {
			jQuery(".wpes-final-survey-issue-content-" + response['issue_id']).html( response['output'] );
			jQuery( "#wpes-answer-final-action-loader-container-" + response['issue_id']).hide();
			jQuery( "#wpes-final-survey-issue-" + response['issue_id'] + " .wpes-loading-picture").remove();
		}, 'json');

		return false;
	});

	/** Add action listener for re using an answer*/
	jQuery( document ).on("click", ".wpes-final-survey-issue-reuse-answer", function(){
		var issue_to_edit = jQuery(this).attr("id").replace("wpes-final-survey-issue-reuse-answer-", "");

		/**	Define the different variable to save answer	*/
		var data = {
			action: "wpes-ajax-reuse-answer",
			"wpes-ajax-answer-reuse-nonce": WPES_JS_VAR_ANSWER_REUSE_AJAX_NONCE,
			issue_ID: issue_to_edit,
		};

		/**	Launch ajax request */
		jQuery.post( ajaxurl, data, function(response) {
			jQuery("#wpes-final-survey-issue-" + response['issue_id'] + " table.wpes-final-survey-last-answer-detail-display").remove();
			jQuery("#wpes-final-survey-issue-" + response['issue_id'] ).append( response['output'] );
			jQuery("#TB_closeWindowButton").click();

			jQuery(".wpes-final-survey-issue-title-" + response['issue_id'] + " .wpes-final-survey-issue-save-state-msg").html( response['state_message'] ).addClass('wpes-msg-udpdate').show();
		    setTimeout(function() {
		    	jQuery(".wpes-final-survey-issue-title-" + response['issue_id'] + " .wpes-final-survey-issue-save-state-msg").hide().html("").removeClass('wpes-msg-udpdate');
		    }, "3000");

		}, 'json');

		return false;
	});

	/**	Change an input type test into a color picker for answer configuration	*/
	jQuery('.wpes-color-field').wpColorPicker();

	/**	Add listener on answer details input for checking corresponding box	*/
	jQuery( document ).on("click", ".wpes-issue-answer-detail-input", function(){
		jQuery( this ).closest('li').children('label').children('input[type=radio].wpes-issue-answers, input[type=checkbox].wpes-issue-answers' ).prop('checked', true);
	});

	/**	Add listener on answer type having configuration in order to display container	*/
	jQuery('.wpes-answer-type').click(function() {
		jQuery(".wpes-answer-type-options").each(function(){
			jQuery(this).hide();
		});
		if ( jQuery(this).hasClass("wpes-answer-type-has-conf") ) {
			jQuery("#" + jQuery(this).attr("id") + "-options" ).show();
		}
	});
	/**	Add listener on parameters for answers' options	*/
	jQuery(".wpes-answer-options-parameters").click(function() {
		if ( jQuery(this).is(":checked") ) {
			jQuery("#" + jQuery(this).attr("id") + "-content" ).show();
		}
		else {
			jQuery("#" + jQuery(this).attr("id") + "-content" ).hide();
		}
	});

	/**	Add listener for select-list type answer' in order to remove them	*/
	jQuery(".wpes-answer-select-list").on("click", ".wpes-answer-type-selectlist-item-remover", function(){
		if ( confirm( wpes_convert_html_accent( WPES_JS_MSG_SURE_TO_DELETE_ANSWER_POSSIBILITY ) ) ) {
			jQuery(this).closest("li.wpes-custom-tree3-content").remove();
		}
	});
	/**	Add listener for new item addition into answer select-list type	*/
	jQuery(".wpes-answer-new-item-into-select-list button").click(function(){
		if ( jQuery(".wpes-answer-new-item-into-select-list input").val() != '') {
			jQuery("ul.wpes-answer-select-list").append( jQuery("#wpes-answer-new-item-for-select-list-empty-tpl-container").html() );
			jQuery("ul.wpes-answer-select-list li:last-child input").val( jQuery(".wpes-answer-new-item-into-select-list input").val() );
			jQuery(".wpes-answer-new-item-into-select-list input").val("");
		}
		else {
			alert( wpes_convert_html_accent( WPES_JS_MSG_FILL_IN_NEW_ITEM_VALUE_FOR_ANSWER_SELECTLIST ) );
		}

		return false;
	});
	/**	Add listener on select list multiple options	*/
	jQuery("#wpes-answer-options-parameters-select-list-use-multiple-input").click(function(){
		if ( jQuery(this).is(":checked") ) {
			jQuery("#wpes-answer-options-parameters-select-list").prop("checked", false);
			jQuery(".wpes-answer-select-list-type-empty-value-for-first").addClass("wpes-hide");
			jQuery("#wpes-answer-options-parameters-select-list-content").val("");
		}
		else {
			jQuery(".wpes-answer-select-list-type-empty-value-for-first").removeClass("wpes-hide");
		}
	});

	/**	Add listener on new evaluation starting button	*/
	jQuery( document ).on("click", ".wpes-final-survey-new-evaluation-start", function( e ){
		e.preventDefault();
		var current_survey_id = jQuery(this).attr("id").replace("wpes-final-survey-new-evaluation-start-", "");
		jQuery(this).next( "img" ).show();

		var data = {
			action: "wpes-start-new-evaluation",
			"wpes-ajax-new-evaluation-start": WPES_JS_VAR_FINAL_SURVEY_NEW_EVALUATION_START_AJAX_NONCE,
			survey_id: current_survey_id,
			post_ID: jQuery("#post_ID").val(),
			post_type: jQuery("#post_type").val(),
		};

		/**	Launch ajax request */
		jQuery.post( ajaxurl, data, function(response) {
			if ( response[ 'status' ] ) {
				jQuery(".wpes-survey-main-container").html( response[ 'output' ] );
			}
			jQuery(this).next( "img" ).hide();
		}, 'json');
	});

	/**	Add listener on new evaluation starting button	*/
	jQuery( document ).on("click", ".wpes-survey-selection-button", function( e ){
		e.preventDefault();

		jQuery( this ).closest( ".wpes-bloc-loader" ).addClass( "wpes-bloc-loading" );

		var current_survey_id = jQuery( this ).closest( ".wpes-survey-selection" ).children( "select#wpes-survey-choice" ).val();

		var data = {
			action: "display_full_survey",
			_wpnonce: jQuery( this ).data( "nonce" ),
			survey_id: current_survey_id,
			post_ID: jQuery("#post_ID").val(),
			post_type: jQuery("#post_type").val(),
		};
		/**	Launch ajax request */
		jQuery.post( ajaxurl, data, function(response) {
			if ( response[ 'success' ] ) {
				jQuery( ".wpes-survey-change-button" ).removeClass( "hidden" );
				jQuery( ".wpes-survey-selection" ).remove();
				jQuery( ".wpes-survey-main-container" ).html( response[ 'data' ][ 'output' ] );
			}

			jQuery( "#" + jQuery("#post_type").val() + "-survey-association div.inside .wpes-bloc-loader" ).removeClass( "wpes-bloc-loading" );
		}, 'json');
	});

	jQuery( document ).on( "click", ".wpes-final-survey-view-in-progress-evaluation" , function( event ) {
		event.preventDefault();

		jQuery( "select#wpes-survey-choice option[value=" + jQuery( this ).data( "id" ) + "]" ).prop( "selected", "selected" );
		jQuery( ".wpes-survey-selection-button" ).click();
	});

	jQuery( document ).on( "click", ".wpes-survey-change-button" , function( event ) {
		event.preventDefault();

		jQuery( this ).closest( ".wpes-bloc-loader" ).addClass( "wpes-bloc-loading" );
		var data = {
			action: "wpes_survey_list",
			_wpnonce: jQuery( this ).data( "nonce" ),
			post_ID: jQuery("#post_ID").val(),
		};
		/**	Launch ajax request */
		jQuery.post( ajaxurl, data, function(response) {
			if ( response[ 'success' ] ) {
				jQuery("#" + jQuery("#post_type").val() + "-survey-association div.inside").html( response[ 'data' ][ 'output' ] );
			}
		}, 'json');
	});

	/**	Add listener on evaluation close button	*/
	jQuery( document ).on("click", ".wpes-final-survey-evaluation-close-button", function( e ) {
		e.preventDefault();
		var current_survey = jQuery(this).closest(".wpes-survey-current-state").attr("id").replace("wpes-survey-current-state-", "");
		var current_element = jQuery( "#post_ID" ).val();

		if ( ( (jQuery("#wpes-final-survey-" + current_survey + "-current-progression").val() == 100) && confirm( wpes_convert_html_accent( WPES_JS_VAR_FINAL_SURVEY_EVALUATION_CLOSE_ALL_ANSWER ) ) )
			|| confirm( wpes_convert_html_accent( WPES_JS_VAR_FINAL_SURVEY_EVALUATION_CLOSE_NOT_ALL_ANSWER ) ) ) {
			jQuery(this).next( "img" ).show();

			var data = {
				action: "wpes-close-evaluation",
				"wpes-ajax-close-evaluation": WPES_JS_VAR_FINAL_SURVEY_EVALUATION_CLOSE_AJAX_NONCE,
				survey_id: current_survey,
				post_ID: current_element,
			};

			/**	Launch ajax request */
			jQuery.post( ajaxurl, data, function(response) {
				if ( response[ 'status' ] ) {
					jQuery("#" + jQuery( "#post_type" ).val() + "-survey-association .inside div.wpes-survey-container").html( response[ 'output' ] );
				}
				jQuery(this).next( "img" ).hide();
			}, 'json');
		}
	});

	/**	Add listener on export link	*/
	jQuery( document ).on( "click", "body.post-php .wpes-final-survey-evaluation-view-export-button", function( e ){
		e.preventDefault();
		jQuery( "#wpes-final-survey-evaluation-export-message-" + jQuery(this).closest( "div.wpes-audit-result-export-container" ).children( "input[name=wpes-final-survey-evaluation-view-final-survey-id]" ).val() ).html( "" );
		jQuery(this).closest( "div.wpes-audit-result-export-container" ).children( "img.wpes-loading-picture" ).show();

		var export_type = jQuery( this ).closest( "li" ).attr( "class" ).replace( "wpes-final-survey-evaluation-view-export-to-", "" );
		var data = {
			action: "wpes-ajax-final-survey-evaluation-result-export",
			survey_id: jQuery(this).closest( "div.wpes-audit-result-export-container" ).children( "input[name=wpes-final-survey-evaluation-view-survey-id]" ).val(),
			final_survey_id: jQuery(this).closest( "div.wpes-audit-result-export-container" ).children( "input[name=wpes-final-survey-evaluation-view-final-survey-id]" ).val(),
			evaluation_state: jQuery(this).closest( "div.wpes-audit-result-export-container" ).children( "input[name=wpes-final-survey-evaluation-view-evaluation-state]" ).val(),
			evaluation_id: jQuery(this).closest( "div.wpes-audit-result-export-container" ).children( "input[name=wpes-final-survey-evaluation-view-evaluation-id]" ).val(),
			element_id: jQuery(this).closest( "div.wpes-audit-result-export-container" ).children( "input[name=wpes-final-survey-evaluation-view-element-id]" ).val(),
			export_type: export_type,
		};
		jQuery.post( ajaxurl, data, function( response ){
			jQuery( "img.wpes-loading-picture-" + response[ 'final_survey_id' ] ).hide();
			if ( (true == response[ 'status' ]) && ( "" != response[ 'output' ] ) )  {
				jQuery( ".wpes-existing-export-container-" + response[ 'final_survey_id' ] ).html( response[ 'output' ] );
			}
			jQuery( "#wpes-final-survey-evaluation-export-message-" + response[ 'final_survey_id' ] ).html( response[ 'message' ] );
			setTimeout(function(){
				jQuery( "#wpes-final-survey-evaluation-export-message-" + response[ 'final_survey_id' ] ).html( "" );
			}, '2500');
		}, "json");
	} );

	/**	Add listener on cancel answer button*/
	jQuery( document ).on( "click", ".wpes-answer-cancel-button", function(){
		var current_issue_id = jQuery(this).attr("id").replace("wpes-answer-cancel-button-", "");
		var current_state = jQuery(".wpes-issue-final-answer-" + current_issue_id + "-answer-state").val();

		if ( current_state == 'edition' ) {
			jQuery(".wpes-final-survey-issue-content-" + current_issue_id).html( "" );
			jQuery( "#wpes-final-survey-issue-edit-answer-" + current_issue_id).show();
		}
		else {
			jQuery(".wpes-survey-issue-response-table-details-" + current_issue_id).hide();
		}

		jQuery("#wpes-survey-issue-response-table-" + current_issue_id + " .wpes-final-survey-answer-container input[type=radio], #wpes-survey-issue-response-table-" + current_issue_id + " .wpes-final-survey-answer-container input[type=checkbox]").each(function(){
			jQuery(this).prop("checked", false);
		});

		jQuery("#wpes-survey-issue-response-table-" + current_issue_id + " .wpes-final-survey-answer-container input[type=text], #wpes-survey-issue-response-table-" + current_issue_id + " .wpes-final-survey-answer-container select, input[type=text].wpes-issue-answer-input, textarea.wpes-issue-answer-input").each(function(){
			jQuery(this).val("");
		});

		return false;
	});

	jQuery( document ).on("click", ".wpes-final-survey-evaluation-list-complete-list-opener", function(){
		jQuery( this ).closest( "ul" ).children( "li.wpes-hide" ).each(function(){
			jQuery(this).slideDown( "slow" );
		});
		jQuery( this ).hide();

		return false;
	});

	/**	Add listener on answers' input in order to display complete form when choosing an answer	*/
	jQuery( document ).on( "click", ".wpes-final-survey-answer-container input", function() {
		var current_issue_id = jQuery(this).closest("table.wpes-survey-issue-response-table").attr("id").replace("wpes-survey-issue-response-table-", "");
		jQuery(".wpes-survey-issue-response-table-details-" + current_issue_id).show();
	});

	/**	Add listener on final answer history opener	*/
	jQuery( document ).on( "click", ".wpes-final-answer-history-opener", function(){
		var current_answer = jQuery( this ).attr( "id" ).replace( "wpes-final-answer-history-opener-", "" );
		jQuery( this ).append( "  " + jQuery( ".wpes-final-survey-loader-picto" ).html() );

		var data = {
			action: "wpes-ajax-survey-view-issue-history",
			"wpes-ajax-issue-history-view-nonce": WPES_JS_VAR_FINAL_SURVEY_ISSUE_AJAX_NONCE,
			issue_id: current_answer,
			post_ID: jQuery( "#post_ID" ).val(),
			survey_id: jQuery( "#wpes-final-survey-identifier-for-survey-" + jQuery( this ).closest( "div.postbox" ).attr( "id" ).replace( jQuery( "#post_type" ).val() + "-survey-association-", "" ) ) .val(),
		};
		jQuery.post( ajaxurl, data, function(response) {
			jQuery( "#wpes-final-answer-history-container-" + current_answer + " .wpes-final-answer-history-content" ).html( response );
			jQuery( "#wpes-final-answer-history-container-" + current_answer ).slideDown();

			jQuery( "#wpes-final-answer-history-opener-" + current_answer + " img " ).remove();
			jQuery( "#wpes-final-answer-history-opener-" + current_answer ).hide();
		});
	});

	/**	Add listener on close answer history button	*/
	jQuery( document ).on( "click", ".wpes-final-answer-history-closer", function(){
		var current_answer_id = jQuery( this ).closest( "div" ).attr( "id" ).replace( "wpes-final-answer-history-container-", "" );
		jQuery( "#wpes-final-answer-history-opener-" + current_answer_id ).show();
		jQuery( "#wpes-final-answer-history-container-" + current_answer_id + " .wpes-final-answer-history-content" ).html( "" );
		jQuery( "#wpes-final-answer-history-container-" + current_answer_id ).slideUp();
	});


	/**	Add listener on item expander/collapser click	*/
/*	jQuery( document ).on( "click", ".wpes-nestable-button.expander", function(){
		if ( !jQuery( this ).hasClass( "expanded" ) ) {
			jQuery( this ).addClass( "expanded" );
			var current_node = jQuery( this ).closest( "li" ).attr( "data-id" );
			var data = {
				action: "wpes-ajax-load-children",
				node_id: current_node,
			};
			jQuery.post( ajaxurl, data, function( response ){
				jQuery( ".wpes-custom-tree-list-" + response[ 'item_id' ] ).html( response[ 'output' ] );
				setTimeout( function() {
					jQuery(".wpes-custom-tree").nestable().init();
				}, "1500" );
			}, "json");
		}
	});*/

});
