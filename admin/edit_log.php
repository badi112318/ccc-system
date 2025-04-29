<?php
include 'db_connect.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid request.");
}

$log_id = mysqli_real_escape_string($conn, $_GET['id']);
$qry = $conn->query("SELECT * FROM daily_logs WHERE id = '$log_id'");
if ($qry->num_rows == 0) {
    die("Log not found.");
}
$log = $qry->fetch_assoc();

$changes = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $date_input = mysqli_real_escape_string($conn, $_POST['date']);
    $date = date('Y-m-d', strtotime($date_input)); // Convert mm/dd/yyyy to yyyy-mm-dd
    $time = mysqli_real_escape_string($conn, $_POST['time']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $incident_type = mysqli_real_escape_string($conn, $_POST['incident_type']);
    $details = mysqli_real_escape_string($conn, $_POST['details']);
    $action_taken = mysqli_real_escape_string($conn, $_POST['action_taken']);
    $agency = mysqli_real_escape_string($conn, $_POST['agency']);

    // Detect changes
    $fields = [
        'date' => $date,
        'time' => $time,
        'location' => $location,
        'incident_type' => $incident_type,
        'details' => $details,
        'action_taken' => $action_taken,
        'agency' => $agency,
    ];

    foreach ($fields as $key => $newValue) {
        if ($log[$key] !== $newValue) {
            $changes[] = ucfirst(str_replace("_", " ", $key)) . ": " . $log[$key] . " → " . $newValue;
        }
    }

    if (!empty($changes)) {
        // ✅ FIXED: Removed comma before WHERE
        $update_qry = "UPDATE daily_logs SET 
            date = '$date', 
            time = '$time', 
            location = '$location', 
            incident_type = '$incident_type', 
            details = '$details', 
            action_taken = '$action_taken',
            agency = '$agency'
            WHERE id = '$log_id'";

        if ($conn->query($update_qry)) {
            echo "<script>
                alert('Log updated successfully! Changes:\\n" . implode("\\n", $changes) . "');
                window.location.href='http://localhost/cctv/admin/index.php?page=daily';
            </script>";
        } else {
            echo "<script>alert('Error updating log.');</script>";
        }
    } else {
        echo "<script>alert('No changes were made.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Observation Log</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <style>
        body {
            background-color: #44235e;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        img {
            max-width: 100px;
            display: block;
            margin-top: 10px;
        }

        .change-summary {
            background: #ffdd57;
            padding: 10px;
            border-radius: 5px;
            font-weight: bold;
        }

        .back-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 10;
        }
    </style>
</head>

<body>

    <div class="back-btn">
        <a href="http://localhost/cctv/admin/index.php?page=daily" class="btn btn-secondary">← Back</a>
    </div>

    <div class="container">
        <h2 class="text-center">Edit Observation Log</h2>

        <?php if (!empty($changes)): ?>
            <div class="change-summary">
                <p>Changes made:</p>
                <ul>
                    <?php foreach ($changes as $change): ?>
                        <li><?php echo $change; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Date</label>
                <input type="text" name="date" id="datepicker" class="form-control"
                    value="<?php echo date('m/d/Y', strtotime($log['date'])); ?>" required>
            </div>

            <div class="form-group">
                <label>Time</label>
                <input type="time" name="time" class="form-control" value="<?php echo $log['time']; ?>" required>
            </div>

            <div class="form-group">
                <label>Location</label>
                <input type="text" name="location" class="form-control" value="<?php echo $log['location']; ?>"
                    required>
            </div>

            <div class="form-group">
                <label>Incident Type</label>
                <input type="text" name="incident_type" class="form-control"
                    value="<?php echo $log['incident_type']; ?>" required>
            </div>

            <div class="form-group">
                <label>Details</label>
                <textarea name="details" class="form-control" required><?php echo $log['details']; ?></textarea>
            </div>

            <div class="form-group">
                <label>Action Taken</label>
                <textarea name="action_taken" class="form-control"
                    required><?php echo $log['action_taken']; ?></textarea>
            </div>

            <div class="form-group">
                <label>Agency</label>
                <input type="text" name="agency" class="form-control" value="<?php echo $log['agency']; ?>" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Update Log</button>
            <a href="http://localhost/cctv/admin/index.php?page=daily" class="btn btn-secondary btn-block">Cancel</a>
        </form>
    </div>

    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        flatpickr("#datepicker", {
            dateFormat: "m/d/Y"
        });
    </script>

</body>

</html>