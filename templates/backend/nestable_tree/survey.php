<?php if ( !empty($node_list) ) :  ?>
	<input type="hidden" name="wpes-survey-order" value="<?php echo str_replace( '"', "'", get_post_meta( $post->ID, '_wpes_survey_order', true) ); ?>" id="wpes-survey-order" />
	<div class="wpes-custom-tree-root-node wpes-custom-tree" >
		<?php echo $node_list; ?>
	</div>
	<script type="text/javascript" >
		jQuery(document).ready(function(){
			initialize_nestable( true );
		});
	</script>
<?php else: ?>
	<?php _e('This survey is empty for the moment.'); ?>
	<input type="hidden" name="wpes-survey-nothing-created" value="empty" />
<?php endif; ?>

<div class="wpes-root-issue-actions" >
	<a href="<?php echo admin_url('admin-ajax.php'); ?>?action=wpes-ajax-survey-item-add&amp;wpes-ajax-survey-item-addition-nonce=<?php echo $wpes_survey_node_addition_nonce; ?>&amp;post_parent=<?php echo $post->ID; ?>&amp;post_parent_localisation=<?php echo ( empty($node_list) ? 'empty_' : '') . 'root'; ?>&amp;width=600&amp;height=300" title="<?php echo sprintf( __('Add a new issue for %s', 'wp_easy_survey'), get_the_title( $post->ID ) ); ?>" class="thickbox wpes-cpt-custom-add-button alignright">
		<img src="<?php echo WPEASYSURVEY_COMMON_MEDIAS_URL . 'ajouter-question.png'; ?>" alt="<?php _e('Add new issue', 'wp_easy_survey'); ?>" title="<?php _e('Add new issue', 'wp_easy_survey'); ?>" />
	</a>
</div>
<div class="wpes-clear" ></div>