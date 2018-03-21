<?php
session_start();
class VendorRegistration{
	
	public $action, $company_name, $username, $password, $first_name, $last_name, $email, $phone, $street_address, $secondary_address, $city, $state, $zip, $country, $url, $data_table, $last_key;
	
	public function __construct(){		
		$this->assignValues();
		switch ($this->action) {
		    case "registerNewVendor":
		        $this->addVendor();
		        break;
		    default:
		        break;
		}
	}
	
	public function assignValues(){
		$this->action = filter_input(INPUT_POST, 'action');
		$this->company_name = filter_input(INPUT_POST, "company_name");
		$this->username = filter_input(INPUT_POST, "username");
		$this->password = md5(filter_input(INPUT_POST, "password"));
		$this->first_name = filter_input(INPUT_POST, "first_name");
		$this->last_name = filter_input(INPUT_POST, "last_name");
		$this->email = filter_input(INPUT_POST, "email");
		$this->phone = filter_input(INPUT_POST, "phone");
		$this->street_address = filter_input(INPUT_POST, "street_address");
		$this->secondary_address = filter_input(INPUT_POST, "building_number");
		$this->city = filter_input(INPUT_POST, "city");
		$this->state = filter_input(INPUT_POST, "state");
		$this->zip = filter_input(INPUT_POST, "zip");
		$this->country = filter_input(INPUT_POST, "country");
		$this->url = filter_input(INPUT_POST, "web-prefix") + filter_input(INPUT_POST, "url");
		$this->data_table = $this->getDataTable($this->zip);

	}
	
	private function addVendor() {
	    include "DbConnection.php";
	    $sql = "INSERT INTO VENDOR_LOGIN(USERNAME, PASSWORD, DATA_TABLE, FIRST_NAME, LAST_NAME) VALUES(:USERNAME, :PASSWORD, :DATA_TABLE, :FIRST_NAME, :LAST_NAME)";
	    try {
	        $stmt = $conn->prepare($sql);
	        $stmt->bindParam(":USERNAME", $this->username);
	        $stmt->bindParam(":PASSWORD", $this->password);
	        $stmt->bindParam(":DATA_TABLE", $this->data_table);
	        $stmt->bindParam(":FIRST_NAME", $this->first_name);
	        $stmt->bindParam(":LAST_NAME", $this->last_name);
	        $stmt->execute();
	        $num_rows = $stmt->rowCount();
	
	        if ($num_rows > 0) {
	            $this->last_key = $conn->lastInsertId();
	            $this->checkForCompany();
	        } else {
	            echo ("Error adding vendor");
	        }
	    } catch (Exception $ex) {
	        echo $ex->getMessage();
	    }
	}
	
	private function checkForCompany() {
	    include 'DbConnection.php';
	    $sql = "SELECT ID FROM " . strtolower($this->data_table) . "_customer WHERE COMPANY_NAME = :COMPANY_NAME AND PRIMARY_ADDRESS = :ADDRESS AND CITY = :CITY AND STATE = :STATE AND ZIP = :ZIP";
	    try {
	        $stmt = $conn->prepare($sql);
	        $stmt->bindParam(":COMPANY_NAME", $this->company_name);
	        $stmt->bindParam(":ADDRESS", $this->street_address);
	        $stmt->bindParam(":CITY", $this->city);
	        $stmt->bindParam(":STATE", $this->state);
	        $stmt->bindParam(":ZIP", $this->zip);
	        $stmt->execute();
	        $stmt->setFetchMode(PDO::FETCH_ASSOC);
	
	        $num_rows = $stmt->rowCount();
	        if ($num_rows > 0) {
	            // The business already exists. 
	            // The user should be notified to have the account manager give them access to the account. 
	            // Also provide contact information in case there has been an error. 
	            $results = $stmt->fetch();
				$this->applyCompanyToUser($results["ID"], $this->last_key);
	        } else {
	            $this->registerCompany();
	        }
	    } catch (Exception $ex) {
	        echo $ex->getMessage();
	    }
	}
	
	/*
	 * This function will register a new company and return the unique company ID based on the data table key. 
	 * 
	 * @params $company_name, $email, $phone, $street_address, $secondary_address, $city, $state, $zip, $country, $url, $data_table, $last_key
	 */
	
	private function registerCompany() {
	    include "DbConnection.php";
	    // Get the latitude and longitude based on the address
	    $address = str_replace(" ", "+",$this->street_address) . '+' . str_replace(" ", "*", $this->city) . '+' . $this->state . '+' . $this->zip . '+' . $this->country;
	    $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?address=' . $address . '&sensor=false');
	    $location_info = json_decode($geocode);
	    $latitude = $location_info->results[0]->geometry->location->lat;
	    $longitude = $location_info->results[0]->geometry->location->lng;
	
	    $sql = "INSERT INTO " . strtolower($this->data_table) . "_customer (COMPANY_NAME, PRIMARY_ADDRESS, SECONDARY_ADDRESS, CITY, STATE, ZIP, PRIMARY_PHONE, COMPANY_URL, PRIMARY_EMAIL, LATITUDE, LONGITUDE) VALUES(:COMPANY_NAME, :PRIMARY_ADDRESS, :SECONDARY_ADDRESS, :CITY, :STATE, :ZIP, :PRIMARY_PHONE, :COMPANY_URL, :PRIMARY_EMAIL, :LATITUDE, :LONGITUDE)";
	    try {
	        $stmt = $conn->prepare($sql);
	        $stmt->bindParam(":COMPANY_NAME", $this->company_name);
	        $stmt->bindParam(":PRIMARY_ADDRESS", $this->street_address);
	        $stmt->bindParam(":SECONDARY_ADDRESS", $this->secondary_address);
	        $stmt->bindParam(":CITY", $this->city);
	        $stmt->bindParam(":STATE", $this->state);
	        $stmt->bindParam(":ZIP", $this->zip);
	        $stmt->bindParam(":PRIMARY_PHONE", $this->phone);
	        $stmt->bindParam(":COMPANY_URL", $this->url);
	        $stmt->bindParam(":PRIMARY_EMAIL", $this->email);
	        $stmt->bindParam(":LATITUDE", $latitude);
	        $stmt->bindParam(":LONGITUDE", $longitude);
	       
	        $stmt->execute();
	        $stmt->setFetchMode(PDO::FETCH_ASSOC);
	
	        $num_rows = $stmt->rowCount();
	
	        if ($num_rows > 0) {
	            $this->applyCompanyToUser($conn->lastInsertId(), $this->last_key);
	        } else {
	            echo "error";
	        }
	    } catch (Exception $ex) {
	        echo $ex->getMessage();
	    }
	}
	
	private function applyCompanyToUser($company_id, $user_id) {
	    include 'DbConnection.php';
	    $sql = "UPDATE VENDOR_LOGIN SET COMPANY_ID = :COMPANY_ID WHERE VENDOR_LOGIN.ID = :ID";
	    try {
	        $stmt = $conn->prepare($sql);
	        $stmt->bindParam(":COMPANY_ID", $company_id);
	        $stmt->bindParam(":ID", $user_id);
	        $stmt->execute();
			$num_rows = $stmt->rowCount();
	        if ($num_rows > 0) {
	            $_SESSION["COMPANY_ID"] = $company_id;
	            $_SESSION["USER_ID"] = $user_id;            
	            $_SESSION['PRIMARY_ADDRESS'] = $this->street_address;
	            $_SESSION['SECONDARY_ADDRESS'] = $this->secondary_address;
	            $_SESSION['CITY'] = $this->city;
	            $_SESSION['STATE'] = $this->state;
	            $_SESSION['ZIP'] = $this->zip;
	            $_SESSION['PHONE'] = $this->phone;
	            $_SESSION['LINK'] = $this->url;
	            $_SESSION['EMAIL'] = $this->email;
	            $_SESSION['DATA_TABLE'] = strtolower($this->data_table);
	            $_SESSION['COMPANY_NAME'] = $this->company_name;
	            echo "success";
	        }else{
	            echo "fail";
	        }
	    } catch (Exception $ex) {
	        echo $ex->getMessage();
	    }
	}
	
	private function getDataTable($zip) {
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
}
new VendorRegistration();



