<div class="wpes-final-survey-evaluation-state" >
	<h3 class="hndle"><span><?php _e('Survey evaluation informations', 'wp_easy_survey'); ?></span></h3>
	<div class="inside">
<?php if ( !empty($current_element_evaluation) ) : ?>

	<?php /**	Evaluation in progress	*/	?>
	<?php if ( !empty($evaluation_infos) ) : ?>
		<?php $user_data = get_userdata( $evaluation_infos[ 'user' ] ); ?>
		<div class="wpes-final-survey-stats-current-evaluation-container" >
			<?php printf( __('Started by %1$s on %2$s', 'wp_easy_survey'), $user_data->display_name, mysql2date( sprintf( __( '%1$s \a\t %2$s', 'wp_easy_survey' ), get_option( 'date_format' ), get_option( 'time_format' ) ), $evaluation_infos[ 'date_started' ], true ) ); ?>
			<button class="wpes-final-survey-evaluation-close-button" id="wpes-final-survey-evaluation-close-button-<?php echo $survey_id; ?>" >
				<?php _e( 'Close evaluation', 'wp_easy_survey' ); ?>
			</button>
			<img class="wpes-hide wpes-loading-picture" src="<?php echo admin_url('images/loading.gif'); ?>" alt="<?php _e('save in progress', 'wp_easy_survey'); ?>" />
		</div>
	<?php else: ?>
		<?php require( $this->get_template_part( "backend", "final-survey/evaluation", "notstarted" ) ); ?>
	<?php endif; ?>

	<?php /**	Closed evaluation	*/	?>
	<?php require( $this->get_template_part( "backend", "final-survey/evaluation", "closed" ) ); ?>

<?php else: ?>
	<?php require( $this->get_template_part( "backend", "final-survey/evaluation", "notstarted" ) ); ?>
<?php endif; ?>
	</div>
</div>
