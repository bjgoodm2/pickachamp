<?php
$servername = "pickachamp.web.engr.illinois.edu";
$username = "pickacha_admin";
$password = "Dickdick";
$dbname = "pickacha_db";

//Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

//Check connection
if (mysqli_connect_error()){
	die("Database failed: " . mysqli_connect_error());
}

echo "Connected successfully <br><br>";

//Check that username and pword exist in DB
$username = $_POST["username"];
$password = $_POST["password"];

$sql = "SELECT * FROM users WHERE name = \"$username\" AND password = \"$password\"";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0){
	while($row = mysqli_fetch_assoc($result)){
		echo "Log in successful.";
	}
}
else{
		echo "That user does not exist.";
}

mysqli_close($conn);