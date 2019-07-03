<?php
include 'SearchAnalytics.php';
$location = '36.100272:-79.50047';//filter_input(INPUT_POST, 'location'); //"36.100443:-79.500472";
$limit = 1000;
$offset = 0; //filter_input(INPUT_POST, 'offset');
$distance = 10; //filter_input(INPUT_POST, 'distance');
$type = "Mobile";
$platform = "Grid";

getResults($location, $distance);


$GLOBALS['lat'] = '';
$GLOBALS['lng'] = '';

/*$search_analytics = new SearchAnalytics();
$search_analytics->save_search_terms($terms);
$search_analytics->save_search_general_information($location, $distance, "GRID SEARCH", $type, $platform);*/

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

function getResults($location, $distance) {
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

    $sql = "SELECT $items_one.ID AS 'ITEM_ID', '$data_tables[1]' as 'DATA_TABLE', $customer_one.LATITUDE AS 'LATITUDE', $customer_one.LONGITUDE AS 'LONGITUDE', $items_one.COMPANY_ID AS 'COMPANY_ID', $items_one.ITEM_IMAGE AS 'PHOTO_DIRECTORY' FROM $items_one JOIN $customer_one ON $customer_one.ID = $items_one.COMPANY_ID WHERE DEGREES((ACOS(SIN(RADIANS($lat))*SIN(RADIANS($customer_one.LATITUDE))+ COS(RADIANS($lat))*COS(RADIANS($customer_one.LATITUDE))*COS(RADIANS(($customer_one.LONGITUDE)-($lng)))))) * 60 * 1.1515 <= $distance";
    if ($data_tables[2] != 'blank_customer') {
        $sql .= " UNION ALL SELECT $items_two.ID AS 'ITEM_ID', '$data_tables[2]' as 'DATA_TABLE', $customer_two.LATITUDE AS 'LATITUDE', $customer_two.LONGITUDE AS 'LONGITUDE', $items_two.COMPANY_ID AS 'COMPANY_ID',$items_two.ITEM_IMAGE AS 'PHOTO_DIRECTORY' FROM $items_two JOIN $customer_two ON $customer_two.ID = $items_two.COMPANY_ID WHERE AND DEGREES((ACOS(SIN(RADIANS($lat))*SIN(RADIANS($customer_two.LATITUDE))+ COS(RADIANS($lat))*COS(RADIANS($customer_two.LATITUDE))*COS(RADIANS(($customer_two.LONGITUDE)-($lng)))))) * 60 * 1.1515 <= $distance";
    }
    if ($data_tables[3] != 'blank_customer') {
        $sql .= " UNION ALL SELECT $items_three.ID AS 'ITEM_ID', '$data_tables[3]' as 'DATA_TABLE', $customer_three.LATITUDE AS 'LATITUDE', $customer_three.LONGITUDE AS 'LONGITUDE', $items_three.COMPANY_ID AS 'COMPANY_ID', $items_three.ITEM_IMAGE AS 'PHOTO_DIRECTORY' FROM $items_three JOIN $customer_three ON $customer_three.ID = $items_three.COMPANY_ID WHERE AND DEGREES((ACOS(SIN(RADIANS($lat))*SIN(RADIANS($customer_three.LATITUDE))+ COS(RADIANS($lat))*COS(RADIANS($customer_three.LATITUDE))*COS(RADIANS(($customer_three.LONGITUDE)-($lng)))))) * 60 * 1.1515 <= $distance";
    } 
    
    $sql .= " ORDER BY RAND() limit 24 OFFSET $offset";
	
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $num_rows = $stmt->rowCount();
    
    if ($num_rows > 0) {
        $results = $stmt->fetchAll();
        
        //print_r($results);
        
        // Calculate the distance to each vendor from the applied location
        $distance_sorted_results = array();
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
		        array_push($distance_sorted_results, $results[$i]);
	        }
        }       
        
        $counter = 0;
        $android_results = array();
        foreach ($distance_sorted_results as &$result) {
            $image = $result['PHOTO_DIRECTORY'];
           if(!preg_match('|images\/[^a-zA-Z0-9]_images|', $images) || $image == null){
	            if(strpos(get_headers("https://www.utterfare.com/images/profile_pictures/" . md5($result['COMPANY_ID']))[0], '200 OK') > -1){
		            $result['IMAGE_URL'] = "https://www.utterfare.com/images/profile_pictures/" . md5($result['COMPANY_ID']);
	            }else{
		            $result['IMAGE_URL'] = "https://www.utterfare.com/images/290sc_images/emptyplate.png";
	            }
            } else {
                $result['IMAGE_URL'] = $result['PHOTO_DIRECTORY'];
            }
            array_push($android_results, $result);
        }
        echo json_encode($android_results);
        
    } else {
        echo "No Results";
    }
}

function rankItems($a, $b) {
    $retval = strnatcmp($a['rank'], $b['rank']);
    return $retval;
}
