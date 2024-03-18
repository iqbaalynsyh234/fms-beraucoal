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
</script>

<div id="main_data">
		<form id="frmdist">		
			<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
				<tr>
					<td style="text-align:center" colspan="2">
						<h2>DISTRIBUTOR ( DETAIL )</h2>
					</td>
				</tr>
				<tr>
					<td style="text-align:left">Distributor Code</td>
					<td><?php if (isset($data)) { echo $data->dist_code; } ?></td>
				</tr>
				<tr>
					<td style="text-align:left">Username</td>
					<td><?php if (isset($data)) { echo $data->dist_username; } ?></td>
				</tr>
				<tr>
					<td style="text-align:left">Password</td>
					<td><?php if (isset($data)) { echo $data->dist_password; } ?></td>
				</tr>
				<tr>
					<td style="text-align:left">Name</td>
					<td><?php if (isset($data)) { echo $data->dist_name; } ?></td>
				</tr>
				<tr>
					<td style="text-align:left">Mobile</td>
					<td><?php if (isset($data)) { echo $data->dist_mobile; } ?></td>
				</tr>
				<tr>
					<td style="text-align:left">Email</td>
					<td><?php if (isset($data)) { echo $data->dist_email; } ?></td>
				</tr>
				<tr>
					<td style="text-align:left">WH Coverage</td>
					<td><?php if (isset($data)) { echo $data->dist_wh_coverage; } ?></td>
				</tr>
				<tr>
					<td style="text-align:left">LeadDay Origin</td>
					<td><?php if (isset($data)) { echo $data->dist_leadday_wh_origin; } ?></td>
				</tr>
				<tr>
					<td style="text-align:left">LeadDay WH JKT</td>
					<td><?php if (isset($data)) { echo $data->dist_leadday_wh_jkt; } ?></td>
				</tr>
				<tr>
					<td style="text-align:left">LeadDay WH Medan</td>
					<td><?php if (isset($data)) { echo $data->dist_leadday_wh_medan; } ?></td>
				</tr>
				<tr>
					<td style="text-align:left">LeadDay WH SBY</td>
					<td><?php if (isset($data)) { echo $data->dist_leadday_wh_sby; } ?></td>
				</tr>
				<tr>
					<td style="text-align:left">Customer Type</td>
					<td><?php if (isset($data)) { echo $data->dist_customer_type; } ?></td>
				</tr>
				<tr>
					<td style="text-align:left">Schedule</td>
					<td><?php if (isset($data)) { echo $data->dist_schedule; } ?></td>
				</tr>
				<tr>
					<td style="text-align:left">Ship Zone</td>
					<td><?php if (isset($data)) { echo $data->dist_ship_zone; } ?></td>
				</tr>
				<tr>
					<td style="text-align:left">Schedule Priority</td>
					<td><?php if (isset($data)) { echo $data->dist_schedule_priority; } ?></td>
				</tr>
			</table>
		</form>
</div>