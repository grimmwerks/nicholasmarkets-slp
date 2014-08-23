var $j = jQuery.noConflict();

$j(document).ready(function(){
	// need to make sure php $schoolID is not set before dealing with this
	
	$j("#schoolID").change(function(){
		if($j(this).attr('value')){
			//alert('studentID is ' + $j(this).attr('value'));
			//$j("#spanteacher").html("");
			//alert($j('#spanteacher').html());
		}
	});
	
	
	
	
});