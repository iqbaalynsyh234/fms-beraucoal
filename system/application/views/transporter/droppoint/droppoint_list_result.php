<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<thead>
				<tr>
					<th style="text-align:center;">No.</td>
					<th style="text-align:center;">Name</th>
					<th style="text-align:center;">Code</th>
					<th style="text-align:center;">Geofence</th>
					<th style="text-align:center;">Type</th>
					<th style="text-align:center;">Distrep</th>
					<th style="text-align:center;">Group</th>
					<th style="text-align:center;">Area</th>
					<th style="text-align:center;">Target</th>
					<?php if ($this->sess->user_group == 0 ){ ?>							
					<th style="text-align:center;">Control</th>						
					<?php } ?>
				</tr>
			</thead>
			<tbody>
			<?php
			
			if(count($data) > 0){
			
			for($i=0; $i < count($data); $i++)
			{
			
			?>
				<tr <?=($i%2) ? "class='odd'" : "";?>>
					<td valign="top" align="center" style="text-align:center;"><?=$i+1+$offset?></td>
					<td valign="top" style="text-align:center;"><?=$data[$i]->droppoint_name;?></td>
					<td valign="top" style="text-align:center;"><?=$data[$i]->droppoint_code;?></td>
					<td valign="top" style="text-align:center;"><?=$data[$i]->droppoint_geofence;?></td>
					<td valign="top" style="text-align:center;">
					<?php if ($data[$i]->droppoint_type == 1){ ?>
						COMBINE
					<?php }else{ ?>
						REGULAR
					<?php } ?>
					</td>
					<td valign="top" style="text-align:center;"><?=$data[$i]->distrep_name;?></td>
					<td valign="top" style="text-align:center;">
						<?php 
						if (isset($rparent))
						{
							foreach ($rparent as $par)
							{
								if ($par->parent_id == $data[$i]->parent_id)
								{
									echo $par->parent_name;
								}
							}
						}
						?>
					</td>
					<td valign="top" style="text-align:center;">
						<?php 
							if (isset($rcompany))
							{
								foreach ($rcompany as $com)
								{
									if ($com->company_id == $data[$i]->parent_company)
									{
										echo $com->company_name;
									}
								}
							}
						?>
					</td>
					<td valign="top" style="text-align:center;"><?php echo date("H:i", strtotime($data[$i]->droppoint_target_time))?></td>
					<?php if ($this->sess->user_group == 0 ){ ?>
					<td valign="top" style="text-align:center;">
						<a href="#" onclick="javascript:edit(<?=$data[$i]->droppoint_id;?>)"><img src="<?=base_url();?>assets/images/edit.gif" border="0" width="20" alt="Edit Data" title="Edit Data"></a>					
						<a href="#" onclick="javascript:delete_data(<?=$data[$i]->droppoint_id;?>)"><img src="<?=base_url();?>assets/images/trash.gif" border="0" width="20" alt="Delete Data" title="Delete Data"></a>
					</td>
					<?php } ?>
					
				</tr>
				
			<?php
			}
			}else{
			?>
			<tr><td colspan="10">No Available Data</td></tr>
			<?php } ?>
			</tbody>
			<tfoot>
					<tr>
						
						<td colspan="10"><?=$paging?></td>
					</tr>
			</tfoot>
		</table>
