<style media="screen">
  .table thead tr th {
    font-size: 10px;
    font-weight: 600;
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
            <header class="panel-heading" style="background-color: #221f1f; color:white;">RESULT</header>
            <div class="panel-body" id="bar-parent10" style=" overflow-x:auto;">

              <div class="col-lg-4 col-sm-4">
								<a href="javascript:void(0);" id="export_xcel" type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect m-b-10 btn-circle btn-default"><small>Export to Excel</small></a>
							</div>

							<div id="isexport_xcel">
                  <table id="example1" class="table table-striped" style="font-size:10px;">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Vehicle No</th>
                        <th>Company</th>
                        <th>Violation</th>
                        <th>Location</th>
                        <th>Shift</th>
                        <th>Jalur</th>
                        <th>Week</th>
                        <th>Month</th>
                        <th>Jalur</th>
                        <th>Coordinate</th>
                        <th>Level</th>
                        <th>Speed (Kph)</th>
                        <th>Speed Limit</th>
                        <th>Geofence</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if (sizeof($data) > 0) {?>
                        <?php for ($i=0; $i < sizeof($data); $i++) {?>
                          <tr>
                            <td><?php echo ($i+1) ?></td>
                            <td><?php echo date("Y-m-d", strtotime($data[$i]['gps_time'])) ?></td>
                            <td><?php echo date("H:i:s", strtotime($data[$i]['gps_time'])) ?></td>
                            <td><?php echo $data[$i]['vehicle_no'] ?></td>
                            <td>
                              <?php for ($j=0; $j < sizeof($rcompany); $j++) {
                                if ($rcompany[$j]->company_id == $data[$i]['vehicle_company']) {
                                  echo $rcompany[$j]->company_name;
                                }
                              } ?>
                            </td>
                            <td><?php echo $data[$i]['violation'] ?></td>
                            <td><?php echo $data[$i]['position'] ?></td>
                            <td>
                              <?php
                                $jam = date("H:i:s", strtotime($data[$i]['gps_time']));
                                  if ($jam >= "06:00:00" && $jam <= "18:00:00") {
                                    echo "Shift 1";
                                  }else {
                                    echo " Shift 2";
                                  }
                               ?>
                            </td>
                            <td><?php echo $data[$i]['jalur_name'] ?></td>
                            <td>
                              <?php
                              $ddate = date("Y-m-d", strtotime($data[$i]['gps_time']));
                              $duedt = explode("-", $ddate);
                                $date  = mktime(0, 0, 0, $duedt[1], $duedt[2], $duedt[0]);
                                $week  = (int)date('W', $date);
                                echo $week;
                               ?>
                            </td>
                            <td><?php echo date("F", strtotime($data[$i]['gps_time'])) ?></td>
                            <td><?php echo $data[$i]['jalur_name'] ?></td>
                            <td>
                              <a href="https://maps.google.com/?q=<?php echo $data[$i]['gps_latitude_real'].','.$data[$i]['gps_longitude_real'] ?>" target="_blank"><?php echo $data[$i]['gps_latitude_real'].','.$data[$i]['gps_longitude_real'] ?></a>
                            </td>
                            <td><?php echo $data[$i]['violation_level'] ?></td>
                            <td><?php echo $data[$i]['gps_speed'] ?></td>
                            <td>
                              <?php
                                if (isset($data[$i]['gps_speed_limit'])) {?>
                                    <?php echo $data[$i]['gps_speed_limit'];  ?>
                                <?php }
                               ?>
                           </td>

                           <td>
                               <?php
                                 if (isset($data[$i]['geofence'])) {?>
                                     <?php echo $data[$i]['geofence'];  ?>
                                 <?php }
                                ?>
                            </td>
                          </tr>
                        <?php } ?>
                      <?php }else { ?>
                        <tr>
                          <td>Data is empty</td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
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
            </div>
      		</div>
        </div>
      </div>
