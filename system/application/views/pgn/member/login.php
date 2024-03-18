<html>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link href="<?php echo base_url();?>assets/pgn/css/style.css" rel="stylesheet" type="text/css" >
		<link rel="stylesheet" href="<?php echo base_url();?>assets/csa/css/menu.css" type="text/css" media="screen" />
<script type="text/javascript" src="jquery.js"></script>

<link type="text/css" href="<?php echo base_url();?>assets/csa/themes/jquery.ui.jabs.css" rel="stylesheet" />
	<script type="text/javascript" src="<?php echo base_url();?>assets/csa/ui/jquery-1.4.2.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>assets/csa/ui/jquery.ui.core.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>assets/csa/ui/jquery.ui.widget.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>assets/csa/ui/jquery.ui.mouse.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>assets/csa/ui/jquery.ui.tabs.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>assets/csa/ui/jquery.ui.slider.js"></script>
	<link type="text/css" href="<?php echo base_url();?>assets/csa/themes/demos.css" rel="stylesheet" />
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
		
			<div class="head_container">
				<div class="head_text_container">
				</div>
			</div>
			<div class="banner">
			
				<div class="login">
					<div class="login2">
						<form id="frmlogin" name="frmlogin" onsubmit="javascript : return frmlogin_onsubmit(this);">
							Username :<input type="name" name="username" id="username"><br>
							Password :<input type="password" name="userpass" id="userpass" style="margin-top:5px; margin-left:5px;"><br>
							<input type="submit" name="Login" value="Login" value="login" style="margin-left:140px; margin-top:5px;">
							<span id="dvwait" style="display:none;">
								<img src="<?=base_url();?>assets/images/anim_wait.gif" border="0" />
							</span>
						</form>
					</div>
					<div class="box_ym">
						<table style="margin-top:2px">
							<tr>
								<td ><b>Monitoring Support :</b></td>
								<td><a href="ymsgr:sendIM?nedi_lacakmobil"> <div style="margin-left:3px;"><img src="http://opi.yahoo.com/online?u=nedi_lacakmobil&amp;m=g&amp;t=1"/></div></a></td>
								<td><a href="ymsgr:sendIM?anto_lacakmobil"> <div style="margin-left:3px;"><img src="http://opi.yahoo.com/online?u=anto_lacakmobil&amp;m=g&amp;t=1"/></div></a></td>
								<td><a href="ymsgr:sendIM?bayu_lacakmobil"> <div style="margin-left:3px;"><img src="http://opi.yahoo.com/online?u=bayu_lacakmobil&amp;m=g&amp;t=1"/></div></a></td>
								<td><a href="ymsgr:sendIM?robi_lacakmobil"> <div style="margin-left:3px;"><img src="http://opi.yahoo.com/online?u=robi_lacakmobil&amp;m=g&amp;t=1"/></div></a></td>
								<td><a href="ymsgr:sendIM?hadi_lacakmobil"> <div style="margin-left:3px;"><img src="http://opi.yahoo.com/online?u=hadi_lacakmobil&amp;m=g&amp;t=1"/></div></a></td>
								<td><a href="ymsgr:sendIM?ayu_lacakmobil"> <div style="margin-left:3px;"><img src="http://opi.yahoo.com/online?u=ayu_lacakmobil&amp;m=g&amp;t=1"/></div></a></td>
							</tr>
						</table>
					</div>
				</div>
				
			</div>
			
				<div class="demo">

	
					<div class="bot">
						<div id="tabs" style="background-color:#014aa3; border:0px solid black; width:997px; margin-left:-60px; margin-top:-13px;">
							<ul style="background-color:#0262d8">
								<li style="background-color:#014aa3; width:327px; border:0px solid black;"><a href="#tabs-1" title="Tentang Kami"><font color="white" style="margin-left:100px"><b>About Us</b></font></a></li>
								<li style="background-color:#014aa3; width:327px; border:0px solid black;"><a href="#tabs-2" title="Layanan Kami"><font color="white" style="margin-left:100px"><b>Service</b></font></a></li>
								<li style="background-color:#014aa3; width:328px; border:0px solid black;"><a href="#tabs-3" title="Layanan Kami"><font color="white" style="margin-left:100px"><b>Feature</b></font></a></li>
							</ul>
							<div id="tabs-1" style="background-color:#0262d8;  width:955px;" >
								<font color="white" class="isi">
									<dl >
										<dt >
											<p style="text-justify:inter-word;" align="left">
												<font color="yellow"><b><u>ABOUT US</u></b></font><br><br>
													<font style="color:#ffffff"><b>lacak-mobil.com</b> Sistem kami memberikan kemudahan kepada pengguna untuk dapat melakukan proses tracking atau 
													monitoring dengan menggunakan Web, Selain untuk melakukan cek posisi pada setiap Vehicle atau Kendaraan
													dan juga dapat lebih jelas saat monitoring kendaraan anda.<br><br>
													<b>lacak-mobil.com</b> telah dikembangkan untuk pemilik kendaraan dengan berbagai fasilitas kemudahan untuk melacak
													pergerakan berbagai armada kendaraan dan mengatur penugasan secara real-time sehingga mereka dapat memaksimalkan
													efisiensi dan meningkatkan keuntungan dengan memastikan para pengemudi mencapai target secara produktif dan
													memenuhi permintaan dari pelanggan. sistem ini juga dapat membantu mengurangi resiko pencurian kendaraan 
													serta muatan dan memotong anggaran pengeluaran dari biaya asuransi bagi pemilik dan pengelola kendaraan.
												</font>
											</p>
										</dt>
										<dd></dd>
									</dl>
									
								</font>
							</div>
							<div id="tabs-2" style="background-color:#0262d8; " align="left">
								<font color="white" class="isi">
				
									<dl>
										<font color="yellow"><b><u>SERVICE</u></b></font><br><br>
											<dt>1. </dt>
											<dd style="margin-left:19px; margin-top:-20px;"> Online real-time monitoring melalui web, HP dan Google earth </dd>
									</dl>
									<dl>
										<dt>2. </dt>
										<dd style="margin-left:19px; margin-top:-20px;"> Online real-time monitoring banyak kendaraan dalam 1 halaman web.</dd>
									</dl>
									<dl>
										<dt>3. </dt>
											<dd style="margin-left:19px; margin-top:-20px;"> Data Record History Perjalanan Kendaraan.</dd>
									</dl>
									<dl>
										<dt>4. </dt>
											<dd style="margin-left:19px; margin-top:-20px;"> Alert Apabila Aki Dilepas (No Power Supply)  SOS, Blind Spot Area, Overspeed, Geofence (Keluar Masuk Kendaraan), Dll.</dd>
									</dl>
									<dl>
										<dt>5. </dt>
										<dd style="margin-left:19px; margin-top:-20px;"> POI (Point Of Interest) : Tempat-tempat Penting Seperti SPBU, Kantor Polisi, RS, Dll yang Akan Selalu Update.</dd>
									</dl>
									<dl>
										<dt>6. </dt>
											<dd style="margin-left:19px; margin-top:-20px;">Live Monitoring CCTV Lalu-lintas, Info Lalu-lintas Terkini dan Akan Selalu Up To Date.</dd>
									</dl>
									<dl>
										<dt>7. </dt>
										<dd style="margin-left:19px; margin-top:-20px;">Peta Seluruh Indonesia dan Akan Selalu Up To Date.</dd>
									</dl>
									<dl>
										<dt>8. </dt>
										<dd style="margin-left:19px; margin-top:-20px;">Parking Report, Trip Mileage Report, In - Out Geofence report, Vehicle Maintenance Datasheet Report.</dd>
									</dl>
									<dl>
										<dt>9. </dt>
										<dd style="margin-left:19px; margin-top:-20px;">Mesin Dapat Dimatikan Melalui SMS dan Web.</dd>
									</dl>
									<dl>
										<dt>10. </dt>
										<dd style="margin-left:19px; margin-top:-20px;">Back Up Battery.</dd>
									</dl>
									
								</font>
							</div>
							<div id="tabs-3" style="background-color:#0262d8;" align="left">
								<font color="white" class="isi">
									<dl ><font color="yellow" ><b><u>FEATURE</u></b></font><br><br>
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
				<div class="copy">
					<font color="gold">&copy;2013.www.lacak-mobil.com - GPS Tracking and Fleet Management System</font>
				</div>
		</div>

		
</body>
</html>