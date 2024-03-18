<br />
<script src="<?php echo base_url();?>assets/js/jsblong/jquery.table2excel.js"></script>
<script>
jQuery(document).ready(
		function()
		{
			jQuery("#export_xcel").click(function() 
			{ 
				window.open('data:application/vnd.ms-excel,' + encodeURIComponent(jQuery('#isexport_xcel').html()));
			});
		}
	);

</script>


<a class="button" href="javascript:void(0);" id="export_xcel">Export to Excel</a>

<div id="isexport_xcel">
<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
	<thead>
    	<tr>
	        <th style="text-align:center;" width="3%">No</td>
			<th style="text-align:center;" width="5%">Vehicle No</th>
			<th style="text-align:center;" width="5%">Vehicle Name</th>
			<th style="text-align:center;" width="7%">Alert Type</th>
			<th style="text-align:center;" width="7%">Alert Time</th>
			<th style="text-align:center;" width="20%">Location</th>
			<th style="text-align:center;" width="7%">Coordinate</th>
		</tr>
    </thead>
	<tbody>
		<?php
			if (count($data)>0)
			{
			
				for ($i=0;$i<count($data);$i++)
				{ 
				
				?>
				<tr>
					<td style="text-align:center;"><?php echo $i+1;?></td>
					<td style="text-align:center;"><?php echo $vehicle->vehicle_no;?></td>
					<td style="text-align:center;"><?php echo $vehicle->vehicle_name;?></td>
					<td style="text-align:center;">
						<?php if($data[$i]->item == "dt") { ?>
							<?php echo "POWER OFF";?>
						<?php } else { ?>
							<?php echo $data[$i]->item;?>
						<?php } ?>
					</td>
					<td style="text-align:center;"><?php echo date("Y-m-d H:i:s",strtotime($data[$i]->datetime));?></td>
					<td>
						<?php 
							$location = $this->gpsmodel->GeoReverse($data[$i]->latitude, $data[$i]->longitude);
							$geofence = $this->gpsmodel->getGeofence_location_other($data[$i]->latitude, $data[$i]->longitude, $vehicle->vehicle_user_id);
						?>
						<font color="red"><?php echo $geofence; ?></font><br />
						<?php echo $location->display_name; ?>
					</td>
					<td style="text-align:center;">
						<a target="_blank" href="http://maps.google.com/maps?q=<?=$data[$i]->latitude.",".$data[$i]->longitude;?>">
						<strong><?=$data[$i]->latitude.",".$data[$i]->longitude;?></strong>
						</a>
					</td>
				</tr>
		<?php
				}
			}else{
				echo "<tr><td colspan='12'>No Data Available</td></tr>";
			}
			?>
    </tbody>
	
	
</table>
</div>