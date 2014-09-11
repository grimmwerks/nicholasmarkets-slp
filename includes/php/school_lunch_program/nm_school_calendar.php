<?php

if(!(function_exists('draw_calendar'))){
	function draw_calendar($month,$year, $student_selected){           

			$select_count =0;
	
			//$customerId =  shopp('customer', 'id', 'return=true');
			$customer = shopp_customer();
			$customerId = $customer->id;
			$customerType = getCustomerType($customer);

			
	/**	
			MEALS  getting all hot meals and addons without the needed strings to cut down on repeating loops
	**/
			
			$allVirginMeals = '';
			$catArray = array("Salads", "Soups", "Sandwiches");
			foreach($catArray as $catTitle){
				shopp('storefront', 'category', 'slug=slp-'.sanitize_title_with_dashes($catTitle).'&load=true');
				if(shopp('collection', 'hasproducts', 'load=prices')):
					$allVirginMeals.='<optgroup  label="Select '.$catTitle.':">';
					while(shopp('category', 'products')):
						$tmpName = shopp('product', 'name', 'return=true');
						$tmpId = shopp('product', 'id', 'return=true');
						$tmpPrice = shopp('product', 'price', 'return=true');
						$allVirginMeals.='<option  value="~~|'.$tmpId.'|Meal">'.$tmpName.' - '.$tmpPrice.'</option>';
					endwhile;
					$allVirginMeals.='</optgroup>';
				endif;
			}
			
				
			$allVirginAddons = '';
			shopp('catalog', 'category', 'slug=slp-add-ons&load=true');
			if(shopp('category', 'hasproducts', 'load=prices')):
				while(shopp('category', 'products')):
				$tmpName = shopp('product', 'name', 'return=true');
				$tmpId = shopp('product', 'id', 'return=true');
				$tmpPrice = shopp('product', 'price', 'return=true');
				$allVirginAddons.='<option value="~~|'.$tmpId.'|AddOn'.$pos.'">'.$tmpName.' - '.$tmpPrice.'</option>';
				endwhile;
			endif;
			
			/**  TAGGED MEALS  **/
		
			$tagOptions = ''; 
			shopp('storefront','tag-products', 'tag=all&load=true');
			shopp("collection", "has-products", "load=true");
			while(shopp('collection','products')):
				$tmpName = shopp('product', 'name', 'return=true');
				$tmpId = shopp('product', 'id', 'return=true');
				$tmpPrice = shopp('product', 'price', 'return=true');
				$tagOptions.='<option  value="~*~|'.$tmpId.'|Meal">'.$tmpName.' - '.$tmpPrice.'</option>';
			endwhile;

			
			$allVirginBagels='<option value=""></option>';
			$bagelData = shopp_product( 'BagelDay', 'name' );
			$variants=shopp_product_variants("BagelDay", "name");
			foreach ($variants as $val) {
				// krumo($val->label);
				$tmpId = $val->product;
				$tmpName = $val->label;
				$allVirginBagels.='<option  value="~~|'.$tmpId.'|Bagel">'.$tmpName.' - $0.00</option>';
				// krumo($val);
			}

			// $vars = shopp_product_variant_options($bagelData->id);
			// need to construct options
			// foreach($vars['type'] as $x){
			// 	// krumo($x);
			// }
		 
	/**	
			MEALS  getting all hot meals and addons without the needed strings to cut down on repeating loops
	**/
			
			
			
			
		$students  = new WP_Query(array('post_type'=>'slp_student', 'meta_query'=> array( array('key'=>'parentID', 'value'=>$customerId, 'compare'=>'='))));  
	
	      
	
		 /***  ATTEMPT AT CONTROLS  ***/
	
		 /* date settings */
		 $fmonth = (int) ($_REQUEST['fmonth'] ? $_REQUEST['fmonth'] : date('m'));  
	  
		 $fyear = (int)  ($_REQUEST['fyear'] ? $_REQUEST['fyear'] : date('Y')); 
		 $timestamp  =  mktime(0, 0, 0, $fmonth, 1, $fyear);                     
		 $month_name = date("F", $timestamp);
	    
	
	/** BREAKALL THIS TO CONTROL FUNCTION **/
		 /* select month control */
		 $select_month_control = '<select class="month" name="fmonth" id="month">';
		 for($x = 1; $x <= 12; $x++) {
		 	$select_month_control.= '<option value="'.$x.'"'.($x != $fmonth ? '' : ' selected="selected"').'>'.date('F',mktime(0,0,0,$x,1,$year)).'</option>';
		 }
		 $select_month_control.= '</select>';
	
		 /* select year control */
		 $year_range = 2; $year_start = date('Y');
		 $select_year_control = '<select class="year" name="fyear" id="year">';
		 for($x = ($fyear-floor($year_range/2)); $x <= ($fyear+floor($year_range/2)); $x++) {
		 	$select_year_control.= '<option  value="'.$x.'"'.($x != $fyear ? '' : ' selected="selected"').'>'.$x.'</option>';
		 }
		 $select_year_control.= '</select>';
	
	
		 $controls = '<div id="controls" style="width:220px; padding:1em; float:left;">Select Month:<br />';
		 //$controls .= '<span class="et-tooltip" style="z-index:16;">';
		 //$controls .= '<span class="et-tooltip-box" style="bottom: 35px; display: none; ">Buy lunch selections by month; to change the date of your lunch selections, select the date and click <b>Go</b>. Be sure to <b>Buy Lunches</b> for the current student before changing dates. Past dates cannot, of course, be selected/purchased.<span class="et-tooltip-arrow"></span></span>';
		 $controls.='<form action="" method="post">'.$select_month_control.$select_year_control.'&nbsp;<input id="buy" type="submit" name="dateset" value="Go" class="btn small button-primary"/></form></div>';
	
		 echo '<div id="cuttoff_status" style="width:100%;"></div>';
	
		   echo '<div id="calendar_top" style="width:100%; display:block;  background:#00CC332;">';
			 echo '<div id="row" style="display:block;" >';     
			
		 echo $controls;
/** BREAKALL THIS TO CONTROL FUNCTION **/

	
	
	
				
		echo '<form action="" method="post">';
		echo '<input id="ajax_admin_url" type="hidden" value="'.admin_url('admin-ajax.php').'" />';
		echo '<input type="hidden" name="customer" value="'.$customerId.'" />';
		echo '<input type="hidden" name="holidays" id="holidays" value="" />';
		echo '<input type="hidden" name="weekdays" id="weekdays" value="" />';
		      
		  
		 echo '<div id="left" style="width:260px; padding:1.1em; float:left;"> Select Student:'; 
/*
?>
<span class="et-tooltip" style="z-index:16;">
	<span class="et-tooltip-box" style="bottom: 35px; display: none; ">Select the student in order to make lunch selections. Remember, each student needs their own order, so you must <b>Buy Lunches</b> before you can select another student. <b>Selecting a different student will clear your lunch choices.</b><span class="et-tooltip-arrow"></span>
</span>
<?
*/
		 echo '<div style="float: right; margin-top:15px;">';
		 echo '<input type="button" id="studentChange" value="Change Student" style="display: none; " class="btn small button-primary"/></div><br />';
		 echo '<select style="width:170px;  z-index:+1;"  name="student[recipient_id]" id="student" >';
		 echo '<option  value="" ></option>';
		 	if($customerType=="Staff"){
				echo '<option  value="'.$customerId.'"'.($customerId != $student_selected ? '' : ' selected="selected"').'>'.$customer->firstname.' '.$customer->lastname.'</option>';
			}
	
			foreach ($students->posts as $student){
				echo '<option  value="'.$student->ID.'"'.($student->ID != $student_selected ? '' : ' selected="selected"').'>'.$student->post_title.'</option>';
			}
			
			
			echo '</select><div id="studentName" style="font-weight:bold;"></div></div>';
	
			echo '<div id="middle" style="padding: 1.2em; float:left; vertical-align: middle;" ><h2 style="font-size:26px;">'.$month_name.'&nbsp;'.$year.'</h2></div>';
			echo '<div id="right" style="padding-top:1.1em; float:right; margin-right:40px;" >';
			//echo '<span class="et-tooltip" style="z-index:16;">';
			//echo '<span class="et-tooltip-box" style="bottom: 35px; display: none; ">If all your lunch choices are selected for the current student, click to purchase lunch items.<span class="et-tooltip-arrow"></span></span>';
			echo '<input id="buy" type="submit" name="addtocart" value="Buy Lunches" class="btn small button-primary"/>';
			
			echo '</div></div>';
	
			echo '<br class="clear">';
			echo '<br class="clear">';
			// $today = date('d');  // to know whether items should be disabled; need to check server time
			$today = date("Y-m-d"); 
			
			$calendar = '<table cellpadding="0" cellspacing="0" class="calendar">';
	
			/* table headings */
			$headings = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
			$calendar.= '<tr class="calendar-row"><td class="calendar-day-head">'.implode('</td><td class="calendar-day-head">',$headings).'</td></tr>';
	
			/* days and weeks vars now ... */
			$running_day = date('w',mktime(0,0,0,$month,1,$year));
			$days_in_month = date('t',mktime(0,0,0,$month,1,$year));
			$days_in_this_week = 1;
			$day_counter = 0;
			$dates_array = array();
	
				
	
			
	
	
			/* row for week one */
			$calendar.= '<tr class="calendar-row">';
	
			/* print "blank" days until the first of the current week */
			for($x = 0; $x < $running_day; $x++):
				$calendar.= '<td class="calendar-day-np">&nbsp;</td>';
				$days_in_this_week++;
			endfor;
	
			/* keep going with days.... */
			/**   BEGINNING OF CALENDAR LOOOP  **/
			for($list_day = 1; $list_day <= $days_in_month; $list_day++):
	
	
			$calendar.= '<td class="calendar-day day'.$days_in_this_week.'">';
			/* add in the day number */
			$calendar.= '<div class="day-number">'.$list_day.'</div>';
	
	
	
	
			$calendar.= str_repeat('<p>&nbsp;</p>',1);
			$strDate = $year.'-'.$month.'-'.$list_day;
			
	
			
	
			if($days_in_this_week>1 && $days_in_this_week<7){
				$select_count++;
				/**   attempt to create pulldown  **/
	                       
	
				if(strtotime($strDate) >= strtotime($today)){
					$append = 'disabled="disabled" ';
				}else{
					$append =  'disabled="disabled" past="true"';
					//$append = 'disabled="disabled" ';
						
				}
	            
				$calendar.='<div id="meal-group-'.$list_day.'" style="width:120px;overflow:hidden;">';
				$calendar.="<Label><b>Select Meal:</b></Label>";
				$calendar.='<div class="singleline" id="div-Meal-'.$strDate.'"></div><div style="width:120px;overflow:hidden;"><select  class="wide" style="z-index:+1;" weekday="day'.$days_in_this_week.'"  name="meal[select-meal-'.$select_count.']"  id="Meal-'.$strDate.'" '.$append.' >';
				$calendar.='<option name="Select Lunch:"  value="" selected />';
				
				
				// get all regular meals
				$calendar.= str_replace("~~", $strDate, $allVirginMeals);
				
				// special hotmeal one by tags
	
				$tag = $month.'-'.$list_day.'-'.$year;
				$calendar.='<optgroup  label="Select Hot Meal:">';
				$calendar.=str_replace("~*~", $strDate, $tagOptions);
				shopp('storefront','tag-products', 'tag='.$tag.'&load=true');
				if(shopp('collection', 'hasproducts', 'load=prices')):
					while(shopp('category', 'products')):
						$tmpName = shopp('product', 'name', 'return=true');
						$tmpId = shopp('product', 'id', 'return=true');
						$tmpPrice = shopp('product', 'price', 'return=true');
						$calendar.='<option  value="'.$strDate.'|'.$tmpId.'|Meal">'.$tmpName.' - '.$tmpPrice.'</option>';
					endwhile;
				endif;
				$calendar.='</optgroup>';
				$calendar.= '</select></div></div>';
	
				// see if this meal is in the cart   <------ wtf?
				$mkey = "Meal-".$strDate;
				if(array_key_exists($mkey, $cartitems)){
						
				}




				/** bagel day print out and hiding? **/
				$calendar.='<div id="bagelday-group-'.$list_day.'" style="width:120px;overflow:hidden;">';
				$calendar.="<Label><b>Select Bagel:</b></Label>";
				$tag = $month.'-'.$list_day.'-'.$year;
				$calendar.='<div class="singleline" id="div-BagelDay-'.$strDate.'"></div><div style="width:120px;overflow:hidden;"><select  class="wide" style="z-index:+1;" weekday="day'.$days_in_this_week.'"  name="bagelday[select-bagelday-'.$select_count.']"  id="BagelDay-'.$strDate.'"  >'; //'.$append.'
				// $calendar.='<optgroup  label="Make BagelDay Selection:">';
				$calendar.= str_replace("~~", $strDate, $allVirginBagels);
				$calendar.='</select></div>';
				$calendar.='</div>';





	
	
	
				/*   ADD ons pull down */
				$calendar.="<Label>Select Add Ons:</Label>";
				for($pos=1; $pos<4; $pos++):
					$calendar.='<div class="singleline"  id="div-AddOn'.$pos.'-'.$strDate.'"></div><div style="width:120px;overflow:hidden;"><select class="wide" style="z-index:+1;" name="addon[select-addon'.$pos.'-'.$select_count.']" id="AddOn'.$pos.'-'.$strDate.'" '.$append.' >';
					$calendar.='<optgroup selected label="Select Add-On:">';
					$calendar.='<option name="" value=""></option>';
					$calendar.= str_replace("~~", $strDate, $allVirginAddons);
					$calendar.='</optgroup></select></div>';
				endfor;
	

				

	
	
			}
	
	
	
			$calendar.= '</td>';
			if($running_day == 6):
			$calendar.= '</tr>';
			if(($day_counter+1) != $days_in_month):
			$calendar.= '<tr class="calendar-row">';
			endif;
			$running_day = -1;
			$days_in_this_week = 0;
			endif;
			$days_in_this_week++; $running_day++; $day_counter++;
			endfor;
	
			/* finish the rest of the days in the week */
			if($days_in_this_week < 8):
			for($x = 1; $x <= (8 - $days_in_this_week); $x++):
			$calendar.= '<td class="calendar-day-np">&nbsp;</td>';
			endfor;
			endif;
	
			/* final row */
			$calendar.= '</tr>';
	
			/* end the table */
			$calendar.= '</table>';
	
			$calender.='</form>';
	
			/* all done, return result */
			return $calendar;
		}
	}
?>