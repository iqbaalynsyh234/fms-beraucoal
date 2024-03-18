<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>GPS Andalas - Monitoring Vehicle System</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="<?php echo base_url();?>assets/transportergpsandalas/css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo base_url();?>assets/transportergpsandalas/js/jquery-1.3.2.min.js"></script>
<!-- Cufon -->
<script type="text/javascript" src="<?php echo base_url();?>assets/transportergpsandalas/js/cufon-yui.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/transportergpsandalas/js/myradpro.font.js"></script>
<script type="text/javascript">
Cufon.replace('h1')('h2')('h3')('h4')('div.menu li');
</script>
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

</head>
<body>
<div class="main">
	<div class="header">
		<div class="rss">
        <br />
			
		</div>
		
		<div class="clr"></div>
		<div class="headmenu">
        
        <div class="logo">
				<a href="index.html">
                <br />
					<img src="<?php echo base_url();?>assets/transportergpsandalas/images/logo.png" border="0" alt="logo" />
				</a>
          
		</div>
        <br />
        <br />
            <form id="frmlogin" name="frmlogin" onsubmit="javascript : return frmlogin_onsubmit(this);">            
		    <div class="loginmenu" style="height:100px; width:960px;" align="right">
			     <ul>
				 <li>
					<a class="active">
					<span>Username</span>
						<input name="username" id="username" type="text" />
					</a>
				 </li>
				
				 <li>
					<a class="active">
					<span>Password</span>
						<input type="password" name="userpass" id="userpass" />
					</a>
				 </li>
				
				 <li>
					<a class="active">
                    <span>
                    <p>
                    <p>
						<input type="Submit" value="Login" style="background-color:inherit;color:white;" />
					</span>
					</a>
				</li>
				<li>
					<span id="dvwait" style="display:none;">
						<img src="<?=base_url();?>assets/images/anim_wait.gif" border="0" />
					</span>
				</li>
			</ul>
		</div>
		</form>
			
		</div>
	</div>
	
	<div id="slider">
		<!-- start slideshow -->
		<div class="flash_slider">
			<img src="<?php echo base_url();?>/assets/transportergpsandalas/images/banner11.jpg" />
		</div>
		
		<div class="footmenu" style="height:60px; width:962px;">
		
                
                <div class="click_blog" align="right">
                <a href="ymsgr:sendim?dedy.gpsandalas">
                <img border=0 src="http://opi.yahoo.com/online?u=dedy.gpsandalas&amp;m=g&amp;t=1" title="Marketing II"/>
				</a>
				<a href="ymsgr:sendim?zad_anwar">
				<img border=0 src="http://opi.yahoo.com/online?u=zad_anwar&amp;m=g&amp;t=1" title="Marketting I"/>
	       		</a>
				<a href="ymsgr:sendim?vidi_nia">
                <img border=0 src="http://opi.yahoo.com/online?u=vidi_nia&amp;m=g&amp;t=1" title="Info Tagihan"/>
                </a>
				<a href="ymsgr:sendim?cs_andalas02">
                <img border=0 src="http://opi.yahoo.com/online?u=cs_andalas02&amp;m=g&amp;t=1" title="Monitoring II"/>
                </a>
                <a href="ymsgr:sendim?j.andalas">
                <img border=0 src="http://opi.yahoo.com/online?u=j.andalas&amp;m=g&amp;t=1" title="Monitoring I"/>
                </a>
                
           		<a>	ONLINE SUPPORT</a></div>

            </div>
		</div>
  
			<div class="click_blog" align="right">
		
                </div>

			<div class="clr"></div></div>
	</div>

	    <div class="clr"></div>
	    <div class="FBG">
		<div class="FBG_resize">
			<div class="blog"> <img src="<?php echo base_url();?>assets/transportergpsandalas/images/fbg_img_1.gif" alt="picture" width="66" height="63" />
				<h2>Fleet Management System<br />
				<span>Control your vehicles</span></h2>
				<div class="clr"></div>
			</div>
			<div class="blog">
				<img src="<?php echo base_url();?>assets/transportergpsandalas/images/fbg_img_2.gif" alt="picture" width="66" height="63" />
				<h2>Online Conception<br />
				<span>Tracking via website, Mobile, SMS</span></h2>
				<div class="clr"></div>
			</div>
			<div class="blog"> <img src="<?php echo base_url();?>assets/transportergpsandalas/images/fbg_img_3.gif" alt="picture" width="66" height="63" />
				<h2>Custom Report<br />
				<span>Analyze the performance of vehicles</span></h2>
				<div class="clr"></div>
			</div>
			<div class="clr"></div>
		</div>
	</div>
	
	</div>
  <div class="clr"></div>
</div>

<div class="footer">
  <div class="footer_resize">
	<p class="left"><a href="#">Distributed For Andalas Jaya Corporation. </a>
    <p class="left">� Copyright <a href="http://www.gpsandalas.com" target="_blank">www.gpsandalas.com</a> - All Rights Reserved </p>
    <p class="right"> Andalas Jaya Corporation - 2012</a></p>
    <div class="clr"></div>
  </div>
</div>
</body>
</html>