<!-- start sidebar menu -->
<div class="sidebar-container">
  <?=$sidebar;?>
</div>
<!-- end sidebar menu -->

<!-- start page content -->
<div class="page-content-wrapper">
  <div class="page-content">
    <div class="row">
      <div id="map_canvas" style="width: 1121px; height: 530px; margin-top: -1%;"></div>
    </div>
  </div>
</div>
<!-- end page content -->

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
  var infowindow, infowindow2, infowindowkedua = null;
  var JSONStringdataticket, JSONStringdatadatadest, objdataticket, objdatadatadest, JSONStringalldata;
  var car = "M17.402,0H5.643C2.526,0,0,3.467,0,6.584v34.804c0,3.116,2.526,5.644,5.643,5.644h11.759c3.116,0,5.644-2.527,5.644-5.644 V6.584C23.044,3.467,20.518,0,17.402,0z M22.057,14.188v11.665l-2.729,0.351v-4.806L22.057,14.188z M20.625,10.773 c-1.016,3.9-2.219,8.51-2.219,8.51H4.638l-2.222-8.51C2.417,10.773,11.3,7.755,20.625,10.773z M3.748,21.713v4.492l-2.73-0.349 V14.502L3.748,21.713z M1.018,37.938V27.579l2.73,0.343v8.196L1.018,37.938z M2.575,40.882l2.218-3.336h13.771l2.219,3.336H2.575z M19.328,35.805v-7.872l2.729-0.355v10.048L19.328,35.805z";


  function initialize() {
    JSONStringalldata      = '<?php echo json_encode($vehicle); ?>';
    JSONString             = '<?php echo json_encode($vehicle[0]['vehicle_device']); ?>';
    JSONStringdataticket   = '<?php echo json_encode($dataticket); ?>';
    JSONStringdatadatadest = '<?php echo json_encode($datadest); ?>';

    obj                    = JSON.parse(JSONString);
    objdataticket          = JSON.parse(JSONStringdataticket);
    objdatadatadest        = JSON.parse(JSONStringdatadatadest);
    objdataalldata         = JSON.parse(JSONStringalldata);

    console.log("obj : ", obj);
    console.log("objdataticket : ", objdataticket);
    console.log("objdatadatadest : ", objdatadatadest);
    console.log("objdataalldata : ", objdataalldata);

     bounds = new google.maps.LatLngBounds();
     boundsdest = new google.maps.LatLngBounds();

     map = new google.maps.Map(
      document.getElementById("map_canvas"), {
        center: new google.maps.LatLng(-6.2293867, 106.6894289),
        zoom: 10,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        options: {
           gestureHandling: 'greedy'
         }
      });

      var icondest = {
        url: "http://transporter.lacak-mobil.com/assets/images/markergif.gif", // url JIKA GIF
        // path: 'assets/images/iconpulsemarker.gif',
        // scale: .5,
        anchor: new google.maps.Point(25,10),
        scaledSize: new google.maps.Size(25,25)
      };

      for (var x = 0; x < objdatadatadest.length; x++) {
        var no = x + 1;
        var positiondest = new google.maps.LatLng(objdatadatadest[x].dest_lat, objdatadatadest[x].dest_lng);
        boundsdest.extend(positiondest);

        markerdest = new google.maps.Marker({
          position: positiondest,
          map: map,
          icon: icondest,
          title: no + ". "+ objdatadatadest[x].dest_address,
          id: objdatadatadest[x].dest_address,
          optimized: false
        });
        markerdest.setIcon(icondest);
        markerdests.push(markerdest);
      }

      jQuery.post("<?=base_url();?>map/lastinfo", {device: obj, lasttime: '<?=$this->config->item('timer_list');?>'},
        function(r)
        {
          console.log("response : ", r.vehicle);
          add_new_markersnya(r.vehicle);
        }, "json");

    var intervalkedua = setInterval(getdata, 10000);
  }

  function getdata() {
        jQuery.post("<?=base_url();?>map/lastinfo", {device: obj, lasttime: '<?=$this->config->item('timer_list');?>'},
          function(r)
          {
            console.log("response : ", r.vehicle);
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
      var engine = "";
      if (value.gps.gps_speed_fmt > 0 && value.status1 == true) {
        var icon = {
            path: car,
            scale: .5,
            strokeColor: 'white',
            strokeWeight: .10,
            fillOpacity: 1,
            fillColor: '#00b300',
            offset: '5%'
        };
        engine = "ON";
      }else {
        var icon = {
            path: car,
            scale: .5,
            strokeColor: 'white',
            strokeWeight: .10,
            fillOpacity: 1,
            fillColor: '#0000FF',
            offset: '5%'
        };
        engine = "OFF";
      }

      markernya = new google.maps.Marker({
        map: map,
        icon: icon,
        position: new google.maps.LatLng(parseFloat(value.gps.gps_latitude_real_fmt), parseFloat(value.gps.gps_longitude_real_fmt)),
        // title: value.vehicle_no + ' - ' + value.vehicle_name + "\n" +
        //  "GPS Time : " + value.gps.gps_date_fmt + " " + value.gps.gps_time_fmt + "\n" +
        //  "Position : " + value.gps.georeverse.display_name + "\n" +
        //  "Coord : " + value.gps.gps_latitude_real_fmt + ", " + value.gps.gps_longitude_real_fmt + "\n" +
        //   "Speed : " + value.gps.gps_speed +" kph" + "\n" +
        //   // "Engine : " + "\n" +
        //   "Odometer : " + value.totalodometer + "\n",
          // "Engine : " + "\n" +
          // "No. Card : " + value.vehicle_card_no "\n",

        id: value.vehicle_device_name+"@"+value.vehicle_device_host
      });
      icon.rotation = Math.ceil(value.gps.gps_course);
      markernya.setIcon(icon);
      markerss.push(markernya);
      map.setCenter(markernya.getPosition());

      var infowindow = new google.maps.InfoWindow({
        content: obj[i].auto_vehicle_no,
        maxWidth: 160
      });

      var gps_status = "";
      if (value.gps.gps_status == "A") {
        gps_status = "Good";
      }else {
        gps_status = "Not Good";
      }

      var ticketnumbertostring = [];
      $.each(objdataticket, function(idx2,val2) {
        var status = "";
          if (val2.ticket_status == 0) {
            status = "Waiting";
          }else if (val2.ticket_status == 1) {
            status = "On Duty";
          }else if (val2.ticket_status == 2) {
            status = "On Process";
          }else {
            status = "Completed";
          }
        var str = (idx2 + 1) + ". " + val2.ticket_number + " ("+ status +")";
        ticketnumbertostring.push(str);
      });
      // console.log(ticketnumbertostring.join('\n'));

      var string = "Ticket Number : <b style='color: blue;'> <br>" + ticketnumbertostring.join('<br>') + "</b><br>" +
       "Vehicle : " + value.vehicle_no + ' - ' + value.vehicle_name + ' - ' + objdataalldata[0].ticket_technician_name + "<br>" +
       "GPS Time : " + value.gps.gps_date_fmt + " " + value.gps.gps_time_fmt + "<br>" +
       "Position : " + value.gps.georeverse.display_name + "<br>" +
       "Coordinate : " + value.gps.gps_latitude_real_fmt + ", " + value.gps.gps_longitude_real_fmt + "<br>" +
        "Speed : " + value.gps.gps_speed +" kph" + "<br>" +
        "Engine : " + engine + "<br>" +
        "Odometer : " + value.totalodometer + "<br>" +
        "GPS Status : " + gps_status + "<br>" +
        "No. Card : " + value.vehicle_card_no;

      var infowindow = new google.maps.InfoWindow({
        content: string,
        maxWidth: 300
      });

      infowindow.open(map, markernya);

      drawRoute();

      // map.setmap(null);
      // markers.setmap(null);
      // marker.setPosition(marker);
      google.maps.event.addListener(markernya, 'click', function(e) {
        map.setZoom(16);
        map.setCenter(e.latLng);
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
    console.log("ini di drawroute");
    console.log("ini di drawroute coordsArray : ", coordsArray);
    console.log("ini di drawroute course : ", course);
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
      console.log("limit simultant : ", limit);
      console.log("timer simultant : ", timer);
     if(timer < limit || nextTimer <= limit){
       var strictLat   = parseFloat(coordsArray[timer].lat);
       var strictLng   = parseFloat(coordsArray[timer].lng);
       var newPosition = new google.maps.LatLng(strictLat,strictLng);
       console.log("coursenya : ", course[timer]);
       icon.rotation = Math.ceil(course[timer]);
       markernya.setIcon(icon);
       markernya.setPosition(newPosition);
       console.log("newPosition : ", newPosition);
       map.setCenter(newPosition);
       var timerHandle = setTimeout("movement(" + (d + 5) + ")", 3000);
       console.log("timer simultant 2: ", timer);
   }
     nextTimer++;
     timer++;
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
var center = {lat : parseFloat(response[0].auto_last_lat), lng: parseFloat(response[0].auto_last_long)};
console.log("center : ", center);

var string = response[0].vehicle_no + ' - ' + response[0].vehicle_name + "<br>" +
  "GPS Time : " + response[0].auto_last_update + "<br>Position : " + response[0].auto_last_position + "<br>Coord : " + response[0].auto_last_lat + ", " + response[0].auto_last_long + "<br>" +
  "Engine : " + response[0].auto_last_engine + "<br>" +
  "Speed : " + response[0].auto_last_speed + " kph" + "<br>" +
  "<a href='<?php echo base_url()?>maps/tracking/" + response[0].vehicle_id + "' target='_blank'>Tracking</a>";

 infowindowkedua = new google.maps.InfoWindow({
  content: string,
  maxWidth: 300
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
map.setCenter(center);
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
    maxWidth: 300
  });
  // DeleteMarkers(response[0].vehicle_device);
  // DeleteMarkerspertama(response[0].vehicle_device);

    var center = {lat : parseFloat(response[0].auto_last_lat), lng: parseFloat(response[0].auto_last_long)};
    console.log("center : ", center);

    infowindowkedua.setContent(string);
    map.setCenter(center);
    markernya.setPosition(center);
    infowindowkedua.open(map, this);
});

}, "json");
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
