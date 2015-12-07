<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include ("top.php");



$query = 'SELECT fldPhoto, fldMake, fldModel, fldType, fldCondition, fldDescription '
        . 'FROM tblBike';
    $info2 = $thisDatabaseReader->select($query, "", 0, 0, 0, 0, false, false);
 
$bikeQuery = 'SELECT pmkSerialNumber FROM tblBike';
    $bikeInfo = $thisDatabaseReader->select($bikeQuery, "", 0, 0, 0, 0, false, false);
    
    $columns = 6;
    $count = 0;
    
    print '<h2>Total Records: ' . count($info2) . '</h2>';
    print '<h3>SQL: ' . $query . '</h3>';
    print"<article class='browseClass'>";
    foreach ($info2 as $rec) {
        $link = "viewBike.php?bikeNum=" . $bikeInfo[$count][0];
        
        print "<section class='bikeClass'>";
        
        for ($i = 0; $i < $columns; $i++) { 
            if($i == 0){
                ?>
                <img src="images/<?php print $rec[0] . ".jpg"; ?> "/>
                <?php
            }else{
                print "<p>" . $rec[$i] . "</p> ";
            }
        }
        print "<a href =" . $link . ">View Bike</a>";
        print "</section>";
        $count++;
    }
    print "</article>";
    
    
include ("footer.php");