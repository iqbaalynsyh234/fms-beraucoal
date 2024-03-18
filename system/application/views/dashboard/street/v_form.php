<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<style type="text/css">
    .olControlEditingToolbar .olControlModifyFeatureItemInactive {
        background-position: -1px 0px ;
    }
    .olControlEditingToolbar .olControlModifyFeatureItemActive {
        background-position: -1px -23px ;
    }

</style>
<script>
jQuery(document).ready(
function()
{
  // showclock();
  init();
  initMap();

  <?php for($i=0; $i < count($streets); $i++) { ?>
    deserialize('<?php echo $street[$i]->geofence_json; ?>');
  <?php } ?>

  jQuery(".olControlDrawFeaturePointItemInactive").css("display", "none");
  jQuery(".olControlDrawFeaturePathItemInactive").css("display", "none");
}
);

function addMarker(no, lng, lat, id, car){
  kml_tracker5 = new OpenLayers.Layer.GML(
      no,
      "<?=base_url()?>map/kmllastcoord/"+lng+"/"+lat+"/"+id+"/"+car+"/0",
      {
          format: OpenLayers.Format.KML,
          projection: new OpenLayers.Projection("EPSG:4326"),
          formatOptions:
          {
              extractStyles: true,
              extractAttributes: true,
              maxDepth: 2
          }
      }
  );

  map.addLayer(kml_tracker5);
}

function gotovehicle()
{
    var v = jQuery("#vehicleid").val();
jQuery.post('<?php echo base_url(); ?>map/lastinfo', {device: v},
  function(r)
  {
    if (! r.vehicle.gps) return;

    map.setCenter(new OpenLayers.LonLat(r.vehicle.gps.gps_longitude_real, r.vehicle.gps.gps_latitude_real).transform(
                      new OpenLayers.Projection("EPSG:4326"),
                      map.getProjectionObject()
                ) , 18);

            //if (kml_tracker5 != null)
            //{
            //map.removeLayer(kml_tracker5);
            //}

    addMarker(r.vehicle.vehicle_no, r.vehicle.gps.gps_longitude_real, r.vehicle.gps.gps_latitude_real, r.vehicle.vehicle_id, r.vehicle.gps.car_icon);
            location = "#mapref";
  }
  , "json"
);
}

function gotocoordinate() {
  //new
  var marker = new OpenLayers.Layer.Markers("Markers");
  var size = new OpenLayers.Size(20, 20);
  var offset = new OpenLayers.Pixel(-(size.w / 2), -(size.h / 2));
  var icon = new OpenLayers.Icon('<?php echo base_url();?>assets/images/markergif.gif', size, offset);
  //end

    var v = jQuery("#coord").val();
    jQuery.post('<?php echo base_url(); ?>street/getstreet_location', {coord: v},
        function(r) {

            map.setCenter(new OpenLayers.LonLat(r.lng, r.lat).transform(
                      new OpenLayers.Projection("EPSG:4326"),
                      map.getProjectionObject()
                ) , 18);

      var lonlat = new OpenLayers.LonLat(r.lng, r.lat).transform(new OpenLayers.Projection("EPSG:4326"), new OpenLayers.Projection("EPSG:900913"));

          map.addLayer(marker);
          var trackMarker = new OpenLayers.Marker(lonlat, icon);
          marker.addMarker(trackMarker);

            location = "#mapref";
        }
        , "json"
    );
}



function frmadd_onsubmit(frm)
{
    var out_options = {
        'internalProjection': map.baseLayer.projection,
        'externalProjection': new OpenLayers.Projection("EPSG:4326")
    };

if (vectors.features.length == 0)
{
  alert("<?php echo $this->lang->line('lempty_street'); ?>");
  return false;
}

if (vectors.features.length > 1)
{
  alert("<?php echo $this->lang->line('ltoomany_street'); ?>");
  return false;
}

var formats = new OpenLayers.Format.GeoJSON(out_options)
var str = formats.write(vectors.features[0], false);

save(str);
return false;
}

function removeall()
{
vectors.removeAllFeatures();
}

function save(str)
{
jQuery.post("<?=base_url()?>street/save", {json: str, address: jQuery("#address").val()},
  function(r)
  {
    if (r.error)
    {
      alert(r.message);
      return false;
    }

    alert("<?php echo $this->lang->line('ladd_street_success'); ?>");
    location='<?=base_url()?>streetdata/';
  }
  , "json"
);

return true;
}

function initMap()
{
vectors = new OpenLayers.Layer.Vector("Vector Layer");

var style_green =
{
  strokeColor: "#f49440",
  strokeOpacity: 0.6,
  strokeWidth: 2,
  fillColor: "#f6c79a",
  fillOpacity: 0.4
};
vectors.style = style_green;

map.addLayer(vectors);

var layeediting = new OpenLayers.Control.EditingToolbar(vectors);
map.addControl(layeediting);

var options =
{
  hover: true
};

var oselect = new OpenLayers.Control.SelectFeature(vectors, options);
map.addControl(oselect);
oselect.activate();

    out_options = {
        'internalProjection': map.baseLayer.projection,
        'externalProjection': new OpenLayers.Projection("EPSG:4326")
    };
}


function deserialize(str)
{
    var out_options = {
        'internalProjection': map.baseLayer.projection,
        'externalProjection': new OpenLayers.Projection("EPSG:4326")
    };

var formats = new OpenLayers.Format.GeoJSON(out_options)
var features = formats.read(str)
var bounds;
if(features)
{
  if(features.constructor != Array)
  {
    features = [features];
  }

        for(var i=0; i<features.length; ++i)
        {
            if (!bounds)
            {
                bounds = features[i].geometry.getBounds();
            } else
            {
                bounds.extend(features[i].geometry.getBounds());
            }

        }
  vectors.addFeatures(features);
  map.zoomToExtent(bounds);
}

}

var map, vectors;

</script>
<form class="block-content form">
<table width="100%" cellpadding="3">
<tr>
    <td>
    <fieldset>
    <legend>
        Vehicle
    </legend>
    <select name="vehicleid" id="vehicleid" class="form-control">
        <?php for($i=0; $i < count($vehicles); $i++) { ?>
        <?php
        $curdev = sprintf("%s@%s", $this->uri->segment("3"), $this->uri->segment("4"));
        $v1 = $vehicles[$i]->vehicle_device;
        ?>
        <option value="<?php echo $v1; ?>"<?php if ($curdev == $vehicles[$i]->vehicle_device) { echo " selected"; }?>><?php if ($this->sess->user_type != 2) { echo $vehicles[$i]->user_name." "; } ?><?php echo $vehicles[$i]->vehicle_no; ?> - <?php echo $vehicles[$i]->vehicle_name; ?></option>
        <?php } ?>
    </select>
    <button class="btn btn-primary" type="button" name="btncopy" id="btncopy" onclick="javascript:gotovehicle();" /><?php echo $this->lang->line("lgoto_vehicle"); ?></button>
    </fieldset>
    </td>

    <td>
    <fieldset>
    <legend>Search Location by coordinate</legend>
    <input name="coord" id="coord" type="text" class="form-control"/>
    <button class="btn btn-success" type="button" name="btnsearch" id="btnsearch" onclick="javascript:gotocoordinate()" />Search</button>
    </fieldset>
    </td>
</tr>
</table>
</form>

<div id="map" style="width: 100%px; height: 500px;"></div>

<form class="block-content form" id="frmadd" onsubmit="javascript: return frmadd_onsubmit(this)">
<table width="100%" cellpadding="3" >
  <tr>
    <td width="10%">
            <fieldset>
            <legend>
            <?=$this->lang->line("laddress");?>
            </legend>
            <textarea cols="50" name="address" id="address" class="formdefault"></textarea>
            </fieldset>
            </td>
  </tr>

  <tr>
    <td>
      <input class="btn btn-success" type="submit" name="btnsave" id="btnsave" value=" Save " />
      <input class="btn btn-danger" type="button" name="btncancel" id="btncancel" value=" Clear " onclick="javascript:removeall()" />
      <input class="btn btn-warning" type="button" name="btncancel" id="btncancel" value=" Cancel " onclick="location='<?=base_url()?>streetdata/';" />
    </td>
  </tr>
</table>
</form>

<footer>
    <div class="float-right">
    <a href="#top" class="button"><img src="<?php echo base_url();?>assets/newfarrasindo/images/icons/fugue/navigation-090.png" width="16" height="16" /> Page top</a>
    </div>
</footer>
