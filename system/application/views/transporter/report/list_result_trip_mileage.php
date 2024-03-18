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
				<th width="8%">Vehicle No</th>
				<th width="10%">Vehicle Name</th>
				<th width="3%">Trip No</td>
				<th width="10%">Start Time</td>
				<th width="10%">End Time</th>					
				<th width="7%">Duration</th>
				<th width="7%">Trip Mileage</th>		
				<th width="7%">Cumulative Mileage</th>
				<th width="15%">Location Start</th>
				<th width="15%">Location End</th>
            </tr>
            </thead>
			 <tbody>
             <?php
			if(count($data) > 0){
			
				
				
				$j=1;
				$new = "";
				foreach($data as $vehicle_no=>$val)
				{
					if($new != $vehicle_no){
						$cumm = 0;
						$trip_no = 1;
					}
					foreach($val as $no=>$report){
					
						$mileage = $report['end_mileage']- $report['start_mileage'];
						if($mileage != 0){
						
						$duration = get_time_difference($report['start_time'], $report['end_time']);
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
				?>
            <tr>
                <td valign="top"><?=$j?></td>
				<td valign="top" style="text-align:center;">
				<?=$vehicle_no;?>
				</td>
				<td valign="top" style="text-align:center;">
				<?=$report['vehicle_name'];?>
				</td>	
				<td valign="top" style="text-align:center;">
				<?=$trip_no++;?>
				</td>	
				<td valign="top" style="text-align:center;">
				<?=$report['start_time'];?>
				</td>	
				<td valign="top" style="text-align:center;">
				<?=$report['end_time'];?>
				</td>	
				<td valign="top" style="text-align:center;">
				<?php
                $duration = get_time_difference($report['start_time'], $report['end_time']);
                //print_r($duration);
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
							echo $show;
							?>
                </td>
				<td valign="top" style="text-align:center;">
                <?php
                $tm = $mileage/1000;
                echo round($tm,2);
                ?> km
                </td>
				<td valign="top" style="text-align:center;">
				<?php
				$cumm += $tm;
				echo round($cumm,2);
				?> km
				</td>
				<td valign="top" style="text-align:center;font-size:10px;">
				<?php
				if ($report['start_geofence_location']) {
					$arrGeo = explode("#", $report['start_geofence_location']);
					if(count($arrGeo)>1){
						$geoname = $arrGeo[1];
					}else{
						$geoname = $arrGeo[0];
					}
					echo "<b><font color='red'>Geofence : " . strtoupper($geoname) . "</font></b><br/>";
				}
				?>
				
				<a target="_blank" href="http://maps.google.com/maps?q=<?=$report['start_latitude'].",".$report['start_longitude'];?>">
					<strong><?=$report['start_position']->display_name;?></strong>
				</a>
				</td>
				<td valign="top" style="text-align:center;font-size:10px;">
				<?php
				if ($report['end_geofence_location']) {
					$arrGeoEnd = explode("#", $report['end_geofence_location']);
					if(count($arrGeoEnd)>1){
						$geonameend = $arrGeoEnd[1];
					}else{
						$geonameend = $arrGeoEnd[0];
					}
					echo "<b><font color='red'>Geofence : " . strtoupper($geonameend) . "</font></b><br/>";
					
				}
				?>
				
				<a target="_blank" href="http://maps.google.com/maps?q=<?=$report['end_latitude'].",".$report['end_longitude'];?>">
					<strong><?=$report['end_position']->display_name;?></strong>
				</a>
				
				</td>
            </tr>
            <?php
            $j++;
            }}}}
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
</div>