
<style media="screen">
  .table thead tr th {
    font-size: 14px;
    font-weight: 600;
  }

  #result{
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

    <?php if ($this->session->flashdata('notif')) {?>
      <div class="alert alert-success" id="notifnya" style="display: none;"><?php echo $this->session->flashdata('notif');?></div>
    <?php }?>
      <div class="row">
        <div class="col-md-12" id="tablevehicles">
          <div class="panel" id="panel_form">
            <header class="panel-heading" id="result">RESULT</header>
            <div class="panel-body" id="bar-parent10" style=" overflow-x:auto;">

              <div class="col-lg-4 col-sm-4">
								<a href="javascript:void(0);" id="export_xcel" type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect m-b-10 btn-circle btn-default"><small>Export to Excel</small></a>
							</div>

							<div id="isexport_xcel">
                <table class="table table-striped" style="font-size:14px;">
                  <tr>
                    <td align="center"><b>Total GPS Installed</b></td>
                    <td align="center"><b>Total MDVR Installed</b></td>
                    <td align="center"><b>Total Fuel Sensor Installed</b></td>
                  </tr>
                  <tr>
                    <td align="center"><?php echo $total_gps ?></td>
                    <td align="center"><?php echo $total_mdvr ?></td>
                    <td align="center"><?php echo $total_fuelsensor ?></td>
                  </tr>
                </table>
                  <table id="example1" class="table table-striped" style="font-size:14px;">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Vehicle</th>
                        <th>Type</th>
                        <th>GPS</th>
                        <th>MDVR</th>
                        <th>Fuel Sensor</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if (isset($data)) {
                        if (sizeof($data) > 0) {?>
                          <?php for ($i=0; $i < sizeof($data); $i++) {?>
                            <tr>
                              <td><?php echo $i+1 ?></td>
                              <td><?php echo $data[$i]['vehicle_no'] ?></td>
                              <td><?php echo $data[$i]['vehicle_name'] ?></td>
                              <td>
                                Yes
                              </td>
                              <td>
                                <?php
                                  $mdvr = $data[$i]['vehicle_mv03'];
                                    if ($mdvr != 0000) {
                                      echo "Yes";
                                    }else {
                                      echo "No";
                                    }
                                  ?>
                              </td>
                              <td>
                                <?php
                                  $fuel_sensor = $data[$i]['vehicle_sensor'];
                                    if ($fuel_sensor != "Ultrasonic") {
                                      echo "No";
                                    }else {
                                      echo "Yes";
                                    }
                                  ?>
                              </td>
                            </tr>
                          <?php } ?>
                        <?php }else {
                          echo "Data is empty";
                        }
                      }else {
                        echo "Data is empty";
                      } ?>
                    </tbody>
                  </table>
              </div>
            </div>
      		</div>
        </div>
      </div>
