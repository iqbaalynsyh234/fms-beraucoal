<?php
	$ishowmap = ($this->sess->user_type == 2) || $this->config->item('mapinhome');
?>
<?php if ($ishowmap) { ?>
<script type="text/javascript" src="<?=base_url();?>assets/js/openlayers/OpenLayers.js"></script>
<script type="text/javascript" src="<?=base_url();?>assets/js/openlayers/lib/OpenLayers/Layer/OpenStreetMap.js"></script>
<script type="text/javascript" src="<?=base_url();?>assets/js/openlayers/lib/OpenLayers/Layer/GoogleAPI.js"></script>
<?php } ?>
<script>
	var gidx = 0;
	var gmarker = new Array();
	var map = null;
	var layers = new Array();
	var listcontrol = null;

	jQuery(document).ready(
		function()
		{
			showclock();

			<?php if ($ishowmap) { ?>
				jQuery("#map").hide();
			<?php }  ?>
			updateLocationEx();
			<?php if (isset($_POST['field'])) { ?>
				jQuery("#field").val('<?=$_POST['field']?>')
			<?php } ?>
			<?php if (isset($_POST['keyword'])) { ?>
				jQuery("#keyword").val('<?=$_POST['keyword']?>')
			<?php } ?>

			field_onchange();
                        showmap(true);
		}
	);

	<?php if ($ishowmap) { ?>
		<?php if (isset($initmap)) echo $initmap; ?>
	<?php }  ?>

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


		// change peta

	<?php if ($ishowmap) { ?>
		var idx = removeMarker(vid);
		addMarker(idx, (gidx+1) + ' ' + r.vehicle.vehicle_no, r.vehicle.gps.gps_longitude_real, r.vehicle.gps.gps_latitude_real, vid , r.vehicle.gps.car_icon, r.vehicle.vehicle_device_name, r.vehicle.vehicle_device_host, r);
	<?php }  ?>

		jQuery("[id='timestamp"+r.vehicle.vehicle_device_name+"_"+r.vehicle.vehicle_device_host+"']").html(r.vehicle.gps.gps_timestampori);
		jQuery("[id='datetime"+vid+"']").html(r.vehicle.gps.gps_date_fmt + " " + r.vehicle.gps.gps_time_fmt);
		jQuery("[id='position"+vid+"']").html(r.vehicle.gps.georeverse.display_name);
		jQuery("[id='coord"+vid+"']").html(r.vehicle.gps.gps_latitude_real_fmt+","+r.vehicle.gps.gps_longitude_real_fmt);
		jQuery("[id='speed"+vid+"']").html(r.vehicle.gps.gps_speed_fmt+ " kph");
		jQuery("[id='map"+vid+"']").show();

		if (r.vehicle.gps.css_delay)
		{
			jQuery("[id='tr"+vid+"']").contents('td').css("background-color", r.vehicle.gps.css_delay[1]);
			jQuery("[id='tr"+vid+"']").contents('td').css("color", r.vehicle.gps.css_delay[2]);
		}

		jQuery("#pointer"+gidx).hide();
		gidx = gidx+1;
		if (gidx >= <?=count($data);?>)
		{
			gidx = 0;
		}

		var n = jQuery("#devname"+gidx).val();
		var h = jQuery("#devhost"+gidx).val();

		jQuery("#pointer"+gidx).show();

		glasttime = jQuery("#timestamp"+n+"_"+h).html();
		return n+'@'+h;

	}

	<?php if ($ishowmap) { ?>
	    function removeMarker(id)
	    {
	    	for (var i = 0; i < gmarker.length; i++)
	    	{
	    		if (gmarker[i][0] != id) continue;
	    		if (map)
	    		{
	    			if (gmarker[i][6]) map.removePopup(gmarker[i][6]);
	    			map.removeLayer(gmarker[i][1]);
	    		}
	    		return i;
	    	}

	    	return -1;
	    }

        function addMarker(idx, no, lng, lat, id, car, vename, vehost, r)
        {
        	var popup = null;
			var kml_tracker5 = new OpenLayers.Layer.GML
			(
    			no,
    			"<?=base_url()?>map/kmllastcoord/"+lng+"/"+lat+"/"+id+"/"+car+"/on1/"+r.vehicle.gps.css_delay_index,
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

            kml_tracker5.events.on({
                'featureselected': kml_tracker5_onFeatureSelect
            });

            function kml_tracker5_onFeatureSelect(evt)
            	{
            		info(r);
            		if (poiSelectControl)
            		{
            			poiSelectControl.unselect(evt.feature);
            		}
            		else
            		{
            			listcontrol.unselect(evt.feature);
            		}
            	}


			if (map)
			{
				var center = new OpenLayers.LonLat(lng, lat);
				center.transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject());

				var nos = no.substring(2);
                //popup = new OpenLayers.Popup.AnchoredBubble("featurePopup",center, new OpenLayers.Size(40, 20), "<font style='font-size: 10px; color: #000000;'><b>" + nos + "</b></font>", null, false, null);
                //popup = new OpenLayers.Popup("featurePopup",center, new OpenLayers.Size(20,20), "<font style='font-size: 10px; font-color: #000000;'><b>" + no + "</b></font>", null, false, null);
                //popup.autoSize = true;
                //popup.setBackgroundColor("#80FFFF");
                //popup.setOpacity(0.6);
                //map.addPopup(popup);
				map.addLayer(kml_tracker5);
			}
			else
			{
				popup = null;
			}

			if (idx < 0)
			{
				var data = new Array(10);
				data[0] = id;
				data[1] = kml_tracker5;
				data[2] = no;
				data[3] = lng;
				data[4] = lat;
				data[5] = car;
				data[6] = popup;
				data[7] = vename;
				data[8] = vehost;
				data[9] = r;

				gmarker.push(data);
				layers.push(kml_tracker5);
			}
			else
			{
				gmarker[idx][1] = kml_tracker5;
				gmarker[idx][2] = no;
				gmarker[idx][3] = lng;
				gmarker[idx][4] = lat;
				gmarker[idx][5] = car;
				gmarker[idx][6] = popup;
				gmarker[idx][7] = vename;
				gmarker[idx][8] = vehost;
				gmarker[idx][9] = r;

				layers[idx] = kml_tracker5;
			}

			if (map)
			{
				if (poiSelectControl)
				{
					if (listcontrol)
					{
						map.removeControl(listcontrol);
						listcontrol = null;

						var layer1 = poiSelectControl.layer;
						layers.push(layer1);
					}

					poiSelectControl.setLayer(layers);

				}
				else
				{

					if (! listcontrol)
					{
						listcontrol = new OpenLayers.Control.SelectFeature(kml_tracker5);
						map.addControl(listcontrol);
						listcontrol.activate();
					}
					else
					{
						listcontrol.setLayer(layers);
					}

				}

			}
        }

        function info(r)
        {
        	var html = r.info;
        	html = html.replace("tablelist1", "tablelist");

        	jQuery('#dialog').html(html);
			jQuery('#dialog').dialog('option', 'title', "<?=$this->lang->line('llast_info');?>: " + r.vehicle.vehicle_no + " - " + r.vehicle.vehicle_name);
			jQuery('#dialog').dialog('option', 'width', 700);
			jQuery('#dialog').dialog('option', 'height', 400);
			jQuery('#dialog').dialog('option', 'modal', false);
			jQuery('#dialog').dialog('open');
        }

	    function setcenter(lat, lng)
	    {
	    	var center = new OpenLayers.LonLat(lng, lat);
			map.setCenter(center.transform(
                    			new OpenLayers.Projection("EPSG:4326"),
                    			map.getProjectionObject()
                			), <?=$this->config->item('zoom_realtime')?>);
           jQuery("#dialog").dialog("close");
	    }

	    function showmap(ismap)
	    {
	    	if (ismap)
	    	{
	    		jQuery("#tblrealtime").show();
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
	    				layers = new Array();
	    				listcontrol = null;
	    				init();
    					for(var i=0; i < gmarker.length; i++)
    					{
    						addMarker(i, gmarker[i][2], gmarker[i][3], gmarker[i][4], gmarker[i][0], gmarker[i][5], gmarker[i][7], gmarker[i][8], gmarker[i][9]);
    					}
	    			}
	    		);

	    		return;
	    	}

    		jQuery("#tblrealtime").show("slow");
	    	jQuery("#map").show("slow");
	    }

	<?php } ?>

	function order(by)
	{
		if (by == jQuery("#sortby").val())
		{
			if (jQuery("#orderby").val() == "asc")
			{
				jQuery("#orderby").val("desc");
			}
			else
			{
				jQuery("#orderby").val("asc");
			}
		}
		else
		{
			jQuery("#orderby").val('asc')
		}

		jQuery("#sortby").val(by);
		document.frmsearch.submit();
	}

	function showGoogleEarth(txt)
	{

		showdialog('<h3><?=$this->lang->line('lgoogle_earth_network_link_desc')?></h3>' + txt, '<?=$this->lang->line('lgoogle_earth_network_link')?>', 1000, 150);
	}

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

			  <span id='tblrealtime'><br>
              <table align="center" width="100%"  ><tr>
                  <td width="100%" align="center" valign="middle" bgcolor="#FF6600" height="20">
       <font size="1" color="#FFFFFF"><b>VEHICLE <u> LIST :</u></b></font></td></tr></table>
              <div style="border:0px red solid;  width:200px; height:150px; overflow:auto;">
                <table align="center" width="100%" >
					<tr><td><a href="<?=base_url();?>trackers"><font size="1"><b>ALL(<?=count($data);?>)</b></font></a></td></tr>

					<form id="frmvehicle">
						<?php
						for($i=0; $i < count($data); $i++)
						{
						?>
						<input type="hidden" name="devid[]" id="devid<?=$i;?>" value="<?=$data[$i]->vehicle_id;?>" />
						<input type="hidden" name="devname[]" id="devname<?=$i;?>" value="<?=$data[$i]->vehicle_device_name;?>" />
						<input type="hidden" name="devhost[]" id="devhost<?=$i;?>" value="<?=$data[$i]->vehicle_device_host;?>" />
						<?php
						}
						?>
					</form><tr><td>
					<table width="25%"  class="tablelist">

						<tbody>
						<?php
						for($i=0; $i < count($data); $i++)
						{
						?>
							<tr <?=($i%2) ? "class='odd'" : "";?> id="tr<?=$data[$i]->vehicle_id;?>">
								<td width="1%"><div id="pointer<?=$i?>" style="display: none; "text-align: right;">&#9654;</div></td>
								<td><font size="1"><?=$i+1?></font></td>
								<?php if ($this->sess->user_type != 2) { ?>
								<td><?=$data[$i]->user_name;?></td>
								<?php } ?>
								<td><font size="1"><a href="<?=base_url(); ?>map/realtime/<?=$data[$i]->vehicle_device_name;?>/<?=$data[$i]->vehicle_device_host;?>"><?php echo $data[$i]->vehicle_name. "".$data[$i]->vehicle_no;?></a></font></td>

								<td>
									<span id="map<?=$data[$i]->vehicle_id;?>" style="display: none;">

									<a href="#" onclick="javascript:showGoogleEarth('<?=base_url()?>map/googleearth/<?=$this->sess->user_login?>/<?=substr($this->sess->user_pass, 1)?>/<?=$data[$i]->vehicle_device_name?>/<?=$data[$i]->vehicle_device_host?>')"><img src="<?=base_url();?>assets/images/zoom-world-mini.png" border="0" alt="<?=$this->lang->line("lgoogle_earth"); ?>" title="<?=$this->lang->line("lgoogle_earth"); ?>"></a>
									</span>
								</td>
							</tr>
					  <span id="timestamp<?=$data[$i]->vehicle_device_name;?>_<?=$data[$i]->vehicle_device_host;?>" style="display: none;">0</span>
						<?php
						}
						?>
						</tbody>
					</table></td></tr>
                </table>
			</div>
			  </span>
</div>
<table width="100%" align="left">
    <tr>
  <td width="100%" align="center" valign="center" bgcolor="#FF6600" height="20"><font size="1" color="#FFFFFF"><b>VEHICLE <u> INFO </u></b></font></td></tr>
   </table>
<div div style="border:0px red solid;  width:200px; height:220px; overflow:auto;">
<table width="100%" align="left">
<tr>
  <td  width="100%" align="center"><?php
						for($i=0; $i < count($data); $i++)
						{
						?>
							<tr <?=($i%2) ? "class='odd'" : "";?> id="tr<?=$data[$i]->vehicle_id;?>">

								<td width="1%"><font size="1"><b>No :</b><?=$i+1?></font></td></tr>
								<?php if ($this->sess->user_type != 2) { ?>
								<tr><td><?=$data[$i]->user_name;?></td></tr>
								<?php } ?>
								<tr><td><font size="1"><b>Vehicle Name :</b> <a href="<?=base_url(); ?>map/realtime/<?=$data[$i]->vehicle_device_name;?>/<?=$data[$i]->vehicle_device_host;?>"><?php echo $data[$i]->vehicle_name. " ".$data[$i]->vehicle_no;?></a></font></td></tr>
                                                                <tr><td><font size="1"><b>Vehicle Card No :</b> <?=$data[$i]->vehicle_card_no;?></font></td></tr>
                                                                <tr><td><font size="1"><b>Date Time :</b></font></td></tr>
                                                                <tr> <td id="datetime<?=$data[$i]->vehicle_id;?>" style="font-size:10px"></td></tr>
                                                                <tr><td><font size="1"><b>Position :</b></font></td></tr>
                                                                <tr><td id="position<?=$data[$i]->vehicle_id;?>" style="font-size:10px"></td></tr>
								<tr><td><font size="1"><b>Coordinate :</b></font></td></tr>
                                                                <tr><td id="coord<?=$data[$i]->vehicle_id;?>" style="font-size:10px"></td></tr>
								<tr><td><font size="1"><b>Speed :</b></font></td></tr>
                                                                <tr><td id="speed<?=$data[$i]->vehicle_id;?>" style="font-size:10px"></font></td></tr>
                                                                <tr>
								<td>
									<span id="map<?=$data[$i]->vehicle_id;?>" style="display: none;">
                                                                            <font size="1"><b><a href="#" onclick="javascript:showGoogleEarth('<?=base_url()?>map/googleearth/<?=$this->sess->user_login?>/<?=substr($this->sess->user_pass, 1)?>/<?=$data[$i]->vehicle_device_name?>/<?=$data[$i]->vehicle_device_host?>')">Show Google Earth</a></b></font><br>
									</span><br>
								</td></tr>
							</tr>
					  <span id="timestamp<?=$data[$i]->vehicle_device_name;?>_<?=$data[$i]->vehicle_device_host;?>" style="display: none;">0</span>
						<?php
						}
						?></tr></td>

</table>
</div>
    <table align="left" width="100%" >
        <tr><td width="100%" align="center" valign="center" bgcolor="#FF6600" height="20"><font size="1" color="#FFFFFF" ><b>MESSAGE <u> ALERT </u></b></font></td></tr>
        <tr>
  <td align="right"><a href="<?=base_url();?>alarm"><font size="1"><b>View Details</b></font></a></tr></td></tr>
    </table>


</div>

<?php if ($ishowmap) { ?>
<div id="map"></div>
<?php } ?>	