<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
	<table>
		<tr>
			<td>BarCode_No</td>
			<td>Schedule_Date</td>
			<td>WH</td>
			<td>DB_Type</td>
			<td>Destination</td>
			<td>SLCARS</td>
			<td>Expedition_Name</td>
			<td>Fleet_Type</td>
			<td>Fleet_CBM</td>
		</tr>
			<?php 
			foreach($csvData as $field) { ?>
			<tr>
				<td><?=$field["BarCode_No"];?></td>
				<td><?=$field["Schedule_Date"];?></td>
				<td><?=$field["WH"];?></td>
				<td><?=$field["DB_Type"];?></td>
				<td><?=$field["Destination"];?></td>
				<td><?=$field["SLCARS"];?></td>
				<td><?=$field["Expedition_Name"];?></td>
				<td><?=$field["Fleet_Type"];?></td>
				<td><?=$field["Fleet_CBM"];?></td>
			</tr>
			<?php } ?>
	</table>
</div>