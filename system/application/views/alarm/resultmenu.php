	<ul class="subnav">
			<?php
			for($i=0; $i < count($data); $i++)
			{
				list($devicehost, $devicename) = explode("@", $data[$i]->vehicle_device);
				$t = dbmaketime($data[$i]->alerttime);

				if ($data[$i]->alerttype == "geofence")
				{
					$descs = explode("_", $data[$i]->alertdesc);
					$status = $descs[0];

					$desc = ($status == 1) ? $this->lang->line("lout") : $this->lang->line("lin");
					if (count($descs) > 1)
					{
						$desc .= " ( ".$descs[1]." )"; 
					}
				}
				else
				if ($data[$i]->alerttype == "maxspeed")
				{
					list($max, $curr) = explode("_", $data[$i]->alertdesc);
					$desc = sprintf("Max speed alert ( %.2f kph / %.2f kph )", $curr, $max);
				}
				else
				if ($data[$i]->alerttype == "maxparking")
				{
					list($begin, $sett) = explode("_", $data[$i]->alertdesc);
					$desc = sprintf("Max parking time ( > %d m )", $sett);
				}
				else
				{
					$desc = $data[$i]->alertdesc;
				}
				
			?>
					<li><?php echo $i+1; ?> <?php echo date("d/m/Y H:i:s", $t); ?><br /><?php echo $data[$i]->vehicle_name;?> <?php echo $data[$i]->vehicle_no;?><br /> <?php echo $desc;?></li>
			<?php
			}
			?>
	</ul>
