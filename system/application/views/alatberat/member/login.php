<html>
<title>Heavy Equipment Management System</title>
<link href="<?php echo base_url();?>assets/alatberat/css/style.css" rel="stylesheet" type="text/css">
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

	<br>
	<div align="center">
		<!---begin of header--->
			<img src="<?php echo base_url();?>assets/alatberat/images/logo.png">
			<hr color="#f7ae03">
		<!---end of header--->
		
		<!---begin of menu--->	
			<font color="white" size="5px"></font>
			<div class="bgmenu"><font color="black" style="font-size:27px";>Heavy Equipment Management System</font></div>
		<!---end of menu--->
	
		<!---begin of content--->
					<form id="frmlogin" name="frmlogin" onsubmit="javascript : return frmlogin_onsubmit(this);">	
					<table border="0px solid gray" style="margin-left:700px; margin-top:50px;">
						<tr>
						<td><font color="#f7ae03">Username:</font></td>
						<td><input class="form" name="username" id="username" style="background-color:transparent; border-color:#f7ae03;"/></td>
						</tr>
						<tr>
						<td><font color="#f7ae03">Password:</font> </td>
						<td><input class="form" type="password" name="userpass" id="userpass" style="background-color:transparent; border-color:#f7ae03;"/></td>
						</tr>
						<tr>
						<td></td>
						<td><div style="margin-left:88px">
						<input  class="form" type="submit" name="Login" value="Enter" style="background-color:transparent; border-color:#f7ae03;"/>
						<span id="dvwait" style="display:none;">
						<img src="<?=base_url();?>assets/images/anim_wait.gif" border="0" />
						</span>
						</div></td>
						</tr>
					</table>
					</form>
			<div class="content">
				<div class="box"><img src="<?php echo base_url();?>assets/alatberat/images/slid.png"></div>
				<div class="box"><img src="<?php echo base_url();?>assets/alatberat/images/slid2.png"></div>
				<div class="box"><img src="<?php echo base_url();?>assets/alatberat/images/slid3.png"></div>
			</div>
		<!---end of content--->
	
		<!---begin of footer--->
			<div class="bg-foot">
				
			</div>
			<hr color="#f7ae03">
			<div class="text-foot">copyright <a href="www.lacak-mobil.com" target="_blank">wwww.lacak-mobil.com</a> - All Right Reserved 2012</div>
		<!---end of footer--->
	</div>
			<table style="margin-top:-75px; margin-left:530px; border:1px solid gray" >
				<tr>
					<td>
					<b>Online Support :</b></td>
					<td><a href="ymsgr:sendIM?nedi_lacakmobil"> <div style="margin-left:0px;"><img src="http://opi.yahoo.com/online?u=nedi_lacakmobil&amp;m=g&amp;t=1"/></div></a></td>
						<td><a href="ymsgr:sendIM?anto_lacakmobil"> <div style="margin-left:0px;"><img src="http://opi.yahoo.com/online?u=anto_lacakmobil&amp;m=g&amp;t=1"/></div></a></td>
						<td><a href="ymsgr:sendIM?hadi_lacakmobil"> <div style="margin-left:0px;"><img src="http://opi.yahoo.com/online?u=hadi_lacakmobi&amp;m=g&amp;t=1"/></div></a></td>
						<td><a href="ymsgr:sendIM?bayu_lacakmobil"> <div style="margin-left:0px;"><img src="http://opi.yahoo.com/online?u=bayu_lacakmobil&amp;m=g&amp;t=1"/></div></a></td>
						<td><a href="ymsgr:sendIM?robi_lacakmobil"> <div style="margin-left:0px;"><img src="http://opi.yahoo.com/online?u=robi_lacakmobil&amp;m=g&amp;t=1"/></div></a></td>
						<td><a href="ymsgr:sendIM?ayu_lacakmobil"> <div style="margin-left:0px;"><img src="http://opi.yahoo.com/online?u=ayu_lacakmobi&amp;m=g&amp;t=1"/></div></a></td>
				</tr>
			</table>
</body>
</html>