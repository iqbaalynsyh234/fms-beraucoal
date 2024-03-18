<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
	<thead>
		<tr>
			<th width="2%" style="text-align:center;">No.</td>
			<th style="text-align:center;">No. CO</th>
			<th style="text-align:center;">Vehicle</th>
			<th style="text-align:center;">Driver</th>
			<th style="text-align:center;">Date</th>					
			<th style="text-align:center;">Control</th>
		</tr>
	</thead>
	<tbody>
	<?php
	if(count($data) > 0)
	{
		for($i=0; $i < count($data); $i++)
		{
	?>
		<tr <?=($i%2) ? "class='odd'" : "";?>>
			<td valign="top"><?=$i+1+$offset?></td>
			<td valign="top"><?=$data[$i]->destination_name1;?></td>
			<td valign="top"><?=$data[$i]->destination_vehicle_no;?></td>
			<td valign="top">
				<?php
					if (isset($driver))
					{
						foreach ($driver as $d)
						{
							if ($d->driver_id == $data[$i]->destination_driver)
							{
								echo $d->driver_name;
							}
						}
					}
				?>
			</td>	
			<td valign="top"><?php echo date("d-m-Y",strtotime($data[$i]->destination_date));?></td>
			<td valign="top">
				<a href="javascript: edit('<?php echo $data[$i]->destination_id;?>')" title="Edit No. CO"><img src="<?php echo base_url();?>assets/images/edit.gif" alt="Edit No. CO" /></a>
				<a href="javascript: delete_data('<?php echo $data[$i]->destination_id;?>')" title="Delete No. CO"><img src="<?php echo base_url();?>assets/images/trash.gif" alt="Delete No. CO" /></a>
			</td>
		</tr>
	<?php
		}
	}
	else
	{
		echo "<tr><td colspan='6'>No Data Available</td></tr>";
	}
	?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="6"><?=$paging?></td>
		</tr>
	</tfoot>
</table>
