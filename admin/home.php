<?php
// Start session and connect to the database
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_connect.php';

$year = date('Y');

// Fetch Totals Data
function fetchTotalData($conn, $year)
{
    $sql = "
        SELECT 
            (SELECT COUNT(*) FROM daily_logs WHERE YEAR(date) = ?) AS total_incidents_monitored,
            (SELECT COUNT(*) FROM daily_logs WHERE YEAR(date) = ? AND incident_type IS NOT NULL) AS total_incidents_reported,
            (SELECT COUNT(*) FROM footages) AS total_footage_reviews,
            (SELECT COUNT(*) FROM footages WHERE release_status = 'Released') AS total_footage_released
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $year, $year);
    $stmt->execute();
    $result = $stmt->get_result();
    $totals = $result->fetch_assoc();
    $stmt->close();

    return $totals;
}

$totals = fetchTotalData($conn, $year);

// Fetch logs data from the database
$query = "SELECT agency FROM daily_logs";
$result = $conn->query($query);

// Initialize the agency counts array
$agencyCounts = [];

// Count the logs per agency
while ($row = $result->fetch_assoc()) {
    $agency = $row['agency'];
    if (!isset($agencyCounts[$agency])) {
        $agencyCounts[$agency] = 0;
    }
    $agencyCounts[$agency]++;
}

// Fetch Incident Type Data
function fetchIncidentTypeData($conn, $year)
{
    $sql = "
        SELECT incident_type, COUNT(*) AS count
        FROM daily_logs
        WHERE YEAR(date) = ?
        GROUP BY incident_type
        ORDER BY count DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $year);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $data;
}

// Fetch Location Data
function fetchLocationData($conn, $year)
{
    $sql = "
        SELECT location, COUNT(*) AS count
        FROM daily_logs
        WHERE YEAR(date) = ?
        GROUP BY location
        ORDER BY count DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $year);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $data;
}

$incidentTypeData = fetchIncidentTypeData($conn, $year);
$locationData = fetchLocationData($conn, $year);

// Fetch Captured vs Uncaptured count
$capturedQuery = "
    SELECT 
        SUM(CASE WHEN caught_on_cam = 'Captured' THEN 1 ELSE 0 END) AS captured_count,
        SUM(CASE WHEN caught_on_cam = 'Uncaptured' THEN 1 ELSE 0 END) AS uncaptured_count
    FROM footages";
$capturedResult = $conn->query($capturedQuery);
$capturedData = $capturedResult->fetch_assoc() ?? ['captured_count' => 0, 'uncaptured_count' => 0];

$capturedCount = (int) $capturedData['captured_count'];
$uncapturedCount = (int) $capturedData['uncaptured_count'];



// Fetch monthly data for the last 3 years
$chartQuery = "
    SELECT 
        YEAR(date_requested) AS year, 
        MONTH(date_requested) AS month, 
        COUNT(*) AS request_count
    FROM footages
    WHERE YEAR(date_requested) IN (2025, 2026, 2027)
    GROUP BY year, month
    ORDER BY year, month";
$chartResult = $conn->query($chartQuery);

$chartData = [];
while ($row = $chartResult->fetch_assoc()) {
    $chartData[$row['year']][$row['month']] = (int) $row['request_count'];
}

// Generate data for the last 3 years (including months with 0)
$currentYear = date('Y');
$years = [2025, 2026, 2027];
$months = range(1, 12);

$chartOutput = [];
foreach ($years as $year) {
    $data = [];
    foreach ($months as $month) {
        $data[] = $chartData[$year][$month] ?? 0; // Default to 0 if no data
    }
    $chartOutput[$year] = $data;
}


// Fetch logs data from the database
$query = "SELECT agency FROM daily_logs";
$result = $conn->query($query);

// Initialize the agency counts array
$agencyCounts = [];

// Count the logs per agency
while ($row = $result->fetch_assoc()) {
    $agency = $row['agency'];
    if (!isset($agencyCounts[$agency])) {
        $agencyCounts[$agency] = 0;
    }
    $agencyCounts[$agency]++;
}
$usefulCount = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM footages WHERE usefulness = 'Useful'"));
$somehowUsefulCount = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM footages WHERE usefulness = 'Somehow Useful'"));
$notUsefulCount = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM footages WHERE usefulness = 'Not Useful'"));

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCTV UNIT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- CSS -->
	<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

	<!-- JS -->
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Chart.js Data Labels Plugin -->
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>


</head>

<body>
    <div class="container mt-4">
    <h2 class="text-center mb-4 fw-bold">CCTV ACCOMPLISHMENT REPORT</h2>

    <div class="row mb-4">
        <div class="col-md-3">
            <a href="http://localhost/cctv/admin/index.php?page=annual" class="text-decoration-none">
                <div class="bg-warning p-4 rounded shadow text-center border border-white">
                    <div class="text-3xl font-bold text-white">
                        <?php echo $totals['total_incidents_monitored']; ?>
                        </div>
                        <div class="text-white">Total Incidents Monitored</div>
                    </div>
                </a>
            </div>
    
            <div class="col-md-3">
                <a href="http://localhost/cctv/admin/index.php?page=annual" class="text-decoration-none">
                    <div class="bg-danger p-4 rounded shadow text-center border border-white">
                        <div class="text-3xl font-bold text-white">
                            <?php echo $totals['total_incidents_reported']; ?>
                        </div>
                        <div class="text-white">Total Incidents Reported</div>
                    </div>
                </a>
            </div>
    
            <div class="col-md-3">
                <a href="http://localhost/cctv/admin/index.php?page=annual" class="text-decoration-none">
                    <div class="bg-success p-4 rounded shadow text-center border border-white">
                        <div class="text-3xl font-bold text-white">
                            <?php echo $totals['total_footage_reviews']; ?>
                        </div>
                        <div class="text-white">Number of Footage Reviews</div>
                    </div>
                </a>
            </div>
    
            <div class="col-md-3">
                <a href="http://localhost/cctv/admin/index.php?page=annual" class="text-decoration-none">
                    <div class="bg-primary p-4 rounded shadow text-center border border-white">
                        <div class="text-3xl font-bold text-white">
                            <?php echo $totals['total_footage_released']; ?>
                        </div>
                        <div class="text-white">Number of Footage Releases</div>
                    </div>
                </a>
            </div>
        </div>
    </div>




        <!-- Charts Section -->
       <div class="container mt-3">
            <h6>📊 Monthly Playback Requests</h6>
            <div class="chart-container mt-3">
                <canvas id="threeYearChart" style="width: 50%; height: 50px;"></canvas>
            </div>

            <div class="row mt-3">
                <!-- Location Distribution Chart on the left -->
                <div class="col-12 col-md-4">
                    <div id="locationChart" style="width: 130%; height: 10px;"></div>
                </div>

                <!-- Footage Usefulness Summary Pie Chart in the middle -->
                <div class="col-12 col-md-4">
                    <h5 class="text-center">Footage Usefulness Summary</h5>
                    <canvas id="usefulnessPieChart" width="400" height="400"></canvas>
                </div>

                <!-- Agency Pie Chart on the right -->
                <div class="col-12 col-md-4">
                    <h5 class="text-center">Agency</h5>
                    <div id="agencyChart" style="height: 300px;"></div>
                </div>
            </div>

            <!-- Incident Type Distribution Chart (Below) -->
            <div class="chart-container mt-3">
                <div id="incidentTypeChart" style="width: 100%; height: 300px;"></div>
            </div>
        </div>

<script>
    // Monthly Playback Requests Chart (Last 3 Years)
    new Chart(document.getElementById('threeYearChart'), {
        type: 'line',
        data: {
            labels: [<?= implode(',', $months) ?>],
                datasets: [
                    <?php foreach ($years as $year): ?>
                                {
                            label: '<?= $year ?>',
                            data: [<?= implode(',', $chartOutput[$year]) ?>],
                            borderColor: '#' + Math.floor(Math.random() * 16777215).toString(16),
                            fill: false
                        },
                    <?php endforeach; ?>
                ]
            }
        });

        // Footage Usefulness Pie Chart
        const useful = <?= $usefulCount ?>;
        const somehowUseful = <?= $somehowUsefulCount ?>;
        const notUseful = <?= $notUsefulCount ?>;

        const usefulnessData = [useful, somehowUseful, notUseful];
        const usefulnessLabels = ['Useful', 'Somehow Useful', 'Not Useful'];

        const ctx = document.getElementById('usefulnessPieChart').getContext('2d');
        const usefulnessPieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: usefulnessLabels,
                datasets: [{
                    data: usefulnessData,
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545'], // Green, Yellow, Red
                    borderWidth: 0
                }]
            },
            options: {
                plugins: {
                    legend: { display: true },
                    tooltip: { enabled: true }
                }
            }
        });

        // Agency Pie Chart Data from PHP
        const agencyLabels = <?= json_encode(array_keys($agencyCounts)) ?>;
        const agencyData = <?= json_encode(array_values($agencyCounts)) ?>;

        var data = [{
            labels: agencyLabels,
            values: agencyData,
            type: 'pie',
            hole: 0.4,  // Add this to create a donut chart
            marker: {
                colors: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8', '#6f42c1', '#fd7e14']
            }
        }];


        var layout = {
            title: {
                text: 'Agency',
                font: { size: 16 }
            },
            showlegend: true
        };

        Plotly.newPlot('agencyChart', data, layout);

        // Incident Type Distribution Chart - Styled as gradient line chart
        var incidentTypeData = <?php echo json_encode($incidentTypeData); ?>;
        var incidentLabels = incidentTypeData.map(function (item) {
            return item.incident_type;
        });
        var incidentCounts = incidentTypeData.map(function (item) {
            return item.count;
        });

        var incidentTypeChart = new ApexCharts(document.querySelector("#incidentTypeChart"), {
            chart: {
                type: 'area',
                height: 250,
                toolbar: { show: false },
                zoom: { enabled: false }
            },
            series: [{
                name: 'Incidents',
                data: incidentCounts
            }],
            xaxis: {
                categories: incidentLabels,
                labels: {
                    rotate: -45,
                    style: {
                        fontSize: '12px'
                    }
                }
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.1,
                    stops: [0, 90, 100],
                    colorStops: [
                        [
                            {
                                offset: 0,
                                color: "#ff416c",
                                opacity: 1
                            },
                            {
                                offset: 100,
                                color: "#ff4b2b",
                                opacity: 0.2
                            }
                        ]
                    ]
                }
            },
            colors: ['#ff416c'],
            dataLabels: {
                enabled: false
            },
            markers: {
                size: 4,
                colors: ["#ff416c"],
                strokeColors: "#fff",
                strokeWidth: 2,
                hover: {
                    size: 6
                }
            },
            title: {
                text: '📌 Incident Type ',
                align: 'left',
                style: {
                    fontSize: '16px'
                }
            },
            tooltip: {
                enabled: true
            },
            grid: {
                borderColor: '#f1f1f1'
            }
        });
        incidentTypeChart.render();

        // Location Distribution Chart
        var locationData = <?php echo json_encode($locationData); ?>;
        var locationLabels = locationData.map(function (item) {
            return item.location;
        });
        var locationCounts = locationData.map(function (item) {
            return item.count;
        });

        var locationChart = new ApexCharts(document.querySelector("#locationChart"), {
            chart: {
                type: 'donut',
            },
            series: locationCounts,
            labels: locationLabels,
            title: {
                text: '📍 Location',
            }
        });

        locationChart.render();
    </script>
</body>

</html>