<link rel="stylesheet" type="text/css" href="<?=base_url();?>assets/css/maps.css" />
<script type="text/javascript" src="<?=base_url();?>assets/js/openlayers/OpenLayers.js"></script> 
<script type="text/javascript" src="<?=base_url();?>assets/js/openlayers/lib/OpenLayers/Layer/OpenStreetMap.js"></script> 
<script src="<?php echo base_url();?>assets/js/jsblong/jquery.table2excel.js"></script>
<?php $key = $this->config->item("GOOGLE_MAP_API_KEY");
	
	if(isset($key) && $key != "") { ?>
		<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $key;?>" type="text/javascript"></script>
	<?php } else { ?>
		<script src="http://maps.google.com/maps/api/js?V=3.3&amp;sensor=false"></script> 
	<? } ?>




	<script>
		
		var map;
		
		var g_timeaddmarker = null;
		var g_imarker = 0;
		var g_vehicles = new Array();
		
		<?php for($i=0; $i < count($data); $i++) { ?>
		var ldata = new Array();
		ldata[0] = <?=$i+1?>;
		ldata[1] = '<?=$data[$i]->gps_longitude_real_fmt;?>';
		ldata[2] = '<?=$data[$i]->gps_latitude_real_fmt;?>';
		ldata[3] = '<?php echo "Archive"; ?>';
		g_vehicles.push(ldata);
		<?php } ?>
		
		
		<?php if (isset($initmap)) echo $initmap; ?>
		
		jQuery(document).ready(
			function()
			{
					init();

					var center = new OpenLayers.LonLat(<?=$data[0]->gps_longitude_real_fmt;?>, <?=$data[0]->gps_latitude_real_fmt;?>);

					map.setCenter(center.transform
						(
                    		new OpenLayers.Projection("EPSG:4326"),
                    		map.getProjectionObject()
                		), <?=$this->config->item('zoom_history')?>);


				track("<?php echo $this->lang->line('ltrack'); ?>: <?php echo "Archive"; ?>");


				var ref = "";
				
				
				ref += '<a href="<?=base_url();?>map/historyfull?dummy=on&vehicle=<?php echo "Archive";?>';
				ref += "&sessionid=<?php echo $uniqid; ?>";

				
				
				addMarkerTimer();
			}
		);
		
		function addMarkerTimer()		
		{
			if (g_timeaddmarker != null)
			{
				clearTimeout(g_timeaddmarker);
			}
			
			if (g_imarker >= g_vehicles.length)
			{
				return;
			}
			
			var ldata = g_vehicles[g_imarker];
			addMarker(ldata[0], ldata[1], ldata[2], ldata[3]);
			
			g_imarker++;			
			g_timeaddmarker = setTimeout("addMarkerTimer()", 500);
		}

	    function track(no)
	    {
			var lgpx = new OpenLayers.Layer.GML(no, "<?=base_url()?>map/gpx/<?php echo $uniqid; ?>", 
				{
					format: OpenLayers.Format.GPX,
					style: {strokeColor: "#FF0000", strokeWidth: 4, strokeOpacity: 0.9},
					projection: new OpenLayers.Projection("EPSG:4326")
				}
			);
			map.addLayer(lgpx);	    	
	    }        

       function addMarker(no, lng, lat, id)
        {
			var kml_tracker5 = new OpenLayers.Layer.GML
			(
    			no,
    			"<?=base_url()?>map/kmllastcoord/"+lng+"/"+lat+"/"+id+"/off/on",
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

			var center = new OpenLayers.LonLat(lng, lat);
			center.transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject());

			kml_tracker5.size = new OpenLayers.Size(-11, -30);

			popup = new OpenLayers.Popup.FramedCloud(
				"featurePopup"
				, center
				, new OpenLayers.Size(48, 33)
				, "<div id='pup'>" + no + "</div>"
				, kml_tracker5
				, false,
                null
			);

            popup.autoSize = true;
            popup.calculateRelativePosition = function(){
                   return 'tr';
               }
            var popup = map.addPopup(popup);
                        
        }
	</script>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
<?=$navigation;?>
<div id="map" style="border: 1px #000000 solid; width: 100%; height: 700px;"></div>
<div id="main" style="margin: 20px;">
	<section class="grid_12">
		<div class="block-border">
			
		</div>
	</section>
</div>
</div>

