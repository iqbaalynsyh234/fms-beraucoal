<H2>LAYANAN GPS TRACKER SMS</h2>

<h3>Format SMS</h3>

<ol>
<li>Untuk monitor posisi kendaraan Anda.
	Ketik <?php echo htmlspecialchars("PSS<spasi><username><spasi><no kendaraan>", ENT_QUOTES); ?> kirim ke <?php echo $this->config->item("SMS_MYGSM"); ?></li>
</ol>

<h3>Auto Refill</h3>
<ul>
	<li>Auto Refill adalah mekanisme pengisian pulsa kartu gsm yang berada dalam alat GPS secara otomatis.</li>
	<li>Ketika pulsa kartu gsm "habis", sistem secara otomatis akan men-transfer pulsa sebesar <?php echo number_format($this->config->item("SMS_AUTOREFILL_AMOUNT_PULSA"), 0, "", "."); ?> pada kartu gsm tersebut.</li>
	<li>Syarat dan aturan auto refill sistem adalah:
		<ol>
			<li>Anda harus men-setting kendaraan mana saja yang akan diauto refill. Klik <a href="<?php echo base_url(); ?>sms/autorefill"><?php echo $this->lang->line("lauto_refill"); ?></a>
			</li>
			<li>Anda harus mempunyai deposit uang yang cukup pada kami. Untuk melakukan deposit klik <a href="<?php echo base_url(); ?>sms/deposit"><?php echo $this->lang->line("ldeposit"); ?></a>				
			</li>
			<li>Satu kali auto refill, maka deposit Anda akan dipotong sejumlah pulsa yang ditransfer <?php echo number_format($this->config->item("SMS_AUTOREFILL_AMOUNT_PULSA"), 0, "", "."); ?>+<?php echo  number_format($this->config->item("SMS_AUTOREFILL_AMOUNT_DEBET")-$this->config->item("SMS_AUTOREFILL_AMOUNT_PULSA"), 0, "", "."); ?>
			</li>
			<li>Anda dapat melihat saldo dan transaksi pada menu [ <?php echo $this->lang->line("lbalance"); ?> ]. Klik <a href="<?php echo base_url(); ?>sms/balance"><?php echo $this->lang->line("lbalance"); ?></a>
		</ol>
	</li>
</ul>

<h3>Layanan Premium</h3>
Layanan ini masih dalam pengembangan.
<ol>
	<li>Sistem mengirim posisi ke no hp Anda secara periodik. Maksimum lama periodik adalah 1 jam. Untuk sistem ini akan dikenakan biaya 2.000 per kendaraan per bulan.
</ol>

<br />&nbsp;
<br /><a href='<?php echo base_url(); ?>sms/home'><font color="#aaaa00">[ <?php echo $this->lang->line('lback'); ?> ]</font></a>