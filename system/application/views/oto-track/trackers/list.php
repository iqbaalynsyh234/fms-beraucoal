<?php 
	$ishowmap = ($this->sess->user_type == 2) || $this->config->item('mapinhome');
?>
<?php if ($ishowmap) { ?>
<link rel="stylesheet" type="text/css" href="<?=base_url();?>assets/css/maps.css" />
<script type="text/javascript" src="<?=base_url();?>assets/js/openlayers/OpenLayers.js"></script> 
<script type="text/javascript" src="<?=base_url();?>assets/js/openlayers/lib/OpenLayers/Layer/OpenStreetMap.js"></script> 
<script src="http://maps.google.com/maps/api/js?sensor=false"></script> 
<?php } ?>
<style>
	#tdinfo 
	{
		color: #000000;
		border: 0px;
		font-size: 80%;
		margin-top: -5px;
	}
</style>
<script>
	var gidx = 0;
	var gmarker = new Array();
	var map = null;
	var layers = new Array();
	var listcontrol = null;	
	var glayerstart = -1;
	var gzindex = 0;
	var nvehicle = <?php echo count($data); ?>
	
			function vform(v)
			{
				showdialog();
				jQuery.post('<?php echo base_url(); ?>user/formvehicle/', {id: v},
					function(r)
					{
						showdialog(r.html, "<?=$this->lang->line("lupdate_vehicle"); ?>");
					}
					, "json"
				);
			}
			
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
			location = "#atop";
		}
	);
	
	<?php if ($ishowmap) { ?>		
		<?php if (isset($initmap)) echo $initmap; ?>
	<?php }  ?>
	
	<?=$updateinfo;?>
	
	function page()
	{
		document.frmsearch.submit();
	}
	
	function field_onchange()
	{
		var v = jQuery("#field").val();

		jQuery("#keyword").hide();
		jQuery("#type").hide();
		jQuery("#status").hide();
		jQuery("#vehicle_type").hide();
		jQuery("#company").hide();
		jQuery("#usergroup1").hide();
		jQuery("#server").hide();
		
		var s = "delayed";
		if (v.substring(0, s.length) == s)
		{
			v = "delayed";
		}

		switch(v)
		{
			case "vexpired":
			case "vactive":
			case "delayed":
			break;
			case "vehicle_type":
				jQuery("#vehicle_type").show();
			break;
			case "user_company":
				jQuery("#company").show();
				loadgroup1();
			break;
			case "server":
				jQuery("#server").show();
			break;
			default:
				jQuery("#keyword").show();			
		}
	}
	
        function loadgroup1()
        {
                jQuery.post("<?php echo base_url(); ?>group/options<?php if (isset($_POST['group'])) { echo "/".$_POST['group']; } ?>", {usersite: jQuery("#company").val(), showadmin: 0},
                        function(r)
                        {
                                if (r.empty)
                                {
                                       jQuery("#usergroup1").hide();
	                                 return;
                                }

                                jQuery("#usergroup1").show();
                                jQuery("#usergroup1").html(r.html);
                        }
                        , "json"
                );
        }		
	
	function updateLocationEx()
	{
		if (nvehicle <= 0)
		{
			return;
		}
		
		var timerupdate = Math.round(<?php echo $this->config->item("timer_updated"); ?>*60*1000/nvehicle);
		
		if (timerupdate < <?=$this->config->item('timer_list');?>)
		{
			timerupdate = <?=$this->config->item('timer_list');?>;
		}
				
		var n = jQuery("#devname"+gidx).val();
		var h = jQuery("#devhost"+gidx).val();
		
		jQuery("#pointer0").show();
		updateLocation(n+'@'+h, timerupdate);
	}
	
	function update(r)
	{		
		var vid = jQuery('#devid'+gidx).val();
		
		if (gidx == 0)
		{
			if (jQuery("#autoscroll").attr("checked"))
			{
				location = "#atop";
			}
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
				jQuery("[id='signal"+vid+"']").html("");
				jQuery("[id='engine"+vid+"']").html("");
				jQuery("[id='door"+vid+"']").html("");
				jQuery("[id='map"+vid+"']").html("");
				jQuery("[id='pulsa"+vid+"']").html("");
				jQuery("[id='restart"+vid+"']").html("");								
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
		addMarker(idx, (gidx+1) + ' ' + r.vehicle.vehicle_no, r.vehicle.gps.gps_longitude_real, r.vehicle.gps.gps_latitude_real, vid , r.vehicle.gps.car_icon, r.vehicle.vehicle_device_name, r.vehicle.vehicle_device_host, r, true);
	<?php }  ?>				
		
		jQuery("[id='timestamp"+r.vehicle.vehicle_device_name+"_"+r.vehicle.vehicle_device_host+"']").html(r.vehicle.gps.gps_timestampori);
		jQuery("[id='datetime"+vid+"']").html(r.vehicle.gps.gps_date_fmt + " " + r.vehicle.gps.gps_time_fmt);
		jQuery("[id='position"+vid+"']").html('<a target="_blank" href="http://maps.google.com/maps?q='+r.vehicle.gps.gps_latitude_real_fmt+','+r.vehicle.gps.gps_longitude_real_fmt+'"><font color="#0000ff">'+r.vehicle.gps.georeverse.display_name+'</font></a>');
		jQuery("[id='coord"+vid+"']").html(r.vehicle.gps.gps_latitude_real_fmt+","+r.vehicle.gps.gps_longitude_real_fmt);		
		
		if (r.vehicle.gps.gps_status == "A")
		{
			jQuery("[id='signal"+vid+"']").html("OK");
		}
		else
		{
			jQuery("[id='signal"+vid+"']").html("NOT OK");
		}
		
		if (r.vehicle.status3)
		{
			jQuery("[id='door"+vid+"']").html("<?php echo $this->lang->line('lopened'); ?>");
		}
		else
		{
			jQuery("[id='door"+vid+"']").html("<?php echo $this->lang->line('lclosed'); ?>");
		}

		if (r.vehicle.status1)
		{
			jQuery("[id='engine"+vid+"']").html("<?php echo $this->lang->line('lon'); ?>");
		}
		else
		{
			jQuery("[id='engine"+vid+"']").html("<?php echo $this->lang->line('loff'); ?>");
		}
				
		if (r.vehicle.pulse)
		{
			jQuery("[id='pulsadiv"+vid+"']").show();
			jQuery("[id='pulsa"+vid+"']").html(r.vehicle.pulse);
			
			jQuery("[id='masaktifdiv"+vid+"']").show();
			jQuery("[id='masaktif"+vid+"']").html(r.vehicle.masaaktif);
		}
		else
		{
			jQuery("[id='pulsadiv"+vid+"']").hide();
			jQuery("[id='masaktifdiv"+vid+"']").hide();			
		}
		
		if (r.vehicle.fuel)
		{
			jQuery("[id='fuel"+vid+"']").html(r.vehicle.fuel);
		}
		jQuery("[id='speed"+vid+"']").html(r.vehicle.gps.gps_speed_fmt+ " kph");		
		jQuery("[id='map"+vid+"']").show();
						
        var featurecard_member = "";
        if ((r.vehicle.restartcommand != "NOT SUPPORT") && (r.vehicle.restartcommand != ""))
            {
			     featurecard_member = "<a href='javascript:sendsms(\""+r.vehicle.restartcommand+"\", \""+r.vehicle.vehicle_card_no+"\", \"<?php echo $this->lang->line("lsending_restart"); ?>\")'><font color='#0000ff'><?php echo $this->lang->line("lrestart"); ?></font></a>";
            }
            
            
		if (r.vehicle.gps.css_delay)
		{
            jQuery("[id='restart_member"+vid+"']").html(featurecard_member);
            
            if((r.vehicle.gps.css_delay[1] == "#ff0000") || (r.vehicle.gps.css_delay[1] == "#ffff00")) {
                
                jQuery("[id='restart_member"+vid+"']").show();
            }
            
			jQuery("[id='tr"+vid+"']").contents('td').css("background-color", r.vehicle.gps.css_delay[1]);
			jQuery("[id='tr"+vid+"']").contents('td').css("color", r.vehicle.gps.css_delay[2]);			
		} 
        
		var featurecard = "";
		if ((r.vehicle.restartcommand != "NOT SUPPORT") && (r.vehicle.restartcommand != ""))
		{
			featurecard = "<a href='javascript:sendsms(\""+r.vehicle.restartcommand+"\", \""+r.vehicle.vehicle_card_no+"\", \"<?php echo $this->lang->line("lsending_restart"); ?>\")'><font color='#0000ff'><?php echo $this->lang->line("lrestart"); ?></font></a>";
		}
		
		if (r.vehicle.checkpulsa != "")
		{
			featurecard += " | <a href='javascript:sendsms(\""+r.vehicle.checkpulsa+"\", \""+r.vehicle.vehicle_card_no+"\", \"<?php echo $this->lang->line("lsending_checkpulse"); ?>\")'><font color='#0000ff'><?php echo $this->lang->line("lcheck_pulse"); ?></font></a>";
		}
		
		jQuery("[id='restart"+vid+"']").html(featurecard);
				
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
	    			//map.removeLayer(gmarker[i][1]);
	    		}
	    		return i;
	    	}
	    	
	    	return -1;
	    }	
	    
        function addMarker(idx, no, lng, lat, id, car, vename, vehost, r, isupdate)
        {
        	var popup = null;
        	var isadded = true;

			var idx1;
			if (idx < 0)
			{
				idx1 = gmarker.length;
			}
			else
			{
				idx1 = idx;
			}
        	
        	var gml_url = "<?=base_url()?>map/kmllastcoord/"+lng+"/"+lat+"/"+id+"/"+car+"/on1/"+r.vehicle.gps.css_delay_index;
			var kml_tracker5 = new OpenLayers.Layer.GML
			(
    			no + " " + r.vehicle.gps.gps_time_fmt,
    			gml_url,
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
				kml_tracker5.size = new OpenLayers.Size(-47, -45);
				var nos = no.substring(2);
                popup = new OpenLayers.Popup.FramedCloud("featurePopup",
                center,
                new OpenLayers.Size(68, 37),
                "<div id='pup2' onclick='javascript:showinfo("+idx1+")'>" + nos + "</div>",
                kml_tracker5,
                false,
                null);
                popup.panMapIfOutOfView = gzindex == 0;
                popup.autoSize = false;
                popup.calculateRelativePosition = function(){
                return 'tr';
                }
                        
                                       
                if (idx == -1)
                {
					if (glayerstart == -1)
					{
						glayerstart = map.layers.length;
					}
					
					map.addPopup(popup);
					map.addLayer(kml_tracker5);
					gzindex++;
				}
				else
				if (! isupdate)
				{
					if (glayerstart == -1)
					{
						glayerstart = map.layers.length;
					}
					
					map.addPopup(popup);
					map.addLayer(kml_tracker5);
					gzindex++;
				}
				else
				{
					if (map.layers[idx+glayerstart].visibility)
					{
						map.addPopup(popup);
					}
					map.layers[idx+glayerstart].name = kml_tracker5.name;
					map.layers[idx+glayerstart].setUrl(gml_url);
					map.layers[idx+glayerstart].setZIndex(gzindex);
					gzindex++;
					isadded = false;
				}
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
			/*
			if (map)
			{
				if (poiSelectControl)
				{
					if (isadded)
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
			*/
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
	    		jQuery("#lnshowmap").css("text-align", "left");
	    		
	    		jQuery("#tblrealtime").hide();
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
    						addMarker(i, gmarker[i][2], gmarker[i][3], gmarker[i][4], gmarker[i][0], gmarker[i][5], gmarker[i][7], gmarker[i][8], gmarker[i][9], false);
    					}
    					
    					map.controls[2].maximizeControl();
	    			}
	    		);	    			    			    		
	    		
	    		return;
	    	}
	    	
	    	jQuery("#lnshowmap").css("text-align", "right");
    		jQuery("#tblrealtime").show("slow");
	    	jQuery("#map").hide("slow");	    	
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
		jQuery("#autoscroll").attr("checked", false)
		
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
		jQuery("#autoscroll").attr("checked", false)
		showdialog();
		
		jQuery.post("<?php echo base_url(); ?>payment/confirmation/"+id, {},
			function(r)
			{
				showdialog(r.html, r.title);
			}
			, "json"
		);		
	}
	
	function sendsms(message, hp, success)
	{
		jQuery.post("<?php echo base_url(); ?>smsserver/send/", {message: message, hp: hp},
			function(r)
			{
				alert(success);
			}
		);		
	}
	
	gnscroll = 4;
</script>

<script>
function feature_member() {
    
    
}
</script>
<style>
	#pup2 {
    font-size: 10px;
    font-weight: bold;
    width:73px;
    height:37px;
    color:black;
    text-align:center;
    background-repeat:no-repeat;
    background-image: url('<?=base_url();?>assets/images/pup2.png');
}
	label a:visited {color: red;}
	label a:hover {font-size:24; font-weight:bold; color: green;}
	
	}
	
	</style>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
<?=$navigation;?>	


<?php if (base_url() == "http://app.oto-track.com/") 
{ 
	if ($this->sess->user_type == 3) { ?>
<center>
<table id="tblinvoice_alert" width="100%" style="display:none;position:absolute;top:50px">
<tr><td align="center" colspan="2">
<img src="<?php echo base_url();?>assets/images/alertInvoice.jpg" width="25px" height="25px" align="middle"/>
</td></tr>
<tr>
<td align="center"><b><?=$this->lang->line("linvoice_note"); ?></b><label id="dvpayments_info"></label>
</td>
</tr>
<tr><td align="center" colspan="2">Klik menu <b><u>INVOICE</u></b> untuk informasi lebih detail</td></tr>
</table>
</center>
<?php } 
}
	?>
	
	
			<div id="main" style="margin: 20px;">
<?php if (! $ishowmap) { ?>
&nbsp;
<?php } else { ?>
<p style="text-align: right" id="lnshowmap" align="right">
	[<a href="javascript:showmap(false)"><font color="#0000ff"><?=$this->lang->line('lshow_table');?></font></a>]
	[<a href="javascript:showmap(true)"><font color="#0000ff"><?=$this->lang->line('lshow_map');?></font></a>]
</p>
<?php } ?>
				<span id='tblrealtime'>					
					<h1><?=$this->lang->line("llist_trackers"); ?> (<?=count($data);?>)</h1>
		<h2><?=$this->lang->line("lsearch"); ?></h2>
		<form name="frmsearch" id="frmsearch" method="post" action="<?=base_url()?>trackers">
				<input type="hidden" name="offset" id="offset" value="" />
			<input type="hidden" id="sortby" name="sortby" value="<?=$sortby?>" />
			<input type="hidden" id="orderby" name="orderby" value="<?=$orderby?>" />				
			<table width="100%" cellpadding="3" class="tablelist">
				<tr>
					<td width="10%"><?=$this->lang->line("lsearchby");?></td>
					<td>
						<select id="field" name="field" onchange="javascript:field_onchange()">
							<?php if ($this->sess->user_type != 2) { ?>
							<option value="user_login"><?=$this->lang->line("llogin");?></option>
							<option value="user_name"><?=$this->lang->line("lname");?></option>
							<option value="user_agent"><?=$this->lang->line("lagent");?></option>
							<option value="user_company"><?=$this->lang->line("lcompany");?></option>
							<?php } ?>
							<option value="vehicle"><?=$this->lang->line("lvehicle");?></option>
							<option value="device"><?=$this->lang->line("ldevice_id");?></option>
							<option value="vehicle_card_no"><?=$this->lang->line("lcardno");?></option>
							<?php if ($this->sess->user_type != 2) { ?>
							<option value="vexpired"><?=$this->lang->line("lvehicle_expired");?></option>
							<option value="vactive"><?=$this->lang->line("lvehicle_active");?></option>
								<?php if (! $this->config->item('vehicle_type_fixed')) { ?>
							<option value="vehicle_type"><?=$this->lang->line("lvehicle_type");?></option>
								<?php } ?>
									<?php foreach ($this->config->item("css_tracker_delay") as $val) { ?>
										<?php if ($val[0] == 0) continue; ?>
										<option value="delayed<?php echo $val[0]; ?>_<?php echo $val[3]; ?>">&gt;= <?php echo $val[0]; ?> <?php echo $this->lang->line("lminute_delayed"); ?></option>
									<?php } ?>
							<?php } ?>
							<?php if ($this->sess->user_type == 1) { ?>
							<option value="server"><?=$this->lang->line("lserver");?></option>
							<?php } ?>
						</select>
						<input type="text" name="keyword" id="keyword" value="" class="formdefault" />
						<select name='vehicle_type' id='vehicle_type' style="display: none;">
							<?php 
								foreach($this->config->item("vehicle_type") as $key=>$val) { 
									//if (! in_array($key, $this->config->item('vehicle_type_visible'))) continue;
							?>							
							<option value="<?php echo $key; ?>" <?php echo (isset($_POST['vehicle_type']) && ($key==$_POST['vehicle_type'])) ? " selected" : "";?>><?php echo $key; ?></option>
							<?php } ?>								
						</select>
						<select id="company" name="company" onchange="javascript: loadgroup1()" style="display: none;">
							<?php for($i=0; $i < count($companies); $i++) { ?>
							<option value="<?php echo $companies[$i]->company_id; ?>"><?php echo $companies[$i]->company_name; ?></option>
							<?php } ?>
							</select>
							<span id="usergroup1"></span>
						<select id="server" name="server" style="display: none;">	
						<?php foreach($this->config->item("SERVER_TRACKERS") as $key=>$val) { ?>
						<option value="<?php echo $key; ?>"<?php echo (isset($_POST['server']) && ($key==$_POST['server'])) ? " selected" : "";?>><?php echo $val; ?></option>
						<?php } ?>						
						</select>
					</td>
				</tr>
				<tr>
					<td style="border: 0px;">&nbsp;</td>
					<td style="border: 0px;"><input type="submit" name="btnsearch" value="<?=$this->lang->line("lsearch");?>" /></td>
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
								<th width="2%"><?=$this->lang->line("lno"); ?></td>
								<?php if (($this->sess->user_type != 2) || $this->sess->user_company) { ?>
								<th width="10%"><a href="#" onclick="javascript:order('user_name')"><?if ($sortby == 'user_name') { echo '<u>'; }?><?=$this->lang->line("lusername"); ?><?if ($sortby == 'user_name') { echo '</u>'; }?></a></th>
								<?php } ?>
								<th width="18%" colspan="2"><a href="#" onclick="javascript:order('vehicle_name')"><?if ($sortby == 'vehicle_name') { echo '<u>'; }?><?=$this->lang->line("lvehicle"); ?><?if ($sortby == 'vehicle_name') { echo '</u>'; }?></a></th>
								<th width="7%"><a href="#" onclick="javascript:order('vehicle_card_no')"><?if ($sortby == 'vehicle_card_no') { echo '<u>'; }?><?=$this->lang->line("lcardno"); ?><?if ($sortby == 'vehicle_card_no') { echo '</u>'; }?></a></th>
								<th width="10%" align="center"><?=$this->lang->line("ldatetime"); ?></th>
								<th><?=$this->lang->line("lposition"); ?></th>
								<th width="10%"><?=$this->lang->line("lcoordinate"); ?></th>
								<th width="10%"><?=$this->lang->line("linfo"); ?></th>
								<th width="4%"><?=$this->lang->line("lspeed"); ?></th>
								<th width="70px;">&nbsp;</th>
							</tr>
						</thead>
						<tbody>
						<?php
						$vehiclewithpulse = $this->config->item("vehicle_pulse");
						for($i=0; $i < count($data); $i++)
						{
						?>
							<tr <?=($i%2) ? "class='odd'" : "";?> id="tr<?=$data[$i]->vehicle_id;?>">
								<td><a name="a<?=$i?>"></a><div id="pointer<?=$i?>" style="display: none; "text-align: right;">&#9654;</div></td>
								<td><?=$i+1?></td>
								<?php if (($this->sess->user_type != 2) || $this->sess->user_company) { ?>
								<td><a href="<?php echo base_url(); ?>user/add/<?php echo $data[$i]->user_id; ?>" target="_blank"><font color='#0000FF'><?=$data[$i]->user_name;?><br /><?php if (($this->sess->user_type == 1) && $data[$i]->user_payment_period) { echo ($data[$i]->user_payment_period >= 12) ? " (T)" : " (B)"; } ?></font></a></td>
								<?php } ?>
								<?php if (($this->sess->user_type == 1) || (($this->sess->user_type == 3) && ($this->sess->user_agent_admin == 1) && $this->sess->agent_canedit_vactive)) { ?>
								<td width="1px;"><a href="javascript:vform(<?php echo $data[$i]->vehicle_id; ?>)"><font color='#0000FF'><?=$data[$i]->vehicle_name;?></font></a></td>
								<td width="1"><a href="javascript:vform(<?php echo $data[$i]->vehicle_id; ?>)"><font color='#0000FF'><?=$data[$i]->vehicle_no;?> <?php if ($this->sess->user_type == 1) { printf("<br />(%s)", $data[$i]->vehicle_type); } ?></font></a></td>								
								<?php } else { ?>
								<td width="1px;"><?=$data[$i]->vehicle_name;?></td>
								<td width="1"><?=$data[$i]->vehicle_no;?> <?php if ($this->sess->user_type == 1) { printf("<br />(%s)", $data[$i]->vehicle_type); } ?></td>																							
								<?php } ?>
								<td>
									
									<?=$data[$i]->vehicle_card_no;?>
									<?php if ($this->sess->user_type == 1) { ?>
									<span id="restart<?=$data[$i]->vehicle_id;?>"></span>
									<?php } ?>
                                    
                                    <?php if (($this->sess->user_type == 2) || ($this->sess->user_type == 3)) { ?>
									<span id="restart_member<?=$data[$i]->vehicle_id;?>" style="display: none;" ></span>
									<?php } ?>
                                    
								</td>
								<td id="datetime<?=$data[$i]->vehicle_id;?>"></td>
								<td id="position<?=$data[$i]->vehicle_id;?>"></td>
								<td id="coord<?=$data[$i]->vehicle_id;?>" style="text-align: center;"></td>
								<td style="text-align: left;" valign="top">
									<div style="position: absolute; ">GPS </div> <div style="position: relative; left : 50%;" id="signal<?=$data[$i]->vehicle_id;?>">-</div>
									<?php if (in_array(strtoupper($data[$i]->vehicle_type), $this->config->item("vehicle_gtp"))) { ?>
									<div  style="position: absolute;"><?php echo $this->lang->line("lengine"); ?></div> <div style="position: relative; left : 50%;" id="engine<?=$data[$i]->vehicle_id;?>">-</div>
										<?php if (in_array(strtoupper($data[$i]->vehicle_type), $this->config->item("vehicle_gtp_door"))) { ?>
									<div style="position: absolute;"><?php echo $this->lang->line("ldoor"); ?> </div> <div style="position: relative; left : 50%;" id="door<?=$data[$i]->vehicle_id;?>">-</div>
										<?php } ?>
										<?php if (in_array(strtoupper($data[$i]->vehicle_type), $this->config->item("vehicle_fuel"))) { ?>
									<div style="position: absolute;"><?php echo $this->lang->line("lfuel"); ?> </div> <div style="position: relative; left : 50%;" id="fuel<?=$data[$i]->vehicle_id;?>">-</div>
										<?php } ?>
									<?php } ?>
									<?php if ((($this->sess->user_type == 1) || (($this->sess->user_type == 3) && ($this->sess->user_agent_admin == 1)) || $this->sess->user_payment_pulsa)) { ?>
									<div id="pulsadiv<?=$data[$i]->vehicle_id;?>" style="width: 100%; display: none;"><div style="position: absolute;"><?php echo $this->lang->line("lpulse_remain"); ?> </div> <div style="position: relative; left : 50%;" id="pulsa<?=$data[$i]->vehicle_id;?>">-</div></div>
									<div id="masaktifdiv<?=$data[$i]->vehicle_id;?>" style="width: 100%; display: none;"><div style="position: absolute;"><?php echo $this->lang->line("lmasa_aktif"); ?> </div> <div style="position: relative; left : 50%;" id="masaktif<?=$data[$i]->vehicle_id;?>">-</div></div>
									<?php } ?>
								</td>
								<td id="speed<?=$data[$i]->vehicle_id;?>" style="text-align: right;"></td>
								<td>
									<span id="map<?=$data[$i]->vehicle_id;?>" style="display: none;">
									<a href="<?=base_url(); ?>map/realtime/<?=$data[$i]->vehicle_device_name;?>/<?=$data[$i]->vehicle_device_host;?>"><img src="<?=base_url();?>assets/images/realtime.png" width="20" border="0" alt="<?=$this->lang->line("lshow_map"); ?>" title="<?=$this->lang->line("lshow_map"); ?>"></a>
									<a href="#" onclick="javascript:showGoogleEarth('<?=base_url()?>map/googleearth/<?=$this->sess->user_login?>/<?=substr($this->sess->user_pass, 1)?>/<?=$data[$i]->vehicle_device_name?>/<?=$data[$i]->vehicle_device_host?>')"><img src="<?=base_url();?>assets/images/gearth.png" width="20" border="0" alt="<?=$this->lang->line("lgoogle_earth"); ?>" title="<?=$this->lang->line("lgoogle_earth"); ?>"></a>									
									<a href="<?=base_url()?>trackers/overspeed/<?=$data[$i]->vehicle_device_name;?>/<?=$data[$i]->vehicle_device_host;?>"><img src="<?=base_url();?>assets/images/speedometer.png" width="20" border="0" alt="<?=$this->lang->line("loverspeed_report"); ?>" title="<?=$this->lang->line("loverspeed_report"); ?>"></a>
									<br />
									<a href="<?=base_url()?>trackers/parkingtime/<?=$data[$i]->vehicle_device_name;?>/<?=$data[$i]->vehicle_device_host;?>"><img src="<?=base_url();?>assets/images/park.png" width="20" border="0" alt="<?=$this->lang->line("lparking_time"); ?>" title="<?=$this->lang->line("lparking_time"); ?>"></a>
									<a href="<?=base_url()?>trackers/history/<?=$data[$i]->vehicle_device_name;?>/<?=$data[$i]->vehicle_device_host;?>"><img src="<?=base_url();?>assets/images/hist.png" width="20" border="0" alt="<?=$this->lang->line("lhistory"); ?>" title="<?=$this->lang->line("lhistory"); ?>"></a>
									<a href="<?=base_url()?>trackers/workhour/<?=$data[$i]->vehicle_device_name;?>/<?=$data[$i]->vehicle_device_host;?>"><img src="<?=base_url();?>assets/images/workhour.png" width="20" border="0" alt="<?=$this->lang->line("lworkhour_report"); ?>" title="<?=$this->lang->line("lworkhour_report"); ?>"></a>
									</span>
								</td>
							</tr>
							<span id="timestamp<?=$data[$i]->vehicle_device_name;?>_<?=$data[$i]->vehicle_device_host;?>" style="display: none;">0</span>
						<?php
						}
						?>
						</tbody>
					</table>
				</span>				
			</div>					
</div>
<?php if ($ishowmap) { ?>
			<div id="map"></div>
<?php } ?>			
