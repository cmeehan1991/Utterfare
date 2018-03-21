<?php
session_start();
include 'header.php'; 
mainHeader(); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-md-4 mx-auto"> 
            <form name="loginForm" onsubmit="return validateLoginForm()">
                <div class="row">
                    <div class="col-md-6 mx-auto">
                        <h2>Utterfare Sign In</h2>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mx-auto">
	                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
	                        <label class="mdl-textfield__label" for="username">Username</label>
	                        <input class="mdl-textfield__input" type="text" name="username" />
	                    </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mx-auto">
                        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
	                        <label class="mdl-textfield__label" for="password">Password</label>
	                        <input class="mdl-textfield__input" type="password" name="password" />
	                    </div>

                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mx-auto" style="text-align: center">
                        <button class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored mdl-js-ripple-effect" type="submit">Sign In</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row" style="text-align: center">
        <div class="col-xs-12 col-md-4 mx-auto">
            <a class="mdl-link mdl-link--light" href="registration.php">Register</a>
        </div>
    </div>
</div>
</body>
</html>