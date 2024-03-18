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
		ldata[1] = '<?=$data[$i]->gps_longitude_real;?>';
		ldata[2] = '<?=$data[$i]->gps_longitude_real;?>';
		ldata[3] = '<?php echo "Archive"; ?>';
		g_vehicles.push(ldata);
		<?php } ?>
		
		
		<?php if (isset($initmap)) echo $initmap; ?>
		
		jQuery(document).ready(
			function()
			{
					init();

					var center = new OpenLayers.LonLat(<?=$data[0]->gps_longitude_real;?>, <?=$data[0]->gps_latitude_real;?>);

					map.setCenter(center.transform
						(
                    		new OpenLayers.Projection("EPSG:4326"),
                    		map.getProjectionObject()
                		), 15); //zoom 15


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
<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
<thead>
    	<tr>
	        <th style="text-align:center;" width="3%">No</td>
			<th style="text-align:center;" width="10%">Vehicle</th>
			<th style="text-align:center;" width="10%">Start Time</th>
			<th style="text-align:center;" width="10%">End Time</th>
			<th style="text-align:center;" width="5%">Engine</th>
			<th style="text-align:center;" width="7%">Duration</th>
			<th style="text-align:center;" width="20%">Location Start</th>
			<th style="text-align:center;" width="20%">Location End</th>
			<th style="text-align:center;" width="7%">Trip Mileage</th>		
			<!--<th style="text-align:center;" width="7%">Commulative Mileage</th>-->
			
		</tr>
    </thead>
<tbody>
		<?php
			if (count($row)>0)
			{
				$j = 0;
				for ($i=0;$i<count($row);$i++)
				{ 
					$j = $j + $row->trip_mileage_trip_mileage;
					$doorstart_status = "";
					$doorend_status = "";
					$vdoorstatus = "";
					$vtype = "";
				?>
				<tr>
					<td style="text-align:center;"><?php echo $i+1;?></td>
					<td style="text-align:center;"><?php echo $row->trip_mileage_vehicle_name;?> - <?php echo $row->trip_mileage_vehicle_no;?>
					</td>
					<td style="text-align:center;"><?php echo $row->trip_mileage_start_time;?></td>
					<td style="text-align:center;"><?php echo $row->trip_mileage_end_time;?></td>
					<td style="text-align:center;">
						<?php if($row->trip_mileage_engine == 0) { ?>
							<?php echo "OFF";?>
						<?php } else { ?>
							<?php echo "ON";?>
						<?php } ?>
					</td>
					<td style="text-align:center;">
					<?php 
						if( (isset($row->trip_mileage_coordinate_list)) && ($row->trip_mileage_engine == "1") && ($row->trip_mileage_coordinate_list != "") ){ ?>
							<!--<a href="javascript:mn_map(<?=$row->trip_mileage_id;?>)" target="_blank"><?php echo $data[$i]->trip_mileage_duration;?></a> -->
							<a href="<?php echo base_url();?>operational_report/map/<?=$row->trip_mileage_id;?>" target="_blank"><?php echo $row->trip_mileage_duration;?>
							</a> 
						<?php }else{?>
							<?php echo $row->trip_mileage_duration;?>
						<?php }
					?>
					</td>
					<td>
						<?php $geofence_start = strlen($row->trip_mileage_geofence_start); 
							if (strlen($geofence_start == 1)){	
							$geofence_start_name = "";?>
								<strong><font color="red"><?php echo $geofence_start_name." ";?></font></strong>
								<?php echo $row->trip_mileage_location_start;?><br />
						<?php } ?>
						
						<?php
							if (strlen($geofence_start > 1)){	
							$geofence_start_name = $row->trip_mileage_geofence_start;?>
								<strong><font color="red"><?php echo $geofence_start_name." ";?></font></strong>
								<?php echo $row->trip_mileage_location_start;?><br />
						<?php } ?>
						<strong><font color="red"><?php echo $row->trip_mileage_coordinate_start;?></font></strong><br />
						<?php 
						//cek apakah sudah pernah ada filenya
						$this->db->order_by("vehicle_id","asc");
						$this->db->select("vehicle_device,vehicle_type");
						$this->db->where("vehicle_device",$row->trip_mileage_vehicle_id);
						$this->db->where("vehicle_status <>",3);
						$this->db->limit(1);
						$qv = $this->db->get("vehicle");
						$rv = $qv->row();
						if(count($rv) > 0){
							$vtype = $rv->vehicle_type;
							if($vtype == "T5DOOR"){
								$vdoorstatus = "YES";
							}else{
								$vdoorstatus = "NO";
							}
						}else{
							$vtype = "";
						}
						?>
						<!-- cek door status start-->
						<?php if(isset($row->trip_mileage_door_start) && ($row->trip_mileage_door_start == 1)){
							$doorstart_status = "OPEN";
						}
							if(isset($row->trip_mileage_door_start) && ($row->trip_mileage_door_start == 0)){
							$doorstart_status = "CLOSE";
						}
						?>
						<?php if($vdoorstatus == "YES"){ ?>
							Door: <strong><font color="red"><?php echo $doorstart_status;?></font></strong>
						<?php } ?>
						
					</td>
					
					<td>
						<?php $geofence_end = strlen($row->trip_mileage_geofence_end); 
							if (strlen($geofence_end == 1)){	
							$geofence_end_name = "";?>
								<strong><font color="red"><?php echo $geofence_end_name." ";?></font></strong>
								<?php echo $row->trip_mileage_location_end;?><br />
						<?php } ?>
						
						<?php
							if (strlen($geofence_end > 1)){	
							$geofence_end_name = $row->trip_mileage_geofence_end;?>
								<strong><font color="red"><?php echo $geofence_end_name." ";?></font></strong>
								<?php echo $row->trip_mileage_location_end;?><br />
						<?php } ?>
						<strong><font color="red"><?php echo $row->trip_mileage_coordinate_end;;?></font></strong><br />
						
						<!-- cek door status end-->
						<?php if(isset($data[$i]->trip_mileage_door_end) && ($row->trip_mileage_door_end == 1)){
							$doorend_status = "OPEN";
						}
							else if(isset($data[$i]->trip_mileage_door_end) && ($row->trip_mileage_door_end == 0)){
							$doorend_status = "CLOSE";
						}
						?>
						<?php if($vdoorstatus == "YES"){ ?>
							Door: <strong><font color="red"><?php echo $doorend_status;?></font></strong>
						<?php } ?>
					</td>
					
					<td><?php echo $row->trip_mileage_trip_mileage." "."KM";?></td>
					<!--<td><?php echo $j." "."KM";?></td>-->
				</tr>
		<?php
				}
			}else{
				echo "<tr><td colspan='12'>No Data Available</td></tr>";
			}
			?>
    </tbody>
</table>
<br />
<div id="map" style="border: 1px #000000 solid; width: 100%; height: 700px;"></div>
<div id="main" style="margin: 20px;">
	<section class="grid_12">
		<div class="block-border">
			
		</div>
	</section>
</div>
</div>

