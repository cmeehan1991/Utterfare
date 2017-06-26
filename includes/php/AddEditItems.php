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
    default:
        break;
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
    $file_path = $_SESSION['DATA_TABLE'] . '_images/' . $_SESSION['ID'] . "_" . $item_id . ".png";
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

function addNewItem() {
    include 'DbConnection.php';

    $item_name = filter_input(INPUT_POST, 'itemName');
    $item_description = filter_input(INPUT_POST, 'itemDescription');
    $item_image = $_FILES['itemImage']['name'];

    // Data table for  the items
    $data_table = $_SESSION['DATA_TABLE'] . "_items";

    // Customer ID
    $customer_id = $_SESSION['ID'];

    $sql = "INSERT INTO $data_table (COMPANY_ID, ITEM_NAME, ITEM_DESCRIPTION) VALUES (:ID, :NAME, :DESCRIPTION)";
    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":ID", $customer_id);
        $stmt->bindParam(":NAME", $item_name);
        $stmt->bindParam(":DESCRIPTION", $item_description);
        $stmt->execute();

        $num_rows = $stmt->rowCount();

        if ($num_rows > 0) {
            echo $conn->lastInsertId();
        } else {
            echo "fail";
        }
    } catch (Exception $ex) {
        echo $ex->getMessage();
    }
}

function getItems() {
    include 'DbConnection.php';

    $offset = filter_input(INPUT_POST, 'offset');
    $limit = filter_input(INPUT_POST, 'limit');
    $customer_id = $_SESSION["ID"];
    $data_table = $_SESSION['DATA_TABLE'] . "_items"; // This is only the prefix ###ST 

    $sql = "SELECT ID, ITEM_NAME, ITEM_DESCRIPTION, ITEM_IMAGE FROM $data_table WHERE COMPANY_ID = :CUSTOMER_ID ORDER BY ITEM_NAME ASC LIMIT $limit OFFSET $offset ";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":CUSTOMER_ID", $customer_id);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);

    $num_rows = $stmt->rowCount();

    if ($num_rows > 0):
        while ($results = $stmt->fetch()):
            ?>
            <tr class="<?php echo $results["ID"]; ?>" onclick="return editItem(<?php echo $results["ID"] ?>)">
                <td class="<?php echo $results["ID"]; ?>-item-image"><img src="<?php echo $results['ITEM_IMAGE']; ?>" alt="<?php echo $results['ITEM_IMAGE']; ?>" style="width:100px;"/></td>
                <td class="<?php echo $results["ID"]; ?>-item-name"><?php echo $results["ITEM_NAME"]; ?></td>
                <td class="<?php echo $results["ID"]; ?>-item-description"><?php echo $results["ITEM_DESCRIPTION"]; ?></td>
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
    $customer_id = $_SESSION["ID"];

    $sql = "SELECT ID FROM $data_table WHERE COMPANY_ID = :CUSTOMER_ID";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":CUSTOMER_ID", $customer_id);
    $stmt->execute();
    $num_rows = $stmt->rowCount();

    if ($num_rows > 0) {
        $count = 1;
        $total_pages = ceil($num_rows / $items_per_page);
        echo "<nav aria-label='Page navigation'>";
        echo "<ul class='pagination'>";
        while ($count < $total_pages + 1) {
            echo "<li onclick='return getPage(".$count.")' data-page='" . $count . "'><a href='#'>" . $count . "</a></li>";
            $count++;
        }
        echo "</ul>";
        echo "</nav>";
    }
}
