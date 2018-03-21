function showSnackbar(message, length){
	if(length == "long"){
		length = 5000
	}else if(length == "short"){
		length = 3000
	}else{
		length = 5000;
	}
	var x = document.getElementById("snackbar");
	x.className = "show";
	x.innerHTML = message;
	setTimeout(function(){
		x.className = x.className.replace("show");
	}, length);
}
