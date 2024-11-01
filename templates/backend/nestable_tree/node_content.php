<div class="wpes-survey-node-main-container" >
	<?php echo $node_name; ?><?php if ( !empty($node->post_content) ) : ?><span class="wpes-survey-item-description" > - <?php echo $node->post_content; ?></span><?php endif; ?>
</div>
<div class="wpes-survey-node-actions-container" >
	<img src="<?php echo WPEASYSURVEY_COMMON_MEDIAS_URL . 'supprimer.png'; ?>" alt="<?php _e('Delete', 'wp_easy_survey'); ?>" title="<?php _e('Delete', 'wp_easy_survey'); ?>" class="alignright wpes-delete-issue" id="wpes-delete-issue-<?php echo $node->ID; ?>" />
	<!-- <img src="<?php echo WPEASYSURVEY_COMMON_MEDIAS_URL . 'dissocier.png'; ?>" class="alignright wpes-dissociate-issue" id="wpes-dissociate-issue-<?php echo $node->ID; ?>" alt="<?php _e('Dissociate issue', 'wp_easy_survey'); ?>" title="<?php _e('Dissociate issue', 'wp_easy_survey'); ?>" /> -->
	<a href="<?php echo admin_url('admin-ajax.php'); ?>?action=wpes-ajax-survey-element-edit&amp;wpes-ajax-survey-item-edition-nonce=<?php echo $node_edition_nonce; ?>&amp;post_parent=<?php echo $node_parent_id; ?>&amp;post_id=<?php echo $node->ID; ?>&amp;width=600&amp;height=300"
			title="<?php echo sprintf( __('Edit %s', 'wp_easy_survey'), $node_name ); ?>" class="thickbox wpes-cpt-custom-edit-button alignright">
		<img src="<?php echo WPEASYSURVEY_COMMON_MEDIAS_URL . 'editer.png'; ?>" alt="<?php _e('Edit', 'wp_easy_survey'); ?>" title="<?php _e('Edit', 'wp_easy_survey'); ?>" />
	</a>
	<a href="<?php echo admin_url('admin-ajax.php'); ?>?action=wpes-ajax-survey-item-add&amp;wpes-ajax-survey-item-addition-nonce=<?php echo $node_adition_nonce; ?>&amp;post_parent=<?php echo $node->ID; ?>&amp;post_parent_localisation=tree&amp;width=600&amp;height=300"
			title="<?php echo sprintf( __('Add a new issue for %s', 'wp_easy_survey'), $node_name ); ?>" class="thickbox wpes-cpt-custom-add-button alignright">
		<img src="<?php echo WPEASYSURVEY_COMMON_MEDIAS_URL . 'ajouter-question.png'; ?>" alt="<?php _e('Add new issue', 'wp_easy_survey'); ?>" title="<?php _e('Add new issue', 'wp_easy_survey'); ?>" />
	</a>
</div>