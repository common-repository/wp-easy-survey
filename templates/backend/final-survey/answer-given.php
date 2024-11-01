<table class="wpes-final-survey-last-answer-detail-display" >
	<tr>
		<td<?php echo ( !empty($from) && ($from == 'history') ? ' colspan="2"' : '' ); ?>>
	<?php if ( empty( $from ) || ( 'export' != $from ) ) : ?>
			&nbsp;&gt;&nbsp;
			<div class="wpes-final-survey-answer-legend-color alignleft" style="background-color:#<?php echo $answers_type[ $answer_state ][ 'color' ]; ?>;" ></div>
<?php
			echo sprintf(
				 __('The %1$s %2$s answered', 'wp_easy_survey'),
				'<span class="wpes-answer-info" >' . mysql2date( sprintf( __( '%1$s \a\t %2$s', 'wp_easy_survey' ), get_option( 'date_format' ), get_option( 'time_format' ) ), $given_answer_content['content']['answer_date'], true) . '</span>',
				'<span class="wpes-answer-info" >' . $user_data->display_name . '</span>'
			);
?>
	<?php else: ?>
			<table style="border: 1px solid blue;" >
				<tr>
					<td>&nbsp;&gt;&nbsp; <div class="wpes-final-survey-answer-legend-color" style="background-color:#<?php echo $answers_type[ $answer_state ][ 'color' ]; ?>;" ></div></td>
					<td><?php echo sprintf( __('The %1$s %2$s answered', 'wp_easy_survey'), mysql2date( sprintf( __( '%1$s \a\t %2$s', 'wp_easy_survey' ), get_option( 'date_format' ), get_option( 'time_format' ) ), $given_answer_content['content']['answer_date'], true) , $user_data->display_name );	?></td>
				</tr>
			</table>
	<?php endif; ?>




<?php /**	In case it is not history, display a link allowing to open history if there are other answers	*/	?>
<?php if ( empty($from) && $given_answer_content['count'] > 1 ) :	 ?>
			<div id="wpes-final-answer-history-opener-<?php echo $issue_ID; ?>" class="wpes-final-answer-history-opener" >
				<?php echo sprintf( __( 'View previous answers (%s)', 'wp_easy_survey' ), ($given_answer_content['count'] - 1) ); ?>
			</div>
<?php endif; ?>

		</td>

<?php if ( !empty( $from ) && ($from == 'history') ) : ?>
	</tr>
	<tr>
<?php endif; ?>

		<td>
			<span class="wpes-final-survey-answer-given" ><?php echo trim( substr( $the_answer, 0, -3 ) ); ?></span>

<?php if ( $given_answer_content['content'][ 'notes' ] ) : ?>
			<div class="wpes-final-survey-answer-given-notes-final-container" >
				<img class="wpes-final-answer-note-picto" src="<?php echo site_url('/'); ?>/wp-includes/images/wlw/wp-comments.png" alt="<?php _e( 'Notes given per user on this answer', 'wp_easy_survey' ); ?>" title="<?php _e( 'Notes given per user on this answer', 'wp_easy_survey' ); ?>" />
				<?php echo $given_answer_content['content'][ 'notes' ]; ?>
			</div><!-- wpes-final-survey-answer-given-notes-final-container -->
<?php endif; ?>
		</td>

<?php if ( empty($from) ) : ?>
		<td >
			<img src="<?php echo WPEASYSURVEY_COMMON_MEDIAS_URL . 'editer.png'; ?>" alt="<?php _e('Edit', 'wp_easy_survey'); ?>" title="<?php _e('Edit', 'wp_easy_survey'); ?>" class="wpes-final-survey-issue-edit-answer" id="wpes-final-survey-issue-edit-answer-<?php echo $issue_ID; ?>" />
		</td>
<?php elseif ( !empty( $from ) && ($from == 'history') ) : ?>
		<td>
			<button class="wpes-final-survey-issue-reuse-answer button-secondary alignright" id="wpes-final-survey-issue-reuse-answer-<?php echo $given_answer_content['meta_id']; ?>" >
				<?php _e('Re-use this answer', 'wp_easy_survey'); ?>
			</button>
		</td>
<?php endif; ?>
	</tr>



<?php /**	Display history container in case issue has several answers	*/ ?>
<?php if ( empty($from) && ( $given_answer_content['count'] > 1 ) ) : ?>
	<tr>
	    <td colspan="3" >
	    	<div id="wpes-final-answer-history-container-<?php echo $issue_ID; ?>" class="wpes-final-answer-history" >
	    		<h3><?php _e( 'History for answer', 'wp_easy_survey' ); ?><span class="alignright wpes-final-answer-history-closer" ></span></h3>
	    		<div>
	    			<a href="<?php echo admin_url('admin-ajax.php'); ?>?action=wpes-ajax-survey-view-issue-history&amp;wpes-ajax-issue-history-view-nonce=<?php echo $issue_history_load_nonce; ?>&amp;post_ID=<?php echo $element_id; ?>&amp;width=800&amp;height=600&amp;issue_id=<?php echo $issue_ID; ?>&amp;survey_id=<?php echo $survey_id; ?>" title="<?php _e('Complete answer history', 'wp_easy_survey'); ?>" class="thickbox wpes-final-answer-history-full-opener"></a>
	    			<div class="clear wpes-final-answer-history-content" ></div>
	    		</div>
	    	</div>
	    </td>
	</tr>
<?php endif; ?>

</table><!-- wpes-final-survey-last-answer-detail-display -->