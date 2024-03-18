<?php
  $sizedata = sizeof($data);
  $finaldata = array();
  $perulanganmaksimal = "";
  if ($sizedata == 0) {
    $nodata = "No Data";
  }elseif ($sizedata < 21){
    $index = 0;
    foreach ($data as $datanya) {
		if($data[$index]->trip_mileage_geofence_start != "0"){
			$geofence_start = $data[$index]->trip_mileage_geofence_start;
		}else{
			$geofence_start = "";
		}
      array_push($finaldata, array(
	    "geofence_start" => $geofence_start,
        "lnglat"         => str_replace(' ', ', ', $data[$index]->trip_mileage_coordinate_start),
        "lnglat_pecah"   => explode(' ', $data[$index]->trip_mileage_coordinate_start),
        "title"          => "(".date("d-m-Y H:i", strtotime($data[$index]->trip_mileage_start_time)).")"." "."( ".$data[$index]->trip_mileage_duration.")"." ".$data[$index]->trip_mileage_location_start
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
    foreach ($data as $datanya) {
		if($data[$index]->trip_mileage_geofence_start != "0"){
			$geofence_start = $data[$index]->trip_mileage_geofence_start;
		}else{
			$geofence_start = "";
		}
      array_push($finaldata, array(
		"geofence_start" => $geofence_start,
        "lnglat"         => str_replace(' ', ', ', $data[$index]->trip_mileage_coordinate_start),
        "lnglat_pecah"   => explode(' ', $data[$index]->trip_mileage_coordinate_start),
        "title"          => "(".date("d-m-Y H:i", strtotime($data[$index]->trip_mileage_start_time)).")".$data[$index]->trip_mileage_location_start
      ));
      $index++;
    }
    $finaldata_json = json_encode($finaldata);
    $perulanganmaksimal = 20;
  }
?>
<?php
	$key = $this->config->item("GOOGLE_MAP_API_KEY");
	if(isset($key) && $key != "") { ?>
		<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $key;?>&callback=initialize"></script>
	<?php } else { ?>
		<script src="http://maps.google.com/maps/api/js?V=3.3&amp;sensor=false"></script> 
	<? } ?>

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
      // console.log("obj : ", obj);

    var map = new google.maps.Map(
      document.getElementById("map"), {
        center: new google.maps.LatLng(obj[0].lnglat_pecah[0], obj[0].lnglat_pecah[1]),
        zoom: 12, //11
        mapTypeId: google.maps.MapTypeId.ROADMAP
      });

      var iconBase =
            '<?php echo base_url()?>assets/images/';
            var pos_pertama = new google.maps.LatLng(obj[0].lnglat_pecah[0], obj[0].lnglat_pecah[1]);
            // TUUAN AKHIR
            lastdata = obj.pop();
            var pos_terakhir = new google.maps.LatLng(lastdata.lnglat_pecah[0], lastdata.lnglat_pecah[1]);
            var locations = [
               [obj[0].title, obj[0].lnglat_pecah[0], obj[0].lnglat_pecah[1], obj[0].geofence_start],
               [lastdata.title, lastdata.lnglat_pecah[0], lastdata.lnglat_pecah[1], lastdata.geofence_start]
             ];
             // console.log("locations : ", locations);

             var markerutama = new google.maps.Marker({
               position: new google.maps.LatLng(locations[0][1], locations[0][2]),
               map: map,
               title : locations[0][3] + '\n' + locations[0][0] + '\n' + locations[0][1] + ', ' + locations[0][2],
               icon : iconBase + 'origin-marker.png'
             });

             var markerutama2 = new google.maps.Marker({
               position: new google.maps.LatLng(locations[1][1], locations[1][2]),
               map: map,
               title : locations[1][3] + '\n' + locations[1][0] + '\n' + locations[1][1] + ', ' + locations[1][2],
               icon : iconBase + 'finish-marker.png'
             });


            for (var j = 1; j < perulanganmaksimal - 1; j++) {
              var pos = new google.maps.LatLng(obj[j].lnglat_pecah[0], obj[j].lnglat_pecah[1]);

              markers[j] = new google.maps.Marker({
                  position: pos,
                  map: map,
                  title: obj[j].geofence_start + '\n' + obj[j].title + '\n' + obj[j].lnglat_pecah[0] + ', ' + obj[j].lnglat_pecah[1],
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
        for (var x = 1; x < perulanganmaksimal -1; x++) {
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
           scale: 6,
           strokeColor: '#F00'
         };
         polyline = new google.maps.Polyline({
          path: [],
          strokeColor: '#0000FF',
          strokeWeight: 3,
          icons: [{
            icon: symbol,
            offset: '100%'
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
      foranimate = window.setInterval(playme, 400);
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
      foranimate = window.setInterval(playme, 400);
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
      foranimate = window.setInterval(playme, 400);
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
   height:600px;
   width:100%;
   
}
</style>
<div class="col-lg-12 col-sm-12">	
<div class="col-lg-6 col-sm-6">	
	<input id="btn_hide_form" class="btn btn-circle btn-danger" title="" type="button" value="Hide Form" onclick="javascript:return option_form('hide')" style="display:none"/>
	<input id="btn_show_form" class="btn btn-circle btn-success" title="" type="button" value="Show Form" onClick="javascript:return option_form('show')" />
	
</div>	
<div class="row">
	<div class="col-md-12 col-sm-12">
		<div class="panel">
			<header class="panel-heading panel-heading-blue">MAP</header>
				<div class="panel-body" id="bar-parent10">
					<div class="row">	
					<div class="col-md-12 col-sm-12">
						<small>Total Data: <?=count($data);?></small>
					</div>
					<?php if ($sizedata == 0) {
							echo "<p>".$nodata."</p>";
					}else{ ?>
					<div class="col-md-12 col-sm-12">
							<small>Note: Map ini menunjukan maksimal 20 data status Engine ON. <br />
							Gunakan filter jam untuk menampilkan data </small>
					</div>
					<div class="col-md-12 col-sm-12">
					<button type="button" id="pauseAnimate" class="btn btn-circle btn-warning">Pause</button>
						<button type="button" id="stopAnimate" class="btn btn-circle btn-danger" style="display: none;">Stop</button>
						<button type="button" id="startAnimate" class="btn btn-circle btn-success">Play</button>
						<button type="button" id="resumeAnimate" class="btn btn-circle btn-success" style="display: none;">Resume</button>
						<button type="button" id="playAgainAnimate" class="btn btn-circle btn-success" style="display: none;">Play Again</button>
						<br />
						<div id="map"></div>
					</div>
					<?php } ?>
					</div>
				</div>
		</div>
	</div>
</div>
</div>
