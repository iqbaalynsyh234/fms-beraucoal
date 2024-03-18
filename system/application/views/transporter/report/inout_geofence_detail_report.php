<table width="100%" cellpadding="3" class="table sortable no-margin">
			<thead>
				<tr>
					<th width="2%">*</td>
					<th width="20%">Keluar</th>
					<th>Masuk</th>
					<th>Duration</th>
				</tr>
			</thead>
			<tbody>
			<?php 
			$j = count($data);
			for($i=0; $i < count($data); $i++) {
			if ($data[$i]->geoalert_direction == 2) {
			?>
				<tr <?=($i%2) ? "class='odd'" : "";?>>
					<td>*</td>
					<td>
					
					<?php 

						echo $data[$i]->geofence_name;
						echo "<br />";
						echo "Start date". " " .date("d/m/Y H:i:s", $data[$i]->geoalert_time_t) ."<br />";
					?>
					</td>
					<td>
					 <?php 
						if ($data[$i]->geoalert_direction == 2) 
						{ 
							if (isset($data[$i+1]->geofence_name))
							{

								echo $data[$i+1]->geofence_name;
								echo "<br />";
								echo "Finish ". " ". date("d/m/Y H:i:s", $data[$i+1]->geoalert_time_t) . "<br />"; 
							} 
							else
							{
								echo "-";
							}
						} 
					?>
					</td>
					<td>
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
				</tr>
			<?php } } ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="7"></td>
				</tr>
			</tfoot>
		</table>
