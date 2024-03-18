		<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<thead>
				<tr>
					<th width="2%" style="text-align:center;">No.</td>
					<th style="text-align:center;">Name</th>
					<th style="text-align:center;">Telp</th>
					<th style="text-align:center;">Fax</th>
					<th style="text-align:center;">Address</th>
					<!--<th style="text-align:center;">Control</th>-->
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
					<td valign="top">
						<?php echo $data[$i]->workshop_name;?>
					</td>
					<td valign="top"><?=$data[$i]->workshop_telp;?></td>
					<td valign="top" style="text-align:center;"><?=$data[$i]->workshop_fax;?></td>
					<td valign="top" style="text-align:center;"><?=$data[$i]->workshop_address;?></td>
					<!--<td valign="top" style="text-align:center;">	
						<a href="#" onclick="javascript:delete_data(<?=$data[$i]->workshop_id;?>)"><img src="<?=base_url();?>assets/images/trash.gif" border="0"  alt="Delete Data" title="Delete Data"></a>
						<a href="#" onclick="javascript:workshop_edit(<?=$data[$i]->workshop_id;?>)"><img src="<?=base_url();?>assets/images/edit.gif" border="0" alt="Edit" title="Edit"></a>
					</td>-->
					
				</tr>
			<?php
			}
			}else{
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
