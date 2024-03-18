

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
    </style>

<script>
	jQuery.maxZIndex = jQuery.fn.maxZIndex = function(opt) {
	    var def = { inc: 10, group: "*" };
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

	jQuery(document).ready(function(){
    // FOR DISABLE SUBMIT FORM
    jQuery(window).keydown(function(event){
      if(event.keyCode == 13) {
        event.preventDefault();
        return false;
      }
    });

    // jQuery("#mapnya").hide();
    //
    // jQuery("#showmap").click(function(){
    //   jQuery("#mapnya").slideToggle();
    // });

				showclock();

        jQuery("#project_startdate").datepicker(
    							{
    										dateFormat: 'dd-mm-yy'
    									, 	startDate: '01-01-2000'
    									, 	showOn: 'button'
    									, 	buttonImage: '<?=base_url()?>assets/images/calendar.gif'
    									, 	buttonImageOnly: true
    									,	beforeShow:
    											function()
    											{
    												jQuery('#ui-datepicker-div').maxZIndex();
    											}
    							}
    						);

        jQuery("#project_enddate").datepicker(
                  {
                        dateFormat: 'dd-mm-yy'
                      , 	startDate: '01-01-2000'
                      , 	showOn: 'button'
                      , 	buttonImage: '<?=base_url()?>assets/images/calendar.gif'
                      , 	buttonImageOnly: true
                      ,	beforeShow:
                          function()
                          {
                            jQuery('#ui-datepicker-div').maxZIndex();
                          }
                  }
                );
		});

	function frmadd_onsubmit()
	{
		jQuery("#loader").show();
		jQuery.post("<?=base_url()?>projectschedule/update_pool", jQuery("#frmadd").serialize(),
			function(r)
			{
				jQuery("#loader").hide();
        var status = r.status;
          if (status == "success") {
            window.location = '<?php echo base_url()?>projectschedule/pool_list';
          }else {
            confirm(alert("Failed save project schedule"));
          }
				// if (r.error)
				// {
				// 	alert(r.message);
				// 	return false;
				// }
        //
				// alert(r.message);
				// location = r.redirect;
			}
			, "json"
		);
		return false;
	}
</script>

<script type="text/javascript">
var circles = [];
var newpopulation = 2.5;
function initialize(){
  var map = new google.maps.Map(
    document.getElementById("mapview"), {
      center: new google.maps.LatLng(-6.1753924, 106.82715280000002),
      zoom: 12,
      mapTypeId: google.maps.MapTypeId.ROADMAP
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
        var circle = new google.maps.Circle({
          strokeColor: '#FF0000',
          strokeOpacity: 0.8,
          strokeWeight: 2,
          fillColor: '#FF0000',
          fillOpacity: 0.35,
          map: map,
          radius: Math.sqrt(newpopulation) * 100,
          editable: true
          // radius: 1000    // 10 miles in metres
          // fillColor: '#AA0000'
        });
        circle.bindTo('center', marker, 'position');
        circles.push(circle);
        console.log("circles : ", circles);
        console.log("circles : ", circle.getRadius());

        // add resize behaviour to the circle
    circle.addListener('radius_changed', function(e){
         storeCircleRadius(circle);
     });

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
          jQuery("#allcordinates").val(circle.getRadius());
          jQuery("#allcordinatesforview").val(Math.round(circle.getRadius()));
          infowindow.open(map, marker);
        });

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

function storeCircleRadius(circle) {
   // set input to store value
   jQuery("#allcordinates").val(circle.getRadius());
   jQuery("#allcordinatesforview").val(Math.round(circle.getRadius()));
   // trigger OnChange event
   jQuery("#allcordinates").trigger('change');
   jQuery("#allcordinatesforview").trigger('change');
}


</script>

<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;">
<?=$navigation;?>
<!-- <?php echo $this->sess->user_id; ?> -->
	<div id="main" style="margin: 20px;">
		<div class="block-border">
			<form class="block-content form" id="frmadd" onsubmit="javascript: return frmadd_onsubmit(this)">
        <input type="hidden" name="idforupdate" id="idforupdate" value="<?php echo $datapool[0]['pool_no'] ?>">
			<h1>Edit Project Schedule</h1>
      [ <a href="<?=base_url();?>projectschedule"><font color="#0000ff">Project List</font></a> ] <br><br>
      <table class="table tablelist" cellpadding="3">
        <tr>
          <td>Pool Name *</td>
          <td>:</td>
          <td>
            <input type="text" name="pool_name" id="pool_name" class="form-control" value="<?php echo $datapool[0]['pool_name'] ?>" required>
          </td>
        </tr>

      </table>
      <small style="color:red;">
        <i>* must filled</i><br>
      </small>
      <!-- <input type="checkbox" name="showmap" id="showmap" value="">Show Map For Pool Address -->
      <br><br>
      <div id="mapnya">
        <div class="pac-card" id="pac-card">
          <div>
            <div id="title">
              Autocomplete search
            </div>
            <div id="type-selector" class="pac-controls">
              <input type="radio" name="type" id="changetype-all" checked="checked" hidden>
              <label for="changetype-all" hidden>Pool Location</label>

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
            <input id="pac-input" type="text"
                placeholder="Enter a location">
          </div>
        </div>
        <div id="mapview"></div>
        <div id="infowindow-content">
          <img src="" width="16" height="16" id="place-icon">
          <span id="place-name"  class="title"></span><br>
          <span id="place-address"></span>
        </div><br><br>
        <table class="table">
          <tr>
            <td>Latitude</td>
            <td>:</td>
            <td>
              <input type="text" name="latitude" id="latitude" value="<?php echo $datapool[0]['pool_latitude'] ?>" readonly>
            </td>
            <td>Longitude</td>
            <td>:</td>
            <td>
              <input type="text" name="longitude" id="longitude" value="<?php echo $datapool[0]['pool_longitude'] ?>" readonly>
            </td>
          </tr>

          <tr>
            <td>Address Fix</td>
            <td>:</td>
            <td>
              <textarea name="addressfix" id="addressfix" rows="8" cols="80" readonly>value="<?php echo $datapool[0]['pool_address'] ?>"</textarea>
            </td>

            <td>Radius</td>
            <td>:</td>
            <td>
              <input type="text" name="allcordinates" id="allcordinates" value="<?php echo $datapool[0]['pool_radius'] ?>"  readonly hidden>
              <input type="text" name="allcordinatesforview" id="allcordinatesforview" value="<?php echo $datapool[0]['pool_radius'] ?>" readonly> Mtr
            </td>
          </tr>

          <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>
              <div style="text-align: right;">
                <button type="submit" class="button">Update Data Pool</button>
              </div>
            </td>
          </tr>
        </table>
      </div>
			</form>
		</div>
	</div>
</div>

<?php
	$key = $this->config->item("GOOGLE_MAP_API_KEY");
	if(isset($key) && $key != "") { ?>
    <?php echo $key; ?>
		<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo $key."&libraries=places,drawing&callback=initialize"?>" type="text/javascript"></script>
	<?php } else { ?>
		<script src="http://maps.google.com/maps/api/js?V=3.3&amp;sensor=false"></script>
	<?php } ?>
