<?php
// Start session and connect to the database
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_connect.php';

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Get the year and search term from the URL or use default values
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
$search_term = isset($_GET['search']) ? $_GET['search'] : '';

// Fetch data from footages.php, including time_requested
$query = "
    SELECT requesting_party, 
           DATE_FORMAT(date_requested, '%Y-%m-%d %H:%i:%s') AS date_time, 
           time_requested, 
           incident_type, 
           description, 
           caught_on_cam, 
           location AS cam_location
    FROM footages 
    WHERE YEAR(date_requested) = ? 
    AND (requesting_party LIKE ? OR description LIKE ? OR DATE_FORMAT(date_requested, '%Y-%m-%d') LIKE ?) 
    ORDER BY date_requested DESC
";
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

// Fetch ONLY released footages for the "FOOTAGE REVIEW PER REQUEST (Release)" section
$released_query = "
    SELECT requesting_party, 
           DATE_FORMAT(date_requested, '%Y-%m-%d %H:%i:%s') AS date_time, 
           time_requested, 
           incident_type, 
           description
    FROM footages 
    WHERE YEAR(date_requested) = ? 
    AND release_status = 'Released'
    AND (requesting_party LIKE ? OR description LIKE ? OR DATE_FORMAT(date_requested, '%Y-%m-%d') LIKE ?)
    ORDER BY date_requested DESC
";
$released_stmt = $conn->prepare($released_query);
$released_stmt->bind_param("ssss", $year, $search_term_wildcard, $search_term_wildcard, $search_term_wildcard);
$released_stmt->execute();
$released_result = $released_stmt->get_result();
$releasedData = [];
while ($row = $released_result->fetch_assoc()) {
    $releasedData[] = $row;
}
$released_stmt->close();

// Top 10 Incidents
$incident_query = "
    SELECT incident_type, COUNT(*) AS total 
    FROM footages 
    WHERE YEAR(date_requested) = ?
    AND incident_type IS NOT NULL AND incident_type != ''
    GROUP BY incident_type 
    ORDER BY total DESC 
    LIMIT 10
";
$incident_stmt = $conn->prepare($incident_query);
$incident_stmt->bind_param("i", $year);
$incident_stmt->execute();
$incident_result = $incident_stmt->get_result();
$topIncidents = [];
while ($row = $incident_result->fetch_assoc()) {
    $topIncidents[] = $row;
}
$incident_stmt->close();

// Top 10 Locations
$location_query = "
    SELECT location, COUNT(*) AS total 
    FROM footages 
    WHERE YEAR(date_requested) = ?
    AND location IS NOT NULL AND location != ''
    GROUP BY location 
    ORDER BY total DESC 
    LIMIT 10
";
$location_stmt = $conn->prepare($location_query);
$location_stmt->bind_param("i", $year);
$location_stmt->execute();
$location_result = $location_stmt->get_result();
$topLocations = [];
while ($row = $location_result->fetch_assoc()) {
    $topLocations[] = $row;
}
$location_stmt->close();

// Fetch all footages based on release status
$query = "SELECT requesting_party, date_requested, time_requested, incident_type, description, caught_on_cam 
          FROM footages 
          WHERE release_status IN ('Released', 'Unreleased')";
$result = mysqli_query($conn, $query);

// Close the database connection
mysqli_close($conn);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Annual Report - <?= $year ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
</head>

<body class="bg-white text-black font-sans">
    <div class="flex flex-col items-center">
        <div class="flex justify-between items-center w-full p-4">
            <div class="flex space-x-4">
                <img alt="City of Malaybalay Official Seal" class="h-20" src="https://placehold.co/100x100" />
                <img alt="Disaster Risk Reduction and Management Office Seal" class="h-20"
                    src="https://placehold.co/100x100" />
            </div>
            <div class="text-center">
                <h1 class="text-4xl font-bold">CDRRMO</h1>
                <p class="text-lg">City Disaster Risk Reduction and Management Office</p>
            </div>
            <div class="flex flex-col items-start space-y-2">
                <div class="flex items-center space-x-2"><i
                        class="fas fa-envelope"></i><span>malaybalaycitydrrmo@gmail.com</span></div>
                <div class="flex items-center space-x-2"><i class="fas fa-phone"></i><span>(088) 813-3611</span></div>
                <div class="flex items-center space-x-2"><i class="fas fa-facebook"></i><span>CDRRMO-Malaybalay</span>
                </div>
                <div class="flex items-center space-x-2"><i class="fas fa-map-marker-alt"></i><span>CDRRMO Building,
                        Barangay 9, Malaybalay City, Bukidnon</span></div>
            </div>
        </div>
        <div class="w-full bg-blue-800 text-white text-center py-2">
            <span class="text-2xl font-bold">CCC</span>
            <span class="text-lg">Communication Command Central Section</span>
        </div>
    </div>

    <div class="container mx-auto p-4">
        <div class="text-center mb-4">
            <h1 class="text-purple-700 font-bold text-lg uppercase">FOOTAGE REVIEW PER REQUEST</h1>
            <p class="text-purple-500 text-sm">For the year <?= htmlspecialchars($year) ?></p>
        </div>

        <div class="mt-5 w-full max-w-5xl mx-auto">
            <?php if (!empty($releasedData)): ?>
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Requesting Party</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Incident Type</th>
                        <th>Details</th>
                        <th>Caught on Cam</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                        $i = 1;
                        while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= $i++; ?></td>
                                <td><?= htmlspecialchars($row['requesting_party']); ?></td>
                                <td><?= htmlspecialchars($row['date_requested']); ?></td>
                                <td><?= htmlspecialchars($row['time_requested']); ?></td>
                                <td><?= htmlspecialchars($row['incident_type']); ?></td>
                                <td><?= htmlspecialchars($row['description']); ?></td>
                                <td>
                                    <table class="w-full h-full border border-gray-300">
                                        <tr class="h-4">
                                            <td class="<?= $row['caught_on_cam'] === 'Uncaptured' ? 'bg-white-400' : 'bg-gray-200'; ?> w-1/2 border-r border-gray-300">
                                            </td>
                                            <td class="<?= $row['caught_on_cam'] === 'Captured' ? 'bg-yellow-400' : 'bg-gray-200'; ?> w-1/2"></td>
                                        </tr>
                                    </table>
                                </td>




                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-muted text-center mt-3">No footage data found for <?= htmlspecialchars($year) ?>.</p>
            <?php endif; ?>
        </div>
        <div class="container mx-auto p-4">
        <div class="text-center mb-4">
            <h1 class="text-purple-700 font-bold text-lg uppercase">FOOTAGE REVIEW PER REQUEST  ()</h1>
            <p class="text-purple-500 text-sm">For the year <?= htmlspecialchars($year) ?></p>
            </div>
        
            <!-- Footage Review Table -->
            <div class="mt-5 w-full max-w-5xl mx-auto">
                <?php if (!empty($footagesData)): ?>
                    <table class="table table-bordered text-sm text-center align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Requesting Party</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Incident Type</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($releasedData as $index => $footage): ?>
                                <?php $formattedTime = date('h:i A', strtotime($footage['time_requested'])); ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($footage['requesting_party']) ?></td>
                                    <td><?= date('Y-m-d', strtotime($footage['date_time'])) ?></td>
                                    <td><?= $formattedTime ?: '-' ?></td>
                                    <td><?= htmlspecialchars($footage['incident_type']) ?></td>
                                    <td><?= htmlspecialchars($footage['description']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                <?php else: ?>
                    <p class="text-muted text-center mt-3">No footage data found for <?= htmlspecialchars($year) ?>.</p>
                <?php endif; ?>
            </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-10">
            <!-- Top 10 Incident Section -->
            <div class="p-3 text-sm">
                <h6 class="font-bold mb-2 text-black uppercase">Top 10 Incidents</h6>
                <?php if (count($topIncidents) > 0): ?>
                    <ul class="list-none text-black">
                        <?php
                        $rank = 1;
                        $topIncidents = array_slice($topIncidents, 0, 10); // Limit to top 10
                        foreach ($topIncidents as $row): ?>
                            <li class="font-bold text-purple-700">
                                <?= $rank++ ?>. <?= htmlspecialchars($row['incident_type']) ?> (<?= $row['total'] ?>)
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-gray-600">No incident data available for this year.</p>
                <?php endif; ?>
            </div>

        
            <!-- Top 10 Locations Section -->
            <div class="p-3 text-sm">
                <h6 class="font-bold mb-2 text-black uppercase">Top 10 Locations</h6>
                <?php if (count($topLocations) > 0): ?>
                    <ul class="list-none text-black">
                        <?php
                        $rank = 1;
                        $topLocations = array_slice($topLocations, 0, 10); // Limit to top 10
                        foreach ($topLocations as $row): ?>
                            <li class="font-bold text-purple-700">
                                <?= $rank++ ?>. <?= htmlspecialchars($row['location']) ?> (<?= $row['total'] ?>)
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-gray-600">No location data available for this year.</p>
                <?php endif; ?>
            </div>


        </div>


        <div class="grid grid-cols-4 text-center text-xs mt-16 mb-2">
            <div>Prepared by:</div>
            <div>Checked by:</div>
            <div>Noted:</div>
            <div>Approved:</div>
        </div>
        <div class="grid grid-cols-4 gap-4 text-center text-xs">
            <div>
                <div class="border-t border-black w-48 mx-auto mb-1"></div>
                <div class="font-bold text-green-800">MICHELLE R. SALES</div>
                <div class="text-gray-600">CCTV In-Charge</div>
            </div>
            <div>
                <div class="border-t border-black w-48 mx-auto mb-1"></div>
                <div class="font-bold text-green-800">RITCHEL B. AGNE, RN, EMT</div>
                <div class="text-gray-600">CCC Section Head</div>
            </div>
            <div>
                <div class="border-t border-black w-48 mx-auto mb-1"></div>
                <div class="font-bold text-green-800">ARIAN JOHNSON B. CAGA-ANAN</div>
                <div class="text-gray-600">Operations and Warning Division Head</div>
            </div>
            <div>
                <div class="border-t border-black w-48 mx-auto mb-1"></div>
                <div class="font-bold text-green-800">ALAN J. COMISO</div>
                <div class="text-gray-600">CGDH-I (CDRRMO)</div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('.table').DataTable({
                paging: true,
                searching: true,
                info: true,
                lengthMenu: [10, 25, 50, 100],
                pageLength: 25
            });
        });
    </script>
</body>

</html>