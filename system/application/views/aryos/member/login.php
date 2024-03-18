<!DOCTYPE html>
<!--[if lt IE 7]> <html class="lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]> <html class="lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]> <html class="lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <title>Aryos | Vehicle Tracking Management System</title>
  <link rel="stylesheet" href="<?php echo base_url();?>assets/aryos/css/style.css">
  <!--[if lt IE 9]><script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
</head>
<body>
  <section class="container" style="opacity:0.7;">
    <div class="login">
      <h1>LOGIN</h1>
      	<form id="frmlogin" name="frmlogin" onsubmit="javascript : return frmlogin_onsubmit(this);">
        <p><input type="text" name="username" value="" placeholder="Username"></p>
        <p><input type="password" name="userpass" value="" placeholder="Password"></p>
        <p class="remember_me">
         
        </p>
        <p class="submit"><input type="submit" name="commit" value="Login"></p>
      </form>
    </div>

    <div class="login-help">
     
    </div>
  </section>

  <section class="about" style="width:505px; opacity:0.7;">
  <p>Monitoring Support :</p><br>
	<table>
		<tr>
			<td><a href="ymsgr:sendIM?nedi_lacakmobil"><img border=0 src="http://opi.yahoo.com/online?u=nedi_lacakmobil&amp;m=g&amp;t=1" /></a></td>
			<td><a href="ymsgr:sendIM?robi_lacakmobil" style="margin-left:10px;"><img border=0 src="http://opi.yahoo.com/online?u=robi_lacakmobil&amp;m=g&amp;t=1" /></a></td>
			<td><a href="ymsgr:sendIM?erlan_lacakmobil" style="margin-left:10px;"><img border=0 src="http://opi.yahoo.com/online?u=erlan_lacakmobil&amp;m=g&amp;t=1" /></a></td>
			<td><a href="ymsgr:sendIM?wirdi_lacakmobil" style="margin-left:10px;"><img border=0 src="http://opi.yahoo.com/online?u=wirdi_lacakmobil&amp;m=g&amp;t=1" /></a></td>
			<td><a href="ymsgr:sendIM?hadi_lacakmobil" style="margin-left:10px;"><img border=0 src="http://opi.yahoo.com/online?u=hadi_lacakmobil&amp;m=g&amp;t=1" /></a></td>
			<td><a href="ymsgr:sendIM?cici_lacakmobil" style="margin-left:10px;"><img border=0 src="http://opi.yahoo.com/online?u=cici_lacakmobil&amp;m=g&amp;t=1" /></a></td>
			<td><a href="ymsgr:sendIM?ichsan_lacakmobil" style="margin-left:10px;"><img border=0 src="http://opi.yahoo.com/online?u=ichsan_lacakmobil&amp;m=g&amp;t=1" /></a></td>
		</tr>
	</table>
   </section>
	<p align="center" style="margin-top:-30px; color:#fff;">2016@www.lacak-mobil.com</p>
</body>
</html>
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