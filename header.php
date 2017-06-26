<?php

function mainHeader() { ?>
    <nav class="hidden-xs hidden-sm navbar navbar-normal">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="index.php">Utterfare</a>
            </div>
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" arai-haspopup="true" aria-expanded="false"><img class="hidden-xs hidden-sm" src="includes/img/fast-food-99180_640.png" alt="" width="50px" height="50px"/></a>
                    <ul class="dropdown-menu">
                        <li><a href="login">Vendor Login</a></li>
                        <li><a href="">Contact Us</a></li>
                        <li><a href="">About</a></li>
                        <li><a href="">Terms</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
    <nav class="hidden-md hidden-lg navbar">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <img src="includes/img/fast-food-99180_640.png" alt="" width="40px" height="40px"/>
                </button>
                <a class="navbar-brand" href="index.php">Utterfare</a>
            </div>
            <div class="collapse navbar-collapse" id="navbar-collapse">
                <ul class="nav navbar-nav">
                    <li><a href="login">Vendor Login</a></li>
                    <li><a href="">Contact Us</a></li>
                    <li><a href="">About</a></li>
                    <li><a href="">Terms</a></li>
                </ul>
            </div>
        </div>
    </nav>

<?php
}

function userHeader() {
    ?>
    <nav class="navbar">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <img src="includes/img/fast-food-99180_640.png" alt="" width="40px" height="40px"/>
                </button>
                <a class="navbar-brand" href="index.php">Utterfare</a>
            </div>
            <div class="collapse navbar-collapse" id="navbar-collapse">
                <ul class=" hidden-md hidden-lg nav navbar-nav navbar-right">
                    <li><a href="userHome.php">Home</a></li>
                    <li><a href="">Add Items</a></li>
                    <li><a href="">Log Out</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <?php
}
