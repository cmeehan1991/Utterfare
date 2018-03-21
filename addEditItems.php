<?php
session_start();
if (!isset($_SESSION["USER_ID"])) {
    header("location:login.php");
}
include 'header.php';
?>
<!DOCTYPE html>
<?php userHeader('Add Edit Items'); ?>
<div class="main">
    <div class="container-fluid">
        <div class="row">
	        <div class='row'>
		        <div class="col-md-12">
			        <div class="error-message">
				        
			        </div>
		        </div>
	        </div>
            <div class="col-md-12">
                <table class='currentItems'>
                    <thead>
                        <tr><td align="left"><label for="limit">Items per Page:</label>
                                <select name="limit" onchange="return getItems()">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="">View All</option>
                                </select>
                            </td>
                            <td colspan="2" align="right">
	                            <input id="fileupload" type="file" hidden="true" accept=".csv"/>
	                            <button type="file" class="uploadMenuButton mdl-button mdl-js-button mdl-js-ripple-effect" onclick="return uploadMenuItems();"><i class="fa fa-cloud-upload" aria-hidden="true"></i> Upload Menu Items</button>
	                            <button type='button' class="addItemButton mdl-button mdl-js-button mdl-js-ripple-effect "><i class="fa fa-plus-circle" aria-hidden="true"></i> Add Item</button> 
	                            <button type='button' class="saveChangesButton mdl-button mdl-js-button mdl-js-ripple-effect"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save Changes</button>
                            
                        </tr>
                        <tr class="table-heading">
                            <td align="left" width="10%">Image</td>
                            <td align="left" width="45%">Name</td>
                            <td align="left" width="50%">Description</td>
                        </tr>
                    </thead>
                    <tbody class="currentItems__body">
                        
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="table-footer__pagination" colspan="3" align="center"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
<div id="snackbar"></div>
<div id="error-toast" class="mdl-js-snackbar mdl-snackbar"><div class="mdl-snackbar__text"></div><button class="mdl-snackbar__action" type="button"></button></div>
<input type="file" onchange="return setImage();"/>
</div>
</body>
</html>