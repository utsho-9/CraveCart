<?php 
function openConnection() {
    $db_host = "127.0.0.1";
    $db_user = "root";
    $db_password = "";
    $db_name = "cravecart_db";

    $connection = mysqli_connect($db_host, $db_user, $db_password, $db_name);
    if (!$connection) {
        die("Could not connect to the database: " . mysqli_connect_error());
    }

    return $connection;
}
?>
