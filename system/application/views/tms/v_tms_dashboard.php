<style media="screen">
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

<!-- start sidebar menu -->
<div class="sidebar-container">
  <?=$sidebar;?>
</div>
<!-- end sidebar menu -->

<!-- start page content -->
<div class="page-content-wrapper">


  <div class="page-content">
    <div class="row" style="margin-left:-31px; margin-top:-17px;">
      <div class="col-md-12" id="showmaps">
        <!-- <button type="button" class="btn btn-primary" style="margin-left: 88%; z-index: 1; margin-top: 1.5%; position: absolute;" title="Show Table" onclick="showtableperarea('<?php echo $companyid;?>')">
          <span class="fa fa-list"></span>
        </button> -->
         <div id="map_canvas" style="height:590px; width:101%; margin-left: 0%; margin-top: 0%; position:relative; "></div>
      </div>
    </div>

    <br>


  </div>
</div>

<!-- MODAL LIST VEHICLE -->
<div id="modallistvehicle" style="display: none;">
  <div id="mydivheader"></div>
  <div class="row" >
    <div class="col-md-12">
        <div class="card card-topline-yellow">
            <div class="card-head">
                <header>List of Vehicle</header>
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


<!-- end page content -->


<script type="text/javascript" src="js/script.js"></script>
<script src="<?php echo base_url()?>assets/dashboard/assets/js/jquery-1.7.1.min.js" type="text/javascript"></script>

<script>
  $("#showtable").hide();
  $("#modallistvehicle").hide();
  $("#modalfivereport").hide();

  var map;
  var datafixnya = "";
  var indeksglobal = 0;
  var infoWindow = null;
  var infoWindow2 = null;
  var i;
  var marker = [];
  var markernya = [];
  var markers = [];
  var markerss = [];
  var markerpools = [];
  var limitmobilnya;
  var laststatus = "-";
  var laststatus2;
  var intervalstart;
  var objectnumberfix = 1;
  var objectnumber = 0;
  var obj;
  var infowindowkedua = null;
  var markerpool = [];
  var JSONpoolmaster, objpoolmaster, objpoolmasterfix;

  var car = "M17.402,0H5.643C2.526,0,0,3.467,0,6.584v34.804c0,3.116,2.526,5.644,5.643,5.644h11.759c3.116,0,5.644-2.527,5.644-5.644 V6.584C23.044,3.467,20.518,0,17.402,0z M22.057,14.188v11.665l-2.729,0.351v-4.806L22.057,14.188z M20.625,10.773 c-1.016,3.9-2.219,8.51-2.219,8.51H4.638l-2.222-8.51C2.417,10.773,11.3,7.755,20.625,10.773z M3.748,21.713v4.492l-2.73-0.349 V14.502L3.748,21.713z M1.018,37.938V27.579l2.73,0.343v8.196L1.018,37.938z M2.575,40.882l2.218-3.336h13.771l2.219,3.336H2.575z M19.328,35.805v-7.872l2.729-0.355v10.048L19.328,35.805z";

  function initialize() {
    var user_id     = '<?php echo $this->sess->user_id?>';
    JSONString      = '<?php echo json_encode($datafix); ?>';
    // console.log("JSONString : ", JSONString);

    if (datafixnya == "") {
      try {
        var datacode = JSON.parse(JSONString);
        // console.log("disini objpoolmaster: ", objpoolmaster);
      } catch (e) {
        // console.log("e : ", e);
      }
    } else {
      var datacode = JSON.parse(JSONString);
    }

    obj = datacode;
    console.log("obj : ", obj);

    var bounds = new google.maps.LatLngBounds();
    var boundspool = new google.maps.LatLngBounds();
    map = new google.maps.Map(
      document.getElementById("map_canvas"), {
        center: new google.maps.LatLng(-6.1753871, 106.8249641),
        zoom: 8,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        options: {
          gestureHandling: 'greedy'
        }
      });

      // Add multiple markers to map
      marker, i;
      infowindow      = new google.maps.InfoWindow();

      // console.log("datafinya : ", datafixnya);
      var htmlautoupdaterow = "";
      for (i = 0; i < obj.length; i++) {
        var position = new google.maps.LatLng(obj[i].auto_last_lat, obj[i].auto_last_long);
        bounds.extend(position);
        // JIKA IS_UPDATE = YES MAKA ITU MOBIL HIJAU ATAU BIRU
        // JIKA US_UPDATE = NO MAKA MOBIL ITU MERAH
        if (obj[i].ticket_status == 0) {
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
              fillColor: '#0000FF',
              offset: '5%'
            };
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
              fillColor: '#FF0000',
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
                fillColor: '#0000FF',
                offset: '5%'
              };
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

        google.maps.event.addListener(marker, 'click', (function(marker, i) {
          return function() {
            var data = {device : marker.id};
            jQuery.post("<?php echo base_url() ?>maps/getlastinfonya", data, function(response){
              console.log("response saat mobil diklik nih : ", response);
              infowindow.close();
              var gps_status = response.gps_status;
                if (gps_status == "A") {
                  gps_status = "Good";
                }else {
                  gps_status = "Lost Signal";
                }
              var string = "<div style='z-index: 1;'>" +
               "Ticket Number : <b style='color: blue;'>" + obj[i].ticket_name_number + "</b><br>" +
                "Vehicle : " + obj[i].vehicle_no + " - " + obj[i].vehicle_name + " - " + obj[i].technician_name + "<br>" +
                "GPS Time : " + obj[i].auto_last_update + "<br>" + "Position : " + response.georeverse.display_name + "<br>" + "Coord : " + obj[i].auto_last_lat + ", " + obj[i].auto_last_long + "<br>" +
                "Engine : " + obj[i].auto_last_engine + "<br>" +
                "Speed : " + obj[i].auto_last_speed + " kph" + "<br>" +
                "Odometer : " + response.gps_odometer + "<br>" +
                "GPS Status : " + gps_status + "<br>" +
                // "Card No : " + obj[i].vehicle_card_no + "<br>" +
                "<a href='<?php echo base_url()?>tms/tracking/" + obj[i].vehicle_id + "' target='_blank'>Tracking</a>" +
                "</div>";

                infowindow = new google.maps.InfoWindow({
                  content: string,
                  maxWidth: 300
                });

                infowindow.setContent(string);
                map.setCenter(new google.maps.LatLng(parseFloat(response.gps_latitude_real_fmt), parseFloat(response.gps_longitude_real_fmt)));
                infowindow.open(map, marker);
            }, "json");
          };
        })(marker, i));
      }
      // intervalstart = setInterval(simultango, 15000); /// 30 detik after autocheck done (30 detik = 30000)
  }

  jQuery.maxZIndex = jQuery.fn.maxZIndex = function(opt) {
    var def = {
      inc: 10,
      group: "*"
    };
    jQuery.extend(def, opt);
    var zmax = 0;
    jQuery(def.group).each(function() {
      var cur = parseInt(jQuery(this).css('z-index'));
      zmax = cur > zmax ? cur : zmax;
    });
    if (!this.jquery)
      return zmax;

    return this.each(function() {
      zmax += def.inc;
      jQuery(this).css("z-index", zmax);
    });
  }

  function page(p) {
    if (p == undefined) {
      p = 0;
    }
    jQuery("#offset").val(p);
    jQuery("#result").html('<img src="<?php echo base_url();?>assets/transporter/images/loader2.gif">');
    jQuery("#loader").show();
  }

  function frmsearch_onsubmit() {
    jQuery("#loader").show();
    page(0);
    return false;
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
