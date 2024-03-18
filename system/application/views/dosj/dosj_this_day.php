<script>
	
	
	jQuery(document).ready(
		function()
		{
			
				showclock();
		}
	);
	
		
</script>
<div id="main_data">
		<h2>Driver : <?php if (isset($data) && count($data)>0 )
				 {
					echo $data[0]->driver_name;
				 }
				 ?>
				 ,
				<?php if (isset($data) && count($data)>0 )
				 {
					foreach($vehicle as $v)
					{
						if ($v->vehicle_device == $data[0]->do_delivered_vehicle)
						{
							echo $v->vehicle_name." ".$v->vehicle_no;
						}
					}
				 }
				 ?>
		</h2>
		<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
				<thead>
				<tr>
					<th rowspan="2" width="2%">No.</td>
					<th colspan="2" style="text-align:center;">DO</th>
					<th rowspan="2" style="text-align:center;">Customer</th>
					<th rowspan="2" style="text-align:center;">Item Desc.</th>
					<th colspan="2" style="text-align:center;">SJP</th>
					<th colspan="2" style="text-align:center;">Quantity</th>
					<th rowspan="2" style="text-align:center;">Cost</th>
				</tr>
                <tr>
                    <td style="text-align:center">Block</td>
                    <td style="text-align:center">Mortar</td>
                    <td style="text-align:center">Block</td>
                    <td style="text-align:center">Mortar</td>
                    <td style="text-align:center">Block</td>
                    <td style="text-align:center">Mortar</td>
                </tr>
				</thead>
				<?php
					$tot_item = 0;
                    $tot_item_mortar = 0;
					$tot_cost = 0;
					
					if (isset($data) && count($data)>0)
					{
						for ($i=0;$i<count($data);$i++)
						{
				?>
				
					<tr>
						<td style="text-align:center;"><?=$i+1?></td>
						<td><?php echo $data[$i]->do_delivered_do_block; ?></td>
                        <td><?php echo $data[$i]->do_delivered_do_mortar; ?></td>
						<td>
							<?php 
								if (isset($customer) && $data[$i]->dosj_customer_tmp == "")
								{
									foreach ($customer as $group)
									{
										if ($group->group_id == $data[$i]->dosj_customer_id)
										{
											echo $group->group_name;
										}
									}
								}
								else
								{
									echo $data[$i]->dosj_customer_tmp;
								}
							?>
						</td>
						<td style="text-align:center;">
						<?php 
							echo $data[$i]->dosj_item_desc;
							echo "<br />";
							echo $data[$i]->dosj_item_panjang."x".$data[$i]->dosj_item_lebar."x".$data[$i]->dosj_item_tinggi." ".$data[$i]->dosj_item_unit;
						?>
						</td>
                        <td style="text-align:right;"><?php echo $data[$i]->dosj_block_no; ?></td>
						<td style="text-align:right;"><?php echo $data[$i]->dosj_mortar_no; ?></td>
						<td style="text-align:right;"><?php echo $data[$i]->do_delivered_quantity; ?></td>
                        <td style="text-align:right;"><?php echo $data[$i]->do_delivered_quantity_mortar; ?></td>
						<td style="text-align:right;"><?php echo number_format($data[$i]->cost); ?></td>
					<tr>
				<?php
						$tot_item = $tot_item + $data[$i]->do_delivered_quantity;
                        $tot_item_mortar = $tot_item_mortar + $data[$i]->do_delivered_quantity_mortar;
						$tot_cost = $tot_cost + $data[$i]->cost;
						}
				?>
					<tr>
						<td colspan="10"><hr /></td>
					</tr>
					<tr>
						<td style="text-align:right;" colspan="7">
							TOTAL :
						</td>
						<td style="text-align:right;">
							<?php echo $tot_item;?>
						</td>
                        <td style="text-align:right;"> 
                            <?php echo $tot_item_mortar;?>
                        </td>
						<td style="text-align:right;">
							<?php echo number_format($tot_cost);?>
						</td>
					</tr>
				<?php
					}
				?>
		</table>
</div>