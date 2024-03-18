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
<table width="100%" id="tblresult" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
	<thead>
		<tr>
			<th rowspan="2" style="text-align:center;">NO</td>
			<th rowspan="2" style="text-align:center;">PLATE NUMBER</td>
			<th rowspan="2" style="text-align:center;">JENIS KENDARAAN</td>
			<th colspan="2" style="text-align:center;">TRAVEL TIME</td>
			<th rowspan="2" style="text-align:center;">TOTAL DRIVING TIME</td>
			<th colspan="2" style="text-align:center;">TRAVEL MOVETIME</td>
			<th colspan="2" style="text-align:center;">ODOMETER</td>
			<th rowspan="2" style="text-align:center;">DISTANCE</td>
			<th rowspan="2" style="text-align:center;">TOTAL DISTANCE WEEK</td>
			<th rowspan="2" style="text-align:center;">TOTAL DISTANCE MONTH</td>
		</tr>
		<tr>
			<th style="text-align:center;">Start Date Time</td>
			<th style="text-align:center;">End Date Time</td>
			<th style="text-align:center;">Start Location</td>
			<th style="text-align:center;">End Location</td>
			<th style="text-align:center;">Early</td>
			<th style="text-align:center;">End</td>
		</tr>	
	</thead>
	<tbody>
		<?php 
			if(isset($vehicle))
			{
				for($i=0;$i<count($vehicle);$i++)
				{
		?>
		<tr>
			<td><?php echo $i+1; ?></td>
			<td><?php echo $vehicle[$i]->vehicle_no; ?></td>
			<td><?php echo $vehicle[$i]->vehicle_name; ?></td>
			<td style="text-align:center;">
				<?php 
					if(isset($vehicle[$i]->sdt) && $vehicle[$i]->sdt != "1970-01-01 07:00:00" && $vehicle[$i]->sdt != "")
					{
						echo date("d-m-Y H:i:s",strtotime($vehicle[$i]->sdt)); 
					}
					else
					{
						echo "-";
					}
				?>
			</td>
			<td style="text-align:center;">
				<?php 
					
					if(isset($vehicle[$i]->edt) && $vehicle[$i]->edt != "1970-01-01 07:00:00" && $vehicle[$i]->edt != "")
					{
						echo date("d-m-Y H:i:s",strtotime($vehicle[$i]->edt)); 
					}
					else
					{
						echo "-";
					}
				?>
			</td>
			<td style="text-align:center;"><?php echo $vehicle[$i]->total_time; ?></td>
			<td style="text-align:left;"><?php echo $vehicle[$i]->slocation; ?></td>
			<td style="text-align:left;"><?php echo $vehicle[$i]->elocation; ?></td>
			<td style="text-align:center;"><?php echo $vehicle[$i]->sodo; ?></td>
			<td style="text-align:center;"><?php echo $vehicle[$i]->eodo; ?></td>
			<td style="text-align:center;"><?php echo $vehicle[$i]->daily_distance; ?></td>
			<td style="text-align:center;"><?php echo $vehicle[$i]->odo_week; ?></td>
			<td style="text-align:center;"><?php echo $vehicle[$i]->odo_month; ?></td>
		</tr>
		<?php
				}
			}
		?>
	</tbody>
</table>
</div>
