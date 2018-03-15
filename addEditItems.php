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
                            <td colspan="2" align="right"><button type='button' class="addItemButton"><i class="glyphicon glyphicon-plus-sign"></i>Add Item</button> <button type='button' class="saveChangesButton"><i class="glyphicon glyphicon-saved"></i>Save Changes</button>
                            
                        </tr>
                        <tr class="table-heading">
                            <td align="center">Image</td>
                            <td align="center">Name</td>
                            <td align="center">Description</td>
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
</div>
</body>
</html>