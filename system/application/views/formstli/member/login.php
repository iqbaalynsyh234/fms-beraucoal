<!doctype html>
<html lang="en">
  <head>
  	<title>PT. SEMESTA TRANSPORTASI LIMBAH INDONESIA</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/formstli/css/style.css">

	<style>
	body {
		background-image: url(assets/formstli/images/bg.jpg);
		}

	</style>
	<style>

		.field-icon {
  position: absolute;
  top: 50%;
  right: 15px;
  -webkit-transform: translateY(-50%);
  -ms-transform: translateY(-50%);
  transform: translateY(-50%);
  color: #010; }

.form-control {
  background: transparent;
  border: none;
  height: 50px;
  color: #010 !important;
  border: 1px solid transparent;
  background: rgba(255, 255, 255, 0.8);
  border-radius: 40px;
  padding-left: 20px;
  padding-right: 20px;
  -webkit-transition: 0.3s;
  -o-transition: 0.3s;
  transition: 0.3s; }
  @media (prefers-reduced-motion: reduce) {
    .form-control {
      -webkit-transition: none;
      -o-transition: none;
      transition: none; } }
  .form-control::-webkit-input-placeholder {
    /* Chrome/Opera/Safari */
    color: #010 !important; }
  .form-control::-moz-placeholder {
    /* Firefox 19+ */
    color: #010 !important; }
  .form-control:-ms-input-placeholder {
    /* IE 10+ */
    color: rgba(255, 255, 255, 0.8) !important; }
  .form-control:-moz-placeholder {
    /* Firefox 18- */
    color: rgba(255, 255, 255, 0.8) !important; }
  .form-control:hover, .form-control:focus {
    background: #fff;
    outline: none;
    -webkit-box-shadow: none;
    box-shadow: none;
    border-color: rgba(255, 255, 255, 0.4); }
  .form-control:focus {
    border-color: rgba(255, 255, 255, 0.4); }






	</style>

	</head>
	<body class="img js-fullheight">
	<section class="ftco-section" >
		<div class="container" style="border:0px solid black; margin-top:-100px;">
			<div class="row justify-content-center" style="margin-top:70px;">
				<div class="col-md-6 text-center mb-5">
					<h2 class="heading-section" ><img src="<?php echo base_url(); ?>assets/formstli/images/logogstli.png" style="width:180px;" height="auto"></h2>
					<!--<h2 class="heading-section text-center">PT. SEMESTA TRANSPORTASI LIMBAH INDONESIA</h2>-->
				</div>
			</div>
			<div class="row justify-content-center">
				<div class="col-md-6 col-lg-4">
					<div class="login-wrap p-0">

		      	<!--<h3 class="mb-4 text-center">PT. Rezki Batulicin Transportation</h3>-->
		      	<form class="signin-form" id="frmlogin" name="frmlogin" onsubmit="javascript : return frmlogin_onsubmit(this);">
		      		<div class="form-group">
		      			<input type="text" class="form-control" name="username" id="username" placeholder="Username" required>
						<span class="fa fa-fw fa-lock field-icon"></span>
		      		</div>
					<div class="form-group">
					  <input id="password-field" name="userpass" id="userpass" type="password" class="form-control" placeholder="Password" required>
					  <span toggle="#password-field" class="fa fa-fw fa-eye-slash field-icon toggle-password"></span>
					</div>
					<div class="form-group">
						<button type="submit" class="form-control btn-primary submit px-3" style="background-color:#0000FE; opacity:0.7"><font color="white">LOG IN </font></button>
						<span id="dvwait" style="display:none;" class="row justify-content-center">
							<img src="<?=base_url();?>assets/images/anim_wait.gif" border="0" />
						</span>
					</div>
					<div class="form-group d-md-flex">
						<!--<div class="w-50">
							<label class="checkbox-wrap checkbox-primary" style="color: #fff">Remember Me
										  <input type="checkbox" checked style="color: #fff">
										  <span class="checkmark" style="color: #fff"></span>
										</label>
									</div>
									<div class="w-50 text-md-right">
										<a href="#" style="color: #fff">Forgot Password</a>
									</div>-->
                  <table>
										<tr align="center">
											<td style="width:200px;"><a href="mailto:ketut.mii@sinarmasmining.com" style="color:#fff; ">Register</a></td>
											<!--<td style="width:200px;" ><a href="https://api.whatsapp.com/send?phone=+6281119143655&text=Saya%20ingin%20merubah%20password." target="_blank" style="color:#fff;">Forgot Password</a></td>-->
											<td style="width:200px;"><a href="<?=base_url();?>turing" target="_blank" style="color:#fff;">Help Center</a></td>
										</tr>
									</table>
					</div>
	          </form>

	          <!--<p class="w-100 text-center">&mdash; Or Sign In With &mdash;</p>
	          <div class="social d-flex text-center">
	          	<a href="#" class="px-2 py-2 mr-md-1 rounded"><span class="ion-logo-facebook mr-2"></span> Facebook</a>
	          	<a href="#" class="px-2 py-2 ml-md-1 rounded"><span class="ion-logo-twitter mr-2"></span> Twitter</a>
	          </div>-->
			  <div align="center">
								  <div id="g_id_onload"
						         data-client_id="244399799775-0dv5mtq3baoao644j1acgi9h49nosjv6.apps.googleusercontent.com"
										 data-callback="handleCredentialResponse"
	         					 data-auto_prompt="false">
						      </div>
						      <div
									   class="g_id_signin"
						         data-type="dark"
						         data-size="large"
						         data-theme="outline"
						         data-text="sign_in_with"
						         data-shape="circle"
						         data-logo_alignment="left"
								 data-color="#77c385"
								 >
						      </div>
							</div>
		      </div>
				</div>
			</div>
		</div>
	</section>

	<script>
	function handleCredentialResponse(response) {
		 // console.log("response : ", response.credential);
		 var base64Url       = response.credential.split('.')[1];
		 var base64          = base64Url.replace('-', '+').replace('_', '/');
			 var responsePayload = JSON.parse(window.atob(base64));
			 var email = responsePayload.email;
			 // console.log("ID: " + responsePayload.sub);
		 // console.log('Full Name: ' + responsePayload.name);
		 // console.log('Given Name: ' + responsePayload.given_name);
		 // console.log('Family Name: ' + responsePayload.family_name);
		 // console.log("Image URL: " + responsePayload.picture);
		 // console.log("Email: " + responsePayload.email);

			 var data = {
				 email : email,
				 googlesignin : 1
			 };

			 console.log("data for sent : ", data);

			 jQuery.post("<?php echo base_url() ?>member/googlesignin", data, function(r){
				 jQuery("#dvwait").hide();
				 console.log("r : ", r);
				 if (r.error)
				 {
						 alert(r.message);
						 return;
						 }
						 location = r.redirect;
					 }, "json");
				 return false;
	}

    function frmlogin_onsubmit()
    {
        jQuery("#dvwait").show();
        jQuery.post("<?=base_url();?>member/dologin", jQuery("#frmlogin").serialize(),
        function(r)
        {
            jQuery("#dvwait").hide();
						//console.log("r : ", r);
            if (r.error){
	            alert(r.message);
	            return;
            }
            location = r.redirect;
          }, "json");
    		return false;
      }
    </script>

  <script src="<?php echo base_url(); ?>assets/formstli/js/jquery.min.js"></script>
  <script src="<?php echo base_url(); ?>assets/formstli/js/popper.js"></script>
  <script src="<?php echo base_url(); ?>assets/formstli/js/bootstrap.min.js"></script>
  <script src="<?php echo base_url(); ?>assets/formstli/js/main.js"></script>
  <script src="https://accounts.google.com/gsi/client" async defer></script>

	</body>
</html>
