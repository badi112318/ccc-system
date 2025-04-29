<?php
// ✅ Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ Connect to database FIRST
include 'db_connect.php';
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// ✅ Get the search term from URL (if available)
$search_term = isset($_GET['search']) ? $_GET['search'] : '';

// ✅ Fetch "Usefulness" Data
$query = "SELECT usefulness, COUNT(*) as count FROM footages 
          WHERE requesting_party LIKE ? 
          OR description LIKE ? 
          OR DATE_FORMAT(date_requested, '%Y-%m-%d') LIKE ? 
          GROUP BY usefulness";
$stmt = $conn->prepare($query);
$search_term_wildcard = "%" . $search_term . "%";
$stmt->bind_param("sss", $search_term_wildcard, $search_term_wildcard, $search_term_wildcard);
$stmt->execute();
$result = $stmt->get_result();

// Initialize arrays for chart data
$usefulness_labels = [];
$usefulness_counts = [];

// Fetch the results and format them for the chart
while ($row = $result->fetch_assoc()) {
    $usefulness_labels[] = $row['usefulness'];
    $usefulness_counts[] = (int) $row['count'];
}

// ✅ Fetch the main table data
$query = "SELECT * FROM footages 
          WHERE requesting_party LIKE ? 
          OR description LIKE ? 
          OR DATE_FORMAT(date_requested, '%Y-%m-%d') LIKE ? 
          ORDER BY id DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("sss", $search_term_wildcard, $search_term_wildcard, $search_term_wildcard);
$stmt->execute();
$table_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Playback Requests</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 0.2rem 0.5rem;
            text-align: center;
            vertical-align: middle;
            font-size: 0.85rem;
            word-wrap: break-word;
            margin: 0;
        }

        /* Responsive table styles */
        @media (min-width: 1200px) {

            .table th:nth-child(1),
            .table td:nth-child(1) {
                width: 5%;
            }

            .table th:nth-child(n+2):nth-child(-n+5),
            .table td:nth-child(n+2):nth-child(-n+5) {
                width: 12%;
            }
        }

        .btn-sm {
            margin: 2px;
        }
    </style>
</head>

<body>
    <div class="container-fluid mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>CCTV Playback Requests</h2>
        <a href="add_footage_form.php" class="btn btn-success">➕ Add Footage</a>
    </div>

    <!-- Search Form -->
    <?php if (isset($_GET['page']) && $_GET['page'] === 'footages'): ?>
            <div class="d-flex justify-content-end mb-4 no-print">
                <form method="GET" action="footages.php" style="max-width: 250px; width: 100%;">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search..."
                            value="<?= htmlspecialchars($search_term) ?>" />
                    </div>
                </form>
            </div>
        <?php endif; ?>
    
        <!-- Table for Footage Data -->
        <div class="table-responsive" style="overflow-x: auto;">
            <table id="footageTable" class="table table-bordered table-hover table-striped" style="min-width: 1800px;">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Date Requested</th>
                        <th>Time Requested</th>
                        <th>Name</th>
                        <th>Phone No.</th>
                        <th>Location</th>
                        <th>Incident Date</th>
                        <th>Incident Time</th>
                        <th>Description of Incident</th>
                        <th>Incident Type</th>
                        <th>Captured</th>
                        <th>Usefulness</th>
                        <th>Release Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $counter = 1; ?>
                    <?php if ($table_result && $table_result->num_rows > 0): ?>
                        <?php while ($row = $table_result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $counter++ ?></td>
                                <td>
                                    <?php
                                    $dateRequested = $row['date_requested'] ?? '';
                                    echo (!empty($dateRequested) && $dateRequested !== '0000-00-00' && strtotime($dateRequested))
                                        ? date('M d, Y', strtotime($dateRequested))
                                        : '-';
                                    ?>
                                </td>
                                <td><?= $row['time_requested'] ? date('H:i', strtotime($row['time_requested'])) : '-' ?></td>
                                <td><?= htmlspecialchars($row['requesting_party'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['phone_no'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['location'] ?? '-') ?></td>
                                <td><?= date('M d, Y', strtotime($row['incident_date'])) ?></td>
                                <td><?= $row['incident_time'] ? date('H:i', strtotime($row['incident_time'])) : '-' ?></td>
                                <td><?= htmlspecialchars($row['description'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['incident_type'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['caught_on_cam'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['usefulness'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['release_status'] ?? '-') ?></td>
                                <td>
                                    <a href="edit_footage.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="delete_footage.php?id=<?= $row['id'] ?>&redirect=true" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure you want to delete this record?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="14" class="text-center">No records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Monthly Summary Tables -->
    <div class="container-fluid mt-5">
        <?php
        // Function to get monthly data grouped by column (incident_type or location)
        function getMonthlyData($conn, $column)
        {
            $data = [];

            $query = "SELECT $column, MONTH(date_requested) as month, COUNT(*) as count
                  FROM footages
                  WHERE $column IS NOT NULL AND $column != ''
                  GROUP BY $column, MONTH(date_requested)";
            $result = $conn->query($query);

            while ($row = $result->fetch_assoc()) {
                $category = $row[$column] ?? 'Unknown';
                $month = (int) $row['month'];
                $count = (int) $row['count'];

                if (!isset($data[$category])) {
                    $data[$category] = array_fill(1, 12, 0);
                }
                $data[$category][$month] = $count;
            }

            return $data;
        }

        // Get data
        $incidentTypeData = getMonthlyData($conn, 'incident_type');
        $locationData = getMonthlyData($conn, 'location');

        // Function to render summary table
        function renderSummaryTable($data, $title)
        {
            echo "<div class='mb-5'>";
            echo "<h4 class='fw-bold mb-3'>$title</h4>";
            echo "<div class='table-responsive'>";
            echo "<table class='table table-bordered table-sm table-striped'>";
            echo "<thead class='table-dark'><tr><th>Category</th>";
            for ($m = 1; $m <= 12; $m++) {
                echo "<th>" . date('F', mktime(0, 0, 0, $m, 10)) . "</th>";
            }
            echo "<th>Total</th></tr></thead><tbody>";

            foreach ($data as $category => $months) {
                $total = array_sum($months);
                echo "<tr><td>" . htmlspecialchars($category) . "</td>";
                for ($m = 1; $m <= 12; $m++) {
                    echo "<td>{$months[$m]}</td>";
                }
                echo "<td class='fw-bold'>$total</td></tr>";
            }

            echo "</tbody></table></div></div>";
        }

        // Render both summary tables
        renderSummaryTable($incidentTypeData, '📊 Incident Type');
        renderSummaryTable($locationData, '📍 Location');
        ?>
    </div>

    
</body>

</html>