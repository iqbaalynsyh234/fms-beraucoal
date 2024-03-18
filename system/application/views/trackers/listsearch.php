	<?php if ($id == "history") { ?>
	<script>
		var g_timeaddmarker = null;
		var g_imarker = 0;
		var g_vehicles = new Array();

		<?php for($i=0; $i < count($data); $i++) { ?>
		var ldata = new Array();
		ldata[0] = <?=$i+1?>;
		ldata[1] = '<?=$data[$i]->gps_longitude_real_fmt;?>';
		ldata[2] = '<?=$data[$i]->gps_latitude_real_fmt;?>';
		ldata[3] = <?=$vehicle->vehicle_id;?>;

		g_vehicles.push(ldata);
		<?php } ?>

		function animate()
		{
			jQuery("#isanimate").val("1");
			document.frmsearch.target = "_blank";
			document.frmsearch.submit();
		}

		jQuery(document).ready(
			function()
			{
				<?php if (count($data) > 0) { ?>
					jQuery("#tdmap").show();
					init();

					var center = new OpenLayers.LonLat(<?=$data[0]->gps_longitude_real_fmt;?>, <?=$data[0]->gps_latitude_real_fmt;?>);

					map.setCenter(center.transform
						(
                    		new OpenLayers.Projection("EPSG:4326"),
                    		map.getProjectionObject()
                		), <?=$this->config->item('zoom_history')?>);


				track("<?php echo $this->lang->line('ltrack'); ?>: <?php echo $vehicle->vehicle_no; ?> <?php echo $vehicle->vehicle_name; ?>");


				var ref = "";

				ref += "<a href='javascript:animate()'><font color='#000000'>[ <?php echo $this->lang->line("lanimation"); ?> ]</font></a>";
				ref += '<a href="<?=base_url();?>map/historyfull?dummy=on&vehicle=<?=$vehicle->vehicle_id;?>';
				ref += "&sessionid=<?php echo $uniqid; ?>";
				ref += '" target="_blank"><font color="#000000">[ <?=$this->lang->line('lfull_size');?> ]</font></a>';
				jQuery("#refmap").html(ref);

				<?php } ?>

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
	<?php } ?>
    <!--new table-->
    <!-- Content -->
        <div class="block-border">
            <?php if ($id == "history") { ?>
		<?php if (! $isgtp) { ?>
		<!--<h3>Odometer: <b><?=$totalodometer;?> km</b></h3>-->
		<?php if($isgtp_portable){ ?>
			<p><?php echo $this->lang->line("lodometer"); ?> <?php echo date("d/m/Y H:i:s", $this->period1); ?> - <?php echo date("d/m/Y H:i:s", $this->period2); ?> <b><? echo ($totalodometer1 >= 0) ? $totalodometer1 : 0;?> km</b>
			<br /><?php echo $this->lang->line("lodometer"); ?> <?php echo $this->lang->line("luntil1"); ?> <?php echo date("d/m/Y H:i:s", $this->period2); ?> <b><? echo ($totalodometer >= 0) ? number_format($totalodometer, 0, ".", ",") : 0;?> km</b>
		<?php } ?>
		<?php } else { ?>
			<p><?php echo $this->lang->line("lodometer"); ?> <?php echo date("d/m/Y H:i:s", $this->period1); ?> - <?php echo date("d/m/Y H:i:s", $this->period2); ?> <b><? echo ($totalodometer1 >= 0) ? $totalodometer1 : 0;?> km</b>
			<br /><?php echo $this->lang->line("lodometer"); ?> <?php echo $this->lang->line("luntil1"); ?> <?php echo date("d/m/Y H:i:s", $this->period2); ?> <b><? echo ($totalodometer >= 0) ? number_format($totalodometer, 0, ".", ",") : 0;?> km</b>
		<?php } ?>
		<br />
	<?php } ?>
        </div>

        <div class="block-border">
            <h1 style="font-size: 12px;">
            </h1>
            <br />
            <table class="table sortable no-margin" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th width="2%" style="text-align: center;">No.</td>
                        <th width="15%" colspan="2" style="text-align: center;"><?=$this->lang->line("ldatetime"); ?></th>
                        <th style="text-align: center;"><?=$this->lang->line("lposition"); ?></th>
                        <th width="10%" style="text-align: center;"><?=$this->lang->line("lcoordinate"); ?></th>
                        <?php if (($id == "overspeed") || ($id == "history")) { ?>
                        <?php if ($id == "history") { ?>
                        <th width="8%" style="text-align: center;"><?=$this->lang->line("lstatus"); ?></th>
                        <?php } ?>
                        <th width="8%" style="text-align: center;"><?=$this->lang->line("lspeed"); ?></th>
                        <?php } else if ($id == "parkingtime") { ?>
                        <th width="8%" style="text-align: center;"><?=$this->lang->line("lparking_time"); ?></th>
                        <?php } ?>
                        <?php if (isset($vehicle) && (in_array(strtoupper($vehicle->vehicle_type), $this->config->item("vehicle_gtp")))) { ?>
                         <?php
							if ($id == "history")
							{
								if (isset($gps_type) && ( $gps_type == "T5DOOR" || $gps_type == "TK315DOOR")) {
						?>
								<th width="8%" style="text-align: center;"><?php echo "Door Status"; ?></th>
						<?php
								}
								if (isset($gps_type) && $gps_type == "T5FAN") {
						?>
								<th width="8%" style="text-align: center;"><?php echo "Fan Status"; ?></th>
						<?php
								}
								if (isset($gps_type) && $gps_type == "T5PTO") {
						?>
								<th width="8%" style="text-align: center;"><?php echo "PTO Status"; ?></th>
						<?php } ?>
                        <th width="8%" style="text-align: center;"><?php echo $this->lang->line('lengine_1'); ?></th>
                        <th width="8%" style="text-align: center;"><?=$this->lang->line("lodometer"); ?> (km)</th>
                        <?php } ?>
                        <?php } ?>
						<?php if (isset($vehicle) && (in_array(strtoupper($vehicle->vehicle_type), $this->config->item("vehicle_odometer_portable")))) { ?>
							<th width="8%" style="text-align: center;"><?=$this->lang->line("lodometer"); ?> (km)</th>
						<?php } ?>
                        <th width="18px;" style="text-align: center;">&nbsp;</th>
                    </tr>
					</thead>

					<tbody>
                        <?php for($i=0; $i < count($data); $i++) { ?>
                        <tr <?=($i%2) ? "class='odd'" : "";?>>
                            <td><?=$i+1+$offset?></td>
                            <td><?=$data[$i]->gps_date_fmt;?></td>
                            <td><?=$data[$i]->gps_time_fmt;?></td>
                            <td>
								<?php
									if ($id == "parkingtime")
									{
										echo "<b>";
										echo "Geofence : "." ".$data[$i]->geofence;
										echo "</b>";
										echo "<br />";
									}
									echo $data[$i]->georeverse->display_name;
								?>
							</td>
                            <td><?=$data[$i]->gps_latitude_real_fmt;?> <?=$data[$i]->gps_longitude_real_fmt;?></td>
                                <?php if (($id == "overspeed") || ($id == "history")) { ?>
                                <?php if ($id == "history") { ?>
                            <!--<td style="text-align: center"><?php echo ($data[$i]->gps_status == "V") ? "NOT OK" : "OK"; ?></td>-->
							<td style="text-align: center"><?=$data[$i]->gps_status;?></td>
                                <?php } ?>
                            <td style="text-align: center;"><?=$data[$i]->gps_speed_fmt;?> <?=$this->lang->line("lkph"); ?></td>
                                <?php } else if ($id == "parkingtime") { ?>
                            <td style="text-align: center;"><?php echo isset($data[$i]->parkingtime_fmt) ? $data[$i]->parkingtime_fmt : "";?></td>
                        <?php } ?>
                        <?php if (isset($vehicle) && (in_array(strtoupper($vehicle->vehicle_type), $this->config->item("vehicle_gtp")))) { ?>
                         <?php if ($id == "history")
								{
								if (isset($gps_type) && ($gps_type == "T5FAN" || $gps_type == "T5DOOR" || $gps_type == "T5PTO" || $gps_type == "TK315DOOR"))
								{
						?>
								<th width="8%" style="text-align: center;">
									<?php
										if($data[$i]->fan == "1")
										{
											if (isset($gps_type) && ($gps_type == "T5DOOR" || $gps_type == "TK315DOOR"))
											{
												echo "OPEN";
											}
											else
											{
												echo "ON";
											}
										}
										else
										{
											if (isset($gps_type) && ($gps_type == "T5DOOR" || $gps_type == "TK315DOOR"))
											{
												echo "CLOSE";
											}
											else
											{
												echo "OFF";
											}
										}
									?>
								</th>
						<?php
								}
						?>
                            <td style="text-align: center;"><?php echo $data[$i]->status1; ?></td>
                            <td style="text-align: center;"><?php echo $data[$i]->odometer; ?></td>
                        <?php } ?>
                        <?php } ?>
						 <?php if (isset($vehicle) && (in_array(strtoupper($vehicle->vehicle_type), $this->config->item("vehicle_odometer_portable")))) { ?>
							<td style="text-align: center;"><?php echo $data[$i]->odometer; ?></td>
						 <?php }?>
                            <td><a href="<?=base_url(); ?>map/history/<?=$gps_name?>/<?=$gps_host?>/<?=$data[$i]->gps_id;?>"><img src="<?=base_url();?>assets/images/zoomin.gif" border="0"></a></td>
                        </tr>
                        <?php } ?>
                    </tbody>
            </table>
            <?php if (isset($vehicle) && (in_array(strtoupper($vehicle->vehicle_type), $this->config->item("vehicle_gtp")))) { ?>
            <?php if ($id == "history") { ?>
            <?=$paging;?>
            <?php } else { ?>
            <?=$paging;?>
            <?php } ?>
            <?php } else { ?>
            <?=$paging;?>
            <?php } ?>
        </div>
<!-- End content -->
<!-- end new table -->
