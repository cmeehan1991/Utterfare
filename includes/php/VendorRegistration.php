<?php
session_start();
$action = filter_input(INPUT_POST, 'action');

switch ($action) {
    case "registerNewVendor":
        addVendor();
        break;
    default:
        break;
}

function addVendor() {
    include "DbConnection.php";
    $company_name = filter_input(INPUT_POST, "company_name");
    $username = filter_input(INPUT_POST, "username");
    $password = md5(filter_input(INPUT_POST, "password"));
    $first_name = filter_input(INPUT_POST, "first_name");
    $last_name = filter_input(INPUT_POST, "last_name");
    $email = filter_input(INPUT_POST, "email");
    $phone = filter_input(INPUT_POST, "phone");
    $street_address = filter_input(INPUT_POST, "street_address");
    $building_number = filter_input(INPUT_POST, "building_number");
    $city = filter_input(INPUT_POST, "city");
    $state = filter_input(INPUT_POST, "state");
    $zip = filter_input(INPUT_POST, "zip");
    $country = filter_input(INPUT_POST, "country");
    $website = filter_input(INPUT_POST, "web-prefix" + "url");
    $data_table = getDataTable($zip);
    $sql = "INSERT INTO VENDOR_LOGIN(USERNAME, PASSWORD, DATA_TABLE, FIRST_NAME, LAST_NAME) VALUES(:USERNAME, :PASSWORD, :DATA_TABLE, :FIRST_NAME, :LAST_NAME)";
    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":USERNAME", $username);
        $stmt->bindParam(":PASSWORD", $password);
        $stmt->bindParam(":DATA_TABLE", $data_table);
        $stmt->bindParam(":FIRST_NAME", $first_name);
        $stmt->bindParam(":LAST_NAME", $last_name);
        $stmt->execute();
        $num_rows = $stmt->rowCount();


        if ($num_rows > 0) {
            $last_key = $conn->lastInsertId();
            checkForCompany($company_name, $email, $phone, $street_address, $building_number, $city, $state, $zip, $country, $website, $data_table, $last_key);
        } else {
            echo ("Error adding vendor");
        }
    } catch (Exception $ex) {
        echo $ex->getMessage();
    }
}

function checkForCompany($company_name, $email, $phone, $street_address, $building_number, $city, $state, $zip, $country, $website, $data_table, $last_key) {
    include 'DbConnection.php';
    $sql = "SELECT ID FROM " . strtolower($data_table) . "_customer WHERE COMPANY_NAME = :COMPANY_NAME AND PRIMARY_ADDRESS = :ADDRESS AND CITY = :CITY AND STATE = :STATE AND ZIP = :ZIP";
    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":COMPANY_NAME", $company_name);
        $stmt->bindParam(":ADDRESS", $street_address);
        $stmt->bindParam(":CITY", $city);
        $stmt->bindParam(":STATE", $state);
        $stmt->bindParam(":ZIP", $zip);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $num_rows = $stmt->rowCount();
        if ($num_rows > 0) {
            // The business already exists. 
            // The user should be notified to have the account manager give them access to the account. 
            // Also provide contact information in case there has been an error. 
            $results = $stmt->fetch();
           applyCompanyToUser($results["ID"], $last_key);
        } else {
            registerCompany($company_name, $email, $phone, $street_address, $building_number, $city, $state, $zip, $country, $website, $data_table, $last_key);
        }
    } catch (Exception $ex) {
        echo $ex->getMessage();
    }
}

/*
 * This function will register a new company and return the unique company ID based on the data table key. 
 * 
 * @params $company_name, $email, $phone, $street_address, $building_number, $city, $state, $zip, $country, $website, $data_table, $last_key
 */

function registerCompany($company_name, $email, $phone, $street_address, $building_number, $city, $state, $zip, $country, $website, $data_table, $last_key) {
    include "DbConnection.php";

    // Get the latitude and longitude based on the address
    $address = str_replace(" ", "+",$street_address) . '+' . str_replace(" ", "*", $city) . '+' . $state . '+' . $zip . '+' . $country;
    $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?address=' . $address . '&sensor=false');
    $location_info = json_decode($geocode);
    $latitude = $location_info->results[0]->geometry->location->lat;
    $longitude = $location_info->results[0]->geometry->location->lng;

    $sql = "INSERT INTO " . strtolower($data_table) . "_customer (COMPANY_NAME, PRIMARY_ADDRESS, SECONDARY_ADDRESS, CITY, STATE, ZIP, PRIMARY_PHONE, COMPANY_URL, LATITUDE, LONGITUDE) VALUES(:COMPANY_NAME, :PRIMARY_ADDRESS, :SECONDARY_ADDRESS, :CITY, :STATE, :ZIP, :PRIMARY_PHONE, :COMPANY_URL, :LATITUDE, :LONGITUDE)";
    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":COMPANY_NAME", $company_name);
        $stmt->bindParam(":PRIMARY_ADDRESS", $street_address);
        $stmt->bindParam(":SECONDARY_ADDRESS", $building_number);
        $stmt->bindParam(":CITY", $city);
        $stmt->bindParam(":STATE", $state);
        $stmt->bindParam(":ZIP", $zip);
        $stmt->bindParam(":PRIMARY_PHONE", $phone);
        $stmt->bindParam(":COMPANY_URL", $website);
        $stmt->bindParam(":LATITUDE", $latitude);
        $stmt->bindParam(":LONGITUDE", $longitude);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $num_rows = $stmt->rowCount();

        if ($num_rows > 0) {
            applyCompanyToUser($conn->lastInsertId(), $last_key);
        } else {
            echo "error";
        }
    } catch (Exception $ex) {
        echo $ex->getMessage();
    }
}

function applyCompanyToUser($company_id, $user_id) {
    include 'DbConnection.php';

    $sql = "UPDATE VENDOR_LOGIN SET COMPANY_ID = :COMPANY_ID WHERE VENDOR_LOGIN.ID = :ID";
    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":COMPANY_ID", $company_id);
        $stmt->bindParam(":ID", $user_id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $_SESSION["ID"] = $company_id;
            $_SESSION["USER_ID"] = $user_id;
            echo "success";
        }else{
            echo "fail";
        }
    } catch (Exception $ex) {
        echo $ex->getMessage();
    }
}

function getDataTable($zip) {
    include 'DbConnection.php';

    $sql = "SELECT DISTINCT(DATA_TABLE) FROM zips WHERE ZIPCODE = :ZIPCODE";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":ZIPCODE", $zip);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);

    if ($stmt->rowCount() > 0) {
        $results = $stmt->fetch();
        return $results["DATA_TABLE"];
    } else {
        return null;
    }
}
