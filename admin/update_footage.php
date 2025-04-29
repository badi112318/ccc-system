<?php
// ✅ Start session
session_start();

// ✅ Connect to the database
include 'db_connect.php';
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// ✅ Check if the form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ✅ Sanitize and validate input
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $date_requested = $_POST['date_requested'] ?? '';
    $time_requested = $_POST['time_requested'] ?? ''; // ✅ make sure this matches your form
    $requesting_party = $_POST['requesting_party'] ?? '';
    $phone_no = $_POST['phone_no'] ?? '';
    $location = $_POST['location'] ?? '';
    $incident_date = $_POST['incident_date'] ?? '';
    $incident_time = $_POST['incident_time'] ?? '';
    $incident_type = $_POST['incident_type'] ?? '';
    $description = $_POST['description'] ?? '';
    $usefulness = $_POST['usefulness'] ?? '';
    $caught_on_cam = $_POST['caught_on_cam'] ?? '';
    $agency = $_POST['agency'] ?? '';
    $client_feedback = $_POST['client_feedback'] ?? '';
    $release_status = $_POST['release_status'] ?? '';

    // ✅ Basic validation for required fields
    $missing_fields = [];
    if (empty($date_requested))
        $missing_fields[] = 'Date Requested';
    if (empty($time_requested))
        $missing_fields[] = 'Time Requested';
    if (empty($requesting_party))
        $missing_fields[] = 'Requesting Party';

    if (!empty($missing_fields)) {
        echo "Required fields are missing: " . implode(', ', $missing_fields);
        exit();
    }

    // ✅ Prepare the update SQL query
    $query = "
        UPDATE footages
        SET 
            date_requested = ?, 
            time_requested = ?, 
            requesting_party = ?, 
            phone_no = ?, 
            location = ?, 
            incident_date = ?, 
            incident_time = ?, 
            incident_type = ?, 
            description = ?, 
            usefulness = ?, 
            caught_on_cam = ?, 
            agency = ?, 
            client_feedback = ?, 
            release_status = ?
        WHERE id = ?";

    // ✅ Prepare and bind parameters
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param(
            "ssssssssssssssi",
            $date_requested,
            $time_requested,
            $requesting_party,
            $phone_no,
            $location,
            $incident_date,
            $incident_time,
            $incident_type,
            $description,
            $usefulness,
            $caught_on_cam,
            $agency,
            $client_feedback,
            $release_status,
            $id
        );

        // ✅ Execute the update query
        if ($stmt->execute()) {
            // ✅ Redirect after success
            header("Location: http://localhost/cctv/admin/index.php?page=footages");
            exit();
        } else {
            echo "Error updating record: " . $stmt->error;
        }

        // ✅ Close statement
        $stmt->close();
    } else {
        echo "Error preparing query: " . $conn->error;
    }

    // ✅ Close DB connection
    $conn->close();
} else {
    // If not POST, redirect
    header("Location: footages.php");
    exit();
}
?>