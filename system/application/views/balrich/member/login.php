<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/balrich/css/demo.css" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/balrich/css/menu.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo base_url();?>assets/balrich/java/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/balrich/java/script.js"></script>


 <link href="<?php echo base_url();?>assets/balrich/css/style.css" rel="stylesheet" type="text/css">
 
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
	<div align="center">
		<div class="boxhead">
			<div class="boxhead2">
				<br>
				<br>
				<img src="<?php echo base_url();?>assets/balrich/images/logo.png"/>
			</div>
		</div>
		<div class="menubar">
		</div>
	<!---end header--->
	<!---content--->
		<div class="count">
			<div class="boxcount">
			<marquee align="left" direction="left" height="200" scrollamount="3" width="100%">
				<span style="font-weight:bold; width:100px;">
					<span style="font-weight:bold;">
						<img src="<?php echo base_url();?>assets/balrich/img/sample_slides/Balrich1.jpg" width="605" height="360" alt="side" />
						<img src="<?php echo base_url();?>assets/balrich/img/sample_slides/Balrich2.jpg" width="605" height="360" alt="side" />
						<img src="<?php echo base_url();?>assets/balrich/img/sample_slides/Balrich3.jpg" width="605" height="360" alt="side" />
						<img src="<?php echo base_url();?>assets/balrich/img/sample_slides/Balrich4.jpg" width="605" height="360" alt="side" />
						<img src="<?php echo base_url();?>assets/balrich/img/sample_slides/Balrich5.jpg" width="605" height="360" alt="side" />
						<img src="<?php echo base_url();?>assets/balrich/img/sample_slides/Balrich6.jpg" width="605" height="360" alt="side" />
						
						
					</span>
				</span> 
			</marquee>
			</div>
			<div class="boxcount3">
				<div class="boxmonit1">
				<form id="frmlogin" name="frmlogin" onsubmit="javascript : return frmlogin_onsubmit(this);">
					<br>
					<font size="5px" color="white" style="margin-left:100px">Member Login</font>
					<hr>
					<table border="0px solid white" style="margin-left:2px; margin-top:0px;">
						<tr>
						<td><font color="white">Username:</font></td>
						<td><input class="form" type="name" name="username" id="username" style="background-color:white; border-color:white; width:170px; margin-left:12px;"/></td>
						</tr>
						<tr>
						<td><font color="white">Password:</font></td>
						<td><input class="form" type="password" name="userpass" id="userpass" style="background-color:white; border-color:white; width:170px; margin-left:12px;"/></td>
						</tr>
						<tr>
						<td></td>
						<td><div style="margin-left:132px"><input  class="form" type="submit" name="Login" value="Login" style="background-color:white; border-color:white; width:50px;"/></div>
						</td>
						<td><span id="dvwait" style="display:none;">
							<img src="<?=base_url();?>assets/images/anim_wait.gif" border="0" />
						</span></td>
						</tr>
						
					</table>
				</form>
				</div>
				<div class="boxmonit2">
					<br>
					<div style="margin-top:-18px">
					<font size="4px" color="white" style="margin-left:100px;">Monitoring Support</font>
					</div>
					<hr>
					<font size="3px" color="white" style="margin-left:0px;"></font>
					<table style="margin-top:2px">
					<tr>
						<td><a href="ymsgr:sendIM?nedi_lacakmobil"> <div style="margin-left:0px;"><img src="http://opi.yahoo.com/online?u=nedi_lacakmobil&amp;m=g&amp;t=1"/></div></a></td>
						<td><a href="ymsgr:sendIM?anto_lacakmobil"> <div style="margin-left:0px;"><img src="http://opi.yahoo.com/online?u=anto_lacakmobil&amp;m=g&amp;t=1"/></div></a></td>
						<td><a href="ymsgr:sendIM?hadi_lacakmobil"> <div style="margin-left:0px;"><img src="http://opi.yahoo.com/online?u=hadi_lacakmobi&amp;m=g&amp;t=1"/></div></a></td>
					</tr>
					</table>
					<table style="margin-top:2px">
					<tr>
						<td><a href="ymsgr:sendIM?bayu_lacakmobil"> <div style="margin-left:0px;"><img src="http://opi.yahoo.com/online?u=bayu_lacakmobil&amp;m=g&amp;t=1"/></div></a></td>
						<td><a href="ymsgr:sendIM?robi_lacakmobil"> <div style="margin-left:0px;"><img src="http://opi.yahoo.com/online?u=robi_lacakmobil&amp;m=g&amp;t=1"/></div></a></td>
						<td><a href="ymsgr:sendIM?ayu_lacakmobil"> <div style="margin-left:0px;"><img src="http://opi.yahoo.com/online?u=ayu_lacakmobil&amp;m=g&amp;t=1"/></div></a></td>
					</tr>
					</table>
				</div>
			</div>
		</div>
			
		<!---<div style="margin-left:0px;"> <div id="gallery">
			<div id="slides">
				<div class="slide"><img src="<?php echo base_url();?>assets/balrich/img/sample_slides/balrich1.jpg" width="605" height="360" alt="side" /></div>
				<div class="slide"><img src="<?php echo base_url();?>assets/balrich/img/sample_slides/balrich2.jpg" width="605" height="360" alt="side" /></div>
				<div class="slide"><img src="<?php echo base_url();?>assets/balrich/img/sample_slides/balrich3.jpg" width="605" height="360" alt="side" /></div>
				<div class="slide"><img src="<?php echo base_url();?>assets/balrich/img/sample_slides/balrich4.jpg" width="605" height="360" alt="side" /></div>
				<div class="slide"><img src="<?php echo base_url();?>assets/balrich/img/sample_slides/balrich5.jpg" width="605" height="360" alt="side" /></div>
				<div class="slide"><img src="<?php echo base_url();?>assets/balrich/img/sample_slides/balrich6.jpg" width="605" height="360" alt="side" /></div>
			</div>
		<div id="menu1">
			<ul>
				<li class="fbar">&nbsp;</li>
				<li class="menuItem"><a href=""><img src="<?php echo base_url();?>assets/balrich/img/sample_slides/thumb_macbook.png" alt="thumbnail" /></a></li>
				<li class="menuItem"><a href=""><img src="<?php echo base_url();?>assets/balrich/img/sample_slides/thumb_iphone.png" alt="thumbnail" /></a></li>
				<li class="menuItem"><a href=""><img src="<?php echo base_url();?>assets/balrich/img/sample_slides/thumb_imac.png" alt="thumbnail" /></a></li>
				<li class="menuItem"><a href=""><img src="<?php echo base_url();?>assets/balrich/img/sample_slides/thumb_iphone.png" alt="thumbnail" /></a></li>
				<li class="menuItem"><a href=""><img src="<?php echo base_url();?>assets/balrich/img/sample_slides/thumb_imac.png" alt="thumbnail" /></a></li>
				<li class="menuItem"><a href=""><img src="<?php echo base_url();?>assets/balrich/img/sample_slides/thumb_imac.png" alt="thumbnail" /></a></li>
			</ul>
		</div>
		</div>
		</div>--->
			
			
		
	<!---end of content--->
	<!---footer--->
		<div class="mfoot">
		</div>
		<div class="footer">
		<div style="margin-left:0px"><div class="cont"><img src="<?php echo base_url();?>assets/balrich/images/about.png"></div></div>
		<div style="margin-left:0px"><div class="cont"><img src="<?php echo base_url();?>assets/balrich/images/feature.png"></div></div>
		<div style="margin-left:0px"><div class="cont"><img src="<?php echo base_url();?>assets/balrich/images/our.png"></div></div>
		<div style="margin-top:100px" font color="black"><b>Copyright@www.lacak-mobil.com/2012</b></div>
		</div>
	
	<!---end of footer--->
	</div>

	
	
	
	</body>
</html>