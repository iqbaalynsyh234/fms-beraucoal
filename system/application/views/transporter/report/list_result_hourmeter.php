<script>
	function showdetail()
	{
		jQuery("#tbl_hourmeter_detail").show("hide");
		jQuery("#tbl_hourmeter_detail").show("slow");
	}
</script>

<?php //echo "Hourmeter Total until"." ".$now." ". ":"  . " " . $total_hourmeter;?>
<?php 
if(count($data) > 0)
{
	$j=1;
	$z=0;
	$y = count($data);
	$new = "";
	foreach($data as $vehicle_no=>$val)
	{
		if($new != $vehicle_no)
		{
			$cumtime = 0;
			$trip_no = 1;
		}
		foreach($val as $no=>$report)
		{
			$mileage = $report['end_mileage']- $report['start_mileage'];
			$duration = get_time_difference($report['start_time'], $report['end_time']);
			$show = "";
			if($duration[0]!=0)
			{
				$show .= $duration[0] ." Day ";
			}
			if($duration[1]!=0)
			{
				$show .= $duration[1] ." Hour ";
			}
			if($duration[2]!=0)
			{
				$show .= $duration[2] ." Min ";
			}
			if ($duration[3]!=0)
			{
				$show .= $duration[3] ." Detik";
			}
			if($show == "")
			{
				$show .= "0 Min";
			}
			$ex = explode(" ",$show);
			
			if ($ex[1]=="Day")
			{
				$val = $ex[0];
			}
			if ($ex[1]=="Hour")
			{	
				$val = $ex[0]*60*60;
				if (isset($ex[2]))
				{
					$val += $ex[2]*60;
				}
				if (isset($ex[4]))
				{
					$val += $ex[4];
				}
			}
			if ($ex[1]=="Min")
			{
				$val = $ex[0]*60;
				if (isset($ex[2]))
				{
					$val += $ex[2];
				}
			}
			if ($ex[1] == "Detik")
			{
				$val = $ex[0];
			}
			if (isset($val))
			{
				$cumtime += $val;
				$cummulative_time = gmdate("H:i:s", $cumtime);
			}
			if ($report['start_geofence_location']) 
			{
				$arrGeo = explode("#", $report['start_geofence_location']);
				if(count($arrGeo)>1)
				{
					$geoname = $arrGeo[1];
				}
				else
				{
					$geoname = $arrGeo[0];
				}
			}
			if ($z==0)
			{
				if (isset($geoname))
				{
					$startlocation = $geoname." ".$report['start_position']->display_name;
				}
				else
				{
					$startlocation = $report['start_position']->display_name;
				}
			}
			$z++;
			if (($z-1) == $y)
			{
				if (isset($geoname))
				{
					$endlocation = $geoname." ".$report['start_position']->display_name;
				}
				else
				{
					$endlocation = $report['start_position']->display_name;
				}
			}
			
			$j++;
		}
		
	}
}
else
{
	echo "No Available Data !";
}
?>

<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
<tr>
	<td>
		Rakapitulasi Total Hourmeter Periode : 
		<strong><?php echo $startdate." ".$starttime." "." - "." ".$enddate." ".$endtime;?></strong>
	</td>
</tr>
<tr>
	<td>
		Total Hourmeter : <strong><?php if (isset($cummulative_time)) { echo $cummulative_time; }?></strong>
	</td>
</tr>
<tr>
	<td>
		Start Project(Location) :
		<strong>
		<?php 
			if (isset($startlocation))
			{
				echo $startlocation;
			}
		?>
		</strong>
	</td>
</tr>
<tr>
	<td>
		End Project(Location) :
		<strong>
		<?php 
			if (isset($endlocation))
			{
				echo $endlocation;
			}
		?>
		</strong>
	</td>
</tr>
<tr>
	<td><a class="button" href="javascript:showdetail();">Click For Detail</a></td>
</tr>
</table>



<table id="tbl_hourmeter_detail" name="tbl_hourmeter_detail" style="display:none;" width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
<thead>
	<tr>
		<th width="3%" style="text-align:center">No</td>
		<th width="8%" style="text-align:center">Vehicle No</th>
		<th width="10%" style="text-align:center">Vehicle Name</th>
		<th width="3%" style="text-align:center">Active</td>
		<th width="10%" style="text-align:center">Start Working Time</td>
		<th width="10%" style="text-align:center">End Working Time</th>					
		<th width="7%" style="text-align:center">Duration</th>
		<th width="7%" style="text-align:center">Cumulative</th>
		<th width="15%" style="text-align:center">Project Location</th>
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
						$cumtime = 0;
						$trip_no = 1;
					}
					foreach($val as $no=>$report){
						$mileage = $report['end_mileage']- $report['start_mileage'];
				?>
                
            <tr>
                <td valign="top" style="text-align:center;"><?=$j?></td>
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
							if ($duration[3]!=0){
								$show .= $duration[3] ." Detik";
								}
								if($show == ""){
									$show .= "0 Min";
									}
							echo $show;
							?>
                </td>
				<td style="text-align:center;">
					<?php
					
					$ex = explode(" ",$show);
					
					if ($ex[1]=="Day")
					{
						$val = $ex[0];
					}
					
					if ($ex[1]=="Hour")
					{	
						$val = $ex[0]*60*60;
						
						if (isset($ex[2]))
						{
							$val += $ex[2]*60;
						}
						
						if (isset($ex[4]))
						{
							$val += $ex[4];
						}

					}
					if ($ex[1]=="Min")
					{
						$val = $ex[0]*60;
						if (isset($ex[2]))
						{
							$val += $ex[2];
						}
					}
					if ($ex[1] == "Detik")
					{
						$val = $ex[0];
					}
					
					if (isset($val))
					{
						$cumtime += $val;
						$cummulative_time = gmdate("H:i:s", $cumtime);
						echo $cummulative_time;
					}
					else
					{
						echo "-";
					}
					?>
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
					echo "<b>" . strtoupper($geoname) . "</b><br/>";
				}
				?>
				<?=$report['start_position']->display_name;?>
				</td>
            </tr>
            <?php
            $j++;
            }}
			}else{
			?>
            <tr><td colspan="11">No Available Data</td></tr>
			<?php
			}
			?>
            </tbody>
			<tfoot>
					<tr>
						<td colspan="11">
						</td>
					</tr>
			</tfoot>
		</table>