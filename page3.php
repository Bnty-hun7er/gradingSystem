<html>
    <head>
    <link rel="stylesheet" href="page3.css">
    </head>
    <body>
<?php
$year = $_GET["year"];
$accyear = $_GET["accyear"];
$sem = $_GET["sem"];
$subject = $_GET["subject"];

$servername = "localhost";
$username = "root";
$password = "";

$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Connect to the new database
$dbName = $year . "_Y_" . $accyear . "_S_" . $sem;
$conn->select_db($dbName);

// Create the table 'Marks' with user-provided subject if it does not already exist
$tableName = "Marks_" . preg_replace('/[^a-zA-Z0-9_]/', '_', $subject);
$sqlCreateTable = "CREATE TABLE IF NOT EXISTS $tableName (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,  
    index_No VARCHAR(28) NOT NULL,
    ca1 INT,
    ca2 INT,
    ca3 INT,
    avgCA FLOAT,
    exam_marks INT,
    avg_marks FLOAT,
    grade VARCHAR(2)
)";

if ($conn->query($sqlCreateTable) === TRUE) {
    // Table creation success
} else {
    echo "Error creating table: " . $conn->error;
}

// Copy data from another database to this table
$sourceDB = "students".$year;  // Source database name
$sourceTable = "students";  // Source table name

// Copy the id and index_No columns from the source table
$sqlCopyData = "INSERT IGNORE INTO $tableName (id, index_No)
                SELECT id, index_number FROM $sourceDB.$sourceTable";

if ($conn->query($sqlCopyData) === TRUE) {
    // Data copy success
} else {
    echo "Error copying data: " . $conn->error;
}

// Fetch data for the form
$sql = "SELECT id, index_No FROM $tableName";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<form action='page4.php' method='POST'>";
    echo "<table border='1'>
        <tr>
            <th>ID</th>
            <th>Index NO</th>
            <th>CA 1</th>
            <th>CA 2</th>
            <th>CA 3</th>
            <th>AVG CA</th>
            <th>Exam Marks</th>
            <th>Avg Marks</th>
            <th>Grade</th>
        </tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['index_No']}</td>
            <td><input type='number' name='ca1[{$row['id']}]'  min='0' max='100' oninput='calculateAvg(this, {$row['id']})'></td>
            <td><input type='number' name='ca2[{$row['id']}]'  min='0' max='100' oninput='calculateAvg(this, {$row['id']})'></td>
            <td><input type='number' name='ca3[{$row['id']}]'  min='0' max='100' oninput='calculateAvg(this, {$row['id']})'></td>
            <td><input type='text' id='avgCA_{$row['id']}' readonly></td>
            <td><input type='number' name='exam_marks[{$row['id']}]'  min='0' max='100' oninput='calculateAvg(this, {$row['id']})'></td>
            <td><input type='text' id='avgMarks_{$row['id']}' readonly></td>
            <td><input type='text' name='grade[{$row['id']}]' id='grade_{$row['id']}' readonly></td>  <!-- Name corrected -->
        </tr>";
    }

    echo "</table>";

    // Hidden inputs to pass parameters
    echo "<input type='hidden' name='year' value='$year'>";
    echo "<input type='hidden' name='accyear' value='$accyear'>";
    echo "<input type='hidden' name='sem' value='$sem'>";
    echo "<input type='hidden' name='subject' value='$subject'>";
    echo "<button type='submit' style='margin-top: 20px;'>Save Marks</button>";
    echo "</form>";
} else {
    echo "No records found.";
}

$conn->close();
?>

<script>
function calculateAvg(input, id) {
	if(input.value<0) input.value=0;
	if(input.value>100) input.value=100;
    // Get CA1, CA2, CA3, and Exam Marks values
    let ca1 = parseFloat(document.querySelector(`input[name='ca1[${id}]']`).value) || 0;
    let ca2 = parseFloat(document.querySelector(`input[name='ca2[${id}]']`).value) || 0;
    let ca3 = parseFloat(document.querySelector(`input[name='ca3[${id}]']`).value) || 0;
    let examMarks = parseFloat(document.querySelector(`input[name='exam_marks[${id}]']`).value) || 0;

    // Calculate average CA
    let avgCA = ((ca1 + ca2 + ca3) / 3) * 0.35;
    document.getElementById(`avgCA_${id}`).value = avgCA.toFixed(2);

    // Calculate total average marks
    let totalAvg = avgCA + (examMarks * 0.65);
    document.getElementById(`avgMarks_${id}`).value = totalAvg.toFixed(2);

    // Determine the grade based on total average
    let grade;
    if (totalAvg >= 75) {
        grade = 'A+';
    } else if (totalAvg >= 70) {
        grade = 'A';
    } else if (totalAvg >= 65) {
        grade = 'A-';
    } else if (totalAvg >= 60) {
        grade = 'B+';
    } else if (totalAvg >= 55) {
        grade = 'B';
    }else if (totalAvg >= 50) {
        grade = 'B-';
    }else if (totalAvg >= 45) {
        grade = 'C+';
    }else if (totalAvg >= 40) {
        grade = 'C';
    }else if (totalAvg >= 35) {
        grade = 'C-';
    }else {
        grade = 'F';
    }
    document.getElementById(`grade_${id}`).value = grade;
}
</script>





</body>
</html>
