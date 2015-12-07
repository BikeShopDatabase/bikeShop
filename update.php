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
    //$query .= "JOIN tblCustomer ON tblBike.fnkCustomerId = tblCustomer.pmkCustomerId ";
    
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
foreach($bikes as $bike){
    print '<li><a href="add.php?id=' . $bike["pmkSerialNumber"] . '&update=true">[UPDATE]</a> ';
    print $bike["fldMake"] . ' ' . $bike["fldModel"] . '</li>';    
}
print '</ol>';
print '</section>';

print '</article>';
include 'footer.php';
?>