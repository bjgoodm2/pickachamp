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


//Check that username and pword exist in DB
$username = $_POST["username"];
$password = $_POST["password"];

$sql = "SELECT * FROM users WHERE name = \"$username\" AND password = \"$password\"";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0){
	while($row = mysqli_fetch_assoc($result)){
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
        
	}

	else {
		header("Location: ../index.html");
    }
}

/**function redirect($username){
    //set POST variables
    $url = 'app.php'; 
    $fields = array(
                        'uname' => urlencode($username)
                    );
    //url-ify the data for POST
    foreach($fields as $key=>$value) { $fields_string .= $key. '='.$value.'&';}
    rtrim($fields_string, '&');

    //open connection
    $ch = curl_init();

    //set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, count($fields));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);

    //execute post
    $result = curl_exec($ch);

    //close connection
    curl_close($ch);
}**/

?>


