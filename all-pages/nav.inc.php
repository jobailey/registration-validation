
<ul>
    <?php
    echo ($currentfile == "index.php") ? "<li>Home</li>" : "<li><a href='index.php'>Home</a></li>";
    echo ($currentfile == "profileinsert.php") ? "<li>Register</li>" : "<li><a href='profileinsert.php'>Register</a></li>";
    if(isset($_SESSION['ID'])){echo "<li><a href='profilemanage.php'>Manage Members</a></li>";}
    if(isset($_SESSION['ID'])){echo "<li><a href='profilepwd.php'>Change Password</a></li>";}
    echo (isset($_SESSION['ID'])) ? "<li><a href='logout.php'>Log Out</a></li>" : "<li><a href='login.php'>Log In</a></li>";
    if (isset($_SESSION['ID'])){echo "Welcome back, " . $_SESSION['username'];}
    ?>
</ul>

