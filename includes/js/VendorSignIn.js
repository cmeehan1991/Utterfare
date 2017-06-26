var loginAttempt = 0;

function validateForm(){
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
        signIn();
    }
    return false;
}

function signIn(){
    var data = $('form').serialize();
    $.ajax({
        url:"includes/php/UserLogIn.php",
        data:data,
        method:"post",
        success:function(results){
            if(results === "success"){
                window.location.href="userHome.php";
            }else{
	            console.log(results);
            }
        }
    });
}