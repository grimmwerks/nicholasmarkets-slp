<?php

/**
	GRADES SHORT CODE
**/

function slp_grades_shortcode($args) {
	$sel = $args[selected]; $disabled = $args[disabled];
    $ret = '<label class="howto" for="grade">';
	$ret.= '<span>Select Grade:</span><br />';
    $ret.='<select style="z-index:+1; width:100%;'. ($disabled=="true" ? 'opacity:0.5;' : "") .'" name="grade" id="grade" '. ($disabled=="true" ? 'disabled' : "") .'>';
	$ret.='<option value></option>';
	if($args[all]=="true"){$ret.='<option value="%"'.($sel != "%" ? '' : ' selected="selected"').'>All Grades</option>';}
	$ret.='<option value="Pre-K"'.($sel != "Pre-K" ? '' : ' selected="selected"').'>Pre-K</option>';
	$ret.='<option value="Kindergarten"'.($sel != "Kindergarten" ? '' : ' selected="selected"').'>Kindergarten</option>';
	for($gr=1; $gr<13; $gr++){
		$ret.='<option value="'.$gr.'" '.($sel != $gr ? '' : ' selected="selected"').'>'.$gr.'</option>';
	}         
	if($args[staff]=="true"){$ret.='<option value="staff"'.($sel != "staff" ? '' : ' selected="selected"').'>Staff</option>';}
	$ret.='</select>';
	$ret.='</label>';
	return $ret;
}
add_shortcode("slp_grades", "slp_grades_shortcode");

/**  end of grades shortcode   **/


/**
	silly shortcode for commission rate diff
**/
function slp_commission_select($args){
	$selected = $args[selected];  if(!$selected){$selected = "";}
	$ret='<span style="float:left;">';
	$ret.='<label class="howto" for="commission">';
	$ret.='	<span>Commission</span><br />';
	$ret.='<select style="z-index:+1;" id="commission" name="commission">';
	for ($i = 2.50; $i > 0; $i -= .05){
		$ii = number_format((float)$i, 2, '.', '');
		$target = "+ ". $ii;
		$ret.='<option value="'.$target.'"'.($selected != $target ? '' : ' selected="selected"').'>+ $'.$ii.'</option>';	
	}
	$ret.='<option value=""'.($selected != '' ? '' : ' selected="selected"').'>$ 0.00</option>';
	/*
for ($i = 0.5; $i < 1.55; $i += .05){
		$ii = number_format((float)$i, 2, '.', '');
		$target = "- ". $ii;
		$ret.='<option value="'.$target.'"'.($selected != $target ? '' : ' selected="selected"').'>- $'.$ii.'</option>';	
	}
*/
	$ret.='</select></label></span>';
	echo $ret;
}
add_shortcode("slp_commission", "slp_commission_select");





/**
	SCHOOLS PULLDOWN SHORT CODE
**/
function slp_schools_select_shortcode($args){
	$schools = get_posts(array('showposts'=>-1,'post_type'=>'slp_school', 'orderby'=>'post_title', 'order'=>'ASC'));
	$allIDs = array(); 
	foreach($schools as $school){
		$allIDs[] = $school->ID;
	}
	
	$sel = $args[selected]; $disabled = $args[disabled];
	$info = ($args[info]=="true");
	$all = ($args[all]=="true");
	$ret = '<label class="howto" for="schoolID">';
	$ret.= '<span>Select School:</span><br />';
	$ret .= '<select style="z-index:+1; width:100%;'. ($disabled=="true" ? 'opacity:0.5;' : "");
	if($info){
		$ret .= '" name="info[schoolID]" id="infoSchoolID" '. ($disabled=="true" ? 'disabled' : "") .'>';
	}else{
		$ret .= '" name="schoolID" id="schoolID" '. ($disabled=="true" ? 'disabled' : "") .'>';
	}
	
	$ret .= '<option value></option>';
	if($all){
		$imp = implode(',', $allIDs); 
		//krumo($imp, $sel);
		$ret.='<option value="'. $imp .'  '.($sel != $imp ? '' : ' selected="selected"').'">All Schools</option>';}
	foreach($schools as $school){
		$ret .= '<option value="'.$school->ID.'"'.($sel != $school->ID ? '' : ' selected="selected"').'>'.$school->post_title.'</option>';
	}
	$ret.='</select>';
	$ret.='</label>';
	return $ret;
}
add_shortcode('slp_schools_select', 'slp_schools_select_shortcode');

/** end of schools pulldown shortcode **/


/**
	getting all products for select
**/
function slp_reports_products_shortcode($args){
	// this could be cleaned up
	$sel = $args[selected];
	$type = $args[type];
	$datestr = $args[date];
	$date = DateTime::createFromFormat('Y-m-d', $datestr);
	$tagDate = $date->format('n-j-Y');
	$allLunches='';
	if($type=="Meal"){
		$catArray = array("Salads", "Soups", "Sandwiches");
		foreach($catArray as $catTitle){
			shopp('storefront', 'category', 'slug=slp-'.sanitize_title_with_dashes($catTitle).'&load=true');
			if(shopp('collection', 'hasproducts', 'load=prices')):
				while(shopp('category', 'products')):
					$tmpName = shopp('product', 'name', 'return=true');
					$tmpId = shopp('product', 'id', 'return=true');
					$allLunches.='<option  value="'.$tmpId.'" '.($sel != $tmpId ? '' : ' selected="selected"').'>'.$tmpName.'</option>';
				endwhile;
			endif;
			// tag lunches and date
			shopp('storefront','tag-products', 'tag=all&load=true');
			if(shopp('collection', 'hasproducts', 'load=prices')):
				while(shopp('category', 'products')):
					$tmpName = shopp('product', 'name', 'return=true');
					$tmpId = shopp('product', 'id', 'return=true');
					$allLunches.='<option  value="'.$tmpId.'" '.($sel != $tmpId ? '' : ' selected="selected"').'>'.$tmpName.'</option>';
				endwhile;
			endif;
			shopp('storefront','tag-products', 'tag='.$tagDate.'&load=true');
			if(shopp('collection', 'hasproducts', 'load=prices')):
				while(shopp('category', 'products')):
					$tmpName = shopp('product', 'name', 'return=true');
					$tmpId = shopp('product', 'id', 'return=true');
					$allLunches.='<option  value="'.$tmpId.'" '.($sel != $tmpId ? '' : ' selected="selected"').'>'.$tmpName.'</option>';
				endwhile;
			endif;
				
		}		
	}
	if($type=="AddOn"){
		shopp('catalog', 'category', 'slug=slp-add-ons&load=true');
		if(shopp('category', 'hasproducts', 'load=prices')):
			while(shopp('category', 'products')):
			$tmpName = shopp('product', 'name', 'return=true');
			$tmpId = shopp('product', 'id', 'return=true');
			$allLunches.='<option  value="'.$tmpId.'" '.($sel != $tmpId ? '' : ' selected="selected"').'>'.$tmpName.'</option>';
			endwhile;
		endif;
	}

	$ret = '<label class="howto" for="shopp_lunchID">';
	$ret .= '<span>Select Lunch:</span><br />';
	$ret .= '<select style="z-index:+1; width:100%;" name="lunchID" id="lunchID" >';
	$ret .= $allLunches;
	$ret .='</select>';
	$ret.='</label>';
	return $ret;
}

add_shortcode('slp_reports_products', 'slp_reports_products_shortcode');





/**
	PARENT PULLDOWN SHORTCODE
**/
function slp_shopp_parents_select_shortcode($args){
	global $Shopp;
	$parents = shopp_customer_marketing_list(false);
	function byLastName($a, $b){
		//return $a['lastname'] - $b['lastname'];
		if($a->lastname < $b->lastname) return -1;
		if($a->lastname > $b->lastname) return 1;
		return 0;
	}
	usort($parents, 'byLastName');
	$sel = $args[selected]; $disabled = $args[disabled];
	$ret = '<label class="howto" for="parentID">';
	$ret.= '<span>Select Parent:</span><br />';
	$ret .= '<select style="z-index:+1; width:100%; '. ($disabled=="true" ? 'opacity:0.5;' : "") .'" name="parentID" id="parentID" '. ($disabled=="true" ? 'disabled' : "") .' >';
	$ret .= '<option value></option>';
	foreach($parents as $parent){
		$ret .= '<option value="'.$parent->id.'"'.($sel != $parent->id ? '' : ' selected="selected"').'>'.$parent->firstname.' '.$parent->lastname.'</option>';
	}
	$ret.='</select>';
	$ret.='</label>';
	return $ret;
}
add_shortcode('slp_parents_select', 'slp_shopp_parents_select_shortcode');

/** parent pulldown **/







/**
	Teacher PULLDOWN SHORTCODE  'meta_key'=>'grade','orderby'=>'meta_value','order'=>'desc',
	make it function this way - if no id passed in $args, it will be all teachers
	if id=schoolID it will return all the teachers for that school
	selected of course does the proper selection
**/
function slp_teacher_select_shortcode($args){
	$sel=$args[selected]; $disabled = $args[disabled];
	$schoolID = $args[schoolid];
	$showOptions = ($schoolID || $sel);
	$disabled = !$showOptions OR $disabled;
	
	$teachers = get_posts(array('showposts'=>-1,'post_type'=>'slp_teacher', 'meta_key'=>'grade','orderby'=>'meta_value','order'=>'asc', 'meta_query'=> array( array('key'=>'schoolID', 'value'=>$schoolID, 'compare'=>'='))));
	$ret = '<label class="howto" for="teacherID">';
	$ret.= '<span>Select Teacher:</span><br />';
	$ret .= '<select style="z-index:+1; width:100%;'. ($disabled=="true" ? 'opacity:0.5;' : "") .'" name="teacherID" id="teacherID" '. ($disabled=="true" ? 'disabled' : "") .'>';
	$ret .= '<option value></option>';
	if($showOptions){
		foreach($teachers as $teacher){
			$grade = get_post_meta($teacher->ID, "grade", true);
			$ret .= '<option value="'.$teacher->ID.'"'.($sel != $teacher->ID ? '' : ' selected="selected"').'>Grade: '. $grade .'&nbsp;&nbsp;&nbsp;'.$teacher->post_title. '</option>';
		}
	}
	$ret.= '</select>';
	$ret.='</label>';
	return $ret;
}
add_shortcode('slp_teacher_select', 'slp_teacher_select_shortcode');

/**  end teacher pulldown shortcode **/







/**
	Students to parent shortcode
	For adding / deleting students to a parent listing
**/
function slp_customer_students_shortcode($args){
	$parentID = $args[parentid]; 
	$addButton=$args[addbutton];
	$postType = $args[posttype];
	echo do_shortcode('[slp_create_student_form noparent="true" posttype="'.$postType.'" addbutton="true" parentid="'.$parentID.'"]');
	echo '<p>&nbsp;</p>';
	echo do_shortcode('[slp_parent_children_list delete="true" parentid="'. $parentID. '"]');
}

add_shortcode('slp_customer_students', 'slp_customer_students_shortcode');










/**
	STUDENT CREATE FORM SHORTCODE
	this is being used in two place; COULD create it as a helper function but since an echo shortcode is the same as a helper function...
	Used for the main student post type for creating post types
	Used also on the parent facing page for creating/adding students but also have <form> elements wrapped around the do_shortcode
**/

function slp_create_student_form_shortcode($args){
	global $post;
	?>
	<script type="text/javascript">
		var $j = jQuery.noConflict();
		var ajax_admin_url = '<?php echo admin_url('admin-ajax.php'); ?>'; 
		
		$j(document).ready(function(){
			
			$j("#schoolID").change(function(){
				if($j(this).attr('value')){
					$j('#teacherID').attr('disabled', 'disabled');
					$j('#teacherID').attr('style', 'opacity:0.5;');
					
					var schoolID= $j(this).attr('value');
					var data = {
						action: "teacher_select",
						schoolID: schoolID
					}
					$j.post(
						ajax_admin_url,
						data,
						function(response){
							$j("#spanteacher").html(response);
						}
					); // end of post
				}
			}); //end of change
		}); // end of ready
	
	
	</script>	
	<?php		
			$postType = $args[posttype];
			$noparent = $args[noparent];  
			$linked = $args[linked];
			$addButton = $args[addbutton];
			// passed in from front-facing use; $parentID will come in as logged in shopp customer and use hidden form element to pass through.
			if($args[parentid]){$parentID=$args[parentid];}
			if($args[studentid]){$studentID=$args[studentid];}
			if($_GET['studentid']){$studentID = $_GET['studentid'];}
					//krumo($args);
			if($postType=="register" && !($_POST[customer]=="true")){
				/*
				echo 'line 240 <br />';
				krumo($_POST);
				*/
				$url = $_SERVER['REQUEST_URI'];
			
			
				$firstname = $_POST['firstname'];
				$lastname = $_POST['lastname'];
				$schoolID = $_POST['schoolID'];
				if($_POST['parentID']){$parentID = $_POST['parentID'];}
				$teacherID = $_POST['teacherID'];
			}else{
			// all is used to display student post, but not really used if from parent submitted:			
				$firstname = get_post_meta($post->ID, 'firstname', TRUE);
				$lastname = get_post_meta($post->ID, 'lastname', TRUE);
				$schoolID = get_post_meta($post->ID, 'schoolID', TRUE);
				$parentID = get_post_meta($post->ID, 'parentID', TRUE);
				$teacherID = get_post_meta($post->ID, 'teacherID', true);
			}
			
				//krumo($firstname, $lastname, $schoolID, $parentID, $teacherID);
			
			wp_nonce_field('slp_student-info-nonce', 'slp_student-info-nonce');
	?>
			<p>
				<span style="float:left;  width:47%; display: inline-block; padding-right:5%;">
					<label class="howto" for="slp_firstname">
						<span>First Name</span><br />
						<input type="text" name="slp_firstname" id="slp_firstname" value="<?php echo $firstname; ?>" style="width:100%;" />
					</label>
				</span>
				
				<span style="float:left;  width:47%; display: inline-block;">
					<label class="howto" for="slp_lastname">
						<span>Last Name</span><br />
						<input type="text" name="slp_lastname" id="slp_lastname" value="<?php echo $lastname; ?>" style="width:100%;" />
					</label>
				</span>
			</p>
			<p>&nbsp;</p>
	<?php	
			
			if($noparent){
			
				echo '<input type="hidden" name="parentID" id="parentID" value="'. $parentID .'" />';
			}else{
				echo '<span id="spanparent" style="float:left;padding-right:15px; width:30%;">' . do_shortcode('[slp_parents_select selected="'.$parentID.'"]');
				if($linked && $parentID){echo '<br /><a href="/wp-admin/admin.php?page=shopp-customers&id='.$parentID.'">Edit Parent</a>';}
				echo '</span>';
			}
			echo '<div>';
			echo '<span id="spanschool" style="float:left;padding-right:15px; width:30%;">' . do_shortcode('[slp_schools_select selected="'.$schoolID.'"]');
			if($linked && $schoolID){echo '<br /><a href=/wp-admin/post.php?action=edit&post='.$schoolID.'">Edit School</a>';}
			echo '</span>';
			echo '<span id="spanteacher" style="float:left; width:30%;">' . do_shortcode('[slp_teacher_select schoolid="'.$schoolID .'" selected="'.$teacherID.'"]');
			if($linked && $teacherID){echo '<br /><a href="/wp-admin/post.php?action=edit&post='.$teacherID.'">Edit Teacher</a>';}
			echo '</span>';
			if($addButton){echo '<span id="spanadd" style="float:left; padding:20px;"><input class="btn small " type="submit" name="addbutton" id="addbutton" value="Save Student"></span>';}
			echo '</div>';
			if($studentID){echo '<input type="hidden" name="studentID" value="'.$studentID.'" />';}
	?>
			<input type="hidden" name="post_title" id="post_title" value="<?php echo $slp_firstname . ' ' . $slp_lastname; ?>" />
			<br class="clear" />
			
	<?php		
	
}
add_shortcode('slp_create_student_form', 'slp_create_student_form_shortcode');
/**  end of helper shortcode for creating student form **/







/**	
		PARENT CHILDREN SHORTCODE
		HELPER / SUB shortcode for displaying all students to this parentID (shopp customer ID) 
		going to be used on front-facing registration and account page as well as on admin page
**/
function slp_parent_children_list_shortcode($args){
	$parentID = $args[parentid];
	$linked = $args[linked]; // if link to student post should be on
	$delete = $args[delete]; // if delete should be shown
	$edit = $args[edit]; // rather than delete, edit and send current info to post?
	
	
	$studentQuery  = new WP_Query(array('post_type'=>'slp_student', 'meta_query'=> array( array('key'=>'parentID', 'value'=>$parentID, 'compare'=>'='))));
	
	if($studentQuery->post_count){
	
		parse_str($_SERVER['QUERY_STRING'], $query_string);
				
		$out = '<form method="post" action="">';
		$out.= wp_nonce_field('slp_parent_student_delete', 'slp_parent_student_delete_submitted');
		$out.='<table id="students" class="widefat" cellspacing="0" style="width:100%;">';
		$out.='<thead><th scope="col" class="manage-column" >Student Name</th><th scope="col" class="manage-column" >School</th>';
		$out.='<th scope="col" class="manage-column" >Teacher</th><th scope="col" class="manage-column" >Grade</th>';
		if($edit){$out.='<th scope="col" id="cb" class="manage-column">Edit</th>';}
		if($delete){$out.='<th scope="col" id="cb" class="manage-column">Delete</th>';}
		$out.='</thead><tbody>';
		foreach($studentQuery->posts as $student){
			$query_string['studentid'] = $student->ID;
			if($_SERVER['REDIRECT_URL']){
				$pre = $_SERVER['REDIRECT_URL'];
			}else{
				$pre =$_SERVER['PHP_SELF'];
			}
			$editURL = $pre .'?'. http_build_query($query_string);
			
			
			
			$schoolID = get_post_meta($student->ID, "schoolID", true);
			$teacherID = get_post_meta($student->ID, "teacherID", true);
			
			//krumo($student, $schoolID, $teacherID);
			
			$out.=wp_nonce_field('slp_student_delete_'.$student->ID, 'slp_student_delete_id_'.$student->ID, false);
			$out.='<tr class="slp_student type-slp_student status-publish hentry alternate iedit author-self">';
			$out.='<td>';
			if($linked){$out.='<a href="/wp-admin/post.php?action=edit&post='.$student->ID.'" title="Edit student">';}
			$out.= $student->post_title;
			if($linked){$out.='</a>';}
			$out.='</td>';
			// school
			$out.='<td>';
			if($linked){$out.='<a href="/wp-admin/post.php?action=edit&post='.$schoolID.'" title="Edit School">';}
			$out.=get_the_title($schoolID);
			if($linked){$out.='</a>';}
			$out.='</td>'; // school
			// grade / teacher
			$out.='<td>';
			if($linked){$out.='<a href="/wp-admin/post.php?action=edit&post='.$teacherID.'" title="Edit Teacher">';}
			$out.=get_the_title($teacherID);
			if($linked){$out.='</a>';}
			$out.='</td>'; 
			// grade number
			$out.='<td>';
			$out.= get_post_meta($teacherID, 'grade', true);
			$out.='</td>';
			if($edit){$out.='<td><a style="color:red;" href="'.$editURL .'" >Edit</a></td>';}
			if($delete){$out.='<th scope="row" class="check-column"><input type="checkbox" name="slp_delete_id[]" value="'. $student->ID . '"/></th>';}
			
			$out.='</tr>';
		}
		$out.='</tbody></table>';
		if($delete){$out.='<input type="submit" name="slp_student_delete" value="Delete Selected Students">';}
		$out.='</form>';
		$out.='<br class="clear" />';
		return $out;
		
	}else{
		echo '<span style="text-align:center;">There are no students for this account.</span>';
	}
}
add_shortcode('slp_parent_children_list', 'slp_parent_children_list_shortcode');	




/** 
		using the code for registering students to a parent  as a short code
		only so that it can be echoed in two different places
**/

function slp_parent_child_registration_code_shortcode(){

	if($_GET['studentid'] && !(isset($_POST['slp_student-info-nonce']) && wp_verify_nonce($_POST['slp_student-info-nonce'], 'slp_student-info-nonce'))){
		// try tossing the post meta into $_POST?
		$studentPostID = $_GET['studentid'];
		/*

		echo 'before <br />';
		krumo($_POST);
		
*/
		$_POST['firstname'] = get_post_meta($studentPostID, 'firstname', TRUE);
		$_POST['lastname'] = get_post_meta($studentPostID, 'lastname', TRUE);
		$_POST['schoolID'] = get_post_meta($studentPostID, 'schoolID', TRUE);
		$_POST['parentID'] = get_post_meta($studentPostID, 'parentID', TRUE);
		$_POST['teacherID'] = get_post_meta($studentPostID, 'teacherID', true);
		/*

		echo 'after <br />';
		krumo($_POST);
		
*/
	}
		// adding a student to the current registered user
	if(isset($_POST['slp_student-info-nonce']) && wp_verify_nonce($_POST['slp_student-info-nonce'], 'slp_student-info-nonce')){
		unset($_GET['studentid']);
		
		//krumo($_POST, $_GET);
	
		$slp_student_firstname = trim($_POST['slp_firstname']);
		$slp_student_lastname = trim($_POST['slp_lastname']);
		
		$schoolID = $_POST['schoolID'];
		$teacherID = $_POST['teacherID'];
		$parentID = $_POST['parentID'];
	
		if($slp_student_firstname!='' && $slp_student_lastname!='' && $parentID!='' && $schoolID!='' && $teacherID!=''){
			$data = array(
			'post_title'=>$slp_student_firstname.' '.$slp_student_lastname,
			'post_status'=>'publish',
			'post_type'=>'slp_student'
			);
			
			
			if(isset($_POST['studentID'])){
				$studentPostID = $_POST['studentID'];
				$data['ID'] = $studentPostID;
				wp_update_post($data);
				
			}else{
				$studentPostID = wp_insert_post($data);	
			}
			
			update_post_meta($studentPostID, 'firstname', esc_attr($_POST['slp_firstname']));
			update_post_meta($studentPostID, 'lastname', esc_attr($_POST['slp_lastname']));
			update_post_meta($studentPostID, 'schoolID', esc_attr($_POST['schoolID']));
			update_post_meta($studentPostID, 'parentID', esc_attr($_POST['parentID']));
			$tID = update_post_meta($studentPostID, 'teacherID', esc_attr($_POST['teacherID']));
			
			unset($_POST);			
		}
		
	}
	// end adding a student to the current registered user


	// attemping deleting students
	if(isset($_POST['slp_parent_student_delete_submitted']) && wp_verify_nonce($_POST['slp_parent_student_delete_submitted'], 'slp_parent_student_delete')){
		if(isset($_POST['slp_delete_id'])){
			$studentsID = $_POST['slp_delete_id'];
			foreach($studentsID as $studentID){
				if(isset($_POST['slp_student_delete_id_'.$studentID]) && wp_verify_nonce($_POST['slp_student_delete_id_' . $studentID], 'slp_student_delete_' . $studentID)){
					wp_trash_post($studentID);
				}
			}
		unset($_POST);
		}
	}
	


}
add_shortcode('slp_parent_child_registration_code', 'slp_parent_child_registration_code_shortcode');	








/**
	ADDITIONAL NEED OF AJAX bridge to shortcode for jquery pulldowns
**/
add_action('wp_ajax_nopriv_teacher_select', 'ajax_teacher_select');
add_action('wp_ajax_teacher_select', 'ajax_teacher_select');

function ajax_teacher_select(){
	$selected = $_POST['selected'];
	$schoolID = $_POST['schoolID'];
	
	echo do_shortcode('[slp_teacher_select schoolID="'.$schoolID.'" selected="'.$teacherID.'"]');
	die();
}






?>