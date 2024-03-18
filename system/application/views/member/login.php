<!DOCTYPE html>
<html lang="en">
<head>
	<title>TEMAN INDOBARA - Terukur Aman</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->
	<link rel="icon" type="image/png" href="<?php echo base_url(); ?>assets/temanindobara/images/icons/favicon.ico"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/temanindobara/vendor/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/temanindobara/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/temanindobara/vendor/animate/animate.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/temanindobara/vendor/select2/select2.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/temanindobara/css/util.css">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/temanindobara/css/main.css">
<!--===============================================================================================-->

	<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">

	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/temanindobara/css/font-awesome.min.css">

	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/temanindobara/css/style.css">
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
<body>

	<!--  -->
	<div class="simpleslide100">
		<div class="simpleslide100-item bg-img1" style="background-image: url('assets/temanindobara/images/img-01.jpg');"></div>
		<div class="simpleslide100-item bg-img1" style="background-image: url('assets/temanindobara/images/img-02.jpg');"></div>
	</div>

	<div class="size1 overlay1" >
		<!--  -->
		<div class="w-full flex-w flex-sb-m p-l-90 justify-content-center" style="background-color:#fff" >
			<div class="wrappic1 m-r-10 m-l-0 m-t-10 m-b-5 img2"  >

				<a href="https://www.goldenenergymines.com/" target="_blank" ><img src="<?php echo base_url(); ?>assets/temanindobara/images/gems-logo.png" alt="PT. BORNEO INDOBARA"></a>
				<a href="https://www.goldenenergymines.com/" target="_blank"><img src="<?php echo base_url(); ?>assets/temanindobara/images/bib.png" alt="GOLDEN ENERGY MINES" ></a>

			</div>

			<div class="flex-w m-t-0 m-b-0 m-r-90 m-l-0" style="margin-top:-5px;">
				<!--<a href="https://www.goldenenergymines.com/" target="_blank"><img src="<?php echo base_url(); ?>assets/temanindobara/images/bib.png" alt="PT. BORNEO INDOBARA" style="width:85%;height:85%;"></a>-->
				<!--<a href="https://www.goldenenergymines.com/" target="_blank"><img src="<?php echo base_url(); ?>assets/temanindobara/images/gems-logo.png" alt="GOLDEN ENERGY MINES" style="width:85%;height:85%;"></a>-->
				<a href="https://www.goldenenergymines.com/" target="_blank"><img src="<?php echo base_url(); ?>assets/temanindobara/images/borneo_indobara.png" alt="Member of Sinarmas" ></a>
			</div>
		</div>
		<section class="ftco-section">
		<div class="container" style="margin-top:-70px;">
			<div class="row justify-content-center">
				<div class="col-md-6 text-center mb-5">
					<h2 class="heading-section"><img src="<?php echo base_url(); ?>assets/temanindobara/images/temanindobaralogo.png" alt="LOGO" style="border-radius:50%;"></h2>
				</div>
			</div>
			<div class="row justify-content-center">
				<div class="col-md-6 col-lg-4">
					<div class="login-wrap p-0">
		      	<!--<h3 class="mb-4 text-center" style="font-family:arial black;">PT. BORNEO INDOBARA</h3>-->
		      	<form class="signin-form" id="frmlogin" name="frmlogin" onsubmit="javascript : return frmlogin_onsubmit(this);">
		      		<div class="form-group">
		      			<input type="text" name="username" id="username" class="form-control" placeholder="Username" required>
						<span class="fa fa-fw fa-lock field-icon"></span>
		      		</div>
	            <div class="form-group">
	              <input id="password-field" name="userpass" id="userpass" type="password" class="form-control" placeholder="Password" required>
	              <span toggle="#password-field" class="fa fa-fw fa-eye-slash field-icon toggle-password"></span>
	            </div>
	            <div class="form-group">
	            	<button type="submit" class="form-control btn submit px-3" style="background-color:rgba(255, 12, 43, 0.8);;"><a style="color:white;" >Log In </a></button>
						<span id="dvwait" style="display:none;" class="row justify-content-center">
							<img src="<?=base_url();?>assets/images/anim_wait.gif" border="0" />
						</span>
	            </div>

	            <div class="form-group d-md-flex">
	            	<!--<div class="w-50">
		            	<label class="checkbox-wrap checkbox-primary">Remember Me
									  <input type="checkbox" checked>
									  <span class="checkmark"></span>
						</label>
					</div>
					<div class="w-50">
		            	<a href="#" style="color: #fff">Register</a>
					</div>
					<div class="w-50 text-md-right">
						<a href="#" style="color: #fff">Forgot Password</a>
					</div>
					-->

					<table>
					<tr align="center">
						<td style="width:200px;"><a href="mailto:digitech@samantha.id" style="color:#fff; ">Registers</a></td><td style="width:200px;" ><a href="https://api.whatsapp.com/send?phone=+6281119143655&text=Saya%20ingin%20merubah%20password." target="_blank" style="color:#fff;">Forgot Password</a></td>
					</tr>
				</table>
	            </div>
	          </form>
	          <!--<p class="w-100 text-center">&mdash; Or Sign In With &mdash;</p>-->
				<!--<p class="text-center" style="font-size:13px; ">&mdash; Or Sign In With &mdash;</p>
	          <div class="social">
	          	<a href="#" class="px-2 py-2 mr-md-1 rounded"><span class="ion-logo-facebook mr-2"></span> Facebook</a>
	          	<a href="#" class="px-2 py-2 ml-md-1 rounded"><span class="ion-logo-twitter mr-2"></span> Twitter</a>

	          </div>-->
	              <!-- <a href="#" id="password-field" class="form-control" align="center;" style="font-size:20px; background-color:grey;" ><i style="float: left;"><img src="<?php echo base_url(); ?>assets/temanindobara/images/search.png"></i><p style="margin-left:110px;">Google</p></a> -->
								<script src="https://accounts.google.com/gsi/client" async defer></script>
                        <script>
                          window.onload = function () {
                            google.accounts.id.initialize({
                              client_id: "<?=$this->config->item('GOOGLE_SIGNIN_CLIENT_ID');?>",
                              callback: handleCredentialResponse
                            });
                            google.accounts.id.renderButton(
                              document.getElementById("buttonDiv"),
                              { theme: "outline", size: "large", width:400, type: "standard", text:"sign_in_with",
                                shape: "rectangular", logo_alignment:"center", locale:"id"}
                            );
                            // google.accounts.id.prompt(); // also display the One Tap dialog
                          }
                      </script>
                      <div id="buttonDiv"></div>

								<!-- <div align="center">
								  <div id="g_id_onload"
						         data-client_id="<?=$this->config->item('GOOGLE_SIGNIN_CLIENT_ID');?>"
										 data-callback="handleCredentialResponse"
	         					 data-auto_prompt="false">
						      </div>
						      <div
									   class="g_id_signin"
						         data-type="standard"
						         data-size="large"
						         data-theme="outline"
						         data-text="sign_in_with"
						         data-shape="circle"
						         data-logo_alignment="left" >
						      </div>
								</div> -->




		      </div>
				</div>
			</div>
		</div>
	</section>

		<!--
		<div class="flex-col-c-m p-l-15 p-r-15 p-t-50 p-b-120">
			<h3 class="l1-txt1 txt-center p-b-40 respon1">
				Coming Soon
			</h3>

			<div class="flex-w flex-c-m cd100">
				<div class="flex-col-c wsize1 m-b-30">
					<span class="l1-txt2 p-b-9 days">35</span>
					<span class="s1-txt1 where1 p-l-35">Days</span>
				</div>

				<div class="flex-col-c wsize1 m-b-30">
					<span class="l1-txt2 p-b-9 hours">17</span>
					<span class="s1-txt1 where1 p-l-35">Hours</span>
				</div>

				<div class="flex-col-c wsize1 m-b-30">
					<span class="l1-txt2 p-b-9 minutes">50</span>
					<span class="s1-txt1 where1 p-l-35">Minutes</span>
				</div>

				<div class="flex-col-c wsize1 m-b-30">
					<span class="l1-txt2 p-b-9 seconds">39</span>
					<span class="s1-txt1 where1 p-l-35">Seconds</span>
				</div>
			</div>
		</div>

		 -->
		<!--<div class="flex-w flex-c-m p-b-35">
			<a href="#" class="size3 flex-c-m how-social trans-04 m-r-3 m-l-3 m-b-5">
				<i class="fa fa-facebook"></i>
			</a>

			<a href="#" class="size3 flex-c-m how-social trans-04 m-r-3 m-l-3 m-b-5">
				<i class="fa fa-twitter"></i>
			</a>

			<a href="#" class="size3 flex-c-m how-social trans-04 m-r-3 m-l-3 m-b-5">
				<i class="fa fa-youtube-play"></i>
			</a>

		</div>-->



	</div>





<!--===============================================================================================-->
	<script src="<?php echo base_url(); ?>assets/temanindobara/vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="<?php echo base_url(); ?>assets/temanindobara/vendor/bootstrap/js/popper.js"></script>
	<script src="<?php echo base_url(); ?>assets/temanindobara/vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="<?php echo base_url(); ?>assets/temanindobara/vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
	<script src="<?php echo base_url(); ?>assets/temanindobara/vendor/countdowntime/moment.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/temanindobara/vendor/countdowntime/moment-timezone.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/temanindobara/vendor/countdowntime/moment-timezone-with-data.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/temanindobara/vendor/countdowntime/countdowntime.js"></script>
	<script src="https://accounts.google.com/gsi/client" async defer></script>
	<script>

	function handleCredentialResponse_old(response) {
		 // console.log("response : ", response.credential);
		 // var base64Url       = response.credential.split('.')[1];
     // var base64          = base64Url.replace('-', '+').replace('_', '/');
		 // var responsePayload = JSON.parse(window.atob(base64));
		 // var email = responsePayload.email;
		 // console.log("ID: " + responsePayload.sub);
     // console.log('Full Name: ' + responsePayload.name);
     // console.log('Given Name: ' + responsePayload.given_name);
     // console.log('Family Name: ' + responsePayload.family_name);
     // console.log("Image URL: " + responsePayload.picture);
     // console.log("Email: " + responsePayload.email);

     const responsePayload = decodeJwtResponse(response.credential);
     console.log("responsePayload : " + responsePayload);

     var emaillogin          = responsePayload.email;

		 var data = {
			 email : emaillogin,
			 googlesignin : 1
		 };

		 // console.log("data for sent : ", data);

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

function decodeJwtResponse(token) {
    var base64Url = token.split('.')[1];
    var base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
    var jsonPayload = decodeURIComponent(window.atob(base64).split('').map(function(c) {
        return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
    }).join(''));

    return JSON.parse(jsonPayload);
  }

		$('.cd100').countdown100({
			/*Set Endtime here*/
			/*Endtime must be > current time*/
			endtimeYear: 0,
			endtimeMonth: 0,
			endtimeDate: 35,
			endtimeHours: 19,
			endtimeMinutes: 0,
			endtimeSeconds: 0,
			timeZone: ""
			// ex:  timeZone: "America/New_York"
			//go to " http://momentjs.com/timezone/ " to get timezone
		});
	</script>
<!--===============================================================================================-->
	<script src="<?php echo base_url(); ?>assets/temanindobara/vendor/tilt/tilt.jquery.min.js"></script>
	<script >
		$('.js-tilt').tilt({
			scale: 1.1
		})
	</script>
<!--===============================================================================================-->

<script>

    function frmlogin_onsubmit()
    {
        jQuery("#dvwait").show();
        jQuery.post("<?=base_url();?>member/dologin", jQuery("#frmlogin").serialize(),
        function(r)
        {
            jQuery("#dvwait").hide();
						console.log("r : ", r);
            if (r.error){
	            alert(r.message);
	            return;
            }
            location = r.redirect;
          }, "json");
    		return false;
      }
    </script>


  <script src="<?php echo base_url(); ?>assets/temanindobara/js/main.js"></script>
  <script src="<?php echo base_url(); ?>assets/temanindobara/js/main2.js"></script>
  <script src="<?php echo base_url(); ?>assets/temanindobara/js/jquery.min.js"></script>
  <script src="<?php echo base_url(); ?>assets/temanindobara/js/popper.js"></script>
  <script src="<?php echo base_url(); ?>assets/temanindobara/js/bootstrap.min.js"></script>


</body>
</html>
