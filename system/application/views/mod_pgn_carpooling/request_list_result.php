		<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<thead>
				<tr>
					<th width="2%" style="text-align:center;">No.</td>
					<th style="text-align:center;">NIK</th>
					<th style="text-align:center;">Name</th>
					<th style="text-align:center;">Date</th>
					<th style="text-align:center;">Time</th>
					<th style="text-align:center;">Car</th>
					<th style="text-align:center;">Driver</th>
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
					<td valign="top"><?=$data[$i]->request_nik?></td>
					<td valign="top"><?=$data[$i]->request_name?></td>
					<td valign="top" style="text-align:center;"><?=date("d-m-Y",strtotime($data[$i]->request_date));?></td>
					<td valign="top" style="text-align:center;"><?=$data[$i]->request_time?></td>
					<td valign="top">
						<?php 
							if(isset($vehicle))
							{
								foreach($vehicle as $v)
								{
									if($v->pgn_vehicle_device == $data[$i]->request_car)
									{
										echo $v->pgn_vehicle_no." ".$v->pgn_vehicle_name;
									}
								}
							}
						?>
					</td>
					<td valign="top"><?=$data[$i]->request_driver?></td>
					<td style="text-align:center;">
						<a href="javascript: detail('<?php echo $data[$i]->request_id;?>')" title="Info Detail"><img src="<?php echo base_url();?>assets/images/postreq.png" alt="Info Detail" /></a>
						<?php if($data[$i]->request_process_status == 0) { ?>
						<a href="javascript: edit('<?php echo $data[$i]->request_id;?>')" title="Edit Request"><img src="<?php echo base_url();?>assets/images/edit.gif" alt="Edit Request" /></a>						
						<a href="javascript: delete_data('<?php echo $data[$i]->request_id;?>')" title="Delete Request"><img src="<?php echo base_url();?>assets/images/trash.gif" alt="Delete Request" /></a>
						<?php if($this->sess->user_id == $this->config->item("dispatcher_pgn_id")) { ?>
						<a href="javascript: process_request('<?php echo $data[$i]->request_id;?>')" title="Process Request"><img src="<?php echo base_url();?>assets/images/data_preferences.png" alt="Process Request" /></a>						
						<?php } ?>
						<?php } ?>
						<?php if($data[$i]->request_process_status == 1 && $data[$i]->request_complete_status == 0) { ?>
						<?php if($this->sess->user_id == $this->config->item("dispatcher_pgn_id")) { ?>
						<a href="javascript: complete_request('<?php echo $data[$i]->request_id;?>')" title="Complete Request Schedule"><img src="<?php echo base_url();?>assets/images/clock.png" alt="Complete Request Schedule" /></a>						
						<?php } ?>
						<?php }?>
					</td>
				</tr>
			<?php
			}
			}else{
				echo "<tr><td colspan='8'>No Data Available</td></tr>";
			}
			?>
			</tbody>
			<tfoot>
					<tr>
							<td colspan="8"><?=$paging?></td>
					</tr>
			</tfoot>
		</table>
