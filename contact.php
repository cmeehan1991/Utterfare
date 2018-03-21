<?php 
include('header.php'); 
echo mainHeader();
?>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-6 col-md-offset-6">
			<h1>Contact Us</h1>
		</div>
	</div>
	<div class="row">
		<div class="col-md-3 col-md-offset-3">
			<div class="confirmation"></div>
			<form name="contact-form" class="contact-form" onsubmit="return sendEmail()">
				<div class="row">
					<label for="name">Name:*</label><br/>
					<input type="text" name="name" class="wide-fat" required/>
				</div>
				<div class="row">
					<label for="name">Email:*</label><br/>
					<input type="email" name="email" class="wide-fat" required/>
				</div>
				<div class="row">
					<label for="name">Telephone:*</label><br/>
					<input type="telephone" name="telephone" class="wide-fat" required/>
				</div>
				<div class="row">
					<label for="name">Restaurant:</label><br/>
					<input type="text" name="restaurant" class="wide-fat"/>
				</div>
				<div class="row">
					<label for="subject">Subject</label><br/>
					<select name="subject">
						<option value="New Listing">I want to get listed on Utterfare.com</option>
						<option value="General">General Query</option>
					</select>
				</div>
				<div class="row">
					<label for="name">Message:*</label><br/>
					<textarea cols="75" rows="5" name="message" class="wide-fat" required></textarea>
				</div>
				<div class="row">
					<button type="submit" class="submit">Send Message</button>
				</div>
			</form>
		</div>
		<div class="col-md-3 col-md-offset-1">
			<h2>Utterfare&trade;</h2>
			<p><label>Telepone:</label><a href="tel:336.260.7945">(336) 260-7945</a></p>
			<p><label>Email:</label><a href="mailto:customerservice@utterfare.com">customerservice@utterfare.com</a></p>
			<address>
			Utterfare, LLC.<br/>
			Hilton Head Island, SC, 29926
			</address>
		</div>
	</div>
</div>
	
<?php include_once('footer.php'); ?>
	