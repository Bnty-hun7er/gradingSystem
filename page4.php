<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";

// Create a connection
$conn = new mysqli($servername, $username, $password);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve and sanitize form data
$year = isset($_POST["year"]) ? (int)$_POST["year"] : 0;
$accyear = isset($_POST["accyear"]) ? (int)$_POST["accyear"] : 0;
$sem = isset($_POST["sem"]) ? (int)$_POST["sem"] : 0;
$subject = isset($_POST["subject"]) ? mysqli_real_escape_string($conn, $_POST["subject"]) : '';

// Debugging output
echo "Year: $year<br>";
echo "Accyear: $accyear<br>";
echo "Sem: $sem<br>";
echo "Subject: $subject<br>";

// Check if required parameters are present
if ($year === 0 || $accyear === 0 || $sem === 0 || empty($subject)) {
    die("Missing parameters.");
}

// Select the database
$dbName = $year . "_Y_" . $accyear . "_S_" . $sem;
$conn->select_db($dbName);

// Prepare the table name
$tableName = "Marks_" . $subject;

// Check if the table exists before attempting to update it
$result = $conn->query("SHOW TABLES LIKE '$tableName'");
if ($result->num_rows == 0) {
    die("Table '$tableName' does not exist.");
}

// Prepare the SQL statement
$sql = "UPDATE $tableName SET 
        ca1 = ?, 
        ca2 = ?, 
        ca3 = ?, 
        avgCA = ?, 
        exam_marks = ?, 
        avg_marks = ?, 
        grade = ? 
        WHERE id = ?";
$stmt = $conn->prepare($sql);

// Check if statement preparation was successful
if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}

// Bind parameters
$stmt->bind_param("iiididsi", $ca1, $ca2, $ca3, $avgCA, $examMarks, $avgMarks, $grade, $id);

// Loop through POST data and update records
foreach ($_POST['ca1'] as $id => $ca1) {
    $ca1 = (int)$ca1;
    $ca2 = isset($_POST['ca2'][$id]) ? (int)$_POST['ca2'][$id] : 0;
    $ca3 = isset($_POST['ca3'][$id]) ? (int)$_POST['ca3'][$id] : 0;
    $examMarks = isset($_POST['exam_marks'][$id]) ? (int)$_POST['exam_marks'][$id] : 0;
    
   // Calculate average CA
$avgCA = (($ca1 + $ca2 + $ca3) / 3) * 0.35;

// Calculate total average marks
$avgMarks = $avgCA + ($examMarks * 0.65);

// Determine the grade based on total average
$grade = 'F';
if ($avgMarks >= 75) {
    $grade = 'A+';
} else if ($avgMarks >= 70) {
    $grade = 'A';
} else if ($avgMarks >= 65) {
    $grade = 'A-';
} else if ($avgMarks >= 60) {
    $grade = 'B+';
} else if ($avgMarks >= 55) {
    $grade = 'B';
} else if ($avgMarks >= 50) {
    $grade = 'B-';
} else if ($avgMarks >= 45) {
    $grade = 'C+';
} else if ($avgMarks >= 40) {
    $grade = 'C';
} else if ($avgMarks >= 35) {
    $grade = 'C-';
} 


    // Execute the prepared statement
    if (!$stmt->execute()) {
        echo "Error updating record with ID $id: " . $stmt->error;
    }
}

// Add column to the final_grade table
$sqlAddColumn = "ALTER TABLE final_grade ADD COLUMN $subject VARCHAR(2)";
if (!$conn->query($sqlAddColumn)) {
    die("Error adding column: " . $conn->error);
}

// Update final_grade table with grades from the subject table
$sqlUpdateGrades = "UPDATE final_grade 
                     JOIN marks_$subject ON marks_$subject.id = final_grade.id
                     SET final_grade.$subject = marks_$subject.grade";
if (!$conn->query($sqlUpdateGrades)) {
    die("Error updating grades: " . $conn->error);
}

// Close the statement and connection
$stmt->close();
$conn->close();

// Redirect with parameters
header("Location: page2.php?year=$year&accyear=$accyear&sem=$sem&subject=" );
exit;
?>
