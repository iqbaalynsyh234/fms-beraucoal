<style media="screen">
	#report{
		background-color: #221f1f;
  		color: white;
	}
</style>

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

			<tr>
				<td style="text-align:center;"><small>Periode: <?php echo $startdate." - ".$enddate;?></small></td>
			</tr>

			<header class="panel-heading" id="report">REPORT</header>
				<div class="panel-body" id="bar-parent10">
					<div class="row">
						<div class="col-md-12 col-sm-12">

							<div class="col-lg-4 col-sm-4">
								<a href="javascript:void(0);" id="export_xcel" type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect m-b-10 btn-circle btn-default"><small>Export to Excel</small></a>
							</div>

							<div id="isexport_xcel">
							<table class="table table-striped custom-table table-hover">
								<thead>
									<tr>
										<th style="text-align:center;" width="3%">No</th>
										<th style="text-align:center;" width="10%">Date</th>
										<th style="text-align:center;" width="8%">Time</th>
										<th style="text-align:center;" width="10%">Vehicle No</th>
										<th style="text-align:center;" width="10%">Vehicle Name</th>
                    <th style="text-align:center;" width="10%">Type</th>
                    <th style="text-align:center;" width="10%">Speed</th>
                    <!-- <th style="text-align:center;" width="10%">Jalur</th> -->
                    <th style="text-align:center;" width="10%">Location</th>
                    <th style="text-align:center;" width="10%">Coordinate</th>
									</tr>
								</thead>
								<tbody>
								 <?php if(isset($data) && (count($data) > 0)){
											for ($i=0;$i<count($data);$i++){
											?>
											<tr>
												<td style="text-align:center;font-size:12px;"><?php echo $i+1;?></td>
												<td style="text-align:center;font-size:12px;"><?php echo date("d-m-Y", strtotime($data[$i]['outgeofence_report_gps_time']));?></td>
												<td style="text-align:center;font-size:12px;"><?php echo date("H:i:s", strtotime($data[$i]['outgeofence_report_gps_time']));?></td>
                        <td style="text-align:center;font-size:12px;"><?php echo $data[$i]['outgeofence_report_vehicle_no'];?></td>
                        <td style="text-align:center;font-size:12px;"><?php echo $data[$i]['outgeofence_report_vehicle_name'];?></td>
                        <td style="text-align:center;font-size:12px;"><?php echo $data[$i]['outgeofence_report_name'];?></td>
                        <td style="text-align:center;font-size:12px;"><?php echo $data[$i]['outgeofence_report_speed'];?></td>
                        <!-- <td style="text-align:center;font-size:12px;"><?php echo $data[$i]['outgeofence_report_jalur'];?></td> -->
                        <td style="text-align:center;font-size:12px;"><?php echo $data[$i]['outgeofence_report_location'];?></td>
                        <td style="text-align:center;font-size:12px;"><?php echo $data[$i]['outgeofence_report_coordinate'];?></td>
											</tr>
									<?php }?>
								<?php }else{ ?>
						        <tr>
						        	<td colspan="10">No Available Data</td>
										</tr>
								<?php } ?>
							</tbody>
						</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
