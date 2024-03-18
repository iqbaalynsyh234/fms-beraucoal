<script>
			<!--
				function frmlogin_onsubmit()
				{
					jQuery("#wait").show();
					jQuery.post("<?=base_url();?>member/dologin", jQuery("#frmlogin").serialize(),
						function(r)
						{
							jQuery("#wait").hide();
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
<body bgcolor="#000000" text="#000000">
<center>
<div id="wb_Image2" align="center">
  <div align="center"><img src="<?=base_url();?>assets/farrasindo/images/img1.png" alt="" name="Image2" border="0" align="top" id="Image2" style="width:872px;height:136px;"></div>
</div>
<div id="wb_Image1"  align="center"><img src="<?=base_url();?>assets/farrasindo/images/img2.jpg" id="Image1" alt="" align="top" border="0" style="width:872px;height:165px;">
<img src="<?=base_url();?>assets/farrasindo/images/img3.jpg" id="Image6" alt="" align="top" border="0" style="width:872px;height:153px;"><img src="<?=base_url();?>assets/farrasindo/images/img4.png" id="Image3" alt="" align="top" border="0" style="width:872px;height:108px;"></div>
<form id="frmlogin" onsubmit="javascript: return frmlogin_onsubmit(this)">
  <div id="wb_Text2" style="position:absolute; left:720px; top:120px; width:75px; height:16px; z-index:0;" align="center">
<font style="font-size:13px" color="#FF0000" face="Arial"><b>Username</b></font></div>
<div id="wb_Text1" style="position:absolute; left:720px; top:154px; width:75px; height:16px; z-index:1;" align="center">
<font style="font-size:13px" color="#FF0000" face="Arial"><b>Password</b></font></div>
<input type="text" name="username" id="username" value="" class="formshort" style="position:absolute; left:820px; top:115px; width:149px; font-family:Courier New; font-size:16px; z-index:2">
<input type="password" name="userpass" id="userpass" value="" class="formshort" style="position:absolute; left:820px; top:154px; width:151px; font-family:Courier New; font-size:16px; z-index:3">
<input type="submit" value=" Login " style="position:absolute; left:820px; top:186px; width:151px; font-family:Courier New; font-size:16px; z-index:4">
<div id="wait" style="display: none;"><img src="<?=base_url();?>assets/images/anim_wait.gif" border="0" /></div>
<div id="wb_Image6"  align="center"></div>

<div id="wb_Image3" align="center"></div>

<div id="wb_Image4" style="position:absolute; left:196px; top:472px; width:112px; height:108px; z-index:12;" align="left">
<img src="<?=base_url();?>assets/farrasindo/images/img-secure.png" id="Image4" alt="" align="top" border="0" style="width:112px;height:108px;"></div>
</form>

</body>
</html>