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
		jQuery("#loader2").show();
		jQuery.post("<?=base_url()?>transporter/tupperware/saveedit_distributor", jQuery("#frmdist").serialize(),
			function(r)
			{
				jQuery("#loader2").hide();
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
<div id="main_data">
		<form id="frmdist" onsubmit="javascript: return frmdist_onsubmit(this)">		
		<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
				<input type="hidden" name="id" id="id" value="<?php echo $data->dist_id; ?>" />
				<input type="hidden" name="dist_code_bf" id="dist_code_bf" value="<?php echo $data->dist_code; ?>" />
				<input type="hidden" name="dist_username_bf" id="dist_username_bf" value="<?php echo $data->dist_username; ?>" />
				<tr>
					<td style="text-align:left" colspan="2"><h2>Distributor ( EDIT )</h2></td>
				</tr>
				<tr>
					<td style="text-align:right">Distributor Code</td>
					<td>
						<input type="text" name="dist_code" id="dist_code" onKeyPress="return numbersonly(this, event)"  value="<?php if (isset($data)) { echo $data->dist_code; } ?>" />
					</td>
				</tr>
				<tr>
					<td style="text-align:right">Username</td>
					<td>
						<input type="text" name="dist_username" id="dist_username" value="<?php if (isset($data)) { echo $data->dist_username; } ?>" />
					</td>
				</tr>
				<tr>
					<td style="text-align:right">Password</td>
					<td>
						<input type="text" name="dist_password" id="dist_password" value="<?php if (isset($data)) { echo $data->dist_password; } ?>" />
					</td>
				</tr>
				<tr>
					<td style="text-align:right">Name</td>
					<td>
						<input type="text" size="50" name="dist_name" id="dist_name" value="<?php if (isset($data)) { echo $data->dist_name; } ?>" />
					</td>
				</tr>
				<tr>
					<td style="text-align:right">Mobile</td>
					<td>
						<input type="text" size="50" name="dist_mobile" id="dist_mobile" value="<?php if (isset($data)) { echo $data->dist_mobile; } ?>" />
					</td>
				</tr>
				<tr>
					<td style="text-align:right">Email</td>
					<td>
						<input type="text" size="50" name="dist_email" id="dist_email" value="<?php if (isset($data)) { echo $data->dist_email; } ?>" />
					</td>
				</tr>
				<tr>
					<td style="text-align:right">WH Coverage</td>
					<td>
						<input type="text" size="10" name="dist_wh_coverage" id="dist_wh_coverage" value="<?php if (isset($data)) { echo $data->dist_wh_coverage; } ?>" />
					</td>
				</tr>
				<tr>
					<td style="text-align:right">LeadDay WH Origin</td>
					<td>
						<input type="text" size="10" name="dist_leadday_wh_origin" id="dist_leadday_wh_origin" value="<?php if (isset($data)) { echo $data->dist_leadday_wh_origin; } ?>" />
					</td>
				</tr>
				<tr>
					<td style="text-align:right">LeadDay WH JKT</td>
					<td>
						<input type="text" size="10" name="dist_leadday_wh_jkt" id="dist_leadday_wh_jkt" value="<?php if (isset($data)) { echo $data->dist_leadday_wh_jkt; } ?>" />
					</td>
				</tr>
				<tr>
					<td style="text-align:right">LeadDay WH Medan</td>
					<td>
						<input type="text" size="10" name="dist_leadday_wh_medan" id="dist_leadday_wh_medan" value="<?php if (isset($data)) { echo $data->dist_leadday_wh_medan; } ?>" />
					</td>
				</tr>
				<tr>
					<td style="text-align:right">LeadDay WH SBY</td>
					<td>
						<input type="text" size="10" name="dist_leadday_wh_sby" id="dist_leadday_wh_sby" value="<?php if (isset($data)) { echo $data->dist_leadday_wh_sby; } ?>" />
					</td>
				</tr>
				<tr>
					<td style="text-align:right">Customer Type</td>
					<td>
						<input type="text" size="10" name="dist_customer_type" id="dist_customer_type" value="<?php if (isset($data)) { echo $data->dist_customer_type; } ?>" />
					</td>
				</tr>
				<tr>
					<td style="text-align:right">Schedule</td>
					<td>
						<input type="text" size="10" name="dist_schedule" id="dist_schedule" value="<?php if (isset($data)) { echo $data->dist_schedule; } ?>" />
					</td>
				</tr>
				<tr>
					<td style="text-align:right">Ship Zone</td>
					<td>
						<input type="text" size="10" name="dist_ship_zone" id="dist_ship_zone" value="<?php if (isset($data)) { echo $data->dist_ship_zone; } ?>" />
					</td>
				</tr>
				<tr>
					<td style="text-align:right">Schedule Priority</td>
					<td>
						<input type="text" size="10" name="dist_schedule_priority" id="dist_schedule_priority" value="<?php if (isset($data)) { echo $data->dist_schedule_priority; } ?>" />
					</td>
				</tr>
				
				<tr>
					<td>&nbsp;</td>
					<td>
						<input type="submit" name="submit" id="submit" value="Save" />
						<input type="button" name="btn_cancel" id="btn_cancel" value="Cancel" onclick="location='<?=base_url()?>transporter/tupperware/mn_distributor';" />
						<img id="loader2" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
					</td>
				</tr>
		</table>
		</form>
</div>