<?php if ( empty( $from ) || ( 'export' != $from ) ) : ?>
<div id="wpes-audit-result-export-container" class="clear alignright wpes-audit-result-export-container" >
<?php
	$element_id = !empty( $_REQUEST ) && !empty( $_REQUEST[ 'post_id' ] ) ? $_REQUEST[ 'post_id' ] : 0;
	$survey_id = $_REQUEST[ 'survey_id' ];
	$final_survey_id = $_REQUEST[ 'final_survey_id' ];
	$evaluation_id = $_REQUEST[ 'evaluation_id' ];
	$current_evaluation_state = 'closed';
	$associated_element_type = !empty( $evaluation_to_display ) && !empty( $evaluation_to_display[ 'element_type' ] ) ? $evaluation[ 'element_type' ] : '' ;

	require_once( $this->get_template_part( "backend", "final-survey/export", "link" ) );
?>
</div>
<?php endif; ?>

<div class="" ><?php echo sprintf( __('Started by %1$s on %2$s', 'wp_easy_survey'), $user_data->display_name, mysql2date( sprintf( __( '%1$s \a\t %2$s', 'wp_easy_survey' ), get_option( 'date_format' ), get_option( 'time_format' ) ), $evaluation_to_display[ 'date_started' ], true ) ); ?></div>
<div class="" ><?php echo sprintf( __('Closed by %1$s on %2$s', 'wp_easy_survey'), $user_closed_data->display_name, mysql2date( sprintf( __( '%1$s \a\t %2$s', 'wp_easy_survey' ), get_option( 'date_format' ), get_option( 'time_format' ) ), $evaluation_to_display[ 'date_closed' ], true ) ); ?></div>

<div class="wpes-clear" ><?php echo	$this->display_final_survey_statistics_content( $evaluation_to_display[ 'survey_id' ], $this->total_issue_number, $this->issues_with_answers, $from ); ?></div>
<div class="wpes-clear" ><?php _e('Evaluation final result', 'wp_easy_survey'); ?></div>
<?php echo $this->display( 'wpes_survey_issue_display_in_associate_element_container', array( 'ISSUES_LIST' => $content ) ); ?>