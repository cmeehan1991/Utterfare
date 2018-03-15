$(document).ready(function(){
	if(document.getElementsByClassName("registrationForm")){
		var numPages = $('.registrationForm>fieldset').length;	
		setRegistrationPages(numPages);
	}
	
	$('#contactInformation').hide();
	$('#companyInformation').hide();
	
	$('.next--contact-information').hide();
	$('.mdl-textfield__input[name=password]').keyup(function(){
		if($(this).val().length >= 8){
			$(this).css('outline', '2px solid green');
			$(this).css('border-bottom', '2px solid green');
		}else{
			$(this).css('outline', '2px solid red');
			$(this).css('border-bottom', '2px solid red');
		}
	});
	
	$('.mdl-textfield__input[name=confirm_password]').keyup(function(){
		
		var confirmPassword = $(this).val();
		var password = $('.mdl-textfield__input[name=password]').val();
		if(confirmPassword === password){
			$('.next--contact-information').fadeIn('fast');
			$(this).css('outline', '2px solid green');
			$(this).css('border-bottom', '2px solid green');
		}else{
			$('.next--contact-information').fadeOut('fast');
			$(this).css('outline', '2px solid red');
			$(this).css('border-bottom', '2px solid red');
		}
	});
	
	
	$('.page-count--page-number>a').click(function(e){
		e.preventDefault();
		$(this).parent().addClass('active');		
		var goToPage = $(this).attr('href');
		var goToPageId = $(goToPage).attr('id');
		$('.page-count--page-number').removeClass('active');
		$(this).parent().addClass('active');	
		
		$(goToPage).attr('active', 'true');
		$('fieldset').each(function(){
			if($(this).attr('id') !== $(goToPage).attr('id')){
				$(this).attr('active', 'false');	
			}
			
			if($(this).attr('active') == 'true'){
				$(this).fadeIn('slow');
			}else{
				$(this).hide();
			}
		});
	});
	
	
	$('.registrationForm button').click(function(){
		
		var navigateTo = $(this).data('page');			
		var currentPage = null;
		var selectedPage = null;
		$('.registrationForm>fieldset').each(function(){
			var page = $(this).attr('id');
			if($("#" + page).is(":visible")){
				currentPage = page;
			}
		});
		if(navigateTo === 'next'){
			selectedPage = $('fieldset:visible').next().attr('data-page');
			var nextPage = $('#' + currentPage).next()
			$("#" + currentPage).hide();
			$(nextPage[0]).fadeIn('slow');
			console.log(nextPage);
			$('.page-count--page-number').removeClass('active');
			$('.page-count--page-number[data-page=' + selectedPage + ']').addClass('active');


		}else{
			selectedPage = $('fieldset:visible').prev().attr('data-page');
			var prevPage = $('#' + currentPage).prev();
			$("#" + currentPage).hide();
			$(prevPage[0]).fadeIn('slow');
			$('.page-count--page-number').removeClass('active');
			$('.page-count--page-number[data-page=' + selectedPage + ']').addClass('active');
		}
	});
});


function setRegistrationPages(numPages){
	for(i = 1; i <= numPages; i++){
		var page = $('fieldset[data-page=' + i + ']');
		var goToPage = $(page).attr('id');
		$('.page-count').append("<div class='page-count--page-number page-" + i +"' data-page='"+i+"'><a class='registrationPageLink' href='#" + goToPage + "'>" + i + "</div>");
		if(i > 1 && i < 3){
			$('.page-count--page-number').append("<span class='page-count--page-separator'></span>");	
		}
	}
	var activePage = location.search;
	if($('#contactInformation').attr("active") == "true"){
		$(".page-2").addClass("active");
	}else if($('#companyInformation').attr("active") == "true"){
		console.log('page 3 active');
		var pageNumber = $('.page-count--page-number').data('page', 3);
		$(".page-3").addClass("active");
	}else{
		var pageNumber = $('.page-count--page-number').data('page', 1);
		$('.page-1').addClass("active");
	}
	
	
}



function validatePassword() {
    var pwdInput = $("input[name='password']");
    var cnfPwdIpt = $("input[name='confirm_password']");
    var submitButton = $("button[type='submit']");
    if (pwdInput.val().length < 4) {
        $(pwdInput).css('outline', '1px solid red');
    } else {
        $(pwdInput).css('outline', '1px solid green');
        if (pwdInput.val() !== cnfPwdIpt.val()) {
            $(cnfPwdIpt).css('outline', '1px solid red');
        } else {
            $(cnfPwdIpt).css('outline', '1px solid green');
            $(submitButton).attr('disabled', false);
        }
    }

}

function registerCompany() {
    var data = "action=registerNewVendor";
    data += "&" + $('form[name="registrationform"]').serialize();
	$.ajax({
        data: data,
        url: "includes/php/VendorRegistration.php",
        method: "post",
        success: function (response) {
            if (response === "success") {
                window.location.href = "userHome.php";
            } else if (response === "exists") {
                window.location.href = "userHome.php";
            } else {
                console.log("Response: " + response);
            }
        }
    });
    return false;
}


function validateUsername(username){
	var unameInput = $('input[name="username"]');
	var submitButton = $("button[type='submit']");
	
	var data = {
		action: 'validateUsername',
		'username': username,
	}
	
	$.ajax({
		data:data, 
		url: "includes/php/VendorRegistration.php",
		method: 'post', 
		success:function(result){
			if(result == false){ 
				$(unameInput).css('outline', '1px solid red');
				$(submitButton).attr('disabled', true);				
			}else{
				$(unameInput).css('outline', '1px solid green');
				$(submitButton).attr('disabled', false);
			}
		}
	})
}


