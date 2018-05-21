$(document).ready(function(){
	if(document.getElementsByClassName("registrationForm")){
		var numPages = $('.registrationForm>fieldset').length;	
		setRegistrationPages(numPages);
	}
	
	// Hide the company and contact information when the document is ready
	$('#contactInformation').hide();
	$('#companyInformation').hide();
	
	// Select2 select boxes
	$("select[name='state']").select2({width:'100%', placeholder: "Select a state", allowClear: false});
	$("select[name='country']").select2({width:'100%', placeholder: "Select a country"});
	$("select[name='web-prefix']").select2({width:'fit-content'});
	$("select[name='role']").select2({width:'100%', placeholder: "Role", alowClear: false});
	
	$('input[name=phone]').keydown(function(){
		$(this).val($(this).val().replace(/[^0-9\(\)\-]/g, '')); 
	});
	
	$('input[name=phone]').keyup(function(){
		$(this).val($(this).val().replace(/^(\d{3})(\d{3})(\d)+$/, "($1)$2-$3"));
	});
	
	navigationButtons();
	goToPage();
	pages();
	keylisteners();		
});

function recaptchaChecked(){
	$('.next--submit-form').fadeIn('fast');
}

function navigationButtons(){
	// Hide the next buttons
	$('.next--contact-information').hide();
	$('.next--company-information').hide();
	$('.next--submit-form').hide();
	
	$('.registrationPageLink').click(function(event){
		event.preventDefault();
	});
}

function goToPage(){
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
		if(navigateTo === 'confirm'){
			$('.registrationForm').hide();
			$('.loader>i').after('<h2>Loading...</h2>');
			$('.loader').show();
			registerCompany();
			return false;
		}else if(navigateTo === 'next'){
			selectedPage = $('fieldset:visible').next().attr('data-page');
			var nextPage = $('#' + currentPage).next()
			$("#" + currentPage).hide();
			$(nextPage[0]).fadeIn('slow');

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
}

function pages(){
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

}

function keylisteners(){	
	console.log($('form[name=registrationform]').index());
	if($('form[name=registrationform]').index() > -1){
		
		$('input[name=username]').keyup(function(){
			var username = $(this).val();
			validateUsername(username);
			console.log('isValid');
		});	
		
		$('.mdl-textfield__input[name=password]').keyup(function(){
			// Make sure the password is the proper length
			if($(this).val().length >= 8){
				$(this).css('outline', '2px solid green');
				$(this).css('border-bottom', '2px solid green');
			}else{
				$(this).css('outline', '2px solid red');
				$(this).css('border-bottom', '2px solid red');
			}
		});
		
		$('.mdl-textfield__input[name=confirm_password]').keyup(function(){
			// Confirm the passwords match. 
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
	
	
		// Company Information
		$('input[name=company_name]').keyup(function(){
			var company_name = $(this).val();
			if(company_name.length > 0){
				$('.next--company-information').fadeIn('fast');
			}else{
				
				$('.next--company-information').hide();
			}
		});
	}
}

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

function registrationNotification(email){
	var data = "action=registrationNotification";
	data += "&email=" + email;
	
	$.ajax({
		data:data,
		url: "includes/php/VendorRegistration.php",
		method: 'post',
		success:function(response){
			if(!response){
				$('.next--contact-information').is(":disabled");
				$('input[name=username]').css('outline', 'red solid 1px');
			}else{
				$('.next--contact-information').is(":enabled");
				$('input[name=username]').css('outline', 'green solid 1px');
			}
		},
		error: function(error){
			console.log(error);
		}
	});
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
	});
}


