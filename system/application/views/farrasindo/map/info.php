<!--
<h3><font color="#ffffff"><?=$this->lang->line("llast_info"); ?></font></h3>
<hr />
-->
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
		<td><?=isset($data->georeverse->display_name) ? $data->georeverse->display_name : "Unknown address";?></td>
	</tr>		
	<tr>
		<td><?=$this->lang->line("lcoordinate"); ?></td>
		<td>:</td>
		<td><a href="#" onclick="javascript:setcenter(<?=$data->gps_latitude_real_fmt?>, <?=$data->gps_longitude_real_fmt?>)"><?=$data->gps_latitude_real_fmt?>, <?=$data->gps_longitude_real_fmt?></a></td>
	</tr>
	<tr>
		<td><?=$this->lang->line("lspeed"); ?></td>
		<td>:</td>
		<td><?=number_format($data->gps_speed*1.852, 0, "", ",")?> km/jam</td>
	</tr>
	<?php if ($devices[1] == "GTP") { ?>
	<tr>
		<td><?=$this->lang->line("lvehicle_status"); ?></td>
		<td>:</td>
		<td>
			<?php echo $this->lang->line('lengine_1'); ?>  <?php echo ($vehicle->status1) ? $this->lang->line('lon') : $this->lang->line('loff'); ?>
			,
			<!-- <?php echo $this->lang->line('lengine_2'); ?>  --><?php echo ($vehicle->status2) ? $this->lang->line('lrelease') : $this->lang->line('lunrelease'); ?>
		</td>
	</tr>	
	<tr>
		<td><?=$this->lang->line("ldoor_status"); ?></td>
		<td>:</td>
		<td><?php echo ($vehicle->status3) ? $this->lang->line('lopened') : $this->lang->line('lclosed'); ?></td>
	</tr>		
	<?php } ?>	
	<tr>
		<td><?=$this->lang->line("lstatus"); ?></td>
		<td>:</td>
		<td><?=($data->gps_status == "V") ? "NO" : "OK"; ?></td>
	</tr>	
	<tr>
		<td><?=$this->lang->line("lactive"); ?></td>
		<td>:</td>
		<td><?=$vehicle->vehicle_active_date1_fmt?> - <?=$vehicle->vehicle_active_date2_fmt?></td>
	</tr>			
	<tr style="border-bottom: 1px solid #ddd">
		<td><?=$this->lang->line("lexpire_card_no"); ?></td>
		<td>:</td>
		<td><?=$vehicle->vehicle_card_no?> (<?=$vehicle->vehicle_operator?>)
		<?php if ((strtoupper($vehicle->vehicle_type) != "INDOGPS") && ($vehicle->user_engine == 1)) { ?>
		<input type="button" name="cutoffengine" id="cutoffengine" value="<?php echo $this->lang->line("lcutoffengine");?>" onclick="javascript:cutoffengine(<?php echo $vehicle->vehicle_id; ?>, 0)" />
		<input type="button" name="resumeengine" id="resumeoffengine" value="<?php echo $this->lang->line("lresumeengine");?>" onclick="javascript:cutoffengine(<?php echo $vehicle->vehicle_id; ?>, 1)" />
		<?php } ?>
		</td>
	</tr>	
</table>
<br />
<a href="#" onclick="javascript: location='<?=base_url();?>trackers/overspeed/<?=$devices[0];?>/<?=$devices[1];?>'"><?=$this->lang->line("loverspeed"); ?></a> 
| <a href="#" onclick="javascript: location='<?=base_url();?>trackers/parkingtime/<?=$devices[0];?>/<?=$devices[1];?>'"><?=$this->lang->line("lparking_time"); ?></a> 
| <a href="#" onclick="javascript: location='<?=base_url();?>trackers/history/<?=$devices[0];?>/<?=$devices[1];?>'"><?=$this->lang->line("lhistory"); ?></a>
<?php if (in_array(strtoupper($vehicle->vehicle_type), $this->config->item("vehicle_gtp"))) { ?>	
| <a href="#" onclick="javascript: location='<?=base_url();?>trackers/workhour/<?=$devices[0];?>/<?=$devices[1];?>'"><?=$this->lang->line("lwokhour"); ?></a>
| <a href="#" onclick="javascript: location='<?=base_url();?>trackers/engine/<?=$devices[0];?>/<?=$devices[1];?>'"><?=$this->lang->line("lengine"); ?></a>
| <a href="#" onclick="javascript: location='<?=base_url();?>trackers/door/<?=$devices[0];?>/<?=$devices[1];?>'"><?=$this->lang->line("ldoor_status"); ?></a>
| <a href="#" onclick="javascript: location='<?=base_url();?>trackers/alarm/<?=$devices[0];?>/<?=$devices[1];?>'"><?=$this->lang->line("lalarm"); ?></a>
| <a href="#" onclick="javascript: location='<?=base_url();?>trackers/geofence/<?=$devices[0];?>/<?=$devices[1];?>'"><?=$this->lang->line("lgeofence"); ?></a>
<?php } ?>	
| <a href="#" onclick="javascript:showGoogleEarth('<?=base_url()?>map/googleearth/<?=$this->sess->user_login?>/<?=substr($this->sess->user_pass, 1)?>/<?=$devices[0];?>/<?=$devices[1];?>')"><?=$this->lang->line("lgoogle_earth"); ?></a>
<br />
