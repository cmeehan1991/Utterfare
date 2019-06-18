function userSignOut(){
	
	var data = {
		'action': 'signout'
	}
	
	console.log(data);
	
	$.post(window.user_url, data, function(response){
		console.log(response);
	}).done(function(){
		console.log("reload");
		window.location.reload();
	});
}

function userSignIn(username, password){
	
	var data = {
		'username': username, 
		'password': password,
		'action': 'sign_in'
	}
		
	var userData = {};
	$.post(window.user_url, data, function(response){
		
		if(response.success !== true){
			$('.signInErrorMessage').show();
		}else{
			userData.success = response.success;
		}
	},"json")
	.done(function(){
		console.log(userData);
		if(userData.success === true){
			if(window.location.href.indexOf('sign-up') > 0){
				window.location.href = './'
			}
			window.location.reload();
			
			$('#signInModal').modal('hide');
		}
	})
	.fail(function(fail){
		console.log("fail");
		console.log(fail);
	});
}

function insertNewUser(data){
	data.action = "new_user";
	
	var success;
	
	$.post(window.user_url, data, function(response){
		console.log(response);
		success = response.success;
	},'json')
	.done(function(){
		if(success === true){
			userSignIn(data.username, data.password);			
		}else{
			
		}
	})
	.fail(function(fail){
		console.log("fail");
		console.log(fail);
	});
}

function updateUserInformation(){
	var data = $('.userInformationForm').serialize();
	data += "&action=sign_in";
	
	$.post(window.user_url, data, function(response){
		console.log(response);
	})
	.done(function(){
		
	})
	.fail(function(fail){
		console.log("fail");
		console.log(fail);
	});
}

/*
* Get the current user's data and fill out the page. 
function getUserData(){
	
}