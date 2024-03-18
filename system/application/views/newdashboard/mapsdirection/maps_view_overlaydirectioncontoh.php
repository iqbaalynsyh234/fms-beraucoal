<style media="screen">
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

  div#realtimealertshow {
    position: fixed;
    width: 65%;
    background-color: white;
    min-height: 110px;
    max-height: 140px;
    overflow-y: auto;
    overflow-x: hidden;
    bottom: 0px;
    left: 17.5%;
    right: 0px;
    margin-bottom: 0px;
    margin-left: 5%
    /* margin-top: 30.5%;
  margin-left: -1%;
  position: absolute;
  z-index: 9999;
  background-color: #f1f1f1;
  text-align: left;
  border: 1px solid #d3d3d3;
  width: 74%;
  height: 10px; */
  }

  div#modalalertsummry {
    width: 82%;
    margin-top: -33%;
    margin-left: -5%;
    overflow-x: auto;
    position: absolute;
    z-index: 9;
    background-color: #f1f1f1;
    text-align: left;
    border: 1px solid #d3d3d3;
    max-height: 300px;
  }

  div#modaladdcomment {
    margin-top: 8%;
    margin-left: 20%;
    overflow-x: auto;
    position: absolute;
    z-index: 9;
    background-color: #f1f1f1;
    text-align: left;
    border: 1px solid #d3d3d3;
    width: 56%;
  }

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

<div class="sidebar-container">
  <?=$sidebar;?>
</div>

<div class="page-content-wrapper">
  <div class="page-content">
    <div class="col-sm-12 col-md-4 col-lg-3">
      <button class="btn btn-info" id="notifdevicestatus" style="display:none;"></button>
    </div>
    <div class="row">
      <div class="col-sm-12">
        <!-- <h4>Realtime Monitoring</h4> -->
        <div class="card-box">
          <div class="card-body" style="height: 95%;" id="cardbodyformode">
            <button type="button" name="button" id="btnvideomode" class="btn btn-primary" onclick="showvideomode();" style="margin-left: 82%; z-index: 1; margin-top: 1.7%; position: absolute;">
              <span class="fa fa-video-camera"></span>
            </button>
            <button type="button" name="button" id="btnmapmode" class="btn btn-success" onclick="showmapmode();" style="display: none; margin-left: 85%; z-index: 1; margin-top: 1.7%; position: absolute;">
              <span class="fa fa-map-marker"></span>
            </button>
            <br>

            <div id="tools">
              start:
              <input type="text" name="start" id="start" value="union square, NY" />end:
              <input type="text" name="end" id="end" value="times square, NY" />
              <input type="submit" onclick="calcRoute();" />
            </div>

            <div id="map_canvas" style="width:100%;height:300px;"></div>

            <div class="row">
              <div id="videomode" style="display:none; width: 70%; height: 400%;">
                <div>
                  <div id="streamcontent">
                    <br> Please Select Vehicle First
                  </div>
                </div>
              </div>

              <div id="mapmode" style="width: 98%; height: 460%; margin-left: 1%; margin-top: -3%;">
                <div>
                  <button id="btnmaptable" type="button" class="btn btn-info btnmaptable" title="Show Table" onclick="showtableperarea('<?php echo $companyid;?>')" style="margin-left: 88%; z-index: 1; margin-top: 1.3%; position: absolute;">
                    <span class="fa fa-list"></span>
                  </button>
                  <div id="mapsnya" style="width: 103%; height: 460px; margin-top: 1%; margin-left: -1.5%;"></div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>

    <!-- REALTIME ALERT START -->
    <div id="realtimealertshow">
      <!--style="display:none;"-->
      <!-- <div class="row" id="modalalertrealtime">
        <div class="col-md-12">
          <div class="card">
            <div class="card-body"> -->
      <div style="margin-left: 1%; margin-top: 1%">
        <a href="#" onclick="realtimealerttoggle();">
          <b>Realtime Alert OVERLAY N DIRECTION MAP</b>
        </a>
        <button type="button" name="button" class="btn btn-success btn-sm" title="Realtime Alert Summary" onclick="alertsummary();">
          <span class="fa fa-list"></span>
        </button>

        <button type="button" name="button" id="activatesound" class="btn btn-flat btn-sm" title="Sound" onclick="activatesound();">
          <span class="fa fa-volume-up"></span>
        </button>

        <button type="button" name="button" id="activatesound2" class="btn btn-flat btn-sm" title="Sound" onclick="activatesound2();" style="display:none;">
          <span class="fa fa-volume-off"></span>
        </button>
      </div>

      <div id="realtimealertcontent"></div>
      <div id="alertfromdevicealert" style="margin-left: 1%;"></div>
      <!-- </div>
          </div>
        </div>
      </div> -->
    </div>

    <div class="card" id="modalalertsummry" style="display: none;">
      <div class="card card-topline-yellow">
        <div class="card-body">
          <h4>
              <b>Realtime Summary Alert</b>
            </h4>
          <div class="text-right" style="margin-top: -5%;">
            <button type="button" name="button" class="btn btn-sm btn-danger" onclick="closemodalsummaryalert();">X</button>
          </div>
          <div id="summaryalertcontent" style="margin-top: 1%;"></div>
          <div id="summaryalertfromdevicealert"></div>
        </div>
      </div>
    </div>
    <!-- REALTIME ALERT END -->

  </div>
</div>

<!-- MODAL LIST VEHICLE -->
<div id="modallistvehicle" style="display: none;">
  <div id="mydivheader"></div>
  <div class="row">
    <div class="col-md-12">
      <div class="card card-topline-yellow">
        <div class="card-head">
          <h4>List of Vehicle</h4>
          <div class="tools" style="margin-top: -40px;">
            <!-- <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a> -->
            <!-- <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a> -->
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
                <!-- <th>Transporter</th> -->
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

<div id="modaladdcomment" style="display: none;">
  <div id="changepassreport"></div>
  <div class="row">
    <div class="col-md-12">
      <div class="card card-topline-yellow">
        <div class="card-head">
          <header>Add Comment</header>
          <div class="tools">
            <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
            <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
            <button type="button" class="btn btn-danger" name="button" onclick="closemodaladdcomment();">X</button>
          </div>
        </div>
        <div class="card-body">
          <div id="addcommentcontent"></div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- end page content -->

<script src="https://maps.googleapis.com/maps/api/js?libraries=geometry&key=AIzaSyB-FN7p9A3UXtdGNMBfmV5xbn7RC35V5Fc"></script>
<script type="text/javascript">
  var map;
  var directionDisplay;
  var directionsService;
  var stepDisplay;
  var markerArray = [];
  var position;
  var marker = null;
  var polyline = null;
  var poly2 = null;
  var speed = 0.000005,
    wait = 1;
  var infowindow = null;

  var myPano;
  var panoClient;
  var nextPanoId;
  var timerHandle = null;

  function createMarker(latlng, label, html) {
    // alert("createMarker("+latlng+","+label+","+html+","+color+")");
    var contentString = '<b>' + label + '</b><br>' + html;
    var marker = new google.maps.Marker({
      position: latlng,
      map: map,
      title: label,
      zIndex: Math.round(latlng.lat() * -100000) << 5
    });
    marker.myname = label;
    // gmarkers.push(marker);

    google.maps.event.addListener(marker, 'click', function() {
      infowindow.setContent(contentString);
      infowindow.open(map, marker);
    });
    return marker;
  }


  function initialize() {
    infowindow = new google.maps.InfoWindow({
      size: new google.maps.Size(150, 50)
    });
    // Instantiate a directions service.
    directionsService = new google.maps.DirectionsService();

    // Create a map and center it on Manhattan.
    var myOptions = {
      zoom: 13,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

    address = 'lampung'
    geocoder = new google.maps.Geocoder();
    geocoder.geocode({
      'address': address
    }, function(results, status) {
      map.setCenter(results[0].geometry.location);
    });

    // Create a renderer for directions and bind it to the map.
    var rendererOptions = {
      map: map
    }
    directionsDisplay = new google.maps.DirectionsRenderer(rendererOptions);

    // Instantiate an info window to hold step text.
    stepDisplay = new google.maps.InfoWindow();

    polyline = new google.maps.Polyline({
      path: [],
      strokeColor: '#FF0000',
      strokeWeight: 3
    });
    poly2 = new google.maps.Polyline({
      path: [],
      strokeColor: '#FF0000',
      strokeWeight: 3
    });
  }



  var steps = []

  function calcRoute() {
    if (timerHandle) {
      clearTimeout(timerHandle);
    }
    if (marker) {
      marker.setMap(null);
    }
    polyline.setMap(null);
    poly2.setMap(null);
    directionsDisplay.setMap(null);
    polyline = new google.maps.Polyline({
      path: [],
      strokeColor: '#FF0000',
      strokeWeight: 3
    });
    poly2 = new google.maps.Polyline({
      path: [],
      strokeColor: '#FF0000',
      strokeWeight: 3
    });
    // Create a renderer for directions and bind it to the map.
    var rendererOptions = {
      map: map
    }
    directionsDisplay = new google.maps.DirectionsRenderer(rendererOptions);

    var start      = document.getElementById("start").value;
    var end        = document.getElementById("end").value;
    var travelMode = google.maps.DirectionsTravelMode.DRIVING

    var request = {
      origin: start,
      destination: end,
      travelMode: travelMode
    };

    // Route the directions and pass the response to a
    // function to create markers for each step.
    directionsService.route(request, function(response, status) {
      if (status == google.maps.DirectionsStatus.OK) {
        directionsDisplay.setDirections(response);

        var bounds = new google.maps.LatLngBounds();
        var route = response.routes[0];
        startLocation = new Object();
        endLocation = new Object();

        // For each route, display summary information.
        var path = response.routes[0].overview_path;
        var legs = response.routes[0].legs;
        for (i = 0; i < legs.length; i++) {
          if (i == 0) {
            startLocation.latlng = legs[i].start_location;
            startLocation.address = legs[i].start_address;
            // marker = google.maps.Marker({map:map,position: startLocation.latlng});
            marker = createMarker(legs[i].start_location, "start", legs[i].start_address, "green");
          }
          endLocation.latlng = legs[i].end_location;
          endLocation.address = legs[i].end_address;
          var steps = legs[i].steps;
          for (j = 0; j < steps.length; j++) {
            var nextSegment = steps[j].path;
            for (k = 0; k < nextSegment.length; k++) {
              polyline.getPath().push(nextSegment[k]);
              bounds.extend(nextSegment[k]);
            }
          }
        }

        polyline.setMap(map);
        map.fitBounds(bounds);
        //        createMarker(endLocation.latlng,"end",endLocation.address,"red");
        map.setZoom(18);
        startAnimation();
      }
    });
  }



  var step = 50; // 5; // metres
  var tick = 100; // milliseconds
  var eol;
  var k = 0;
  var stepnum = 0;
  var speed = "";
  var lastVertex = 1;


  //=============== animation functions ======================
  function updatePoly(d) {
    // Spawn a new polyline every 20 vertices, because updating a 100-vertex poly is too slow
    if (poly2.getPath().getLength() > 20) {
      poly2 = new google.maps.Polyline([polyline.getPath().getAt(lastVertex - 1)]);
      // map.addOverlay(poly2)
    }

    if (polyline.GetIndexAtDistance(d) < lastVertex + 2) {
      if (poly2.getPath().getLength() > 1) {
        poly2.getPath().removeAt(poly2.getPath().getLength() - 1)
      }
      poly2.getPath().insertAt(poly2.getPath().getLength(), polyline.GetPointAtDistance(d));
    } else {
      poly2.getPath().insertAt(poly2.getPath().getLength(), endLocation.latlng);
    }
  }


  function animate(d) {
    // alert("animate("+d+")");
    if (d > eol) {
      map.panTo(endLocation.latlng);
      marker.setPosition(endLocation.latlng);
      return;
    }
    var p = polyline.GetPointAtDistance(d);
    map.panTo(p);
    marker.setPosition(p);
    updatePoly(d);
    timerHandle = setTimeout("animate(" + (d + step) + ")", tick);
  }


  function startAnimation() {
    eol = google.maps.geometry.spherical.computeLength(polyline.getPath());
    map.setCenter(polyline.getPath().getAt(0));
    // map.addOverlay(new google.maps.Marker(polyline.getAt(0),G_START_ICON));
    // map.addOverlay(new GMarker(polyline.getVertex(polyline.getVertexCount()-1),G_END_ICON));
    // marker = new google.maps.Marker({location:polyline.getPath().getAt(0)} /* ,{icon:car} */);
    // map.addOverlay(marker);
    poly2 = new google.maps.Polyline({
      path: [polyline.getPath().getAt(0)],
      strokeColor: "#0000FF",
      strokeWeight: 10
    });
    // map.addOverlay(poly2);
    setTimeout("animate(50)", 2000); // Allow time for the initial map display
  }


  //=============== ~animation funcitons =====================


  google.maps.event.addDomListener(window, "load", initialize);
  /*********************************************************************\
  *                                                                     *
  * epolys.js                                          by Mike Williams *
  * updated to API v3                                  by Larry Ross    *
  *                                                                     *
  * A Google Maps API Extension                                         *
  *                                                                     *
  * Adds various Methods to google.maps.Polygon and google.maps.Polyline *
  *                                                                     *
  * .Contains(latlng) returns true is the poly contains the specified   *
  *                   GLatLng                                           *
  *                                                                     *
  * .Area()           returns the approximate area of a poly that is    *
  *                   not self-intersecting                             *
  *                                                                     *
  * .Distance()       returns the length of the poly path               *
  *                                                                     *
  * .Bounds()         returns a GLatLngBounds that bounds the poly      *
  *                                                                     *
  * .GetPointAtDistance() returns a GLatLng at the specified distance   *
  *                   along the path.                                   *
  *                   The distance is specified in metres               *
  *                   Reurns null if the path is shorter than that      *
  *                                                                     *
  * .GetPointsAtDistance() returns an array of GLatLngs at the          *
  *                   specified interval along the path.                *
  *                   The distance is specified in metres               *
  *                                                                     *
  * .GetIndexAtDistance() returns the vertex number at the specified    *
  *                   distance along the path.                          *
  *                   The distance is specified in metres               *
  *                   Returns null if the path is shorter than that      *
  *                                                                     *
  * .Bearing(v1?,v2?) returns the bearing between two vertices          *
  *                   if v1 is null, returns bearing from first to last *
  *                   if v2 is null, returns bearing from v1 to next    *
  *                                                                     *
  *                                                                     *
  ***********************************************************************
  *                                                                     *
  *   This Javascript is provided by Mike Williams                      *
  *   Blackpool Community Church Javascript Team                        *
  *   http://www.blackpoolchurch.org/                                   *
  *   http://econym.org.uk/gmap/                                        *
  *                                                                     *
  *   This work is licenced under a Creative Commons Licence            *
  *   http://creativecommons.org/licenses/by/2.0/uk/                    *
  *                                                                     *
  ***********************************************************************
  *                                                                     *
  * Version 1.1       6-Jun-2007                                        *
  * Version 1.2       1-Jul-2007 - fix: Bounds was omitting vertex zero *
  *                                add: Bearing                         *
  * Version 1.3       28-Nov-2008  add: GetPointsAtDistance()           *
  * Version 1.4       12-Jan-2009  fix: GetPointsAtDistance()           *
  * Version 3.0       11-Aug-2010  update to v3                         *
  *                                                                     *
  \*********************************************************************/


  google.maps.LatLng.prototype.latRadians = function() {
    return this.lat() * Math.PI / 180;
  }

  google.maps.LatLng.prototype.lngRadians = function() {
    return this.lng() * Math.PI / 180;
  }


  // === A method which returns a GLatLng of a point a given distance along the path ===
  // === Returns null if the path is shorter than the specified distance ===
  google.maps.Polyline.prototype.GetPointAtDistance = function(metres) {
    // some awkward special cases
    if (metres == 0) return this.getPath().getAt(0);
    if (metres < 0) return null;
    if (this.getPath().getLength() < 2) return null;
    var dist = 0;
    var olddist = 0;
    for (var i = 1;
      (i < this.getPath().getLength() && dist < metres); i++) {
      olddist = dist;
      dist += google.maps.geometry.spherical.computeDistanceBetween(this.getPath().getAt(i), this.getPath().getAt(i - 1));
    }
    if (dist < metres) {
      return null;
    }
    var p1 = this.getPath().getAt(i - 2);
    var p2 = this.getPath().getAt(i - 1);
    var m = (metres - olddist) / (dist - olddist);
    return new google.maps.LatLng(p1.lat() + (p2.lat() - p1.lat()) * m, p1.lng() + (p2.lng() - p1.lng()) * m);
  }

  // === A method which returns the Vertex number at a given distance along the path ===
  // === Returns null if the path is shorter than the specified distance ===
  google.maps.Polyline.prototype.GetIndexAtDistance = function(metres) {
    // some awkward special cases
    if (metres == 0) return this.getPath().getAt(0);
    if (metres < 0) return null;
    var dist = 0;
    var olddist = 0;
    for (var i = 1;
      (i < this.getPath().getLength() && dist < metres); i++) {
      olddist = dist;
      dist += google.maps.geometry.spherical.computeDistanceBetween(this.getPath().getAt(i), this.getPath().getAt(i - 1));
    }
    if (dist < metres) {
      return null;
    }
    return i;
  }

  function playsound() {
    var audio = new Audio('<?php echo base_url() ?>assets/sounds/alert1.mp3');
      audio.play();
    // var sound = document.getElementById(soundObj);
    // sound.Play();
  }
</script>
