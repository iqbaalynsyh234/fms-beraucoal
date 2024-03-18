<html>
 <link href="<?php echo base_url();?>assets/berdikari/css/style.css" rel="stylesheet" type="text/css">
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
	<!---content--->
	<div id="content"><br><br><br><br><br><br><br><br><br>
		<div align="center">
		<table style="margin-left:630px; margin-top:-75px;">
						<tr>
							<td ><b><u><font color="#02395a">DIVISION MONITORING</font></u></b></td>
						</tr>
					</table>
					<table style="margin-left:740px; margin-top:80px; color:white;">
						<tr>
							<td ><b><font size="5px" color="#02395a">Member Login</font></b></td>
						</tr>
					</table>
				<form id="frmlogin" name="frmlogin" onsubmit="javascript : return frmlogin_onsubmit(this);">
					<table border="0px solid white" style="margin-left:630px; margin-top:0px;">
						<tr>
							<td class="form"><font color="#02395a">Username:</font></td>
							<td><input class="form2" type="name" name="username" id="username" style="background-color:#02395a; border-color:white; width:200px;"/></td>
						</tr>
						<tr>
							<td class="form"><font color="#02395a">Password:</font> </td>
							<td><input class="form2" type="password" name="userpass" id="userpass" style="background-color:#02395a; border-color:white; width:200px;"/></td>
						</tr>
						<tr>
							<td></td>
							<td><div style="margin-left:146px"><input  class="form" type="submit" name="Login" value="Enter" style="background-color:transparent; border-color:#02395a;"/></div>
								<span id="dvwait" style="display:none;">
									<img src="<?=base_url();?>assets/images/anim_wait.gif" border="0" />
								</span>
							</td>
						</tr>
					</table>
				</form>
					<table style="margin-left:630px; margin-top:115px; bgcolor:white;">
						<tr>
							<td class="text-foot"><div style="margin-left:20px">Monitoring Support : 021-82434946</div></td>
						</tr>
					</table>
					<table style="margin-left:630px; margin-top:0px; bgcolor:white;">
						<tr>
						<td><a href="ymsgr:sendIM?nedi_lacakmobil"> <div style="margin-left:0px;"><img src="http://opi.yahoo.com/online?u=nedi_lacakmobil&amp;m=g&amp;t=1"/></div></a></td>
						<td><a href="ymsgr:sendIM?anto_lacakmobil"> <div style="margin-left:0px;"><img src="http://opi.yahoo.com/online?u=anto_lacakmobil&amp;m=g&amp;t=1"/></div></a></td>
						<td><a href="ymsgr:sendIM?hadi_lacakmobil"> <div style="margin-left:0px;"><img src="http://opi.yahoo.com/online?u=hadi_lacakmobi&amp;m=g&amp;t=1"/></div></a></td>
					</tr>
					
					<tr>
						<td><a href="ymsgr:sendIM?bayu_lacakmobil"> <div style="margin-left:0px;"><img src="http://opi.yahoo.com/online?u=bayu_lacakmobil&amp;m=g&amp;t=1"/></div></a></td>
						<td><a href="ymsgr:sendIM?robi_lacakmobil"> <div style="margin-left:0px;"><img src="http://opi.yahoo.com/online?u=robi_lacakmobil&amp;m=g&amp;t=1"/></div></a></td>
						<td><a href="ymsgr:sendIM?ayu_lacakmobil"> <div style="margin-left:0px;"><img src="http://opi.yahoo.com/online?u=ayu_lacakmobil&amp;m=g&amp;t=1"/></div></a></td>
					</tr>
					</table>
					<table style="margin-left:630px; margin-top:50px; color:white;">
						<tr>
							<td><font color="#02395a"><b>www.lacak-mobil.com</b></font></td>
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
					<td class="menu" style="margin-left:-320px; margin-top:-42px;"><img src="<?php echo base_url();?>assets/berdikari/images/about.png"></td>
					<td class="menu2" style="margin-left:-220px; margin-top:-42px;"><img src="<?php echo base_url();?>assets/berdikari/images/feature.png"></td>
					<td class="menu3" style="margin-left:-35px; margin-top:-42px;"><img src="<?php echo base_url();?>assets/berdikari/images/service.png"></td>
				</tr>
			</table>
		</div>
	</div>
<!---end of footer--->
	
	
	
	
	


</body>
</html>