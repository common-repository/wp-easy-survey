<div class="wpes-bloc-loader" >
	<button class="wpes-survey-change-button wpes-bton-fifth hidden" data-nonce="<?php echo wp_create_nonce( 'wpes-survey-list' ); ?>" data-post_id="<?php echo $post->ID; ?>" ><i class="dashicons dashicons-arrow-left-alt" ></i> <?php _e( 'Back to survey list', 'wp_easy_survey' ); ?></button>

	<div class="wpes-survey-selection">
		<?php if ( !empty( $not_started_survey ) ) : ?>
		<select id="wpes-survey-choice" name="wpes_survey_choice" >
			<?php foreach ( $not_started_survey as $index => $survey ) : ?>
			<option value="<?php echo $survey->post_id; ?>" ><?php echo get_the_title( $survey->post_id ); ?></option>
			<?php endforeach; ?>
		</select>
		<button class="wpes-survey-selection-button wpes-bton-first" data-nonce="<?php echo wp_create_nonce( 'wpes-survey-selection' ); ?>" ><?php _e( 'Use selected survey', 'wp_easy_survey' ); ?></button>
		<?php endif; ?>
	</div>

	<div class="wpes-survey-main-container" >
		<?php if ( !empty( $already_started_survey_list ) ) : ?>
			<?php foreach ( $already_started_survey_list as $survey_id => $survey_infos ) : ?>
			<?php $current_element_evaluation = $survey_infos[ 'audit' ]; ?>
		<div class="wpes-custom-dialog wpes-final-survey-list-for-association">
			<h3 class="hndle ui-sortable-handle"><span><?php printf( __( 'Existing evaluation for survey : %s', 'wp_easy_survey' ), get_the_title( $survey_id ) ); ?></span></h3>
			<div class="inside">
			<?php	if ( !empty( $current_element_evaluation ) && !empty( $current_element_evaluation[ 'in_progress' ] ) ) : ?>
			<?php
				if ( count( $current_element_evaluation [ 'in_progress' ] ) == 1 ) :
					$evaluation_infos = $current_element_evaluation[ 'in_progress' ][ 1 ];
				endif;
				$user_data = get_userdata( $evaluation_infos[ 'user' ] );
				printf( __('Started by %1$s on %2$s', 'wp_easy_survey'), $user_data->display_name, mysql2date( sprintf( __( '%1$s \a\t %2$s', 'wp_easy_survey' ), get_option( 'date_format' ), get_option( 'time_format' ) ), $evaluation_infos[ 'date_started' ], true ) );
			?>
			<button class="wpes-final-survey-view-in-progress-evaluation" data-id="<?php echo $survey_id; ?>" ><?php _e( 'View survey details', 'wp_easy_survey' ); ?></button>
			<?php endif; ?>

			<?php $parent_survey_id = $survey_id;  require( $this->get_template_part( "backend", "final-survey/evaluation", "closed" ) ); ?>
			</div>
		</div>
			<?php endforeach; ?>
		<?php endif; ?>

	</div>
	<div class="clear" ></div>
</div>
