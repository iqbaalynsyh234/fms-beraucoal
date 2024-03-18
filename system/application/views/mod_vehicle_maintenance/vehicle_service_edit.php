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
		jQuery.post("<?=base_url()?>transporter/mod_vehicle_maintenance/service_update", jQuery("#frmedit").serialize(),	
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
		<input type="hidden" name="service_id" id="service_id" value="<?php if(isset($data->service_id)) { echo $data->service_id; };?>" />		
		<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<tr>
				<td>Vehicle</td>
				<td>:</td>
				<td>
					<select name="service_mobil" id="service_mobil" >
						<?php
							foreach($vehicle as $v)
							{
								if (isset($data) && $data->service_mobil == $v->mobil_id)
								{
									$selected = "selected"; 
								}
								else
								{
									$selected = "";
								}
								echo "<option value='" . $v->mobil_id ."' " . $selected . ">" . $v->mobil_name." ".$v->mobil_no . "</option>";
							}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Driver</td>
				<td>:</td>
				<td>
					<select name="service_driver" id="service_driver" >
						<?php
							foreach($driver as $v)
							{
								if (isset($data) && $data->service_driver == $v->driver_id)
								{
									$selected = "selected"; 
								}
								else
								{
									$selected = "";
								}
								echo "<option value='" . $v->driver_id ."' " . $selected . ">" . $v->driver_name."</option>";
							}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Workshop</td>
				<td>:</td>
				<td>
					<select name="service_workshop" id="service_workshop" >
						<option value="0" >--Select Workshop--</option>
						<?php	
							foreach($workshop as $v)
							{
								if (isset($data) && $data->service_workshop == $v->workshop_id)
								{
									$selected = "selected"; 
								}
								else
								{
									$selected = "";
								}
								echo "<option value='" . $v->workshop_id ."' " . $selected . ">" . $v->workshop_name."</option>";
							}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Mechanic</td>
				<td>:</td>
				<td>
					<select name="service_mechanic" id="service_mechanic" >
						<option value="0" >--Select Mechanic--</option>
						<?php	
							foreach($mechanic as $v)
							{
								if (isset($data) && $data->service_mechanic == $v->mechanic_id)
								{
									$selected = "selected"; 
								}
								else
								{
									$selected = "";
								}
								echo "<option value='" . $v->mechanic_id ."' " . $selected . ">" . $v->mechanic_name."</option>";
							}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Service Date</td>
				<td>:</td>
				<td>
					<?php 
						if (isset($data->service_date))
						{
							$dt = $data->service_date;
							$v = date("d-m-Y",strtotime($dt));
						}
					?>
					<input type="text" name="service_date" id="service_date" value="<?php if (isset($v)) { echo $v; } ?>" />
				</td>
			</tr>
			<tr>
				<td>Service Type</td>
				<td>:</td>
				<td>
					<select name="service_type" id="service_type" >
						<option value="0" >--Service Type--</option>
						<?php	
							foreach($service_model as $v)
							{
								if (isset($data) && $data->service_type == $v->service_model_id)
								{
									$selected = "selected"; 
								}
								else
								{
									$selected = "";
								}
								echo "<option value='" . $v->service_model_id ."' " . $selected . ">" . $v->service_model."</option>";
							}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Invoice</td>
				<td>:</td>
				<td><input type="text" name="service_invoice" id="service_invoice" value="<?php if (isset($data->service_invoice)) { echo $data->service_invoice; } ?>" /></td>
			</tr>
			<tr>
				<td>Cost</td>
				<td>:</td>
				<td><input type="text" name="service_cost" id="service_cost" value="<?php if (isset($data->service_cost)) { echo $data->service_cost; } ?>" /></td>
			</tr>
			<tr>
				<td>Notes</td>
				<td>:</td>
				<td><textarea cols="30" rows="3" name="service_note" id="service_note"><?php if (isset($data->service_note)) { echo $data->service_note; } ?></textarea></td>
			</tr>
			<tr>
				<td colspan="3">
					<input type="submit" name="submit" id="submit" value="Update" />
					<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" style="display:none;">
				</td>
			</tr>
		</table>
		</form>
		
