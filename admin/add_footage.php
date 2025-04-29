<?php
// ✅ Start session and connect to DB
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_connect.php';  // Database connection

// ✅ Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize and validate inputs
    $date_requested = $_POST['date_requested'] ?? '';
    $time_requested = $_POST['time_requested'] ?? '';
    $name = $_POST['name'] ?? '';
    $phone_no = $_POST['phone_no'] ?? '';
    $location = $_POST['location_of_incident'] ?? '';
    $incident_date = $_POST['incident_date'] ?? '';
    $incident_time = $_POST['incident_time'] ?? '';
    $incident_type = $_POST['incident_type'] ?? '';
    $description = $_POST['description_of_incident'] ?? '';
    $useful = isset($_POST['useful']) ? 1 : 0;
    $somehow_useful = isset($_POST['somehow_useful']) ? 1 : 0;
    $not_useful = isset($_POST['not_useful']) ? 1 : 0;
    $captured = isset($_POST['captured']) ? 1 : 0;
    $uncaptured = isset($_POST['uncaptured']) ? 1 : 0;
    $agency = $_POST['agency'] ?? '';
    $feedback = $_POST['client_feedback'] ?? '';

    // ✅ Form validation: Required fields
    if (
        empty($date_requested) || empty($time_requested) || empty($name) || empty($phone_no) ||
        empty($location) || empty($incident_date) || empty($incident_time) ||
        empty($incident_type) || empty($description) || empty($agency) || empty($feedback)
    ) {
        $_SESSION['error'] = "Please fill in all required fields.";
        header("Location: add_footage.php");
        exit();
    }

    // ✅ Insert into the database using prepared statement
    $stmt = $conn->prepare("
        INSERT INTO footages (
            date_requested, time_requested, name, phone_no, location_of_incident, 
            incident_date, incident_time, incident_type, description_of_incident, 
            useful, somehow_useful, not_useful, captured, uncaptured, agency, client_feedback
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "ssssssssssssssss",
        $date_requested,
        $time_requested,
        $name,
        $phone_no,
        $location,
        $incident_date,
        $incident_time,
        $incident_type,
        $description,
        $useful,
        $somehow_useful,
        $not_useful,
        $captured,
        $uncaptured,
        $agency,
        $feedback
    );

    if ($stmt->execute()) {
        $_SESSION['success'] = "Footage successfully added!";
        header("Location: footages.php");
        exit();
    } else {
        $_SESSION['error'] = "Error adding footage: " . $stmt->error;
        header("Location: add_footage.php");
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Footage</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>

    <div class="container mt-4">

        <h2 class="mb-4">Add Footage</h2>

        <!-- ✅ Display session messages -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error']; ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form action="add_footage.php" method="POST">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Date Requested</label>
                    <input type="date" name="date_requested" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Time Requested</label>
                    <input type="time" name="time_requested" class="form-control" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Phone No.</label>
                    <input type="text" name="phone_no" class="form-control" required>
                </div>
            </div>

            <div class="mb-3">
                <label>Location of Incident</label>
                <input type="text" name="location_of_incident" class="form-control" required>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Incident Date</label>
                    <input type="date" name="incident_date" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Incident Time</label>
                    <input type="time" name="incident_time" class="form-control" required>
                </div>
            </div>

            <div class="mb-3">
                <label>Incident Type</label>
                <input type="text" name="incident_type" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Description of Incident</label>
                <textarea name="description_of_incident" class="form-control" rows="3" required></textarea>
            </div>

            <!-- ✅ Outcome checkboxes -->
            <div class="mb-3">
                <label>Outcome</label><br>
                <input type="checkbox" name="useful" value="1"> Useful
                <input type="checkbox" name="somehow_useful" value="1"> Somehow Useful
                <input type="checkbox" name="not_useful" value="1"> Not Useful
            </div>

            <div class="mb-3">
                <label>Captured</label><br>
                <input type="checkbox" name="captured" value="1"> Captured
                <input type="checkbox" name="uncaptured" value="1"> Uncaptured
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Agency</label>
                    <input type="text" name="agency" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Client Feedback</label>
                    <input type="text" name="client_feedback" class="form-control" required>
                </div>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary">Save</button>
                <a href="footages.php" class="btn btn-secondary">Cancel</a>
            </div>

        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>