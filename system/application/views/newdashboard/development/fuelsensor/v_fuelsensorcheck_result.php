<style media="screen">
  .table thead tr th {
    font-size: 10px;
    font-weight: 600;
  }

  #fuel-history{
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
            <header class="panel-heading" id="fuel-history">RESULT FUEL SENSOR HISTORY - DEVELOPMENT</header>
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
                        <th>Hour</th>
                        <th>Vehicle</th>
                        <th>Speed</th>
                        <th>Liter</th>
                        <!-- <th>Last Update MDVR</th> -->
                        <th>Status</th>
                        <th align="center">TOTAL OK</th>
                        <th align="center">Total NOT OK</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if (sizeof($data) < 1) {?>
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
                        </tr>
                      <?php }else {?>
                        <?php
                        $childawal = 0;
                          for($i=0;$i<count($data);$i++) {?>
                            <?php
                            $totalfuelok  = 0;
                            $totalfuelnotok = 0;
                              for ($j=0; $j < sizeof($data[$i]['data']); $j++) {
                                for ($k=0; $k < sizeof($data[$i]['data']); $k++) {
                                  $speed = $data[$i]['data'][$k]['fuelcheck_speed'];
                                  $liter = $data[$i]['data'][$k]['fuelcheck_liter'];
                                    if ($speed == 0 && $liter > 0) {
                                      $totalfuelok += 1;
                                    }else {
                                      $totalfuelnotok += 1;
                                    }
                                    // print_r("totalfuelok : " . $totalfuelok . " ");
                                }
                                ?>
                                <tr>
                                <td width="2%"><?=$i+1?></td>
                                <td><?=date("Y-m-d", strtotime($data[$i]['data'][$j]['fuelcheck_date_real']));?></td>
                                <td><?=date("H:i:s", strtotime($data[$i]['data'][$j]['fuelcheck_date_real']));?></td>
                                <td><?=$data[$i]['data'][$j]['fuelcheck_vehicle_no'];?></td>
                                <td><?=$data[$i]['data'][$j]['fuelcheck_speed'];?></td>
                                <td>
                                  <?=
                                    $last_mdvrupdate = $data[$i]['data'][$j]['fuelcheck_liter'];
                                  ?>
                                </td>
                                <td><?=$data[$i]['data'][$j]['fuelcheck_status'];?></td>
                                  <?php
                                      if ($j == 0) {?>
                                        <td align="center" rowspan="<?php echo sizeof($data[$i]['data']) ?>" style="background:white; color:black;">
                                          <?php echo $totalfuelok; ?>
                                        </td>

                                        <td align="center" rowspan="<?php echo sizeof($data[$i]['data']) ?>" style="background:white; color:black;">
                                          <?php echo $totalfuelnotok; ?>
                                        </td>
                                      <?php }
                                   ?>
                              </tr>
                              <?php }
                             ?>

                        <?php } ?>
                      <?php } ?>
                    </tbody>
                  </table>
              </div>
            </div>
      		</div>
        </div>
      </div>
