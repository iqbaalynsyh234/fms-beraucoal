<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
	<thead>
    	<tr>
	        <th style="text-align:center;" width="3%">No</td>
			<th style="text-align:center;" width="10%">Vehicle</th>
			<!--<th style="text-align:center;" width="7%">Trip No.</th>-->
			<th style="text-align:center;" width="10%">Start Time</td>
			<th style="text-align:center;" width="10%">End Time</th>					
			<th style="text-align:center;" width="7%">Duration</th>
			<th style="text-align:center;" width="7%">Mileage</th>		
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
					<td><?php echo $data[$i]->history_trip_mileage_vehicle_name." ".$data[$i]->history_trip_mileage_vehicle_no;?></td>
					<!--<td style="text-align:center;" ><?php echo $data[$i]->history_trip_mileage_trip_no;?></td>-->
					<td><?php echo $data[$i]->history_trip_mileage_start_time;?></td>
					<td><?php echo $data[$i]->history_trip_mileage_end_time;?></td>
					<td><?php echo $data[$i]->history_trip_mileage_duration;?></td>
					<td><?php echo $data[$i]->history_trip_mileage_trip_mileage." "."KM";?></td>
					<td><?php echo $data[$i]->history_trip_mileage_cummulative_mileage." "."KM";?></td>
					<td><?php echo $data[$i]->history_trip_mileage_location_start;?></td>
					<td><?php echo $data[$i]->history_trip_mileage_location_end;?></td>
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
			$tot_hour = 0;
			$tot_dur = 0;
			if ((isset($data)) && (count($data)>0))
			{
				for ($i=0;$i<count($data);$i++)
				{
					$dur = $data[$i]->history_trip_mileage_duration;
					$tot_dur = $tot_dur + $data[$i]->history_trip_mileage_duration;
				
					$ex = explode(" ",$dur);
					if (isset($ex[1]) && ($ex[1] == "Min"))
					{
						$detik = $ex[0] * 60;
					}
					elseif (isset($ex[1]) && ($ex[1] == "Hour"))
					{
					
						$detik = $ex[0] * 60 * 60;
						if (isset($ex[2]))
						{
							$det = $ex[2] * 60;
							$detik  = $detik + $det;
						}
					}
				
					$tot_hour = $tot_hour + $detik;
				}
			}
			
						
		?>
    </tbody>
	<tfoot>
		<tr>
			<td colspan="4">Hour Meter : </td>
			
			<td colspan="3"><?php 
								if (isset($tot_hour))
									{
										$conval = $tot_hour;
										$seconds = $conval;
										
										// extract hours
										$hours = floor($seconds / (60 * 60));
	 
										// extract minutes
										$divisor_for_minutes = $seconds % (60 * 60);
										$minutes = floor($divisor_for_minutes / 60);
	 
										// extract the remaining seconds
										$divisor_for_seconds = $divisor_for_minutes % 60;
										$seconds = ceil($divisor_for_seconds);
										
										if(isset($hours) && $hours > 0)
										{
											if($hours > 0 && $hours <= 1)
											{
												echo $hours." "."Hour"." ";
											}
											if($hours >= 2)
											{
												echo $hours." "."Hours"." ";
											}
										}
										if(isset($minutes) && $minutes > 0)
										{
											if($minutes > 0 && $minutes <= 1 )
											{
												echo $minutes." "."Minute"." ";
											}
											if($minutes >= 2)
											{
												echo $minutes." "."Minutes"." ";
											}
										}
										 if(isset($seconds) && $seconds > 0)
										{
											echo $seconds." "."Detik"." ";
										}
									}
								

			?></td>
			
			<td colspan="10">&nbsp;</td>
		</tr>
	</tfoot>
</table>