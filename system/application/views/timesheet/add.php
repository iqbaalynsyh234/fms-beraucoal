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
	
	function frmtimesheet_onsubmit()
	{
		jQuery("#loader").show();
		jQuery.post("<?=base_url()?>transporter/timesheet/save", jQuery("#frmtimesheet").serialize(),
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
	
</script>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
<?=$navigation;?>
	<div id="main" style="margin: 20px;">
		<div class="block-border">
			<form class="block-content form" id="frmtimesheet" onsubmit="javascript: return frmtimesheet_onsubmit(this)">				
            <h1><?php echo "Add Timesheet"; ?></h1>
			<table width="100%" cellpadding="3" class="tablelist">
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">Route Name</td>
					<td>&nbsp;</td>
					<td style="border: 0px;">
						<select name="route" id="route">
							<option value="0">-- Select Route --</option>
							<?php 
								if (isset($my_route) && $my_route>0)
								{
									for ($i=0;$i<count($my_route);$i++)
									{
							?>
										<option value="<?php echo $my_route[$i]->route_id; ?>"><?php echo $my_route[$i]->route_name; ?></option>
							<?php
									}
								}
							?>
						</select>
					</td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr style="border: 0px;">
					<td valign="top" align="top" style="text-align:top">Timesheet Name</td>
					<td>&nbsp;</td>
					<td>
						<select name="timesheet" id="timesheet">
						<option value="0">--Select Timesheet--</option>
						<?php 
							if (isset($my_geofence) && count($my_geofence)>0)
							{
								for($i=0;$i<count($my_geofence);$i++)
								{
						?>
									<option value="<?php echo $my_geofence[$i];?>">
										<?php echo $my_geofence[$i];?>
									</option>
						<?php
								}
							}
						?>
						</select>
					</td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr style="border: 0px;">
					<td valign="top" align="top" style="text-align:top">Time Plan(In)</td>
					<td>&nbsp;</td>
					<td>
						<select name="timeplan" id="timeplan">
						<option value="0">--Select Time Plan--</option>
						<?php 
							if (isset($timecontrol) && count($timecontrol)>0)
							{
								for($i=0;$i<count($timecontrol);$i++)
								{
						?>
									<option value="<?php echo $timecontrol[$i]->time;?>">
										<?php echo $timecontrol[$i]->time;?>
									</option>
						<?php
								}
							}
						?>
						</select>
					</td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr style="border: 0px;">
					<td valign="top" align="top" style="text-align:top">Time Plan(Out)</td>
					<td>&nbsp;</td>
					<td>
						<select name="timeplan_out" id="timeplan_out">
						<option value="0">--Select Time Plan--</option>
						<?php 
							if (isset($timecontrol) && count($timecontrol)>0)
							{
								for($i=0;$i<count($timecontrol);$i++)
								{
						?>
									<option value="<?php echo $timecontrol[$i]->time;?>">
										<?php echo $timecontrol[$i]->time;?>
									</option>
						<?php
								}
							}
						?>
						</select>
					</td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">Vehicle</td>
					<td>&nbsp;</td>
					<td style="border: 0px;">
						<select name="vehicle" id="vehicle">
							<option value="0">-- Select Vehicle --</option>
							<?php 
								if (isset($my_vehicle) && $my_vehicle>0)
								{
									for ($i=0;$i<count($my_vehicle);$i++)
									{
							?>
										<option value="<?php echo $my_vehicle[$i]->vehicle_device; ?>"><?php echo $my_vehicle[$i]->vehicle_name." ".$my_vehicle[$i]->vehicle_no; ?></option>
							<?php
									}
								}
							?>
						</select>
					</td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">Driver</td>
					<td>&nbsp;</td>
					<td style="border: 0px;">
						<select name="driver" id="driver">
							<option value="0">-- Select Driver --</option>
							<?php 
								if (isset($my_driver) && $my_driver>0)
								{
									for ($i=0;$i<count($my_driver);$i++)
									{
							?>
										<option value="<?php echo $my_driver[$i]->driver_id; ?>"><?php echo $my_driver[$i]->driver_name; ?></option>
							<?php
									}
								}
							?>
						</select>
					</td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">Cycle</td>
					<td>&nbsp;</td>
					<td style="border: 0px;">
						<select name="cycle" id="cycle">
							<option value="0">-- Select Cycle --</option>
							<?php 
								$cycle = 10;
								for($i=1;$i<$cycle;$i++)
								{
							?>
							<option value="<?php echo $i;?>"><?php echo $i;?></option>
							<?
								}
							?>
						</select>
					</td>
				</tr>
				<tr><td>&nbsp;</td></tr>
    			<tr style="border: 0px;">
					<td style="border: 0px;" colspan="3">
						<input class="button" type="submit" name="btnsave" id="btnsave" value=" Save " />
						<input class="button" type="button" name="btncancel" id="btncancel" value=" Cancel " onclick="location='<?=base_url()?>transporter/timesheet';" />
						<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
					</td>
				</tr>					
			</table>
			</form>
		</div>
	</div>
</div>	