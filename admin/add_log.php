<?php
include 'db_connect.php';
date_default_timezone_set('Asia/Manila');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Try to create the DateTime object and check if it was successful
    $dateObj = DateTime::createFromFormat('m/d/Y', trim($_POST['date']));

    if ($dateObj === false) {
        echo "<script>alert('Invalid date format. Please use MM/DD/YYYY.'); window.history.back();</script>";
        exit();
    }

    $date = $dateObj->format('Y-m-d');
    $time_input = trim($_POST['time']);
    $time = $date . ' ' . $time_input . ':00'; // full datetime with military format

    $location = ($_POST['location'] === "Others") ? trim($_POST['other_location']) : trim($_POST['location']);
    $incident_type = ($_POST['incident_type'] === "Others") ? trim($_POST['other_incident_type']) : trim($_POST['incident_type']);
    $details = trim($_POST['details']);
    $action_taken = ($_POST['action_taken'] === "Others") ? trim($_POST['other_action']) : trim($_POST['action_taken']);
    $agency = ($_POST['agency'] === "Others") ? trim($_POST['other_agency']) : trim($_POST['agency']);

    if (empty($date) || empty($time_input) || empty($location) || empty($incident_type) || empty($details) || empty($action_taken) || empty($agency)) {
        echo "<script>alert('All fields are required.'); window.history.back();</script>";
        exit();
    }

    $stmt = $conn->prepare("
        INSERT INTO daily_logs (date, time, location, incident_type, details, action_taken, agency)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("sssssss", $date, $time, $location, $incident_type, $details, $action_taken, $agency);

    if ($stmt->execute()) {
        echo "<script>alert('Daily log added successfully!'); window.location.href = 'http://localhost/cctv/admin/index.php?page=daily';</script>";
    } else {
        echo "<script>alert('Error: Could not add log. Please try again.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en-GB">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Daily Log</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #44235e;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            border: 2px solid #ddd;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            width: 100%;
            padding: 10px;
            background: rgb(82, 129, 69);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background: #218838;
        }

        #other_action,
        #other_agency,
        #other_location,
        #other_incident_type {
            display: none;
        }
    </style>
</head>

<body>
    <div class="position-absolute top-0 end-0 m-4">
        <a href="http://localhost/cctv/admin/index.php?page=daily" class="btn btn-secondary">← Back</a>
    </div>

    <div class="container">
        <h2>Add Daily Log</h2>
        <form action="add_log.php" method="POST">
            <label>Date:</label>
            <input type="text" id="date" name="date" required placeholder="MM/DD/YYYY">

            <label>Time:</label>
            <input type="text" id="time" name="time" required placeholder="HH:MM (24-hour format)" oninput="validateTime(this)">
            

            <label>Location:</label>
            <select name="location" required onchange="checkLocation(this.value)">
                <option value="">Select Location</option>
                <option value="Barracks">Barracks</option>
                <option value="Carbajal St">Carbajal St</option>
                <option value="Fuji">Fuji</option>
                <option value="Diversion">Diversion</option>
                <option value="Polymedic">Polymedic</option>
                <option value="Gaisano">Gaisano</option>
                <option value="Moriah">Moriah</option>
                <option value="Sayre Highway-BSU">Sayre Highway-BSU</option>
                <option value="Isidro Carillo">Isidro Carillo</option>
                <option value="Sumpong-Diversion">Sumpong-Diversion</option>
                <option value="Sayre Highway-Claro St.">Sayre Highway-Claro St.</option>
                <option value="Sayre Highway Dunkin">Sayre Highway Dunkin</option>
                <option value="Sayre Highway-Rizal St.">Sayre Highway Rizal St.</option>
                <option value="Abello-Tabios St.">Abello-Tabios St.</option>
                <option value="Isidro-Moreno St.">Isidro-Moreno St.</option>
                <option value="Isidro-Carillo">Isidro-Carillo</option>
                <option value="Old Cityhall Brgy. 1">Old Cityhall Brgy. 1</option>
                <option value="Terminal Entrance Brgy. 9">Terminal Entrance Brgy. 9</option>
                <option value="Dunkin Market Brgy. 9">Dunkin Market Brgy. 9</option>
                <option value="N/A">N/A</option>
                <option value="Others">Others</option>
            </select>
            <input type="text" id="other_location" name="other_location" placeholder="Specify other location">

            <label>Incident Type:</label>
            <select name="incident_type" required onchange="checkIncidentType(this.value)">
                <option value="">Select Incident Type</option>
                <option value="V.A">V.A</option>
                <option value="No helmet">No helmet</option>
                <option value="Collision">Collision</option>
                <option value="Congestion">Congestion</option>
                <option value="Illegal Loading/Unloading">Illegal Loading/Unloading</option>
                <option value="Illegal Turn">Illegal Turn</option>
                <option value="Public Safety">Public Safety</option>
                <option value="Water Leakage">Water Leakage</option>
                <option value="N/A">N/A</option>
                <option value="Others">Others</option>
            </select>
            <input type="text" id="other_incident_type" name="other_incident_type"
                placeholder="Specify other incident type">

            <label>Details:</label>
            <textarea name="details" required></textarea>

            <label>Action Taken:</label>
            <select name="action_taken" required onchange="checkActionTaken(this.value)">
                <option value="">Select Action</option>
                <option value="Seen">Seen</option>
                <option value="10-5 911">10-5 911</option>
                <option value="Done">Done</option>
                <option value="Monitored">Monitored</option>
                <option value="Brown out and building">Brown out and building</option>
                <option value="Ticket by TMC">Ticket by TMC</option>
                <option value="N/A">N/A</option>
                <option value="Others">Others</option>
            </select>
            <input type="text" id="other_action" name="other_action" placeholder="Specify other action">

            <label>Agency:</label>
            <select name="agency" required onchange="checkAgency(this.value)">
                <option value="">Select Agency</option>
                <option value="TMC">TMC</option>
                <option value="MCPS">MCPS</option>
                <option value="BFP">BFP</option>
                <option value="EMS">EMS</option>
                <option value="BPSO">BPSO</option>
                <option value="N/A">N/A</option>
                <option value="Others">Others</option>
            </select>
            <input type="text" id="other_agency" name="other_agency" placeholder="Specify other agency">

            <button type="submit">Submit</button>
        </form>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        flatpickr("#date", {
            dateFormat: "m/d/Y" // mm/dd/yyyy
        });

        function checkActionTaken(value) {
            var input = document.getElementById("other_action");
            input.style.display = (value === "Others") ? "block" : "none";
            input.required = (value === "Others");
        }

        function checkAgency(value) {
            var input = document.getElementById("other_agency");
            input.style.display = (value === "Others") ? "block" : "none";
            input.required = (value === "Others");
        }

        function checkLocation(value) {
            var input = document.getElementById("other_location");
            input.style.display = (value === "Others") ? "block" : "none";
            input.required = (value === "Others");
        }

        function checkIncidentType(value) {
            var input = document.getElementById("other_incident_type");
            input.style.display = (value === "Others") ? "block" : "none";
            input.required = (value === "Others");
        }
    </script>
    <script>
        function validateTime(input) {
            // Regular expression for 24-hour time format (HH:MM)
            var regex = /^([01]?[0-9]|2[0-3]):([0-5]?[0-9])$/;
            if (!regex.test(input.value)) {
                input.setCustomValidity('Please enter a valid time in HH:MM format (24-hour).');
            } else {
                input.setCustomValidity('');
            }
        }
    </script>
</body>

</html>