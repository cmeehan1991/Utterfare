$(document).ready(function(){
	$('.explain').hide();
	$('select[name=referral]').select2({
		width: 'fit-content'
	});
	$('select[name=referral]').on('change',function(){
		var selectedValue = $(this).val();
		if(selectedValue === 'Other'){
			$('.explain').fadeIn(250);
		}else{
			$('.explain').hide();
		}
	})
});

function submitSurvey(){
	var data = $('.survey-form').serialize();
	data += "&action=new_user_survey";
	console.log(data);
	$.ajax({
		data: data, 
		url: 'includes/php/UserData.php',
		method: 'post',
		success:function(response){ 
			$('.survey-form').fadeOut('fast', function(){
				$('.message').text('Thank you for taking this survey.');
				$('.message').fadeIn();
			}); 
		},
		error:function(error){
			console.log(error);
		}
	});
	
	return false;
}