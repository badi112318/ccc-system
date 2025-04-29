<?php
// Include the TCPDF library
require_once __DIR__ . '/../vendor/autoload.php';

// Get the year from the URL
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Connect to the database
include 'db_connect.php';

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch the data you want to display in the PDF
$query = "SELECT requesting_party, DATE_FORMAT(date_requested, '%Y-%m-%d %H:%i:%s') AS date_time, time_requested, incident_type, description, caught_on_cam
          FROM footages 
          WHERE YEAR(date_requested) = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $year);
$stmt->execute();
$footageResult = $stmt->get_result();
$footagesData = [];
while ($row = $footageResult->fetch_assoc()) {
    $footagesData[] = $row;
}
$stmt->close();

// Close the database connection
mysqli_close($conn);

// Create new PDF document
$pdf = new TCPDF();
$pdf->AddPage();

// Set document information
$pdf->SetTitle("Footage Review Report - $year");

// Set font
$pdf->SetFont('helvetica', '', 12);

// Add a title
$pdf->Cell(0, 10, "Footage Review Report for $year", 0, 1, 'C');

// Add the table header
$pdf->Ln(10);
$pdf->Cell(20, 10, 'No.', 1, 0, 'C');
$pdf->Cell(40, 10, 'Requesting Party', 1, 0, 'C');
$pdf->Cell(40, 10, 'Date', 1, 0, 'C');
$pdf->Cell(40, 10, 'Time', 1, 0, 'C');
$pdf->Cell(40, 10, 'Incident Type', 1, 0, 'C');
$pdf->Cell(50, 10, 'Details', 1, 1, 'C');

// Add the data rows
foreach ($footagesData as $index => $footage) {
    $formattedTime = date('h:i A', strtotime($footage['time_requested']));
    $pdf->Cell(20, 10, $index + 1, 1, 0, 'C');
    $pdf->Cell(40, 10, $footage['requesting_party'], 1, 0, 'L');
    $pdf->Cell(40, 10, date('Y-m-d', strtotime($footage['date_time'])), 1, 0, 'C');
    $pdf->Cell(40, 10, $formattedTime, 1, 0, 'C');
    $pdf->Cell(40, 10, $footage['incident_type'], 1, 0, 'C');
    $pdf->Cell(50, 10, $footage['description'], 1, 1, 'L');
}

// Output the PDF as a download
$pdf->Output('footage_review_report_' . $year . '.pdf', 'D');
?>