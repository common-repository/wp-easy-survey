<?php
	$stats_answers_type = unserialize( WPES_ANSWER_TYPE );

	$given_answers = 0;
	$different_answer = null;
	if ( !empty( $issues_with_answers ) ) {
		foreach ( $issues_with_answers as $issue_ID ) {
			/**	Get last answer for current issue	*/
			$last_answer = $this->answers->get_last_answer_for_issue( $issue_ID );
			if ( !empty( $last_answer ) ) {
				/**	Check answer state	*/
				$current_state = $this->answers->check_answer_state( $issue_ID, $last_answer[ $issue_ID ][ 'content' ][ 'answers' ] );

				if ( empty($different_answer[$current_state]) ) {
					$different_answer[$current_state] = 1;
				}
				else {
					$different_answer[$current_state]++;
				}

				$given_answers++;
			}
		}
	}
	$different_answer['not_answered'] = ($survey_issue_number - $given_answers) - (!empty($different_answer['not_answered']) ? $different_answer['not_answered'] : 0);

	/**	Progression calculation */
	$final_survey_progession = number_format( ( $given_answers * 100 ) / $survey_issue_number, 1 );
	$final_survey_progession = substr( $final_survey_progession, -2 ) == '.0' ? substr( $final_survey_progession, 0, -2 ) : $final_survey_progession;
?>
<?php if( !empty( $given_answers ) ) : ?>
	<div class="wpes-final-survey-stats-progression" >
		<div>
			<input type="hidden" value="<?php echo $final_survey_progession; ?>" id="wpes-final-survey-<?php echo $survey_id; ?>-current-progression" />
			<?php if ( empty( $from ) || ( 'export' != $from ) ) : ?>
				<div class="alignleft wpes-survey-progression-0" ><?php _e('0%', 'wp_easy_survey'); ?></div>
				<div class="alignright wpes-survey-progression-100" ><?php _e('100%', 'wp_easy_survey'); ?></div>
				<div class="alignleft wpes-survey-progression-current wpes-survey-progression-current-<?php echo $final_survey_progession; ?>" style="text-align: right; width:<?php echo $final_survey_progession; ?>%;" ><?php echo $final_survey_progession; ?>%</div>
			<?php else : ?>
				<table class="wpes-survey-progression" >
					<tr>
						<td class="wpes-survey-progression-0" style="width:<?php echo $final_survey_progession; ?>%;" ><?php _e('0%', 'wp_easy_survey'); ?></td>
						<td class="wpes-survey-progression-current" ><?php echo $final_survey_progession; ?>%</td>
						<td class="wpes-survey-progression-100" ><?php _e('100%', 'wp_easy_survey'); ?></td>
					</tr>
				</table>
			<?php endif; ?>
			<div class="wpes-clear wpes-final-survey-progressbar" ><div class="wpes-final-survey-progressbar-progression" style="width:<?php echo $final_survey_progession; ?>%;" >&nbsp;</div></div>
		</div>
	</div><!-- wpes-final-survey-stats-progression -->


	<?php
		/**	Create the chart with legend	*/
		$pieDefinition = $pieLegend = '';
		foreach ( $stats_answers_type as $state => $state_definition ) :
			$color = $state_definition['color'];
			$count = !empty($different_answer[ $state ]) ? $different_answer[ $state ] : 0;

			/**	Pie definition	*/
			$pieDefinition .= '{ value: ' . $count . ', color:"#' . $color . '" },';

			/**	Pie legend	*/
			ob_start();
	?>
			<li>
	<?php if ( empty( $from ) || ( 'export' != $from ) ) : ?>
				<div class="wpes-final-survey-answer-legend-color alignleft" style="background-color:#<?php echo $color; ?>;" ></div>&nbsp;<?php echo $state_definition['name']; ?> (<?php echo $count; ?>)
	<?php else: ?>
				<table><tr><td><div class="wpes-final-survey-answer-legend-color alignleft" style="background-color:#<?php echo $color; ?>;" ></div></td><td><?php echo $state_definition['name']; ?> (<?php echo $count; ?>)</td></tr></table>
	<?php endif; ?>
			</li>
	<?php
			$pieLegend .= ob_get_contents();
			ob_end_clean();
		endforeach;
	?>
	<div class="wpes-final-survey-stats-answer-repartition" >
		<?php _e('Answer repartition', 'wp_easy_survey'); ?><div class="clear" ></div>
		<ul class="wpes-final-survey-stats-pie-legend alignleft" >
			<?php echo $pieLegend; ?>
		</ul>
	<?php if ( empty( $from ) || ( 'export' != $from ) ) : ?>
		<canvas id="wpes-final-survey-chart-<?php echo $survey_id; ?>" width="200" height="200" ></canvas>
		<script type="text/javascript" >
			jQuery(document).ready(function(){
				var pieData = [<?php echo $pieDefinition; ?>];
				new Chart( document.getElementById("wpes-final-survey-chart-<?php echo $survey_id; ?>").getContext("2d") ).Pie( pieData );
			});
		</script>
	<?php endif; ?>
	</div><!-- wpes-final-survey-stats-answer-repartition -->

<?php
	else:
		_e('This survey has no response for the moment', 'wp_easy_survey');
	endif;
?>