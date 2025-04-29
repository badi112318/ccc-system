<?php
include 'db_connect.php';

// Year to update
$year = 2025;

// List of months to ensure order
$months = [
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December"
];

// Loop through each month to update the count
foreach ($months as $index => $month) {
    $field_name = "troubleshooting_" . $month;
    $count = isset($_POST[$field_name]) ? intval($_POST[$field_name]) : 0;

    // Convert month name to month number
    $month_num = $index + 1;

    // Delete existing entries for the month/year (to avoid duplicates)
    $conn->query("DELETE FROM external_services WHERE YEAR(service_date) = '$year' AND MONTH(service_date) = '$month_num'");

    // Insert new rows based on the entered count
    for ($i = 0; $i < $count; $i++) {
        $date = "$year-$month_num-01"; // Placeholder day
        $stmt = $conn->prepare("INSERT INTO external_services (service_date) VALUES (?)");
        $stmt->bind_param("s", $date);
        $stmt->execute();
    }
}

// Redirect back after update
header("Location: annual_report.php");
exit;
?>