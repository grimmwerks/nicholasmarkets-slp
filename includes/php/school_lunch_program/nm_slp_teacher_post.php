<?php

/**  
 *
 *      THIS IS THE CUSTOM POST TYPE FOR SLP TEACHER
 * 
 */

function slp_create_teacher_post_type(){
	register_post_type('slp_teacher',
	array(
		'labels' => array('name'=> __("SLP Teachers"),
							'singular_name'=>__('Teacher'),
							'add_new'=>__('Add Teacher'),
							'add_new_item'=>__('Add New Teacher'),
							'edit'=>__('Edit'),
							'edit_item'=>__('Edit Teacher'),
							'new_item'=>__("New Teacher"),
							'view'=>__('View Teacher'),
							'view_item' => __('View Teacher'),
							'not_found' => __('No Teacher found.'),
							'not_found_in_trash'=>__('No Teacher found in Trash')
							),
		'public'=>true,
		'can_export'=>true,
		'publicly_querable'=>true,
		'exclude_from_search' => true,
		'hierarchical' => false,
		'show_in_menu'=> 'school-lunch-program',
		'supports'=> array('title', 'custom-fields')
		)
	);
	
}

add_action('init', 'slp_create_teacher_post_type');
	
	
	
/**
	META BOX FOR TEACHER
**/
function add_slp_teacher_metabox(){
	add_meta_box('teacher-info', __('Teacher Info'), 'show_slp_teacher_info', 'slp_teacher', 'normal', 'core');
	
	
	/** beginning of teacher metabox    **/
	function show_slp_teacher_info($post){
		global $post;
		$schoolID = get_post_meta($post->ID, 'schoolID', TRUE);
		$grade = get_post_meta($post->ID, 'grade', TRUE);
		echo '<input type="hidden" name="teacher-info-nonce" id="teacher-info-nonce" value="'. wp_create_nonce( 'slp_teacher'.$post->ID ). '" />';
		echo '<span id="spangrade" style="float:left;padding-right:15px; width:30%;">' . do_shortcode('[slp_grades selected="'.$grade.'"]') . '</span>';
		echo '<span id="spanschool" style="float:left;padding-right:15px; width:30%;">' . do_shortcode('[slp_schools_select selected="'.$schoolID.'"]') . '</span>';
		echo '<br class="clear" />';
	}
	/**   end of teacher metabox      **/
	
	
	
	
	/**   start of saving teacher information    **/
	function save_teacher_information($post_id){
		if(!wp_verify_nonce($_POST['teacher-info-nonce'], 'slp_teacher'.$post_id)){
			return $post_id;
		}
		// verify if this is an auto save routine. If it is our form has not been submitted, so we dont want to do anything
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
			return $post_id;
			
		// Check permissions
		if ( !current_user_can( 'edit_post', $post_id ) )
			return $post_id;
			
		$post = get_post($post_id);
		
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
		
	}
	/**   end of saving teacher information   **/
	
	
	
	add_action('save_post', 'save_teacher_information');
}


if(is_admin()){
	add_action('admin_menu', 'add_slp_teacher_metabox');
}



/**
	COLUMNS AND SORTING
**/
// adding new columns to view
function slp_teacher_columns($cols){var_dump($cols);
	$cols = array(
			'cb' => '<input type="checkbox" />',
			'title' => 'Title',
			'school' => 'School',
			'grade' => 'Grade',
			'students' => 'Students',
			'date' => 'Date'
		);
	return $cols;	
}
add_filter('manage_edit-slp_teacher_columns', 'slp_teacher_columns');

// adding to sorting
function slp_teacher_register_sortable($cols){
	$cols['title'] = 'title';
	$cols['grade'] = 'grade';
	$cols['school'] = 'school';
	$cols['date'] = 'date';
	return $cols;
}

add_filter('manage_edit-slp_teacher_sortable_columns', 'slp_teacher_register_sortable');


/**
		CUSTOM CONTENT FOR  TEACHER LIST
**/
function slp_teacher_column_data($col, $post_id){
	if(get_post_type($post_id) == "slp_teacher"){
		// query for slp_student where meta_key schoolID = post_id
		
		switch($col){
			case 'grade':
				$grade = get_post_meta($post_id, 'grade', true);
				echo $grade;
				break;
			case 'school':
				$schoolID = get_post_meta($post_id, "schoolID", true);
				echo '<a href="/wp-admin/post.php?action=edit&post='.$schoolID.'" title="Edit School">'. get_the_title($schoolID).'</a>';
				break;
			case 'students':
				$students  = new WP_Query(array('showposts'=>-1, 'post_type'=>'slp_student', 'meta_query'=> array( array('key'=>'teacherID', 'value'=>$post_id, 'compare'=>'='))));
				if($students->post_count){
					foreach($students->posts as $student){
						echo '<a href="/wp-admin/post.php?action=edit&post='. $student->ID .'">' . $student->post_title . '</a><br /> ';
					}
				}
				break;
		}
	}
}

add_filter("manage_posts_custom_column", "slp_teacher_column_data", 10, 2);


?>