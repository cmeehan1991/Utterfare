<?php

$whitelist = array(
	'127.0.0.1', 
	'::1', 
	'localhost'
);

if(!in_array($_SERVER['REMOTE_ADDR'], $whitelist)){
	$db_name = 'cmeehan_utterfare';
	$db_host = 'localhost';
	$db_user = 'cmeehan_ufare';
	$db_pass = 'Utt3rF4re1954!';
	
	$conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
}else{
	$db_name = 'utterfare'; //'cmeehan_utterfare';
	$db_host = 'localhost';
	$db_user = 'root'; //'cmeehan_ufare';
	$db_pass = 'root'; //'Utt3rF4re1954!';
	
	$conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
}
