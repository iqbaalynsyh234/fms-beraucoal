<html>
<link href="<?php echo base_url();?>assets/tamari/css/style.css" rel="stylesheet" type="text/css">
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
	<div id="wrapper">
		<div align="center">
			<div class="head1"><img src="<?php echo base_url();?>assets/tamari/image/logo.png"></div>
			<div style="margin-top:-10px"><img src="<?php echo base_url();?>assets/tamari/image/garis.png"/></div>
		</div>
	</div>

<!---end of header--->
<!---content--->	
	<div id="content">
		<div align="center">
			<img src="<?php echo base_url();?>assets/tamari/image/bgcontent.png"/>
				<form id="frmlogin" name="frmlogin" onsubmit="javascript : return frmlogin_onsubmit(this);">
					<table border="0px solid white" style="margin-left:0px; margin-top:-185px;">
						<tr>
						<td class="form"><font color="white">Username:</font></td>
						<td><input class="form" type="name" name="username" id="username" style="background-color:transparent; border-color:white; width:200px;"/></td>
						</tr>
						<tr>
						<td class="form"><font color="white">Password:</font> </td>
						<td><input class="form" type="password" name="userpass" id="userpass" style="background-color:transparent; border-color:white; width:200px;"/></td>
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
			<table style="margin-top:73px; bgcolor:white;">
				<tr>
					<td class="text-foot">Monitoring Support :</td>
					<td class="text-foot">021 8242 6589 (Hunting), </td>
					<td><a href="ymsgr:sendIM?nedi_lacakmobil"> <div style="margin-left:3px;"><img src="http://opi.yahoo.com/online?u=nedi_lacakmobil&amp;m=g&amp;t=1"/></div></a></td>
							<td><a href="ymsgr:sendIM?anto_lacakmobil"> <div style="margin-left:3px;"><img src="http://opi.yahoo.com/online?u=anto_lacakmobil&amp;m=g&amp;t=1"/></div></a></td>
							<td><a href="ymsgr:sendIM?hadi_lacakmobil"> <div style="margin-left:3px;"><img src="http://opi.yahoo.com/online?u=hadi_lacakmobil&amp;m=g&amp;t=1"/></div></a></td>
							<td><a href="ymsgr:sendIM?bayu_lacakmobil"> <div style="margin-left:3px;"><img src="http://opi.yahoo.com/online?u=bayu_lacakmobil&amp;m=g&amp;t=1"/></div></a></td>
							<td><a href="ymsgr:sendIM?robi_lacakmobil"> <div style="margin-left:3px;"><img src="http://opi.yahoo.com/online?u=robi_lacakmobil&amp;m=g&amp;t=1"/></div></a></td>
							<td><a href="ymsgr:sendIM?ayu_lacakmobil"> <div style="margin-left:3px;"><img src="http://opi.yahoo.com/online?u=ayu_lacakmobil&amp;m=g&amp;t=1"/></div></a></td>
				</tr>
			</table>
		</div>
	</div>
<!---end of content--->

<!---footer--->
	<div align="center">
		<div id="footer">
			<table style="margin-top:0px">
				<tr>
					<td class="menu" style="margin-left:0px"><img src="<?php echo base_url();?>assets/tamari/image/about.png"></td>
					<td class="menu2" style="margin-left:0px"><img src="<?php echo base_url();?>assets/tamari/image/feature.png"></td>
					<td class="menu3" style="margin-left:0px"><img src="<?php echo base_url();?>assets/tamari/image/service.png"></td>
				</tr>
			</table>
		</div>
	</div>
	<br>
	<div align="center">
		<a href="www.lacak-mobil.com"><img src="<?php echo base_url();?>assets/tamari/image/bawah.png"/></a>
	</div>
<!---end of footer--->


</body>
</html>