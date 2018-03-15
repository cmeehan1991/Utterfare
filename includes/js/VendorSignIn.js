var loginAttempt = 0;

function validateLoginForm(){
	console.log('validatingForm');
    var username = $("input[name='username']").val();
    var password = $("input[name='password']").val();
    
    var loginFormValid = new Array();
    
    if(username === "" || username === null || username.length < 5){
        loginFormValid["username"] = false;
    }else{
        loginFormValid["username"] = true;
    }
    
    if(password === "" || password === null || password.length < 8){
        loginFormValid["password"] = false;
    }else{
        loginFormValid["password"] = true;
    }
    
    if(loginFormValid["username"] === false || loginFormValid["password"] === false){
       $("input[name='username']").css("outline","1px solid red");
       $("input[name='username']").css("border","1px solid red");
       $("label[for='username']").css("color","red");
       $("input[name='password']").css("outline","1px solid red");
       $("input[name='password']").css("border","1px solid red");
       $("label[for='password']").css("color","red");
    }else{
	    console.log('signing in');
        signIn();
    }
    return false;
}

function signIn(){
    var data = $('form[name="loginForm"]').serialize();
    console.log(data);
    $.ajax({
        url:"includes/php/UserLogIn.php",
        data:data,
        method:"post",
        success:function(results){
	        console.log(results);
            if(results === "success"){
                window.location.href="https://www.utterfare.com/userHome.php";
            }else{
	            console.log("Results: " + results);
            }
        }, 
        error:function(error){
	        console.log('error');
        }
    });
}