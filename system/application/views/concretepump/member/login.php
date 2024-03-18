<!--Author: W3layouts
Author URL: http://w3layouts.com
License: Creative Commons Attribution 3.0 Unported
License URL: http://creativecommons.org/licenses/by/3.0/
-->
<!DOCTYPE HTML>
<html lang="zxx">

<head>
	<title>Congcrete Pump</title>
	<!-- Meta tag Keywords -->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="UTF-8" />
	<meta name="keywords" content="lacakmobil,transporter, concrete pump"
	/>
	<script>
		addEventListener("load", function () {
			setTimeout(hideURLbar, 0);
		}, false);

		function hideURLbar() {
			window.scrollTo(0, 1);
		}
		
		function frmlogin_onsubmit()
				{
					jQuery("#dvwait").show();
					jQuery.post("<?=base_url();?>member/dologin", jQuery("#formlogin").serialize(),
						function(r)
						{
							jQuery("#dvwait").hide();
							if (r.error)
							{
								alert(r.message);
								return;
							}

							location = r.redirect;
						}
						, "json"
					);
					return false;
				}
	</script>
	<!-- Meta tag Keywords -->
	<!-- css files -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/concretepump/css/style.css" type="text/css" media="all" />
	<!-- Style-CSS -->
	<link rel="stylesheet" href="css/fontawesome-all.css">
	<!-- Font-Awesome-Icons-CSS -->
	<!-- //css files -->
	<!-- web-fonts -->
	<link href="//fonts.googleapis.com/css?family=Reem+Kufi&amp;subset=arabic" rel="stylesheet">
	<link href="//fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i" rel="stylesheet">
	<!-- //web-fonts -->
</head>

<body>
	<!-- title -->
	<h1>
		<!--<span>M</span>usic
		<span>L</span>ogin
		<span>F</span>orm</h1>-->
		<h1>CONCRETE PUMP</h1>
	<!-- //title -->
	<!-- content -->
	<div class="sub-main-w3">
		<form id="formlogin" onSubmit="javascript: return frmlogin_onsubmit(this)" >
			<div class="form-style-agile">
				<label>
					Username
					<i class="fas fa-user"></i>
				</label>
				<input placeholder="Username" name="username" id="username" type="text" required="">
			</div>
			<div class="form-style-agile">
				<label>
					Password
					<i class="fas fa-unlock-alt"></i>
				</label>
				<input placeholder="Password" name="userpass" id="userpass" type="password" required="">
			</div>
			<!-- checkbox -->
			<!--<div class="wthree-text">
				<ul>
					<li>
						<label class="anim">
							<input type="checkbox" class="checkbox" required="">
							<span>Remember me</span>
						</label>
					</li>
					<li>
						<a href="#">Forgot Password?</a>
					</li>
				</ul>
			</div>-->
			<!-- //checkbox -->
			<input type="submit" value="Log In">
			<div id="dvwait" style="display:none;">
				<img src="<?=base_url();?>assets/images/anim_wait.gif" border="0" />
			</div>
			<!-- social icons -->
			<div class="footer-social">
				<!--<h2>Or</h2>-->
				<ul>
					<li>
						<a href="#">
							<i class="fab fa-facebook-f icon_facebook"></i>
						</a>
					</li>
					<li>
						<a href="#">
							<i class="fab fa-twitter icon_twitter"></i>
						</a>
					</li>
					<li>
						<a href="#">
							<i class="fab fa-dribbble icon_dribbble"></i>
						</a>
					</li>
					<li>
						<a href="#">
							<i class="fab fa-google-plus-g icon_g_plus"></i>
						</a>
					</li>
				</ul>
			</div>
			<!-- //social icons -->
		</form>
	</div><br><br>
	<!-- //content -->

	<!-- copyright -->
	<div class="footer">
		<p>&copy; 2020 Concrete Pump GPS Tracking System by
			<a href="http://www.lacak-mobil.com">www.lacak-mobil.com</a>
		</p>
	</div>
	<!-- //copyright -->

</body>

</html>