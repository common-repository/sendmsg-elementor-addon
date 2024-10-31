<?php
/*
Misc Functions
*/

function sendmsgs_perform_remote($endpoint,$body){
	 
	$body = wp_json_encode( $body );
	 
	$options = [
	    'body'        => $body,
	    'headers'     => [
	        'Content-Type' => 'application/json',
	    ],
	    'timeout'     => 60,
	    'redirection' => 5,
	    'blocking'    => true,
	    'httpversion' => '1.0',
	    'sslverify'   => false,
	    'data_format' => 'body',
	];
	 
	$response = wp_remote_post( $endpoint, $options );

	if ( is_wp_error( $response ) ) {
	    $error_message = $response->get_error_message();
	    die();
	}

	$response = wp_remote_retrieve_body($response);
	return $response;
}

function sendmsgs_perform_remote_auth($endpoint,$token,$body){
	 
	$body = wp_json_encode( $body );
	 
	$options = [
	    'body'        => $body,
	    'headers'     => [
	        'Content-Type' => 'application/json',
	        'Authorization' => $token
	    ],
	    'timeout'     => 60,
	    'redirection' => 5,
	    'blocking'    => true,
	    'httpversion' => '1.0',
	    'sslverify'   => false,
	    'data_format' => 'body',
	];
	 
	$response = wp_remote_post( $endpoint, $options );

	if ( is_wp_error( $response ) ) {
	    $error_message = $response->get_error_message();
	    die();
	}

	$response = wp_remote_retrieve_body($response);
	return $response;
}

function sendmsgs_get_lists(){
	sendmsg_refresh_token();
	$sendmsg_api_options = get_option( 'sendmsg_api_option_name' ); 
	$send_msg_token = $sendmsg_api_options['send_msg_token'];
	if(!empty($send_msg_token)){
		$listdata = array();

		$response = sendmsgs_perform_remote_auth('https://gconvertrest.sendmsg.co.il/api/Sendmsg/GetMailingListNames',$send_msg_token,'');

		$response = json_decode($response);
		foreach ($response->listNames as $list_single) {
			$listdata[$list_single->ExistingListID] =  $list_single->NewListName;
		}
		return $listdata;
	}
}

/** Delete User From The List **/
function sendmsgs_delete_user_from_lists($email,$phone,$lists){
	sendmsg_refresh_token();
	$sendmsg_api_options = get_option( 'sendmsg_api_option_name' ); 
	$send_msg_token = $sendmsg_api_options['send_msg_token'];
	if(!empty($send_msg_token)){

		$list_data = array();

		foreach ($lists as $singelist) {
			$list_data[]['ExistingListID'] = $singelist;
		}
		
		if(!empty($email)){
			$bodydata = [
			   "users" => [
			         [
			            "EmailAddress" => $email
			         ] 
			      ], 
			   "mailingLists" => $list_data
			]; 
			$response = sendmsgs_perform_remote_auth('https://gconvertrest.sendmsg.co.il/api/Sendmsg/RemoveFromMailingLists/ManyToMany',$send_msg_token,$bodydata);
		}
		if(!empty($phone)){
			$bodydata = [
			   "users" => [
			         [
			            "Cellphone" => $phone
			         ] 
			      ], 
			   "mailingLists" => $list_data
			]; 
			$response = sendmsgs_perform_remote_auth('https://gconvertrest.sendmsg.co.il/api/Sendmsg/RemoveFromMailingLists/ManyToMany',$send_msg_token,$bodydata);
		}
		//print_r($response);

	}
}

function sendmsgs_get_custom_fields(){
	sendmsg_refresh_token();
	$sendmsg_api_options = get_option( 'sendmsg_api_option_name' ); 
	$send_msg_token = $sendmsg_api_options['send_msg_token'];

	$response = json_decode(sendmsgs_perform_remote_auth('https://gconvertrest.sendmsg.co.il/api/Sendmsg/GetAllFields',$send_msg_token,''));

	$total_custom_field = array();

	foreach ($response->AllFields as $single_key => $single_label) {
		$total_custom_field[$single_key] = $single_label;
	}

	return $total_custom_field;
}


function sendmsgs_submit_data($custom_fields,$email_phone,$list_id){
	sendmsg_refresh_token();

	$sendmsg_api_options = get_option( 'sendmsg_api_option_name' ); 
	$send_msg_token = $sendmsg_api_options['send_msg_token'];

	 $sub_data = [
	   [
	         "EmailAddress" => $email_phone['eMail'], 
	         "Cellphone" => $email_phone['cellPhone'], 
	         "userSystemFields" => $custom_fields
	      ] 
	]; 

	$response = json_decode(sendmsgs_perform_remote_auth("https://gconvertrest.sendmsg.co.il/api/Sendmsg/AddUsersOnly",$send_msg_token,$sub_data));

	$result_data = explode("\n", $response->result->ResultMessage);

	$user_id = str_replace(" added IDs: ", "", $result_data[1]);
	$list_data = array();

	foreach ($list_id as $singelist) {
		$list_data[]['ExistingListID'] = $singelist;
	}

	$user_pr_data = [
	   "users" => [
	         [
	            "UserID" => $user_id
	         ] 
	      ], 
	   "mailingLists" => $list_data
	]; 
 
	$response = json_decode(sendmsgs_perform_remote_auth("https://gconvertrest.sendmsg.co.il/api/Sendmsg/AddUsersToLists",$send_msg_token,$user_pr_data));

	if($response->success){

		return array('success'=>'added');

	}
	
}

function sendmsg_refresh_token(){

	    $sendmsg_api_options = get_option( 'sendmsg_api_option_name' ); // Array of All Options
	    $site_id = $sendmsg_api_options['site_id_0']; // Site ID
	    $password = $sendmsg_api_options['api_key_1']; // API Key

		$api_data = array(
			"siteID" => $site_id,
			"password" => $password
		);

		$response = json_decode(sendmsgs_perform_remote("https://gconvertrest.sendmsg.co.il/api/Sendmsg/token",$api_data));

		if(!empty($response->Token)){
			$result = array('type'=> 'success', 'token' => $response->Token);
		}
		else{
			$result = array('type'=> 'error', 'message' => $response->Message);
		}

		$sendmsg_api_option_name = array('site_id_0'=>$site_id,"api_key_1"=>$password,'send_msg_token'=>$response->Token);

		update_option('sendmsg_api_option_name',$sendmsg_api_option_name);
}

// Change date format to work with the API

add_action('wp_footer', function () {
       ?>
      <script>
      jQuery( document ).ready( function( $ ){
            setTimeout( function(){
                  $('.flatpickr-input').each(function(){ flatpickr( $(this)[0] ).set('dateFormat', 'd/m/Y');});
            }, 1000 );
      });
      </script>
      <?php
},9999);


