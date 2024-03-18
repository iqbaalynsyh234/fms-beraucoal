<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
<thead>
	<tr>
		<th width="2%">No.</td>
		<th style="text-align:center;">Route</th>
		<th style="text-align:center;" >Note</th>
		<th style="text-align:center;">Control</th>
	</tr>
</thead>
<tbody>
	<?php 
		for($i=0;$i<count($data);$i++)
		{
	?>
	<tr>
		<td><?php echo $i+1; ?></td>
		<td><?php echo $data[$i]->route_name; ?></td>
		<td><?php echo $data[$i]->route_note; ?></td>
		<td>
		<a href="javascript: route_edit('<?php echo $data[$i]->route_id;?>')" title="Edit"><img src="<?php echo base_url();?>assets/images/edit.gif" /></a>
		<a href="javascript: route_delete('<?php echo $data[$i]->route_id;?>')" title="Delete"><img src="<?php echo base_url();?>assets/images/trash.gif" alt="Delete" /></a>
		</td>
	</tr>
	<?php } ?>
</tbody>
<tfoot>
	<tr>
		<td colspan="4"><?php if (isset($paging)) { echo $paging; } ?></td>
	</tr>
</tfoot>
</table>
