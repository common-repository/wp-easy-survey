<?php if ( !empty($current_element_evaluation[ 'closed' ]) ) : ?>
	<div class="wpes-final-survey-stats-closed-evaluation-container" >
		<?php _e('Closed evaluation list', 'wp_easy_survey'); ?>
		<ul>
		<?php
			$i = 1;
			foreach ( $current_element_evaluation[ 'closed' ] as $closed_evaluation_index => $evaluation_informations ) :
				$user_data = get_userdata( $evaluation_informations[ 'user' ] );
				$user_closed_data = get_userdata( $evaluation_informations[ 'user_closed' ] );
		?>
			<li class="wpes-final-survey-closed-evaluation-list<?php echo ((1 == $i) ? '' : ' wpes-hide'); ?>" >
				<div class="alignleft" >
					<?php echo sprintf( __( 'Started by %1$s on %2$s', 'wp_easy_survey' ), $user_data->display_name, mysql2date( sprintf( __( '%1$s \a\t %2$s', 'wp_easy_survey' ), get_option( 'date_format' ), get_option( 'time_format' ) ), $evaluation_informations[ 'date_started' ], true ) ); ?><br/>
					<?php echo sprintf( __( 'Closed by %1$s on %2$s', 'wp_easy_survey' ), $user_closed_data->display_name, mysql2date( sprintf( __( '%1$s \a\t %2$s', 'wp_easy_survey' ), get_option( 'date_format' ), get_option( 'time_format' ) ), $evaluation_informations[ 'date_closed' ], true ) ); ?>
				</div>
				<div class="wpes-final-survey-closed-evaluation-list-viewer" >
					 - <a href="<?php echo admin_url('admin-ajax.php'); ?>?action=<?php echo $ajax_action; ?>&amp;wpes-ajax-survey-final-result-view-nonce=<?php echo wp_create_nonce( 'wpes-ajax-view-survey-results' ); ?>&amp;post_id=<?php echo $associated_element_id; ?>&amp;survey_id=<?php echo $parent_survey_id; ?>&amp;final_survey_id=<?php echo $evaluation_informations[ 'survey_id' ]; ?>&amp;evaluation_id=<?php echo $closed_evaluation_index; ?>&amp;width=600&amp;height=800" title="<?php _e('View'); ?>" class="thickbox"><?php _e('View'); ?></a>
				</div>
				<div class="wpes-clear"></div>
				<?php if ( (1 == $i) && ( count($current_element_evaluation[ 'closed' ]) > 1 ) ) : ?>
					<button class="wpes-final-survey-evaluation-list-complete-list-opener" ><?php _e('Complete list', 'wp_easy_survey'); ?></button>
				<?php endif; ?>
			</li>
		<?php
			$i++;
			endforeach;
		?>
		</ul>
	</div>
<?php endif; ?>
