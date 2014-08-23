<?php
/**  
 *
 *      THIS IS THE CUSTOM POST TYPE FOR SLP Report / Lunch
 * 
 */
/*
function slp_create_report_post_type(){
	register_post_type('slp_report',
	array(
		'labels' => array('name'=> __("SLP Reports"),
							'singular_name'=>__('Report'),
							'add_new'=>__('Add Report'),
							'add_new_item'=>__('Add New Report'),
							'edit'=>__('Edit'),
							'edit_item'=>__('Edit Report'),
							'new_item'=>__("New Report"),
							'view'=>__('View Report'),
							'view_item' => __('View Report'),
							'not_found' => __('No Report found.'),
							'not_found_in_trash'=>__('No Report found in Trash')
							),
		'public'=>true,
		'can_export'=>true,
		'publicly_querable'=>true,
		'exclude_from_search' => true,
		'hierarchical' => false,
		'show_in_menu'=> 'school-lunch-program',
		'supports'=> array('')
		)
	);
	
}

add_action('init', 'slp_create_report_post_type');
*/









/**
			Hide the 'Add New' -- not going to be allowed
**/
/*
function hide_add_new() {
	$pt = $_GET['post_type'];
	if('slp_report' == get_post_type() OR $pt=='slp_report')
	  echo '<style type="text/css">
	    #favorite-actions {display:none;}
	    .add-new-h2{display:none;}
	    .tablenav{display:none;}
	    </style>';
}
add_action('admin_head', 'hide_add_new');
*/









/**
	COLUMNS AND SORTING
**/
// adding new columns to view
/*
function slp_report_columns($cols){
	$cols = array(
			'order' => 'Order #',
			'lunchDate' => "Date",
			'student' => "Student",
			'lunch' => 'Lunch',
			'school' => 'School',
			'teacher' => 'Teacher',
			'grade' => 'Grade',
			'parent' => 'Parent',
			'active' => 'Active'
		);
	return $cols;	
}
add_filter('manage_edit-slp_report_columns', 'slp_report_columns');

// adding to sorting
function slp_report_register_sortable($cols){
	$cols['order'] = 'order';
	$cols['lunchDate'] = 'lunchDate';
	$cols['lunch'] = 'lunch';
	$cols['school'] = 'school';
	$cols['teacher'] = 'teacher';
	$cols['grade'] = 'grade';
	$cols['parent'] = 'parent';
	$cols['date'] = 'date';
	return $cols;
}
*/

//add_filter('manage_edit-slp_report_sortable_columns', 'slp_report_register_sortable');

function custom_menu_page(){
   echo "Admin Page Test";	
}

add_action('admin_menu', 'register_custom_menu_page');

function register_custom_menu_page() {
   add_menu_page('custom menu title', 'custom menu', 'add_users', 'custompage', 'custom_menu_page', plugins_url('myplugin/images/icon.png'), 6);
}
//




?>