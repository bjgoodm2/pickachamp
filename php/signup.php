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

echo $username;
echo "Connected successfully <br><br>";

//Manipulate some data in the db
$username = $_POST["username"];
$password = $_POST["password"];

$sql = "INSERT INTO users VALUES (\"$username\", \"$password\")";
//echo $sql;
$result = mysqli_query($conn, $sql);

/*$sql = "SELECT * FROM users";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0){
	while($row = mysqli_fetch_assoc($result)){
		echo "BLHAHAHSHRLKEWHR: " . $row["name"] . "<br>";
	}
}
else{
		echo "0 results <br>";
}*/

mysqli_close($conn);