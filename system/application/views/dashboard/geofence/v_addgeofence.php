<style media="screen">
  .page-content{
    width: 90%;
  }

  .olControlEditingToolbar .olControlModifyFeatureItemInactive {
      background-position: -1px 0px ;
  }
  .olControlEditingToolbar .olControlModifyFeatureItemActive {
      background-position: -1px -23px ;
  }
</style>
<link rel="stylesheet" type="text/css" href="<?=base_url();?>assets/css/maps.css" />
<script type="text/javascript" src="<?=base_url();?>assets/js/openlayers/OpenLayers.js"></script>
<script type="text/javascript" src="<?=base_url();?>assets/js/openlayers/lib/OpenLayers/Layer/OpenStreetMap.js"></script>

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
      <div class="col-md-12" id="tablevehicleforgeofence">
          <div class="card-box">
            <div class="card-body">
              <form class="block-content form" id="frmadd" onsubmit="javascript: return carilokasi()">
                <h4>Manage Geofence</h4>
                <table width="100%">
                  <tr>
                    <td>
                      <fieldset>
                        <legend>
                        <?=$this->lang->line("lmangeofence"); ?> '<?php echo $vehicle->vehicle_name ?> - <?php echo $vehicle->vehicle_no ?>'
                        </legend>
                        <button class="btn btn-flat" type="button" name="btncopy" id="btncopy" onclick="javascript:copyto();" />Copy Geofence To</button>
                        <button class="btn btn-flat" type="button" name="btncopy" id="btncopy" onclick="javascript:gotovehicle();" />Center To Vehicle Position</button>
                        <button class="btn btn-flat" type="button" name="btncancel" id="btncancel" onclick="location='<?=base_url()?>geofencedata/manage/<?php echo $this->uri->segment("3"); ?>/<?php echo $this->uri->segment("4"); ?>/<?php echo uniqid();?>';" />Center To Geofence</button>
                      </fieldset>
                    </td>

                    <td>
                      <fieldset>
                        <legend>
                        <?php echo "Coordinate" . " " . $this->lang->line("llocation"); ?>
                        </legend>
                        <input type="text" class="form-control" value="" id="lokasi" name="lokasi" size="30" />
                        <input class="form-control btn btn-primary" type="button" value="<?php echo $this->lang->line("lcenter"); ?>" onclick="javascript: carilokasi()" />
                      </fieldset>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      <fieldset>
                        <legend>Vehicle List</legend>
                        <select name="vehicleid" id="vehicleid" class="form-control">
                        <?php for($i=0; $i < count($vehicles); $i++) { ?>
                          <?php
                            $curdev = sprintf("%s@%s", $this->uri->segment("3"), $this->uri->segment("4"));
                            $v1 = str_replace("@", "/", $vehicles[$i]->vehicle_device);
                          ?>
                        <option value="<?php echo $v1; ?>"<?php if ($curdev == $vehicles[$i]->vehicle_device) { echo " selected"; }?>><?php if ($this->sess->user_type != 2) { echo $vehicles[$i]->user_name." "; } ?><?php echo $vehicles[$i]->vehicle_no; ?> - <?php echo $vehicles[$i]->vehicle_name; ?></option>
                        <?php } ?>
                        </select>
                      <input class="button" type="button" name="btnmove" id="btnmove" value=" <?php echo $this->lang->line('lgo'); ?> " onclick="javascript: othervehicle(this)" />
                      </fieldset>
                    </td>

                    <td>
                      <fieldset>
                      <legend>Control</legend>
                          <input class="btn btn-success" type="button" name="btnsave" id="btnsave" value=" Save " onclick="javascript: frmadd_onsubmit(this)" />
                          <input class="btn btn-flat" type="button" name="btnlabel" id="btnlabel" value=" <?php echo $this->lang->line("lgeofence_list"); ?> " onclick="javascript:showdata()" />
                          <input class="btn btn-warning" type="button" name="btncancel" id="btncancel" value=" <?php echo $this->lang->line("lreset"); ?> " onclick="location='<?=base_url()?>geofencedata/manage/<?php echo $this->uri->segment("3"); ?>/<?php echo $this->uri->segment("4"); ?>/<?php echo uniqid();?>';" />
                      </fieldset>
                    </td>
                  </tr>
                </table>
        			     <a name="mapref"></a>
        			   <div id="map_canvas" style="width: 100%; height: 400px;"></div>
      		    </form>
            </div>
      </div>
    </div>


</div>
</div>
</div>

<script type="text/javascript">
var drawingManager;
var all_overlays = [];
var selectedShape;
var colors = ['#1E90FF', '#FF1493', '#32CD32', '#FF8C00', '#4B0082'];
var selectedColor;
var colorButtons = {};

  function clearSelection() {
    if (selectedShape) {
      selectedShape.setEditable(false);
      selectedShape = null;
    }
  }

  function setSelection(shape) {
    clearSelection();
    selectedShape = shape;
    shape.setEditable(true);
    selectColor(shape.get('fillColor') || shape.get('strokeColor'));
  }

  function deleteSelectedShape() {
    if (selectedShape) {
      selectedShape.setMap(null);
    }
  }

  function deleteAllShape() {
    for (var i = 0; i < all_overlays.length; i++) {
      all_overlays[i].overlay.setMap(null);
    }
    all_overlays = [];
  }

  function selectColor(color) {
    selectedColor = color;
    for (var i = 0; i < colors.length; ++i) {
      var currColor = colors[i];
      colorButtons[currColor].style.border = currColor == color ? '2px solid #789' : '2px solid #fff';
    }

    // Retrieves the current options from the drawing manager and replaces the
    // stroke or fill color as appropriate.
    var polylineOptions = drawingManager.get('polylineOptions');
    polylineOptions.strokeColor = color;
    drawingManager.set('polylineOptions', polylineOptions);

    var rectangleOptions = drawingManager.get('rectangleOptions');
    rectangleOptions.fillColor = color;
    drawingManager.set('rectangleOptions', rectangleOptions);

    var circleOptions = drawingManager.get('circleOptions');
    circleOptions.fillColor = color;
    drawingManager.set('circleOptions', circleOptions);

    var polygonOptions = drawingManager.get('polygonOptions');
    polygonOptions.fillColor = color;
    drawingManager.set('polygonOptions', polygonOptions);
  }

  function setSelectedShapeColor(color) {
    if (selectedShape) {
      if (selectedShape.type == google.maps.drawing.OverlayType.POLYLINE) {
        selectedShape.set('strokeColor', color);
      } else {
        selectedShape.set('fillColor', color);
      }
    }
  }

  function makeColorButton(color) {
    var button = document.createElement('span');
    button.className = 'color-button';
    button.style.backgroundColor = color;
    google.maps.event.addDomListener(button, 'click', function() {
      selectColor(color);
      setSelectedShapeColor(color);
    });

    return button;
  }

  function buildColorPalette() {
    var colorPalette = document.getElementById('color-palette');
    for (var i = 0; i < colors.length; ++i) {
      var currColor = colors[i];
      var colorButton = makeColorButton(currColor);
      colorPalette.appendChild(colorButton);
      colorButtons[currColor] = colorButton;
    }
    selectColor(colors[0]);
  }

  function removeshapenya(){
    deleteAllShape();
  }

  function initialize() {
    var map = new google.maps.Map(document.getElementById('map_canvas'), {
      center: new google.maps.LatLng(-6.2293867, 106.6894289),
      zoom: 10,
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      disableDefaultUI: true,
      zoomControl: true,
      options: {
        gestureHandling: 'greedy'
      }
    });

    var polyOptions = {
      strokeWeight: 0,
      fillOpacity: 0.45,
      editable: true
    };
    // Creates a drawing manager attached to the map that allows the user to draw
    // markers, lines, and shapes.
    drawingManager = new google.maps.drawing.DrawingManager({
      drawingMode: google.maps.drawing.OverlayType.POLYGON,
      markerOptions: {
        draggable: true
      },
      polylineOptions: {
        editable: true
      },
      rectangleOptions: polyOptions,
      circleOptions: polyOptions,
      polygonOptions: polyOptions,
      map: map
    });

    google.maps.event.addListener(drawingManager, 'overlaycomplete', function(e) {
      all_overlays.push(e);
      console.log("e : ", e);
      if (e.type != google.maps.drawing.OverlayType.MARKER) {
        // Switch back to non-drawing mode after drawing a shape.
        drawingManager.setDrawingMode(null);

        // Add an event listener that selects the newly-drawn shape when the user
        // mouses down on it.
        var newShape = e.overlay;
        newShape.type = e.type;
        console.log("newShape : ", newShape);
        // google.maps.event.addListener(newShape, 'click', function() {
        //   setSelection(newShape);
        // });
        // setSelection(newShape);
      }
    });

    // Clear the current selection when the drawing mode is changed, or when the
    // map is clicked.
    // google.maps.event.addListener(drawingManager, 'drawingmode_changed', clearSelection);
    // google.maps.event.addListener(map, 'click', clearSelection);
    // google.maps.event.addDomListener(document.getElementById('delete-button'), 'click', deleteSelectedShape);
    // google.maps.event.addDomListener(document.getElementById('delete-all-button'), 'click', deleteAllShape);

    buildColorPalette();
  }
  google.maps.event.addDomListener(window, 'load', initialize);
</script>

<?php
$key = $this->config->item("GOOGLE_MAP_API_KEY");
//$key = "AIzaSyAYe-6_UE3rUgSHelcU1piLI7DIBnZMid4";

if(isset($key) && $key != "") { ?>
  <script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo $key;?>&libraries=drawing&callback=initialize" type="text/javascript"></script>
  <?php } else { ?>
    <script src="http://maps.google.com/maps/api/js?sensor=false"></script>
    <?php } ?>
