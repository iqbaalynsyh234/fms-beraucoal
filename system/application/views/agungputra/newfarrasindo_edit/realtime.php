	<link rel="stylesheet" type="text/css" href="<?=base_url();?>assets/css/maps.css" />
	<script type="text/javascript" src="<?=base_url();?>assets/js/openlayers/OpenLayers.js"></script>
	<script type="text/javascript" src="<?=base_url();?>assets/js/openlayers/lib/OpenLayers/Layer/OpenStreetMap.js"></script>
	<script src="http://maps.google.com/maps/api/js?sensor=false"></script>

    <script>
	    var gidx = 0;
        var map;
		var layers = new Array();
        var gmarker = new Array();
        var glastinfo = null;
        var glastr = null;

        jQuery(document).ready(
        	function()
        	{
        		showclock();
        		init();
        		updateLocation('<?=$data->vehicle_device;?>', <?=$this->config->item('timer_realtime')?>);
        		showgeofence('<?=$data->vehicle_device;?>');
 
                        field_onchange();
        	}
        );


		<?=$initmap;?>
        <?=$updateinfo;?>

       	function field_onchange()
	{
		var v = jQuery("#field").val();

		jQuery("#keyword").hide();
		jQuery("#type").hide();
		jQuery("#status").hide();
		jQuery("#vehicle_type").hide();

		switch(v)
		{
			case "vexpired":
			case "vactive":
			break;
			case "vehicle_type":
				jQuery("#vehicle_type").show();
			break;
			default:
				jQuery("#keyword").show();
		}
	}

function updateLocationEx()
	{
		var n = jQuery("#devname"+gidx).val();
		var h = jQuery("#devhost"+gidx).val();

		jQuery("#pointer0").show();
		updateLocation(n+'@'+h, <?=$this->config->item('timer_list');?>);
	}

        function update(r)
	{
		var vid = jQuery('#devid'+gidx).val();
		if (! r.vehicle.gps)
		{
			jQuery("#pointer"+gidx).hide();
			gidx = gidx+1;
			if (gidx >= <?=count($data);?>)
			{
				gidx = 0;
			}

			if (glasttime == 0)
			{

				var device = r.vehicle.vehicle_device;
				if (device == undefined)
				{
					return;
				}
				var devices = device.split("@");
				var html;
				if (devices.length < 2)
				{
					html = '-';
				}
				else
				{
					html = "<a href='<?php echo base_url();?>trackers/history/"+devices[0]+"/"+devices[1]+"'><i><font color='#8080FF'><?php echo $this->lang->line('lgo_to_history'); ?></font></i></a>";
				}


				jQuery("[id='datetime"+vid+"']").html(html);
				jQuery("[id='position"+vid+"']").html(html);
				jQuery("[id='coord"+vid+"']").html(html);
				jQuery("[id='speed"+vid+"']").html("");
				jQuery("[id='map"+vid+"']").html("");
			}

			var n = jQuery("#devname"+gidx).val();
			var h = jQuery("#devhost"+gidx).val();

			jQuery("#pointer"+gidx).show();

			glasttime = jQuery("#timestamp"+n+"_"+h).html();
			return n+'@'+h;
		}
}
       	function showgeofence(vehicle)
       	{
       		jQuery.post("<?=base_url()?>geofence/getlist", {vehicle: vehicle},
       			function(r)
       			{
       				if (r.error) return;

       				vectors = new OpenLayers.Layer.Vector("Vector Layer");
       				map.addLayer(vectors);

       				for(var i=0; i < r.geofence.length; i++)
       				{
       					deserialize(r.geofence[i].geofence_json);
       				}
       			}
       			, "json"
       		);
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
				//map.zoomToExtent(bounds);
			}

		}
		function update(r)
		{
			if (! r.vehicle.gps) return false;

			glastr = glastinfo;
			glastinfo = r;

    		var center = new OpenLayers.LonLat(r.vehicle.gps.gps_longitude_real, r.vehicle.gps.gps_latitude_real);
    		if ((glastlat == null) && (glastlon == null))
    		{
    			changeMap(center, <?=$zoom;?>, r);
    		}
    		else
    		if ((glastlat != r.vehicle.gps.gps_latitude_real) || (glastlon != r.vehicle.gps.gps_longitude_real))
    		{
    			changeMap(center, map.getZoom(), r);
    		}

    		return false;

		}

	    function setcenter(lat, lng)
	    {
	    	var center = new OpenLayers.LonLat(lng, lat);
			map.setCenter(center.transform(
                    			new OpenLayers.Projection("EPSG:4326"),
                    			map.getProjectionObject()
                			), map.getZoom());
	    }

	    function changeMap(center, zoom, r)
	    {
					map.setCenter(center.transform(
                    			new OpenLayers.Projection("EPSG:4326"),
                    			map.getProjectionObject()
                			), zoom);

        		glastlat = r.vehicle.gps.gps_latitude_real;
        		glastlon = r.vehicle.gps.gps_longitude_real;

        		var idx = removeMarker(<?=$data->vehicle_id;?>);
        		addMarker(idx, '<?=$data->vehicle_no;?> - <?=$data->vehicle_name;?>'+r.info, glastlon, glastlat, <?=$data->vehicle_id;?>, r.vehicle.gps.car_icon);
	    }

	    function removeMarker(id)
	    {
	    	for (var i = 0; i < gmarker.length; i++)
	    	{
	    		if (gmarker[i][0] != id) continue;

	    		map.removeLayer(gmarker[i][1]);
	    		return i;
	    	}

	    	return -1;
	    }

        function addMarker(idx, no, lng, lat, id, car)
        {
			var kml_tracker5 = new OpenLayers.Layer.GML
			(
    			no,
    			"<?=base_url()?>map/kmllastcoord/"+lng+"/"+lat+"/"+id+"/"+car+"/<?=$ishistory;?>",
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

			if (glastr)
			{
	    		var point1 = new OpenLayers.Geometry.Point(glastr.vehicle.gps.gps_longitude_real, glastr.vehicle.gps.gps_latitude_real);
				point1.transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject());

	    		var point2 = new OpenLayers.Geometry.Point(glastinfo.vehicle.gps.gps_longitude_real, glastinfo.vehicle.gps.gps_latitude_real);
				point2.transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject());

        		var points = new Array(point1, point2);
				var line = new OpenLayers.Geometry.LineString(points);

                var style = { strokeColor: '#ff0000',  strokeOpacity: 0.5, strokeWidth: 5 };

      			var lineFeature = new OpenLayers.Feature.Vector(line, null, style);
                kml_tracker5.addFeatures([lineFeature]);
			}

            kml_tracker5.events.on({
                'featureselected': kml_tracker5_onFeatureSelect
            });

            function kml_tracker5_onFeatureSelect(evt)
            	{
            		info();
            		poiSelectControl.unselect(evt.feature);
            	}


			if (poiSelectControl)
			{
				var layers = poiSelectControl.layers;
				if (layers == null)
				{
					poiSelectControl.setLayer(new Array(poiSelectControl.layer, kml_tracker5));
				}
				else
				{
					poiSelectControl.setLayer(new Array(layers[0], kml_tracker5));
				}
			}


			if (idx < 0)
			{
				var data = new Array(2);
				data[0] = id;
				data[1] = kml_tracker5;

				gmarker.push(data);
			}
			else
			{
				gmarker[idx][1] = kml_tracker5;
			}
        }

        function info(id)
        {
        	if (! glastinfo)
        	{
        		alert("<?=$this->lang->line('lwait_loading_data'); ?>");
        		return;
        	}

        	var html = glastinfo.info;
        	html = html.replace("tablelist1", "tablelist");

        	jQuery('#dialog').html(html);
			jQuery('#dialog').dialog('option', 'title', "<?=$this->lang->line('llast_info');?>: " + glastinfo.vehicle.vehicle_no + " - " + glastinfo.vehicle.vehicle_name);
			jQuery('#dialog').dialog('option', 'width', 700);
			jQuery('#dialog').dialog('option', 'height', 420);
			jQuery('#dialog').dialog('option', 'modal', false);
			jQuery('#dialog').dialog('open');
        }

        function info2(id)
        {
        	if (! glastinfo)
        	{
        		alert("<?=$this->lang->line('lwait_loading_data'); ?>");
        		return false;
        	}

        	var html = glastinfo.info;
        	html = html.replace("tablelist1", "tablelist");

        	jQuery('#dialog').html(html);
			jQuery('#dialog').dialog('option', 'title', "<?=$this->lang->line('llast_info');?>: " + glastinfo.vehicle.vehicle_no + " - " + glastinfo.vehicle.vehicle_name);
			jQuery('#dialog').dialog('option', 'width', 700);
			jQuery('#dialog').dialog('option', 'height', 420);
			jQuery('#dialog').dialog('option', 'modal', false);
			jQuery('#dialog').dialog('open');
                        return false;
        }

		function showGoogleEarth(txt)
		{

			jQuery('#dialog').dialog('close');
			showdialog('<h3><?=$this->lang->line('lgoogle_earth_network_link_desc')?></h3>' + txt, '<?=$this->lang->line('lgoogle_earth_network_link')?>', 1000, 150);
		}

		var vectors = null;

    </script>
    <style>
body {
    margin: 0;
    padding: 0;
}

#map {
    width: 85%;
    height: 100%;
    float: right;
}
</style>
	<table width="100%"><tr>
        <td width="16%"><div style="position: absolute; margin: 0;  padding: 0; z-index: 1000; background-color:#FF6600; width: 100%; height:30px;  ">
                <font size="1" color="white"><b>FARRASINDO<br>
                    Monitoring Vehicle Division</b></font></div>
    </td>
        <td>
    <div style="position: absolute; margin: 0;  padding: 0; z-index: 1000; width: 83%;">
 		<?=$navigation;?></div></td>
    </tr>
</table>
        <div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 202px; top: 17px; left: 0px;">
<div id="main" >
			  <span id='tblrealtime'><br><table align="center" width="100%"  ><tr>
                  <td width="100%" align="center" bgcolor="#FF6600" height="20">
       <font size="1" color="#FFFFFF"><b>VEHICLE <u> LIST :</u></b></font></td></tr></table>
              <div style="border:0px red solid;  width:200px; height:150px; overflow:auto;">
                <table align="center" width="100%" >

                                  <?php
                                  $qry = "select * from webtracking_vehicle where vehicle_user_id = $data->vehicle_user_id order by vehicle_no asc"  ;
                                  $result = mysql_query($qry);
                                  $ttl = mysql_num_rows($result);
                                  ?>
				  <tr><td><a href="<?=base_url();?>trackers"><font size="1"><b>VIEW ALL <?php echo "[$ttl]"; ?></b></font></a> - <font size="1"><b>LIVE ( <?=$data->vehicle_name." ".$data->vehicle_no;?> )</b></font></td></tr></table>
<table>

                                  <?php

                                  if(mysql_num_rows($result) > 0 )
                                  {

                                     while($row2 = mysql_fetch_array($result))

                                 { ?>

 <?php
 $rname = $row2["vehicle_name"];
 $rdev_name = $row2["vehicle_device"];
 $rdev_ex = explode("@",$rdev_name);
 $rdev_ex0 = $rdev_ex[0];
 $rdev_ex1 = $rdev_ex[1];
 $rno = $row2["vehicle_no"];?>

<tr>
<td align="left" width="20px"><img src="<?=base_url();?>assets/farrasindo/images/checklist.png" width="15px" height="15px" /></td>
<td valign="left"  width="100%">
<font size="1"><b>
<a href="<?=base_url();?>map/realtime/<?php echo $rdev_ex0; ?>/<?php echo $rdev_ex1; ?>">
<?php echo $rname." ".$rno; ?></font></a>

<?php      echo "</br>" ; ?>

                </td></tr>

                                                         <?php }  ?>
               <?php
                                  } else {
                                      echo "DATA KOSONG !";
                                  }
                                  ?>

                                  <tr><td></table>
 </div>
<div style="border:0px red solid;  width:200px; height:150px;">
<table width="100%" align="left">
<tr>
  <td width="100%" align="center" valign="center" bgcolor="#FF6600"><font size="1" color="#FFFFFF"><b>VEHICLE <u> INFO ( <?=$data->vehicle_name." ".$data->vehicle_no;?> )</u><br><a href="" onclick="return info2()">- SHOW INFO -</a></b></font></td></tr>
<tr>
  <td><div id="dialog" >
          
      </div></td></tr>
  <td width="100%" align="center" valign="center" bgcolor="#FF6600" height="20"><font size="1" color="#FFFFFF" ><b>MESSAGE <u> ALERT </u></b></font></td></tr>
<tr>
  <td align="right"><a href="<?=base_url();?>alarm"><font size="1"><b>View Details</b></font></a></td></tr>

</table>

      </td></tr>

                    </table>
</div>
</div></div>
    <div id="map"></div>
