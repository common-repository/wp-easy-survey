<div class="wpes-survey-container" >
	<div class="wpes-survey-title" ><?php echo get_the_title( $survey_id ); ?></div>
	<div class="wpes-survey-content" >
		<?php if ( $associated_item->have_posts() ) : ?>
		<ol class="wpes-survey-issue-list-for-answer" >
			<?php foreach ( $associated_item->posts as $item ) : ?>
				<?php echo $this->display_issue( $item, $associated_element_id, $preview, $survey_id ); ?>
			<?php endforeach; ?>
		</ol>
		<?php else: ?>
			<?php printf( __('There are no issues in this survey for the moment. %s', 'wp_easy_survey'), '<a href="' . admin_url('post.php') . '?post=' . $survey_id . '&amp;action=edit" >' . __('Edit survey', 'wp_easy_survey') . '</a>' ); ?>
		<?php endif; ?>
	</div>
	<div class="wpes-survey-current-state" id="wpes-survey-current-state-<?php echo $survey_id; ?>" >
		<?php
			echo $main_evaluation_informations;
			if ( !$preview ) {
				echo $this->display_final_survey_statistics( $this->total_issue_number, $survey_id, $parent_survey_id, $associated_element_id, ( !empty( $external_evaluation ) && !empty( $external_evaluation[ 'element_type' ] ) ? $external_evaluation[ 'element_type' ] : '' ) );
			}
		?>
	</div>
	<div class="wpes-clear" ></div>
	<input type="hidden" value="<?php echo $survey_id; ?>" name="wpes-final-survey-identifier" id="wpes-final-survey-identifier-for-survey-<?php echo $parent_survey_id ?>" />
	<input type="hidden" value="<?php echo $this->total_issue_number ?>" name="wpes-final-survey-total-answer-to-give" id="wpes-final-survey-total-answer-to-give-for-survey-<?php echo $parent_survey_id ?>" />
	<input type="hidden" value="<?php echo serialize( $this->issues_with_answers ) ?>" name="wpes-final-survey-issues-with-answers" id="wpes-final-survey-issues-with-answers-for-survey-<?php echo $parent_survey_id ?>" />
	<div class="wpes-hide wpes-final-survey-loader-picto" ><img class="wpes-loading-picture" src="<?php echo admin_url('images/loading.gif') ?>" alt="<?php echo __('Loading', 'wp_easy_survey') ?>" /></div>
</div>
