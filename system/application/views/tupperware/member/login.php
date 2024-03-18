<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/tupperware/css/style.css" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/tupperware/css/menu.css" type="text/css" media="screen" />
<script type="text/javascript" src="jquery.js"></script>

<link type="text/css" href="<?php echo base_url();?>assets/tupperware/themes/jquery.ui.jabs.css" rel="stylesheet" />
	<script type="text/javascript" src="<?php echo base_url();?>assets/tupperware/ui/jquery-1.4.2.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>assets/tupperware/ui/jquery.ui.core.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>assets/tupperware/ui/jquery.ui.widget.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>assets/tupperware/ui/jquery.ui.mouse.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>assets/tupperware/ui/jquery.ui.tabs.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>assets/tupperware/ui/jquery.ui.slider.js"></script>
	<link type="text/css" href="<?php echo base_url();?>assets/tupperware/themes/demos.css" rel="stylesheet" />
	<style type="text/css">
		#demo-frame > div.demo { padding: 10px !important; }
	</style>
<script type="text/javascript">
	$(function() {
		$("#tabs").tabs({
			select: function(event, ui) {
				$("#slider").slider("value", ui.index);
			}
		});
		$("#slider").slider({
			min: 0,
			max: $("#tabs").tabs("length") - 1,
			slide: function(event, ui) {
				$("#tabs").tabs("select", ui.value);
			}
		});
	});
	
	</script>
	<script type="text/javascript" src="simple-slide-panel_data/jquery.js"></script>
	<script type="text/javascript">
$(document).ready(function(){

	$(".men2 a").append("<em></em>");
	
	$(".men2 a").hover(function() {
		$(this).find("em").animate({opacity: "show", top: "-75"}, "slow");
		var hoverText = $(this).attr("img src");
	    $(this).find("em").text(hoverText);
	}, function() {
		$(this).find("em").animate({opacity: "hide", top: "-85"}, "fast");
	});


});


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
	<div class="box3">
		<div id="head">
			<div class="boxlogin">
				<form id="frmlogin" name="frmlogin" onsubmit="javascript : return frmlogin_onsubmit(this);">
					<table border="0px solid white" style="margin-left:0px; margin-top:0px;">
						<tr>
						<td><font color="white" size="3px">Username:</font></td>
						<td><input class="form" type="name" name="username" id="username" style="background-color:white; opacity:0.7; border-color:white; width:150px; margin-left:0px;"/></td>
						</tr>
						<tr>
						<td><font color="white" size="3px">Password:</font></td>
						<td><input class="form" type="password" name="userpass" id="userpass" style="background-color:white; opacity:0.7; border-color:white; width:150px; margin-left:0px;"/></td>
						</tr>
						
						<tr>
						<td></td>
						<td><div style="margin-left:105px"><input  class="form" type="submit" name="Login" value="Login" style="background-color:white; opacity:0.7; border-color:white; width:50px;"/></div>
						</td>
						<td><span id="dvwait" style="display:none;">
							<img src="<?=base_url();?>assets/images/anim_wait.gif" border="0" />
						</span></td>
						</tr>
					</table>
				</form>
			</div>
		</div>
		<div class="boxcont">
			<div class="boxym">
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
	</div>
	
			<div class="demo">

	
	<div class="bot">
		<div id="tabs" style="background-color:transparent;; border:0px solid black; margin-top:-20px; width:895px; margin-left:-10px;">
			<ul style="background-color:#ffffff">
				<li style="background-color:#9b3da5; width:293px; border:0px solid black;"><a href="#tabs-1" title="Tentang Kami"><font color="white" style="margin-left:100px"><b>About Us</b></font></a></li>
				<li style="background-color:#9b3da5; width:293px; border:0px solid black;"><a href="#tabs-2" title="Layanan Kami"><font color="white" style="margin-left:100px"><b>Service</b></font></a></li>
				<li style="background-color:#9b3da5; width:296px; border:0px solid black;"><a href="#tabs-3" title="Layanan Kami"><font color="white" style="margin-left:100px"><b>Feature</b></font></a></li>
			</ul>
			<div id="tabs-1" style="background-color:transparent; width:865px;" >
				<font color="white" class="isi">
					<dl >
						<dt >
							<p style="text-justify:inter-word;">
								<font color="white"><b><u>ABOUT US</u></b></font><br><br>
								<b>lacak-mobil.com</b> Sistem kami memberikan kemudahan kepada pengguna untuk dapat melakukan proses tracking atau 
								monitoring dengan menggunakan Web, Selain untuk melakukan cek posisi pada setiap Vehicle atau Kendaraan
								dan juga dapat lebih jelas saat monitoring kendaraan anda.<br><br>
								<b>lacak-mobil.com</b> telah dikembangkan untuk pemilik kendaraan dengan berbagai fasilitas kemudahan untuk melacak
								pergerakan berbagai armada kendaraan dan mengatur penugasan secara real-time sehingga mereka dapat memaksimalkan
								efisiensi dan meningkatkan keuntungan dengan memastikan para pengemudi mencapai target secara produktif dan
								memenuhi permintaan dari pelanggan. sistem ini juga dapat membantu mengurangi resiko pencurian kendaraan 
								serta muatan dan memotong anggaran pengeluaran dari biaya asuransi bagi pemilik dan pengelola kendaraan.
							</p>
						</dt>
					<dd></dd>
					</dl>
									
				</font>
			</div>
			<div id="tabs-2" style="background-color:transparent;">
				<font color="white" class="isi">
				
					<dl>
						<font color="white"><b><u>SERVICE</u></b></font><br><br>
						<dt>1. </dt>
						<dd style="margin-left:19px; margin-top:-25px;"> Online real-time monitoring melalui web, HP dan Google earth </dd>
					</dl>
					<dl>
						<dt>2. </dt>
						<dd style="margin-left:19px; margin-top:-25px;"> Online real-time monitoring banyak kendaraan dalam 1 halaman web.</dd>
					</dl>
					<dl>
						<dt>3. </dt>
						<dd style="margin-left:19px; margin-top:-25px;"> Data Record History Perjalanan Kendaraan.</dd>
					</dl>
					<dl>
						<dt>4. </dt>
						<dd style="margin-left:19px; margin-top:-25px;"> Alert Apabila Aki Dilepas (No Power Supply)  SOS, Blind Spot Area, Overspeed, Geofence (Keluar Masuk Kendaraan), Dll.</dd>
					</dl>
					<dl>
						<dt>5. </dt>
						<dd style="margin-left:19px; margin-top:-25px;"> POI (Point Of Interest) : Tempat-tempat Penting Seperti SPBU, Kantor Polisi, RS, Dll yang Akan Selalu Update.</dd>
					</dl>
					<dl>
						<dt>6. </dt>
						<dd style="margin-left:19px; margin-top:-25px;">Live Monitoring CCTV Lalu-lintas, Info Lalu-lintas Terkini dan Akan Selalu Up To Date.</dd>
					</dl>
					<dl>
						<dt>7. </dt>
						<dd style="margin-left:19px; margin-top:-25px;">Peta Seluruh Indonesia dan Akan Selalu Up To Date.</dd>
					</dl>
					<dl>
						<dt>8. </dt>
						<dd style="margin-left:19px; margin-top:-25px;">Parking Report, Trip Mileage Report, In - Out Geofence report, Vehicle Maintenance Datasheet Report.</dd>
					</dl>
					<dl>
						<dt>9. </dt>
						<dd style="margin-left:19px; margin-top:-25px;">Mesin Dapat Dimatikan Melalui SMS dan Web.</dd>
					</dl>
					<dl>
						<dt>10. </dt>
						<dd style="margin-left:19px; margin-top:-25px;">Back Up Battery.</dd>
					</dl>
									
				</font>
			</div>
			<div id="tabs-3" style="background-color:transparent;">
				<font color="white" class="isi">
					<dl ><font color="white" ><b><u>FEATURE</u></b></font><br><br>
						<dt><b>1. Customer Care</b> </dt>
						<dd style="margin-left:15px; "> Online real-time monitoring melalui web, HP dan Google earth. </dd>
					</dl>
					<dl>
						<dt><b>2. Monitoring Support</b> </dt>
						<dd style="margin-left:15px;"> Kendaraan di Monitoring Oleh Team Monitoring.</dd>
					</dl>
					<dl>
						<dt><b>3. Free Consulting & Training</b> </dt>
						<dd style="margin-left:15px;"> Gratis Konsultasi Training System dan Produk.</dd>
					</dl>
					<dl>
						<dt><b>4. Free Installation</b> </dt>
						<dd style="margin-left:15px;"> Gratis Biaya Setting dan Pemasangan</dd>
					</dl>
					<dl>
						<dt><b>5. Free SMS Tracking System</b> </dt>
						<dd style="margin-left:15px;"> - SMS Tracking<br> - Alert</dd>
					</dl>
					<dl>
						<dt><b>6. Lifetime Warranty</b> </dt>
						<dd style="margin-left:15px;"> Garansi Seumur Hidup</dd>
					</dl>
					<dl>
						<dt><b>7. Replacement Warranty</b> </dt>
						<dd style="margin-left:15px;"> Garansi Ganti Unit Baru</dd>
					</dl>
					<dl>
						<dt><b>8. Customizing</b> </dt>
						<dd style="margin-left:15px;"> Aplikasi Disesuaikan Dengan Keinginan dan Kebutuhan Customer.</dd>
					</dl>
									
				</font>
					
			</div>
		</div>
	</div>

</div>

<p style="background-color:transparent;"><a href="http://www.lacak-mobil.com" target="blank"><font color="#fffffff" size="3px">&copy; 2013 www.lacak-mobil.com</font></a></p>
 


</div>
</body>
</html>
