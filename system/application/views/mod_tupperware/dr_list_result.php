<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
	<thead>
		<tr>
			<th width="2%" style="text-align:center;">No.</td>
			<th style="text-align:center;">SO</th>
			<th style="text-align:center;">DR</th>
			<th style="text-align:center;">ID BOOKING</th>
			<th style="text-align:center;">SO TYPE</th>					
			<th style="text-align:center;">DB</th>
		</tr>
	</thead>
	<tbody>
	<?php
	if(count($data) > 0)
	{
		for($i=0; $i < count($data); $i++)
		{
	?>
		<tr <?=($i%2) ? "class='odd'" : "";?>>
			<td valign="top"><?=$i+1+$offset?></td>
			<td valign="top"><?=$data[$i]->transporter_dr_so;?></td>
			<td valign="top"><?=$data[$i]->transporter_dr_dr;?></td>
			<td valign="top">
				<?php 
					echo $data[$i]->transporter_dr_booking_id;
					if (isset($data[$i]->booking_delivery_status))
					{
						if ($data[$i]->booking_delivery_status == 2)
						{
							echo "<br />";
							echo "<b>";
							echo "DELIVERED :"." ";
							echo "</b>";
							echo date("d-m-Y H:i:s",strtotime($data[$i]->booking_delivered_datetime));
						}
					}
				?>
			</td>	
			<td valign="top"><?=$data[$i]->transporter_so_type;?></td>
			<td valign="top">
				<?php 
					if (isset($data[$i]->dist_name))
					{
						echo $data[$i]->dist_name;
					}
					else
					{
						echo "DB NOT SET";
					}
				?>
			</td>
		</tr>
	<?php
		}
	}
	else
	{
		echo "<tr><td colspan='6'>No Data Available</td></tr>";
	}
	?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="6"><?=$paging?></td>
		</tr>
	</tfoot>
</table>
