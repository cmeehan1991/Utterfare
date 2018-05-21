
var offset = "0";
var page = "1";
var ppp = null;

$(document).ready(function () {
	var currPathName = window.location.pathname;
    var newItemNumber = 1;

	if(currPathName == '/addEditItems.php' || currPathName == '/addEditItems' || currPathName == '/utterfare/addEditItems'){
		getItems();
	}
	
    $('.addItemButton').on('click', function () {
        addItem(newItemNumber++);
    });

    $('.saveChangesButton').on('click', function () {
        saveChanges();
    });  
    
});


$(window).on('load', function(){
	var search = window.location.search;
	if(search){
		var page = search.split("=")[1];
		getPage(page);
	}
});

function setImage(row){
	var imageInput = $('input[name=itemImage]');
	var file = $("." + row + " input[name=itemImage]").prop('files')[0];
	var src = $(imageInput).val();
	var imageView = document.getElementsByClassName('item-image');
	if(file){
		var reader = new FileReader();
		reader.onload = function(event){
			$("." + row + " .item-image").attr('src', event.target.result);
		}
		reader.readAsDataURL(file)
	}
	return false;
}

/*
* Handle menu item file upload
*/
function uploadMenuItems(){
	$('#fileupload').trigger('click');
	$("#fileupload").change(function(){
		var file = $(this)[0].files[0];
		if(file){
			console.log("file");
			saveMenuItems(file);
			
		}
	});
	return false;
}

function saveMenuItems(file){
	
    
    var formData = new FormData();
    formData.append('action', 'uploadMenuFile');
    formData.append('file', file);
		
	$.ajax({
		url: 'includes/php/AddEditItems.php', 
		data: formData,
		cache: false,
		contentType: false, 
		processData: false,
		method: 'post',  
		success:function(response){
			console.log(response);
			var received = JSON.parse(response);
			
			if(received.Existing){
				showInformationSnackbar( "Items already exist", 10000, "OK");
				$.each(received.Existing, function(k,v){
					$('.error-message').append('<p class="error-text">' + v + ' already exists. Try adding an item with a different name or manually enter the item.</p>')
				});
			}
			
			if(received.Error){	
				showInformationSnackbar("Error uploading Items", 10000, "OK");	
			}
			
			if(received.Success){
				showInformationSnackbar("Items sucessfully uploaded", 10000, "OK");
				window.location.reload();
			}
		},
		error: function(error){
			console.log("Error: ");
			console.log(error);
		}
	});
}

function showInformationSnackbar( message, timeout, actionText){
	var notification = document.querySelector('.mdl-js-snackbar');
	var data = {
		message: message,
		actionHandler: function(event){},
		actionText: actionText,
		timeout: timeout
	};
	notification.MaterialSnackbar.showSnackbar(data);
}

function editItem(className) {
    var itemName = $("." + className + "-item-name").html();
    var itemDescription = $("." + className + "-item-description").html();
    $("." + className).removeAttr("onclick");
    $("." + className + "-item-image").append("<input type='file' name='itemImage' value='" + itemName + "' accept='.png,.jpg,.jpeg,.gif'  onchange='return setImage(" + className + ");'/>");
    $("." + className + "-item-name").html("<input type='text' name='itemName' value='" + itemName + "' class='itemNameInput'/>");
    $("." + className + "-item-description").html("<textarea cols='30' name='itemDescription' class='itemDescriptionInput'>" + itemDescription + "</textarea>");
    $("." + className).append("<td class='actions'><button onclick='return applyChanges(" + className + ")'><i class='glyphicon glyphicon-check'></i>Apply</button><br/><button onclick='return deleteItem(" + className + ")'><i class='glyphicon glyphicon-trash'></i>Delete</button></td>");
    return false;
}

function deleteItem(itemId){
	var conf = confirm("Are you sure you want to delete that item? This action cannot be undone.");
	if(conf == true){
		var data = {
			action: "deleteItem",
			"itemId": itemId
		};
		
		$.ajax({
			data: data,
			url: "includes/php/AddEditItems.php",
			method: "post", 
			success: function(results){
				if(results == true){
					getItems();
					showSnackbar("Item Successfully Deleted", "long");
				}else{
					showSnackbar("Error Removing Item", "long");
				}
			},error: function(error){
				showSnackbar("Error removing item.", "short");
			}
		});
	}
}

function applyChanges(itemID) {
    var itemImage = $("input[name='itemImage']").prop('files')[0];
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
            if (itemImage !== null) {
                uploadImage(itemID, itemImage);
                showSnackbar("Changes Saved", "short");
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
            getItems();
        }
    });
}

function addItem(newItemNumber) {
    $(".currentItems__body").prepend("<tr class='newItem' id='" + newItemNumber + "'>"
            + "<td><input type='file' name='item_image' class='item-image' accept='.png,.jpg,.jpeg,.gif' onchange='return setImage(" + newItemNumber + ");'/></td>"
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
            var newUrl = window.location.protocol + '//' + window.location.host + window.location.pathname + "?page=" + page;
		    window.history.pushState({path:newUrl}, '', newUrl);
        }
    });
}