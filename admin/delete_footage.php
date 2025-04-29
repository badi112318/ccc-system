<?php
// ✅ Start session
session_start();

// ✅ Connect to database
include 'db_connect.php';
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// ✅ Check if ID is passed
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // ✅ Delete query
    $deleteQuery = "DELETE FROM footages WHERE id = ?";
    if ($stmt = $conn->prepare($deleteQuery)) {
        $stmt->bind_param("i", $id); // Bind the ID parameter
        if ($stmt->execute()) {
            // ✅ Redirect back to the main page after deletion
            header("Location: http://localhost/cctv/admin/index.php?page=footages");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }
} else {
    // Redirect to the main page if no ID is passed
    header("Location: home.php");
    exit();
}

$conn->close();
?>