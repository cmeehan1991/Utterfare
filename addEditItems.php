<?php
session_start();
if (!isset($_SESSION["ID"])) {
    header("location:login.php");
}
include 'header.php';
?>
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name='description' content='Utterfare is the premier search application for your food cravings. If you are having a craving for something specific, are new to the area, or just want to change things up some, then this is the right application for you.'>
        <meta name='keyworkds' content='food, drink, cravings, date night, lunch, dinner, supper, breakfast, brunch, menu, restaurant'>
        <meta name='author' content='CBM Web Development'>
        <!--JQuery-->
        <script src="includes/jquery/jquery-3.1.1.min.js" type="text/javascript"></script>

        <!-- Google API-->
        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false"></script> 

        <!--Bootstrap-->
        <link href="includes/bootstrap/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <script src="includes/bootstrap/bootstrap.min.js" type="text/javascript"></script>

        <!--Styles and Scripts-->
        <link href="includes/css/main.css" rel="stylesheet" type="text/css"/>
        <link href="includes/css/userMain.css" rel="stylesheet" type="text/css"/>
        <link href="includes/css/addEditItems.css" rel="stylesheet" type="text/css"/>
        <script src="includes/js/AddEditItems.js" type="text/javascript"></script>
        
        <title>Add Edit Items | Utterfare</title>
    </head>
    <body>
        <?php userHeader(); ?>
            <?php include 'sidebar.php'; ?>
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
    </div>
</body>
</html>