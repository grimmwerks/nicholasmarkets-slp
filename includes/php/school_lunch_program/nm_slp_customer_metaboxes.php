<?php


/**
	adding metabox to shopp customers / parents
**/
function add_shopp_customer_slp_student_metabox(){
	add_meta_box('slp-children', __('Children / Students'), 'show_children_metabox', 'shopp_page_shopp-customers', 'normal', 'low');
	
	function show_children_metabox($Customer){
		echo do_shortcode('[slp_parent_children_list linked="true" parentid="'. $Customer->id. '"]');
	}
}

function add_shopp_customer_slp_staff_metabox(){
	add_meta_box('slp-staff', __('School Staff'), 'show_staff_metabox', 'shopp_page_shopp-customers', 'side', 'default');
	
	function show_staff_metabox($Customer){
		if($Customer->type=="Staff"){
			wp_enqueue_script( 'jquery' );?>
			 
			<script type="text/javascript">
				var $j = jQuery.noConflict();
				var ajax_admin_url = '<?php echo admin_url('admin-ajax.php'); ?>'; 
				$j(document).ready(function(){
					var customerID = <?php echo $Customer->id; ?>
					
					$j("#schoolID").change(function(){
						if($j(this).attr('value')){
							var schoolID= $j(this).attr('value');
							$j("#customer-info-schoolid").val(schoolID);
							var data = {
								action: "staff_school_select",
								schoolID: schoolID,
								customerID: customerID
							}
							$j.post(
								ajax_admin_url,
								data,
								function(response){
									$j("#schoolmessage").html(response);
								}
							); // end of post
						} // end of value
					}); //end of change
				}); // end of ready
			
			
			</script>	
			<?php		
			
			$schoolID = shopp_meta($Customer->id, "customer", "schoolID");
			echo do_shortcode('[slp_schools_select selected="'.$schoolID.'"]');
			echo '<span id="schoolmessage"></span>';
		}else{
			echo '<center>This is for staff only.</center>';
		}			
		
	}
}




	
if(is_admin()){
	add_action('admin_menu', 'add_shopp_customer_slp_student_metabox');
	add_action('admin_menu', 'add_shopp_customer_slp_staff_metabox');
	add_action('wp_ajax_nopriv_staff_school_select', 'ajax_staff_school_select');
	add_action('wp_ajax_staff_school_select', 'ajax_staff_school_select');
}

// saving staff school select
function ajax_staff_school_select(){
	$schoolID = $_POST['schoolID'];
	$customerID = $_POST['customerID'];
	$title = get_the_title($schoolID);
	$ret = shopp_set_meta($customerID, "customer", "schoolID", $schoolID);
	if($ret){
		echo "<center>Staff saved to ".$title.".</center>";
	}else{
		echo '<center><span style="color:#FF0000;">There was a problem saving staff to school .</span></center>';
	}
	die();
}


?>