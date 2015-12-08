<?php
include "top.php";

// Begin output
print '<article>';
print '<h2>Database: ' . DATABASE_NAME . '</h2>';

//show the current persons list of bikes
print '<section id="existingBikes" class="float_left">';

if($currentUser == "bandreol" or $currentUser == "jrdyke"){
    $query  = "SELECT fldMake, fldModel, pmkSerialNumber ";
    $query .= "FROM tblBike ";
    $bikes = $thisDatabaseReader->select($query, "", 0, 0 ,0 ,0 ,false, false);
}else{
     //// Pull info for 1 customer
     $query  = "SELECT fldMake, fldModel, pmkSerialNumber ";
     $query .= "FROM tblBike ";
     $query .= "JOIN tblCustomer ON tblBike.fnkCustomerId = tblCustomer.pmkCustomerId ";
     $query .= "WHERE tblCustomer.pmkCustomerId = '" . $currentUser . "'";
     $bikes = $thisDatabaseReader->select($query, "", 1, 0 ,2 ,0 ,false, false);
}
// Store bike info in associative array

//$bikes = $thisDatabaseReader->testQuery($query, "", 1, 0 ,2 ,0 ,false, false);
$numBikes = 0;
foreach($bikes as $bike){
    $numBikes = $numBikes + 1;
}
if($numBikes > 1){
    print "<h1>Here are your bikes</h1>";
}else{
    print "<h1>Here is your bike</h1>";
}
print '<ol>';

$deleted = false;

    foreach($bikes as $bike){
        if(!$deleted){
        print '<li><a href="delete.php?id=' . $bike["pmkSerialNumber"] . '">[DELETE]</a> ';
        print $bike["fldMake"] . ' ' . $bike["fldModel"] . '</li>';  
        }
    }
    print '</ol>';

$query1  = "SELECT fldMake, fldModel, pmkSerialNumber ";
$query1 .= "FROM tblBike ";
$query1 .= "JOIN tblCustomer ON tblBike.fnkCustomerId = tblCustomer.pmkCustomerId ";
$query1 .= "WHERE tblCustomer.pmkCustomerId = '" . $currentUser . "'";

// Store bike info in associative array

//$bikes = $thisDatabaseReader->testQuery($query, "", 1, 0 ,2 ,0 ,false, false);
$bikes1 = $thisDatabaseReader->select($query1, "", 1, 0 ,2 ,0 ,false, false);

if (isset($_GET["id"])){
    $serialNumber = (string) htmlentities($_GET["id"], ENT_QUOTES, "UTF-8");
    $query = 'DELETE FROM tblBike ';
    $query .= 'WHERE pmkSerialNumber = ? ';
    $data = array($serialNumber);
    
    $results = $thisDatabaseWriter->delete($query, $data);
    unlink($serialNumber . ".jpg");
    foreach($bikes1 as $bike1){
        if($bike1["pmkSerialNumber"] == $serialNumber){
        print '<h1>' . $bike1["fldMake"] . $bike1["fldModel"] . ' removed from database</h1>';
        $deleted = true;
        }
    }    
    
}


    
print '</section>';

print '</article>';

include 'footer.php';
?>