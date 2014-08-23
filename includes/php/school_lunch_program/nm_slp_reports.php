<?php

class SLP_Reports_List_Table extends WP_List_Table {
    

    
    /** ************************************************************************
     * REQUIRED. Set up a constructor that references the parent constructor. We 
     * use the parent reference to set some default configs.
     ***************************************************************************/
    function __construct(){
        global $status, $page;
        
       // add_action('admin_head', array(&$this, 'admin_header'));
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'report',     //singular name of the listed records
            'plural'    => 'reports',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
        
    }
       
    /**
    .column-active {
    padding: 11px 0 0;
    vertical-align: center;
    width: 2.2em;
}
.widefat .column-orderID {
    padding: 11px 0 0;
    vertical-align: top;
    width: 2.2em;
}
**/
    
    
    /** ************************************************************************
     * Recommended. This method is called when the parent class can't find a method
     * specifically build for a given column. Generally, it's recommended to include
     * one method for each column you want to render, keeping your package class
     * neat and organized. For example, if the class needs to process a column
     * named 'title', it would first see if a method named $this->column_title() 
     * exists - if it does, that method will be used. If it doesn't, this one will
     * be used. Generally, you should try to use custom column methods as much as 
     * possible. 
     * 
     * Since we have defined a column_title() method later on, this method doesn't
     * need to concern itself with any column with a name of 'title'. Instead, it
     * needs to handle everything else.
     * 
     * For more detailed insight into how columns are handled, take a look at 
     * WP_List_Table::single_row_columns()
     * 
     * @param array $item A singular item (one full row's worth of data)
     * @param array $column_name The name/slug of the column to be processed
     * @return string Text or HTML to be placed inside the column <td>
     **************************************************************************/
    function column_default($item, $column_name){
    	$url = "/wp-admin/post.php?action=edit&post=".$item[$column_name];
        switch($column_name){
            case 'lunchID':
            	global $Shopp;
            	$lunch = shopp_product($item['lunchID']);
            	$url = "/wp-admin/admin.php?page=shopp-products&id=".$item[$column_name];
            	return('<a title="'.$lunch->name.'" href="'.$url.'">'.$lunch->name.'</a><br /><span style="color:silver">'.$item['lunchType'].': '.$item['lunchID'].'</span>');
            	break;
            case 'lunchDate':
            	return ($item[$column_name]);
            	break;
            case 'lunchPrice':
            	$price = 'Price: <b>$'.number_format((float)$item[$column_name], 2, '.', '').'</b>';
            	if($item['lunchCommission']>0){$price.='<br /><span style="color:silver"></span>';}
            	return $price;
            	break;
            case 'schoolID':
            	$schoolName = get_the_title($item[$column_name]);
            	return '<a href="'.$url.'">'.$schoolName.'</a>';
            	break;
            case 'teacherID':
            	if($item[$column_name]==0){
	            	return '<span style="color:red;">Staff</span>';
            	}else{
	            	$teacherName = get_the_title($item[$column_name]);
	            	$grade = get_post_meta($item[$column_name], "grade", true);
	            	return '<a href="'.$url.'">'.$teacherName.'</a><br /><span style="color:silver">Grade: '.$grade.'</span>';
	            }
            case 'orderID':
            	$url="/wp-admin/admin.php?page=shopp-orders&id=".$item[$column_name];
            	return('<a href="'.$url.'">'.$item[$column_name].'</a>');
            case 'active':
                //return $item[$column_name];
                return sprintf(
		            '<input type="checkbox" name="%1$s[]" value="%2$s" disabled="disabled" %3$s />',
		            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
		            /*$2%s*/ $item['ID'],
		            $item[$column_name]==true ? 'checked="checked"' : ""               //The value of the checkbox should be the record's id
		         );
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }
    
        
    /** ************************************************************************
     * Recommended. This is a custom column method and is responsible for what
     * is rendered in any column with a name/slug of 'title'. Every time the class
     * needs to render a column, it first looks for a method named 
     * column_{$column_title} - if it exists, that method is run. If it doesn't
     * exist, column_default() is called instead.
     * 
     * This example also illustrates how to implement rollover actions. Actions
     * should be an associative array formatted as 'slug'=>'link html' - and you
     * will need to generate the URLs yourself. You could even ensure the links
     * 
     * 
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     **************************************************************************/
    
    function column_lunchRecipientID($item){
        // is student or staff?
        if($item['teacherID']==0){// staff
        	global $Shopp;
        	$customer = shopp_customer($item['lunchRecipientID'], 'id');
        	$entry = '<a style="color:red;" href="/wp-admin/admin.php?page=shopp-customers&id='.$item['lunchRecipientID'].'"><b>'.$customer->firstname .' '.$customer->lastname .'</b></a>';
        	 $info = "Staff: " .$item['lunchRecipientID'];
       }else{
	        $name = get_the_title($item['lunchRecipientID']);
	        $url = get_permalink($item['lunchRecipientID']);
	        $entry = '<a href="'.$url.'" >'.$name.'</a>';
	        $info = "Student: " .$item['lunchRecipientID'];
        }
        $actions = array(
//            'edit'      => sprintf('<a href="?page=%s&action=%s&report=%s">Edit</a>',$_REQUEST['page'],'edit',$item['id']),
			            'edit'      => sprintf('<a href="?page=slp-reports_form&action=%s&id=%s">Edit</a>','edit',$item['id']),

            'delete'    => sprintf('<a href="?page=%s&action=%s&id=%s">Delete</a>',$_REQUEST['page'],'delete',$item['id']),
        );
        
        //Return the title contents
        return sprintf('%1$s <br /><span style="color:silver">(%2$s)</span>%3$s',
            /*$1%s*/ $entry,
            /*$2%s*/ $info,
            /*$3%s*/ $this->row_actions($actions)
        );
    }
    
    /** ************************************************************************
     * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
     * is given special treatment when columns are processed. It ALWAYS needs to
     * have it's own method.
     * 
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     **************************************************************************/
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ $item['ID']                //The value of the checkbox should be the record's id
        );
    }
    
    
    /** ************************************************************************
     * REQUIRED! This method dictates the table's columns and titles. This should
     * return an array where the key is the column slug (and class) and the value 
     * is the column's title text. If you need a checkbox for bulk actions, refer
     * to the $columns array below.
     * 
     * The 'cb' column is treated differently than the rest. If including a checkbox
     * column in your table you must create a column_cb() method. If you don't need
     * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
     * 
     * @see WP_List_Table::::single_row_columns()
     * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
     **************************************************************************/

	function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
            'orderID'	=>	'Order',
            'lunchRecipientID'     => 'Recipient',
            'lunchID'    => 'Lunch',
            'lunchDate'  => 'Date',
            'lunchPrice' => 'Totals',
            'schoolID' => 'School',
            'teacherID' => 'Teacher',
            'active'	=> 	'Active'
        );
        return $columns;
    }

    
    /** ************************************************************************
     * Optional. If you want one or more columns to be sortable (ASC/DESC toggle), 
     * you will need to register it here. This should return an array where the 
     * key is the column that needs to be sortable, and the value is db column to 
     * sort by. Often, the key and value will be the same, but this is not always
     * the case (as the value is a column name from the database, not the list table).
     * 
     * This method merely defines which columns should be sortable and makes them
     * clickable - it does not handle the actual sorting. You still need to detect
     * the ORDERBY and ORDER querystring variables within prepare_items() and sort
     * your data accordingly (usually by modifying your query).
     * 
     * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
     **************************************************************************/
    function get_sortable_columns() {
        $sortable_columns = array(
            'lunchID'     => array('lunchID',true),     //true means its already sorted
            'schoolID'    => array('schoolID',false),
            'teacherID'  => array('teacherID',false),
            'orderID'  => array('orderID',false),
            'lunchDate'  => array('lunchDate',false)
        );
        return $sortable_columns;
    }
    
    
    /** ************************************************************************
     * Optional. If you need to include bulk actions in your list table, this is
     * the place to define them. Bulk actions are an associative array in the format
     * 'slug'=>'Visible Title'
     * 
     * If this method returns an empty value, no bulk action will be rendered. If
     * you specify any bulk actions, the bulk actions box will be rendered with
     * the table automatically on display().
     * 
     * Also note that list tables are not automatically wrapped in <form> elements,
     * so you will need to create those manually in order for bulk actions to function.
     * 
     * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
     **************************************************************************/
    function get_bulk_actions() {
        $actions = array(
        	'edit'		=> 	'Edit',
            'delete'    => 'Delete'
        );
        return $actions;
    }
    
    
    /** ************************************************************************
     * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
     * For this example package, we will handle it in the class to keep things
     * clean and organized.
     * 
     * @see $this->prepare_items()
     **************************************************************************/
    function process_bulk_action() {
        
        //Detect when a bulk action is being triggered...
        if( 'delete'===$this->current_action() ) {
            wp_die('Sorry; you cannot delete a report from this interface. The SLP Reports admin will have to delete via mySQL interface. This is to ensure no mistakes will be made.');
        }
       
    }
        
    
    /** ************************************************************************
     * REQUIRED! This is where you prepare your data for display. This method will
     * usually be used to query the database, sort and filter the data, and generally
     * get it ready to be displayed. At a minimum, we should set $this->items and
     * $this->set_pagination_args(), although the following properties and methods
     * are frequently interacted with here...
     * 
     * @uses $this->_column_headers
     * @uses $this->items
     * @uses $this->get_columns()
     * @uses $this->get_sortable_columns()
     * @uses $this->get_pagenum()
     * @uses $this->set_pagination_args()
     **************************************************************************/
    function prepare_items() {
        
        /**
         * First, lets decide how many records per page to show
         */
        $per_page = 10;
        
        
        /**
         * REQUIRED. Now we need to define our column headers. This includes a complete
         * array of columns to be displayed (slugs & titles), a list of columns
         * to keep hidden, and a list of columns that are sortable. Each of these
         * can be defined in another method (as we've done here) before being
         * used to build the value for our _column_headers property.
         */
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        
        /**
         * REQUIRED. Finally, we build an array to be used by the class for column 
         * headers. The $this->_column_headers property takes an array which contains
         * 3 other arrays. One for all columns, one for hidden columns, and one
         * for sortable columns.
         */
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        
        /**
         * Optional. You can handle your bulk actions however you see fit. In this
         * case, we'll handle them within our package just to keep things clean.
         */
        $this->process_bulk_action();
        
        
        /**
         * Instead of querying a database, we're going to fetch the example data
         * property we created for use in this plugin. This makes this example 
         * package slightly different than one you might build on your own. In 
         * this example, we'll be using array manipulation to sort and paginate 
         * our data. In a real-world implementation, you will probably want to 
         * use sort and pagination data to build a custom query instead, as you'll
         * be able to use your precisely-queried data immediately.
         */
         
         global $wpdb;
         $data = $wpdb->get_results("select * from ".$wpdb->prefix."slp_reports", ARRAY_A);
        //$data = $this->example_data;
                
        
        /**
         * This checks for sorting input and sorts the data in our array accordingly.
         * 
         * In a real-world situation involving a database, you would probably want 
         * to handle sorting by passing the 'orderby' and 'order' values directly 
         * to a custom query. The returned data will be pre-sorted, and this array
         * sorting technique would be unnecessary.
         */
      /*
  function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'title'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
        usort($data, 'usort_reorder');
*/
        
        
        /***********************************************************************
         * ---------------------------------------------------------------------
         * vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
         * 
         * In a real-world situation, this is where you would place your query.
         * 
         * ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
         * ---------------------------------------------------------------------
         **********************************************************************/
        
                
        /**
         * REQUIRED for pagination. Let's figure out what page the user is currently 
         * looking at. We'll need this later, so you should always include it in 
         * your own package classes.
         */
        $current_page = $this->get_pagenum();
        
        /**
         * REQUIRED for pagination. Let's check how many items are in our data array. 
         * In real-world use, this would be the total number of items in your database, 
         * without filtering. We'll need this later, so you should always include it 
         * in your own package classes.
         */
        $total_items = count($data);
        
        
        /**
         * The WP_List_Table class does not handle pagination for us, so we need
         * to ensure that the data is trimmed to only the current page. We can use
         * array_slice() to 
         */
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        
        
        
        /**
         * REQUIRED. Now we can add our *sorted* data to the items property, where 
         * it can be used by the rest of the class.
         */
        $this->items = $data;
        
        
        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }
    
}



	
	add_action('admin_menu', 'register_reports_page');
	
	function register_reports_page() {
	   add_submenu_page('school-lunch-program', 'School Lunch Reports', 'SLP Reports', 'add_users', 'slp-reports', 'render_school_reports_page');
	   add_submenu_page('school-lunch-program', '', '', 'add_users', 'slp-reports_form', 'custom_edit_report_form_handler');
	}







/***************************** RENDER TEST PAGE ********************************
 *******************************************************************************
 * This function renders the admin page and the example list table. Although it's
 * possible to call prepare_items() and display() from the constructor, there
 * are often times where you may need to include logic here between those steps,
 * so we've instead called those methods explicitly. It keeps things flexible, and
 * it's the way the list tables are used in the WordPress core.
 */
function render_school_reports_page(){

/*
global $Shopp;
krumo($Shopp->Flow->Admin->Pages);
*/
    
    //Create an instance of our package class...
    $reportsTable = new SLP_Reports_List_Table();
    //Fetch, prepare, sort, and filter our data...
    $reportsTable->prepare_items();
    
    ?>
    <style type="text/css">
    	.column-active {
		    vertical-align: middle;
		    width: 70px;
		}
		td .column-active{
			vertical-align: middle;
		}
		.widefat .column-lunchDate, .widefat .column-lunchPrice {
			width: 10%;
		}
		
		.widefat .column-lunchRecipientID{
			width: 10%;
		}
		.widefat .column-orderID {
		    padding: 11px 0 0;
		    vertical-align: top;
		    width: 75px;
		}

    </style>
    <div class="wrap">
        
        <div id="icon-edit-pages" class="icon32 icon32-posts-page"><br/></div>
        <h2>School Lunch Reports</h2>
        
                
        <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
        <form id="slp_reports-table" method="GET">
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
            <!-- Now we can render the completed list table -->
            <?php $reportsTable->display() ?>
        </form>
        
    </div>
    <?php
}


/***************************** MYSQL DB SETUP * ********************************
 *******************************************************************************
 * Checking to see if slp_report database exists, and if not, creates it
 studentID int(11) NOT NULL DEFAULT 0,
staffID int(11) NOT NULL DEFAULT 0,
 */
 function check_report_database(){
	 global $wpdb;
	 $tablename = $wpdb->prefix . "slp_reports";
	 if($wpdb->get_var("SHOW TABLES LIKE '$tablename'") != $tablename){
		 // need to create tablename
		 
		$sql = "CREATE TABLE $tablename (
		 	id int(11) NOT NULL AUTO_INCREMENT,
			lunchID int(11) NOT NULL,
			lunchPrice float(20,6) NOT NULL,
			lunchCommission float(20,6) NOT NULL DEFAULT 0,
			lunchQuantity int(11) NOT NULL,
			lunchType varchar(100) NOT NULL,
			lunchDate date,
			lunchRecipientID int(11) NOT NULL DEFAULT 0,
			schoolID int(11) NOT NULL,
			teacherID int(11) NOT NULL DEFAULT 0,
			orderID int(11) NOT NULL, 
			orderItem int(11) NOT NULL,
			active tinyint(1) NOT NULL DEFAULT 1,
			PRIMARY KEY (id)	 	
		);";

		 			 
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	 }
 }
 
 add_action('init', 'check_report_database');
 
 

 
 
 
 

 
 /**
 			below needs to be redone for above
 **/
 
 
 /**
 *			editing an slp report
 **/
   function custom_edit_report_form_handler(){
	    global $wpdb;
	    $tablename = $wpdb->prefix . "slp_reports";
	    $message = ''; 
	    $notice = '';
	    
	    $default = array(
	    	'id'				=>	0,
	    	'lunchID'			=>	'',
	    	'lunchPrice'		=>	'',
	    	'lunchQuantity'		=> 	'',
	    	'lunchType'			=>	'',
	    	'lunchDate'			=>	'',
	    	'lunchRecipientID'	=>	'',
	    	'schoolID'			=>	'',
	    	'teacherID'			=>	'',
	    	'orderID'			=>	'',
	    	'orderItem'			=>	'',
	    	'active'			=>	0,
	    	'lunchCommission'	=>	''
	    );
	    
	    if(wp_verify_nonce($_POST['slp_report-nonce'], 'slp_report-nonce')){
		    $report = shortcode_atts($default, $_REQUEST);
		    
		    // skipping validity for now
		    $result = $wpdb->update($tablename, $report, array('id'=>$report['id']));
		    if($result){
			    $message = __('Report #'. $report['id'] .' successfully updated', 'slp_reports');
		    }else{
			    $notice = __('There was an error while updating report #'.$report['id'], 'slp_reports');
		    }
	    }else{
		    // displaying the item,
		    $report = $default;
		  
		    if(isset($_REQUEST['id'])){
		    	$tmpID = $_REQUEST['id'];
			    $report = $wpdb-> get_row($wpdb->prepare("SELECT * FROM $tablename WHERE id = $tmpID"), ARRAY_A);
			    if(!$report){
				    $report = $default;
				    $notice = __('Report not found', 'slp_reports');
			   
			    }
		    }
	    }
	   
// adding custom metabox; seems strange that it's here though
		
		add_meta_box('slp_reports_form_meta_box', __('SLP Report'), 'slp_reports_form_meta_box_handler', 'slp_report', 'normal', 'default');	   
	
	    ?>
<div class="wrap">
    <div id="icon-edit-pages" class="icon32 icon32-posts-page"><br></div>
    <h2><?php _e('SLP Report', 'slp_reports')?> <a class="add-new-h2"
                                href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=slp_report');?>"><?php _e('back to list', 'slp_reports')?></a>
    </h2>

    <?php if (!empty($notice)): ?>
    <div id="notice" class="error"><p><?php echo $notice ?></p></div>
    <?php endif;?>
    <?php if (!empty($message)): ?>
    <div id="message" class="updated"><p><?php echo $message ?></p></div>
    <?php endif;?>

    <form id="form" method="POST">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('slp_report-nonce')?>"/>
        <?php /* NOTICE: here we storing id to determine will be item added or updated */ ?>
        <input type="hidden" name="id" value="<?php echo $report['id'] ?>"/>

        <div class="metabox-holder" id="poststuff">
            <div id="post-body">
                <div id="post-body-content">
                    <?php /* And here we call our custom meta box */ ?>
                    <?php do_meta_boxes('slp_report', 'normal', $report); ?>
                    <input type="submit" value="<?php _e('Save', 'slp_reports')?>" id="submit" class="button-primary" name="submit">
                </div>
            </div>
        </div>
    </form>
</div>
<?php   
	    
    }

 // the custom metabox form
 function slp_reports_form_meta_box_handler($report)
 {
 	// check on staff or student
 	// by checking if teacherID!=0  then lunchRecipientID is student else lunchRecipientID is customer
 	
    ?>

<table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
    <tbody>
    <tr class="form-field">
      	
       
        <td>
        	<?php echo do_shortcode('[slp_schools_select selected="'. $report['schoolID'].'"]'); ?>
        </td>
        <td>
        	<?php echo do_shortcode('[slp_reports_products date="'.$report['lunchDate'].'" type="'.$report['lunchType'].'" selected="'. $report['lunchID'].'"]'); ?>
        </td>
        
        <td>
        	<?php echo do_shortcode('[slp_teacher_select  selected="'. $report['teacherID'].'" '. ($report['teacherID'] =="0" ? 'disabled="disabled"' : "") .' schoolid="'. $report['schoolID'].'"]'); ?>
        </td>
       
    </tr>
  
    <tr>
    	<td>
<label class="howto" for="lunchPrice"><span>Lunch Price:</span><br>
<input id="lunchPrice" name="lunchPrice" type="text" style="width: 95%" value="<?php echo esc_attr($report['lunchPrice'])?>" size="50" class="code"  required>
</label>
    	</td>
    	<td>
<label class="howto" for="lunchCommission"><span>Lunch Commission:</span><br>
<input id="lunchCommission" name="lunchCommission" type="text" style="width: 95%" value="<?php echo esc_attr($report['lunchCommission'])?>" size="50" class="code"  required>
</label>
    	</td>
    	<td>
<label class="howto" for="lunchQuantity"><span>Lunch Quantity:</span><br>
<input id="lunchQuantity" name="lunchQuantity" type="text" style="width: 95%" value="<?php echo esc_attr($report['lunchQuantity'])?>" size="50" class="code"  required>
</label>
    	</td>
    </tr>
    
    <tr>
<td>
<label class="howto" for="lunchRecipientID"><span>Lunch Recipient ID:</span><br>
<input id="lunchRecipientID" name="lunchRecipientID" type="text" style="width: 95%" value="<?php echo esc_attr($report['lunchRecipientID'])?>" size="50" class="code"  required>
</label>
    	</td>  
<td>
<label class="howto" for="lunchDate"><span>Lunch Date:</span><br>
<input id="lunchDate" name="lunchDate" type="text" style="width: 95%" value="<?php echo esc_attr($report['lunchDate'])?>" size="50" class="code"  required>
</label>
    	</td>  

<td>
<label class="howto" for="lunchType"><span>Lunch Type:</span><br>
<input id="lunchType" name="lunchType" type="text" style="width: 95%" value="<?php echo esc_attr($report['lunchType'])?>" size="50" class="code"  required>
</label>
    	</td>  
    </tr>
 
   <tr>
<td>
<label class="howto" for="active"><span>Active</span><br>
<input id="active" name="active" type="text" style="width: 95%" value="<?php echo esc_attr($report['active'])?>" size="50" class="code"  required>
</label>
    	</td>
   </tr>
   
    </tbody>
</table>
<?php

 
 
	 
 }

//  function custom_menu_page(){
//    echo "Admin Page Test";  
// }

// add_action('admin_menu', 'register_custom_menu_page');

// function register_custom_menu_page() {
//    add_menu_page('custom menu title', 'custom menu', 'add_users', 'custompage', 'custom_menu_page', plugins_url('myplugin/images/icon.png'), 6);
// }
 
 

?>