<?php

/**  
 *
 *      THIS IS THE CUSTOM POST TYPE FOR SLP SCHOOL
 * 
 */
 
	function slp_create_school_post_type(){
		register_post_type('slp_school',
		array(
			'labels' => array('name'=> __("SLP Schools"),
								'singular_name'=>__('School'),
								'add_new'=>__('Add School'),
								'add_new_item'=>__('Add New School'),
								'edit'=>__('Edit'),
								'edit_item'=>__('Edit School'),
								'new_item'=>__("New School"),
								'view'=>__('View School'),
								'view_item' => __('View School'),
								'not_found' => __('No Schools found.'),
								'not_found_in_trash'=>__('No Schools found in Trash')
								),
			'public'=>true,
			'can_export'=>true,
			'publicly_querable'=>true,
			'exclude_from_search' => true,
			'menu_position'=>5,
			'hierarchical' => false,
			'show_in_menu'=> 'school-lunch-program',
			'supports'=> array('title', 'custom-fields')
			
				)
		);
		
	}
	
	add_action('init', 'slp_create_school_post_type');
	
	
	
	
	
	/**
		meta box for school
	**/
	function add_slp_school_metabox(){
		add_meta_box('school-info', __('School Info'), 'show_slp_school_info', 'slp_school', 'normal', 'core');
		
		/** beginning MAIN SCHOOL INFO metabox **/
		function show_slp_school_info($post){
			global $post;
			$schoolID = $post->ID;
			$commission = get_post_meta($post->ID, 'commission', TRUE);
			$weekdays = unserialize(get_post_meta($post->ID, 'weekdays', TRUE));
			$hasCommission = get_post_meta($post->ID, 'hasCommission', TRUE);
			$lunchPrice = get_post_meta($post->ID, 'lunchPrice', TRUE);
			
		
			
			$staff  = new WP_Query(array('showposts'=>-1, 'post_type'=>'slp_teacher', 'meta_key'=>'grade','orderby'=>'meta_value','order'=>'asc','meta_query'=> array( array('key'=>'schoolID', 'value'=>$schoolID, 'compare'=>'='))));
			
			wp_nonce_field('slp_school-info-nonce', 'slp_school-info-nonce');
			
			echo '<div style="width:100%; padding-top:5px; padding-bottom: 5px;"><div style="float:left;">';
?>			
			<div style="padding-left:10px; float:left;padding-bottom:10px;">
			<label class="howto" for="lunchPrice">
			<span style="padding-bottom:10px">Lunch Price</span><br /></label>
			$<input type="text" name="lunchPrice" size="4" value="<?php echo $lunchPrice; ?>" />
			</div>	
			
			<div style="padding-left:10px; float:left;padding-bottom:10px;">
				<label class="howto" for="lunchPrice"><span style="padding-bottom:20px">Commission</span><br /></label>
				<span><label><input type="checkbox" name="hasCommission"  value="true" <?php echo (($hasCommission=="true") ?  ' checked ' :  ""); ?> /> Commission</label></span>
			</div>
			
			<div style="padding-left:10px; float:left; padding-bottom:10px;">
				<label class="howto" for="weekdays[]">
				<span style="padding-bottom:10px">Amount</span><br /></label>
				$<input type="text" name="commission" size="4" value="<?php echo $commission; ?>" />
			</div>	
			
		
			
<!--
<?php			
			echo do_shortcode('[slp_commission selected="'.$commission.'"]');
			
?>	
-->		
			<div style="padding-left:10px; width:400px; float:left;">
			<label class="howto" for="weekdays[]">
			<span style="padding-bottom:10px">Active Weekdays</span><br /></label>
<?php
				echo '<span style="padding-top:5px;"><label><input type="checkbox" name="weekdays[]"  value="Monday" '. (in_array('Monday', $weekdays) ? ' checked ' : "") .' /> Monday</label></span>';
				echo '<span style="padding-left:5px;"><label><input type="checkbox" name="weekdays[]" value="Tuesday" '. (in_array('Tuesday', $weekdays) ? ' checked ' : "") .' /> Tuesday</label></span>';
				echo '<span style="padding-left:5px;"><label><input type="checkbox" name="weekdays[]" value="Wednesday" '. (in_array('Wednesday', $weekdays) ? ' checked ' : "") .' /> Wednesday</label></span>';
				echo '<span style="padding-left:5px;"><label><input type="checkbox" name="weekdays[]" value="Thursday" '. (in_array('Thursday', $weekdays) ? ' checked ' : "") .' /> Thursday</label></span>';
				echo '<span style="padding-left:5px;"><label><input type="checkbox" name="weekdays[]" value="Friday" '. (in_array('Friday', $weekdays) ? ' checked ' : "") .' /> Friday</label></span>';
?>				
			</div>
<?php			
			echo '</div></div>';
			
			echo '<p>&nbsp;</p><br class="clear" />';
			
	
echo '<table class="wp-list-table widefat fixed posts" cellspacing="0"><thead><tr><th scope="col" id="grade" class="manage-column column-categories" style=""><span>Grade</span></th><th scope="col" id="title" class="manage-column column-title" style=""><span>Teacher Name</span></th></tr></thead>';
			echo '<tbody id="the-list">';

			if($staff->post_count){
				foreach($staff->posts as $teacher){
					echo '<tr><td>'. get_post_meta($teacher->ID, 'grade', TRUE) .'</td>';
					echo '<td><a href="/wp-admin/post.php?action=edit&post='. $teacher->ID .'">'. $teacher->post_title . '</a></td></tr>';
				}
			}

			echo '</tbody>';
			echo '</table>';
			echo '<br class="clear" />';
		}
		
		/** END MAIN SCHOOL INFO metabox **/
		
		
		
		
		
		
		/**  ADDING CALENDAR METABOX FOR HOLIDAYS  **/
		
		add_meta_box('school-holidays',__('School Holidays'), 'show_slp_school_holidays', 'slp_school', 'side', 'core');
		
		function show_slp_school_holidays($post){
			echo do_shortcode('[slp_holiday_calendar]');
		}
		
		/**  END CALENDAR METABOX FOR HOLIDAYS  **/
		
		
		/** metabox for calendar ordering cuttoff **/
		
		add_meta_box('school-cuttoffs', __('School Ordering Cutoff Dates'), 'show_slp_school_cuttoffs', 'slp_school', 'normal', 'core');
		
		
		function show_slp_school_cuttoffs($post){
			global $post;
			$cutoff_september = get_post_meta($post->ID, 'cutoff_september', TRUE);
			$cutoff_october = get_post_meta($post->ID, 'cutoff_october', TRUE);
			$cutoff_november = get_post_meta($post->ID, 'cutoff_november', TRUE);
			$cutoff_december = get_post_meta($post->ID, 'cutoff_december', TRUE);
			$cutoff_january = get_post_meta($post->ID, 'cutoff_january', TRUE);
			$cutoff_february = get_post_meta($post->ID, 'cutoff_february', TRUE);
			$cutoff_march = get_post_meta($post->ID, 'cutoff_march', TRUE);
			$cutoff_april = get_post_meta($post->ID, 'cutoff_april', TRUE);
			$cutoff_may = get_post_meta($post->ID, 'cutoff_may', TRUE);
			$cutoff_june = get_post_meta($post->ID, 'cutoff_may', TRUE);

		
?>
			<label class="howto" for="cuttoffs"><span style="padding-bottom:10px">Put in the actual <b>DAY</b> for the cuttoff of ordering for that month; for example if <b>4</b> is in the September box, then no more orders for Septempber will be accepted on the 4th.  Make sure this is single digit (ie 4) rather than <b>04</b>.</span><br /></label><br />
			<div id="cuttoffs" style="width: 100%;">
				
				<div style="padding-left:10px; float:left; padding-bottom:10px;">
					<label class="howto" for="cutoff_september">
						<span style="padding-bottom:10px">September</span><br /></label>
							<input type="text" name="cutoff_september" size="4" value="<?php echo $cutoff_september; ?>" />
				</div>	
			
				<div style="padding-left:10px; float:left; padding-bottom:10px;">
					<label class="howto" for="cutoff_october">
						<span style="padding-bottom:10px">October</span><br /></label>
							<input type="text" name="cutoff_october" size="4" value="<?php echo $cutoff_october; ?>" />
				</div>	
			
				<div style="padding-left:10px; float:left; padding-bottom:10px;">
					<label class="howto" for="cutoff_november">
						<span style="padding-bottom:10px">November</span><br /></label>
							<input type="text" name="cutoff_november" size="4" value="<?php echo $cutoff_november; ?>" />
				</div>	
			
				<div style="padding-left:10px; float:left; padding-bottom:10px;">
					<label class="howto" for="cutoff_december">
						<span style="padding-bottom:10px">December</span><br /></label>
							<input type="text" name="cutoff_december" size="4" value="<?php echo $cutoff_december; ?>" />
				</div>	
		
				<div style="padding-left:10px; float:left; padding-bottom:10px;">
					<label class="howto" for="cutoff_january">
						<span style="padding-bottom:10px">January</span><br /></label>
							<input type="text" name="cutoff_january" size="4" value="<?php echo $cutoff_january; ?>" />
				</div>	
				
				<div style="padding-left:10px; float:left; padding-bottom:10px;">
					<label class="howto" for="cutoff_february">
						<span style="padding-bottom:10px">February</span><br /></label>
							<input type="text" name="cutoff_february" size="4" value="<?php echo $cutoff_february; ?>" />
				</div>	
				
				<div style="padding-left:10px; float:left; padding-bottom:10px;">
					<label class="howto" for="cutoff_march">
						<span style="padding-bottom:10px">March</span><br /></label>
							<input type="text" name="cutoff_march" size="4" value="<?php echo $cutoff_march; ?>" />
				</div>	
				
				<div style="padding-left:10px; float:left; padding-bottom:10px;">
					<label class="howto" for="cutoff_april">
						<span style="padding-bottom:10px">April</span><br /></label>
							<input type="text" name="cutoff_april" size="4" value="<?php echo $cutoff_april; ?>" />
				</div>	
				
				<div style="padding-left:10px; float:left; padding-bottom:10px;">
					<label class="howto" for="cutoff_may">
						<span style="padding-bottom:10px">May</span><br /></label>
							<input type="text" name="cutoff_may" size="4" value="<?php echo $cutoff_may; ?>" />
				</div>	
				
				<div style="padding-left:10px; float:left; padding-bottom:10px;">
					<label class="howto" for="cutoff_june">
						<span style="padding-bottom:10px">June</span><br /></label>
							<input type="text" name="cutoff_june" size="4" value="<?php echo $cutoff_june; ?>" />
				</div>		
			</div>
		
			<p>&nbsp;</p><br class="clear" />
<?php
		}
		
		
		
	}



	
	
	
if(is_admin()){
	add_action('admin_menu', 'add_slp_school_metabox');
}









/** 
			SAVING SCHOOL INFO 
**/
function save_school_information($post_id){
	if(get_post_type($post_id) == "slp_school"){
		if(!wp_verify_nonce($_POST['slp_school-info-nonce'], 'slp_school-info-nonce')){
			return $post_id;
		}
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
			return $post_id;
				
			// Check permissions
		if ( !current_user_can( 'edit_post', $post_id ) )
			return $post_id;
		
		update_post_meta($post_id, "weekdays",serialize($_POST['weekdays']));
		
		if(isset($_POST['holidays'])){
			sort($_POST['holidays'], 'SORT_NUMERIC');
			update_post_meta($post_id, 'holidays', $_POST['holidays']);
		}
			
		if(isset($_POST['commission'])){
			update_post_meta($post_id, "commission", esc_attr($_POST['commission']));
		}
		
		if(isset($_POST['lunchPrice'])){
			update_post_meta($post_id, "lunchPrice", esc_attr($_POST['lunchPrice']));
		}
		
		if(isset($_POST['hasCommission'])){
			update_post_meta($post_id, "hasCommission", esc_attr($_POST['hasCommission']));
		}else{
			delete_post_meta($post_id, 'hasCommission');
		}
		
		// cuttoff dates
		if(isset($_POST['cutoff_september'])){
			update_post_meta($post_id, "cutoff_september", esc_attr($_POST['cutoff_september']));
		}else{
			delete_post_meta($post_id, 'cutoff_september');
		}
		if(isset($_POST['cutoff_september'])){
			update_post_meta($post_id, "cutoff_october", esc_attr($_POST['cutoff_october']));
		}else{
			delete_post_meta($post_id, 'cutoff_october');
		}
		
		if(isset($_POST['cutoff_november'])){
			update_post_meta($post_id, "cutoff_november", esc_attr($_POST['cutoff_november']));
		}else{
			delete_post_meta($post_id, 'cutoff_november');
		}
		
		if(isset($_POST['cutoff_december'])){
			update_post_meta($post_id, "cutoff_december", esc_attr($_POST['cutoff_december']));
		}else{
			delete_post_meta($post_id, 'cutoff_december');
		}
		
		if(isset($_POST['cutoff_january'])){
			update_post_meta($post_id, "cutoff_january", esc_attr($_POST['cutoff_january']));
		}else{
			delete_post_meta($post_id, 'cutoff_january');
		}
		
		if(isset($_POST['cutoff_february'])){
			update_post_meta($post_id, "cutoff_february", esc_attr($_POST['cutoff_february']));
		}else{
			delete_post_meta($post_id, 'cutoff_february');
		}
		
		if(isset($_POST['cutoff_march'])){
			update_post_meta($post_id, "cutoff_march", esc_attr($_POST['cutoff_march']));
		}else{
			delete_post_meta($post_id, 'cutoff_march');
		}
		
		if(isset($_POST['cutoff_april'])){
			update_post_meta($post_id, "cutoff_april", esc_attr($_POST['cutoff_april']));
		}else{
			delete_post_meta($post_id, 'cutoff_april');
		}
		
		if(isset($_POST['cutoff_may'])){
			update_post_meta($post_id, "cutoff_may", esc_attr($_POST['cutoff_may']));
		}else{
			delete_post_meta($post_id, 'cutoff_may');
		}
		
		if(isset($_POST['cutoff_june'])){
			update_post_meta($post_id, "cutoff_june", esc_attr($_POST['cutoff_june']));
		}else{
			delete_post_meta($post_id, 'cutoff_june');
		}

	}
}

add_action('save_post', 'save_school_information');







/**
	COLUMNS AND SORTING  SCHOOL VIEWS
**/
// adding new columns to view
function slp_school_columns($cols){
	$cols = array(
			'cb' => '<input type="checkbox" />',
			'title' => 'Title',
			'pricing' => "Pricing", 
			'teachers' => 'Teachers',
			'weekdays' => 'Weekdays', 
			'holidays' => 'Holidays'
			/* 'date' => 'Date' */
		);
	return $cols;	
}
add_filter('manage_edit-slp_school_columns', 'slp_school_columns');

// adding to sorting
function slp_school_register_sortable($cols){
	$cols['title'] = 'title';
	$cols['commission'] = 'commission';
	$cols['date'] = 'date';
	return $cols;
}

add_filter('manage_edit-slp_school_sortable_columns', 'slp_school_register_sortable');




/**
		CUSTOM CONTENT FOR  STUDENT LIST
**/
function slp_school_column_data($col, $post_id){
	if(get_post_type($post_id) == "slp_school"){
		// query for slp_student where meta_key schoolID = post_id
		//$meta = get_post_meta($post_id);
		switch($col){
			case 'pricing':
				echo "<p>Lunch Prices:<br />";
				echo '<strong>$ '. get_post_meta($post_id, "lunchPrice", true) . '</strong></p>';
				if(get_post_meta($post_id, "hasCommission", true)=="true"){
					echo "Commission:<br />";
					echo "<strong>$ ".get_post_meta($post_id, "commission", true).'</strong>';
				}
				break;
			case 'teachers':
				$staff  = new WP_Query(array('showposts'=>-1, 'post_type'=>'slp_teacher',  'meta_key'=>'grade','orderby'=>'meta_value','order'=>'desc','meta_query'=> array( array('key'=>'schoolID', 'value'=>$post_id, 'compare'=>'='))));
				
				if($staff->post_count){
					foreach($staff->posts as $teacher){
						echo '<a href="/wp-admin/post.php?action=edit&post='. $teacher->ID .'">'. $teacher->post_title . '</a><br />';
					}
				}
				break;
			case "weekdays":
				$weekdays = unserialize(get_post_meta($post_id, 'weekdays', TRUE));
				echo implode(", ", $weekdays);
				break;
			case 'holidays':
				echo do_shortcode('[slp_holiday_calendar readonly="true"]');
				break;
		}
	}
}

add_filter("manage_posts_custom_column", "slp_school_column_data", 10, 2);




		
?>