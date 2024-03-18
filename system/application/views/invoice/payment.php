	<table>
		<tr>
			<td>
				<strong>Tanggal Transaksi</strong>
			</td>
			<td>
				<strong>Pembayaran Melalui</strong>
			</td>
			<td>
				<strong>ID Transaksi</strong>
			</td>
		</tr>
		<?php for($i=0; $i < count($payments); $i++) { ?>
		<?php
			$t = dbmaketime($payments[$i]->payment_date);
		?>
		<tr  class="odd">
			<td><?php echo date("d/m/Y", $t); ?></td>
			<td><?php echo $payments[$i]->payment_method; ?></td>
			<td><?php echo $payments[$i]->payment_id; ?></td>
		</tr>
		<?php } ?>
	</table>
