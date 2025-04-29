<?php
include 'db_connect.php';  // Database connection

// ✅ Handle update form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $review_start = $_POST['review_start'];
    $review_end = $_POST['review_end'];
    $result = $_POST['result'];
    $reviewed_by = $_POST['reviewed_by'];
    $footage_outcome = $_POST['footage_outcome'];
    $feedback = $_POST['feedback'];
    $status = $_POST['status'];

    $sql = "UPDATE footages 
            SET review_start = ?, review_end = ?, result = ?, reviewed_by = ?, 
                footage_outcome = ?, feedback = ?, status = ? 
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssi", $review_start, $review_end, $result, $reviewed_by, $footage_outcome, $feedback, $status, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Footage updated successfully!'); window.location='footage_review.php';</script>";
    } else {
        echo "<script>alert('Error updating footage.');</script>";
    }

    $stmt->close();
}

// ✅ Fetch all footages from the database
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$sql = "SELECT * FROM footages";

if ($status_filter) {
    $sql .= " WHERE status = '$status_filter'";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Footage Review</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-light">

    <div class="container my-5">
        <h2 class="text-center">Footage Review</h2>

        <!-- ✅ Filter by Status -->
        <form method="GET" class="form-inline my-3">
            <label for="status" class="mr-2">Filter by Status:</label>
            <select name="status" id="status" class="form-control mr-2">
                <option value="">All</option>
                <option value="Pending" <?= $status_filter == 'Pending' ? 'selected' : '' ?>>Pending</option>
                <option value="Reviewed" <?= $status_filter == 'Reviewed' ? 'selected' : '' ?>>Reviewed</option>
                <option value="Resolved" <?= $status_filter == 'Resolved' ? 'selected' : '' ?>>Resolved</option>
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>

        <!-- ✅ Display Footage Requests -->
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Phone No</th>
                    <th>Location</th>
                    <th>Incident Type</th>
                    <th>Requested On</th>
                    <th>Review Start</th>
                    <th>Review End</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['phone_no']) ?></td>
                        <td><?= htmlspecialchars($row['location']) ?></td>
                        <td><?= htmlspecialchars($row['incident_type']) ?></td>
                        <td><?= $row['date_requested'] . ' ' . $row['time_requested'] ?></td>
                        <td><?= $row['review_start'] ?: 'N/A' ?></td>
                        <td><?= $row['review_end'] ?: 'N/A' ?></td>
                        <td>
                            <span
                                class="badge badge-<?= $row['status'] == 'Pending' ? 'warning' : ($row['status'] == 'Reviewed' ? 'primary' : 'success') ?>">
                                <?= $row['status'] ?>
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-info" data-toggle="modal"
                                data-target="#editModal<?= $row['id'] ?>">Edit</button>
                        </td>
                    </tr>

                    <!-- ✅ Edit Modal -->
                    <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1" role="dialog"
                        aria-labelledby="editModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Footage #<?= $row['id'] ?></h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form method="POST" action="">
                                    <div class="modal-body">
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">

                                        <div class="form-group">
                                            <label>Review Start</label>
                                            <input type="time" name="review_start" class="form-control"
                                                value="<?= $row['review_start'] ?>">
                                        </div>

                                        <div class="form-group">
                                            <label>Review End</label>
                                            <input type="time" name="review_end" class="form-control"
                                                value="<?= $row['review_end'] ?>">
                                        </div>

                                        <div class="form-group">
                                            <label>Result</label>
                                            <textarea name="result" class="form-control"><?= $row['result'] ?></textarea>
                                        </div>

                                        <div class="form-group">
                                            <label>Reviewed By</label>
                                            <input type="text" name="reviewed_by" class="form-control"
                                                value="<?= $row['reviewed_by'] ?>">
                                        </div>

                                        <div class="form-group">
                                            <label>Outcome</label>
                                            <select name="footage_outcome" class="form-control">
                                                <option value="Useful" <?= $row['footage_outcome'] == 'Useful' ? 'selected' : '' ?>>Useful</option>
                                                <option value="Somehow Useful" <?= $row['footage_outcome'] == 'Somehow Useful' ? 'selected' : '' ?>>Somehow Useful</option>
                                                <option value="Not Useful" <?= $row['footage_outcome'] == 'Not Useful' ? 'selected' : '' ?>>Not Useful</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Feedback</label>
                                            <textarea name="feedback"
                                                class="form-control"><?= $row['feedback'] ?></textarea>
                                        </div>

                                        <div class="form-group">
                                            <label>Status</label>
                                            <select name="status" class="form-control">
                                                <option value="Pending" <?= $row['status'] == 'Pending' ? 'selected' : '' ?>>
                                                    Pending</option>
                                                <option value="Reviewed" <?= $row['status'] == 'Reviewed' ? 'selected' : '' ?>>
                                                    Reviewed</option>
                                                <option value="Resolved" <?= $row['status'] == 'Resolved' ? 'selected' : '' ?>>
                                                    Resolved</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-success">Save Changes</button>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>