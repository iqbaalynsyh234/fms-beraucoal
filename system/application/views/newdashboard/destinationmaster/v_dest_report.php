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
			<header class="panel-heading panel-heading-blue">DESTINATION REPORT</header>
				<div class="panel-body" id="bar-parent10">
          <a href="javascript:void(0);" id="export_xcel" type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect m-b-10 btn-circle btn-info"><small>Export to Excel</small></a><br><br>
					<?php if (count($data) == 0) {
							echo "<p>No Data</p>";
					}else{ ?>
							<div id="isexport_xcel">
  							<table class="table">
                  <thead>
            				<tr>
                      <th>No</th>
            					<th>Vehicle</td>
            					<th>Driver</th>
            					<th>Dest Name</th>
            					<th>Address</th>
                      <th>Dest Date</th>
            				</tr>
            			</thead>
  								<tbody>
                    <?php
                    $count = sizeof($data);
                    $no = 1;
                    if ($count > 0) {
                      for ($i=0; $i < $count; $i++) {?>
                        <tr>
                          <td><?php echo $no ?></td>
                          <td><?php echo $data[$i]['dest_vehicle_no'] ?></td>
                          <td><?php echo $data[$i]['dest_driver_name'] ?></td>
                          <td><?php echo $data[$i]['dest_name'] ?></td>
                          <td><?php echo $data[$i]['dest_address'] ?></td>
                          <td><?php echo date("d-m-Y", strtotime($data[$i]['dest_endshowing_date'])) ?></td>
                        </tr>
                      <?php
                      $no++;
                        }
                      }else { ?>
                        <tr>
                          <td>Data is empty</td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                        </tr>
                      <?php } ?>
  								</tbody>
  							</table>
							</div>
					<?php } ?>
				</div>
		</div>
	</div>
</div>
