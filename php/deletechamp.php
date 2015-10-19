<?php
/**
 * Created by PhpStorm.
 * User: bgoodman
 * Date: 10/18/15
 * Time: 5:55 PM
 */
$servername = "pickachamp.web.engr.illinois.edu";
$serverUser = "pickacha_admin";
$serverPassword = "admin";
$dbname = "pickacha_db";

//Create connection
$conn = new mysqli($servername, $serverUser, $serverPassword, $dbname);

//Check connection
if (mysqli_connect_error()){
    die("Database failed: " . mysqli_connect_error());
}

//echo "Connected successfully <br><br>";
$champ = $_POST["champ"];
$username= $_POST["username"];

$sql = "SELECT * FROM favoritechampions WHERE Name = \"$username\" AND Champion = \"$champ\"";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0){
    echo "You have not favorited this champion.";
}
else{
    $sql = "DELETE FROM favoritechampions WHERE Name=\"$username\" AND Champion=\"$champ\"";
    $result = mysqli_query($conn, $sql);
    echo "Champion deleted!";
}
mysqli_close($conn);