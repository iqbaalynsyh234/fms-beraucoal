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

<div class="row">
	<div class="col-md-12 col-sm-12">
		<div class="panel">
			<header class="panel-heading panel-heading-blue">RITASE REPORT</header>
				<div class="panel-body" id="bar-parent10">
          <a href="javascript:void(0);" id="export_xcel" type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect m-b-10 btn-circle btn-info"><small>Export to Excel</small></a><br><br>
					<div class="row">
					<?php if (count($data) == 0) {
							echo "<p>No Data</p>";
					}else{ ?>
							<div id="isexport_xcel">
  							<table class="table">
                  <thead>
            				<tr>
            					<th>*</td>
            					<th>Keluar</th>
            					<th>Masuk</th>
            					<th>Duration</th>
            				</tr>
            			</thead>
  								<tbody>
                    <?php
              			$totalritase = 0;
              			$j = count($data);
              			for($i=0; $i < count($data); $i++) {
              			if ($data[$i]->geoalert_direction == 2 && isset($data[$i+1]->geofence_name)) {
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
              			<?php $totalritase+=1; } } ?>
  								</tbody>
  							</table>
							</div>
					<?php } ?>
					</div>
				</div>
		</div>
	</div>
</div>
