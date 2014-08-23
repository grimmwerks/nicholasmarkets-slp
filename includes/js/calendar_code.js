<script type="text/javascript">

//<![CDATA[
var $j = jQuery.noConflict();
var thisYear = <?php echo $thisYear; ?>;
var thisMonth = <?php echo $thisMonth; ?>;
var thisCart = <?php echo $json_cartitems; ?>;
var thisStudent = <?php echo $student_cart; ?>;
	var thisCustomerID = <?php echo $customerId; ?>;
var holidays = new Array();

$j(document).ready(function(){ 
	checkWeekdays();
	$j("td div select").mousedown(function(){
		if($j.browser.msie){$j(this).css("width", "auto")}
	});
			
	// change function
	$j("td div select").change(function(){
		if($j.browser.msie){$j(this).width(120)}
	});
			
	// blur function
	$j("td div select").blur(function(){
		if($j.browser.msie){$j(this).width(120)}
	});		
	
	
   

	$j("#studentChange").hide();
	if(thisStudent>0){
		setUpStudent(thisStudent);
	}
			 
	// reset click
	$j("#studentChange").click(function(){
	   	if(confirm("Changing the student will clear your currently selected items.  Are you sure you want to do this?")){ 
	   	// attempting to clear cart
	   	var admin_ajaxurl = jQuery('#ajax_admin_url').val();
	   	var data = {action: "student_lunch_clear_cart"};
	   	$j.post(admin_ajaxurl, data, function(res){});
	   	// end clear cart           
	   	$j(".calendar-day").children().show(100);
	   	$j(".calendar-day").removeClass("calendar-day-vacation");    	
		$j("#student").show(100); $j("#student").val("");
		$j("#studentChange").hide(100);
		$j("#studentName").hide(100); 
		$j("[id*='div-']").html("");
		/* $j("[id*='Meal']").show(100); $j("[id*='AddOn']").show(100); */
		checkWeekdays();
	
		$j("td div select").attr("disabled", true);
		$j("td div select").val("");
		$j("td div select").show(100);
		}
	})
	
	
	$j("#student").change(function(){        
		if($j(this).attr('value')){          
			setUpStudent($j(this).attr('value'));
		}
	})
	
	
	function changeMealPrices(price){
		$j("[id*='Meal'] option").each(function(index){ 
			if($j(this).text()!=""){
				var n=($j(this).text()).split("$");
				$j(this).text(n[0] + '$' + price);
			}
		});
	}
	
	function removeWeekends(){
		$j(".day1").addClass("calendar-day-np");
		$j(".day7").addClass("calendar-day-np");
	}
	
	function checkWeekdays(weekdays){
		$j(".calendar-day").removeClass("calendar-day-np");
		$j("select[past='true']").parents("td").addClass("calendar-day-past");
		removeWeekends();
		if(weekdays){
		// have to make a negative of the array; I'm getting days that SHOULD be active
			targetDays = new Array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday");
			for (var i = 0; i<weekdays.length; i++) {
				var arrlen = targetDays.length;
				for (var j = 0; j<arrlen; j++) {
					if (weekdays[i] == targetDays[j]) {
						targetDays = targetDays.slice(0, j).concat(targetDays.slice(j+1, arrlen));
					}//if close
				}//for close
			}
			for(var i=0; i<targetDays.length; i++){
				var day = mapWeekday(targetDays[i]);
				$j("." + day).addClass("calendar-day-np");
				$j(".calendar-day-np div select").attr('disabled', true); 
				$j(".calendar-day-np").children().hide();
				$j(".day-number").show();
				
			}
		}
	}
	
	
	function checkHolidays(holidays){
		if(holidays){
			target = thisYear + '-' + thisMonth;
			result = new Array();
			dates = holidays.split(",");
			for(var i=0; i<dates.length; i++){
				if(dates[i].indexOf(target)!=-1){
					$j('select[id$="'+dates[i]+'"]').parents('td').addClass("calendar-day-vacation");
					$j(".calendar-day-vacation").children().hide();
					$j(".day-number").show();
				}
			}
		}
	}
	
	
	function mapWeekday(day){
		if(day=="Monday"){return "day2";}
		if(day=="Tuesday"){return "day3";}
		if(day=="Wednesday"){return "day4";}
		if(day=="Thursday"){return "day5";}
		if(day=="Friday"){return "day6";}
	}
	

	function setUpStudent(id){
		var studentName= $j("#student :selected").text();
		   
		$j("#studentName").html(studentName);   
		$j("#studentName").show(100);  
		$j("#studentChange").show();
		$j("#student").hide();
		$j("select[past='true']").parents("td").addClass("calendar-day-past");

		var admin_ajaxurl = jQuery('#ajax_admin_url').val();
		
		var schoolData = {action: "slp_school_data_by_recipient", recipientID: id, customerID: thisCustomerID};
		$j.post(admin_ajaxurl, schoolData, function(response){//console.log(response);
			ret = $j.parseJSON(response);
			$j("input[name=holidays]").val(ret['holidays']);
			$j("input[name=weekdays]").val(ret['weekdays']);
			changeMealPrices(ret['lunchPrice']);
			checkWeekdays(ret['weekdays']);
			checkHolidays(ret['holidays']);
		});
		
		var data = {action: "student_lunch_lookup", studentId: id, month: thisMonth, year: thisYear };
		
	    $j("[id*='Meal']").attr('disabled', true);
		$j("[id*='AddOn']").attr('disabled', true);

		$j.post(admin_ajaxurl, data, function(response) { 
			//console.log(response); 
			$j("[id*='Meal'][past!='true']").attr('disabled', false);                   
		    $j("[id*='AddOn'][past!='true']").attr('disabled', false); 
		    // disable all the non active day selects - weekdays or holidays.
		    $j(".calendar-day-np div select").attr('disabled', true);

		    $j("[id*='Meal']").val("");
		    $j("[id*='AddOn']").val("");
		    
		    for(var index in thisCart) {
			    $j("#"+index).val(thisCart[index]);
		    }
		   
		   // echo need to see how this works 
			var ret = $j.parseJSON(response);
			
			for(var i=0; i<ret.length; i++){
				var tmpId = ret[i].lunchType + '-' + ret[i].lunchDate;
				var tmpVal = ret[i].lunchDate + '|'+ret[i].lunchId+'|'+ret[i].lunchType;
				//$j("#"+tmpId).val(tmpVal);   
			   // $j("#"+tmpId).attr('disabled', true);  
				  $j("#"+tmpId).hide();
				  var tmpName = ret[i].lunchName.substring(0,14);
				  if(ret[i].lunchName.length>20){tmpName+="..."};
				  $j("#div-"+tmpId).html('<b>'+tmpName+'</b>');        
			}
			
		});
	}
});

//]]>  
</script>
