<?php
 
include 'recommend_champ.php';
 
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
 
$sql = "SELECT name FROM championList";
$result = $conn->query($sql);
 
if ($result->num_rows > 0) {
    // update each row
    while($row = $result->fetch_assoc()) {
        $name = $row["name"];
        $sql = "UPDATE championList SET winrate=" . get_winrate_for_champ($name) . "WHERE name=\"$name\"";
        echo $sql;
        if ($conn->query($sql) === TRUE) {
            echo "Record updated successfully";
        } else {
            echo "Error updating record: " . $conn->error;
        }
    }
} else {
    //echo "0 results";
}
$conn->close();
return 0;
