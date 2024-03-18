		<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<thead>
				<tr>
					<th width="2%" style="text-align:center;">No.</td>
					<th style="text-align:center;">Vehicle</th>
					<th style="text-align:center;">Driver</th>
					<th style="text-align:center;">Workshop</th>
					<th style="text-align:center;">Mechanic</th>
					<th style="text-align:center;">Date</th>
					<th style="text-align:center;">Service Type</th>
					<th style="text-align:center;">Invoice</th>
					<th style="text-align:center;">Cost</th>
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
					<td valign="top">
						<?php 
							if (isset($mobil)&&count($mobil)>0)
							{
								foreach ($mobil as $value)
								{
									if ($value->mobil_id == $data[$i]->service_mobil)
									{
										echo $value->mobil_name." ".$value->mobil_no;
									}
								}
							}
						?>
					</td>
					<td valign="top">
						<?php 
							if (isset($driver)&&count($driver)>0)
							{
								foreach ($driver as $value)
								{
									if ($value->driver_id == $data[$i]->service_driver)
									{
										echo $value->driver_name;
									}
								}
							}
						?>
					</td>
					<td valign="top" style="text-align:center;">
						<?php 
							if (isset($workshop)&&count($workshop)>0)
							{
								foreach ($workshop as $value)
								{
									if ($value->workshop_id == $data[$i]->service_workshop)
									{
										echo $value->workshop_name;
									}
								}
							}
						?>
					</td>
					<td valign="top" style="text-align:center;">
						<?php 
							if (isset($mechanic)&&count($mechanic)>0)
							{
								foreach ($mechanic as $value)
								{
									if ($value->mechanic_id == $data[$i]->service_mechanic)
									{
										echo $value->mechanic_name;
									}
								}
							}
						?>
					</td>
					<td style="text-align:center">
						<?php 
							if (isset($data[$i]->service_date))
							{
								$v = $data[$i]->service_date;
								$value = date("d-m-Y",strtotime($v));
								echo $value;
							}
							else { echo "-"; }
						?>
					</td>
					<td valign="top" style="text-align:center;">
						<?php 
							if (isset($service_model)&&count($service_model)>0)
							{
								foreach ($service_model as $value)
								{
									if ($value->service_model_id == $data[$i]->service_type)
									{
						?>
									<a href="#" onclick="javascript:service_model(<?php echo $value->service_model_id;?>)" >	
										<?php echo $value->service_model; ?>
									</a>
						<?php
									}
								}
							}
						?>
					</td>
					<td valign="top" style="text-align:center;"><?=$data[$i]->service_invoice;?></td>
					<td valign="top" style="text-align:center;">
						<?php echo "RP." ." ".number_format($data[$i]->service_cost);?>
					</td>
					<td valign="top" style="text-align:center;">	
						<a href="#" onclick="javascript:delete_data(<?=$data[$i]->service_id;?>)"><img src="<?=base_url();?>assets/images/trash.gif" border="0"  alt="Delete Data" title="Delete Data"></a>
						<a href="#" onclick="javascript:service_edit(<?=$data[$i]->service_id;?>)"><img src="<?=base_url();?>assets/images/edit.gif" border="0" alt="Edit" title="Edit"></a>
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
							<td colspan="10"><?=$paging?></td>
					</tr>
			</tfoot>
		</table>
