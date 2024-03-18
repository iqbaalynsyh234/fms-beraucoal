<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
            <thead>
            <tr>
                <th width="3%">No.</td>
				<th width="8%">Vehicle No</th>
				<th width="10%">Vehicle Name</th>
				<th width="10%">Card No</th>
				<th width="10%">Coordinate</th>
				<th width="20%">Location</th>					
				<th width="7%">GPS Time</th>
				<th width="7%">Speed</th>		
				<th width="7%">Odometer</th>
				<th width="7%">Engine</th>
				<th width="7%">GPS</th>	
            </tr>

            </thead>
			 <tbody>
            <?php
			if(count($data) > 0){
			for($i=0; $i < count($data); $i++)
			{
			?>
            <tr>
                <td valign="top"><?=$i+1?></td>
                <td valign="top">
					<?php 
						foreach($data_vehicle as $vehicle)
						{
							if ($vehicle->vehicle_device == ($data[$i]->gps_name."@".$data[$i]->gps_host))
							{
								echo $vehicle->vehicle_no;
							}
						}
					?>
				</td>
                <td valign="top">
					<?php 
						foreach($data_vehicle as $vehicle)
						{
							if ($vehicle->vehicle_device == ($data[$i]->gps_name."@".$data[$i]->gps_host))
							{
								echo $vehicle->vehicle_name;
							}
						}
					?>
				</td>
                <td valign="top">
					<?php 
						foreach($data_vehicle as $vehicle)
						{
							if ($vehicle->vehicle_device == ($data[$i]->gps_name."@".$data[$i]->gps_host))
							{
								echo $vehicle->vehicle_card_no;?>
							<br/>
					<?php
								echo "(".$vehicle->vehicle_operator.")";
							}
						}
					?>
				</td>
				<td valign="top"><?=$data[$i]->gps_latitude_real_fmt?>, <?=$data[$i]->gps_longitude_real_fmt?></td>
				<td valign="top">
					<?php if (isset($data[$i]->geofence_location) && $data[$i]->geofence_location != "") 
					{
						$x = $data[$i]->geofence_location;
						$y = explode("#", $x);
						
						echo "Geofence :" . " " . $y[1]. "<br />";
						echo $data[$i]->result_position->display_name;
					} 
					else
					{
						echo $data[$i]->result_position->display_name;
					}
					?>
				</td>
				<td valign="top" style="text-align:center;"><?=date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($data[$i]->gps_time)));?></td>
				<td valign="top" style="text-align:right;"><?=number_format($data[$i]->gps_speed*1.852, 0, "", ",")?> kph</td>
				<td valign="top" style="text-align:right;"><?=number_format($data[$i]->result_gps_odometer, 0,"",".");?> km</td>
				<td valign="top" style="text-align:center;"><?php echo ($data[$i]->status1) ? $this->lang->line('lon') : $this->lang->line('loff'); ?></td>
				<td valign="top" style="text-align:center;"><?=($data[$i]->gps_status == "V") ? "NO" : "OK"; ?></td>  
            </tr>
            <?php
			}
			}else{
				echo "<tr><td colspan='11'>No Data Available</td></tr>";
			}
			?>
            </tbody>
			<tfoot>
					<tr>
							<td colspan="11">&nbsp;</td>
					</tr>
			</tfoot>
		</table>
   