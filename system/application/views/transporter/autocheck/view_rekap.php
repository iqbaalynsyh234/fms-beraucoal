
<div class="block-border">
<a class="button" href="<?=base_url()?>autocheck">View Detail</a>

<table width="100%" cellpadding="3" class="table sortable no-margin">
<thead>
	<tr>
		<th style="text-align:center;">No.</th>
		<th style="text-align:center;">Vehicle</th>
		<th style="text-align:center;">GPS Info</th>
		<th style="text-align:center;">Area</th>
		<th style="text-align:center;">Status</th>
	</tr>
</thead>
<tbody>
<?php 
if(count($rowp) > 0){ ?>
<th colspan="5" style="text-align:center;">GPS Online</th>
	<?php for($i=0; $i < count($rowp); $i++){ ?>
	
	<tr>
		<td valign="top" align="center" style="text-align:center;"><?=$i+1;?></td>
		<td valign="top" style="text-align:center;"><?=$rowp[$i]->auto_vehicle_no;?> <br /> <?=$rowp[$i]->auto_vehicle_name;?></td>
		<td valign="top" style="text-align:left;">
		Last Updated: <?=date("d-m-Y H:i:s", strtotime($rowp[$i]->auto_last_update));?><br />
		Last Position: <?=$rowp[$i]->auto_last_position;?><br />
		Last Coord: <?=$rowp[$i]->auto_last_lat.",".$rowp[$i]->auto_last_long;?><br />
		Last Engine: <?=$rowp[$i]->auto_last_engine;?>
		</td>
		<td style="text-align:center;">
						<?php 
							if (isset($rcompany))
							{
								foreach ($rcompany as $com)
								{
									if ($com->company_id == $rowp[$i]->auto_vehicle_company)
									{
										echo $com->company_name;
									}
								}
							}
						?>
					</td>
		<td valign="top" style="text-align:center;">
		<?php if ($rowp[$i]->auto_status == "P"){ ?>
			GPS Online
		<?php } ?>
		</td>
	</tr>
<?php }}?>
<br />

<?php 
if(count($rowk) > 0){ ?>
	<th colspan="4" style="text-align:center;">GPS Online (Kuning)</th>
	<?php for($i=0; $i < count($rowk); $i++){ ?>
	<tr>
		<td valign="top" align="center" style="text-align:center;"><?=$i+1;?></td>
		<td valign="top" style="text-align:center;"><?=$rowk[$i]->auto_vehicle_no;?> <br /> <?=$rowk[$i]->auto_vehicle_name;?></td>
		<td valign="top" style="text-align:left;">
		Last Updated: <?=date("d-m-Y H:i:s", strtotime($rowk[$i]->auto_last_update));?><br />
		Last Position: <?=$rowk[$i]->auto_last_position;?><br />
		Last Coord: <?=$rowk[$i]->auto_last_lat.",".$rowk[$i]->auto_last_long;?><br />
		Last Engine: <?=$rowk[$i]->auto_last_engine;?>
		</td>
		<td style="text-align:center;">
						<?php 
							if (isset($rcompany))
							{
								foreach ($rcompany as $com)
								{
									if ($com->company_id == $rowp[$i]->auto_vehicle_company)
									{
										echo $com->company_name;
									}
								}
							}
						?>
					</td>
		<td valign="top" style="text-align:center;">
		<?php if ($rowk[$i]->auto_status == "K"){ ?>
			GPS Online (kuning)
		<?php } ?>
		</td>
	</tr>
<?php }}?>
<br />
<?php 
if(count($rowm) > 0){  ?>
	<th colspan="4" style="text-align:center;">GPS Offline (Merah)</th>
	
	<?php 
	for($i=0; $i < count($rowm); $i++){ ?>

	<tr>
		<td valign="top" align="center" style="text-align:center;"><?=$i+1;?></td>
		<td valign="top" style="text-align:center;"><?=$rowm[$i]->auto_vehicle_no;?> <br /> <?=$rowm[$i]->auto_vehicle_name;?></td>
		<td valign="top" style="text-align:left;">
		Last Updated: <?=date("d-m-Y H:i:s", strtotime($rowm[$i]->auto_last_update));?><br />
		Last Position: <?=$rowm[$i]->auto_last_position;?><br />
		Last Coord: <?=$rowm[$i]->auto_last_lat.",".$rowm[$i]->auto_last_long;?><br />
		Last Engine: <?=$rowm[$i]->auto_last_engine;?>
		</td>
		<td style="text-align:center;">
						<?php 
							if (isset($rcompany))
							{
								foreach ($rcompany as $com)
								{
									if ($com->company_id == $rowp[$i]->auto_vehicle_company)
									{
										echo $com->company_name;
									}
								}
							}
						?>
					</td>
		<td valign="top" style="text-align:center;">
		<?php if ($rowm[$i]->auto_status == "M"){ ?>
			GPS Offline
		<?php } ?>
		</td>
	</tr>
<?php }}?>
</table>
</div>
</form>
