	<link rel="stylesheet" type="text/css" href="<?=base_url();?>assets/css/maps.css" />
	<link href="<?php echo base_url();?>assets/newfarrasindo/css/mini3537.css?files=reset,common,form,standard,960.gs.fluid,simple-lists,block-lists,planning,table,calendars,wizard,gallery" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="<?=base_url();?>assets/js/jquery-ui-1.7.2.custom/development-bundle/themes/ui-lightness/jquery-ui-1.7.2.custom.css" type="text/css" media="all" /> 
	<link rel="stylesheet" href="<?=base_url();?>assets/js/jquery-ui-1.7.2.custom/development-bundle/themes/ui-lightness/ui.theme.css" type="text/css" media="all" />
	<script type="text/javascript" src="<?=base_url();?>assets/js/openlayers/OpenLayers.js"></script> 
	<script src="<?=base_url();?>assets/js/jquery-ui-1.7.2.custom/js/jquery-1.3.2.min.js" type="text/javascript"></script> 
	<script src="<?=base_url();?>assets/js/jquery-ui-1.7.2.custom/js/jquery-ui-1.7.2.custom.min.js" type="text/javascript"></script> 
	<script type="text/javascript" src="<?=base_url();?>assets/js/openlayers/lib/OpenLayers/Layer/OpenStreetMap.js"></script>
	<script src="<?=base_url();?>assets/js/jquery-ui-1.7.2.custom/development-bundle/ui/ui.datepicker.js" type="text/javascript"></script> 
	<script type="text/javascript" src="<?=base_url();?>assets/js/dropdownHover.js"></script>
    <script src="<?php echo base_url();?>assets/newfarrasindo/js/libs/modernizr.custom.min.js"></script>  		
	<script src="http://maps.google.com/maps/api/js?V=3.3&amp;sensor=false"></script> 
	<style>
	.olControlLayerSwitcher { font-size: 12px; width: 440px; }
	.olLayerGoogleCopyright { display: none; }
	.olLayerGooglePoweredBy{ display:none; }
	#dvalert_geofence{ height: 90px; width:250px; -moz-border-radius-bottomright: 15px; -moz-border-radius-bottomleft: 15px;
	-moz-border-radius-topleft: 15px; -moz-border-radius-topright: 15px; border-bottom-right-radius: 15px;
	border-bottom-left-radius: 15px; border-top-right-radius: 15px; border-top-left-radius: 15px; }
	</style>
    <script> 
        var map;
        var gmarker = new Array();
        var glastinfo = null;
        var glastr = null;
        var ggpx = null;
        
        jQuery(document).ready(
        	function()
        	{
        		init();
        		gtimer = <?=$this->config->item('timer_realtime')?>;
        		updateLocation('<?=$data->vehicle_device;?>');
        		showgeofence('<?=$data->vehicle_device;?>');        		
        		map.controls[2].maximizeControl();
        		jQuery('#dialog').dialog({ autoOpen: false });
				jQuery(".ui-dialog .ui-dialog-titlebar").css("color", "white");
				
        	}
        );
        
		<?=$initmap;?>        
        <?=$updateinfo;?>
       	
       	function showgeofence(vehicle)
       	{
       		jQuery.post("<?=base_url()?>geofence/getlist", {vehicle: vehicle}, 
       			function(r)
       			{
       				if (r.error) return;
       				
       				vectors = new OpenLayers.Layer.Vector("Vector Layer");
       				vectors.displayInLayerSwitcher = false;
       				map.addLayer(vectors);
       				
       				for(var i=0; i < r.geofence.length; i++)
       				{
						var style_green =
						{
							strokeColor: "#f49440",
							strokeOpacity: 0.6,
							strokeWidth: 2,
							fillColor: "#f6c79a",
							fillOpacity: 0.4,
							fontSize: '11px',
							label:r.geofence[i].geofence_name
						};       				
						vectors.style = style_green;
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
				map.zoomToExtent(bounds);
			}
			
		}        
		function update(r)
		{
			if (! r.vehicle.gps) 
			{
				if (r.info == "expired")
				{
					alert("<?php echo $this->lang->line('lvehicle_expired'); ?>");
					location = "<?php echo base_url(); ?>";
					return false;
				}
				return false;
			}
			
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
			ggpx.displayInLayerSwitcher = false;
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
				p  = "lon[]="+glastr.vehicle.gps.gps_longitude_real+"&lat[]="+glastr.vehicle.gps.gps_latitude_real;
				p += "&lon[]="+glastinfo.vehicle.gps.gps_longitude_real+"&lat[]="+glastinfo.vehicle.gps.gps_latitude_real;
				track(no, p);
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
			jQuery('#dialog').dialog('option', 'height', 250);
			jQuery('#dialog').dialog('option', 'modal', false);
			jQuery('#dialog').dialog('open');		        	
        }

		var vectors = null;

    </script> 
	<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%; height:100%"> 
	<div id="dialog" style='font-size: 12px; font-face: Tahoma;'></div>
		<center>
		<table width="100%">
			<tr>
				<td style="text-align:center;">TUPPERWARE DISTRIBUTOR MONITORING VEHICLE</td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
				<td><div id="map" style="position: absolute;"></div></td>
			</tr>
		</table>
		</center>
	</div>
       