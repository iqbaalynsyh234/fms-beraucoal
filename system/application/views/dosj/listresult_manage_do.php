		<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<thead>
				<tr>
					<th rowspan="2" width="2%">No.</td>
					<th rowspan="2" style="text-align:center;">SO Type</th>
					<th colspan="2" style="text-align:center;">SO Number</th>
					<th rowspan="2" style="text-align:center;">Vehicle</th>
					<th rowspan="2" style="text-align:center;">Driver</th>
					<th colspan="2" style="text-align:center;">
						Quantity
						<br />
						( On Delivery )
					</th>
					<th rowspan="2" style="text-align:center;">Cost</th>
					<th rowspan="2" style="text-align:center;">Ship Date</th>
					<th rowspan="2" style="text-align:center;">Control</th>
				</tr>
                <tr>
                    <td style="text-align:center">Block</td>
                    <td style="text-align:center">Mortar</td>
                    <td style="text-align:center">Block</td>
                    <td style="text-align:center">Mortar</td>
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
					<td style="text-align:center;"><?=$data[$i]->do_delivered_do_type;?></td>
					<td style="text-align:center;"><?=$data[$i]->do_delivered_do_block;?></td>
                    <td style="text-align:center;"><?=$data[$i]->do_delivered_do_mortar;?></td>
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
					<td style="text-align:center;"><?=$data[$i]->driver_name;?></td>
					<td style="text-align:center;"><?=$data[$i]->do_delivered_quantity;?></td>
                    <td style="text-align:center;"><?=$data[$i]->do_delivered_quantity_mortar;?></td>
					<td style="text-align:center;"><?php echo "Rp."." ".number_format($data[$i]->cost);?></td>
					<td style="text-align:center;">
						<?php 
							$sh_date = date("d-m-Y", strtotime($data[$i]->do_delivered_date));
							echo $sh_date;
						?>
					</td>
					<td>
						<a href="javascript: dosj_hist_edit('<?php echo $data[$i]->do_delivered_id;?>')" title="Edit"><img src="<?php echo base_url();?>assets/images/edit.gif" /></a>
						<a href="javascript: dosj_hist_delete('<?php echo $data[$i]->do_delivered_id;?>')" title="Delete"><img src="<?php echo base_url();?>assets/images/trash.gif" alt="Delete" /></a>
					</td>
				</tr>
			<?php
			$total_cost = $total_cost + $data[$i]->cost;
			}
			?>
			<tr>
				<td style="text-align:right;" colspan="11">Total Cost : <?php echo "Rp."." ".number_format($total_cost); ?></td>
			</tr>
			</tbody>
			<tfoot>
					<tr>
							<td colspan="11"><?=$paging?></td>
					</tr>
			</tfoot>
		</table>
