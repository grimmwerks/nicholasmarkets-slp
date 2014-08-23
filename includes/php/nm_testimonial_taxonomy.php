<?php 

function nm_testimonial_taxonomy(){
	register_taxonomy( 'testimonial_type', 'testimonial', 
		array( 
			'hierarchical' => true, 
			'labels' => array(
				'name' => __('Testimonial Type'),
				'singular_name' => __('Testimonial Type'),
				'search_items' => __('Search Testimonial Types'),
				'all_items' => __('All Testimonial Types'),
				'parent_item' => __('Parent Testimonial Type'),
				'parent_item_colon' => __('Parent Testimonial Type'),
				'edit_item' => __('Edit Testimonial Types'),
				'update_item' => __('Update Testimonial Type'),
				'add_new_item' => __('Add Testimonial Type'),
				'new_item_name' => __('New Testimonial Type'),
			),
			'query_var' => true, 
			'rewrite' => true 
		) 
	);
}

add_action('init', 'nm_testimonial_taxonomy');
?>