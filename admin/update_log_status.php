<?php
// Include the database connection
include('db_connect.php');

// Check if the necessary data is received from the AJAX request
if (isset($_POST['id']) && isset($_POST['is_checked'])) {
    $logId = $_POST['id'];
    $isChecked = $_POST['is_checked'];

    // Update the log status in the database
    $sql = "UPDATE daily_logs SET is_checked = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $isChecked, $logId);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
} else {
    echo "error";
}

// Close the database connection
$conn->close();
?>