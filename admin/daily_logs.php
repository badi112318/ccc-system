<?php
include 'db_connect.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Observation Logs</title>

    <!-- CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }

        .container-fluid {
            margin: 30px;
        }

        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        .card-header h2 {
            margin-bottom: 15px;
        }

        .table th {
            background-color: #007BFF;
            color: white;
        }

        .table th,
        .table td {
            text-align: center;
            vertical-align: middle;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .btn-sm {
            margin: 0 5px;
        }

        /* Hide elements when printing */
        @media print {

            .no-print,
            .card-header,
            .btn,
            .form-group {
                display: none !important;
            }

            .print-header {
                display: block !important;
                text-align: center;
                margin-bottom: 20px;
            }

            #print-table {
                display: table;
            }
        }

        .print-header {
            display: none;
        }
    </style>
</head>

<body>

    <div class="container-fluid">
        <div class="col-lg-12">
            <div class="card">

                <!-- Print header -->
                <div class="print-header">
                    <h2>Daily Observation Logs</h2>
                </div>

                <div class="card-header text-center no-print">
                    <h2>Daily Observation</h2>

                    <!-- Create Button -->
                    <a href="add_log.php" class="btn btn-success no-print">
                        <i class="fas fa-plus"></i> Create
                    </a>
                </div>

                <div class="card-body">
                    <table class="table table-bordered table-hover" id="daily-log-tbl">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Location</th>
                                <th>Incident Type</th>
                                <th>Details</th>
                                <th>Action Taken</th>
                                <th>Agency</th>
                                <th class="no-print">Actions</th> <!-- Hidden in print -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $qry = $conn->query("SELECT * FROM daily_logs ORDER BY date DESC, time DESC");
                            while ($row = $qry->fetch_array()):
                                $formattedDate = date('M d, Y', strtotime($row['date']));
                                $monthFilter = date('Y-m', strtotime($row['date']));
                                ?>
                                <tr data-date="<?php echo $monthFilter; ?>">
                                    <td><?php echo $formattedDate; ?></td>
                                    <td><?php echo date('h:i A', strtotime($row['time'])); ?></td>
                                    <td><?php echo htmlspecialchars($row['location']); ?></td>
                                    <td><?php echo htmlspecialchars($row['incident_type']); ?></td>
                                    <td><?php echo htmlspecialchars($row['details']); ?></td>
                                    <td><?php echo htmlspecialchars($row['action_taken']); ?></td>
                                    <td><?php echo htmlspecialchars($row['agency']); ?></td>
                                    <td class="text-center no-print">
                                        <a href="view_log.php?id=<?php echo $row['id']; ?>" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <a href="edit_log.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <!-- Delete Button -->
                                        <form action="delete_log.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>

                    <!-- Generate Button -->
                    <div class="text-center mt-4">
                        <button onclick="printTable()" class="btn btn-danger no-print">
                            <i class="fas fa-print"></i> Generate
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            let table = $('#daily-log-tbl').DataTable();

            // Filter by month using DataTables search
            $('#month-filter').on('change', function () {
                let selectedMonth = $(this).val();
                if (selectedMonth) {
                    table.column(0).search(selectedMonth).draw();
                } else {
                    table.search('').draw();
                }
            });
        });

        // Print function
        function printTable() {
            window.print();
        }
    </script>

</body>

</html>