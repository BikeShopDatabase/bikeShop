
<?php
    include ("top.php");
?>

    <article class='mainArt'>
        <div class='headDiv'>
            <h1 class="hHead">Registration</h1>
        </div>
<?php
    $debug = false;
    
    if (isset($_GET["debug"])) {
        $debug = true;
    }
    
    if ($debug)
        print "<p>DEBUG MODE IS ON</p>";
    
    $yourURL = $domain . $phpSelf;
    
    $firstName = "";
    $lastName = "";
    $email = "";
    $address = "";
    $netId = "";
    $phone = "";

    $mountain = true;
    $road = false;
    $bmx = false;
    $other = false;
    
    $firstNameERROR = false;
    $lastNameERROR = false;
    $emailERROR = false;
    $addressERROR = false;
    $idERROR = false;
    $phoneERROR = false;

    $errorMsg = array();

    $dataRecord = array();
    $checkRecord = "";

    $mailed=false;
    
    if (isset($_POST["btnSubmit"])) {
    
        if (!securityCheck($path_parts, $yourURL, true)) {
            print "<p>From: " . $fromPage . " should match your Url: " . $yourURL . "</p>";
            $msg = "<p>Sorry you cannot access this page. ";
            $msg.= "Security breach detected and reported</p>";
            die($msg);
        }
        
        $netId = htmlentities($_POST["txtId"],ENT_QUOTES,"UTF-8");
        $dataRecord[] = $netId;
        
        $firstName = htmlentities($_POST["txtFirstName"], ENT_QUOTES, "UTF-8");
        $dataRecord[] = $firstName;
    
        $lastName = htmlentities($_POST["txtLastName"], ENT_QUOTES, "UTF-8");
        $dataRecord[] = $lastName;
    
        $email = filter_var($_POST["txtEmail"], FILTER_SANITIZE_EMAIL);
        $dataRecord[] = $email;
        
        $address = htmlentities($_POST["txtAddress"], ENT_QUOTES, "UTF-8");
        $dataRecord[] = $address; 
        
        $phone = htmlentities($_POST["txtPhone"],ENT_QUOTES,"UTF-8");
        $dataRecord[] = $phone; 
        
        if(isset($_POST["chkMountain"])) {
            $mountain = true;
            $checkRecord .= "Mountain ";
        }
        if(isset($_POST["chkRoad"])) {
            $road = true;
            $checkRecord .= "Road ";
        }
        if(isset($_POST["chkBMX"])) {
            $bmx = true;
            $checkRecord .= "BMX ";
        }
        if(isset($_POST["chkOther"])) {
            $other = true;
            $checkRecord .= "Other ";
        }

        if ($firstName == "") {
            $errorMsg[] = "Please enter your first name";
            $firstNameERROR = true;
        } elseif (!verifyAlphaNum($firstName)) {
            $errorMsg[] = "Your first name appears to have extra character.";
            $firstNameERROR = true;
        }

        if ($lastName == "") {
            $errorMsg[] = "Please enter your last name";
            $lastNameERROR = true;
        } elseif (!verifyAlphaNum($lastName)) {
            $errorMsg[] = "Your first name appears to have extra character.";
            $lastNameERROR = true;
        }

        if ($email == "") {
            $errorMsg[] = "Please enter your email address";
            $emailERROR = true;
        } elseif (!verifyEmail($email)) {
            $errorMsg[] = "Your email address appears to be incorrect.";
            $emailERROR = true;
        }
        
        if ($address == "") {
            $errorMsg[] = "Please enter your address";
            $addressERROR = true;
        } elseif (!verifyAlphaNum($address)) {
            $errorMsg[] = "Your address appears to be incorrect.";
            $addressERROR = true;
        }
        
        if ($netId == "") {
            $errorMsg[] = "Please enter your net ID";
            $idERROR = true;
        } elseif (!verifyAlphaNum($netId)) {
            $errorMsg[] = "Your net ID appears to be incorrect.";
            $idERROR = true;
        }
        
        if ($phone == "") {
            $errorMsg[] = "Please enter your net ID";
            $phoneERROR = true;
        } elseif (!verifyAlphaNum($phone)) {
            $errorMsg[] = "Your phone number appears to be incorrect.";
            $phoneERROR = true;
        }
        
        if (!$errorMsg) {
            if ($debug){
                print "<p>Form is valid</p>";
            }

            $query = "INSERT INTO tblCustomer "
                    . "(pmkCustomerId, fldFirstName, fldLastName, fldEmail, fldAddress, fldPhone, fldType) VALUES "
                    . "(?, ?, ?, ?, ?, ?, '" . $checkRecord . "')";
            
            $results = $thisDatabaseWriter->insert($query, $dataRecord, 0, 0, 2, 0, false, false);
            
            $message = '<h2>Your Information:</h2>';
            $message_admin = '<h2>Customer Information:</h2>';
            foreach ($_POST as $key => $value) {
		  if($key != "btnSubmit"){
                     $message .= "<p>";
                     $message_admin .= "<p>";
                     $camelCase = preg_split('/(?=[A-Z])/', substr($key, 3));
                     foreach ($camelCase as $one) {
                         $message .= $one . " ";
                         $message_admin .= $one . " ";
                     } 
                     $message .= " = " . htmlentities($value, ENT_QUOTES, "UTF-8") . "</p>";
                     $message_admin .= " = " . htmlentities($value, ENT_QUOTES, "UTF-8") . "</p>";
		  }
            }
            
            $to = $email; // the person who filled out the form
            $cc = "";
            $bcc = "";
            $from = "Bike Shop <noreply@email.com>";
            // subject of mail should make sense to your form
            $todaysDate = strftime("%x");
            $subject = "Registration: " . $todaysDate;
            $mailed = sendMail_1($to, $cc, $bcc, $from, $subject, $message);
            
            $mailed_josh = sendMail_1("jrdyke@uvm.edu", $cc, $bcc, $from, $subject, $message_admin);
            $mailed_blaine = sendMail_1("bandreol@uvm.edu", $cc, $bcc, $from, $subject, $message_admin);
        }
    }
?>

    <article id="main"> 
<?php
    if (isset($_POST["btnSubmit"]) AND empty($errorMsg)) { // closing of if marked with: end body submit
        print "<div class='requestText'>";
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
        
        print $message;
        print "</div>";
    } else {
        //####################################
        //
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
?>
        <form action="<?php print $phpSelf; ?>"
              method="post"
              id="frmRegister">

            <fieldset class="wrapper">
                <legend id="formLegend">Register Today</legend>
                <p id="formText">Fill out the form to register</p>

                <fieldset class="wrapperTwo">
                    <legend>Please Complete the Following Form</legend>

                    <fieldset class="contact">
                        <legend>Contact Information</legend>
                        <label for="txtFirstName" class="required">First Name:
                            <input type="text" id="txtFirstName" name="txtFirstName"
                                   value="<?php print $firstName; ?>"
                                   tabindex="100" maxlength="45" placeholder="Enter your first name"
                                   <?php if ($firstNameERROR) print 'class="mistake"'; ?>
                                   onfocus="this.select()"
                                   autofocus>
                        </label>
                        <label for="txtLastName" class="required">Last Name:
                            <input type="text" id="txtLastName" name="txtLastName"
                                   value="<?php print $LastName; ?>"
                                   tabindex="110" maxlength="45" placeholder="Enter your last name"
                                   <?php if ($lastNameERROR) print 'class="mistake"'; ?>
                                   onfocus="this.select()"
                                   autofocus>
                        </label>
                        <label for="txtEmail" class="required">Email: 
                            <input type="text" id="txtEmail" name="txtEmail"
                                   value="<?php print $email ?>"
                                   tabindex="120" maxlength="45" placeholder="Enter a valid email address"
                                   <?php if ($emailERROR) print 'class="mistake"'; ?>
                                   onfocus="this.select()" 
                                   autofocus>
                        </label>
                        <label for="txtAddress" class="required">Address: 
                            <input type="text" id="txtAddress" name="txtAddress"
                                   value="<?php print $address ?>"
                                   tabindex="130" maxlength="45" placeholder="Enter a valid address"
                                   <?php if ($addressERROR) print 'class="mistake"'; ?>
                                   onfocus="this.select()" 
                                   autofocus>
                        </label>
                        <label for="txtId" class="required">Net ID: 
                            <input type="text" id="txtId" name="txtId"
                                   value="<?php print $netId ?>"
                                   tabindex="140" maxlength="45" placeholder="Enter a valid net ID"
                                   <?php if ($idERROR) print 'class="mistake"'; ?>
                                   onfocus="this.select()" 
                                   autofocus>
                        </label>
                        <label for="txtPhone" class="required">Phone Number: 
                            <input type="text" id="txtPhone" name="txtPhone"
                                   value="<?php print $phoneId ?>"
                                   tabindex="150" maxlength="45" placeholder="Enter a valid phone number"
                                   <?php if ($phoneERROR) print 'class="mistake"'; ?>
                                   onfocus="this.select()" 
                                   autofocus>
                        </label>
                    </fieldset>
                    <fieldset class="checkbox">
                        <legend>What bike's are you interested in?</legend>
                        <label class="checkText"><input type="checkbox"
                            id="chkMountain"
                            name="chkMountain"
                            value="Mountain Bike"
                            <?php if ($mountain) print ' checked '; ?>
                            tabindex="160">Mountain Bike</label>
                        <label class="checkText"><input type="checkbox"
                            id="chkRoad"
                            name="chkRoad"
                            value="Road Bike"
                            <?php if ($road) print ' checked '; ?>
                            tabindex="170">Road Bike</label>
                        <label class="checkText"><input type="checkbox"
                            id="chkBMX"
                            name="chkBMX"
                            value="BMX"
                            <?php if ($bmx) print ' checked '; ?>
                            tabindex="180">BMX</label>
                        <label class="checkText"><input type="checkbox"
                            id="chkOther"
                            name="chkOther"
                            value="Other"
                            <?php if ($other) print ' checked '; ?>
                            tabindex="190">Other</label>
                    </fieldset>

                </fieldset>
                    
                <fieldset class="buttons">
                    <legend></legend>
                    <input type="submit" id="btnSubmit" name="btnSubmit" value="Register" tabindex="900" class="button">
                </fieldset>
                
            </fieldset>
        </form>
<?php
    }
?>
    </article>
    </article>
    <?php include "footer.php"; ?>

    </body>
</html>
