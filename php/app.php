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

if($_POST['action'] === "login") {
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

if($_POST['action'] === "signup") {
	//Manipulate some data in the db
	$username = $_POST["username"];
	$password = $_POST["password"];
	$summoner = $_POST["summoner"];

	$sql = "SELECT * FROM users WHERE name = \"$username\"";
	$result = mysqli_query($conn, $sql);
	if (mysqli_num_rows($result) > 0){ //If the user exists then don't add it
		echo "User already exists";
		mysqli_close($conn);
		return;
	}
	else{
		echo "Success";
		$sql = "INSERT INTO users VALUES (\"$username\", \"$password\", \"$summoner\")";
		$result = mysqli_query($conn, $sql);
		mysqli_close($conn);
		return;
	}
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

if($_POST['action'] === "getSummoner") {
	$username = $_POST["username"];

	$sql = "SELECT * FROM users WHERE name = \"$username\"";
	$result = mysqli_query($conn, $sql);

	if (mysqli_num_rows($result) > 0){
		while($row = mysqli_fetch_assoc($result)) {
			echo $row["summoner_name"];
		}
		mysqli_close($conn);
		return;
	}
	else{
		echo "False";
		mysqli_close($conn);
		return;
	}
}

if($_POST['action'] === "getFavs") {
	$username = $_POST["username"];

	$sql = "SELECT * FROM favoritechampions WHERE name = \"$username\"";
	$result = mysqli_query($conn, $sql);

	if (mysqli_num_rows($result) > 0){
		while($row = mysqli_fetch_assoc($result)) {
			echo "<li id=\"" . $row["champion"] . "\">" . $row["champion"] . "<button class=\"deleteChamp\" value=\"" . $row["champion"] . "\" style=\"color:red; background-color:Transparent; border: none\">X</button></li>";
		}
		mysqli_close($conn);
		return;
	}
	else{
		echo "No favorites";
		mysqli_close($conn);
		return;
	}
}
