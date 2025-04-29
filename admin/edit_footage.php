<?php
// ✅ Start session and connect to DB
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_connect.php';

// ✅ Validate the ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid log ID.");
}

$logId = (int) $_GET['id'];

// ✅ Fetch the record
$query = "SELECT * FROM footages WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $logId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("No record found with ID: " . $logId);
}

$footage = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Footage</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        html,
        body {
            background-color: #44235e;
        }

        .container {
            background-color: #44235e;
        }

        .form-wrapper {
            background-color: white;
        }

        .white-text {
            color: white;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="position-absolute top-0 end-0 m-4">
            <a href="http://localhost/cctv/admin/index.php?page=footages" class="btn btn-secondary fw-bold px-4 py-2">
                ← Back
            </a>
        </div>

        <h2 class="white-text">Edit Footage (ID: <?= $footage['id'] ?>)</h2>

        <div class="form-wrapper border p-4 rounded mt-4">
            <form action="update_footage.php" method="POST">
                <input type="hidden" name="id" value="<?= $footage['id'] ?>">

                <?php
                $fields = [
                    ['label' => 'Date Requested', 'name' => 'date_requested', 'type' => 'date'],
                    ['label' => 'Time Requested', 'name' => 'time_requested', 'type' => 'text', 'class' => 'time-picker'],
                    ['label' => 'Requesting Party', 'name' => 'requesting_party'],
                    ['label' => 'Phone No.', 'name' => 'phone_no'],
                    ['label' => 'Location', 'name' => 'location'],
                    ['label' => 'Incident Date', 'name' => 'incident_date', 'type' => 'date'],
                    ['label' => 'Incident Time', 'name' => 'incident_time', 'type' => 'text', 'class' => 'time-picker'],
                    ['label' => 'Incident Type', 'name' => 'incident_type'],
                ];

                foreach ($fields as $field) {
                    $type = $field['type'] ?? 'text';
                    $class = $field['class'] ?? '';
                    $value = htmlspecialchars($footage[$field['name']] ?? '');
                    echo <<<HTML
                        <div class="mb-3">
                            <label class="form-label">{$field['label']}</label>
                            <input type="$type" name="{$field['name']}" class="form-control $class" value="$value" required>
                        </div>
                    HTML;
                }
                ?>

                <div class="mb-3">
                    <label>Description</label>
                    <textarea name="description" class="form-control"
                        required><?= htmlspecialchars($footage['description']) ?></textarea>
                </div>

                <div class="mb-3">
                    <label>Outcome</label>
                    <input type="text" name="usefulness" class="form-control"
                        value="<?= htmlspecialchars($footage['usefulness']) ?>" required>
                </div>

                <div class="mb-3">
                    <label>Captured</label>
                    <input type="text" name="caught_on_cam" class="form-control"
                        value="<?= htmlspecialchars($footage['caught_on_cam']) ?>" required>
                </div>

                <div class="mb-3">
                    <label>Agency</label>
                    <input type="text" name="agency" class="form-control"
                        value="<?= htmlspecialchars($footage['agency']) ?>" required>
                </div>

                <div class="mb-3">
                    <label>Client Feedback</label>
                    <textarea name="client_feedback" class="form-control"
                        required><?= htmlspecialchars($footage['client_feedback']) ?></textarea>
                </div>

                <div class="mb-3">
                    <label>Release Status</label>
                    <input type="text" name="release_status" class="form-control"
                        value="<?= htmlspecialchars($footage['release_status']) ?>" readonly>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    <button type="submit" class="btn btn-primary px-4">Update</button>
                    <a href="http://localhost/cctv/admin/index.php?page=footages"
                        class="btn btn-secondary ms-3 px-4">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Custom Time Picker (24-Hour Format)
        document.addEventListener('DOMContentLoaded', function () {
            const timeInputs = document.querySelectorAll('.time-picker');

            timeInputs.forEach(input => {
                input.addEventListener('focus', function () {
                    // Make sure the input is formatted to 24-hour military time when it gains focus
                    let value = input.value;
                    if (!value) value = '00:00';  // Default to 00:00 if empty
                    input.value = formatTo24Hour(value);
                });

                input.addEventListener('input', function () {
                    let value = input.value;
                    input.value = formatTo24Hour(value);
                });
            });

            // Function to format input into 24-hour military time format
            function formatTo24Hour(value) {
                let parts = value.split(':');
                if (parts.length === 2) {
                    let hour = parseInt(parts[0]);
                    let minute = parts[1];
                    // Ensure hour is within 0-23 range
                    if (hour < 0) hour = 0;
                    if (hour > 23) hour = 23;
                    // Format hour with leading zero if needed
                    if (hour < 10) hour = '0' + hour;
                    return hour + ':' + minute;
                }
                return value;
            }
        });
    </script>
</body>

</html>

<?php
$stmt->close();
$conn->close();
?>