<script type="text/javascript" src="<?=base_url();?>system/application/views/lacakmobil_tmpl1/lacak_tmpl1_contact/jquery-1.2.6.min.js"></script>
<script>
			<!--
			
				jQuery(document).ready(
					function()
					{																														
       					var zIndexNumber = 1000;
       					// Put your target element(s) in the selector below!
       					jQuery("div").each(function() {
               				jQuery(this).css('zIndex', zIndexNumber);
               				zIndexNumber += 10;
       					})
       					
       					jQuery("#container").css("margin-top", -130);
					}
				)

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
			-->
			</script>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo base_url(); ?>assets/oto-track/css/login.css" />
<style>
div#container
{
   top:20px;
   position: absolute;
   left: 50%;
   width: 994px;
   margin-left: -497px;
}

body {

background:url(<?=base_url();?>assets/oto-track/images/globe_east_2048.jpg) no-repeat center -50px #333;

}
</style>
<body>
	<div id="container">
		<div style="position:absolute;left:285px;top:185px;">
			<img src="<?=base_url();?>assets/oto-track/images/logo.png" />
		</div>
		
		<div style="position:absolute;left:225px;top:500px;">
			<img src="<?=base_url();?>assets/oto-track/images/vehicles.png" />
		</div>
		
		<div id="dvwait" style="position:absolute;left:700px;top:300px;display: none;">
			<img src="<?=base_url();?>assets/images/anim_wait.gif" border="0" />
		</div>
		
		<div style="position:absolute;left:350px;top:620px;">copyright 
			<img src="<?=base_url();?>assets/oto-track/images/copyright.png" width="15px" height="15px"/>
			www.oto-track.com 2011
		</div>
	</div>

	<div id="login_box" align="center" style="margin:auto;margin-top:140px;padding:5px;height:200px;width:480px;background-color:black;text-align:center;filter:alpha(opacity=80);
		-moz-opacity:0.8;
		-khtml-opacity: 0.8;
		opacity: 0.8;
	}">
	
	<p align="center"><a href="" /></a></p>
		<table width="100%" border="0" align="center" cellpadding="10" cellspacing="10">
			<tr>
				<td align="center" valign="top" class="style83">
					<form id="frmlogin" onSubmit="javascript: return frmlogin_onsubmit(this)">
						<table width="100%" border="0" cellspacing="2" cellpadding="2">
							<tr>
								<td class="label" style="color:white">
									Username:
								</td>
								<td align="left" valign="middle">
									<input type="text" name="username" id="username" value="" class="maintextfont" tabindex="1"/>
								</td>
							</tr>
              
							<tr>
								<td class="label" style="color:white">
									Password: 
								</td>
								<td align="left" valign="middle">
									<input type="password" name="userpass" id="userpass" value="" class="maintextfont" tabindex="2"/>
									<br />
								</td>
							</tr>
              
							<tr>
								<td colspan="2" height="20px" align="center">
									<center>
										<input name="" type="submit" value="Login" />
									</center>
								</td>
							</tr>
						</table>
					
						<br />
			
						<?php if ($this->config->item("showmessagelogin")) { ?>
							<div id="dvwait" style="position:absolute;left:228px;top:5px; width: 520px; text-align: center;">
								<font face="Tahoma" color="#ff0000">Mohon maaf dikarenakan ada maintenance server, untuk sementara <br />
									<b>
										http://www.lacak-mobil.com dan sub domainnya
									</b>
									<br /> 
										dialihkan ke 
										<br />
											<b>
												http://119.235.20.251
											</b>
								</font>		
							</div>
						<?php } ?>
					
					</form>
				</td>
			</tr>
        </table>
        <br />
	</div>
</body>