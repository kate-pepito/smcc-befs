<?php
$mysql_servername = $_ENV["BEFS_MYSQL_HOST"] ?? "localhost";
$mysql_username = $_ENV["BEFS_MYSQL_USERNAME"] ?? "root";
$mysql_password = $_ENV["BEFS_MYSQL_PASSWORD"] ?? "";
$mysql_dbname = $_ENV["BEFS_MYSQL_DBNAME"] ?? "smcc_befs";
$sql_file = $_ENV["BEFS_MYSQL_IMPORT_FILE"] ?? "database/smcc_befs.sql";

$c1 = new mysqli($mysql_servername, $mysql_username, $mysql_password);
if (!$c1 || $c1->connect_error) {
    throw new mysqli_sql_exception("[Connection failed] " . conn()->connect_error);
} else {
    if (!$c1->query("USE $mysql_dbname")) {
        $c1->query("CREATE DATABASE $mysql_dbname");
    }
    $c1->close();
}

// Create connection
$conn = new mysqli($mysql_servername, $mysql_username, $mysql_password, $mysql_dbname);
// Check connection
if (!$conn || $conn->connect_error) {
    throw new mysqli_sql_exception("[Connection failed] " . conn()->connect_error);
}

function check_seed_exists()
{
    $sq = "SELECT * FROM users WHERE id = 1";
    $result = mysqli_query(conn(), $sq);
    return $result !== false && mysqli_num_rows($result) > 0;
}

function seed_database($sql_file)
{
    if (!check_seed_exists()) {
        $mysqli = conn();
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
}


seed_database($sql_file);