<?php

$pagename = "Profile - Insert";
include_once "header.inc.php";
// no login needed to register

//set initial values
$showform = 1;  //form is shown
$errmsg = 0;
$errusername = "";
$erremail = "";
$errpassword = "";
$errpassword2 = "";
$errcap = "";
$errfname = "";
$errbio = "";
$errgenre = "";

if($_SERVER['REQUEST_METHOD'] == "POST"){
    /* user data santized with trim() for user-typed data */
    /* strtolower() for case-sensitive info */
    $formdata['username'] = trim(strtolower($_POST['username']));
    $formdata['email'] = trim(strtolower($_POST['email']));
    $formdata['password'] = trim($_POST['password']);
    $formdata['password2'] = trim($_POST['password2']);
    $formdata['fname'] = trim($_POST['fname']);
    $formdata['bio'] = trim($_POST['bio']);
    $formdata['genre'] = trim($_POST['genre']);

    /* empty field check */
    if (empty($formdata['fname'])){
        $errfname = "The first name is required.";
        $errmsg = 1;
    }
    if (empty($formdata['username'])){
        $errusername = "The username is required.";
        $errmsg = 1;
    }
    if (empty($formdata['email'])){
        $erremail = "The email address is required.";
        $errmsg = 1;
    }
    if (empty($formdata['bio'])){
        $errbio = "The bio is required.";
        $errmsg = 1;
    }
    if (empty($formdata['password'])){
        $errpassword = "The password is required.";
        $errmsg = 1;
    }
    if (empty($formdata['password2'])){
        $errpassword2 = "The confirmation password is required.";
        $errmsg = 1;
    }
    if (empty($formdata['genre'])){
        $errgenre = "Favorite genre must be selected.";
        $errmsg = 1;
    }
    if (empty($_POST['g-recaptcha-response'])) {
        $errcap = "The reCAPTCHA is required.";
        $errmsg = 1;
    }

    /* Ensure passwords match */
    if ($formdata['password'] != $formdata['password2'])
    {
        $errmsg = 1;
        $errpassword2 = "The passwords do not match.";
    }

    /* Check existing data */
    $sql = "SELECT * FROM profiles WHERE username = ?";
    $count = checkDuplicates($pdo, $sql, $formdata['username']);
    if($count > 0)
    {
        $errmsg = 1;
        $errusername = "The username is already taken.";
    }

    //Checking for duplicate email
    $sql = "SELECT * FROM profiles WHERE email = ?";
    $count = checkDuplicates($pdo, $sql, $formdata['email']);
    if($count > 0)
    {
        $errmsg = 1;
        $erremail = "The email is already taken.";
    }

    /* Error handling */
    if($errmsg == 1){
        echo "<p class='error'>There are errors. Correct them and try again.</p>";
    }else{ //insert into database if no error is returned
        //sensitive info such as passwords hashed
        $hashedpwd = password_hash($formdata['password'], PASSWORD_BCRYPT);

        try{
            $sql = "INSERT INTO profiles (fname, username, email, bio, password, genre, inputdate)
                    VALUES (:fname, :username, :email, :bio, :password, :genre, :inputdate) ";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':fname', $formdata['fname']);
            $stmt->bindValue(':username', $formdata['username']);
            $stmt->bindValue(':email', $formdata['email']);
            $stmt->bindValue(':bio', $formdata['bio']);
            $stmt->bindValue(':password', $hashedpwd);
            $stmt->bindValue(':genre', $formdata['genre']);
            $stmt->bindValue(':inputdate', $rightnow);
            $stmt->execute();

            $showform = 0; //hide the form
            echo "<p class='success'>Thanks for entering the information.</p>";
        }catch (PDOException $e){
            die( $e->getMessage() );
        }
    } // else errormsg

}//submit form

if($showform == 1){
    /* action set inside form tag to tell form where to go to load script to website */
    ?>
    <form name="profileinsert" id="profileinsert" method="post" action="profileinsert.php" enctype="multipart/form-data">
        <table>
            <tr><th><label for="fname">First Name:</label><span class="error">*</span></th>
                <td><input name="fname" id="fname" type="text" size="40" placeholder="Required first name"
                           value="<?php if(isset($formdata['fname'])){echo $formdata['fname'];}?>"/>
                    <span class="error"><?php if(isset($errfname)){echo $errfname;}?></span></td>
            </tr>
            <tr><th><label for="username">Username:</label><span class="error">*</span></th>
                <td><input name="username" id="username" type="text" size="40" placeholder="Required username"
                           value="<?php if(isset($formdata['username'])){echo $formdata['username'];}?>"/>
                    <span class="error"><?php if(isset($errusername)){echo $errusername;}?></span></td>
            </tr>
            <tr><th><label for="email">Email:</label><span class="error">*</span></th>
                <td><input name="email" id="email" type="email" size="40" placeholder="Required email"
                           value="<?php if(isset($formdata['email'])){echo $formdata['email'];}?>"/>
                    <span class="error"><?php if(isset($erremail)){echo $erremail;}?></span></td>
            </tr>
            <tr><th><label for="bio">Bio:</label><span class="error">*</span></th>
                <td><span class="error"><?php if(isset($errbio)){echo $errbio;}?></span>
                    <textarea name="bio" id="bio" placeholder="Required bio"><?php if(isset($formdata['bio'])){echo $formdata['bio'];}?></textarea>
                </td>
            </tr>
            <tr><th><label for="password">Password:</label><span class="error">*</span></th>
                <td><input name="password" id="password" type="password" size="40" placeholder="Required password" />
                    <span class="error"><?php if(isset($errpassword)){echo $errpassword;}?></span></td>
            </tr>
            <tr><th><label for="password2">Password Confirmation:</label><span class="error">*</span></th>
                <td><input name="password2" id="password2" type="password" size="40" placeholder="Required confirmation password" />
                    <span class="error"><?php if(isset($errpassword2)){echo $errpassword2;}?></span></td>
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
                <td><span class="error"><?php if(isset($errcap)){echo $errcap;}?></span>
                    <div class="g-recaptcha" data-sitekey="6LevcB0UAAAAAI_Y_dKMg-bT_USxicPojFxWTgp_"></div>
                    <input type="submit" name="submit" id="submit" value="Submit"/></td>
            </tr>
        </table>
    </form>
    <?php
}//end showform
include_once "footer.inc.php";

?>

