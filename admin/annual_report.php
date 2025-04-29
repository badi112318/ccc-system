<?php
include 'db_connect.php';

// ✅ Total Incidents Monitored
$qry_monitored = $conn->query("SELECT DATE_FORMAT(date, '%M') as month, COUNT(*) as count FROM daily_logs WHERE YEAR(date) = '2025' GROUP BY MONTH(date)");
$incident_monitored = [];
while ($row = $qry_monitored->fetch_assoc()) {
    $incident_monitored[$row['month']] = $row['count'];
}

// ✅ Total Incidents Reported
$qry_reported = $conn->query("SELECT DATE_FORMAT(date, '%M') as month, COUNT(*) as count FROM daily_logs WHERE YEAR(date) = '2025' AND details IS NOT NULL AND details != '' GROUP BY MONTH(date)");
$incident_reported = [];
while ($row = $qry_reported->fetch_assoc()) {
    $incident_reported[$row['month']] = $row['count'];
}

// ✅ Footage Reviews
$qry_footage = $conn->query("SELECT DATE_FORMAT(date_requested, '%M') as month, COUNT(*) as count FROM footages WHERE YEAR(date_requested) = '2025' AND description IS NOT NULL AND description != '' GROUP BY MONTH(date_requested)");
$footage_reviews = [];
while ($row = $qry_footage->fetch_assoc()) {
    $footage_reviews[$row['month']] = $row['count'];
}

// ✅ Footage Released
$qry_released = $conn->query("SELECT DATE_FORMAT(date_requested, '%M') as month, COUNT(*) as count FROM footages WHERE YEAR(date_requested) = '2025' AND release_status = 'Released' GROUP BY MONTH(date_requested)");
$footage_released = [];
while ($row = $qry_released->fetch_assoc()) {
    $footage_released[$row['month']] = $row['count'];
}

// ✅ External Services
$qry_troubleshooting = $conn->query("SELECT DATE_FORMAT(service_date, '%M') as month, COUNT(*) as count FROM external_services WHERE YEAR(service_date) = '2025' GROUP BY MONTH(service_date)");
$troubleshooting_services = [];
while ($row = $qry_troubleshooting->fetch_assoc()) {
    $troubleshooting_services[$row['month']] = $row['count'];
}

// ✅ Month Labels
$months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

$total_monitored = $total_reported = $total_footage = $total_released = $total_troubleshooting = 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Annual CCTV Statistics</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="overflow-x-auto">
        <!-- HIDDEN SECTION START -->
        <div class="hidden">
            <form method="POST" action="update_troubleshooting.php">
                <table class="min-w-full bg-white border border-gray-300">
                    <thead>
                        <tr>
                            <th colspan="14" class="text-center text-xl font-bold py-4 bg-gray-200">CCTV OPERATION
                                STATISTICS</th>
                        </tr>
                        <tr>
                            <th colspan="14" class="text-center text-lg py-2">For the Year 2025</th>
                        </tr>
                        <tr class="bg-gray-300">
                            <th class="border px-4 py-2">Category</th>
                            <?php foreach ($months as $month): ?>
                                <th class="border px-4 py-2"><?php echo $month; ?></th>
                            <?php endforeach; ?>
                            <th class="border px-4 py-2">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Monitored -->
                        <tr class="bg-gray-100">
                            <td class="border px-4 py-2 font-bold">Total Incidents Monitored</td>
                            <?php foreach ($months as $month):
                                $count = $incident_monitored[$month] ?? 0;
                                $total_monitored += $count;
                                ?>
                                <td class="border px-4 py-2 text-center"><?php echo $count; ?></td>
                            <?php endforeach; ?>
                            <td class="border px-4 py-2 text-center font-bold"><?php echo $total_monitored; ?></td>
                        </tr>

                        <!-- Reported -->
                        <tr class="bg-gray-200">
                            <td class="border px-4 py-2 font-bold">Total Incidents Reported</td>
                            <?php foreach ($months as $month):
                                $count = $incident_reported[$month] ?? 0;
                                $total_reported += $count;
                                ?>
                                <td class="border px-4 py-2 text-center"><?php echo $count; ?></td>
                            <?php endforeach; ?>
                            <td class="border px-4 py-2 text-center font-bold"><?php echo $total_reported; ?></td>
                        </tr>

                        <!-- Footage Reviews -->
                        <tr class="bg-gray-100">
                            <td class="border px-4 py-2 font-bold">Number of Footage Reviews</td>
                            <?php foreach ($months as $month):
                                $count = $footage_reviews[$month] ?? 0;
                                $total_footage += $count;
                                ?>
                                <td class="border px-4 py-2 text-center"><?php echo $count; ?></td>
                            <?php endforeach; ?>
                            <td class="border px-4 py-2 text-center font-bold"><?php echo $total_footage; ?></td>
                        </tr>

                        <!-- Footage Released -->
                        <tr class="bg-gray-200">
                            <td class="border px-4 py-2 font-bold">Number of Footage Released</td>
                            <?php foreach ($months as $month):
                                $count = $footage_released[$month] ?? 0;
                                $total_released += $count;
                                ?>
                                <td class="border px-4 py-2 text-center"><?php echo $count; ?></td>
                            <?php endforeach; ?>
                            <td class="border px-4 py-2 text-center font-bold"><?php echo $total_released; ?></td>
                        </tr>

                        <!-- Troubleshooting (Editable) -->
                        <tr class="bg-gray-100">
                            <td class="border px-4 py-2 font-bold">CCTV Troubleshooting External Services</td>
                            <?php foreach ($months as $month):
                                $count = $troubleshooting_services[$month] ?? 0;
                                $total_troubleshooting += $count;
                                ?>
                                <td class="border px-4 py-2 text-center">
                                    <input type="number" name="troubleshooting_<?php echo $month; ?>"
                                        value="<?php echo $count; ?>" min="0" class="text-center w-full">
                                </td>
                            <?php endforeach; ?>
                            <td class="border px-4 py-2 text-center font-bold"><?php echo $total_troubleshooting; ?>
                            </td>
                        </tr>

                        <!-- Submit Button Row -->
                        <tr>
                            <td colspan="14" class="text-center py-3">
                                <button type="submit"
                                    class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">Update
                                    Troubleshooting Data</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
        <!-- HIDDEN SECTION END -->

        <!-- OK Button -->
        <div class="text-center mt-5">
            <button onclick="window.location.href='http://localhost/cctv/admin/index.php?page=annual'"
                class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700 transition">
                ok
            </button>
        </div>
    </div>
</body>

</html>