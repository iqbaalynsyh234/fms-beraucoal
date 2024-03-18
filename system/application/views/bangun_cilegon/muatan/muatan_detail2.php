
		<form method="post" action="#" >
		<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<tr align="right ">
				<h2><?php echo "Vehicle :"." [ ".$row->muatan_vehicle_name." ] "." ".$row->muatan_vehicle_no; ?></h2></b>
			</tr>
			<tr>
				<td><b>Driver</b></td>
				<td>:</td>
				<td><?=$row->driver_name;?></td>
			</tr>
			<tr>
				<td><b>Datetime</b></td>
				<td>:</td>
				<td><?=date("d-m-Y H:i",strtotime($row->muatan_startdate. " ".$row->muatan_starttime)) ?></td>	
			</tr>
			<tr>
				<td><b>Muatan</b></td>
				<td>:</td>
				<td><?=$row->muatan_data_name;?></td>
			</tr>
			<tr>
				<td><b>Weight</b></td>
				<td>:</td>
				<td><?=$row->muatan_weight;?></td>
			</tr>
			<tr>
				<td><b>Destination</b></td>
				<td>:</td>
				<td><?=$row->muatan_dest;?></td>
			</tr>
			<tr>
				<td><b>Notes</b></td>
				<td>:</td>
				<td><?=$row->muatan_note;?></td>
			</tr>
	
		</table>
		</form>
