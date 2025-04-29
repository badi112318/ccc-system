<?php
include 'db_connect.php';

// Get selected month from the URL, default to current month if not set
$selected_month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');

// Query to fetch complaints for the selected month
$qry = $conn->query("
    SELECT * FROM complaints 
    WHERE DATE_FORMAT(date, '%Y-%m') = '$selected_month'
    ORDER BY unix_timestamp(date_created) DESC
");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Complaints (<?php echo $selected_month; ?>)</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
</head>

<body>

    <div class="container mt-4">
        <h2 class="text-center">Complaints for <?php echo date('F Y', strtotime($selected_month . '-01')); ?></h2>
        <a href="complaints.php" class="btn btn-secondary mb-3">Back to All Complaints</a>

        <table class="table table-bordered table-hover" id="monthly-complaints-tbl">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Location</th>
                    <th>Incident Type</th>
                    <th>Details</th>
                    <th>Action Taken</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $qry->fetch_array()): ?>
                    <tr>
                        <td><?php echo date('M d, Y', strtotime($row['date'])); ?></td>
                        <td><?php echo $row['time']; ?></td>
                        <td><?php echo $row['location']; ?></td>
                        <td><?php echo $row['incident_type']; ?></td>
                        <td><?php echo $row['details']; ?></td>
                        <td><?php echo $row['action_taken']; ?></td>
                        <td class="text-center">
                            <button class="action-btn view-btn btn btn-info btn-sm"
                                data-id="<?php echo $row['id']; ?>">View</button>
                            <button class="action-btn edit-btn btn btn-warning btn-sm"
                                data-id="<?php echo $row['id']; ?>">Edit</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function () {
            $('#monthly-complaints-tbl').DataTable();

            $('.view-btn').click(function () {
                let complaintId = $(this).data('id');
                window.location.href = "view_complaint.php?id=" + complaintId;
            });

            $('.edit-btn').click(function () {
                let complaintId = $(this).data('id');
                window.location.href = "edit_complaint.php?id=" + complaintId;
            });
        });
    </script>

</body>

</html>