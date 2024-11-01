<div class="wpes-final-survey-stats-container" >
	<h3 class="hndle"><span><?php _e('Survey statistics', 'wp_easy_survey'); ?></span></h3>
	<div class="inside">
		<div class="clear alignright wpes-audit-result-export-container" >
		<?php
			$element_id = !empty( $_GET ) && !empty( $_GET[ 'post' ] ) ? $_GET[ 'post' ] : $associated_element_id;
			$final_survey_id = $survey_id;
			$survey_id = $parent_survey_id;
			$current_evaluation_state = 'in_progress';
			$evaluation_id = 1;
			require( $this->get_template_part( "backend", "final-survey/export", "link" ) );
		?>
		</div>
		<div class="clear wpes-audit-the-stats" >
			<?php require( $this->get_template_part( "backend", "final-survey-result/stats", "detail" ) ); ?>
		</div>
	</div>
</div>