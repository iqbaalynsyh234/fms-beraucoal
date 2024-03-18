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
function mn_map(v)
	{
		location = '<?php echo base_url();?>operational_report/map/'+v,'_blank';
		
		/*window.open(
		  '<?php echo base_url();?>operational_report/dataoperational_map/'+v',
		  '_blank' // <- This is what makes it open in a new window.
		);*/
		return false;
	}

</script>


<a class="button" href="javascript:void(0);" id="export_xcel">Export to Excel</a>
<p>
<tr>
	<td style="text-align:center;">
		<small>Note: Jika Interval Data GPS 2 Menit, jumlah data 600 s/d 800 perhari : GPS Normal. Kurang dari jumlah data tersebut, Ada Lost data GPS.</small>
	</td>	
</tr>

<div id="isexport_xcel">
<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
	<thead>
    	<tr>
	        <th style="text-align:center;" width="3%">No</th>
			<th style="text-align:center;" width="10%">Vehicle</th>
			<th style="text-align:center;" width="10%">Area</th>
			<th style="text-align:center;" width="10%">Start Time</th>
			<th style="text-align:center;" width="10%">End Time</th>
			<th style="text-align:center;" width="7%">Duration (Engine ON)</th>
			<th style="text-align:right;" width="7%">Trip Mileage (KM)</th>		
			<th style="text-align:center;" width="20%">Location Start</th>
			<th style="text-align:center;" width="20%">Location End</th>
			<th style="text-align:center;" width="5%">Total Data GPS</th>
			
		</tr>
    </thead>
	<tbody>
		<?php
			if (count($data)>0)
			{
				$total_rows = 0;
				for ($i=0;$i<count($data);$i++)
				{ 
					$this->dbtrip = $this->load->database("operational_report",true);
					$this->dbtrip->order_by("trip_mileage_start_time","asc");
					$this->dbtrip->where("trip_mileage_vehicle_id", $data[$i]->vehicle_device);
					$this->dbtrip->where("trip_mileage_start_time >=",$sdate);
					$this->dbtrip->where("trip_mileage_end_time <=", $edate);
					$q = $this->dbtrip->get($dbtable);
					$rows = $q->result();
					$total_rows = count($rows);
					
					$totalcumm = 0;
					$totalcumm_on = 0;
					$totalcumm_off = 0;
					$totaldur = 0;
					$totaldatagps = 0;
					
					for($x=0; $x < count($rows); $x++)
					{	
						if($rows[$x]->trip_mileage_engine == 1 ){
							$totalcumm_on += $rows[$x]->trip_mileage_trip_mileage;
							$totaldur += $rows[$x]->trip_mileage_duration_sec;	
						}
						if($rows[$x]->trip_mileage_engine == 0 ){
							$totalcumm_off += $rows[$x]->trip_mileage_trip_mileage;
						}
						
						$totalcumm += $rows[$x]->trip_mileage_trip_mileage;
						$totaldatagps = $rows[$x]->trip_mileage_totaldata;
						
					}
					
						$starttime = "";
						$endtime = "";
						$locationstart = "";
						$locationend = "";
						$geofencestart = "";
						$geofenceend = "";
						$koordstart = "";
						$koordend = "";
						
					if($total_rows > 0){
						$starttime = $rows[0]->trip_mileage_start_time;
						$endtime = $rows[($total_rows-1)]->trip_mileage_end_time; //print_r($endtime);exit();
						$locationstart = $rows[0]->trip_mileage_location_start;
						$locationend = $rows[($total_rows-1)]->trip_mileage_location_end;
						$geofencestart = $rows[0]->trip_mileage_geofence_start;
						$geofenceend = $rows[($total_rows-1)]->trip_mileage_geofence_end;
						$koordstart = $rows[0]->trip_mileage_coordinate_start; 
						$koordend = $rows[($total_rows-1)]->trip_mileage_coordinate_end;
					
					}
					
				?>
				<tr>
					<td style="text-align:center;"><?php echo $i+1;?></td>
					<td style="text-align:center;">
						<?php echo $data[$i]->vehicle_no;?> <br /> 
						<?php echo $data[$i]->vehicle_name;?>
					</td>
					<td style="text-align:center;">
						<?php 
							if (isset($rcompany))
							{
								foreach ($rcompany as $com)
								{
									if ($com->company_id == $data[$i]->vehicle_company)
									{
										echo $com->company_name;
									}
								}
							}
						?>
					</td>
					<td style="text-align:center;">
						<?=$starttime;?>
					</td>
					<td style="text-align:center;">
						<?=$endtime;?>
					</td>
					<td style="text-align:center;">
						<?php
						if (isset($totaldur))
									{
										$conval = $totaldur;
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
										/*  if(isset($seconds) && $seconds > 0)
										{
											echo $seconds." "."Detik"." ";
										} */
									}
						?>
						<?php if($totaldur == 0){ ?>
							<?=$totaldur;?>
						<?php } ?>
						
					</td>
					
					<td style="text-align:right;">
						 <?=round($totalcumm,2);?>
					</td>
					
					<td style="text-align:center;">
						<?php if($geofencestart != 0){ ?>
							<font color="red"><?=$geofencestart;?></font><br />
						<?php } ?>
						<?=$locationstart;?> <br />
						<small><?=$koordstart;?></small>
					</td>
					
					<td style="text-align:center;">
						<?php if($geofenceend != 0){ ?>
							<font color="red"><?=$geofenceend;?></font><br />
						<?php } ?>
						<?=$locationend;?> <br />
						<small><?=$koordend;?></small>
					</td>
					<td style="text-align:center;">
						<?=$totaldatagps;?>
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