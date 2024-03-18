<?php
	$key = $this->config->item("GOOGLE_MAP_API_KEY");

  	// echo "<pre>";
    // var_dump($data);die();
    // echo "<pre>";
	if(isset($key) && $key != "") { ?>
		<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $key."&callback=initialize";?>" type="text/javascript"></script>
	<?php } else { ?>
		<script src="http://maps.google.com/maps/api/js?V=3.3&amp;sensor=false"></script>
	<?php } ?>

<script type="text/javascript">
var map;
var coords, coordsawal;
var coordsreverese;
var perulanganmaksimal = '<?php echo sizeof($data);?>';
var JSONString = '<?php echo json_encode($data) ?>';
var obj = JSON.parse(JSONString);
//	var obj = objectnya.reverse();
var marker = [];
var markers = [];
var car = "M17.402,0H5.643C2.526,0,0,3.467,0,6.584v34.804c0,3.116,2.526,5.644,5.643,5.644h11.759c3.116,0,5.644-2.527,5.644-5.644 V6.584C23.044,3.467,20.518,0,17.402,0z M22.057,14.188v11.665l-2.729,0.351v-4.806L22.057,14.188z M20.625,10.773 c-1.016,3.9-2.219,8.51-2.219,8.51H4.638l-2.222-8.51C2.417,10.773,11.3,7.755,20.625,10.773z M3.748,21.713v4.492l-2.73-0.349 V14.502L3.748,21.713z M1.018,37.938V27.579l2.73,0.343v8.196L1.018,37.938z M2.575,40.882l2.218-3.336h13.771l2.219,3.336H2.575z M19.328,35.805v-7.872l2.729-0.355v10.048L19.328,35.805z";
var icon = {
		path: car,
		scale: .7,
		strokeColor: 'white',
		strokeWeight: .10,
		fillOpacity: 1,
		fillColor: '#006eff',
		offset: '5%',
		rotation : 180
};

//console.log("object : ", object);
console.log("obj nya : ", obj);

coordsawal = [];
				for (var x = 0; x < obj.length; x++) {
						coordsawal.push({
							lat: parseFloat(obj[x].gps_latitude_real_fmt),
							lng: parseFloat(obj[x].gps_longitude_real_fmt),
							coursenya: obj[x].gps_course
						});
						// console.log("x : ", x);
				}
coords = coordsawal.reverse();
	console.log("coords : ", coords);

	function initialize() {
				 var markLAT = parseFloat(coords[0].lat);
				 var markLNG = parseFloat(coords[0].lng);
		//console.log("markLAT : ", parseFloat(markLAT));
		 //console.log("markLNG : ", parseFloat(markLNG));

		 var bounds = new google.maps.LatLngBounds();
		map = new google.maps.Map(document.getElementById("map"), {
					 center: new google.maps.LatLng(markLAT, markLNG),
					 zoom: 16,
					 mapTypeId: google.maps.MapTypeId.ROADMAP
				 });
	 autoRefresh();
		 }

		function moveMarker(map, marker, lat, lon) {
				 marker.setPosition(new google.maps.LatLng(lat, lon));
				 map.panTo(new google.maps.LatLng(lat, lon));
		 }

		 function autoRefresh() {
				 var i, route, marker;

				 route = new google.maps.Polyline({
						 path: [],
						 geodesic : false,
						 strokeColor: '#FF0000',
						 strokeOpacity: 1.0,
						 strokeWeight: 2,
						 editable: false,
						 map:map
				 });

				 marker = new google.maps.Marker({
				 map:map,
			 	 icon: icon
			 });

				 for (i = 0; i < coords.length; i++) {
						 setTimeout(function (coords)
						 {
								 icon.rotation = Math.ceil(coords.coursenya);
								 marker.setIcon(icon);
								 route.getPath().push(new google.maps.LatLng(parseFloat(coords.lat), parseFloat(coords.lng)));
								 moveMarker(map, marker, parseFloat(coords.lat),  parseFloat(coords.lng));
						 }, 1000 * i, coords[i]);
				 }
		 }
</script>
<style media="screen">
#map {
	height: 400px;
	width: 99%;
	margin-left: 2%;
	margin-top: -1%;
}
</style>
<div id="map"></div>
