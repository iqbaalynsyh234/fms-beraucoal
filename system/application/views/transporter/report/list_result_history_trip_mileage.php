<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
	<thead>
    	<tr>
	        <th style="text-align:center;" width="3%">No</td>
			<th style="text-align:center;" width="10%">Vehicle</th>
			<th style="text-align:center;" width="7%">Trip No.</th>
			<th style="text-align:center;" width="10%">Start Time</td>
			<th style="text-align:center;" width="10%">End Time</th>					
			<th style="text-align:center;" width="7%">Duration</th>
			<th style="text-align:center;" width="7%">Trip Mileage</th>		
			<th style="text-align:center;" width="7%">Cumulative Mileage</th>
			<th style="text-align:center;" width="18%">Location Start</th>
			<th style="text-align:center;" width="18%">Location End</th>
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
					<td style="text-align:center;" ><?php echo $i+1;?></td>
					<td><?php echo $data[$i]->trip_mileage_vehicle_name." ".$data[$i]->trip_mileage_vehicle_no;?></td>
					<td style="text-align:center;" ><?php echo $data[$i]->trip_mileage_trip_no;?></td>
					<td><?php echo $data[$i]->trip_mileage_start_time;?></td>
					<td><?php echo $data[$i]->trip_mileage_end_time;?></td>
					<td><?php echo $data[$i]->trip_mileage_duration;?></td>
					<td><?php echo $data[$i]->trip_mileage_trip_mileage." "."KM";?></td>
					<td><?php echo $data[$i]->trip_mileage_cummulative_mileage." "."KM";?></td>
					<td><?php echo $data[$i]->trip_mileage_location_start;?></td>
					<td><?php echo $data[$i]->trip_mileage_location_end;?></td>
				</tr>
		<?php
				}
			}
			else
			{
		?>
		<tr>
			<td colspan="10">Data Not Available !</td>
		</tr>
		<?php
			}
		?>
    </tbody>
	<tfoot>
		<tr>
			<td colspan="10">&nbsp;</td>
		</tr>
	</tfoot>
</table>