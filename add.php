<?php
include "top.php";


// Begin output
print '<article>';
print '<h2>Database: ' . DATABASE_NAME . '</h2>';

//show the current persons list of bikes
print '<section id="existingBikes" class="float_left">';

print '<table>';
$columns = 5;
//// Pull info for 1 customer
$query  = "SELECT fldMake, fldModel, fldCondition, fldTireSize, fldDescription ";
$query .= "FROM tblBike ";
$query .= "JOIN tblCustomer ON tblBike.fnkCustomerId = tblCustomer.pmkCustomerId ";
$query .= "WHERE tblCustomer.pmkCustomerId = '" . $currentUser . "'";

// Store bike info in associative array

//$bikes = $thisDatabaseReader->testQuery($query, "", 1, 0 ,2 ,0 ,false, false);
$bikes = $thisDatabaseReader->select($query, "", 1, 0 ,2 ,0 ,false, false);
print '<tr><th>Make</th><th>Model</th><th>Condition</th><th>Tire Size</th><th>Description</th></tr>';
foreach($bikes as $bike){
    print '<tr class="">';
    for ($i = 0; $i < $columns; $i++) {
        print '<td>' . $bike[$i] . '</td>';
    }
    print '</tr>';
}

print '</table>';
print '</section>';

// -------------------- Form starts here -------------------- //
//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1 Initialize variables
$update = false;
$target_dir = "images/";

$newSerial = "";

if (isset($_POST["btnSubmit"])) {
    $newSerial = filter_var($_POST["txtSerialNum"], FILTER_SANITIZE_STRING);
}

$target_file = $target_dir . $newSerial . ".jpg";
//print $target_file;
$uploadOk = 1;
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);


// SECTION: 1a.
// variables for the classroom purposes to help find errors.
$debug = false;
if (isset($_GET["debug"])) { // ONLY do this in a classroom environment
    $debug = true;
}
if ($debug)
    print "<p>DEBUG MODE IS ON</p>";


//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1b Security
//
// define security variable to be used in SECTION 2a.
$yourURL = $domain . $phpSelf;

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1c form variables

// Initialize variables one for each form element
// in the order they appear on the form
if ((isset($_GET["id"])) && (isset($_GET["update"]))) {
    
    $serialNumber = (string) htmlentities($_GET["id"], ENT_QUOTES, "UTF-8");
    $update = (bool) htmlentities($_GET["update"], ENT_QUOTES, "UTF-8");
    
    $query = 'SELECT fnkCustomerId, pmkSerialNumber, fldType, fldMake, fldModel, fldPhoto, fldTireSize, fldCondition, fldDescription ';
    $query .= 'FROM tblBike ';
    $query .= 'WHERE pmkSerialNumber = ?';

    $results = $thisDatabaseReader->select($query, array($serialNumber), 1, 0, 0, 0, false, false);
    //print_r($results);    
    if($update){
        print '<p>update variable = true';
    }
    
    $email = $results[0]["fnkCustomerId"] . "@uvm.edu";
    $client = $results[0]["fnkCustomerId"];
//    $serialNumber = $results[0]["pmkSerialNumber"];
    $type = $results[0]["fldType"];
    $make = $results[0]["fldMake"];
    $model = $results[0]["fldModel"];
    $photo = $results[0]["fldPhoto"];
    $tireSize = $results[0]["fldTireSize"];
    $condition = $results[0]["fldCondition"];
    $description = $results[0]["fldDescription"];

}else{
    $email = $currentUser . "@uvm.edu";
    $client = $currentUser;
    $serialNumber = "";
    $type = "Mountain";
    $make = "";
    $model = "";
    $photo = "";
    $tireSize = "26";
    $condition = "New";
    $description = "";
}

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1d form error flags
//
// Initialize Error Flags one for each form element we validate
// in the order they appear in section 1c.
$clientERROR = false;
$serialNumberERROR = false;
$typeERROR = false;
$makeERROR = false;
$modelERROR = false;
$photoERROR = false;
$conditionERROR = false;
$tireSizeERROR = false;
$descriptionERROR = false;

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1e misc variables
//
// create array to hold error messages filled (if any) in 2d displayed in 3c.
$errorMsg = array();
//$data = array();


// used for building email message to be sent and displayed
$mailed = false;
$messageA = "";
$messageB = "";
$messageC = "";

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2 Process for when the form is submitted
//
if (isset($_POST["btnSubmit"])) {
//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2a Security
//
    if (!securityCheck($path_parts, $yourURL, true)) {
        $msg = "<p>Sorry you cannot access this page. ";
        $msg.= "Security breach detected and reported</p>";
        die($msg);
    }

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2b Sanitize (clean) data
// remove any potential JavaScript or html code from users input on the
// form. Note it is best to follow the same order as declared in section 1c.
    
    $client = filter_var($_POST["txtClient"], FILTER_SANITIZE_STRING);

    
    $serialNumber = filter_var($_POST["txtSerialNum"], FILTER_SANITIZE_STRING);
    $serialNumber1 = (string) htmlentities($_POST["hidSerialId"], ENT_QUOTES, "UTF-8");
    if($serialNumber1 != ""){
        $update = true;
    }
    
    
    
    $type = htmlentities($_POST["lstTypes"], ENT_QUOTES,"UTF-8");

    
    $make = filter_var($_POST["txtMake"], FILTER_SANITIZE_STRING);

    
    $model = filter_var($_POST["txtModel"], FILTER_SANITIZE_STRING);


    $photo = $serialNumber; 
    
    $tireSize = htmlentities($_POST["radTireSize"],ENT_QUOTES,"UTF-8");

    
    $condition = htmlentities($_POST["lstCondition"],ENT_QUOTES,"UTF-8");

    
    $description = filter_var($_POST["txtDescription"], FILTER_SANITIZE_STRING);


//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2c Validation
//
// Validation section. Check each value for possible errors, empty or
// not what we expect. You will need an IF block for each element you will
// check (see above section 1c and 1d). The if blocks should also be in the
// order that the elements appear on your form so that the error messages
// will be in the order they appear. errorMsg will be displayed on the form
// see section 3b. The error flag ($emailERROR) will be used in section 3c.
    if ($client != $currentUser) {
        $errorMsg[] = "There was an issue when logging in. Please re-log in";
        $clientERROR = true;
    }
    
    if($serialNumber == ""){
        $errorMsg[] = "Please enter the serial number of your bike located on the bottom bracket";
        $serialNumberERROR = true;
    }
    //$type
    if($make == "") {
        $errorMsg[] = "Please enter the brand of your bike";
        $makeERROR = true;
    }
    if($model == "") {
        $errorMsg[] = "Please enter the brand of your bike";
        $modelERROR = true;
    }
    
    if(isset($_POST["submit"])) {
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if($check !== false) {
            echo "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }
    }
    
    // Check if file already exists
    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }
    // Check file size
    if ($_FILES["fileToUpload"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }
    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
    
    //$condition
    if($tireSize == ""){
        $errorMsg[] = "Select a tire size";
        $tireSizeERROR = true;
    }
    //condition
    if($description == ""){
        $errorMsg[] = "Please enter a brief description of your bike";
        $descriptionERROR = true;
    }

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2d Process Form - Passed Validation
//
// Process for when the form passes validation (the errorMsg array is empty)
//
    if (!$errorMsg) {
        if ($debug)
            print "<p>Form is valid</p>";

        //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
        //
        // SECTION: 2e Save Data
        //

        $primaryKey = "";
        
        $dataEntered = false;
        
        try {
            $thisDatabaseWriter->db->beginTransaction();
            if($update){
                $query = 'UPDATE tblBike SET ';

                $query .= 'fnkCustomerId = ?, ';
                $query .= 'pmkSerialNumber = ?, ';
                $query .= 'fldType = ?, ';
                $query .= 'fldMake = ?, ';
                $query .= 'fldModel = ?, ';
                $query .= 'fldPhoto = ?, ';
                $query .= 'fldTireSize = ?, ';
                $query .= 'fldCondition = ?, ';
                $query .= 'fldDescription = ? ';
                $query .= 'WHERE pmkSerialNumber = ? ';

                
                $data = array($client, $serialNumber, $type, $make, $model, $photo, $tireSize, $condition, $description, $serialNumber);
                
                $results = $thisDatabaseWriter->update($query, $data);
                $update = false;

            }else{
                $query = 'INSERT INTO tblBike SET ';
                $query .= 'fnkCustomerId = ?, ';
                $query .= 'pmkSerialNumber = ?, ';
                $query .= 'fldType = ?, ';
                $query .= 'fldMake = ?, ';
                $query .= 'fldModel = ?, ';
                $query .= 'fldPhoto = ?, ';
                $query .= 'fldTireSize = ?, ';
                $query .= 'fldCondition = ?, ';
                $query .= 'fldDescription = ? ';

                $data = array($client, $serialNumber, $type, $make, $model, $photo, $tireSize, $condition, $description);

                $results = $thisDatabaseWriter->insert($query, $data);
                $primaryKey = $thisDatabaseWriter->lastInsert();
                if ($debug){
                    print "<p>pmk= " . $primaryKey;
                }

            }
            
            
            if ($debug){
                print "<p>sql " . $query;
                print"<p><pre>";
                print_r($data);
                print"</pre></p>";
            }

            // all sql statements are done so lets commit to our changes
            $dataEntered = $thisDatabaseWriter->db->commit();
            $dataEntered = true;
            if ($debug)
                print "<p>transaction complete ";
        } catch (PDOException $e) {
            $thisDatabase->db->rollback();
            if ($debug)
                print "Error!: " . $e->getMessage() . "</br>";
            $errorMsg[] = "There was a problem with accpeting your data please contact us directly.";
        }
        // If the transaction was successful, give success message
        if ($dataEntered) {
            if ($debug)
                print "<p>data entered now prepare keys ";
            //#################################################################
            // create a key value for confirmation

            $query = "SELECT fldDateJoined FROM tblRegister WHERE pmkRegisterId=" . $primaryKey;
            $results = $thisDatabaseWriter->select($query);

            $dateSubmitted = $results[0]["fldDateJoined"];

            $key1 = sha1($dateSubmitted);
            $key2 = $primaryKey;

            if ($debug)
                print "<p>key 1: " . $key1;
            if ($debug)
                print "<p>key 2: " . $key2;


            //#################################################################
            //
            //Put forms information into a variable to print on the screen
            //

            $messageA = '<h2>Thank you for registering a new bike</h2>';

            $messageB = "<p>Click this link to confirm your registration: ";
            $messageB .= '<a href="' . $domain . $path_parts["dirname"] . '/confirmation.php?q=' . $key1 . '&amp;w=' . $key2 . '">Confirm Registration</a></p>';
            $messageB .= "<p>or copy and paste this url into a web browser: ";
            $messageB .= $domain . $path_parts["dirname"] . '/confirmation.php?q=' . $key1 . '&amp;w=' . $key2 . "</p>";

            $messageC .= "<p><b>Make: </b><i>   " . $make . "</i></p>";
            $messageC .= "<p><b>Model: </b><i>   " . $model . "</i></p>";
            $messageC .= "<p><b>Description: </b><i>   " . $description . "</i></p>";
            
            //##############################################################
            //
            // email the form's information
            //
            $to = $currentUser . "@uvm.edu"; // the person who filled out the form
            $cc = "";
            $bcc = "";
            $from = "JB Bike Share";
            $subject = "Added your " . $make . " to our system";

            $mailed = sendMail($to, $cc, $bcc, $from, $subject, $messageA . $messageB . $messageC);
        } //data entered  
    } // end form is valid
} // ends if form was submitted.
////#############################################################################
//
// SECTION 3 Display Form
//
?>
<article id="main">
<?php
//####################################
// SECTION 3a.
//
//
//
//
// If its the first time coming to the form or there are errors we are going
// to display the form.
    if (isset($_POST["btnSubmit"]) AND empty($errorMsg)) { // closing of if marked with: end body submit
        print "<h1>Your Request has ";
        if (!$mailed) {
            print "not ";
        }
        print "been processed</h1>";
        print "<p>A copy of this message has ";
        if (!$mailed) {
            print "not ";
        }
        print "been sent</p>";
        print "<p>To: " . $email . "</p>";
        print "<p>Mail Message:</p>";
        print $messageA . $messageC;
    } else {

//####################################
// SECTION 3b Error Messages
//
// display any error messages before we print out the form
        if ($errorMsg) {
            print '<div id="errors">';
            print "<ol>\n";
            foreach ($errorMsg as $err) {
                print "<li>" . $err . "</li>\n";
            }
            print "</ol>\n";
            print '</div>';
        }
//####################################
// SECTION 3c html Form
//
        /* Display the HTML form. note that the action is to this same page. $phpSelf
          is defined in top.php
          NOTE the line:
          value="<?php print $email; ?>
          this makes the form sticky by displaying either the initial default value (line 35)
          or the value they typed in (line 84)
          NOTE this line:
          <?php if($emailERROR) print 'class="mistake"'; ?>
          this prints out a css class so that we can highlight the background etc. to
          make it stand out that a mistake happened here.
         */
   
?>
        <form action="<?php print $phpSelf; ?>"
              method="post"
              enctype="multipart/form-data"
              id="frmRegister">
            <fieldset class="wrapper">
                <legend>Add Bike</legend>
                <p>This form add's a bike do your existing donations</p>
                <input type="hidden" id="hidSerialId" name="hidSerialId"
                       value="<?php print $serialNumber; ?>"
                       >                
                <fieldset class="wrapperTwo">
                    <fieldset class="contact">
                        <legend>Bike Information</legend>
                        <label for="txtClient" class="required">Your User ID
                            <input type="text" id="txtClient" name="txtClient"
                                   value="<?php print $client; ?>"
                                   tabindex="1" maxlength="45" placeholder="Enter your user ID"
                                   <?php if ($clientERROR) print 'class="mistake"'; ?>
                                   onfocus="this.select()"
                                   >
                        </label>
                        <label for="txtSerialNum" class="required">Bike Serial Number
                            <input type="text" id="txtSerialNum" name="txtSerialNum"
                                   value="<?php print $serialNumber; ?>"
                                   tabindex="2" maxlength="45" placeholder="Enter the serial number"
                                   <?php if ($serialNumberERROR) print 'class="mistake"'; ?>
                                   onfocus="this.select()"
                                   >
                        </label>

<?php                        
//----------------- Accessing database for possible types of bikes -----------------//
// Step one: generally code is in top.php
$dbUserName = 'jrdyke_reader';
$whichPass = "r"; //flag for which one to use.
$dbName = 'JRDYKE_bike_shop';

$thisDatabase = new Database($dbUserName, $whichPass, $dbName);
// Step Two: code can be in initialize variables or where step four needs to be
$query  = "SELECT fldType ";
$query .= "FROM tblType ";
$query .= "ORDER BY pmkId";


// Step Three: code can be in initialize variables or where step four needs to be
// $buildings is an associative array
$styles = $thisDatabase->select($query, "", 0, 1, 0, 0, false, false);

print '<label for="lstTypes">Type ';
print '<select id="lstTypes" ';
print '        name="lstTypes"';
print '        tabindex="3" >';


foreach ($styles as $style) {

    print '<option ';
    if ($type == $style["fldType"])
        print " selected='selected' ";

    print 'value="' . $style["fldType"] . '">' . $style["fldType"];

    print '</option>';  
}
print '</select></label>';
?>
                        <label for="txtMake" class="required">Make
                            <input type="text" id="txtMake" name="txtMake"
                                   value="<?php print $make; ?>"
                                   tabindex="120" maxlength="45" placeholder="Enter the make"
                                   <?php if ($makeERROR) print 'class="mistake"'; ?>
                                   onfocus="this.select()"
                                   >
                        </label>
                        <label for="txtModel" class="required">Model
                            <input type="text" id="txtModel" name="txtModel"
                                   value="<?php print $model; ?>"
                                   tabindex="121" maxlength="45" placeholder="Enter the model"
                                   <?php if ($modelERROR) print 'class="mistake"'; ?>
                                   onfocus="this.select()"
                                   >
                        </label>
<fieldset class="radio">
    <legend>Pick your Tire Diameter:</legend>

    <label for="radSmall">
        <input type="radio" 
               id="radTireSizeSmall" 
               name="radTireSize"
               value="26"
               tabindex="200"
               <?php if($tireSize =="26") echo ' checked="checked" ';?>>26"</label>

    <label for="radLarge">
        <input type="radio" 
               id="radTireSizeLarge" 
               name="radTireSize" 
               value="29" 
               tabindex="201"
               <?php if($tireSize == "29") echo ' checked="checked" ';?>>29"</label>
    
    <label for="radOther">
        <input type="radio" 
               id="radTireSizeOther" 
               name="radTireSize" 
               value="Other" 
               tabindex="202"
               <?php if($tireSize == "Other") echo ' checked="checked" ';?>>Other</label>
                        
<label for="lstCondition" id="labelCondition">Condition
<select id="lstCondition"
        name="lstCondition"
        tabindex="250" >
  
    <option value="New">New</option>
    <option value="Used" selected="selected">Used</option>
    <option value="Needs Maintenance">Needs Maintenance</option>

</select></label>
</fieldset>
                <fieldset class="description">
                        <label for="txtDescription" class="required">Description
                            <input type="text" id="txtDescription" name="txtDescription"
                                   value="<?php print $description; ?>"
                                   tabindex="300" maxlength="160" placeholder="Enter brief description"
                                   <?php if ($descriptionERROR) print 'class="mistake"'; ?>
                                   onfocus="this.select()"
                                   >
                        </label>
                </fieldset>

                    </fieldset> <!-- ends contact -->
                </fieldset> <!-- ends wrapper Two -->
                <fieldset class="image">
                    <input type="file" name="fileToUpload" id="fileToUpload" value="<?php print $photo; ?>" tabindex="800">
                    <!--<input type="submit" value="Upload Image" name="submit">-->
                <fieldset class="buttons">
                    <legend></legend>
                    <input type="submit" id="btnSubmit" name="btnSubmit" value="Register" tabindex="900" class="button">
                </fieldset> <!-- ends buttons -->
            </fieldset> <!-- Ends Wrapper -->
        </form>
        <?php
    } // end body submit
    ?>
</article>    
<?php
// -------------------- Form ends here -------------------- //


include 'footer.php';
?>