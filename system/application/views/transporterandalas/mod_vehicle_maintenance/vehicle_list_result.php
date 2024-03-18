		<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<thead>
				<tr>
					<th width="2%" style="text-align:center;">No.</td>
					<th style="text-align:center;">Vehicle</th>
					<?php 
						//$app_route = $this->config->item("app_route");
						//if (isset($app_route) && ($app_route == 1))
						//{
					?>
						<!--<th style="text-align:center;">Route</th>-->
					<?
						//}
					?>
					<th style="text-align:center;">Type</th>
					<th style="text-align:center;">Year</th>
					<th style="text-align:center;">STNK</th>
					<th style="text-align:center;">KIR</th>
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
						<?php echo $data[$i]->mobil_name." ".$data[$i]->mobil_no;?>
					</td>
					<?php 
						//$app_route = $this->config->item("app_route");
						//if (isset($app_route) && ($app_route == 1))
						//{
					?>
					<!--
						<td>
							<?php 
							/*if (isset($my_route) && (count($my_route)>0))
							{
								foreach($my_route as $route)
								{
									if($route->route_id == $data[$i]->mobil_route)
									{
										echo $route->route_name;
									}
								}
							}*/
							?>
						</td>
					-->
					<?
						//}
					?>
					<td valign="top"><?=$data[$i]->mobil_model;?></td>
					<td valign="top" style="text-align:center;"><?=$data[$i]->mobil_year;?></td>
					<td valign="top" style="text-align:center;">
						<?=$data[$i]->mobil_stnk_no;?><br />
						<small>( <?=date("d-m-Y",strtotime($data[$i]->mobil_stnk_expired));?> )</small>
					</td>
					<td valign="top" style="text-align:center;">
						<?=$data[$i]->mobil_no_kir;?><br />
						<small>( <?=date("d-m-Y",strtotime($data[$i]->mobil_kir_active_date));?> )</small>
					</td>
					<td valign="top" style="text-align:center;">	
						<a href="#" onclick="javascript:delete_data(<?=$data[$i]->mobil_id;?>)"><img src="<?=base_url();?>assets/images/trash.gif" border="0"  alt="Delete Data" title="Delete Data"></a>
						<a href="#" onclick="javascript:vehicle_detail(<?=$data[$i]->mobil_id;?>)"><img src="<?=base_url();?>assets/images/learning_profile.png" border="0" alt="Detail" title="Detail"></a>
						<a href="#" onclick="javascript:vehicle_edit(<?=$data[$i]->mobil_id;?>)"><img src="<?=base_url();?>assets/images/edit.gif" border="0" alt="Edit" title="Edit"></a>
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
