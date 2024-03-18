  <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
  <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
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
			
/*			function closenewyear()
  	{
  		$("#gbr").hide("scale", {}, 1000);
  		$("#container").show("scale", {}, 1000);
 
  	}
  	
 	$(document).ready(
  		function()
  		{
  			
  			$("#gbr").css("left", ($(document).width() - 512)/2);
  			$("#gbr").css("top", ($(document).height() - 512)/2);
  		  $("#gbr").show("scale", {}, 1000);
  		  $("#container").hide();
  			
  		}
  	); */
  	
			</script>
			<style type="text/css">
div#container
{
   position: absolute;
   left: 50%;
   right: 50%;
   margin-top: auto;
   margint-bottom:auto;
   width: 994px;
   margin-left: -450px;
}

#gbr {
   position: absolute;
   width: 530px;
   height: 420px;
   display: none;
  	}

</style>
<body bgcolor="#FFFFFF" text="#000000">
<div id="container">
<form id="frmlogin" onSubmit="javascript: return frmlogin_onsubmit(this)">
<div id="wb_Image1" style="position:absolute;left:170px;top:113px;width:525px;height:243px;z-index:1;" align="left">
<img src="<?=base_url();?>assets/farrasindo/images/loginfarrasindo.png" id="Image1" alt="" align="top" border="0" style="width:525px;height:243px;"></div>
    <div id="dvusername">
    <input type="text" name="username" id="username" value="" class="formshort" style="position:absolute;left:470px;top:191px;width:115px;background-color:#000000;color:#FFFFFF;font-family:Calibri;font-weight:bold;font-size:13px;z-index:2">
    </div>
    <div id="dvuserpass">
    <input type="password" name="userpass" id="userpass" value="" class="formshort" style="position:absolute;left:469px;top:220px;width:115px;background-color:#000000;color:#FFFFFF;font-family:Calibri;font-weight:bold;font-size:13px;z-index:3">
    </div>
    <div id="dvbtnlogin">
    <input type="submit" value="Login" style="position:absolute;left:528px;top:247px;width:65px;height:20px;background-color:#FF6820;font-family:Calibri;font-weight:bold;font-size:13px;z-index:4">
    </div>
    </form>
    <div id="wait" style="position:absolute; display:none;left:660px;top:110px;">
    <img src="<?=base_url();?>assets/images/anim_wait.gif" border="0" />
</div>
</div>
<div id="gbr">
		<div style="margin: 10px;">
			<div style="text-align: right;">
				<font size="2px"><b>Close For Login</b></font>
				<a href="javascript:closenewyear()"><img src="<?=base_url();?>assets/farrasindo/images/closebutton.jpg" border="0" width=20px height=20px/><br /></a>
			</div>
			<div>
				<img src="<?=base_url();?>assets/farrasindo/images/HappyNewYear2011.jpg" border="0" />
			</div>
		</div>
	</div>

</body>
