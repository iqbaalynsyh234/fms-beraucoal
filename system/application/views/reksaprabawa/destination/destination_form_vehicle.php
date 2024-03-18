<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
	<div id="main" style="margin: 20px;"><br />
	<form method="post" action="<?php echo base_url();?>destination/save_take_vehicle" >
	<h2>Take Vehicle</h2>
	
	<table width="100%" cellpadding="3" class="tablelist" style="font-size:12px">
	
		<tr>
			<td>
				<input type="hidden" name="destination_id" id="destination_id" value="<?php echo $row->destination_id;?>" />
				Destination Name
			</td>
			<td>:</td>
			<td><?php echo $row->destination_name1;?></td>
		</tr>
		
		<tr>
			<td>
				Vehicle
			</td>
			<td>:</td>
			<td>
				<select id="destination_vehicle" name="destination_vehicle">
				<option value="0">None</option>
				<?php for($i=0;$i<count($rows_vehicle);$i++) { ;?>
				<option value="<?php echo $rows_vehicle[$i]->vehicle_id;?>" <?php if ($row->destination_vehicle == $rows_vehicle[$i]->vehicle_id) { echo "selected"; } ?> ><?php echo $rows_vehicle[$i]->vehicle_name." ".$rows_vehicle[$i]->vehicle_no;?></option>
				<?php }?>
				</select>
			</td>
		</tr>
		
		<tr>
			<td colspan="3">
				<input type="submit" value="Save" name="submit" id="submit" />
				<input type="button" value="Cancel" name="btn_cancel" id="btn_cancel" onclick="location='<?php echo base_url();?>destination'" />
			</td>
		</tr>
	</table>
	</form>
	</div>
</div>