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

    #vehicle-data{
      background-color: #221f1f;
      color: white;
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
    div#modalDeleteCustomer {
      margin-top: 5%;
      margin-left: 45%;
      max-height: 300px;
      max-width: 400px;
      position: absolute;
      background-color: #f1f1f1;
      text-align: left;
      border: 1px solid #d3d3d3;
    }

    #formaddpoi{
      width: 100%;
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
    <!--<div class="alert alert-success" id="notifnya2" style="display: none;"></div>-->
    <div class="row">
      <div class="col-md-12" id="tablepoi">
        <div class="panel" id="panel_form">
          <header class="panel-heading panel-heading-red" id="vehicle-data">Vehicle Data</header>
          <div class="panel-body" id="bar-parent10">
              <table id="example1" class="table table-striped">
                <thead>
          				<tr>
          					<th>
                      <?php if ($privilegecode == 3) {?>

                      <?php }else {?>
                        <button type="button" class="btn btn-success btn-xs" onclick="showaddpoi();">
                          <span class="fa fa-plus"></span>
                        </button>
                      <?php } ?>
                      No
                    </th>
          					<th>Icon</th>
                    <th>POI Name</th>
                    <th>Address</th>
          					<!-- <th>Control</th> -->
          				</tr>
          			</thead>
                  <tbody>
                    <?php
              			for($i=0; $i < count($data); $i++){?>
                      <tr>
                        <td>
                          <?=$i+1?>
                        </td>
                        <?php if ($data[$i]->poi_cat_icon) { ?>
                          <td style="text-align: center"><img src="<?=base_url()?>assets/images/poi/<?=$data[$i]->poi_cat_icon;?>" border="0" /></td>
                          <?php } else { ?>
                            <td style="text-align: center">&nbsp;</td>
                            <?php } ?>
                              <td>
                                <?=$data[$i]->poi_name;?>
                              </td>
                              <td>
                                <a target="_blank" href="https://www.google.com/maps?z=12&t=m&q=loc:<?=$data[$i]->poi_latitude . ',' . $data[$i]->poi_longitude?>">
                                  <span style="color:black"><?=$data[$i]->location_address;?></span> <br>
                                  <?=$data[$i]->poi_latitude . ',' . $data[$i]->poi_longitude?>
                                </a>
                              </td>
                              <!-- <td>
                                <?php if ($data[$i]->updated) { ?>
                                  <a href="<?=base_url();?>poi/add/<?=$data[$i]->poi_id;?>"><img src="<?=base_url();?>assets/images/edit.gif" border="0" alt="<?=$this->lang->line("ledit_data"); ?>" title="<?=$this->lang->line("ledit_data"); ?>"></a>
                                  <a href="<?=base_url();?>poi/remove/<?=$data[$i]->poi_id;?>" onclick="javascript: return confirm('<?=$this->lang->line(" lconfirm_delete "); ?>')"><img src="<?=base_url();?>assets/images/trash.gif" border="0" alt="<?=$this->lang->line("lremove_data"); ?>" title="<?=$this->lang->line("lremove_data"); ?>"></a>
                                  <?php } else { ?>
                                    &nbsp; &nbsp;
                                    <?php } ?>
                              </td> -->
                      </tr>
                      <?php } ?>
                  </tbody>
  						</table>
            </div>
      </div>
    </div>

    <div class="col-md-12" id="formaddpoi" style="display: none;">
      <div class="panel" id="panel_form">
        <header class="panel-heading panel-heading-red">Vehicle Data</header>
        <div class="panel-body" id="bar-parent10">
                <div class="form-horizontal" id="frmadd">
                  <table class="table">
                    <?php if (isset($row)) { ?>
          					<input type="hidden" id="id" name="id" value="<?=$row->poi_id;?>" />
          					<tr style="border: 0px;">
          						<td style="border: 0px;">ID</td>
          						<td style="border: 0px;"><?=$row->poi_id;?></td>
          					</tr>
          					<?php } ?>
                    <tr>
                      <td>
                        POI Name
                      </td>
                      <td>
                        <input type="text" class="form-control" id="poi_name" name="poi_name" placeholder="Poi Name">
                      </td>
                    </tr>

                    <tr>
                      <td>POI Category</td>
                      <td>
            							<select id="poicat" name="poicat" onchange="javascript:poicat_onchange()" class="form-control">
              							<?php for($i=0; $i < count($categories); $i++) { ?>
              							<option value="<?=$categories[$i]->poi_cat_id;?>"<?php if (isset($row) && ($categories[$i]->poi_cat_id == $row->poi_category)) { echo " selected"; } ?>><?=$categories[$i]->poi_cat_name;?></option>
              							<?php } ?>
              						</select>
                        <?php for($i=0; $i < count($categories); $i++) { ?>
            							<?php if ($categories[$i]->poi_cat_icon) { ?>
            								<span id="img<?=$categories[$i]->poi_cat_id;?>" style="display: none;"><img src="<?=base_url()?>assets/images/poi/<?=$categories[$i]->poi_cat_icon;?>" border="0" /></span>
            								<?php } else { ?>
            								<span id="img<?=$categories[$i]->poi_cat_id;?>">&nbsp;</span>
            								<?php } ?>
            						<?php } ?>
                      </td>
                    </tr>
                  </table>

                  <div id="mapnya">
                    <div class="pac-card" id="pac-card">
                      <div>
                        <div id="title">
                          Pointing Point Of Interest
                        </div>
                        <div id="type-selector" class="pac-controls">
                          <input type="radio" name="type" id="changetype-all" checked="checked" hidden>
                        </div>
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
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                          <div class="form-group">
                              <div class="offset-md-3 col-md-9">
                                  <button type="submit" class="btn btn-info" onclick="saveThisPoi()">Save</button>
                                  <button type="button" class="btn btn-default" onclick="btncancel()">Cancel</button>
                              </div>
                          </div>
                        </td>
                      </tr>
                  </div>
              </form>
            </div>
          </div>
      </div>
  </div>


</div>
</div>
</div>



<script type="text/javascript">
  function showaddpoi(){
    $("#tablepoi").hide();
    $("#formaddpoi").show();
  }

  function saveThisPoi(){
    var poi_name  = $("#poi_name").val();
    var poicat    = $("#poicat").val();
    var latitude  = $("#latitude").val();
    var longitude = $("#longitude").val();
    var data = {poi_name : poi_name, poicat : poicat, latitude : latitude, longitude : longitude};
		$.post("<?=base_url()?>poidata/save", data, function(r){
        console.log("response : ", r);
        if (r.code == 200) {
          if (confirm(alert(r.msg))) {
            window.location = '<?php echo base_url()?>poidata';
          }
        }
			}, "json");
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
                        // document.getElementById("addressfix").value = results[0].formatted_address;
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
            // jQuery("#addressfix").val(address);
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

  function poicat_onchange()
	{
		<?php for($i=0; $i < count($categories); $i++) { ?>
		jQuery("#img<?=$categories[$i]->poi_cat_id;?>").hide();
		<?php } ?>

		var poiid = jQuery("#poicat").val();
		jQuery("#img"+poiid).show();
	}

  // FOR DISABLE SUBMIT FORM
  $(window).keydown(function(event){
    if(event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });

  function btncancel(){
    window.location = '<?php echo base_url() ?>poidata';
  }
</script>

<?php
	$key = $this->config->item("GOOGLE_MAP_API_KEY");
	if(isset($key) && $key != "") { ?>
    <!-- <?php echo $key; ?> -->
		<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo $key."&libraries=places,drawing&callback=initialize"?>" type="text/javascript"></script>
	<?php } else { ?>
		<script src="http://maps.google.com/maps/api/js?V=3.3&amp;sensor=false"></script>
	<?php } ?>
