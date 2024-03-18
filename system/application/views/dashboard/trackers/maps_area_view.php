  <?php
   $user_id        = $this->sess->user_id;
   $user_company   = $this->sess->user_company;
   $user_group     = $this->sess->user_group;
   $finaldata      = array();

   //echo $user_id." - ".$user_company." - ".$user_group;
   $finaldata_json = json_encode($finaldata);
   $sizetotal      = sizeof($vehicletotal);
   $sizetotal2     = sizeof($vehicle);
   // echo "<pre>";
   // var_dump($finaldata);die();
   // echo "<pre>";
   ?>
   
<script type="text/javascript" src="js/script.js"></script>
<script src="<?php echo base_url()?>assets/dashboard/assets/js/jquery-1.7.1.min.js" type="text/javascript"></script>



    <script>
      var map;
      var datafixnya = "";
      var indeksglobal = 0;
      var infoWindow, i;
      var marker = [];
      var markernya = [];
      var markers = [];
      var markerss = [];
      var limitmobilnya;
  	  var laststatus = "-";
      var intervalstart;
      var objectnumberfix;
      var objectnumber = 0;

    var car = "M17.402,0H5.643C2.526,0,0,3.467,0,6.584v34.804c0,3.116,2.526,5.644,5.643,5.644h11.759c3.116,0,5.644-2.527,5.644-5.644 V6.584C23.044,3.467,20.518,0,17.402,0z M22.057,14.188v11.665l-2.729,0.351v-4.806L22.057,14.188z M20.625,10.773 c-1.016,3.9-2.219,8.51-2.219,8.51H4.638l-2.222-8.51C2.417,10.773,11.3,7.755,20.625,10.773z M3.748,21.713v4.492l-2.73-0.349 V14.502L3.748,21.713z M1.018,37.938V27.579l2.73,0.343v8.196L1.018,37.938z M2.575,40.882l2.218-3.336h13.771l2.219,3.336H2.575z M19.328,35.805v-7.872l2.729-0.355v10.048L19.328,35.805z";

      function initialize() {
        var user_id = '<?php echo $this->sess->user_id?>';
        JSONString = '<?php echo json_encode($vehicle); ?>';
        // console.log("JSONString : ", JSONString);
        if (datafixnya == "") {
    			try {
    				var datacode = JSON.parse(JSONString);
            // console.log("disini : ", datacode);
    			  } catch (e) {
              // console.log("e : ", e);
    			  }
          }else {
            //var datacode = datafixnya;
    	        var datacode = JSON.parse(JSONString);
          }

        obj = datacode;
         // console.log("obj awal : ", obj);
        //console.log("obj : ", obj);
        // console.log("objlength : ", obj.length);

        var bounds = new google.maps.LatLngBounds();
        map = new google.maps.Map(
          document.getElementById("map_canvas"), {
            center: new google.maps.LatLng(obj[0].auto_last_lat, obj[0].auto_last_long),
            zoom: 8,
            mapTypeId: google.maps.MapTypeId.ROADMAP
          });


        // Add multiple markers to map
          marker, i;
          var infowindow = new google.maps.InfoWindow();

        // console.log("datafinya : ", datafixnya);
        for (i = 0; i < obj.length; i++) {
          var position = new google.maps.LatLng(obj[i].auto_last_lat, obj[i].auto_last_long);
          bounds.extend(position);

  		if(obj[i].auto_status == 'M'){
  			laststatus = 'GPS Offline';
        var icon = {
          // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
          path: car,
          scale: .7,
          strokeColor: 'white',
          strokeWeight: .10,
          fillOpacity: 1,
          fillColor: '#FF0000',
          offset: '5%',
        };
  		}
  		if(obj[i].auto_status == 'K'){
  			laststatus = 'GPS Online(Delay)';
        var icon = {
          // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
          path: car,
          scale: .7,
          strokeColor: 'white',
          strokeWeight: .10,
          fillOpacity: 1,
          fillColor: '#ffff00',
          offset: '5%',
        };
  		}
  		if(obj[i].auto_status == 'P'){
  			laststatus = 'GPS Online';
        var icon = {
          // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
          path: car,
          scale: .7,
          strokeColor: 'white',
          strokeWeight: .10,
          fillOpacity: 1,
          fillColor: '#006eff',
          offset: '5%',
        };
  		}

          marker = new google.maps.Marker({
            position: position,
            map: map,
            icon: icon,
            title: obj[i].auto_vehicle_no + " - " + obj[i].auto_vehicle_name + " (" + laststatus + ")" + "\n" +
  		    "GPS Time : " + obj[i].auto_last_update + "\n" + obj[i].auto_last_position + "\n" + obj[i].auto_last_lat + ", " + obj[i].auto_last_long + "\n" +
              "Speed : " + obj[i].auto_last_speed +" kph",
            id: obj[i].auto_vehicle_device
          });
          // console.log("obj : ", obj);
            icon.rotation = Math.ceil(obj[i].auto_last_course);
            marker.setIcon(icon);

           var infowindow = new google.maps.InfoWindow({
             content: obj[i].auto_vehicle_no,
             maxWidth: 160
           });
           infowindow.open(map, marker);

          markers.push(marker);

          google.maps.event.addListener(marker, 'click', function(marker, i) {
           return function() {
             clearInterval(intervalstart);
             map.setZoom(14);
             map.setCenter(i.latLng);
             console.log("marker id pertama : ", marker.id);
             var data = {devices : marker.id};
             jQuery("#loader2").show();
             jQuery.post("<?php echo base_url() ?>maps/onevehicle", data, function(response){
               jQuery("#loader2").hide();
               console.log("response saat mobil diklik nih : ", response);
               jQuery("#result").html(response.html);
             }, "json");
           }
         }(marker, i));
        }

  	  intervalstart = setInterval(simultango, 30000); /// 30 detik after autocheck done (30 detik = 30000)
      }

      function simultango() {
        objectnumberfix = objectnumber + 1;
        objectnumber = objectnumber + 1;
        if (objectnumberfix == obj.length) {
          objectnumber = 0;
        }
        console.log("get data bro : ", objectnumberfix);
            jQuery.post("<?=base_url();?>map/lastinfo", {device: obj[objectnumberfix].auto_vehicle_device, lasttime: '<?=$this->config->item('timer_list');?>'},
              function(r)
              {
                console.log("response : ", r.vehicle);
                add_new_markers(r.vehicle);
              }, "json");
      }

      function add_new_markers(value) {
        //console.log("ini di add new marker : ", locations);
          // console.log("ini di add new marker value: ", value);
          // console.log("course : ", value.gps.gps_course);
          // console.log("Status : ", value.gps.gps_status);
          // console.log("Status : ", value.gps.css_delay_index);
          var css_delay_index = value.gps.css_delay_index;
          var warna = "";
          if (css_delay_index == 0) {
            warna = "#FF0000";
          }else if (css_delay_index == 1) {
            warna = "#ffff00";
          }else {
            warna = "#006eff";
          }

            laststatus = 'GPS Online';
            var icon = {
              // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
              path: car,
              scale: .7,
              strokeColor: 'white',
              strokeWeight: .10,
              fillOpacity: 1,
              fillColor: warna,
              offset: '5%',
            };

          DeleteMarkers(value.vehicle_device_name+"@"+value.vehicle_device_host);
          DeleteMarkerspertama(value.vehicle_device_name+"@"+value.vehicle_device_host);
          var infowindow = new google.maps.InfoWindow();
          markernya = new google.maps.Marker({
            map: map,
            icon: icon,
            position: new google.maps.LatLng(parseFloat(value.gps.gps_latitude_real_fmt), parseFloat(value.gps.gps_longitude_real_fmt)),
            title: value.vehicle_no + ' - ' + value.vehicle_name + value.driver + "\n" +
             "GPS Time : " + value.gps.gps_date_fmt + " " + value.gps.gps_time_fmt + "\n" + value.gps.georeverse.display_name + "\n" + value.gps.gps_latitude_real_fmt + ", " + value.gps.gps_longitude_real_fmt + "\n" +
              "Speed : " + value.gps.gps_speed +" kph",
            id: value.vehicle_device_name+"@"+value.vehicle_device_host
          });
          icon.rotation = Math.ceil(value.gps.gps_course);
          markernya.setIcon(icon);
          markerss.push(markernya);

          var infowindow = new google.maps.InfoWindow({
            content: value.vehicle_no,
            maxWidth: 160
          });
          infowindow.open(map, markernya);


          // map.setmap(null);
          // markers.setmap(null);
          // marker.setPosition(marker);
          google.maps.event.addListener(markernya, 'click', function(markernya, i) {
           return function() {
             clearInterval(intervalstart);
             map.setZoom(14);
             map.setCenter(this.getPosition());
             console.log("marker id kedua : ", markernya.id);
             var data = {devices : markernya.id};
             jQuery("#loader2").show();
             jQuery.post("<?php echo base_url() ?>maps/onevehicle", data, function(response){
               jQuery("#loader2").hide();
               console.log("response saat mobil diklik nih : ", response);
               jQuery("#result").html(response.html);
             }, "json");
           }
         }(markernya, i));
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

    // UNTUK REALTIME PER 1 MOBIL
    function v_forrealtime(){
      var isivalue = jQuery("#selectVehicleForTrack").val();
      console.log("isivalue : ", isivalue);
      var data = {devices : isivalue};
      jQuery("#loader2").show();
      jQuery.post("<?php echo base_url() ?>maps/onevehicle", data, function(response){
        jQuery("#loader2").hide();
        // console.log(response);
        jQuery("#result").html(response.html);
      }, "json");
    }

    </script>
	
	<?php
      $key = $this->config->item("GOOGLE_MAP_API_KEY");
	  
        if(isset($key) && $key != "") { ?>
      <script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo $key;?>&callback=initialize" type="text/javascript"></script>
    <?php } else { ?>
      <script src="http://maps.google.com/maps/api/js?sensor=false"></script>
    <?php } ?>

<!-- start sidebar menu -->
 			<div class="sidebar-container">
 				<?=$sidebar;?>
            </div>
			 <!-- end sidebar menu -->

<!-- start page content -->
            <div class="page-content-wrapper">
                <div class="page-content">
                   	<div id="map_canvas" style="height: 510px; width:100%; margin-left: 0%; margin-top: 0%; position:relative; "></div>
				</div>
            </div>
            <!-- end page content -->