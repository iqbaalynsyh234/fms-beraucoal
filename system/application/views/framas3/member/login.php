<html>
 <link href="<?php echo base_url();?>assets/framas/css/style.css" rel="stylesheet" type="text/css">
 <script>

    jQuery(document).ready(
					
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
    </script>
<body>

	<div align="center"><div style="margin-top:30px"><img src="<?php echo base_url();?>assets/framas/images/logo.png"></div>
		<div id="container"></div>
			
	</div>	
			
	
	
	<div align="center">
		<div id="content"><br>
			<div class="textc" style="margin-top:-55px"><u>GPS Tracking And Fleet Management System</u></div><br>
			<img src="<?php echo base_url();?>assets/framas/images/isi.png" style="margin-left:75px; margin-top:30px;"/>
			<form id="frmlogin" name="frmlogin" onsubmit="javascript : return frmlogin_onsubmit(this);">
			<table border="0px solid white" style="margin-left:30px; margin-top:-270px;">
						<tr>
						<td class="form"><font color="white">Username:</font></td>
						<td><input class="form" type="name" name="username" id="username" style="background-color:transparent; border-color:white;"/></td>
						</tr>
						<tr>
						<td class="form"><font color="white">Password:</font> </td>
						<td><input class="form" type="password" name="userpass" id="userpass" style="background-color:transparent; border-color:white;"/></td>
						</tr>
						<tr>
						<td></td>
						<td><div style="margin-left:95px"><input  class="form" type="submit" name="Login" value="Enter" style="background-color:transparent; border-color:white;"/></div>
						<span id="dvwait" style="display:none;">
							<img src="<?=base_url();?>assets/images/anim_wait.gif" border="0" />
						</span>
						</td>
						</tr>
			</table>
			</form>
			<table style="margin-top:195px; bgcolor:white;">
				<tr>
					<td class="text-foot">Monitoring Support :</td>
					<td class="text-foot">021 8242 6589 (Hunting), </td>
					<td><a href="ymsgr:sendIM?eddigunasadhega"> <div style="margin-left:3px;"><img src="http://opi.yahoo.com/online?u=lacak.mobil&amp;m=g&amp;t=1"/></div></a></td>
					<td><a href="ymsgr:sendIM?putra_so7@rocketmail.com"> <div style="margin-left:3px;"><img src="http://opi.yahoo.com/online?u=lacak.mobil&amp;m=g&amp;t=1"/></div></a></td>
					<td><a href="ymsgr:sendIM?lacak.mobil"> <div style="margin-left:3px;"><img src="http://opi.yahoo.com/online?u=lacak.mobil&amp;m=g&amp;t=1"/></div></a></td>
				</tr>
			</table><br><br>
			<table>
				<tr><td class="box"><img src="<?php echo base_url();?>assets/framas/images/hover.png"/></td>
				<td class="fea"><img src="<?php echo base_url();?>assets/framas/images/feature.png"/></td>
				<td class="fea"><img src="<?php echo base_url();?>assets/framas/images/service.png"/></td></tr>
			</table>
			<div class="text-bottom">&copy;www.lacak-mobil.com</div><br>
			
		
			</div>
		</div>
	</div>
	
	
		
	




</body>
</html>