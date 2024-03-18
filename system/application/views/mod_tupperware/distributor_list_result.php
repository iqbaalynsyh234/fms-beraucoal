		<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<thead>
				<tr>
					<th width="2%" style="text-align:center;">No.</td>
					<th style="text-align:center;">DB. Code</th>
					<th style="text-align:center;">DB. Name</th>
					<th style="text-align:center;">User Login</th>
					<th style="text-align:center;">Email</th>
					<th style="text-align:center;">WH <br/>Coverage</th>
					<th style="text-align:center;">LeadDay <br/>WH Origin</th>
					<th style="text-align:center;">LeadDay <br/>WH JKT</th>
					<th style="text-align:center;">LeadDay <br/>WH Medan</th>
					<th style="text-align:center;">LeadDay <br/>WH SBY</th>
					<th style="text-align:center;">Schedule</th>
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
					<td valign="top"><?=$data[$i]->dist_code?></td>
					<td valign="top"><?=$data[$i]->dist_name?></td>
					<td valign="top"><?=$data[$i]->dist_username?></td>
					<td valign="top"><?=$data[$i]->dist_email?></td>
					<td valign="top"><?=$data[$i]->dist_wh_coverage?></td>
					<td valign="top"><?=$data[$i]->dist_leadday_wh_origin?></td>
					<td valign="top"><?=$data[$i]->dist_leadday_wh_jkt?></td>
					<td valign="top"><?=$data[$i]->dist_leadday_wh_medan?></td>
					<td valign="top"><?=$data[$i]->dist_leadday_wh_sby?></td>
					<td valign="top" style="text-align:center;"><?=$data[$i]->dist_schedule?></td>
					<td>
						<a href="javascript: edit('<?php echo $data[$i]->dist_id;?>')" title="Edit Distributor"><img src="<?php echo base_url();?>assets/images/edit.gif" alt="Edit Distributor" /></a>
						<a href="javascript: detail('<?php echo $data[$i]->dist_id;?>')" title="Info Detail"><img src="<?php echo base_url();?>assets/images/postreq.png" alt="Info Detail" /></a>
						<a href="javascript: delete_data('<?php echo $data[$i]->dist_id;?>')" title="Delete ID Distributor"><img src="<?php echo base_url();?>assets/images/trash.gif" alt="Delete Distributor" /></a>
					</td>
				</tr>
			<?php
			}
			}else{
				echo "<tr><td colspan='11'>No Data Available</td></tr>";
			}
			?>
			</tbody>
			<tfoot>
					<tr>
							<td colspan="11"><?=$paging?></td>
					</tr>
			</tfoot>
		</table>
