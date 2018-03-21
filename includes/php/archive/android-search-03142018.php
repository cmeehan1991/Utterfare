<?php
include 'SearchAnalytics.php';
$terms =  filter_input(INPUT_POST, 'terms');
$location = filter_input(INPUT_POST, 'location');
$limit =   filter_input(INPUT_POST, 'limit');
$page = filter_input(INPUT_POST, 'page');
$offset = filter_input(INPUT_POST, 'offset');
$distance = filter_input(INPUT_POST, 'distance');
$type = "Mobile";
$platform = "Android";

if (isset($terms)) {
    getResults($location, $distance, $terms);
}

$GLOBALS['lat'] = '';
$GLOBALS['lng'] = '';

$search_analytics = new SearchAnalytics();
$search_analytics->save_search_terms($terms);
$search_analytics->save_search_general_information($location, $distance, $terms, $type, $platform);

/*
 * Get the location based on either the city/state or the zip code given.
 * The application will automatically choose the location based on the user's IP address.
 * The user can also choose to update this information if they want, so it is necessary
 * to check and see what information has been given.
 * 
 * @params String location         Either zip code or city/state combination
 * @return array data tables       Maximum of three data tables
 */

function getDataTables($location, $distance) {
    // Include the database connection
    include 'DbConnection.php';

    $GLOBALS['distance'] = $distance;

    // First determine if zip or city/state was given
    $length = strlen($location);
    if(strpos($location, ":") > -1){
	    $lat_lng = explode(":", $location);
	    $lat = $GLOBALS['lat'] = $lat_lng[0];
	    $lng = $GLOBALS['lng'] = $lat_lng[1];
	    
	}elseif(strpos($location, "%3A") > -1){
	    $lat_lng = explode("%3A", $location);
	    $lat = $GLOBALS['lat'] = $lat_lng[0];
	    $lng = $GLOBALS['lng'] = $lat_lng[1];
	}else{
	    if ($length == 5 && preg_match('|[0-9]|', $location)) {
		    // If we are working with a zip code then we need to get the city, state, and country
		    $json_zip = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=$location&key=AIzaSyDo0e6jGh6cZhToU-XRWeRHCKezjLT9_Ko");
	        $obj_zip = json_decode($json_zip, true);
	        $city = str_replace(' ', '%20', $obj_zip['results'][0]['address_components'][1]['long_name']);
	        $state = str_replace(' ', '%20', $obj_zip['results'][0]['address_components'][2]['short_name']);
	        $country = str_replace(' ', '%20', $obj_zip['results'][0]['address_components'][3]['short_name']);
	        
	        // Form the search location 
	        $search_location = $city . ',' . $state . ',' . $country;
	        $json = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=$search_location&key=AIzaSyDo0e6jGh6cZhToU-XRWeRHCKezjLT9_Ko");
	        $obj = json_decode($json, true);
	        
	        // Get the latitude and longitude
	        $lat = $obj['results'][0]['geometry']['location']['lat'];
	        $lng = $obj['results'][0]['geometry']['location']['lng'];
	        
	        // Assign the latitude and longitude to the global variables lat & lng, respectively.
	        $GLOBALS['zip'] = $location;
	        $GLOBALS['lat'] = $lat;
	        $GLOBALS['lng'] = $lng;
	    } else {
	        $location = explode(',', str_replace(' ', ' ', $location));
	        $loc = str_replace(' ', '%20', implode($location));
	        $json = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=$loc&key=AIzaSyDo0e6jGh6cZhToU-XRWeRHCKezjLT9_Ko");
	        $obj = json_decode($json, true);
	        $lat = $obj['results'][0]['geometry']['location']['lat'];
	        $lng = $obj['results'][0]['geometry']['location']['lng'];
	        $GLOBALS['zip'] = $zipcode;
	        $GLOBALS['lat'] = $lat;
	        $GLOBALS['lng'] = $lng;
	    }
    }
    
    // Get the closest city within x miles of the location

    $sql = "SELECT DISTINCT DATA_TABLE FROM zips WHERE (ACOS(SIN(radians($lat))*SIN(radians(LATITUDE))+ COS(radians($lat))*COS(radians(LATITUDE))*COS(radians(LONGITUDE)-radians($lng)))*3443.89849) <= $distance limit 3";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $num_rows = $stmt->rowCount();
    $data_tables = array(
        '1' => 'blank_customer',
        '2' => 'blank_customer',
        '3' => 'blank_customer'
    );
    
    
    if ($num_rows > 0) {
        $count = 1;
        $num_rows += 1;
        while ($count < $num_rows) {
            $dt_results = $stmt->fetch();
            $data_tables[$count] = strtolower($dt_results['DATA_TABLE']);
            $count++;
        }
    }
   
    $conn = null;
    return $data_tables;
}

/*
*	This function will rank the customers based on distance from the search radius
*/	



/*
 * This is the primary function of this file. 
 * This function will parse the search terms and return results.
 * 
 * @return json array results   SQL results of the search based on the user's location and inputs
 */

function getResults($location, $distance, $terms) {
    include 'DbConnection.php';
    // Get the datatables to be used for the search. 
    $data_tables = getDataTables($location, $distance);

    // Set the latitude, longidute, and distance variables
    $lat = $GLOBALS['lat'];
    $lng = $GLOBALS['lng'];
    $distance = $GLOBALS['distance'];
    //$limit = $GLOBALS['limit'];
    $offset = $GLOBALS['offset'];

    // Set the datatable names
    if ($data_tables[1] != 'blank_customer') {
        $customer_one = $data_tables[1] . '_customer';
        $items_one = $data_tables[1] . '_items';
        $datatable_one = $data_tables[1];
    }

    if ($data_tables[2] != 'blank_customer') {
        $customer_two = $data_tables[2] . '_customer';
        $items_two = $data_tables[2] . '_items';
        $datatable_two = $data_tables[2];
    }

    if ($data_tables[3] != 'blank_customer') {
        $customer_three = $data_tables[3] . '_customer';
        $items_three = $data_tables[3] . '_items';
        $datatable_three = $data_tables[3];
    }


    // Remove all unwanted words from the search terms
    $pattern = "/\ band | that | or | this | or | because | want | of | with | |[^a-zA-Z0-9]+\b/i";
    $sterile_terms = explode(" ", preg_replace($pattern, '/', $terms));

    foreach ($sterile_terms as $sterilized) {
        $sterilized_string .= $sterilized;
    }

    $search_terms = array_diff(explode('/', $sterilized_string), ['']);

	/*
    $parameters_one = "$items_one.ITEM_NAME = '$search_terms' OR $items_one.ITEM_DESCRIPTION = '$search_terms' OR $items_one.COMPANY_ID = (SELECT COMPANY_ID FROM $customer_one WHERE $customer_one.COMPANY_NAME = '$search_terms') OR $customer_one.COMPANY_NAME = '$search_terms'";
    
    $parameters_two = "$items_two.ITEM_NAME = '$terms' OR $items_two.ITEM_DESCRIPTION = '$terms' OR $items_two.COMPANY_ID = (SELECT COMPANY_ID FROM $customer_two WHERE $customer_two.COMPANY_NAME = '$terms') OR $customer_two.COMPANY_NAME = '$terms'";
    
    $parameters_three = "$items_three.ITEM_NAME = '$search_terms' OR $items_three.ITEM_DESCRIPTION = '$$search_terms' OR $items_three.COMPANY_ID = (SELECT COMPANY_ID FROM $customer_three WHERE $customer_three.COMPANY_NAME = '$search_terms') OR $customer_three.COMPANY_NAME = '$search_terms'";
	*/

	$loop = 0;
    foreach ($search_terms as $new_terms) {
	    if($loop > 0){
		    $joiner = 'OR';
	    }
        $parameters_one .= "$joiner $items_one.ITEM_NAME = '$new_terms' OR $items_one.ITEM_DESCRIPTION = '$new_terms' OR $items_one.COMPANY_ID = (SELECT COMPANY_ID FROM $customer_one WHERE $customer_one.COMPANY_NAME = '$$new_terms') OR $customer_one.COMPANY_NAME = '$new_terms'  OR $items_one.ITEM_DESCRIPTION LIKE '%" . $new_terms . "%' OR $items_one.ITEM_NAME LIKE '%" . $new_terms . "%' OR $customer_one.COMPANY_NAME LIKE '%" . $new_terms . "%' ";
        $parameters_two .= "$joiner $items_two.ITEM_DESCRIPTION LIKE '%" . $new_terms . "%' OR $items_two.ITEM_NAME LIKE '%" . $new_terms . "%' OR $customer_two.COMPANY_NAME LIKE '%" . $new_terms . "%' ";
		$parameters_three .= "$joiner $items_three.ITEM_DESCRIPTION LIKE '%" . $new_terms . "%' OR $items_three.ITEM_NAME LIKE '%" . $new_terms . "%' OR $customer_three.COMPANY_NAME LIKE '%" . $new_terms . "%' ";
		$parameters_three .= "$joiner $items_three.ITEM_DESCRIPTION LIKE '%" . $new_terms . "%' OR $items_three.ITEM_NAME LIKE '%" . $new_terms . "%' OR $customer_three.COMPANY_NAME LIKE '%" . $new_terms . "%' ";

		$loop++;
    }

    $sql = "SELECT $items_one.ID AS 'ITEM_ID', '$data_tables[1]' as 'DATA_TABLE', $customer_one.KEYWORDS AS 'KEYWORDS',$customer_one.LATITUDE AS 'LATITUDE', $customer_one.LONGITUDE AS 'LONGITUDE', $items_one.COMPANY_ID AS 'COMPANY_ID', $customer_one.COMPANY_NAME AS 'COMPANY', if($customer_one.SECONDARY_ADDRESS != '', CONCAT($customer_one.PRIMARY_ADDRESS, '\n', $customer_one.SECONDARY_ADDRESS), $customer_one.PRIMARY_ADDRESS) as 'ADDRESS', $customer_one.city as 'CITY', $customer_one.state AS 'STATE', $customer_one.zip AS 'ZIP', $items_one.ITEM_NAME AS 'NAME', $items_one.ITEM_RANK AS 'ITEM_RANK', $items_one.ITEM_DESCRIPTION AS 'DESCRIPTION', $items_one.ITEM_IMAGE AS 'PHOTO_DIRECTORY', $items_one.ID AS 'ID',  IF($customer_one.PRIMARY_PHONE != '', $customer_one.PRIMARY_PHONE, '') AS 'PHONE', $customer_one.COMPANY_URL AS 'LINK', DEGREES((ACOS(SIN(RADIANS($lat))*SIN(RADIANS($customer_one.LATITUDE))+ COS(RADIANS($lat))*COS(RADIANS($customer_one.LATITUDE))*COS(RADIANS(($customer_one.LONGITUDE)-($lng)))))) * 60 * 1.1515 AS 'DISTANCE' FROM $items_one JOIN $customer_one ON $customer_one.ID = $items_one.COMPANY_ID WHERE ($parameters_one) AND DEGREES((ACOS(SIN(RADIANS($lat))*SIN(RADIANS($customer_one.LATITUDE))+ COS(RADIANS($lat))*COS(RADIANS($customer_one.LATITUDE))*COS(RADIANS(($customer_one.LONGITUDE)-($lng)))))) * 60 * 1.1515 <= $distance";
    if ($data_tables[2] != 'blank_customer') {
        $sql .= " UNION ALL SELECT $items_two.ID AS 'ITEM_ID', '$data_tables[2]' as 'DATA_TABLE', $customer_two.KEYWORDS AS 'KEYWORDS',$customer_two.LATITUDE AS 'LATITUDE', $customer_two.LONGITUDE AS 'LONGITUDE', $items_two.COMPANY_ID AS 'COMPANY_ID', $customer_two.COMPANY_NAME AS 'COMPANY', if($customer_two.SECONDARY_ADDRESS != '', CONCAT($customer_two.PRIMARY_ADDRESS, '\n', $customer_two.SECONDARY_ADDRESS), $customer_two.PRIMARY_ADDRESS) as 'ADDRESS', $customer_two.city as 'CITY', $customer_two.state AS 'STATE', $customer_two.zip AS 'ZIP', $items_two.ITEM_NAME AS 'NAME', $items_two.ITEM_RANK AS 'ITEM_RANK', $items_two.ITEM_DESCRIPTION AS 'DESCRIPTION', $items_two.ITEM_IMAGE AS 'PHOTO_DIRECTORY', $items_two.ID AS 'ID', IF($customer_two.PRIMARY_PHONE != '', $customer_two.PRIMARY_PHONE, '') AS 'PHONE', $customer_two.COMPANY_URL AS 'LINK', DEGREES((ACOS(SIN(RADIANS($lat))*SIN(RADIANS($customer_two.LATITUDE))+ COS(RADIANS($lat))*COS(RADIANS($customer_two.LATITUDE))*COS(RADIANS(($customer_two.LONGITUDE)-($lng)))))) * 60 * 1.1515 AS 'DISTANCE' FROM $items_two JOIN $customer_two ON $customer_two.ID = $items_two.COMPANY_ID WHERE ($parameters_two) AND DEGREES((ACOS(SIN(RADIANS($lat))*SIN(RADIANS($customer_two.LATITUDE))+ COS(RADIANS($lat))*COS(RADIANS($customer_two.LATITUDE))*COS(RADIANS(($customer_two.LONGITUDE)-($lng)))))) * 60 * 1.1515 <= $distance";
    }
    if ($data_tables[3] != 'blank_customer') {
        $sql .= " UNION ALL SELECT $items_three.ID AS 'ITEM_ID', '$data_tables[3]' as 'DATA_TABLE', $customer_three.KEYWORDS AS 'KEYWORDS',$customer_three.LATITUDE AS 'LATITUDE', $customer_three.LONGITUDE AS 'LONGITUDE', $items_three.COMPANY_ID AS 'COMPANY_ID', $customer_three.COMPANY_NAME AS 'COMPANY', if($customer_three.SECONDARY_ADDRESS != '', CONCAT($customer_three.PRIMARY_ADDRESS, '\n', $customer_three.SECONDARY_ADDRESS), $customer_three.PRIMARY_ADDRESS) as 'ADDRESS', $customer_three.city as 'CITY', $customer_three.state AS 'STATE', $customer_three.zip AS 'ZIP', $items_three.ITEM_RANK AS 'ITEM_RANK', $items_three.ITEM_NAME AS 'NAME', $items_three.ITEM_DESCRIPTION AS 'DESCRIPTION', $items_three.ITEM_IMAGE AS 'PHOTO_DIRECTORY', $items_three.ID AS 'ID' , IF($customer_three.PRIMARY_PHONE != '', $customer_three.PRIMARY_PHONE, '') AS 'PHONE', $customer_three.COMPANY_URL AS 'LINK',DEGREES((ACOS(SIN(RADIANS($lat))*SIN(RADIANS($customer_three.LATITUDE))+ COS(RADIANS($lat))*COS(RADIANS($customer_three.LATITUDE))*COS(RADIANS(($customer_three.LONGITUDE)-($lng)))))) * 60 * 1.1515 AS 'DISTANCE' FROM $items_three JOIN $customer_three ON $customer_three.ID = $items_three.COMPANY_ID WHERE ($parameters_three) AND DEGREES((ACOS(SIN(RADIANS($lat))*SIN(RADIANS($customer_three.LATITUDE))+ COS(RADIANS($lat))*COS(RADIANS($customer_three.LATITUDE))*COS(RADIANS(($customer_three.LONGITUDE)-($lng)))))) * 60 * 1.1515 <= $distance";
    } 
    
    $sql .= " ORDER BY DISTANCE limit 10 OFFSET $offset";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $num_rows = $stmt->rowCount();
    
    
    if ($num_rows > 0) {
        $results = $stmt->fetchAll();
        
        //print_r($results);
        
        // Calculate the distance to each vendor from the applied location
        $sorted_results = array();
        foreach ($results as &$result) {
	        $lat1 = $result['LATITUDE'];
	        $lng1 = $result['LONGITUDE'];
	        $lat2 = $lat;
	        $lng2 = $lng;
	        $theta = $lng1 - $lng2;
	        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
	        $dist = acos($dist);
	        $dist = rad2deg($dist);
	        $result['DISTANCE'] = $dist * 60 * 1.1515;
        }
        
        
        for($i = 0; $i < count($results); $i++){
	        if(ceil($result['DISTANCE']) <= $distance){
		        array_push($sorted_results, $results[$i]);
	        }
        }       
						
        $num_words = count($search_terms);
        foreach ($sorted_results as &$result) {
            $result['rank'] = 100;
            $key_words = " " . preg_replace('/\s+/', '^', preg_replace('/[[:punct:]]/', ' ', strtoupper(' ' . $result['KEYWORDS'] . ' ')));
            $item_description = " " . preg_replace('/\s+/', '^', preg_replace('/[[:punct:]]/', ' ', strtoupper(' ' . $result['DESCRIPTION'] . ' ')));
            $customer_name = " " . preg_replace('/\s+/', '^', preg_replace('/[[:punct:]]/', ' ', strtoupper(' ' . $result['COMPANY'] . ' ')));
            $item_name = " " . preg_replace('/\s+/', '^', preg_replace('/[[:punct:]]/', ' ', strtoupper(' ' . $result['NAME'] . ' ')));
            $customer_distance = $result['DISTANCE'];
            $counter = 0;
            $name_match = 0;
            while ($counter < $num_words) {
            	$name_match = 0;
                $search_word = $search_terms[$counter];
                // Ranks the word based on number of matches with the name
                foreach(explode('^', $item_name) as $i_name){
	                
	                //echo $name_match;
	                if ( in_array($i_name, array_map('strtoupper',$search_terms))) {
	                    $name_match += 1;
	
	                    //Adjust the ranking accordingly
	                    if ($name_match > 1) {
	                        $result['rank'] = $result['rank'] - (10 * $name_match);
	                    } else {
	                        $result['rank'] = $result['rank'] - (5 * $name_match);
	                    }
                	}

                }
                
                // Ranks based on number of matches with the description
                if (strpos($item_description, $search_word) != false) {
                    $name_match += 1;

                    //Adjust the ranking accordingly
                    if ($name_match > 1) {
                        $result['rank'] = $result['rank'] - 5;
                    }
                }
                if($customer_distance <= $distance){
	                $name_match += 1;
	                 $result['rank'] = $result['rank'] - (10 * $name_match);
	                 //Adjust the ranking accordingly
                    if ($name_match > 1) {
                        $result['rank'] = $result['rank'] - (10 * $name_match);
                    } else {
                        $result['rank'] = $result['rank'] - (5 * $name_match);
                    }
                }

                // Ranks based on number of matches with the company name
                if (strpos($customer_name, $search_word) != false) {
                    $name_match += 1;

                    //Adjust the ranking accordingly
                    if ($name_match > 1) {
                        $result['rank'] = $result['rank'] - 3;
                    }
                }

                $counter++;
            }
        }
        usort($sorted_results, 'rankItems');
        $counter = 0;
        $android_results = array();
        foreach ($sorted_results as &$result) {
            $image = $result['PHOTO_DIRECTORY'];
            if(strpos($image, "images") == false || $image == null) {
	            if(strpos(get_headers("https://www.utterfare.com/images/profile_pictures/" . md5($result['COMPANY_ID']) . ".png")[0], '200 OK') > -1){
		            $result['IMAGE_URL'] = "https://www.utterfare.com/images/profile_pictures/" . md5($result['COMPANY_ID']) . ".png";
	            }else{
		            $result['IMAGE_URL'] = "https://www.utterfare.com/images/290sc_images/emptyplate.png";
	            }
            } else {
                $result['IMAGE_URL'] = $result['PHOTO_DIRECTORY'];
            }
            array_push($android_results, $result);
        }
        echo json_encode($sorted_results);
        
    } else {
        echo "No Results";
    }
}

function rankItems($a, $b) {
    return $a['rank'] - $b['rank'];
}
