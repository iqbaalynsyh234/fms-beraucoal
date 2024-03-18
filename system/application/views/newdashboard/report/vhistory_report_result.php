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
             center: new google.maps.LatLng(-3.666408, 115.644870), //markLAT, markLNG
             zoom: 16, //18
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

					 intervaldata  = setInterval(autorefreshwithdirectionmethod, 1000);
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
				 currcoords = currcoords + 1;
				 nextcoords = nextcoords + 1;
				 calcroute(currcoordfixlat, currcoordfixlng, nextcoordsfixlat, nextcoordsfixlng);
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
				 				geodesic : false
				         // icons: [{
				         //   icon: symbol,
				         //   offset: '100%',
				         //   repeat: false
				         //  }]
				       });
				       var bounds = new google.maps.LatLngBounds();
				       // console.log("polyline", polyline);

				       var legs = response.routes[0].legs;
				       console.log("legs", legs);
							 console.log("lat", legs[0].end_location);
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
							 var iconsymbol = {
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

							 console.log("markerss : ", markerss);
							 console.log("currcoords : ", currcoords);
							 DeleteMarkers(currcoords);

							 marker = new google.maps.Marker({
								position: new google.maps.LatLng(destlat, destlng), //markLAT, markLNG, nxtcoordnew,
								map: map,
								// icon: iconsymbol,
								icon: [{
								  icon: iconsymbol,
								  offset: '100%',
								  repeat: false
								 }],
								id: currcoords
								// optimized: false
							});
							 markerss.push(marker);
							 moveMarker(map, marker, destlat, destlng);
				      } else {
				        window.alert("Directions request failed due to " + status);
				      }
				    }
				  );
			 }

			 // google.maps.event.addDomListener(window, 'load', initialize);

       function moveMarker(map, marker, lat, lon) {
				 // console.log("latonmovemarker : ", lat);
				 // console.log("lngonmovemarker : ", lon);
           marker.setPosition(new google.maps.LatLng(lat, lon));
           map.panTo(new google.maps.LatLng(lat, lon));
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
