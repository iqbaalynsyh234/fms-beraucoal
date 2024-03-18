<style type="text/css">
div#container
{
   position: absolute;
   left: 50%;
   top: 50%;
   width: 994px;
   height: 768px;
   margin-top: -384px;
   margin-left: -497px;
}
</style>
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
<body bgcolor="#FFFFFF" text="#000000">
<div id="container">
<form id="frmlogin" onSubmit="javascript: return frmlogin_onsubmit(this)">
  <div id="wb_Image1" style="position:absolute;left:180px;top:180px;width:525px;height:240px;z-index:1;" align="left">
  <img src="<?=base_url();?>assets/agungputra/login.jpg" id="Image1" alt="" align="top" border="0" style="width:525px;height:240px;"></div>
<div id="dvusername">
<input type="text" id="username" style="position:absolute;left:584px;top:262px;width:98px;background-color:#009300;color:#FFFFFF;font-family:Calibri;font-size:13px;z-index:2" name="username" value="" class="formshort">
</div>
<div id="dvuserpass">
<input type="password" id="userpass" style="position:absolute;left:584px;top:302px;width:98px;background-color:#009300;color:#FFFFFF;font-family:Calibri;font-size:13px;z-index:3" name="userpass" value="" class="formshort">
</div>
<div id="dvbtnlogin">
<input type="submit" value="Login" style="position:absolute;left:627px;top:337px;width:46px;height:25px;background-color:transparent;color:#FFFFFF;font-family:Calibri;font-size:13px;z-index:4">
</div>
</form>
    <div id="wait" style="position:absolute; display:none;left:660px;top:160px;">
    <img src="<?=base_url();?>assets/images/anim_wait.gif" border="0" />
</div>
</div>
</body>
