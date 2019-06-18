<?php

echo "<html>";
echo "<head>";
echo "<script type='text/javascript' src='script.js'></script>";
echo "</head>";
echo "</html>";


If (isset($_POST['page1']) && isset($_SESSION['allresults'])) {
    If ($_POST['page1'] and $_SERVER['REQUEST_METHOD'] == "POST") {
        $firstitem = 0;
        IF ($_SESSION['totalitems'] > 9) {
            $lastitem = 9;
        }
        $Display = "YES";
    }
}
If (isset($_POST['page2']) && isset($_SESSION['allresults'])) {
    If ($_POST['page2'] and $_SERVER['REQUEST_METHOD'] == "POST") {
        $firstitem = 10;
        IF ($_SESSION['totalitems'] > 19) {
            $lastitem = 19;
        } else {
            $lastitem = $_SESSION['totalitems'];
        }
        $Display = "YES";
    }
}
If (isset($_POST['page3']) && isset($_SESSION['allresults'])) {
    If ($_POST['page3'] and $_SERVER['REQUEST_METHOD'] == "POST") {
        $firstitem = 20;
        IF ($_SESSION['totalitems'] > 29) {
            $lastitem = 29;
        } else {
            $lastitem = $_SESSION['totalitems'];
        }
        $Display = "YES";
    }
}
If (isset($_POST['page4']) && isset($_SESSION['allresults'])) {
    If ($_POST['page4'] and $_SERVER['REQUEST_METHOD'] == "POST") {
        $firstitem = 30;
        IF ($_SESSION['totalitems'] > 39) {
            $lastitem = 39;
        } else {
            $lastitem = $_SESSION['totalitems'];
        }
        $Display = "YES";
    }
}
If (isset($_POST['page5']) && isset($_SESSION['allresults'])) {
    If ($_POST['page5'] and $_SERVER['REQUEST_METHOD'] == "POST") {
        $firstitem = 40;
        IF ($_SESSION['totalitems'] > 49) {
            $lastitem = 49;
        } else {
            $lastitem = $_SESSION['totalitems'];
        }
        $Display = "YES";
    }
}

If ($Display == "YES") {

    If ($lastitem == $_SESSION['totalitems']) {
        $lastitem = $_SESSION['totalitems'] - 1;
    }

    $allresults = $_SESSION['allresults'];
    $counter = $firstitem;
    While ($counter <= $lastitem && $counter <= $_SESSION['totalitems']) {

        //retrieve image if available
        $datatable = $allresults[$counter]['photo'];
        $path = getcwd() . DIRECTORY_SEPARATOR . $datatable . "_images";
        $path = "/home1/cmeehan/public_html/" . $datatable . "_images";
        $name = $path . DIRECTORY_SEPARATOR . $allresults[$counter]['custid'] . "_" . $allresults[$counter]['itemnum'] . ".*";
        If (glob($name)) {
            foreach (glob($name) as $longname) {
                $extension = pathinfo($longname, PATHINFO_EXTENSION);
                $plate = $allresults[$counter]['custid'] . "_" . $allresults[$counter]['itemnum'] . "." . $extension;
            } //foreach 
        }//If (glob($name) 
        ELSE {
            $plate = "emptyplate.png";
        }

        echo "<tbody>";
        echo "<tr>";
        $check_add2 = trim($allresults[$counter]['add2']);
        If ($check_add2 == "") {
            $thisimage = "http://www.utterfare.com/" . $datatable . "_images//" . $plate . " ";
            echo "<tr><td><img src=" . $thisimage . " width='75' height='75' alt='' 
	    </td><td><h4>" .
            $allresults[$counter]['name'] . "</h4>" .
            $allresults[$counter]['descript'] . "</td> <td><h4>" .
            $allresults[$counter]['cname'] . "</h4>" .
            $allresults[$counter]['add1'] . "<br>" .
            $allresults[$counter]['city'] .
            $allresults[$counter]['state'] . " " .
            $allresults[$counter]['zip'] . "<br>" .
            $allresults[$counter]['phone'] . " (" . $allresults[$counter]['tier'] . ")<br><a target='_blank' href=http://" .
            $allresults[$counter]['link'] . "><font color='blue'>" .
            $allresults[$counter]['link'] . "</font></a>    " .
            //$allresults[$counter]['rank'] .
            "</td> </tr>";
        } Else {
            $thisimage = "http://www.utterfare.com/" . $datatable . "_images//" . $plate . " ";
            echo "<tr><td><img src=" . $thisimage . " width='75' height='75' alt='' 
				</td><td><h4>" .
            $allresults[$counter]['name'] . "</h4>" .
            $allresults[$counter]['descript'] . "</td> <td><h4>" .
            $allresults[$counter]['cname'] . "</h4>" .
            $allresults[$counter]['add1'] . "<br>" .
            $allresults[$counter]['add2'] . "<br>" .
            $allresults[$counter]['city'] .
            $allresults[$counter]['state'] . " " .
            $allresults[$counter]['zip'] . "<br>" .
            $allresults[$counter]['phone'] . " (" . $allresults[$counter]['tier'] . ")<br><a target='_blank' href=http://" .
            $allresults[$counter]['link'] . "><font color='blue'>" .
            $allresults[$counter]['link'] . "</font></a>    " .
            //$allresults[$counter]['rank'] .
            "</td> </tr>";
        }
        $counter++;
    }//while

    If ($_SESSION['totalitems'] < 5 && $distance < 25) {
        $thismessage = "Only a few menu items matching your search were found, try increasing the search distance for more results.";
        echo '<script>alert("' . $thismessage . '");</script>';
        echo '<script>searchform.distance.style.background="yellow"</script>';
    }
}// If $Display="YES"
//$statement->closecursor(); //can close cursor because data is in array $menus	
//echo "</table>";
?>
 