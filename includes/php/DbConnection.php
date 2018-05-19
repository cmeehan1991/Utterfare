<?php
$db_name = 'cmeehan_utterfare';
$db_host = 'ns8139.hostgator.com'; //'localhost';
$db_user = 'cmeehan_dbsearch';
$db_pass = 'Wadiver15!';

$conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);