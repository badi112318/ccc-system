<?php
include 'db_connect.php';

// Check if the 'id' is passed via POST
if (isset($_POST['id'])) {
    $log_id = $_POST['id'];

    // Prepare the DELETE query to remove the log from the database
    $qry = $conn->query("DELETE FROM daily_logs WHERE id = '$log_id'");

    // Check if the query was successful
    if ($qry) {
        // If the deletion was successful, redirect to the logs page with a success message
        echo "<script>alert('Log deleted successfully!'); window.location.href='http://localhost/cctv/admin/index.php?page=daily';</script>";
    } else {
        // If the deletion failed, redirect to the logs page with an error message
        echo "<script>alert('Error deleting log. Please try again.'); window.location.href='http://localhost/cctv/admin/index.php?page=daily';</script>";
    }
} else {
    // If no 'id' is passed, redirect to the logs page with an error message
    echo "<script>alert('Invalid request.'); window.location.href='http://localhost/cctv/admin/index.php?page=daily';</script>";
}
?>