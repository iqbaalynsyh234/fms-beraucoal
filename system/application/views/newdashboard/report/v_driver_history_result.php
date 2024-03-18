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
										<th width="3%" valign="top" style="text-align:center;">No</td>
										<th width="10%" valign="top" style="text-align:center;">Vehicle</th>
										<th width="5%" valign="top" style="text-align:center;">Driver</th>
										<th width="7%" valign="top" style="text-align:center;">Creator</th>
										<th width="11%" valign="top" style="text-align:center;">Submited Date</th>
									</tr>
								</thead>
								<tbody>
                    <?php
                         if (isset($data)) {
                           $no = 1;
                           foreach ($data as $datanya) {?>
                      <tr>
                        <td valign="top" style="text-align:center;">
                          <?php echo $no; ?>
                        </td>
                        <td valign="top" style="text-align:center;">
                          <?php echo $datanya->driver_history_vehicle_no . " - " . $datanya->driver_history_vehicle_name; ?>
                        </td>
                        <td valign="top" style="text-align:center;">
                          <?php echo $datanya->driver_history_driver_name; ?>
                        </td>
                        <td valign="top" style="text-align:center;">
                          <?php echo $datanya->driver_history_username; ?>
                        </td>
                        <td valign="top" style="text-align:center;">
                          <?php echo date("d-m-Y H:i:s", strtotime($datanya->driver_history_tanggal_submit));?>
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
