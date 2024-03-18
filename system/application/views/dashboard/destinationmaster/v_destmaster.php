<style>
     /* Set the size of the div element that contains the map */
    #mapview {
      height: 400px;  /* The height is 400 pixels */
      width: 100%;  /* The width is the width of the web page */
     }

     #description {
      font-family: Roboto;
      font-size: 15px;
      font-weight: 300;
    }

    #infowindow-content .title {
      font-weight: bold;
    }

    #infowindow-content {
      display: none;
    }

    #mapview #infowindow-content {
      display: inline;
    }

    .pac-card {
      margin: 10px 10px 0 0;
      border-radius: 2px 0 0 2px;
      box-sizing: border-box;
      -moz-box-sizing: border-box;
      outline: none;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
      background-color: #fff;
      font-family: Roboto;
    }

    #pac-container {
      padding-bottom: 12px;
      margin-right: 12px;
    }

    .pac-controls {
      display: inline-block;
      padding: 5px 11px;
    }

    .pac-controls label {
      font-family: Roboto;
      font-size: 13px;
      font-weight: 300;
    }

    #pac-input {
      background-color: #fff;
      font-family: Roboto;
      font-size: 15px;
      font-weight: 300;
      margin-left: 12px;
      padding: 0 11px 0 13px;
      text-overflow: ellipsis;
      width: 400px;
    }

    #pac-input:focus {
      border-color: #4d90fe;
    }

    #title {
      color: #fff;
      background-color: #4d90fe;
      font-size: 25px;
      font-weight: 500;
      padding: 6px 12px;
    }

    /* MODAL STYLE */
    div#modalDeletedest {
      margin-top: 5%;
      margin-left: 45%;
      max-height: 300px;
      max-width: 400px;
      position: absolute;
      background-color: #f1f1f1;
      text-align: left;
      border: 1px solid #d3d3d3;
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
    <br>
    <?php if ($this->session->flashdata('notif')) {?>
      <div class="alert alert-success" id="notifnya" style="display: none;"><?php echo $this->session->flashdata('notif');?></div>
    <?php }?>
    <?php
     $userid = $this->sess->user_id;
      if ($userid = 1821) {?>
        <div class="row">
          <div class="col-md-12" id="formdestreport">
          		<div class="panel">
          			<header class="panel-heading panel-heading-blue">REPORT FILTER</header>
          				<div class="panel-body" id="bar-parent10">
                    <div class="card-box">
                      <div class="card-body">
                        <form  class="block-content form" name="frmsearch" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
                          <div class="form-group row">
                              <label for="horizontalFormEmail" class="col-sm-2 control-label">Vehicle</label>
                              <div class="col-sm-10">
                                <select class="form-control select2" name="report_vehicle" id="report_vehicle">
                                  <option value="">-- All --</option>
                                  <?php foreach ($mastervehicle as $rowvehicle) {?>
                                    <option value="<?php echo $rowvehicle['vehicle_device']?>"><?php echo $rowvehicle['vehicle_no']?></option>
                                  <?php } ?>
                                </select>
                              </div>
                          </div>

                          <div class="form-group row">
                              <label for="horizontalFormEmail" class="col-sm-2 control-label">Driver</label>
                              <div class="col-sm-10">
                                <select class="form-control select2" name="report_driver" id="report_driver" style="width: 30%;">
                                  <option value="">-- All --</option>
                                  <?php for ($x=0; $x < sizeof($driver); $x++) {?>
                                    <option value="<?php echo $driver[$x]->driver_id?>"><?php echo $driver[$x]->driver_name?></option>
                                  <?php } ?>
                                </select>
                              </div>
                          </div>

                          <div class="form-group row">
                             <label for="horizontalFormEmail" class="col-sm-2 control-label">Start Date</label>
                              <div class="input-group date form_date col-md-4" data-date="" data-date-format="dd-mm-yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
                                  <input class="form-control" size="5" type="text" readonly name="report_startdate" id="report_startdate" value="<?=date('d-m-Y')?>">
                                  <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                              </div>
                              <input type="hidden" id="dtp_input2" value="" />

                              <label for="horizontalFormEmail" class="col-sm-2 control-label">End Date</label>
                               <div class="input-group date form_date col-md-4" data-date="" data-date-format="dd-mm-yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
                                   <input class="form-control" size="5" type="text" readonly name="report_enddate" id="report_enddate" value="<?=date('d-m-Y')?>">
                                   <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                               </div>
                               <input type="hidden" id="dtp_input3" value="" />
                          </div>
                          <div class="text-right">
                            <button type="submit" name="button" class="btn btn-success">Search</button>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
          </div>
        </div>
      <?php } ?>

      <div id="reportresultfix" style="display: none;">

      </div>

    <div class="row">
      <div class="col-md-12" id="formtabledestmaster">
          <div class="card-box">
            <!-- <div class="card-header">
              <button type="button" class="btn btn-success btn-xs" onclick="showadddestination()">
                <span class="fa fa-plus"></span>
              </button>
            </div> -->

            <div class="card-body">
              <table class="table" class="display" class="full-width">
                  <thead>
                      <tr>
                          <th>
                            <button type="button" class="btn btn-success btn-xs" onclick="showadddestination()" title="Add New Destination">
                            <span class="fa fa-plus"></span>
                          </button>No
                          </th>
                          <th>Vehicle No</th>
                          <th>Driver</th>
                          <th>Dest Name</th>
                          <th width="30%">Address</th>
                          <th>Dest Date</th>
                          <th>Option</th>
                      </tr>
                  </thead>
                  <tbody>
                    <?php $no = 1; foreach ($destmaster as $rowdest) {?>
                      <tr>
                        <td class="text-center"><?php echo $no ?></td>
                        <td><?php echo $rowdest['dest_vehicle_no'] ?></td>
                        <td><?php echo $rowdest['dest_driver_name'] ?></td>
                        <td><?php echo $rowdest['dest_name'] ?></td>
                        <td>
                          <?php echo $rowdest['dest_address'] ?> <br>
                          <a href="http://maps.google.com/?q=<?php echo $rowdest['dest_lat']. "," .$rowdest['dest_lng'] ;?>" target="_blank">
                            <?php echo $rowdest['dest_lat']. "," .$rowdest['dest_lng'] ;?>
                          </a>
                        </td>
                        <td><?php echo date("d-m-Y", strtotime($rowdest['dest_endshowing_date'])) ?></td>
                        <td>
                          <a type="button" class="btn btn-primary" href="<?php echo base_url();?>destinationmaster/dest_destmasteredit/<?php echo $rowdest['dest_id'];?>" title="Edit Data">
                            <span class="fa fa-edit"></span>
                          </a>

                          <button type="button" class="btn btn-danger" onclick="btnDelete('<?php echo $rowdest['dest_id'];?>')" title="Delete Data">
                            <span class="fa fa-trash"></span>
                          </button>
                        </td>
                      </tr>
                    <?php $no++; } ?>
                  </tbody>
              </table>
            </div>
          </div>
      </div>

      <div class="col-md-12" id="formadddestmaster" style="display: none;">
        <div class="card-box">
          <div class="card-header">
            Add Destination
          </div>

          <div class="card-body">
            <form class="form-horizontal" action="<?php echo base_url()?>destinationmaster/savedestmaster" method="post" enctype="multipart/form-data">
              <div class="form-group row">
                  <label for="horizontalFormEmail" class="col-sm-2 control-label">Vehicle No</label>
                  <div class="col-sm-10">
                    <select class="form-control select2" name="dest_vehicle" id="dest_vehicle" style="width: 30%;">
                      <option value="">-- Select Vehicle --</option>
                      <?php foreach ($mastervehicle as $rowvehicle) {?>
                        <option value="<?php echo $rowvehicle['vehicle_device'].'.'.$rowvehicle['vehicle_no']?>"><?php echo $rowvehicle['vehicle_no']?></option>
                      <?php } ?>
                    </select>
                  </div>
              </div>

              <div class="form-group row">
                  <label for="horizontalFormEmail" class="col-sm-2 control-label">Driver</label>
                  <div class="col-sm-10">
                    <select class="form-control select2" name="dest_driver" id="dest_driver" style="width: 30%;">
                      <option value="">-- Select Driver --</option>
                      <?php for ($x=0; $x < sizeof($driver); $x++) {?>
                        <option value="<?php echo $driver[$x]->driver_id.'.'.$driver[$x]->driver_name?>"><?php echo $driver[$x]->driver_name?></option>
                      <?php } ?>
                    </select>
                  </div>
              </div>

              <div class="form-group row">
                  <label for="horizontalFormEmail" class="col-sm-2 control-label">Destination Name</label>
                  <div class="col-sm-10">
                      <input type="text" class="form-control" id="destname" name="destname" placeholder="Destination Name">
                  </div>
              </div>

              <div class="form-group row">
                 <label for="horizontalFormEmail" class="col-sm-2 control-label">Destination Date</label>
                  <div class="input-group date form_date col-md-4" data-date="" data-date-format="dd-mm-yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
                      <input class="form-control" size="5" type="text" readonly name="dest_endshowing_date" id="dest_endshowing_date" value="<?=date('d-m-Y')?>">
                      <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                  </div>
                  <input type="hidden" id="dtp_input2" value="" />
              </div>

              <div id="mapnya">
                <div class="pac-card" id="pac-card">
                  <div>
                    <div id="title">
                      Destination Master
                    </div>
                    <div id="type-selector" class="pac-controls">
                      <input type="radio" name="type" id="changetype-all" checked="checked" hidden>
                      <!-- <label for="changetype-all" hidden>Project Location</label> -->

                      <!-- <input type="radio" name="type" id="changetype-establishment">
                      <label for="changetype-establishment">Establishments</label>

                      <input type="radio" name="type" id="changetype-address">
                      <label for="changetype-address">Addresses</label>

                      <input type="radio" name="type" id="changetype-geocode">
                      <label for="changetype-geocode">Geocodes</label> -->
                    </div>
                    <!-- <div id="strict-bounds-selector" class="pac-controls">
                      <input type="checkbox" id="use-strict-bounds" value="">
                      <label for="use-strict-bounds">Strict Bounds</label>
                    </div> -->
                  </div>
                  <div id="pac-container">
                    <input id="pac-input" type="text" placeholder="Enter a location" class="form-control">
                  </div>
                </div>
                <div id="mapview"></div>
                <div id="infowindow-content">
                  <img src="" width="16" height="16" id="place-icon">
                  <span id="place-name"  class="title"></span><br>
                  <span id="place-address"></span>
                </div><br><br>

                  <tr>
                    <td>
                      <div class="form-group row">
                          <label for="horizontalFormEmail" class="col-sm-2 control-label">Latitude</label>
                          <div class="col-sm-10">
                              <input type="text" class="form-control" id="latitude" name="latitude" readonly>
                          </div>
                      </div>
                    </td>

                    <td>
                      <div class="form-group row">
                          <label for="horizontalFormEmail" class="col-sm-2 control-label">Longitude</label>
                          <div class="col-sm-10">
                              <input type="text" class="form-control" id="longitude" name="longitude" readonly>
                          </div>
                      </div>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      <div class="form-group row">
                          <label for="horizontalFormEmail" class="col-sm-2 control-label">Address</label>
                          <div class="col-sm-10">
                            <textarea type="text" class="form-control" id="addressfix" name="addressfix" rows="8" cols="80" readonly>
                            </textarea>
                          </div>
                      </div>
                    </td>
                  </tr>
              </div>
              <div class="text-right">
                  <button type="button" class="btn btn-warning" onclick="btncancel()">Cancel</button>
                  <button type="submit" class="btn btn-success">Save</button>
              </div>
          </form>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<div id="modalDeletedest" style="display: none;">
  <div id="mydivheader"></div>
  <div class="row" >
    <div class="col-md-12">
        <div class="card card-topline-yellow">
            <div class="card-head">
                <header>Delete Destination</header>
                <div class="tools">
                    <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
                  <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                  <button type="button" class="btn btn-danger" name="button" onclick="closemodallistofvehicle();">X</button>
                </div>
            </div>
            <div class="card-body">
                <form class="form-horizontal" action="<?php echo base_url()?>destinationmaster/delete" method="post" enctype="multipart/form-data">
                  <input type="hidden" name="iddelete" id="iddelete">
                  Are you sure want to delete this data?<br><br>
                  <div class="text-right">
                    <button type="button" name="button" class="btn btn-warning" onclick="btnCloseModal();">Cancel</button>
                    <button type="submit" name="button" class="btn btn-danger">Delete</button>
                  </div>
                </form>
            </div>
        </div>
    </div>
  </div>
</div>

<script type="text/javascript" src="js/script.js"></script>
<script src="<?php echo base_url()?>assets/dashboard/assets/js/jquery-1.7.1.min.js" type="text/javascript"></script>
<script src="<?php echo base_url()?>assets/js/assetsbaru/jquery.min.js"></script>
<script src="<?php echo base_url()?>assets/js/assetsbaru/jquery-ui.min.js"></script>
<script src="<?php echo base_url()?>assets/js/assetsbaru/chosen.jquery.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url()?>assets/js/assetsbaru/chosen.min.css">

<script type="text/javascript">
$(".chosen").chosen();
$("#formadddestmaster").hide();
$("#notifnya").fadeIn(1000);
$("#notifnya").fadeOut(5000);

function frmsearch_onsubmit()
{
  searchreportdest(0);
  return false;
}

function searchreportdest(){
  jQuery.post("<?=base_url();?>destinationmaster/searchforreport", jQuery("#frmsearch").serialize(),
    function(r)
    {
      console.log("r : ", r);
      $("#reportresultfix").html(r.html);
      $("#reportresultfix").show();
      $("#formtabledestmaster").hide();
    }, "json");
}

function showadddestination(){
  $("#formadddestmaster").show();
  $("#formtabledestmaster").hide();
}

function btncancel(){
  $("#formadddestmaster").hide();
  $("#formtabledestmaster").show();
}

function btnDelete(id){
  $("#iddelete").val(id);
  $("#modalDeletedest").show();
}

function closemodallistofvehicle(){
  $("#modalDeletedest").hide();
}

function btnCloseModal(){
  $("#modalDeletedest").hide();
}

var circles = [];
var newpopulation = 2.5;
function initialize(){
  var map = new google.maps.Map(
    document.getElementById("mapview"), {
      center: new google.maps.LatLng(-6.1753924, 106.82715280000002),
      zoom: 12,
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      options: {
        gestureHandling: 'greedy'
      }
    });

        var card = document.getElementById('pac-card');
        var input = document.getElementById('pac-input');
        var types = document.getElementById('type-selector');
        var strictBounds = document.getElementById('strict-bounds-selector');
        var geocoder = new google.maps.Geocoder();



        map.controls[google.maps.ControlPosition.TOP_RIGHT].push(card);

        var autocomplete = new google.maps.places.Autocomplete(input);

        // Bind the map's bounds (viewport) property to the autocomplete object,
        // so that the autocomplete requests use the current map bounds for the
        // bounds option in the request.
        autocomplete.bindTo('bounds', map);

        // Set the data fields to return when the user selects a place.
        autocomplete.setFields(
            ['address_components', 'geometry', 'icon', 'name']);

        var infowindow = new google.maps.InfoWindow();
        var infowindowContent = document.getElementById('infowindow-content');
        infowindow.setContent(infowindowContent);

        var marker = new google.maps.Marker({
          draggable : true,
          map: map,
          anchorPoint: new google.maps.Point(0, -29)
        });

        // Add circle overlay and bind to marker
    //     var circle = new google.maps.Circle({
    //       strokeColor: '#FF0000',
    //       strokeOpacity: 0.8,
    //       strokeWeight: 2,
    //       fillColor: '#FF0000',
    //       fillOpacity: 0.35,
    //       map: map,
    //       radius: Math.sqrt(newpopulation) * 100,
    //       editable: true
    //       // radius: 1000    // 10 miles in metres
    //       // fillColor: '#AA0000'
    //     });
    //     circle.bindTo('center', marker, 'position');
    //     circles.push(circle);
    //     console.log("circles : ", circles);
    //     console.log("circles : ", circle.getRadius());
    //
    //     // add resize behaviour to the circle
    // circle.addListener('radius_changed', function(e){
    //      storeCircleRadius(circle);
    //  });

            google.maps.event.addListener(marker, 'dragend', function() {
                geocoder.geocode({'latLng': marker.getPosition()}, function(results, status) {
                  if (status == google.maps.GeocoderStatus.OK) {
                    if (results[0]) {
                      document.getElementById("addressfix").value = results[0].formatted_address;
                      var lat = marker.getPosition().lat();
                      var lng = marker.getPosition().lng();
                      document.getElementById("latitude").value = lat.toString().slice(0, 10);
                      document.getElementById("longitude").value = lng.toString().slice(0, 10);
                      // document.getElementById("latitude").value = marker.getPosition().lat();
                      // document.getElementById("longitude").value = marker.getPosition().lng();
                  }
                }
              });
            });

        autocomplete.addListener('place_changed', function() {
          infowindow.close();
          marker.setVisible(false);
          var place = autocomplete.getPlace();
          if (!place.geometry) {
            // User entered the name of a Place that was not suggested and
            // pressed the Enter key, or the Place Details request failed.
            window.alert("No details available for input: '" + place.name + "'");
            return;
          }

          // If the place has a geometry, then present it on a map.
          if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
          } else {
            map.setCenter(place.geometry.location);
            map.setZoom(17);  // Why 17? Because it looks good.
          }
          marker.setPosition(place.geometry.location);
          marker.setVisible(true);
            var lat = marker.getPosition().lat();
            var lng = marker.getPosition().lng();
          document.getElementById("latitude").value = lat.toString().slice(0, 10);
          document.getElementById("longitude").value = lng.toString().slice(0, 10);

          var address = '';
          if (place.address_components) {
            address = [
              (place.address_components[0] && place.address_components[0].short_name || ''),
              (place.address_components[1] && place.address_components[1].short_name || ''),
              (place.address_components[2] && place.address_components[2].short_name || '')
            ].join(' ');
          }

          infowindowContent.children['place-icon'].src = place.icon;
          infowindowContent.children['place-name'].textContent = place.name;
          infowindowContent.children['place-address'].textContent = address;
          jQuery("#addressfix").val(address);
          // jQuery("#allcordinates").val(circle.getRadius());
          // jQuery("#allcordinatesforview").val(Math.round(circle.getRadius()));
          infowindow.open(map, marker);
        });

        function postData(url,data,callback){
          jQuery.ajax({
            type:"POST",
            contentType: "application/json",
            dataType: 'json',
            url:url,
            data: JSON.stringify(data),
            success: function(response){
                return callback(null,response);
            },
            error:function(err){
               return callback(true,err);
            }
          });
        }

        // Sets a listener on a radio button to change the filter type on Places
        // Autocomplete.
        function setupClickListener(id, types) {
          var radioButton = document.getElementById('changetype-all');
          radioButton.addEventListener('click', function() {
            autocomplete.setTypes('changetype-all');
          });
        }

        setupClickListener('changetype-all', []);
        // setupClickListener('changetype-address', ['address']);
        // setupClickListener('changetype-establishment', ['establishment']);
        // setupClickListener('changetype-geocode', ['geocode']);

        // document.getElementById('use-strict-bounds')
        //     .addEventListener('click', function() {
        //       console.log('Checkbox clicked! New state=' + this.checked);
        //       autocomplete.setOptions({strictBounds: this.checked});
        //     });
}

// function storeCircleRadius(circle) {
//    // set input to store value
//    jQuery("#allcordinates").val(circle.getRadius());
//    jQuery("#allcordinatesforview").val(Math.round(circle.getRadius()));
//    // trigger OnChange event
//    jQuery("#allcordinates").trigger('change');
//    jQuery("#allcordinatesforview").trigger('change');
// }

// FOR DISABLE SUBMIT FORM
jQuery(window).keydown(function(event){
  if(event.keyCode == 13) {
    event.preventDefault();
    return false;
  }
});


</script>


<?php
	$key = $this->config->item("GOOGLE_MAP_API_KEY");
	if(isset($key) && $key != "") { ?>
    <!-- <?php echo $key; ?> -->
		<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo $key."&libraries=places,drawing&callback=initialize"?>" type="text/javascript"></script>
	<?php } else { ?>
		<script src="http://maps.google.com/maps/api/js?V=3.3&amp;sensor=false"></script>
	<?php } ?>
