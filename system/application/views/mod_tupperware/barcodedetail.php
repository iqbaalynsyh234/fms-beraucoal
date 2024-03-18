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
</script>
<table width="100%" cellpadding="3" class="tablelist">
	<input type="hidden" name="dbtype" id="dbtype" value="<?php if(isset($data->transporter_barcode_db_type)) { echo $data->transporter_barcode_db_type; } ?>" />
	<tr style="border: 0px;">
		<td width="100" style="border: 0px;">Destination</td>
		<td style="border: 0px;">
			<input type="text" name="booking_destination" id="booking_destination" value="<?php if(isset($data->transporter_barcode_destination)) { echo $data->transporter_barcode_destination; } ?>" />
			<font color="red">*</font>
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>			
	<tr style="border: 0px;">
		<td width="100" style="border: 0px;">Type Armada</td>
		<td style="border: 0px;">
			<select name="booking_armada_type" id="booking_armada_type" >
				<option value="0">--Select Type Armada</option>
				<?php 
				if (isset($typearmada))
				{
					foreach ($typearmada as $ta)
					{
						if (isset($data) && $data->transporter_barcode_fleet_type == $ta->typearmada_name)
						{
							$selected = "selected"; 
						}
						else
						{
							$selected = "";
						}
						echo "<option value='" . $ta->typearmada_id ."' " . $selected . ">" . $ta->typearmada_name. "</option>";
					}
				}
				?>
			</select>
			<font color="red">*</font>
		</td>
	</tr>			
	<tr><td>&nbsp;</td></tr>			
	<tr style="border: 0px;">
		<td width="100" style="border: 0px;">Vehicle</td>
		<td style="border: 0px;">
			<select name="booking_vehicle" id="booking_vehicle">
				<option value="0">--Select Vehicle--</option>
				<?php 
					for ($i=0;$i<count($vehicle);$i++) {
				?>
					<option value="<?php echo $vehicle[$i]->vehicle_device;?>">
						<?php echo $vehicle[$i]->vehicle_name." ".$vehicle[$i]->vehicle_no;?></option>
				<?php } ?>
			</select>
			<font color="red">*</font>
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>			
	<tr style="border: 0px;">
		<td width="100" style="border: 0px;">Driver</td>
		<td style="border: 0px;">
			<select name="booking_driver" id="booking_driver">
				<option value="0">--Select Driver--</option>
					<?php 
						if (isset($driver) && count($driver)>0)
						{
							for ($i=0;$i<count($driver);$i++) {
					?>
							<option value="<?php if (isset($driver[$i]->driver_id)) { echo $driver[$i]->driver_id; } ?>">
					<?php 
							if (isset($driver[$i]->driver_name))
							{
								echo $driver[$i]->driver_name;
							}
					?>
				</option>
					<?php } } ?>
			</select>
			<font color="red">*</font>
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>			
	<tr style="border: 0px;">
		<td width="100" style="border: 0px;">CBM Loading</td>
		<td style="border: 0px;">
			<input type="text" name="booking_cbm_loading" id="booking_cbm_loading" value="<?php if(isset($data->transporter_barcode_fleet_cbm)) { echo $data->transporter_barcode_fleet_cbm; } ?>" />
			<font color="red">*</font>
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>			
	<tr style="border: 0px;">
		<td width="100" style="border: 0px;">Tanggal Masuk Gudang</td>
		<td style="border: 0px;">
			<?php 
				if (isset($data->transporter_barcode_schedule_date))
				{
					$a = date("d-m-Y",strtotime($data->transporter_barcode_schedule_date));
				}
			?>
			<input type="text" name="booking_date_in" id="booking_date_in"  class="date-pick" value="<?php if(isset($a)) { echo $a; } ?>" />
			<font color="red">*</font>
		</td>
	</tr>			
	<tr><td>&nbsp;</td></tr>
	<tr style="border: 0px;">
		<td width="100" style="border: 0px;">Jam Masuk Gudang</td>
		<td style="border: 0px;">
			<select name="booking_time_in" id="booking_time_in">
				<option value="0">--Select Time--</option>
					<?php 
						foreach ($timecontrol as $tc)
						{	
							$b = $tc->time.":00";
							if ($b == $data->transporter_barcode_time)
							{
								$selected = "selected"; 
							}
							else
							{
								$selected = "";
							}
							echo "<option value='" . $tc->time ."' " . $selected . ">" . $tc->time. "</option>";
						}
					?>
			</select>
			<font color="red">*</font>
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr style="border: 0px;">
		<td width="100" style="border: 0px;">Tujuan Gudang</td>
		<td style="border: 0px;">
			<input type="text" name="booking_warehouse" id="booking_warehouse" value="<?php if(isset($data->transporter_barcode_wh)) { echo $data->transporter_barcode_wh; } ?>"/>
			<font color="red">*</font>
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>			
	<!-- Note -->
	<tr style="border: 0px;">
		<td width="100" valign="center" colspan="2">Note <br />
		<textarea name="booking_notes" id="booking_notes" cols="50" rows="5"></textarea></td></tr>
	<tr><td>&nbsp;</td></tr>			
	<tr>
		<td colspan="2"><font color="red">*</font> ) Harus Di Isi</td>
	</tr>			
	<tr><td>&nbsp;</td></tr>
    <tr style="border: 0px;">
		<td style="border: 0px;" colspan="2">
			<input type="submit" name="btnsave" id="btnsave" value=" Save " />
			<input type="button" name="btncancel" id="btncancel" value=" Cancel " onclick="location='<?=base_url()?>transporter/tupperware/booking_id';" />
			<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
		</td>
	</tr>					
</table>