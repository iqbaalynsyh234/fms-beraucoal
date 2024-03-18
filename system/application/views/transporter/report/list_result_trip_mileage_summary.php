
           
			
			
			 
        <?php
			 
			$cek_data = count($data);
			$no_urut = 0;
			$aqum_trip = 0;
			$start_time = "";
			$end_time = "";
			$location_end = "";
			$location_start = "";
			$duration_plus = 0;
			$show = "";
			$vehic_no = 0;
			$vehic_name = "";
			$trip_nom = 0;
			$x = 0;
			$y = 0;
			$z = 0;
			
			if(count($data) > 0){
				
				$j=1;
				$new = "";
				
				foreach($data as $vehicle_no=>$val)
				{
				
					$cek_data--;
				
					if($new != $vehicle_no){
						$cumm = 0;
						$trip_no = 1;
					}
					
					
					
					foreach($val as $no=>$report){
					
						$mileage = $report['end_mileage']- $report['start_mileage'];
						
						if($mileage != 0){
						
							if ($x == 0)
							{
								$y = $report['start_time'];
							}
								
							$z = $report['end_time'];
							$x = $x + 1;
								
							$duration = get_time_difference($y, $z);
							$show = "";
							
							if($duration[0]!=0){
								$show .= $duration[0] ." Day ";
							}
							
							if($duration[1]!=0){
								$show .= $duration[1] ." Hour ";
							}
							
							if($duration[2]!=0){
								$show .= $duration[2] ." Min ";
							}
							
							if($show == ""){
								$show .= "0 Min";
							}
						
							if ($show != "0 Min")
							{
							
								if($j == 1){
									
									$no_urut++;
									$vehic_no = $vehicle_no;
									$vehic_name = $report['vehicle_name'];
									$start_time = $report['start_time'];
								
									if ($report['start_geofence_location']) {
										$arrGeo = explode("#", $report['start_geofence_location']);
										if(count($arrGeo)>1){
											$geoname = $arrGeo[1];
										}else{
											$geoname = $arrGeo[0];
										}
										
									}
									else
									{
										$geoname ="";
									}
										
									$location_start = strtoupper($geoname)." ".$report['start_position']->display_name;
									
								}	
								 

								$tm = $mileage/1000;
								$aqum_trip = $aqum_trip + $tm;
								
								
								$j++;
								
								$end_time = $report['end_time'];
								
								
								if ($report['end_geofence_location']) {
										$arrGeoEnd = explode("#", $report['end_geofence_location']);
										if(count($arrGeoEnd)>1){
											$geonameend = $arrGeoEnd[1];
										}else{
											$geonameend = $arrGeoEnd[0];
										}
										
								}else{
									$geonameend = "";
								}
								
								$location_end = strtoupper($geonameend)." ".$report['end_position']->display_name;
							
						
							}
						}
						
						
					}	
		?>
						

						
				<?php	
					
					if($cek_data == 0){
					
						
				?>
				
						<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
						
							 <thead>
								<tr>
									<th width="3%">No</td>
									<th width="8%">Vehicle No</th>
									<th width="10%">Vehicle Name</th>
									<th width="10%">Start Time</td>
									<th width="10%">End Time</th>
									<th width="10%">Duration</th>
									<th width="7%">Trip Mileage</th>		
									<th width="15%">Location Start</th>
									<th width="15%">Location End</th>
								</tr>
							</thead>
							
							<tbody>
								<tr>
									<td valign="top" style="text-align:center;">
										<?php
										echo $no_urut;
										?>
									</td>
									
									<td valign="top" style="text-align:center;">
										<?php
										echo $vehic_no;
										?>
									</td>
									
									<td valign="top" style="text-align:center;">
										<?php
										echo $vehic_name;
										?>
									</td>
								
									
									<td valign="top" style="text-align:center;">
										<?php
										echo $start_time;
										?>
									</td>	
									
									<td valign="top" style="text-align:center;">
										<?php
										echo $end_time;
										?>
									</td>
									
									<td valign="top" style="text-align:center;">
										<?php
										echo $show;
										?>
									</td>
							
									<td valign="top" style="text-align:center;">
										<?php
										echo $aqum_trip;
										?> km
									</td>
							
									<td valign="top" style="text-align:center;">
										<?php
										echo $location_start;
										?>
									</td>	
									
									<td valign="top" style="text-align:center;">
										<?php
										echo $location_end;
										?>
									</td>
								
								</tr>
							</tbody>
						
						</table>
				
					
				<?php
					
					}
				?>	
					
					
			<?php		
				}
			
			}else{
				?>
				<tr><td colspan="11">No Available Data</td></tr>
				<?php
			}
			?>
            </tbody>
			<tfoot>
					<tr>
							<td colspan="11">&nbsp;</td>
					</tr>
			</tfoot>
		</table>