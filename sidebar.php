<div class="hidden-xs hidden-sm left-nav">
    <ul class="left-nav__primary-nav">
        <li><a href="userHome.php">Home</a></li>
        <li><a href="addEditItems.php">Add/Edit Items</a></li>
        <li><a href="">View Statistics</a></li>
        <hr>
        <li><a href="">Contact Support</a></li>
        <li><a href="?signout=true">Log Out</a></li>
    </ul>
</div>
<?php 
function signOut(){
    session_unset();
    echo '<script type="text/javascript">window.location.href="login.php";</script>';
}
if(isset($_GET['signout'])){
    signOut();
}