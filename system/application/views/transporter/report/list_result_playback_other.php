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
	        <th width="3%">No</td>
			<th width="10%">Vehicle</th>
			<th width="7%">Engine</th>
			<th width="10%">Start Time</td>
			<th width="10%">End Time</th>					
			<th width="7%">Duration</th>
			<th width="7%">Trip Mileage</th>		
			<th width="7%">Cumulative Mileage</th>
			<th width="18%">Location Start</th>
			<th width="18%">Location End</th>
			
         </tr>
    </thead>
	<tbody>
    <?php
		if(count($data) > 0){
			$j=1;
			$cummulative = 0;
			$location_start = "";
			$location_end = "";
			$show_url_start = false;
			$show_url_end = false;
			foreach($data as $vehicles=>$value_vehicles){
				foreach($value_vehicles as $number=>$value_number){
					foreach($value_number as $engine=>$report){
					
						if(isset($report['geofence_start']) && $report['geofence_start'] != ""){
							$arr_geofence_start = explode("#", $report['geofence_start']);
							if(count($arr_geofence_start)>1){
								$report['geofence_start'] = $arr_geofence_start[1];
							}
							$location_start .= "<b>GEOFENCE: " . strtoupper($report['geofence_start']) . "</b><br/>";
						}else{
							$gps_longitude_real = $report['start']->gps_longitude_real;
							$gps_latitude_real = $report['start']->gps_latitude_real;
								
							$coordinate_geofence_url = "history_" . $gps_latitude_real .",".$gps_longitude_real;
								
							//$start_map_url = base_url() . "geofence/manage/". str_replace("@", "/", $report['start']->gps_name) ."/" . base64_encode($coordinate_geofence_url);
							$start_map_url = "http://maps.google.com/maps?q=".$gps_latitude_real.",".$gps_longitude_real;
							$show_url_start = true;
						}
					
						$location_start .= $report['location_start']->display_name;
						
						if(isset($report['geofence_end']) && $report['geofence_end'] != ""){
							$arr_geofence_end = explode("#", $report['geofence_end']);
							if(count($arr_geofence_end)>1){
								$report['geofence_end'] = $arr_geofence_end[1];
							}
							$location_end .= "<b>GEOFENCE: " . strtoupper($report['geofence_end']) . "</b><br/>";
						}else{
							$gps_longitude_real_end = $report['end']->gps_longitude_real;
							$gps_latitude_real_end = $report['end']->gps_latitude_real;
								
							$coordinate_geofence_url_end = "history_" . $gps_latitude_real_end .",".$gps_longitude_real_end;
								
							//$end_map_url = base_url() . "geofence/manage/". str_replace("@", "/", $report['end']->gps_name) ."/" . base64_encode($coordinate_geofence_url_end);
							$end_map_url = "http://maps.google.com/maps?q=".$gps_latitude_real_end.",".$gps_longitude_real_end;
							$show_url_end = true;
							
							
						}
											
						$location_end .= $report['location_end']->display_name;
					if($engine == "ON"){
						$cummulative += $report['mileage'];
					}
					
	?>
                
        		<tr>
                	<td valign="top"><?=$j?></td>
					<td valign="top" style="text-align:center;">
						<?=str_replace("#", "<br/>", strtoupper($vehicles));?>
					</td>
					<td valign="top" style="text-align:center;">
						<?=$engine;?>
					</td>	
					<td valign="top" style="text-align:center;">
						<?=str_replace(" ", "<br/>", date("d/m/Y H:i:s", strtotime($report['start']->gps_time)));?>
					</td>	
					<td valign="top" style="text-align:center;">
						<?=str_replace(" ", "<br/>", date("d/m/Y H:i:s", strtotime($report['end']->gps_time)));?>
					</td>	
					<td valign="top" style="text-align:center;">
						<?=$report['duration'];?>
					</td>	
					<td valign="top" style="text-align:center;">
					<?php if($engine == "ON"){ ?>
						<?=$report['mileage'];?> km 
					<?php }else{ ?>
						0 km
					<?php } ?>
						
                	</td>
					<td valign="top" style="text-align:center;">
                		<?=$cummulative;?> km 
                	</td>
					<td valign="top" style="font-size:10px;">
					<?php 
					if($show_url_start){
						echo "<a href='". $start_map_url."' target='_blank'><font color='#0000ff'>" . $location_start . "</font></a>";
					}else{
						echo $location_start;
					}
					?>
					<br />
					<!--<font color="red"> <?=$report['start']->gps_latitude_real;?>, <?=$report['start']->gps_longitude_real;?></font>-->
					</td>
					<td valign="top" style="font-size:10px;">
					<?php 
					if($show_url_end){
						echo "<a href='". $end_map_url."' target='_blank'><font color='#0000ff'>" . $location_end . "</font></a>";
					}else{
						echo $location_end;
					}
					?>
					<br />
					<!--<font color="red"> <?=$report['end']->gps_latitude_real;?>, <?=$report['end']->gps_longitude_real;?> </font>-->
					</td>
					  
            </tr>
    <?php
					$show_url_start = false;
					$show_url_end = false;
					$location_start = "";
					$location_end = "";
            		$j++;
					}
				}
				$cummulative = 0;
				echo "<tr><td colspan='10' style='background-color:#DCDCDC;'>&nbsp;</td></tr>";
			}
			
		}else{
	?>
        <tr>
        	<td colspan="10">No Available Data</td>
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
</div>