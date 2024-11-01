<li class="wpes-custom-tree-item wpes-custom-tree3-item wpes-custom-tree-item-<?php echo $item->ID; ?>" data-type="<?php echo $item->post_type; ?>" data-id="<?php echo $item->ID; ?>" >
	<div class="wpes-custom-tree-handle wpes-custom-tree3-handle" ></div>
	<div class="wpes-custom-tree3-content" id="wpes-survey-node-<?php echo $item->ID; ?>">
		<?php echo $this->display_survey_node( $item ); ?>
	</div>
	<?php
		$children_list = $this->get_issues( $item->ID );
		if ( !empty($children_list) && $children_list->have_posts() ) {
			$parent_id = $item->ID;
			$list_children = WPES_LIST_ALL;
			if ( $list_children ) {
				$survey_items_list = $children_list->posts;
			}
			require( $this->get_template_part( "backend", "nestable_tree/node_container" ) );
		}
	?>
</li>