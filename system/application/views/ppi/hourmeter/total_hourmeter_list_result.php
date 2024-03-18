		
		<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<thead>
				<tr>
					<th width="1%">No.</td>
					<th width="10%" valign="top" style="text-align:center;">Vehicle No</th>
					<th width="10%" valign="top" style="text-align:center;">Vehicle Name</th>
					<!--<th width="8%" valign="top" style="text-align:center;">Data Hourmeter</th>
					<th width="8%" valign="top" style="text-align:center;">GPS Hourmeter</th>-->
					<th width="8%" valign="top" style="text-align:center;">Total Hourmeter</th>
					<th width="8%" valign="top" style="text-align:center;">Last Update</th>
				</tr>
			</thead>
			<tbody>
			<?php
			if(count($data) > 0){
			for($i=0; $i < count($data); $i++)
			{
			?>
				<tr <?=($i%2) ? "class='odd'" : "";?>>
					<td valign="top"><?=$i+1+$offset?></td>
					<td valign="top" style="text-align:left;">
						<?=$data[$i]->data_hm_daily_vehicle_no; ?>
					</td>
					<td valign="top" style="text-align:left;">
						<?=$data[$i]->data_hm_daily_vehicle_name; ?>
					</td>
					<!--<td valign="top" style="text-align:center;">
						<?=$data[$i]->data_hm_value; ?>
					</td>
					<td valign="top" style="text-align:center;">
						<?=$data[$i]->data_hm_daily_string_cum; ?>
					</td>-->
					<td valign="top" style="text-align:left;">
					<?php	
						$tot_sec = 0;
						$dur_hm = $data[$i]->data_hm_value;
						$hm_sec = $dur_hm * 3600;
						$cum_sec = $data[$i]->data_hm_daily_cum;
						$tot_sec = $cum_sec + $hm_sec; 
						//print_r($hm_sec." + ".$cum_sec." = ".$tot_sec);exit();
						
						$conval = $tot_sec;
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
					?>
					</td>
					<td valign="top" style="text-align:center;">
						<?=date("d-m-Y",strtotime($data[$i]->data_hm_daily_datetime)) ?>
					</td>
					
				</tr>
			<?php
			}
			}else{
				echo "<tr><td colspan='12'>No Data Available</td></tr>";
			}
			?>
			</tbody>
			<tfoot>
					<tr>
						<td colspan="12"><!--<?=$paging?>--></td>
					</tr>
			</tfoot>
		</table>
