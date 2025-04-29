<?php
include 'db_connect.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Daily Observation Logs</title>

	<!-- CSS -->
	<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

	<!-- JS -->
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

	<style>
		body {
			font-family: Arial, sans-serif;
			background-color: #f8f9fa;
			color: #333;
		}

		.container-fluid {
			margin: 30px;
		}

		.card {
			box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
			border-radius: 10px;
		}

		.card-header h2 {
			margin-bottom: 15px;
		}

		.table th {
			background-color: #007BFF;
			color: white;
		}

		.table th,
		.table td {
			text-align: center;
			vertical-align: middle;
		}

		.btn-sm {
			margin: 0 3px;
		}

		@media print {

			.no-print,
			.card-header,
			.btn,
			.form-group {
				display: none !important;
			}

			.print-header {
				display: block !important;
				text-align: center;
				font-size: 24px;
				font-weight: bold;
				margin-bottom: 20px;
			}
		}

		.print-header {
			display: none;
		}
	</style>
</head>

<body>

	<div class="container-fluid">
		<div class="print-header">Daily Observation Logs</div>
		<div class="col-lg-12">
			<div class="card">

				<div class="card-header text-center no-print">
					<h2>Daily Observation Logs</h2>
					<a href="add_log.php" class="btn btn-success">
						<i class="fas fa-plus"></i> Create
					</a>
					
				</div>

				<!-- Search -->
				<div class="d-flex justify-content-end mb-4 no-print">
					<form method="GET" action="http://localhost/cctv/admin/daily.php?page=daily" style="max-width: 250px; width: 100%;">
						<div class="input-group">
							<input type="text" name="search" class="form-control" placeholder="Search..."
								value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
							<button type="submit" class="btn btn-primary">
								<i class="fa fa-search"></i>
							</button>
						</div>
					</form>
				</div>

				<div class="card-body">
					<table class="table table-bordered table-hover" id="daily-log-tbl">
						<thead>
							<tr>
								<th>Date</th>
								<th>Time</th>
								<th>Location</th>
								<th>Incident Type</th>
								<th>Details</th>
								<th>Action Taken</th>
								<th>Agency</th>
								<th class="no-print">Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$search_term = $_GET['search'] ?? '';
							$query = "SELECT * FROM daily_logs WHERE 
								location LIKE ? OR 
								incident_type LIKE ? OR 
								details LIKE ? OR 
								agency LIKE ? 
								ORDER BY date DESC, time DESC";

							$stmt = $conn->prepare($query);
							$like = "%" . $search_term . "%";
							$stmt->bind_param("ssss", $like, $like, $like, $like);
							$stmt->execute();
							$result = $stmt->get_result();

							// For agency chart
							$agencyCounts = [];

							if ($result->num_rows > 0):
								while ($row = $result->fetch_assoc()):
									$agency = $row['agency'];
									if (!isset($agencyCounts[$agency])) {
										$agencyCounts[$agency] = 0;
									}
									$agencyCounts[$agency]++;

									$formattedDate = date('M d, Y', strtotime($row['date']));
									$formattedTime = date('H:i', strtotime($row['time'])); // 24-hour format (military time)

									?>
									<tr>
										<td><?= $formattedDate ?></td>
										<td><?= $formattedTime ?></td>
										<td><?= htmlspecialchars($row['location']) ?></td>
										<td><?= htmlspecialchars($row['incident_type']) ?></td>
										<td><?= htmlspecialchars($row['details']) ?></td>
										<td><?= htmlspecialchars($row['action_taken']) ?></td>
										<td><?= htmlspecialchars($row['agency']) ?></td>
										<td class="no-print">
											<a href="view_log.php?id=<?= $row['id'] ?>" class="btn btn-info btn-sm"
												title="View">
												<i class="fas fa-eye"></i>
											</a>
											<a href="edit_log.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm"
												title="Edit">
												<i class="fas fa-edit"></i>
											</a>
											<form action="delete_log.php" method="POST" style="display:inline;"
												onsubmit="return confirm('Are you sure you want to delete this log?');">
												<input type="hidden" name="id" value="<?= $row['id'] ?>">
												<button type="submit" class="btn btn-danger btn-sm" title="Delete">
													<i class="fas fa-trash"></i>
												</button>
											</form>
										</td>
									</tr>
									<?php
								endwhile;
							else:
								echo '<tr><td colspan="8">No logs found.</td></tr>';
							endif;

							$stmt->close();
							$conn->close();
							?>
						</tbody>
					</table>

					<!-- Generate Button -->
					<div class="text-center mt-4 no-print">
						<a href="http://localhost/cctv/admin/index.php?page=daily" class="btn btn-success">
							<i class="fa fa-check" aria-hidden="true"></i> 
						</a>
					</div>


				</div>

			</div>
		</div>
	</div>

	<script>
		$(document).ready(function () {
			$('#daily-log-tbl').DataTable({
				"order": []
			});
		});

		function printTable() {
			window.print();
		}

		// Pie Chart Data from PHP
		const agencyLabels = <?= json_encode(array_keys($agencyCounts)) ?>;
		const agencyData = <?= json_encode(array_values($agencyCounts)) ?>;

		const ctx = document.getElementById('agencyChart').getContext('2d');
		new Chart(ctx, {
			type: 'pie',
			data: {
				labels: agencyLabels,
				datasets: [{
					label: 'Logs per Agency',
					data: agencyData,
					backgroundColor: [
						'#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8', '#6f42c1', '#fd7e14'
					],
					borderWidth: 1
				}]
			},
			options: {
				responsive: false,
				plugins: {
					legend: {
						position: 'bottom'
					}
				}
			}
		});
	</script>

</body>

</html>