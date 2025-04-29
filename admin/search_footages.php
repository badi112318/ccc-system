<?php
include 'db_connect.php';

$search = isset($_POST['search']) ? $_POST['search'] : '';

$query = "SELECT * FROM footages WHERE requesting_party LIKE ? OR description LIKE ? ORDER BY id DESC";
$stmt = $conn->prepare($query);
$search_term = "%" . $search . "%";
$stmt->bind_param("ss", $search_term, $search_term);
$stmt->execute();
$result = $stmt->get_result();

$counter = 1;

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$counter}</td>
            <td>" . date('Y-m-d', strtotime($row['date_requested'])) . "</td>
            <td>" . ($row['time_requested'] ? date('h:i A', strtotime($row['time_requested'])) : '-') . "</td>
            <td>" . htmlspecialchars($row['requesting_party'] ?? '-') . "</td>
            <td>" . htmlspecialchars($row['phone_no'] ?? '-') . "</td>
            <td>" . htmlspecialchars($row['location'] ?? '-') . "</td>
            <td>" . date('Y-m-d', strtotime($row['incident_date'])) . "</td>
            <td>" . ($row['incident_time'] ? date('h:i A', strtotime($row['incident_time'])) : '-') . "</td>
            <td>" . htmlspecialchars($row['incident_type'] ?? '-') . "</td>
            <td>" . htmlspecialchars($row['caught_on_cam'] ?? '-') . "</td>
            <td>" . htmlspecialchars($row['agency'] ?? '-') . "</td>
            <td>" . htmlspecialchars($row['usefulness'] ?? '-') . "</td>
            <td>" . htmlspecialchars($row['release_status'] ?? '-') . "</td>
            <td>
                <a href='edit_footage.php?id={$row['id']}' class='btn btn-warning btn-sm'><i class='fas fa-edit'></i></a>
                <a href='delete_footage.php?id={$row['id']}&redirect=true' class='btn btn-danger btn-sm'
                   onclick='return confirm(\"Are you sure you want to delete this record?\")'>
                   <i class='fas fa-trash'></i>
                </a>
            </td>
        </tr>";
        $counter++;
    }
} else {
    echo "<tr><td colspan='14' class='text-center'>No records found.</td></tr>";
}
?>