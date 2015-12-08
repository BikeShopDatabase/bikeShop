<?php

//##############################################################################
//
// This page lists your tables and fields within your database. if you click on
// a database name it will show you all the records for that table. 
// 
// 
// This file is only for class purposes and should never be publicly live
//##############################################################################
include "top.php";

$tableName = "";

if (isset($_GET['getRecordsFor'])) {
    $tableName = $_GET['getRecordsFor'];
}

// Begin output
print '<article>';
print '<div id="home">';
    print '<p>Welcome to J&B Bikes.  Register today and connect with other bike owners looking to donate their old bikes.  ';
    print "It's quick, easy, and best of all totally free.</p>";

print '</div>';

print '</article>';
include "footer.php";
?>