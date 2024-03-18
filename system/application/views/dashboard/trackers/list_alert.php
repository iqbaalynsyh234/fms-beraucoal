<!-- start sidebar menu -->
<div class="sidebar-container">
  <?=$sidebar;?>
</div>
<!-- end sidebar menu -->

<!-- start page content -->
<div class="page-content-wrapper">
  <div class="page-content">
    <div class="row">
      <div class="col-md-12" id="formtabledevicealert">
              <div class="card card-topline-aqua">
                  <div class="card-head">
                      <header>Device Alert</header>
                      <div class="tools">
                        <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
                        <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                        <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                      </div>
                  </div>
                  <div class="card-body">
                    <div class="table-scrollable">
                      <table id="example4" class="table table-striped table-bordered table-hover full-width" style="font-size: 12px;">
                        <thead>
                            <tr>
                              <th>No</th>
                              <th>Vehicle</th>
                              <th>Alert Name</th>
      												<th width="40%">Information</th>
                              <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                          <?php $devicealertarray = array(
      											"dt"    => "Cut Power Alert",
      											"BO010" => "Cut Power Alert",
      											"BO012" => "Panic Button (SOS)"
      										);
      										 ?>
                          <?php $no = 1; foreach ($devicealert as $rowalert) {?>
                            <tr>
                              <td><?php echo $no; ?></td>
                              <td><?php echo $rowalert['vehicle_name'].' '. $rowalert['vehicle_no'] ; ?></td>
                              <td>
      													<?php
      													 	if (in_array($rowalert['gps_alert'], $devicealertarray)) {
      													 		echo $devicealertarray[$rowalert['gps_alert']]."<br>";
      													 	}else {
      													 		echo $rowalert['gps_alert']."<br>";
      													 	}
      													 ?>
      												</td>
      												<td>
      													<?php echo $rowalert['address'].'<br>'; ?>
      													<?php
      														if ($rowalert['gps_status'] == "A") {
      															echo "GPS OK"."<br>";
      														}else {
      															echo "GPS NOT OK"."<br>";
      														}
      													 ?>

      													 <!-- <?php
       														if ($rowalert['gps_speed'] > 0) {
       															echo "Engine On"."<br>";
       														}else {
       															echo "Engine Off"."<br>";
       														}
       													 ?> -->
      													 <?php echo "Speed : ".number_format($rowalert['gps_speed']*1.852, 0, "",".")." Kph </br>" ?>
      													<a href="https://maps.google.com/?q=<?php echo $rowalert['vehicle_lat'].','.$rowalert['vehicle_lng'] ?>" target="_blank">
      														<?php echo $rowalert['vehicle_lat'].','.$rowalert['vehicle_lng'] ?>
      													</a>
      												</td>
                              <td><?php echo $rowalert['vehicle_alert_datetime'] ?></td>
                            </tr>
                          <?php $no++; } ?>
                        </tbody>
                      </table>
                      </div>
                  </div>
              </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript" src="js/script.js"></script>
<script src="<?php echo base_url()?>assets/dashboard/assets/js/jquery-1.7.1.min.js" type="text/javascript"></script>
