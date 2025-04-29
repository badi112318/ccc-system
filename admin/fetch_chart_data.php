<?php
// ✅ Connect to the database
include 'db_connect.php';

$chartData = [];

// Get current year and the two previous years
$currentYear = date('Y');
$year1 = $currentYear - 1;
$year2 = $currentYear - 2;

// Prepare SQL query to fetch monthly counts for the last 3 years
$query = "
    SELECT 
        YEAR(date_requested) AS year, 
        MONTH(date_requested) AS month, 
        COUNT(*) AS total 
    FROM footages
    WHERE YEAR(date_requested) IN ($currentYear, $year1, $year2)
    GROUP BY YEAR(date_requested), MONTH(date_requested)
    ORDER BY year, month
";

$result = $conn->query($query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $chartData[] = [
            'year' => $row['year'],
            'month' => $row['month'],
            'total' => $row['total']
        ];
    }
}

// ✅ Return the data as JSON
header('Content-Type: application/json');
echo json_encode($chartData);

// Close the connection
$conn->close();
?>