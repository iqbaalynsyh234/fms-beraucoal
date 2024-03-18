		<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<thead>
				<tr>
					<th width="2%">No.</td>
					<th style="text-align:center;">Driver</th>
					<th style="text-align:center;">Vehicle</th>
					<th style="text-align:center;">Customer</th>
					<th style="text-align:center;">Item</th>
					<th style="text-align:center;">SO Type</th>
					<th style="text-align:center;">SO</th>
					<th style="text-align:center;">
						Quantity
						<br />
						( On Delivery )
					</th>
					<th style="text-align:center;">Cost</th>
					<th style="text-align:center;">Date</th>
				</tr>
			</thead>
			<tbody>
			<?php
			$total_cost = 0;
			for($i=0; $i < count($data); $i++)
			{
			?>
				<tr <?=($i%2) ? "class='odd'" : "";?>>
					<td style="text-align:center;"><?=$i+1+$offset?></td>
					<td style="text-align:left;"><?=$data[$i]->driver_name;?></td>
					<td style="text-align:left;">
						<?php 
							if (isset($vehicle))
							{
								if (count($vehicle)>0)
								{
									for ($y=0;$y<count($vehicle);$y++)
									{
										if ($vehicle[$y]->vehicle_device == $data[$i]->do_delivered_vehicle)
										{
											echo $vehicle[$y]->vehicle_name." ".$vehicle[$y]->vehicle_no;
										}
									}
								}
								else
								{
									echo "-";
								}
							}
							else
							{
								echo "-";
							}
						?>
					</td>
					<td style="text-align:left;">
						<?php 
							if (isset($customer) && $data[$i]->dosj_customer_tmp == "")
							{
								if (count($customer)>0)
								{
									for ($y=0;$y<count($customer);$y++)
									{
										if ($customer[$y]->group_id == $data[$i]->dosj_customer_id)
										{
											echo $customer[$y]->group_name;
										}
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
							echo $data[$i]->dosj_item_size;;
						?>
					</td>
					<td style="text-align:center;"><?=$data[$i]->do_delivered_do_type;?></td>
					<td style="text-align:center;"><?=$data[$i]->do_delivered_do_number;?></td>
					<td style="text-align:center;"><?=$data[$i]->do_delivered_quantity;?></td>
					<td style="text-align:center;"><?=$data[$i]->cost;?></td>
					<td style="text-align:center;">
						<?php 
							$sh_date = date("d-m-Y", strtotime($data[$i]->do_delivered_date));
							echo $sh_date;
						?>
					</td>
				</tr>
			<?php
			$total_cost = $total_cost + $data[$i]->cost;
			}
			?>
			<tr><td style="text-align:right;" colspan="11">Total Cost : <?php echo "Rp."." ".number_format($total_cost); ?></td></tr>
			</tbody>
			<tfoot>
					<tr>
							<td colspan="11"><?=$paging?></td>
					</tr>
			</tfoot>
		</table>
