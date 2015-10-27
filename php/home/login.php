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

//echo "Connected successfully <br><br>";

//Check that username and pword exist in DB
$username = $_POST["username"];
$password = $_POST["password"];

//echo $username;
//echo $password;
$sql = "SELECT * FROM users WHERE name = \"$username\" AND password = \"$password\"";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0){
	while($row = mysqli_fetch_assoc($result)){
		//echo $sql;
		$success = True;
		logIn($username, $password, $success);
	}
}
else{
		$success = False;
		logIn($username, $password, $success);
}

mysqli_close($conn);


function logIn($username, $password, $success){
	if ($success){
		echo "<h1> Log in successful. </h1>";
		echo "<p> Logged in as $username </p>";
		echo "<p> You entered your password as $password and it was correct! </p>";
	}
	else {
		header("Location: http://pickachamp.web.engr.illinois.edu/index.html");
		echo "<script>
    			alert(\"Get better nub\");
    			</script>";
    }
}

function listChamps(){
    global $username;
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

    $sql = "SELECT * FROM favoritechampions WHERE Name = \"$username\"";
    $result = mysqli_query($conn, $sql);
    if (!$result || mysqli_num_rows($result) == 0){
        echo "You have no favorite champions. HA.";
    }
    else{
        $result = mysqli_query($conn, $sql);
        while($row = mysqli_fetch_assoc($result)){
            echo "<li> <!--<button style=\"background-color:red\" type=\"submit\" name=\"action\">&#10006</button>-->" . $row["Champion"] .
                 "</li>";
        }
    }
    mysqli_close($conn);
}
?>


<!DOCTYPE html>
<html>
	<head>
		<title>Home</title>

	 <!-- Compiled and minified CSS -->
	 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.1/css/materialize.min.css">

<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
	 <!-- Compiled and minified JavaScript -->
	 <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.1/js/materialize.min.js"></script>
	</head>
	<body>
		<form method ="post" class="col s12" action="../lookupchamp.php">
			<input type="text" name="query" id="query" class="validate">
			<input type="hidden" name="username" id="username" value="<?php echo "$username" ?>">
			<button class="btn" type="submit" name="action">Favorite It!</button>
		</form>
        <form method="post" class="col s12" action="../deletechamp.php">
            <input type="text" name="champ" id="champ" class="validate">
            <input type="hidden" name="username" id="username" value="<?php echo "$username" ?>">
            <button class="btn" type="submit" name="action">Delete It!</button>
        </form>
        <h2>Your Favorite Champs</h2>
    <ul>
        <?php listChamps();?>
    </ul>
	</body>
</html>