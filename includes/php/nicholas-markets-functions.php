<?php /**
	SHOPP FUNCTIONS
**/

if(!(function_exists('nm_customer_types_filter'))){
	function nm_customer_types_filter($types){
		//$arr = array('Parent', 'Staff');
		 $types[] = 'Staff';
		 return $types;
	}
}
add_filter('shopp_customer_types', 'nm_customer_types_filter');



add_filter('shopp_customer_info_input', 'shopp_customer_info_input_filter');

// create filter function
function shopp_customer_info_input_filter( $input ) {
    // usually of the form 
    ?>
    <input type="text" name="info[id]" id="info-id" value="the value of the info" />

    // .. filter the $input here
   <?php
}


/**
	checking customer type
**/
if(!(function_exists('getCustomerType'))){
	function getCustomerType($customer) {
		if (is_a($customer, 'Customer') === false)
			$customer = shopp_customer((int) $customer);
	
		if ($customer === false)
			return 'Non-existent';
	
		switch ($customer->type) {
			case '':
			case 'Retail':
				return 'Retail';
			break;
	
			case 'Wholesale':
				return 'Wholesale';
				break;
			case 'Referral':
				return 'Referral';
				break;
			case 'Tax-Exempt':
				return 'Tax-Exampt';
				break;
			case 'Staff':
				return 'Staff';
				break;
			break;
	
			default:
				return 'Unknown';
			break;
		}
	}
}




/********************* SAVING TO REPORTS UPON PURCHASE *************************
 *******************************************************************************
*	takes items and saves to report db upon purchase
*/
function slp_order_processing ($Purchase) {
	global $Shopp;  
	
	
	$data = $Purchase->data;
	if($data['Order']=="School Lunch Program"){
	
	$orderID = $Purchase->id;	
	
	$recipientID = $data['Recipient ID'];
	$schoolID = $data['School ID'];
	$teacherID = $data["Teacher ID"];
	// schoolPrice based on $schoolID; commission etc
	

	foreach($Purchase->purchased as $item){
		$lunchId = $item->product;
		$lunchName = $item->name;
		$lunchPrice = $item->total;
		$lunchQuantity = $item->quantity;
		$lunchDate = $item->data['Date'];   
		$lunchType = $item->data['Type'];
		$orderItem = $item->id;
		$lunchCommission = get_post_meta();
	
		global $wpdb;
		
$wpdb->insert($wpdb->prefix .'slp_reports', array(
			'lunchID'=>$lunchId,
			'lunchPrice'=>$lunchPrice,
			'lunchQuantity'=>$lunchQuantity,
			'lunchDate'=>$lunchDate,     
			'lunchType'=>$lunchType,
			'lunchRecipientID'=>$recipientID,
			'schoolId'=>$schoolID,
			'teacherID'=>$teacherID,
			'orderID'=>$orderID,
			'orderItem'=>$orderItem,
			'active'=>true));

		/** database
		lunchID, lunchPrice, lunchQuantity, lunchType, lunchDate, lunchRecipientID, schoolID, teacherID, orderId, orderItem(line?), active
		**/
		
		}
	}

}   

add_action("shopp_order_success", "slp_order_processing", 11);

add_filter('shopp_purchase_order_authorizenet_processing',create_function('','return "sale";'));
?>