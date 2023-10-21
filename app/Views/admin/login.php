<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Login - Administrator</title>
	<link href="<?= base_url("css/bootstrap.min.css") ?>" rel="stylesheet" />
	<link href="<?= base_url("css/font-awesome.min.css") ?>" rel="stylesheet" />

	<script src="js/jquery.js"></script>
	<script src="js/popper.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<style>
		body {
			color: #fff;
			background: #737373;
		}

		.form-control {
			min-height: 41px;
			background: #fff;
			box-shadow: none !important;
			border-color: #e3e3e3;
		}

		.form-control:focus {
			border-color: #70c5c0;
		}

		.form-control,
		.btn {
			border-radius: 2px;
		}

		.login-form {
			width: 350px;
			margin: 0 auto;
			padding: 100px 0 30px;
		}

		.login-form form {
			color: #7a7a7a;
			border-radius: 2px;
			margin-bottom: 15px;
			font-size: 13px;
			background: #ececec;
			box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
			padding: 30px;
			position: relative;
		}

		.login-form h2 {
			font-size: 22px;
			margin: 35px 0 25px;
		}

		.login-form .avatar {
			position: absolute;
			margin: 0 auto;
			left: 0;
			right: 0;
			top: -50px;
			width: 95px;
			height: 95px;
			border-radius: 50%;
			z-index: 9;
			background: #70c5c0;
			padding: 15px;
			box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.1);
		}

		.login-form .avatar img {
			width: 100%;
		}

		.login-form input[type="checkbox"] {
			position: relative;
			top: 1px;
		}

		.login-form .btn,
		.login-form .btn:active {
			font-size: 16px;
			font-weight: bold;
			background: #70c5c0 !important;
			border: none;
			margin-bottom: 20px;
		}

		.login-form .btn:hover,
		.login-form .btn:focus {
			background: #50b8b3 !important;
		}

		.login-form a {
			color: #fff;
			text-decoration: underline;
		}

		.login-form a:hover {
			text-decoration: none;
		}

		.login-form form a {
			color: #7a7a7a;
			text-decoration: none;
		}

		.login-form form a:hover {
			text-decoration: underline;
		}

		.login-form .bottom-action {
			font-size: 14px;
		}
	</style>
</head>

<body>
	<div class="login-form">
		<form action="admin/login" method="post">
			<div class="avatar">
				<li style="font-size:63px;color:white;margin-left:10px" class="fa fa-user text-center"></li>
			</div>
			<h2 class="text-center">Admin Login</h2>
			<p class="text-center"><?= $check ?></p>
			<?= $message ?>
			<div class="form-group">
				<input type="text" class="form-control" name="username" placeholder="Username" required="required">
			</div>
			<div class="form-group">
				<input type="password" class="form-control" name="password" placeholder="Password" required="required">
			</div>
			<div class="form-group">
				<button type="submit" class="btn btn-danger btn-lg btn-block">Sign in</button>
			</div>
		</form>
	</div>
</body>

</html>