<?php
// Include TCPDF library
require_once __DIR__ . '/../vendor/autoload.php'; // If you are using Composer
// Or if you're manually including TCPDF:
require_once('tcpdf/tcpdf.php');

// Connect to the database
include 'db_connect.php';

$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Fetch data for the PDF generation (same query as in your code)
$search_term = isset($_GET['search']) ? $_GET['search'] : '';
$query = "SELECT requesting_party, DATE_FORMAT(date_requested, '%Y-%m-%d %H:%i:%s') AS date_time, time_requested, incident_type, description, caught_on_cam
        FROM footages 
        WHERE YEAR(date_requested) = ? 
        AND (requesting_party LIKE ? OR description LIKE ? OR DATE_FORMAT(date_requested, '%Y-%m-%d') LIKE ?) 
        ORDER BY date_requested DESC";

$stmt = $conn->prepare($query);
$search_term_wildcard = "%" . $search_term . "%";
$stmt->bind_param("ssss", $year, $search_term_wildcard, $search_term_wildcard, $search_term_wildcard);
$stmt->execute();
$footageResult = $stmt->get_result();
$footagesData = [];
while ($row = $footageResult->fetch_assoc()) {
    $footagesData[] = $row;
}
$stmt->close();

// Create new TCPDF instance
$pdf = new TCPDF();

// Set PDF metadata
$pdf->SetCreator('CCTV Report');
$pdf->SetAuthor('Your Company');
$pdf->SetTitle('Footage Review for ' . $year);
$pdf->SetSubject('Monthly CCTV Report');

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', '', 12);

// Title
$pdf->Cell(0, 10, 'Footage Review for ' . date('F Y', strtotime($year . '-01-01')), 0, 1, 'C');

// Table header
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(20, 10, 'No.', 1, 0, 'C');
$pdf->Cell(40, 10, 'Requesting Party', 1, 0, 'C');
$pdf->Cell(40, 10, 'Date', 1, 0, 'C');
$pdf->Cell(40, 10, 'Time', 1, 0, 'C');
$pdf->Cell(50, 10, 'Incident Type', 1, 0, 'C');
$pdf->Cell(70, 10, 'Description', 1, 1, 'C');

// Table data
$pdf->SetFont('helvetica', '', 10);
$index = 1;
foreach ($footagesData as $footage) {
    $pdf->Cell(20, 10, $index++, 1, 0, 'C');
    $pdf->Cell(40, 10, $footage['requesting_party'], 1, 0, 'C');
    $pdf->Cell(40, 10, date('Y-m-d', strtotime($footage['date_time'])), 1, 0, 'C');
    $pdf->Cell(40, 10, date('h:i A', strtotime($footage['time_requested'])), 1, 0, 'C');
    $pdf->Cell(50, 10, $footage['incident_type'], 1, 0, 'C');
    $pdf->Cell(70, 10, $footage['description'], 1, 1, 'C');
}

// Output the PDF
$pdf->Output('footage_report_' . $year . '.pdf', 'I'); // 'I' for inline output (browser view)
?>