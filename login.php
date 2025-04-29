<?php
session_start();
?>

<div class="container-fluid">
	<form id="login-frm" method="POST">
		<div class="form-group">
			<label for="email" class="control-label">Email</label>
			<input type="email" name="email" id="email" required class="form-control">
		</div>
		<div class="form-group">
			<label for="password" class="control-label">Password</label>
			<input type="password" name="password" id="password" required class="form-control">
			<small><a href="javascript:void(0)" id="new_account">Create New Account</a></small>
		</div>
		<button type="submit" class="btn btn-primary btn-sm">Login</button>
		<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
	</form>
</div>

<style>
	#uni_modal .modal-footer {
		display: none;
	}
</style>

<script>
	$(document).ready(function () {
		// Open new account modal
		$('#new_account').click(function () {
			uni_modal("Create an Account", 'signup.php?redirect=index.php?page=checkout');
		});

		// Handle form submission
		$('#login-frm').submit(function (e) {
			e.preventDefault();

			// Clear previous alerts
			$('#login-frm .alert').remove();

			$.ajax({
				url: 'admin/ajax.php?action=login2',
				method: 'POST',
				data: $(this).serialize(),
				dataType: 'json',   // Expecting JSON response
				beforeSend: function () {
					start_load();
				},
				success: function (resp) {
					end_load();

					if (resp.status === 'success') {
						// Redirect on success
						let redirectUrl = '<?php echo isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php?page=home'; ?>';
						window.location.href = redirectUrl;
					} else {
						// Display error message
						$('#login-frm').prepend('<div class="alert alert-danger">' + resp.message + '</div>');
					}
				},
				error: function (xhr, status, error) {
					end_load();
					console.error('AJAX Error:', status, error);
					$('#login-frm').prepend('<div class="alert alert-danger">An error occurred. Please try again.</div>');
				}
			});
		});
	});
</script>