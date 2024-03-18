<?php 						
	function hitungJarak($lokasi1_lat, $lokasi1_long, $lokasi2_lat, $lokasi2_long, $unit = 'km', $desimal = 2) {
	// Menghitung jarak dalam derajat
	$derajat = rad2deg(acos((sin(deg2rad($lokasi1_lat))*sin(deg2rad($lokasi2_lat))) + (cos(deg2rad($lokasi1_lat))*cos(deg2rad($lokasi2_lat))*cos(deg2rad($lokasi1_long-$lokasi2_long)))));
							  
	// Mengkonversi derajat kedalam unit yang dipilih (kilometer, mil atau mil laut)
	switch($unit) {
		case 'km':
			$jarak = $derajat * 111.13384; // 1 derajat = 111.13384 km, berdasarkan diameter rata-rata bumi (12,735 km)
		break;
		case 'mi':
			$jarak = $derajat * 69.05482; // 1 derajat = 69.05482 miles(mil), berdasarkan diameter rata-rata bumi (7,913.1 miles)
		break;
		case 'nmi':
			$jarak =  $derajat * 59.97662; // 1 derajat = 59.97662 nautic miles(mil laut), berdasarkan diameter rata-rata bumi (6,876.3 nautical miles)
		}
		return round($jarak, $desimal);
	}
							
function distance($lat1, $lng1, $lat2, $lng2, $miles = true)
{
	$pi80 = M_PI / 180;
	$lat1 *= $pi80;
	$lng1 *= $pi80;
	$lat2 *= $pi80;
	$lng2 *= $pi80;

	$r = 6372.797; // radius dalam km
	$dlat = $lat2 - $lat1;
	$dlng = $lng2 - $lng1;
	$a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlng / 2) * sin($dlng / 2);
	$c = 2 * atan2(sqrt($a), sqrt(1 - $a));
	$km = $r * $c;

	return round(($miles ? ($km * 0.621371192) : $km),2);
}

function getDistanceBetween($latitude1, $longitude1, $latitude2, $longitude2, $unit = 'Mi') 
{ 
	$theta = $longitude1 - $longitude2; 
	$distance = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2)))  + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta))); 
	$distance = acos($distance); 
	$distance = rad2deg($distance); 
	$distance = $distance * 60 * 1.1515; 
	switch($unit) 
	{ 
		case 'Mi': break; 
		case 'Km' : $distance = $distance * 1.609344; 
	} 
	return (round($distance,2)); 
}

?>
<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<thead>
				<tr>
					<th width="1%" valign="top" align="center" style="text-align:center;">*</th>
					<th width="15%" valign="top" align="center" style="text-align:center;">Keluar</th>
					<th width="15%" valign="top" align="center" style="text-align:center;">Masuk</th>
					<th width="10%" valign="top" align="center" style="text-align:center;">Duration</th>
					<th width="10%" valign="top" align="center" style="text-align:center;">Mileage</th>
				</tr>
			</thead>
			<tbody>
			<?php
				if(count($data) > 0){
				for($i=0; $i < count($data); $i++)
				{
				if ($data[$i]->geoalert_direction == 2) {
				?>
					<tr>
						<td valign="top" align="center" style="text-align:center;">*</td>
						<td valign="top" align="center" style="text-align:left;">
						<?php 
							if(isset($data[$i]->geofence_name)){
								$geofence_name = $data[$i]->geofence_name;
								
								if(preg_match("/#/", $geofence_name)) {
									$geofence_rute = explode("#",$geofence_name);
									echo "<font color='red'>"."RUTE : ".$geofence_rute[1]."</font>";
								}else{
									echo "<font color='red'>".$geofence_name."</font>";
								}
							}else{
								$geofence_name = "-";
							}
							echo "<br />";
							
							echo "Start date". " " .date("d/m/Y H:i:s", $data[$i]->geoalert_time_t) ."<br />";
								if($data[$i]->geoalert_vehicle_type == "T5DOOR"){
									if ($data[$i]->geoalert_door == 1){
											echo "Door:"."<font color='red'>"."OPEN"."</font>";
										}else{
											echo "Door:"."<font color='red'>"."CLOSE"."</font>";
										}									
									echo "<br />";
								}
									if ($data[$i]->geoalert_engine == 1){
										echo "Engine:"."<font color='red'>"."ON"."</font>";
									}else{
										echo "Engine:"."<font color='red'>"."OFF"."</font>";
									}
							echo "<br />";
									echo $data[$i]->geoalert_speed." kph";
						?>
						</td>
						
						
						<td valign="top" align="center" style="text-align:left;"> 
							<?php 
							if ($data[$i]->geoalert_direction == 2) 
							{ 
								if (isset($data[$i+1]->geofence_name))
								{

									if(isset($data[$i+1]->geofence_name)){
										$geofence_name = $data[$i+1]->geofence_name;
										
										if(preg_match("/#/", $geofence_name)) {
											$geofence_rute = explode("#",$geofence_name);
											echo "<font color='red'>"."RUTE : ".$geofence_rute[1]."</font>";
										}else{
											echo "<font color='red'>".$geofence_name."</font>";
										}
									}else{
										$geofence_name = "-";
									}
								echo "<br />";
								echo "Finish ". " ". date("d/m/Y H:i:s", $data[$i+1]->geoalert_time_t) . "<br />";
									if($data[$i+1]->geoalert_vehicle_type == "T5DOOR"){
										if ($data[$i+1]->geoalert_door == 1){
												echo "Door:"."<font color='red'>"."OPEN"."</font>";
											}else{
												echo "Door:"."<font color='red'>"."CLOSE"."</font>";
											}									
										echo "<br />";
									}
									if ($data[$i+1]->geoalert_engine == 1){
										echo "Engine:"."<font color='red'>"."ON"."</font>";
									}else{
										echo "Engine:"."<font color='red'>"."OFF"."</font>";
									}
								echo "<br />";
									echo $data[$i+1]->geoalert_speed." kph";
								} 
								else
								{
									echo "-";
								}
							} 
							
							?>
						</td>
					
						<td valign="top" align="center" style="text-align:center;">
						<?php
							if (isset($data[$i+1]->geofence_name))
							{
								$startdate = new DateTime(date("d-m-Y H:i:s", $data[$i]->geoalert_time_t));
								$enddate = new DateTime(date("d-m-Y H:i:s", $data[$i+1]->geoalert_time_t));
								$duration = $startdate->diff($enddate);
								$d_day = $duration->format('%d');
								$d_hour = $duration->format('%h');
								$d_minute = $duration->format('%i');
								$d_second = $duration->format('%s');
								if (isset($d_day) && ($d_day > 0))
								{
									echo $d_day ." ". "Day" ." ".$d_hour ." "."Jam" ." ". $d_minute ." ". "Menit" ." ". $d_second ." ". "Detik";
								}
								else if (isset($d_hour) && ($d_hour > 0))
								{
									echo $d_hour ." "."Jam" ." ". $d_minute ." ". "Menit" ." ". $d_second ." ". "Detik";
								}
								else
								{
									echo $d_minute ." ". "Menit" ." ". $d_second ." ". "Detik";
								}
							}
							else
							{
								echo "-";
							} 
						?>
						</td>
						<td valign="top" align="center" style="text-align:left;"> 
						<?php
						$currentLat = $data[$i]->geoalert_lat; //garis bujur lokasi 1
						$currentLong = $data[$i]->geoalert_lng; //garis lintang lokasi 1
						//$lat1 = $data[$i]->geoalert_lat; //garis bujur lokasi 1
						//$lng1 = $data[$i]->geoalert_lng; //garis lintang lokasi 1
						if (isset($data[$i+1]->geofence_name)){
							$destLat = $data[$i+1]->geoalert_lat; //garis bujur lokasi 2
							$destLon = $data[$i+1]->geoalert_lng; //garis lintang lokasi 2
							//$lat2 = $data[$i+1]->geoalert_lat; //garis bujur lokasi 2
							//$lng2 = $data[$i+1]->geoalert_lng; //garis lintang lokasi 2
							$distance1 = distance($currentLat,$currentLong, $destLat, $destLon); 
							$distance2 = hitungJarak($currentLat,$currentLong, $destLat, $destLon);
							$distance3 = getDistanceBetween($currentLat,$currentLong, $destLat, $destLon, 'Km');
							//echo hitungJarak($currentLat,$currentLong, $destLat, $destLon)."KMA ";
							//echo distance($currentLat,$currentLong, $destLat, $destLon)." KMB ";
							//echo getDistanceBetween($currentLat,$currentLong, $destLat, $destLon, 'Km')." Km";
							//echo $distance3." KM";
							if (isset($distance3))
								{
									if ($distance3 > 10 &&  $distance3 < 20)
									{
										$newkm = $distance3 + 3;
										echo $newkm." KM";
									}
									if ($distance3 >= 10 &&  $distance3 < 30)
									{
										$newkm = $distance3 + 5;
										echo $newkm." KM";
									}
									if ($distance3 >= 30 &&  $distance3 < 50)
									{
										$newkm = $distance3 + 7;
										echo $newkm." KM";
									}
									if ($distance3 >= 50 &&  $distance3 < 70)
									{
										$newkm = $distance3 + 9;
										echo $newkm." KM";
									}
									if ($distance3 >= 70)
									{
										$newkm = $distance3 + 11;
										echo $newkm." KM";
									}
									
									if ($distance3 <= 10)
									{
										$newkm = $distance3;
										echo $newkm." KM";
									}
									
								}

						}
						
						?>
						</td>
					</tr>
					
				<?php
				} }
				}else{
					?>
					<tr><td colspan="14">No Available Data</td></tr>
				<?php } ?>
			</tbody>			
			</tfoot>
		</table>

		


