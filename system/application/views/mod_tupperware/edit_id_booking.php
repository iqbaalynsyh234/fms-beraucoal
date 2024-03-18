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
				jQuery("#booking_date_in").datepicker(
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
	
	function frmidbooking_onsubmit()
	{
		jQuery("#loader2").show();
		jQuery.post("<?=base_url()?>transporter/tupperware/saveedit_id_booking", jQuery("#frmidbooking").serialize(),
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
		<form id="frmidbooking" onsubmit="javascript: return frmidbooking_onsubmit(this)">		
		<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
				<input type="hidden" name="id" id="id" value="<?php echo $data->id; ?>" />
				<input type="hidden" name="booking_id_bf" id="booking_id_bf" value="<?php echo $data->booking_id; ?>" />
				<input type="hidden" name="booking_vehicle_bf" id="booking_vehicle_bf" value="<?php echo $data->booking_vehicle; ?>" />
				<input type="hidden" name="booking_driver_bf" id="booking_vehicle_bf" value="<?php echo $data->booking_driver; ?>" />
				<tr>
					<td style="text-align:left" colspan="2"><h2>ID Booking ( EDIT )</h2></td>
				</tr>
				<tr>
					<td style="text-align:right">ID Booking</td>
					<td>
						<input type="text" name="booking_id" id="booking_id" value="<?php if (isset($data)) { echo $data->booking_id; } ?>" />
					</td>
				</tr>
				<tr>
					<td style="text-align:right">Destination</td>
					<td>
						<input type="text" name="booking_destination" id="booking_destination" value="<?php if (isset($data)) { echo $data->booking_destination; } ?>" />
					</td>
				</tr>
				<tr>
					<td style="text-align:right">Type Armada</td>
					<td>
                                                <select name="booking_armada_type" id="booking_armada_type">
                                                    <option value="0" >--Select Type Armada--</option>
							<?php
							foreach($typearmada as $typear)
							{
								if (isset($data) && $data->booking_armada_type == $typear->typearmada_id)
								{
									$selected = "selected"; 
								}
								else
								{
									$selected = "";
								}
								echo "<option value='" . $typear->typearmada_id ."' " . $selected . ">" . $typear->typearmada_name. "</option>";
							}
							?>
                                                </select>
					</td>
				</tr>
				<tr>
					<td style="text-align:right">Vehicle</td>
					<td>
						<select name="booking_vehicle" id="booking_vehicle" >
							<option value="0" >--Select Vehicle--</option>
							<?php
							foreach($vehicle as $v)
							{
								if (isset($data) && $data->booking_vehicle == $v->vehicle_device)
								{
									$selected = "selected"; 
								}
								else
								{
									$selected = "";
								}
								echo "<option value='" . $v->vehicle_device ."' " . $selected . ">" . $v->vehicle_name." ".$v->vehicle_no . "</option>";
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td style="text-align:right">Driver</td>
					<td>
						<select name="booking_driver" id="booking_driver" >
							<option value="0" >--Select Driver--</option>
							<?php
							foreach($driver as $d)
							{
								if (isset($data) && $data->booking_driver == $d->driver_id)
								{
									$selected = "selected"; 
								}
								else
								{
									$selected = "";
								}
								echo "<option value='" . $d->driver_id ."' " . $selected . ">" . $d->driver_name. "</option>";
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td style="text-align:right">CBM Loading</td>
					<td>
						<input type="text" name="booking_cbm_loading" id="booking_cbm_loading" value="<?php if (isset($data)) { echo $data->booking_cbm_loading; } ?>" />
					</td>
				</tr>
				<tr>
					<td style="text-align:right">Tanggal Masuk Gudang</td>
					<td>
						<input type="text" name="booking_date_in" id="booking_date_in" value="<?php if (isset($data)) { echo date("d-m-Y",strtotime($data->booking_date_in)); } ?>" class="date-pick" />
					</td>
				</tr>
				<tr>
					<td style="text-align:right">Jam Masuk Gudang</td>
					<td>
						<select name="booking_time_in" id="booking_time_in" >
							<option value="0" >--Select Time--</option>
							<?php
							foreach($timecontrol as $t)
							{
								if (isset($data) && $data->booking_time_in == $t->time)
								{
									$selected = "selected"; 
								}
								else
								{
									$selected = "";
								}
								echo "<option value='" . $t->time ."' " . $selected . ">" . $t->time. "</option>";
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td style="text-align:right">Tujuan Gudang</td>
					<td>
						<input type="text" name="booking_warehouse" id="booking_warehouse" value="<?php if (isset($data)) { echo $data->booking_warehouse; } ?>" />
					</td>
				</tr>
				<tr>
					<td style="text-align:right">Note</td>
					<td>
						<textarea name="booking_notes" id="booking_notes" cols="40" rows="3" ><?php if (isset($data)) { echo $data->booking_notes; } ?></textarea>
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>
						<input type="submit" name="submit" id="submit" value="Save" />
						<input type="button" name="btn_cancel" id="btn_cancel" value="Cancel" onclick="location='<?=base_url()?>transporter/tupperware/booking_id';" />
						<img id="loader2" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
					</td>
				</tr>
		</table>
		</form>
</div>