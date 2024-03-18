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
	$(document).ready(
		function()
		{
			showclock();
			jQuery("#map").hide();
		}
	);

	function dosetcenter(lat, lng)
	{
    	var center = new OpenLayers.LonLat(lng, lat);
		map.setCenter(center.transform(
                			new OpenLayers.Projection("EPSG:4326"),
                			map.getProjectionObject()
            			), <?=$this->config->item('zoom_realtime'); ?>);
	}

    function setcenter(lat, lng)
    {
    	if (! ismapshow)
    	{
    		var func = 'dosetcenter('+lat+','+lng+')';
    		showmap(func);
    	}
    }

    function showmap(callback)
    {
    	if (! ismapshow)
    	{
    		jQuery("#map").show("slow",
    			function()
    			{
        			if (map && poilayer)
        			{
        				map.removeLayer(poilayer);
        				poilayer = null;
        			}

    				if (map) map.destroy();
    				map = null;

    				init();
    				if (callback) eval(callback);
    			}
    		);

    		jQuery("#lblshowmap").html('<?=$this->lang->line('lhide_map');?>');

    		ismapshow = true;
    		return;
    	}

    	jQuery("#lblshowmap").html('<?=$this->lang->line('lshow_map');?>');
    	jQuery("#map").hide("slow");

    	ismapshow = false;
    }

	<?php if (isset($initmap)) echo $initmap; ?>

	var map = null;
	var ismapshow = false;

</script>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;">
<?=$navigation;?>
<div id="main" style="margin: 20px;">
<div class="block-border">
<p style="text-align: right" align="right">
	<!--[<a href="javascript:showmapex()"><font color="#0000ff"><span id='lblshowmap'><?=$this->lang->line('lshow_map');?></a></font></a>]-->
</p>
<?=$contentstreet?>
</div>
</div>
</div>
