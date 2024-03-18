<br>
<div class="row" style="margin-left:4px; margin-right:4px; margin-bottom:12px; background-color:#f2f2f2; padding-top:10px; padding-bottom:10px;">

  <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12" style="padding-bottom: 2px; margin-bottom:2px;">

    <!-- <i class="fa fa-car"> -->
      <span id="alertvehicle"><?php echo $contentdata[0]['alarm_report_vehicle_no'] . ' ' . $contentdata[0]['alarm_report_vehicle_name'] ?></span>
    <!-- </i> -->
  </div>
  <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12" style="padding-bottom: 2px; margin-bottom:2px; padding-right:0px;">
    <div class="fa fa-warning">
      <?php
        $alarmreportnamefix = "";
        $alarmreporttype = $contentdata[0]['alarm_report_type'];
          if ($alarmreporttype == 626) {
            $alarmreportnamefix = "Driver Undetected Alarm Level One Start";
          }elseif ($alarmreporttype == 627) {
            $alarmreportnamefix = "Driver Undetected Alarm Level Two Start";
          }elseif ($alarmreporttype == 702) {
            $alarmreportnamefix = "Distracted Driving Alarm Level One Start";
          }elseif ($alarmreporttype == 703) {
            $alarmreportnamefix = "Distracted Driving Alarm Level Two Start";
          }elseif ($alarmreporttype == 752) {
            $alarmreportnamefix = "Distracted Driving Alarm Level One End";
          }elseif ($alarmreporttype == 753) {
            $alarmreportnamefix = "Distracted Driving Alarm Level Two End";
          }else {
            $alarmreportnamefix = $contentdata[0]['alarm_report_name'];
          }
       ?>
      <span id="alerttype" style="color:red; font-size:12px;"><?php echo str_replace("Start", "", $alarmreportnamefix); ?></span><br>
    </div>
    <?php
    if ($geofence_speed_limit == 0) {
    } else if ($speed > $geofence_speed_limit) { ?>
      <div class="fa fa-warning">
        <span id="alerttypeoverspeed" style="color:red; font-size:14px;">
          Overspeed in : <?php echo $geofence_name ?>
        </span><br>
        <span id="alerttypeoverspeed" style="color:red; font-size:14px;">
          Limit : <?php echo $geofence_speed_limit . " Kph"; ?>
        </span><br>
        <span id="alerttypeoverspeed" style="color:black; font-size:14px;">
          Jalur : <?php echo $jalur ?>
        </span>
      </div>
    <?php } ?>
  </div>
  <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12" style="padding-bottom: 2px; margin-bottom:2px;">
    <div class="fa fa-dashboard">
      <span id="alertspeed"><?php echo $speed . " (KM / H)" ?></span>
    </div>
  </div>
  <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12" style="padding-bottom: 2px; margin-bottom:2px;">
    <div class="fa fa-clock-o">
      <!-- <span id="alerttime"><?php echo date("d-m-Y H:i:s", strtotime($contentdata[0]['alarm_report_start_time']) + 60 * 60) ?></span> -->
      <span id="alerttime"><?php echo date("d-m-Y H:i:s", strtotime($contentdata[0]['alarm_report_start_time'])) ?></span>
    </div>
  </div>
  <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12" style="padding-bottom: 2px; margin-bottom:2px;">
    <div class="fa fa-map-marker">
      <?php
      $coordstart = $contentdata[0]['alarm_report_coordinate_start'];
      if (strpos($coordstart, '-') !== false) {
        $coordstart = $coordstart;
      } else {
        $coordstart = "-" . $coordstart;
      }
      ?>
      <span id="alertcoord"><a href='http://maps.google.com/maps?z=12&t=m&q=loc:<?php echo $coordstart ?>' target='_blank'><?php echo $coordstart ?></a></span>
    </div>
  </div>
  <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12" style="padding-bottom: 2px; margin-bottom:2px;">
    <i class="fa fa-map">
      <span id="alerttime">
        <?php
        if (isset($position)) {
          $position = explode(",", $position);
          echo $position[0];
        }
        ?>
      </span>
    </i>
  </div>
</div>

<div class="row justify-content-center" style="margin-left:4px; margin-right:4px; margin-bottom:12px; background-color:#f2f2f2; padding-top:10px; padding-bottom:10px;">
  <div class="col-lg-5 col-md-6 col-sm-10 col-xs-12" style="padding-bottom: 2px; margin-bottom:2px;">
    <video controls style="padding-bottom: 2px; margin-bottom:2px; width:100%; height:auto; ">
      <source src='<?= $videourl; ?>' type='video/mp4'>
    </video>

  </div>
  <div class="col-lg-5 col-md-6 col-sm-10 col-xs-12" style="padding-bottom: 2px; margin-bottom:2px;">
    <img src="<?php echo $imageurl; ?>" style="padding-bottom: 2px; margin-bottom:2px; width:100%; height:auto;">
  </div>
</div>
