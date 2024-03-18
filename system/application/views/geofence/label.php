	<form class="block-content form" id="frmadd1">
		<table class="table sortable no-margin"  width="100%" cellpadding="3" class="tablelist" style="margin: 3px;">
			<thead>
				<tr>
					<th width="2%">No.</td>
					<th><?php echo $this->lang->line("lcoordinate"); ?></th>
					<th width="15%"><?=$this->lang->line("lname"); ?></th>
					<th width="5%">&nbsp;</th>
					<th width="5%">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
			<?php for($i=0; $i < count($rows); $i++) { ?>
				<tr <?=($i%2) ? "class='odd'" : "";?>>
					<td valign="top"><?php echo $i+1;?></td>
					<td valign="top"><?php echo $rows[$i]->geofence_coordinate; ?></td>
					<td valign="top"><input type="hidden" name="ids[]" id="ids_<?php echo $rows[$i]->geofence_id; ?>" value="<?php echo $rows[$i]->geofence_id; ?>" /><input type="text" name="names[]" id="names_<?php echo $rows[$i]->geofence_id; ?>" value="<?php echo $rows[$i]->geofence_name; ?>" /> </td>
					<td valign="top"><a href="javascript:gotogeofence(<?php echo $rows[$i]->geofence_id; ?>)"><?php echo $this->lang->line("lgo"); ?></a></td>
					<td valign="top"><a href="javascript:removegeofence(<?php echo $rows[$i]->geofence_id; ?>)"><?php echo $this->lang->line("lremove"); ?></a></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
		<input class="button" type="button" value=" Save " onclick="javascript:savegeo()" />
		<?php 
			$val = 0;
			if (isset($rows[$i-1]->geofence_vehicle))
			{
				list($vid, $vhost) = explode("@",$rows[$i-1]->geofence_vehicle);
				if (isset($vid))
				{
					$val = $vid;
				}
			}
		?>
		<input class="button" type="button" name="btncancel" id="btncancel" value=" <?php echo $this->lang->line("lremove_all_geofence"); ?> " onclick="javascript:removegeofence_byvehicle('<?php echo $val; ?>');" />
	</form>
