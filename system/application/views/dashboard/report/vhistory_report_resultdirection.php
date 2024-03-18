<?php
	$sizedata = 1;
?>
<script>
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

	var car = "M17.402,0H5.643C2.526,0,0,3.467,0,6.584v34.804c0,3.116,2.526,5.644,5.643,5.644h11.759c3.116,0,5.644-2.527,5.644-5.644 V6.584C23.044,3.467,20.518,0,17.402,0z M22.057,14.188v11.665l-2.729,0.351v-4.806L22.057,14.188z M20.625,10.773 c-1.016,3.9-2.219,8.51-2.219,8.51H4.638l-2.222-8.51C2.417,10.773,11.3,7.755,20.625,10.773z M3.748,21.713v4.492l-2.73-0.349 V14.502L3.748,21.713z M1.018,37.938V27.579l2.73,0.343v8.196L1.018,37.938z M2.575,40.882l2.218-3.336h13.771l2.219,3.336H2.575z M19.328,35.805v-7.872l2.729-0.355v10.048L19.328,35.805z";

  function initialize() {
    var obj = [
      {"lat" : "-3.745498", "lng" : "115.645363"}, // PORT BUNATI
			{"lat" : "-3.706639", "lng" : "115.644167"}, // hauling 1
			{"lat" : "-3.703",   "lng" : "115.644111"},  // hauling 2
			{"lat" : "-3.692944", "lng" : "115.644139"}, // hauling 3
			{"lat" : "-3.674389", "lng" : "115.649333"}, // hauling 4
			{"lat" : "-3.671694", "lng" : "115.649472"}, // hauling 5

      {"lat" : "-3.598056", "lng" : "115.625639"}, // ROM 3
			{"lat" : "-3.671694", "lng" : "115.649472"}, // hauling 5
			{"lat" : "-3.674389", "lng" : "115.649333"}, // hauling 4
			{"lat" : "-3.692944", "lng" : "115.644139"}, // hauling 3
			// {"lat" : "-3.703",   "lng" : "115.644111"},  // hauling 2
			// {"lat" : "-3.706639", "lng" : "115.644167"}, // hauling 1

			// {"lat" : "-3.5705",   "lng" : "115.633222"}, // ROM 4
			// {"lat" : "-3.536114", "lng" : "115.639194"}, // ROM 6
			// {"lat" : "-3.50825",  "lng" : "115.637917"}, // ROM 7
			// {"lat" : "-3.51225",  "lng" : "115.638694"}, // ROM 8
      // {"lat" : "-3.742741", "lng" : "115.652681"}  // PORT TIA
			{"lat" : "-3.745498", "lng" : "115.645363"}, // PORT BUNATI
    ];
    // obj = JSON.parse(JSONString);
    // console.log("obj historymap direction: ", JSONString);
    console.log("obj historymap direction: ", obj);
    console.log("obj historymap direction: ", parseFloat(obj[0].lat)+' - '+parseFloat(obj[0].lng));


  var map = new google.maps.Map(
    document.getElementById("map"), {
      center: new google.maps.LatLng(parseFloat(obj[0].lat), parseFloat(obj[0].lng)),
      zoom: 12,
      mapTypeId: google.maps.MapTypeId.SATELLITE,
			options: {
				gestureHandling: 'greedy'
			}
    });

    var iconBase = '<?php echo base_url()?>assets/images/';
           var markerutama = new google.maps.Marker({
             position: new google.maps.LatLng(parseFloat(obj[0].lat), parseFloat(obj[0].lng)),
             map: map,
             title : "Origin",
             icon : iconBase + 'origin-marker.png'
           });

					 // TUUAN AKHIR
           lastdata = obj.pop();
           console.log("lastdata : ", lastdata);
           var markerutama2 = new google.maps.Marker({
             position: new google.maps.LatLng(parseFloat(lastdata.lat), parseFloat(lastdata.lng)),
             map: map,
             title : "End Point",
             icon : iconBase + 'finish-marker.png'
           });

          for (var j = 1; j < obj.length -1; j++) {
            var pos = new google.maps.LatLng(parseFloat(obj[j].lat), parseFloat(obj[j].lng));

            markers[j] = new google.maps.Marker({
                position: pos,
                map: map,
                title: j + ". ",
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
      for (var x = 0; x < obj.length; x++) {
          waypts.push({
            location: new google.maps.LatLng(parseFloat(obj[x].lat), parseFloat(obj[x].lng)),
            stopover: false
          });
          // console.log("x : ", x);
      }
      console.log("waypts : ", waypts);
      // console.log("latlnginwypts : ", obj[1].lat + ' - ' + obj[1].lng);

  directionsService.route({
    origin: new google.maps.LatLng(parseFloat(obj[0].lat), parseFloat(obj[0].lng)),
    destination: new google.maps.LatLng(parseFloat(lastdata.lat), parseFloat(lastdata.lng)),
      waypoints: waypts,
      travelMode: google.maps.TravelMode.DRIVING
  }, function(response, status) {
    if (status === google.maps.DirectionsStatus.OK) {
       symbol = {
         // path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
				 path: car,
         // path : 'M 10,30 A 20,20 0,0,1 50,30 A 20,20 0,0,1 90,30 Q 90,60 50,90 Q 10,60 10,30 z',
         strokeColor: '#F00',
				 scale: .5,
				 // anchor: new google.maps.Point(25,10),
				 // scaledSize: new google.maps.Size(30,20),
				 strokeColor: 'white',
				 strokeWeight: .10,
				 fillOpacity: 1,
				 fillColor: '#00b300',
				 offset: '5%'
       };
       polyline = new google.maps.Polyline({
        path: [],
        strokeColor: '#FF0000',
        strokeWeight: 3,
				geodesic : true,
        icons: [{
          icon: symbol,
          offset: '100%',
          repeat: true
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
// google.maps.event.addDomListener(window, "load", initialize);

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
     count           = (count + 1) % 300;
     icons           = polyline.get('icons');
     icons[0].offset = (count / 2) + '%';
     polyline.set('icons', icons);
     console.log("count : ", count);
     console.log("foranimate : ", foranimate);
   }
  }
</script>
<style media="screen">
#map {
   height:400px;
   width:100%;
}
</style>
<script src="<?php echo base_url();?>assets/js/jsblong/jquery.table2excel.js"></script>

<?php if($mapview == 1){ ?>
<div class="row">
	<div class="col-md-12 col-sm-12">
		<div class="panel">
			<header class="panel-heading panel-heading-blue">MAP</header>
				<div class="panel-body" id="bar-parent10">
					<div class="row">
					<div class="col-md-12 col-sm-12">
						<small>Total Data GPS (<?=$totalgps;?>)</small>
					</div>
				<?php if ($sizedata == 0) {
							echo "<p>No Data</p>";
				}else{ ?>
					<div class="col-md-12 col-sm-12">
						<small>Total Coordinate (<?=$totaldata;?>)</small>
					</div>
          	<div id="panelcontroldirection" style="margin-left:1%;">
							<button type="button" id="pauseAnimate" class="btn btn-warning btn-sm">Pause</button>
		          <button type="button" id="stopAnimate" class="btn btn-danger btn-sm" style="display: none;">Stop</button>
		          <button type="button" id="startAnimate" class="btn btn-success btn-sm">Play</button>
		          <button type="button" id="resumeAnimate" class="btn btn-primary btn-sm" style="display: none;">Resume</button>
		          <button type="button" id="playAgainAnimate" class="btn btn-primary btn-sm" style="display: none;">Play Again</button>
          	</div>
					<div class="col-md-12 col-sm-12">
						<div id="map"></div>
					</div>
				<?php } ?>
					</div>
				</div>
		</div>
	</div>
</div>
<?php } ?>

<div class="col-lg-6 col-sm-6">
	<input id="btn_hide_form" class="btn btn-circle btn-danger" title="" type="button" value="Hide Form" onclick="javascript:return option_form('hide')" />
	<input id="btn_show_form" class="btn btn-circle btn-success" title="" type="button" value="Show Form" onClick="javascript:return option_form('show')" style="display:none"/>
</div>
<div class="col-lg-2 col-sm-2">
</div>
<br />

<?php
	$key = $this->config->item("GOOGLE_MAP_API_KEY");
	if(isset($key) && $key != "") { ?>
		<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $key;?>&callback=initialize"></script>
	<?php } else { ?>
		<script src="http://maps.google.com/maps/api/js?V=3.3&amp;sensor=false"></script>
	<? } ?>
