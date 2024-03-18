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
	
	function frmcost_onsubmit()
	{
		jQuery("#loader").show();
		jQuery.post("<?=base_url()?>transporter/dosj_all/saveedit_cost", jQuery("#frmcost").serialize(),
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
<div id="main_data">
		<form id="frmcost" onsubmit="javascript: return frmcost_onsubmit(this)">		
		<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
				<input type="hidden" name="cost_id" id="cost_id" value="<?php echo $data->cost_id; ?>" />
				<tr>
					<td style="text-align:left" colspan="2"><h2>EDIT COST</h2></td>
				</tr>
				<tr>
					<td style="text-align:right">Destination</td>
					<td>
						<select name="cost_destination" id="cost_destination" >
						<?php
							foreach($destination as $dest)
							{
								if (isset($data) && $data->cost_destination == $dest->destination_id)
								{
									$selected = "selected"; 
								}
								else
								{
									$selected = "";
								}
								echo "<option value='" . $dest->destination_id ."' " . $selected . ">" . $dest->destination_name . "</option>";
							}
						?>
						</select>
					</td>
				</tr>
				<tr>
					<td style="text-align:right">Vehicle Type</td>
					<td>
						<select name="cost_vehicle_type" id="cost_vehicle_type" >
						<?php 
							if (isset($data->cost_vehicle_type))
							{
								if ($data->cost_vehicle_type == "COLT DIESEL")
								{
						?>
								<option value="COLT DIESEL">COLT DIESEL</option>
								<option value="FUSO">FUSO</option>
						<?php	
								}
								else
								{
						?>
								<option value="FUSO">FUSO</option>
								<option value="COLT DIESEL">COLT DIESEL</option>
						<?php
								}
							}
						?>
					</td>
				</tr>
				<tr>
					<td style="text-align:right">Cost</td>
					<td>
						Rp. <input size="10" type="text" name="cost" id="cost" value="<?php if (isset($data)) { echo $data->cost; } ?>" onKeyPress="return numbersonly(this, event)" /><br />
						<small>Ex: xxxxxx ( Otomatis akan dikonversi ke xxx,xxx )</small>
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>
						<input type="submit" name="submit" id="submit" value="Save" />
						<input type="button" name="btn_cancel" id="btn_cancel" value="Cancel" onclick="javascript:jQuery('#dialog').dialog('close');" />
						<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
					</td>
				</tr>
		</table>
		</form>
</div>