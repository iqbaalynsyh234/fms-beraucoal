<style media="screen">
.custom-map-control-button {
  appearance: button;
  background-color: #fff;
  border: 0;
  border-radius: 2px;
  box-shadow: 0 1px 4px -1px rgba(0, 0, 0, 0.3);
  cursor: pointer;
  margin: 10px;
  height: 40px;
  font: 400 18px Roboto, Arial, sans-serif;
  overflow: hidden;
}

div#realtimealertshow{
  position: fixed;
  width: 65%;
  background-color: white;
  min-height: 110px;
  max-height: 140px;
  overflow-y: auto;
  overflow-x: hidden;
  bottom: 0px;
  left: 17.5%;
  right: 0px;
  margin-bottom: 0px;
  margin-left: 5%

  /* margin-top: 30.5%;
  margin-left: -1%;
  position: absolute;
  z-index: 9999;
  background-color: #f1f1f1;
  text-align: left;
  border: 1px solid #d3d3d3;
  width: 74%;
  height: 10px; */
}

div#modalalertsummry {
  width: 82%;
  margin-top: -33%;
  margin-left: -5%;
  overflow-x: auto;
  position: absolute;
  z-index: 9;
  background-color: #f1f1f1;
  text-align: left;
  border: 1px solid #d3d3d3;
  max-height: 300px;
}

div#modaladdcomment {
  margin-top: 8%;
  margin-left: 20%;
  overflow-x: auto;
  position: absolute;
  z-index: 9;
  background-color: #f1f1f1;
  text-align: left;
  border: 1px solid #d3d3d3;
  width: 56%;
}

div#modallistvehicle {
  /* margin-top: 5%; */
  /* margin-left: 20%; */
  max-height: 500px;
  max-width: 900px;
  overflow-x: auto;
  position: absolute;
  z-index: 9;
  background-color: #f1f1f1;
  text-align: left;
  border: 1px solid #d3d3d3;
}

#mydivheader {
  padding: 10px;
  cursor: move;
  z-index: 10;
  background-color: #2196F3;
  color: #fff;
}

.white {
  background-color: white;
}

.yellow {
  background-color: yellow;
}

.red {
  background-color: red;
  color: white;
}
</style>

<div class="sidebar-container">
  <?=$sidebar;?>
</div>

<div class="page-content-wrapper">
  <div class="page-content">
    <div class="col-sm-12 col-md-4 col-lg-3">
      <button class="btn btn-info" id="notifdevicestatus" style="display:none;"></button>
    </div>
    <div class="row">
      <div class="col-sm-12">
        <!-- <h4>Realtime Monitoring</h4> -->
        <div class="card-box">
          <div class="card-body" style="height: 95%;" id="cardbodyformode">
            <button type="button" name="button" id="btnvideomode" class="btn btn-primary" onclick="showvideomode();" style="margin-left: 82%; z-index: 1; margin-top: 1.7%; position: absolute;">
              <span class="fa fa-video-camera"></span>
            </button>
            <button type="button" name="button" id="btnmapmode" class="btn btn-success" onclick="showmapmode();" style="display: none; margin-left: 85%; z-index: 1; margin-top: 1.7%; position: absolute;">
              <span class="fa fa-map-marker"></span>
            </button><br>

            <div class="row">
              <div id="videomode" style="display:none; width: 70%; height: 400%;">
                <div>
                  <div id="streamcontent">
                    <br>
                    Please Select Vehicle First
                  </div>
                </div>
              </div>

              <div id="mapmode" style="width: 98%; height: 460%; margin-left: 1%; margin-top: -3%;">
                <div>
                  <button id="btnmaptable" type="button" class="btn btn-info btnmaptable" title="Show Table" onclick="showtableperarea('<?php echo $companyid;?>')" style="margin-left: 88%; z-index: 1; margin-top: 1.3%; position: absolute;">
                    <span class="fa fa-list"></span>
                  </button>
                   <div id="mapsnya" style="width: 103%; height: 460px; margin-top: 1%; margin-left: -1.5%;"></div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>

    <!-- REALTIME ALERT START -->
    <div id="realtimealertshow">  <!--style="display:none;"-->
      <!-- <div class="row" id="modalalertrealtime">
        <div class="col-md-12">
          <div class="card">
            <div class="card-body"> -->
              <div style="margin-left: 1%; margin-top: 1%">
                <a href="#" onclick="realtimealerttoggle();">
                  <b>Realtime Alert OVERLAY N DIRECTION MAP</b>
                </a>
                <button type="button" name="button" class="btn btn-success btn-sm" title="Realtime Alert Summary" onclick="alertsummary();">
                  <span class="fa fa-list"></span>
                </button>

                <button type="button" name="button" id="activatesound" class="btn btn-flat btn-sm" title="Sound" onclick="activatesound();">
                  <span class="fa fa-volume-up"></span>
                </button>

                <button type="button" name="button" id="activatesound2" class="btn btn-flat btn-sm" title="Sound" onclick="activatesound2();" style="display:none;">
                  <span class="fa fa-volume-off"></span>
                </button>
              </div>

              <div id="realtimealertcontent"></div>
              <div id="alertfromdevicealert" style="margin-left: 1%;"></div>
            <!-- </div>
          </div>
        </div>
      </div> -->
    </div>

    <div class="card" id="modalalertsummry" style="display: none;">
      <div class="card card-topline-yellow">
        <div class="card-body">
            <h4>
              <b>Realtime Summary Alert</b>
            </h4>
            <div class="text-right" style="margin-top: -5%;">
              <button type="button" name="button" class="btn btn-sm btn-danger" onclick="closemodalsummaryalert();">X</button>
            </div>
            <div id="summaryalertcontent" style="margin-top: 1%;"></div>
            <div id="summaryalertfromdevicealert"></div>
        </div>
      </div>
    </div>
    <!-- REALTIME ALERT END -->

  </div>
</div>

<!-- MODAL LIST VEHICLE -->
<div id="modallistvehicle" style="display: none;">
  <div id="mydivheader"></div>
  <div class="row" >
    <div class="col-md-12">
        <div class="card card-topline-yellow">
            <div class="card-head">
                <h4>List of Vehicle</h4>
                <div class="tools" style="margin-top: -40px;">
                  <!-- <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a> -->
                  <!-- <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a> -->
                  <button type="button" class="btn btn-danger" name="button" onclick="closemodallistofvehicle();">X</button>
                </div>
            </div>
            <div class="card-body">
                <table class="table" class="display" class="full-width">
                    <thead>
                        <tr>
                            <th></th>
                            <th>No</th>
                            <th>Vehicle</th>
                            <th>Driver</th>
                            <th>Information</th>
                            <!-- <th>Transporter</th> -->
                            <th>Simcard</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="autoupdaterow">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
  </div>
</div>

<!-- end page content -->


<script type="text/javascript" src="js/script.js"></script>
<script src="<?php echo base_url()?>assets/dashboard/assets/js/jquery-1.7.1.min.js" type="text/javascript"></script>

<script>
  // $("#btnmaptable").show();
  $("#showtable").hide();
  $("#modallistvehicle").hide();
  $("#modalfivereport").hide();
  // alert("Default View");
  var map, setTimeoutConst;
  var datafixnya           = "";
  var indeksglobal         = 0;
  var infoWindow           = null;
  var infoWindow2          = null;
  var infowindowonsimultan = null;
  var i;
  var marker               = [];
  var markernya            = [];
  var markers              = [];
  var markerss             = [];
  var markerpools          = [];
  var limitmobilnya;
  var laststatus           = "-";
  var laststatus2;
  var intervalstart;
  var objectnumberfix      = 1;
  var objectnumber         = 0;
  var obj;
  var infowindowkedua      = null;
  var markerpool           = [];
  var JSONpoolmaster, objpoolmaster, objpoolmasterfix;
  var overlaystatus         = 0;
  var overlaysarray         = [];
  var camdevices           = ["TK510CAMDOOR", "TK510CAM", "GT08", "GT08DOOR", "GT08CAM", "GT08CAMDOOR"];
  var bibarea              = ["KM", "POOL", "ST", "ROM", "PIT", "PORT", "POOl", "WS", "WB", "PT.BIB"];

  var car = "M17.402,0H5.643C2.526,0,0,3.467,0,6.584v34.804c0,3.116,2.526,5.644,5.643,5.644h11.759c3.116,0,5.644-2.527,5.644-5.644 V6.584C23.044,3.467,20.518,0,17.402,0z M22.057,14.188v11.665l-2.729,0.351v-4.806L22.057,14.188z M20.625,10.773 c-1.016,3.9-2.219,8.51-2.219,8.51H4.638l-2.222-8.51C2.417,10.773,11.3,7.755,20.625,10.773z M3.748,21.713v4.492l-2.73-0.349 V14.502L3.748,21.713z M1.018,37.938V27.579l2.73,0.343v8.196L1.018,37.938z M2.575,40.882l2.218-3.336h13.771l2.219,3.336H2.575z M19.328,35.805v-7.872l2.729-0.355v10.048L19.328,35.805z";

  function initialize(){
    // -6.2293867,106.6894286
    // console.log("Default View");
    // console.log("Maps Code : ", '<?php echo $maps_code; ?>');
    var mapscode = '<?php echo $maps_code; ?>';

      var vehicle    = '<?php echo json_encode($vehicledata); ?>';
      var poolmaster = '<?php echo json_encode($poolmaster); ?>';

      var bounds = new google.maps.LatLngBounds();
      var boundspool = new google.maps.LatLngBounds();

      if (datafixnya == "") {
        try {
          var datacode  = JSON.parse(vehicle);
          objpoolmaster = JSON.parse(poolmaster);
          // console.log("disini objpoolmaster: ", objpoolmaster);
        } catch (e) {
          // console.log("e : ", e);
        }
      } else {
        var datacode  = vehicle;
        objpoolmaster = poolmaster;
      }

      obj              = datacode;
      objpoolmasterfix = objpoolmaster;
      console.log("obj : ", obj);
      // console.log("objpoolmasterfix : ", objpoolmasterfix);

      map = new google.maps.Map(
       document.getElementById("mapsnya"), {
         center: new google.maps.LatLng(parseFloat(obj[0].auto_last_lat), parseFloat(obj[0].auto_last_long)),
         zoom: 11,
         mapTypeId: google.maps.MapTypeId.SATELLITE,
         options: {
           gestureHandling: 'greedy'
         }
       });


    // Add multiple markers to map
    marker, i;
     infowindow      = new google.maps.InfoWindow();
     infowindow2     = new google.maps.InfoWindow();
     infowindowkedua = new google.maps.InfoWindow();
     infowindowgif = new google.maps.InfoWindow();

    // console.log("datafinya : ", datafixnya);


    for (var x = 0; x < objpoolmasterfix.length; x++) {
      // console.log("masuk looping", x);
      var positionpool = new google.maps.LatLng(parseFloat(objpoolmasterfix[x].poi_lat), parseFloat(objpoolmasterfix[x].poi_lng));
      boundspool.extend(positionpool);
      if (objpoolmasterfix[x].poi_image) {
        var iconpool = {
          url: objpoolmasterfix[x].poi_image, // url JIKA GIF
          // path: 'assets/images/iconpulsemarker.gif',
          // scale: .5,
          anchor: new google.maps.Point(25,10),
          scaledSize: new google.maps.Size(17,17)
        };
      }else {
        var iconpool = {
          url: "http://transporter.lacak-mobil.com/assets/images/markergif.gif", // url JIKA GIF
          // path: 'assets/images/iconpulsemarker.gif',
          // scale: .5,
          anchor: new google.maps.Point(25,10),
          scaledSize: new google.maps.Size(17,17)
        };
      }

      markerpool = new google.maps.Marker({
        position: positionpool,
        map: map,
        icon: iconpool,
        title: objpoolmasterfix[x].poi_name,
        id: objpoolmasterfix[x].poi_name,
        optimized: false
      });
      markerpool.setIcon(iconpool);
      markerpools.push(markerpool);
    }

    var htmlautoupdaterow = "";
    var d     = new Date();
    var year  = d.getFullYear();
    var month = d.getMonth();
    var date  = d.getDate();
    var fixmonth = (month+1);
    var stringmonth = fixmonth.toString().length;
      if (stringmonth == 1) {
        month = "0"+fixmonth;
      }else {
        month = fixmonth;
      }
    var currdate = year+""+month+""+date;
    var expired;

    for (i = 0; i < obj.length; i++) {
      var position = new google.maps.LatLng(parseFloat(obj[i].auto_last_lat), parseFloat(obj[i].auto_last_long));
      bounds.extend(position);

      // JIKA IS_UPDATE = YES MAKA ITU MOBIL HIJAU ATAU BIRU
      // JIKA US_UPDATE = NO MAKA MOBIL ITU MERAH
      expired = obj[i].vehicle_active_date2;
      // console.log("expired : ", expired);
      // console.log("currdate : ", currdate);
      if (currdate > expired) {
        // console.log("expired gan");
      }else {
        // console.log("belum expired gan");
        var nums         = Number(obj[i].auto_last_speed);
        var roundstrings = nums.toFixed(0);
        var roundedspeed = Number(roundstrings);

        // console.log("Ritase : ", obj[i].auto_last_ritase);
        // console.log("speed : ", roundedspeed);
        // console.log("engine : ", obj[i].auto_last_engine);

        // SIKON 1 JALUR MUATAN
        // SPEED > 0
        if (obj[i].is_update == "yes") {
          laststatus = 'GPS Online';
          laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
          if (obj[i].auto_last_road == "muatan") {
            if (roundedspeed == 0 && obj[i].auto_last_engine == "ON") {
              // ICON UNGU
              var icon = {
                // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
                path: car,
                scale: .5,
                // anchor: new google.maps.Point(25,10),
                // scaledSize: new google.maps.Size(30,20),
                strokeColor: 'white',
                strokeWeight: .10,
                fillOpacity: 1,
                fillColor: '#ffff00',
                offset: '5%'
              };
            }else if (roundedspeed > 0 && obj[i].auto_last_engine == "ON") {
              // ICON HIJAU
              var icon = {
                // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
                path: car,
                scale: .5,
                // anchor: new google.maps.Point(25,10),
                // scaledSize: new google.maps.Size(30,20),
                strokeColor: 'white',
                strokeWeight: .10,
                fillOpacity: 1,
                fillColor: '#00b300',
                offset: '5%'
              };
            }else {
              // ICON BIRU
              var icon = {
                // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
                path: car,
                scale: .5,
                // anchor: new google.maps.Point(25,10),
                // scaledSize: new google.maps.Size(30,20),
                strokeColor: 'white',
                strokeWeight: .10,
                fillOpacity: 1,
                fillColor: '#ff0040',
                offset: '5%'
              };
            }
          }else {
            if (roundedspeed > 0 && obj[i].auto_last_engine == "ON") {
              // ICON HIJAU
              var icon = {
                // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
                path: car,
                scale: .5,
                // anchor: new google.maps.Point(25,10),
                // scaledSize: new google.maps.Size(30,20),
                strokeColor: 'white',
                strokeWeight: .10,
                fillOpacity: 1,
                fillColor: '#0000FF',
                offset: '5%'
              };
            }else if (roundedspeed == 0 && obj[i].auto_last_engine == "ON") {
              // ICON UNGU
              var icon = {
                // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
                path: car,
                scale: .5,
                // anchor: new google.maps.Point(25,10),
                // scaledSize: new google.maps.Size(30,20),
                strokeColor: 'white',
                strokeWeight: .10,
                fillOpacity: 1,
                fillColor: '#ffff00',
                offset: '5%'
              };
            }else {
              // ICON BIRU
              var icon = {
                // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
                path: car,
                scale: .5,
                // anchor: new google.maps.Point(25,10),
                // scaledSize: new google.maps.Size(30,20),
                strokeColor: 'white',
                strokeWeight: .10,
                fillOpacity: 1,
                fillColor: '#ff0040',
                offset: '5%'
              };
            }
          }
        }else {
          if (obj[i].auto_status == 'M') {
              laststatus = 'GPS Offline';
              laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-danger">GPS Offline</span></h5>';
              var icon = {
                // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
                path: car,
                scale: .5,
                // anchor: new google.maps.Point(25,10),
                // scaledSize: new google.maps.Size(30,20),
                strokeColor: 'white',
                strokeWeight: .10,
                fillOpacity: 1,
                fillColor: '#ff0040',
                offset: '5%'
              };
            }else if (obj[i].auto_status == 'K') {
              laststatus = 'GPS Online (Delay)';
              laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-warning">GPS Online (Delay)</span></h5>';
              var icon = {
                // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
                path: car,
                scale: .5,
                // anchor: new google.maps.Point(25,10),
                // scaledSize: new google.maps.Size(30,20),
                strokeColor: 'white',
                strokeWeight: .10,
                fillOpacity: 1,
                fillColor: '#ffff00',
                offset: '5%'
              };
            }else {
              laststatus = 'GPS Online';
              laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
              if (obj[i].auto_last_speed > 0) {
                var icon = {
                  // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
                  path: car,
                  scale: .5,
                  // anchor: new google.maps.Point(25,10),
                  // scaledSize: new google.maps.Size(30,20),
                  strokeColor: 'white',
                  strokeWeight: .10,
                  fillOpacity: 1,
                  fillColor: '#00b300',
                  offset: '5%'
                };
              } else {
                var icon = {
                  // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
                  path: car,
                  scale: .5,
                  // anchor: new google.maps.Point(25,10),
                  // scaledSize: new google.maps.Size(30,20),
                  strokeColor: 'white',
                  strokeWeight: .10,
                  fillOpacity: 1,
                  fillColor: '#ff0040',
                  offset: '5%'
                };
              }
            }
        }
      }

      marker = new google.maps.Marker({
        position: position,
        map: map,
        icon: icon,
        title: obj[i].vehicle_no,
        // + " - " + obj[i].vehicle_name + " (" + laststatus + ")" + "\n" +
        //   "GPS Time : " + obj[i].auto_last_update + "\n" + obj[i].auto_last_position + "\n" + obj[i].auto_last_lat + ", " + obj[i].auto_last_long + "\n" +
        //   "Speed : " + obj[i].auto_last_speed + " kph",
        id: obj[i].vehicle_device
      });
      // console.log("obj di marker : ", obj);
      // console.log("auto_last_course di marker : ", parseFloat(obj[8].auto_last_course));
      icon.rotation = Math.ceil(obj[i].auto_last_course);
      marker.setIcon(icon);

      // infowindow.open(map, marker);
      markers.push(marker);

      // SAMPLE UNTUK BIKIN ZOOM IN BARU MUNCULIN MARKER POOL START
      // google.maps.event.addListener(map, 'zoom_changed', function() {
      // var currentZoom = map.getZoom();
      //   if (currentZoom > 10){
      //     for (var x = 0; x < markerpools.length; x++) {
      //       var positionpool = new google.maps.LatLng(parseFloat(markerpools[x].getPosition().lat()), parseFloat(markerpools[x].getPosition().lng()));
      //       console.log("positionpool : ", positionpool);
      //       boundspool.extend(positionpool);
      //         var iconpool = {
      //           url: markerpools[x].icon.url,
      //           anchor: new google.maps.Point(25,10),
      //           scaledSize: new google.maps.Size(17,17)
      //         };
      //
      //       markerpool = new google.maps.Marker({
      //         position: positionpool,
      //         map: map,
      //         icon: iconpool,
      //         title: markerpools[x].title,
      //         id: markerpools[x].title,
      //         optimized: false
      //       });
      //       markerpool.setIcon(iconpool);
      //     }
      //   }
      // });
      // SAMPLE UNTUK BIKIN ZOOM IN BARU MUNCULIN MARKER POOL START

      google.maps.event.addListener(marker, 'click', (function(marker, i) {
        return function() {
          var data = {device_id : marker.id};
          // console.log("data marker on load diklik", data);
          $.post("<?php echo base_url() ?>maps/getdetailbydevid", data, function(response){
            console.log("klik load pertama kali : ", response);
            var center = {lat : parseFloat(response[0].auto_last_lat), lng: parseFloat(response[0].auto_last_long)};
            // console.log("center : ", center);
            var num         = Number(response[0].auto_last_speed);
            var roundstring = num.toFixed(0);
            var rounded     = Number(roundstring);

            var addresssplit = response[0].auto_last_position.split(" ");
            var inarea       = response[0].auto_last_position.split(",");
            var addressfix   = bibarea.includes(addresssplit[0]);
            if (addressfix) {
              var addressfix = inarea[0];
            }else {
              var addressfix = response[0].auto_last_position;
            }

            var string = response[0].vehicle_no + ' - ' + response[0].vehicle_name + "<br>" +
              "GPS Time : " + response[0].auto_last_update + "<br>Position : " + addressfix + "<br>Coord : " + response[0].auto_last_lat + ", " + response[0].auto_last_long + "<br>" +
              "Engine : " + response[0].auto_last_engine + "<br>" +
              "Speed : " + rounded + " kph" + "<br> Ritase : " + response[0].auto_last_ritase + "</br>" +
              "<a href='<?php echo base_url()?>maps/tracking/" + response[0].vehicle_id + "' target='_blank'>Tracking</a>";

             infowindowkedua = new google.maps.InfoWindow({
              content: string,
              maxWidth: 300
            });
            DeleteMarkers(response[0].vehicle_device);
            DeleteMarkerspertama(response[0].vehicle_device);


            if (response[0].auto_last_road) {
              // console.log("jalur on");
              if (response[0].auto_last_road == "muatan") {
                // console.log("jalur muatan");
                if (rounded == 0 && response[0].auto_last_engine == "ON") {
                  // console.log("jalur muatan 1");
                  laststatus = 'GPS Online';
                  laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
                  // ICON UNGU
                  var icon = {
                    // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
                    path: car,
                    scale: .5,
                    // anchor: new google.maps.Point(25,10),
                    // scaledSize: new google.maps.Size(30,20),
                    strokeColor: 'white',
                    strokeWeight: .10,
                    fillOpacity: 1,
                    fillColor: '#ffff00',
                    offset: '5%'
                  };
                }else if (rounded > 0 && response[0].auto_last_engine == "ON") {
                  // console.log("jalur muatan 2");
                  laststatus = 'GPS Online';
                  laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
                  // ICON HIJAU
                  var icon = {
                    // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
                    path: car,
                    scale: .5,
                    // anchor: new google.maps.Point(25,10),
                    // scaledSize: new google.maps.Size(30,20),
                    strokeColor: 'white',
                    strokeWeight: .10,
                    fillOpacity: 1,
                    fillColor: '#00b300',
                    offset: '5%'
                  };
                }else {
                  // console.log("jalur muatan 3");
                  laststatus = 'GPS Online';
                  laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
                  // ICON BIRU
                  var icon = {
                    // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
                    path: car,
                    scale: .5,
                    // anchor: new google.maps.Point(25,10),
                    // scaledSize: new google.maps.Size(30,20),
                    strokeColor: 'white',
                    strokeWeight: .10,
                    fillOpacity: 1,
                    fillColor: '#ff0040',
                    offset: '5%'
                  };
                }
              }else {
                // console.log("jalur kosongan");
                if (rounded == 0 && response[0].auto_last_engine == "ON") {
                  // ICON UNGU
                  var icon = {
                    // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
                    path: car,
                    scale: .5,
                    // anchor: new google.maps.Point(25,10),
                    // scaledSize: new google.maps.Size(30,20),
                    strokeColor: 'white',
                    strokeWeight: .10,
                    fillOpacity: 1,
                    fillColor: '#ffff00',
                    offset: '5%'
                  };
                }else if (rounded > 0 && response[0].auto_last_engine == "ON") {
                  // ICON HIJAU
                  var icon = {
                    // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
                    path: car,
                    scale: .5,
                    // anchor: new google.maps.Point(25,10),
                    // scaledSize: new google.maps.Size(30,20),
                    strokeColor: 'white',
                    strokeWeight: .10,
                    fillOpacity: 1,
                    fillColor: '#0000FF',
                    offset: '5%'
                  };
                }else {
                  // ICON BIRU
                  var icon = {
                    // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
                    path: car,
                    scale: .5,
                    // anchor: new google.maps.Point(25,10),
                    // scaledSize: new google.maps.Size(30,20),
                    strokeColor: 'white',
                    strokeWeight: .10,
                    fillOpacity: 1,
                    fillColor: '#ff0040',
                    offset: '5%'
                  };
                }
              }
            }else {
              console.log("jalur off");
            }

            markernya = new google.maps.Marker({
              map: map,
              icon: icon,
              position: new google.maps.LatLng(parseFloat(response[0].auto_last_lat), parseFloat(response[0].auto_last_long)),
              title: response[0].vehicle_no,
              // + ' - ' + value.vehicle_name + value.driver + "\n" +
              //   "GPS Time : " + value.gps.gps_date_fmt + " " + value.gps.gps_time_fmt + "\n" + value.gps.georeverse.display_name + "\n" + value.gps.gps_latitude_real_fmt + ", " + value.gps.gps_longitude_real_fmt + "\n" +
              //   "Speed : " + value.gps.gps_speed + " kph",
              id: response[0].vehicle_device
            });
            markerss.push(markernya);
            icon.rotation = Math.ceil(response[0].auto_last_course);
            markernya.setIcon(icon);


            // map.setZoom(18);
            infowindowkedua.open(map, markernya);
            map.setCenter(center);
            markernya.setPosition(center);

            google.maps.event.addListener(markernya, 'click', function(evt){
              // console.log("data marker on load diklik kedua", data);
              infowindow2.close();
              infowindowkedua.close();
              infowindow.close();

              var num         = Number(response[0].auto_last_speed);
              var roundstring = num.toFixed(0);
              var rounded     = Number(roundstring);

              var string = response[0].vehicle_no + ' - ' + response[0].vehicle_name + "<br>" +
                "GPS Time : " + response[0].auto_last_update + "<br>Position : " + response[0].auto_last_position + "<br>Coord : " + response[0].auto_last_lat + ", " + response[0].auto_last_long + "<br>" +
                "Engine : " + response[0].auto_last_engine + "<br>" +
                "Speed : " + rounded + " kph" + "<br> Ritase : " + response[0].auto_last_ritase + "</br>" +
                "<a href='<?php echo base_url()?>maps/tracking/" + response[0].vehicle_id + "' target='_blank'>Tracking</a>";

               infowindowkedua = new google.maps.InfoWindow({
                content: string,
                maxWidth: 300
              });
              // DeleteMarkers(response[0].vehicle_device);
              // DeleteMarkerspertama(response[0].vehicle_device);

                var center = {lat : parseFloat(response[0].auto_last_lat), lng: parseFloat(response[0].auto_last_long)};
                infowindowkedua.setContent(string);
                map.setCenter(markernya.position);
                markernya.setPosition(markernya.position);
                infowindowkedua.open(map, this);
            });

          }, "json");
        };
      })(marker, i));

      var num         = Number(obj[i].auto_last_speed);
      var roundstring = num.toFixed(0);
      var rounded     = Number(roundstring);

      var courseround   = Number(obj[i].auto_last_course);
      var coursestring  = courseround.toFixed(0);
      var courserounded = Number(coursestring);

      htmlautoupdaterow += '<tr id="rowid_'+obj[i].vehicle_device+'">'+
                              '<td><a name="'+(i+1)+'"></a><div id="pointer_'+obj[i].vehicle_device+'" style="display: none; "text-align: right;">&#9654;</div></td>'+
                              '<td style="font-size:12px; vertical-align:top">'+(i+1)+'</td>'+
                              '<td style="font-size:12px; vertical-align:top" id="vehicle_id_'+obj[i].vehicle_device+'"><a style="color:green;" onclick="forgetcenter('+obj[i].vehicle_id+')">'+obj[i].vehicle_no + " - " + obj[i].vehicle_name+'</a></td>'+
                              '<td style="font-size:12px; vertical-align:top" id="driver_'+obj[i].vehicle_device+'"></td>'+
                              '<td style="font-size:12px; vertical-align:top" id="position_'+obj[i].vehicle_device+'"><span style="color:blue;">Position : '+obj[i].auto_last_position + "</span> <br> GPS Time : " + obj[i].auto_last_update + "<br>" + "Coord : " + "<a href='http://maps.google.com/maps?z=12&t=m&q=loc:"+obj[i].auto_last_lat+','+obj[i].auto_last_long+"' target='_blank'>" + obj[i].auto_last_lat + ", " + obj[i].auto_last_long + "</a>"+ "<br>" + "Engine : " + obj[i].auto_last_engine + "<br>" + "Speed : " + rounded + ' Kph </br> Jalur : '+obj[i].auto_last_road+' </br> Ritase : '+obj[i].auto_last_ritase+'</br>Direction : '+courserounded+'</td>'+
                              // '<td style="font-size:12px; vertical-align:top" id="description_'+obj[i].vehicle_device+'"></td>'+
                              // '<td style="font-size:12px; vertical-align:top" id="customer_'+obj[i].vehicle_device+'"></td>'+
                              '<td style="font-size:12px; vertical-align:top" id="cardno_'+obj[i].vehicle_device+'">'+obj[i].vehicle_card_no+'</td>'+
                              '<td style="font-size:12px; vertical-align:top" id="laststatus_'+obj[i].vehicle_device+'">'+laststatus2+'</td>'+
                           '</tr>';
    }
    $("#autoupdaterow").before(htmlautoupdaterow);
    // INTERVAL SETTING
    // var intervalsetting;
    //   if (vehicletotal >= 100) {
    //     intervalsetting = 5000;
    //   }else {
    //     intervalsetting = 10000;
    //   }

    // TOOGEL BUTTON BIB MAP
      var toggleButton = document.createElement("button");
      toggleButton.textContent = "BIB MAPS";
      toggleButton.classList.add("custom-map-control-button");
      map.controls[google.maps.ControlPosition.TOP_CENTER].push(toggleButton);

       toggleButton.addEventListener("click", () => {
        addoverlay(map);
      });

      // STORE VEHICLE DEVICE & LASTTIME TO LOCAL STORAGE
      for (var looplasttime = 0; looplasttime < obj.length; looplasttime++) {
        // console.log("lasttime : ", obj[looplasttime].auto_last_update);
        // Check browser support
        if (typeof(Storage) !== "undefined") {
          // Store
          localStorage.setItem(obj[looplasttime].vehicle_device, obj[looplasttime].auto_last_update);
        } else {
          alert("Sorry, your browser does not support Web Storage. Please contact administrator.");
        }
      }

    intervalstart = setInterval(simultango, 10000); /// 30 detik after autocheck done (30 detik = 30000)
  }

  function simultango() {
    var lastpointer;
    if (objectnumberfix == (obj.length - 1)) {
      objectnumberfix = 0;
      objectnumber    = 0;
      lastpointer     = 0;
      // console.log("sama");
    }else {
      // console.log("tak sama");
      if (objectnumber == 0) {
        objectnumber    = objectnumber + 1;
        objectnumberfix = 0;
        lastpointer     = 0;
      }else {
        objectnumberfix = objectnumber;
        lastpointer     = objectnumberfix - 1;
        objectnumber    = objectnumber + 1;
      }
    }

    var lasttimefromlocalstorage = localStorage.getItem(obj[objectnumberfix].vehicle_device);
    console.log("lasttimefromlocalstorage : ", lasttimefromlocalstorage);

    // console.log("device yang dikirim : ", obj[objectnumberfix].vehicle_device);
    $("[id='pointer_"+obj[(obj.length - 1)].vehicle_device+"']").hide();
    $("[id='pointer_"+obj[lastpointer].vehicle_device+"']").hide();
    // console.log("timer_list di simultango : ", '<?php echo $this->config->item('timer_list ');?>');
      jQuery.post("<?=base_url();?>map/lastinfonew", {
          device: obj[objectnumberfix].vehicle_device, //"69969039633231@TK510", //obj[objectnumberfix].vehicle_device,
          lasttime: lasttimefromlocalstorage,
          vuserid: obj[objectnumberfix].vehicle_user_id
        },
        function(r) {
          // console.log("response map/lastinfonew : ", r);
          var totallasttime = r.vehicle.gps.length;
          if (totallasttime > 1) {
            var lasttimeforupdatelocalstorage = r.vehicle.gps[totallasttime-1].gps_date_fmt+" "+r.vehicle.gps[totallasttime-1].gps_time_fmt;
          }else {
            var lasttimeforupdatelocalstorage = r.vehicle.gps[0].gps_date_fmt+" "+r.vehicle.gps[0].gps_time_fmt;
          }
          // console.log("map/lastinfonew totallasttime : ", totallasttime);
          // console.log("map/lastinfonew lasttimeforupdatelocalstorage : ", );
          localStorage.setItem(r.vehicle.vehicle_device, lasttimeforupdatelocalstorage);
          // console.log("response jika obj banyak : ", r.vehicle);
          // console.log("response vdevice jika obj banyak : ", r.vehicle.vehicle_device);
          transition(r.vehicle);
        }, "json");
  }

  var positionmove;
  var numDeltas = 100;
  var delay     = 10; //milliseconds
  var iloopnya  = 0;
  var deltaLat;
  var deltaLng;

  function transition(value){
    console.log("vehicle : ", value.vehicle_no+" - "+value.vehicle_name);
    var totalcoordinate = value.gps.length;
    var num         = Number(value.gps[totalcoordinate-1].gps_speed_fmt);
    var roundstring = num.toFixed(0);
    var rounded     = Number(roundstring);

    var statusengine = "";
    if (value.status1 == true) {
      statusengine = "ON";
    }else {
      statusengine = "OFF";
    }

    // DEVICE STATUS (CAMERA ONLINE / OFFLINE)
    if (value.devicestatus) {
      if (value.devicestatus == 1) {
        var devicestatus = "Camera : Online </br>" ;
      }else {
        var devicestatus = "Camera : Offline </br>" ;
      }
    }else {
      var devicestatus = "" ;
    }

      // console.log("devicestatus ", value.devicestatus);
    // console.log("devicestatus : ", devicestatus);

    var geofencestatus = "";
      if (value.geofence_location != "") {
        geofencestatus = "<span style='color:black'>Geofence : " + value.geofence_location + "</span></br>";
      }
      // console.log("geofence : ", geofencestatus);

    // UNTUK ADD COMMENT
    var comment = "<span id='comment"+value.vehicle_id+"' style='display: none'></span>";
    // console.log("comment : ", comment);

    var num         = Number(value.gps[totalcoordinate-1].gps_speed_fmt);
    var roundstring = num.toFixed(0);
    var rounded     = Number(roundstring);

    var ritasefix = 0;
    if (value.ritase) {
      if (value.ritase == 0) {
        ritasefix = 0;
      }else {
        ritasefix = value.ritase;
      }
    }

    var jalurfix;
    if (value.jalur) {
        jalurfix = value.jalur;
    }else {
      jalurfix = "";
    }

    var gps_status = "";
    if (value.gps.gps_status == "A" || value.gps.gps_status == "V") {
      gps_status = "Good";
    }else {
      gps_status = "Not Good";
    }

    var courseround   = Number(value.gps[0].gps_course);
    var coursestring  = courseround.toFixed(0);
    var courserounded = Number(coursestring);


    var titlemarker  = "";
    var addresssplit = value.gps[totalcoordinate-1].georeverse.display_name.split(" ");
    var inarea       = value.gps[totalcoordinate-1].georeverse.display_name.split(",");
    // console.log("addresssplit : ", addresssplit);

    var addressfix = bibarea.includes(addresssplit[0]);
    if (addressfix) {
      var addressfix = inarea[0];
    }else {
      var addressfix = value.gps[totalcoordinate-1].georeverse.display_name;
    }

    var vehicle_id_ = '<a style="color:green;" onclick="forgetcenter('+value.vehicle_id+')">'+value.vehicle_no + " - " + value.vehicle_name+'</a>';
    var position_   = geofencestatus + '<span style="color:blue;">' + addressfix + "</span> <br> GPS Time : " + value.gps[totalcoordinate-1].gps_date_fmt + " " + value.gps[totalcoordinate-1].gps_time_fmt + "<br>" + "Coord : " + "<a href='http://maps.google.com/maps?z=12&t=m&q=loc:"+value.gps[totalcoordinate-1].gps_latitude_real_fmt+','+value.gps[totalcoordinate-1].gps_longitude_real_fmt+"' target='_blank'>" + value.gps.gps_latitude_real_fmt + ", " + value.gps[totalcoordinate-1].gps_longitude_real_fmt + "</a> <br>" + "Engine : " + statusengine + "<br>" + "Speed : " + rounded + " Kph<br>" + devicestatus + "Jalur : " + jalurfix + "</br> Ritase : " + ritasefix + "</br>Direction : "+ courserounded + "</br>GPS Status : " + gps_status + "<br>" + comment;

    var descriptionvalue = "";
    if (value.dataproject) {
      descriptionvalue = value.dataproject;
    }
    var description_ = descriptionvalue;

    var cutpowerfix = "";
    if (value.cutpower) {
      cutpowerfix = "</br><b><font color='red'>Power Off : "+" "+value.cutpower+"</font></b><br/>";
    }

    var cardno_     = value.vehicle_card_no + cutpowerfix;

    // BIODATA DRIVER START
    var datadriver;
    if (value.driver){
      // console.log("sikon 0");
       datadriver   = value.driver.split('-');
      if (value.driverimage) {
        // console.log("sikon 1");
        if (value.driverimage != 0) {
          // console.log("sikon 2");
          // var showdriver   = "<a href='#' onclick='getmodaldriver("+datadriver[0]+");'>"+ datadriver[1] +"</a>";
          var detaildriver = '<img src="<?php echo base_url().$this->config->item("dir_photo");?>'+value.driverimage+'" width="100px;" height="100px;"> </br>' + datadriver[1] ;
          $("[id='driver_"+value.vehicle_device+"']").html(detaildriver);
        }else {
          var detaildriver = datadriver[1] + ' </br> No Driver Image';
          $("[id='driver_"+value.vehicle_device+"']").html(detaildriver);
        }
      }else {
        var detaildriver = datadriver[1] + ' </br> No Driver Image';
        $("[id='driver_"+value.vehicle_device+"']").html(detaildriver);
      }
    }
    // BIODATA DRIVER END

    //Get driver ID CARD
    if (value.driver_idcard){
      // var sDriver = value.driver_idcard.split('-');
      // $("[id='driver_"+value.vehicle_device+"']").html("<a style='color: black;' href=" + "javascript:driver_profile(" + sDriver[0] + ")" + ">" + sDriver[1] + "</a>");
      $("[id='driver_"+value.vehicle_device+"']").html(value.driver_sj);
    }
    //end get driver ID CARD

    //Get driver ID CARD
    if (value.driver){
      // var sDriver = value.driver.split('-');
      // $("[id='driver_"+value.vehicle_device+"']").html("<a style='color: black;' href=" + "javascript:driver_profile(" + sDriver[0] + ")" + ">" + sDriver[1] + "</a>");
      $("[id='driver_"+value.vehicle_device+"']").html(value.driver_sj);
    }
    //end get driver ID CARD
    //Get Customer Groups
    if (value.customer_groups)
    {
        jQuery("[id='customer_"+value.vehicle_device+"']").html(value.customer_groups);
    }

  $("[id='pointer_"+value.vehicle_device+"']").show();
  $("[id='vehicle_id_"+value.vehicle_device+"']").html(vehicle_id_);
  $("[id='position_"+value.vehicle_device+"']").html(position_);
  // $("[id='description_"+value.vehicle_device+"']").html(description_);
  $("[id='cardno_"+value.vehicle_device+"']").html(cardno_);

  // console.log("course : ", value.gps.gps_course);
  // console.log("Status : ", value.gps.gps_status);
  // console.log("value : ", value);
  // console.log("jalur on marker new : ", value.jalur);
  // console.log("speed on marker new : ", rounded);
  // console.log("engine on marker new : ", value.status1);

    if (value.jalur) {
      // console.log("jalur on");
      if (value.jalur == "muatan") {
        // console.log("jalur muatan");
        if (rounded == 0 && value.status1 == true) {
          // console.log("jalur muatan 1");
          laststatus = 'GPS Online';
          laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
          // ICON UNGU
          var icon = {
            // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
            path: car,
            scale: .5,
            // anchor: new google.maps.Point(25,10),
            // scaledSize: new google.maps.Size(30,20),
            strokeColor: 'white',
            strokeWeight: .10,
            fillOpacity: 1,
            fillColor: '#ffff00',
            offset: '5%'
          };
        }else if (rounded > 0 && value.status1 == true) {
          // console.log("jalur muatan 2");
          laststatus = 'GPS Online';
          laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
          // ICON HIJAU
          var icon = {
            // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
            path: car,
            scale: .5,
            // anchor: new google.maps.Point(25,10),
            // scaledSize: new google.maps.Size(30,20),
            strokeColor: 'white',
            strokeWeight: .10,
            fillOpacity: 1,
            fillColor: '#00b300',
            offset: '5%'
          };
        }else {
          // console.log("jalur muatan 3");
          laststatus = 'GPS Online';
          laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
          // ICON BIRU
          var icon = {
            // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
            path: car,
            scale: .5,
            // anchor: new google.maps.Point(25,10),
            // scaledSize: new google.maps.Size(30,20),
            strokeColor: 'white',
            strokeWeight: .10,
            fillOpacity: 1,
            fillColor: '#ff0040',
            offset: '5%'
          };
        }
      }else {
        // console.log("jalur kosongan");
        if (rounded == 0 && value.status1 == true) {
          // ICON UNGU
          var icon = {
            // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
            path: car,
            scale: .5,
            // anchor: new google.maps.Point(25,10),
            // scaledSize: new google.maps.Size(30,20),
            strokeColor: 'white',
            strokeWeight: .10,
            fillOpacity: 1,
            fillColor: '#ffff00',
            offset: '5%'
          };
        }else if (rounded > 0 && value.status1 == true) {
          // ICON HIJAU
          var icon = {
            // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
            path: car,
            scale: .5,
            // anchor: new google.maps.Point(25,10),
            // scaledSize: new google.maps.Size(30,20),
            strokeColor: 'white',
            strokeWeight: .10,
            fillOpacity: 1,
            fillColor: '#0000FF',
            offset: '5%'
          };
        }else {
          // ICON BIRU
          var icon = {
            // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
            path: car,
            scale: .5,
            // anchor: new google.maps.Point(25,10),
            // scaledSize: new google.maps.Size(30,20),
            strokeColor: 'white',
            strokeWeight: .10,
            fillOpacity: 1,
            fillColor: '#ff0040',
            offset: '5%'
          };
        }
      }
    }else {
      console.log("jalur off");
    }

    $("[id='laststatus_"+value.vehicle_device+"']").html(laststatus2);

    DeleteMarkers(value.vehicle_device_name + "@" + value.vehicle_device_host);
    DeleteMarkerspertama(value.vehicle_device_name + "@" + value.vehicle_device_host);
    infowindow           = new google.maps.InfoWindow();
    infowindow2          = new google.maps.InfoWindow();
    infowindowkedua      = new google.maps.InfoWindow();
    infowindowonsimultan = new google.maps.InfoWindow();

    var camdevicesfix = camdevices.includes(value.vehicle_type);
    if (camdevicesfix) {
      var lct = "Last Captured Time: " +  value.snaptime + "</br>";
      var imglct = "<img src='"+ value.snapimage+"'> </br>";
    }else {
      var lct = "";
      var imglct = "</br>";
    }

    positionmove = [parseFloat(value.gps[0].gps_latitude_real_fmt), parseFloat(value.gps[0].gps_longitude_real_fmt)];
    markernya = new google.maps.Marker({
      map: map,
      icon: icon,
      // position: new google.maps.LatLng(parseFloat(value.gps.gps_latitude_real_fmt), parseFloat(value.gps.gps_longitude_real_fmt)),
      position: new google.maps.LatLng(positionmove[0], positionmove[1]),
      title: value.vehicle_no + ' - ' + value.vehicle_name,
      // + ' - ' + value.vehicle_name + value.driver + "\n" +
      //   "GPS Time : " + value.gps.gps_date_fmt + " " + value.gps.gps_time_fmt + "\n" + value.gps.georeverse.display_name + "\n" + value.gps.gps_latitude_real_fmt + ", " + value.gps.gps_longitude_real_fmt + "\n" +
      //   "Speed : " + value.gps.gps_speed + " kph",
      id: value.vehicle_device_name + "@" + value.vehicle_device_host
    });
    markerss.push(markernya);
    // INI UNTUK MOVE MARKER DGN SMOOTH
    var prevLat = parseFloat(value.gps[0].gps_latitude_real_fmt);
    var prevLng = parseFloat(value.gps[0].gps_longitude_real_fmt);
      if (totalcoordinate == 1) {
        var nextLat = parseFloat(value.gps[0].gps_latitude_real_fmt);
        var nextLng = parseFloat(value.gps[0].gps_longitude_real_fmt);
      }else {
        var nextLat = parseFloat(value.gps[totalcoordinate-1].gps_latitude_real_fmt);
        var nextLng = parseFloat(value.gps[totalcoordinate-1].gps_longitude_real_fmt);
      }

    i = 0;
    deltaLat = (nextLat - prevLat)/numDeltas;
    deltaLng = (nextLng - prevLng)/numDeltas;
    // console.log("totalvalue : ", totalvalue);
    console.log("prevLatLng : ", prevLat+","+prevLng);
    console.log("nextLatLng : ", nextLat+","+nextLng);
    console.log("deltaLat : ", deltaLat);
    console.log("deltaLng : ", deltaLng);
    moveMarker();
    icon.rotation = Math.ceil(value.gps[0].gps_course);
    markernya.setIcon(icon);
    // INI UNTUK MOVE MARKER DGN SMOOTH

    // BUAT InfoWindow
    if (value.driver) {
      var datadriversplit   = value.driver.split('-');
      var datadriveronhover = datadriver[1];
    }else {
      var datadriveronhover = "Not set yet";
    }

    if (value.driverimage) {
      var imagedriveronhover = '<img src=<?php echo base_url().$this->config->item("dir_photo");?>'+value.driverimage+' width="100px" height="100px"> </br>';
    }else {
      var imagedriveronhover = "";
    }

        titlemarker += '<table class="table" style="font-size:12px;">';
          titlemarker += '<tr>';
            titlemarker += '<td>'+imagedriveronhover+'</td>';
            titlemarker += '<td>';
              titlemarker += value.vehicle_no + ' - ' + value.vehicle_name +'</br>';
              titlemarker += 'Driver : ' + datadriveronhover +'</br>';
              titlemarker += 'Gps Time : ' + value.gps[totalcoordinate-1].gps_date_fmt + " " + value.gps[totalcoordinate-1].gps_time_fmt + '</br>';
              titlemarker += 'Position : ' + geofencestatus + addressfix + '</br>';
              titlemarker += 'Coord : ' + value.gps[totalcoordinate-1].gps_latitude_real_fmt + ", " + value.gps[totalcoordinate-1].gps_longitude_real_fmt + '</br>';
              titlemarker += 'Engine : ' + statusengine + '</br>';
              titlemarker += 'Speed : ' + rounded + ' kph </br>';
              titlemarker += 'Odometer : ' + value.totalodometer + '</br>';
              titlemarker +=  devicestatus;
              titlemarker += 'Ritase : ' + ritasefix + '</br>';
              titlemarker += 'Card No : ' + value.vehicle_card_no + '</br>';
              // titlemarker += '<a href="<?php echo base_url()?>maps/tracking/"' + value.vehicle_id + '"target="_blank">Tracking</a> </br>';
              titlemarker +=   lct + imglct;
            titlemarker += '</td>';
          titlemarker += '</tr>';
        titlemarker += '</table>';

    google.maps.event.addListener(markernya, 'mouseover', function(){
      var varthis = this;
      setTimeoutConst = setTimeout(function() {
        console.log("mouseover 3second on simultan");
        console.log("masuk titlemarker", imagedriveronhover);
          infowindowonsimultan = new google.maps.InfoWindow({
            content: titlemarker,
            maxWidth: 300
          });

         infowindowonsimultan.setContent(titlemarker);
         infowindowonsimultan.open(map, varthis);
      }, 2000);
    });

    // assuming you also want to hide the infowindow when user mouses-out
    google.maps.event.addListener(markernya, 'mouseout', function(){
      console.log("mouseout on simultan");
      clearTimeout(setTimeoutConst);
        infowindowonsimultan.close();
    });

    google.maps.event.addListener(markernya, 'click', function(evt){
      console.log("ini simultan di klik");
      infowindow2.close();
      infowindowkedua.close();
      infowindow.close();

         infowindow2 = new google.maps.InfoWindow({
          content: titlemarker,
          maxWidth: 300
        });

        var center = {lat : parseFloat(value.gps[totalcoordinate-1].gps_latitude_real_fmt), lng: parseFloat(value.gps[totalcoordinate-1].gps_longitude_real_fmt)};

        infowindow2.setContent(titlemarker);
        map.setCenter(markernya.position);
        markernya.setPosition(markernya.position);
        infowindow2.open(map, this);
    });
  }

  function moveMarker(){
    positionmove[0] += deltaLat;
    positionmove[1] += deltaLng;
    var latlng = new google.maps.LatLng(positionmove[0], positionmove[1]);
    // console.log("Movemarker Latitude:"+positionmove[0]+" | Longitude:"+positionmove[1]);
    // console.log("globalicon : ", globalicon);
    // console.log("globalcourse : ", globalcourse);
    markernya.setPosition(latlng);
    if(iloopnya!=numDeltas){
        iloopnya++;
        setTimeout(moveMarker, delay);
    }else {
      positionmove = "";
      deltaLat     = "";
      deltaLng     = "";
      globalicon   = "";
      globalcourse = "";
      iloopnya     = 0;
    }
  }

  function DeleteMarkers(id) {
    //Loop through all the markers and remove
    // console.log("marker pertama id yg dihapus : ", id);
    for (var i = 0; i < markerss.length; i++) {
      if (markerss[i].id == id) {
        //Remove the om Map
        markerss[i].setMap(null);

        //Remove the marker from array.
        markerss.splice(i, 1);
        return;
      }
    }
  }

  function DeleteMarkerspertama(id) {
    //Loop through all the markers and remove
    // console.log("marker kedua id yg dihapus : ", id);
    for (var i = 0; i < markers.length; i++) {
      if (markers[i].id == id) {
        //Remove the marker from Map
        markers[i].setMap(null);

        //Remove the marker from array.
        markers.splice(i, 1);
        return;
      }
    }
  }

  function showtableperarea(companyid) {
    // console.log("Company id : ", companyid);
    // window.open('<?php echo base_url()?>maps/tableview/' + companyid);
    $("#modallistvehicle").show();
    $("#showtable").show();
  }

  function closemodallistofvehicle(){
    $("#modallistvehicle").hide();
  }

// KLIK DARI TABLE
  function forgetcenter(deviceid){
    // console.log("device id forgetcenter 1 : ", deviceid);
    var data = {device_id : deviceid};

    var data = {device_id : deviceid};

    if (infowindowkedua) {
        infowindowkedua.close();
    }

    if (infowindow) {
        infowindow.close();
    }

    if (infowindow2) {
        infowindow2.close();
    }

    $.post("<?php echo base_url() ?>maps/getdetailbydevid_0", data, function(response){
      console.log("forgetcenter on table click : ", response);
      // DEVICE STATUS (CAMERA ONLINE / OFFLINE)
      if (response[0].devicestatusfixnya) {
        // console.log("devicestatus ada : ");
        if (response[0].devicestatusfixnya == 1) {
          var devicestatus = "Camera : Online <br>" ;
        }else {
          var devicestatus = "Camera : Offline <br>" ;
        }
      }else {
        if (response[0].devicestatusfixnya == "") {
          var devicestatus = "";
        }else if (response[0].devicestatusfixnya == 0) {
          var devicestatus = "Camera : Offline </br>";
        }else {
          var devicestatus = "";
        }
      }

      if (response[0].drivername) {
        var drivername = response[0].drivername;
       if (response[0].driverimage) {
         console.log("sikon 1");
         if (response[0].driverimage != 0) {
           console.log("sikon 2");
           // var showdriver   = "<a href='#' onclick='getmodaldriver("+datadriver[0]+");'>"+ datadriver[1] +"</a>";
           var detaildriver = '<img src="<?php echo base_url().$this->config->item("dir_photo");?>'+response[0].driverimage+'" width="100px;" height="100px;">';
         }else {
           var detaildriver = drivername + ' </br> No Driver Image';
         }
       }else {
         var detaildriver = drivername + ' </br> No Driver Image';
       }
      }

      // console.log("devicestatus : ", response[0].devicestatusfixnya);
      // console.log("devicestatus : ", devicestatus);

      var center = {lat : parseFloat(response[0].auto_last_lat), lng: parseFloat(response[0].auto_last_long)};
      // console.log("center : ", center);
      var num         = Number(response[0].auto_last_speed);
      var roundstring = num.toFixed(0);
      var rounded     = Number(roundstring);

      var addresssplit = response[0].auto_last_position.split(" ");
      var inarea       = response[0].auto_last_position.split(",");
      // console.log("addresssplit : ", addresssplit);

      var addressfix = bibarea.includes(addresssplit[0]);
      if (addressfix) {
        var addressfix = inarea[0];
      }else {
        var addressfix = response[0].auto_last_position;
      }

      titlemarker = "";
      titlemarker += '<table class="table" style="font-size:12px;">';
        titlemarker += '<tr>';
          titlemarker += '<td>'+detaildriver+'</td>';
          titlemarker += '<td>';
            titlemarker += response[0].vehicle_no + ' - ' + response[0].vehicle_name +'</br>';
            titlemarker += 'Driver : ' + drivername +'</br>';
            titlemarker += 'Gps Time : ' + response[0].auto_last_update+ '</br>';
            titlemarker += 'Position : ' + addressfix + '</br>';
            titlemarker += 'Coord : ' + response[0].auto_last_lat + ", " + response[0].auto_last_long + '</br>';
            titlemarker += 'Engine : ' + response[0].auto_last_engine + '</br>';
            titlemarker += 'Speed : ' + rounded + ' kph </br>';
            titlemarker +=  devicestatus;
            titlemarker += 'Ritase : ' + response[0].auto_last_ritase + '</br>';
            titlemarker += '<a href="<?php echo base_url()?>maps/tracking/' + response[0].vehicle_id + '" target="_blank">Tracking</a> </br>';
            // titlemarker +=   lct + imglct;
          titlemarker += '</td>';
        titlemarker += '</tr>';
      titlemarker += '</table>';

      // var string = response[0].vehicle_no + ' - ' + response[0].vehicle_name + "<br>" +
      //   "GPS Time : " + response[0].auto_last_update + "<br>Position : " + response[0].auto_last_position + "<br>Coord : " + response[0].auto_last_lat + ", " + response[0].auto_last_long + "<br>" +
      //   "Engine : " + response[0].auto_last_engine + "<br>" +
      //   "Speed : " + rounded + " kph </br>"+ devicestatus + "Ritase : " + response[0].auto_last_ritase + "</br>" +
      //   "<a href='<?php echo base_url()?>maps/tracking/" + response[0].vehicle_id + "' target='_blank'>Tracking</a>";

       infowindowkedua = new google.maps.InfoWindow({
        content: titlemarker,
        maxWidth: 300
      });
      DeleteMarkers(response[0].vehicle_device);
      DeleteMarkerspertama(response[0].vehicle_device);


      if (response[0].auto_last_road == "muatan") {
        laststatus = 'GPS Online';
        laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
        if (rounded == 0 && response[0].auto_last_engine == "ON") {
          // console.log("muatan : sikon 1");
          // ICON UNGU
          var icon = {
            // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
            path: car,
            scale: .5,
            // anchor: new google.maps.Point(25,10),
            // scaledSize: new google.maps.Size(30,20),
            strokeColor: 'white',
            strokeWeight: .10,
            fillOpacity: 1,
            fillColor: '#ffff00',
            offset: '5%'
          };
        }else if (rounded > 0 && response[0].auto_last_engine == "ON") {
          // console.log("muatan : sikon 2");
          laststatus = 'GPS Online';
          laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
          // ICON HIJAU
          var icon = {
            // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
            path: car,
            scale: .5,
            // anchor: new google.maps.Point(25,10),
            // scaledSize: new google.maps.Size(30,20),
            strokeColor: 'white',
            strokeWeight: .10,
            fillOpacity: 1,
            fillColor: '#00b300',
            offset: '5%'
          };
        }else {
          // console.log("muatan : sikon 3");
          laststatus = 'GPS Online';
          laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
          // ICON BIRU
          var icon = {
            // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
            path: car,
            scale: .5,
            // anchor: new google.maps.Point(25,10),
            // scaledSize: new google.maps.Size(30,20),
            strokeColor: 'white',
            strokeWeight: .10,
            fillOpacity: 1,
            fillColor: '#ff0040',
            offset: '5%'
          };
        }
      }else {
        laststatus = 'GPS Online';
        laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
        if (rounded > 0 && response[0].auto_last_engine == "ON") {
          // console.log("kosongan : sikon 1");
          // ICON HIJAU
          var icon = {
            // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
            path: car,
            scale: .5,
            // anchor: new google.maps.Point(25,10),
            // scaledSize: new google.maps.Size(30,20),
            strokeColor: 'white',
            strokeWeight: .10,
            fillOpacity: 1,
            fillColor: '#0000FF',
            offset: '5%'
          };
        }else if (rounded == 0 && response[0].auto_last_engine == "ON") {
          // console.log("kosongan : sikon 2");
          laststatus = 'GPS Online';
          laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
          // ICON UNGU
          var icon = {
            // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
            path: car,
            scale: .5,
            // anchor: new google.maps.Point(25,10),
            // scaledSize: new google.maps.Size(30,20),
            strokeColor: 'white',
            strokeWeight: .10,
            fillOpacity: 1,
            fillColor: '#ffff00',
            offset: '5%'
          };
        }else {
          // console.log("kosongan : sikon 3");
          // ICON BIRU
          var icon = {
            // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
            path: car,
            scale: .5,
            // anchor: new google.maps.Point(25,10),
            // scaledSize: new google.maps.Size(30,20),
            strokeColor: 'white',
            strokeWeight: .10,
            fillOpacity: 1,
            fillColor: '#ff0040',
            offset: '5%'
          };
        }
      }

      markernya = new google.maps.Marker({
        map: map,
        icon: icon,
        position: new google.maps.LatLng(parseFloat(response[0].auto_last_lat), parseFloat(response[0].auto_last_long)),
        title: response[0].vehicle_no,
        // + ' - ' + value.vehicle_name + value.driver + "\n" +
        //   "GPS Time : " + value.gps.gps_date_fmt + " " + value.gps.gps_time_fmt + "\n" + value.gps.georeverse.display_name + "\n" + value.gps.gps_latitude_real_fmt + ", " + value.gps.gps_longitude_real_fmt + "\n" +
        //   "Speed : " + value.gps.gps_speed + " kph",
        id: response[0].vehicle_device
      });
      markerss.push(markernya);
      icon.rotation = Math.ceil(response[0].auto_last_course);
      markernya.setIcon(icon);


      // map.setZoom(18);
      infowindowkedua.open(map, markernya);
      map.setCenter(center);
      markernya.setPosition(center);

      // ON HOVER START
      google.maps.event.addListener(markernya, 'mouseover', function(){
        var varthis = this;
        setTimeoutConst = setTimeout(function() {
          console.log("mouseover 2second on forgetcenter : from table");
            infowindowonsimultan = new google.maps.InfoWindow({
              content: titlemarker,
              maxWidth: 300
            });

           infowindowonsimultan.setContent(titlemarker);
           infowindowonsimultan.open(map, varthis);
        }, 2000);
      });

      // assuming you also want to hide the infowindow when user mouses-out
      google.maps.event.addListener(markernya, 'mouseout', function(){
        console.log("mouseout on forgetcenter : from table");
        clearTimeout(setTimeoutConst);
          infowindowonsimultan.close();
      });
      // ON HOVER END

      google.maps.event.addListener(markernya, 'click', function(evt){
        console.log("icon map di klik from table");
        infowindow2.close();
        infowindowkedua.close();
        infowindow.close();

        var num         = Number(response[0].auto_last_speed);
        var roundstring = num.toFixed(0);
        var rounded     = Number(roundstring);

        // var string = response[0].vehicle_no + ' - ' + response[0].vehicle_name + "<br>" +
        //   "GPS Time : " + response[0].auto_last_update + "<br>Position : " + response[0].auto_last_position + "<br>Coord : " + response[0].auto_last_lat + ", " + response[0].auto_last_long + "<br>" +
        //   "Engine : " + response[0].auto_last_engine + "<br>" +
        //   "Speed : " + rounded + " kph" + "<br>" +
        //   "<a href='<?php echo base_url()?>maps/tracking/" + response[0].vehicle_id + "' target='_blank'>Tracking</a>";

         infowindowkedua = new google.maps.InfoWindow({
          content: titlemarker,
          maxWidth: 300
        });
        // DeleteMarkers(response[0].vehicle_device);
        // DeleteMarkerspertama(response[0].vehicle_device);

          var center = {lat : parseFloat(response[0].auto_last_lat), lng: parseFloat(response[0].auto_last_long)};
          infowindowkedua.setContent(titlemarker);
          map.setCenter(markernya.position);
          markernya.setPosition(markernya.position);
          infowindowkedua.open(map, this);
      });

    }, "json");
  }

// KLIK DARI SIDEBAR
  function forgetcenter2(deviceid){
    // console.log("device id forgetcenter 2 : ", deviceid);
    var data = {device_id : deviceid};

    if (infowindowkedua) {
        infowindowkedua.close();
    }

    if (infowindow) {
        infowindow.close();
    }

    if (infowindow2) {
        infowindow2.close();
    }

    $.post("<?php echo base_url() ?>maps/getdetailbydevid", data, function(response){
      console.log("forgetcenter2 on sidebar click : ", response);
      // DEVICE STATUS (CAMERA ONLINE / OFFLINE)
      if (response[0].devicestatusfixnya) {
        // console.log("devicestatus ada : ");
        if (response[0].devicestatusfixnya == 1) {
          var devicestatus = "Camera : Online <br>" ;
        }else {
          var devicestatus = "Camera : Offline <br>" ;
        }
      }else {
        if (response[0].devicestatusfixnya == "") {
          var devicestatus = "";
        }else if (response[0].devicestatusfixnya == 0) {
          var devicestatus = "Camera : Offline </br>";
        }else {
          var devicestatus = "";
        }
      }

      if (response[0].drivername) {
        var drivername = response[0].drivername;
       if (response[0].driverimage) {
         console.log("sikon 1");
         if (response[0].driverimage != 0) {
           console.log("sikon 2");
           // var showdriver   = "<a href='#' onclick='getmodaldriver("+datadriver[0]+");'>"+ datadriver[1] +"</a>";
           var detaildriver = '<img src="<?php echo base_url().$this->config->item("dir_photo");?>'+response[0].driverimage+'" width="100px;" height="100px;">';
         }else {
           var detaildriver = drivername + ' </br> No Driver Image';
         }
       }else {
         var detaildriver = drivername + ' </br> No Driver Image';
       }
      }

      // console.log("devicestatus : ", response[0].devicestatusfixnya);
      // console.log("devicestatus : ", devicestatus);

      var center = {lat : parseFloat(response[0].auto_last_lat), lng: parseFloat(response[0].auto_last_long)};
      // console.log("center : ", center);
      var num         = Number(response[0].auto_last_speed);
      var roundstring = num.toFixed(0);
      var rounded     = Number(roundstring);

      var addresssplit = response[0].auto_last_position.split(" ");
      var inarea       = response[0].auto_last_position.split(",");
      // console.log("addresssplit : ", addresssplit);

      var addressfix = bibarea.includes(addresssplit[0]);
      if (addressfix) {
        var addressfix = inarea[0];
      }else {
        var addressfix = response[0].auto_last_position;
      }

      titlemarker = "";
      titlemarker += '<table class="table" style="font-size:12px;">';
        titlemarker += '<tr>';
          titlemarker += '<td>'+detaildriver+'</td>';
          titlemarker += '<td>';
            titlemarker += response[0].vehicle_no + ' - ' + response[0].vehicle_name +'</br>';
            titlemarker += 'Driver : ' + drivername +'</br>';
            titlemarker += 'Gps Time : ' + response[0].auto_last_update+ '</br>';
            titlemarker += 'Position : ' + addressfix + '</br>';
            titlemarker += 'Coord : ' + response[0].auto_last_lat + ", " + response[0].auto_last_long + '</br>';
            titlemarker += 'Engine : ' + response[0].auto_last_engine + '</br>';
            titlemarker += 'Speed : ' + rounded + ' kph </br>';
            titlemarker +=  devicestatus;
            titlemarker += 'Ritase : ' + response[0].auto_last_ritase + '</br>';
            titlemarker += '<a href="<?php echo base_url()?>maps/tracking/'+response[0].vehicle_id+'" target="_blank">Tracking</a> </br>';
            // titlemarker +=   lct + imglct;
          titlemarker += '</td>';
        titlemarker += '</tr>';
      titlemarker += '</table>';

      // var string = response[0].vehicle_no + ' - ' + response[0].vehicle_name + "<br>" +
      //   "GPS Time : " + response[0].auto_last_update + "<br>Position : " + response[0].auto_last_position + "<br>Coord : " + response[0].auto_last_lat + ", " + response[0].auto_last_long + "<br>" +
      //   "Engine : " + response[0].auto_last_engine + "<br>" +
      //   "Speed : " + rounded + " kph </br>"+ devicestatus + "Ritase : " + response[0].auto_last_ritase + "</br>" +
      //   "<a href='<?php echo base_url()?>maps/tracking/" + response[0].vehicle_id + "' target='_blank'>Tracking</a>";

       infowindowkedua = new google.maps.InfoWindow({
        content: titlemarker,
        maxWidth: 300
      });
      DeleteMarkers(response[0].vehicle_device);
      DeleteMarkerspertama(response[0].vehicle_device);


      if (response[0].auto_last_road == "muatan") {
        laststatus = 'GPS Online';
        laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
        if (rounded == 0 && response[0].auto_last_engine == "ON") {
          // console.log("muatan : sikon 1");
          // ICON UNGU
          var icon = {
            // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
            path: car,
            scale: .5,
            // anchor: new google.maps.Point(25,10),
            // scaledSize: new google.maps.Size(30,20),
            strokeColor: 'white',
            strokeWeight: .10,
            fillOpacity: 1,
            fillColor: '#ffff00',
            offset: '5%'
          };
        }else if (rounded > 0 && response[0].auto_last_engine == "ON") {
          // console.log("muatan : sikon 2");
          laststatus = 'GPS Online';
          laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
          // ICON HIJAU
          var icon = {
            // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
            path: car,
            scale: .5,
            // anchor: new google.maps.Point(25,10),
            // scaledSize: new google.maps.Size(30,20),
            strokeColor: 'white',
            strokeWeight: .10,
            fillOpacity: 1,
            fillColor: '#00b300',
            offset: '5%'
          };
        }else {
          // console.log("muatan : sikon 3");
          laststatus = 'GPS Online';
          laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
          // ICON BIRU
          var icon = {
            // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
            path: car,
            scale: .5,
            // anchor: new google.maps.Point(25,10),
            // scaledSize: new google.maps.Size(30,20),
            strokeColor: 'white',
            strokeWeight: .10,
            fillOpacity: 1,
            fillColor: '#ff0040',
            offset: '5%'
          };
        }
      }else {
        laststatus = 'GPS Online';
        laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
        if (rounded > 0 && response[0].auto_last_engine == "ON") {
          // console.log("kosongan : sikon 1");
          // ICON HIJAU
          var icon = {
            // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
            path: car,
            scale: .5,
            // anchor: new google.maps.Point(25,10),
            // scaledSize: new google.maps.Size(30,20),
            strokeColor: 'white',
            strokeWeight: .10,
            fillOpacity: 1,
            fillColor: '#0000FF',
            offset: '5%'
          };
        }else if (rounded == 0 && response[0].auto_last_engine == "ON") {
          // console.log("kosongan : sikon 2");
          laststatus = 'GPS Online';
          laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
          // ICON UNGU
          var icon = {
            // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
            path: car,
            scale: .5,
            // anchor: new google.maps.Point(25,10),
            // scaledSize: new google.maps.Size(30,20),
            strokeColor: 'white',
            strokeWeight: .10,
            fillOpacity: 1,
            fillColor: '#ffff00',
            offset: '5%'
          };
        }else {
          // console.log("kosongan : sikon 3");
          // ICON BIRU
          var icon = {
            // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
            path: car,
            scale: .5,
            // anchor: new google.maps.Point(25,10),
            // scaledSize: new google.maps.Size(30,20),
            strokeColor: 'white',
            strokeWeight: .10,
            fillOpacity: 1,
            fillColor: '#ff0040',
            offset: '5%'
          };
        }
      }

      markernya = new google.maps.Marker({
        map: map,
        icon: icon,
        position: new google.maps.LatLng(parseFloat(response[0].auto_last_lat), parseFloat(response[0].auto_last_long)),
        title: response[0].vehicle_no,
        // + ' - ' + value.vehicle_name + value.driver + "\n" +
        //   "GPS Time : " + value.gps.gps_date_fmt + " " + value.gps.gps_time_fmt + "\n" + value.gps.georeverse.display_name + "\n" + value.gps.gps_latitude_real_fmt + ", " + value.gps.gps_longitude_real_fmt + "\n" +
        //   "Speed : " + value.gps.gps_speed + " kph",
        id: response[0].vehicle_device
      });
      markerss.push(markernya);
      icon.rotation = Math.ceil(response[0].auto_last_course);
      markernya.setIcon(icon);


      // map.setZoom(18);
      infowindowkedua.open(map, markernya);
      map.setCenter(center);
      markernya.setPosition(center);

      // ON HOVER START
      google.maps.event.addListener(markernya, 'mouseover', function(){
        var varthis = this;
        setTimeoutConst = setTimeout(function() {
          console.log("mouseover 2second on forgetcenter2");
            infowindowonsimultan = new google.maps.InfoWindow({
              content: titlemarker,
              maxWidth: 300
            });

           infowindowonsimultan.setContent(titlemarker);
           infowindowonsimultan.open(map, varthis);
        }, 2000);
      });

      // assuming you also want to hide the infowindow when user mouses-out
      google.maps.event.addListener(markernya, 'mouseout', function(){
        console.log("mouseout on forgetcenter2");
        clearTimeout(setTimeoutConst);
          infowindowonsimultan.close();
      });
      // ON HOVER END

      google.maps.event.addListener(markernya, 'click', function(evt){
        console.log("icon map di klik from getcenter 2");
        infowindow2.close();
        infowindowkedua.close();
        infowindow.close();

        var num         = Number(response[0].auto_last_speed);
        var roundstring = num.toFixed(0);
        var rounded     = Number(roundstring);

        // var string = response[0].vehicle_no + ' - ' + response[0].vehicle_name + "<br>" +
        //   "GPS Time : " + response[0].auto_last_update + "<br>Position : " + response[0].auto_last_position + "<br>Coord : " + response[0].auto_last_lat + ", " + response[0].auto_last_long + "<br>" +
        //   "Engine : " + response[0].auto_last_engine + "<br>" +
        //   "Speed : " + rounded + " kph" + "<br>" +
        //   "<a href='<?php echo base_url()?>maps/tracking/" + response[0].vehicle_id + "' target='_blank'>Tracking</a>";

         infowindowkedua = new google.maps.InfoWindow({
          content: titlemarker,
          maxWidth: 300
        });
        // DeleteMarkers(response[0].vehicle_device);
        // DeleteMarkerspertama(response[0].vehicle_device);

          var center = {lat : parseFloat(response[0].auto_last_lat), lng: parseFloat(response[0].auto_last_long)};
          infowindowkedua.setContent(titlemarker);
          map.setCenter(markernya.position);
          markernya.setPosition(markernya.position);
          infowindowkedua.open(map, this);
      });

    }, "json");
  }

  dragElement(document.getElementById("modallistvehicle"));

function dragElement(elmnt) {
  var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
  if (document.getElementById(elmnt.id + "header")) {
    // if present, the header is where you move the DIV from:
    document.getElementById(elmnt.id + "header").onmousedown = dragMouseDown;
  } else {
    // otherwise, move the DIV from anywhere inside the DIV:
    elmnt.onmousedown = dragMouseDown;
  }

  function dragMouseDown(e) {
    e = e || window.event;
    e.preventDefault();
    // get the mouse cursor position at startup:
    pos3 = e.clientX;
    pos4 = e.clientY;
    document.onmouseup = closeDragElement;
    // call a function whenever the cursor moves:
    document.onmousemove = elementDrag;
  }

  function elementDrag(e) {
    e = e || window.event;
    e.preventDefault();
    // calculate the new cursor position:
    pos1 = pos3 - e.clientX;
    pos2 = pos4 - e.clientY;
    pos3 = e.clientX;
    pos4 = e.clientY;
    // set the element's new position:
    elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
    elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
  }

  function closeDragElement() {
    // stop moving when mouse button is released:
    document.onmouseup = null;
    document.onmousemove = null;
  }
}

function forsearchinput(){
  var deviceid = $("#searchnopol").val();
  console.log("device id forsearchinput : ", deviceid);

  var data = {key : deviceid};

  if (infowindowkedua) {
      infowindowkedua.close();
  }

  if (infowindow) {
      infowindow.close();
  }

  if (infowindow2) {
      infowindow2.close();
  }

  $.post("<?php echo base_url() ?>maps/forsearchvehicle", data, function(response){
    console.log("ini respon pencarian : ", response);
    // DEVICE STATUS (CAMERA ONLINE / OFFLINE)
    if (response[0].devicestatusfixnya) {
      // console.log("devicestatus ada : ");
      if (response[0].devicestatusfixnya == 1) {
        var devicestatus = "Camera : Online <br>" ;
      }else {
        var devicestatus = "Camera : Offline <br>" ;
      }
    }else {
      if (response[0].devicestatusfixnya == "") {
        var devicestatus = "";
      }else if (response[0].devicestatusfixnya == 0) {
        var devicestatus = "Camera : Offline </br>";
      }else {
        var devicestatus = "";
      }
    }

    if (response[0].drivername) {
      var drivername = response[0].drivername;
     if (response[0].driverimage) {
       console.log("sikon 1");
       if (response[0].driverimage != 0) {
         console.log("sikon 2");
         // var showdriver   = "<a href='#' onclick='getmodaldriver("+datadriver[0]+");'>"+ datadriver[1] +"</a>";
         var detaildriver = '<img src="<?php echo base_url().$this->config->item("dir_photo");?>'+response[0].driverimage+'" width="100px;" height="100px;">';
       }else {
         var detaildriver = drivername + ' </br> No Driver Image';
       }
     }else {
       var detaildriver = drivername + ' </br> No Driver Image';
     }
    }

    // console.log("devicestatus : ", response[0].devicestatusfixnya);
    // console.log("devicestatus : ", devicestatus);

    var center = {lat : parseFloat(response[0].auto_last_lat), lng: parseFloat(response[0].auto_last_long)};
    // console.log("center : ", center);
    var num         = Number(response[0].auto_last_speed);
    var roundstring = num.toFixed(0);
    var rounded     = Number(roundstring);

    var addresssplit = response[0].auto_last_position.split(" ");
    var inarea       = response[0].auto_last_position.split(",");
    // console.log("addresssplit : ", addresssplit);

    var addressfix = bibarea.includes(addresssplit[0]);
    if (addressfix) {
      var addressfix = inarea[0];
    }else {
      var addressfix = response[0].auto_last_position;
    }

    titlemarker = "";
    titlemarker += '<table class="table" style="font-size:12px;">';
      titlemarker += '<tr>';
        titlemarker += '<td>'+detaildriver+'</td>';
        titlemarker += '<td>';
          titlemarker += response[0].vehicle_no + ' - ' + response[0].vehicle_name +'</br>';
          titlemarker += 'Driver : ' + drivername +'</br>';
          titlemarker += 'Gps Time : ' + response[0].auto_last_update+ '</br>';
          titlemarker += 'Position : ' + addressfix + '</br>';
          titlemarker += 'Coord : ' + response[0].auto_last_lat + ", " + response[0].auto_last_long + '</br>';
          titlemarker += 'Engine : ' + response[0].auto_last_engine + '</br>';
          titlemarker += 'Speed : ' + rounded + ' kph </br>';
          titlemarker +=  devicestatus;
          titlemarker += 'Ritase : ' + response[0].auto_last_ritase + '</br>';
          titlemarker += '<a href="<?php echo base_url()?>maps/tracking/"' + response[0].vehicle_id + '"target="_blank">Tracking</a> </br>';
          // titlemarker +=   lct + imglct;
        titlemarker += '</td>';
      titlemarker += '</tr>';
    titlemarker += '</table>';

    // var string = response[0].vehicle_no + ' - ' + response[0].vehicle_name + "<br>" +
    //   "GPS Time : " + response[0].auto_last_update + "<br>Position : " + response[0].auto_last_position + "<br>Coord : " + response[0].auto_last_lat + ", " + response[0].auto_last_long + "<br>" +
    //   "Engine : " + response[0].auto_last_engine + "<br>" +
    //   "Speed : " + rounded + " kph </br>"+ devicestatus + "Ritase : " + response[0].auto_last_ritase + "</br>" +
    //   "<a href='<?php echo base_url()?>maps/tracking/" + response[0].vehicle_id + "' target='_blank'>Tracking</a>";

     infowindowkedua = new google.maps.InfoWindow({
      content: titlemarker,
      maxWidth: 300
    });
    DeleteMarkers(response[0].vehicle_device);
    DeleteMarkerspertama(response[0].vehicle_device);


    if (response[0].auto_last_road == "muatan") {
      laststatus = 'GPS Online';
      laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
      if (rounded == 0 && response[0].auto_last_engine == "ON") {
        // console.log("muatan : sikon 1");
        // ICON UNGU
        var icon = {
          // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
          path: car,
          scale: .5,
          // anchor: new google.maps.Point(25,10),
          // scaledSize: new google.maps.Size(30,20),
          strokeColor: 'white',
          strokeWeight: .10,
          fillOpacity: 1,
          fillColor: '#ffff00',
          offset: '5%'
        };
      }else if (rounded > 0 && response[0].auto_last_engine == "ON") {
        // console.log("muatan : sikon 2");
        laststatus = 'GPS Online';
        laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
        // ICON HIJAU
        var icon = {
          // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
          path: car,
          scale: .5,
          // anchor: new google.maps.Point(25,10),
          // scaledSize: new google.maps.Size(30,20),
          strokeColor: 'white',
          strokeWeight: .10,
          fillOpacity: 1,
          fillColor: '#00b300',
          offset: '5%'
        };
      }else {
        // console.log("muatan : sikon 3");
        laststatus = 'GPS Online';
        laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
        // ICON BIRU
        var icon = {
          // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
          path: car,
          scale: .5,
          // anchor: new google.maps.Point(25,10),
          // scaledSize: new google.maps.Size(30,20),
          strokeColor: 'white',
          strokeWeight: .10,
          fillOpacity: 1,
          fillColor: '#ff0040',
          offset: '5%'
        };
      }
    }else {
      laststatus = 'GPS Online';
      laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
      if (rounded > 0 && response[0].auto_last_engine == "ON") {
        // console.log("kosongan : sikon 1");
        // ICON HIJAU
        var icon = {
          // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
          path: car,
          scale: .5,
          // anchor: new google.maps.Point(25,10),
          // scaledSize: new google.maps.Size(30,20),
          strokeColor: 'white',
          strokeWeight: .10,
          fillOpacity: 1,
          fillColor: '#0000FF',
          offset: '5%'
        };
      }else if (rounded == 0 && response[0].auto_last_engine == "ON") {
        // console.log("kosongan : sikon 2");
        laststatus = 'GPS Online';
        laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
        // ICON UNGU
        var icon = {
          // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
          path: car,
          scale: .5,
          // anchor: new google.maps.Point(25,10),
          // scaledSize: new google.maps.Size(30,20),
          strokeColor: 'white',
          strokeWeight: .10,
          fillOpacity: 1,
          fillColor: '#ffff00',
          offset: '5%'
        };
      }else {
        // console.log("kosongan : sikon 3");
        // ICON BIRU
        var icon = {
          // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
          path: car,
          scale: .5,
          // anchor: new google.maps.Point(25,10),
          // scaledSize: new google.maps.Size(30,20),
          strokeColor: 'white',
          strokeWeight: .10,
          fillOpacity: 1,
          fillColor: '#ff0040',
          offset: '5%'
        };
      }
    }

    showmapmode();

    markernya = new google.maps.Marker({
      map: map,
      icon: icon,
      position: new google.maps.LatLng(parseFloat(response[0].auto_last_lat), parseFloat(response[0].auto_last_long)),
      title: response[0].vehicle_no,
      // + ' - ' + value.vehicle_name + value.driver + "\n" +
      //   "GPS Time : " + value.gps.gps_date_fmt + " " + value.gps.gps_time_fmt + "\n" + value.gps.georeverse.display_name + "\n" + value.gps.gps_latitude_real_fmt + ", " + value.gps.gps_longitude_real_fmt + "\n" +
      //   "Speed : " + value.gps.gps_speed + " kph",
      id: response[0].vehicle_device
    });
    markerss.push(markernya);
    icon.rotation = Math.ceil(response[0].auto_last_course);
    markernya.setIcon(icon);


    // map.setZoom(18);
    infowindowkedua.open(map, markernya);
    map.setCenter(center);
    markernya.setPosition(center);

    // ON HOVER START
    google.maps.event.addListener(markernya, 'mouseover', function(){
      var varthis = this;
      setTimeoutConst = setTimeout(function() {
        console.log("mouseover 2second on search key");
          infowindowonsimultan = new google.maps.InfoWindow({
            content: titlemarker,
            maxWidth: 300
          });

         infowindowonsimultan.setContent(titlemarker);
         infowindowonsimultan.open(map, varthis);
      }, 2000);
    });

    // assuming you also want to hide the infowindow when user mouses-out
    google.maps.event.addListener(markernya, 'mouseout', function(){
      console.log("mouseout on search key");
      clearTimeout(setTimeoutConst);
        infowindowonsimultan.close();
    });
    // ON HOVER END

    google.maps.event.addListener(markernya, 'click', function(evt){
      console.log("icon map di klik from search key");
      infowindow2.close();
      infowindowkedua.close();
      infowindow.close();

      var num         = Number(response[0].auto_last_speed);
      var roundstring = num.toFixed(0);
      var rounded     = Number(roundstring);

      // var string = response[0].vehicle_no + ' - ' + response[0].vehicle_name + "<br>" +
      //   "GPS Time : " + response[0].auto_last_update + "<br>Position : " + response[0].auto_last_position + "<br>Coord : " + response[0].auto_last_lat + ", " + response[0].auto_last_long + "<br>" +
      //   "Engine : " + response[0].auto_last_engine + "<br>" +
      //   "Speed : " + rounded + " kph" + "<br>" +
      //   "<a href='<?php echo base_url()?>maps/tracking/" + response[0].vehicle_id + "' target='_blank'>Tracking</a>";

       infowindowkedua = new google.maps.InfoWindow({
        content: titlemarker,
        maxWidth: 300
      });
      // DeleteMarkers(response[0].vehicle_device);
      // DeleteMarkerspertama(response[0].vehicle_device);

        var center = {lat : parseFloat(response[0].auto_last_lat), lng: parseFloat(response[0].auto_last_long)};
        infowindowkedua.setContent(titlemarker);
        map.setCenter(markernya.position);
        markernya.setPosition(markernya.position);
        infowindowkedua.open(map, this);
    });

  }, "json");
}

function getmodaldriver(iddriver){
  alert("Data Driver with ID : " + iddriver + " is Coming Soon");
}

function notifnya(content){
  if (content == 1) {
    var notif = "Camera is Online";
  }else {
    var notif = "Camera is Offline";
  }
  // console.log("masuk ke notif", content);
  $.toast().reset('all');
  $.toast({
      heading: 'Alert',
      text: notif,
      position: 'top-center',
      stack: false
  })
}

var sikonvideo;
function getthevideo(imeimv03, vdevice){
  sikonvideo = 1;
  var imei = imeimv03;
  // console.log("imei : ", imei);
  // console.log("vdevice : ", vdevice);
  // console.log("markernya : ", markernya);
  var data = {deviceid : vdevice, imei : imei};
  // console.log("markers", markers);
  // console.log("markerss", markerss);
  DeleteMarkers(vdevice);
  DeleteMarkerspertama(vdevice);
  $.post("<?php echo base_url() ?>maps/getthisvideo", data, function(response){
    // console.log("response : ", response);

      // console.log("masuk notif on click");
      notifnya(response.devicestatus);

    if (response.vehicle[0].auto_last_engine == "ON" && response.vehicle[0].auto_last_speed > 0) {
      laststatus = 'GPS Online';
      laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
      var icon = {
        // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
        path: car,
        scale: .5,
        // anchor: new google.maps.Point(25,10),
        // scaledSize: new google.maps.Size(30,20),
        strokeColor: 'white',
        strokeWeight: .10,
        fillOpacity: 1,
        fillColor: '#00b300',
        offset: '5%'
      };
    } else {
      laststatus = 'GPS Online';
      laststatus2 = '<h5 class="text-medium full-width"><span class="label label-sm label-success">GPS Online</span></h5>';
      var icon = {
        // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
        path: car,
        scale: .5,
        // anchor: new google.maps.Point(25,10),
        // scaledSize: new google.maps.Size(30,20),
        strokeColor: 'white',
        strokeWeight: .10,
        fillOpacity: 1,
        fillColor: '#ff0040',
        offset: '5%'
      };
    }

    markernya = new google.maps.Marker({
      map: map,
      icon: icon,
      position: new google.maps.LatLng(parseFloat(response.vehicle[0].auto_last_lat), parseFloat(response.vehicle[0].auto_last_long)),
      title: response.vehicle[0].vehicle_no,
      // + ' - ' + value.vehicle_name + value.driver + "\n" +
      //   "GPS Time : " + value.gps.gps_date_fmt + " " + value.gps.gps_time_fmt + "\n" + value.gps.georeverse.display_name + "\n" + value.gps.gps_latitude_real_fmt + ", " + value.gps.gps_longitude_real_fmt + "\n" +
      //   "Speed : " + value.gps.gps_speed + " kph",
      id: response.vehicle[0].vehicle_device
    });
    markerss.push(markernya);
    icon.rotation = Math.ceil(response.vehicle[0].auto_last_course);
    markernya.setIcon(icon);

    var center = {lat : parseFloat(response.vehicle[0].auto_last_lat), lng: parseFloat(response.vehicle[0].auto_last_long)};
    map.setZoom(18);
    map.setCenter(center);
    markernya.setPosition(center);

    google.maps.event.addListener(markernya, 'click', function(evt){
      console.log("data marker on load diklik kedua", data);
      infowindow2.close();
      infowindowkedua.close();
      infowindow.close();

      var num         = Number(response.vehicle[0].auto_last_speed);
      var roundstring = num.toFixed(0);
      var rounded     = Number(roundstring);

      var string = response.vehicle[0].vehicle_no + ' - ' + response.vehicle[0].vehicle_name + "<br>" +
        "GPS Time : " + response.vehicle[0].auto_last_update + "<br>Position : " + response.vehicle[0].auto_last_position + "<br>Coord : " + response.vehicle[0].auto_last_lat + ", " + response.vehicle[0].auto_last_long + "<br>" +
        "Engine : " + response.vehicle[0].auto_last_engine + "<br>" +
        "Speed : " + rounded + " kph" + "<br>" +
        "<a href='<?php echo base_url()?>maps/tracking/" + response.vehicle[0].vehicle_id + "' target='_blank'>Tracking</a>";

       infowindowkedua = new google.maps.InfoWindow({
        content: string,
        maxWidth: 300
      });
      // DeleteMarkers(response[0].vehicle_device);
      // DeleteMarkerspertama(response[0].vehicle_device);

      // var center = {lat : parseFloat(response.vehicle[0].auto_last_lat), lng: parseFloat(response.vehicle[0].auto_last_long)};
      infowindowkedua.setContent(string);
      map.setCenter(markernya.position);
      markernya.setPosition(markernya.position);
      infowindowkedua.open(map, this);
    });

    $("#streamcontent").html('<iframe src="'+response.urlfix+'" width="400" height="450" frameborder="0" style="border:0;"></iframe>');
    // document.getElementById("cardbodyformode").setAttribute("style", "height: 95%;");
    // document.getElementById("mapsnya").setAttribute("style", "width: 30%; height: 50px;");
    document.getElementById("mapmode").setAttribute("style", "width: 31%; height: 460%; margin-left: 70%; margin-top: -42%;");
    document.getElementById("btnmaptable").setAttribute("style", "margin-left: 21%; z-index: 1; margin-top: 1.3%; position: absolute;");
    document.getElementById("btnmapmode").setAttribute("style", "margin-left: 84%; z-index: 1; margin-top: 3%; position: absolute;");
    document.getElementById("videomode").setAttribute("style", "width: 70%; height: 400%; margin-top:-1%; margin-left:-1%;");
    $("#btnvideomode").hide();
    $("#btnmapmode").show();
    $("#mapmode").show();
    $("#streamcontent").show();
    $("#videomode").show();
  }, "json");
}

function showmapmode(){
  $("#videomode").hide();
  $("#streamcontent").hide();
  document.getElementById("btnvideomode").setAttribute("style", "margin-left: 83%; z-index: 1; margin-top: 1.6%; position: absolute;");
  $("#btnvideomode").show();
  $("#btnmapmode").hide();
  document.getElementById("btnmaptable").setAttribute("style", "margin-left: 88%; z-index: 1; margin-top: 1.3%; position: absolute;");
  document.getElementById("mapmode").setAttribute("style", "width: 98%; height: 380%; margin-left: 1%; margin-top:-3%;");
  $("#videomode").show();
}

function showvideomode(){
  if (sikonvideo == 1) {
    document.getElementById("mapmode").setAttribute("style", "width: 30%; height: 460%; margin-left: 70%; margin-top: -42%;");
    document.getElementById("btnmapmode").setAttribute("style", "margin-left: 84%; z-index: 1; margin-top: 3%; position: absolute;");
  }else {
    document.getElementById("mapmode").setAttribute("style", "width: 31%; height: 460%; margin-left: 68%; margin-top: -7%;");
    document.getElementById("btnmapmode").setAttribute("style", "margin-left: 82.5%; z-index: 1; margin-top: 1.6%; position: absolute;");
  }
  $("#btnvideomode").hide();
  $("#streamcontent").show();
  $("#videomode").show();
  // document.getElementById("cardbodyformode").setAttribute("style", "height: 95%;");
  document.getElementById("btnmaptable").setAttribute("style", "margin-left: 21%; z-index: 1; margin-top: 1.3%; position: absolute;");
  $("#mapmode").show();
}

// REALTIME ALERT START
var realtimealertarray        = [];
var realtimealertarraysummary = [];
var userid                    = '<?php echo $this->sess->user_id?>';
var intervalalert2, intervalalert1, intervalalert;
var soundisactive             = 1;
var alertloop = 0;

var intervalalert = setInterval(dataalert, 1000);

function dataalert(){
  // console.log("jalan");
  alertloop = alertloop + 1;
  if (realtimealertarray.length >= 1) {
    realtimealertarray = [];
  }
  var vehicle = '<?php echo json_encode($vehicledata); ?>';
  var obj     = JSON.parse(vehicle);
  // console.log("obj : ", obj);
  // console.log("alertloop : ", alertloop);
  var vehicledevice = obj[alertloop].vehicle_mv03;
  // console.log("vehicledevice : ", vehicledevice);
    if (vehicledevice != "0000") {
      getalertnow(vehicledevice);
    }
    if (alertloop == obj.length-1) {
      alertloop = 0;
    }
}

function getalertnow(devid){
  // console.log("devid : ", devid);
  // var vdevice    = $("#vehicledeviceforgetalert").val();
  // var vdevicefix = vdevice.split("@");
  $.post("<?php echo base_url() ?>securityevidence/realtimealert", {device : devid}, function(response){
    if (response.sizedata != 0) {
      console.log("response getalertnow : ", response);
      realtimealertarray.push(response.alertdata[0]);
      realtimealertarraysummary.push(response.alertdata[0]);
      var reversearray        = realtimealertarray.reverse();
      var reversearraysummary = realtimealertarraysummary.reverse();
      var html = "";

        for (var i = 0; i < reversearray.length; i++) {
          var addresssplit = reversearray[i].position.split(" ");
          var inarea       = reversearray[i].position.split(",");
          var positionfix  = bibarea.includes(addresssplit[0]);
          if (positionfix) {
            var positionfix = inarea[0];
          }else {
            var positionfix = response[0].auto_last_position;
          }

          if (response.stTypeis == "yes") {
            var alertname = '<span style="font-size: 12px; color:green; margin-left:1%;">' + response.vehiclenoandname +" : </span> <span style='color:red;font-size:12px;'>"+ reversearray[i].stType +'</span> : <span style="font-size:12px;">'+ reversearray[i].srcTm +' </span> '+
                                        '<span style="font-size: 12px; color:red;">'+ reversearray[i].type +'</span> :  <span style="font-size:12px;">'+ reversearray[i].time +' </span> <span style="font-size:12px;"><a style="font-size: 12px;" href="http://maps.google.com/maps?z=12&t=m&q=loc'+reversearray[i].Gps.mlat+','+reversearray[i].Gps.mlng+'" target="_blank">'+positionfix+'</span></a> </br>';
                                        // '<a style="font-size: 12px;" href="http://maps.google.com/maps?z=12&t=m&q=loc'+reversearray[i].Gps.mlat+','+reversearray[i].Gps.mlng+'">'+ reversearray[i].Gps.mlat +', '+ reversearray[i].Gps.mlng +' </a> </br>';
          }else {
            var alertname = '<span style="font-size: 12px; color:green; margin-left:1%;">' + response.vehiclenoandname +" : </span> <span style='color:red;'>"+ reversearray[i].type +'</span> : <span style="font-size:12px;">'+ reversearray[i].time +
                                      '<a style="font-size: 12px;" href="http://maps.google.com/maps?z=12&t=m&q=loc'+reversearray[i].Gps.mlat+','+reversearray[i].Gps.mlng+'" target="_blank">'+ positionfix+' </a></br>';
          }
          html += '<span class="subject">'+
           alertname +
           '</span>';
        }
      $("#realtimealertcontent").html(html);
        if (soundisactive == 1) {
          playsound();
        }
      $("#realtimealertshow").show();
      // REALTIME SUMMARY ALERT START
      console.log("summary alertinarray : ", realtimealertarraysummary);
        var htmlsummary = "";
        for (var j = 0; j < reversearraysummary.length; j++) {
          var addresssplit = reversearray[i].position.split(" ");
          var inarea       = reversearray[i].position.split(",");
          var positionfix  = bibarea.includes(addresssplit[0]);
          if (positionfix) {
            var positionfix = inarea[0];
          }else {
            var positionfix = response[0].auto_last_position;
          }

          if (response.stTypeis == "yes") {
            htmlsummary += '<tr>';
              // htmlsummary += '<td style="font-size: 12px;">'+ (j+1) +'. </td>';
              htmlsummary += '<td style="font-size: 12px; color:green;">'+ response.vehiclenoandname +'</td>';
              htmlsummary += '<td><span style="font-size:12px; color: red;">'+ reversearraysummary[j].stType + '</span>' + ' ' + ' <span style="font-size: 12px;"> ' +reversearraysummary[j].srcTm+'</span></td>';
              htmlsummary += '<td><span style="font-size:12px; color: red;">'+ reversearraysummary[j].type   + '</span>' + ' ' + ' <span style="font-size: 12px;"> ' + reversearraysummary[j].time +'</span></td>';
              htmlsummary += '<td style="font-size:12px;"><a style="font-size:12px;" href="http://maps.google.com/maps?z=12&t=m&q=loc'+reversearraysummary[j].Gps.mlat+','+reversearraysummary[j].Gps.mlng+'" target="_blank">'+ positionfix +' </a></td> </br>';
            htmlsummary += '</tr>';
          }else {
            htmlsummary += '<tr>';
              // htmlsummary += '<td style="font-size: 12px;">'+ (j+1) +'. </td>';
              htmlsummary += '<td style="font-size: 12px; color:green;">'+ response.vehiclenoandname +'</td>';
              htmlsummary += '<td><span style="font-size:12px; color: red;">'+ reversearraysummary[j].type   + '</span>' + ' ' + ' <span style="font-size: 12px;"> ' + reversearraysummary[j].time +'</span></td>';
              htmlsummary += '<td style="font-size:12px;"><a style="font-size:12px;" href="http://maps.google.com/maps?z=12&t=m&q=loc'+reversearraysummary[j].Gps.mlat+','+reversearraysummary[j].Gps.mlng+'" target="_blank">'+ positionfix +' </a></td> </br>';
            htmlsummary += '</tr>';
          }
        }
        $("#summaryalertcontent").html(htmlsummary);
        // $("#modalalertsummry").show();
      // REALTIME SUMMARY ALERT END
    }
  }, "json");
}


function closemodalalertrealtime(){
  // $("#modalalertrealtime").hide();
  $("#realtimealertshow").hide();
}

function alertsummary(){
  $("#modalalertsummry").show();
}

function closemodalsummaryalert(){
  $("#modalalertsummry").hide();
}

function playsound() {
  var audio = new Audio('<?php echo base_url() ?>assets/sounds/alert1.mp3');
    audio.play();
  // var sound = document.getElementById(soundObj);
  // sound.Play();
}

function activatesound(){
  if (soundisactive == 1) {
    soundisactive = 0;
    $("#activatesound2").show();
    $("#activatesound").hide();
  }else {
    soundisactive = 1;
    $("#activatesound2").hide();
    $("#activatesound").show();
  }
}

function activatesound2(){
  if (soundisactive == 1) {
    soundisactive = 0;
    $("#activatesound2").show();
    $("#activatesound").hide();
  }else {
    soundisactive = 1;
    $("#activatesound2").hide();
    $("#activatesound").show();
  }
}

var togglestatus = 0;
function realtimealerttoggle(){
  console.log("togglestatus : ", togglestatus);
  if (togglestatus == 0) {
    togglestatus = 1;
    document.getElementById("realtimealertshow").setAttribute("style", "position: fixed; width: 65%; background-color: white; min-height: 50px; max-height: 90px; overflow-y: auto; overflow-x: hidden; bottom: 0px; left: 17.5%; right: 0px; margin-bottom: 0px;");
  }else {
    togglestatus = 0;
    document.getElementById("realtimealertshow").setAttribute("style", "position: fixed; width: 65%; background-color: white; min-height: 110px; max-height: 140px; overflow-y: auto; overflow-x: hidden; bottom: 0px; left: 17.5%; right: 0px; margin-bottom: 0px;");
  }
    // $("#realtimealertshow").toggle();
}
// REALTIME ALERT END

// OVERLAY BIB MAP START
function addoverlay(map){
  console.log("masuk overlay");
  if (overlaystatus == 0) {
    console.log(map.getMapTypeId());
    map.setMapTypeId((map.getMapTypeId() === 'satellite') ? 'hidden' : google.maps.MapTypeId.ROADMAP);

    overlaystatus = 1;
    // INI BUAT KOORDINAT PORTBUNATI3_SMALL.PNG
    var latlngoverlay1                = new google.maps.LatLng(-3.752973, 115.627142);
    var latlngoverlay2                = new google.maps.LatLng(-3.741050, 115.651995);
    var jalanpcs1latlng1              = new google.maps.LatLng(-3.742245, 115.634781);
    var jalanpcs1latlng2              = new google.maps.LatLng(-3.731234, 115.658683);
    var jalanpcs2latlng1                = new google.maps.LatLng(-3.731293, 115.635119);
    var jalanpcs2latlng2                = new google.maps.LatLng(-3.719973, 115.658875);
    var jalanpcs3latlng1                = new google.maps.LatLng(-3.720716, 115.627392);
    var jalanpcs3latlng2                = new google.maps.LatLng(-3.709085, 115.650982);
    var jalanpcs4latlng1                = new google.maps.LatLng(-3.709082, 115.626623);
    var jalanpcs4latlng2                = new google.maps.LatLng(-3.696086, 115.650399);
    var jalanpcs5latlng1                = new google.maps.LatLng(-3.698382, 115.628647);
    var jalanpcs5latlng2                = new google.maps.LatLng(-3.686875, 115.650329);
    var jalanpcs5nikunglatlng1          = new google.maps.LatLng(-3.690207, 115.640326);
    var jalanpcs5nikunglatlng2          = new google.maps.LatLng(-3.684617, 115.651178);
    var jalanpcs5nikungpcs3barulatlng1  = new google.maps.LatLng(-3.685194, 115.642723);
    var jalanpcs5nikungpcs3barulatlng2  = new google.maps.LatLng(-3.679627, 115.653479);

    var jalanpcs5nikungpcs4barulatlng1  = new google.maps.LatLng(-3.679623, 115.643087);
    var jalanpcs5nikungpcs4barulatlng2  = new google.maps.LatLng(-3.672130, 115.658138);

    var jalanpcspatahancroplatlng1      = new google.maps.LatLng(-3.680263, 115.646667);
    var jalanpcspatahancroplatlng2      = new google.maps.LatLng(-3.675736, 115.651107);

    var jalanpcs5nikungpcs5barulatlng1  = new google.maps.LatLng(-3.672140, 115.643943);
    var jalanpcs5nikungpcs5barulatlng2  = new google.maps.LatLng(-3.665922, 115.657922);



    var jalanpcs6latlng1                = new google.maps.LatLng(-3.666083, 115.644821);
    var jalanpcs6latlng2                = new google.maps.LatLng(-3.661015, 115.656904);
    var jalanpcs7latlng1                = new google.maps.LatLng(-3.661060, 115.644165);
    var jalanpcs7latlng2                = new google.maps.LatLng(-3.653857, 115.655901);
    var jalanpcs8latlng1                = new google.maps.LatLng(-3.654903, 115.641802);
    var jalanpcs8latlng2                = new google.maps.LatLng(-3.648403, 115.653267);
    var jalanpcs9latlng1                = new google.maps.LatLng(-3.648949, 115.642282);
    var jalanpcs9latlng2                = new google.maps.LatLng(-3.642311, 115.653412);
    var jalanpcs10latlng1               = new google.maps.LatLng(-3.642658, 115.646624);
    var jalanpcs10latlng2               = new google.maps.LatLng(-3.636349, 115.655885);
    var jalanpcstekukankananataslatlng1 = new google.maps.LatLng(-3.642287, 115.647807);
    var jalanpcstekukankananataslatlng2 = new google.maps.LatLng(-3.624354, 115.659467);
    var jalanpcs15latlng1               = new google.maps.LatLng(-3.624377, 115.645157);
    var jalanpcs15latlng2               = new google.maps.LatLng(-3.613030, 115.657190);
    var jalanpcs16latlng1               = new google.maps.LatLng(-3.615390, 115.640438);
    var jalanpcs16latlng2               = new google.maps.LatLng(-3.604488, 115.664935);
    var jalanpcs17latlng1               = new google.maps.LatLng(-3.605948, 115.650892);
    var jalanpcs17latlng2               = new google.maps.LatLng(-3.591340, 115.675094);
    var jalanpcs18latlng1               = new google.maps.LatLng(-3.591364, 115.651680);
    var jalanpcs18latlng2               = new google.maps.LatLng(-3.579234, 115.666260);
    var jalanpcs19latlng1               = new google.maps.LatLng(-3.579299, 115.652807);
    var jalanpcs19latlng2               = new google.maps.LatLng(-3.574495, 115.659479);
    var jalanpcs20latlng1               = new google.maps.LatLng(-3.574582, 115.653795);
    var jalanpcs20latlng2               = new google.maps.LatLng(-3.571493, 115.657894);
    var jalanpcs21latlng1               = new google.maps.LatLng(-3.571526, 115.652658);
    var jalanpcs21latlng2               = new google.maps.LatLng(-3.568056, 115.656361);
    var jalanpcs22latlng1               = new google.maps.LatLng(-3.572094, 115.649348);
    var jalanpcs22latlng2               = new google.maps.LatLng(-3.566102, 115.657244);
    var jalanpcs23latlng1               = new google.maps.LatLng(-3.566103, 115.645197);
    var jalanpcs23latlng2               = new google.maps.LatLng(-3.558789, 115.661364);
    var jalanpcs24latlng1               = new google.maps.LatLng(-3.559233, 115.645378);
    var jalanpcs24latlng2               = new google.maps.LatLng(-3.554506, 115.656520);
    var jalanpcs25latlng1               = new google.maps.LatLng(-3.554939, 115.642562);
    var jalanpcs25latlng2               = new google.maps.LatLng(-3.547679, 115.657653);
    var jalanpcs26latlng1               = new google.maps.LatLng(-3.547848, 115.643332);
    var jalanpcs26latlng2               = new google.maps.LatLng(-3.540213, 115.657087);
    var jalanpcs27latlng1               = new google.maps.LatLng(-3.540904, 115.643003);
    var jalanpcs27latlng2               = new google.maps.LatLng(-3.535258, 115.656237);
    var jalanpcs28latlng1               = new google.maps.LatLng(-3.535323, 115.643353);
    var jalanpcs28latlng2               = new google.maps.LatLng(-3.527523, 115.656180);
    var jalanpcs29latlng1               = new google.maps.LatLng(-3.527815, 115.644625);
    var jalanpcs29latlng2               = new google.maps.LatLng(-3.523184, 115.654230);
    var jalanpcs30latlng1               = new google.maps.LatLng(-3.523197, 115.640709);
    var jalanpcs30latlng2               = new google.maps.LatLng(-3.516298, 115.655688);
    var jalanpcs32latlng1               = new google.maps.LatLng(-3.521122, 115.629392);
    var jalanpcs32latlng2               = new google.maps.LatLng(-3.514110, 115.644582);
    var jalanpcs33latlng1               = new google.maps.LatLng(-3.521091, 115.615832);
    var jalanpcs33latlng2               = new google.maps.LatLng(-3.514375, 115.629674);
    var jalanpcs34latlng1               = new google.maps.LatLng(-3.521108, 115.606516);
    var jalanpcs34latlng2               = new google.maps.LatLng(-3.515529, 115.620556);
    var jalanpcs36latlng1               = new google.maps.LatLng(-3.517714, 115.633010);
    var jalanpcs36latlng2               = new google.maps.LatLng(-3.506656, 115.648205);

    var imageportfix                    = "<?php echo base_url()?>assets/images/portbunati3_small.png";
    var imagejalanpcs1fix               = "<?php echo base_url()?>assets/images/pcs_1_new.png";
    var imagejalanpcs2fix              = "<?php echo base_url()?>assets/images/pcs_2_new.png";
    var imagejalanpcs3fix               = "<?php echo base_url()?>assets/images/pcs_3_new.png";
    var imagejalanpcs4fix               = "<?php echo base_url()?>assets/images/pcs_4_new.png";
    var imagejalanpcs5fix               = "<?php echo base_url()?>assets/images/pcs_5_new.png";
    var imagejalanpcs5nikungfix         = "<?php echo base_url()?>assets/images/pcs_2_baru.png";
    var imagejalanpcs5nikungpcs3barufix = "<?php echo base_url()?>assets/images/pcs_3_baru.png";
    var imagejalanpcs5nikungpcs4barufix = "<?php echo base_url()?>assets/images/pcs_4_baru.png";
    var imagejalanpcs5nikungpcs5barufix = "<?php echo base_url()?>assets/images/pcs_5_baru.png";

    var imagejalanpcspatahancrop5barufix = "<?php echo base_url()?>assets/images/patahan_tikungan5_baru_nobackground.png";

    var imagejalanpcs6fix                = "<?php echo base_url()?>assets/images/pcs_6_baru.png";
    var imagejalanpcs7fix                = "<?php echo base_url()?>assets/images/pcs_7_barufix.png";
    var imagejalanpcs8fix                = "<?php echo base_url()?>assets/images/pcs_8_baru.png";
    var imagejalanpcs9fix                = "<?php echo base_url()?>assets/images/pcs_9_baru.png";
    var imagejalanpcs10fix               = "<?php echo base_url()?>assets/images/pcs_10_baru.png";
    var imagejalanpcstekukankananatasfix = "<?php echo base_url()?>assets/images/pcs_tekukan_kanan_atas.png";
    var imagejalanpcs15fix               = "<?php echo base_url()?>assets/images/pcs_15_baru.png";
    var imagejalanpcs16fix               = "<?php echo base_url()?>assets/images/pcs_16_baru.png";
    var imagejalanpcs17fix               = "<?php echo base_url()?>assets/images/pcs_17_baru.png";
    var imagejalanpcs18fix               = "<?php echo base_url()?>assets/images/pcs_18_baru.png";
    var imagejalanpcs19fix               = "<?php echo base_url()?>assets/images/pcs_19_baru.png";
    var imagejalanpcs20fix               = "<?php echo base_url()?>assets/images/pcs_20_baru.png";
    var imagejalanpcs21fix               = "<?php echo base_url()?>assets/images/pcs_21_baru.png";
    var imagejalanpcs22fix               = "<?php echo base_url()?>assets/images/pcs_22_baru.png";
    var imagejalanpcs23fix               = "<?php echo base_url()?>assets/images/pcs_23_baru.png";
    var imagejalanpcs24fix               = "<?php echo base_url()?>assets/images/pcs_24_baru.png";
    var imagejalanpcs25fix               = "<?php echo base_url()?>assets/images/pcs_25_baru.png";
    var imagejalanpcs26fix               = "<?php echo base_url()?>assets/images/pcs_26_baru.png";
    var imagejalanpcs27fix               = "<?php echo base_url()?>assets/images/pcs_27_baru2.png";
    var imagejalanpcs28fix               = "<?php echo base_url()?>assets/images/pcs_28_baru.png";
    var imagejalanpcs29fix               = "<?php echo base_url()?>assets/images/pcs_29_baru.png";
    var imagejalanpcs30fix               = "<?php echo base_url()?>assets/images/pcs_30_baru.png";
    var imagejalanpcs32fix               = "<?php echo base_url()?>assets/images/pcs_32_baru.png";
    var imagejalanpcs33fix               = "<?php echo base_url()?>assets/images/pcs_33_baru.png";
    var imagejalanpcs34fix               = "<?php echo base_url()?>assets/images/pcs_34_baru.png";
    var imagejalanpcs36fix               = "<?php echo base_url()?>assets/images/pcs_36_baru.png";

    var imageport                  = new google.maps.LatLngBounds(latlngoverlay1, latlngoverlay2);
    var imagejalanpcs1             = new google.maps.LatLngBounds(jalanpcs1latlng1, jalanpcs1latlng2);
    var imagejalanpcs2               = new google.maps.LatLngBounds(jalanpcs2latlng1, jalanpcs2latlng2);
    var imagejalanpcs3               = new google.maps.LatLngBounds(jalanpcs3latlng1, jalanpcs3latlng2);
    var imagejalanpcs4               = new google.maps.LatLngBounds(jalanpcs4latlng1, jalanpcs4latlng2);
    var imagejalanpcs5               = new google.maps.LatLngBounds(jalanpcs5latlng1, jalanpcs5latlng2);
    var imagejalanpcs5nikung         = new google.maps.LatLngBounds(jalanpcs5nikunglatlng1, jalanpcs5nikunglatlng2);
    var imagejalanpcs5nikungpcs3baru = new google.maps.LatLngBounds(jalanpcs5nikungpcs3barulatlng1, jalanpcs5nikungpcs3barulatlng2);
    var imagejalanpcs5nikungpcs4baru = new google.maps.LatLngBounds(jalanpcs5nikungpcs4barulatlng1, jalanpcs5nikungpcs4barulatlng2);
    var imagejalanpcs5nikungpcs5baru = new google.maps.LatLngBounds(jalanpcs5nikungpcs5barulatlng1, jalanpcs5nikungpcs5barulatlng2);

    var imagejalanpcspatahancrop    = new google.maps.LatLngBounds(jalanpcspatahancroplatlng1, jalanpcspatahancroplatlng2);


    var imagejalanpcs6                = new google.maps.LatLngBounds(jalanpcs6latlng1, jalanpcs6latlng2);
    var imagejalanpcs7                = new google.maps.LatLngBounds(jalanpcs7latlng1, jalanpcs7latlng2);
    var imagejalanpcs8                = new google.maps.LatLngBounds(jalanpcs8latlng1, jalanpcs8latlng2);
    var imagejalanpcs9                = new google.maps.LatLngBounds(jalanpcs9latlng1, jalanpcs9latlng2);
    var imagejalanpcs10               = new google.maps.LatLngBounds(jalanpcs10latlng1, jalanpcs10latlng2);
    var imagejalanpcstekukankananatas = new google.maps.LatLngBounds(jalanpcstekukankananataslatlng1, jalanpcstekukankananataslatlng2);
    var imagejalanpcs15               = new google.maps.LatLngBounds(jalanpcs15latlng1, jalanpcs15latlng2);
    var imagejalanpcs16               = new google.maps.LatLngBounds(jalanpcs16latlng1, jalanpcs16latlng2);
    var imagejalanpcs17               = new google.maps.LatLngBounds(jalanpcs17latlng1, jalanpcs17latlng2);
    var imagejalanpcs18               = new google.maps.LatLngBounds(jalanpcs18latlng1, jalanpcs18latlng2);
    var imagejalanpcs19               = new google.maps.LatLngBounds(jalanpcs19latlng1, jalanpcs19latlng2);
    var imagejalanpcs20               = new google.maps.LatLngBounds(jalanpcs20latlng1, jalanpcs20latlng2);
    var imagejalanpcs21               = new google.maps.LatLngBounds(jalanpcs21latlng1, jalanpcs21latlng2);
    var imagejalanpcs22               = new google.maps.LatLngBounds(jalanpcs22latlng1, jalanpcs22latlng2);
    var imagejalanpcs23               = new google.maps.LatLngBounds(jalanpcs23latlng1, jalanpcs23latlng2);
    var imagejalanpcs24               = new google.maps.LatLngBounds(jalanpcs24latlng1, jalanpcs24latlng2);
    var imagejalanpcs25               = new google.maps.LatLngBounds(jalanpcs25latlng1, jalanpcs25latlng2);
    var imagejalanpcs26               = new google.maps.LatLngBounds(jalanpcs26latlng1, jalanpcs26latlng2);
    var imagejalanpcs27               = new google.maps.LatLngBounds(jalanpcs27latlng1, jalanpcs27latlng2);
    var imagejalanpcs28               = new google.maps.LatLngBounds(jalanpcs28latlng1, jalanpcs28latlng2);
    var imagejalanpcs29               = new google.maps.LatLngBounds(jalanpcs29latlng1, jalanpcs29latlng2);
    var imagejalanpcs30               = new google.maps.LatLngBounds(jalanpcs30latlng1, jalanpcs30latlng2);
    var imagejalanpcs32               = new google.maps.LatLngBounds(jalanpcs32latlng1, jalanpcs32latlng2);
    var imagejalanpcs33               = new google.maps.LatLngBounds(jalanpcs33latlng1, jalanpcs33latlng2);
    var imagejalanpcs34               = new google.maps.LatLngBounds(jalanpcs34latlng1, jalanpcs34latlng2);
    var imagejalanpcs36               = new google.maps.LatLngBounds(jalanpcs36latlng1, jalanpcs36latlng2);

    Overlay                       = new google.maps.GroundOverlay(imageportfix, imageport);
    Overlayjalanpcs_1             = new google.maps.GroundOverlay(imagejalanpcs1fix, imagejalanpcs1);
    Overlayjalanpcs_2               = new google.maps.GroundOverlay(imagejalanpcs2fix, imagejalanpcs2);
    Overlayjalanpcs_3               = new google.maps.GroundOverlay(imagejalanpcs3fix, imagejalanpcs3);
    Overlayjalanpcs_4               = new google.maps.GroundOverlay(imagejalanpcs4fix, imagejalanpcs4);
    Overlayjalanpcs_5               = new google.maps.GroundOverlay(imagejalanpcs5fix, imagejalanpcs5);
    Overlayjalanpcs_5nikung         = new google.maps.GroundOverlay(imagejalanpcs5nikungfix, imagejalanpcs5nikung);
    Overlayjalanpcs_5nikungpcs3baru = new google.maps.GroundOverlay(imagejalanpcs5nikungpcs3barufix, imagejalanpcs5nikungpcs3baru);
    Overlayjalanpcs_5nikungpcs4baru = new google.maps.GroundOverlay(imagejalanpcs5nikungpcs4barufix, imagejalanpcs5nikungpcs4baru);
    Overlayjalanpcs_5nikungpcs5baru = new google.maps.GroundOverlay(imagejalanpcs5nikungpcs5barufix, imagejalanpcs5nikungpcs5baru);

    Overlayjalanpcs_patahancrop = new google.maps.GroundOverlay(imagejalanpcspatahancrop5barufix, imagejalanpcspatahancrop);


    Overlayjalanpcs_6                = new google.maps.GroundOverlay(imagejalanpcs6fix, imagejalanpcs6);
    Overlayjalanpcs_7                = new google.maps.GroundOverlay(imagejalanpcs7fix, imagejalanpcs7);
    Overlayjalanpcs_8                = new google.maps.GroundOverlay(imagejalanpcs8fix, imagejalanpcs8);
    Overlayjalanpcs_9                = new google.maps.GroundOverlay(imagejalanpcs9fix, imagejalanpcs9);
    Overlayjalanpcs_10               = new google.maps.GroundOverlay(imagejalanpcs10fix, imagejalanpcs10);
    // ini nanti Overlayjalanpcs_11               = new google.maps.GroundOverlay(imagejalanpcs11fix, imagejalanpcs11);
    // ini nanti Overlayjalanpcs_12               = new google.maps.GroundOverlay(imagejalanpcs12fix, imagejalanpcs12);
    Overlayjalanpcs_tekukankananatas = new google.maps.GroundOverlay(imagejalanpcstekukankananatasfix, imagejalanpcstekukankananatas);
    Overlayjalanpcs_15               = new google.maps.GroundOverlay(imagejalanpcs15fix, imagejalanpcs15);
    Overlayjalanpcs_16               = new google.maps.GroundOverlay(imagejalanpcs16fix, imagejalanpcs16);
    Overlayjalanpcs_17               = new google.maps.GroundOverlay(imagejalanpcs17fix, imagejalanpcs17);
    Overlayjalanpcs_18               = new google.maps.GroundOverlay(imagejalanpcs18fix, imagejalanpcs18);
    Overlayjalanpcs_19               = new google.maps.GroundOverlay(imagejalanpcs19fix, imagejalanpcs19);
    Overlayjalanpcs_20               = new google.maps.GroundOverlay(imagejalanpcs20fix, imagejalanpcs20);
    Overlayjalanpcs_21               = new google.maps.GroundOverlay(imagejalanpcs21fix, imagejalanpcs21);
    Overlayjalanpcs_22               = new google.maps.GroundOverlay(imagejalanpcs22fix, imagejalanpcs22);
    Overlayjalanpcs_23               = new google.maps.GroundOverlay(imagejalanpcs23fix, imagejalanpcs23);
    Overlayjalanpcs_24               = new google.maps.GroundOverlay(imagejalanpcs24fix, imagejalanpcs24);
    Overlayjalanpcs_25               = new google.maps.GroundOverlay(imagejalanpcs25fix, imagejalanpcs25);
    Overlayjalanpcs_26               = new google.maps.GroundOverlay(imagejalanpcs26fix, imagejalanpcs26);
    Overlayjalanpcs_27               = new google.maps.GroundOverlay(imagejalanpcs27fix, imagejalanpcs27);
    Overlayjalanpcs_28               = new google.maps.GroundOverlay(imagejalanpcs28fix, imagejalanpcs28);
    Overlayjalanpcs_29               = new google.maps.GroundOverlay(imagejalanpcs29fix, imagejalanpcs29);
    Overlayjalanpcs_30               = new google.maps.GroundOverlay(imagejalanpcs30fix, imagejalanpcs30);
    Overlayjalanpcs_32               = new google.maps.GroundOverlay(imagejalanpcs32fix, imagejalanpcs32);
    Overlayjalanpcs_33               = new google.maps.GroundOverlay(imagejalanpcs33fix, imagejalanpcs33);
    Overlayjalanpcs_34               = new google.maps.GroundOverlay(imagejalanpcs34fix, imagejalanpcs34);
    Overlayjalanpcs_36               = new google.maps.GroundOverlay(imagejalanpcs36fix, imagejalanpcs36);

    Overlay.setMap(map);
    Overlayjalanpcs_1.setMap(map);
    Overlayjalanpcs_2.setMap(map);
    Overlayjalanpcs_3.setMap(map);
    Overlayjalanpcs_4.setMap(map);
    Overlayjalanpcs_5.setMap(map);
    Overlayjalanpcs_5nikung.setMap(map);
    Overlayjalanpcs_5nikungpcs3baru.setMap(map);
    Overlayjalanpcs_5nikungpcs4baru.setMap(map);
    Overlayjalanpcs_5nikungpcs5baru.setMap(map);
    Overlayjalanpcs_patahancrop.setMap(map);
    Overlayjalanpcs_6.setMap(map);
    Overlayjalanpcs_7.setMap(map);
    Overlayjalanpcs_8.setMap(map);
    Overlayjalanpcs_9.setMap(map);
    Overlayjalanpcs_10.setMap(map);
    Overlayjalanpcs_tekukankananatas.setMap(map);
    Overlayjalanpcs_15.setMap(map);
    Overlayjalanpcs_16.setMap(map);
    Overlayjalanpcs_17.setMap(map);
    Overlayjalanpcs_18.setMap(map);
    Overlayjalanpcs_19.setMap(map);
    Overlayjalanpcs_20.setMap(map);
    Overlayjalanpcs_21.setMap(map);
    Overlayjalanpcs_22.setMap(map);
    Overlayjalanpcs_23.setMap(map);
    Overlayjalanpcs_24.setMap(map);
    Overlayjalanpcs_25.setMap(map);
    Overlayjalanpcs_26.setMap(map);
    Overlayjalanpcs_27.setMap(map);
    Overlayjalanpcs_28.setMap(map);
    Overlayjalanpcs_29.setMap(map);
    Overlayjalanpcs_30.setMap(map);
    Overlayjalanpcs_32.setMap(map);
    Overlayjalanpcs_33.setMap(map);
    Overlayjalanpcs_34.setMap(map);
    Overlayjalanpcs_36.setMap(map);

    // ARRAY overlay
    overlaysarray.push(Overlay);
    overlaysarray.push(Overlayjalanpcs_1);
    overlaysarray.push(Overlayjalanpcs_2);
    overlaysarray.push(Overlayjalanpcs_3);
    overlaysarray.push(Overlayjalanpcs_4);
    overlaysarray.push(Overlayjalanpcs_5);
    overlaysarray.push(Overlayjalanpcs_5nikung);
    overlaysarray.push(Overlayjalanpcs_5nikungpcs3baru);
    overlaysarray.push(Overlayjalanpcs_5nikungpcs4baru);
    overlaysarray.push(Overlayjalanpcs_5nikungpcs5baru);
    overlaysarray.push(Overlayjalanpcs_patahancrop);


    overlaysarray.push(Overlayjalanpcs_6);
    overlaysarray.push(Overlayjalanpcs_7);
    overlaysarray.push(Overlayjalanpcs_8);
    overlaysarray.push(Overlayjalanpcs_9);
    overlaysarray.push(Overlayjalanpcs_10);
    overlaysarray.push(Overlayjalanpcs_tekukankananatas);
    overlaysarray.push(Overlayjalanpcs_15);
    overlaysarray.push(Overlayjalanpcs_16);
    overlaysarray.push(Overlayjalanpcs_17);
    overlaysarray.push(Overlayjalanpcs_18);
    overlaysarray.push(Overlayjalanpcs_19);
    overlaysarray.push(Overlayjalanpcs_20);
    overlaysarray.push(Overlayjalanpcs_21);
    overlaysarray.push(Overlayjalanpcs_22);
    overlaysarray.push(Overlayjalanpcs_23);
    overlaysarray.push(Overlayjalanpcs_24);
    overlaysarray.push(Overlayjalanpcs_25);
    overlaysarray.push(Overlayjalanpcs_26);
    overlaysarray.push(Overlayjalanpcs_27);
    overlaysarray.push(Overlayjalanpcs_28);
    overlaysarray.push(Overlayjalanpcs_29);
    overlaysarray.push(Overlayjalanpcs_30);
    overlaysarray.push(Overlayjalanpcs_32);
    overlaysarray.push(Overlayjalanpcs_33);
    overlaysarray.push(Overlayjalanpcs_34);
    overlaysarray.push(Overlayjalanpcs_36);
  }else {
    console.log(map.getMapTypeId());
    map.setMapTypeId((map.getMapTypeId() === 'hidden') ? google.maps.MapTypeId.ROADMAP : 'hidden');

    clearOverlays();
    overlaystatus = 0;
  }
}

function clearOverlays() {
 while(overlaysarray.length) {
   overlaysarray.pop().setMap(null);
 }
  overlaysarray.length = 0;
}
// OVERLAY BIB MAP END
</script>

<?php
$key = $this->config->item("GOOGLE_MAP_API_KEY");
//$key = "AIzaSyAYe-6_UE3rUgSHelcU1piLI7DIBnZMid4";

if(isset($key) && $key != "") { ?>
  <script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo $key;?>&callback=initialize" type="text/javascript"></script>
  <?php } else { ?>
    <script src="http://maps.google.com/maps/api/js?sensor=false"></script>
    <?php } ?>
