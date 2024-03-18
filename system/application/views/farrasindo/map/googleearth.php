<?php
echo '<';
echo '?xml version="1.0" encoding="UTF-8"';
echo '?';
echo '>';

$dir = $info->vehicle_image ? $info->vehicle_image : "car";

?>

<kml xmlns="http://earth.google.com/kml/2.0">
	<?php if (isset($info)) { ?>
		<Document>
			<Style id="normalPlacemark">
				<LabelStyle>
				<scale>0.7</scale>
				</LabelStyle>
				<IconStyle>
					<scale>0.65</scale>
					<Icon>
					<?php if ($info->gps->css_delay_index == 0) { ?>
					<href><?=base_url()?>assets/images/<?php echo $dir; ?>/car4earth-red.png</href>
					<?php } else if ($info->gps->css_delay_index == 1) { ?>
					<href><?=base_url()?>assets/images/<?php echo $dir; ?>/car4earth-yellow.png</href>
					<?php } else { ?>
					<href><?=base_url()?>assets/images/<?php echo $dir; ?>/car4earth.png</href>
					<?php } ?>
					</Icon>
				</IconStyle>
			</Style>
			<Style id="linestyle1">
				<LineStyle>
			      		<color>ff0000ff</color>
			      		<width>2</width>			      		
			    	</LineStyle>
			</Style>
	  		<Placemark>
	    			<name><?=$info->vehicle_name?> <?=$info->vehicle_no?></name>
	    			<styleUrl>#normalPlacemark</styleUrl>
	    			<description>
	    				Tanggal: <?=$info->gps->gps_date_fmt?><?="\r\n"?>
	    				Jam: <?=$info->gps->gps_time_fmt?> WIB<?="\r\n"?>
	    				Posisi: <?=$info->gps->georeverse->display_name?><?="\r\n"?>
	    				Koordinat: <?=$info->gps->gps_latitude_real?>,<?=$info->gps->gps_longitude_real?><?="\r\n"?>
	    				Kecepatan: <?=$info->gps->gps_speed_fmt?> km/h<?="\r\n"?>
	    				Sinyal GPS: <? echo ($info->gps->gps_status == "A") ? "OK" : "NO";?><?="\r\n"?>
	    				Aktif: <?=$info->vehicle_active_date1_fmt?> - <?=$info->vehicle_active_date2_fmt?><?="\r\n"?>
	    				No Kartu: <?=$info->vehicle_card_no?>
	    			</description>
				<Camera>
					<longitude><?=$info->gps->gps_longitude_real?></longitude>
					<latitude><?=$info->gps->gps_latitude_real?></latitude>
				</Camera>	    			
	    			<Point>
	      				<coordinates>	      					
     						<?=$info->gps->gps_longitude_real?>,<?=$info->gps->gps_latitude_real?>,0
	      				</coordinates>
	    			</Point>
	  		</Placemark>
	  		<Placemark>
	  				<name><?=$info->vehicle_name?> <?=$info->vehicle_no?></name>
	    			<description>
	    				Tanggal: <?=$info->gps->gps_date_fmt?><?="\r\n"?>
	    				Jam: <?=$info->gps->gps_time_fmt?> WIB<?="\r\n"?>
	    				Posisi: <?=$info->gps->georeverse->display_name?><?="\r\n"?>
	    				Koordinat: <?=$info->gps->gps_latitude_real?>,<?=$info->gps->gps_longitude_real?><?="\r\n"?>
	    				Kecepatan: <?=$info->gps->gps_speed_fmt?> km/h<?="\r\n"?>
	    				Sinyal GPS: <? echo ($info->gps->gps_status == "A") ? "OK" : "NO";?><?="\r\n"?>
	    				Aktif: <?=$info->vehicle_active_date1_fmt?> - <?=$info->vehicle_active_date2_fmt?><?="\r\n"?>
	    				No Kartu: <?=$info->vehicle_card_no?>
	    			</description>
	    			<styleUrl>#linestyle1</styleUrl>
	    			<LineString>
	      				<coordinates>
	      					<?php 
	      						for($i=0; $i < count($infoall); $i++) 
	      						{ 
									if ($i >= 2) break;
									$lng = getLongitude($infoall[$i]->gps_longitude, $infoall[$i]->gps_ew);
									$lat = getLatitude($infoall[$i]->gps_latitude, $infoall[$i]->gps_ns);	      					
									
									echo " ".$lng.",".$lat.",0";
	      						} 
	      					?>
	      				</coordinates>
	    			</LineString>
	  		</Placemark>	  		
	  	</Document>		
	<?php } ?>
</kml>
