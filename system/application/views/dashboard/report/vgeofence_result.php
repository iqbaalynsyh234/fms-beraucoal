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


							<div class="col-lg-6 col-sm-6">	
								<input id="btn_hide_form" class="btn btn-circle btn-danger" title="" type="button" value="Hide Form" onclick="javascript:return option_form('hide')" />
								<input id="btn_show_form" class="btn btn-circle btn-success" title="" type="button" value="Show Form" onClick="javascript:return option_form('show')" style="display:none"/>
							</div>
							<div class="col-lg-2 col-sm-2">	
							</div>
							<br />
							
<div class="row">
	<div class="col-md-12 col-sm-12">
		<div class="panel">
			<header class="panel-heading panel-heading-blue">REPORT</header>
				<div class="panel-body" id="bar-parent10">
					<div class="row">	
					<?php if (count($data) == 0) {
							echo "<p>No Data</p>";
					}else{ ?>
						<div class="col-md-12 col-sm-12">
							
							<div class="col-lg-4 col-sm-4">	
								<a href="javascript:void(0);" id="export_xcel" type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect m-b-10 btn-circle btn-info"><small>Export to Excel</small></a>
							</div>	
							
							<div id="isexport_xcel">
							<table class="table table-striped custom-table table-hover">
								<thead>
									<tr>
										<th width="2%">*</th>
										<th width="20%">Keluar</th>
										<th>Masuk</th>
										<th>Duration</th>
									</tr>
								</thead>
								<tbody>
								
								
	 <?php
		if(isset($data) && (count($data) > 0)){
		$j = count($data);
			for($i=0; $i < count($data); $i++) {
			if ($data[$i]->geoalert_direction == 2) {
			?>
				<tr <?=($i%2) ? "class='odd'" : "";?>>
					<td valign="top" style="text-align:center;font-size:12px;">*</td>
					<td valign="top" style="text-align:center;font-size:12px;">
					
					<?php 

						echo $data[$i]->geofence_name;
						echo "<br />";
						echo "Start date". " " .date("d/m/Y H:i:s", $data[$i]->geoalert_time_t) ."<br />";
					?>
					</td>
					<td valign="top" style="text-align:center;font-size:12px;">
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
					<td valign="top" style="text-align:center;font-size:12px;">
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
			<?php } } 
		
		
		}else{
	?>
        <tr>
        	<td colspan="10">No Available Data</td>
		</tr>
	<?php
		}
	?>
								</tbody>
							</table>
							</div>
						</div>	
					
					<?php } ?>
					
					</div>
				</div>
		</div>
	</div>
</div>
