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
										<th width="3%" valign="top" style="text-align:center;font-size:12px">No</td>
										<th width="10%" valign="top" style="text-align:center;font-size:12px">Vehicle No</th>
										<th width="10%" valign="top" style="text-align:center;font-size:12px">Vehicle Name</th>
										<th width="5%" valign="top" style="text-align:center;font-size:12px">Driver ID </th>
										<th width="5%" valign="top" style="text-align:center;font-size:12px">Driver Name</th>
										<th width="5%" valign="top" style="text-align:center;font-size:12px">Driver Image</th>
										<th width="11%" valign="top" style="text-align:center;font-size:12px">Face Detected</th>
									</tr>
								</thead>
								<tbody>
                    <?php
                         if (isset($data)) {
                           $no = 1;
                           foreach ($data as $datanya) {

							   if (isset($vehicles)) {
											foreach ($vehicles as $vehicle) {
												if ($vehicle->vehicle_mv03 == $datanya->change_imei) {
													$vehicle_no = $vehicle->vehicle_no;
													$vehicle_name = $vehicle->vehicle_name;
												}
											}
										}

								if (isset($drivers)) {
											foreach ($drivers as $driver) {
												if ($driver->driver_idcard == $datanya->change_driver_id) {
													$driver_name = $driver->driver_name;
													$driver_idcard = $driver->driver_idcard;
												}
											}
										}

										foreach ($driverimage as $driverdata) {
										 if ($datanya->change_driver_id == $driverdata['driveridcard']) {
												$driverimagefix = $driverdata['driverimage'];
											}
									 }

							?>
							  <tr>
								<td valign="top" style="text-align:center;font-size:12px;">
								  <?php echo $no; ?>
								</td>
								<td valign="top" style="text-align:center;font-size:12px;">
									<?php echo $vehicle_no; ?>
								</td>
								<td valign="top" style="text-align:center;font-size:12px">
									<?php echo $vehicle_name; ?>
								</td>
								<td valign="top" style="text-align:center;font-size:12px">
								  <?php echo $driver_idcard; ?>
								</td>
								<td valign="top" style="text-align:center;font-size:12px">
								  <?php echo $driver_name; ?>
								</td>
								<td valign="top" style="text-align:center;font-size:12px">
									<img src="<?php echo base_url().$this->config->item("dir_photo").$driverimagefix;?>" width="100px;" height="100px;">
								</td>
								<td valign="top" style="text-align:center;font-size:12px">
								  <?php echo date("d-m-Y H:i:s", strtotime($datanya->change_driver_time." +1hours"));?>
								</td>
							  </tr>
						<?php $no++;    }?>

                        <?php }else{ ?>
                          <tr>
                            <td colspan="4">No Available Data</td>
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
