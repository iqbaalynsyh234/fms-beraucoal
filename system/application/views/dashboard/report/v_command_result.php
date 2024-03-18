
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
					<?php if (count($data) == 0) {
							echo "<p>NO DATA AVAILABLE</p>";
					}else{ ?>
						<div class="col-md-12 col-sm-12">
							
							<div class="col-lg-4 col-sm-4">	
								<a href="javascript:void(0);" id="export_xcel" type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect m-b-10 btn-circle btn-info"><small>Export to Excel</small></a>
							</div>	
							
							<div id="isexport_xcel">
							<table class="table table-striped custom-table table-hover">
								<thead>
									<tr>
										<th style="text-align:center;" width="2%">No</td>
										<th style="text-align:center;" width="10%">Vehicle</th>
										<th style="text-align:center;" width="5%">Command</th>
										<th style="text-align:center;" width="5%">Value</th>
										<th style="text-align:center;" width="10%">Datetime</th>
										<th style="text-align:center;" width="10%">Status</th>
										<th style="text-align:center;" width="10%">Geofence Now</th>
										<th style="text-align:center;" width="10%">Road Type</th>
										<th style="text-align:center;" width="10%">Last Site/Port</th>
										<th style="text-align:center;" width="8%">Coordinate</th>											
									</tr>
								</thead>
								<tbody>
									<?php
									if (count($data)>0)
									{
										
										for ($i=0;$i<count($data);$i++)
										{ 
											$datetime = date("d-m-Y H:i:s", strtotime("+1 hour", strtotime($data[$i]->command_date)));
											
											if($data[$i]->command_status == 1){
												$status = "SUCCESS";
											}else if($data[$i]->command_status == 0){
												$status = "PENDING";
											}else{
												$status = "INVALID";
											}
										?>
										<tr>
											<td style="text-align:center;"><small><?php echo $i+1;?></td>
											<td style="text-align:center;"><small><?php echo $vehicle_no;?></td>
											<td style="text-align:center;"><small><?php echo $data[$i]->command_text;?></td>
											<td style="text-align:center;"><small><?php echo $data[$i]->command_value*10;?></td>
											<td style="text-align:center;"><small><?php echo $datetime;?></td>
											<td style="text-align:center;"><small><?php echo $status;?></td>
											<td style="text-align:center;"><small>
												<strong><?php echo $data[$i]->command_geofence;?></strong>
											</td>
											<td style="text-align:center;"><small>
												<strong><?php echo $data[$i]->command_road_type;?></strong>
											</td>
											<td style="text-align:center;"><small>
												<strong><?php echo $data[$i]->command_geofence_site;?></strong>
											</td>
											<td style="text-align:center;"><small><a target="_blank" href="http://maps.google.com/maps?q=<?=$data[$i]->command_coordinate;?>"><?=$data[$i]->command_coordinate;?></a></td>
											
											
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
					
					<?php } ?>
					
					</div>
				</div>
		</div>
	</div>
</div>
