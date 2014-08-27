<?php
/*
Plugin Name: Nicholas Markets Customization
Plugin URI: www.icirclemedia.com
Description: Customization of NicholasMarkets with custom post types.
Version: 2.0
Author: Garry Schafer
Author URI: http://www.grimmwerks.com
Site Wide Only: true
Network: true
*/

define('NM_BASE_FILE', __FILE__);
define('NM_BASE_DIR', dirname(__FILE__));
//define('datepick_package', NM_BASE_DIR . "/includes/admin/jquery.datepick.package");
define('datepick_package',  plugins_url('/includes/js/jquery.datepick.package', NM_BASE_FILE));

require_once(NM_BASE_DIR . "/../krumo_0/class.krumo.php");


class NM_Custom{
	
	public function __construct(){
		require_once( NM_BASE_DIR . "/includes/php/nicholas-markets-functions.php");
		// require_once( NM_BASE_DIR . "/includes/php/nm_posttype_recipes.php");
		// require_once( NM_BASE_DIR . "/includes/php/nm-truly-local_posttypes.php");

		add_action("admin_menu", array(&$this, "create_slp_menu_page"));

		$this->nm_init_slp_posttypes();

		add_action('init', array(&$this, 'nm_init_posttypes'));
		add_action( 'wp_enqueue_scripts', array(&$this, 'wp_enqueue_scripts') );
	}
	


	
	public function wp_enqueue_scripts(){
		wp_enqueue_style( 'nm-custom.css', plugins_url('/includes/css/nicholasmarkets.css', NM_BASE_FILE));
	}


	
	public function create_slp_menu_page(){
		add_menu_page("School Lunch", "School Lunch", "add_users", "school-lunch-program",'','', 3);
	}
	
	public function nm_init_slp_posttypes(){
		if(!class_exists('WP_List_Table')){
			require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
		}
		require_once(NM_BASE_DIR . '/includes/php/school_lunch_program/nm_slp_school_post.php');
		require_once(NM_BASE_DIR .  "/includes/php/school_lunch_program/nm_slp_student_post.php");
		require_once(NM_BASE_DIR .  "/includes/php/school_lunch_program/nm_slp_teacher_post.php");
		require_once(NM_BASE_DIR  . "/includes/php/school_lunch_program/nm_slp_reports.php");
		require_once(NM_BASE_DIR . "/includes/php/school_lunch_program/nm_slp_customer_metaboxes.php");
		require_once(NM_BASE_DIR . "/includes/php/school_lunch_program/shortcodes.php");
		require_once( NM_BASE_DIR . "/includes/php/school_lunch_program/nm_school_calendar.php");
		require_once( NM_BASE_DIR . "/includes/php/school_lunch_program/nm_school_calendar_admin.php");
		require_once( NM_BASE_DIR . "/includes/php/school_lunch_program/nm_slp_dashboard_totals.php");
		//require_once( NM_BASE_DIR . "/includes/php/school_lunch_program/nm_slp_dashboard_reporter.php");
		require_once( NM_BASE_DIR . "/includes/php/school_lunch_program/shortcodes_holiday_calendar.php");
		require_once( NM_BASE_DIR . "/includes/php/school_lunch_program/shortcodes_bagelday_calendar.php");
		require_once( NM_BASE_DIR . "/includes/php/school_lunch_program/nm_slp_admin.php");
		require_once(NM_BASE_DIR . "/includes/php/school_lunch_program/nm_slp_report_runner.php");
		require_once(NM_BASE_DIR . '/includes/php/nm_testimonial_taxonomy.php');

		//require_once( NM_BASE_DIR . "/includes/php/nm_posttype_recipes.php");
	}
	
	
}

$nm = new NM_Custom();



add_action('activated_plugin','save_error');
function save_error(){
    update_option('plugin_error',  ob_get_contents());
}





?>