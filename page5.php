<?php
// Debugging output
echo "Finished grading.<br>";

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$tableName = "final_grade"; // Replace with your table name

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve and sanitize POST data
$year = isset($_POST["year"]) ? $_POST["year"] : "";
$accyear = isset($_POST["accyear"]) ? $_POST["accyear"] : "";
$sem = isset($_POST["sem"]) ? $_POST["sem"] : "";

// Debugging output
echo "Year: " . htmlspecialchars($year) . "<br>";
echo "Accyear: " . htmlspecialchars($accyear) . "<br>";
echo "Sem: " . htmlspecialchars($sem) . "<br>";

// Check if required parameters are present
if (empty($year) || empty($accyear) || empty($sem)) {
    die("Missing parameters.");
}

$dbName = $year . "_Y_" . $accyear . "_S_" . $sem;

// Select the database
$conn->select_db($dbName);

// Fetch column names
$sql = "SHOW COLUMNS FROM $tableName";
$result = $conn->query($sql);

if ($result === false) {
    die("Query failed: " . $conn->error);
}

// Start HTML output
echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="page5.css">
    <title>Grading Results</title>
</head>
<body>
<div class="container">
    <h1>Grading Results</h1>';

if ($result->num_rows > 0) {
    $columns = [];
    while ($row = $result->fetch_assoc()) {
        $columns[] = $row['Field']; // Store column names
    }

    // Fetch data
    $sql = "SELECT * FROM $tableName";
    $dataResult = $conn->query($sql);

    if ($dataResult === false) {
        die("Query failed: " . $conn->error);
    }

    if ($dataResult->num_rows > 0) {
        // Start HTML table
        echo "<table><tr>";

        // Print column headers
        foreach ($columns as $column) {
            echo "<th>" . htmlspecialchars($column) . "</th>";
        }
        echo "</tr>";

        // Print rows
        while ($row = $dataResult->fetch_assoc()) {
            echo "<tr>";
            foreach ($columns as $column) {
                echo "<td>" . htmlspecialchars($row[$column]) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No data found.";
    }
} else {
    echo "No columns found.";
}

// Close connection
$conn->close();

echo '</div>
</body>
</html>';
?>
