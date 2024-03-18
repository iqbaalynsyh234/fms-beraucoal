<h3 style="text-align:right;">Periode <?=date("d-m-Y", strtotime($startdate));?> s/d <?=date("d-m-Y", strtotime($enddate));?></h3>
<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<thead>
				<tr>
					<th style="text-align:center;">No.</td>
					<th style="text-align:center;">Created</th>
					<th style="text-align:center;">Alert</th>
					<th style="text-align:center;">Vehicle No</th>
					<th style="text-align:center;">Vehicle Name</th>
					<th style="text-align:center;">GPS Last Time</th>
					<th style="text-align:center;">Company</th>
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
					<td valign="top" style="text-align:center;"><?php echo date("d-m-Y H:i:s", strtotime($data[$i]->alert_create))?></td>
					<td valign="top" style="text-align:center;"><?=$data[$i]->alert_note;?></td>
					<td valign="top" style="text-align:center;"><?=$data[$i]->alert_vehicle_no;?></td>
					<td valign="top" style="text-align:center;"><?=$data[$i]->alert_vehicle_name;?></td>
					<td valign="top" style="text-align:center;"><?php echo date("d-m-Y H:i:s", strtotime($data[$i]->alert_starttime))?></td>
					<td valign="top" style="text-align:center;">
						<?php 
							if (isset($rcompany))
							{
								foreach ($rcompany as $com)
								{
									if ($com->company_id == $data[$i]->alert_vehicle_company)
									{
										echo $com->company_name;
									}
								}
							}
						?>
					</td>
					
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
