<?php 
	// User input variables
	$name = filter_input(INPUT_POST, 'name');
	$email = filter_input(INPUT_POST, 'email');
	$telephone = filter_input(INPUT_POST, 'telephone');
	$subject = filter_input(INPUT_POST, 'subject');
	$restaurant = filter_input(INPUT_POST, 'restaurant');
	$message_text = filter_input(INPUT_POST, 'message');
	
	// Email message
	$message = "<html><table>
	<tr>
	<td><b>Name:</b></td><td>$name</td>
	</tr>
	<tr>
	<td><b>Email:</b></td><td>$email</td>
	</tr>
	<tr>
	<td><b>Telephone:</b></td><td>$telephone</td>
	</tr>
	<tr>
	<td><b>Restaurant:</b></td><td>$restaurant</td>
	</tr>
	<tr>
	<td><b>Message:</b></td><td>$message_text</td>
	</tr>
	</table></html>";
	
	// Standard variables
	$to = "customerservice@utterfare.com";
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type: text/html;charset=UTF-8" . "\r\n";
	
	// More headers
	$headers .= 'From: Connor Meehan<webmail@utterfare.com>' . "\r\n";
	$headers .= 'Cc: brian.meehan@utterfare.com' . "\r\n";
	$headers .= 'Cc: connor.meehan@utterfare.com' . "\r\n";
	
	mail($to, $subject, $message, $headers);
