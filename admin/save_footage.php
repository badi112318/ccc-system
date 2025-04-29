<?php
// ✅ Start session and connect to DB
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_connect.php';  // Database connection

// ✅ Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and collect form data
    $date_requested = $_POST['date_requested'] ?? '';
    $time_requested = $_POST['time_requested'] ?? '';
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
    $release_status = $_POST['release_status'] ?? 'Unreleased';

    // ✅ Validate Time (24-hour format HH:MM)
    if (!preg_match("/^([01][0-9]|2[0-3]):[0-5][0-9]$/", $time_requested)) {
        echo "<script>alert('⚠️ Invalid format for Time Requested. Please use HH:MM in 24-hour format (e.g., 14:30).'); history.back();</script>";
        exit;
    }

    if (!preg_match("/^([01][0-9]|2[0-3]):[0-5][0-9]$/", $incident_time)) {
        echo "<script>alert('⚠️ Invalid format for Incident Time. Please use HH:MM in 24-hour format (e.g., 09:45).'); history.back();</script>";
        exit;
    }

    // ✅ Validate dates
    if (empty($date_requested) || !strtotime($date_requested)) {
        echo "<script>alert('⚠️ Invalid Date Requested. Please enter a valid date.'); history.back();</script>";
        exit;
    } else {
        $date_requested = date('Y-m-d', strtotime($date_requested));
    }

    if (empty($incident_date) || !strtotime($incident_date)) {
        echo "<script>alert('⚠️ Invalid Incident Date. Please enter a valid date.'); history.back();</script>";
        exit;
    } else {
        $incident_date = date('Y-m-d', strtotime($incident_date));
    }

    // ✅ SQL query
    $query = "INSERT INTO footages (
        date_requested, time_requested, requesting_party, phone_no, location, 
        incident_date, incident_time, incident_type, description, usefulness, 
        caught_on_cam, agency, client_feedback, release_status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // ✅ Prepare and execute
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param(
            'ssssssssssssss',
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
            $release_status
        );

        if ($stmt->execute()) {
            header("Location: http://localhost/cctv/admin/index.php?page=footages");
            exit;
        } else {
            echo "<script>alert('❌ Database error: " . htmlspecialchars($stmt->error) . "'); history.back();</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('❌ Failed to prepare SQL statement: " . htmlspecialchars($conn->error) . "'); history.back();</script>";
    }

    $conn->close();
}
?>