<?php
$db_name = 'utterfare'; //'cmeehan_utterfare';
$db_host = 'localhost';
$db_user = 'root'; //'cmeehan_dbsearch';
$db_pass = 'root'; //'Wadiver15!';

$conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);