<?php
  $sizedata = sizeof($data);
  // echo "<pre>";
  // var_dump($data);die();
  // echo "<pre>";
  $finaldata = array();
  $perulanganmaksimal = "";
  if ($sizedata == 0) {
    echo "Tidak Ada Data";
  }elseif ($sizedata < 21){
    $index = 0;
    for ($index=0; $index < $sizedata; $index++) {
        array_push($finaldata, array(
          "sikon"          => "1",
          "geofence_start" => $data[$index]->trip_mileage_geofence_start,
          "geofence_end"   => $data[$index]->trip_mileage_geofence_end,
          "lnglatend"       => str_replace(' ', ', ', $data[$index]->trip_mileage_coordinate_end),
          "lnglat_pecahend" => explode(' ', $data[$index]->trip_mileage_coordinate_end),
          "lnglat"         => str_replace(' ', ', ', $data[$index]->trip_mileage_coordinate_start),
          "lnglat_pecah"   => explode(' ', $data[$index]->trip_mileage_coordinate_start),
          "title"          => $data[$index]->trip_mileage_location_start,
          "titleends"      => $data[$index]->trip_mileage_location_end,
          "start_time"     => $data[$index]->trip_mileage_start_time,
          "end_time"       => $data[$index]->trip_mileage_end_time,
          "duration"       => $data[$index]->trip_mileage_duration,
        ));
        $index++;
    }
    $finaldata_json = json_encode($finaldata);
    // echo "<pre>";
    // var_dump($finaldata);die();
    // echo "<pre>";
    $perulanganmaksimal = $sizedata;
  }else {
    $index = 0;
    for ($index=0; $index < 21; $index++) {
        array_push($finaldata, array(
          "sikon"          => "2",
          "geofence_start"  => $data[$index]->trip_mileage_geofence_start,
          "geofence_end"    => $data[$index]->trip_mileage_geofence_end,
          "lnglatend"       => str_replace(' ', ', ', $data[$index]->trip_mileage_coordinate_end),
          "lnglat_pecahend" => explode(' ', $data[$index]->trip_mileage_coordinate_end),
          "lnglat"          => str_replace(' ', ', ', $data[$index]->trip_mileage_coordinate_start),
          "lnglat_pecah"    => explode(' ', $data[$index]->trip_mileage_coordinate_start),
          "title"           => $data[$index]->trip_mileage_location_start,
          "titleends"       => $data[$index]->trip_mileage_location_end,
          "start_time"      => $data[$index]->trip_mileage_start_time,
          "end_time"        => $data[$index]->trip_mileage_end_time,
          "duration"        => $data[$index]->trip_mileage_duration,
        ));
        $index++;
    }
    $finaldata_json = json_encode($finaldata);
    $perulanganmaksimal = 20;
  }
?>

<script type="text/javascript">
  // MAPS START HERE
  var map;
  var directionsDisplay;
  var directionsService;
  var polyline, symbol;
  var icons, count;
  var positions = [];
  var markers = [];
  var markertujuanakhir;
  var foranimate = 0;
  var count = 0;
  var lastdata;
  var waypts;
  var JSONString;
  var obj;
  var perulanganmaksimal = '<?php echo $perulanganmaksimal?>';

  // console.log("perulanganmaksimal : ", perulanganmaksimal);

    function initialize() {
      JSONString = '<?php echo $finaldata_json ?>';
      obj = JSON.parse(JSONString);
      console.log("obj : ", obj);


    var map = new google.maps.Map(
      document.getElementById("map"), {
        center: new google.maps.LatLng(obj[0].lnglat_pecah[0], obj[0].lnglat_pecah[1]),
        zoom: 12,
        mapTypeId: google.maps.MapTypeId.ROADMAP
      });

      var iconBase = '<?php echo base_url()?>assets/images/';
            var pos_pertama = new google.maps.LatLng(obj[0].lnglat_pecah[0], obj[0].lnglat_pecah[1]);
            // TUUAN AKHIR
            lastdata = obj.pop();
            var pos_terakhir = new google.maps.LatLng(lastdata.lnglat_pecah[0], lastdata.lnglat_pecah[1]);
            var locations = [
               [obj[0].title, obj[0].lnglat_pecah[0], obj[0].lnglat_pecah[1], obj[0].geofence_start, obj[0].start_time, obj[0].end_time, obj[0].duration],
               [lastdata.titleends, lastdata.lnglat_pecahend[0], lastdata.lnglat_pecahend[1], lastdata.geofence_end, lastdata.start_time, lastdata.end_time, lastdata.duration]
             ];
             // console.log("locations : ", locations);

             var markerutama = new google.maps.Marker({
               position: new google.maps.LatLng(locations[0][1], locations[0][2]),
               map: map,
               title : locations[0][3] + '\n' + locations[0][0] + '\n' + locations[0][1] + ', ' + locations[0][2] + '\n' + locations[0][4] + '\n' + locations[0][6],
               icon : iconBase + 'origin-marker.png'
             });

             var markerutama2 = new google.maps.Marker({
               position: new google.maps.LatLng(locations[1][1], locations[1][2]),
               map: map,
               title : locations[1][3] + '\n' + locations[1][0] + '\n' + locations[1][1] + ', ' + locations[1][2] + '\n' + locations[1][5] + '\n' + locations[1][6],
               icon : iconBase + 'finish-marker.png'
             });


            for (var j = 1; j < obj.length -1; j++) {
              var pos = new google.maps.LatLng(obj[j].lnglat_pecah[0], obj[j].lnglat_pecah[1]);

              markers[j] = new google.maps.Marker({
                  position: pos,
                  map: map,
                  title: j+1 + ". " + obj[j].geofence_start + '\n' + obj[j].title + '\n' + obj[j].lnglat_pecah[0] + ', ' + obj[j].lnglat_pecah[1] + '\n' + obj[j].end_time + '\n' + obj[j].duration,
                  id: j,
                  // icon : iconBase + 'marker_baru2.png'
              });
          }



    var directionsService = new google.maps.DirectionsService();
    var directionsDisplay = new google.maps.DirectionsRenderer({
      map: map,
      preserveViewport: true
    });
    // console.log("Lastdata : ", lastdata);
    // console.log("perulangan maksimal : ", perulanganmaksimal);
     waypts = [];
        for (var x = 1; x < obj.length -1; x++) {
            waypts.push({
              location: new google.maps.LatLng(obj[x].lnglat_pecah[0], obj[x].lnglat_pecah[1]),
              stopover: false
            });
            // console.log("x : ", x);
        }

    directionsService.route({
      origin: new google.maps.LatLng(obj[0].lnglat_pecah[0], obj[0].lnglat_pecah[1]),
      destination: new google.maps.LatLng(lastdata.lnglat_pecah[0], lastdata.lnglat_pecah[1]),
        waypoints: waypts,
        travelMode: google.maps.TravelMode.DRIVING
    }, function(response, status) {
      if (status === google.maps.DirectionsStatus.OK) {
         symbol = {
           path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
           // path : 'M 10,30 A 20,20 0,0,1 50,30 A 20,20 0,0,1 90,30 Q 90,60 50,90 Q 10,60 10,30 z',
           scale: 3,
           strokeColor: '#F00'
         };
         polyline = new google.maps.Polyline({
          path: [],
          strokeColor: '#0000FF',
          strokeWeight: 3,
           geodesic: geodesicvalue,
          icons: [{
            icon: symbol,
            offset: '100%',
            repeat: repeated
           }]
        });
        var bounds = new google.maps.LatLngBounds();
        // console.log("polyline", polyline);

        var legs = response.routes[0].legs;
        // console.log("legs", legs);
        for (ulang = 0; ulang < legs.length; ulang++) {
          var steps = legs[ulang].steps;
          for (j = 0; j < steps.length; j++) {
            var nextSegment = steps[j].path;
            for (k = 0; k < nextSegment.length; k++) {
              polyline.getPath().push(nextSegment[k]);
              bounds.extend(nextSegment[k]);
            }
          }
        }

        polyline.setMap(map);
        // map.setCenter(bounds.getCenter());

        // animateCircle();
      } else {
        window.alert('Directions request failed to finished beacuse : ' + status);
      }
    });
  }



    // Click events
    jQuery('#startAnimate').click(function(){
      jQuery("#startAnimate").hide();
      jQuery("#resumeAnimate").hide();
      jQuery("#stopAnimate").show();
        playAnimate();
    });

    jQuery("#resumeAnimate").click(function(){
      console.log("foranimate : ", foranimate);
      foranimate = window.setInterval(playme, 500);
    });

    jQuery("#stopAnimate").click(function(){
      jQuery("#startAnimate").hide();
      jQuery("#resumeAnimate").hide();
      jQuery("#playAgainAnimate").show();
      stopAnimate();
      foranimate = 0;
      count = 0;
    });

    jQuery("#playAgainAnimate").click(function(){
      foranimate = window.setInterval(playme, 500);
    });

    jQuery('#pauseAnimate').click(function(){
      jQuery("#resumeAnimate").show();
      jQuery("#playAgainAnimate").hide();
       pauseAnimate();
    });

    function pauseAnimate() {
     console.log("foranimate Terakhir pause animate: ", foranimate);
     console.log("count Terakhir: ", count);
      window.clearInterval(foranimate);
    }

    function stopAnimate() {
     console.log("foranimate Terakhir to stop animater: ", foranimate);
     console.log("count Terakhir to stop animate: ", count);
      window.clearInterval(count);
      window.clearInterval(foranimate);
    }

    function playAnimate() {
      foranimate = window.setInterval(playme, 500);
    }

    function playme(){
     if (foranimate > 0) {
       count = (count + 1) % 200;
       icons = polyline.get('icons');
       icons[0].offset = (count / 2) + '%';
       polyline.set('icons', icons);
       console.log("count : ", count);
       console.log("foranimate : ", foranimate);
     }
    }
    google.maps.event.addDomListener(window, "load", initialize);
</script>
<style media="screen">
#map {
  height: 600px;
  width: 100%;
}
</style>
<button type="button" id="pauseAnimate">Pause</button>
<button type="button" id="stopAnimate" style="display: none;">Stop</button>
<button type="button" id="startAnimate">Play</button>
<button type="button" id="resumeAnimate" style="display: none;">Resume</button>
<button type="button" id="playAgainAnimate" style="display: none;">Play Again</button>
<div id="map"></div>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCfgBNhidINbKJELAwzCrVVBrePClgFLbo&callback=initialize"></script>
<!--<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDgDxL_3CpFInoeSmGy-oZElFJeKtgEUWA&callback=initialize"></script>-->
