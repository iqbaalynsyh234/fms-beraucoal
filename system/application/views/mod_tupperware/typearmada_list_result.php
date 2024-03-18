		<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<thead>
				<tr>
					<th width="2%" style="text-align:center;">No.</td>
					<th style="text-align:center;">Type Armada</th>
                                        <th style="text-align:center;">Description</th>
					<th style="text-align:center;">Volume</th>
					<th style="text-align:center;">Control</th>					
				</tr>
			</thead>
			<tbody>
			<?php
			if(count($data) > 0){
			for($i=0; $i < count($data); $i++)
			{
			?>
				<tr <?=($i%2) ? "class='odd'" : "";?>>
					<td valign="top"><?=$i+1+$offset?></td>
					<td valign="top"><?=$data[$i]->typearmada_name?></td>
                                        <td valign="top"><?=$data[$i]->typearmada_description?></td>
					<td valign="top"><?=$data[$i]->typearmada_volume?></td>
					<td>
						<a href="javascript: edit('<?php echo $data[$i]->typearmada_id;?>')" title="Edit Type Armada"><img src="<?php echo base_url();?>assets/images/edit.gif" alt="Edit" /></a>
						<a href="javascript: detail('<?php echo $data[$i]->typearmada_id;?>')" title="Info Detail"><img src="<?php echo base_url();?>assets/images/postreq.png" alt="Info Detail" /></a>
						<a href="javascript: delete_data('<?php echo $data[$i]->typearmada_id;?>')" title="Delete"><img src="<?php echo base_url();?>assets/images/trash.gif" alt="Delete" /></a>
					</td>
				</tr>
			<?php
			}
			}else{
				echo "<tr><td colspan='10'>No Data Available</td></tr>";
			}
			?>
			</tbody>
			<tfoot>
					<tr>
							<td colspan="5"><?=$paging?></td>
					</tr>
			</tfoot>
		</table>
