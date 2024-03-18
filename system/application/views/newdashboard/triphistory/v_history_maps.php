<style media="screen">
  #map{
    width: 1030px;
    height: 300px;
  }
</style>

<div id="map"></div>

<script type="text/javascript">
var JSONString;
var obj;
var infowindow;
var marker, x, map;
var markers;

function initialize(){
  JSONString = '<?php echo json_encode($data) ?>';
  obj = JSON.parse(JSONString);
  // console.log("JSONString : ", JSONString);
  console.log("obj : ", obj);

     map = new google.maps.Map(
      document.getElementById("map"), {
        center: new google.maps.LatLng(parseFloat(obj[0].gps_latitude_real_fmt), parseFloat(obj[0].gps_longitude_real_fmt)),
        zoom: 12,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        options: {
          gestureHandling: 'greedy'
        }
      });

      // SHOW MARKER
       infowindow = new google.maps.InfoWindow();
       marker, x;
       markers = new Array();

       var labelindex = 1;
      for (x = 0; x < obj.length; x++) {
        marker = new google.maps.Marker({
          position: new google.maps.LatLng(parseFloat(obj[x].gps_latitude_real_fmt), parseFloat(obj[x].gps_longitude_real_fmt)),
          label: "" + labelindex,
          map: map
        });
        labelindex++;
        markers.push(marker);
        // google.maps.event.addListener(marker, 'click', (function(marker, i) {
        //   return function() {
        //     infowindow.setContent(locations[i][0]);
        //     infowindow.open(map, marker);
        //   }
        // })(marker, i));
  }
}
</script>

<?php
	$key = $this->config->item("GOOGLE_MAP_API_KEY");
	if(isset($key) && $key != "") { ?>
    <!-- <?php echo $key; ?> -->
		<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo $key."&callback=initialize"?>" type="text/javascript"></script>
	<?php } else { ?>
		<script src="http://maps.google.com/maps/api/js?V=3.3&amp;sensor=false"></script>
	<?php } ?>
