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
			jQuery("#service_date").datepicker(
				{
							dateFormat: 'dd-mm-yy'
						, 	showOn: 'button'
						, 	buttonImage: '<?=base_url()?>assets/images/calendar.gif'
						, 	buttonImageOnly: true
						,	beforeShow: 
								function() 
								{	
									jQuery('#ui-datepicker-div').maxZIndex();
								}
				}
				);	
			showclock();
		}
	);
	
	function frmadd_onsubmit()
	{
		jQuery("#loader").show();
		jQuery.post("<?=base_url()?>transporter/mod_vehicle_maintenance/save_service", jQuery("#frmadd").serialize(),
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
			<form class="block-content form" id="frmadd" onsubmit="javascript: return frmadd_onsubmit(this)">				
			<h1><?php echo "Add Service"; ?></h1>
			
			<table width="100%" cellpadding="3" class="tablelist">
				<tr>
					<td>Vehicle</td>
					<td>
					<select name="service_mobil" id="service_mobil" >
						<option value="0" >--Select Vehicle--</option>
						<?php 
							if (isset($vehicle) && count($vehicle)>0)
							{
								for ($i=0;$i<count($vehicle);$i++)
								{
						?>
								<option value="<?php echo $vehicle[$i]->mobil_id;?>" />
								<?php echo $vehicle[$i]->mobil_name." ".$vehicle[$i]->mobil_no;?>
								</option>
						<?php
								}
							}
						?>
					</select>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr>
					<td>Driver</td>
					<td>
					<select name="service_driver" id="service_driver" >
						<option value="0" >--Select Driver--</option>
						<?php 
							if (isset($driver) && count($driver)>0)
							{
								for ($i=0;$i<count($driver);$i++)
								{
						?>
								<option value="<?php echo $driver[$i]->driver_id;?>" />
								<?php echo $driver[$i]->driver_name;?>
								</option>
						<?php
								}
							}
						?>
					</select>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr>
					<td>Workshop</td>
					<td>
					<select name="service_workshop" id="service_workshop" >
						<option value="0" >--Select Workshop--</option>
						<?php 
							if (isset($workshop) && count($workshop)>0)
							{
								for ($i=0;$i<count($workshop);$i++)
								{
						?>
								<option value="<?php echo $workshop[$i]->workshop_id;?>" />
								<?php echo $workshop[$i]->workshop_name;?>
								</option>
						<?php
								}
							}
						?>
					</select>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				
				<tr>
					<td>Mechanic</td>
					<td>
					<select name="service_mechanic" id="service_mechanic" >
						<option value="0" >--Select Mechanic--</option>
						<?php 
							if (isset($mechanic) && count($mechanic)>0)
							{
								for ($i=0;$i<count($mechanic);$i++)
								{
						?>
								<option value="<?php if  (isset($mechanic[$i])) { echo $mechanic[$i]->mechanic_id; } ?>" />
								<?php if (isset($mechanic[$i])) { echo $mechanic[$i]->mechanic_name; } ?>
								</option>
						<?php
								}
							}
						?>
					</select>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">Service Date</td>
					<td style="border: 0px;">
						<input size="10" maxlength="10" type="text" name="service_date" id="service_date" class="date-pick" />
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				
				<tr>
					<td>Service Type</td>
					<td>
					<select name="service_type" id="service_type" >
						<option value="0" >--Select Type--</option>
						<?php 
							
							if (isset($service_model) && count($service_model)>0)
							{
								for ($i=0;$i<count($service_model);$i++)
								{
						?>
								<option value="<?php echo $service_model[$i]->service_model_id;?>" />
								<?php echo $service_model[$i]->service_model;?>
								</option>
						<?php
								}
							}
						?>
					</select>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">Invoice No.</td>
					<td style="border: 0px;">
						<input size="10" maxlength="10" type="text" name="service_invoice" id="service_invoice" />
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">Service Cost</td>
					<td style="border: 0px;">
						<input size="10" maxlength="10" type="text" name="service_cost" id="service_cost" />
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				
				<tr>
					<td colspan="2" >Note<br />
					<textarea cols="30" rows="3" name="service_note" id="service_note" ></textarea></td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr style="border: 0px;">
					<td style="border: 0px;" colspan="2">
						<input type="submit" name="btnsave" id="btnsave" value=" Save " />
						<input type="button" name="btncancel" id="btncancel" value=" Cancel " onclick="location='<?=base_url()?>transporter/mod_vehicle_maintenance/service';" />
						<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
					</td>
				</tr>		
			</table>
			</form>
		</div>
	</div>
</div>
			
