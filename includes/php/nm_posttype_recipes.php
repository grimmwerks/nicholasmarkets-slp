<?php
	
function slp_create_recipe_post_type(){
	register_post_type('nm_recipe',
	array(
		'labels' => array('name'=> __("Recipe"),
							'singular_name'=>__('Recipe'),
							'add_new'=>__('Add Recipe'),
							'add_new_item'=>__('Add New Recipe'),
							'edit'=>__('Edit'),
							'edit_item'=>__('Edit Recipe'),
							'new_item'=>__("New Recipe"),
							'view'=>__('View Recipe'),
							'view_item' => __('View Recipe'),
							'not_found' => __('No Recipes found.'),
							'not_found_in_trash'=>__('No Recipes found in Trash')
							),
		'public'=>true,
		'can_export'=>true,
		'publicly_querable'=>true,
		'exclude_from_search' => false,
		'menu_position'=>5.1,
		'hierarchical' => false,
		'rewrite' => array('slug'=>'recipe','with_front'=>false),
		'has_archive' => 'recipes',
		'supports'=> array('title', 'editor', 'thumbnail', 'comments')
		
			)
	);
	
	

	register_taxonomy( 'cuisine', 'nm_recipe', 
		array( 
			'hierarchical' => true, 
			'labels' => array(
				'name' => __('Cuisine'),
				'singular_name' => __('Cuisine'),
				'search_items' => __('Search Cuisines'),
				'all_items' => __('All Cuisines'),
				'parent_item' => __('Parent Cuisine'),
				'parent_item_colon' => __('Parent Cuisine'),
				'edit_item' => __('Edit Cuisines'),
				'update_item' => __('Update Cuisine'),
				'add_new_item' => __('Add Cuisines'),
				'new_item_name' => __('New Cuisine'),
			),
			'query_var' => true, 
			'rewrite' => true 
		) 
	);
	register_taxonomy( 'meal', 'nm_recipe', 
		array( 
			'hierarchical' => true, 
			'labels' => array(
				'name' => __('Meal'),
				'singular_name' => __('Meal'),
				'search_items' => __('Search Meals'),
				'all_items' => __('All Meals'),
				'parent_item' => __('Parent Meal'),
				'parent_item_colon' => __('Parent Meal'),
				'edit_item' => __('Edit Meal'),
				'update_item' => __('Update Meal'),
				'add_new_item' => __('Add Meals'),
				'new_item_name' => __('New Meal'),
			),
			'query_var' => true, 
			'rewrite' => true 
		) 
	);	
	
	
}

add_action('init', 'slp_create_recipe_post_type');

?>