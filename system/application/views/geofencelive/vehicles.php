<script>
	function checkall()
	{
		<?php for($i=0; $i < count($vehicles); $i++) { ?>
		jQuery("#vehicle<?php echo $vehicles[$i]->vehicle_id; ?>").attr("checked", jQuery("#vehicleall").attr("checked"));
		<?php } ?>
	}
	
	function save()
	{
		var found = false;
		var serialize = "src=<?php echo $sourceid; ?>";
		
		<?php for($i=0; $i < count($vehicles); $i++) { ?>
		if (jQuery("#vehicle<?php echo $vehicles[$i]->vehicle_id; ?>").attr("checked"))
		{
			found = true;
			serialize += "&vid[]="+<?php echo $vehicles[$i]->vehicle_id; ?>;
		}
		<?php } ?>
		
		if (! found)
		{
			alert("<?php echo $this->lang->line("lempty_geofence_copy_to"); ?>");
			return;
		}
		
		jQuery("#loader").show();
		jQuery.post('<?php echo base_url(); ?>geofence/savecopyto', serialize,
			function(r)
			{
				jQuery("#loader").hide();
				alert(r.message);
				
				if (r.error)
				{					
					return;
				}
				
				//alert(r);
			}
			, "json"
		);
	}
</script>
<table class="table sortable no-margin" width="100%" cellpadding="3" class="tablelist">
	<thead>
		<tr>			
			<th width="5%"><input type="checkbox" id="vehicleall" name="vehicleall" value="-1" onclick="javascript:checkall()" /></th>
			<th width="30%"><?=$this->lang->line("lusername"); ?></th>
			<th><?=$this->lang->line("lvehicle"); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php if (count($vehicles) == 0) { ?>
		<tr>
			<td colspan="3"><?php echo $this->lang->line("lno_another_vehicle"); ?></td>
		</tr>
		<?php } else { ?>
		<?php $lastname = ""; 
			for($i=0; $i < count($vehicles); $i++) {
				if ($vehicles[$i]->vehicle_id != $sourceid) {?>
				<tr>	
					<td align="center"><input type="checkbox" id="vehicle<?php echo $vehicles[$i]->vehicle_id; ?>" name="vehicle[]" value="<?php echo $vehicles[$i]->vehicle_id; ?>" /></td>
					<?php if ($lastname != $vehicles[$i]->user_name) { $lastname = $vehicles[$i]->user_name; ?>
					<td><?=$vehicles[$i]->user_name;?>&nbsp;</td>		
					<?php } else { ?>
					<td>&nbsp;</td>
					<?php } ?>
					<td><?=$vehicles[$i]->vehicle_name;?> - <?=$vehicles[$i]->vehicle_no;?>&nbsp;</td>							
				</tr>
		<?php } } ?>
			<tr>
				<th colspan="3">
					<input class="button" type="button" value="<?php echo $this->lang->line("lsave"); ?>" onclick="javascript:save()" />
					<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
				</th>
			</tr>
		<?php } ?>
	</tbody>	
</table>
