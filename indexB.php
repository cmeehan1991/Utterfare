<?php

$Display = "NO";
$totalitems = 0;

//This function checks the validity of the search form	
If (isset($_POST['submit'])) { //if the search button has a value, is pressed
    $Display = "YES";
    If (isset($_GET['go'])) {  // If the 'go' parameter is set
        //if (preg_match("/^[A-Za-z]+/", Trim($_POST['name']))) { // if the name contains letters 
        //    $name = $_POST['name'];
        $city = "none";
        $state = "none";
        $zipcode = "none";
        // If Geolocation is active
        If ($_POST['city'] == "Location Found" && $_POST['zip'] == '') {
            $_SESSION['newlat'] = $_POST['latitude'];
            $thislat = $_SESSION['newlat'];
            $_SESSION['newlon'] = $_POST['longitude'];
            $thislon = $_SESSION['newlon'];
            $geodistance = 100;
            $distance = $_POST['distance'];

            $sqlresult = 0;
            // get closest city within 100 miles of location
            $getcities = "select distinct zip,city,state,
						(ACOS(SIN(radians($thislat))*SIN(radians(lat))+ COS(radians($thislat))*COS(radians(lat))*
						COS(radians(lon)-radians($thislon)))*3443.89849) as distance 
						From zips 
						Where (ACOS(SIN(radians($thislat))*SIN(radians(lat))+ COS(radians($thislat))*COS(radians(lat))*
						COS(radians(lon)-radians($thislon)))*3443.89849)<$geodistance
						Order By distance";
            $statement = $db->prepare($getcities);
            $statement->execute();
            $getcities = $statement->fetch();
            $statement->closeCursor();
            $sqlresult = $statement->rowCount();
            If ($sqlresult == 0) {
                echo "<script language='javascript'>alert('Unable to search because because GeoLocation service was not allowed.  \ Enter your location 					informtaion or allow permission for Geolocation when the prompt appears.');</script>";
                include('GetLatLon.html');
                exit;
            } ELSE {
                $city = $getcities['city'];
                $state = $getcities['state'];
                $zipcode = $getcities['zip'];
                echo "<script>searchform.city.value = '$city' </script>";
                echo "<script>searchform.state.value = '$state' </script>";
                echo "<script>searchform.zip.value = '$zipcode' </script>";
            }
        } ELSE { //Geolocation not active
            If ($city == "none") {
                If (trim($_POST['city'] <> "")) {
                    $city = $_POST['city'];
                } else {
                    $city = "city";
                }
            }
            If ($state == "none") {
                If (trim($_POST['state'] <> "")) {
                    $state = $_POST['state'];
                } else {
                    $state = "state";
                }
            }
            If ($zipcode == "none") {
                If (trim($_POST['zip']) <> "") {
                    $zipcode = $_POST['zip'];
                } else {
                    $zipcode = "zipcode"; //used in search for city,state,zipcode below
                }
            }

            // select lat and lon for search zipcode and assign to $thislat and $thislon	
            // check zipcode entered against database
            $distance = $_POST['distance'];
            $sqlresult = 0;
            If ($zipcode <> "zipcode") {
                $getlatlon = "select distinct zip,city,state,lat,lon from zips where zip=$zipcode";
                $statement = $db->prepare($getlatlon);
                $statement->execute();
                $getlatlon = $statement->fetchall();
                $sqlresult = $statement->rowCount();
                If ($sqlresult == 0) {
                    $thismessage = "The zipcode entered, " . $zipcode . ", was not found!";
                    echo '<script>alert("' . $thismessage . '");</script>';
                    exit;
                }
                Foreach ($getlatlon as $lalo):
                    $thislat = $lalo['lat'];
                    $thislon = $lalo['lon'];
                ENDFOREACH;

                //Check for city:zipcode match
                If ($city <> "city" && $city <> "Located") {
                    //put all cities at this zipcode into array
                    $zipcityarray = array();
                    $anum = 1;
                    Foreach ($getlatlon as $cityzip):
                        $zipcityarray[$anum] = $cityzip['city'];
                        $anum = $anum++;
                    ENDFOREACH;
                    $citysrch = StrToUpper($city);
                    // Scan array for city name
                    If (In_array($citysrch, $zipcityarray) == 0) {
                        //city not in array for this zipcode 
                        $counter = 1;
                        While ($counter <= count($zipcityarray)) {
                            $thismessage = "The city and zipcode do not match!\\n\\n The zipcode entered is for " . $zipcityarray[$counter] .
                                    ".\\n\\n  Enter the city and state OR the zipcode.";
                            echo '<script>alert("' . $thismessage . '");</script>';
                            exit;
                        } //While
                    }
                }//If ($city<>"city")
            }//If ($zipcode <> "zipcode")
            // capture lat and lon for city and state and assign to $thislat and $thislon		
            // Check city and state against database
            If ($zipcode == "zipcode" and $city <> "city" and $state <> "state") {
                $getlatlon = "select distinct zip,city,state,lat,lon from zips 
						where upper(city)=upper('$city') and upper(state)=upper('$state')";
                $statement = $db->prepare($getlatlon);
                $statement->execute();
                $getlatlon = $statement->fetchall();
                $sqlresult = $statement->rowCount();
                If ($sqlresult == 0) {
                    $thismessage = "The city & state combination, " . $city . ", " . $state . ", was not found!";
                    echo '<script>alert("' . $thismessage . '");</script>';
                    exit;
                }




                Foreach ($getlatlon as $lalo):
                    If ($lalo['lat'] <> 0 and $lalo['lat'] <> 0) {
                        $thislat = $lalo['lat'];
                        $thislon = $lalo['lon'];
                    }
                ENDFOREACH;
            }
            If ($sqlresult == 0) {
                $thismessage = "You must enter the city and state or the zipcode.\\n\\n - OR - \\n\\n Allow permission for UTTERFARE to track your location.";
                echo '<script>alert("' . $thismessage . '");</script>';
                include ("GetLatLon.html");
                exit;
            }
        }//ELSE	Geolocation not active
        // Assign table names for use in SQL statement	
        $customer1 = 'blank_customer';
        $items1 = 'blank_items';
        $customer2 = 'blank_customer';
        $items2 = 'blank_items';
        $customer3 = 'blank_customer';
        $items3 = 'blank_items';
        // capture up to three datatables within distance indicated from this location
        $perimeter = "select distinct datatable,(ACOS(SIN(radians($thislat))*SIN(radians(lat))+ COS(radians($thislat))*COS(radians(lat))*
			COS(radians(lon)-radians($thislon)))*3443.89849) as distance 
			From zips 
			Where (ACOS(SIN(radians($thislat))*SIN(radians(lat))+ COS(radians($thislat))*COS(radians(lat))*
			COS(radians(lon)-radians($thislon)))*3443.89849)<$distance
			Order By distance";



        $statement = $db->prepare($perimeter);
        $statement->execute();
        $perimeter = $statement->fetchAll();
        $statement->closecursor();
        Foreach ($perimeter as $dat):
            $datatable = strtolower($dat['datatable']);
            If ($dat['datatable'] <> null) {
                If ($customer1 == 'blank_customer') {
                    $customer1 = $datatable . "_customer";
                    $items1 = $datatable . "_items";
                    $datatable1 = $datatable;
                    $SearchTrack = $datatable . "_srchhistory";
                }
                If ($customer2 == 'blank_customer' && $datatable <> $datatable1) {
                    $customer2 = $datatable . "_customer";
                    $items2 = $datatable . "_items";
                    $datatable2 = $datatable;
                }
                If ($customer3 == 'blank_customer' && $datatable <> $datatable1 && $datatable <> $datatable2) {
                    $customer3 = $datatable . "_customer";
                    $items3 = $datatable . "_items";
                }
            }
        ENDFOREACH;

        If (isset($_POST['name'])) {
            If (preg_match("/^[A-Za-z]+/", Trim($_POST['name']))) { // if the query name contains letters 
                $name = $_POST['name'];
            } ELSE {
                If (!isset($_POST['radio'])) {
                    $thismessage = "Please enter a menu item name or description.";
                    echo '<script>alert("' . $thismessage . '");</script>';
                    exit;
                }
            }
        } ELSE {
            $name = $_SESSION['initialname'];
        }


// This section is for roulette
        If (isset($_POST['radio'])) {
            $GetAllItems = "Select iname from $customer1 inner join $items1 on custid=icustid where
	 			(ACOS(SIN(radians($thislat))*SIN(radians(latitude))+ COS(radians($thislat))*COS(radians(latitude))*
				COS(radians(longitude)-radians($thislon)))*3443.89849)<$distance
			union Select iname from $customer2 inner join $items2 on custid=icustid where
				(ACOS(SIN(radians($thislat))*SIN(radians(latitude))+ COS(radians($thislat))*COS(radians(latitude))*
				COS(radians(longitude)-radians($thislon)))*3443.89849)<$distance
			union Select iname from $customer3 inner join $items3 on custid=icustid where
			 	(ACOS(SIN(radians($thislat))*SIN(radians(latitude))+ COS(radians($thislat))*COS(radians(latitude))*
				COS(radians(longitude)-radians($thislon)))*3443.89849)<$distance";

            $statement = $db->prepare($GetAllItems);
            $statement->execute();
            $TotalItems = $statement->rowCount();
            If ($TotalItems <> 0) {
                $ItemView = $statement->fetchAll(); // capture select results into cursor $ItemView
                $statement->closeCursor();
                //read results one line at a time and place into 2D array $AllItems
                $row = 0;
                $allItems = Array();
                Foreach ($ItemView as &$IV):
                    $allItems[$row] = $IV['iname'];
                    $row = $row + 1;
                EndForeach;
                $name = $allItems[mt_rand(1, $TotalItems)];
            }
        } //isset($_POST['radio']		
        // Check for subscribers at the selected location
        $FindCustomers = "SELECT COUNT(*) FROM $customer1 where 
			(ACOS(SIN(radians($thislat))*SIN(radians(latitude))+ COS(radians($thislat))*COS(radians(latitude))*
							COS(radians(longitude)-radians($thislon)))*3443.89849)<$distance";
        $statement = $db->prepare($FindCustomers);
        $statement->execute();
        $Findcustomer1 = $statement->fetch();
        $statement->closeCursor();
        $FindCustomers = "SELECT COUNT(*) FROM $customer2 where
		(ACOS(SIN(radians($thislat))*SIN(radians(latitude))+ COS(radians($thislat))*COS(radians(latitude))*
							COS(radians(longitude)-radians($thislon)))*3443.89849)<$distance";
        $statement = $db->prepare($FindCustomers);
        $statement->execute();
        $Findcustomer2 = $statement->fetch();
        $statement->closeCursor();
        $FindCustomers = "SELECT COUNT(*) FROM $customer3 where
		(ACOS(SIN(radians($thislat))*SIN(radians(latitude))+ COS(radians($thislat))*COS(radians(latitude))*
							COS(radians(longitude)-radians($thislon)))*3443.89849)<$distance";
        $statement = $db->prepare($FindCustomers);
        $statement->execute();
        $Findcustomer3 = $statement->fetch();
        $statement->closeCursor();
        If ($Findcustomer1[0] < 1 && $Findcustomer2[0] < 1 && $Findcustomer3[0] < 1) {
            $thismessage = "We curently do not have any subscribers near this location.\\n Try expanding your search distance, or \\n input a different location, or \\n select a location from the links below.";
            echo '<script>alert("' . $thismessage . '");</script>';
            echo '  
 		   	<table style="width:1100px; background-color:lightgrey; margin-top:75px; padding:5px;" >
			<tbody><tr><tr> <td>
			<div class="mdl-mega-footer__drop-down-section">
				<h1 style="color:navy;" class="mdl-mega-footer__heading"><b><u>North Carolina</b></u> </h1>
					<ul class="mdl-mega-footer__link-list">
						<li><a href="http://caryrestaurants.Utterfare.com/index.php"> Cary Restaurants</a></li>
						<li><a href="http://charlotterestaurants.Utterfare.com/index.php"> Charlotte Restaurants</a></li>
						<li><a href="http://chapelhillrestaurants.Utterfare.com/index.php"> Chapel Hill Restaurants</a></li>
						<li><a href="http://durhamrestaurants.Utterfare.com/index.php"> Durham Restaurants</a></li>
						<li><a href="http://fayettevillerestaurants.Utterfare.com/index.php"> Fayetteville Restaurants</a></li>
						<li><a href="http://greensbororestaurants.Utterfare.com/index.php"> Greensboro Restaurants</a></li>
						<li><a href="http://highpointrestaurants.Utterfare.com/index.php"> High Point Restaurants</a></li>
						<li><a href="http://raleighrestaurants.Utterfare.com/index.php"> Raleigh Restaurants</a></li>
						<li><a href="http://wilmingtonrestaurants.Utterfare.com/index.php"> Wilmington Restaurants</a></li>
						<li><a href="http://winstonsalemrestaurants.Utterfare.com/index.php"> Winston Salem Restaurants</a></li>
					</ul>
 			</div>
			<div class="mdl-mega-footer__drop-down-section">
                        	<h1 style="color:navy;" class="mdl-mega-footer__heading"><b><u>South Carolina</b></u> </h1>
					<ul class="mdl-mega-footer__link-list">
						<li><a href="http://hiltonheadislandrestaurants.Utterfare.com/index.php"> Beaufort Restaurants</a></li>
						<li><a href="http://charlestonrestaurants.Utterfare.com/index.php"> Charleston Restaurants</a></li>	
						<li><a href="http://columbiarestaurants.Utterfare.com/index.php"> Columbia Restaurants</a></li>	
						<li><a href="http://greenvillerestaurants.Utterfare.com/index.php"> Greenville Restaurants</a></li>
						<li><a href="http://hiltonheadislandrestaurants.Utterfare.com/index.php"> Hilton Head Island Restaurants</a></li>
                    				<li><a href="http://myrtlebeachrestaurants.Utterfare.com/index.php"> Myrtle Beach Restaurants</a></li>
                    				<li><a href="http://northcharlestonrestaurants.Utterfare.com/index.php"> North Chareston Restaurants</a></li>
					</ul>
                    	</div>
                    	<div class="mdl-mega-footer__drop-down-section">
                       		<h1 style="color:navy;" class="mdl-mega-footer__heading"><b><u>Georgia</b></u> </h1>
					<ul class="mdl-mega-footer__link-list">
					<li><a href="http://atlantarestaurants.Utterfare.com/index.php"> Atlanta Restaurants</a></li>
					<li><a href="http://augustarestaurants.Utterfare.com/index.php"> Augusta Restaurants</a></li>	
					<li><a href="http://columbusrestaurants.Utterfare.com/index.php"> Columbus Restaurants</a></li>
					<li><a href="http://maconrestaurants.Utterfare.com/index.php"> Macon Restaurants</a></li>
					<li><a href="http://savannahrestaurants.Utterfare.com/index.php"> Savannah Restaurants</a></li>
				</ul>
            </div>
			</td><td></td> </tr>
            </table>';
            exit;
        }

        //This section writes search to srchhistory table	
        If (isset($_POST['name'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
            //echo "ip=".$ip."<br>";
            $geo = unserialize(file_get_contents("http://www.geoplugin.net/php.gp?ip=$ip"));
            $iplocation = $geo["geoplugin_city"];
            $ipstate = $geo["geoplugin_region"];
            //echo "state=".$ipstate."<br>";
            $srchname = $name;
            $source = "Utterfare";
            If (isset($_POST['radio'])) {
                $srchname = 'roulette: ' . $srchname;
            }
            $savesrch = "Insert into srchhistory(city,state,zipcode,searchvalue,date,IP,IPlocation,IPstate,source) Values 									('$city','$state','$zipcode','$srchname',CURDATE(),'$ip','$iplocation','$ipstate','$source')";
            $statement = $db->prepare($savesrch);
            $statement->execute();
        }

        //remove unwanted words from $name (search query)
        $patternarray = array();
        $patternarray[0] = '/ and /';
        $patternarray[1] = '/ with /';
        $patternarray[2] = '/ the /';
        $patternarray[3] = '/ The /';
        $patternarray[4] = '/ THE /';
        $patternarray[5] = '/ & /';
        $patternarray[6] = '/ on /';
        $patternarray[7] = '/ in /';
        $patternarray[8] = '/ of /';
        $patternarray[9] = '/ to /';
        $replace = " ";
        $name = Preg_replace($patternarray, $replace, strtolower($name));

        //convert $name to uppercase
        $name = strtoupper($name);

        // assign variable names
        $kwA = "?";
        $kwB = "?";
        $kwC = "?";
        $kwD = "?";
        $kwE = "?";
        $kwF = "?";
        $kwG = "?";
        $kwH = "?";
        $kwI = "?";
        $kwJ = "?";
        $kwK = "?";
        $kwL = "?";
        $kwM = "?";
        $kwN = "?";
        $kwO = "?";
        $kwP = "?";
        $kwQ = "?";
        $kwR = "?";
        $kwS = "?";
        $kwT = "?";
        $rankA = "?";
        $rankB = "?";
        $rankC = "?";
        $rankD = "?";
        $rankE = "?";
        $rankF = "?";
        $rankG = "?";
        $rankH = "?";
        $rankI = "?";
        $rankJ = "?";
        $rankK = "?";
        $rankL = "?";
        $rankM = "?";
        $rankN = "?";
        $rankO = "?";
        $rankP = "?";
        $rankQ = "?";
        $rankR = "?";
        $rankS = "?";
        $rankT = "?";
        $allkws = trim($name) . " ";

        // capture search string length and modify words with wildcard and assign each word to a variable ($kwA..$kwT)
        $name = trim($name);
        $words = preg_split("/[\s,]+/", $name);
        If (count($words) >= 0) {
            $kwA = "%" . $words[0] . "%";
        }
        If (count($words) > 1) {
            $kwB = "%" . $words[1] . "%";
        }
        If (count($words) > 2) {
            $kwC = "%" . $words[2] . "%";
        }
        If (count($words) > 3) {
            $kwD = "%" . $words[3] . "%";
        }
        If (count($words) > 4) {
            $kwE = "%" . $words[4] . "%";
        }
        If (count($words) > 5) {
            $kwF = "%" . $words[5] . "%";
        }
        If (count($words) > 6) {
            $kwG = "%" . $words[6] . "%";
        }
        If (count($words) > 7) {
            $kwH = "%" . $words[7] . "%";
        }
        If (count($words) > 8) {
            $kwI = "%" . $words[8] . "%";
        }
        If (count($words) > 9) {
            $kwJ = "%" . $words[9] . "%";
        }
        If (count($words) > 10) {
            $kwK = "%" . $words[10] . "%";
        }
        If (count($words) > 11) {
            $kwL = "%" . $words[11] . "%";
        }
        If (count($words) > 13) {
            $kwM = "%" . $words[12] . "%";
        }
        If (count($words) > 13) {
            $kwN = "%" . $words[13] . "%";
        }
        If (count($words) > 14) {
            $kwO = "%" . $words[14] . "%";
        }
        If (count($words) > 15) {
            $kwP = "%" . $words[15] . "%";
        }
        If (count($words) > 16) {
            $kwQ = "%" . $words[16] . "%";
        }
        If (count($words) > 17) {
            $kwR = "%" . $words[17] . "%";
        }
        If (count($words) > 18) {
            $kwS = "%" . $words[18] . "%";
        }
        If (count($words) > 19) {
            $kwT = "%" . $words[19] . "%";
        }

        // place quotes around keywords (kwA...)
        $kwA_q = $db->quote($kwA);
        $kwB_q = $db->quote($kwB);
        $kwC_q = $db->quote($kwC);
        $kwD_q = $db->quote($kwD);
        $kwE_q = $db->quote($kwE);
        $kwF_q = $db->quote($kwF);
        $kwG_q = $db->quote($kwG);
        $kwH_q = $db->quote($kwH);
        $kwI_q = $db->quote($kwI);
        $kwJ_q = $db->quote($kwJ);
        $kwK_q = $db->quote($kwK);
        $kwL_q = $db->quote($kwL);
        $kwM_q = $db->quote($kwM);
        $kwN_q = $db->quote($kwN);
        $kwO_q = $db->quote($kwO);
        $kwP_q = $db->quote($kwP);
        $kwQ_q = $db->quote($kwQ);
        $kwR_q = $db->quote($kwR);
        $kwS_q = $db->quote($kwS);
        $kwT_q = $db->quote($kwT);

        $now = time();


        // search datatables for keywords
        // expdate>curdate() and removed and used in fetch statement that follows
        $search = "select custid,item,cname,iname,iphoto,idescript,link,phone,keywords,tier,irank,add1,add2,
		           city,state,zip,phone,link,expdate,latitude,longitude
			from $customer1 inner join $items1 on custid=icustid 
			where 
			(upper(keywords) like $kwA_q or upper(keywords) like $kwB_q or upper(keywords) like $kwC_q or upper(keywords) like $kwD_q
			or upper(keywords) like $kwE_q or upper(keywords) like $kwF_q or upper(keywords) like $kwG_q or upper(keywords) like $kwH_q
			or upper(keywords) like $kwI_q or upper(keywords) like $kwJ_q or upper(keywords) like $kwK_q or upper(keywords) like $kwL_q
			or upper(keywords) like $kwM_q or upper(keywords) like $kwN_q or upper(keywords) like $kwO_q or upper(keywords) like $kwP_q
			or upper(keywords) like $kwQ_q or upper(keywords) like $kwR_q or upper(keywords) like $kwS_q or upper(keywords) like $kwT_q
			or upper(idescript) like $kwA_q or upper(idescript) like $kwB_q or upper(idescript) like $kwC_q or upper(idescript) like $kwD_q
			or upper(idescript) like $kwE_q or upper(idescript) like $kwF_q or upper(idescript) like $kwG_q or upper(idescript) like $kwH_q
			or upper(idescript) like $kwI_q or upper(idescript) like $kwJ_q or upper(idescript) like $kwK_q or upper(idescript) like $kwL_q
			or upper(idescript) like $kwM_q or upper(idescript) like $kwN_q or upper(idescript) like $kwO_q or upper(idescript) like $kwP_q
			or upper(idescript) like $kwQ_q or upper(idescript) like $kwR_q or upper(idescript) like $kwS_q or upper(idescript) like $kwT_q	
			or upper(iname) like $kwA_q or upper(iname) like $kwB_q or upper(iname) like $kwC_q or upper(iname) like $kwD_q
			or upper(iname) like $kwE_q or upper(iname) like $kwF_q or upper(iname) like $kwG_q or upper(iname) like $kwH_q
			or upper(iname) like $kwI_q or upper(iname) like $kwJ_q or upper(iname) like $kwK_q or upper(iname) like $kwL_q
			or upper(iname) like $kwM_q or upper(iname) like $kwN_q or upper(iname) like $kwO_q or upper(iname) like $kwP_q
			or upper(iname) like $kwQ_q or upper(iname) like $kwR_q or upper(iname) like $kwS_q or upper(iname) like $kwT_q
			or upper(cname) like $kwA_q or upper(cname) like $kwB_q or upper(cname) like $kwC_q or upper(cname) like $kwD_q
			or upper(cname) like $kwE_q or upper(cname) like $kwF_q or upper(cname) like $kwG_q or upper(cname) like $kwH_q
			or upper(cname) like $kwI_q or upper(cname) like $kwJ_q or upper(cname) like $kwK_q or upper(cname) like $kwL_q
			or upper(cname) like $kwM_q or upper(cname) like $kwN_q or upper(cname) like $kwO_q or upper(cname) like $kwP_q
			or upper(cname) like $kwQ_q or upper(cname) like $kwR_q or upper(cname) like $kwS_q or upper(cname) like $kwT_q)				
			and (ACOS(SIN(radians(latitude))*SIN(radians($thislat))+ COS(radians(latitude))*COS(radians($thislat))*
							COS(radians($thislon)-radians(longitude)))*3443.89849)<$distance
			union all							
			select custid,item,cname,iname,iphoto,idescript,link,phone,keywords,tier,irank,add1,add2,
					city,state,zip,phone,link,expdate,latitude,longitude
			from $customer2 inner join $items2 on custid=icustid 
			where 
			(upper(keywords) like $kwA_q or upper(keywords) like $kwB_q or upper(keywords) like $kwC_q or upper(keywords) like $kwD_q
			or upper(keywords) like $kwE_q or upper(keywords) like $kwF_q or upper(keywords) like $kwG_q or upper(keywords) like $kwH_q
			or upper(keywords) like $kwI_q or upper(keywords) like $kwJ_q or upper(keywords) like $kwK_q or upper(keywords) like $kwL_q
			or upper(keywords) like $kwM_q or upper(keywords) like $kwN_q or upper(keywords) like $kwO_q or upper(keywords) like $kwP_q
			or upper(keywords) like $kwQ_q or upper(keywords) like $kwR_q or upper(keywords) like $kwS_q or upper(keywords) like $kwT_q
			or upper(idescript) like $kwA_q or upper(idescript) like $kwB_q or upper(idescript) like $kwC_q or upper(idescript) like $kwD_q
			or upper(idescript) like $kwE_q or upper(idescript) like $kwF_q or upper(idescript) like $kwG_q or upper(idescript) like $kwH_q
			or upper(idescript) like $kwI_q or upper(idescript) like $kwJ_q or upper(idescript) like $kwK_q or upper(idescript) like $kwL_q
			or upper(idescript) like $kwM_q or upper(idescript) like $kwN_q or upper(idescript) like $kwO_q or upper(idescript) like $kwP_q
			or upper(idescript) like $kwQ_q or upper(idescript) like $kwR_q or upper(idescript) like $kwS_q or upper(idescript) like $kwT_q	
			or upper(iname) like $kwA_q or upper(iname) like $kwB_q or upper(iname) like $kwC_q or upper(iname) like $kwD_q
			or upper(iname) like $kwE_q or upper(iname) like $kwF_q or upper(iname) like $kwG_q or upper(iname) like $kwH_q
			or upper(iname) like $kwI_q or upper(iname) like $kwJ_q or upper(iname) like $kwK_q or upper(iname) like $kwL_q
			or upper(iname) like $kwM_q or upper(iname) like $kwN_q or upper(iname) like $kwO_q or upper(iname) like $kwP_q
			or upper(iname) like $kwQ_q or upper(iname) like $kwR_q or upper(iname) like $kwS_q or upper(iname) like $kwT_q
			or upper(cname) like $kwA_q or upper(cname) like $kwB_q or upper(cname) like $kwC_q or upper(cname) like $kwD_q
			or upper(cname) like $kwE_q or upper(cname) like $kwF_q or upper(cname) like $kwG_q or upper(cname) like $kwH_q
			or upper(cname) like $kwI_q or upper(cname) like $kwJ_q or upper(cname) like $kwK_q or upper(cname) like $kwL_q
			or upper(cname) like $kwM_q or upper(cname) like $kwN_q or upper(cname) like $kwO_q or upper(cname) like $kwP_q
			or upper(cname) like $kwQ_q or upper(cname) like $kwR_q or upper(cname) like $kwS_q or upper(cname) like $kwT_q)
			and (ACOS(SIN(radians(latitude))*SIN(radians($thislat))+ COS(radians(latitude))*COS(radians($thislat))*
							COS(radians($thislon)-radians(longitude)))*3443.89849)<$distance
			union all
			select custid,item,cname,iname,iphoto,idescript,link,phone,keywords,tier,irank,add1,add2,
				city,state,zip,phone,link,expdate,latitude,longitude
			from $customer3 inner join $items3 on custid=icustid 
			where  
			(upper(keywords) like $kwA_q or upper(keywords) like $kwB_q or upper(keywords) like $kwC_q or upper(keywords) like $kwD_q
			or upper(keywords) like $kwE_q or upper(keywords) like $kwF_q or upper(keywords) like $kwG_q or upper(keywords) like $kwH_q
			or upper(keywords) like $kwI_q or upper(keywords) like $kwJ_q or upper(keywords) like $kwK_q or upper(keywords) like $kwL_q
			or upper(keywords) like $kwM_q or upper(keywords) like $kwN_q or upper(keywords) like $kwO_q or upper(keywords) like $kwP_q
			or upper(keywords) like $kwQ_q or upper(keywords) like $kwR_q or upper(keywords) like $kwS_q or upper(keywords) like $kwT_q
			or upper(idescript) like $kwA_q or upper(idescript) like $kwB_q or upper(idescript) like $kwC_q or upper(idescript) like $kwD_q
			or upper(idescript) like $kwE_q or upper(idescript) like $kwF_q or upper(idescript) like $kwG_q or upper(idescript) like $kwH_q
			or upper(idescript) like $kwI_q or upper(idescript) like $kwJ_q or upper(idescript) like $kwK_q or upper(idescript) like $kwL_q
			or upper(idescript) like $kwM_q or upper(idescript) like $kwN_q or upper(idescript) like $kwO_q or upper(idescript) like $kwP_q
			or upper(idescript) like $kwQ_q or upper(idescript) like $kwR_q or upper(idescript) like $kwS_q or upper(idescript) like $kwT_q	
			or upper(iname) like $kwA_q or upper(iname) like $kwB_q or upper(iname) like $kwC_q or upper(iname) like $kwD_q
			or upper(iname) like $kwE_q or upper(iname) like $kwF_q or upper(iname) like $kwG_q or upper(iname) like $kwH_q
			or upper(iname) like $kwI_q or upper(iname) like $kwJ_q or upper(iname) like $kwK_q or upper(iname) like $kwL_q
			or upper(iname) like $kwM_q or upper(iname) like $kwN_q or upper(iname) like $kwO_q or upper(iname) like $kwP_q
			or upper(iname) like $kwQ_q or upper(iname) like $kwR_q or upper(iname) like $kwS_q or upper(iname) like $kwT_q
			or upper(cname) like $kwA_q or upper(cname) like $kwB_q or upper(cname) like $kwC_q or upper(cname) like $kwD_q
			or upper(cname) like $kwE_q or upper(cname) like $kwF_q or upper(cname) like $kwG_q or upper(cname) like $kwH_q
			or upper(cname) like $kwI_q or upper(cname) like $kwJ_q or upper(cname) like $kwK_q or upper(cname) like $kwL_q
			or upper(cname) like $kwM_q or upper(cname) like $kwN_q or upper(cname) like $kwO_q or upper(cname) like $kwP_q
			or upper(cname) like $kwQ_q or upper(cname) like $kwR_q or upper(cname) like $kwS_q or upper(cname) like $kwT_q)
			and (ACOS(SIN(radians(latitude))*SIN(radians($thislat))+ COS(radians(latitude))*COS(radians($thislat))*
							COS(radians($thislon)-radians(longitude)))*3443.89849)<$distance";
        $statement = $db->prepare($search);
        $statement->execute();
        $sqlresult = $statement->rowCount();
        If ($sqlresult == 0) {
            echo "There were no matches to your search query.<br>";
            echo "Revise your query and search again.<br>";
            exit;
        }
        $webview = $statement->fetchAll(); // capture select results into cursor $webview
        $statement->closeCursor();

        // calculate distance to vendor from location
        Foreach ($webview as &$wv):
            $wv['tier'] = (ACOS(SIN(deg2rad($wv['latitude'])) * SIN(deg2rad($thislat)) + COS(deg2rad($wv['latitude'])) * COS(deg2rad($thislat)) *
                            COS(deg2rad($thislon) - deg2rad($wv['longitude']))) * 3443.89849);
            $wv['tier'] = number_format($wv['tier'], 1) . "mi.";
        EndForeach;

        // assign ranks to items in cursor webview and calculate distances[tier] to each location
        Foreach ($webview as &$wv):
            $wv['irank'] = 100;
            $skeywords = strtoupper(" " . $wv['keywords'] . " ");
            $skeywords = preg_replace('/[[:punct:]]/', ' ', $skeywords);
            $skeywords = " " . preg_replace('/\s+/', '^', $skeywords);
            $sidescript = strtoupper(" " . $wv['idescript'] . " ");
            $sidescript = preg_replace('/[[:punct:]]/', ' ', $sidescript);
            $sidescript = " " . preg_replace('/\s+/', '^', $sidescript);
            $iname = strtoupper(" " . $wv['iname'] . " ");
            $iname = preg_replace('/[[:punct:]]/', ' ', $iname);
            $iname = " " . preg_replace('/\s+/', '^', $iname);
            $cname = strtoupper(" " . $wv['cname'] . " ");
            $cname = preg_replace('/[[:punct:]]/', ' ', $cname);
            $cname = " " . preg_replace('/\s+/', '^', $cname);
            $numofwords = count($words);
            $counter = 0;
            $namematch = 0;
            While ($counter < $numofwords) {
                $justword = "^" . $words[$counter] . "^";
                //$conjugatword=$words[$counter];
                // Ranks exact word matches
                If (strpos($iname, $justword) != false) {
                    $namematch = $namematch + 1;
                    If ($namematch > 1) {
                        $wv['irank'] = $wv['irank'] - (10 * $namematch);
                    } ELSE {
                        $wv['irank'] = $wv['irank'] - (5);
                    }
                }
                If (strpos($sidescript, $justword) != false) {
                    $wv['irank'] = $wv['irank'] - 5;
                }
                If (strpos($cname, $justword) != false) {
                    $wv['irank'] = $wv['irank'] - 3;
                }
                If (strpos($skeywords, $justword) != false) {
                    $wv['irank'] = $wv['irank'] - 2;
                }
                $counter++;
            }//While ($counter<$numofwords
        EndForeach;

        //place webview in rank order
        function Rankitems($a, $b) {
            $retval = strnatcmp($a['irank'], $b['irank']);
            //if (!$retval)
            //    $retval = strnatcmp($a['tier'], $b['tier']);
            return $retval;
        }

        usort($webview, 'Rankitems');

        //read results one line at a time and place into 2D array $allresults for posting on web page
        $allresults = array();
        Foreach ($webview as &$wv):
            $eachrow = array();
            If (strtotime($wv['expdate']) > strtotime(Date('Y-m-d'))) {
                $eachrow['photo'] = $wv['iphoto'];
                $eachrow['add1'] = $wv['add1'];
                $eachrow['add2'] = $wv['add2'];
                $eachrow['city'] = $wv['city'] . ", ";
                $eachrow['state'] = Strtoupper($wv['state']);
                $eachrow['zip'] = $wv['zip'];
                $eachrow['phone'] = $wv['phone'];
                $eachrow['link'] = $wv['link'];
                $eachrow['rank'] = "[" . $wv['irank'];
                $eachrow['tier'] = $wv['tier'];
            } ELSE {
                $eachrow['photo'] = " ";
                $eachrow['add1'] = " ";
                $eachrow['add2'] = " ";
                $eachrow['city'] = " ";
                $eachrow['state'] = " ";
                $eachrow['zip'] = " ";
                $eachrow['phone'] = " ";
                $eachrow['link'] = " ";
                $eachrow['rank'] = " ";
                $eachrow['tier'] = " ";
            }
            $eachrow['itemnum'] = $wv['item'];
            $eachrow['name'] = $wv['iname'];
            $eachrow['descript'] = $wv['idescript'];
            $eachrow['cname'] = $wv['cname'];
            $eachrow['expdate'] = $wv['expdate'];
            $eachrow['custid'] = $wv['custid'];
            $allresults[] = $eachrow;
        Endforeach;

        $page = 9;
        $counter = 0;
        $totalitems = count($allresults) - 1;
        $firstitem = 0;
        $lastitem = 9;

        $_SESSION['page'] = $page;
        $_SESSION['counter'] = $counter;
        $_SESSION['totalitems'] = $totalitems;
        $_SESSION['firstitem'] = $firstitem;
        $_SESSION['lastitem'] = $lastitem;
        $_SESSION['allresults'] = $allresults;
    }//else {echo "Please enter a query";} //If (isset($_POST['submit']))
}
?>
 