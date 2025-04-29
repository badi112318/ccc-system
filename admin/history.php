<?php
// Include the database connection
include('db_connect.php');

// Query to fetch data from daily_logs
$sql_daily = "SELECT * FROM daily_logs ORDER BY date DESC";
$result_daily = $conn->query($sql_daily);

// Query to fetch data from footages
$sql_footages = "SELECT * FROM footages ORDER BY date_requested DESC";
$result_footages = $conn->query($sql_footages);

// Query for annual report data (Incidents by Year)
$sql_annual = "SELECT YEAR(date_filed) AS year, COUNT(*) AS total_incidents
               FROM complaints
               GROUP BY YEAR(date_filed)
               ORDER BY year DESC";
$result_annual = $conn->query($sql_annual);

// Query for monthly report data (Complaints per Month)
$sql_monthly = "SELECT MONTH(date_filed) AS month, COUNT(*) AS total_complaints
                FROM complaints
                GROUP BY MONTH(date_filed)
                ORDER BY month DESC";
$result_monthly = $conn->query($sql_monthly);

// Error handling in case of query failure
if (!$result_daily || !$result_footages || !$result_annual || !$result_monthly) {
    die("Error in fetching data: " . $conn->error);
}

// Check if the Release Status is being updated
if (isset($_POST['footage_id']) && isset($_POST['release_status']) && $_POST['release_status'] == 'Released') {
    $footage_id = $_POST['footage_id'];
    $release_date = date('Y-m-d H:i:s'); // Get the current date and time

    // Update the release status and release date in the database
    $sql_update = "UPDATE footages SET release_status = 'Released', release_date = ? WHERE id = ?";
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param('si', $release_date, $footage_id);
    $stmt->execute();

    // Redirect after form submission
    echo "<script>window.location.href = 'http://localhost/cctv/admin/index.php?page=history';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History - CCTV System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">History Logs</h1>

        <!-- Display Daily Logs -->
        <section>
            <h2 class="mt-4">Daily Logs</h2>
            <?php if ($result_daily->num_rows > 0) { ?>
                <table id="dailyLogsTable" class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Location</th>
                            <th>Incident Type</th>
                            <th>Details</th>
                            <th>Action Taken</th>
                            <th>Agency</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result_daily->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo date('Y-m-d', strtotime($row['date'])); ?></td>
                                <td><?php echo date('H:i:s', strtotime($row['time'])); ?></td>
                                <td><?php echo htmlspecialchars($row['location']); ?></td>
                                <td><?php echo htmlspecialchars($row['incident_type']); ?></td>
                                <td><?php echo htmlspecialchars($row['details']); ?></td>
                                <td><?php echo htmlspecialchars($row['action_taken']); ?></td>
                                <td><?php echo htmlspecialchars($row['agency']); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <p>No daily logs available.</p>
            <?php } ?>
        </section>

        <!-- Display Footage Requests -->
        <section>
            <h2 class="mt-4">Footage Requests</h2>
            <?php if ($result_footages->num_rows > 0) { ?>
                <table id="footagesTable" class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Requesting Party</th>
                            <th>Incident Type</th>
                            <th>Status</th>
                            <th>Release Status</th>
                            <th>Release Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result_footages->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['requesting_party']); ?></td>
                                <td><?php echo htmlspecialchars($row['incident_type']); ?></td>
                                <td><?php echo htmlspecialchars($row['status']); ?></td>
                                <td><?php echo htmlspecialchars($row['release_status']); ?></td>
                                <td><?php echo $row['release_date'] ? date('Y-m-d H:i:s', strtotime($row['release_date'])) : 'N/A'; ?>
                                </td>
                                <td>
                                    <?php if ($row['release_status'] !== 'Released') { ?>
                                        <form method="POST" action="history.php">
                                            <input type="hidden" name="footage_id" value="<?php echo $row['id']; ?>">
                                            <input type="hidden" name="release_status" value="Released">
                                            <button type="submit" class="btn btn-success">Release</button>
                                        </form>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <p>No footage requests available.</p>
            <?php } ?>
        </section>

        <!-- Display Annual Report -->
        <section>
            <h2 class="mt-4">Annual Report</h2>
            <?php if ($result_annual->num_rows > 0) { ?>
                <table id="annualReportTable" class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Year</th>
                            <th>Total Incidents</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result_annual->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['year']); ?></td>
                                <td><?php echo htmlspecialchars($row['total_incidents']); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <p>No annual data available.</p>
            <?php } ?>
        </section>

        <!-- Display Monthly Report -->
        <section>
            <h2 class="mt-4">Monthly Report</h2>
            <?php if ($result_monthly->num_rows > 0) { ?>
                <table id="monthlyReportTable" class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Month</th>
                            <th>Total Complaints</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result_monthly->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo date('F', mktime(0, 0, 0, $row['month'], 10)); ?></td>
                                <td><?php echo htmlspecialchars($row['total_complaints']); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <p>No monthly data available.</p>
            <?php } ?>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            // Initialize DataTables for each table
            $('#dailyLogsTable').DataTable();
            $('#footagesTable').DataTable();
            $('#annualReportTable').DataTable();
            $('#monthlyReportTable').DataTable();
        });
    </script>
</body>

</html>