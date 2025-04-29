<?php
// Include the database connection
include('db_connect.php');

// Check if the necessary data is received from the AJAX request
if (isset($_POST['id']) && isset($_POST['release_status'])) {
    $footageId = $_POST['id'];
    $releaseStatus = $_POST['release_status'];

    // Update the release status in the database
    $sql = "UPDATE footages SET release_status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $releaseStatus, $footageId);

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