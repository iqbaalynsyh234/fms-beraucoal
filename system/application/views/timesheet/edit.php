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
		jQuery.post("<?=base_url()?>transporter/timesheet/update", jQuery("#frmtimesheet").serialize(),
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
<div id="main_data">
		<form id="frmtimesheet" onsubmit="javascript: return frmtimesheet_onsubmit(this)">	
		<input type="hidden" name="timesheet_id" id="timesheet_id" value="<?php if (isset($data)) { echo $data->timesheet_id; } ?>" />
		<input type="hidden" name="driver_old" id="driver_old" value="<?php if (isset($data)) { echo $data->timesheet_driver; } ?>" />
		<input type="hidden" name="vehicle_old" id="vehicle_old" value="<?php if (isset($data)) { echo $data->timesheet_vehicle; } ?>" />
		<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
                <tr>
					<td style="text-align:left" colspan="2"><h2>EDIT Timesheet</h2></td>
				</tr>
                <tr>
					<td>Route</td>
					<td>:</td>
					<td>
					<select name="route" id="route">
							<option value="0">-- Select Route --</option>
							<?php 
								if (isset($my_route) && $my_route>0)
								{
									foreach($my_route as $myroute)
									{
										if (isset($data) && $data->timesheet_route == $myroute->route_id)
										{
											$selected = "selected"; 
										}
										else
										{
											$selected = "";
										}
										echo "<option value='" . $myroute->route_id ."' " . $selected . ">" . $myroute->route_name . "</option>";
									}
								}
							?>
					</select>
					</td>
                </tr>
				 <tr>
					<td>Timesheet</td>
					<td>:</td>
					<td>
					<select name="timesheet" id="timesheet">
						<option value="0">--Select Timesheet--</option>
						<?php 
							if (isset($my_geofence) && count($my_geofence)>0)
							{
									for($i=0;$i<count($my_geofence);$i++)
									{
										if (isset($data) && $data->timesheet_geo_name == $my_geofence[$i])
										{
											$selected = "selected"; 
										}
										else
										{
											$selected = "";
										}
										echo "<option value='" . $my_geofence[$i] ."' " . $selected . ">" . $my_geofence[$i] . "</option>";
									}
							}
						?>
					</select>
					</td>
                </tr>
				<tr>
					<td>Time Plan ( In )</td>
					<td>:</td>
					<td>
						<select name="timeplan" id="timeplan">
						<option value="0">-- Select Time Plan --</option>
						<?php 
							if (isset($timecontrol) && count($timecontrol)>0)
							{	
								foreach($timecontrol as $timec)
								{
									$timenew = date("H:i",strtotime($data->timesheet_time));
									if ($timenew == $timec->time)
									{
										$selected = "selected"; 
									}
									else
									{
										$selected = "";
									}
									echo "<option value='" . $timec->time ."' " . $selected . ">" . $timec->time . "</option>";
								}
							}
						?>
						</select>
					</td>
				</tr>
				<tr>
					<td>Time Plan ( Out )</td>
					<td>:</td>
					<td>
						<select name="timeplan_out" id="timeplan_out">
						<option value="0">-- Select Time Plan Out --</option>
						<?php 
							if (isset($timecontrol) && count($timecontrol)>0)
							{	
								foreach($timecontrol as $timec)
								{
									$timenew = date("H:i",strtotime($data->timesheet_time_out));
									if ($timenew == $timec->time)
									{
										$selected = "selected"; 
									}
									else
									{
										$selected = "";
									}
									echo "<option value='" . $timec->time ."' " . $selected . ">" . $timec->time . "</option>";
								}
							}
						?>
						</select>
					</td>
				</tr>
				<tr>
					<td>Vehicle</td>
					<td>:</td>
					<td>
						<select name="vehicle" id="vehicle">
						<option value="0">-- NONE --</option>
						<?php 
								if (isset($vehicle) && $vehicle>0)
								{
									foreach($vehicle as $v)
									{
										if (isset($data) && $data->timesheet_vehicle == $v->vehicle_device)
										{
											$selected = "selected"; 
										}
										else
										{
											$selected = "";
										}
										echo "<option value='" . $v->vehicle_device ."' " . $selected . ">" . $v->vehicle_name ." ". $v->vehicle_no . "</option>";
									}
								}
							?>
					</td>
				</tr>
				
				<tr>
					<td>Driver</td>
					<td>:</td>
					<td>
						<select name="driver" id="driver">
						<option value="0">-- NONE --</option>
						<?php 
								if (isset($driver) && $driver>0)
								{
									foreach($driver as $d)
									{
										if (isset($data) && $data->timesheet_driver == $d->driver_id)
										{
											$selected = "selected"; 
										}
										else
										{
											$selected = "";
										}
										echo "<option value='" . $d->driver_id ."' " . $selected . ">" . $d->driver_name . "</option>";
									}
								}
							?>
					</td>
				</tr>
				
				<tr>
					<td>Cycle</td>
					<td>:</td>
					<td>
						<select name="cycle" id="cycle">
						<?php 
							$cycle = 10;
							for($x=1;$x<$cycle;$x++)
							{
								if (isset($data) && $data->timesheet_cycle == $x)
								{
									$selected = "selected"; 
								}
								else
								{
									$selected = "";
								}
								echo "<option value='" . $x ."' " . $selected . ">" . $x . "</option>";
							}
						?>
					</td>
				</tr>
				
                <tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>
						<input type="submit" name="submit" id="submit" value="Save" />
						<input type="button" name="btn_cancel" id="btn_cancel" value="Cancel" onclick="location='<?=base_url()?>transporter/timesheet';" />
						<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
					</td>
				</tr>
		</table>
		</form>
</div>