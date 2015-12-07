<!-- ######################     Main Navigation   ########################## -->
<nav>
    
        <ol>
            <?php
            // This sets the current page to not be a link. Repeat this if block for
            //  each menu item 
            if ($path_parts['filename'] == "index") {
                print '<li class="activePage">Home</li>';
            } else {
                print '<li><a href="index.php">Home</a></li>';
            }

            //Which options will show for nav
            $currentUser = htmlentities($_SERVER["REMOTE_USER"], ENT_QUOTES, "UTF-8");
            //-- Get existing customer array
            $query = 'SELECT pmkCustomerId ' . 
                     'FROM tblCustomer ' ;
            $users = $thisDatabaseReader->select($query, "", 0, 0, 0, 0, false, false);

            //print_r($users);
            //print $currentUser;

            $exist = false;
            foreach ($users as $user) {
                //check if current user already has an account
                if($currentUser == $user[0]){
                    //They have an acct
                    $exist = true;
                }
            }        

            if($exist){
                if ($path_parts['filename'] == "browse") {
                    print '<li class="activePage">Browse Bikes</li>';
                } else {
                    print '<li><a href="browse.php">Browse Bikes</a></li>';
                }
                if ($path_parts['filename'] == "update") {
                    print '<li class="activePage">Update Record</li>';
                } else {
                    print '<li><a href="update.php">Update Record</a></li>';
                }
                if ($path_parts['filename'] == "add") {
                    print '<li class="activePage">Add Record</li>';
                } else {
                    print '<li><a href="add.php">Add Record</a></li>';
                }
                if ($path_parts['filename'] == "delete") {
                    print '<li class="activePage">Delete Record</li>';
                } else {
                    print '<li><a href="delete.php">Delete Record</a></li>';
                }
            }else{
                if ($path_parts['filename'] == "register") {
                    print '<li class="activePage">Register</li>';
                } else {
                    print '<li><a href="register.php">Register</a></li>';
                }        
                if ($path_parts['filename'] == "browse") {
                    print '<li class="activePage">Browse Bikes</li>';
                } else {
                    print '<li><a href="browse.php">Browse Bikes</a></li>';
                }            
            }
            ?>
        </ol>
    
</nav>
<!-- #################### Ends Main Navigation    ########################## -->

