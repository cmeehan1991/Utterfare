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
		       case "registrationNotification":
		       $this->registration_notification();
		       case "validateUsername":
		       $this->validateUsername();
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
		$this->url = filter_input(INPUT_POST, "url");
		$this->data_table = $this->getDataTable($this->zip);
	}
	
	private function validateUsername(){
		include 'DbConnection.php';
		
		$username = filter_input(INPUT_POST, 'username');
		
		$sql = 'SELECT COUNT(ID) as TOTAL FROM VENDOR_LOGIN WHERE USERNAME = :USERNAME';
		
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(":USERNAME", $username);
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		
		$res = $stmt->fetch();
		if($res['TOTAL'] > 0){
			echo false;
		}else{
			echo true;
		}
	}
	
	/*
	* Send a confirmation email to the new vendor
	*/
	private function registration_notification(){
		$to = $this->email;
		$subject = "Utterfare Vendor Registration Confirmation";
		
		$msg = "<html>";
		$msg .="<head><title>Utterfare Vendor Registration Confirmation</title></head><body>";
		$msg .= $this->first_name . ' ' . $this->last_name . ',';
		$msg .= '<br/>';
		$msg .= '<p>Welcome and thank you for registering as a vendor on Utterfare. Once you start entering in menu items you will become immediately visible on Utterfare searches in your area. Please take a moment to look around the vendor dashboard.</p>'; 
		$msg .= "<p>If you need any assistance please contact us at <a href=\"mailto:vendor.services@utterfare.com\">vendor.services@utterare.com</a>. A representative from Utterfare will be contacting you shortly to help you get set up for the first time.</p>";
		$msg .= "<p>If you did not sign up to be a vendor for Utterfare or think you should not be receiving this email please contact us immediately at <a href=\"mailto:listings@utterfare.com\">listings@utterare.com</a>.</p>";
		$msg .= "Thank you,";
		$msg .= "<b>The Utterfare Team</b>";
		$msg .= "<br/>";
		$msg .= "<a href=\"https://www.utterfare.com\">Utterfare.com</a>";
		$msg .= '<br/>';
		$msg .= "<a href=\"mailto:vendor.services@utterfare.com\">vendor.services@utterfare.com</a>";
		$msg .= '<br/>';
		$msg .= "<a href=\"tel:3362600061\">(336) 260-0061</a>";
		$msg .= "<br/>";
		$msg .= "<img src=\"https://www.utterfare.com/images/Email%20Logo.png\" alt=\"Utterfare Logo\"/>";
		$msg .= "</body>";
		$msg .= "</html>";
		
		$message = wordwrap($msg, 70);
		
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		$headers .= "From: listings@utterfare.com" . "\r\n";
		
		$send = mail($to, $subject, $msg, $headers);
		$this->notify_listing();	
	}
	
	/*
	* Notify the Utterfare listings of the new registration
	*/
	private function notify_listing(){
		$to = "listings@utterfare.com";
		$subject = "New Vendor Registration Notificatoin";
				
		$msg = "<html>";
		$msg .="<head><title>New Vendor Registration Notification</title></head><body>";
		$msg .= "<p>" . $this->company_name . " has registered to be a vendor.</p>"; 
		$msg .= "<br/><br/>";
		$msg .= "<table><tbody>";
		$msg .= "<tr>";
		$msg .= "<td><b>Company Name:</b></td><td>" . $this->company_name . "</td>";
		$msg .= "</tr><tr>";
		$msg .= "<td><b>Contact Name:</b></td><td>" .  $this->first_name . ', ' . $this->last_name . "</td>";
		$msg .= "</tr><tr>";
		$msg .= "<td><b>Contact Email:</b></td><td>" . $this->email . "</td>";
		$msg .= "</tr><tr>";
		$msg .= "<td><b>Contact Phone:</b></td><td>" . $this->phone . "</td>";
		$msg .= "</tr><tr>";
		$msg .= "<td><b>Registration Date:</b></td><td>" . date('D,  F jS, Y G') . "</td>";
		$msg .= "</tr><tr>";
		$msg .= "<td><b>Location:</b></td><td>" . $this->city . ', ' . $this->state . "</td>";
		$msg .= "</tr><tr>";
		$msg .= "<td><b>Website URL:</b></td><td>" . $this->url . "</td>";
		$msg .= "</tr>";
		$msg .= "</tbody></table>";
		$msg .= "</body>";
		$msg .= "</html>";
		
		$message = wordwrap($msg, 70);
		
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		$headers .= "From: listings@utterfare.com" . "\r\n";

		mail($to, $subject, $message, $headers);	
		
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
				echo "exists";
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
	    $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?address=' . $address . '&sensor=false&key=AIzaSyDVThKX7yiVem3vU7457VYdZmDOM_EWW7k');
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
	            $this->registration_notification();
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
	        return "No datatable";
	    }
	}	
}
new VendorRegistration();



