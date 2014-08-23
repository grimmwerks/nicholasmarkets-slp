<?php
function init_slp_reporting_widget(){
	global $wp_meta_boxes;
	wp_add_dashboard_widget('slp_reporting', "School Lunch Reporter", 'show_slp_reporting');
}





function show_slp_reporting(){
	
	global $wpdb;
	
	?>
	<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/includes/styles/jquery-ui-1.8.14.custom.css" type="text/css" media="all" />    
	<p> Please choose report parameters:</p> 
	
	<div class="wrap">
		<div class="alignleft">      
			<span>
				<label class="howto" for="report">
					<span>Report Type:</span><br />
					<select style="z-index:+1;" name="report" id="report">
						<option value></option> 
						<option value="kitchen">Kitchen Report - All Grades</option> 
						<option value="server">Server Report - By Grade</option> 
						<option value="student">Student Report - By Grade</option>
					</select>
				</label>
			</span>
		</div>
		
		<div class="alignleft">
			<span>
			<?php echo do_shortcode('[slp_schools_select]'); ?>
			</span>
		</div>
		
	</div>
	<div class="clear" /></div>
	<div id="div_results" >
	</div>
	<div class="clear" /></div>
	
<?php

}

add_action('wp_dashboard_setup', 'init_slp_reporting_widget');
?>