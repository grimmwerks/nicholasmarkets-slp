<?php
/*************************DASHBOARD WIDGET FOR TOTALS **************************
 *******************************************************************************
 *		function for looking up previous purchases and returning to calendar
 */
function student_lunch_lookup(){
	global $wpdb;
	$data = $_POST;
//	echo	print_r($data); die();
	$studentId = $data['studentId'];
	$month = $data['month'];
	$year = $data['year'];
	$tablename = $wpdb->prefix . "slp_reports";

	  // might want to change these to just lunchID and lookup in catalog. Also fix slp_
	$nsql = "SELECT lunchID, lunchType, lunchDate FROM $tablename WHERE lunchRecipientID = '$studentId' AND month(lunchDate)='$month' AND YEAR(lunchDate)='$year' and active=1";

	$lunches = $wpdb->get_results($nsql);
 	foreach($lunches as $lunch){
 		$newDate = date("Y-n-j", strtotime($lunch->lunchDate));
 		$lunch->lunchDate = $newDate;
 		$lunch->lunchName = get_the_title($lunch->lunchID);
 	}
    echo json_encode($lunches);      
	die();
}   





add_action('wp_ajax_student_lunch_lookup', 'student_lunch_lookup');
add_action('wp_ajax_nopriv_student_lunch_lookup', 'student_lunch_lookup');







/************************* widget communication  ******************************
 *******************************************************************************
 *		widget round trip to mysql lookups
 */
 function slp_widget_commission_lookup(){
 	global $wpdb;
	 $month = $_POST[month];
	 $month = str_pad($_POST[month], 2, "0", STR_PAD_LEFT);
	 $year = $_POST[year];
	 $targetDate = '%'.$year.'-'.$month.'-%';
	 
	 $allTotals =$wpdb->get_results("select sum(lunchPrice) as lunchPrice, count(DISTINCT orderID) as count from ". $wpdb->prefix."slp_reports where schoolID!=0 and active=1 and lunchDate LIKE '$targetDate'", ARRAY_A);
	 
	 $allLunchTotals =$wpdb->get_results("select sum(lunchPrice) as lunchPrice, count(lunchType) as count, sum(lunchQuantity) as lunchQuantity from ".$wpdb->prefix."slp_reports where lunchType='Meal' and lunchDate LIKE '$targetDate' and active=1",ARRAY_A);
	 
	 $allSchoolTotals =  $wpdb->get_results("select schoolID, sum(lunchPrice), count(DISTINCT orderID) from ". $wpdb->prefix."slp_reports where schoolID!=0 and active=1 and lunchDate LIKE '$targetDate' group by schoolID order by schoolID",ARRAY_A);
	
	$ret='<div class="table" style="width:96%"><table><tbody><tr><th colspan="48"><h2>All Totals : '. $month .'/'.$year . '</h2></th></tr>';
	$ret.= '<tr><td colspan="1" class="label">Orders:</td><td class="amount">'.$allTotals[0]['count'] . '</td><td colspan="1" class="label">Total:</td><td class="amount">'.'$'.number_format((float)$allTotals[0]['lunchPrice'], 2, '.', '') . '</td></tr>';
	$ret.= '<tr><td colspan="1" class="label">Hot Lunches:</td><td class="amount">'.$allLunchTotals[0]['count'] . '</td><td colspan="1" class="label">Lunch Totals:</td><td class="amount">'.'$'.number_format((float) $allLunchTotals[0]['lunchPrice'], 2, '.', '') . '</td></tr></table>';
	
	
	
	foreach($allSchoolTotals as $schoolTotal){
		$schoolID = $schoolTotal['schoolID'];
		$orderCount = $schoolTotal['count(DISTINCT orderID)'];
		$totalSales = $schoolTotal['sum(lunchPrice)'];
		$hotLunchTotals = $wpdb->get_results("select sum(lunchPrice), count(lunchType), sum(lunchQuantity) from ".$wpdb->prefix."slp_reports where schoolID='$schoolID' and lunchType='Meal' and lunchDate LIKE '$targetDate' and active=1",ARRAY_A);
		$schoolName = get_the_title($schoolID);
		$hasCommission = get_post_meta($schoolID, 'hasCommission', TRUE);
		$commission = get_post_meta($schoolID, 'commission', TRUE);
		$lunchPrice = get_post_meta($schoolID, 'lunchPrice', TRUE);
		$hotLunchCount = $hotLunchTotals[0]['sum(lunchQuantity)'];
		$hotLunchSales = $hotLunchTotals[0]['sum(lunchPrice)'];
		
				
		$ret.= '<div class="table" style="width:96%"><table><tbody><tr><th colspan="7"><h2>'.$schoolName.' : '. $month .'/'.$year . '</h2></th></tr>';
		$ret.= '<tr><td colspan="2" class="label">Total Orders:</td><td class="amount">'.$orderCount . '</td><td colspan="2"></td><td class="label">Total Sales:</td><td class="amount">$'.number_format((float)$totalSales, 2, '.', '') . '</td></tr>';
		$ret.= '<tr><td><h4>Hot Lunches:</h4></td>';
		$ret.= '<td class="label">Each:</td><td class="amount">$'.number_format((float)$lunchPrice, 2, '.', '').'</td>';
		$ret.= '<td class="label">Sold:</td><td class="amount">'. $hotLunchCount. '</td>';
		$ret.= '<td class="label">Lunch Totals:</td><td class="amount">$'. number_format((float)$hotLunchSales, 2, '.', '') . '</td></tr>';
		$ret.= '<tr><td colspan="2" class="label">School Commission: </td><td class="amount">' . ($hasCommission=="true" ? '$'.number_format((float)$commission, 2, '.', '') : " ----").'</td><td></td>';
		$ret.= '<td colspan="2" class="label">Total Commissions:</td><td class="amount">' . ($hasCommission=="true" ? '$'.number_format((float)($hotLunchCount * $commission), 2, '.', '') : " -----") .'</td></tr>';
		$ret.= '</tbody></table></div>';
	}
	 
	 echo $ret;
	 die();
 }
 
add_action('wp_ajax_slp_widget_commission_lookup', 'slp_widget_commission_lookup');
add_action('wp_ajax_nopriv_slp_widget_commission_lookup', 'slp_widget_commission_lookup');












/************************* clearning out cart ******************************
 *******************************************************************************
 *		clearing out cart upon recipient chang
 */
 function student_lunch_clear_cart(){
	 shopp_empty_cart();
	 echo "success!";
	 die();
 }
 
add_action('wp_ajax_student_lunch_clear_cart', 'student_lunch_clear_cart');
add_action('wp_ajax_nopriv_student_lunch_clear_cart', 'student_lunch_clear_cart');




/************************* hot meal price return  ******************************
 *******************************************************************************
 *		clearing out cart upon recipient chang
 */
 function slp_school_data_by_recipient(){
	 $data = $_POST;
	 $recipientID = $data['recipientID'];
	 $customerID = $data['customerID'];
	 $month = $data['month'];
	 $year = $data['year'];
	 
	 $timestamp  =  mktime(0, 0, 0, $month, 1, $year);                     
	 $month_name = date("F", $timestamp);
	 
	 $str = 'cutoff_'. strtolower($month_name);	 
	if($recipientID == $customerID){ // staff
		$schoolID = shopp_meta($customerID, "customer", "schoolID");
		$msg = "STAFF ". $schoolID;
	}else{
		$schoolID = get_post_meta($recipientID, "schoolID", true);
		$msg = "STUDENT ". $schoolID;
	};	
	$lunchPrice = get_post_meta($schoolID, 'lunchPrice', TRUE);
	$weekdays = unserialize(get_post_meta($schoolID, 'weekdays', TRUE));
	$holidays = get_post_meta($schoolID,'holidays',true);
	$cutoffDay = get_post_meta($schoolID, $str, TRUE );
	// look up school price, holiday, weekday
	$ret = json_encode(array("lunchPrice" => $lunchPrice, "weekdays"=>$weekdays, "holidays"=>$holidays, 'monthName'=> $month_name, 'cutoffDay'=>$cutoffDay, 'string'=>$str, 'schoolID'=>$schoolID));
	echo $ret;
		 die();
	 
 }
 
add_action('wp_ajax_slp_school_data_by_recipient', 'slp_school_data_by_recipient');
add_action('wp_ajax_nopriv_slp_school_data_by_recipient', 'slp_school_data_by_recipient');

function slp_school_data_by_id(){
	$data = $_POST;
	$schoolID = $_POST['schoolID'];
	$lunchPrice = get_post_meta($schoolID, 'lunchPrice', TRUE);
	$weekdays = unserialize(get_post_meta($schoolID, 'weekdays', TRUE));
	$holidays = get_post_meta($schoolID,'holidays',true);
	// look up school price, holiday, weekday
	$ret = json_encode(array("lunchPrice" => $lunchPrice, "weekdays"=>$weekdays, "holidays"=>$holidays));
	echo $ret;
		 die();
}
add_action('wp_ajax_slp_school_data_by_id', 'slp_school_data_by_id');
add_action('wp_ajax_nopriv_slp_school_data_by_id', 'slp_school_data_by_id');




?>