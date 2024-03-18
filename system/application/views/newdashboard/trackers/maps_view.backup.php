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
        <button type="button" class="btn btn-primary" style="margin-left: 88%; z-index: 1; margin-top: 1.5%; position: absolute;" title="Show Table" onclick="showtableperarea('<?php echo $companyid;?>')">
          <span class="fa fa-list"></span>
        </button>
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
    JSONString      = '<?php echo json_encode($vehicle); ?>';
     JSONpoolmaster = '<?php echo json_encode($poolmaster)?>';
    // console.log("JSONString : ", JSONString);
    if (datafixnya == "") {
      try {
        var datacode = JSON.parse(JSONString);
        objpoolmaster  = JSON.parse(JSONpoolmaster);
        console.log("disini objpoolmaster: ", objpoolmaster);
      } catch (e) {
        // console.log("e : ", e);
      }
    } else {
      var datacode = JSON.parse(JSONString);
      objpoolmaster  = JSON.parse(JSONpoolmaster);
    }

    obj = datacode;
    objpoolmasterfix = objpoolmaster;
    console.log("obj : ", obj);
    console.log("objpoolmasterfix : ", objpoolmasterfix);

    var bounds = new google.maps.LatLngBounds();
    var boundspool = new google.maps.LatLngBounds();
    map = new google.maps.Map(
      document.getElementById("map_canvas"), {
        center: new google.maps.LatLng(obj[0].auto_last_lat, obj[0].auto_last_long),
        zoom: 8,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        options: {
          gestureHandling: 'greedy'
        }
      });

    // Add multiple markers to map
    marker, i;
     infowindow      = new google.maps.InfoWindow();
     infowindow2     = new google.maps.InfoWindow();
     infowindowkedua = new google.maps.InfoWindow();
     infowindowgif = new google.maps.InfoWindow();

    // console.log("datafinya : ", datafixnya);


    var iconpool = {
      url: "http://transporter.lacak-mobil.com/assets/images/markergif.gif", // url JIKA GIF
      // path: 'assets/images/iconpulsemarker.gif',
      // scale: .5,
      anchor: new google.maps.Point(25,10),
      scaledSize: new google.maps.Size(15,15)
    };
    for (var x = 0; x < objpoolmasterfix.length; x++) {

      console.log("masuk looping", x);
      var positionpool = new google.maps.LatLng(objpoolmasterfix[x].poi_lat, objpoolmasterfix[x].poi_lng);
      boundspool.extend(positionpool);

      markerpool = new google.maps.Marker({
        position: positionpool,
        map: map,
        icon: iconpool,
        title: objpoolmasterfix[x].poi_name,
        id: objpoolmasterfix[x].poi_name,
        optimized: false
      });
      markerpool.setIcon(iconpool);
      markerpools.push(markerpool);
    }

    var htmlautoupdaterow = "";
    for (i = 0; i < obj.length; i++) {
      var position = new google.maps.LatLng(obj[i].auto_last_lat, obj[i].auto_last_long);
      bounds.extend(position);

      // JIKA IS_UPDATE = YES MAKA ITU MOBIL HIJAU ATAU BIRU
      // JIKA US_UPDATE = NO MAKA MOBIL ITU MERAH
      if (obj[i].is_update == "yes") {
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
        title: obj[i].vehicle_no + " Marker from map",
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
            infowindow2.close();
            infowindowkedua.close();
            infowindow.close();
            var gps_status = response.gps_status;
              if (gps_status == "A") {
                gps_status = "Good";
              }else {
                gps_status = "Lost Signal";
              }
            var string = "<div style='z-index: 1;'>" +
              obj[i].vehicle_no + " - " + obj[i].vehicle_name + "<br>" +
              "GPS Time : " + obj[i].auto_last_update + "<br>" + "Position : " + response.georeverse.display_name + "<br>" + "Coord : " + obj[i].auto_last_lat + ", " + obj[i].auto_last_long + "<br>" +
              "Engine : " + obj[i].auto_last_engine + "<br>" +
              "Speed : " + obj[i].auto_last_speed + " kph" + "<br>" +
              "Odometer : " + response.gps_odometer + "<br>" +
              "GPS Status : " + gps_status + "<br>" +
              "<a href='<?php echo base_url()?>maps/tracking/" + obj[i].vehicle_id + "' target='_blank'>Tracking</a>" +
              "</div>";

              infowindow2 = new google.maps.InfoWindow({
                content: string,
                maxWidth: 300
              });

              infowindow2.setContent(string);
              map.setCenter(new google.maps.LatLng(parseFloat(response.gps_latitude_real_fmt), parseFloat(response.gps_longitude_real_fmt)));
              infowindow2.open(map, marker);
          }, "json");
        };
      })(marker, i));

      htmlautoupdaterow += '<tr id="rowid_'+obj[i].vehicle_device+'">'+
                              '<td><a name="'+(i+1)+'"></a><div id="pointer_'+obj[i].vehicle_device+'" style="display: none; "text-align: right;">&#9654;</div></td>'+
                              '<td style="font-size:12px;">'+(i+1)+'</td>'+
                              '<td style="font-size:12px;" id="vehicle_id_'+obj[i].vehicle_device+'"><a style="color:green;" onclick="forgetcenter('+obj[i].vehicle_id+')">'+obj[i].vehicle_no + " - " + obj[i].vehicle_name+'</a></td>'+
                              '<td style="font-size:12px;" id="driver_'+obj[i].vehicle_device+'"></td>'+
                              '<td style="font-size:12px;" id="position_'+obj[i].vehicle_device+'"><span style="color:blue;">Position : '+obj[i].auto_last_position + "</span> <br> GPS Time : " + obj[i].auto_last_update + "<br>" + "Coord : " + "<a href='http://maps.google.com/maps?z=12&t=m&q=loc:"+obj[i].auto_last_lat+','+obj[i].auto_last_long+"' target='_blank'>" + obj[i].auto_last_lat + ", " + obj[i].auto_last_long + "</a>"+ "<br>" + "Engine : " + obj[i].auto_last_engine + "<br>" + "Speed : " + obj[i].auto_last_speed + ' Kph</td>'+
                              '<td style="font-size:12px;" id="customer_'+obj[i].vehicle_device+'"></td>'+
                              '<td style="font-size:12px;" id="cardno_'+obj[i].vehicle_device+'">'+obj[i].vehicle_card_no +'</td>'+
                              '<td style="font-size:12px;" id="laststatus_'+obj[i].vehicle_device+'">'+laststatus2+'</td>'+
                           '</tr>';
    }
    $("#autoupdaterow").before(htmlautoupdaterow);
    intervalstart = setInterval(simultango, 15000); /// 30 detik after autocheck done (30 detik = 30000)
  }

  function simultango() {
    var lastpointer;
    if (objectnumberfix == (obj.length - 1)) {
      objectnumberfix = 0;
      objectnumber    = 0;
      lastpointer     = 0;
      console.log("sama");
    }else {
      console.log("tak sama");
      if (objectnumber == 0) {
        objectnumber    = objectnumber + 1;
        objectnumberfix = 0;
        lastpointer     = 0;
      }else {
        objectnumberfix = objectnumber;
        lastpointer     = objectnumberfix - 1;
        objectnumber    = objectnumber + 1;
      }
    }
    console.log("get data bro : ", objectnumberfix);
    console.log("obj di simultango : ", obj[objectnumberfix].vehicle_device);
    $("[id='pointer_"+obj[(obj.length - 1)].vehicle_device+"']").hide();
    $("[id='pointer_"+obj[lastpointer].vehicle_device+"']").hide();
    // console.log("timer_list di simultango : ", '<?php echo $this->config->item('timer_list ');?>');
      jQuery.post("<?=base_url();?>map/lastinfo", {
          device: obj[objectnumberfix].vehicle_device,
          lasttime: 100
        },
        function(r) {
          // console.log("response jika obj banyak : ", r);
          console.log("response jika obj banyak : ", r.vehicle);
          console.log("response vdevice jika obj banyak : ", r.vehicle.vehicle_device);
          add_new_markers1(r.vehicle);
        }, "json");
  }

  function add_new_markers1(value) {
    //console.log("ini di add new marker : ", locations);
    console.log("ini di add new marker value: ", value);
    var statusengine = "";
    if (value.status1 == true) {
      statusengine = "ON";
    }else {
      statusengine = "OFF";
    }

    var vehicle_id_ = '<a style="color:green;" onclick="forgetcenter('+value.vehicle_id+')">'+value.vehicle_no + " - " + value.vehicle_name+'</a>';
    var position_   = '<span style="color:blue;">Position : '+value.gps.georeverse.display_name + "</span> <br> GPS Time : " + value.gps.gps_date_fmt + " " + value.gps.gps_time_fmt + "<br>" + "Coord : " + "<a href='http://maps.google.com/maps?z=12&t=m&q=loc:"+value.gps.gps_latitude_real_fmt+','+value.gps.gps_longitude_real_fmt+"' target='_blank'>" + value.gps.gps_latitude_real_fmt + ", " + value.gps.gps_longitude_real_fmt + "</a> <br>" + "Engine : " + statusengine + "<br>" + "Speed : " + value.gps.gps_speed_fmt + " Kph<br>";
    var cardno_     = value.vehicle_card_no;


      //Get driver ID CARD
  		if (value.driver_idcard){
  			var sDriver = value.driver_idcard.split('-');
			  $("[id='driver_"+value.vehicle_device+"']").html("<a style='color: black;' href=" + "javascript:driver_profile(" + sDriver[0] + ")" + ">" + sDriver[1] + "</a>");
      }
  		//end get driver ID CARD

  		//Get driver ID CARD
  		if (value.driver){
  			var sDriver = value.driver.split('-');
			  $("[id='driver_"+value.vehicle_device+"']").html("<a style='color: black;' href=" + "javascript:driver_profile(" + sDriver[0] + ")" + ">" + sDriver[1] + "</a>");
      }
  		//end get driver ID CARD
      //Get Customer Groups
      if (value.customer_groups)
      {
          jQuery("[id='customer_"+value.vehicle_device+"']").html(value.customer_groups);
      }

    $("[id='pointer_"+value.vehicle_device+"']").show();
    $("[id='vehicle_id_"+value.vehicle_device+"']").html(vehicle_id_);
    $("[id='position_"+value.vehicle_device+"']").html(position_);
    $("[id='cardno_"+value.vehicle_device+"']").html(cardno_);
    // console.log("course : ", value.gps.gps_course);
    // console.log("Status : ", value.gps.gps_status);
    // console.log("Status : ", value.gps.css_delay_index);
    var css_delay_index = value.gps.css_delay_index;
    var warna = "";
    if (css_delay_index == 0) {
      warna = "#ff0000";
      changerowcolor(css_delay_index, value.vehicle_device);
      laststatus = 'GPS Offline';
      statusnyafix = '<h5 class="text-medium full-width"><span class="label label-sm label-danger">'+laststatus+'</span></h5>';
    } else if (css_delay_index == 1) {
      warna = "#ffff00";
      changerowcolor(css_delay_index, value.vehicle_device);
      laststatus = 'GPS Online (Delay)';
      statusnyafix = '<h5 class="text-medium full-width"><span class="label label-sm label-warning">'+laststatus+'</span></h5>';
    } else {
      if (value.status1 == true && value.gps.gps_speed_fmt > 0) {
        warna = "#00b300";
        changerowcolor(css_delay_index, value.vehicle_device);
        laststatus = 'GPS Online';
        statusnyafix = '<h5 class="text-medium full-width"><span class="label label-sm label-success">'+laststatus+'</span></h5>';
      }else {
        warna = "#0000FF";
        changerowcolor(css_delay_index, value.vehicle_device);
        laststatus = 'GPS Online';
        statusnyafix = '<h5 class="text-medium full-width"><span class="label label-sm label-primary">'+laststatus+'</span></h5>';
      }
    }
    $("[id='laststatus_"+value.vehicle_device+"']").html(statusnyafix);

    // console.log("warna : ", warna);

    var icon = {
      // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
      path: car,
      scale: .5,
      // anchor: new google.maps.Point(25,10),
      // scaledSize: new google.maps.Size(30,20),
      strokeColor: 'white',
      strokeWeight: .10,
      fillOpacity: 1,
      fillColor: warna,
      offset: '5%',
    };

    DeleteMarkers(value.vehicle_device_name + "@" + value.vehicle_device_host);
    DeleteMarkerspertama(value.vehicle_device_name + "@" + value.vehicle_device_host);
    infowindow      = new google.maps.InfoWindow();
    infowindow2     = new google.maps.InfoWindow();
    infowindowkedua = new google.maps.InfoWindow();


    markernya = new google.maps.Marker({
      map: map,
      icon: icon,
      position: new google.maps.LatLng(parseFloat(value.gps.gps_latitude_real_fmt), parseFloat(value.gps.gps_longitude_real_fmt)),
      title: value.vehicle_no + " Marker from simultan",
      // + ' - ' + value.vehicle_name + value.driver + "\n" +
      //   "GPS Time : " + value.gps.gps_date_fmt + " " + value.gps.gps_time_fmt + "\n" + value.gps.georeverse.display_name + "\n" + value.gps.gps_latitude_real_fmt + ", " + value.gps.gps_longitude_real_fmt + "\n" +
      //   "Speed : " + value.gps.gps_speed + " kph",
      id: value.vehicle_device_name + "@" + value.vehicle_device_host
    });
    icon.rotation = Math.ceil(value.gps.gps_course);
    markernya.setIcon(icon);
    markerss.push(markernya);

    // infowindow = new google.maps.InfoWindow({
    //   content: value.vehicle_no,
    //   maxWidth: 160
    // });

    // infowindow.open(map, markernya);

    google.maps.event.addListener(markernya, 'click', function(evt){
      infowindow2.close();
      infowindowkedua.close();
      infowindow.close();
        var string = value.vehicle_no + ' - ' + value.vehicle_name + "<br>" +
          "GPS Time : " + value.gps.gps_date_fmt + " " + value.gps.gps_time_fmt + "<br>" + value.gps.georeverse.display_name + "<br>" + value.gps.gps_latitude_real_fmt + ", " + value.gps.gps_longitude_real_fmt + "<br>" +
          "Engine : " + statusengine + "<br>" + "Speed : " + value.gps.gps_speed + " kph" + "<br>" +
          "<a href='<?php echo base_url()?>maps/tracking/" + value.vehicle_id + "' target='_blank'>Tracking</a>";

         infowindow2 = new google.maps.InfoWindow({
          content: string,
          maxWidth: 300
        });

        console.log("lat when clicked : ", evt.latLng.lat().toFixed(3));
        console.log("lng when clicked : ", evt.latLng.lng().toFixed(3));
        console.log("position when clicked : ", markernya.position);

        var center = {lat : parseFloat(value.gps.gps_latitude_real_fmt), lng: parseFloat(value.gps.gps_longitude_real_fmt)};
        console.log("center : ", center);

        infowindow2.setContent(string);
        map.setCenter(markernya.position);
        markernya.setPosition(markernya.position);
        infowindow2.open(map, this);
    });
  }

  function changerowcolor(value, device) {
    var element = document.getElementById("rowid_"+device);
    if (value == 0) {
      element.classList.add("red");
    }else if (value == 1) {
      element.classList.add("yellow");
    }else {
      element.classList.add("white");
    }
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

  function showtableperarea(companyid) {
    console.log("Company id : ", companyid);
    // window.open('<?php echo base_url()?>maps/tableview/' + companyid);
    $("#modallistvehicle").show();
    $("#showtable").show();
  }

  function closemodallistofvehicle(){
    $("#modallistvehicle").hide();
  }

  function closemodalfivereport(){
    $("#modalfivereport").hide();
  }

// KLIK DARI TABLE
  function forgetcenter(deviceid){
    console.log("device id forgetcenter 1 : ", deviceid);
    var data = {device_id : deviceid};

    var data = {device_id : deviceid};

    if (infowindowkedua) {
        infowindowkedua.close();
    }

    if (infowindow) {
        infowindow.close();
    }

    if (infowindow2) {
        infowindow2.close();
    }

    $.post("<?php echo base_url() ?>maps/getdetailbydevid_0", data, function(response){
      console.log(response);
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
        title: response[0].vehicle_no + " Marker from table",
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

      google.maps.event.addListener(markernya, 'click', function(evt){
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

        console.log("lat when clicked : ", evt.latLng.lat().toFixed(3));
        console.log("lng when clicked : ", evt.latLng.lng().toFixed(3));
        console.log("position when clicked : ", markernya.position);

          var center = {lat : parseFloat(response[0].auto_last_lat), lng: parseFloat(response[0].auto_last_long)};
          console.log("center : ", center);

          infowindowkedua.setContent(string);
          map.setCenter(markernya.position);
          markernya.setPosition(markernya.position);
          infowindowkedua.open(map, this);
      });

    }, "json");
  }

// KLIK DARI SIDEBAR
  function forgetcenter2(deviceid){
    console.log("device id forgetcenter 2 : ", deviceid);
    var data = {device_id : deviceid};

    if (infowindowkedua) {
        infowindowkedua.close();
    }

    if (infowindow) {
        infowindow.close();
    }

    if (infowindow2) {
        infowindow2.close();
    }

    $.post("<?php echo base_url() ?>maps/getdetailbydevid", data, function(response){
      console.log(response);
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
        title: response[0].vehicle_no + " Marker from sidebar",
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

      google.maps.event.addListener(markernya, 'click', function(evt){
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

        console.log("lat when clicked : ", evt.latLng.lat().toFixed(3));
        console.log("lng when clicked : ", evt.latLng.lng().toFixed(3));
        console.log("position when clicked : ", markernya.position);

          var center = {lat : parseFloat(response[0].auto_last_lat), lng: parseFloat(response[0].auto_last_long)};
          console.log("center : ", center);

          infowindowkedua.setContent(string);
          map.setCenter(markernya.position);
          markernya.setPosition(markernya.position);
          infowindowkedua.open(map, this);
      });

    }, "json");
  }

  dragElement(document.getElementById("modallistvehicle"));

function dragElement(elmnt) {
  var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
  if (document.getElementById(elmnt.id + "header")) {
    // if present, the header is where you move the DIV from:
    document.getElementById(elmnt.id + "header").onmousedown = dragMouseDown;
  } else {
    // otherwise, move the DIV from anywhere inside the DIV:
    elmnt.onmousedown = dragMouseDown;
  }

  function dragMouseDown(e) {
    e = e || window.event;
    e.preventDefault();
    // get the mouse cursor position at startup:
    pos3 = e.clientX;
    pos4 = e.clientY;
    document.onmouseup = closeDragElement;
    // call a function whenever the cursor moves:
    document.onmousemove = elementDrag;
  }

  function elementDrag(e) {
    e = e || window.event;
    e.preventDefault();
    // calculate the new cursor position:
    pos1 = pos3 - e.clientX;
    pos2 = pos4 - e.clientY;
    pos3 = e.clientX;
    pos4 = e.clientY;
    // set the element's new position:
    elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
    elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
  }

  function closeDragElement() {
    // stop moving when mouse button is released:
    document.onmouseup = null;
    document.onmousemove = null;
  }
}

function forsearch(){
  console.log("yg dicari : ", $("#searchnopol").val());
}

function showmodalfivereport(){
  $.post("<?php echo base_url()?>maps/getallvehicle", {}, function(response){
    // console.log("response getallvehicle : ", response);
    var JSONString = JSON.parse(response);
    // console.log("response getallvehicle 2: ", JSONString);
    var htmlvehiclelistfivereport = "";
    var data          = JSONString.data;
    // console.log("vdevicearray : ", vdevicearray);
    for (var i = 0; i < data.length; i++) {
      var vdevice      = data[i].vehicle_device;
      var vdevicearray = vdevice.split("@");
      htmlvehiclelistfivereport += '<tr id="rowid_'+data[i].vehicle_device+'">'+
                              '<td><a name="'+(i+1)+'"></a>'+
                              '<td style="font-size:12px;">'+(i+1)+'</td>'+
                              '<td style="font-size:12px;">'+data[i].vehicle_no + " - " + data[i].vehicle_name+'</td>'+
                              '<td style="font-size:12px;">'+
                              '<a title="History" href="<?php echo base_url()?>trackers/history/'+vdevicearray[0]+"/"+vdevicearray[1]+'" target="_blank" class="btn btn-primary btn-sm"><span class="fa fa-car"></span></a>' + '<a title="Workhour" href="<?php echo base_url()?>trackers/workhour/'+vdevicearray[0]+"/"+vdevicearray[1]+'" target="_blank" class="btn btn-success btn-sm"><span class="fa fa-clock-o"></span></a>' + '<a title="Overspeed" href="<?php echo base_url()?>trackers/overspeed/'+vdevicearray[0]+"/"+vdevicearray[1]+'" target="_blank" class="btn btn-info btn-sm"><span class="fa fa-dashboard"></span></a>'+ '<a title="Geofence" href="<?php echo base_url()?>trackers/geofence/'+vdevicearray[0]+"/"+vdevicearray[1]+'" target="_blank" class="btn btn-warning btn-sm"><span class="fa fa-globe"></span></a>' + '<a title="Parking Time" href="<?php echo base_url()?>trackers/parkingtime/'+vdevicearray[0]+"/"+vdevicearray[1]+'" target="_blank" class="btn btn-danger btn-sm"><span class="fa fa-stop"></span></a>' +
                              '</td>'+
                           '</tr>';
    }
    $("#vehiclelistfivereport").before(htmlvehiclelistfivereport);
    $("#modalfivereport").show();
  });
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
