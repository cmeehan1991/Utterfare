$(document).ready(function(){
	
});

function getCompanyInformation(){

}

function updateCompanyInformation(){
	var data = "action=updateCompanyInformation&"; 
	data += $("form[name='companyInformation']").serialize();
	$.ajax({
		data: data,
		method: 'post', 
		url: 'includes/php/CompanyInformation.php',  
		success:function(result){
			console.log(result);
			if(result == true){
				showSnackbar("Company Information Updated", "long");
			}else{
				showSnackbar("Error: Company Information Was Not Updated", "short");
			}
		}, 
		error: function(error){
			showSnackbar("Error: Company Information Was Not Updated", "short");
		}
	});
	
	return false;
}

function updateProfilePicture(){
	var image = $('.profile-picture__upload').prop('files')[0];
	//setProfilePreview();
	var data = new FormData();
	data.append("action", "updateProfilePicture");
	data.append("picture", image);
	console.log(image);
	$.ajax({
		data: data,
		type: 'post', 
		url: 'includes/php/CompanyInformation.php', 
        cache: false,
        contentType: false,
        processData: false,
		success:function(data){
			$('.profile-picture').attr('src', data + "?ver=" + String(Math.random()));
			$(".profile-picture").css('background-color', 'transparent');
			$('.profile-picture__upload').val('');
			showSnackbar('Image successfully uploaded.', 'long');
		}, error: function(error){
			showSnackbar('Error uploading image', 'long');
		}
	});
	return false;
}