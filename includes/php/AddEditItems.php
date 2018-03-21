<?php
session_start();

$action = filter_input(INPUT_POST, 'action');
switch ($action) {
    case "pagination":
        getPages();
        break;
    case "addNewItem":
        addNewItem();
        break;
    case "updateItem":
        updateItem();
        break;
    case "getItems":
        getItems();
        break;
    case "addImage":
        addImage();
        break;
    case "deleteItem":
    	deleteItem();
    	break;
    case "uploadMenuFile":
    	uploadMenuFile();
    	break;
    default:
        break;
}

function uploadMenuFile(){
	$error_message = null;
	$existing_items = null;
	$csv_file = $_FILES['file']['tmp_name'];
	$handle = fopen($csv_file, "r");
	$item_images = array();
	$item_names = array();
	$item_descriptions = array();
	if($csv_file){
		$flag = true;
		fgetcsv($handle);
		while(($data = fgetcsv($handle, 10000, ',')) != false){
			if($flag){$flag = false;}
			array_push($item_names, $data[0]);
			array_push($item_descriptions, $data[1]);
		}
		$validateEntries = validateEntries($item_names);
		if($validateEntries != "Error" && count($validateEntries) < 1){
			$i = 0;
			foreach($item_names as $item_name){
				addNewItem($item_name, $item_descriptions[$i]);
			}
		}else{
			if($validateEntries == "Error"){
				$error_message = "Error validating the entries";
			}else{
				$existing_items = $validateEntries;
			}
		}
	}	
	else{
		array_push($file_data, "None");
	}
	
	if($error_message){
		echo json_encode(array("Error" => $error_message));
		return;
	}
	
	if($existing_items){
		echo json_encode(array("Existing" => $existing_items));
		return;	
	}	
	
	echo json_encode(array("Success" => "Items Successfully Uploaded"));
	
	die();
}

/*
* Check to see if the items already exist based on the name
*/
function validateEntries($item_names){
	include 'DbConnection.php';
	$items_data_table = strtolower($_SESSION['DATA_TABLE']) . "_items";
	$sql = "SELECT ID FROM $items_data_table WHERE ITEM_NAME = :ITEM_NAME AND COMPANY_ID = :COMPANY_ID";
	$existing_items = array();
	try{
		foreach($item_names as $item_name){
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(":ITEM_NAME", $item_name);
			$stmt->bindParam(":COMPANY_ID", $_SESSION['COMPANY_ID']);
			$stmt->execute();
			$num_rows = $stmt->rowCount();
			if($num_rows > 0){
				array_push($existing_items, $item_name);
			}	
		}
	}catch(Exception $ex){
		$existing_items = "Error";
	}
	
	return $existing_items;
}

function updateItem() {
    include 'DbConnection.php';

    $item_id = filter_input(INPUT_POST, 'ID');
    $item_name = filter_input(INPUT_POST, 'itemName');
    $item_description = filter_input(INPUT_POST, 'itemDescription');
    $items_data_table = strtolower($_SESSION['DATA_TABLE']) . "_items";

    $sql = "UPDATE $items_data_table SET ITEM_NAME = :ITEM_NAME, ITEM_DESCRIPTION = :ITEM_DESCRIPTION WHERE ID = :ID";
    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":ITEM_NAME", $item_name);
        $stmt->bindparam(":ITEM_DESCRIPTION", $item_description);
        $stmt->bindParam(":ID", $item_id);
        $stmt->execute();
        
        $num_rows = $stmt->rowCount();
        if ($num_rows > 0) {
            echo "Update Succeeded";
        } else {
            echo "Update Failed";
        }
    } catch (Exception $ex) {
        echo "Error: " . $ex->getMessage();
    }
}

/*
 * This function will convert the image to a .png file if it is not already a png file as well as resize it to 400 x 400
 * 
 * @params $image, $item_id      The image that is uploaded by the user.
 * @returns $imageURL   The URL of the saved image to insert into the database
 */

function addImage() {
    include 'DbConnection.php';
    $item_id = filter_input(INPUT_POST, 'itemID');
    $image = file_get_contents($_FILES['itemImage']['tmp_name']);
    $image_name = $_FILES['itemImage']['name'];
    $extension = pathinfo($image_name, PATHINFO_EXTENSION);
    $png_image = null;
    switch ($extension) {
        case "jpg":
            $png_image = imagecreatefromjpeg($_FILES['itemImage']['tmp_name']);
            break;
        case "jpeg":
            $png_image = imagecreatefromjpeg($_FILES['itemImage']['tmp_name']);
            break;
        case "gif":
            $png_image = imagecreatefromgif($_FILES['itemImage']['tmp_name']);
            break;
        case "png":
            $png_image = imagecreatefrompng($_FILES['itemImage']['tmp_name']);
            break;
        default:
            break;
    }
    // Save the file as
    
    $directory = dirname(getcwd(), 2);    
    $file_path = "images/" . $_SESSION['DATA_TABLE'] . '_images/' . $_SESSION['COMPANY_ID'] . "_" . $item_id . ".png";
    if (imagepng($png_image, $directory . '/' . $file_path) == true) {
        echo "image saved";
    } else {
        echo "image not saved";
    }

    $data_table = $_SESSION['DATA_TABLE'] . "_items";
    $image_url = "https://www.utterfare.com/" . $file_path;

    $sql = "UPDATE $data_table SET ITEM_IMAGE = :IMAGE WHERE ID = :ITEM_ID";
    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":IMAGE", $image_url);
        $stmt->bindParam(":ITEM_ID", $item_id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo "success";
        } else {
            echo "fail";
        }
    } catch (Exception $ex) {
        echo $ex->getMessage();
    }
}

function addNewItem($item_name, $item_description) {
    include 'DbConnection.php';

    $item_name = isset($item_name) ? $item_name : filter_input(INPUT_POST, 'itemName');
    $item_description = isset($item_description) ? $item_description : filter_input(INPUT_POST, 'itemDescription');
   // $item_image = $_FILES['itemImage']['name'];

    // Data table for  the items
    $data_table = $_SESSION['DATA_TABLE'] . "_items";

    // Customer ID
    $customer_id = $_SESSION['COMPANY_ID'];

    $sql = "INSERT INTO $data_table (COMPANY_ID, ITEM_NAME, ITEM_DESCRIPTION) VALUES (:ID, :NAME, :DESCRIPTION)";
    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":ID", $customer_id);
        $stmt->bindParam(":NAME", $item_name);
        $stmt->bindParam(":DESCRIPTION", $item_description);
		$stmt->execute();
		
        $num_rows = $stmt->rowCount();

        if ($num_rows > 0) {
            return $conn->lastInsertId();
        } else {
            return "fail";
        }
    } catch (Exception $ex) {
        return $ex->getMessage();
    }
}

function getItems() {
    include 'DbConnection.php';

    $offset = isset($_POST['offset']) ? filter_input(INPUT_POST, 'offset') : null;
    $limit = isset($_POST['limit']) ? filter_input(INPUT_POST, 'limit') : null;
    $customer_id = $_SESSION["COMPANY_ID"];
    $data_table = $_SESSION['DATA_TABLE'] . "_items"; // This is only the prefix ###ST 

    $sql = "SELECT ID, ITEM_NAME, ITEM_DESCRIPTION, ITEM_IMAGE FROM $data_table WHERE COMPANY_ID = :CUSTOMER_ID ORDER BY ITEM_NAME ASC"; 
    if($limit != null){
	    $sql .= " LIMIT $limit";
	}
	if($limit != null){
		$sql .= " OFFSET $offset ";
	}
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":CUSTOMER_ID", $customer_id);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $num_rows = $stmt->rowCount();

    if ($num_rows > 0):
    	$i = 0;
        while ($results = $stmt->fetch()):
            ?>
            <tr class="<?php echo $results["ID"]; ?>" onclick="return editItem(<?php echo $results["ID"] ?>)" >
	            <td class="<?php echo $results["ID"]; ?>-item-image" width="10%">
	            <?php
		        $item_image = $results['ITEM_IMAGE'];
		       	if($item_image != null && strpos(get_headers($item_image)[0], '200 OK') > -1): 
		       	?>
		       	 <img src="<?php echo $results['ITEM_IMAGE']; ?>" alt="<?php echo $results['ITEM_NAME']; ?>" style="width:150px; height:150px;background:#d3d3d3;"/>
		       	<?php else: ?>
		       	 <img src="" alt="<?php echo $results['ITEM_NAME']; ?>" class="item-image" style="min-width:150px; min-height:150px;background:#d3d3d3;"/>
		       	<?php endif; ?>
               </td>
                <td class="<?php echo $results["ID"]; ?>-item-name" width="40%"><?php echo $results["ITEM_NAME"]; ?></td>
                <td class="<?php echo $results["ID"]; ?>-item-description" width="50%"><?php echo $results["ITEM_DESCRIPTION"]; ?></td>
            </tr>
            <?php
        endwhile;
    else:
        ?> 
        <tr>
            <td colspan="3">No Items</td>
        </tr> 
    <?php
    endif;
}

function getPages() {
    include 'DbConnection.php';

    $items_per_page = filter_input(INPUT_POST, 'itemsPerPage');
    $data_table = $_SESSION["DATA_TABLE"] . "_items";
    $customer_id = $_SESSION["COMPANY_ID"];

    $sql = "SELECT ID FROM $data_table WHERE COMPANY_ID = :CUSTOMER_ID";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":CUSTOMER_ID", $customer_id);
    $stmt->execute();
    $num_rows = $stmt->rowCount();

    if ($num_rows > 0) {
        $count = 1;
        $total_pages = ceil($num_rows / $items_per_page);
        echo "<nav aria-label='Page navigation'>";
        echo "<ul class='pagination d-inline-flex'>";
        while ($count < $total_pages + 1) {
            echo "<li class='page-item' onclick='return getPage(".$count.")' data-page='" . $count . "'><a class='page-link' href='#'>" . $count . "</a></li>";
            $count++;
        }
        echo "</ul>";
        echo "</nav>";
    }
    
}

function deleteItem(){
    include 'DbConnection.php';
    
    $item_id = filter_input(INPUT_POST, 'itemId');
    
    $data_table = $_SESSION['DATA_TABLE'] . "_items";
    $sql = "DELETE FROM $data_table WHERE ID = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":id", $item_id);
    $stmt->execute();
    $num_rows = $stmt->rowCount();
    
    if($num_rows > 0){
	    echo true;
    }else{
	    echo false;
    }
}

