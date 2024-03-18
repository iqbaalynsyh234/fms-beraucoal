<!--
<h3><font color="#ffffff"><?=$this->lang->line("llast_info"); ?></font></h3>
<hr />
-->
<style>

.odometer{
	font-family: ds-digi;
	font-weight:bold;
	color:#00ff00;
	font-size:14px;
	padding:0px 7px;
	border:1px solid #00ff00;
	background-color:#000000;
	display:inline-block;
}

</style>
<?php 
$fontsize="2px";
$fontcolor="yellow";

?>
<div class="block-border">
    <form>
    <fieldset>
    <div id='spacetop'>&nbsp;</div>
    <table width="100%" cellpadding="0" cellspacing="0">
	
	   <tr style="border-top: 1px solid #ddd"> 
            <td width="30%"><font size="<?=$fontsize;?>" color="<?=$fontcolor;?>"><?=$this->lang->line("ldatetime"); ?></td>
            <td width="5px;"><font size="<?=$fontsize;?>" color="<?=$fontcolor;?>">:</td>
            <td><font size="<?=$fontsize;?>" color="<?=$fontcolor;?>"><?=$data->gps_date_fmt?> <?=$data->gps_time_fmt?></td>
	   </tr>
	   
       <tr>
            <td><font size="<?=$fontsize;?>" color="<?=$fontcolor;?>"><?=$this->lang->line("lposition"); ?></td>
            <td><font size="<?=$fontsize;?>" color="<?=$fontcolor;?>">:</td>
            <td><font size="<?=$fontsize;?>" color="<?=$fontcolor;?>">
                <?php 
                //echo $vehicle->geofence_location; 
                ?>
                <?=isset($data->georeverse->display_name) ? $data->georeverse->display_name : "Unknown address";?></td>
       </tr>		
       
      <tr>
            <td><font size="<?=$fontsize;?>" color="<?=$fontcolor;?>"><?=$this->lang->line("lcoordinate"); ?></td>
            <td><font size="<?=$fontsize;?>" color="<?=$fontcolor;?>">:</td>
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
		<td><font size="<?=$fontsize;?>" color="<?=$fontcolor;?>">
			<a href="http://www.google.com/maps?q=<?=$data->gps_latitude_real_fmt?>,<?=$data->gps_longitude_real_fmt?>" target="_blank">
				<?=$data->gps_latitude_real_fmt?>,<?=$data->gps_longitude_real_fmt?>&nbsp;&nbsp;&nbsp;<?php echo $sdir; ?> (<?=$data->gps_course; ?>&deg;)
			</a>
		</td>
	</tr>
	
	<tr>
		<td><font size="<?=$fontsize;?>" color="<?=$fontcolor;?>"><?=$this->lang->line("lspeed"); ?></td>
		<td><font size="<?=$fontsize;?>" color="<?=$fontcolor;?>">:</td>
		<td><font size="<?=$fontsize;?>" color="<?=$fontcolor;?>"><?=number_format($data->gps_speed*1.852, 0, "", ",")?> km/jam</td>
	</tr>
	
	<?php if ($this->sess->user_group == 0) { ?>
	<?php if (in_array(strtoupper($vehicle->vehicle_type), $this->config->item("vehicle_gtp"))) { ?>
	<tr>
		<td><font size="<?=$fontsize;?>" color="<?=$fontcolor;?>"><?=$this->lang->line("lvehicle_status"); ?></td>
		<td><font size="<?=$fontsize;?>" color="<?=$fontcolor;?>">:</td>
		<td><font size="<?=$fontsize;?>" color="<?=$fontcolor;?>">
			<?php echo $this->lang->line('lengine_1'); ?>  <?php echo ($vehicle->status1) ? $this->lang->line('lon') : $this->lang->line('loff'); ?>
			<?php if (in_array(strtoupper($vehicle->vehicle_type), $this->config->item("vehicle_gtp_engine2"))) { ?>
			,
			<!-- <?php echo $this->lang->line('lengine_2'); ?>  --><?php echo ($vehicle->status2) ? $this->lang->line('lrelease') : $this->lang->line('lunrelease'); ?>
			<?php } ?>
		</td>
	</tr>	
	<?php } ?>
	<?php } ?>
	
	<?php 
	if ($this->sess->user_type != 2 && $this->sess->user_agent != "1") {
	if (in_array(strtoupper($vehicle->vehicle_type), $this->config->item("vehicle_gtp_door"))) { ?>
	<tr>
		<td><font size="<?=$fontsize;?>" color="<?=$fontcolor;?>"><?=$this->lang->line("ldoor_status"); ?></td>
		<td><font size="<?=$fontsize;?>" color="<?=$fontcolor;?>">:</td>
		<td><font size="<?=$fontsize;?>" color="<?=$fontcolor;?>"><?php echo ($vehicle->status3) ? $this->lang->line('lopened') : $this->lang->line('lclosed'); ?></td>
	</tr>		
	<?php } } ?>
	
	<?php if ($this->sess->user_group == 0) { ?>
	<?php if (in_array(strtoupper($vehicle->vehicle_type), $this->config->item("vehicle_fuel"))) { ?>
	<tr>
		<td><font size="<?=$fontsize;?>" color="<?=$fontcolor;?>"><?=$this->lang->line("lfuel"); ?></td>
		<td><font size="<?=$fontsize;?>" color="<?=$fontcolor;?>">:</td>
		<td><font size="<?=$fontsize;?>" color="<?=$fontcolor;?>">
		<?php 
		if(isset($vehicle->fuel_scale)){
			$img = "level_new_" . $vehicle->fuel_scale . ".gif";
			if($vehicle->blink){
				$img = "level_new_" . $vehicle->fuel_scale . "_blink.gif";
			}
			
			echo "<img src='" . base_url() . "assets/images/e_new.gif' /><img src='" . base_url() . "assets/images/" . $img . "' /><img src='" . base_url() . "assets/images/f_new.gif' />";
		}
			echo "<img src='" . base_url() . "assets/images/fuel_pump.png' /> "  . $vehicle->fuel ;
		?>
		</td>
	</tr>		
	<?php } ?>
	<?php } ?>
	
	<?php if ($this->sess->user_group == 0) { ?>
	<?php if (in_array(strtoupper($vehicle->vehicle_type), $this->config->item("vehicle_odometer"))) { ?>
	<tr>
		<td><font size="<?=$fontsize;?>" color="<?=$fontcolor;?>"><?=$this->lang->line("lodometer"); ?></td>
		<td><font size="<?=$fontsize;?>" color="<?=$fontcolor;?>">:</td>
		<td><font size="<?=$fontsize;?>" color="<?=$fontcolor;?>">
		<?php 
		if(isset($vehicle->totalodometer)){
			echo "<div class='odometer'>" . $vehicle->totalodometer . "</div> km";
		}
		?>
		</td>
	</tr>		
	<?php } ?>
	<?php } ?>
	
	<tr>
		<td><font size="<?=$fontsize;?>" color="<?=$fontcolor;?>"><?=$this->lang->line("lstatus"); ?></td>
		<td><font size="<?=$fontsize;?>" color="<?=$fontcolor;?>">:</td>
		<td><font size="<?=$fontsize;?>" color="<?=$fontcolor;?>"><?=($data->gps_status == "V") ? "NO" : "OK"; ?></td>
	</tr>	
    
	<?php if ($this->sess->user_group == 0) { ?>
	<tr style="border-bottom: 1px solid #ddd">
		<td><font size="<?=$fontsize;?>" color="<?=$fontcolor;?>"><?=$this->lang->line("lexpire_card_no"); ?></td>
		<td><font size="<?=$fontsize;?>" color="<?=$fontcolor;?>">:</td>
		<td><font size="<?=$fontsize;?>" color="<?=$fontcolor;?>"><?=$vehicle->vehicle_card_no?>
			(<?=$vehicle->vehicle_operator?>)
		<?php $vehiclewithpulse = $this->config->item("vehicle_pulse"); if ((($this->sess->user_type == 1) || (($this->sess->user_type == 3) && ($this->sess->user_agent_admin == 1)) || $this->sess->user_payment_pulsa) && in_array($vehicle->vehicle_type, $vehiclewithpulse)) 
		{ 
		if (isset($vehicle->pulse) && $vehicle->pulse) {
		printf("<br />%s<br />", $vehicle->pulse . " ( " . $vehicle->masaaktif . " ) " );} 
		} ?>
       
		</td>
	</tr>
	<?php } ?>
	
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
<br />

<?php if ($this->sess->user_group != 0) { ?><strong><font size="<?=$fontsize;?>" color="<?=$fontcolor;?>">
<a href="#" onclick="javascript: location='<?=base_url();?>trackers/overspeed/<?=$devices[0];?>/<?=$devices[1];?>'"><?=$this->lang->line("loverspeed"); ?></a> 
| <a href="#" onclick="javascript: location='<?=base_url();?>trackers/parkingtime/<?=$devices[0];?>/<?=$devices[1];?>'"><?=$this->lang->line("lparking_time"); ?></a> 
| <a href="#" onclick="javascript: location='<?=base_url();?>trackers/history/<?=$devices[0];?>/<?=$devices[1];?>'"><?=$this->lang->line("lhistory"); ?></a>
<?php if (in_array(strtoupper($vehicle->vehicle_type), $this->config->item("vehicle_gtp"))) { ?>	
| <a href="#" onclick="javascript: location='<?=base_url();?>trackers/workhour/<?=$devices[0];?>/<?=$devices[1];?>'"><?=$this->lang->line("lwokhour"); ?></a>
<?php }}  ?>

</div>

