<script>
	jQuery.maxZIndex = jQuery.fn.maxZIndex = function(opt) {
	    var def = { inc: 10, group: "*" };
	    jQuery.extend(def, opt);
	    var zmax = 0;
	    jQuery(def.group).each(function() {
	        var cur = parseInt(jQuery(this).css('z-index'));
	        zmax = cur > zmax ? cur : zmax;
	    });
	    if (!this.jquery)
	        return zmax;
	
	    return this.each(function() {
	        zmax += def.inc;
	        jQuery(this).css("z-index", zmax);
	    });
	}
	
	jQuery(document).ready(
		function()
		{
				showclock();
		}
	);
	
	function frmdist_onsubmit()
	{
		jQuery("#loader").show();
		jQuery.post("<?=base_url()?>transporter/tupperware/save_distributor", jQuery("#frmdist").serialize(),
			function(r)
			{
				jQuery("#loader").hide();
				if (r.error)
				{
					alert(r.message);
					return false;
				}
				
				alert(r.message);
				location = r.redirect;
			}
			, "json"
		);
		return false;
	}
	
	function numbersonly(myfield, e, dec)
	{
		var key;
		var keychar;
		
		if (window.event)
		key = window.event.keyCode;
		else if (e)
		key = e.which;
		else
		return true;
	
		keychar = String.fromCharCode(key);

		// control keys
		if ((key==null) || (key==0) || (key==8) || 
		(key==9) || (key==13) || (key==27) )
		return true;

		// numbers
		else if ((("0123456789").indexOf(keychar) > -1))
		return true;

		// decimal point jump
		else if (dec && (keychar == "."))
		{
			myfield.form.elements[dec].focus();
			return false;
		}
		else
		return false;
	}
		
</script>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
<?=$navigation;?>
	<div id="main" style="margin: 20px;">
		<div class="block-border">
			<form class="block-content form" id="frmdist" onsubmit="javascript: return frmdist_onsubmit(this)">				
            <h1><?php echo "Add Distributor"; ?></h1>
			
			<table width="100%" cellpadding="3" class="tablelist">
			
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">
						Distributor Code
					</td>
					<td style="border: 0px;">
						<input type="text" name="dist_code" id="dist_code" onKeyPress="return numbersonly(this, event)" />
						<font color="red">*</font>
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">
						Username
					</td>
					<td style="border: 0px;">
						<input type="text" name="dist_username" id="dist_username" />
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">
						Password
					</td>
					<td style="border: 0px;">
						<input type="text" name="dist_password" id="dist_password" />
					</td>
				</tr>
				
				
                <tr><td>&nbsp;</td></tr>
				
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">
						Name
					</td>
					<td style="border: 0px;">
						<input type="text" name="dist_name" id="dist_name" />
						<font color="red">*</font>
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">
						Mobile
					</td>
					<td style="border: 0px;">
						<input type="text" name="dist_mobile" id="dist_mobile" />
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">
						Email
					</td>
					<td style="border: 0px;">
						<input type="text" name="dist_email" id="dist_email" />
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">
						WH Coverage
					</td>
					<td style="border: 0px;">
						<input type="text" name="dist_wh_coverage" id="dist_wh_coverage" />
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">
						LeadDay WH Origin
					</td>
					<td style="border: 0px;">
						<input type="text" name="dist_leadday_wh_origin" id="dist_leadday_wh_origin" />
					</td>
				</tr>
				
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">
						LeadDay WH Jakarta
					</td>
					<td style="border: 0px;">
						<input type="text" name="dist_leadday_wh_jkt" id="dist_leadday_wh_jkt" />
					</td>
				</tr>
				
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">
						LeadDay WH Medan
					</td>
					<td style="border: 0px;">
						<input type="text" name="dist_leadday_wh_medan" id="dist_leadday_wh_medan" />
					</td>
				</tr>
				
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">
						LeadDay WH SBY
					</td>
					<td style="border: 0px;">
						<input type="text" name="dist_leadday_wh_sby" id="dist_leadday_wh_sby" />
					</td>
				</tr>
				
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">
						Customer Type
					</td>
					<td style="border: 0px;">
						<input type="text" name="dist_customer_type" id="dist_customer_type" />
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">
						Schedule
					</td>
					<td style="border: 0px;">
						<input type="text" name="dist_schedule" id="dist_schedule" />
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">
						Ship Zone
					</td>
					<td style="border: 0px;">
						<input type="text" name="dist_ship_zone" id="dist_ship_zone" />
					</td>
				</tr>

				<tr><td>&nbsp;</td></tr>
				
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">
						Schedule Priority
					</td>
					<td style="border: 0px;">
						<input type="text" name="dist_schedule_priority" id="dist_schedule_priority" />
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				
    			<tr style="border: 0px;">
						<td style="border: 0px;" colspan="2">
								<input type="submit" name="btnsave" id="btnsave" value=" Save " />
								<input type="button" name="btncancel" id="btncancel" value=" Cancel " onclick="location='<?=base_url()?>transporter/tupperware/mn_distributor';" />
								<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
						</td>
				</tr>					
				</table>
			</form>
		</div>
	</div>
</div>
			

			