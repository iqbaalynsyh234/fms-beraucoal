<style style type="text/css" media="screen">
	
	#map
    {
       top:25%;
	   width:70%;
    }
	
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
        		updateLocation_realtime();
        		showgeofence('<?=$data->vehicle_device;?>');        		
        		map.controls[2].maximizeControl();
        	}
        );
        
		<?=$initmap;?>
		<?=$updateinfo_realtime;?>
       	
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
		
		function update_realtime(r)
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
			
			/* if (r.vehicle.vehicle_no)
			{
				jQuery("#myvehicle").html(r.vehicle.vehicle_no);
			}
			
			if (r.vehicle.gps.gps_date_fmt)
			{
				jQuery("#mydate").html(r.vehicle.gps.gps_date_fmt + " " + r.vehicle.gps.gps_time_fmt);
			}
			
			if (r.vehicle.gps.gps_latitude_real_fmt)
			{
				jQuery("#myposition").html('<a target="_blank" href="http://maps.google.com/maps?q='+r.vehicle.gps.gps_latitude_real_fmt+','+r.vehicle.gps.gps_longitude_real_fmt+'"><font color="#0000ff">'+r.vehicle.gps.georeverse.display_name+'</font></a>');
			}
			
			if (r.vehicle.gps.gps_speed_fmt) 
			{
				jQuery("#myspeed").html(r.vehicle.gps.gps_speed_fmt+ " kph");
			}
			
			if (r.vehicle.gps.gps_status == "A")
			{
				jQuery("#mysignal").html("OK");
			}
			else
			{
				jQuery("#mysignal").html("NOT OK");
			}
    		 */
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
			jQuery('#dialog').dialog('option', 'height', 420);
			jQuery('#dialog').dialog('option', 'modal', false);
			jQuery('#dialog').dialog('open');		        	
        }

		function showGoogleEarth(txt)
		{
			
			jQuery('#dialog').dialog('close');
			showdialog('<h3><?=$this->lang->line('lgoogle_earth_network_link_desc')?></h3>' + txt, '<?=$this->lang->line('lgoogle_earth_network_link')?>', 1000, 150);
		}
        
        function show_navigation()
        {
            jQuery("#nav").show();
            jQuery("#hide_nav").show();
            jQuery("#show_nav").hide();
        }
        
        function hide_navigation()
        {
            jQuery("#nav").hide();
            jQuery("#hide_nav").hide();
            jQuery("#show_nav").show();
        }
        
        function show_info_traffic()
        {
            jQuery("#info_traffic").show();
            jQuery("#hide_info_traffic").show();
            jQuery("#show_info_traffic").hide();
        }
        
        function hide_info_traffic()
        {
            jQuery("#info_traffic").hide();
            jQuery("#hide_info_traffic").hide();
            jQuery("#show_info_traffic").show();
        }
		
		
		var vectors = null;

    </script> 
	<style>
		#myinfo {
			position:absolute;
			bottom:0px;
			background-color:white;
			width:100%;
		}
	</style>
	<div id="map" style="position: absolute;"></div>
	<!--
	<div id="myinfo">
		<small><strong>Last Information : <span id="myvehicle"></span></strong></small>
		<table>
			<tr>
				<td>
					<small>Date</small>
				</td>
				<td>:</td>
				<td><small><span id="mydate"></span></small></td>
			</tr>
			<tr>
				<td>
					<small>Position</small>
				</td>
				<td>:</td>
				<td><small><span id="myposition"></span></small></td>
			</tr>
			<tr>
				<td><small>Speed</small></td>
				<td>:</td>
				<td><small><span id="myspeed"></span></small></td>
			</tr>
			<tr>
				<td><small>Status</small></td>
				<td>:</td>
				<td><small><span id="mysignal"></span></small></td>
			</tr>
		</table>
	</div>
	-->
	
       
	   