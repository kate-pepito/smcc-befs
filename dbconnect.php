<?php
$mysql_servername = "localhost";
$mysql_username = "root";
$mysql_password = "";
$mysql_dbname = "smcc_befs";
$sql_file = "database/smcc_befs.sql";

$c1 = new mysqli($mysql_servername, $mysql_username, $mysql_password);
if ($c1->connect_error) {
    throw new mysqli_sql_exception("[Connection failed] " . $conn->connect_error);
} else {
    if (!$c1->query("USE $mysql_dbname")) {
        $c1->query("CREATE DATABASE $mysql_dbname");
    }
    $c1->close();
}

// Create connection
$conn = new mysqli($mysql_servername, $mysql_username, $mysql_password, $mysql_dbname);
// Check connection
if ($conn->connect_error) {
    throw new mysqli_sql_exception("[Connection failed] " . $conn->connect_error);
}

function check_seed_exists($conn) {
    $sq = "SELECT * FROM users WHERE id = 1";
    $result = mysqli_query($conn, $sq);
    return mysqli_num_rows($result) > 0;
}

function seed_database()
{
    global $mysql_servername, $mysql_username, $mysql_password, $mysql_dbname, $sql_file;
    $mysqli = new mysqli($mysql_servername, $mysql_username, $mysql_password, $mysql_dbname);
    // Check connection
    if ($mysqli->connect_error) {
        throw new mysqli_sql_exception("[Connection failed] " . $mysqli->connect_error);
    }
    if (!check_seed_exists($mysqli)) {
        // Read the SQL file
        $sql = file_get_contents($sql_file);
        echo mysqli_next_result($mysqli) . "<br/>";
        // Execute the SQL file
        if ($mysqli->multi_query($sql)) {
            do {
                // Store the result set (if any)
                if ($result = $mysqli->store_result()) {
                    $result->free();
                }
            } while ($mysqli->more_results() && $mysqli->next_result());
        } else {
            echo "Error importing SQL file: " . $mysqli->error;
        }
    }
    // Close the connection
    $mysqli->close();
}


seed_database();