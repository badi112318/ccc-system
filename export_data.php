<?php
$conn = new mysqli("localhost", "root", "", "cctv_db");

$data = [];

// Get data from footages
$result = $conn->query("SELECT * FROM footages");
while ($row = $result->fetch_assoc()) {
    $data['footages'][] = $row;
}

// Pwede mong idagdag ibang tables kung gusto
// $data['playback_requests'] = ...
