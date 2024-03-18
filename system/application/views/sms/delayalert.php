<html>
	<head>
	</head>
	<body>
		<h2>Berikut daftar kendaraan yang tidak terupdate datanya</h2>
		<table width="100%" cellpadding="2" cellspacing="2" border="1">
			<tr>
				<td width="4%" align="center">No</td>
				<td width="20%" align="center">Login</td>
				<td width="20%" align="center">No Kendaraan</td>
				<td width="5%" align="center">GPS</td>
				<td align="center">Update Terakhir</td>
			</tr>
			<?php $i=1; foreach($datas as $row) { ?>
			<tr bgcolor="<?php echo $row->delays[1]; ?>">
				<td align="right"><font color="<?php echo $row->delays[2]; ?>"><?php echo $i++; ?></font></td>
				<td><font color="<?php echo $row->delays[2]; ?>"><?php echo $row->user_login; ?></font></td>
				<td><font color="<?php echo $row->delays[2]; ?>"><?php echo $row->vehicle_no; ?></font></td>
				<td><font color="<?php echo $row->delays[2]; ?>"><?php echo $row->vehicle_type; ?></font></td>
				<td align="center"><font color="<?php echo $row->delays[2]; ?>"><?php echo $row->time_fmt; ?></font></td>
			</tr>
			<?php } ?>
		</table>
	</body>
</html>
