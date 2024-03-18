<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>KPA - Monitoring Vehicle System</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="<?php echo base_url();?>assets/kpa/css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo base_url();?>assets/kpa/js/jquery-1.3.2.min.js"></script>
<!-- Cufon -->
<script type="text/javascript" src="<?php echo base_url();?>assets/kpa/js/cufon-yui.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/kpa/js/myradpro.font.js"></script>
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
			<a>ONLINE SUPPORT</a>
			<a href="ymsgr:sendIM?jtriyadi1981">
				<img border=0 src="http://opi.yahoo.com/online?u=jtriyadi1981&amp;m=g&amp;t=1"/>
			</a>
			<a href="ymsgr:sendIM?khanza_audah">
				<img border=0 src="http://opi.yahoo.com/online?u=jtriyadi1981&amp;m=g&amp;t=1"/>
			</a>
			<a href="ymsgr:sendIM?eddhiegunasadhega">
				<img border=0 src="http://opi.yahoo.com/online?u=jtriyadi1981&amp;m=g&amp;t=1"/>
			</a>			
		</div>
		<div style="float:right;">
		<br/>
		<a href="http://www.lacak-mobil.com" target="_blank">
		<img border="0" src="<?php echo base_url();?>assets/kpa/images/logolacakmobil.png"/>
		</a>
		</div>
		<div class="clr"></div>
		<div class="menu">
			<div class="logo">
				<a href="index.html">
					<img src="<?php echo base_url();?>assets/kpa/images/logo.png" border="0" alt="logo" />
				</a>
			</div>
		</div>
	</div>
	
	<div id="slider">
		<!-- start slideshow -->
		<div class="flash_slider">
			<img src="<?php echo base_url();?>/assets/kpa/images/banner.jpg" />
		</div>
		<form id="frmlogin" name="frmlogin" onsubmit="javascript : return frmlogin_onsubmit(this);">
		<div class="menu" style="height:60px; width:962px;">
			<ul>
				<li>
					<a class="active">
					<span>Username
						<input name="username" id="username" type="text" />
					</span>
					</a>
				</li>
				
				<li>
					<a class="active">
					<span>Password
						<input type="password" name="userpass" id="userpass" />
					</span>
					</a>
				</li>
				
				<li>
					<a class="active">
					<span>
						<input type="Submit" value="Login" style="background-color:black;color:white;" />
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
			<div class="click_blog"> <img src="<?php echo base_url();?>assets/kpa/images/test_img.gif" alt="picture" width="35" height="29" />
				<p>Intelligent Monitoting Transportation<br />
				<a href="#">Distributed For PT. Karya Putra Andini. </a></p>
			<div class="clr"></div></div>
	</div>

	<div class="clr"></div>
	<div class="FBG">
		<div class="FBG_resize">
			<div class="blog"> <img src="<?php echo base_url();?>assets/kpa/images/fbg_img_1.gif" alt="picture" width="66" height="63" />
				<h2>Fleet Management System<br />
				<span>Control your vehicles</span></h2>
				<div class="clr"></div>
			</div>
			<div class="blog">
				<img src="<?php echo base_url();?>assets/kpa/images/fbg_img_2.gif" alt="picture" width="66" height="63" />
				<h2>Online Conception<br />
				<span>Tracking via website, Mobile, SMS</span></h2>
				<div class="clr"></div>
			</div>
			<div class="blog"> <img src="<?php echo base_url();?>assets/kpa/images/fbg_img_3.gif" alt="picture" width="66" height="63" />
				<h2>Custom Report<br />
				<span>Analyze the performance of vehicles</span></h2>
				<div class="clr"></div>
			</div>
			<div class="clr"></div>
		</div>
	</div>
	
	<div class="body">
		

		<div class="clr"></div>
		</div>
	</div>
  <div class="clr"></div>
</div>

<div class="footer">
  <div class="footer_resize">
    <p class="leftt">© Copyright <a href="http://www.lacak-mobil.com" target="_blank">www.lacak-mobil.com</a> - All Rights Reserved </p>
    <p class="right"> PT. Karya Putra Andini - 2012</a></p>
    <div class="clr"></div>
  </div>
</div>
</body>
</html>