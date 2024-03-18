<html>
	<link href="<?php echo base_url();?>assets/intanutama/css/style.css" rel="stylesheet" type="text/css">
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
<!---header--->
	<div id="head">
		<div align="center">
			<div class="logo"><div style="margin-top:20px"><img src="<?php echo base_url();?>assets/intanutama/images/logo.png"></div></div>
		</div>
	</div>
<!---end of header--->

<!---content--->
	<div align="center">
		<div id="content">
		<img src="<?php echo base_url();?>assets/intanutama/images/awal.png"/>
			<div style="margin-left:-50px"><img src="<?php echo base_url();?>assets/intanutama/images/content.png"/>
				<form id="frmlogin" name="frmlogin" onsubmit="javascript : return frmlogin_onsubmit(this);">
				<table border="0px solid white" style="margin-left:500px; margin-top:-335px;">
						<tr>
						<td><font color="white">Username:</font></td>
						<td><input class="form" type="name" name="username" id="username" style="background-color:transparent; border-color:white;"/></td>
						</tr>
						<tr>
						<td><font color="white">Password:</font> </td>
						<td><input class="form" type="password" name="userpass" id="userpass" style="background-color:transparent; border-color:white;"/>
						<span id="dvwait" style="display:none;">
							<img src="<?=base_url();?>assets/images/anim_wait.gif" border="0" />
						</span>
						</td>
						</tr>
						<tr>
						<td></td>
						<td><div style="margin-left:93px"><input  class="form" type="submit" name="Login" value="Enter" style="background-color:transparent; border-color:white;"/></div></td>
						</tr>
				</table>
				</form>
			</div>
		</div>
	</div>
<!---end of content--->

<!---footer--->
	<div align="center">
		<div id="foot">
			<img src="<?php echo base_url();?>assets/intanutama/images/foot.png"/>
			<table style="margin-top:-42px">
				<tr>
					<td class="text-foot">Monitoring Support :</td>
					<td class="text-foot">Hunting - 021 8242 6589</td>
					<td><a href="ymsgr:sendIM?eddigunasadhega"> <div style="margin-left:3px;"><img src="http://opi.yahoo.com/online?u=lacak.mobil&amp;m=g&amp;t=1"/></div></a></td>
					<td><a href="ymsgr:sendIM?putra_so7@rocketmail.com"> <div style="margin-left:3px;"><img src="http://opi.yahoo.com/online?u=lacak.mobil&amp;m=g&amp;t=1"/></div></a></td>
					<td><a href="ymsgr:sendIM?lacak.mobil"> <div style="margin-left:3px;"><img src="http://opi.yahoo.com/online?u=lacak.mobil&amp;m=g&amp;t=1"/></div></a></td>
				</tr>
			</table>
			<div style="margin-top:20px"><div class="about"><img src="<?php echo base_url();?>assets/intanutama/images/about.png"></div></div>
			<div style="margin-top:-10px"><div class="abot"><img src="<?php echo base_url();?>assets/intanutama/images/feature.png"></div></div>
			<div style="margin-top:-10px"><div class="abot"><img src="<?php echo base_url();?>assets/intanutama/images/service.png"></div></div>
		</div>
			<div class="text-end"><a href="www.lacak-mobil.com">&copy; www.lacak-mobil.com/2012</a></div>
	</div>
</body>
</html>