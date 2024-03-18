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
                        location = "#atop";
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

		if (gidx == 0)
		{
			location = "#atop";
		}
		else
		if (gidx == gnscroll)
		{
			if (jQuery("#autoscroll").attr("checked"))
			{
				location = "#a"+gidx;
			}
		}
		else
		if ((gidx > gnscroll) && ((gidx-gnscroll)%10 == 0))
		{
			if (jQuery("#autoscroll").attr("checked"))
			{
				location = "#a"+gidx;
			}
		}

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
					if (r.info == "expired")
					{
						html  = "<i><font color='#ff0000'><?php echo $this->lang->line('lexpired'); ?> ("+r.vehicle.vehicle_active_date2_fmt+")</font></i>";
						html += "<br /><a href='<?php echo base_url(); ?>invoice'><font color='#0000ff'>[ <?php echo $this->lang->line("linvoice"); ?> ]</font></a>";
						html += " <a href='javascript: contactus("+r.vehicle.vehicle_id+")'><font color='#0000ff'>[ <?php echo $this->lang->line("lcontact_us"); ?> ]</font></a>";
						<?php if (($this->sess->user_type == 1) || ($this->sess->user_type == 3 && $this->sess->agent_canedit_vactive == 1 && $this->sess->user_agent_admin == 1)) { ?>
						html += " <a href='javascript: renew("+r.vehicle.vehicle_id+")'><font color='#0000ff'>[ <?php echo $this->lang->line("l_renew"); ?> ]</font></a>";
						<?php } ?>
					}
					else
					{
						html = "<a href='<?php echo base_url();?>trackers/history/"+devices[0]+"/"+devices[1]+"'><i><font color='#8080FF'><?php echo $this->lang->line('lgo_to_history'); ?></font></i></a>";
					}
				}


				jQuery("[id='datetime"+vid+"']").html("");
				jQuery("[id='position"+vid+"']").html(html);
				jQuery("[id='coord"+vid+"']").html("");
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
            	
      var idx1;
			if (idx < 0)
			{
				idx1 = gmarker.length;
			}
			else
			{
				idx1 = idx;
			}

			if (map)
			{
				var center = new OpenLayers.LonLat(lng, lat);
				center.transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject());

               kml_tracker5.size = new OpenLayers.Size(-20, -45);
	        var nos = no.substring(2);
                popup = new OpenLayers.Popup.FramedCloud("featurePopup",
                center,
                new OpenLayers.Size(43, 33),
                "<div id='pup' onclick='javascript:showinfo("+idx1+")'>" + nos + "</div>",
                kml_tracker5,
                false,
                null);
               // popup = new OpenLayers.Popup("featurePopup",center, new OpenLayers.Size(20,20), "<font style='font-size: 10px; font-color: #000000;'><b>" + no + "</b></font>", null, false, null);
                popup.autoSize = false;
               // popup.setBackgroundColor("#CC6600");
               // popup.padding = "2";
               // popup.setOpacity(0.6);
                popup.calculateRelativePosition = function(){
                   return 'tr';
               }
                map.addPopup(popup);
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
          
        function showinfo(idx)
        {
        	info(gmarker[idx][9]);
        }
        
        
        function info(r)
        {
        	var html = r.info;
        	html = html.replace("tablelist1", "tablelist");

        	jQuery('#dialog').html(html);
			jQuery('#dialog').dialog('option', 'title', "<?=$this->lang->line('llast_info');?>: " + r.vehicle.vehicle_no + " - " + r.vehicle.vehicle_name);
			jQuery('#dialog').dialog('option', 'width', 600);
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


 function contactus(id)
	{
		showdialog();
		jQuery.post("<?php echo base_url(); ?>home/contactus/"+id, {},
			function(r)
			{
				showdialog(r.html, r.title);
			}
			, "json"
		);
	}

	function renew(id)
	{
		jQuery("#autoscroll").attr("checked", false)

		showdialog();
		jQuery.post("<?php echo base_url(); ?>vehicle/renew/"+id, {},
			function(r)
			{
				showdialog(r.html, r.title);
			}
			, "json"
		);
	}

	function paymentconfirmation(id)
	{
		showdialog();

		jQuery.post("<?php echo base_url(); ?>payment/confirmation/"+id, {},
			function(r)
			{
				showdialog(r.html, r.title);
			}
			, "json"
		);
	}
	gnscroll = 4;
</script>


<script>



function hide_div_vehicle_list() {

var objvlayer = document.getElementById('layerswitcher');

if (objvlayer.style.visibility == 'visible') {

if (document.getElementById) {
    document.getElementById('vehicle_list').style.visibility = 'hidden';
    document.getElementById('lbl_alert').style.top= '171px';
    document.getElementById('alarm_data').style.top= '197px';
    document.getElementById('button_down_alarm_data').style.top= '174px';
    document.getElementById('button_up_alarm_data').style.top= '174px';
    document.getElementById('button_down_vehicle_list').style.visibility = 'visible';
    document.getElementById('button_up_vehicle_list').style.visibility = 'hidden';
}
else {
    if (document.layers) {
        document.vehicle_list.visibility = 'hidden';
        document.lbl_alert.top= '171px';
        document.alarm_data.top= '197px';
        document.button_down_alarm_data.top= '174px';
        document.button_up_alarm_data.top= '174px';
        document.button_down_vehicle_list.visibility = 'visible';
        document.button_up_vehicle_list.visibility = 'hidden';
    }
    else {
        document.all.vehicle_list.style.visibility = 'hidden';
        document.all.lbl_alert.style.top= '171px';
        document.all.alarm_data.style.top= '197px';
        document.all.button_down_alarm_data.style.top = '174px';
        document.all.button_up_alarm_data.style.top = '174px';
        document.all.button_down_vehicle_list.style.visibility = 'visible';
        document.all.button_up_vehicle_list.style.visibility = 'hidden';
    }
}
}

if (objvlayer.style.visibility == 'hidden') {

if (document.getElementById) {
    document.getElementById('vehicle_list').style.visibility = 'hidden';
    document.getElementById('lbl_alert').style.top= '100px';
    document.getElementById('alarm_data').style.top= '127px';
    document.getElementById('button_down_alarm_data').style.top= '100px';
    document.getElementById('button_up_alarm_data').style.top= '100px';
    document.getElementById('button_down_vehicle_list').style.visibility = 'visible';
    document.getElementById('button_up_vehicle_list').style.visibility = 'hidden';
}
else {
    if (document.layers) {
        document.vehicle_list.visibility = 'hidden';
        document.lbl_alert.top= '100px';
        document.alarm_data.top= '127px';
        document.button_down_alarm_data.top= '100px';
        document.button_up_alarm_data.top= '100px';
        document.button_down_vehicle_list.visibility = 'visible';
        document.button_up_vehicle_list.visibility = 'hidden';
    }
    else {
        document.all.vehicle_list.style.visibility = 'hidden';
        document.all.lbl_alert.style.top= '100px';
        document.all.alarm_data.style.top= '127px';
        document.all.button_down_alarm_data.style.top = '100px';
        document.all.button_up_alarm_data.style.top = '100px';
        document.all.button_down_vehicle_list.style.visibility = 'visible';
        document.all.button_up_vehicle_list.style.visibility = 'hidden';
    }
}
}


}

function show_div_vehicle_list() {


var objvlayer = document.getElementById('layerswitcher');

if (objvlayer.style.visibility == 'visible') {

        if (document.getElementById) {
            document.getElementById('vehicle_list').style.visibility = 'visible';
            document.getElementById('lbl_alert').style.top= '370px';
            document.getElementById('alarm_data').style.top= '400px';
            document.getElementById('button_up_alarm_data').style.top= '375px';
            document.getElementById('button_down_alarm_data').style.top= '375px';
            document.getElementById('button_down_vehicle_list').style.visibility = 'hidden';
            document.getElementById('button_up_vehicle_list').style.visibility = 'visible';
        }
        else {
            if (document.layers) {
                document.vehicle_list.visibility = 'visible';
                document.lbl_alert.top= '370px';
                document.alarm_data.top= '400px';
                document.button_up_alarm_data = '375px';
                document.button_down_alarm_data = '375px';
                document.button_down_vehicle_list.visibility = 'hidden';
                document.button_up_vehicle_list.visibility = 'visible';

            }
            else {
                document.all.vehicle_list.style.visibility = 'visible';
                document.all.lbl_alert.style.top= '370px';
                document.all.alarm_data.style.top= '400px';
                document.all.button_up_alarm_data.style.top = '375px';
                document.all.button_down_alarm_data.style.top = '375px';
                document.all.button_down_vehicle_list.style.visibility = 'hidden';
                document.all.button_up_vehicle_list.style.visibility = 'visible';
            }
        }
}

if (objvlayer.style.visibility == 'hidden') {

        if (document.getElementById) {
            document.getElementById('vehicle_list').style.visibility = 'visible';
            document.getElementById('lbl_alert').style.top= '320px';
            document.getElementById('alarm_data').style.top= '350px';
            document.getElementById('button_up_alarm_data').style.top= '320px';
            document.getElementById('button_down_alarm_data').style.top= '320px';
            document.getElementById('button_down_vehicle_list').style.visibility = 'hidden';
            document.getElementById('button_up_vehicle_list').style.visibility = 'visible';
        }
        else {
            if (document.layers) {
                document.vehicle_list.visibility = 'visible';
                document.lbl_alert.top= '320px';
                document.alarm_data.top= '350px';
                document.button_up_alarm_data = '320px';
                document.button_down_alarm_data = '320px';
                document.button_down_vehicle_list.visibility = 'hidden';
                document.button_up_vehicle_list.visibility = 'visible';

            }
            else {
                document.all.vehicle_list.style.visibility = 'visible';
                document.all.lbl_alert.style.top= '320px';
                document.all.alarm_data.style.top= '350px';
                document.all.button_up_alarm_data.style.top = '320px';
                document.all.button_down_alarm_data.style.top = '320px';
                document.all.button_down_vehicle_list.style.visibility = 'hidden';
                document.all.button_up_vehicle_list.style.visibility = 'visible';
            }
        }
}

}

function hide_div_alarm_data() {

if (document.getElementById) {
    document.getElementById('alarm_data').style.visibility = 'hidden';
    document.getElementById('button_down_alarm_data').style.visibility = 'visible';
    document.getElementById('button_up_alarm_data').style.visibility = 'hidden';

}
else {
    if (document.layers) {
        document.alarm_data.visibility = 'hidden';
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


function hide_div_layer_map() {

var objvlist = document.getElementById('vehicle_list');

if (objvlist.style.visibility == 'visible') {
if (document.getElementById) {
    document.getElementById('layerswitcher').style.visibility = 'hidden';
    document.getElementById('paneldiv').style.visibility = 'hidden';
    document.getElementById('lbl_vehicle_list').style.top= '48px';
    document.getElementById('button_down_vehicle_list').style.top= '48px';
    document.getElementById('button_up_vehicle_list').style.top= '48px';
    document.getElementById('vehicle_list').style.top= '75px';
    document.getElementById('lbl_alert').style.top= '320px';
    document.getElementById('button_down_alarm_data').style.top= '320px';
    document.getElementById('button_up_alarm_data').style.top= '320px';
    document.getElementById('alarm_data').style.top= '350px';
    document.getElementById('button_down_layer_map').style.visibility= 'visible';
    document.getElementById('button_up_layer_map').style.visibility= 'hidden';

}
else {
    if (document.layers) {
        document.layerswitcher.visibility = 'hidden';
        document.paneldiv.visibility = 'hidden';
        document.lbl_vehicle_list.top= '171px';
        document.button_down_vehicle_list.top= '174px';
        document.button_up_vehicle_list.top= '174px';
        document.lbl_alert.top= '320x';
        document.alarm_data.top= '350px';
        document.button_down_alarm_data.top= '320px';
        document.button_up_alarm_data.top= '320px';
        document.button_down_layer_map.visibility= 'visible';
        document.button_up_layer_map.visibility= 'hidden';
    }
    else {
        document.all.layerswitcher.style.visibility = 'hidden';
        document.all.paneldiv.style.visibility = 'hidden';
        document.all.lbl_vehicle_list.style.top= '171px';
        document.all.button_down_vehicle_list.style.top= '174px';
        document.all.button_up_vehicle_list.style.top= '174px';
        document.all.lbl_alert.style.top= '320px';
        document.all.alarm_data.style.top= '350px';
        document.all.button_down_alarm_data.style.top = '320px';
        document.all.button_up_alarm_data.style.top = '320px';
        document.all.button_down_layer_map.style.visibility= 'visible';
        document.all.button_up_layer_map.style.visibility= 'hidden';
    }

}
}

if (objvlist.style.visibility == 'hidden') {
if (document.getElementById) {
    document.getElementById('layerswitcher').style.visibility = 'hidden';
    document.getElementById('paneldiv').style.visibility = 'hidden';
    document.getElementById('lbl_vehicle_list').style.top= '48px';
    document.getElementById('button_down_vehicle_list').style.top= '48px';
    document.getElementById('button_up_vehicle_list').style.top= '48px';
    document.getElementById('vehicle_list').style.top= '75px';
    document.getElementById('lbl_alert').style.top= '100px';
    document.getElementById('button_down_alarm_data').style.top= '100px';
    document.getElementById('button_up_alarm_data').style.top= '100px';
    document.getElementById('alarm_data').style.top= '130px';
    document.getElementById('button_down_layer_map').style.visibility= 'visible';
    document.getElementById('button_up_layer_map').style.visibility= 'hidden';

}
else {
    if (document.layers) {
        document.layerswitcher.visibility = 'hidden';
        document.paneldiv.visibility = 'hidden';
        document.lbl_vehicle_list.top= '171px';
        document.button_down_vehicle_list.top= '174px';
        document.button_up_vehicle_list.top= '174px';
        document.lbl_alert.top= '100px';
        document.alarm_data.top= '130px';
        document.button_down_alarm_data.top= '100px';
        document.button_up_alarm_data.top= '100px';
        document.button_down_layer_map.visibility= 'visible';
        document.button_up_layer_map.visibility= 'hidden';
    }
    else {
        document.all.layerswitcher.style.visibility = 'hidden';
        document.all.paneldiv.style.visibility = 'hidden';
        document.all.lbl_vehicle_list.style.top= '171px';
        document.all.button_down_vehicle_list.style.top= '174px';
        document.all.button_up_vehicle_list.style.top= '174px';
        document.all.lbl_alert.style.top= '100px';
        document.all.alarm_data.style.top= '130px';
        document.all.button_down_alarm_data.style.top = '100px';
        document.all.button_up_alarm_data.style.top = '100px';
        document.all.button_down_layer_map.style.visibility= 'visible';
        document.all.button_up_layer_map.style.visibility= 'hidden';
    }

}
}



}


function show_div_layer_map() {

var objvlist = document.getElementById('vehicle_list');

if (objvlist.style.visibility == 'visible') {

        if (document.getElementById) {
            document.getElementById('layerswitcher').style.visibility = 'visible';
            document.getElementById('paneldiv').style.visibility = 'visible';
            document.getElementById('lbl_vehicle_list').style.top= '121px';
            document.getElementById('button_down_vehicle_list').style.top= '125px';
            document.getElementById('button_up_vehicle_list').style.top= '125px';
            document.getElementById('vehicle_list').style.top= '152px';
            document.getElementById('lbl_alert').style.top= '370px';
            document.getElementById('alarm_data').style.top= '400px';
            document.getElementById('button_up_alarm_data').style.top= '375px';
            document.getElementById('button_down_alarm_data').style.top= '375px';
            document.getElementById('button_down_layer_map').style.visibility= 'hidden';
            document.getElementById('button_up_layer_map').style.visibility= 'visible';
        }
        else {
            if (document.layers) {
                document.layerswitcher.visibility = 'visible';
                document.paneldiv.visibility = 'visible';
                document.lbl_vehicle_list.top= '121px';
                document.button_down_vehicle_list.top= '125px';
                document.button_up_vehicle_list.top= '125px';
                document.vehicle_list.top= '152px';
                document.lbl_alert.top= '370px';
                document.alarm_data.top= '400px';
                document.button_up_alarm_data = '375px';
                document.button_down_alarm_data = '375px';
                document.button_down_layer_map.visibility= 'hidden';
                document.button_up_layer_map.visibility= 'visible';

            }
            else {
                document.all.layerswitcher.style.visibility = 'visible';
                document.all.paneldiv.style.visibility = 'visible';
                document.all.lbl_vehicle_list.style.top= '121px';
                document.all.button_down_vehicle_list.style.top= '125px';
                document.all.button_up_vehicle_list.style.top= '125px';
                document.all.vehicle_list.style.top= '152px';
                document.all.lbl_alert.style.top= '370px';
                document.all.lbl_alert.style.top= '370px';
                document.all.alarm_data.style.top= '400px';
                document.all.button_up_alarm_data.style.top = '375px';
                document.all.button_down_alarm_data.style.top = '375px';
                document.all.button_down_layer_map.style.visibility= 'hidden';
                document.all.button_up_layer_map.style.visibility= 'visible';
            }
        }
}

if (objvlist.style.visibility == 'hidden') {

        if (document.getElementById) {
            document.getElementById('layerswitcher').style.visibility = 'visible';
            document.getElementById('paneldiv').style.visibility = 'visible';
            document.getElementById('lbl_vehicle_list').style.top= '121px';
            document.getElementById('button_down_vehicle_list').style.top= '125px';
            document.getElementById('button_up_vehicle_list').style.top= '125px';
            document.getElementById('vehicle_list').style.top= '152px';
            document.getElementById('lbl_alert').style.top= '170px';
            document.getElementById('button_up_alarm_data').style.top= '170px';
            document.getElementById('button_down_alarm_data').style.top= '170px';
            document.getElementById('alarm_data').style.top= '200px';
            document.getElementById('button_down_layer_map').style.visibility= 'hidden';
            document.getElementById('button_up_layer_map').style.visibility= 'visible';
        }
        else {
            if (document.layers) {
                document.layerswitcher.visibility = 'visible';
                document.paneldiv.visibility = 'visible';
                document.lbl_vehicle_list.top= '121px';
                document.button_down_vehicle_list.top= '125px';
                document.button_up_vehicle_list.top= '125px';
                document.vehicle_list.top= '152px';
                document.lbl_alert.top= '370px';
                document.alarm_data.top= '400px';
                document.button_up_alarm_data = '375px';
                document.button_down_alarm_data = '375px';
                document.button_down_layer_map.visibility= 'hidden';
                document.button_up_layer_map.visibility= 'visible';

            }
            else {
                document.all.layerswitcher.style.visibility = 'visible';
                document.all.paneldiv.style.visibility = 'visible';
                document.all.lbl_vehicle_list.style.top= '121px';
                document.all.button_down_vehicle_list.style.top= '125px';
                document.all.button_up_vehicle_list.style.top= '125px';
                document.all.vehicle_list.style.top= '152px';
                document.all.lbl_alert.style.top= '370px';
                document.all.lbl_alert.style.top= '370px';
                document.all.alarm_data.style.top= '400px';
                document.all.button_up_alarm_data.style.top = '375px';
                document.all.button_down_alarm_data.style.top = '375px';
                document.all.button_down_layer_map.style.visibility= 'hidden';
                document.all.button_up_layer_map.style.visibility= 'visible';
            }
        }
}


}

function show_table(){
 var stable = document.getElementById('tbreal');

 if (stable.style.display == 'none') {
     document.getElementById('main').style.display = 'none';
     document.getElementById('paneldiv').style.display = 'none';
     document.getElementById('map').style.display = 'none';
     document.getElementById('alr').style.display = 'none';
     document.getElementById('button_up_alarm_data').style.display = 'none';
     document.getElementById('button_down_alarm_data').style.display = 'none';
     document.getElementById('tbreal').style.display = 'block';

 }
 else {
            if (document.layers) {
                document.main.display = 'none';
                document.paneldiv.display = 'none';
                document.map.display = 'none';
                document.alr.display = 'none';
                document.button_up_alarm_data.display = 'none';
                document.button_down_alarm_data.display = 'none';
                document.tbreal.display = 'none';
}
  else {
                document.all.main.style.display = 'none';
                document.all.paneldiv.style.display = 'none';
                document.all.map.style.display = 'none';
                document.all.alr.style.display = 'none';
                document.all.button_up_alarm_data.style.display = 'none';
                document.all.button_down_alarm_data.style.display = 'none';
                document.all.tbreal.style.display = 'none';

            }

}
}

function maphome(){
 var stable = document.getElementById('tbreal');

 if (stable.style.display == 'block') {
     document.getElementById('main').style.display = 'block';
     document.getElementById('paneldiv').style.display = 'block';
     document.getElementById('map').style.display = 'block';
     document.getElementById('alr').style.display = 'block';
     document.getElementById('button_up_alarm_data').style.display = 'block';
     document.getElementById('button_down_alarm_data').style.display = 'block';
     document.getElementById('tbreal').style.display = 'none';


 }
 else {
            if (document.layers) {
                document.main.display = 'none';
                document.paneldiv.display = 'none';
                document.map.display = 'none';
                document.alr.display = 'none';
                document.button_up_alarm_data.display = 'none';
                document.button_down_alarm_data.display = 'none';
                document.tbreal.display = 'none';
}
  else {
                document.all.main.style.display = 'none';
                document.all.paneldiv.style.display = 'none';
                document.all.map.style.display = 'none';
                document.all.alr.style.display = 'none';
                document.all.button_up_alarm_data.style.display = 'none';
                document.all.button_down_alarm_data.style.display = 'none';
                document.all.tbreal.style.display = 'none';

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


#pup {
    font-size: 10px;
    font-weight: bold;
    width:48px;
    height:33px;
    color:black;
    text-align:center;
    background-repeat:no-repeat;
    background-image: url('<?=base_url();?>assets/images/pup.png');
}


</style>

<table width="100%"><tr>
        <td align="center"><div style="position: absolute; left: 0px; padding: 0; z-index: 1000; background-color:#FF4500; width: 202px; height:35px;  ">
                <font size="1" color="white"><b>AGUNG PUTRA<br>
                    Monitoring Vehicle Division</b></font></div>
    </td>
        <td>
    <div style="position: absolute; left: 222px;margin: 0;  padding: 0; z-index: 1000; width: 82%;">
 		<?=$navigation;?></div></td>
    </tr>

</table>

<div style="margin-top: 12; padding: 0; z-index: 1000; width: 202px; left: 0px; right: auto">

<div id="main" style="position:absolute">
<span id='tblrealtime'><br>

<table align="center" width="100%">
<tr>
<td align="center" style="position:absolute; left:0px; top:20px; width:202px; height: 20px; background-color:black;">

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
<td>
<div id="layerswitcher" style="position: absolute; font-size:10px; font-weight: bold; color: black; overflow: auto; width:220px; height:100; font-size:10px; top: 37px; visibility: visible" >
</div>
</td>
</tr>

<tr>
<td id="lbl_vehicle_list"align="center" style="position:absolute; left:0px; top:121px; width:202px; height: 25px; background-color:black;">
<font size="1" color="#FFFFFF">
<b>Vehicle List :<br>
<a href="<?=base_url();?>trackers">
<b>ALL(<?=count($data);?>)</b></a>
    - <a href="javascript:show_table()">
        <b>( SHOW TABLE )</b></a>
</font>
</td>
<td>
<div id="button_up_vehicle_list" style="position:absolute; left:0px; top:125px;">
<a href="javascript:hide_div_vehicle_list()"><img src="<?=base_url();?>assets/farrasindo/images/up.png" height="15" width="15"></a>
</div>
</td>
<td>
<div id="button_down_vehicle_list" style="position:absolute; left:0px; top:125px; visibility:hidden">
<a href="javascript:show_div_vehicle_list()"><img src="<?=base_url();?>assets/farrasindo/images/down.png" height="15" width="15"></a>
</div>
</td>
</tr>
</table>

<div id="vehicle_list" style="position:absolute; left: 0px; top:152px; width:200px; height:223px; overflow:auto; visibility: visible">
<table align="center" width="100%" >
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

</form>
<tr><td>
<table width="25%"  class="tablelist">
<tbody>
<?php
for($i=0; $i < count($data); $i++)
{
?>
<tr <?=($i%2) ? "class='odd'" : "";?> id="tr<?=$data[$i]->vehicle_id;?>">

<td><font size="1"><?=$i+1?></font></td>
<?php if ($this->sess->user_type != 2) { ?>
<td><?=$data[$i]->user_name;?></td>
<?php } ?>
<td><font size="1"><a href="<?=base_url(); ?>map/realtime/<?=$data[$i]->vehicle_device_name;?>/<?=$data[$i]->vehicle_device_host;?>"><?php echo $data[$i]->vehicle_name. "".$data[$i]->vehicle_no;?></a></font>
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

<table id="alr" align="left" width="100%" >
<tr><td id="lbl_alert" align="center" style="position:absolute; left:0px; top:370px; width:198px; height: 25px; background-color:black;"><font size="1" color="#FFFFFF" ><b>Message Alert<br>
- <a href="<?=base_url();?>alarm">View Details</a> -</b></font></td></tr>
<tr>
<td id="alarm_data" style="position:absolute; top:400px; width:198px; height: 20px; font-size:11px">
<div id="alarm">

         <?php

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
     
     if ($result_v <> 0) {
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
  }
  else {
  	echo "<b>DATA ALERT KOSONG";
  }

     ?>
    </div>


    </td></tr>
    </table>

<div id="paneldiv" class="olControlNavToolbar"></div>
</div>

<div id="button_up_alarm_data" style="position:absolute; left:0px; top:375px;"">
<a href="javascript:hide_div_alarm_data()">
<img src="<?=base_url();?>assets/farrasindo/images/up.png" height="15" width="15">
</a>
</div>

<div id="button_down_alarm_data" style="position:absolute; left:0px; top:375px; visibility:hidden">
<a href="javascript:show_div_alarm_data()">
<img src="<?=base_url();?>assets/farrasindo/images/down.png" height="15" width="15">
</a>
</div>
<?php if ($ishowmap) { ?>
<div id="map"></div>
<?php } ?>
<div id="tbreal" style="position: absolute; top:50px; display:none; width:100%">
<span id='tblrealtime2'>
    <table width="100%" cellpadding="3">
        <tr><td width="20%">
	<font size="2"><b><?=$this->lang->line("llist_trackers"); ?> (<?=count($data);?>) - <a href="<?=base_url()?>trackers">Show Map</a></b></font>
            </td> </table>
		<form name="frmsearch" id="frmsearch" method="post" action="<?=base_url()?>trackers">
				<input type="hidden" name="offset" id="offset" value="" />
			<input type="hidden" id="sortby" name="sortby" value="<?=$sortby?>" />
			<input type="hidden" id="orderby" name="orderby" value="<?=$orderby?>" />

			<table width="100%" cellpadding="3" class="tablelist">
				<tr>
					<td width="10%"><font size="2"><b><?=$this->lang->line("lsearchby");?></b></font></td>
					<td>
						<select id="field" name="field" onchange="javascript:field_onchange()">
							<?php if ($this->sess->user_type != 2) { ?>
							<option value="user_login"><?=$this->lang->line("llogin");?></option>
							<option value="user_name"><?=$this->lang->line("lname");?></option>
							<option value="user_agent"><?=$this->lang->line("lagent");?></option>
							<?php } ?>
							<option value="vehicle"><?=$this->lang->line("lvehicle");?></option>
							<option value="device"><?=$this->lang->line("ldevice_id");?></option>
							<?php if ($this->sess->user_type != 2) { ?>
							<option value="vexpired"><?=$this->lang->line("lvehicle_expired");?></option>
							<option value="vactive"><?=$this->lang->line("lvehicle_active");?></option>
								<?php if (! $this->config->item('vehicle_type_fixed')) { ?>
							<option value="vehicle_type"><?=$this->lang->line("lvehicle_type");?></option>
								<?php } ?>
							<?php } ?>
						</select>
						<input type="text" name="keyword" id="keyword" value="" class="formdefault" />
						<select name='vehicle_type' id='vehicle_type' style="display: none;">
							<?php
								foreach($this->config->item("vehicle_type") as $key=>$val) {
									if (! in_array($key, $this->config->item('vehicle_type_visible'))) continue;
							?>
							<option value="<?php echo $key; ?>" <?php echo (isset($_POST['vehicle_type']) && ($key==$_POST['vehicle_type'])) ? " selected" : "";?>><?php echo $key; ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<tr>
					<td style="border: 0px;">&nbsp;</td>
					<td style="border: 0px;"><input type="submit" value="<?=$this->lang->line("lsearch");?>" /></td>
				</tr>
				<tr>
					<td style="border: 0px;">&nbsp;</td>
					<td style="border: 0px;"><input type="checkbox" name="autoscroll" id="autoscroll" value="1" /> <?=$this->lang->line("lauto_scroll"); ?></td>
				</tr>
			</table>
		</form>
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
					</form>
					<table width="100%" cellpadding="3" class="tablelist">
						<thead>
							<tr>
								<th width="1%">&nbsp;</td>
								<th width="2%" style="font-size:8pt"><b><?=$this->lang->line("lno"); ?></b></td>
								<?php if ($this->sess->user_type != 2) { ?>
								<th width="10%" style="font-size:8pt"><b><a href="#" onclick="javascript:order('user_name')"><?if ($sortby == 'user_name') { echo '<u>'; }?><?=$this->lang->line("lusername"); ?><?if ($sortby == 'user_name') { echo '</u>'; }?></b></a></th>
								<?php } ?>
								<th width="12%" colspan="2" style="font-size:8pt"><b><?=$this->lang->line("lvehicle"); ?></b></th>
								<th width="8%" style="font-size:8pt"><b><?=$this->lang->line("lcardno"); ?></b></th>
								<th width="14%" align="center" style="font-size:8pt"><b><?=$this->lang->line("ldatetime"); ?></b></th>
								<th style="font-size:8pt"><b><?=$this->lang->line("lposition"); ?></b></th>
                                                                <th style="font-size:8pt"><b>Engine Status</b></th>
								<th width="1" style="font-size:8pt"><b><?=$this->lang->line("lcoordinate"); ?></b></th>
								<th width="5%" style="font-size:8pt"><b><?=$this->lang->line("lspeed"); ?></b></th>
								<th width="70px;">&nbsp;</th>
							</tr>
						</thead>
						<tbody>
						<?php
						for($i=0; $i < count($data); $i++)
						{
						?>
							<tr <?=($i%2) ? "class='odd'" : "";?> id="tr<?=$data[$i]->vehicle_id;?>">
								<td><a name="a<?=$i?>"></a><div id="pointer<?=$i?>" style="display: none; "text-align: right;">&#9654;</div></td>
								<td><font size="1"><b><?=$i+1?></b></font></td>
								<?php if ($this->sess->user_type != 2) { ?>
								<td><?=$data[$i]->user_name;?></td>
								<?php } ?>
								<td width="1px;" style="font-size:8pt"><b><?=$data[$i]->vehicle_name;?></b></td>
								<td width="1" style="font-size:8pt"><b><?=$data[$i]->vehicle_no;?></b></td>
								<td style="font-size:8pt"><b><?=$data[$i]->vehicle_card_no;?></b></td>
								<td id="datetime<?=$data[$i]->vehicle_id;?>" style="font-size:8pt"></td>
								<td id="position<?=$data[$i]->vehicle_id;?>" style="font-size:8pt"></td>
                                                                <td id="" style="font-size:8pt">
                                                                <?php
                                                                $stdata = $data[$i]->vehicle_id;
                                                                $qryst = "Select * from webtracking_vehicle where vehicle_id = '$stdata'";
                                                                $result_st = mysql_query($qryst);
                                                                $rows_st = mysql_fetch_array($result_st);
                                                                if ($rows_st == 0){
                                                                    echo "-";
                                                                }
                                                                else {
                                                                    $hasil_st = $rows_st["vehicle_device"];
                                                                    $arr_st = explode("@",$hasil_st);
                                                                    if ($arr_st[1] != "GTP") {
                                                                        echo "No Status Engine";
                                                                    }
                                                                    else {
                                                                    	$qryst2 = "Select * from webtracking_gps_info_farrasindo where gps_info_device = '$hasil_st' ORDER BY gps_info_time DESC limit 1 ";
                                                                      $result_st2 = mysql_query($qryst2);
                                                                      $rows_st2 = mysql_fetch_array($result_st2);
                                                                      if ($rows_st2 == 0) {
                                                                      	echo "-";
                                                                      }
                                                                      else {
                                                                      $hasil_st2 = $rows_st2["gps_info_io_port"];
                                                                      $getcode = substr($hasil_st2, 4, 1);
                                                                      switch ($getcode)
                                                                         {
                                                                          case "0" :
                                                                          echo "OFF";
                                                                          break;
                                                                          case "1" :
                                                                          echo "ON";
                                                                          break;
                                                                         }
                                                                      }

                                                                    }
                                                                }
                                                                ?>
                                                                </td>
								<td id="coord<?=$data[$i]->vehicle_id;?>" style="text-align: center;font-size:8pt"></td>
								<td id="speed<?=$data[$i]->vehicle_id;?>" style="text-align: right;font-size:8pt"></td>
								<td>
									<span id="map<?=$data[$i]->vehicle_id;?>" style="display: none;">
									<a href="<?=base_url(); ?>map/realtime/<?=$data[$i]->vehicle_device_name;?>/<?=$data[$i]->vehicle_device_host;?>"><img src="<?=base_url();?>assets/images/realtime.png" width="20" border="0" alt="<?=$this->lang->line("lshow_map"); ?>" title="<?=$this->lang->line("lshow_map"); ?>"></a>
									<a href="<?=base_url()?>trackers/history/<?=$data[$i]->vehicle_device_name;?>/<?=$data[$i]->vehicle_device_host;?>"><img src="<?=base_url();?>assets/images/hist.png" width="20" border="0" alt="<?=$this->lang->line("lhistory"); ?>" title="<?=$this->lang->line("lhistory"); ?>"></a>
									</span>
								</td>
							</tr>
							<span id="timestamp<?=$data[$i]->vehicle_device_name;?>_<?=$data[$i]->vehicle_device_host;?>" style="display: none;">0</span>
						<?php
						}
						?>
						</tbody>
					</table>
				</span></div>

