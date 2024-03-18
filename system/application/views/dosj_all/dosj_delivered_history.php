<h2>SO HISTORY
	<?php 
		if (isset($data1) && (count($data1)>0))
		{
			echo "("." ".$data1->dosj_type." ".$data1->dosj_no_block." ".$data1->dosj_no_mortar." "."  )";
		}
	?>
</h2>
<?php 
	if (isset($data))
	{	
		if (isset($data1->dosj_delivery_status) && ($data1->dosj_delivery_status == 2))
		{
?>
	( Status Complete )
<?php
		}
		else
		{
?>
	( Status InComplete )
<?php
		}
	}
?>
<hr></hr>
<div id="main_data">
		<!-- Identifiakasi Variabel Pendukung -->
		<?php
			$tq = 0;
            $tqm = 0;
			$tc = 0;
		?>
		<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<thead>
				<tr>
					<th rowspan="2" width="2%">No.</td>
					<th rowspan="2" style="text-align:center">Ship Date</td>
					<th rowspan="2" style="text-align:center">Vehicle</td>
					<th rowspan="2" style="text-align:center">Driver</td>
					<th colspan="2" style="text-align:center">Quantity</td>
					<th rowspan="2" style="text-align:center">Delivered Date</td>
					<th rowspan="2" style="text-align:center">Cost</td>
					
				</tr>
                <tr>
                    <td style="text-align:center">Block</td>
                    <td style="text-align:center">Mortar</td>
                </tr>
			</thead>
			<tbody>
				
					<?php 
						if (isset($data) && (count($data)>0))
						{
							for($i=0;$i<count($data);$i++)
							{
					?>
							<tr>
							<td><?php echo $i+1;?></td>
							<td style="text-align:center"><?php echo date("d-M-Y",strtotime($data[$i]->dosj_ship_date));?></td>
							<td>
								<?php 
								if (isset($vehicle))
								{
									if (count($vehicle)>0)
									{
										for ($y=0;$y<count($vehicle);$y++)
										{
											if ($vehicle[$y]->vehicle_device == $data[$i]->do_delivered_vehicle)
											{
												echo $vehicle[$y]->vehicle_name." - ".$vehicle[$y]->vehicle_no;
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
							?></td>
							<td><?php echo $data[$i]->driver_name;?></td>
							<td style="text-align:right"><?php echo $data[$i]->do_delivered_quantity;?></td>
                            <td style="text-align:right"><?php echo $data[$i]->do_delivered_quantity_mortar;?></td>
							<td style="text-align:center"><?php if(isset($data[$i]->do_delivered_date)){echo date("d-M-Y",strtotime($data[$i]->do_delivered_date));}?></td>
							<td style="text-align:right"><?php echo number_format($data[$i]->cost);?></td>
							
							</tr>
					<?php
							$tq = $tq+$data[$i]->do_delivered_quantity;
                            $tqm = $tqm+$data[$i]->do_delivered_quantity_mortar;
							$tc = $tc+$data[$i]->cost;
							}
						} else
						{
							echo "No Delivered History";
						}
					?>
				<tr>
					<?php 
					if (isset($data) && (count($data)>0))
					{
				?>
					<td style="text-align:right" colspan="4">
						<?php 
							$mq = $data1->dosj_item_quantity - $tq;
                            $mqm = $data1->dosj_item_quantity_mortar - $tqm;
                            echo "TOTAL ("." ".$data1->dosj_item_quantity."/".$mq." "." )";
                            echo "("." ".$data1->dosj_item_quantity_mortar."/".$mqm." ".")";
						?>
					</td>
					<td style="text-align:right"><?php echo $tq;?></td>
                    <td style="text-align:right"><?php echo $tqm;?></td>
					<td style="text-align:right" colspan="2"><?php echo number_format($tc);?></td>
					
				<?php
					}
					?>
				</tr>
			</tbody>			
		</table>
</div>