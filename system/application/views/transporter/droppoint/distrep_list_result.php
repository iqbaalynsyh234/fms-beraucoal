<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<thead>
				<tr>
					<th style="text-align:center;">No.</td>
					<th style="text-align:center;">Name</th>
					<th style="text-align:center;">Code</th>
					<th style="text-align:center;">Group</th>
					<th style="text-align:center;">Area</th>
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
					<td valign="top" style="text-align:center;"><?=$data[$i]->distrep_name;?></td>
					<td valign="top" style="text-align:center;"><?=$data[$i]->distrep_code;?></td>
					<td valign="top" style="text-align:center;">
						<?php 
						if (isset($rparent))
						{
							foreach ($rparent as $par)
							{
								if ($par->parent_id == $data[$i]->distrep_parent)
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
					<?php if ($this->sess->user_group == 0 ){ ?>
					<td valign="top" style="text-align:center;">
						<a href="#" onclick="javascript:edit(<?=$data[$i]->distrep_id;?>)"><img src="<?=base_url();?>assets/images/edit.gif" border="0" width="20" alt="Edit Data" title="Edit Data"></a>					
						<a href="#" onclick="javascript:delete_data(<?=$data[$i]->distrep_id;?>)"><img src="<?=base_url();?>assets/images/trash.gif" border="0" width="20" alt="Delete Data" title="Delete Data"></a>
					</td>
					<?php } ?>
					
				</tr>
				
			<?php
			}
			}else{
			?>
			<tr><td colspan="6">No Available Data</td></tr>
			<?php } ?>
			</tbody>
			<tfoot>
					<tr>
						
						<td colspan="6"><?=$paging?></td>
					</tr>
			</tfoot>
		</table>
