<?php
include 'db_connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $qry = $conn->query("SELECT * FROM daily_logs WHERE id = $id");
    if ($qry->num_rows > 0) {
        $row = $qry->fetch_assoc();
    } else {
        echo "<h3>Log not found!</h3>";
        exit;
    }
} else {
    echo "<h3>Invalid Request!</h3>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Details</title>
    <!-- Add Bootstrap CSS for positioning and button style -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #44235e;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            position: relative;
        }

        .log-container {
            width: 50%;
            max-width: 600px;
            background: #ffffff;
            border: 2px solid #007bff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            position: relative;
            z-index: 1;
        }

        h2 {
            background: #007bff;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .log-details {
            text-align: left;
        }

        .log-details p {
            margin: 10px 0;
            font-size: 16px;
            padding: 8px;
            background: #f9f9f9;
            border-left: 4px solid #007bff;
        }

        /* Positioning the "Back" button at the top-right corner */
        .back-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 10;
        }

        @media screen and (max-width: 768px) {
            .log-container {
                width: 90%;
            }
        }
    </style>
</head>

<body>

    <!-- Back Button Positioned Top Right -->
    <div class="back-btn">
        <a href="http://localhost/cctv/admin/index.php?page=daily" class="btn btn-secondary">← Back</a>
    </div>

    <div class="log-container">
        <h2>Log Details</h2>
        <div class="log-details">
            <p><strong>Date:</strong> <?php echo date('M d, Y', strtotime($row['date'])); ?></p>
            <p><strong>Time:</strong> <?php echo $row['time']; ?></p>
            <p><strong>Location:</strong> <?php echo $row['location']; ?></p>
            <p><strong>Incident Type:</strong> <?php echo $row['incident_type']; ?></p>
            <p><strong>Details:</strong> <?php echo $row['details']; ?></p>
            <p>
                <strong>Action Taken:</strong> <?php echo $row['action_taken']; ?> <br>
                <strong>Agency:</strong> <?php echo !empty($row['agency']) ? $row['agency'] : 'N/A'; ?>
            </p>

            <?php if (!empty($row['image_path'])): ?>
                <p><strong>Uploaded Image:</strong></p>
                <img src="<?php echo $row['image_path']; ?>" alt="Log Image"
                    style="max-width: 100%; height: auto; border: 1px solid #ccc;">
            <?php endif; ?>
        </div>

    </div>

</body>

</html>