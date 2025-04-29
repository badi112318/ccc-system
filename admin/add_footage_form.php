<?php
// ✅ Start session and connect to DB
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_connect.php';  // Database connection
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Footage</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        body {
            background-color: #44235e;
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 900px;
            margin: 50px auto;
            padding: 30px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background: #f9f9f9;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        label {
            font-weight: bold;
        }

        .btn-submit {
            width: 100%;
        }

        .back-btn {
            margin-bottom: 20px;
        }

        .form-control {
            border-radius: 8px;
        }

        .form-control,
        select {
            font-size: 14px;
        }
    </style>
</head>

<body>

    <div class="position-absolute top-0 end-0 m-4">
        <a href="http://localhost/cctv/admin/index.php?page=footages" class="btn btn-secondary fw-bold px-4 py-2">
            ⬅ Back
        </a>
    </div>

    <div class="container">
        <h2 class="text-center mb-4">CCTV PLAYBACK REQUEST FORM</h2>

        <!-- Printable Form Area -->
        <div id="printableArea">
            <form action="save_footage.php" method="POST">

                <!-- REQUESTING PARTY Section -->
                <fieldset class="border border-3 border-dark p-3 mb-3">
                    <legend class="fw-bold px-2">REQUESTING PARTY</legend>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="date_requested" class="fw-bold">Date Requested</label>
                            <input type="text" id="date_requested" name="date_requested" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="time_requested" class="fw-bold">Time Requested (Military Time)</label>
                            <input type="text" id="time_requested" name="time_requested" class="form-control"
                                placeholder="HH:MM (24-hour format)" required onblur="validateTime(this)">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="requesting_party" class="fw-bold">Requesting Party</label>
                            <input type="text" id="requesting_party" name="requesting_party" class="form-control"
                                placeholder="Enter Name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="phone_no" class="fw-bold">Phone No.</label>
                            <input type="text" id="phone_no" name="phone_no" class="form-control"
                                placeholder="Enter Phone No." required>
                        </div>
                    </div>
                </fieldset>

                <!-- REQUESTED PLAYBACK Section -->
                <fieldset class="border border-3 border-dark p-3 mb-3">
                    <legend class="fw-bold px-2">REQUESTED PLAYBACK</legend>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="location" class="fw-bold">Location of Incident</label>
                            <input type="text" id="location" name="location" class="form-control"
                                placeholder="Enter Location" required>
                        </div>
                        <div class="col-md-3">
                            <label for="incident_date" class="fw-bold">Incident Date</label>
                            <input type="text" id="incident_date" name="incident_date" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label for="incident_time" class="fw-bold">Incident Time</label>
                            <input type="text" id="incident_time" name="incident_time" class="form-control"
                                placeholder="HH:MM (24-hour format)" required onblur="validateTime(this)">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="incident_type" class="fw-bold">Incident Type</label>
                            <input type="text" id="incident_type" name="incident_type" class="form-control"
                                placeholder="Enter Incident Type" required>
                        </div>
                        <div class="col-md-6">
                            <label for="description" class="fw-bold">Description of Incident</label>
                            <textarea id="description" name="description" class="form-control"
                                placeholder="Enter Description" rows="3" required></textarea>
                        </div>
                    </div>
                </fieldset>

                <!-- FOOTAGE OUTCOME Section -->
                <fieldset class="border border-3 border-dark p-3 mb-3">
                    <legend class="fw-bold px-2">FOOTAGE OUTCOME</legend>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="usefulness" class="fw-bold"></label>
                            <select name="usefulness" class="form-control" required>
                                <option value="Useful">Useful</option>
                                <option value="Somehow Useful">Somehow Useful</option>
                                <option value="Not Useful">Not Useful</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="client_feedback" class="fw-bold">Result</label>
                            <label for="client_feedback" class="fw-bold">Client Feedback</label>
                            <textarea name="client_feedback" class="form-control" placeholder="Enter Feedback" rows="2"
                                required></textarea>
                        </div>
                    </div>
                </fieldset>

                <!-- CDRRMO Section -->
                <fieldset class="border border-3 border-dark p-3 mb-3">
                    <legend class="fw-bold px-2">CDRRMO</legend>

                    <div class="row mb-3">
                        <div class="col-12 col-md-6">
                            <label for="caught_on_cam" class="fw-bold">Captured Outcome</label>
                            <select name="caught_on_cam" class="form-control" required>
                                <option value="Captured">Captured</option>
                                <option value="Uncaptured">Uncaptured</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12 col-md-6 text-start">
                            <label for="release_status" class="fw-bold">Release Status</label>
                            <input type="text" name="release_status" class="form-control" value="Unreleased" readonly>
                        </div>
                    </div>
                </fieldset>

                <!-- Submit Button -->
                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-primary px-4 py-2 fw-bold">
                        ✅ Submit
                    </button>
                </div>

            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        flatpickr("#date_requested", { dateFormat: "Y-m-d" });
        flatpickr("#incident_date", { dateFormat: "Y-m-d" });
    </script>

    <script>
        function validateTime(input) {
            const value = input.value.trim();
            const isValid = /^([01]\d|2[0-3]):([0-5]\d)$/.test(value); // 24-hour time

            if (!isValid && value !== "") {
                alert("⚠️ Invalid 'Time Requested'. Pakibalikan at ayusin.");
                input.value = '';
                input.focus();
            }
        }
    </script>

    <script>
        document.querySelector('form').addEventListener('submit', function (e) {
            const dateRequested = document.querySelector('#date_requested').value;
            const incidentDate = document.querySelector('#incident_date').value;

            if (!dateRequested || dateRequested === '0000-00-00') {
                alert("⚠️ Pakilagay ang tamang 'Date Requested'.");
                e.preventDefault();
                return;
            }

            if (!incidentDate || incidentDate === '0000-00-00') {
                alert("⚠️ Pakilagay ang tamang 'Incident Date'.");
                e.preventDefault();
                return;
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>