<?php

$pagename = "Edit Profile";
require_once "header.inc.php";
checkLogin();

try{
    //query the data
    $sql = "SELECT * FROM profiles";
    //executes a query.
    $result = $pdo->query($sql);

    ?>
    <?php
    echo "<p><b><a href='profileupdate.php?ID=" . $_SESSION['ID'] . "'>Update My Profile</a> | <a href='memberpwd.php?ID=" . $_SESSION['ID'] . "'>Update My Password</a>";
    echo "<table>
            <tr><th>ID</th><th>Username</th><th>Joined</th><th>Last Updated</th></tr>";
    //loop through the results and display to the screen
    foreach ($result as $row){
        echo "<tr><td>" . $row['ID'] . "</td>
        <td><a href=memberdetails.php?ID=" . $row['ID'] . "'>" . $row['username'] . "</a></td>
        <td> ";
        echo date("l, F j, Y", $row['inputdate']) . "</td>
        <td>";
        echo "</td></tr>\n";
    }
    echo "</table>";
}
catch (PDOException $e)
{
    die( $e->getMessage() );
}
require_once "footer.inc.php";
