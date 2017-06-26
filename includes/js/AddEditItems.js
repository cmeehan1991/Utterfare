
var offset = "0";
var page = "1";
var ppp = null;
$(document).ready(function () {
    getItems();
    var newItemNumber = 1;
    $('.addItemButton').on('click', function () {
        addItem(newItemNumber++);
    });

    $('.saveChangesButton').on('click', function () {
        saveChanges();
    });

});

function editItem(className) {
    var itemName = $("." + className + "-item-name").html();
    var itemDescription = $("." + className + "-item-description").html();
    $("." + className).removeAttr("onclick");
    $("." + className + "-item-image").append("<input type='file' name='itemImage' value='" + itemName + "'/>");
    $("." + className + "-item-name").html("<input type='text' name='itemName' value='" + itemName + "'/>");
    $("." + className + "-item-description").html("<textarea cols='80' name='itemDescription'>" + itemDescription + "</textarea>");
    $("." + className).append("<td><button onclick='return applyChanges(" + className + ")'>Apply</button></td>");
    return false;
}

function applyChanges(itemID) {
    var itemImage = $("input[name='itemImage']").prop('files')[0];
    console.log(itemImage);
    var itemName = $('input[name="itemName"]').val();
    var itemDescription = $('textarea[name="itemDescription"]').val();

    var data = {
        action: "updateItem",
        "ID": itemID,
        "itemName": itemName,
        "itemDescription": itemDescription
    };
    $.ajax({
        data: data,
        url: "includes/php/AddEditItems.php",
        type: "post",
        success: function (results) {
            console.log("Results: " + results);
            if (itemImage !== null) {
                console.log('Uploading Image');
                uploadImage(itemID, itemImage);
            }
        }
    });
    return false;
}

function getItems() {
    this.ppp = $("select[name='limit']").val();
    var data = {
        'action': 'getItems',
        'limit': ppp,
        'offset': this.offset
    };
    $.ajax({
        url: 'includes/php/AddEditItems.php',
        data: data,
        method: 'post',
        success: function (results) {
            $(".currentItems__body").html(results);
            getPagination();
        }
    });
}

function getPagination() {
    var data = {
        action: 'pagination',
        'itemsPerPage': $('select[name="limit"]').val()
    };
    $.ajax({
        data: data,
        url: 'includes/php/AddEditItems.php',
        method: 'POST',
        success: function (results) {
            $('.table-footer__pagination').html(results);
        }
    });
}

function saveChanges() {
    var saveChanges = [];
    $('.newItem').each(function (index, row) {
        var itemInformation = [];
        itemInformation["ITEM_IMAGE"] = $(row).find(".item-image").prop('files')[0];
        itemInformation["ITEM_NAME"] = $(row).find(".item-name").val();
        itemInformation["ITEM_DESCRIPTION"] = $(row).find(".item-description").val();
        saveChanges.push(itemInformation);
    });


    for (var i = 0; i < saveChanges.length; i++) {
        var itemInfo = saveChanges[i];
        var data = {
            action: 'addNewItem',
            itemName: itemInfo["ITEM_NAME"],
            itemDescription: itemInfo["ITEM_DESCRIPTION"]
        };

        $.ajax({
            url: 'includes/php/AddEditItems.php',
            data: data,
            type: 'post',
            success: function (results, status, xhr) {
                if (itemInfo["ITEM_IMAGE"] != null) {
                    uploadImage(results, itemInfo["ITEM_IMAGE"]);
                }
                getItems();
            }, error: function (error) {
                console.log(error);
            }
        });
    }
}

function uploadImage(itemID, image) {
    var data = {
        action: "addImage",
        "itemID": itemID,
        "itemImage": image
    };

    var formData = new FormData();
    formData.append("action", "addImage");
    formData.append("itemID", itemID);
    formData.append("itemImage", image);
    $.ajax({
        url: 'includes/php/AddEditItems.php',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        method: 'post',
        success: function (results) {
            console.log("Results: " + results);
            getItems();
        }
    });
}

function addItem(newItemNumber) {
    $(".currentItems__body").prepend("<tr class='newItem' id='" + newItemNumber + "'>"
            + "<td><input type='file' name='item_image' class='item-image'/></td>"
            + "<td><input type='text' name='item_name' class='item-name'/></td>"
            + "<td><textarea name='item_description' class='item-description'></textarea></td>"
            + "</tr>");
}

function getPage(page) {
    this.offset = this.ppp * (page - 1);
    var data = {
        'action': 'getItems',
        'limit': ppp,
        'offset': offset
    };
    $.ajax({
        url: 'includes/php/AddEditItems.php',
        data: data,
        method: 'post',
        success: function (results) {
            $(".currentItems__body").html(results);
            getPagination();
        }
    });
}