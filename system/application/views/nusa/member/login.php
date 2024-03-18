<style type="text/css">
div#container
{
   position: absolute;
   left: 50%;
   width: 994px;
   height: 768px;
   margin-top: -384px;
   margin-left: -497px;
}
</style>
<script>
			<!--
			
				jQuery(document).ready(
					function()
					{																														
       					var zIndexNumber = 1000;
       					// Put your target element(s) in the selector below!
       					jQuery("div").each(function() {
               				jQuery(this).css('zIndex', zIndexNumber);
               				zIndexNumber += 10;
       					})
       					
       					jQuery("#container").css("margin-top", -130);
					}
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
			-->
			</script>	

<body bgcolor="#FFFFFF" text="#000000">
<div id="container">
<div id="background" style="position:absolute;left:189px;top:187px;width:615px;height:299px;z-index:1;" align="left">
<img src="<?=base_url();?>system/application/views/nusa/template/images/background.png" id="Image1" alt="" align="top" border="0" style="width:615px;height:299px;">
</div>

<div id="logo_nusa" style="position:absolute;left:195px;top:110px;width:75px;height:75px;z-index:2;" align="left">
<img src="<?=base_url();?>system/application/views/nusa/template/images/logo_nusa.png" id="Image2" alt="" align="top" border="0" style="width:75px;height:75px;"></div>

<div id="back_cs" style="position:absolute;left:200px;top:199px;width:285px;height:221px;z-index:3;" align="left">
<img src="<?=base_url();?>system/application/views/nusa/template/images/back_cs.png" id="Image3" alt="" align="top" border="0" style="width:285px;height:221px;"></div>

<div id="logo_gps" style="position:absolute;left:463px;top:169px;width:265px;height:47px;z-index:4;" align="left">
<img src="<?=base_url();?>system/application/views/nusa/template/images/logo_gps.png" id="Image4" alt="" align="top" border="0" style="width:265px;height:47px;"></div>

<div id="wb_Text1" style="position:absolute;left:504px;top:219px;width:90px;height:14px;z-index:5;" align="left">
<font style="font-size:11px" color="#FFFFFF" face="Arial"><b>Member Login</b></font></div>

<div id="wb_Image5" style="position:absolute;left:493px;top:238px;width:12px;height:16px;z-index:6;" align="left">
<img src="<?=base_url();?>system/application/views/nusa/template/images/img_member.png" id="Image5" alt="" align="top" border="0" style="width:12px;height:16px;"></div>

<div id="wb_Image6" style="position:absolute;left:509px;top:237px;width:106px;height:18px;z-index:7;" align="left">
<img src="<?=base_url();?>system/application/views/nusa/template/images/back_textform.png" id="Image6" alt="" align="top" border="0" style="width:106px;height:18px;"></div>

<div id="wb_Text2" style="position:absolute;left:494px;top:284px;width:58px;height:16px;z-index:8;" align="left">
<font style="font-size:13px" color="#FFFFFF" face="Arial"><b>Hotline</b></font></div>

<div id="wb_Image8" style="position:absolute;left:494px;top:307px;width:9px;height:9px;z-index:9;" align="left">
<img src="<?=base_url();?>system/application/views/nusa/template/images/logo_tlp.png" id="Image8" alt="" align="top" border="0" style="width:9px;height:9px;"></div>

<div id="wb_Text3" style="position:absolute;left:508px;top:302px;width:127px;height:19px;z-index:10;" align="left">
<font style="font-size:16px" color="#FFFFFF" face="Arial"><b>021 - 8244 2090</b></font></div>

<div id="wb_Text4" style="position:absolute;left:508px;top:321px;width:127px;height:19px;z-index:11;" align="left">
<font style="font-size:16px" color="#FFFFFF" face="Arial"><b>021 - 7016 6695</b></font></div>

<div id="wb_Image9" style="position:absolute;left:494px;top:324px;width:9px;height:9px;z-index:12;" align="left">
<img src="<?=base_url();?>system/application/views/nusa/template/images/logo_tlp.png" id="Image9" alt="" align="top" border="0" style="width:9px;height:9px;"></div>

<div id="wb_Text5" style="position:absolute;left:494px;top:367px;width:58px;height:16px;z-index:13;" align="left">
<font style="font-size:13px" color="#FFFFFF" face="Arial"><b>SMS</b></font></div>

<div id="wb_Image10" style="position:absolute;left:495px;top:388px;width:9px;height:9px;z-index:14;" align="left">
<img src="<?=base_url();?>system/application/views/nusa/template/images/sms_logo.png" id="Image10" alt="" align="top" border="0" style="width:9px;height:9px;"></div>

<div id="wb_Text6" style="position:absolute;left:508px;top:383px;width:136px;height:19px;z-index:15;" align="left">
<font style="font-size:16px" color="#FFFFFF" face="Arial"><b>0812 8203 39555</b></font></div>

<div id="wb_Text7" style="position:absolute;left:206px;top:206px;width:149px;height:16px;z-index:16;" align="left">
<font style="font-size:13px" color="#000000" face="Arial"><b>CUSTOMER SERVICES</b></font></div>

<div id="wb_Text8" style="position:absolute;left:206px;top:233px;width:39px;height:14px;z-index:17;" align="left">
<font style="font-size:11px" color="#000000" face="Arial"><b>Antie</b></font></div>

<div id="wb_Text9" style="position:absolute;left:205px;top:267px;width:45px;height:14px;z-index:18;" align="left">
<font style="font-size:11px" color="#000000" face="Arial"><b>Salman</b></font></div>

<div id="wb_Text10" style="position:absolute;left:206px;top:301px;width:45px;height:14px;z-index:19;" align="left">
<font style="font-size:11px" color="#000000" face="Arial"><b>Ichsan</b></font></div>

<div id="wb_Text11" style="position:absolute;left:206px;top:336px;width:45px;height:14px;z-index:20;" align="left">
<font style="font-size:11px" color="#000000" face="Arial"><b>Rizki</b></font></div>

<div id="wb_Image7" style="position:absolute;left:639px;top:237px;width:106px;height:18px;z-index:21;" align="left">
<img src="<?=base_url();?>system/application/views/nusa/template/images/back_textform.png" id="Image7" alt="" align="top" border="0" style="width:106px;height:18px;"></div>

<div id="wb_Image11" style="position:absolute;left:618px;top:237px;width:17px;height:17px;z-index:23;" align="left">
<img src="<?=base_url();?>system/application/views/nusa/template/images/password.png" id="Image11" alt="" align="top" border="0" style="width:17px;height:17px;">
</div>

<div style="position:absolute;">
<form id="frmlogin" onSubmit="javascript: return frmlogin_onsubmit(this)">
<div id="dvusername">
<input type="text" id="username" style="position:absolute;left:508px;top:237px;width:103px;border:0px #000000 inset;background-color:transparent;color:#FFFFFF;font-family:Arial Narrow;font-size:13px;z-index:22" name="username" value="" maxlength="50">
</div>
<div id="dvuserpass">
<input type="password" id="userpass" style="position:absolute;left:637px;top:237px;width:103px;border:0px #000000 inset;background-color:transparent;color:#FFFFFF;font-family:Arial Narrow;font-size:13px;z-index:24" name="userpass" value="" maxlength="50">
</div>
<div id="dvbtnlogin">
<input type="submit" id="Button1" value="Login" style="position:absolute;left:752px;top:236px;width:45px;height:20px;font-family:Arial;font-size:11px;z-index:25">
</div>
<div id="dvwait" style="position:absolute;left:755px;top:200px;display: none;">
		<img src="<?=base_url();?>assets/images/anim_wait.gif" border="0" />
	</div>
	<?php if ($this->config->item("showmessagelogin")) { ?>
	<div id="dvwait" style="position:absolute;left:228px;top:405px; width: 520px; text-align: center;">
		<font face="Tahoma" color="#ff0000">Mohon maaf dikarenakan ada maintenance server, untuk sementara <br /><b>http://www.lacak-mobil.com dan sub domainnya</b><br /> dialihkan ke <br /><b>http://119.235.20.251</b></font>		
	</div>
	<?php } ?>
</form>
</div>


<div style="position:absolute;left:350px;top:230px;width:99px;height:14px;z-index:42;" align="left">
<a href="ymsgr:sendIM?triharjanti">
<img border=0 src="http://opi.yahoo.com/online?u=triharjanti&amp;m=g&amp;t=1"/></a>
</div>

<div style="position:absolute;left:350px;top:266px;width:99px;height:14px;z-index:42;" align="left">
<a href="ymsgr:sendIM?salman_w3indo">
<img border=0 src="http://opi.yahoo.com/online?u=salman_w3indo&amp;m=g&amp;t=1"/></a>
</div>

<div style="position:absolute;left:350px;top:299px;width:99px;height:14px;z-index:42;" align="left">
<a href="ymsgr:sendIM?acebae">
<img border=0 src="http://opi.yahoo.com/online?u=acebae&amp;m=g&amp;t=1"/></a>
</div>

<div style="position:absolute;left:350px;top:333px;width:99px;height:14px;z-index:42;" align="left">
<a href="ymsgr:sendIM?w3indo.rizky">
<img border=0 src="http://opi.yahoo.com/online?u=w3indo.rizky&amp;m=g&amp;t=1"/></a>
</div>




<div id="wb_Text12" style="position:absolute;left:206px;top:373px;width:227px;height:42px;z-index:26;" align="left">
<font style="font-size:11px" color="#FF0000" face="Arial"><b>Email</b></font><font style="font-size:11px" color="#000000" face="Arial"><b><br>
Sales : sales@nusasecurity.com<br>
Support : support@nusasecurity.com</b></font></div>
</div>