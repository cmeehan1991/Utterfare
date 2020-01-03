<?php
class Item_Search{
	private $terms, $location, $page, $offset, $distance, $type, $platform, $search_id, $search_analytics;
	function __construct(){
		$this->init();
	}
	
	private function init(){
		$action = filter_input(INPUT_POST, 'action');
		
		
		switch($action){
			case 'get_recommendations': 
				$this->getRecommendations();
				break;
			case 'search':
				$this->doSearch();
				break;
			case 'getSingleItem': 
				$this->getSingleItem();
				break;
			case 'get_top_items': 
				$this->getTopItems();
				break;
			case 'get_local_items': 
				$this->getLocalItems();
				break;
			case 'getExplorerItems': 
				$this->getExplorerItems();
				break;
			default: break;
		}
	}	
	
	private function getExplorerItems(){
		include 'DbConnection.php'; 
		
		$location = filter_input(INPUT_POST, 'location');
		
		// Form the search location
		$location = urlencode($location);
				
		$json = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=$location&key=AIzaSyBNOJbx_2Q5h8f0ONZ4Abf5ULE0w4B-VTc");
		$obj = json_decode($json, true);
		
		// Get the latitude and longitude
        $lat = $obj['results'][0]['geometry']['location']['lat'];
        $lng = $obj['results'][0]['geometry']['location']['lng'];
		
		$sql = "SELECT DISTINCT menu_items.item_id, menu_items.vendor_id, vendors.profile_picture"; 
		$sql .= " FROM menu_items"; 
		$sql .= " INNER JOIN vendors ON vendors.vendor_id = menu_items.vendor_id";
		$sql .= " LEFT JOIN item_reviews ON item_reviews.item_id = menu_items.item_id"; 
		$sql .= " WHERE DEGREES((ACOS(SIN(RADIANS($lat))*SIN(RADIANS(vendors.latitude))+ COS(RADIANS($lat))*COS(RADIANS(vendors.latitude))*COS(RADIANS((vendors.longitude)-($lng)))))) * 60 * 1.1515 <= '25'";
		$sql .= " GROUP BY menu_items.item_id, menu_items.vendor_id";
		$sql .= " ORDER BY rand()"; 
		$sql .= " LIMIT 25";
		
		$stmt = $conn->prepare($sql);
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
				
		$results = $stmt->fetchall();
		
		for($i = 0; $i < count($results); $i++){	
			
			if(!$this->check_image($results[$i]['primary_image'], $results[$i]['vendor_id']) && ($results[$i]['profile_picture'] == 'None' || $results[$i]['profile_picture'] == null)){
				$results[$i]['primary_image'] = "https://www.utterfare.com/assets/img/UF%20Logo.png";
			}elseif(!$this->check_image($results[$i]['primary_image'])){
				$results[$i]['primary_image'] = $results[$i]['profile_picture'];
			}else{
				$results[$i]['primary_image'] = $results[$i]['primary_image'];
			}
			
			$results[$i]['address'] =  $this->get_vendor_meta($results[$i]['vendor_id']);
		}
		
		echo json_encode($results);

	}
	
	private function getLocalItems(){
		$location = filter_input(INPUT_POST, 'location');

				
		// Form the search location
		$location = urlencode($location);
		
		$json = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=$location&key=AIzaSyBNOJbx_2Q5h8f0ONZ4Abf5ULE0w4B-VTc");
		$obj = json_decode($json, true);
		
		// Get the latitude and longitude
        $lat = $obj['results'][0]['geometry']['location']['lat'];
        $lng = $obj['results'][0]['geometry']['location']['lng'];
		//$distance = 10, $latitude = null, $longitude = null, $ppp = null, $page = 1, $offset = 0, $terms = null, $random = false
		echo $this->search(10, $lat, $lng, 8, 1, 0, null, true);
	}
	
	private function getSingleItem(){
		include 'DbConnection.php';
		$item_id = filter_input(INPUT_POST, 'item_id');
		
		$sql = "SELECT item_name, item_description, primary_image, menu_items.vendor_id, vendors.vendor_name, latitude, longitude FROM menu_items INNER JOIN vendors ON vendors.vendor_id = menu_items.vendor_id WHERE item_id = ?";
		
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(1, $item_id);
		
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		
		$result = $stmt->fetch();
		
					
		if(!$this->check_image($result['primary_image'], $result['vendor_id'])){
			$result['primary_image'] = "https://www.utterfare.com/assets/img/UF%20Logo.png";
		}else{
			$result['primary_image'] = $this->check_image($result['primary_image'], $result['vendor_id']);	
		}
		
		$result['address'] =  $this->get_vendor_meta($result['vendor_id'], '_address');
		$result['telephone'] =  $this->get_vendor_meta($result['vendor_id'], '_telephone');
		
		echo json_encode($result);
		
	}
	
	private function checkImageExists($image_url){
		if(is_array($image_url)){
			return false;
		}
		return strpos(@get_headers($image_url)[0], '200') > -1;
	}
	
	private function doSearch(){
		$distance = filter_input(INPUT_POST, 'distance');
		$location = filter_input(INPUT_POST, 'location');
		$terms = filter_Input(INPUT_POST, 'terms');
		$limit = filter_input(INPUT_POST, 'limit');
		$page = filter_input(INPUT_POST, 'page');
		$offset = filter_input(INPUT_POST, 'offset');
				
		// Form the search location
		$location = urlencode($location);
		
		$json = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=$location&key=AIzaSyBNOJbx_2Q5h8f0ONZ4Abf5ULE0w4B-VTc");
		$obj = json_decode($json, true);
		
		// Get the latitude and longitude
        $lat = $obj['results'][0]['geometry']['location']['lat'];
        $lng = $obj['results'][0]['geometry']['location']['lng'];
        
        
        echo $this->search($distance, $lat, $lng, $limit, $page, $offset, $terms, false);
	}
	
	private function getTopItems(){
		include 'DbConnection.php';
		
		$sql = "SELECT DISTINCT menu_items.item_id, menu_items.vendor_id, vendors.vendor_name, item_name, item_short_description, primary_image, SUM(item_reviews.rating) AS 'rating' , vendors.profile_picture, vendors.city, vendors.state
				FROM menu_items 
				INNER JOIN vendors ON vendors.vendor_id = menu_items.vendor_id
				LEFT JOIN item_reviews ON item_reviews.item_id = menu_items.item_id 
				GROUP BY menu_items.item_id, menu_items.vendor_id
				ORDER BY rand()
				LIMIT 3";
				
		$stmt = $conn->prepare($sql);
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
				
		$results = $stmt->fetchall();
		
		
		
		
		for($i = 0; $i < count($results); $i++){	
			
					
			if(!$this->check_image($results[$i]['primary_image'], $results[$i]['vendor_id']) && ($results[$i]['profile_picture'] == 'None' || $results[$i]['profile_picture'] == null)){
				$results[$i]['primary_image'] = "https://www.utterfare.com/assets/img/UF%20Logo.png";
			}elseif(!$this->check_image($results[$i]['primary_image'])){
				$results[$i]['primary_image'] = $results[$i]['profile_picture'];
			}else{
				$results[$i]['primary_image'] = $results[$i]['primary_image'];
			}			
			$results[$i]['address'] = $results[$i]['city'] . ', ' . $results[$i]['state'];
		}
		
		
		echo json_encode($results);
		
	}
	
	/*
	* Check if the returned image exists. 
	* If it does not exist then we will either return the vendor profile image 
	* or we will return the default utterfare logo. 
	*/
	public function check_image($image_url){
	
		$image_exists = strpos(@get_headers($image_url)[7], '200') > -1 || strpos(@get_headers($image_url)[0], '200') > -1;	

		return $image_exists;	
	}
	
	/*
	* Get top 8 recommended items based on the user's previous searches, location
	*/
	private function getRecommendations(){
		$location = filter_input(INPUT_POST, 'location');				
		$distance = 10;
		
		
		// Form the search location
		$search_location = urlencode($location);
		
		$json = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=$search_location&key=AIzaSyBNOJbx_2Q5h8f0ONZ4Abf5ULE0w4B-VTc");
		$obj = json_decode($json, true);
	
		// Get the latitude and longitude
        $lat = $obj['results'][0]['geometry']['location']['lat'];
        $lng = $obj['results'][0]['geometry']['location']['lng'];
		
		echo $this->search(10, $lat, $lng, 8, 1, 0, null, true);
	}
	
	
	/*
	* This is the primary search logic 
	*/
	private function search($distance = 10, $latitude = null, $longitude = null, $ppp = null, $page = 1, $offset = 0, $terms = null, $random = false){
		include 'DbConnection.php';
				
		$sql = "SELECT DISTINCT item_id, item_name, item_short_description, primary_image, vendors.vendor_id, vendors.vendor_name, md5(vendors.vendor_name) as 'name_hash', vendors.latitude, vendors.longitude, vendors.primary_address, vendors.secondary_address, vendors.city, vendors.state, vendors.postal_code, vendors.profile_picture
		FROM menu_items 
		INNER JOIN vendors ON vendors.vendor_id = menu_items.vendor_id
		INNER JOIN vendor_meta ON vendor_meta.vendor_id = vendors.vendor_id
		WHERE DEGREES((ACOS(SIN(RADIANS($latitude))*SIN(RADIANS(vendors.latitude))+ COS(RADIANS($latitude))*COS(RADIANS(vendors.latitude))*COS(RADIANS((vendors.longitude)-($longitude)))))) * 60 * 1.1515 <= $distance";

		if($terms){
			$split_terms = explode(' ', $terms);
			
			$sql .= " AND (item_name = '$terms' OR item_name like '%$terms%' OR item_short_description = '$terms' OR item_short_description LIKE '%$terms%' OR item_description = '$terms' OR item_description LIKE '%$terms%' OR vendor_name = '$terms' OR vendor_name LIKE '%$terms%'";
			foreach($split_terms as $term){
				$sql .= " OR (item_name = '$term' OR item_name like '%$term%' OR item_short_description = '$term' OR item_short_description LIKE '%$term%' OR item_description = '$term' OR item_description LIKE '%$term%' OR vendor_name = '$term' OR vendor_name LIKE '%$term%')";
			}
			$sql .= ")";
		}
		
		if($random){
			$sql .= " GROUP BY item_id, vendor_id ORDER BY rand()";
		}
	
		$sql .= " LIMIT $ppp OFFSET $offset";			
				
		$stmt = $conn->prepare($sql);
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		
		$results = $stmt->fetchall();
		
		for($i = 0; $i < count($results); $i++){		
			
			
			$results[$i]['address'] = $results[$i]['primary_address'];
			
			if($results[$i]['secondary_address']){
				$results[$i]['address'] .= ', ' . $results[$i]['secondary_address'];
			}
			
			$results[$i]['address'] .= ', ' . $results[$i]['city'];
			$results[$i]['address'] .= ', ' . $results[$i]['state'];
			$results[$i]['address'] .= ' ' . $results[$i]['postal_code'];
				
			
			if(!$this->check_image($results[$i]['primary_image'], $results[$i]['vendor_id']) && ($results[$i]['profile_picture'] == 'None' || $results[$i]['profile_picture'] == null)){
				$results[$i]['primary_image'] = "https://www.utterfare.com/assets/img/UF%20Logo.png";
			}elseif(!$this->check_image($results[$i]['primary_image'])){
				$results[$i]['primary_image'] = $results[$i]['profile_picture'];
			}else{
				$results[$i]['primary_image'] = $results[$i]['primary_image'];
			}
			
			
			if($terms){
				// Rank the terms
				$results[$i]['rank'] = $this->rank_item($results[$i], $terms);
			}
		}

		if($terms){
			
			// If this is an actual search we are going to sort the array by rank
			usort($results, function($a, $b){
				return $a['rank'] - $b['rank'];
			});
			
			// We nee dto reverse the array because usort orders from lowest to highest. 
			$results = array_reverse($results);
		}
				
		
		echo json_encode($results);
	}
	
	private function rank_item($item, $terms){
		$rank = 0;	
				
		if($item['item_name'] == $terms){
			$rank += 20; 
		}
		
		
		if(strpos($item['item_name'], $terms) > -1){
			$rank += 10; 
		}
		
		
		if(strpos($item['item_description'], $terms) > -1){
			$rank += 10;	
		}
		
		if(strpos($item['vendor_name'], $terms) > -1){
			$rank += 10;
		}
		
		$split_terms = explode(' ', $terms);
		
		foreach($split_terms as $term){
			
			if($item['item_name'] == $term){
				$rank += 5; 
			}
			
			if(strpos($item['item_name'], $term) !== false){
				$rank += 5; 
			}
			
			if(strpos($item['item_description'], $term) !== false){
				$rank += 1;	
			}
			
			if(strpos($item['vendor_name'], $term) !== false){
				$rank += 1;
			}
		}
		
		return $rank;
		
	}

	
	private function get_vendor_meta($vendor_id, $keyword = null){
		include 'DbConnection.php';
		
		
		$sql = "SELECT meta_keyword, meta_value FROM vendor_meta WHERE vendor_id = ?";
		
		if($keyword){
			if($keyword == '_address'){
				$sql .= " AND (meta_keyword = '_primary_address' OR meta_keyword = '_secondary_address' OR meta_keyword = '_city' OR meta_keyword = '_state' OR meta_keyword = 'postal_code')";
			}else{
				$sql .= " AND meta_keyword = ?";
			}
		}
		
		$stmt = $conn->prepare($sql);
		
		$stmt->bindParam(1, $vendor_id);
		
		if($keyword != null && $keyword != '_address'){
			$stmt->bindParam(2, $keyword);
		}
		
		$stmt->execute();
		
		$stmt->setFetchMode(PDO::FETCH_ASSOC);	
		$results = $stmt->fetchall();
		
		
		$response = array();
		if($results){
			foreach($results as $result){
				
				$response[$result['meta_keyword']] = $result['meta_value'];
			
			}
		}
			
		return json_encode($response);
		
		
	}
	
	/*
	private addRecentSearch($terms){
		if(isset($_SESSION['RECENT_SEARCHES'])){
			$recent_searches = $_SESSION['RECENT_SEARCHES'];
			
			// Check for the search
			$key = array_search($terms, $recent_searches);
			
			if($key){
				unset($recent_searches[$key]);
			}
			
			// Add the search to the top of the list. 
			array_push($recent_searches, $terms);
			
		}else{
			$_SESSION['RECENT_SEARCHES'] = array($terms);
		}
	}
	*/
	
} new Item_search();