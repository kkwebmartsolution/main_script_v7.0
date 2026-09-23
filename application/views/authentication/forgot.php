<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width, initial-scale=1" name="viewport">
	<meta name="keywords" content="">
	<meta name="description" content="<?php echo $global_config['institute_name']; ?>">
	<meta name="author" content="<?php echo $global_config['institute_name']; ?>">
	<title><?php echo translate('password_restoration'); ?> | <?php echo $global_config['institute_name']; ?></title>
	<link rel="shortcut icon" href="<?php echo base_url('assets/images/favicon.png'); ?>">

	<!-- Google Fonts -->
	<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
	
	<!-- Vendor CSS -->
	<link rel="stylesheet" href="<?php echo base_url('assets/vendor/bootstrap/css/bootstrap.css'); ?>">
	<link rel="stylesheet" href="<?php echo base_url('assets/vendor/font-awesome/css/all.min.css'); ?>">
	<link rel="stylesheet" href="<?php echo base_url('assets/vendor/sweetalert/sweetalert-custom.css'); ?>">
	
	<script src="<?php echo base_url('assets/vendor/jquery/jquery.js'); ?>"></script>
	<script src="<?php echo base_url('assets/vendor/sweetalert/sweetalert.min.js'); ?>"></script>
	
	<script type="text/javascript">
		var base_url = '<?php echo base_url(); ?>';
	</script>

	<!-- Modern SaaS Login & Password Restoration Custom Styling -->
	<style>
		*, *::before, *::after {
			box-sizing: border-box;
		}
		body.ramom-login-body {
			font-family: 'Plus Jakarta Sans', sans-serif;
			background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
			min-height: 100vh;
			display: flex;
			align-items: center;
			justify-content: center;
			margin: 0;
			padding: 20px;
			color: #334155;
			position: relative;
			overflow-x: hidden;
		}

		/* Ambient background glows */
		body.ramom-login-body::before {
			content: '';
			position: absolute;
			top: -100px;
			left: -100px;
			width: 500px;
			height: 500px;
			background: radial-gradient(circle, rgba(37, 99, 235, 0.25) 0%, rgba(15, 23, 42, 0) 70%);
			border-radius: 50%;
			pointer-events: none;
		}

		body.ramom-login-body::after {
			content: '';
			position: absolute;
			bottom: -100px;
			right: -100px;
			width: 500px;
			height: 500px;
			background: radial-gradient(circle, rgba(2, 132, 199, 0.25) 0%, rgba(15, 23, 42, 0) 70%);
			border-radius: 50%;
			pointer-events: none;
		}

		.login-card-container {
			width: 100%;
			max-width: 1020px;
			background: #ffffff;
			border-radius: 24px;
			box-shadow: 0 25px 60px rgba(0, 0, 0, 0.35);
			overflow: hidden;
			display: flex;
			min-height: 600px;
			position: relative;
			z-index: 10;
		}

		/* Left Hero Branding Section */
		.login-brand-section {
			width: 45%;
			background: linear-gradient(145deg, #0f172a 0%, #1e293b 100%);
			padding: 48px;
			display: flex;
			flex-direction: column;
			justify-content: space-between;
			color: #ffffff;
			position: relative;
			overflow: hidden;
		}

		.login-brand-section::after {
			content: '';
			position: absolute;
			bottom: 0;
			right: 0;
			width: 300px;
			height: 300px;
			background: radial-gradient(circle, rgba(37, 99, 235, 0.2) 0%, rgba(0, 0, 0, 0) 70%);
			pointer-events: none;
		}

		.login-logo-box {
			display: flex;
			align-items: center;
			gap: 14px;
			margin-bottom: 30px;
		}

		.login-logo-box img {
			max-height: 80px;
			width: auto;
			filter: drop-shadow(0 4px 10px rgba(0,0,0,0.3));
		}

		.login-brand-title {
			font-family: 'Outfit', sans-serif;
			font-size: 32px;
			font-weight: 800;
			color: #ffffff;
			line-height: 1.2;
			margin-bottom: 12px;
			letter-spacing: -0.5px;
		}

		.login-brand-desc {
			font-size: 15px;
			color: #94a3b8;
			line-height: 1.6;
			margin-bottom: 30px;
		}

		.login-feature-list {
			display: flex;
			flex-direction: column;
			gap: 14px;
			margin-bottom: 30px;
		}

		.login-feature-item {
			display: flex;
			align-items: center;
			gap: 12px;
			font-size: 14px;
			color: #e2e8f0;
			font-weight: 500;
		}

		.login-feature-item i {
			color: #38bdf8;
			font-size: 16px;
		}

		.login-social-links {
			display: flex;
			gap: 10px;
		}

		.login-social-btn {
			width: 36px;
			height: 36px;
			background: rgba(255, 255, 255, 0.08);
			color: #ffffff !important;
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 14px;
			transition: all 0.3s ease;
			text-decoration: none;
		}

		.login-social-btn:hover {
			background: #2563eb;
			transform: translateY(-2deg);
		}

		/* Right Form Section */
		.login-form-section {
			width: 55%;
			padding: 48px;
			display: flex;
			flex-direction: column;
			justify-content: center;
			background: #ffffff;
		}

		.login-form-header {
			margin-bottom: 32px;
		}

		.login-form-header h2 {
			font-family: 'Outfit', sans-serif;
			font-size: 28px;
			font-weight: 800;
			color: #0f172a;
			margin: 0 0 6px 0;
			letter-spacing: -0.5px;
		}

		.login-form-header p {
			font-size: 14.5px;
			color: #64748b;
			margin: 0;
		}

		.input-group-custom {
			position: relative;
			margin-bottom: 22px;
		}

		.input-group-custom label {
			display: block;
			font-family: 'Outfit', sans-serif;
			font-size: 13.5px;
			font-weight: 700;
			color: #0f172a;
			margin-bottom: 8px;
		}

		.input-wrapper {
			position: relative;
			display: flex;
			align-items: center;
		}

		.input-icon-left {
			position: absolute;
			left: 16px;
			color: #94a3b8;
			font-size: 16px;
			pointer-events: none;
			transition: color 0.3s ease;
		}

		.input-control-custom {
			width: 100%;
			padding: 13px 16px 13px 46px;
			font-size: 14.5px;
			color: #0f172a;
			background-color: #f8fafc;
			border: 1.5px solid #e2e8f0;
			border-radius: 10px;
			outline: none;
			transition: all 0.25s ease;
			font-family: inherit;
		}

		.input-control-custom:focus {
			background-color: #ffffff;
			border-color: #2563eb;
			box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
		}

		.input-control-custom:focus + .input-icon-left,
		.input-wrapper:focus-within .input-icon-left {
			color: #2563eb;
		}

		.btn-login-submit {
			width: 100%;
			padding: 14px;
			font-family: 'Outfit', sans-serif;
			font-size: 16px;
			font-weight: 700;
			color: #ffffff;
			background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
			border: none;
			border-radius: 10px;
			cursor: pointer;
			box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
			transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
			display: flex;
			align-items: center;
			justify-content: center;
			gap: 10px;
			margin-bottom: 20px;
		}

		.btn-login-submit:hover {
			transform: translateY(-2deg);
			box-shadow: 0 8px 22px rgba(37, 99, 235, 0.45);
			background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
		}

		.back-to-login-link {
			display: inline-flex;
			align-items: center;
			gap: 8px;
			color: #2563eb;
			font-weight: 600;
			font-size: 14px;
			text-decoration: none;
			transition: all 0.2s ease;
		}

		.back-to-login-link:hover {
			color: #1d4ed8;
			text-decoration: underline;
		}

		.login-footer-text {
			margin-top: 30px;
			text-align: center;
			font-size: 13px;
			color: #94a3b8;
		}

		html, body.ramom-login-body {
			max-width: 100vw !important;
			overflow-x: hidden !important;
			box-sizing: border-box !important;
		}

		/* Responsive Design */
		@media (max-width: 880px) {
			body.ramom-login-body {
				padding: 12px 10px !important;
			}
			.login-card-container {
				flex-direction: column !important;
				width: 100% !important;
				max-width: 100% !important;
				border-radius: 16px !important;
				min-height: auto !important;
			}
			.login-brand-section, .login-form-section {
				width: 100% !important;
				padding: 24px 16px !important;
				box-sizing: border-box !important;
			}
			.login-brand-title {
				font-size: 22px !important;
			}
		}
	</style>
</head>
<body class="ramom-login-body">

	<div class="login-card-container">
		<!-- Left Side: Brand Hero & Platform Info -->
		<div class="login-brand-section">
			<div>
				<div class="login-logo-box">
					<?php if(!empty($branch_id)) { ?>
						<img src="<?=$this->application_model->getBranchImage($branch_id, 'logo')?>" alt="Logo">
					<?php } else { ?>
						<img src="<?php echo base_url('uploads/appIcons/icon-512x512.png'); ?>" alt="Logo" style="max-height:80px;">
					<?php } ?>
				</div>
				<h1 class="login-brand-title"><?php echo $global_config['institute_name']; ?></h1>
				<p class="login-brand-desc">Enterprise School Management Portal providing seamless access for Administrators, Teachers, Parents, and Students.</p>

				<div class="login-feature-list">
					<div class="login-feature-item">
						<i class="fas fa-shield-halved"></i>
						<span>Bank-Grade Data Encryption & Security</span>
					</div>
					<div class="login-feature-item">
						<i class="fas fa-key"></i>
						<span>Self-Service Password Restoration</span>
					</div>
					<div class="login-feature-item">
						<i class="fas fa-bolt"></i>
						<span>Instant Reset Instructions via Email</span>
					</div>
				</div>
			</div>

			<div>
				<?php if(!empty($global_config['address'])) { ?>
					<p style="font-size:13px; color:#94a3b8; margin-bottom:16px;">
						<i class="fas fa-location-dot me-1 text-primary"></i> <?php echo $global_config['address']; ?>
					</p>
				<?php } ?>
				<div class="login-social-links">
					<?php if (!empty($global_config['facebook_url'])) { ?>
						<a href="<?php echo $global_config['facebook_url']; ?>" target="_blank" class="login-social-btn"><i class="fab fa-facebook-f"></i></a>
					<?php } ?>
					<?php if (!empty($global_config['twitter_url'])) { ?>
						<a href="<?php echo $global_config['twitter_url']; ?>" target="_blank" class="login-social-btn"><i class="fab fa-twitter"></i></a>
					<?php } ?>
					<?php if (!empty($global_config['linkedin_url'])) { ?>
						<a href="<?php echo $global_config['linkedin_url']; ?>" target="_blank" class="login-social-btn"><i class="fab fa-linkedin-in"></i></a>
					<?php } ?>
					<?php if (!empty($global_config['youtube_url'])) { ?>
						<a href="<?php echo $global_config['youtube_url']; ?>" target="_blank" class="login-social-btn"><i class="fab fa-youtube"></i></a>
					<?php } ?>
				</div>
			</div>
		</div>

		<!-- Right Side: Reset Form -->
		<div class="login-form-section">
			<div class="login-form-header">
				<h2><?php echo translate('password_restoration'); ?></h2>
				<p>Enter your username or email address and we'll send you instructions to reset your password.</p>
			</div>

			<?php 
				if($this->session->flashdata('reset_res')){
					if($this->session->flashdata('reset_res') == 'true'){
						echo '<div class="alert alert-success mb-4" style="border-radius:10px; font-size:14px;"><i class="fas fa-check-circle me-1"></i> Password reset email sent successfully. Please check your inbox.</div>';
					}elseif($this->session->flashdata('reset_res') == 'false'){
						echo '<div class="alert alert-danger mb-4" style="border-radius:10px; font-size:14px;"><i class="fas fa-exclamation-triangle me-1"></i> You entered the wrong username or email address.</div>';
					}
				}
			?>

			<?php echo form_open($this->uri->uri_string()); ?>
				<!-- Username Input -->
				<div class="input-group-custom <?php if (form_error('username')) echo 'has-error'; ?>">
					<label for="inputUsername"><?php echo translate('username'); ?> / Email *</label>
					<div class="input-wrapper">
						<i class="far fa-user input-icon-left"></i>
						<input type="text" class="input-control-custom" id="inputUsername" name="username" value="<?=set_value('username')?>" placeholder="Enter your username or email" required autocomplete="off">
					</div>
					<?php if (form_error('username')) { ?>
						<span class="text-danger small mt-1 d-block"><?php echo form_error('username'); ?></span>
					<?php } ?>
				</div>

				<!-- Submit Button -->
				<button type="submit" id="btn_submit" class="btn-login-submit">
					<i class="far fa-paper-plane"></i> <?php echo translate('send_reset_link'); ?>
				</button>
			<?php echo form_close(); ?>

			<div class="text-center mt-3">
				<a href="<?php echo base_url("{$this->authentication_model->getSegment(1)}authentication"); ?>" class="back-to-login-link">
					<i class="fas fa-arrow-left"></i> <?php echo translate('back_to_login'); ?>
				</a>
			</div>

			<div class="login-footer-text">
				Developed by KKWEBMART
			</div>

		</div>
	</div>

</body>
</html>