Yth <?php echo $payment->user_name; ?>,<br /><br />

Ini adalah resi pembayaran untuk tagihan #<?php echo $payment->invoice_no; ?> yang dibuat tanggal <?php $t = dbmaketime($payment->invoice_date); echo date("d/m/Y", $t); ?><br />

Jatuh tempo: <?php $t = dbmaketime($payment->invoice_period1); echo date("d/m/Y", $t); ?><br />
Jumlah yang dibayar: Rp. <?php echo number_format($payment->payment_amount, 0, "", "."); ?>

