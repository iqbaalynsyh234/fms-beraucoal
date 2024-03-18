	<link rel="stylesheet" type="text/css" href="<?=base_url();?>assets/css/maps.css" />
	<script type="text/javascript" src="<?=base_url();?>assets/js/openlayers/OpenLayers.js"></script>
	<script type="text/javascript" src="<?=base_url();?>assets/js/openlayers/lib/OpenLayers/Layer/OpenStreetMap.js"></script>
	
	<?php
	$key = $this->config->item("GOOGLE_MAP_API_KEY");
	
	if(isset($key) && $key != "") { ?>
		<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $key;?>" type="text/javascript"></script>
	<?php } else { ?>
		<script src="http://maps.google.com/maps/api/js?V=3.3&amp;sensor=false"></script> 
	<? } ?>

    <script>
        var map;
        jQuery(document).ready(
        	function()
        	{
        		showclock();
        		init();

        		<?php if (! isset($_GET['lnglat'])) { ?>
        			return;
        		<?php } ?>

        		<?php if (count($_GET['lnglat'])) {
        				list($lng, $lat) = explode(",", $_GET['lnglat'][0]);
        		?>

        		var center = new OpenLayers.LonLat(<?=$lng;?>, <?=$lat;?>);
				map.setCenter(center.transform
						(
                    		new OpenLayers.Projection("EPSG:4326"),
                    		map.getProjectionObject()
                		), <?=$this->config->item('zoom_history')?>);
        		<?php } ?>

				p = "";
				<?php for($i=0; $i < count($_GET['lnglat']); $i++) {
						list($lng, $lat) = explode(",", $_GET['lnglat'][$i]);
				?>
				p += "&lon[]=<?=$lng;?>&lat[]=<?=$lat;?>";
				<?php } ?>
				
				track("<?php echo $this->lang->line('ltrack'); ?>: <?php echo $row->vehicle_no; ?> <?php echo $row->vehicle_name; ?>", p);
				
				<?php for($i=0; $i < count($_GET['lnglat']); $i++) {
						list($lng, $lat) = explode(",", $_GET['lnglat'][$i]);
				?>
					addMarker('<?=$i+1?>', <?=$lng;?>, <?=$lat;?>, <?=$_GET['vehicle'];?>);
					p += "&lon[]=<?=$lng;?>&lat[]=<?=$lat;?>";
				<?php } ?>				
        	}
        );

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

			kml_tracker5.size = new OpenLayers.Size(-11, -30)

			var center = new OpenLayers.LonLat(lng, lat);
			center.transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject());

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
        
	    function track(no, p)
	    {
			var lgpx = new OpenLayers.Layer.GML(no, "<?=base_url()?>map/gpx?"+p, 
				{
					format: OpenLayers.Format.GPX,
					style: {strokeColor: "#FF0000", strokeWidth: 4, strokeOpacity: 0.9},
					projection: new OpenLayers.Projection("EPSG:4326")
				}
			);
			map.addLayer(lgpx);	    	
	    }        


		<?=$initmap;?>
    </script>
	<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;">
 		<?=$navigation;?>
	</div>
	<div id="map" style="position: absolute;top:170px;" ></div>
	
