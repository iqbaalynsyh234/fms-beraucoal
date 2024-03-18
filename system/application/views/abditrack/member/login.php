<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hauling Eyes - FMS Berau Coal</title>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/abditrack/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/abditrack/css/fontawesome-all.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/abditrack/css/iofrm-style.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/abditrack/css/iofrm-theme11.css">
	<link rel="shortcut icon" href="<?=base_url();?>assets/abditrack/images/logo_icon_abditek.ico" />
	<link rel="icon" href="<?=base_url();?>assets/abditrack/images/logo_icon_abditek.ico" />

  <style media="screen">
    .field-icon {
      position: absolute;
      top: 50%;
      margin-left: 9%;
      -webkit-transform: translateY(-50%);
      -ms-transform: translateY(-50%);
      transform: translateY(-50%);
      color: #010;
      cursor: pointer;
    }
  </style>

	<script type="text/javascript" src="simple-slide-panel_data/jquery.js"></script>
	<script type="text/javascript">


	 function frmlogin_onsubmit()
		{
			jQuery("#dvwait").show();
			jQuery.post("<?=base_url();?>member/dologin", jQuery("#frmlogin").serialize(),
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
	</script>


</head>
<body>
    <div class="form-body" class="container-fluid">
        <div class="row">
            <div class="img-holder">
                <div class="bg"></div>
                <div class="info-holder">
                    <h3>FLEET MANAGEMENT SYSTEM</h3>
                    <p>Intelligent Monitoring Transportation System</p>
                    <!--<img src="<?php echo base_url();?>assets/abditrack/images/graphic5.svg" alt="">-->
					<img src="<?php echo base_url();?>assets/abditrack/images/capture01.png" alt="">

                </div>
            </div>
            <div class="form-holder">
                <div class="form-content">
                    <div class="form-items">
                        <!--<div class="website-logo-inside">
                            <a href="<?php echo base_url();?>">
                                <div class="logo">
                                    <img class="logo-size" src="<?php echo base_url();?>assets/abditrack/images/logo-abditek.png" alt="">
                                </div>
                            </a>
                        </div> -->

						 <div align="center">
							<img src="<?php echo base_url();?>assets/abditrack/images/logo-main.png" width="90%" height="90%" alt="">
						 </div>
						<br />
                        <!--<div class="page-links">
                            <a href="login9.html" class="active">Login</a>
                        </div>-->
						<div align="center">
                        <form id="frmlogin" name="frmlogin" onsubmit="javascript : return frmlogin_onsubmit(this);">
                          <div class="form-group">
                            <input class="form-control" type="text" name="username" id="username" placeholder="Username" required>
            	            </div>

                          <div class="form-group">
            	              <input name="userpass" id="userpass" type="password" class="form-control" placeholder="Password" required>
            	              <span id="showPass" class="fa fa-fw fa-eye-slash field-icon toggle-password"></span>
            	            </div>

                            <!-- <input id="password-field" class="form-control" type="password" name="userpass" id="userpass" placeholder="Password" required> -->

                            <div class="form-button">
                                <button id="submit" type="submit" class="ibtn">Login</button>
                            </div>
							<div class="form-button">
								<div id="g_id_onload"
									data-client_id="<?=$this->config->item('GOOGLE_SIGNIN_CLIENT_ID');?>"
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
							 <div align="center">
								<span id="dvwait" style="display:none;">
									<img src="<?=base_url();?>assets/images/anim_wait.gif" border="0" />
								</span>
							</div>
                        </form>
						</div>



                    </div>
                </div>
            </div>
        </div>
    </div>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/abditrack/js/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/abditrack/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/abditrack/js/popper.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/abditrack/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/abditrack/js/main.js"></script>
<script src="https://accounts.google.com/gsi/client" async defer></script>
<script type="text/javascript">
$(document).ready(function(){
  $('#showPass').on('click', function(){
      var passInput=$("#userpass");
      if(passInput.attr('type')==='password')
        {
          passInput.attr('type','text');
      }else{
         passInput.attr('type','password');
      }
    });
});


</script>
</body>
</html>
