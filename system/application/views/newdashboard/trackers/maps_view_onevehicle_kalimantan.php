<style media="screen">
div#modalstreaming {
  margin-top: 2%;
  margin-left: 18%;
  /* overflow-x: auto; */
  position: absolute;
  z-index: 9;
  background-color: #f1f1f1;
  text-align: left;
  border: 1px solid #d3d3d3;
  width: 56%;
  height: 1px;
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

#map_canvas{
  width: 103%;
  height: 86%;
  margin-top: -0%;
  margin-left: -1.5%;
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
  margin-left: 5%;
  z-index:9999;
}

div#modalalertsummry {
  margin-top: 2%;
  margin-left: 18%;
  overflow-x: auto;
  position: absolute;
  z-index: 9;
  background-color: #f1f1f1;
  text-align: left;
  border: 1px solid #d3d3d3;
  width: 65%;
  max-height: 350px;
}
</style>

<!-- start sidebar menu -->
<div class="sidebar-container">
  <?=$sidebar;?>
</div>

<div class="page-content-wrapper">
  <div class="page-content">
    <?php if ($this->sess->user_id == 4201 || $this->sess->user_id == 4203) {?>
      <input type="text" id="vehicledeviceforgetalert" value="<?php echo $vehicledatafix[0]['vehicle_mv03'] ?>" hidden>
      <div class="checkbox checkbox-icon-red" style="margin-left: 63%; z-index: 1; margin-top: 1.3%; position: absolute;">
        <label for="checkboxrealtimealert">
            Realtime Alert
        </label>
        <input id="checkboxrealtimealert" type="checkbox" onclick="realtimealertchange();" value="1">
      </div>

      <?php
      if ($urlfix != "") {?>
        <button type="button" class="btn btn-primary" style="margin-left: 73%; z-index: 1; margin-top: 1.3%; position: absolute;" title="Live Streaming" onclick="showmodalstream('<?php echo $vehicledatafix[0]['vehicle_mv03']; ?>')">
          <span class="fa fa-video-camera"></span>
        </button>
      <?php } ?>



      <div id="realtimealertshow">
        <div class="row" id="modalalertrealtime">
          <div class="col-md-12">
            <div class="card card-topline-red">
              <div class="card-head">
                <div class="card-title">
                  <a href="#" onclick="realtimealerttoggle();">
                    <b>Realtime Alert</b>
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
                <div class="tools">
                  <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                  <button type="button" class="btn btn-danger" name="button" onclick="closemodalalertrealtime();">X</button>
                </div>
              </div>
              <div class="card-body">
                <div id="realtimealertcontent"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php } ?>
   <div id="map_canvas"></div>
  </div>
</div>
      <!-- end page content -->

      <div id="modallistvehicle" style="display: none;">
        <div id="mydivheader"></div>
        <div class="row" >
          <div class="col-md-12">
              <div class="card card-topline-yellow">
                  <div class="card-head">
                      <header>Information</header>
                      <div class="tools">
                          <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
                        <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
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
                                <th>Description</th>
                                <th>Customer</th>
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

      <div class="card" id="modalstreaming" style="display: none;">
        <div class="card card-topline-yellow">
          <div class="card-head">
            <h4>
              <b>Live Streaming</b>
            </h4>
            <div class="tools" style="margin-top: -6%;">
              <!-- <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a> -->
              <button type="button" class="btn btn-danger" name="button" onclick="closemodallivestreaming();">X</button>
            </div>
          </div>
          <div class="card-body">
            <?php
            if ($urlfix != "") {?>
              <iframe src="<?php echo $urlfix; ?>" width="420" height="450" frameborder="0" style="border:0;"></iframe>
            <?php } ?>
          </div>
        </div>
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

      <script type="text/javascript">
        var markernya = [];
        var markernyaa = [];
        var marker = [];
        var markers = [];
        var markerss = [];
        var positions = [];
        var positionsbaru = [];
        var coordsArray = [];
        var JSONString, obj, infoWindow;
        var map, center;
        var timer = 0;
        var limit;
        var nextTimer = 1;
        var movingmarker;
        var brng;
        var course = [];
        var alldata = [];
        var reversecoords;
        var reversecourse;
        var reversealldata;
        var reversecoords2;
        var reversecourse2;
        var reversealldata2;
        var getsimultant = 0;
        var jamlanjutan;
        var bounds, boundspool, boundsdest;
        var markerpool  = [];
        var markerpools = [];
        var markerdest  = [];
        var markerdests = [];
        var laststatus = "-";
        var laststatus2;
        var infowindow, infowindow2, infowindowkedua = null;
        var isshowhideinfodetail = 0;
        var camdevices = ["TK510CAMDOOR", "TK510CAM", "GT08", "GT08DOOR", "GT08CAM", "GT08CAMDOOR"];

        var car = "M17.402,0H5.643C2.526,0,0,3.467,0,6.584v34.804c0,3.116,2.526,5.644,5.643,5.644h11.759c3.116,0,5.644-2.527,5.644-5.644 V6.584C23.044,3.467,20.518,0,17.402,0z M22.057,14.188v11.665l-2.729,0.351v-4.806L22.057,14.188z M20.625,10.773 c-1.016,3.9-2.219,8.51-2.219,8.51H4.638l-2.222-8.51C2.417,10.773,11.3,7.755,20.625,10.773z M3.748,21.713v4.492l-2.73-0.349 V14.502L3.748,21.713z M1.018,37.938V27.579l2.73,0.343v8.196L1.018,37.938z M2.575,40.882l2.218-3.336h13.771l2.219,3.336H2.575z M19.328,35.805v-7.872l2.729-0.355v10.048L19.328,35.805z";


        function initialize() {
          JSONString = '<?php echo json_encode($vehicledatafix[0]['vehicle_device']); ?>';
          obj = JSON.parse(JSONString);

          // var JSONString2 = '<?php echo json_encode($vehicledatafix); ?>';
          // var obj2 = JSON.parse(JSONString2);
          //
          // console.log("obj2 : ", obj2);

           bounds = new google.maps.LatLngBounds();
           boundspool = new google.maps.LatLngBounds();
           boundsdest = new google.maps.LatLngBounds();

           map = new google.maps.Map(
            document.getElementById("map_canvas"), {
              center: new google.maps.LatLng(-6.2293867, 106.6894289),
              zoom: 10,
              mapTypeId: google.maps.MapTypeId.SATELLITE,
              options: {
                 gestureHandling: 'greedy'
                 // clickableIcons: false
               }
            });

            var iconpool = {
              url: "http://transporter.lacak-mobil.com/assets/images/markergif.gif", // url JIKA GIF
              // path: 'assets/images/iconpulsemarker.gif',
              // scale: .5,
              anchor: new google.maps.Point(25,10),
              scaledSize: new google.maps.Size(15,15)
            };

            var icondest = {
              url: "http://transporter.lacak-mobil.com/assets/images/markergif.gif", // url JIKA GIF
              // path: 'assets/images/iconpulsemarker.gif',
              // scale: .5,
              anchor: new google.maps.Point(25,10),
              scaledSize: new google.maps.Size(15,15)
            };

            var JSONSTRINGnya = '<?php echo json_encode($poolmaster) ?>';
            var objpoolmasterfix = JSON.parse(JSONSTRINGnya);

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

            var JSONSTRINGnyadest = '<?php echo json_encode($dest) ?>';
            var objdest = JSON.parse(JSONSTRINGnyadest);
            for (var x = 0; x < objdest.length; x++) {

              var positiondest = new google.maps.LatLng(objdest[x].dest_lat, objdest[x].dest_lng);
              boundsdest.extend(positiondest);

              markerdest = new google.maps.Marker({
                position: positiondest,
                map: map,
                icon: iconpool,
                title: objdest[x].dest_name,
                id: objdest[x].dest_name,
                optimized: false
              });
              markerdest.setIcon(icondest);
              markerdests.push(markerdest);
            }

            jQuery.post("<?=base_url();?>map/lastinfo", {device: obj, lasttime: '<?=$this->config->item('timer_list');?>'},
              function(r)
              {
                // console.log("response : ", r.vehicle);
                add_new_markersnya(r.vehicle);
              }, "json");

          var intervalkedua = setInterval(getdata, 5000);
        }

        function getdata() {
          jQuery.post("<?=base_url();?>map/lastinfo", {device: obj, lasttime: '<?=$this->config->item('timer_list');?>'},
            function(r)
            {
              // console.log("response : ", r.vehicle);
              coordsArray.push({lat : parseFloat(r.vehicle.gps.gps_latitude_real_fmt), lng : parseFloat(r.vehicle.gps.gps_longitude_real_fmt)});
              course.push(r.vehicle.gps.gps_course);
              // console.log("coordsArray : ", coordsArray);
              // console.log("course : ", course);
              add_new_markersnya(r.vehicle);
            }, "json");
        }

        function add_new_markersnya(value) {
          for (var i = 0; i < markerss.length; i++ ) {
            markerss[i].setMap(null);
          }
          markerss.length = 0;
          //console.log("ini di add new marker : ", locations);
            console.log("ini di add new marker single track value: ", value);
            // console.log("course : ", value.gps.gps_course);
            DeleteMarkers(value.vehicle_device_name+"@"+value.vehicle_device_host);
            DeleteMarkerspertama(value.vehicle_device_name+"@"+value.vehicle_device_host);

            var statusengine = "";
            if (value.status1 == true) {
              statusengine = "ON";
            }else {
              statusengine = "OFF";
            }

            // DEVICE STATUS (CAMERA ONLINE / OFFLINE)
            if (value.devicestatus) {
              if (value.devicestatus == 1) {
                var devicestatus = "Camera : Online <br>" ;
              }else {
                var devicestatus = "Camera : Offline <br>" ;
              }
            }else {
              if (value.devicestatus == 0) {
                var devicestatus = "Camera : Offline </br>";
              }else {
                var devicestatus = "";
              }
            }

            var num         = Number(value.gps.gps_speed_fmt);
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
                    fillColor: '#0000FF',
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
                    fillColor: '#ffa500',
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
                    fillColor: '#0000FF',
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
              position: new google.maps.LatLng(parseFloat(value.gps.gps_latitude_real_fmt), parseFloat(value.gps.gps_longitude_real_fmt)),
              id: value.vehicle_device_name+"@"+value.vehicle_device_host
            });
            icon.rotation = Math.ceil(value.gps.gps_course);
            markernya.setIcon(icon);
            markerss.push(markernya);
            // map.setCenter(markernya.getPosition());

            var infowindow = new google.maps.InfoWindow({
              content: obj[i].auto_vehicle_no,
              maxWidth: 160
            });

            var gps_status = "";
            if (value.gps.gps_status == "A" || value.gps.gps_status == "V") {
              gps_status = "Good";
            }else {
              gps_status = "Not Good";
            }

            // START FAN
            var doorfanstt = "";
            var doorfix    = "";
        		if (value.fan)
        		{
        			if (value.vehicle_user_id == "1554" || value.vehicle_user_id == "1032" || value.vehicle_user_id == "1801" || value.vehicle_user_id == "1594"
        			|| value.vehicle_type == "T5DOOR")
        			{
        				doorfanstt = "Door : ";
        				if (value.fan == "0")
        				{
        					doorfix = "CLOSE";
        				}
        				else
        				{
        					doorfix = "OPEN";
        				}
        			}

        			if (value.vehicle_user_id == "1225" || value.vehicle_type == "T5FAN")
        			{
        				doorfanstt = "Fan : ";
        				if (value.fan == "0")
        				{
                  doorfix = "OFF";
        				}
        				else
        				{
                  doorfix = "ON";
        				}
        			}

        			if (value.vehicle_user_id == "1077" || value.vehicle_type == "T5PTO")
        			{
                doorfanstt = "PTO : ";
        				if (value.fan == "0")
        				{
        					doorfix = "OFF";
        				}
        				else
        				{
                  doorfix = "ON";
        				}
        			}

        		}
        		//end get fan
        		if (value.vehicle_type == "TK315DOOR")
        		{
              doorfanstt = "Door : ";
        			if (value.fan == "1")
        			{
                doorfix = "OPEN";
        			}
        			else
        			{
                doorfix = "CLOSE";
        			}
        		}

        		if (value.vehicle_type == "X3_DOOR" || value.vehicle_type == "TK315DOOR_NEW" || value.vehicle_type == "TK510DOOR" || value.vehicle_type == "TK510CAMDOOR" || value.vehicle_type == "GT08SDOOR")
        		{
              doorfanstt = "Door : ";
        			if (value.fan == "53")
        			{
                doorfix = "OPEN";
        			}
        			else
        			{
                doorfix = "CLOSE";
        			}
        		}

        		if (value.vehicle_type == "X3_PTO")
        		{
              doorfanstt = "PTO : ";
        			if (value.fan == "53")
        			{
                doorfix = "ON";
        			}
        			else
        			{
                doorfix = "OFF";
        			}
        		}

        		if (value.vehicle_type == "TK315FAN")
        		{
              doorfanstt = "Fan : ";
        			if (value.fan == "53")
        			{
                doorfix = "ON";
        			}
        			else
        			{
                doorfix = "OFF";
        			}
        		}
        		//END FAN
            // console.log("doorfanstt : ", doorfanstt);
            // console.log("doorfix : ", doorfanstt + doorfix);
            var fixfan = "";
            if (doorfanstt+doorfix != "") {
              fixfan = doorfanstt + doorfix +"</br>";
            }else {
              fixfan = "";
            }

            var camdevicesfix = camdevices.includes(value.vehicle_type);
            if (camdevicesfix) {
              var lct                = "Last Captured : " + value.snaptime + "<br>";
              var imglct             = "<img src='"+value.snapimage+"'> <br>";
              var showbuttonhideinfo = "<header class='panel-heading panel-heading-blue' onclick='showhidedetailinfo();' style='text-align: center;'><span id='titlehideshowinfo'>Hide Detail Info</span></header><br>";
            }else {
              var lct                = "";
              var imglct             = "<br>";
              var showbuttonhideinfo = "";
            }

            if (isshowhideinfodetail == 1) {
              var string =  "<div class='panel' id='panel_form'>" +
                              "<header class='panel-heading panel-heading-blue' onclick='showhidedetailinfo();' style='text-align: center;'><span id='titlehideshowinfo'>Show Detail Info</span></header><br>"+
                                "<div id='divshowhideinfo' style='display:none;'>"+
                                 value.vehicle_no + ' - ' + value.vehicle_name + "<br>" +
                                 "GPS Time : " + value.gps.gps_date_fmt + " " + value.gps.gps_time_fmt + "<br>" +
                                 "Position : " + value.gps.georeverse.display_name + "<br>" +
                                 "Coordinate : " + value.gps.gps_latitude_real_fmt + ", " + value.gps.gps_longitude_real_fmt + "<br>" +
                                  "Speed : " + rounded +" kph" + "<br>" +
                                  "Engine : " + statusengine + "<br>" +
                                  "Odometer : " + value.totalodometer + "<br>" +
                                  devicestatus +
                                  "Ritase : " + ritasefix + "<br>" +
                                  "GPS Status : " + gps_status + "<br>" +
                                  fixfan +
                                  "No. Card : " + value.vehicle_card_no + "</br>" +
                                  "</div>" +
                            "</div>" +
                              lct + imglct;
            }else {
              var string = "<div class='panel' id='panel_form'>" +
                              showbuttonhideinfo +
                                "<div id='divshowhideinfo'>"+
                                 value.vehicle_no + ' - ' + value.vehicle_name + "<br>" +
                                 "GPS Time : " + value.gps.gps_date_fmt + " " + value.gps.gps_time_fmt + "<br>" +
                                 "Position : " + value.gps.georeverse.display_name + "<br>" +
                                 "Coordinate : " + value.gps.gps_latitude_real_fmt + ", " + value.gps.gps_longitude_real_fmt + "<br>" +
                                 "Speed : " + rounded +" kph" + "<br>" +
                                 "Engine : " + statusengine + "<br>" +
                                 "Odometer : " + value.totalodometer + "<br>" +
                                 devicestatus +
                                 "Ritase : " + ritasefix + "<br>" +
                                  "GPS Status : " + gps_status + "<br>" +
                                  fixfan +
                                  "No. Card : " + value.vehicle_card_no + "</br>" +
                                  "</div>" +
                            "</div>" +
                              lct + imglct;
            }



          var htmlautoupdaterow;
          htmlautoupdaterow += '<tr id="rowid_'+value.vehicle_device+'">'+
                                  '<td><a name="'+(i+1)+'"></a><div id="pointer_'+value.vehicle_device+'" style="display: none; "text-align: right;">&#9654;</div></td>'+
                                  '<td style="font-size:12px; vertical-align:top">1</td>'+
                                  '<td style="font-size:12px; vertical-align:top" id="vehicle_id_'+value.vehicle_device+'"><a style="color:green;">'+value.vehicle_no + " - " + value.vehicle_name+'</a></td>'+
                                  '<td style="font-size:12px; vertical-align:top" id="driver_'+value.vehicle_device+'"></td>'+
                                  '<td style="font-size:12px; vertical-align:top" id="position_'+value.vehicle_device+'"><span style="color:blue;">Position : '+value.auto_last_position + "</span> <br> GPS Time : " + value.auto_last_update + "<br>" + "Coord : " + "<a href='http://maps.google.com/maps?z=12&t=m&q=loc:"+value.auto_last_lat+','+value.auto_last_long+"' target='_blank'>" + value.auto_last_lat + ", " + value.auto_last_long + "</a>"+ "<br>" + "Engine : " + value.auto_last_engine + "<br>" + "Speed : " + rounded + ' Kph</td>'+
                                  '<td style="font-size:12px; vertical-align:top" id="description_'+value.vehicle_device+'"></td>'+
                                  '<td style="font-size:12px; vertical-align:top" id="customer_'+value.vehicle_device+'"></td>'+
                                  '<td style="font-size:12px; vertical-align:top" id="cardno_'+value.vehicle_device+'">'+value.vehicle_card_no+'</td>'+
                                  '<td style="font-size:12px; vertical-align:top" id="laststatus_'+value.vehicle_device+'">'+laststatus2+'</td>'+
                               '</tr>';
          $("#autoupdaterow").html(htmlautoupdaterow);

          var statusengine = "";
          if (value.status1 == true) {
            statusengine = "ON";
          }else {
            statusengine = "OFF";
          }

          var geofencestatus = "";
            if (value.geofence_location != "") {
              geofencestatus = "Geofence : " + value.geofence_location + "</br>";
            }

          // UNTUK ADD COMMENT
          var comment = "<span id='comment"+value.vehicle_id+"' style='display: none'></span>";
          // console.log("comment : ", comment);

          var vehicle_id_ = '<a style="color:green;">'+value.vehicle_no + " - " + value.vehicle_name+'</a>';
          var position_   = '<span style="color:black;">'+geofencestatus+'</span>' + '<span style="color:blue;">' + value.gps.georeverse.display_name + "</span> <br> GPS Time : " + value.gps.gps_date_fmt + " " + value.gps.gps_time_fmt + "<br>" + "Coord : " + "<a href='http://maps.google.com/maps?z=12&t=m&q=loc:"+value.gps.gps_latitude_real_fmt+','+value.gps.gps_longitude_real_fmt+"' target='_blank'>" + value.gps.gps_latitude_real_fmt + ", " + value.gps.gps_longitude_real_fmt + "</a> <br>" + "Engine : " + statusengine + "<br>" + "Speed : " + value.gps.gps_speed_fmt + " Kph<br>" + comment;

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
          $("[id='description_"+value.vehicle_device+"']").html(description_);
          $("[id='cardno_"+value.vehicle_device+"']").html(cardno_);

          // console.log("course : ", value.gps.gps_course);
          // console.log("Status : ", value.gps.gps_status);
          // console.log("Status : ", value.gps.css_delay_index);
          var css_delay_index = value.gps.css_delay_index;
          var warna = "";
          if (css_delay_index == 0) {
            warna = "#ff0000";
            changerowcolor(css_delay_index, value.vehicle_device);
            laststatus = 'GPS Offline';
            statusnyafix = '<h5 class="text-medium full-width"><span class="label label-sm label-danger">'+laststatus+'</span></h5>';
            // FOR ADD COMMENT
            // console.log(" value.comment : ", value.comment);
              if (value.comment) {
                  // $("#comment"+value.vehicle_id).html("commentfixC1");
                var sComment = value.comment.split('|');
                jQuery("[id='comment"+value.vehicle_id+"']").html("<a style='color: #0000ff' href=" + "javascript:vehicle_comment(" + value.vehicle_id + ")" + ">" + sComment[1] + "</a>");
              }else {
                // $("#comment"+value.vehicle_id).html("commentfixC2");
                jQuery("[id='comment"+value.vehicle_id+"']").html("<a style='color: #0000ff' href=" + "javascript:vehicle_comment(" + value.vehicle_id + ")" + ">" + "Add Comment" + "</a>");
              }

            // show comment jika list merah
            if((value.gps.css_delay[1] == "#ff0000")) {
              $("#comment"+value.vehicle_id).show();
            }

          } else if (css_delay_index == 1) {
            warna = "#ffff00";
            changerowcolor(css_delay_index, value.vehicle_device);
            laststatus = 'GPS Online (Delay)';
            statusnyafix = '<h5 class="text-medium full-width"><span class="label label-sm label-warning">'+laststatus+'</span></h5>';
          } else {
            if (value.status1 == true && value.gps.gps_speed_fmt > 0) {
              warna = "#00b300";
              changerowcolor(css_delay_index, value.vehicle_device);
              laststatus = 'GPS Online';
              statusnyafix = '<h5 class="text-medium full-width"><span class="label label-sm label-success">'+laststatus+'</span></h5>';
            }else {
              warna = "#0000FF";
              changerowcolor(css_delay_index, value.vehicle_device);
              laststatus = 'GPS Online';
              statusnyafix = '<h5 class="text-medium full-width"><span class="label label-sm label-primary">'+laststatus+'</span></h5>';
            }
          }
          $("[id='laststatus_"+value.vehicle_device+"']").html(statusnyafix);

          // console.log("warna : ", warna);

          var icon = {
            // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
            path: car,
            scale: .5,
            // anchor: new google.maps.Point(25,10),
            // scaledSize: new google.maps.Size(30,20),
            strokeColor: 'white',
            strokeWeight: .10,
            fillOpacity: 1,
            fillColor: warna,
            offset: '5%',
          };

            var infowindow = new google.maps.InfoWindow({
              content: string,
              maxWidth: 350
            });

            infowindow.open(map, markernya);

            drawRoute();

            // map.setmap(null);
            // markers.setmap(null);
            // marker.setPosition(marker);
            google.maps.event.addListener(markernya, 'click', function(e) {
              map.setZoom(16);
              // map.setCenter(e.latLng);
            });
            return markernya;
        }

        function DeleteMarkers(id) {
          //Loop through all the markers and remove
          for (var i = 0; i < markerss.length; i++) {
            if (markerss[i].id == id) {
              //Remove the marker from Map
              markerss[i].setMap(null);

              //Remove the marker from array.
              markerss.splice(i, 1);
              return;
            }
          }
        }

        function DeleteMarkerspertama(id) {
          //Loop through all the markers and remove
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


        function drawRoute(){
          // console.log("ini di drawroute");
          // console.log("ini di drawroute coordsArray : ", coordsArray);
          // console.log("ini di drawroute course : ", course);
          var flightPath = new google.maps.Polyline({
             path: coordsArray,
             strokeColor: "#FF0000",
             strokeWeight: 3
           });
            flightPath.setMap(map);
            setTimeout("movement(10000)", 3000);
        }

        function movement(d){
            limit = coordsArray.length - 1;
            // console.log("limit simultant : ", limit);
            // console.log("timer simultant : ", timer);
           if(timer < limit || nextTimer <= limit){
             var strictLat   = parseFloat(coordsArray[timer].lat);
             var strictLng   = parseFloat(coordsArray[timer].lng);
             var newPosition = new google.maps.LatLng(strictLat,strictLng);
             // console.log("coursenya : ", course[timer]);
             icon.rotation = Math.ceil(course[timer]);
             markernya.setIcon(icon);
             markernya.setPosition(newPosition);
             // console.log("newPosition : ", newPosition);
             // map.setCenter(newPosition);
             var timerHandle = setTimeout("movement(" + (d + 5) + ")", 3000);
             // console.log("timer simultant 2: ", timer);
         }
           nextTimer++;
           timer++;
        }

        //  FOR DRAG MODAL VEHICLE LIST
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
    // console.log("device id forsearchinput : ", deviceid);

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
      // console.log("ini respon pencarian : ", response);
      var center = {lat : parseFloat(response[0].auto_last_lat), lng: parseFloat(response[0].auto_last_long)};
      // console.log("center : ", center);

      var string = response[0].vehicle_no + ' - ' + response[0].vehicle_name + "<br>" +
        "GPS Time : " + response[0].auto_last_update + "<br>Position : " + response[0].auto_last_position + "<br>Coord : " + response[0].auto_last_lat + ", " + response[0].auto_last_long + "<br>" +
        "Engine : " + response[0].auto_last_engine + "<br>" +
        "Speed : " + response[0].auto_last_speed + " kph" + "<br>" +
        "<a href='<?php echo base_url()?>maps/tracking/" + response[0].vehicle_id + "' target='_blank'>Tracking</a>";

       infowindowkedua = new google.maps.InfoWindow({
        content: string,
        maxWidth: 350
      });
      DeleteMarkers(response[0].vehicle_device);
      DeleteMarkerspertama(response[0].vehicle_device);


      if (response[0].auto_last_engine == "ON" && response[0].auto_last_speed > 0) {
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
          fillColor: '#0000FF',
          offset: '5%'
        };
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
      // map.setCenter(center);
      markernya.setPosition(center);

      google.maps.event.addListener(markernya, 'click', function(){
        infowindow2.close();
        infowindowkedua.close();
        infowindow.close();
        var string = response[0].vehicle_no + ' - ' + response[0].vehicle_name + "<br>" +
          "GPS Time : " + response[0].auto_last_update + "<br>Position : " + response[0].auto_last_position + "<br>Coord : " + response[0].auto_last_lat + ", " + response[0].auto_last_long + "<br>" +
          "Engine : " + response[0].auto_last_engine + "<br>" +
          "Speed : " + response[0].auto_last_speed + " kph" + "<br>" +
          "<a href='<?php echo base_url()?>maps/tracking/" + response[0].vehicle_id + "' target='_blank'>Tracking</a>";

         infowindowkedua = new google.maps.InfoWindow({
          content: string,
          maxWidth: 350
        });
        // DeleteMarkers(response[0].vehicle_device);
        // DeleteMarkerspertama(response[0].vehicle_device);

          var center = {lat : parseFloat(response[0].auto_last_lat), lng: parseFloat(response[0].auto_last_long)};
          // console.log("center : ", center);

          infowindowkedua.setContent(string);
          // map.setCenter(center);
          markernya.setPosition(center);
          infowindowkedua.open(map, this);
      });

    }, "json");
  }

  function showinfo() {
    $("#modallistvehicle").show();
    $("#showtable").show();
  }

  function closemodallistofvehicle(){
    $("#modallistvehicle").hide();
  }

  function changerowcolor(value, device) {
    var element = document.getElementById("rowid_"+device);
    if (value == 0) {
      element.classList.add("red");
    }else if (value == 1) {
      element.classList.add("yellow");
    }else {
      element.classList.add("white");
    }
  }

  function showhidedetailinfo(){
    if (isshowhideinfodetail == 0) {
      isshowhideinfodetail = 1;
    }else {
      isshowhideinfodetail = 0;
    }

    var x = document.getElementById("divshowhideinfo");
    if (x.style.display === "none") {
      x.style.display = "block";
    } else {
      $("#titlehideshowinfo").html("Show Detail Info");
      x.style.display = "none";
    }
  }

  // UNTUK KALIMANTAN
  function showmodalstream(id){
    // var split     = id.split("@");
    // var deviceid  = split[0];
    // var urlstream = "http://47.91.108.9:8080/808gps/open/player/video.html?lang=en&devIdno="+deviceid+"&jsession=";
    // $.post("<?php echo base_url() ?>securityevidence/getsessionlogin", {
    //   device : deviceid,
    //   url : urlstream
    // }, function(response){
      // console.log("response : ", response);
      // $("#framestreaming").html(response.content);
      $("#modalstreaming").show();
    // }, "json");
  }

  function closemodallivestreaming(){
    $("#modalstreaming").hide();
  }

  // GET REALTIME ALERT
  var realtimealertarray        = [];
  var realtimealertarraysummary = [];
  var userid                    = '<?php echo $this->sess->user_id?>';
  var intervalalert2, intervalalert1, intervalalert;
  var soundisactive             = 1;

  function realtimealertchange(){
    var realtimeischeked = $("#checkboxrealtimealert").val();
    console.log("realtimeischeked : ", realtimeischeked);
      if (realtimeischeked == 1) {
        soundisactive = 1;
        clearInterval(intervalalert);
        $("#checkboxrealtimealert").val("0");
          if (userid == '4201' || userid == '4203') {
            intervalalert1 = setInterval(dataalert, 1000);
          }
      }else {
        clearInterval(intervalalert1);
        soundisactive = 0;
        $("#checkboxrealtimealert").val("1");
        $("#realtimealertshow").hide();
        if (userid == '4201' || userid == '4203') {
          intervalalert = setInterval(dataalert2, 1000);
        }
      }
  }

  function dataalert(){
    if (realtimealertarray.length >= 1) {
      realtimealertarray = [];
    }
    // console.log("data alertinarray : ", realtimealertarray);
    var vdevice    = $("#vehicledeviceforgetalert").val();
    // var vdevicefix = vdevice.split("@");
    // console.log("vdevicefix : ", vdevicefix);
    $.post("<?php echo base_url() ?>securityevidence/realtimealert", {device : vdevice}, function(response){
      if (response.sizedata != 0) {
        console.log("response : ", response);
        realtimealertarray.push(response.alertdata[0]);
        realtimealertarraysummary.push(response.alertdata[0]);
        var reversearray        = realtimealertarray.reverse();
        var reversearraysummary = realtimealertarraysummary.reverse();
        var html = "";

          for (var i = 0; i < reversearray.length; i++) {
            if (response.stTypeis == "yes") {
              var alertname = '. <span style="font-size: 14px; color:red;">'+ reversearray[i].stType +'</span> : <span style="font-size:11px;">'+ reversearray[i].srcTm +' </span> '+
                                          '<span style="font-size: 14px; color:red;">'+ reversearray[i].type +'</span> :  <span style="font-size:11px;">'+ reversearray[i].time +' </span>'+
                                          '<a style="font-size: 14px;" href="http://maps.google.com/maps?z=12&t=m&q=loc'+reversearray[i].Gps.mlat+','+reversearray[i].Gps.mlng+'">'+ reversearray[i].Gps.mlat +', '+ reversearray[i].Gps.mlng +' </a> </br>';
            }else {
              var alertname = '<span style="font-size: 14px; color:red;">'+ reversearray[i].type +'</span> : <span style="font-size:11px;">'+ reversearray[i].time +' </span>'+
                                        '<a style="font-size: 14px;" href="http://maps.google.com/maps?z=12&t=m&q=loc'+reversearray[i].Gps.mlat+','+reversearray[i].Gps.mlng+'">'+ reversearray[i].Gps.mlat +', '+ reversearray[i].Gps.mlng +' </a></br>';
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
            if (response.stTypeis == "yes") {
              htmlsummary += '<tr>';
                // htmlsummary += '<td style="font-size: 12px;">'+ (j+1) +'. </td>';
                htmlsummary += '<td style="font-size: 12px; color:green;">'+ response.vehiclenoandname +'</td>';
                htmlsummary += '<td><span style="font-size:12px; color: red;">'+ reversearraysummary[j].stType + '</span>' + ' ' + ' <span style="font-size: 12px;"> ' +reversearraysummary[j].srcTm+'</span></td>';
                htmlsummary += '<td><span style="font-size:12px; color: red;">'+ reversearraysummary[j].type   + '</span>' + ' ' + ' <span style="font-size: 12px;"> ' + reversearraysummary[j].time +'</span></td>';
                htmlsummary += '<td style="font-size:12px;"><a style="font-size:12px;" href="http://maps.google.com/maps?z=12&t=m&q=loc'+reversearraysummary[j].Gps.mlat+','+reversearraysummary[j].Gps.mlng+'">'+ reversearraysummary[j].Gps.mlat +', '+ reversearraysummary[j].Gps.mlng +' </a></td> </br>';
              htmlsummary += '</tr>';
            }else {
              htmlsummary += '<tr>';
                // htmlsummary += '<td style="font-size: 12px;">'+ (j+1) +'. </td>';
                htmlsummary += '<td style="font-size: 12px; color:green;">'+ response.vehiclenoandname +'</td>';
                htmlsummary += '<td><span style="font-size:12px; color: red;">'+ reversearraysummary[j].type   + '</span>' + ' ' + ' <span style="font-size: 12px;"> ' + reversearraysummary[j].time +'</span></td>';
                htmlsummary += '<td style="font-size:12px;"><a style="font-size:12px;" href="http://maps.google.com/maps?z=12&t=m&q=loc'+reversearraysummary[j].Gps.mlat+','+reversearraysummary[j].Gps.mlng+'">'+ reversearraysummary[j].Gps.mlat +', '+ reversearraysummary[j].Gps.mlng +' </a></td> </br>';
              htmlsummary += '</tr>';
            }
          }
          $("#summaryalertcontent").html(htmlsummary);
          // $("#modalalertsummry").show();
        // REALTIME SUMMARY ALERT END
      }
    }, "json");
  }

  function dataalert2(){
    if (realtimealertarray.length >= 1) {
      realtimealertarray = [];
    }
    // console.log("data alertinarray : ", realtimealertarray);
    // console.log("summary alertinarray : ", realtimealertarraysummary);
    var vdevice    = $("#vehicledeviceforgetalert").val();
    // var vdevicefix = vdevice.split("@");
    console.log("vdevicefix : ", vdevicefix);
    $.post("<?php echo base_url() ?>securityevidence/realtimealert", {device : vdevice}, function(response){
      if (response.sizedata != 0) {
        console.log("response : ", response);
        realtimealertarray.push(response.alertdata[0]);
        realtimealertarraysummary.push(response.alertdata[0]);
        var reversearray        = realtimealertarray.reverse();
        var reversearraysummary = realtimealertarraysummary.reverse();
        var html = "";
          for (var i = 0; i < reversearray.length; i++) {
            if (response.stTypeis == "yes") {
              var alertname = '. <span style="font-size: 14px; color:red;">'+ reversearray[i].stType +'</span> : <span style="font-size:11px;">'+ reversearray[i].srcTm +' </span> '+
                                          '<span style="font-size: 14px; color:red;">'+ reversearray[i].type +'</span> :  <span style="font-size:11px;">'+ reversearray[i].time +' </span>'+
                                          '<a href="http://maps.google.com/maps?z=12&t=m&q=loc'+reversearray[i].Gps.mlat+','+reversearray[i].Gps.mlng+'">'+ reversearray[i].Gps.mlat +', '+ reversearray[i].Gps.mlng +' </a> </br>';
            }else {
              var alertname = '<span style="font-size: 14px; color:red;">'+ reversearray[i].type +'</span> : <span style="font-size:11px;">'+ reversearray[i].time +' </span>'+
                                        '<a href="http://maps.google.com/maps?z=12&t=m&q=loc'+reversearray[i].Gps.mlat+','+reversearray[i].Gps.mlng+'">'+ reversearray[i].Gps.mlat +', '+ reversearray[i].Gps.mlng +' </a></br>';
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
        console.log("summary alertinarray : ", reversearraysummary);
          var htmlsummary = "";
                htmlsummary += '<section id="cd-timeline" class="cd-container">';
          for (var j = 0; j < reversearraysummary.length; j++) {
            if (response.stTypeis == "yes") {
              var summaryalert = '<span style="font-size: 14px; color:red;">'+reversearraysummary[j].stType+'</span> </br> <span style="font-size:11px;">'+ reversearraysummary[j].srcTm +' </span></br> '+
                                 '<span style="font-size: 14px; color:red;">'+ reversearraysummary[j].type +'</span> </br> <span style="font-size:11px;">'+ reversearraysummary[j].time +' </span></br>'+
                                 '<a href="http://maps.google.com/maps?z=12&t=m&q=loc'+reversearraysummary[j].Gps.mlat+','+reversearraysummary[j].Gps.mlng+'">'+ reversearraysummary[j].Gps.mlat +', '+ reversearraysummary[j].Gps.mlng +' </a> </br>';
            }else {
              var summaryalert = '<span style="font-size: 14px; color:red;">'+ reversearraysummary[j].type +'</span> </br> <span style="font-size:11px;">'+ reversearraysummary[j].time +' </span></br>'+
                                 '<a href="http://maps.google.com/maps?z=12&t=m&q=loc'+reversearraysummary[j].Gps.mlat+','+reversearraysummary[j].Gps.mlng+'">'+ reversearraysummary[j].Gps.mlat +', '+ reversearraysummary[j].Gps.mlng +' </a></br>';
            }

               htmlsummary += '<div class="cd-timeline-block">';
                 htmlsummary += '<div class="cd-timeline-img cd-picture">';
                  htmlsummary += '<img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/148866/cd-icon-location.svg" alt="Location">';
                 htmlsummary += '</div>';
                 htmlsummary += '<div class="cd-timeline-content">';
                   htmlsummary += summaryalert;
                   // htmlsummary += '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Iusto, optio, dolorum provident rerum aut hic quasi placeat iure tempora laudantium ipsa ad debitis unde? Iste voluptatibus minus veritatis qui ut.</p>';
                   // htmlsummary += '<button type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect m-b-10 btn-info">Read More</button>';
                   // htmlsummary += '<span class="cd-date">Jan 14</span>';
                 htmlsummary += '</div>';
               htmlsummary += '</div>';
          }
          htmlsummary += '</section>';
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
        document.getElementById("realtimealertshow").setAttribute("style", "position: fixed; width: 60%; background-color: white; min-height: 50px; max-height: 90px; overflow-y: auto; overflow-x: hidden; bottom: 0px; left: 17.5%; right: 0px; margin-bottom: 0px;");
      }else {
        togglestatus = 0;
        document.getElementById("realtimealertshow").setAttribute("style", "position: fixed; width: 60%; background-color: white; min-height: 110px; max-height: 140px; overflow-y: auto; overflow-x: hidden; bottom: 0px; left: 17.5%; right: 0px; margin-bottom: 0px;");
      }
        // $("#realtimealertshow").toggle();
    }
</script>

<?php
  $key = $this->config->item("GOOGLE_MAP_API_KEY");
  //$key = "AIzaSyAYe-6_UE3rUgSHelcU1piLI7DIBnZMid4";

  if(isset($key) && $key != "") { ?>
  <script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo $key;?>&callback=initialize" type="text/javascript"></script>
<?php } else { ?>
  <script src="http://maps.google.com/maps/api/js?sensor=false"></script>
<?php } ?>
