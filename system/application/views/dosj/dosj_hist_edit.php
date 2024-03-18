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
				jQuery("#dosj_delivered_date").datepicker(
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
	
	function frmdosj_onsubmit()
	{
		jQuery("#loader").show();
		jQuery.post("<?=base_url()?>transporter/dosj/saveedit_hist_do", jQuery("#frmdosj").serialize(),
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
		<form id="frmdosj" onsubmit="javascript: return frmdosj_onsubmit(this)">		
		<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
				<input type="hidden" name="do_delivered_id" id="do_delivered_id" value="<?php echo $data->do_delivered_id; ?>" />
				<input type="hidden" name="do_delivered_do_number" id="do_delivered_do_number" value="<?php echo $data->do_delivered_do_number; ?>" />
				<input type="hidden" name="quantity_awal" id="quantity_awal" value="<?php echo $data->do_delivered_quantity; ?>" />
				<input type="hidden" name="total_quantity" id="total_quantity" value="<?php echo $data->dosj_item_quantity; ?>" />
				
				<tr>
					<td style="text-align:left" colspan="2">
						<h2>EDIT <?php if (isset($data)) { echo $data->do_delivered_do_type; } ?> 
							- Number ( <?php if (isset($data)) { echo $data->do_delivered_do_number; } ?> )
						</h2>
					</td>
				</tr>
				<tr>
					<td style="text-align:right">Vehicle</td>
					<td>
						<select name="do_delivered_vehicle" id="do_delivered_vehicle" >
						<?php
							foreach($vehicle as $v)
							{
								if (isset($data) && $data->do_delivered_vehicle == $v->vehicle_device)
								{
									$selected = "selected"; 
								}
								else
								{
									$selected = "";
								}
								echo "<option value='" . $v->vehicle_device ."' " . $selected . ">" . $v->vehicle_name." ".$v->vehicle_no . "L</option>";
							}
						?>
						</select>
					</td>
				</tr>
				<tr>
					<td style="text-align:right">Driver</td>
					<td>
						<select name="do_delivered_driver" id="do_delivered_driver" >
						<?php
							foreach($driver as $d)
							{
								if (isset($data) && $data->do_delivered_driver == $d->driver_id)
								{
									$selected = "selected"; 
								}
								else
								{
									$selected = "";
								}
								echo "<option value='" . $d->driver_id ."' " . $selected . ">" . $d->driver_name . "</option>";
							}
						?>
						</select>
					</td>
				</tr>
				<!--
				<tr>
					<td style="text-align:right">Total Quantity</td>
					<td><input type="text" size="7" name="do_delivered_quantity" id="do_delivered_quantity" value="<?php if (isset($data)) { echo $data->do_delivered_quantity; } ?>" /></td>
				</tr>
				-->
				<tr>
					<td style="text-align:right">Cost</td>
					<td>
						<select name="do_delivered_cost" id="do_delivered_cost" >
						<?php
							foreach($cost as $c)
							{
								if (isset($data) && $data->do_delivered_cost == $c->cost_id)
								{
									$selected = "selected"; 
								}
								else
								{
									$selected = "";
								}
								echo "<option value='" . $c->cost_id ."' " . $selected . ">" . $c->destination_name."-".$c->cost_vehicle_type ."</option>";
							}
						?>						
						</select>
					</td>
				</tr>
				<tr>
					<td style="text-align:right">Ship Date</td>
					<?php 
						$sh_date = date("d-m-Y", strtotime($data->do_delivered_date));
					?>
					<td>
						<input type="text" class="date-pick" name="dosj_delivered_date" id="dosj_delivered_date" value="<?php if (isset($data)) { echo $sh_date; } ?>" />
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>
						<input type="submit" name="submit" id="submit" value="Save" />
						<input type="button" name="btn_cancel" id="btn_cancel" value="Cancel" onclick="location='<?=base_url()?>transporter/dosj';" />
						<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
					</td>
				</tr>
		</table>
		</form>
</div>