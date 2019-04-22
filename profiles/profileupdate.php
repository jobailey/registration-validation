<?php

$pagename = "Update Profile";
include_once "header.inc.php";
checkLogin();

// set initial values
$showform = 1;  // form is shown
$errmsg = 0;
$errusername = "";
$erremail = "";
$errfname = "";
$errbio = "";

if($_SERVER['REQUEST_METHOD'] == "GET" && isset($_GET['ID']))
{
    $ID = $_GET['ID'];
}
elseif($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['ID']))
{
    $ID = $_POST['ID'];
}
else
{
    echo "<p class='error'>Something happened!  Cannot obtain the correct entry.</p>";
    $errormsg = 1;
}

if($_SERVER['REQUEST_METHOD'] == "POST")
{
    /* user data sanitized with trim() for user-typed data */
    /* strtolower() for case-sensitive info */
    $formdata['username'] = trim($_POST['username']);
    $formdata['email'] = trim($_POST['email']);
    $formdata['fname'] = trim($_POST['fname']);
    $formdata['bio'] = trim($_POST['bio']);
    $formdata['genre'] = trim($_POST['genre']);

     /* Check empty field */

    if (empty($formdata['username'])){
        $errusername = "A new username must be selected.";
        $errmsg = 1;
    }
    if (empty($formdata['email'])){
        $erremail = "A new email must be selected.";
        $errmsg = 1;
    }

    //Check for duplicate usernames
    if($formdata['username'] != $_POST['origusername'])
    {
        $sql = "SELECT * FROM profiles WHERE username = ?";
        $count = checkDuplicates($pdo, $sql, $formdata['username']);
        if($count > 0){
            $errmsg = 1;
            $errusername = "The username is already taken.";
        }
    }

    //Check for duplicate emails
    if($formdata['email'] != $_POST['origemail'])
    {
        $sql = "SELECT * FROM profiles WHERE email = ?";
        $count = checkDuplicates($pdo, $sql, $formdata['email']);
        if($count > 0){
            $errmsg = 1;
            $erremail = "The email is already taken.";
        }
    }

    /* error handling */
    if($errmsg == 1)
    {
        echo "<p class='error'>There are errors.  Please make corrections and resubmit.</p>";
    }
    else{

        /* sensitive data hashed */
        $hashedpwd = password_hash($formdata['password'], PASSWORD_BCRYPT);

        /* Update  database */

        try{
            $sql = "UPDATE profiles 
                    SET username = :username, fname = :fname, email = :email, bio = :bio, genre = :genre
                    WHERE ID = :ID";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':username', $formdata['username']);
            $stmt->bindValue(':fname', $formdata['fname']);
            $stmt->bindValue(':email', $formdata['email']);
            $stmt->bindValue(':bio', $formdata['bio']);
            $stmt->bindValue(':genre', $formdata['genre']);
            $stmt->bindValue(':updatedate', $rightnow);
            $stmt->bindValue(':ID', $_SESSION['ID']);
            $stmt->execute();

            $showform = 0; //hide the form
            header("Location: logout.php?state=3");
        }
        catch (PDOException $e)
        {
            die( $e->getMessage() );
        }
    } // else errormsg
}//submit

//display form if Show Form Flag is true
if($showform == 1)
{
    $sql = "SELECT * FROM members WHERE ID = :ID";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':ID', $ID); // VARIABLE WE CREATED
    $stmt->execute();
    $row = $stmt->fetch();
?>
<form name="profileupdate" id="profileupdate" method="post" action="profileupdate.php">
    <table>
        <tr><th><label for="fname">First Name:</label><span class="error">*</span></th>
            <td>
                <input name="fname" id="fname" type="text" placeholder="Required first name" value="<?php
                       if(isset($formdata['fname']) && !empty($formdata['fname'])){echo $formdata['fname'];}else{echo $row['fname'];}?>"/>
                <span class="error"><?php if(isset($errfname)){echo $errfname;}?></span></td>
        </tr>
        <tr><th><label for="username">Username:</label><span class="error">*</span></th>
            <td><input name="username" id="username" type="text" size="40" placeholder="Required username"
                       value="<?php if(isset($formdata['username']) && !empty($formdata['username'])){echo $formdata['username'];
                       } else {echo $row['username'];}?>"/>
                <span class="error"><?php if(isset($errusername)){echo $errusername;}?></span></td>
        </tr>
        <tr><th><label for="email">Email:</label><span class="error">*</span></th>
            <td><input name="email" id="email" type="email" size="40" placeholder="Required email"
                       value="<?php if(isset($formdata['email']) && !empty($formdata['email'])){echo $formdata['email'];
                       } else {echo $row['email'];}?>"/>
                <span class="error"><?php if(isset($erremail)){echo $erremail;}?></span>
            </td>
        </tr>
        <tr><th><label for="bio">Bio:</label><span class="error">*</span></th>
            <td><span class="error"><?php if(isset($errbio)){echo $errbio;}?></span>
                <textarea name="bio" id="bio" placeholder="Required bio"><?php
                    if(isset($formdata['bio']) && !empty($formdata['bio'])){echo $formdata['bio'];}else{echo $row['bio'];}?></textarea>
            </td>
        </tr>
        <tr><th><label for="genre">Favorite Genre:</label><span class="error">*</span></th>
            <td><select name="genre" id="genre">
                    <option value=''
                        <?php if(isset($formdata['genre']) && $formdata['genre'] == ''){
                            echo " SELECTED ";
                        }
                        ?>>CHOOSE</option>
                    <option value='Jazz'
                        <?php if(isset($formdata['genre']) && $formdata['genre'] == 'Jazz'){
                            echo " SELECTED ";
                        }
                        ?>>Jazz</option>
                    <option value='Hip-Hop/Rap'
                        <?php if(isset($formdata['genre']) && $formdata['genre'] == 'Hip-Hop/Rap'){
                            echo " SELECTED ";
                        }
                        ?>>Hip-Hop/Rap</option>
                    <option value='Rock'
                        <?php if(isset($formdata['genre']) && $formdata['genre'] == 'Rock'){
                            echo " SELECTED ";
                        }
                        ?>>Rock</option>
                    <option value='Country'
                        <?php if(isset($formdata['genre']) && $formdata['genre'] == 'Country'){
                            echo " SELECTED ";
                        }
                        ?>>Country</option>
                    <option value='EDM'
                        <?php if(isset($formdata['genre']) && $formdata['genre'] == 'EDM'){
                            echo " SELECTED ";
                        }
                        ?>>EDM</option>
                    <option value='R&B'
                        <?php if(isset($formdata['genre']) && $formdata['genre'] == 'R&B'){
                            echo " SELECTED ";
                        }
                        ?>>R&B</option>
                    <option value='Reggae'
                        <?php if(isset($formdata['genre']) && $formdata['genre'] == 'Reggae'){
                            echo " SELECTED ";
                        }
                        ?>>Reggae</option>
                    <option value='Pop'
                        <?php if(isset($formdata['genre']) && $formdata['genre'] == 'Pop'){
                            echo " SELECTED ";
                        }
                        ?>>Pop</option>
                    <option value='Classical'
                        <?php if(isset($formdata['genre']) && $formdata['genre'] == 'Classical'){
                            echo " SELECTED ";
                        }
                        ?>>Classical</option>
                </select>
            </td>
        </tr>
        <tr><th><label for="submit">Submit:</label></th>
            <td><input type="hidden" name="ID" id="ID" value="<?php echo $row['ID'];?>"/>
                <input type="hidden" name="origusername" id="origusername" value="<?php echo $row['username'];?>"/>
                <input type="hidden" name="origemail" id="origemail" value="<?php echo $row['email'];?>"/>
                <input type="submit" name="submit" id="submit" value="Submit"/></td>
        </tr>
    </table>
</form>
<?php
}//end showform
include_once "footer.inc.php";
?>
