jQuery(document).ready(function($){
    
    $("a.connect-now").click(function(){
    	$(".loading-icon").show();
	    $.ajax({
	        url: sendmsg_ajax_obj.ajaxurl,
	        method : 'POST',
	        dataType: 'json',
	        data: {
	            'action': 'sendmsg_api_auth_ajax_request',
	            'siteid' : $("input#site_id_0").val(),
	            'pass' : $("input#api_key_1").val(),
	            'nonce' : sendmsg_ajax_obj.nonce
	        },
	        success:function(data) {
	            // This outputs the result of the ajax request
	            if(data.type == "error"){
	            	$(".sendmsg-notification").html('<div class="error_notice">'+ data.message +'</div>');
	            	$("input#send_msg_token").val("");
	            }
	            else{
	            	$(".sendmsg-notification").html('<div class="sucess_notice">Connected successfully.</div>');
	            	$("input#send_msg_token").val(data.token);
	            	$("form.send-msg-form input#submit").trigger("click");
	            }


	            $(".loading-icon").hide();
	        },
	        error: function(errorThrown){
	            console.log(errorThrown);
	        }
	    });  
    });

});