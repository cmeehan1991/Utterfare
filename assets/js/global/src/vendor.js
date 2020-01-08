function getVendorStatus(){
	console.log(session);
	
	if(session.length !== 0){
		if(session.UF_VENDOR_ID !== undefined){
			return true;
		}else{
			return false;
		}
	}
	
}

function vendorSignIn(){
	var data = $('form[name="vendorSignInForm"]').serialize();
	data += "&" + "action=vendorSignIn";
	
	var success = false;
	
	$.post(vendor_url, data, function(response){
		success = response.success;
	}, 'json')
	.done(function(){
		
		if(success === true){
			
		}else{
			notifyUser();
		}
	});
}