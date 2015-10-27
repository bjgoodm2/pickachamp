<?php
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

echo $username;
echo "Connected successfully <br><br>";

//Manipulate some data in the db
$username = $_POST["username"];
$password = $_POST["password"];

$sql = "INSERT INTO users VALUES (\"$username\", \"$password\")";
//echo $sql;
$result = mysqli_query($conn, $sql);

mysqli_close($conn);