<?php

/**  
 *
 *      THIS IS THE CUSTOM POST TYPE FOR SLP STUDENT
 * 
 */
 
 
	function slp_create_student_post_type(){
		register_post_type('slp_student',
		array(
			'labels' => array('name'=> __("SLP Student"),
								'singular_name'=>__('Student'),
								'add_new'=>__('Add Student'),
								'add_new_item'=>__('Add New Student'),
								'edit'=>__('Edit'),
								'edit_item'=>__('Edit Student'),
								'new_item'=>__("New Student"),
								'view'=>__('View Student'),
								'view_item' => __('View Student'),
								'not_found' => __('No Student found.'),
								'not_found_in_trash'=>__('No Student found in Trash')
/*
								'rewrite' => array( 'slug' => $slug, 'with_front' => false ),
								'_edit_link' => 'admin.php?page=slp_student&id=%d'
*/
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
	
	add_action('init', 'slp_create_student_post_type');
	
	


	
/**
	meta box for student
**/
function add_slp_student_metabox(){
	add_meta_box('student-info', __('Student Info'), 'show_slp_student_info', 'slp_student', 'normal', 'core');
	
	
	function show_slp_student_info($post){ 
		echo do_shortcode('[slp_create_student_form linked="true"]');	 // calls helper function for creating student save form
	}
	
	
	add_action('save_post', 'save_student_information');
	
	function save_student_information($post_id){
		if(get_post_type($post_id) == "slp_student"){
			if(!wp_verify_nonce($_POST['slp_student-info-nonce'], 'slp_student-info-nonce')){
				return $post_id;
			}
			// verify if this is an auto save routine. If it is our form has not been submitted, so we dont want to do anything
			if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
				return $post_id;
				
			// Check permissions
			if ( !current_user_can( 'edit_post', $post_id ) )
				return $post_id;
								
			$post = get_post($post_id);
		
			if(isset($_POST['slp_firstname'])){
				update_post_meta($post_id, 'firstname', esc_attr($_POST['slp_firstname']));
			}else{
				delete_post_meta($post_id, 'firstname');
			}
			if(isset($_POST['slp_lastname'])){
				update_post_meta($post_id, 'lastname', esc_attr($_POST['slp_lastname']));
			}else{
				delete_post_meta($post_id, 'lastname');
			}
	
			if(isset($_POST['grade'])){
				update_post_meta($post_id, 'grade', esc_attr($_POST['grade']));
			}else{
				delete_post_meta($post_id, 'grade');
			}
			if(isset($_POST['schoolID'])){
				update_post_meta($post_id, 'schoolID', esc_attr($_POST['schoolID']));
			}else{
				delete_post_meta($post_id, 'schoolID');
			}
			if(isset($_POST['parentID'])){
				update_post_meta($post_id, 'parentID', esc_attr($_POST['parentID']));
			}else{
				delete_post_meta($post_id, 'parentID');
			}
			if(isset($_POST['teacherID'])){
				update_post_meta($post_id, 'teacherID', esc_attr($_POST['teacherID']));
			}else{
				delete_post_meta($post_id, 'teacherID');
			}
		}	
	}
}





	
if(is_admin()){
	add_action('admin_menu', 'add_slp_student_metabox');
}

/**
	helper filter for student metabox to create title from first and last name
**/

add_filter('title_save_pre', 'slp_student_save_title_pre');

function slp_student_save_title_pre($title){
	if($_POST['post_type'] == 'slp_student'){
		$studentTitle = $_POST['slp_firstname'] . ' ' . $_POST['slp_lastname'];
		$_POST['post_title'] = $studentTitle;
		$_POST['post_name'] = sanitize_title_with_dashes($studentTitle);
		return $studentTitle;
	}else{
		return $title;
	}
}
/**  end helper filter for first / last name **/





/**
	COLUMNS AND SORTING
**/
// adding new columns to view
function slp_student_columns($cols){
	$cols = array(
			'cb' => '<input type="checkbox" />',
			'title' => 'Title',
			'school' => 'School',
			'teacher' => 'Teacher',
			'parent' => 'Parent',
			'date' => 'Date'
		);
	return $cols;	
}
add_filter('manage_edit-slp_student_columns', 'slp_student_columns');

// adding to sorting
function slp_student_register_sortable($cols){
	$cols['title'] = 'title';
	$cols['teacher'] = 'teacher';
	$cols['school'] = 'school';
	$cols['parent'] = 'parent';
	$cols['date'] = 'date';
	return $cols;
}

add_filter('manage_edit-slp_student_sortable_columns', 'slp_teacher_register_sortable');




/**
		CUSTOM CONTENT FOR  STUDENT LIST
**/
function slp_student_column_data($col, $post_id){
	if(get_post_type($post_id) == "slp_student"){
		// query for slp_student where meta_key schoolID = post_id
		$meta = get_post_meta($post_id);
		switch($col){
			case 'school':
				$schoolID = get_post_meta($post_id, "schoolID", true);
				echo '<a href="/wp-admin/post.php?action=edit&post='.$schoolID.'" title="Edit School">'. get_the_title($schoolID).'</a>';
				break;
			case 'teacher':
				$teacherID = get_post_meta($post_id, "teacherID", true);
				$grade = get_post_meta($teacherID, "grade", true);
				echo '<a href="/wp-admin/post.php?action=edit&post='.$teacherID.'" title="Edit Teacher">'. get_the_title($teacherID).' | Grade: '.$grade.'</a>';
				break;
			case 'parent':
				$parentID = get_post_meta($post_id, 'parentID', true);
				$parent = shopp_customer($parentID);
				echo '<a href="/wp-admin/admin.php?page=shopp-customers&id='.$parentID.'" >'. $parent->firstname . ' ' . $parent->lastname. '</a>';
				echo '<br /><span style="color:silver">ID: '.$parentID.'</span>';
				break;
		}
	}
}

add_filter("manage_posts_custom_column", "slp_student_column_data", 10, 2);




?>