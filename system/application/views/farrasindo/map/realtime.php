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
	    
	    
	    function track(no, p)
	    {
	    	if (ggpx != null)
	    	{
	    		map.removeLayer(ggpx);
	    	}
	    	
			ggpx = new OpenLayers.Layer.GML(no, "<?=base_url()?>map/gpx?"+p, 
				{
					format: OpenLayers.Format.GPX,
					style: {strokeColor: "#FF0000", strokeWidth: 4, strokeOpacity: 0.9},
					projection: new OpenLayers.Projection("EPSG:4326")
				}
			);
			map.addLayer(ggpx);	    	
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
                info2();
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
                show_div_vehicle_info();
        	var html = glastinfo.info;
        	html = html.replace("tablelist1", "tablelist");

        	jQuery('#dialog2').html(html);
			jQuery('#dialog2').dialog('option', 'title', "<?=$this->lang->line('llast_info');?>: " + glastinfo.vehicle.vehicle_no + " - " + glastinfo.vehicle.vehicle_name);
			jQuery('#dialog2').dialog('option', 'width', 700);
			jQuery('#dialog2').dialog('option', 'height', 420);
			jQuery('#dialog2').dialog('option', 'modal', false);
			jQuery('#dialog2').dialog('open');
                        return false;
        }

		function showGoogleEarth(txt)
		{

			jQuery('#dialog').dialog('close');
			showdialog('<h3><?=$this->lang->line('lgoogle_earth_network_link_desc')?></h3>' + txt, '<?=$this->lang->line('lgoogle_earth_network_link')?>', 1000, 150);
		}

		var vectors = null;

    </script>

        <script>

 function hide_div_alarm_data() {

        if (document.getElementById) {
        document.getElementById('alarm_data').style.visibility = 'hidden';
        document.getElementById('button_down_alarm_data').style.visibility = 'visible';
        document.getElementById('button_up_alarm_data').style.visibility = 'hidden';
}
else {
    if (document.layers) {
        document.alarm_datat.visibility = 'hidden';
        document.button_down_alarm_data.visibility = 'visible';
        document.button_up_alarm_data.visibility = 'hidden';

    }
    else {
        document.all.alarm_data.style.visibility = 'hidden';
        document.all.button_down_alarm_data.style.visibility = 'visible';
        document.all.button_up_alarm_data.style.visibility = 'hidden';

    }
}


    }

function show_div_alarm_data() {

        if (document.getElementById) {
        document.getElementById('alarm_data').style.visibility = 'visible';
        document.getElementById('button_down_alarm_data').style.visibility = 'hidden';
        document.getElementById('button_up_alarm_data').style.visibility = 'visible';

}
else {
    if (document.layers) {
        document.alarm_data.visibility = 'visible';
        document.button_down_alarm_data.visibility = 'hidden';
        document.button_up_alarm_data.visibility = 'visible';

    }
    else {
        document.all.alarm_data.style.visibility = 'visible';
        document.all.button_down_alarm_data.style.visibility = 'hidden';
        document.all.button_up_alarm_data.style.visibility = 'visible';

    }
}


    }


function hide_div_vehicle_info() {
var objmap = document.getElementById('layerswitcher');
var objlist = document.getElementById('vehicle_list');

if ((objmap.style.visibility == 'visible') && (objlist.style.visibility == 'visible')) {

        if (document.getElementById) {
        document.getElementById('dialog2').style.visibility = 'hidden';
        document.getElementById('button_down_vehicle_info').style.visibility = 'visible';
        document.getElementById('button_up_vehicle_info').style.visibility = 'hidden';
        document.getElementById('lbl_alarm').style.top = '273px';
        document.getElementById('button_up_alarm_data').style.top = '273px';
        document.getElementById('button_down_alarm_data').style.top = '273px';
        document.getElementById('alarm_data').style.top = '300px';

}
else {
    if (document.layers) {
        document.dialog2.visibility = 'hidden';
        document.button_down_vehicle_info.visibility = 'visible';
        document.button_up_vehicle_info.visibility = 'hidden';
        document.lbl_alarm.top = '273px';
        document.button_up_alarm_data.top = '273px';
        document.button_down_alarm_data.top = '273px';
        document.alarm_data.top = '300px';

    }
    else {
        document.all.dialog2.style.visibility = 'hidden';
        document.all.button_down_vehicle_info.style.visibility = 'visible';
        document.all.button_up_vehicle_info.style.visibility = 'hidden';
        document.all.lbl_alarm.style.top = '273px';
        document.all.button_up_alarm_data.style.top = '273px';
        document.all.button_down_alarm_data.style.top = '273px';
        document.all.alarm_data.style.top = '300px';

    }
}
}
if ((objmap.style.visibility == 'visible') && (objlist.style.visibility == 'hidden')) {

        if (document.getElementById) {
        document.getElementById('dialog2').style.visibility = 'hidden';
        document.getElementById('button_down_vehicle_info').style.visibility = 'visible';
        document.getElementById('button_up_vehicle_info').style.visibility = 'hidden';
        document.getElementById('lbl_alarm').style.top = '180px';
        document.getElementById('button_up_alarm_data').style.top = '180px';
        document.getElementById('button_down_alarm_data').style.top = '180px';
        document.getElementById('alarm_data').style.top = '210px';

}
else {
    if (document.layers) {
        document.dialog2.visibility = 'hidden';
        document.button_down_vehicle_info.visibility = 'visible';
        document.button_up_vehicle_info.visibility = 'hidden';
        document.lbl_alarm.top = '180px';
        document.button_up_alarm_data.top = '180px';
        document.button_down_alarm_data.top = '180px';
        document.alarm_data.top = '210px';

    }
    else {
        document.all.dialog2.style.visibility = 'hidden';
        document.all.button_down_vehicle_info.style.visibility = 'visible';
        document.all.button_up_vehicle_info.style.visibility = 'hidden';
        document.all.lbl_alarm.style.top = '180px';
        document.all.button_up_alarm_data.style.top = '180px';
        document.all.button_down_alarm_data.style.top = '180px';
        document.all.alarm_data.style.top = '210px';

    }
}
}

if ((objmap.style.visibility == 'hidden') && (objlist.style.visibility == 'visible')) {

        if (document.getElementById) {
        document.getElementById('dialog2').style.visibility = 'hidden';
        document.getElementById('button_down_vehicle_info').style.visibility = 'visible';
        document.getElementById('button_up_vehicle_info').style.visibility = 'hidden';
        document.getElementById('lbl_alarm').style.top = '205px';
        document.getElementById('button_up_alarm_data').style.top = '205px';
        document.getElementById('button_down_alarm_data').style.top = '205px';
        document.getElementById('alarm_data').style.top = '230px';

}
else {
    if (document.layers) {
        document.dialog2.visibility = 'hidden';
        document.button_down_vehicle_info.visibility = 'visible';
        document.button_up_vehicle_info.visibility = 'hidden';
        document.lbl_alarm.top = '205px';
        document.button_up_alarm_data.top = '205px';
        document.button_down_alarm_data.top = '205px';
        document.alarm_data.top = '230px';

    }
    else {
        document.all.dialog2.style.visibility = 'hidden';
        document.all.button_down_vehicle_info.style.visibility = 'visible';
        document.all.button_up_vehicle_info.style.visibility = 'hidden';
        document.all.lbl_alarm.style.top = '205px';
        document.all.button_up_alarm_data.style.top = '205px';
        document.all.button_down_alarm_data.style.top = '205px';
        document.all.alarm_data.style.top = '230px';

    }
}
}

if ((objmap.style.visibility == 'hidden') && (objlist.style.visibility == 'hidden')) {

        if (document.getElementById) {
        document.getElementById('dialog2').style.visibility = 'hidden';
        document.getElementById('button_down_vehicle_info').style.visibility = 'visible';
        document.getElementById('button_up_vehicle_info').style.visibility = 'hidden';
        document.getElementById('lbl_alarm').style.top = '105px';
        document.getElementById('button_up_alarm_data').style.top = '105px';
        document.getElementById('button_down_alarm_data').style.top = '105px';
        document.getElementById('alarm_data').style.top = '130px';

}
else {
    if (document.layers) {
        document.dialog2.visibility = 'hidden';
        document.button_down_vehicle_info.visibility = 'visible';
        document.button_up_vehicle_info.visibility = 'hidden';
        document.lbl_alarm.top = '105px';
        document.button_up_alarm_data.top = '105px';
        document.button_down_alarm_data.top = '105px';
        document.alarm_data.top = '130px';

    }
    else {
        document.all.dialog2.style.visibility = 'hidden';
        document.all.button_down_vehicle_info.style.visibility = 'visible';
        document.all.button_up_vehicle_info.style.visibility = 'hidden';
        document.all.lbl_alarm.style.top = '105px';
        document.all.button_up_alarm_data.style.top = '105px';
        document.all.button_down_alarm_data.style.top = '105px';
        document.all.alarm_data.style.top = '130px';

    }
}
}

    }



function show_div_vehicle_info() {
var objmap = document.getElementById('layerswitcher');
var objlist = document.getElementById('vehicle_list');
if ((objmap.style.visibility == 'visible') && (objlist.style.visibility == 'visible')) {


        if (document.getElementById) {
        document.getElementById('dialog2').style.visibility = 'visible';
     // document.getElementById('dialog').style.top = '350px';
        document.getElementById('button_down_vehicle_info').style.visibility = 'hidden';
        document.getElementById('button_up_vehicle_info').style.visibility = 'visible';
        document.getElementById('lbl_alarm').style.top = '421px';
        document.getElementById('button_up_alarm_data').style.top = '421px';
        document.getElementById('button_down_alarm_data').style.top = '421px';
        document.getElementById('alarm_data').style.top = '447px';
}
else {
    if (document.layers) {
        document.dialog2.visibility = 'visible';
        document.button_down_vehicle_info.visibility = 'hidden';
        document.button_up_vehicle_info.visibility = 'visible';
        document.lbl_alarm.top = '421px';
        document.button_up_alarm_data.top = '421px';
        document.button_down_alarm_data.top = '421px';
        document.alarm_data.top = '447px';

    }
    else {
        document.all.dialog2.style.visibility = 'visible';
        document.all.button_down_vehicle_info.style.visibility = 'hidden';
        document.all.button_up_vehicle_info.style.visibility = 'visible';
        document.all.lbl_alarm.style.top = '421px';
        document.all.button_up_alarm_data.style.top = '421px';
        document.all.button_down_alarm_data.style.top = '421px';
        document.all.alarm_data.style.top = '447px';

    }
}
}
if ((objmap.style.visibility == 'visible') && (objlist.style.visibility == 'hidden')) {


        if (document.getElementById) {
        document.getElementById('dialog2').style.visibility = 'visible';
        document.getElementById('dialog2').style.top = '178px';
        document.getElementById('button_down_vehicle_info').style.visibility = 'hidden';
        document.getElementById('button_up_vehicle_info').style.visibility = 'visible';
        document.getElementById('lbl_alarm').style.top = '330px';
        document.getElementById('button_up_alarm_data').style.top = '330px';
        document.getElementById('button_down_alarm_data').style.top = '330px';
        document.getElementById('alarm_data').style.top = '360px';
}
else {
    if (document.layers) {
        document.dialog2.visibility = 'visible';
        document.button_down_vehicle_info.visibility = 'hidden';
        document.button_up_vehicle_info.visibility = 'visible';
        document.lbl_alarm.top = '330px';
        document.button_up_alarm_data.top = '330px';
        document.button_down_alarm_data.top = '330px';
        document.alarm_data.top = '360px';

    }
    else {
        document.all.dialog2.style.visibility = 'visible';
        document.all.button_down_vehicle_info.style.visibility = 'hidden';
        document.all.button_up_vehicle_info.style.visibility = 'visible';
        document.all.lbl_alarm.style.top = '330px';
        document.all.button_up_alarm_data.style.top = '330px';
        document.all.button_down_alarm_data.style.top = '330px';
        document.all.alarm_data.style.top = '360px';

    }
}
}

if ((objmap.style.visibility == 'hidden') && (objlist.style.visibility == 'visible')) {


        if (document.getElementById) {
        document.getElementById('dialog2').style.visibility = 'visible';
        document.getElementById('button_down_vehicle_info').style.visibility = 'hidden';
        document.getElementById('button_up_vehicle_info').style.visibility = 'visible';
        document.getElementById('lbl_alarm').style.top = '355px';
        document.getElementById('button_up_alarm_data').style.top = '355px';
        document.getElementById('button_down_alarm_data').style.top = '355px';
        document.getElementById('alarm_data').style.top = '385px';
}
else {
    if (document.layers) {
        document.dialog2.visibility = 'visible';
        document.button_down_vehicle_info.visibility = 'hidden';
        document.button_up_vehicle_info.visibility = 'visible';
        document.lbl_alarm.top = '355px';
        document.button_up_alarm_data.top = '355px';
        document.button_down_alarm_data.top = '355px';
        document.alarm_data.top = '385px';

    }
    else {
        document.all.dialog2.style.visibility = 'visible';
        document.all.button_down_vehicle_info.style.visibility = 'hidden';
        document.all.button_up_vehicle_info.style.visibility = 'visible';
        document.all.lbl_alarm.style.top = '355px';
        document.all.button_up_alarm_data.style.top = '355px';
        document.all.button_down_alarm_data.style.top = '355px';
        document.all.alarm_data.style.top = '385px';

    }
}
}

if ((objmap.style.visibility == 'hidden') && (objlist.style.visibility == 'hidden')) {


        if (document.getElementById) {
        document.getElementById('dialog2').style.visibility = 'visible';
        document.getElementById('button_down_vehicle_info').style.visibility = 'hidden';
        document.getElementById('button_up_vehicle_info').style.visibility = 'visible';
        document.getElementById('lbl_alarm').style.top = '255px';
        document.getElementById('button_up_alarm_data').style.top = '255px';
        document.getElementById('button_down_alarm_data').style.top = '255px';
        document.getElementById('alarm_data').style.top = '285px';
}
else {
    if (document.layers) {
        document.dialog2.visibility = 'visible';
        document.button_down_vehicle_info.visibility = 'hidden';
        document.button_up_vehicle_info.visibility = 'visible';
        document.lbl_alarm.top = '255px';
        document.button_up_alarm_data.top = '255px';
        document.button_down_alarm_data.top = '255px';
        document.alarm_data.top = '285px';

    }
    else {
        document.all.dialog2.style.visibility = 'visible';
        document.all.button_down_vehicle_info.style.visibility = 'hidden';
        document.all.button_up_vehicle_info.style.visibility = 'visible';
        document.all.lbl_alarm.style.top = '255px';
        document.all.button_up_alarm_data.style.top = '255px';
        document.all.button_down_alarm_data.style.top = '255px';
        document.all.alarm_data.style.top = '285px';

    }
}
}
    }


function hide_div_vehicle_list() {
var objmap = document.getElementById('layerswitcher');
var objdialog = document.getElementById('dialog2');

if ((objmap.style.visibility == 'visible') && (objdialog.style.visibility == 'visible')) {
        if (document.getElementById) {
        document.getElementById('vehicle_list').style.visibility = 'hidden';
        document.getElementById('button_down_vehicle_list').style.visibility = 'visible';
        document.getElementById('button_up_vehicle_list').style.visibility = 'hidden';
        document.getElementById('lbl_vehicle_info').style.top = '150px';
        document.getElementById('button_up_vehicle_info').style.top = '150px';
        document.getElementById('button_down_vehicle_info').style.top = '150px';
        document.getElementById('dialog2').style.top = '180px';
        document.getElementById('lbl_alarm').style.top = '330px';
        document.getElementById('button_up_alarm_data').style.top = '330px';
        document.getElementById('button_down_alarm_data').style.top = '330px';
        document.getElementById('alarm_data').style.top = '360px';
}
else {
    if (document.layers) {
        document.vehicle_list.visibility = 'hidden';
        document.button_down_vehicle_list.visibility = 'visible';
        document.button_up_vehicle_list.visibility = 'hidden';
        document.lbl_vehicle_info.top = '150px';
        document.button_up_vehicle_info.top = '150px';
        document.button_down_vehicle_info.top = '150px';
        document.dialog2.top = '180px';
        document.lbl_alarm.top = '330px';
        document.button_up_alarm_data.top = '330px';
        document.button_down_alarm_data.top = '330px';
        document.alarm_data.top = '360px';
    }
    else {
        document.all.vehicle_list.style.visibility = 'hidden';
        document.all.button_down_vehicle_list.style.visibility = 'visible';
        document.all.button_up_vehicle_list.style.visibility = 'hidden';
        document.all.lbl_vehicle_info.style.top = '150px';
        document.all.button_up_vehicle_info.style.top = '150px';
        document.all.button_down_vehicle_info.style.top = '150px';
        document.all.dialog2.style.top = '180px';
        document.all.lbl_alarm.style.top = '330px';
        document.all.button_up_alarm_data.style.top = '330px';
        document.all.button_down_alarm_data.style.top = '330px';
        document.all.alarm_data.style.top = '360px';
    }
}
        }

if ((objmap.style.visibility == 'hidden') && (objdialog.style.visibility == 'visible')) {
        if (document.getElementById) {
        document.getElementById('vehicle_list').style.visibility = 'hidden';
        document.getElementById('button_down_vehicle_list').style.visibility = 'visible';
        document.getElementById('button_up_vehicle_list').style.visibility = 'hidden';
        document.getElementById('lbl_vehicle_info').style.top = '74px';
        document.getElementById('button_up_vehicle_info').style.top = '74px';
        document.getElementById('button_down_vehicle_info').style.top = '74px';
        document.getElementById('dialog2').style.top = '103px';
        document.getElementById('lbl_alarm').style.top = '255px';
        document.getElementById('button_up_alarm_data').style.top = '255px';
        document.getElementById('button_down_alarm_data').style.top = '255px';
        document.getElementById('alarm_data').style.top = '285px';
}
else {
    if (document.layers) {
        document.vehicle_list.visibility = 'hidden';
        document.button_down_vehicle_list.visibility = 'visible';
        document.button_up_vehicle_list.visibility = 'hidden';
        document.lbl_vehicle_info.top = '74px';
        document.button_up_vehicle_info.top = '74px';
        document.button_down_vehicle_info.top = '74px';
        document.dialog2.top = '103px';
        document.lbl_alarm.top = '255px';
        document.button_up_alarm_data.top = '255px';
        document.button_down_alarm_data.top = '255px';
        document.alarm_data.top = '285px';
    }
    else {
        document.all.vehicle_list.style.visibility = 'hidden';
        document.all.button_down_vehicle_list.style.visibility = 'visible';
        document.all.button_up_vehicle_list.style.visibility = 'hidden';
        document.all.lbl_vehicle_info.style.top = '74px';
        document.all.button_up_vehicle_info.style.top = '74px';
        document.all.button_down_vehicle_info.style.top = '74px';
        document.all.dialog2.style.top = '103px';
        document.all.lbl_alarm.style.top = '255px';
        document.all.button_up_alarm_data.style.top = '255px';
        document.all.button_down_alarm_data.style.top = '255px';
        document.all.alarm_data.style.top = '285px';
    }
}
        }

if ((objmap.style.visibility == 'visible') && (objdialog.style.visibility == 'hidden')) {
        if (document.getElementById) {
        document.getElementById('vehicle_list').style.visibility = 'hidden';
        document.getElementById('button_down_vehicle_list').style.visibility = 'visible';
        document.getElementById('button_up_vehicle_list').style.visibility = 'hidden';
        document.getElementById('lbl_vehicle_info').style.top = '150px';
        document.getElementById('button_up_vehicle_info').style.top = '150px';
        document.getElementById('button_down_vehicle_info').style.top = '150px';
    //    document.getElementById('dialog2').style.top = '177px';
        document.getElementById('lbl_alarm').style.top = '180px';
        document.getElementById('button_up_alarm_data').style.top = '180px';
        document.getElementById('button_down_alarm_data').style.top = '180px';
        document.getElementById('alarm_data').style.top = '210px';
}
else {
    if (document.layers) {
        document.vehicle_list.visibility = 'hidden';
        document.button_down_vehicle_list.visibility = 'visible';
        document.button_up_vehicle_list.visibility = 'hidden';
        document.lbl_vehicle_info.top = '150px';
        document.button_up_vehicle_info.top = '150px';
        document.button_down_vehicle_info.top = '150px';
     //   document.dialog2.top = '177px';
        document.lbl_alarm.top = '180px';
        document.button_up_alarm_data.top = '180px';
        document.button_down_alarm_data.top = '180px';
        document.alarm_data.top = '210px';
    }
    else {
        document.all.vehicle_list.style.visibility = 'hidden';
        document.all.button_down_vehicle_list.style.visibility = 'visible';
        document.all.button_up_vehicle_list.style.visibility = 'hidden';
        document.all.lbl_vehicle_info.style.top = '150px';
        document.all.button_up_vehicle_info.style.top = '150px';
        document.all.button_down_vehicle_info.style.top = '150px';
  //      document.all.dialog2.style.top = '177px';
        document.all.lbl_alarm.style.top = '180px';
        document.all.button_up_alarm_data.style.top = '180px';
        document.all.button_down_alarm_data.style.top = '180px';
        document.all.alarm_data.style.top = '210px';
    }
}
        }
if ((objmap.style.visibility == 'hidden') && (objdialog.style.visibility == 'hidden')) {
        if (document.getElementById) {
        document.getElementById('vehicle_list').style.visibility = 'hidden';
        document.getElementById('button_down_vehicle_list').style.visibility = 'visible';
        document.getElementById('button_up_vehicle_list').style.visibility = 'hidden';
        document.getElementById('lbl_vehicle_info').style.top = '75px';
        document.getElementById('button_up_vehicle_info').style.top = '75px';
        document.getElementById('button_down_vehicle_info').style.top = '75px';
    //    document.getElementById('dialog2').style.top = '177px';
        document.getElementById('lbl_alarm').style.top = '105px';
        document.getElementById('button_up_alarm_data').style.top = '105px';
        document.getElementById('button_down_alarm_data').style.top = '105px';
        document.getElementById('alarm_data').style.top = '130px';
}
else {
    if (document.layers) {
        document.vehicle_list.visibility = 'hidden';
        document.button_down_vehicle_list.visibility = 'visible';
        document.button_up_vehicle_list.visibility = 'hidden';
        document.lbl_vehicle_info.top = '75px';
        document.button_up_vehicle_info.top = '75px';
        document.button_down_vehicle_info.top = '75px';
     //   document.dialog2.top = '177px';
        document.lbl_alarm.top = '105px';
        document.button_up_alarm_data.top = '105px';
        document.button_down_alarm_data.top = '105px';
        document.alarm_data.top = '130px';
    }
    else {
        document.all.vehicle_list.style.visibility = 'hidden';
        document.all.button_down_vehicle_list.style.visibility = 'visible';
        document.all.button_up_vehicle_list.style.visibility = 'hidden';
        document.all.lbl_vehicle_info.style.top = '75px';
        document.all.button_up_vehicle_info.style.top = '75px';
        document.all.button_down_vehicle_info.style.top = '75px';
  //      document.all.dialog2.style.top = '177px';
        document.all.lbl_alarm.style.top = '105px';
        document.all.button_up_alarm_data.style.top = '105px';
        document.all.button_down_alarm_data.style.top = '105px';
        document.all.alarm_data.style.top = '130px';
    }
}
        }
    }

function show_div_vehicle_list() {
var objmap = document.getElementById('layerswitcher');
var objdialog = document.getElementById('dialog2');

if ((objmap.style.visibility == 'visible') && (objdialog.style.visibility == 'visible')) {

        if (document.getElementById) {
        document.getElementById('vehicle_list').style.visibility = 'visible';
        document.getElementById('button_down_vehicle_list').style.visibility = 'hidden';
        document.getElementById('button_up_vehicle_list').style.visibility = 'visible';
        document.getElementById('lbl_vehicle_info').style.top = '243px';
        document.getElementById('button_up_vehicle_info').style.top = '243px';
        document.getElementById('button_down_vehicle_info').style.top = '243px';
        document.getElementById('dialog2').style.top = '270px';
        document.getElementById('lbl_alarm').style.top = '421px';
        document.getElementById('button_up_alarm_data').style.top = '421px';
        document.getElementById('button_down_alarm_data').style.top = '421px';
        document.getElementById('alarm_data').style.top = '447px';

}
else {
    if (document.layers) {
        document.vehicle_list.visibility = 'visible';
        document.button_down_vehicle_list.visibility = 'hidden';
        document.button_up_vehicle_list.visibility = 'visible';
        document.lbl_vehicle_info.top = '243px';
        document.button_up_vehicle_info.top = '243px';
        document.button_down_vehicle_info.top = '243px';
        document.dialog2.top = '270px';
        document.lbl_alarm.top = '421px';
        document.button_up_alarm_data.top = '421px';
        document.button_down_alarm_data.top = '421px';
        document.alarm_data.top = '447px';

    }
    else {
        document.all.vehicle_list.style.visibility = 'visible';
        document.all.button_down_vehicle_list.style.visibility = 'hidden';
        document.all.button_up_vehicle_list.style.visibility = 'visible';
        document.all.lbl_vehicle_info.style.top = '243px';
        document.all.button_up_vehicle_info.style.top = '243px';
        document.all.button_down_vehicle_info.style.top = '243px';
        document.all.dialog2.style.top = '270px';
        document.all.lbl_alarm.style.top = '421px';
        document.all.button_up_alarm_data.style.top = '421px';
        document.all.button_down_alarm_data.style.top = '421px';
        document.all.alarm_data.style.top = '447px';

    }
}
}

if ((objmap.style.visibility == 'hidden') && (objdialog.style.visibility == 'visible')) {

        if (document.getElementById) {
        document.getElementById('vehicle_list').style.visibility = 'visible';
        document.getElementById('vehicle_list').style.top = '75px';
        document.getElementById('button_down_vehicle_list').style.visibility = 'hidden';
        document.getElementById('button_up_vehicle_list').style.visibility = 'visible';
        document.getElementById('lbl_vehicle_info').style.top = '175px';
        document.getElementById('button_up_vehicle_info').style.top = '175px';
        document.getElementById('button_down_vehicle_info').style.top = '175px';
        document.getElementById('dialog2').style.top = '205px';
        document.getElementById('lbl_alarm').style.top = '355px';
        document.getElementById('button_up_alarm_data').style.top = '355px';
        document.getElementById('button_down_alarm_data').style.top = '355px';
        document.getElementById('alarm_data').style.top = '385px';

}
else {
    if (document.layers) {
        document.vehicle_list.visibility = 'visible';
        document.vehicle_list.top = '75px';
        document.button_down_vehicle_list.visibility = 'hidden';
        document.button_up_vehicle_list.visibility = 'visible';
        document.lbl_vehicle_info.top = '175px';
        document.button_up_vehicle_info.top = '175px';
        document.button_down_vehicle_info.top = '175px';
        document.dialog2.top = '205px';
        document.lbl_alarm.top = '355px';
        document.button_up_alarm_data.top = '355px';
        document.button_down_alarm_data.top = '355px';
        document.alarm_data.top = '385px';

    }
    else {
        document.all.vehicle_list.style.visibility = 'visible';
        document.all.vehicle_list.style.top = '75px';
        document.all.button_down_vehicle_list.style.visibility = 'hidden';
        document.all.button_up_vehicle_list.style.visibility = 'visible';
        document.all.lbl_vehicle_info.style.top = '175px';
        document.all.button_up_vehicle_info.style.top = '175px';
        document.all.button_down_vehicle_info.style.top = '175px';
        document.all.dialog2.style.top = '205px';
        document.all.lbl_alarm.style.top = '355px';
        document.all.button_up_alarm_data.style.top = '355px';
        document.all.button_down_alarm_data.style.top = '355px';
        document.all.alarm_data.style.top = '385px';

    }
}
}

if ((objmap.style.visibility == 'visible') && (objdialog.style.visibility == 'hidden')) {

        if (document.getElementById) {
        document.getElementById('vehicle_list').style.visibility = 'visible';
        document.getElementById('button_down_vehicle_list').style.visibility = 'hidden';
        document.getElementById('button_up_vehicle_list').style.visibility = 'visible';
        document.getElementById('lbl_vehicle_info').style.top = '243px';
        document.getElementById('button_up_vehicle_info').style.top = '243px';
        document.getElementById('button_down_vehicle_info').style.top = '243px';
      //  document.getElementById('dialog2').style.top = '205px';
        document.getElementById('lbl_alarm').style.top = '273px';
        document.getElementById('button_up_alarm_data').style.top = '273px';
        document.getElementById('button_down_alarm_data').style.top = '273px';
        document.getElementById('alarm_data').style.top = '300px';

}
else {
    if (document.layers) {
        document.vehicle_list.visibility = 'visible';
        document.button_down_vehicle_list.visibility = 'hidden';
        document.button_up_vehicle_list.visibility = 'visible';
        document.lbl_vehicle_info.top = '243px';
        document.button_up_vehicle_info.top = '243px';
        document.button_down_vehicle_info.top = '243px';
     //   document.dialog2.top = '205px';
        document.lbl_alarm.top = '273px';
        document.button_up_alarm_data.top = '273px';
        document.button_down_alarm_data.top = '273px';
        document.alarm_data.top = '300px';

    }
    else {
        document.all.vehicle_list.style.visibility = 'visible';
        document.all.button_down_vehicle_list.style.visibility = 'hidden';
        document.all.button_up_vehicle_list.style.visibility = 'visible';
        document.all.lbl_vehicle_info.style.top = '243px';
        document.all.button_up_vehicle_info.style.top = '243px';
        document.all.button_down_vehicle_info.style.top = '243px';
     //   document.all.dialog2.style.top = '205px';
        document.all.lbl_alarm.style.top = '273px';
        document.all.button_up_alarm_data.style.top = '273px';
        document.all.button_down_alarm_data.style.top = '273px';
        document.all.alarm_data.style.top = '300px';

    }
}
}

if ((objmap.style.visibility == 'hidden') && (objdialog.style.visibility == 'hidden')) {

        if (document.getElementById) {
        document.getElementById('vehicle_list').style.visibility = 'visible';
        document.getElementById('button_down_vehicle_list').style.visibility = 'hidden';
        document.getElementById('button_up_vehicle_list').style.visibility = 'visible';
        document.getElementById('lbl_vehicle_info').style.top = '175px';
        document.getElementById('button_up_vehicle_info').style.top = '175px';
        document.getElementById('button_down_vehicle_info').style.top = '175px';
      //  document.getElementById('dialog2').style.top = '205px';
        document.getElementById('lbl_alarm').style.top = '205px';
        document.getElementById('button_up_alarm_data').style.top = '205px';
        document.getElementById('button_down_alarm_data').style.top = '205px';
        document.getElementById('alarm_data').style.top = '235px';

}
else {
    if (document.layers) {
        document.vehicle_list.visibility = 'visible';
        document.button_down_vehicle_list.visibility = 'hidden';
        document.button_up_vehicle_list.visibility = 'visible';
        document.lbl_vehicle_info.top = '175px';
        document.button_up_vehicle_info.top = '175px';
        document.button_down_vehicle_info.top = '175px';
     //   document.dialog2.top = '205px';
        document.lbl_alarm.top = '205px';
        document.button_up_alarm_data.top = '205px';
        document.button_down_alarm_data.top = '205px';
        document.alarm_data.top = '235px';

    }
    else {
        document.all.vehicle_list.style.visibility = 'visible';
        document.all.button_down_vehicle_list.style.visibility = 'hidden';
        document.all.button_up_vehicle_list.style.visibility = 'visible';
        document.all.lbl_vehicle_info.style.top = '243px';
        document.all.button_up_vehicle_info.style.top = '243px';
        document.all.button_down_vehicle_info.style.top = '243px';
     //   document.all.dialog2.style.top = '205px';
        document.all.lbl_alarm.style.top = '273px';
        document.all.button_up_alarm_data.style.top = '273px';
        document.all.button_down_alarm_data.style.top = '273px';
        document.all.alarm_data.style.top = '300px';

    }
}
}
    }


function hide_div_layer_map() {

        var objlist = document.getElementById('vehicle_list');
        var objdialog = document.getElementById('dialog2');

        if ((objlist.style.visibility == 'visible') && (objdialog.style.visibility == 'visible')) {

        if (document.getElementById) {
        document.getElementById('layerswitcher').style.visibility = 'hidden';
        document.getElementById('paneldiv').style.visibility = 'hidden';
        document.getElementById('lbl_vehicle_list').style.top = '45px';
        document.getElementById('button_up_vehicle_list').style.top = '45px';
        document.getElementById('button_down_vehicle_list').style.top = '45px';
        document.getElementById('vehicle_list').style.top = '75px';
        document.getElementById('lbl_vehicle_info').style.top = '175px';
        document.getElementById('button_up_vehicle_info').style.top = '175px';
        document.getElementById('button_down_vehicle_info').style.top = '175px';
        document.getElementById('dialog2').style.top = '205px';
        document.getElementById('lbl_alarm').style.top = '355px';
        document.getElementById('button_up_alarm_data').style.top = '355px';
        document.getElementById('button_down_alarm_data').style.top = '355px';
        document.getElementById('alarm_data').style.top = '385px';
        document.getElementById('button_down_layer_map').style.visibility = 'visible';
        document.getElementById('button_up_layer_map').style.visibility = 'hidden';
}
else {
    if (document.layers) {
        document.layerswitcher.visibility = 'hidden';
        document.paneldiv.visibility = 'hidden';
        document.lbl_vehicle_list.top = '45px';
        document.button_up_vehicle_list.top = '45px';
        document.button_down_vehicle_list.top = '45px';
        document.vehicle_list.top = '75px';
        document.lbl_vehicle_info.top = '175px';
        document.button_up_vehicle_info.top = '175px';
        document.button_down_vehicle_info.top = '175px';
        document.dialog2.top = '205px';
        document.lbl_alarm.top = '355px';
        document.button_up_alarm_data.top = '355px';
        document.button_down_alarm_data.top = '355px';
        document.alarm_data.top = '385px';
        document.button_down_layer_map.visibility = 'visible';
        document.button_up_layer_map.visibility = 'hidden';

    }
    else {
        document.all.layerswitcher.style.visibility = 'hidden';
        document.all.paneldiv.style.visibility = 'hidden';
        document.all.lbl_vehicle_list.style.top = '45px';
        document.all.button_up_vehicle_list.style.top = '45px';
        document.all.button_down_vehicle_list.style.top = '45px';
        document.all.vehicle_list.style.top = '75px';
        document.all.lbl_vehicle_info.style.top = '175px';
        document.all.button_up_vehicle_info.style.top = '175px';
        document.all.button_down_vehicle_info.style.top = '175px';
        document.all.dialog2.style.top = '205px';
        document.all.lbl_alarm.style.top = '355px';
        document.all.button_up_alarm_data.style.top = '355px';
        document.all.button_down_alarm_data.style.top = '355px';
        document.all.alarm_data.style.top = '385px';
        document.all.button_down_layer_map.style.visibility = 'visible';
        document.all.button_up_layer_map.style.visibility = 'hidden';

    }
}
}


if ((objlist.style.visibility == 'visible') && (objdialog.style.visibility == 'hidden')) {

        if (document.getElementById) {
        document.getElementById('layerswitcher').style.visibility = 'hidden';
        document.getElementById('paneldiv').style.visibility = 'hidden';
        document.getElementById('lbl_vehicle_list').style.top = '45px';
        document.getElementById('button_up_vehicle_list').style.top = '45px';
        document.getElementById('button_down_vehicle_list').style.top = '45px';
        document.getElementById('vehicle_list').style.top = '75px';
        document.getElementById('lbl_vehicle_info').style.top = '175px';
        document.getElementById('button_up_vehicle_info').style.top = '175px';
        document.getElementById('button_down_vehicle_info').style.top = '175px';
      //  document.getElementById('dialog2').style.top = '205px';
        document.getElementById('lbl_alarm').style.top = '205px';
        document.getElementById('button_up_alarm_data').style.top = '205px';
        document.getElementById('button_down_alarm_data').style.top = '205px';
        document.getElementById('alarm_data').style.top = '235px';
        document.getElementById('button_down_layer_map').style.visibility = 'visible';
        document.getElementById('button_up_layer_map').style.visibility = 'hidden';
}
else {
    if (document.layers) {
        document.layerswitcher.visibility = 'hidden';
        document.paneldiv.visibility = 'hidden';
        document.lbl_vehicle_list.top = '45px';
        document.button_up_vehicle_list.top = '45px';
        document.button_down_vehicle_list.top = '45px';
        document.vehicle_list.top = '75px';
        document.lbl_vehicle_info.top = '175px';
        document.button_up_vehicle_info.top = '175px';
        document.button_down_vehicle_info.top = '175px';
       // document.dialog2.top = '205px';
        document.lbl_alarm.top = '205px';
        document.button_up_alarm_data.top = '205px';
        document.button_down_alarm_data.top = '205px';
        document.alarm_data.top = '235px';
        document.button_down_layer_map.visibility = 'visible';
        document.button_up_layer_map.visibility = 'hidden';

    }
    else {
        document.all.layerswitcher.style.visibility = 'hidden';
        document.all.paneldiv.style.visibility = 'hidden';
        document.all.lbl_vehicle_list.style.top = '45px';
        document.all.button_up_vehicle_list.style.top = '45px';
        document.all.button_down_vehicle_list.style.top = '45px';
        document.all.vehicle_list.style.top = '75px';
        document.all.lbl_vehicle_info.style.top = '175px';
        document.all.button_up_vehicle_info.style.top = '175px';
        document.all.button_down_vehicle_info.style.top = '175px';
      //  document.all.dialog2.style.top = '205px';
        document.all.lbl_alarm.style.top = '205px';
        document.all.button_up_alarm_data.style.top = '205px';
        document.all.button_down_alarm_data.style.top = '205px';
        document.all.alarm_data.style.top = '235px';
        document.all.button_down_layer_map.style.visibility = 'visible';
        document.all.button_up_layer_map.style.visibility = 'hidden';

    }
}
}

if ((objlist.style.visibility == 'hidden') && (objdialog.style.visibility == 'visible')) {

        if (document.getElementById) {
        document.getElementById('layerswitcher').style.visibility = 'hidden';
        document.getElementById('paneldiv').style.visibility = 'hidden';
        document.getElementById('lbl_vehicle_list').style.top = '45px';
        document.getElementById('button_up_vehicle_list').style.top = '45px';
        document.getElementById('button_down_vehicle_list').style.top = '45px';
       // document.getElementById('vehicle_list').style.top = '75px';
        document.getElementById('lbl_vehicle_info').style.top = '74px';
        document.getElementById('button_up_vehicle_info').style.top = '74px';
        document.getElementById('button_down_vehicle_info').style.top = '74px';
        document.getElementById('dialog2').style.top = '103px';
        document.getElementById('lbl_alarm').style.top = '255px';
        document.getElementById('button_up_alarm_data').style.top = '255px';
        document.getElementById('button_down_alarm_data').style.top = '255px';
        document.getElementById('alarm_data').style.top = '285px';
        document.getElementById('button_down_layer_map').style.visibility = 'visible';
        document.getElementById('button_up_layer_map').style.visibility = 'hidden';
}
else {
    if (document.layers) {
        document.layerswitcher.visibility = 'hidden';
        document.paneldiv.visibility = 'hidden';
        document.lbl_vehicle_list.top = '45px';
        document.button_up_vehicle_list.top = '45px';
        document.button_down_vehicle_list.top = '45px';
      //  document.vehicle_list.top = '75px';
        document.lbl_vehicle_info.top = '74px';
        document.button_up_vehicle_info.top = '74px';
        document.button_down_vehicle_info.top = '74px';
        document.dialog2.top = '103px';
        document.lbl_alarm.top = '255px';
        document.button_up_alarm_data.top = '255px';
        document.button_down_alarm_data.top = '255px';
        document.alarm_data.top = '285px';
        document.button_down_layer_map.visibility = 'visible';
        document.button_up_layer_map.visibility = 'hidden';

    }
    else {
        document.all.layerswitcher.style.visibility = 'hidden';
        document.all.paneldiv.style.visibility = 'hidden';
        document.all.lbl_vehicle_list.style.top = '45px';
        document.all.button_up_vehicle_list.style.top = '45px';
        document.all.button_down_vehicle_list.style.top = '45px';
    //    document.all.vehicle_list.style.top = '75px';
        document.all.lbl_vehicle_info.style.top = '74px';
        document.all.button_up_vehicle_info.style.top = '74px';
        document.all.button_down_vehicle_info.style.top = '74px';
        document.all.dialog2.style.top = '103px';
        document.all.lbl_alarm.style.top = '255px';
        document.all.button_up_alarm_data.style.top = '255px';
        document.all.button_down_alarm_data.style.top = '255px';
        document.all.alarm_data.style.top = '285px';
        document.all.button_down_layer_map.style.visibility = 'visible';
        document.all.button_up_layer_map.style.visibility = 'hidden';

    }
}
}

if ((objlist.style.visibility == 'hidden') && (objdialog.style.visibility == 'hidden')) {

        if (document.getElementById) {
        document.getElementById('layerswitcher').style.visibility = 'hidden';
        document.getElementById('paneldiv').style.visibility = 'hidden';
        document.getElementById('lbl_vehicle_list').style.top = '45px';
        document.getElementById('button_up_vehicle_list').style.top = '45px';
        document.getElementById('button_down_vehicle_list').style.top = '45px';
       // document.getElementById('vehicle_list').style.top = '75px';
        document.getElementById('lbl_vehicle_info').style.top = '75px';
        document.getElementById('button_up_vehicle_info').style.top = '75px';
        document.getElementById('button_down_vehicle_info').style.top = '75px';
      //  document.getElementById('dialog2').style.top = '103px';
        document.getElementById('lbl_alarm').style.top = '105px';
        document.getElementById('button_up_alarm_data').style.top = '105px';
        document.getElementById('button_down_alarm_data').style.top = '105px';
        document.getElementById('alarm_data').style.top = '130px';
        document.getElementById('button_down_layer_map').style.visibility = 'visible';
        document.getElementById('button_up_layer_map').style.visibility = 'hidden';
}
else {
    if (document.layers) {
        document.layerswitcher.visibility = 'hidden';
        document.paneldiv.visibility = 'hidden';
        document.lbl_vehicle_list.top = '45px';
        document.button_up_vehicle_list.top = '45px';
        document.button_down_vehicle_list.top = '45px';
      //  document.vehicle_list.top = '75px';
        document.lbl_vehicle_info.top = '75px';
        document.button_up_vehicle_info.top = '75px';
        document.button_down_vehicle_info.top = '75px';
      //  document.dialog2.top = '103px';
        document.lbl_alarm.top = '105px';
        document.button_up_alarm_data.top = '105px';
        document.button_down_alarm_data.top = '105px';
        document.alarm_data.top = '130px';
        document.button_down_layer_map.visibility = 'visible';
        document.button_up_layer_map.visibility = 'hidden';

    }
    else {
        document.all.layerswitcher.style.visibility = 'hidden';
        document.all.paneldiv.style.visibility = 'hidden';
        document.all.lbl_vehicle_list.style.top = '45px';
        document.all.button_up_vehicle_list.style.top = '45px';
        document.all.button_down_vehicle_list.style.top = '45px';
    //    document.all.vehicle_list.style.top = '75px';
        document.all.lbl_vehicle_info.style.top = '75px';
        document.all.button_up_vehicle_info.style.top = '75px';
        document.all.button_down_vehicle_info.style.top = '75px';
      //  document.all.dialog2.style.top = '103px';
        document.all.lbl_alarm.style.top = '105px';
        document.all.button_up_alarm_data.style.top = '105px';
        document.all.button_down_alarm_data.style.top = '105px';
        document.all.alarm_data.style.top = '130px';
        document.all.button_down_layer_map.style.visibility = 'visible';
        document.all.button_up_layer_map.style.visibility = 'hidden';

    }
}
}


}

function show_div_layer_map() {

        var objlist = document.getElementById('vehicle_list');
        var objdialog = document.getElementById('dialog2');

if ((objlist.style.visibility == 'visible') && (objdialog.style.visibility == 'visible')) {


       if (document.getElementById) {

        document.getElementById('layerswitcher').style.visibility = 'visible';
        document.getElementById('paneldiv').style.visibility = 'visible';
        document.getElementById('lbl_vehicle_list').style.top = '120px';
        document.getElementById('button_up_vehicle_list').style.top = '120px';
        document.getElementById('button_down_vehicle_list').style.top = '120px';
        document.getElementById('vehicle_list').style.top = '147px';
        document.getElementById('lbl_vehicle_info').style.top = '243px';
        document.getElementById('button_up_vehicle_info').style.top = '243px';
        document.getElementById('button_down_vehicle_info').style.top = '243px';
        document.getElementById('dialog2').style.top = '270px';
        document.getElementById('lbl_alarm').style.top = '421px';
        document.getElementById('button_up_alarm_data').style.top = '421px';
        document.getElementById('button_down_alarm_data').style.top = '421px';
        document.getElementById('alarm_data').style.top = '447px';
        document.getElementById('button_down_layer_map').style.visibility = 'hidden';
        document.getElementById('button_up_layer_map').style.visibility = 'visible';
}
else {
    if (document.layers) {
        document.layerswitcher.visibility = 'visible';
        document.paneldiv.visibility = 'visible';
        document.lbl_vehicle_list.top = '120px';
        document.button_up_vehicle_list.top = '120px';
        document.button_down_vehicle_list.top = '120px';
        document.vehicle_list.top = '147px';
        document.lbl_vehicle_info.top = '243px';
        document.button_up_vehicle_info.top = '243px';
        document.button_down_vehicle_info.top = '243px';
        document.dialog2.top = '270px';
        document.lbl_alarm.top = '421px';
        document.button_up_alarm_data.top = '421px';
        document.button_down_alarm_data.top = '421px';
        document.alarm_data.top = '447px';
        document.button_down_layer_map.style.visibility = 'hidden';
        document.button_up_layer_map.style.visibility = 'visible';

    }
    else {
        document.all.layerswitcher.style.visibility = 'visible';
        document.all.paneldiv.style.visibility = 'visible';
        document.all.lbl_vehicle_list.style.top = '120px';
        document.all.button_up_vehicle_list.style.top = '120px';
        document.all.button_down_vehicle_list.style.top = '120px';
        document.all.vehicle_list.style.top = '147px';
        document.all.lbl_vehicle_info.style.top = '243px';
        document.all.button_up_vehicle_info.style.top = '243px';
        document.all.button_down_vehicle_info.style.top = '243px';
        document.all.dialog2.style.top = '270px';
        document.all.lbl_alarm.style.top = '421px';
        document.all.button_up_alarm_data.style.top = '421px';
        document.all.button_down_alarm_data.style.top = '421px';
        document.all.alarm_data.style.top = '447px';
        document.all.button_down_layer_map.style.visibility = 'hidden';
        document.all.button_up_layer_map.style.visibility = 'visible';


    }
}
}


if ((objlist.style.visibility == 'visible') && (objdialog.style.visibility == 'hidden')) {


       if (document.getElementById) {

        document.getElementById('layerswitcher').style.visibility = 'visible';
        document.getElementById('paneldiv').style.visibility = 'visible';
        document.getElementById('lbl_vehicle_list').style.top = '120px';
        document.getElementById('button_up_vehicle_list').style.top = '120px';
        document.getElementById('button_down_vehicle_list').style.top = '120px';
        document.getElementById('vehicle_list').style.top = '147px';
        document.getElementById('lbl_vehicle_info').style.top = '243px';
        document.getElementById('button_up_vehicle_info').style.top = '243px';
        document.getElementById('button_down_vehicle_info').style.top = '243px';
        document.getElementById('lbl_alarm').style.top = '273px';
        document.getElementById('button_up_alarm_data').style.top = '273px';
        document.getElementById('button_down_alarm_data').style.top = '273px';
        document.getElementById('alarm_data').style.top = '300px';
        document.getElementById('button_down_layer_map').style.visibility = 'hidden';
        document.getElementById('button_up_layer_map').style.visibility = 'visible';
}
else {
    if (document.layers) {
        document.layerswitcher.visibility = 'visible';
        document.paneldiv.visibility = 'visible';
        document.lbl_vehicle_list.top = '120px';
        document.button_up_vehicle_list.top = '120px';
        document.button_down_vehicle_list.top = '120px';
        document.vehicle_list.top = '147px';
        document.lbl_vehicle_info.top = '243px';
        document.button_up_vehicle_info.top = '243px';
        document.button_down_vehicle_info.top = '243px';
       // document.dialog2.top = '270px';
        document.lbl_alarm.top = '421px';
        document.button_up_alarm_data.top = '421px';
        document.button_down_alarm_data.top = '421px';
        document.alarm_data.top = '447px';
        document.button_down_layer_map.style.visibility = 'hidden';
        document.button_up_layer_map.style.visibility = 'visible';

    }
    else {
        document.all.layerswitcher.style.visibility = 'visible';
        document.all.paneldiv.style.visibility = 'visible';
        document.all.lbl_vehicle_list.style.top = '120px';
        document.all.button_up_vehicle_list.style.top = '120px';
        document.all.button_down_vehicle_list.style.top = '120px';
        document.all.vehicle_list.style.top = '147px';
        document.all.lbl_vehicle_info.style.top = '243px';
        document.all.button_up_vehicle_info.style.top = '243px';
        document.all.button_down_vehicle_info.style.top = '243px';
      //  document.all.dialog2.style.top = '270px';
        document.all.lbl_alarm.style.top = '421px';
        document.all.button_up_alarm_data.style.top = '421px';
        document.all.button_down_alarm_data.style.top = '421px';
        document.all.alarm_data.style.top = '447px';
        document.all.button_down_layer_map.style.visibility = 'hidden';
        document.all.button_up_layer_map.style.visibility = 'visible';


    }
}
}

if ((objlist.style.visibility == 'hidden') && (objdialog.style.visibility == 'visible')) {


       if (document.getElementById) {

        document.getElementById('layerswitcher').style.visibility = 'visible';
        document.getElementById('paneldiv').style.visibility = 'visible';
        document.getElementById('lbl_vehicle_list').style.top = '120px';
        document.getElementById('button_up_vehicle_list').style.top = '120px';
        document.getElementById('button_down_vehicle_list').style.top = '120px';
        document.getElementById('vehicle_list').style.top = '147px';
        document.getElementById('lbl_vehicle_info').style.top = '150px';
        document.getElementById('button_up_vehicle_info').style.top = '150px';
        document.getElementById('button_down_vehicle_info').style.top = '150px';
        document.getElementById('dialog2').style.top = '177px';
        document.getElementById('lbl_alarm').style.top = '330px';
        document.getElementById('button_up_alarm_data').style.top = '330px';
        document.getElementById('button_down_alarm_data').style.top = '330px';
        document.getElementById('alarm_data').style.top = '360px';
        document.getElementById('button_down_layer_map').style.visibility = 'hidden';
        document.getElementById('button_up_layer_map').style.visibility = 'visible';
}
else {
    if (document.layers) {
        document.layerswitcher.visibility = 'visible';
        document.paneldiv.visibility = 'visible';
        document.lbl_vehicle_list.top = '120px';
        document.button_up_vehicle_list.top = '120px';
        document.button_down_vehicle_list.top = '120px';
        document.vehicle_list.top = '147px';
        document.lbl_vehicle_info.top = '150px';
        document.button_up_vehicle_info.top = '150px';
        document.button_down_vehicle_info.top = '150px';
        document.dialog2.top = '177px';
        document.lbl_alarm.top = '330px';
        document.button_up_alarm_data.top = '330px';
        document.button_down_alarm_data.top = '330px';
        document.alarm_data.top = '360px';
        document.button_down_layer_map.style.visibility = 'hidden';
        document.button_up_layer_map.style.visibility = 'visible';

    }
    else {
        document.all.layerswitcher.style.visibility = 'visible';
        document.all.paneldiv.style.visibility = 'visible';
        document.all.lbl_vehicle_list.style.top = '120px';
        document.all.button_up_vehicle_list.style.top = '120px';
        document.all.button_down_vehicle_list.style.top = '120px';
        document.all.vehicle_list.style.top = '147px';
        document.all.lbl_vehicle_info.style.top = '150px';
        document.all.button_up_vehicle_info.style.top = '150px';
        document.all.button_down_vehicle_info.style.top = '150px';
        document.all.dialog2.style.top = '177px';
        document.all.lbl_alarm.style.top = '330px';
        document.all.button_up_alarm_data.style.top = '330px';
        document.all.button_down_alarm_data.style.top = '330px';
        document.all.alarm_data.style.top = '360px';
        document.all.button_down_layer_map.style.visibility = 'hidden';
        document.all.button_up_layer_map.style.visibility = 'visible';


    }
}
}

if ((objlist.style.visibility == 'hidden') && (objdialog.style.visibility == 'hidden')) {


       if (document.getElementById) {

        document.getElementById('layerswitcher').style.visibility = 'visible';
        document.getElementById('paneldiv').style.visibility = 'visible';
        document.getElementById('lbl_vehicle_list').style.top = '120px';
        document.getElementById('button_up_vehicle_list').style.top = '120px';
        document.getElementById('button_down_vehicle_list').style.top = '120px';
        document.getElementById('vehicle_list').style.top = '147px';
        document.getElementById('lbl_vehicle_info').style.top = '150px';
        document.getElementById('button_up_vehicle_info').style.top = '150px';
        document.getElementById('button_down_vehicle_info').style.top = '150px';
        document.getElementById('dialog2').style.top = '177px';
        document.getElementById('lbl_alarm').style.top = '180px';
        document.getElementById('button_up_alarm_data').style.top = '180px';
        document.getElementById('button_down_alarm_data').style.top = '180px';
        document.getElementById('alarm_data').style.top = '210px';
        document.getElementById('button_down_layer_map').style.visibility = 'hidden';
        document.getElementById('button_up_layer_map').style.visibility = 'visible';
}
else {
    if (document.layers) {
        document.layerswitcher.visibility = 'visible';
        document.paneldiv.visibility = 'visible';
        document.lbl_vehicle_list.top = '120px';
        document.button_up_vehicle_list.top = '120px';
        document.button_down_vehicle_list.top = '120px';
       document.vehicle_list.top = '147px';
        document.lbl_vehicle_info.top = '150px';
        document.button_up_vehicle_info.top = '150px';
        document.button_down_vehicle_info.top = '150px';
       document.dialog2.top = '177px';
        document.lbl_alarm.top = '180px';
        document.button_up_alarm_data.top = '180px';
        document.button_down_alarm_data.top = '180px';
        document.alarm_data.top = '210px';
        document.button_down_layer_map.style.visibility = 'hidden';
        document.button_up_layer_map.style.visibility = 'visible';

    }
    else {
        document.all.layerswitcher.style.visibility = 'visible';
        document.all.paneldiv.style.visibility = 'visible';
        document.all.lbl_vehicle_list.style.top = '120px';
        document.all.button_up_vehicle_list.style.top = '120px';
        document.all.button_down_vehicle_list.style.top = '120px';
      document.all.vehicle_list.style.top = '147px';
        document.all.lbl_vehicle_info.style.top = '150px';
        document.all.button_up_vehicle_info.style.top = '150px';
        document.all.button_down_vehicle_info.style.top = '150px';
  document.all.dialog2.style.top = '177px';
      document.all.lbl_alarm.style.top = '180px';
        document.all.button_up_alarm_data.style.top = '180px';
        document.all.button_down_alarm_data.style.top = '180px';
        document.all.alarm_data.style.top = '210px';
        document.all.button_down_layer_map.style.visibility = 'hidden';
        document.all.button_up_layer_map.style.visibility = 'visible';


    }
}
}


    }

        </script>







    <style>
body {
    margin: 0;
    padding: 0;
}

#map {
    position: absolute;
    left: 200px;
    width: 85%;
    height: 100%;
}

.olControlNavToolbar {
    width:100%;
    height:0px;


}
.olControlNavToolbar div {
  height: 30px;
  top: 50px;
  left: 120px;
  background-color: white;
}



</style>
<body onload="waktu = setTimeout ('info2()',5000)">

<table width="100%">

  <tr>
     <td align="center">
     <div style="position: absolute; left: 0px;  padding: 0; z-index: 1000; background-color:#FF4500; width: 202px; height:35px;  ">
     <font size="1" color="white"><b>FARRASINDO<br>
     Monitoring Vehicle Division</b></font>
     </div>
     </td>

     <td>
     <div style="position: absolute; left: 222px; margin: 0;  padding: 0; z-index: 1000; width: 83%;">
     <?=$navigation;?>
     </div>
     </td>
 </tr>

</table>

     <?php
         $qry = "select * from webtracking_vehicle where vehicle_user_id = $data->vehicle_user_id order by vehicle_no asc"  ;
         $result = mysql_query($qry);
         $ttl = mysql_num_rows($result);
     ?>

<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 202px; top: 17px; left: 0px;">
<div id="main" style="position:absolute">
<span id='tblrealtime'>
<br>

<table align="center" width="100%"  >
  <tr>
       <td align="center" style="position:absolute; left:0px; top:21px; width:202px; height: 20px; background-color:black;">

       <div id="button_up_layer_map" style="position:absolute; left:0px; top:2px;"">
       <a href="javascript:hide_div_layer_map()">
       <img src="<?=base_url();?>assets/farrasindo/images/up.png" height="15" width="15">
       </a>
       </div>

       <div id="button_down_layer_map" style="position:absolute; left:0px; top:2px; visibility:hidden">
       <a href="javascript:show_div_layer_map()">
       <img src="<?=base_url();?>assets/farrasindo/images/down.png" height="15" width="15">
       </a>
       </div>

       <font size="1" color="#FFFFFF">
       <b>Change Map</b>
       </font>

       </td>
  </tr>

  <tr>
        <td id="lbl_vehicle_list"align="center" style="position:absolute; left:0px; top:120px; width:202px; height: 25px; background-color:black;">
        <font size="1" color="#FFFFFF">
        <b>Vehicle List :<br>
        - <a href="<?=base_url();?>trackers">View All <?php echo "[$ttl]"; ?></a> -
        Live<font color="green"> ( <?=$data->vehicle_name." ".$data->vehicle_no;?> )</font></b>
        </font>
        </td>

        <td>
        <div id="button_up_vehicle_list" style="position:absolute; left:0px; top:120px;">
        <a href="javascript:hide_div_vehicle_list()">
        <img src="<?=base_url();?>assets/farrasindo/images/up.png" height="15" width="15"></a>
        </div>
        </td>

        <td>
        <div id="button_down_vehicle_list" style="position:absolute; left:0px; top:120px; visibility:hidden">
        <a href="javascript:show_div_vehicle_list()">
        <img src="<?=base_url();?>assets/farrasindo/images/down.png" height="15" width="15"></a>
        </div>
        </td>
  </tr>
</table>

<div id="vehicle_list" style="position: absolute; left:0px; top:147px; width:202px; height:100px; overflow:auto; visibility:visible">
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

<tr><td width="5%"></td>

<td align="left" width="20px">
       <img src="<?=base_url();?>assets/farrasindo/images/checklist.png" width="15px" height="15px" />
</td>

<td valign="left"  width="100%">
<font size="1"><b>
<a href="<?=base_url();?>map/realtime/<?php echo $rdev_ex0; ?>/<?php echo $rdev_ex1; ?>">
<?php echo $rname." ".$rno; ?>
</font>
</a>
<?php echo "</br>" ;
}
  }
  else {
       echo "DATA KOSONG !";
         }
?>
</td>
</tr>
</table>
</div>

 <?php

   //  header('Refresh:5');

     $months = array('Januari','Februari','Maret', 'April', 'Mei', 'Juni','Juli','Agustus','September','Oktober', 'November','Desember');

     $user_id = $this->sess->user_id;
     $qry_alarm = "Select * from webtracking_user where user_id = $user_id";
     $result_alarm = mysql_query($qry_alarm);
     $rows_alarm = mysql_fetch_array($result_alarm) ;


     $qry_created =  "Select * from webtracking_alarm where alarm_user_id = $user_id ORDER BY alarm_created DESC limit 1";
     $result_created = mysql_query($qry_created);
     $rows_created = mysql_fetch_array($result_created);


     $user_v = $rows_created["alarm_gps_info_id"];
     $qry_v = "Select * from webtracking_gps_info_farrasindo where gps_info_id = $user_v ORDER BY gps_info_time DESC limit 1";
     $result_v = mysql_query($qry_v);
     $rows_v = mysql_fetch_array($result_v);
     $rows_v_ex = $rows_v["gps_info_device"];
     $rows_v_alert = $rows_v["gps_info_alarm_alert"];
     $rows_v_info_time = $rows_v["gps_info_time"];


     $t = dbmaketime($rows_v_info_time);
     $t += 7*3600;

     $t_format = date("d ", $t).$months[date('n', $t)-1].date(" Y H:i:s", $t);


     $v_explode = explode("@",$rows_v_ex);


    $valarm = $v_explode[0];
    $qry_valarm = "Select * from webtracking_vehicle where vehicle_device = '$rows_v_ex' and vehicle_type = 'T4 Farrasindo'";
    $result_valarm = mysql_query($qry_valarm);
    $ttl = mysql_num_rows($result_valarm);
    $rows_valarm = mysql_fetch_array($result_valarm);

    ?>


<div style="border:0px width:200px;">
<table width="100%" align="left">
<tr>

<td id="lbl_vehicle_info" align="center" style="position:absolute; left:0px; top:243px; width:202px; height: 25px; background-color:black; ">
<font size="1" color="#FFFFFF"><b>Vehicle Info ( <?=$data->vehicle_name." ".$data->vehicle_no;?> )
<br>
- <a href="" onclick="return info2()">Show Info Realtime</a> -</b>
</font>
</td>

<td>
<div id="button_up_vehicle_info" style="position:absolute; left:0px; top:243px;">
<a href="javascript:hide_div_vehicle_info()">
<img src="<?=base_url();?>assets/farrasindo/images/up.png" height="15" width="15">
</a></div>
    </td>

<td><div id="button_down_vehicle_info" style="position:absolute; left:0px; top:243px; visibility:hidden">
<a href="javascript:show_div_vehicle_info()">
<img src="<?=base_url();?>assets/farrasindo/images/down.png" height="15" width="15">
</a>
</div>
</td>

</tr>

<tr><td>
<div id="dialog2" style="position: absolute; left:0px; top:270px; font-size:10px; height:150px; overflow:auto; width: 202px; visibility:visible">
</div>
</td></tr>


<td id="lbl_alarm" align="center" style="position:absolute; left:0px; top:421px; width:202px; height: 25px; background-color:black;">
<font size="1" color="#FFFFFF" ><b>Message Alert<br>
- <a href="<?=base_url();?>alarm">View Details</a> -</b></font>
</td>

<td>
<div id="button_up_alarm_data" style="position:absolute; left:0px; top:421px;">
<a href="javascript:hide_div_alarm_data()">
<img src="<?=base_url();?>assets/farrasindo/images/up.png" height="15" width="15">
</a></div>
</td>

<td>
<div id="button_down_alarm_data" style="position:absolute; left:0px; top:421px; visibility:hidden">
<a href="javascript:show_div_alarm_data()">
<img src="<?=base_url();?>assets/farrasindo/images/down.png" height="15" width="15">
</a>
</div>
</td>

<tr>
<td id="alarm_data"style="position:absolute; left:0px; top:447px; width:202px; font-size:11px">


 <?php


    echo $rows_alarm["user_name"];
    echo "<br />";
    echo "Vehicle No :". " " .$rows_valarm["vehicle_name"]. " " .$rows_valarm["vehicle_no"];
    echo "<br />";
    echo "Date / Time:";
    echo "<br />";
    echo $t_format;
    echo "<br />";
    echo "Message :";
    echo "<br />";

    switch ($rows_v_alert)
    {
    case "35" :
    $message = "Geofence Outisde Alarm";
    echo $message;
    break;
    case "01" :
    $message = "SOS Emergency";
    echo $message;
    break;
    case "10" :
    $message = "Low Battery Alarm";
    echo $message;
    break;
    case "11":
    $message = "Overspeed Alarm";
    echo $message;
    break;
    case "12"  :
    $message = "Geofence Alarm";
    echo $message;
    break ;
    case "15" :
    $message = "No GPS Signal Alarm";
    echo $message;
    break;
    case "20" :
    $message = "Cut Off Power Supply";
    echo $message;
    break;
    case "53" :
    $message = "Geofence Outside Alarm";
    echo $message;
    break;
    }

     ?>



  </td>
  </tr>
  </table>
  </div>
  </div>

  <div id="paneldiv" class="olControlNavToolbar"></div>
  </div>
  <div id="layerswitcher" style="position: absolute; font-size:10px; font-weight: bold; color: black; overflow: auto; width:220px; height:100; font-size:10px; top: 53px; visibility: visible" >
  </div>
  <div id="map"></div>
</body>
