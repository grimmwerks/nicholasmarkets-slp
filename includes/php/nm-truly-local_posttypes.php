<?php
	
function slp_create_truly_local_post_type(){
	register_post_type('nm_truly_local',
	array(
		'labels' => array('name'=> __("Truly Local"),
							'singular_name'=>__('Truly Local'),
							'add_new'=>__('Add Truly Local'),
							'add_new_item'=>__('Add New Truly Local'),
							'edit'=>__('Edit'),
							'edit_item'=>__('Edit Truly Local'),
							'new_item'=>__("New Truly Local"),
							'view'=>__('View Truly Local'),
							'view_item' => __('View Truly Local'),
							'not_found' => __('No Truly Locals found.'),
							'not_found_in_trash'=>__('No Truly Locals found in Trash')
							),
		'public'=>true,
		'can_export'=>true,
		'publicly_querable'=>true,
		'exclude_from_search' => false,
		'menu_position'=>5.1,
		'hierarchical' => false,
		'rewrite' => array('slug'=>'truly-local','with_front'=>false),
		'has_archive' => true,
		'supports'=> array('title', 'editor', 'thumbnail', 'excerpt', 'comments')
		
			)
	);
	
	

	register_taxonomy( 'business-type', 'nm_truly_local', 
		array( 
			'hierarchical' => true, 
			'labels' => array(
				'name' => __('Business Type'),
				'singular_name' => __('Business Type'),
				'search_items' => __('Search Business Types'),
				'all_items' => __('All Business Types'),
				'parent_item' => __('Parent Business Type'),
				'parent_item_colon' => __('Parent Business Type'),
				'edit_item' => __('Edit Business Types'),
				'update_item' => __('Update Business Types'),
				'add_new_item' => __('Add Business Type'),
				'new_item_name' => __('New Business Type'),
			),
			'query_var' => true, 
			'rewrite' => true 
		) 
	);

	register_taxonomy( 'town', 'nm_truly_local', 
		array( 
			'hierarchical' => false, 
			'labels' => array(
				'name' => __('Town'),
				'singular_name' => __('Town'),
				'search_items' => __('Town'),
				'all_items' => __('All Towns'),
				'parent_item' => __('Parent Town'),
				'parent_item_colon' => __('Parent Town'),
				'edit_item' => __('Edit Town'),
				'update_item' => __('Update Town'),
				'add_new_item' => __('Add Town'),
				'new_item_name' => __('New Town'),
			),
			'query_var' => true, 
			'rewrite' => true 
		) 
	);
	
	
	
}

add_action('init', 'slp_create_truly_local_post_type');

?>