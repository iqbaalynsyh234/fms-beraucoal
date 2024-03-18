<br>
<div class="row" style="margin-left:0%; margin-right:auto;">
  <div class="col-md-12">

    <!-- <video width="400" controls>
      <source src="http://103.253.107.212:6604/3/5?DownType=1&jsession=81b4ef2eadab42fca3e6dde9176d46f2&DevIDNO=142045257806&FILELOC=1&FILESVR=0&FILECHN=1&FILEBEG=88&FILEEND=147&PLAYIFRM=0&PLAYFILE=;0;1;22;1;26;88;147;0;0_0_0_0&PLAYBEG=0&PLAYEND=0&PLAYCHN=0&YEAR=22&MON=1&DAY=26" type="video/mp4">
        DownTask
    </video> -->

      <table class="table table-striped" style="font-size:12px;">
        <tr>
          <td>
            <i class="fa fa-car">
              <span id="alertvehicle"><?php echo $contentdata[0]['alarm_report_vehicle_no'].' '.$contentdata[0]['alarm_report_vehicle_name'] ?></span>
            </i>
          </td>
          <td>
            <i class="fa fa-warning">
              <span id="alerttype" style="color:red; font-size:14px;"><?php echo $contentdata[0]['alarm_report_name'] ?></span><br>
            </i>
            <i class="fa fa-warning">
              <?php
              if ($geofence_speed_limit == 0) {?>

              <?php }else if($speed > $geofence_speed_limit) {?>
                <span id="alerttypeoverspeed" style="color:red; font-size:14px;">
                  Overspeed in : <?php echo $geofence_name ?>
                </span><br>
                <span id="alerttypeoverspeed" style="color:red; font-size:14px;">
                  Limit : <?php echo $geofence_speed_limit." Kph"; ?>
                </span><br>
                <span id="alerttypeoverspeed" style="color:black; font-size:14px;">
                  Jalur : <?php echo $jalur ?>
                </span>
              <?php }?>
            </i>
          </td>

          <td>
            <i class="fa fa-dashboard">
              <span id="alertspeed"><?php echo $speed." (KM / H)" ?></span>
            </i>
          </td>
          <td>
            <i class="fa fa-clock-o">
              <span id="alerttime"><?php echo date("d-m-Y H:i:s", strtotime($contentdata[0]['alarm_report_end_time'])+60*60) ?></span>
            </i>
          </td>

          <td>
            <i class="fa fa-map-marker">
              <?php
              $coordstart = $contentdata[0]['alarm_report_coordinate_start'];
                if (strpos($coordstart, '-') !== false) {
                  $coordstart = $coordstart;
                }else {
                  $coordstart = "-".$coordstart;
                }
                 ?>
              <span id="alertcoord"><a href='http://maps.google.com/maps?z=12&t=m&q=loc:<?php echo $coordstart ?>' target='_blank'><?php echo $coordstart ?></a></span>
            </i>
          </td>

          <td>
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
          </td>
        </tr>
      </table>
  </div>
</div>

<div class="row" style="margin-left:0%; margin-right:auto;">
  <div class="col-md-12">
    <table class="table table-striped" style="font-size:12px;">
      <tr>
        <td>
          <video width='500px' height='500px;' style="margin-top:-15%; margin-left:22%; margin-right:auto;" controls>
            <source src='<?php echo $videourl ?>' type='video/mp4'>
          </video>
        </td>

        <td>
          <img src='<?php echo $imageurl ?>' width='500px' height='390px;' style="margin-top:2%;">
        </td>

      </tr>
    </table>
  </div>
</div>
