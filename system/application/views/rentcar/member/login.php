<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Rent Car Solution</title>
    

    <link href="<?php echo base_url();?>assets/rentcar/css/demo.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url();?>assets/rentcar/css/slide.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/rentcar/js/slide.js"></script>

    <!--menu backgorund-->
    <link href="<?php echo base_url();?>assets/rentcar/css/style.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="<?php echo base_url();?>assets/rentcar/js/jquery-1.3.2.min.js"></script>
    <!-- Cufon -->
    <script type="text/javascript" src="<?php echo base_url();?>assets/rentcar/js/cufon-yui.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/rentcar/js/myradpro.font.js"></script>
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

<!-- Panel -->
<div id="toppanel">
	<div id="panel">
		<div class="content clearfix">
			<div class="left">
				<h1 align="center">Features</h1>
				<img align="center" src="<?php echo base_url();?>assets/rentcar/images/panel-left.png"/>		
			</div>
            
            
            
			<div class="left">
				<!-- Login Form -->
				<form id="frmlogin" name="frmlogin" class="clearfix" onsubmit="javascript : return frmlogin_onsubmit(this);">
					<h1>Member Login</h1>
                    <label class="grey" for="username">Username:</label>
					<input class="field" type="text" name="username" id="username" value="" size="23" />
					<label class="grey" for="password">Password:</label>
					<input class="field" type="password" name="userpass" id="userpass" size="23" />
	            	<div class="clear"></div>
					<input type="submit" name="submit" value="Login" class="bt_login" />
					<span id="dvwait" style="display:none;">
						<img src="<?=base_url();?>assets/images/anim_wait.gif" border="0" />
					</span>
                    <br />
				    
                </form>
			</div>
			<div class="left right">			
				
			</div>
            
            
            <div class="left">
            
                <h1 align="center">Rent Car Solution</h1>
                <img align="center" src="<?php echo base_url();?>assets/rentcar/images/panel-right.png"/>
            
            </div>
          
            
		</div>
	</div> 

    <!-- The tab on top -->	
	<div class="tab">
		<ul class="login">
	    	<li class="left">&nbsp;</li>
	        <li>Welcome</li>
			<li class="sep">|</li>
			<li id="toggle">
				<a id="open" class="open" >Login Panel</a>
				<a id="close" style="display: none;" class="close" >Close Panel</a>			
			</li>
	    	<li class="right">&nbsp;</li>
		</ul> 
	</div> <!-- / top -->
	
</div> <!--panel -->

<!--body-->
<div class="main">
	<div class="header">
		<div class="rss">
        <br />
			
		</div>
		
		<div class="clr"></div>
		<div class="headmenu">
       
        <br />
        <br />
            
		</div>
	</div>
	
	<div id="slider">
		<!-- start slideshow -->
		<div class="flash_slider">
			<img src="<?php echo base_url();?>/assets/rentcar/images/banner.png" />
		</div>
	
		<div class="footmenu" style="height:60px; width:962px;">
	  
		</div>
            <div class="click_blog" align="right">
                    <a href="ymsgr:sendim?anto_lacakmobil" title="Anto">
                    <img border=0 src="http://opi.yahoo.com/online?u=anto_lacakmobil&amp;m=g&amp;t=1" />
                    </a>
                    <a href="ymsgr:sendim?bayu_lacakmobil" title="Bayu">
                    <img border=0 src="http://opi.yahoo.com/online?u=bayu_lacakmobil&amp;m=g&amp;t=1" />
                    </a>
                    <a href="ymsgr:sendim?robi_lacakmobil" title="Robi">
                    <img border=0 src="http://opi.yahoo.com/online?u=robi_lacakmobil&amp;m=g&amp;t=1" />
                    </a>
					<a href="ymsgr:sendim?rico_lacakmobil" title="Rico">
                    <img border=0 src="http://opi.yahoo.com/online?u=nedi_lacakmobil&amp;m=g&amp;t=1" />
                    </a>
					<a href="ymsgr:sendim?nedi_lacakmobil" title="Nedi">
                    <img border=0 src="http://opi.yahoo.com/online?u=nedi_lacakmobil&amp;m=g&amp;t=1" />
                    </a>
                    <a>	ONLINE SUPPORT</a>
            </div>
        </div>
                
	</div>

</body>
</html>
