<!--<table width="100%" cellpadding="2" class="table sortable no-margin" style="margin: 2px;">-->
<table class="table table-striped table-hover">
			<thead>
				<tr>
					<th style="text-align:center;">No.</th>
					<th style="text-align:center;">Vehicle No</th>
					<th style="text-align:center;">Vehicle Name</th>
					<th style="text-align:center;">Vehicle Device</th>
					<th style="text-align:center;">SimCard</th>	
					<th style="text-align:center;">Company</th>	
					<th style="text-align:center;">SubCompany</th>	
					<th style="text-align:center;">Group</th>	
					<th style="text-align:center;">SubGroup</th>	
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
					<td valign="top" align="center" style="text-align:center;"><small><?=$i+1+$offset?></small></td>
					<td valign="top" style="text-align:center;"><small><?=$data[$i]->vehicle_no;?></td>
					<td valign="top" style="text-align:center;"><small><?=$data[$i]->vehicle_name;?></td>
					<td valign="top" style="text-align:center;"><small><?=$data[$i]->vehicle_device;?></td>
					<td valign="top" style="text-align:center;"><small><?=$data[$i]->vehicle_card_no;?></td>
					<td valign="top" style="text-align:center;"><small>
						<?php 
						if (isset($companyall))
							{
								foreach ($companyall as $com)
								{
									if ($com->company_id == $data[$i]->vehicle_company)
									{
										echo $com->company_name;
									}
								}
							}
						?>
					</td>
					<td valign="top" style="text-align:center;"><small>
						<?php 
						if (isset($subcompanyall))
							{
								foreach ($subcompanyall as $subcom)
								{
									if ($subcom->subcompany_id == $data[$i]->vehicle_subcompany)
									{
										echo $subcom->subcompany_name;
									}
								}
							}
						?>
					</td>
					<td valign="top" style="text-align:center;"><small>
						<?php 
						if (isset($groupall))
							{
								foreach ($groupall as $grp)
								{
									if ($grp->group_id == $data[$i]->vehicle_group)
									{
										echo $grp->group_name;
									}
								}
							}
						?>
					</td>
					<td valign="top" style="text-align:center;"><small>
						<?php 
						if (isset($subgroupall))
							{
								foreach ($subgroupall as $subgrp)
								{
									if ($subgrp->group_id == $data[$i]->vehicle_subgroup)
									{
										echo $subgrp->subgroup_name;
									}
								}
							}
						?>
					</td>
					<td valign="top" style="text-align:center;">
						<a href="<?=base_url();?>manage/editvehicle/<?=$data[$i]->vehicle_id;?>" class="btn btn-tbl-delete btn-xs" title="Edit">
							<i class="fa fa-pencil"></i>
						</a>						
					</td>
					
				</tr>
				
			<?php
			}
			}else{
			?>
			<tr><td colspan="7">No Available Data</td></tr>
			<?php } ?>
			</tbody>
			<tfoot>
				
			</tfoot>
		</table>
