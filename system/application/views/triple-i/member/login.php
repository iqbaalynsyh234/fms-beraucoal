<!DOCTYPE html>
<head>
	<meta charset="utf-8" />
	<title><?php echo "TRIPLE-I FMS CONTROL SYSTEM";?></title>
    <script>

    jQuery(document).ready(
					
				)
                
    function frmlogin_onsubmit()
    {
        jQuery("#dvloader").show();
        jQuery.post("<?=base_url();?>member/dologin", jQuery("#frmlogin").serialize(),
        function(r)
        {
            jQuery("#dvloader").hide();
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
</head>

<body>
	<div id="wrapper">
		<!--[if !IE]>start login wrapper<![endif]-->
		<div id="login_wrapper">
			<div class="error">
				<div class="error_inner">
					<strong>Access Denied</strong> | <span>Please Insert user/password</span>
				</div>
			</div>
			<!--[if !IE]>start login<![endif]-->
			<form id="frmlogin" name="frmlogin" onsubmit="javascript : return frmlogin_onsubmit(this);">
				<fieldset>
					<h1 id="logo"><a href="#">websitename Administration Panel</a></h1>
					<div class="formular">
						<div class="formular_inner">
						<label>
							<strong>Username:</strong>
							<span class="input_wrapper">
								<input name="username" id="username" type="text" />
							</span>
						</label>
						<label>
							<strong>Password:</strong>
							<span class="input_wrapper">
								<input type="password" name="userpass" id="userpass" />
							</span>
						</label>
						<label class="inline">
							<input class="checkbox" name="" type="checkbox" value="" />
							Remember me on this computer
						</label>
						<ul class="form_menu">
							<li><span class="button"><span><span>Login</span></span><input type="submit" name=""/></span></li>
							
						</ul>
						</div>
					</div>
                    <div id="dvloader" style="display: none;">
                        <img src="<?php echo base_url();?>assets/triple-i/images/loader.gif" alt="loader" id="imgloader" />
                    </div>
                    <br />
                    <center>
                    <div>
                        <label>
                            <font color="white">All Right Reserved &copy CV. Triple-I - 2012
                            <br />
                            Jl. Batu Ampar 2, Batu Pandan No. 3A-E, Condet - Kramat Jati  <br/>Jakarta Timur</font>
                        </label>
                    </div>
                    </center>
				</fieldset>
			</form>
		</div>
	</div>
</body>
</html>
