	<?php if ($id == "history") { ?>
	<script>
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


				var ref = '<a href="<?=base_url();?>map/historyfull?dummy=on&vehicle=<?=$vehicle->vehicle_id;?>';
				<?php for($i=0; $i < count($data); $i++) { ?>
					addMarker(<?=$i+1?>, '<?=$data[$i]->gps_longitude_real_fmt;?>', '<?=$data[$i]->gps_latitude_real_fmt;?>', <?=$vehicle->vehicle_id;?>);
					ref += '&lnglat[]=' + '<?=$data[$i]->gps_longitude_real_fmt;?>,<?=$data[$i]->gps_latitude_real_fmt;?>';
				<?php } ?>

				ref += '" target="_blank"><font color="#000000">[ <?=$this->lang->line('lfull_size');?> ]</font></a>';
				jQuery("#refmap").html(ref);

				<?php } ?>
			}
		);
		
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

			kml_tracker5.size = new OpenLayers.Size(-26, -30);

			popup = new OpenLayers.Popup.FramedCloud(
				"featurePopup"
				, center
				, new OpenLayers.Size(43, 33)
				, "<div id='pup'>" + no + "</div>"
				, kml_tracker5
				, false,
                null
			);

            popup.autoSize = false;
            popup.calculateRelativePosition = function(){
                   return 'tr';
               }
            var popup = map.addPopup(popup);

        }
	</script>
	<?php } ?>
	<?php if ($id == "history") { ?>
<font size="1"><b>Odometer: <font color="green"><?=$odometer;?></font></b></font><br />
		<?php if ($gps_host == "GTP") { ?>
		<?php } ?>
		<br />
	<?php } ?>
		<table width="100%" cellpadding="3" class="tablelist">
			<thead bgcolor="#CC6600">
				<tr>
					<th width="2%"><font size="1"><b>No.</b></font></td>
					<th width="15%" colspan="2"><font size="1"><b><?=$this->lang->line("ldatetime"); ?></b></font></th>
					<th><font size="1"><b><?=$this->lang->line("lposition"); ?></b></font></th>
					<th width="10%"><font size="1"><b><?=$this->lang->line("lcoordinate"); ?></b></font></th>
					<?php if (($id == "overspeed") || ($id == "history")) { ?>
					<th width="8%"><font size="1"><b><?=$this->lang->line("lspeed"); ?></b></font></th>
					<?php } else if ($id == "parkingtime") { ?>
					<th width="8%"><font size="1"><b><?=$this->lang->line("lparking_time"); ?></b></font></th>
					<?php } ?>
					<th width="18px;">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
			<?php for($i=0; $i < count($data); $i++) { ?>
				<tr <?=($i%2) ? "class='odd'" : "";?>>
					<td><font size="1"><b><?=$i+1+$offset?></b></font></td>
					<td><font size="1"><b><?=$data[$i]->gps_date_fmt;?></b></font></td>
					<td><font size="1"><b><?=$data[$i]->gps_time_fmt;?></b></font></td>
					<td><font size="1"><b><?=$data[$i]->georeverse->display_name;?></b></font></td>
					<td><font size="1"><b><?=$data[$i]->gps_longitude_real_fmt;?> <?=$data[$i]->gps_latitude_real_fmt;?></b></font></td>
					<?php if (($id == "overspeed") || ($id == "history")) { ?>
					<td style="text-align: right"><font size="1"><b><?=$data[$i]->gps_speed_fmt;?> <?=$this->lang->line("lkph"); ?></b></font></td>
					<?php } else if ($id == "parkingtime") { ?>
					<td style="text-align: right"><font size="1"><b><?php echo isset($data[$i]->parkingtime_fmt) ? $data[$i]->parkingtime_fmt : "";?></b></font></td>
					<?php } ?>
					<td><font size="1"><b><a href="<?=base_url(); ?>map/history/<?=$gps_name?>/<?=$gps_host?>/<?=$data[$i]->gps_id;?>"><img src="<?=base_url();?>assets/images/zoomin.gif" border="0"></b></font></a></td>
				</tr>
			<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="7"><font size="1"><b><?=$paging;?></b></font></td>
				</tr>
			</tfoot>
		</table>