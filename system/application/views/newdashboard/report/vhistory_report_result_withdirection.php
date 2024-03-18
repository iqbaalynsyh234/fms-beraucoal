<?php
if($mapview == 0){
	$sizedata = 0;
}else{
	$sizedata = sizeof($data);
}
  $finaldata = array();
  $perulanganmaksimal = "";
  if ($sizedata == 0) {
    $nodata = "No Data";
  }else {
    $index = 0;
	if($totaldata>0){
		foreach ($data as $datanya) {
		  array_push($finaldata, array(
			"latitude"        => $data[$index]->gps_latitude_real,
			"longitude"       => $data[$index]->gps_longitude_real,
		  ));
		  $index++;
		}
	}

    $finaldata_json = json_encode($finaldata);
    $perulanganmaksimal = $sizedata;
  }
?>
<?php
	$key = $this->config->item("GOOGLE_MAP_API_KEY");
	if(isset($key) && $key != "") { ?>
		<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $key;?>&callback=initialize"></script>
	<?php } else { ?>
		<script src="http://maps.google.com/maps/api/js?V=3.3&amp;sensor=false"></script>
	<? } ?>
<script>
       var map;
			 var intervaldata;
       var coords;
			 var directionsService;
			 var directionsRenderer;
			 var polyline, symbol;
			 var markerss = [];

			 var overlaystatus         = 0;
			 var overlaysarray         = [];
       var perulanganmaksimal 	 = '<?php echo $perulanganmaksimal?>';
       var JSONString         	 = '<?php echo $finaldata_json ?>';
       var obj                	 = JSON.parse(JSONString);
			 var car                   = "M17.402,0H5.643C2.526,0,0,3.467,0,6.584v34.804c0,3.116,2.526,5.644,5.643,5.644h11.759c3.116,0,5.644-2.527,5.644-5.644 V6.584C23.044,3.467,20.518,0,17.402,0z M22.057,14.188v11.665l-2.729,0.351v-4.806L22.057,14.188z M20.625,10.773 c-1.016,3.9-2.219,8.51-2.219,8.51H4.638l-2.222-8.51C2.417,10.773,11.3,7.755,20.625,10.773z M3.748,21.713v4.492l-2.73-0.349 V14.502L3.748,21.713z M1.018,37.938V27.579l2.73,0.343v8.196L1.018,37.938z M2.575,40.882l2.218-3.336h13.771l2.219,3.336H2.575z M19.328,35.805v-7.872l2.729-0.355v10.048L19.328,35.805z";
			 // var iconBase 						 = '<?php echo base_url()?>assets/images/';
       // var icon     						 = new google.maps.MarkerImage(iconBase + "carnewmarker.png");
       console.log("obj : ", obj);

       coords = [];
          //for (var x = 1; x < obj.length; x++) {
		  for (var x = (obj.length-1); x >= 0; x--) {
	      //for ($i=($totaldata-1); $i>=0; $i--){
              coords.push({
                lat: obj[x].latitude,
                lng: obj[x].longitude
              });
              // console.log("x : ", x);
          }

        console.log("coords : ", coords);

       function initialize() {
           var markLAT = coords[0].lat;
           var markLNG = coords[0].lng;
           // console.log("coords : ", coords);


           map = new google.maps.Map(document.getElementById("map"), {
             center: new google.maps.LatLng(-3.558500, 115.639224), //markLAT, markLNG
             zoom: 17, //18
             mapTypeId: google.maps.MapTypeId.SATELLITE,
						 options: {
		           gestureHandling: 'greedy'
		         }
           });

					 var icon = {
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

					 marker = new google.maps.Marker({
						 position: new google.maps.LatLng(markLAT, markLNG), //markLAT, markLNG
						 map: map,
						 icon: icon,
						 id: currcoords
						 // optimized: false
					 });
					 markerss.push(marker);

					 directionsService    = new google.maps.DirectionsService();
					 directionsRenderer 	= new google.maps.DirectionsRenderer({
						 map: map,
						 preserveViewport: true
					 });

					 // directionsRenderer.setMap(map);

					 var toggleButton = document.createElement("button");
					 toggleButton.textContent = "BIB MAPS";
					 toggleButton.classList.add("custom-map-control-button");
				   map.controls[google.maps.ControlPosition.TOP_CENTER].push(toggleButton);

						toggleButton.addEventListener("click", () => {
						 addoverlay(map);
					 });

					 intervaldata  = setInterval(autorefreshwithdirectionmethod, 2000);
           // autoRefresh();
					 // addoverlay();
       }

       // google.maps.event.addDomListener(window, 'load', initialize);

			 var currcoords = 0;
			 var nextcoords = 1;
			 var loop = coords.length;
			 function autorefreshwithdirectionmethod(){

				 var currcoordfixlat      = coords[currcoords].lat;
				 var currcoordfixlng      = coords[currcoords].lng;
				 var nextcoordsfixlat     = coords[nextcoords].lat;
				 var nextcoordsfixlng     = coords[nextcoords].lng;

				 // console.log("currcoordfixlat : ", currcoordfixlat);
				 // console.log("currcoordfixlng : ", currcoordfixlng);
				 // console.log("nextcoordsfixlat : ", nextcoordsfixlat);
				 // console.log("nextcoordsfixlng : ", nextcoordsfixlng);
				 DeleteMarkers(currcoords)
				 calcroute(currcoordfixlat, currcoordfixlng, nextcoordsfixlat, nextcoordsfixlng);

				 currcoords = currcoords + 1;
				 nextcoords = nextcoords + 1;
			 }

			 function calcroute(originlat, originlng, destlat, destlng){
				 var curcoordnew = originlat + "," + originlng;
				 var nxtcoordnew = destlat + "," + destlng;
				 // console.log("origin : ", curcoordnew);
				 // console.log("dest : ", nxtcoordnew);

				 directionsService.route({
				      origin: curcoordnew,
				      destination: nxtcoordnew,
				      travelMode: google.maps.TravelMode.WALKING,
				    },
				    (response, status) => {
				      if (status === "OK") {
				        // directionsRenderer.setDirections(response);
								symbol = {
				          // path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
				 				 path: car,
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
				 				geodesic : false,
				         icons: [{
				           icon: symbol,
				           offset: '100%',
				           repeat: false
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
							 moveMarker(map, destlat, destlng);

				      } else {
				        window.alert("Directions request failed due to " + status);
				      }
				    }
				  );
			 }

			 // google.maps.event.addDomListener(window, 'load', initialize);

       function moveMarker(map, lat, lon) {
				 console.log("latonmovemarker : ", lat);
				 console.log("lngonmovemarker : ", lon);
           marker.setPosition(new google.maps.LatLng(lat, lon));
           // map.panTo(new google.maps.LatLng(lat, lon));
       }

			 function DeleteMarkers(id) {
				 //Loop through all the markers and remove
				 // console.log("marker pertama id yg dihapus : ", id);
				 for (var i = 0; i < markerss.length; i++) {
					 if (markerss[i].id == (id-1)) {
						 //Remove the om Map
						 markerss[i].setMap(null);

						 //Remove the marker from array.
						 markerss.splice(i, 1);
						 return;
					 }
				 }
			 }

       function autoRefresh() {
           var i, route, marker;

           route = new google.maps.Polyline({
               path: [],
               geodesic : true,
               strokeColor: '#FF0000',
               strokeOpacity: 1.0,
               strokeWeight: 2,
               editable: false,
               map:map
           });

           marker=new google.maps.Marker({map:map,icon:icon});
           //for (i = 0; i < coords.length; i++) {
		   for (var i = (coords.length-1); i >= 0; i--) {
		   //for ($i=($totaldata-1); $i>=0; $i--){
           setTimeout(function (coords){
               route.getPath().push(new google.maps.LatLng(coords.lat, coords.lng));
               moveMarker(map, marker, coords.lat, coords.lng);
           }, 500 * i, coords[i]);
         }
       }

			 function addoverlay(map){
			   console.log("masuk overlay");
			   if (overlaystatus == 0) {
			     console.log(map.getMapTypeId());
			     map.setMapTypeId((map.getMapTypeId() === 'satellite') ? 'hidden' : google.maps.MapTypeId.ROADMAP);

			     overlaystatus = 1;
			     // INI BUAT KOORDINAT PORTBUNATI3_SMALL.PNG
			     var latlngoverlay1    = new google.maps.LatLng(-3.752973, 115.627142);
			     var latlngoverlay2    = new google.maps.LatLng(-3.741050, 115.651995);
			     var jalanpcs1latlng1  = new google.maps.LatLng(-3.742245, 115.634781);
			     var jalanpcs1latlng2  = new google.maps.LatLng(-3.731234, 115.658683);
					 var jalanpcs2latlng1  = new google.maps.LatLng(-3.731293, 115.635119);
					 var jalanpcs2latlng2  = new google.maps.LatLng(-3.719973, 115.658875);
					 var jalanpcs3latlng1  = new google.maps.LatLng(-3.720716, 115.627392);
					 var jalanpcs3latlng2  = new google.maps.LatLng(-3.709085, 115.650982);
					 var jalanpcs4latlng1  = new google.maps.LatLng(-3.709082, 115.626623);
					 var jalanpcs4latlng2  = new google.maps.LatLng(-3.696086, 115.650399);
					 var jalanpcs5latlng1  = new google.maps.LatLng(-3.698382, 115.628647);
					 var jalanpcs5latlng2  = new google.maps.LatLng(-3.686875, 115.650329);
					 var jalanpcs6latlng1  = new google.maps.LatLng(-3.687041, 115.632740);
					 var jalanpcs6latlng2  = new google.maps.LatLng(-3.675839, 115.654079);
					 var jalanpcs7latlng1  = new google.maps.LatLng(-3.675896, 115.641743);
					 var jalanpcs7latlng2  = new google.maps.LatLng(-3.666118, 115.657159);
					 var jalanpcs8latlng1  = new google.maps.LatLng(-3.666373, 115.640177);
					 var jalanpcs8latlng2  = new google.maps.LatLng(-3.653464, 115.659622);
					 var jalanpcs9latlng1  = new google.maps.LatLng(-3.653489, 115.642516);
					 var jalanpcs9latlng2  = new google.maps.LatLng(-3.642623, 115.655891);
					 var jalanpcs10latlng1  = new google.maps.LatLng(-3.642598, 115.642706);
					 var jalanpcs10latlng2  = new google.maps.LatLng(-3.633244, 115.655783);
					 var jalanpcstekukankananataslatlng1  = new google.maps.LatLng(-3.642394, 115.647796);
					 var jalanpcstekukankananataslatlng2  = new google.maps.LatLng(-3.624354, 115.659467);
					 var jalanpcs15latlng1  = new google.maps.LatLng(-3.624377, 115.645157);
					 var jalanpcs15latlng2  = new google.maps.LatLng(-3.613030, 115.657190);
					 var jalanpcs16latlng1  = new google.maps.LatLng(-3.615390, 115.640438);
					 var jalanpcs16latlng2  = new google.maps.LatLng(-3.604488, 115.664935);
					 var jalanpcs17latlng1  = new google.maps.LatLng(-3.605948, 115.650892);
					 var jalanpcs17latlng2  = new google.maps.LatLng(-3.591340, 115.675094);
					 var jalanpcs18latlng1  = new google.maps.LatLng(-3.591364, 115.651680);
					 var jalanpcs18latlng2  = new google.maps.LatLng(-3.579234, 115.666260);
					 var jalanpcs19latlng1  = new google.maps.LatLng(-3.579299, 115.652807);
					 var jalanpcs19latlng2  = new google.maps.LatLng(-3.574495, 115.659479);
					 var jalanpcs20latlng1  = new google.maps.LatLng(-3.574582, 115.653795);
					 var jalanpcs20latlng2  = new google.maps.LatLng(-3.571493, 115.657894);
					 var jalanpcs21latlng1  = new google.maps.LatLng(-3.571526, 115.652658);
					 var jalanpcs21latlng2  = new google.maps.LatLng(-3.568056, 115.656361);

					 var jalanpcs22latlng1  = new google.maps.LatLng(-3.568143, 115.651919);
					 var jalanpcs22latlng2  = new google.maps.LatLng(-3.566871, 115.656245);

					 var jalanpcs23latlng1  = new google.maps.LatLng(-3.567189, 115.645221);
					 var jalanpcs23latlng2  = new google.maps.LatLng(-3.558805, 115.661915);

					 var jalanpcs24latlng1  = new google.maps.LatLng(-3.558823, 115.645198);
					 var jalanpcs24latlng2  = new google.maps.LatLng(-3.554506, 115.656520);


					 var imageportfix                     = "<?php echo base_url()?>assets/images/portbunati3_small.png";
					 var imagejalanpcs1fix                = "<?php echo base_url()?>assets/images/pcs_1_new.png";
				 	 var imagejalanpcs2fix                = "<?php echo base_url()?>assets/images/pcs_2_new.png";
					 var imagejalanpcs3fix                = "<?php echo base_url()?>assets/images/pcs_3_new.png";
					 var imagejalanpcs4fix                = "<?php echo base_url()?>assets/images/pcs_4_new.png";
					 var imagejalanpcs5fix                = "<?php echo base_url()?>assets/images/pcs_5_new.png";
					 var imagejalanpcs6fix                = "<?php echo base_url()?>assets/images/pcs_6_new.png";
					 var imagejalanpcs7fix                = "<?php echo base_url()?>assets/images/pcs_7_new.png";
					 var imagejalanpcs8fix                = "<?php echo base_url()?>assets/images/pcs_8_new.png";
					 var imagejalanpcs9fix                = "<?php echo base_url()?>assets/images/pcs_9_new.png";
					 var imagejalanpcs10fix               = "<?php echo base_url()?>assets/images/pcs_10_new.png";
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

			     var imageport                   = new google.maps.LatLngBounds(latlngoverlay1, latlngoverlay2);
			     var imagejalanpcs1              = new google.maps.LatLngBounds(jalanpcs1latlng1, jalanpcs1latlng2);
					 var imagejalanpcs2                = new google.maps.LatLngBounds(jalanpcs2latlng1, jalanpcs2latlng2);
					 var imagejalanpcs3                = new google.maps.LatLngBounds(jalanpcs3latlng1, jalanpcs3latlng2);
					 var imagejalanpcs4                = new google.maps.LatLngBounds(jalanpcs4latlng1, jalanpcs4latlng2);
					 var imagejalanpcs5                = new google.maps.LatLngBounds(jalanpcs5latlng1, jalanpcs5latlng2);
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

			     Overlay                        = new google.maps.GroundOverlay(imageportfix, imageport);
			     Overlayjalanpcs_1              = new google.maps.GroundOverlay(imagejalanpcs1fix, imagejalanpcs1);
					 Overlayjalanpcs_2                = new google.maps.GroundOverlay(imagejalanpcs2fix, imagejalanpcs2);
					 Overlayjalanpcs_3                = new google.maps.GroundOverlay(imagejalanpcs3fix, imagejalanpcs3);
					 Overlayjalanpcs_4                = new google.maps.GroundOverlay(imagejalanpcs4fix, imagejalanpcs4);
					 Overlayjalanpcs_5                = new google.maps.GroundOverlay(imagejalanpcs5fix, imagejalanpcs5);
					 Overlayjalanpcs_6                = new google.maps.GroundOverlay(imagejalanpcs6fix, imagejalanpcs6);
					 Overlayjalanpcs_7                = new google.maps.GroundOverlay(imagejalanpcs7fix, imagejalanpcs7);
					 Overlayjalanpcs_8                = new google.maps.GroundOverlay(imagejalanpcs8fix, imagejalanpcs8);
					 Overlayjalanpcs_9                = new google.maps.GroundOverlay(imagejalanpcs9fix, imagejalanpcs9);
					 Overlayjalanpcs_10               = new google.maps.GroundOverlay(imagejalanpcs10fix, imagejalanpcs10);
					 // Overlayjalanpcs_11               = new google.maps.GroundOverlay(imagejalanpcs11fix, imagejalanpcs11);
					 // Overlayjalanpcs_12               = new google.maps.GroundOverlay(imagejalanpcs12fix, imagejalanpcs12);
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

			     Overlay.setMap(map);
			     Overlayjalanpcs_1.setMap(map);
					 Overlayjalanpcs_2.setMap(map);
					 Overlayjalanpcs_3.setMap(map);
					 Overlayjalanpcs_4.setMap(map);
					 Overlayjalanpcs_5.setMap(map);
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

			     // ARRAY overlay
			     overlaysarray.push(Overlay);
			     overlaysarray.push(Overlayjalanpcs_1);
					 overlaysarray.push(Overlayjalanpcs_2);
					 overlaysarray.push(Overlayjalanpcs_3);
					 overlaysarray.push(Overlayjalanpcs_4);
					 overlaysarray.push(Overlayjalanpcs_5);
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
</script>
<style media="screen">
#map {
   height:500px;
   width:100%;

}

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
</style>
<script src="<?php echo base_url();?>assets/js/jsblong/jquery.table2excel.js"></script>
<script>
jQuery(document).ready(
		function()
		{
			jQuery("#export_xcel").click(function()
			{
				window.open('data:application/vnd.ms-excel,' + encodeURIComponent(jQuery('#isexport_xcel').html()));
			});
		}
	);


</script>

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
							echo "<p>".$nodata."</p>";
				}else{ ?>
					<div class="col-md-12 col-sm-12">
						<small>Total Coordinate (<?=$totaldata;?>)</small>
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

<div class="row">
	<div class="col-md-12 col-sm-12">
		<div class="panel" style="display:none">
			<header class="panel-heading panel-heading-blue">REPORT</header>
				<div class="panel-body" id="bar-parent10">
					<div class="row">
					<?php if (count($data) == 0) {
							echo "<p>".$nodata."</p>";
					}else{ ?>
						<div class="col-md-12 col-sm-12">

							<div class="col-lg-4 col-sm-4">
								<a href="javascript:void(0);" id="export_xcel" type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect m-b-10 btn-circle btn-info"><small>Export to Excel</small></a>
							</div>

							<div id="isexport_xcel">
							<table class="table table-striped custom-table table-hover">
								<thead>
									<tr>
										<th style="text-align:center;" width="2%">No</td>
										<th style="text-align:center;" width="8%">Vehicle</th>
										<th style="text-align:center;" width="15%">Datetime</th>
										<th style="text-align:center;" width="25%">Position</th>
										<th style="text-align:center;" width="5%">GPS Status</th>
										<th style="text-align:center;" width="5%">Speed (km/jam)</th>
									<?php if (isset($vehicle_type) && (in_array(strtoupper($vehicle_type), $this->config->item("vehicle_gtp")))) { ?>
										<th style="text-align:center;" width="5%">Engine</th>
										<th style="text-align:center;" width="7%">Odometer (km)</th>
									<?php } ?>



									</tr>
								</thead>
								<tbody>

								</tbody>
							</table>
							</div>
						</div>

					<?php } ?>

					</div>
				</div>
		</div>
	</div>
</div>
