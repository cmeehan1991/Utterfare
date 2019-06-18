<?php
session_start();

$is_signed_in = $_SESSION['IS_SIGNED_IN'];

if($is_signed_in){
	$user_id = $_SESSION['USER_ID'];
}