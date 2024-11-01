<ol class="wpes-custom-tree-list wpes-custom-tree-list-<?php echo $parent_id; ?>" ><?php
	if ( $list_children ) {
		foreach ( $survey_items_list as $item ) {
			require( $this->get_template_part( "backend", "nestable_tree/node" ) );
		}
		wp_reset_query();
	}
	else {
?>
	<li><img src="<?php echo admin_url( "images/loading.gif" ); ?>" alt="<?php _e( 'Loading children', 'wp_easy_survey' ); ?>" /></li>
<?php
	}
?></ol>