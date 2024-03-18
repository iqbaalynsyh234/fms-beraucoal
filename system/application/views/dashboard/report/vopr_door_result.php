<script src="<?php echo base_url();?>assets/js/jsblong/jquery.table2excel.js"></script>
<script>
  jQuery(document).ready(
    function() {
      jQuery("#export_xcel").click(function() {
        window.open('data:application/vnd.ms-excel,' + encodeURIComponent(jQuery('#isexport_xcel').html()));
      });
    }
  );
</script>


<div class="col-lg-6 col-sm-6">
  <input id="btn_hide_form" class="btn btn-circle btn-danger" title="" type="button" value="Hide Form" onclick="javascript:return option_form('hide')" />
  <input id="btn_show_form" class="btn btn-circle btn-success" title="" type="button" value="Show Form" onClick="javascript:return option_form('show')" style="display:none" />
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
                      <th style="text-align:center;" width="3%">No</td>
                        <th style="text-align:center;" width="10%">Vehicle</th>
                        <th style="text-align:center;" width="7%">Start Time</th>
                        <th style="text-align:center;" width="9%">End Time</td>
                          <th style="text-align:center;" width="9%">Door</th>
                          <th style="text-align:center;" width="7%">Duration</th>
                          <th style="text-align:center;" width="20%">Location Start</th>
                          <th style="text-align:center;" width="20%">Location End</th>
                          <th style="text-align:center;" width="5%">Total Data GPS</th>
                    </tr>
                  </thead>
                  <tbody>


                    <?php
		if(isset($data) && (count($data) > 0)){
		for ($i=0;$i<count($data);$i++)
				{
				?>
                      <tr>
                        <td style="text-align:center;font-size:12px;">
                          <?php echo $i+1;?>
                        </td>
                        <td style="text-align:center;font-size:12px;">
                          <?php echo $data[$i]->door_vehicle_name." ".$data[$i]->door_vehicle_no;?>
                        </td>
                        <td style="text-align:center;font-size:12px;">
                          <?php echo $data[$i]->door_start_time;?>
                        </td>
                        <td style="text-align:center;font-size:12px;">
                          <?php echo $data[$i]->door_end_time;?>
                        </td>
                        <td style="text-align:center;font-size:12px;">
                          <?php echo $data[$i]->door_status;?>
                        </td>
                        <td style="text-align:center;font-size:12px;">
                          <?php echo $data[$i]->door_duration;?>
                            <br /><small><?php echo $data[$i]->door_duration_sec;?> s</small></td>
                        <td style="text-align:center;font-size:12px;">
                          <?php $geofence_start = strlen($data[$i]->door_geofence_start);
							if (strlen($geofence_start == 1)){
							$geofence_start_name = "";?>
                            <strong><font color="red"><?php echo $geofence_start_name;?></font></strong>
                            <br />
                            <?php echo $data[$i]->door_location_start;?>
                              <br />
                              <?php } ?>

                                <?php
							if (strlen($geofence_start > 1)){
							$geofence_start_name = $data[$i]->door_geofence_start;?>
                                  <strong><font color="red"><?php echo $geofence_start_name;?></font></strong>
                                  <br />
                                  <?php echo $data[$i]->door_location_start;?>
                                    <br />
                                    <?php } ?>
                                      <strong><font color="red"><?php echo $data[$i]->door_coordinate_start;;?></font></strong>
                        </td>

                        <td style="text-align:center;font-size:12px;">
                          <?php $geofence_end = strlen($data[$i]->door_geofence_end);
							if (strlen($geofence_end == 1)){
							$geofence_end_name = "";?>
                            <strong><font color="red"><?php echo $geofence_end_name;?></font></strong>
                            <br />
                            <?php echo $data[$i]->door_location_end;?>
                              <br />
                              <?php } ?>

                                <?php
							if (strlen($geofence_end > 1)){
							$geofence_end_name = $data[$i]->door_geofence_end;?>
                                  <strong><font color="red"><?php echo $geofence_end_name;?></font></strong>
                                  <br />
                                  <?php echo $data[$i]->door_location_end;?>
                                    <br />
                                    <?php } ?>
                                      <strong><font color="red"><?php echo $data[$i]->door_coordinate_end;;?></font></strong>
                        </td>
                        <td style="text-align:center;font-size:12px;">
                          <?php echo $data[$i]->door_totaldata;?>
                        </td>
                      </tr>
                      <?php
				}
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
