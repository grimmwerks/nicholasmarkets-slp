<?php

if(!(function_exists('draw_admin_calendar'))){
	function draw_admin_calendar($month,$year, $student_selected){           

	/**  TAGGED MEALS  **/
	
		$tagOptions = ''; 
		shopp('storefront','tag-products', 'tag=all&load=true');
		shopp("collection", "has-products", "load=true");
		while(shopp('collection','products')):
			$tmpName = shopp('product', 'name', 'return=true');
			$tagOptions.= $tmpName.'<br />';
		endwhile;

	      
	
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
	
	
	
		 $controls = '<form action="" method="post">';
		 $controls .= '<input id="ajax_admin_url" type="hidden" value="'.admin_url('admin-ajax.php').'" />';
		 $controls .= '<input type="hidden" name="holidays" id="holidays" value="" />';
		 $controls .= '<input type="hidden" name="weekdays" id="weekdays" value="" />';
		 
		  $controls .= '<div id="left" style="width:260px; padding:1em; float:left;">';		  
		 $controls .=   do_shortcode('[slp_schools_select selected="'.$_REQUEST['schoolID'].'"]');
		 $controls .= '</div>';
		 
		 
		 
		 $controls .= '<div id="controls" style="width:220px; padding:1em; float:left;">Select Month:<br />';
		$controls.=$select_month_control.$select_year_control.'&nbsp;<input id="buy" type="submit" name="dateset" value="Go" class="btn small button-primary"/></form></div>';
	
		   echo '<div id="calendar_top" style="width:100%; display:block;  background:#00CC332;">';
			 echo '<div id="row" style="display:block;" >';     
			
		 echo $controls;


	
	
	
		

	
			echo '<div id="middle" style="padding: 2em; float:left; vertical-align: middle;" ><h2>'.$month_name.'&nbsp;'.$year.'</h2></div>';
			
			echo '</div>';
	
			echo '<br class="clear">';
			echo '<br class="clear">';
			


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
				//$calendar.="<Label>Select Meal:</Label>";
	                       
	
			/*
	if(strtotime($strDate) >= strtotime($today)){
					$append = '';
				}else{
					$append =  'disabled="disabled" past="true"';
						
				}
*/
	            
	
	
				$calendar.='<div class="singleline" id="div-Meal-'.$strDate.'" weekday="day'.$days_in_this_week.'"  >';
				
				
				
					
				$tag = $month.'-'.$list_day.'-'.$year;
				$calendar .=$tag . '<br />';
				$calendar.= $tagOptions;
				
				shopp('storefront','tag-products', 'tag='.$tag.'&load=true');
				if(shopp('collection', 'hasproducts', 'load=prices')):
					while(shopp('category', 'products')):
						$calendar.=  shopp('product', 'name', 'return=true') . '<br />';
					endwhile;
				endif;
					
				// see if this meal is in the cart   <------ wtf?
				$mkey = "Meal-".$strDate;
				if(array_key_exists($mkey, $cartitems)){
						
				}
	
				$calendar .= '</div>';
					
					
	
	
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