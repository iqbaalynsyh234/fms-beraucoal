<html>
	<title>Reksa Pratama</title>
	<link rel="stylesheet" href="<?php echo base_url();?>assets/reksaprabawa/css/style.css" type="text/css"> 
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
	<div align="center">
		<!--head-->
			<div id="header">
				<div id="head">
					<div class="logo">
					</div>
					<div class="box2">
					</div>
				</div>
			</div>
		<!--end head-->
	
		<!--content-->
			<div id="content">
				<div class="form">
					<form id="frmlogin" name="frmlogin" onsubmit="javascript : return frmlogin_onsubmit(this);">
						<input type="name" name="username" id="username">
						<input type="password" name="userpass" id="userpass"style="margin-top:3px">
						<input type="submit" value="login" style="margin-top:2px; margin-left:90px;">
						<span id="dvwait" style="display:none;">
						<img src="<?=base_url();?>assets/images/anim_wait.gif" border="0" />
						</span>
					</form>
					
				</div>
				<div class="slide">
						<marquee scrollamount="3">
							<img src="<?php echo base_url();?>assets/reksaprabawa/images/1.jpg">
							<img src="<?php echo base_url();?>assets/reksaprabawa/images/3.jpg">
							<img src="<?php echo base_url();?>assets/reksaprabawa/images/4.jpg">
							<img src="<?php echo base_url();?>assets/reksaprabawa/images/5.jpg">
						</marquee>
					</div>
			</div>
		<!--end content-->
		
		<!--footer-->
			<div id="footer">
				<div class="copy">
				<table style="margin-top:0px">
						<tr>
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
		<!--end footer-->
	</div>
</body>
</html>