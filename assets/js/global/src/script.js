$(document).ready(function(){	

	$('.uf-textfield__input').on('focusin', function(){
		$(this).parent().addClass('is-focused');
	});
	
	$('.uf-textfield__input').on('focusout', function(){
		if($(this).val() == null || $(this).val().trim() == ""){
			$(this).parent().removeClass('is-focused');
		}
	});

});

function sendEmail(){
	var data =$('.contact-form').serialize();
	$.ajax({
		url: 'includes/php/contact.php',
		data: data,
		method: 'post',
		success:function(data){
			console.log(data);
			$('.contact-form').hide();
			$('.confirmation').html('<h2 class="confirmation-message">Your email has been received. We will respond within 72 hours.</h2>')
			setTimeout(function(){
				$('.confirmation').hide();
				$('.contact-form').show();
			}, 2000);
		},
		error: function(error){
			$('.confirmation').html('<h2 class="error-message">There was an error sending your message. Please try again.</h2>')
			
		}
	});
	return false;	
}


