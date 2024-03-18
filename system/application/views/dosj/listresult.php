		<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<thead>
				<tr>
					<th rowspan="2" width="2%">No.</td>
					<th rowspan="2" style="text-align:center;">SO Type</th>
					<th style="text-align:center;" colspan="2">SO Number</th>
					<th rowspan="2" style="text-align:center;">Customer</th>
					<th rowspan="2" style="text-align:center;">Item Desc.</th>
					<th colspan="2" style="text-align:center;">Total Quantity</th>
					<th colspan="2" style="text-align:center;">SJP</th>
					<th rowspan="2" style="text-align:center;">Control</th>
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
			<tbody>
			<?php
			for($i=0; $i < count($data); $i++)
			{
			?>
				<tr <?=($i%2) ? "class='odd'" : "";?>>
					<td style="text-align:center;"><?=$i+1+$offset?></td>
					<td style="text-align:center;"><?=$data[$i]->dosj_type;?></td>
					<td style="text-align:center;"><?=$data[$i]->dosj_no_block;?></td>
					<td><?=$data[$i]->dosj_no_mortar;?></td>
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
							//echo $data[$i]->dosj_item_panjang."x".$data[$i]->dosj_item_lebar."x".$data[$i]->dosj_item_tinggi." ".$data[$i]->dosj_item_unit;
							echo $data[$i]->dosj_item_size;
						?>
					</td>
					<td style="text-align:center;">
						<?php
							echo $data[$i]->dosj_item_quantity;
						?>
					</td>
                    <td style="text-align:center;">
                        <?php
                            echo $data[$i]->dosj_item_quantity_mortar;
                        ?>
                    </td>
                    <td style="text-align:center;"><?=$data[$i]->dosj_block_no;?></td>
					<td style="text-align:center;"><?=$data[$i]->dosj_mortar_no;?></td>
					<td>
						<a href="javascript: dosj_history('<?php echo $data[$i]->dosj_no;?>')" title="Cost & Shiping History">
							<img src="<?php echo base_url();?>assets/images/learning_profile.png" />
						</a>
						<a href="javascript: dosj_edit('<?php echo $data[$i]->dosj_no;?>')" title="Edit"><img src="<?php echo base_url();?>assets/images/edit.gif" /></a>
						<a href="javascript: dosj_delete('<?php echo $data[$i]->dosj_no;?>')" title="Delete"><img src="<?php echo base_url();?>assets/images/trash.gif" alt="Delete" /></a>
						
					</td>
				</tr>
			<?php
			}
			?>
			</tbody>
			<tfoot>
					<tr>
							<td colspan="11"><?php if (isset($paging)) { echo $paging; } ?></td>
					</tr>
			</tfoot>
		</table>
