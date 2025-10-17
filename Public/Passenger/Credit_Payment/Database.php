<?php 
// Database connection:
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "dbtbms";

// Create connection:
    $conn = new mysqli($servername, $username, $password, $database);

// Check Connection
    if ($conn->connect_error){
        die("Connection failed: ".$conn->connect_error);
    }
   echo "<script> console.log('Connected Sucssessfully'); </script>";
?>


<!--
   
    // Querying from the database:
    $sql = "SELECT * FROM users";
    $result = $conn->query($sql);

    if ($result->num_rows > 0){
        // output data of each row

        while($row = $result->fetch_assoc()){
            echo "Working";
            break;
        } 
    }else {
            echo "Not working";
        }
-->