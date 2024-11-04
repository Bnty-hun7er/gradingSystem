<?php

$yearErr = $accyearErr = $semErr = "";
$year = $accyear = $sem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["year"])) {
        $yearErr = "Year is required";
    } else {
        $year = $_POST["year"];
    }

    if (empty($_POST["accyear"])) {
        $accyearErr = "Academic Year is required";
    } else {
        $accyear = $_POST["accyear"];
    }

    if (empty($_POST["sem"])) {
        $semErr = "Semester is required";
    } else {
        $sem = $_POST["sem"];
    }

    

    if ($year && $accyear && $sem) {
        header("Location: page2.php?year=$year&accyear=$accyear&sem=$sem");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grading</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <div class="header">
        <h1>Grading System</h1>
        <h2>Eastern University of Sri Lanka</h2>
        <h2>Trincomalee Campus</h2>
    </div>

    <div class="container">
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="year">
                <table>
                    <tr>
                        <td>Year :</td>
                        <td><input type="text" name="year" placeholder="Year" value="<?php echo $year; ?>"> </td>
                        <span style="color: red;"><?php echo $yearErr; ?></span><br>
                    </tr>
                    <tr>
                        <td>Academic Year :</td>
                        <td><input type="text" name="accyear" placeholder="Academic Year" value="<?php echo $accyear; ?>"> </td>
                        <span style="color: red;"><?php echo $accyearErr; ?></span><br>
                    </tr>
                    <tr>
                        <td> Semester :</td>
                        <td><input type="text" name="sem" placeholder="Semester" value="<?php echo $sem; ?>"> </td>
                        <span style="color: red;"><?php echo $semErr; ?></span><br>
                    </tr>
                </table>
            </div>
            <button type="submit">Submit</button>
        </form>
    </div>
</body>
</html>
