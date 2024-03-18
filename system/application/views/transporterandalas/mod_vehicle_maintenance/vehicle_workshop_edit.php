<script>
	jQuery(document).ready(
		function()
		{
			showclock();
			
		}
	);
	
	function frmedit_onsubmit()
	{
		jQuery("#loader2").show();
		jQuery.post("<?=base_url()?>transporter/mod_vehicle_maintenance/workshop_update", jQuery("#frmedit").serialize(),	
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
		
		<form class="block-content form" name="frmedit" id="frmedit" onsubmit="javascript: return frmedit_onsubmit()">	
		<input type="hidden" name="workshop_id" id="workshop_id" value="<?php if(isset($data->workshop_id)) { echo $data->workshop_id; };?>" />		
		<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<tr>
				<td>Workshop</td>
				<td>:</td>
				<td>
					<input type="text" size="50" name="workshop_name" id="workshop_name" value="<?php if(isset($data->workshop_name)) { echo $data->workshop_name; };?>" />
				</td>
			</tr>
			<tr>
				<td>Telp</td>
				<td>:</td>
				<td><input type="text" name="workshop_telp" id="workshop_telp" value="<?php if(isset($data->workshop_telp)) { echo $data->workshop_telp; };?>" /></td>
			</tr>
			<tr>
				<td>Fax</td>
				<td>:</td>
				<td><input type="text" name="workshop_fax" id="workshop_fax" value="<?php if(isset($data->workshop_fax)) { echo $data->workshop_fax; };?>" /></td>
			</tr>
			<tr>
				<td>Address</td>
				<td>:</td>
				<td><textarea cols="30" rows="3" name="workshop_address" id="workshop_address"><?php if (isset($data->workshop_address)) { echo $data->workshop_address; } ?></textarea></td>
			</tr>
			<tr>
				<td colspan="3">
					<input type="submit" name="submit" id="submit" value="Update" />
					<img id="loader2" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" style="display:none;">
				</td>
			</tr>
		</table>
		</form>
		
