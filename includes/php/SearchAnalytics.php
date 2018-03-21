<?php 
	
	$action = filter_input(INPUT_POST, 'action');
	
	switch($action){
		case 'getTermsData': 
			getTermsData();
			break;
		default:break;
	}
	
	function getTermsData(){
		$terms = array();
		
		include 'DbConnection.php';
		$sql = "SELECT TERM, TERM_COUNT FROM SEARCH_TERMS WHERE TERM != '' ORDER BY TERM_COUNT DESC LIMIT 5";
		$stmt = $conn->prepare($sql);
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		
		$num_rows = $stmt->rowCount();
		
		if($num_rows > 0){
			while($results = $stmt->fetch()){
				$terms[$results['TERM']] = $results['TERM_COUNT'];
			}
			echo json_encode($terms);
		}else{
			echo null;
		}

	}
	
	class SearchAnalytics{
		
		/*
		* This function will be run every time a user submits a search. 
		* The purpose of this function is to capture all valid search terms for analytical purposes. 
		* 
		* @param $terms String
		*/
		function save_search_terms($terms){
			include 'DbConnection.php';
			// Remove unwanted words. 
			$pattern =  "/\band|that|this|or|because|of|and|with\b/i";
			
			$clean_terms = preg_replace($pattern, '', $terms);
			
			$search_terms_array = explode(" ", $clean_terms); // This will separate the string based on spaces 
			
			foreach($search_terms_array as $term){
				// Check if the search term has been used before.
				// If it has not add it to the database, otherwise increase the count. 
				if($term != " "){
					if($this->term_exists($term) == true){
						$this->update_term($term);
					}else{
						$this->insert_term($term);
					} 
				}
			}
		}
		
		private function term_exists($term){
			include 'DbConnection.php';
			$u_term = strtoupper($term);
			$sql = "SELECT TERM FROM SEARCH_TERMS WHERE TERM = :TERM";
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(":TERM", $u_term);
			$stmt->execute();
			$row_count = $stmt->rowCount();
			if($row_count > 0){
				return true;
			}else{
				return false;
			}
		}
		
		private function update_term($term){
			include 'DbConnection.php';
			$u_term = strtoupper($term);
			$term_count_sql = "SELECT TERM_COUNT FROM SEARCH_TERMS WHERE TERM = :TERM";
			$term_count_stmt = $conn->prepare($term_count_sql);
			$term_count_stmt->bindParam(":TERM", $u_term);
			$term_count_stmt->execute();
			$term_count_stmt->setFetchMode(PDO::FETCH_ASSOC);
			
			$count = $term_count_stmt->fetch();
			
			$new_count = $count['TERM_COUNT'] + 1;
			
			$add_count = "UPDATE SEARCH_TERMS SET TERM_COUNT = :TERM_COUNT WHERE TERM = :TERM";
			
			try{
				$add_count_stmt = $conn->prepare($add_count);
				$add_count_stmt->bindParam(":TERM_COUNT", $new_count);
				$add_count_stmt->bindParam(":TERM", $term);
				$add_count_stmt->execute();
			}catch(Exception $ex){
				echo $ex->getMessage();
			}
		}
		
		function insert_term($term){
			include 'DbConnection.php';
			
			$insert_sql = "INSERT INTO SEARCH_TERMS (TERM, TERM_COUNT) VALUES(:TERM, 1)";
			try{
				$insert_stmt = $conn->prepare($insert_sql);
				$insert_stmt->bindParam(":TERM", strtoupper($term));
				$insert_stmt->execute();
			}catch(Exception $ex){
				echo "Insert Term Error: " .  $ex->getMessage();
			}
			
		}
		
		public function save_search_general_information($search_location, $search_radius, $terms, $type, $platform){
			include 'DbConnection.php';
			$sql = "INSERT INTO SEARCH_INFORMATION (SEARCH_LOCATION, SEARCH_RADIUS, SEARCH_DATE, SEARCH_TERMS, PLATFORM_TYPE, PLATFORM) VALUES (:SEARCH_LOCATION, :SEARCH_RADIUS, NOW(), :SEARCH_TERMS, :PLATFORM_TYPE, :PLATFORM)";
			try{
				$stmt = $conn->prepare($sql);
				$stmt->bindParam(":SEARCH_LOCATION", $search_location);
				$stmt->bindParam(":SEARCH_RADIUS", $search_radius);
				$stmt->bindParam(":SEARCH_TERMS", $terms);
				$stmt->bindParam(":PLATFORM_TYPE", $type);
				$stmt->bindParam(":PLATFORM", $platform);
				$stmt->execute();
			}catch(Exception $ex){
				echo "Save Search Error: " . $ex->getMessage();
			}
		}
		
		public function get_total_daily_searches(){
			include 'DbConnection.php';
			$today = date("Y-m-d");
			
			$sql = "SELECT COUNT(ID) AS 'TOTAL_SEARCHES' FROM SEARCH_INFORMATION WHERE DATE_FORMAT(SEARCH_DATE, '%Y-%m-%d') = :SEARCH_DATE";
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(":SEARCH_DATE", $today);
			$stmt->execute();
			$stmt->setFetchMode(PDO::FETCH_ASSOC);
			
			$num_rows = $stmt->rowCount();
			if($num_rows > 0){
				$results = $stmt->fetch();
				echo $results['TOTAL_SEARCHES'];
			}else{
				echo "N/A";
			}
			
		}
		
		public function get_max_min_searches_today(){
			include 'DbConnection.php';
			$sql = "SELECT MAX(SEARCH_COUNT) AS 'MAX', MIN(SEARCH_COUNT) AS 'MIN' FROM (SELECT COUNT(ID) AS 'SEARCH_COUNT' FROM SEARCH_INFORMATION WHERE DATE_FORMAT(SEARCH_DATE, '%Y-%m-%d') = DATE_FORMAT(NOW(), '%Y-%m-%d') GROUP BY DATE_FORMAT(SEARCH_DATE, '%Y-%m-%d %H')) AS SEARCH_INFORMATION_2";
			$stmt = $conn->prepare($sql);
			$stmt->execute();
			$stmt->setFetchMode(PDO::FETCH_ASSOC);
			
			$num_rows = $stmt->rowCount();
			$results = $stmt->fetch();
			if($num_rows > 0 && ($results['MAX'] != null && $results['MIN'] != null)){
				return $results['MAX'] . '/'.$results['MIN'];
			}else{
				return 'N/A';
			}
		}
		
		public function get_average_searches(){
			include 'DbConnection.php';
			$sql = "SELECT COUNT(ID) AS 'SEARCH_COUNT' FROM SEARCH_INFORMATION GROUP BY DATE_FORMAT(SEARCH_DATE, '%Y-%m-%d')";
			$stmt = $conn->prepare($sql);
			$stmt->execute();
			$stmt->setFetchMode(PDO::FETCH_ASSOC);
			
			$num_rows = $stmt->rowCount();
			
			if($num_rows > 0){
				$count = 0;
				while ($results = $stmt->fetch()){
					$count += $results['SEARCH_COUNT'];
				}
				echo round($count/$num_rows, 3);
			}else{
				echo "N/A";
			}
			
		}
		
		public function get_top_terms(){
			include 'DbConnection.php';
			$sql = "SELECT  TERM, TERM_COUNT FROM SEARCH_TERMS WHERE TERM != '' ORDER BY TERM_COUNT DESC LIMIT 5";
			$stmt = $conn->prepare($sql);
			$stmt->execute();
			$stmt->setFetchMode(PDO::FETCH_ASSOC);
			
			$num_rows = $stmt->rowCount();
			
			if($num_rows > 0){
				while($results = $stmt->fetch()){
					
					echo "<li>" . substr($results['TERM'], 0, 1) . strtolower(substr($results['TERM'],1)) . "</li>";
				};
			}else{
				echo "N/A";
			}
		}
		
	}