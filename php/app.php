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

if($_POST['action'] === "login")  {
//Check that username and pword exist in DB
$username = $_POST["username"];
$password = $_POST["password"];

$sql = "SELECT * FROM users WHERE name = \"$username\" AND password = \"$password\"";
$result = mysqli_query($conn, $sql);

	if (mysqli_num_rows($result) > 0){
			echo "True";
			mysqli_close($conn);
			return;
	}
	else{
			echo "False";
			mysqli_close($conn);
			return;
	}
}

//TODO validate unique username penis

if($_POST['action'] === "signup") {
	//Manipulate some data in the db
	$username = $_POST["username"];
	$password = $_POST["password"];

	$sql = "INSERT INTO users VALUES (\"$username\", \"$password\")";
	//echo $sql;
	$result = mysqli_query($conn, $sql);



}


if($_POST['action'] === "favChamp") {
	$champ = $_POST["favoriteChamp"];
	$username= $_POST["username"];
	$sql = "SELECT * FROM favoritechampions WHERE Name = \"$username\" AND  Champion = \"$champ\"";
	$result = mysqli_query($conn, $sql);
	if (!$result || mysqli_num_rows($result) == 0){
	    $sql = "INSERT INTO favoritechampions VALUES (\"$username\", \"$champ\")";
	    $result = mysqli_query($conn, $sql);
	    echo "success";
	}
	else{
	    echo "exists";
	}

}

if($_POST['action'] === "deleteChamp") {
	$champ = $_POST["champ"];
	$username= $_POST["username"];
	$sql = "SELECT * FROM favoritechampions WHERE Name = \"$username\" AND Champion = \"$champ\"";
	$result = mysqli_query($conn, $sql);
	if (!$result || mysqli_num_rows($result) == 0){
	    echo "fail";
	}
	else{
	    $sql = "DELETE FROM favoritechampions WHERE Name=\"$username\" AND Champion=\"$champ\"";
	    $result = mysqli_query($conn, $sql);
	    echo "success";
	}
}

