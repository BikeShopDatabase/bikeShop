<?php
    include ("top.php");
?>
    <article>
<?php
    if(isset($_GET['bikeNum'])){
        $bikeNumber = ($_GET['bikeNum']);
    }else if(isset($_POST['bikeNum'])) {
        $bikeNumber = ($_POST['bikeNum']);
    }
    $debug = false;

    $queryCustomer = 'SELECT fldFirstName, fldLastName, fldEmail '
                   . 'FROM tblCustomer '
                   . 'WHERE pmkCustomerId = "' . $currentUser . '"';
    $customerInfo = $thisDatabaseReader->select($queryCustomer, "", 1, 0, 2, 0, false, false);    
    
    $queryBike = 'SELECT fldPhoto, fldMake, fldModel, fldType, fldCondition, fldTireSize, fldDescription '
               . 'FROM tblBike '
               . 'WHERE pmkSerialNumber = "' . $bikeNumber . '"';
    $bikeInfo = $thisDatabaseReader->select($queryBike, "", 1, 0, 2, 0, false, false);
    
    $queryDonor = 'SELECT fldFirstName, fldLastName, fldEmail '
               . 'FROM tblCustomer, tblBike '
               . 'WHERE fnkCustomerId = pmkCustomerId '
               . 'AND pmkSerialNumber = "' . $bikeNumber . '"';
    $donorInfo = $thisDatabaseReader->select($queryDonor, "", 1, 1, 2, 0, false, false);

    $yourURL = $domain . $phpSelf;
    
    $bikeMessage = "";
    $to = "";
    
    $bikeMessageERROR = false;
    
    $errorMsg = array();
    
    $dataRecord = array();
    
    $mailed = false;
    
    if (isset($_POST["btnSubmit"])) {
    
        if (!securityCheck($path_parts, $yourURL, true)) {
            print "<p>From: " . $fromPage . " should match your Url: " . $yourURL . "</p>";
            $msg = "<p>Sorry you cannot access this page. ";
            $msg.= "Security breach detected and reported</p>";
            die($msg);
        }
        
        $bikeMessage = htmlentities($_POST["txtBikeMessage"],ENT_QUOTES,"UTF-8");
        $dataRecord[] = $bikeMessage;
        
        if ($bikeMessage == "") {
            $errorMsg[] = "Please enter something";
            $bikeMessageERROR = true;
        }
//        } elseif (!verifyAlphaNum($bikeMessage)) {
//            $errorMsg[] = "Your message appears to have extra character.";
//            $bikeMessageERROR = true;
//        }
        
        if(!$errorMsg){
            $message = '<h2>Customer Message:<h2>';
            $message .= '<h3>From: ' . $customerInfo[0]['fldFirstName'] . " " . $customerInfo[0]['fldLastName'] . " " . $customerInfo[0]['fldEmail'] . '</h3>';

            $message .= "<p>";
                
            $message .= $bikeMessage;
            
            $to = $donorInfo[0]['fldEmail'];//$donorInfo[0][2]; // the person who filled out the form
            $cc = "";
            $bcc = "";
            $from = $customerInfo[0]['fldEmail'];
            // subject of mail should make sense to your form
            $todaysDate = strftime("%x");
            $subject = "Sent: " . $todaysDate;
            $mailed = sendMail_1($to, $cc, $bcc, $from, $subject, $message);
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
        print "<p>To: " . $to . "</p>";
        print "<p>Mail Message:</p>";
        
        print $bikeMessage;
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
        <form action="<?php print $phpSelf . "?bikeNum=" . $bikeNumber; ?>"
            method="post"
            id="frmRegister">
            
            <fieldset class="wrapper">
                <legend id ="formLegend">Email the Bike Donor</legend>
                
                <label for="txtBikeMessage" class="required">Message:
                    <input type="text" id="txtBikeMessage" name="txtBikeMessage"
                           value="<?php print$bikeMessage ?>"
                           tabIndex="10" maxLength="160" placeholder="Enter a Message"
                           <?php if (bikeMessageERROR) print 'class="mistake"' ?>
                           onfocus="this.select()"
                           autofocus>
                </label>
            </fieldset>
            <fieldset class="buttons">
                <legend></legend>
                <input type="submit" id="btnSubmit" name="btnSubmit" value="Register" tabindex="900" class="button">
            </fieldset>
        </form>
<?php
    }
?>
    </article>
    </article>
    <?php include ("footer.php") ?>

    </body>
</html>