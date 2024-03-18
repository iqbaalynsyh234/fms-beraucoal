<html>
<head>
<title>Invoice</title>
</head>
<body bgcolor="#ECE5B6">		
		<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<thead>
				<tr>
					<th width="1%">No.</td>
					<th width="8%" valign="top" style="text-align:center;">Vehicle No</th>
					<th width="8%" valign="top" style="text-align:center;">Vehicle Name</th>
					<th width="8%" valign="top" style="text-align:center;">Status </th>
					
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
				
					<td valign="top" style="text-align:center;">
						<?=$data[$i]->daily_alert_vehicle_no; ?>
					</td>
					
					<td valign="top" style="text-align:center;">
						<?=$data[$i]->daily_alert_vehicle_name; ?>
					</td>
					<td valign="top" style="text-align:center;">
						<? echo "STAND BY"; ?>
					</td>	
				</tr>
				
			<?php
			}
			}else{
				echo "<tr><td colspan='5'>No Data Available</td></tr>";
			}
			?>
			</tbody>
			<tfoot>
					<tr>
						
					</tr>
			</tfoot>
		</table>
	</body>
</html>	
