<?php


/*************************DASHBOARD WIDGET FOR TOTALS **************************
 *******************************************************************************
 * Checking to see if slp_report database exists, and if not, creates it
 * This will get a list of schoolIDs, make a query for each - all totals, all lunches, and
 * if hasCommission, commision totals 
 
 */
function init_slp_commissions_widget(){
	global $wp_meta_boxes;
	wp_add_dashboard_widget('slp_commissions', 'School Lunch Totals and Commissions', 'show_slp_commissions');
}

function show_slp_commissions(){

?>
		<script type="text/javascript">
			var ajax_admin_url = '<?php echo admin_url('admin-ajax.php'); ?>';
		
			$j = jQuery.noConflict();
			$j(document).ready(function(){
				
				var data={action: 'slp_widget_commission_lookup', month: $j('#month').val(), year: $j('#year').val()};
				$j.post(ajax_admin_url, data, function(ret){
					displayResult(ret);
				})
			
				$j("select").change(function(){
					var data={action: 'slp_widget_commission_lookup', month: $j('#month').val(), year: $j('#year').val()};
					$j.post(ajax_admin_url, data, function(ret){
						displayResult(ret);
					})
				});
				
				function displayResult(ret){
					$j('#result').html(ret);
				}
				
			});
			
		</script>
	
<?php
	// get totals
	global $wpdb;
	//$allSQL = "select schoolID, sum"
	echo '<center><a href="/slp-admin" target="_blank">Click here to view Monthly Hot Lunches</a></view><br />';
	// controls
	$fmonth = date('m');
	$select_month_control = '<select class="month" name="fmonth" id="month">';
	 for($x = 1; $x <= 12; $x++) {
	 	$select_month_control.= '<option value="'.$x.'"'.($x != $fmonth ? '' : ' selected="selected"').'>'.date('F',mktime(0,0,0,$x,1,$year)).'</option>';
	 }
	 $select_month_control.= '</select>';
  /* select year control */
	 $year_range = 2; $year_start = date('Y'); 
	 $select_year_control = '<select class="year" name="fyear" id="year">';
	 for($x = ($year_start-floor($year_range/2)); $x <= ($year_start+floor($year_range/2)); $x++) {
	 	$select_year_control.= '<option  value="'.$x.'"'.($x != $year_start ? '' : ' selected="selected"').'>'.$x.'</option>';
	 }
	 $select_year_control.= '</select>';	
	 
	 $controls = '<div class="table" style="width:96%; padding:4px;">Select month or year to view commissions:<br />' . $select_month_control . $select_year_control. '</div>';
	 echo $controls;
	 
	 echo '<div id="result"></div>';
	

	/*
$allSchoolTotals =  $wpdb->get_results("select schoolID, sum(lunchPrice), count(DISTINCT orderID) from ". $wpdb->prefix."slp_reports where schoolID!=0 and active=1 group by schoolID order by schoolID",ARRAY_A);
	
	foreach($allSchoolTotals as $schoolTotal){
		$schoolID = $schoolTotal['schoolID'];
		$orderCount = $schoolTotal['count(DISTINCT orderID)'];
		$totalSales = $schoolTotal['sum(lunchPrice)'];
		$hotLunchTotals = $wpdb->get_results("select sum(lunchPrice), count(lunchType), sum(lunchQuantity) from ".$wpdb->prefix."slp_reports where schoolID='$schoolID' and lunchType='Meal' and active=1",ARRAY_A);
	//	krumo($schoolID, $schoolTotal, $hotLunchTotals);
		$schoolName = get_the_title($schoolID);
		$hasCommission = get_post_meta($schoolID, 'hasCommission', TRUE);
		$commission = get_post_meta($schoolID, 'commission', TRUE);
		$lunchPrice = get_post_meta($schoolID, 'lunchPrice', TRUE);
		$hotLunchCount = $hotLunchTotals[0]['sum(lunchQuantity)'];
		$hotLunchSales = $hotLunchTotals[0]['sum(lunchPrice)'];
		
				
		echo '<div class="table" style="width:96%"><table><tbody><tr><th colspan="7"><h2>'.$schoolName.'</h2></th></tr>';
		echo '<tr><td colspan="2" class="label">Total Orders:</td><td class="amount">'.$orderCount . '</td><td colspan="2"></td><td class="label">Total Sales:</td><td class="amount">$'.number_format((float)$totalSales, 2, '.', '') . '</td></tr>';
		echo '<tr><td><h4>Hot Lunches:</h4></td>';
		echo '<td class="label">Each:</td><td class="amount">$'.number_format((float)$lunchPrice, 2, '.', '').'</td>';
		echo '<td class="label">Sold:</td><td class="amount">'. $hotLunchCount. '</td>';
		echo '<td class="label">Lunch Totals:</td><td class="amount">$'. number_format((float)$hotLunchSales, 2, '.', '') . '</td></tr>';
		echo '<tr><td colspan="2" class="label">School Commission: </td><td class="amount">' . ($hasCommission=="true" ? '$'.number_format((float)$commission, 2, '.', '') : " ----").'</td><td></td>';
		echo '<td colspan="2" class="label">Total Commissions:</td><td class="amount">' . ($hasCommission=="true" ? '$'.number_format((float)($hotLunchCount * $commission), 2, '.', '') : " -----") .'</td></tr>';
		echo '</tbody></table></div>';
	}
*/
	

	//echo '<p>There are no schools or totals as yet.</p>';
}

add_action('wp_dashboard_setup', 'init_slp_commissions_widget');

function slp_commissions_css(){
	wp_enqueue_style('slp.commissions', get_stylesheet_directory_uri() . "/includes/styles/dashboard.css", array());
}
add_action('admin_print_styles-index.php', 'slp_commissions_css');

?>