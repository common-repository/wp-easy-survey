	<input type="hidden" name="wpes-final-survey-evaluation-view-element-id" value="<?php echo $element_id; ?>" />
	<input type="hidden" name="wpes-final-survey-evaluation-view-survey-id" value="<?php echo $survey_id; ?>" />
	<input type="hidden" name="wpes-final-survey-evaluation-view-final-survey-id" value="<?php echo $final_survey_id; ?>" />
	<input type="hidden" name="wpes-final-survey-evaluation-view-evaluation-state" value="<?php echo $current_evaluation_state; ?>" />
	<input type="hidden" name="wpes-final-survey-evaluation-view-evaluation-id" value="<?php echo $evaluation_id; ?>" />
	<?php _e( 'Export result', 'wp_easy_survey' ); ?>
	<ul class="wpes-final-evaluation-print" >
		<li class="wpes-final-survey-evaluation-view-export-to-odt" ><a href="#" class="wpes-final-survey-evaluation-view-export-button" ><?php _e( 'ODT', 'wp_easy_survey' ); ?></a></li>
		<!-- <li class="wpes-final-survey-evaluation-view-export-to-pdf" ><a href="#" class="wpes-final-survey-evaluation-view-export-button" ><?php _e( 'PDF', 'wp_easy_survey' ); ?></a></li> -->
	</ul>
	<img class="wpes-hide wpes-loading-picture wpes-loading-picture-<?php echo $final_survey_id; ?>" src="<?php echo admin_url( 'images/loading.gif' ); ?>" alt="enregistrement en cours" />
	<div id="wpes-final-survey-evaluation-export-message-<?php echo $final_survey_id; ?>" ></div>
	<div class="wpes-existing-export-container wpes-existing-export-container-<?php echo $final_survey_id; ?>" ><?php echo $this->display_export_list( $element_id, $survey_id, $final_survey_id, $associated_element_type ); ?></div>