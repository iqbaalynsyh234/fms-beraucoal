<!--
<h3><font color="#ffffff"><?=$this->lang->line("llast_info"); ?></font></h3>
<hr />
-->
<div id='left'>&nbsp;</div>
<table width="100%" cellpadding="0" cellspacing="0" class="tablelist1">
	<tr style="border-top: 1px solid #ddd">
		<td width="10%"><font size="1"><b><?=$this->lang->line("ldatetime"); ?></b></font></td>
		<td width="5px;">:</td>
		<td><font size="1"><b><?=$data->gps_date_fmt?><br /><?=$data->gps_time_fmt?></b></font></td>
	</tr>
	<tr>
		<td><font size="1"><b><?=$this->lang->line("lposition"); ?></b></font></td>
		<td>:</td>
		<td><font size="1"><b><?=isset($data->georeverse->display_name) ? $data->georeverse->display_name : "Unknown address";?></b></font></td>
	</tr>
	<tr>
		<td><font size="1"><b><?=$this->lang->line("lcoordinate"); ?></b></font></td>
		<td>:</td>
		<td><font size="1"><b><a href="#" onclick="javascript:setcenter(<?=$data->gps_latitude_real_fmt?>, <?=$data->gps_longitude_real_fmt?>)"><?=$data->gps_latitude_real_fmt?>, <?=$data->gps_longitude_real_fmt?></a></b></font></td>
	</tr>
	<tr>
		<td><font size="1"><b><?=$this->lang->line("lspeed"); ?></b></font></td>
		<td>:</td>
		<td><font size="1"><b><?=number_format($data->gps_speed*1.852, 0, "", ",")?> km/jam</b></font></td>
	</tr>
	<tr>
		<td><font size="1"><b>GPS Signal</b></font></td>
		<td>:</td>
		<td><font size="1"><b><?=($data->gps_status == "V") ? "NO" : "OK"; ?></b></font></td>
	</tr>
	<tr>
		<td><font size="1"><b><?=$this->lang->line("lactive"); ?></b></font></td>
		<td>:</td>
		<td><font size="1"><b><?=$vehicle->vehicle_active_date1_fmt?> - <?=$vehicle->vehicle_active_date2_fmt?></b></font></td>
	</tr>
	<tr<?php if ($devices[1] != "GTP") { ?> style="border-bottom: 1px solid #ddd"<?php } ?>>
		<td><font size="1"><b><?=$this->lang->line("lexpire_card_no"); ?></b></font></td>
		<td>:</td>
		<td><font size="1"><b><?=$vehicle->vehicle_card_no?> (<?=$vehicle->vehicle_operator?>)</b></font></td>
	</tr>
	<?php if ($devices[1] == "GTP") { ?>
	<tr style="border-bottom: 1px solid #ddd">
		<td><font size="1"><b><?=$this->lang->line("lvehicle_status"); ?></b></font></td>
		<td>:</td>
		<td><font size="1"><b>
			<?php echo $this->lang->line('lengine_1'); ?>  <?php echo ($vehicle->status1) ? $this->lang->line('lon') : $this->lang->line('loff'); ?></b></font>
		
		</td>
	</tr>
	<?php } ?>

</table>
<br /><font size="1"><b>
<a href="#" onclick="javascript: location='<?=base_url();?>trackers/overspeed/<?=$devices[0];?>/<?=$devices[1];?>'"><?=$this->lang->line("loverspeed"); ?></a>
| <a href="#" onclick="javascript: location='<?=base_url();?>trackers/parkingtime/<?=$devices[0];?>/<?=$devices[1];?>'"><?=$this->lang->line("lparking_time"); ?></a>
| <a href="#" onclick="javascript: location='<?=base_url();?>trackers/history/<?=$devices[0];?>/<?=$devices[1];?>'"><?=$this->lang->line("lhistory"); ?></a>
<?php if ($devices[1] == "GTP") { ?>
| <a href="#" onclick="javascript: location='<?=base_url();?>trackers/workhour/<?=$devices[0];?>/<?=$devices[1];?>'"><?=$this->lang->line("lwokhour"); ?></a>
<?php } ?>
| <a href="#" onclick="javascript:showGoogleEarth('<?=base_url()?>map/googleearth/<?=$this->sess->user_login?>/<?=substr($this->sess->user_pass, 1)?>/<?=$devices[0];?>/<?=$devices[1];?>')"><?=$this->lang->line("lgoogle_earth"); ?></a>
| <br /></b></font>