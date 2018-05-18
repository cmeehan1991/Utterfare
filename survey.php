<?php include('header.php'); mainHeader();?>

<div class="container-fluid">
	<div class='row'>
		<div class="col-md-12 mx-auto">
			<h1 style="text-align: center">Survey</h1>
		</div>
	</div>
	<div class="row">
		<div class="col-md-3 mx-auto">
			<h2 class="message"></h2>
		</div>
	</div>
	<div class="row">
		<div class="col-md-3 mx-auto">
			<form class="survey-form" onsubmit="return submitSurvey();">
				<div class="row">
					<div class="col">
						<div class="referral">
							<label for="referral">How did you hear about Utterfare?
							<select name="referral">
								<option value="Hilton Head Guest Services">Hilton Head Guest Services</option>
								<option value="Facebook">Facebook</option>
								<option value="Instagram">Instagram</option>
								<option value="Friend/Family">Friend/Family</option>
								<option value="Other">Other</option>
							</select>
							</label>
						</div>
					</div>
				</div>
				<div class="row">
					<div class='col'> 
						<div class="explain" style="display:none;">
							<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
								<input class="mdl-textfield__input" type="text" id="explain" name="explain">
								<label class="mdl-textfield__label" for="explain">If other, please explain.</label>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col">
						<button class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored" type="submit" >Submit</button>
					</div>
				</div>
		    </form>
		</div>
	</div>
</div>

