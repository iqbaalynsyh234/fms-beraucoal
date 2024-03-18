<script>
	jQuery(document).ready(
		function()
		{
			showclock();
			
		}
	);
	
	function frmedit_onsubmit()
	{
		jQuery("#loader").show();
		jQuery.post("<?=base_url()?>transporter/mod_vehicle_maintenance/mechanic_update", jQuery("#frmedit").serialize(),	
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
		
		<form class="block-content form" name="frmedit" id="frmedit" method="post" onsubmit="javascript: return frmedit_onsubmit()">	
		<input type="hidden" name="mechanic_id" id="mechanic_id" value="<?php if(isset($row->mechanic_id)) { echo $row->mechanic_id; };?>" />		
		<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<tr>
				<td>Name</td>
				<td>:</td>
				<td><input type="text" name="mechanic_name" id="mechanic_name" value="<?php if (isset($row->mechanic_name)) { echo $row->mechanic_name; };?>" /></td>
			</tr>
			<tr>
				<td>Phone</td>
				<td>:</td>
				<td><input type="text" name="mechanic_phone" id="mechanic_phone" value="<?php if (isset($row->mechanic_phone)) { echo $row->mechanic_phone; };?>" onKeyPress="return numbersonly(this, event)"/></td>
			</tr>
			<tr>
				<td>Mobile</td>
				<td>:</td>
				<td><input type="text" name="mechanic_mobile" id="mechanic_mobile" value="<?php if (isset($row->mechanic_mobile)) { echo $row->mechanic_mobile; };?>" onKeyPress="return numbersonly(this, event)" /></td>
			</tr>
			<tr>
				<td>Fax</td>
				<td>:</td>
				<td><input type="text" name="mechanic_fax" id="mechanic_fax" value="<?php if (isset($row->mechanic_fax)) { echo $row->mechanic_fax; };?>" onKeyPress="return numbersonly(this, event)"/></td>
			</tr>
			<tr>
				<td>Address</td>
				<td>:</td>
				<td>
					<textarea cols="40" rows="3" name="mechanic_address" id="mechanic_address" ><?php if (isset($row->mechanic_address)) { echo $row->mechanic_address; };?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="3">
					<input type="submit" name="submit" id="submit" value="Update" />
					<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" style="display:none;">
				</td>
			</tr>
		</table>
		</form>
		
