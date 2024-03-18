<!--
<h3><font color="#ffffff"><?=$this->lang->line("llast_info"); ?></font></h3>
<hr />
-->
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
	
	<?php if ($this->sess->user_group == 0) { ?>
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
	<?php } ?>
	
	<?php 
	if ($this->sess->user_type != 2 && $this->sess->user_agent != "1") {
	if (in_array(strtoupper($vehicle->vehicle_type), $this->config->item("vehicle_gtp_door"))) { ?>
	<tr>
		<td><?=$this->lang->line("ldoor_status"); ?></td>
		<td>:</td>
		<td><?php echo ($vehicle->status3) ? $this->lang->line('lopened') : $this->lang->line('lclosed'); ?></td>
	</tr>		
	<?php } } ?>
	
	<?php if ($this->sess->user_group == 0) { ?>
	<?php if (in_array(strtoupper($vehicle->vehicle_type), $this->config->item("vehicle_fuel"))) { ?>
	<tr>
		<td><?=$this->lang->line("lfuel"); ?></td>
		<td>:</td>
		<td>
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
		<td><?=$this->lang->line("lodometer"); ?></td>
		<td>:</td>
		<td>
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
		<td><?=$this->lang->line("lstatus"); ?></td>
		<td>:</td>
		<td><?=($data->gps_status == "V") ? "NO" : "OK"; ?></td>
	</tr>	
    
	<!--
	<?php if ($this->sess->user_group == 0) { ?>
	<tr>
		<td><?=$this->lang->line("lactive"); ?></td>
		<td>:</td>
		<td><?=$vehicle->vehicle_active_date1_fmt?> - <?=$vehicle->vehicle_active_date2_fmt?></td>
	</tr>		
	<?php } ?>
    -->
	
	<?php if ($this->sess->user_group == 0 && (!in_array($this->sess->user_id, $this->config->item("user_hide_simno")))) { ?>
	<tr style="border-bottom: 1px solid #ddd">
		<td><?=$this->lang->line("lexpire_card_no"); ?></td>
		<td>:</td>
		<td>
		<?=$vehicle->vehicle_card_no?>
		(<?=$vehicle->vehicle_operator?>)
		<?php $vehiclewithpulse = $this->config->item("vehicle_pulse"); if ((($this->sess->user_type == 1) || (($this->sess->user_type == 3) && ($this->sess->user_agent_admin == 1)) || $this->sess->user_payment_pulsa) && in_array($vehicle->vehicle_type, $vehiclewithpulse)) 
		{ 
		if (isset($vehicle->pulse) && $vehicle->pulse) {
		printf("<br />%s<br />", $vehicle->pulse . " ( " . $vehicle->masaaktif . " ) " );} 
		} ?>
        <!--
		<?php if ((strtoupper($vehicle->vehicle_type) != "INDOGPS") && ($this->sess->user_engine == 1)) { ?>
		<input type="button" name="cutoffengine" id="cutoffengine" value="<?php echo $this->lang->line("lcutoffengine");?>" onclick="javascript:cutoffengine(<?php echo $vehicle->vehicle_id; ?>, 0)" />
		<input type="button" name="resumeengine" id="resumeoffengine" value="<?php echo $this->lang->line("lresumeengine");?>" onclick="javascript:cutoffengine(<?php echo $vehicle->vehicle_id; ?>, 1)" />
		<?php } ?>
        -->
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

<?php if ($this->sess->user_group == 0) { ?>
<a href="#" onclick="javascript: location='<?=base_url();?>trackers/overspeed/<?=$devices[0];?>/<?=$devices[1];?>'"><?=$this->lang->line("loverspeed"); ?></a> 
| <a href="#" onclick="javascript: location='<?=base_url();?>trackers/parkingtime/<?=$devices[0];?>/<?=$devices[1];?>'"><?=$this->lang->line("lparking_time"); ?></a> 
| <a href="#" onclick="javascript: location='<?=base_url();?>trackers/history/<?=$devices[0];?>/<?=$devices[1];?>'"><?=$this->lang->line("lhistory"); ?></a>
<?php if (in_array(strtoupper($vehicle->vehicle_type), $this->config->item("vehicle_gtp"))) { ?>	
| <a href="#" onclick="javascript: location='<?=base_url();?>trackers/workhour/<?=$devices[0];?>/<?=$devices[1];?>'"><?=$this->lang->line("lwokhour"); ?></a>
<!--| <a href="#" onclick="javascript: location='<?=base_url();?>trackers/engine/<?=$devices[0];?>/<?=$devices[1];?>'"><?=$this->lang->line("lengine"); ?></a>-->
<?php 
if ($this->sess->user_type != 2 && $this->sess->user_agent != "1") {
if (in_array(strtoupper($vehicle->vehicle_type), $this->config->item("vehicle_gtp_door"))) { ?>
<!--| <a href="#" onclick="javascript: location='<?=base_url();?>trackers/door/<?=$devices[0];?>/<?=$devices[1];?>'"><?=$this->lang->line("ldoor_status"); ?></a>-->
<?php } } ?>
<!--| <a href="#" onclick="javascript: location='<?=base_url();?>trackers/odometer/<?=$devices[0];?>/<?=$devices[1];?>'"><?=$this->lang->line("lodometer"); ?></a>-->
<!--| <a href="#" onclick="javascript: location='<?=base_url();?>trackers/alarm/<?=$devices[0];?>/<?=$devices[1];?>'"><?=$this->lang->line("lalarm"); ?></a>-->
| <a href="#" onclick="javascript: location='<?=base_url();?>trackers/geofence/<?=$devices[0];?>/<?=$devices[1];?>'"><?=$this->lang->line("lgeofence"); ?></a>
<?php } ?>	
| <a href="#" onclick="javascript:showGoogleEarth('<?=base_url()?>map/googleearth/<?=$this->sess->user_login?>/<?=substr($this->sess->user_pass, 1)?>/<?=$devices[0];?>/<?=$devices[1];?>')"><?=$this->lang->line("lgoogle_earth"); ?></a>
<?php } ?>
<br />
</div>

