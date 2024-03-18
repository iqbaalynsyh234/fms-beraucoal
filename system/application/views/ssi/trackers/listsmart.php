<script type="text/javascript" src="<?=base_url();?>assets/transporter/js/loopedslider.js"></script> 
<?php 
	$ishowmap = ($this->sess->user_type == 2 || $this->sess->user_id == 2288) || $this->config->item('mapinhome');
?>
<style style type="text/css" media="screen">

	#tdinfo 
	{
		color: #000000;
		border: 0px;
		font-size: 80%;
		margin-top: -5px;
	}
	
	#map
    {
       top:9%;
	   width:70%;
    }
	
	
	/*
		 * Required 
		*/
		div.slides { position:absolute; top:0; left:0; }
		ul.slides { position:absolute; top:0; left:0; list-style:none; padding:0; margin:0; }
		div.slides div,ul.slides li { position:absolute; top:0; width:900px; display:none; padding:0; margin:0; }
		/*
		 * Optional
		*/
		#loopedSlider,#newsSlider { margin:0 auto; width:900px; height:70px; position:relative; clear:both; }
</style>
<script>
	var gidx = 0;
	var gmarker = new Array();
	var map = null;
	var layers = new Array();
	var listcontrol = null;	
	var glayerstart = -1;
	var gzindex = 0;
	var nvehicle = <?php echo count($data); ?>;
	var gUpdateTimer = 0;
	var checkAllLayer = null;
	var triggerCheckAll = false;
	var triggerCheckItem = false;
	
			
	jQuery(document).ready(
		function()
		{
			showmap(true);
			<?php if ($ishowmap) { ?>
				jQuery("#map").show();
			<?php }  ?>			
			updateLocationEx();
		}
	);
	
	<?php if ($ishowmap) { ?>		
		<?php if (isset($initmap)) echo $initmap; ?>
	<?php }  ?>
	
	<?=$updateinfo;?>
	
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
		
        var mycoord = r.vehicle.gps.gps_latitude_real_fmt+","+r.vehicle.gps.gps_longitude_real_fmt;

		if (r.vehicle.gps.direction == 0)
		{
			mycoord += "<br />" + r.vehicle.gps.gps_course + "&deg; <img src='<?php echo base_url(); ?>assets/images/arrowdirection/car1.png' border='0' width='20' height='20' />";
		}
		else
		{
			mycoord += "<br />" + r.vehicle.gps.gps_course + "&deg; <img src='<?php echo base_url(); ?>assets/images/arrowdirection/car" + r.vehicle.gps.direction + ".png' border='0' width='20' height='20' />";
		}
					
		jQuery("[id='coord"+vid+"']").html(mycoord);		
		
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
			//Get Start On
			if (r.vehicle.since_geofence_in == "")
			{
				if (r.vehicle.startoff)
				{
					jQuery("[id='starton"+vid+"']").html("<br /><img width=10px height=10px src=<?php echo base_url();?>assets/images/calendar.gif />"
													+r.vehicle.starton+"<br />"
													+"<img width=10px height=10px src=<?php echo base_url();?>assets/images/clock.png />"
													+r.vehicle.onduration);
				}
			}
			
			
		}
		else
		{
			jQuery("[id='engine"+vid+"']").html("<?php echo $this->lang->line('loff'); ?>");
			//Get Start Off
			if (r.vehicle.since_geofence_in == "")
			{
				if (r.vehicle.startoff)
				{
					jQuery("[id='startoff"+vid+"']").html("<br /><img width=10px height=10px src=<?php echo base_url();?>assets/images/calendar.gif />"
													+r.vehicle.startoff+"<br />"
													+"<img width=10px height=10px src=<?php echo base_url();?>assets/images/clock.png />"
													+r.vehicle.offduration);
				}
			}
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
        
        //GEOFENCE LOCATION
        if (r.vehicle.geofence_location!="")
        {
			var arrGeofence = r.vehicle.geofence_location.split('#');
			
			var sGeofence = "";
			
			if(arrGeofence.length > 1){
				sGeofence = arrGeofence[1];
			}else{
				sGeofence = arrGeofence[0];
			}
			
			var since = "";
			if(r.vehicle.since_geofence_in != ""){
				since = " <b>Since at</b> " + r.vehicle.since_geofence_in;
			}
			
            jQuery("[id='geofence_location"+vid+"']").html("GEOFENCE : " + sGeofence.toUpperCase() + since + "<br/>");
        }else{
			 jQuery("[id='geofence_location"+vid+"']").html("");
		}
        //END GEOFENCE LOCATION
		
		//Get Fan Status
		if (r.vehicle.fan)
		{
			jQuery("[id='fan_stt"+vid+"']").html("Fan");
			if (r.vehicle.fan == "0")
			{
				jQuery("[id='fan"+vid+"']").html("OFF");
			}
			else
			{
				jQuery("[id='fan"+vid+"']").html("ON");
			}
		}
		//end get fan
		
		//Get driver
		if (r.vehicle.driver)
        {
			var sDriver = r.vehicle.driver.split('-');
			jQuery("[id='driver"+vid+"']").html("<a href=" + "javascript:driver_profile(" + sDriver[0] + ")" + ">" + sDriver[1] + "</a>");
        }
		//end get driver
        
        //Get Customer Groups
        if (r.vehicle.customer_groups)
        {
            jQuery("[id='customer_groups"+vid+"']").html(r.vehicle.customer_groups);
        }
        //End Get Customer Groups
		
		//Get Company
		if (r.vehicle.company)
		{
			jQuery("[id='branch"+vid+"']").html(r.vehicle.company);
		}
		
		//Transporter Tupperware
		if (r.vehicle.id_booking)
		{
			var book_split = r.vehicle.id_booking.split('|');
			var my_book = "";
			if (book_split.length > 1)
			{
				var tot_split = book_split.length;
				for (var i=0;i<tot_split;i++)
				{
					my_book += "<a href=" + "javascript:id_booking(" + book_split[i] + ")" + ">" + book_split[i] + "</a>" + " ";
					my_book += "<br />";
				}
				jQuery("[id='id_booking"+vid+"']").html(my_book);
			}
			else
			{
				jQuery("[id='id_booking"+vid+"']").html("<a href=" + "javascript:id_booking(" + book_split[0] + ")" + ">" + book_split[0] + "</a>");
			}
			
			
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

	function updateLocationEx()
	{
		if (nvehicle <= 0)
		{
			return;
		}
		
		gUpdateTimer = Math.round(<?php echo $this->config->item("timer_updated"); ?>*60*1000/nvehicle);
		
		if (gUpdateTimer < <?=$this->config->item('timer_list');?>)
		{
			gUpdateTimer = <?=$this->config->item('timer_list');?>;
		}
		
		var n = jQuery("#devname"+gidx).val();
		var h = jQuery("#devhost"+gidx).val();
		
		gtimer = <?php echo $this->config->item('timer_list');?>;
		
		jQuery("#pointer0").show();
		updateLocation(n+'@'+h);
	}
	
	<?php if ($ishowmap) { ?>		
	    function removeMarker(id)
	    {
	    	for (var i = 0; i < gmarker.length; i++) 
	    	{
	    		if (gmarker[i][0] != id) continue;	    		
	    		if (map) 
	    		{
	    			//if (gmarker[i][6]) map.removePopup(gmarker[i][6]);
	    			//map.removeLayer(gmarker[i][1]);
	    		}
	    		return i;
	    	}
	    	
	    	return -1;
	    }	
	    
        function addMarker(idx, no, lng, lat, id, car, vename, vehost, r, isupdate)
        {
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
				/* if (checkAllLayer == null)
				{
					checkAllLayer = new OpenLayers.Layer.Markers( "Check All" );
					map.addLayer(checkAllLayer);				
			
					map.events.register('changelayer', null, 
						function(evt)
						{
							if(evt.property === "visibility") 
							{
								if (evt.layer.name == "Check All")
								{					
									if (triggerCheckAll || triggerCheckItem)
									{
										return;
									}
									
									triggerCheckAll = true;
									
									for(var i=0; i < gmarker.length; i++)
									{
										gmarker[i][1].setVisibility(evt.layer.visibility);
										gmarker[i][11] = evt.layer.visibility;
										if (gmarker[i][6]) 
										{
											if (evt.layer.visibility)
											{
												gmarker[i][6].show();
											}
											else
											{
												gmarker[i][6].hide();
											}
										}
									}
									
									triggerCheckAll = false;
								}
								else
								{
									triggerCheckItem = true;
									
									var checkall = true;
									var found = false;
									for(var i=0; i < gmarker.length; i++)
									{									
										if (gmarker[i][1].name == evt.layer.name)									
										{
											gmarker[i][11] = evt.layer.visibility;
											if (evt.layer.visibility)
											{
												gmarker[i][6].show();
											}
											else
											{
												gmarker[i][6].hide();
											}											
											found = true;
										}
										
										if (! gmarker[i][11])
										{
											checkall = false;
										}										
									}		
																		
									if (found)
									{			
										if (! triggerCheckAll)
										{
											checkAllLayer.setVisibility(checkall);
										}
									}
									
									triggerCheckItem = false;
								}
							}
						}
					);
				}
				 */
				var center = new OpenLayers.LonLat(lng, lat);
				center.transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject());
				kml_tracker5.size = new OpenLayers.Size(-47, -45);
				var nos = no.substring(2);
                var popup = new OpenLayers.Popup.FramedCloud("featurePopup",
                center,
                new OpenLayers.Size(68, 37),
                "<div id='pup2' onclick='javascript:showinfo("+idx1+")'>" + nos + "</div>",
                kml_tracker5,
                false,
                null);
				var cr = false;
				if (jQuery('#center_rotation').is(":checked"))
				{
					 cr = true;
				}
				
				popup.panMapIfOutOfView = cr; //gerak2
                popup.autoSize = false;
                popup.calculateRelativePosition = function(){
                return 'tr';
                }
			}
			
			if (map)
			{
				for(var i=0; i < gmarker.length; i++)
				{
					if (! gmarker[i][10]) continue;

					gmarker[i][10] = false;
					
					if (gmarker[i][6]) map.removePopup(gmarker[i][6]);
					map.removeLayer(gmarker[i][1]);
				}
			}
						
			if (idx < 0)
			{
				var data = new Array(12);
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
				data[10] = false;
				data[11] = true;
				
				gmarker.push(data);
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
				gmarker[idx][10] = false;				
			}
			
			if (map)
			{
				
				for(var i=0; i < gmarker.length; i++)
				{
					gmarker[i][10] = true;
					if (gmarker[i][6]) 
					{
						map.addPopup(gmarker[i][6]);
						
						if (gmarker[i][11])
						{
							gmarker[i][6].show();
						}
						else
						{
							gmarker[i][6].hide();
						}
						
					}
					
					map.addLayer(gmarker[i][1]);										
					gmarker[i][1].setVisibility(gmarker[i][11]);
					
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
			jQuery('#dialog').dialog('option', 'width', 700);
			jQuery('#dialog').dialog('option', 'height', 400);
			jQuery('#dialog').dialog('option', 'modal', false);
			jQuery('#dialog').dialog('open');		        	
        }
                
	    function setcenter(lat, lng)
	    {
	    	var center = new OpenLayers.LonLat(110.079345, -4.850154);
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
	    }	
	    	    
	<?php } ?>   

	gnscroll = 4;	
</script>
<style>
    
    .olPopup {
        width: 74px; 
        height: 70px;
        margin-top: 40px;   
		margin-left: 0px;
		left: 0px;
    }
	
	.clearfix {
    float: none;
    clear: both;
	}
	
	#boxtable
	{
		position:absolute;
		font-size:11px;
		margin-top:0px;
		top:50px
	}

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
	</style>
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
		
		<table>
		<tr>
		<td>
			<div id="map" style="position:absolute;"></div>
			<span id='tblrealtime' style='display:none;'>
				<table id="boxtable" width="70%" cellspacing="2" style='display:none;'> 
					<thead>
						<tr class="toprow">
							<th width="2%"><?=$this->lang->line("lno"); ?></th>
							
							<?php if (($this->sess->user_type != 2) || $this->sess->user_company) { ?>
								<th width="8%"><a href="#" onclick="javascript:order('user_name')"><?if ($sortby == 'user_name') { echo '<u>'; }?><?=$this->lang->line("lusername"); ?><?if ($sortby == 'user_name') { echo '</u>'; }?></a></th>
							<?php } ?>
							
							<th colspan="2"><a href="#" onclick="javascript:order('vehicle_name')"><?if ($sortby == 'vehicle_name') { echo '<u>'; }?><?=$this->lang->line("lvehicle"); ?><?if ($sortby == 'vehicle_name') { echo '</u>'; }?></a></th>
							<th><?php echo "Driver"; ?></th>
							<th width="10%" align="center"><?=$this->lang->line("ldatetime"); ?></th>
							<th><?=$this->lang->line("lposition"); ?></th>
							
							<?php if ($this->sess->user_group == 0) { ?>
                                <th>Customer</th>
							<?php } ;?>
							
							<!--<th width="10%"><?=$this->lang->line("lcoordinate"); ?></th>-->
							<th width="12%"><?php echo "Device"; ?></th>
							<!--<th width="4%"><?=$this->lang->line("lspeed"); ?></th>-->
							<th width="70px;">Follow</th>
						</tr>
					</thead>
					
					<tbody>
							<?php
							$vehiclewithpulse = $this->config->item("vehicle_pulse");
							for($i=0; $i < count($data); $i++)
							{
							?>
							<tr <?=($i%2) ? "class='odd'" : "";?> id="tr<?=$data[$i]->vehicle_id;?>">
								<td><?=$i+1?></td>
								
								<?php if (($this->sess->user_type == 1) || $this->sess->user_group == 0) { ?>
									<td>
										<a href="<?php echo base_url(); ?>user/add/<?php echo $data[$i]->user_id; ?>" target="_blank"><font color='#0000FF'><?=$data[$i]->user_name;?><br /><?php if (($this->sess->user_type == 1) && $data[$i]->user_payment_period) { echo ($data[$i]->user_payment_period >= 12) ? " (T)" : " (B)"; } ?></font></a>
										<span id="branch<?=$data[$i]->vehicle_id;?>"></span>
									</td>
								<?php } else { ?>
									<td><font color='#0000FF'><?=$data[$i]->user_name;?><br /></font></td>
								<?php } ?>
								
								<?php if ($this->sess->user_group == 0) { ?>
									<td><a href="javascript:vform(<?php echo $data[$i]->vehicle_id; ?>)"><font color='#0000FF'><?=$data[$i]->vehicle_name;?></font><br />
									</a><?=$data[$i]->vehicle_no;?></td>
									<td><!--<a href="javascript:vform(<?php echo $data[$i]->vehicle_id; ?>)"><font color='#0000FF'><?=$data[$i]->vehicle_no;?>-->
								<?php if ($this->sess->user_type == 1) { printf("<br />(%s)", $data[$i]->vehicle_type); } ?></font></a>
								<?php if (in_array(strtoupper($data[$i]->vehicle_type), $this->config->item("vehicle_gtp"))) { ?>
									<div><?php echo $this->lang->line("lengine"); ?>
										<span id="engine<?=$data[$i]->vehicle_id;?>">-</span>
										<span id="startoff<?=$data[$i]->vehicle_id;?>"></span>
										<span id="starton<?=$data[$i]->vehicle_id;?>"></span>
									</div>
								<?php } ?>
									<span id="speed<?=$data[$i]->vehicle_id;?>" style=""></span>
									</td>
								<?php } else { ?>
									<td width="1px;"><font color='#0000FF'><?=$data[$i]->vehicle_name;?></font></td>
									<td width="1">
										<font color='#0000FF'><?=$data[$i]->vehicle_no;?><br />
										<?php if (in_array(strtoupper($data[$i]->vehicle_type), $this->config->item("vehicle_gtp"))) { ?>
										<div><?php echo $this->lang->line("lengine"); ?>
											<span id="engine<?=$data[$i]->vehicle_id;?>">-</span>
										</div>
								<?php } ?>
								
								<span id="speed<?=$data[$i]->vehicle_id;?>" style=""></span>
								
								<?php if ($this->sess->user_type == 1) { printf("<br />(%s)", $data[$i]->vehicle_type); } ?></font>
								<?php } ?>
								<?php if ($this->sess->user_type == 1) { printf("<br />(%s)", $data[$i]->vehicle_type); } ?>
								</td>																							
								<td>
                                    <span id="driver<?=$data[$i]->vehicle_id;?>" style=""></span>
								</td>
								<td id="datetime<?=$data[$i]->vehicle_id;?>"></td>
                                <td>
									<span id="geofence_location<?php echo $data[$i]->vehicle_id; ?>"></span>
									<span id="position<?=$data[$i]->vehicle_id;?>" style=""></span><br />
									<span id="coord<?=$data[$i]->vehicle_id;?>"></span>
                                
                                <!-- Cutomer Groups -->
                                <?php if ($this->sess->user_group == 0) { ?>
                                <td>
                                    <span id="customer_groups<?=$data[$i]->vehicle_id;?>" ></span>
                                </td>
                                <? } ?>
                                <!-- End Customer Groups -->
                                
                                </td>
								<!--<td id="coord<?=$data[$i]->vehicle_id;?>" style="text-align: center;"></td>-->
								<td style="text-align: left;" valign="top">
									<div style="position: absolute; ">GPS</div>
									<div style="position: relative; left : 20%;" id="signal<?=$data[$i]->vehicle_id;?>">-</div>
									<div  id="fan_stt<?=$data[$i]->vehicle_id;?>" style="position: absolute;"></div> 
									<div style="position: relative; left : 50%;" id="fan<?=$data[$i]->vehicle_id;?>"></div>
									<?php if (in_array(strtoupper($data[$i]->vehicle_type), $this->config->item("vehicle_gtp"))) { ?>
								<!--	<div  style="position: absolute;"><?php echo $this->lang->line("lengine"); ?></div> <div style="position: relative; left : 50%;" id="engine<?=$data[$i]->vehicle_id;?>">-</div> -->
										<?php 
										if ($this->sess->user_type != 2 && $this->sess->user_agent != "1") {
										if (in_array(strtoupper($data[$i]->vehicle_type), $this->config->item("vehicle_gtp_door"))) { ?>
									<div style="position: absolute;"><?php echo $this->lang->line("ldoor"); ?></div>
									<div style="position: relative; left : 50%;" id="door<?=$data[$i]->vehicle_id;?>">-</div>
										<?php } } ?>
										<?php if (in_array(strtoupper($data[$i]->vehicle_type), $this->config->item("vehicle_fuel"))) { ?>
									<div style="position: absolute;"><?php echo $this->lang->line("lfuel"); ?></div>
									<div style="position: relative; left : 50%;" id="fuel<?=$data[$i]->vehicle_id;?>">-</div>
										<?php } ?>
									<?php } ?>
									<?php if ((($this->sess->user_type == 1) || (($this->sess->user_type == 3) && ($this->sess->user_agent_admin == 1)) || $this->sess->user_payment_pulsa)) { ?>
									<div id="pulsadiv<?=$data[$i]->vehicle_id;?>" style="width: 100%; display: none;">
										<div style="position: absolute;"><?php echo $this->lang->line("lpulse_remain"); ?> </div> <div style="position: relative; left : 50%;" id="pulsa<?=$data[$i]->vehicle_id;?>">-</div>
									</div>
									<div id="masaktifdiv<?=$data[$i]->vehicle_id;?>" style="width: 100%; display: none;">
										<div style="position: absolute;"><?php echo $this->lang->line("lmasa_aktif"); ?> </div> <div style="position: relative; left : 50%;" id="masaktif<?=$data[$i]->vehicle_id;?>">-</div>
									</div>
									<?php } ?>

									<?php if ($this->sess->user_group == 0) { ?>
									<?php echo "Card :"." ".$data[$i]->vehicle_card_no;?>
									<?php if ($this->sess->user_type == 1) { ?>
									<br />
									<span id="restart<?=$data[$i]->vehicle_id;?>"></span>
									<?php } ?>
                                    
                                    <?php if (($this->sess->user_type == 2) || ($this->sess->user_type == 3)) { ?>
									<br />
									<span id="restart_member<?=$data[$i]->vehicle_id;?>" style="display: none;" ></span>
									<?php } } ?>
								</td>
								<!--<td style="text-align: right;">-->
								<!--<span id="speed<?= $data[$i]->vehicle_id;?>" style=""></span>-->
								<!--</td>-->
								<td>
									<span id="map<?=$data[$i]->vehicle_id;?>" style="display: none;">
										<a href="<?=base_url(); ?>map/realtime/<?=$data[$i]->vehicle_device_name;?>/<?=$data[$i]->vehicle_device_host;?>"><img src="<?=base_url();?>assets/images/realtime.png" width="20" border="0" alt="<?=$this->lang->line("lshow_map"); ?>" title="<?=$this->lang->line("lshow_map"); ?>"></a>
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
		</td>
	</tr>
</table>


		
