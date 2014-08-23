<?php 

// admin interface

function slp_report_runner_test(){

	wp_enqueue_script("jquery"); 

	
		
?>
	<h2>School Lunch Program Report Runner</h2>
	<p>Welcome to the Foodtown SLP Report Runner.</p>   
	<p> Please choose report parameters:</p> 
	
	
	<div class="wrap">
	
		<div class="alignleft" style="padding-right:15px;">      
			<span>
				<label class="howto" for="report">
					<span>Report Type:</span><br />
					<select style="z-index:+1;" name="report" id="report">
						<option value></option> 
						<option value="kitchen" >Kitchen Report - All Grades</option> 
						<option value="server" >Server Report - By Grade</option> 
						<option value="student" >Student Report - By Grade</option>
					</select>
				</label>
			</span>
		</div>
		
		<div class="alignleft" style="padding-right:15px;"> 
			<span>
			<?php echo do_shortcode('[slp_schools_select all="true"  ]'); ?>
			</span>
		</div>
		
		<div class="alignleft" style="padding-right:15px;"> 
			<span>
			<?php echo do_shortcode('[slp_grades all="true" staff="true"]'); ?>
			</span>
		</div>
		
		<div class="alignleft" style="padding-right:15px; ">
			<span>
				<label class="howto" for="datepick">
					<span>Date of Report:</span><br />
					<input type="text" name="datepick" id="datepick" style="width: 100px;">
				</label>
			</span>
		</div>
		<div style="padding-top:15px;">
			<button type="submit" id="submit" name="submit" class="button-secondary">Run Report</button>
		</div>
	
	</div>
	<div class="clear" /></div>
		<div id="div_results" >
			<div id="result_school"></div>
			<div id="result_date"></div>
		
		 	<table class="widefat" cellspacing="0" id="table_kitchen" style="width: 100%; display: none;" >
				<thead> 
					<tr>
					
					<th scope='col' style=''>Lunch</th>
					<th scope='col' style=''>Quantity</th>  
				   </tr>
				</thead>
				<tbody id='kitchen_reports'>
				</tbody>
			</table>               
			
			  <table class="widefat" cellspacing="0" id="table_server"  style="width: 100%; display: none;">
					<thead> 
						<tr>
	                    <th scope='col' style=''>Teacher</th> 
						<th scope='col' style=''>Lunch</th>
						<th scope='col' style=''>Quantity</th>  
					   </tr>
					</thead>
					<tbody id='server_reports'>
					</tbody>
				</table>
		    
		
			<table class="widefat" cellspacing="0" id="table_student"  style="width: 100%; display: none;">
				<thead> 
					<tr>
					<th scope='col' style=''>Grade /Teacher</th>  
					<th scope='col' style=''>Student</th>
					<th scope='col' style=''>Lunch</th>
					<th scope='col' style=''>Quantity</th>  
				   </tr>
				</thead>
				<tbody id='student_reports'>
				</tbody>
			</table>
		
		</div>
			<div id="debug"></div>
	<div class="clear" /></div>
	
<?php 
	wp_enqueue_script("jquery"); 
	define('cp_lang', 'en-GB');
	
	$cp_months = array(
 	'en-GB' => array(0,'Jan','Feb','Mar','Apr','May','June','July','Aug','Sept','Oct','Nov','Dec'),
 	'sv' => array(0,'jan','feb','mars','apr','maj','juni','juli','aug','sept','okt','nov','dec'),
 	// make your custom one here and set cp_lang to it.
 	'custom' => array(0,'jan','feb','mars','apr','maj','juni','juli','aug','sept','okt','nov','dec')
 	);
	
 //	define('datepick_package', get_stylesheet_directory_uri() . "/includes/admin/jquery.datepick.package");
?>
	<link type="text/css" href="<?php echo datepick_package; ?>/redmond.datepick.css" rel="stylesheet" />
 	<script type="text/javascript" src="<?php echo datepick_package; ?>/jquery-1.4.2.min.js"></script>
 	<script type="text/javascript" src="<?php echo datepick_package; ?>/jquery.datepick.js"></script>
 	<script type="text/javascript" src="<?php echo datepick_package; ?>/jquery.datepick.ext.js"></script>
 	<?php // remove localization when releasing plugin ?>
 	<script type="text/javascript" src="<?php echo datepick_package; ?>/jquery.datepick.lang.min.js"></script> 

	
	<script type="text/javascript">
		//<![CDATA[   
		var $j=jQuery.noConflict();  
		$j.datepick.setDefaults($j.datepick.regional['<?php echo cp_lang; ?>']);  
		
		 var ajax_admin_url = '<?php echo admin_url('admin-ajax.php'); ?>';     
		
		function spewWindow(){             
			var orig_div_cont=document.getElementById("div_results").innerHTML;
			var myWin=window.open("","myWin","menubar,scrollbars,left=30px,top=40px,height=400px,width=600px"); 
			myWin.document.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><title>My Window</title></head><body><link rel="stylesheet" href="/wp-admin/css/colors-fresh.css" type="text/css" media="all" /><div id="dest_div" style="width: 100%;">'+ 
			'</div></body></html>'); 
		   myWin.document.close(); 
		  myWin.document.getElementById("dest_div").innerHTML+=orig_div_cont;
		}
				
		 $j(function(){
			$j('#datepick').datepick({dateFormat: 'yyyy-m-d'});
		 });

 		$j(document).ready(function(){     
			//$j("[id*='table_']").hide();
	
			
			$j("#submit").click(function(){
			    var reportVal = $j("#report").val();  
				var schoolVal = $j("#schoolID").val();   
				var gradeVal = $j("#grade").val(); 
			    var dateVal = $j("#datepick").val();     
			
				if(!reportVal || !schoolVal || !gradeVal || !dateVal){
					alert("Please make sure all report parameters are selected.");
					return;
				}
				
				$j("#result_school").html('<h2>'+ dateVal + ' :  '+ $j("#school :selected").text()  +'</h2>'); 
				
				$j("[id*='table_']").hide(); 
				var tmpId = 'table_'+ reportVal;      
			  
				$j("#"+tmpId).show(200);  
				$j("#"+reportVal+"_reports").html("");
				
				
				var data = {action: "runSLPReport", report: reportVal, school: schoolVal, grade: gradeVal, date: dateVal}; 
				
				
				   
				$j.post(ajax_admin_url, data, function(response){       
				    //$j("#debug").html(response);
					  
					$j('table').css('width', '98%');
					$j("[id*='table']").css('width', '98%') ;
					
					
					
					if(reportVal=='kitchen'){ 
						$j('#kitchen_reports').html(response); 
						$j('#kitchen_reports tr:odd').addClass('alternate');      
						spewWindow();                                       
						return;
					}        
					
					
					if(reportVal=='server'){      
						 $j('#server_reports').html(response);						
						 $j('#server_reports tr:odd').addClass('alternate');        
						 spewWindow();                                     
						return;
					}
					 
					if(reportVal=='student'){
						$j('#student_reports').html(response);
					    $j('#student_reports tr:odd').addClass('alternate');  
					    spewWindow();
					    return;
					}                 
				   
				})
			
			})
		})
		
		 
		//]]>
	</script>

	
<?php 
}





function slp_report_runner_page(){
	add_submenu_page('school-lunch-program', 'School Lunch Report Runner', 'SLP Report Runner', 'add_users', 'slp-reporter', 'slp_report_runner_test');
		//add_dashboard_page('SLP Report Runner', 'SLP Reports', 'manage_options', 'slp-reporter', 'slp_report_runner_test');
}

add_action("admin_menu", "slp_report_runner_page");



/**

		RUNNING STUDENT ORDER
		
**/
function runSLPReport(){
	$data = $_POST;        
	global $wpdb;  
	$report = $data['report'];
	$school = $data['school'];
	$grade = $data['grade'];
	$date = $data['date'];
	     
	$tablename = $wpdb->prefix . "slp_reports";
 
	if( $report == "kitchen" ){ kitchenReport($tablename, $report, $school, $grade, $date); }     
	
	if( $report == "server" ){ serverReport($tablename, $report, $school, $grade, $date);}
		
	if($report=="student"){studentReport($tablename, $report, $school, $grade, $date);}	
}







/***************************** KITCHEN REPORT ********************************
 *******************************************************************************
 * Need to get school, teacher, lunch and quantity and deal with possible all
 * schools
 */
function kitchenReport($table, $report, $school, $grade, $date){
	global $wpdb;
	$schools = explode(",", $school); 
	$sIDs = array();
	foreach($schools as &$value){
		$ssql = "(schoolID='". $value ."')";
		$sIDs[]=$ssql;
	}
	$schoolIDs = implode(' || ', $sIDs);
	
	$mysql = "select   lunchDate,  lunchID, sum(lunchQuantity) from $table  where lunchDate = '$date' and  ($schoolIDs)   and active=1 group by   lunchID, lunchDate order by  sum(lunchQuantity) desc"; 
	$results = $wpdb->get_results($mysql, ARRAY_A);
	if(count($results)==0){
		echo '<center><div style="padding-top:40px;padding-bottom:50px;"><h2>There are no returns for the current request.</h2></div></center>';
	}else{ 
		$ret ='';
		if(count($schools)>1){
			$ret.='<tr><td colspan="2"><h3>All Schools</h3></td></tr>';
		}else{
			$ret.='<tr><td colspan="2"><h3>'. get_the_title($school) .'</h3></td></tr>';
		}
		
		
		$schoolCurrent = '';
		foreach($results as $item){
			$schoolID = $item['schoolID'];
			$lunchID = $item['lunchID'];
			$sum = $item['sum(lunchQuantity)'];
			/*
if($schoolCurrent != $schoolID){
				$schoolCurrent = $schoolID;
				$ret.='<tr><td colspan="2"><h3>School: ' . get_the_title($schoolID) . '</h3></td></tr>';
			}
*/
			$ret.='<tr class="charged">';
			$ret.='<td class="column-lunch" style="color: #000000;">'. get_the_title($lunchID) .'</td>'; 
			$ret.='<td class="column-sum" style="color: #000000;"><strong>'. $sum .'</strong></td>';
			$ret.='</tr>';
		}
		echo $ret;
	}
	
	//echo json_encode($results);
	die();
}








/***************************** SERVER REPORT ***********************************
 *******************************************************************************
 *
 *  If incoming grade = 'all' then need to loop through and create a query for all grades
 *	if incoming grade = "staff" need to construct different query
 *
 */
function serverReport($table, $report, $school, $grade, $date){
	global $wpdb;
	$schools = explode(",", $school); 
	$sIDs = array();
	foreach($schools as &$value){
		$ssql = "(schoolID='". $value ."')";
		$sIDs[]=$ssql;
	}
	$schoolIDs = implode(' || ', $sIDs);
	
	
	
	if($grade=="staff"){
		$mysql = "select schoolID,  lunchDate, teacherID, lunchID, sum(lunchQuantity) from $table  where lunchDate = '$date' and  ($schoolIDs) and teacherID='0'  and active=1 group by  schoolID, lunchDate, teacherID, lunchID order by schoolID, lunchDate, teacherID asc"; 
		
	} elseif ($grade=="%"){
	
		$mysql = "select schoolID,  lunchDate, teacherID, lunchID, sum(lunchQuantity) from $table  where lunchDate = '$date' and  ($schoolIDs)   and active=1 group by  schoolID, lunchDate, teacherID, lunchID order by schoolID, lunchDate, teacherID asc"; 
		
	}else{
		$staff = new WP_Query(array("showposts"=>-1, "post_type"=>"slp_teacher", "meta_query"=> array(array('key'=>'schoolID', 'value'=>$schools, 'compare'=>'IN'),array('key'=>'grade', 'value'=>$grade, 'compare'=>'='))));
		
		if($staff->post_count < 1){
			echo '<center><div style="padding-top:40px;padding-bottom:50px;"><h2>There are no active teachers or grades for the current request.</h2></div></center>'; die();
		}
		
		$IDs = array();
		foreach($staff->posts as $teacher){
			$tsql = "(teacherID = '".$teacher->ID ."')";
			$IDs[] = $tsql;
		}
		$teacherIDs = implode(' || ', $IDs);
		$mysql = "select schoolID,  lunchDate, teacherID, lunchID, sum(lunchQuantity) from $table  where lunchDate = '$date' and  ($schoolIDs) and ($teacherIDs)  and active=1 group by  schoolID, lunchDate, teacherID, lunchID order by schoolID, lunchDate, teacherID asc"; 

	}
		
		// returning html
		$results = $wpdb->get_results($mysql, ARRAY_A);
		
		// no results
		if(count($results)==0){
			echo '<center><div style="padding-top:40px;padding-bottom:50px;"><h2>There were no results for the current request.</h2></div></center>'; die();
		}
		
		$ret='';
		$schoolCurrent = ''; $teacherCurrent='';
		foreach($results as $item){
			$schoolID = $item['schoolID'];
			$lunchID = $item['lunchID'];
			$teacherID = $item['teacherID'];
			$sum = $item['sum(lunchQuantity)'];
			if($schoolCurrent != $schoolID){
				$schoolCurrent = $schoolID;
				$ret.='<tr><td colspan="2"><h3>School:  ' . get_the_title($schoolID) . '</h3></td><td colspan="2"></td></tr>';
			}
			if($teacherCurrent!=$teacherID){
				$teacherCurrent = $teacherID;
				if($teacherID=="0"){
					$ret.='<tr><td><h4>    Staff</h4></td><td colspan="2"></td></tr>';
				}else{
					$ret.='<tr><td><h4>   Grade '.get_post_meta($teacherID, 'grade', true). ' : ' . get_the_title($teacherID) . '</h4></td><td colspan="2"></td></tr>';
				}
			}
			$ret.='<tr class="charged"><td></td>';
			$ret.='<td class="column-lunch" style="color: #000000;">'. get_the_title($lunchID) .'</td>'; 
			$ret.='<td class="column-sum" style="color: #000000;"><strong>'. $sum .'</strong></td>';
			$ret.='</tr>';
		}
		echo $ret;
	//  krumo($schools, $staff, $report, $school, $grade, $mysql, $results);
	 	die();
}








/***************************** STUDENT REPORT ********************************
 *******************************************************************************
 *  If incoming grade = 'all' then need to loop through and create a query for all grades
 *	if incoming grade = "staff" need to construct different query
 *
 */
function studentReport($table, $report, $school, $grade, $date){
	global $wpdb;
	$schools = explode(",", $school); 
	$sIDs = array();
	foreach($schools as &$value){
		$ssql = "(schoolID='". $value ."')";
		$sIDs[]=$ssql;
	}
	$schoolIDs = implode(' || ', $sIDs);
	echo 'here we are';
	if($grade=="staff"){
		$mysql = "select id, schoolID, lunchRecipientID, lunchDate, teacherID, lunchID, sum(lunchQuantity) as lunchQuantity from $table  where lunchDate = '$date' and  ($schoolIDs) and teacherID='0'  and active=1 group by  schoolID, lunchDate, teacherID, lunchID order by lunchDate, schoolID,  teacherID asc";
		
	} elseif ($grade=="%"){
	
		$mysql = "select id, schoolID, lunchRecipientID, lunchDate, teacherID, lunchID, sum(lunchQuantity) as lunchQuantity from $table  where lunchDate = '$date' and  ($schoolIDs)  and active=1 group by  schoolID, lunchDate, teacherID, lunchRecipientID,  lunchID order by lunchDate, schoolID,  teacherID asc";
		
	}else{
	
		$staff = new WP_Query(array("showposts"=>-1, "post_type"=>"slp_teacher", "meta_query"=> array(array('key'=>'schoolID', 'value'=>$schools, 'compare'=>'IN'),array('key'=>'grade', 'value'=>$grade, 'compare'=>'='))));
		
		if($staff->post_count < 1){
			echo '<center><div style="padding-top:40px;padding-bottom:50px;"><h2>There are no active teachers or grades for the current request.</h2></div></center>'; die();
		}

		$IDs = array();
		foreach($staff->posts as $teacher){
			$tsql = "(teacherID = '".$teacher->ID ."')";
			$IDs[] = $tsql;
		}
		$teacherIDs = implode(' || ', $IDs);

		$mysql = "select id, schoolID, lunchRecipientID, lunchDate, teacherID, lunchID, lunchQuantity from $table  where lunchDate = '$date' and  ($schoolIDs) and ($teacherIDs)  and active=1 group by  schoolID, lunchDate, teacherID, lunchRecipientID, lunchID order by lunchDate, schoolID,  teacherID asc"; 
		
	}
	echo $mysql;
	$results = $wpdb->get_results($mysql, ARRAY_A);
	
	// no results
	if(count($results)==0){
		echo '<center><div style="padding-top:40px;padding-bottom:50px;"><h2>There were no results for the current request.</h2></div></center>'; die();
	}
	
	$ret='';
	$schoolCurrent = ''; $teacherCurrent='';
	foreach($results as $item){
		$id = $item['id'];
		$schoolID = $item['schoolID'];
		$lunchID = $item['lunchID'];
		$teacherID = $item['teacherID'];
		$sum = $item['lunchQuantity'];
		$lunchRecipientID = $item['lunchRecipientID'];
		if($schoolCurrent != $schoolID){
			$schoolCurrent = $schoolID;
			$ret.='<tr><td colspan="2"><h3>School:  ' . get_the_title($schoolID) . '</h3></td><td colspan="3"></td></tr>';
		}
		if($teacherCurrent!=$teacherID){
			$teacherCurrent = $teacherID;
			if($teacherID=="0"){
				$ret.='<tr style="background: #d2d2d2;"><td><strong>    Staff</strong></td><td colspan="3"></td></tr>';
				
			}else{
				$ret.='<tr style="background: #d2d2d2;"><td><strong>   Grade '.get_post_meta($teacherID, 'grade', true). ' : ' . get_the_title($teacherID) . '</strong></td><td colspan="3"></td></tr>';
			}
		}
		if($teacherID=="0"){
			global $Shopp;
			$customer = shopp_customer($item['lunchRecipientID'], 'id');
			$recipient = '<span style="color:red;"><b>'.$customer->firstname .' '.$customer->lastname .'</b></span>';
			
		}else{
			$recipient = get_the_title($lunchRecipientID);
		}
		$ret.='<tr class="charged"><td></td>';
		$ret.='<td class="column-recipient" style="color:#000000;">'. $recipient .'</td>';
		$ret.='<td class="column-lunch" style="color: #000000;">'. get_the_title($lunchID) .'</td>'; 
		$ret.='<td class="column-sum" style="color: #000000;"><strong>'. $sum .'</strong></td>';
		$ret.='</tr>';
	}
	echo $ret;
	
	die();
}




//add_action('init', 'register_acf_fields');

//register_activation_hook(__FILE__, 'activate_shop_foodtown_admin');  

//add_action('wp_ajax_studentlookup', 'studentlookup');
//add_action('wp_ajax_nopriv_studentlookup', 'studentlookup');    

add_action('wp_ajax_runSLPReport', 'runSLPReport');
add_action('wp_ajax_nopriv_runSLPReport', 'runSLPReport');

//add_action('admin_menu', 'shopp_foodtown_admin_menu');    
?>