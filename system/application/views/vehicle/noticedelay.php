<html>
	<head>
	</head>
	<body>
		<h1><?php echo $vehicle->vehicle_name;?> <?php echo $vehicle->vehicle_no;?></h1>
		<table width="100%" cellpadding="2" cellspacing="2" border="0">
				<tr>
					<td width="30%">Pemilik</td>
					<td width="5%">:</td>
					<td><?php echo $vehicle->user_name;?> [<?php echo $vehicle->user_mail;?>]</td>
				</tr>
				<tr>
					<td>Device ID</td>
					<td>:</td>
					<td><?php echo $vehicle->vehicle_device;?></td>
				</tr>				
				<tr>
					<td>No Kartu</td>
					<td>:</td>
					<td><?php echo $vehicle->vehicle_card_no;?></td>
				</tr>
				<tr>
					<td>Operator</td>
					<td>:</td>
					<td><?php echo $vehicle->vehicle_operator;?></td>
				</tr>
				<tr>
					<td>Tipe GPS</td>
					<td>:</td>
					<td><?php echo $vehicle->vehicle_type;?></td>
				</tr>				
		</table>
		<p>Data terakhir untuk kendaraan  pada jam <b><?php echo date("d/m/Y H:i:s", $lastreceive);?></b>. 
		<p>Silahkan hubungin agen Anda.
		<p>&nbsp;
		<p>&nbsp;
		<p>Terima Kasih
		<p>&nbsp;
		<p>&nbsp;		
		<p><a href="<?php echo $ownerurl; ?>"><?php echo $owner; ?></a>
	</body>
</html>
