<!DOCTYPE html>
<html lang="en">
<?php
session_start();
include('./db_connect.php');
ob_start();

ob_end_flush();
?>

<head>
	<meta charset="utf-8">
	<meta content="width=device-width, initial-scale=1.0" name="viewport">
	<title>CCTV UNIT</title>

	<!-- Bootstrap CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

	<style>
		body {
			background-color: #343a40;
			height: 100vh;
			display: flex;
			justify-content: center;
			align-items: center;
		}

		.card {
			background: #fff;
			padding: 25px;
			border-radius: 12px;
			/* ✅ Mas rounded na border */
			border: 2px solid #007bff;
			/* ✅ Blue border */
			box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
			/* ✅ Soft shadow */
			width: 100%;
			max-width: 400px;
		}

		.text-title {
			font-size: 22px;
			font-weight: bold;
			text-align: center;
			margin-bottom: 15px;
			color: white;
		}

		.btn-block {
			width: 100%;
		}
	</style>
</head>

<body>

	<div class="container">
		<h2 class="text-center mt-4 pt-3 text-white">CCTV UNIT</h2>
		<div class="card mx-auto">
			<div class="card-body">
				<form id="login-form">
					<div class="mb-3">
						<label for="username" class="form-label">Username</label>
						<input type="text" id="username" name="username" class="form-control">
					</div>
					<div class="mb-3">
						<label for="password" class="form-label">Password</label>
						<input type="password" id="password" name="password" class="form-control">
					</div>
					<button type="submit" class="btn btn-primary btn-block w-100">Login</button>
				</form>
			</div>
		</div>
		<!-- Move Back to Home button outside the card -->
		<div class="text-center mt-3">
			<a href="http://localhost/cctv/index.php" class="btn btn-secondary">Back to Home</a>
		</div>
	</div>

	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script>
		$('#login-form').submit(function (e) {
			e.preventDefault();
			$('button[type="submit"]').attr('disabled', true).text('Logging in...');
			if ($(this).find('.alert-danger').length > 0) {
				$(this).find('.alert-danger').remove();
			}
			$.ajax({
				url: 'ajax.php?action=login',
				method: 'POST',
				data: $(this).serialize(),
				error: function (err) {
					console.log(err);
					$('button[type="submit"]').removeAttr('disabled').text('Login');
				},
				success: function (resp) {
					if (resp == 1) {
						location.href = 'http://localhost/cctv/admin/index.php'; // Redirect to admin page after login
					} else {
						$('#login-form').prepend('<div class="alert alert-danger">Username or password is incorrect.</div>');
						$('button[type="submit"]').removeAttr('disabled').text('Login');
					}
				}
			});
		});
	</script>

</body>

</html>	