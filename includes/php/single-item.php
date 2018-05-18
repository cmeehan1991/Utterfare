<?php 

	$item_id = '100'; //filter_input(INPUT_POST, 'item_id');
	$data_table = '290sc'; //filter_input(INPUT_POST, 'data_table');
	
	if(isset($item_id) && isset($data_table)){
		echo json_encode(get_single_item($item_id, $data_table));
	}else{
		echo "No Results";
	}
	
	function get_single_item($item_id, $data_table){
		$company_datatable = $data_table . '_customer';
		$item_datatable = $data_table . '_items';
		
		include 'DbConnection.php';
		$sql = "SELECT $company_datatable.ID AS 'COMPANY_ID', $company_datatable.COMPANY_NAME AS 'COMPANY_NAME', CONCAT($company_datatable.PRIMARY_ADDRESS, ', ', IF($company_datatable.SECONDARY_ADDRESS != '' AND $company_datatable.SECONDARY_ADDRESS != NULL, CONCAT($company_datatable.SECONDARY_ADDRESS, ', '), ''), $company_datatable.CITY, ', ', $company_datatable.STATE) AS 'ADDRESS', $company_datatable.PRIMARY_PHONE AS 'TEL', $company_datatable.COMPANY_URL AS 'URL', $item_datatable.ID AS 'ITEM_ID', $item_datatable.ITEM_NAME AS 'ITEM_NAME', $item_datatable.ITEM_DESCRIPTION AS 'DESCRIPTION', $item_datatable.ITEM_IMAGE AS 'IMAGE' FROM $item_datatable LEFT JOIN $company_datatable ON $company_datatable.ID = $item_datatable.COMPANY_ID WHERE $item_datatable.ID = :ID";
		
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(":ID", $item_id);
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$stmt->execute();
		$num_rows = $stmt->rowCount();
		
		$res = array();
		if($num_rows > 0){
			$results = $stmt->fetchAll();
			foreach($results as &$result){
				$image = $result['IMAGE'];
		        if(strpos($image, "\/images\/") == false || $image == null) {
		            if(strpos(get_headers("https://www.utterfare.com/images/profile_pictures/" . $data_table . "_profiles/" . md5($result['COMPANY_ID']) .".png")[0], '200 OK') > -1){
		                $result['IMAGE_URL'] = "https://www.utterfare.com/images/profile_pictures/" . $data_table . "_profiles/" . md5($result['COMPANY_ID']) . ".png";
	                }else{
		                $result['IMAGE_URL'] = "https://www.utterfare.com/images/placeholder.png";
	                }
	            } else {
	                $result['IMAGE_URL'] = $result['IMAGE'];
	            }
	            array_push($res, $result);	
			}
		}
		
		array_push($res, $results);
		
		return $res;
	}