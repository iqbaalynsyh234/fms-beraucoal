
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
		<div class="panel" >
			<header class="panel-heading panel-heading-blue">REPORT</header>
				<div class="panel-body" id="bar-parent10">
					<div class="row">
					<!-- <?php if (count($data) == 0) {
							echo "<p>NO DATA AVAILABLE</p>";
					}else{ ?> -->
						<div class="col-md-12 col-sm-12">

							<div class="col-lg-4 col-sm-4">
								<a href="javascript:void(0);" id="export_xcel" type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect m-b-10 btn-circle btn-info"><small>Export to Excel</small></a>
							</div>

							<div id="isexport_xcel">
							<table class="table table-striped custom-table table-hover">
								<thead>
									<tr>
										<th style="text-align:center;" width="2%">No</td>
										<th style="text-align:center;" width="10%">Vehicle No</th>
										<th style="text-align:center;" width="10%">Vehicle Name</th>
										<th style="text-align:center;" width="15%">GPS Date</th>
										<th style="text-align:center;" width="15%">GPS Time</th>
										<th style="text-align:center;" width="20%">Position</th>
										<th style="text-align:center;" width="8%">Coordinate</th>
										<th style="text-align:center;" width="5%">Speed (km/jam)</th>
										<th style="text-align:center;" width="5%">Engine</th>
										<th style="text-align:center;" width="5%">Brake Status</th>
									</tr>
								</thead>
								<tbody>
									<?php
									if (count($data)>0)
									{

										for ($i=0;$i<count($data);$i++)
										{
											$gps_latitude_real  = $data[$i]['gps_latitude_real'];
											$gps_longitude_real = $data[$i]['gps_longitude_real'];

											if($data[$i]['gps_info_io_port'] == "0000100000"){
												$engine = "ON";
											}else{
												$engine = "OFF";
											}
											if($data[$i]['gps_cs'] == "53"){
												$brake = "STOP";
											}else{
												$brake = "MOVE";
											}

											$datefix = date("d-m-Y", strtotime("+7 hour", strtotime($data[$i]['gps_time'])));
											$timefix = date("H:i:s", strtotime("+7 hour", strtotime($data[$i]['gps_time'])));
										?>
										<tr>
											<td style="text-align:center;"><small><?php echo $i+1;?></td>
											<td style="text-align:center;"><small><?php echo $vehicle_no;?></td>
											<td style="text-align:center;"><small><?php echo $vehicle_name;?></td>
											<td style="text-align:center;"><small><?php echo $datefix;?></td>
											<td style="text-align:center;"><small><?php echo $timefix;?></td>
											<td style="text-align:center;"><small>
												<strong><?php echo $data[$i]['gps_position'] ?></strong></a>
											</td>
											<td style="text-align:center;"><small><a target="_blank" href="http://maps.google.com/maps?q=<?=$gps_latitude_real.",".$gps_longitude_real;?>"><?=$gps_latitude_real.",".$gps_longitude_real;?></a></td>
											<td style="text-align:center;"><small><?php echo round($data[$i]['gps_speed']*1.852);?></td>
											<td style="text-align:center;"><small><?=$engine;?></td>
											<td style="text-align:center;"><small><?php echo $brake;?></td>
										</tr>
								<?php
										}
									}else{
										echo "<tr><td colspan='12'><small>No Data Available</td></tr>";
									}
									?>

								</tbody>
							</table>
							</div>
						</div>

					<!-- <?php } ?> -->

					</div>
				</div>
		</div>
	</div>
</div>
