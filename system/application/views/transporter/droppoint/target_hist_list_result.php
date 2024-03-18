<h3 style="text-align:right;">Periode <?=date("d-m-Y", strtotime($startdate));?> s/d <?=date("d-m-Y", strtotime($enddate));?></h3>
<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<thead>
				<tr>
					<th style="text-align:center;">No.</th>
					<th style="text-align:center;">Action</th>
					<th style="text-align:center;">Name</th>
					<th style="text-align:center;">Code</th>
					<th style="text-align:center;">Type</th>
					<th style="text-align:center;">Periode</th>
					<th style="text-align:center;">Target</th>
					<th style="text-align:center;">Area</th>
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
					<td valign="top" style="text-align:center;"><?=$data[$i]->target_hist_note;?></td>
					<td valign="top" style="text-align:center;"><?=$data[$i]->droppoint_name;?></td>
					<td valign="top" style="text-align:center;"><?=$data[$i]->droppoint_code;?></td>
					<td valign="top" style="text-align:center;">
					<?php if ($data[$i]->target_hist_type == 0){ ?>
						REGULAR
					<?php }else{ ?>
						COMBINE
					<?php } ?>
					</td>
					<td valign="top" style="text-align:center;">
						<?php echo date("d-m-Y", strtotime($data[$i]->target_hist_startdate))?> s/d <br />
						<?php echo date("d-m-Y", strtotime($data[$i]->target_hist_enddate))?>
					</td>
					<td valign="top" style="text-align:center;"><?php echo date("H:i", strtotime($data[$i]->target_hist_time))?></td>
					<td valign="top" style="text-align:center;">
						<?php 
							if (isset($rcompany))
							{
								foreach ($rcompany as $com)
								{
									if ($com->company_id == $data[$i]->target_hist_company)
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
