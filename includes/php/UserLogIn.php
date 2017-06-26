<?php
session_start();
$username = filter_input(INPUT_POST, 'username');
$password = md5(filter_input(INPUT_POST, 'password'));

if($username != null && $password != null){
    login($username, $password);
}else{
    echo "nothing";
}

function login($username, $password){
    include 'DbConnection.php';
    
    $sql = "SELECT ID, DATA_TABLE, COMPANY_ID FROM VENDOR_LOGIN WHERE USERNAME = :USERNAME AND PASSWORD = :PASSWORD";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':USERNAME', $username);
    $stmt->bindParam(":PASSWORD", $password);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    
    $num_rows = $stmt->rowCount();
    
    if($num_rows > 0){
        $results = $stmt->fetch();
        getUserInfo($results["ID"], $results["DATA_TABLE"], $results["COMPANY_ID"]);
    }else{
        echo "None";
    }
}

function getUserInfo($user_id, $data_table, $company_id){
    include 'DbConnection.php';
    $cust_info_table = strtolower($data_table) .'_customer';
    $sql = "SELECT ID, COMPANY_NAME, PRIMARY_ADDRESS, SECONDARY_ADDRESS, CITY, STATE, ZIP, PRIMARY_PHONE, COMPANY_URL, PRIMARY_CONTACT, PRIMARY_EMAIL, KEYWORDS FROM $cust_info_table WHERE ID = :ID";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":ID", $company_id);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    
    $num_rows = $stmt->rowCount();
    
    if($num_rows > 0){
        while($results = $stmt->fetch()){
            $_SESSION['ID'] = $results['ID'];
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
            echo "success"; 
        }
    }else{
        echo "fail";
    }
}