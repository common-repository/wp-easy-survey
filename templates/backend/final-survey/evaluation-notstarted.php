<div class="wpes-final-survey-evaluation-starter-button-container" >
	<?php _e('There are no evaluation started for the moment', 'wp_easy_survey'); ?>
	<button class="wpes-final-survey-new-evaluation-start" id="wpes-final-survey-new-evaluation-start-<?php echo $survey_id; ?>" >
		<?php _e('Start a new evaluation with the current survey structure', 'wp_easy_survey'); ?>
	</button>
	<img class="wpes-hide wpes-loading-picture" src="<?php echo admin_url('images/loading.gif'); ?>" alt="<?php _e('save in progress', 'wp_easy_survey'); ?>" />
</div>