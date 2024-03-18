  <div class="row">
    <div class="col-md-8">
      <table class="table table-striped" style="font-size:16px;">
        <tr>
          <td>
            <i class="fa fa-car">
              <span id="alertvehicle"><?php echo $content[0]['alarm_report_vehicle_no'].' '.$content[0]['alarm_report_vehicle_name'] ?></span>
            </i>
          </td>
          <td>
            <i class="fa fa-warning">
              <?php
              $alarmreportnamefix = "";
              $alarmreporttype = $content[0]['alarm_report_type'];
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
                  $alarmreportnamefix = $content[0]['alarm_report_name'];
                }
               ?>
              <span id="alerttype" style="color:red; font-size:14px;"><?php echo $alarmreportnamefix ?></span><br>
            </i>
          </td>
        </tr>

        <tr>
          <td>
            <i class="fa fa-clock-o">
              <span id="alerttime"><?php echo date("d-m-Y H:i:s", strtotime($content[0]['alarm_report_datetime_cr'])) ?></span>
            </i>
          </td>
          <td>
            <i class="fa fa-book">
              <span id="alerttime"><?php echo $content[0]['alarm_report_remark_manual'] ?></span>
            </i>
          </td>
        </tr>
      </table>
    </div>

    <div class="col-md-4">
      <table class="table table-striped" style="font-size:12px;">
        <tr>
          <td>
            <img src="<?php echo $imagealertid ?>" height="300px" width="300px">
          </td>
        </tr>
      </table>
    </div>

  </div>




<!-- <div class="row">
  <div class="col-md-6">
    <div class="row">
      <div class="col-md-12">
        <table class="table table-striped" style="font-size:12px;">
          <tr>
            <td>
              <i class="fa fa-car">
                <span id="alertvehicle"><?php echo $content[0]['alarm_report_vehicle_no'].' '.$content[0]['alarm_report_vehicle_name'] ?></span>
              </i>
            </td>
            <td>
              <i class="fa fa-warning">
                <?php
                $alarmreportnamefix = "";
                $alarmreporttype = $content[0]['alarm_report_type'];
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
                    $alarmreportnamefix = $content[0]['alarm_report_name'];
                  }
                 ?>
                <span id="alerttype" style="color:red; font-size:14px;"><?php echo $alarmreportnamefix ?></span><br>
              </i>
            </td>
          </tr>

          <tr>
            <td>
              <i class="fa fa-clock-o">
                <span id="alerttime"><?php echo date("d-m-Y H:i:s", strtotime($content[0]['alarm_report_datetime_cr'])) ?></span>
              </i>
            </td>
          </tr>

        </table>
      </div>
    </div>

  </div>

  <div class="col-md-6">
    <div class="row">
      <div class="col-md-12">
        <img src="<?php echo $imagealertid ?>" height="300px" width="auto">
      </div>
    </div>
  </div>
</div> -->
