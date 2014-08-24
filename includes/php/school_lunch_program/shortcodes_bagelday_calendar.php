<?php 

function slp_bagelday_calendar_shortcode($args){
	$schoolID = $args['schoolid'];
	$readOnly = $args['readonly'];
	
	
	define('cp_lang', 'en-GB');
	define('datepick_package', get_stylesheet_directory_uri() . "/includes/admin/jquery.datepick.package");
	
	#	how months are displayed for posts. we choose to not use PHP's abbrevations since they
#	are not typographicly correct.
#
	$cp_months = array(
	'en-GB' => array(0,'Jan','Feb','Mar','Apr','May','June','July','Aug','Sept','Oct','Nov','Dec'),
	'sv' => array(0,'jan','feb','mars','apr','maj','juni','juli','aug','sept','okt','nov','dec'),
	// make your custom one here and set cp_lang to it.
	'custom' => array(0,'jan','feb','mars','apr','maj','juni','juli','aug','sept','okt','nov','dec')
	);
#
#	what to separate date and post title with.
#
	$cp_separator = ': ';
	

		wp_enqueue_script("jquery"); 
		global $post;
		$id = $post->ID;

		$cal =  'calendar_pickr_bagelday_'. $id; 
		
		$datum		= get_post_meta($post->ID,'bageldays',true);
		$datum_slut	= get_post_meta($post->ID,'cp_date_end',true);
		$tid 		= get_post_meta($post->ID,'cp_tid',true);	
	
		// output of css linking and jquery packages
	?>
	
	<link type="text/css" href="<?php echo datepick_package; ?>/redmond.datepick.css" rel="stylesheet" />
	<script type="text/javascript" src="<?php echo datepick_package; ?>/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="<?php echo datepick_package; ?>/jquery.datepick.js"></script>
	<script type="text/javascript" src="<?php echo datepick_package; ?>/jquery.datepick.ext.js"></script>
	<?php // remove localization when releasing plugin ?>
	<script type="text/javascript" src="<?php echo datepick_package; ?>/jquery.datepick.lang.min.js"></script>
		
		
		
	<script type="text/javascript">

	var $j = jQuery.noConflict();
	$j.datepick.setDefaults($j.datepick.regional['<?php echo cp_lang; ?>']); // read in plugin setting later for language ...	
		
	$j(function() {
		var saved_dates = $j('#bageldays_<?php echo $id; ?>').val().split(',');
	
		$j.datepick.setDefaults($j.datepick.regional['<?php echo cp_lang; ?>']); // read in plugin setting later for language ...		
		$j("#<?php echo $cal; ?>").datepick(
		{dateFormat: 'yyyy-m-d', firstDay:0,
		 multiSelect: 999, monthsToShow: 1, 
			
			<?php if($readOnly){
				echo 'onDate: $j.datepick.notSelectable,';
			}else{
				echo 'onDate: $j.datepick.noWeekends,';
			} ?>				
			
			onSelect: function(dates) { 
				fixdates = Array();
				//f = $j.datepick.ATOM;
				f = 'yyyy-m-d';
				if(dates.length>1) {
	
					for (var i = 0; i < dates.length; i++) { 
						fixdates.push($j.datepick.formatDate(f,dates[i]));
					}
					fixdates.sort();
					
				}else fixdates = $j.datepick.formatDate(f,dates[0]);
	
				$j('#bageldays_<?php echo $id; ?>').val(fixdates);
			} 
	
		});
	
		$j("#<?php echo $cal; ?>").datepick('setDate', saved_dates);
	});
	</script>
		
		
		
		
	<br />
	
	<center><span>
	<input type="hidden" id="bageldays_<?php echo $id; ?>" name="bageldays" value="<?php echo $datum; ?>" />
	<div style="width:90%;" id="<?php echo $cal; ?>"></div>
	</span></center>
	<p>&nbsp;</p>
<?php
}
	
add_shortcode('slp_bagelday_calendar', 'slp_bagelday_calendar_shortcode');	

/***************************** DATE PICK ADMIN ********************************
 *******************************************************************************/
 
 
 
	
?>