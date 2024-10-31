<?php
/*
Test Authentication
*/
function sendmsg_api_auth_ajax_request() {

	check_ajax_referer( 'sendmsg_ajax_nonce', 'nonce' );

	$site_id = sanitize_text_field($_POST['siteid']);
	$password = sanitize_text_field($_POST['pass']);

	$api_data = array(
		"siteID" => $site_id,
		"password" => $password
	);

	$result = array();
 
    // The $_REQUEST contains all the data sent via ajax
    if ( isset($_REQUEST) ) {

		$response = json_decode(sendmsgs_perform_remote("https://gconvertrest.sendmsg.co.il/api/Sendmsg/token",$api_data));

		if(!empty($response->Token)){
			$result = array('type'=> 'success', 'token' => $response->Token);
		}
		else{
			$result = array('type'=> 'error', 'message' => $response->Message);
		}

		echo json_encode($result);
		     
    }
     
    // Always die in functions echoing ajax content
   die();
}
 
add_action( 'wp_ajax_sendmsg_api_auth_ajax_request', 'sendmsg_api_auth_ajax_request' );
 
// If you wanted to also use the function for non-logged in users (in a theme for example)
add_action( 'wp_ajax_nopriv_sendmsg_api_auth_ajax_request', 'sendmsg_api_auth_ajax_request' );