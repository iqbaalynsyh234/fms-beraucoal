<style>
.odometer{
	font-family: ds-digi;
	font-weight:bold;
	color:#00ff00;
	font-size:20px;
	padding:0px 7px;
	border:1px solid #00ff00;
	background-color:#000000;
	display:inline-block;
}
</style>
<div class="block-border">
    <form>
    <fieldset>
    <div id='spacetop'>&nbsp;</div>
    <table width="100%" cellpadding="0" cellspacing="0" class="tablelist1">
	   <tr style="border-top: 1px solid #ddd">
            <td width="30%"><?=$this->lang->line("ldatetime"); ?></td>
            <td width="5px;">:</td>
            <td><?=$data->gps_date_fmt?> <?=$data->gps_time_fmt?></td>
	   </tr>
       <tr>
            <td><?=$this->lang->line("lposition"); ?></td>
            <td>:</td>
            <td>
                <?php 
                //echo $vehicle->geofence_location; 
                ?>
                <?=isset($data->georeverse->display_name) ? $data->georeverse->display_name : "Unknown address";?></td>
       </tr>		
       
       <tr>
            <td><?=$this->lang->line("lcoordinate"); ?></td>
            <td>:</td>
<?php
			$dir = $data->car_icon-1;			
			$dirs = $this->config->item("direction");
			
			if ($dir < 0)
			{
				$sdir = "Utara";
			}
			else
			if ($dir >= count($dirs))
			{
				$sdir = "Utara";
			}
			else
			{
				$sdir = $dirs[$dir];
			}
?>		
		<td><a href="#" onclick="javascript:setcenter(<?=$data->gps_latitude_real_fmt?>, <?=$data->gps_longitude_real_fmt?>)"><?=$data->gps_latitude_real_fmt?>, <?=$data->gps_longitude_real_fmt?>&nbsp;&nbsp;&nbsp;<?php echo $sdir; ?> (<?=$data->gps_course; ?>&deg;)</a></td>
	</tr>
	
	<tr>
		<td><?=$this->lang->line("lspeed"); ?></td>
		<td>:</td>
		<td><?=number_format($data->gps_speed*1.852, 0, "", ",")?> km/jam</td>
	</tr>
	
	<?php if (in_array(strtoupper($vehicle->vehicle_type), $this->config->item("vehicle_gtp"))) { ?>
	<tr>
		<td><?=$this->lang->line("lvehicle_status"); ?></td>
		<td>:</td>
		<td>
			<?php echo $this->lang->line('lengine_1'); ?>  <?php echo ($vehicle->status1) ? $this->lang->line('lon') : $this->lang->line('loff'); ?>
			<?php if (in_array(strtoupper($vehicle->vehicle_type), $this->config->item("vehicle_gtp_engine2"))) { ?>
			,
			<!-- <?php echo $this->lang->line('lengine_2'); ?>  --><?php echo ($vehicle->status2) ? $this->lang->line('lrelease') : $this->lang->line('lunrelease'); ?>
			<?php } ?>
		</td>
	</tr>	
	<?php } ?>
	
	<tr>
		<td><?=$this->lang->line("lstatus"); ?></td>
		<td>:</td>
		<td><?=($data->gps_status == "V") ? "NO" : "OK"; ?></td>
	</tr>	

	<?php 
		if ($this->config->item("app_tupperware"))
		{
	?>
	<tr>
		<td>No. SO</td>
		<td>:</td>
		<td>
			<?php 
				if (isset($vehicle->noso))
				{
					$x_so = explode("|",$vehicle->noso);
					if (isset($x_so) && count($x_so)>0)
					{
						for ($i=0;$i<count($x_so);$i++)
						{
							echo $x_so[$i];
							if (isset($x_so[$i+1]) && $x_so[$i+1] != "")
							{
								echo ",";
								echo " ";
							}
						}
					}
				}
			?>
		</td>
	</tr>
	<tr>
		<td>No. DR</td>
		<td>:</td>
		<td>
			<?php 
				if (isset($vehicle->nodr))
				{
					$x_so = explode("|",$vehicle->nodr);
					if (isset($x_so) && count($x_so)>0)
					{
						for ($i=0;$i<count($x_so);$i++)
						{
							echo $x_so[$i];
							if (isset($x_so[$i+1]) && $x_so[$i+1] != "")
							{
								echo ",";
								echo " ";
							}
						}
					}
				}
			?>
		</td>
	</tr>
	<tr>
		<td>DB.Code</td>
		<td>:</td>
		<td>
			<?php 
				if (isset($vehicle->dbcode))
				{
					$x_so = explode("|",$vehicle->dbcode);
					if (isset($x_so) && count($x_so)>0)
					{
						for ($i=0;$i<count($x_so);$i++)
						{
							echo $x_so[$i];
							if (isset($x_so[$i+1]) && $x_so[$i+1] != "")
							{
								echo ",";
								echo " ";
							}
						}
					}
				}
			?>
		</td>
	</tr>
	<?php
		}
	?>
</table>
</fieldset>	
</form>
</div>