<div class="page-content-wrapper">
  <!-- <div class="page-content"> -->
    <div class="col-sm-12 col-md-4 col-lg-3">
      <button class="btn btn-info" id="notifdevicestatus" style="display:none;"></button>
    </div>
    <div class="row">
      <div class="col-sm-12"><br>
        <!-- <h4>Realtime Monitoring</h4> -->
        <div class="card-box">
          <div class="card-body" style="height: 95%;" id="cardbodyformode">
            <div class="row">
              <?php for ($i=0; $i < sizeof($datafix); $i++) {?>
                <div class="col-md-6">
                  <label style="font-size:12px;" for=""><?php echo $datafix[$i]['vehicle_no'].' '.$datafix[$i]['vehicle_name'].';'; ?></label><br>
                    <span style="font-size:12px;" id="liveinfodriver<?php echo $datafix[$i]['vehicle_id'];?>"></span>
                    <span style="font-size:12px;" id="liveinfogpstime<?php echo $datafix[$i]['vehicle_id'];?>"></span>
                    <span style="font-size:12px;" id="liveinfoengine<?php echo $datafix[$i]['vehicle_id'];?>"></span>
                    <span style="font-size:12px;" id="liveinfospeed<?php echo $datafix[$i]['vehicle_id'];?>"></span>
                    <span style="font-size:12px;" id="liveinfoposition<?php echo $datafix[$i]['vehicle_id'];?>"></span>
                  <iframe src="<?php echo $datafix[$i]['urlfix_id']?>" width="400" height="400" frameborder="0" style="border:0;"></iframe>
                </div>
              <?php } ?>
            </div>
          </div>
          </div>
        </div>
      </div>
    <!-- </div> -->
  </div>
</div>
<!-- end page content -->

<script type="text/javascript">
var intervalstart;
var objectnumberfix      = 1;
var objectnumber         = 0;
var lastpointer;
var bibarea              = ["KM", "POOL", "ST", "ROM", "PIT", "PORT"];
var data                 = '<?php echo json_encode($datafix) ?>';
var obj                  = JSON.parse(data);
var totaldata            = obj.length;
var totaldatafix         = totaldata - 1;
console.log("obj : ", obj);

  intervalstart = setInterval(simultango, 15000);

  function simultango() {
    if (objectnumberfix == (obj.length - 1)) {
      objectnumberfix = 0;
      objectnumber    = 0;
      // console.log("sama");
    }else {
      // console.log("tak sama");
      if (objectnumber == 0) {
        objectnumber    = objectnumber + 1;
        objectnumberfix = 0;
      }else {
        objectnumberfix = objectnumber;
        objectnumber    = objectnumber + 1;
      }
    }

    console.log("totaldatafix : ", totaldatafix);
    console.log("objectnumberfix : ", objectnumberfix);
    console.log("vehicle yg dicek : ", obj[objectnumberfix].vehicle_no);
    // console.log("obj di simultango : ", obj[objectnumberfix].vehicle_device);
      jQuery.post("<?=base_url();?>map/lastinfo", {
          device: obj[objectnumberfix].vehicle_device,
          lasttime: 100
        },
        function(r) {
          // console.log("response jika obj banyak : ", r);
          console.log("response jika obj banyak : ", r.vehicle);
          // console.log("response vdevice jika obj banyak : ", r.vehicle.vehicle_device);
          updateinfodetail(r.vehicle);
        }, "json");
  }

  function updateinfodetail(value){
    console.log("ini di updateinfodetail value : ", value);
    console.log("ini di updateinfodetail value vehicle_mv03 : ", value.vehicle_mv03);

    var vehicle_mv03 = value.vehicle_mv03;
      if (vehicle_mv03 == "020200360001") {
        console.log("Ini Evalia");
      }else {
        var drivervalue = value.driver;
          if (drivervalue) {
            if (drivervalue.includes("-")) {
              var drivervaluewithmin = drivervalue.split("-");
              var drivernamefix      = driver[1];
            }else {
              var drivernamefix = value.driver;
            }
          }else {
            var drivernamefix = value.driver;
          }

        var statusengine = "";
          if (value.status1 == true) {
            statusengine = "ON";
          }else {
            statusengine = "OFF";
          }

        var num         = Number(value.gps.gps_speed_fmt);
        var roundstring = num.toFixed(0);
        var rounded     = Number(roundstring);

        var addresssplit = value.gps.georeverse.display_name.split(" ");
        var inarea       = value.gps.georeverse.display_name.split(",");
        console.log("addresssplit : ", addresssplit);
        console.log("inarea : ", inarea);

        var addressfix = bibarea.includes(addresssplit[0]);
        if (addressfix) {
          var addressfix = inarea[0];
        }else {
          var addressfix = value.gps.georeverse.display_name;
        }

        $("#liveinfodriver"+value.vehicle_id).html("Driver : "+drivernamefix+";");
        $("#liveinfogpstime"+value.vehicle_id).html("Gps Time : "+ value.gps.gps_date_fmt + " " + value.gps.gps_time_fmt + ";");
        $("#liveinfoengine"+value.vehicle_id).html("Engine : "+statusengine+";");
        $("#liveinfospeed"+value.vehicle_id).html("Speed : "+rounded+";");
        $("#liveinfoposition"+value.vehicle_id).html("Position : "+addressfix+";");
      }
  }
</script>
