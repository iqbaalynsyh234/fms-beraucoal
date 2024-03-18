<script>

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

				function resetvalue() {

				}

</script>
<body bgcolor="black" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<center>
<table id="Table_01" width="961" height="600" border="0" cellpadding="0" cellspacing="0">
<form id="frmlogin" onSubmit="javascript: return frmlogin_onsubmit(this)">
	<tr>
		<td>
			<img src="<?php echo base_url();?>assets/transporter/images/Logo.jpg" width="93" height="50" alt=""></td>
		<td>
			<img src="<?php echo base_url();?>assets/transporter/images/Text-Banner.jpg" width="417" height="50" alt=""></td>
		<td colspan="4">
			<img src="<?php echo base_url();?>assets/transporter/images/Banner.jpg" width="450" height="50" alt=""></td>
		<td>
			<img src="<?php echo base_url();?>assets/transporter/images/spacer.gif" width="1" height="50" alt=""></td>
	</tr>
	<tr>
		<td colspan="2">
			<img src="<?php echo base_url();?>assets/transporter/images/Website-Transporter_04.jpg" width="510" height="35" alt=""></td>
		<td colspan="4">
			<img src="<?php echo base_url();?>assets/transporter/images/Img2.jpg" width="450" height="35" alt=""></td>
		<td>
			<img src="<?php echo base_url();?>assets/transporter/images/spacer.gif" width="1" height="35" alt=""></td>
	</tr>
	<tr>
		<td colspan="2" rowspan="9">
			<img src="<?php echo base_url();?>assets/transporter/images/Website-Transporter_06.jpg" width="510" height="271" alt=""></td>
		<td colspan="4">
			<img src="<?php echo base_url();?>assets/transporter/images/Headline-Text.jpg" width="450" height="48" alt=""></td>
		<td>
			<img src="<?php echo base_url();?>assets/transporter/images/spacer.gif" width="1" height="48" alt=""></td>
	</tr>
	<tr>
		<td colspan="4">
			<img src="<?php echo base_url();?>assets/transporter/images/DescriptionText.jpg" width="450" height="38" alt=""></td>
		<td>
			<img src="<?php echo base_url();?>assets/transporter/images/spacer.gif" width="1" height="38" alt=""></td>
	</tr>
	<tr>
		<td colspan="4">
			<img src="<?php echo base_url();?>assets/transporter/images/Website-Transporter_09.jpg" width="450" height="35" alt=""></td>
		<td>
			<img src="<?php echo base_url();?>assets/transporter/images/spacer.gif" width="1" height="35" alt=""></td>
	</tr>
	<tr>
		<td rowspan="6">
			<img src="<?php echo base_url();?>assets/transporter/images/Website-Transporter_10.jpg" width="150" height="150" alt=""></td>
		<td colspan="2">
			<div id="dvusername" style="position:absolute;">
			<input type="text" name="username" id="username" value="" style="width:210px;border-style:none;border-color:transparent;background-color:transparent;color:white;">

			</div>
			<img src="<?php echo base_url();?>assets/transporter/images/Back-Username.jpg" width="215" height="26" alt=""></td>
		<td rowspan="7">
			<img src="<?php echo base_url();?>assets/transporter/images/Website-Transporter_12.jpg" width="85" height="394" alt=""></td>
		<td>
			<img src="<?php echo base_url();?>assets/transporter/images/spacer.gif" width="1" height="26" alt=""></td>
	</tr>
	<tr>
		<td colspan="2">
			<img src="<?php echo base_url();?>assets/transporter/images/Website-Transporter_13.jpg" width="215" height="20" alt=""></td>
		<td>
			<img src="<?php echo base_url();?>assets/transporter/images/spacer.gif" width="1" height="20" alt=""></td>
	</tr>
	<tr>
		<td colspan="2">
			<div id="dvuserpass" style="position:absolute;">
			<input type="password" name="userpass" id="userpass" value="" style="width:210px;border-style:none;border-color:transparent;background-color:transparent;color:white;">

			</div>
			<img src="<?php echo base_url();?>assets/transporter/images/Back-Password.jpg" width="215" height="26" alt=""></td>
		<td>
			<img src="<?php echo base_url();?>assets/transporter/images/spacer.gif" width="1" height="26" alt=""></td>
	</tr>
	<tr>
		<td colspan="2">
			<img src="<?php echo base_url();?>assets/transporter/images/Website-Transporter_15.jpg" width="215" height="8" alt=""></td>
		<td>
			<img src="<?php echo base_url();?>assets/transporter/images/spacer.gif" width="1" height="8" alt=""></td>
	</tr>
	<tr>
		<td rowspan="3">
			<img src="<?php echo base_url();?>assets/transporter/images/Website-Transporter_16.jpg" width="140" height="314" alt="">
			</td>
		<td>
			<div id="dvbtnlogin" style="position:absolute;">
			<input id="submit" name="submit" type="submit" value="" style="width:75px;height:37px;background-color:transparent;color:white;border-color:transparent;">
			</div>
			<img src="<?php echo base_url();?>assets/transporter/images/Login.jpg" width="75" height="37" alt="">
			<div id="dvwait" style="position:absolute;display:none;top:51%;left:62%">
				<img src="<?=base_url();?>assets/transporter/images/loader2.gif" border="0" />
			</div>
			</td>
		<td>
			<img src="<?php echo base_url();?>assets/transporter/images/spacer.gif" width="1" height="37" alt=""></td>
	</tr>
	<tr>
		<td rowspan="2">
			<img src="<?php echo base_url();?>assets/transporter/images/Website-Transporter_18.jpg" width="75" height="277" alt="">

			</td>
		<td>
			<img src="<?php echo base_url();?>assets/transporter/images/spacer.gif" width="1" height="33" alt="">

			</td>
	</tr>
	<tr>
		<td colspan="3">

			<img src="<?php echo base_url();?>assets/transporter/images/Website-Transporter_19.jpg" width="660" height="244" alt=""></td>
		<td>
			<img src="<?php echo base_url();?>assets/transporter/images/spacer.gif" width="1" height="244" alt=""></td>
	</tr>
</table>
<!--<table>
<tr><td>
	<font color="white">All Right Reserved &copy www.lacak-mobil.com - 2012</font>
	</td></tr>
</table>-->
</form>
</center>
</body>
