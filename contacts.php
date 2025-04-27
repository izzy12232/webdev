<?php
// Connect to MySQL Server (no database selected yet)
$conn = new mysqli("localhost", "root", "root");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create Database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS dbContacts";
if (!$conn->query($sql)) {
    die("Database creation failed: " . $conn->error);
}

// Select the database
$conn->select_db("dbContacts");

// Create Table if not exists
$sql = "CREATE TABLE IF NOT EXISTS tblSMS (
    sms_ID INT AUTO_INCREMENT PRIMARY KEY,
    studno VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    cpno VARCHAR(20) NOT NULL
)";
if (!$conn->query($sql)) {
    die("Table creation failed: " . $conn->error);
}

// Handle Form Actions
$action = isset($_GET['action']) ? $_GET['action'] : '';

$search_studno = "";
$name = "";
$cpno = "";

if (isset($_POST['search'])) {
    $search_studno = $_POST['studno'];
    $sql = "SELECT * FROM tblSMS WHERE studno='$search_studno'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $name = $row['name'];
        $cpno = $row['cpno'];
    } else {
        echo "<script>alert('Student Number not found!');</script>";
    }
}

if (isset($_POST['save'])) {
    $studno = $_POST['studno'];
    $name = $_POST['name'];
    $cpno = $_POST['cpno'];

    $new_studno = $studno;
    $counter = 1;

    // Check if Student Number already exists and generate a new one
    while (true) {
        $check = $conn->query("SELECT * FROM tblSMS WHERE studno='$new_studno'");
        if ($check->num_rows == 0) {
            break;
        } else {
            $new_studno = $studno . '-' . $counter;
            $counter++;
        }
    }

    // Insert with unique student number
    $sql = "INSERT INTO tblSMS (studno, name, cpno) VALUES ('$new_studno', '$name', '$cpno')";
    if (!$conn->query($sql)) {
        echo "Error adding record: " . $conn->error;
    } else {
        echo "<script>alert('Record saved successfully! New Student Number: $new_studno');</script>";
    }
}

if (isset($_POST['update'])) {
    $studno = $_POST['studno'];
    $name = $_POST['name'];
    $cpno = $_POST['cpno'];

    $sql = "UPDATE tblSMS SET name='$name', cpno='$cpno' WHERE studno='$studno'";
    if (!$conn->query($sql)) {
        echo "Error updating record: " . $conn->error;
    } else {
        echo "<script>alert('Record updated successfully!');</script>";
    }
}

if (isset($_POST['delete'])) {
    $studno = $_POST['studno'];

    $sql = "DELETE FROM tblSMS WHERE studno='$studno'";
    if (!$conn->query($sql)) {
        echo "Error deleting record: " . $conn->error;
    } else {
        echo "<script>alert('Record deleted successfully!');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage SMS Records</title>
</head>
<body>

<h3>Menu Links:</h3>
<a href="?action=add">Add Record</a> |
<a href="?action=update">Update Record</a> |
<a href="?action=delete">Delete Record</a>

<?php if ($action == 'add'): ?>

    <!-- Add Record -->
    <h2>Add Record</h2>
    <form method="POST">
        Student Number: <input type="text" name="studno" required><br><br>
        Name: <input type="text" name="name" required><br><br>
        CP No. : <input type="text" name="cpno" required> (ex. 639201234567)<br><br>
        <button type="submit" name="save">Save</button>
    </form>

<?php elseif ($action == 'update'): ?>

    <!-- Update Record -->
    <h2>Update Record</h2>
    <form method="POST">
        Student Number: <input type="text" name="studno" value="<?php echo htmlspecialchars($search_studno); ?>" required>
        <button type="submit" name="search">Search</button><br><br>
        Name: <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>"><br><br>
        CP No.: 63<input type="text" name="cpno" value="<?php echo htmlspecialchars($cpno); ?>"><br><br>
        <button type="submit" name="update">Update</button>
    </form>

<?php elseif ($action == 'delete'): ?>

    <!-- Delete Record -->
    <h2>Delete Record</h2>
    <form method="POST">
        Student Number: <input type="text" name="studno" value="<?php echo htmlspecialchars($search_studno); ?>" required>
        <button type="submit" name="search">Search</button><br><br>
        Name: <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" readonly><br><br>
        CP No.: 63<input type="text" name="cpno" value="<?php echo htmlspecialchars($cpno); ?>" readonly><br><br>
        <button type="submit" name="delete">Delete</button>
    </form>

<?php else: ?>

    <h2>Select an action from the menu.</h2>

<?php endif; ?>

</body>
</html>
