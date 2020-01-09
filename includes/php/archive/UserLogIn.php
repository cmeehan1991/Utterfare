<?php

session_start();

$action = filter_input(INPUT_POST, 'action');
switch($action){
	case 'sign_in': 
	login();
	break;
	case 'sign_out': 
	sign_out();
	break;
	default: break;
}

function sign_out(){
	session_unset();
	session_destroy();
	die(true);
}

function login(){
	$username = filter_input(INPUT_POST, 'username');
	$password = md5(filter_input(INPUT_POST, 'password'));
    include 'DbConnection.php';
    
    $sql = "SELECT ID, DATA_TABLE, COMPANY_ID FROM VENDOR_LOGIN WHERE USERNAME = :USERNAME AND PASSWORD = :PASSWORD";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':USERNAME', $username);
    $stmt->bindParam(":PASSWORD", $password);
    $stmt->execute();
    
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    
    $num_rows = $stmt->rowCount();
    if($num_rows > 0){
        $results = $stmt->fetchAll();
        $result = $results[0];
        getUserInfo($result["ID"], $result["DATA_TABLE"], $result["COMPANY_ID"]);
    }else{
        echo "None";
    }
}

function getUserInfo($user_id, $data_table, $company_id){
    include 'DbConnection.php';
    $cust_info_table = strtolower($data_table) .'_customer';
    $sql = "SELECT ID, COMPANY_NAME, PRIMARY_ADDRESS, SECONDARY_ADDRESS, CITY, STATE, ZIP, PRIMARY_PHONE, COMPANY_URL, PRIMARY_CONTACT, PRIMARY_EMAIL, KEYWORDS, PROFILE_PICTURE FROM $cust_info_table WHERE ID = :COMPANY_ID";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':COMPANY_ID', $company_id);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    
    $num_rows = $stmt->rowCount();
    
    if($num_rows > 0){
        while($results = $stmt->fetch()){
            $_SESSION['COMPANY_ID'] = $results['ID'];
            $_SESSION['PRIMARY_ADDRESS'] = $results['PRIMARY_ADDRESS'];
            $_SESSION['SECONDARY_ADDRESS'] = $results['SECONDARY_ADDRESS'];
            $_SESSION['CITY'] = $results['CITY'];
            $_SESSION['STATE'] = $results['STATE'];
            $_SESSION['ZIP'] = $results['ZIP'];
            $_SESSION['PHONE'] = $results['PRIMARY_PHONE'];
            $_SESSION['LINK'] = $results['COMPANY_URL'];
            $_SESSION['CONTACT'] = $results['PRIMARY_CONTACT'];
            $_SESSION['EMAIL'] = $results['PRIMARY_EMAIL'];
            $_SESSION['KEYWORDS'] = $results['KEYWORDS'];
            $_SESSION['DATA_TABLE'] = strtolower($data_table);
            $_SESSION['COMPANY_NAME'] = $results['COMPANY_NAME'];
            $_SESSION['USER_ID'] = $user_id;
            $_SESSION['PROFILE_PICTURE'] = $results['PROFILE_PICTURE'];
            echo "success"; 
        }
    }else{
        echo "fail";
    }
}