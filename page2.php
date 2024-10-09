<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Retrieve parameters from the URL
$year = isset($_GET["year"]) ? $_GET["year"] : "";
$accyear = isset($_GET["accyear"]) ? $_GET["accyear"] : "";
$sem = isset($_GET["sem"]) ? $_GET["sem"] : "";

// Check if parameters are present
if (empty($year) || empty($accyear) || empty($sem)) {
    die("Missing parameters.");
}

$servername = "localhost";
$username = "root";
$password = "";

// Create a connection
$conn = new mysqli($servername, $username, $password);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the database exists
$dbName = $year . "_Y_" . $accyear . "_S_" . $sem;
$sqlCheck = "SHOW DATABASES LIKE '$dbName'";

$result = $conn->query($sqlCheck);

if ($result === false) {
    die("Query failed: " . $conn->error);
}

if ($result->num_rows == 0) {
    // If the database doesn't exist, create it
    $sqlCreate = "CREATE DATABASE `$dbName`";
    if ($conn->query($sqlCreate) === TRUE) {
        echo "Database created successfully";
    } else {
        echo "Error creating database: " . $conn->error;
    }
} else {
    echo "Database already exists";
}

// Select the database
$sqlSelectDb = "USE $dbName";
$conn->query($sqlSelectDb);

// Create the table if it doesn't exist
$sqlCreateFinal = "CREATE TABLE IF NOT EXISTS final_grade (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    index_No VARCHAR(28) NOT NULL
)";

if ($conn->query($sqlCreateFinal) === TRUE) {
    echo "<br>final_grade table created or already exists";
} else {
    echo "Error creating final_grade table: " . $conn->error;
}




$sourceDB = "students".$year;  // Source database name
$sourceTable = "students";  // Source table name

// Copy the id and index_No columns from the source table
$sqlCopyData = "INSERT IGNORE INTO  final_grade (id, index_No)
                SELECT id, index_number FROM $sourceDB.$sourceTable";

if ($conn->query($sqlCopyData) === TRUE) {
    // Data copy success
} else {
    echo "Error copying data: " . $conn->error;
}









// Close the connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grading</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="header">
        <h1>Grading System</h1>
        <h2>Eastern University of Sri Lanka</h2>
        <h2>Trincomalee Campus</h2>
    </div>

    <div class="yeardiv">
        <h3><?php echo "Year: " . htmlspecialchars($year); ?></h3>
        <h3><?php echo "Y: " . htmlspecialchars($accyear) . "     Sem: " . htmlspecialchars($sem); ?></h3>
    </div>
    <hr>

    <?php
    $subjectErr = ""; // Initialize the error variable
    $subject = ""; // Initialize the subject variable

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (empty($_POST['subject'])) {
            $subjectErr = "Subject is required";
        } else {
            $subject = $_POST['subject'];
            // Redirect to page3.php only if the subject is not empty
            header("Location: page3.php?year=" . urlencode($year) . "&accyear=" . urlencode($accyear) . "&sem=" . urlencode($sem) . "&subject=" . urlencode($subject));
            exit();
        }
    }
    ?>

    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?year=' . urlencode($year) . '&accyear=' . urlencode($accyear) . '&sem=' . urlencode($sem); ?>">
        Subject : <input type="text" name="subject" placeholder="subject" value="<?php echo htmlspecialchars($subject); ?>"><br>
        <button type="submit" style="margin-left: 50px;">Add</button>
        <span style="color: red;"><?php echo $subjectErr; ?></span><br>
    </form>

    <!-- Corrected form with PHP tags -->
    <form method="POST" action="page5.php">
    <input type='hidden' name='year' value='<?php echo htmlspecialchars($year); ?>'>
    <input type='hidden' name='accyear' value='<?php echo htmlspecialchars($accyear); ?>'>
    <input type='hidden' name='sem' value='<?php echo htmlspecialchars($sem); ?>'>
    <button type='submit' style='margin-top: 20px;'>FINISH</button>
</form>

</body>
</html>
