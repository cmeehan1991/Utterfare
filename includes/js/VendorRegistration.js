$(document).ready(function(){
    $("button[type='submit']").attr('disabled', true);
});

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
    var data = $('form[name="registrationform"]').serialize();
    data += "&action=registerNewVendor";
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
                console.log(response);
            }
        }
    });
    return false;
}

