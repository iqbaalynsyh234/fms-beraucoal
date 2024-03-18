<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
	<thead>
		<tr>
			<th width="2%" style="text-align:center;">No.</td>
			<th style="text-align:center;">Lacak-Mobil Code</th>
			<th style="text-align:center;">Transporter</th>
			<th style="text-align:center;">SLCARS</th>
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
					<td valign="top"><?=$data[$i]->slcars_lacak_code?></td>
					<td valign="top"><?=$data[$i]->slcars_name?></td>
					<td valign="top"><?=$data[$i]->slcars_code?></td>
					<td>
						<a href="javascript: edit('<?php echo $data[$i]->slcars_id;?>')" title="Edit SLCARS"><img src="<?php echo base_url();?>assets/images/edit.gif" alt="Edit" /></a>
						<a href="javascript: delete_data('<?php echo $data[$i]->slcars_id;?>')" title="Delete"><img src="<?php echo base_url();?>assets/images/trash.gif" alt="Delete" /></a>
					</td>
				</tr>
			<?php
			}
			}else{
				echo "<tr><td colspan='5'>No Data Available</td></tr>";
			}
			?>
			</tbody>
			<tfoot>
					<tr>
							<td colspan="5"><?=$paging?></td>
					</tr>
			</tfoot>
		</table>
