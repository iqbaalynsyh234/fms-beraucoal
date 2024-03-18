<html>
<head>
<title>Invoice</title>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/invoice.css"> </head>
<body bgcolor="#ECE5B6">
<div id="page">
  <div id="logo">
    <img src="<?php echo base_url(); ?>assets/images/lacak-mobil.png" width="400px" heigth="262px" />
  </div><!--end logo-->
  
  <div id="address">

    <p><h2>INVOICE</h2>
    Tagihan #<?php echo $invoiceno; ?><br />
    Tanggal Cetak Tagihan : <?php echo date("d/m/Y"); ?><br />
	Jatuh Tempo : <?php echo date("d/m/Y", $expiredate1); ?>
    </p>

  </div><!--end address-->

  <div id="content">

    <p>
	  <table border="1">
	  <tr class="odd2">
		<td width="50%">
			<strong>Ditagih Ke</strong>
			<br />Name: <?php echo $user->user_name; ?>
			<?php if ($user->user_mail) {  echo "<br />Email: ".$user->user_mail;  } ?>
			<?php if ($user->user_address) {  echo "<br />Address: ".$user->user_address;  } ?>
			<?php if ($user->user_city) {  echo "<br />".$user->user_city;  } ?>
			<?php if ($user->user_province) {  echo "<br />".$user->user_province;  } ?>
			<?php if ($user->user_mobile) {  echo "<br />telp. ".$user->user_mobile;  } ?>
	    </td>
		<td width="50%">
			<strong>Dibayar Ke</strong><br />
			CV. Anugerah Cipta Karya<br />
			Komplek Ruko Jatiasih<br />
			Jl. Raya Swatantra Kav.4 No.71
			Jatiasih - Bekasi 17424<br />
			Telp : 021 - 82434 946<br />
			</p>
		</td>
	   </tr>
	  </table>
	  
    <hr>
	
    <table>
      <tr>
		<td align="center" width="50%">
			<strong>Deskripsi</strong>
		</td>
		
		<td align="center" width="50%">
			<strong>Jumlah</strong>
		</td>
	  </tr>
      <tr class="odd">
		<td width="50%">Biaya Layanan GPS Periode <?php echo date("d/m/Y", $expiredate1); ?> - <?php echo date("d/m/Y", $expiredate2); ?></td>
		<td width="50%" align="center">Rp. <?php echo number_format($user->user_payment_amount, 0, "", "."); ?></td>
	  </tr>
    </table>
	
    <hr>
	
	<table>
		<tr>
			<td width="50%" align="right">
				<strong>Total</strong>
			</td>
			<td width="50%" align="center">
				<strong>Rp. <?php echo number_format($user->user_payment_amount, 0, "", "."); ?></strong>
			</td>
		</tr>
	</table>
	
	<hr>
	<p><br />
	<!-- transaction-list -->	
	<hr>
    <p>
	
	<table>
		<tr>
			<td width="50%" valign="top">
				<strong>Pembayaran dapat di transfer ke</strong><p>
				BANK BCA Cab. Time Square Cibubur<br />
				A/C Nama  : JAYATRIYADI<br />
				A/C Nomor : 7400-94-0481
				<p>
				BANK MANDIRI Cab. Vila Nusa Indah<br />
				A/C Nama  : WIDA HARFIATY<br />
				A/C Nomor : 156-0000-912-321
				
			</td>
			<td width="50%" valign="top">
				<strong>Konfirmasi Pembayaran</strong> (<a href="<?php echo base_url(); ?>home/login/<?php echo $session; ?>/invoice/<?php echo $invoiceno; ?>">Klik Disini</a>)<p />
				Kirim SMS dengan Format :<p />
				[BAYAR] [INVOICE#] [JML DIBAYAR DLM RIBUAN] [TGL BAYAR] [CARA PEMBAYARAN] [NAMA PEMILIK REKENING / NO. VALIDASI JIKA TUNAI]<p/>
				Contoh :<br />
				BAYAR 000001 150 28092011 ATM ABDULLAH<p />
				SMS Di Kirim Ke : 0877777 97 920
			</td>
				
		</tr>
	</table>
	  <p><br /><font size="3"><center>
      Terima kasih atas kepecaryaan anda menggunakan sistem kami untuk memonitor kendaraan anda.<br/> 
	  Transaksi ini akan muncul pada tagihan anda sebagai invoice dari sistem monitoring / GPS Vehicle Tracking System "www.lacak-mobil.com".
      Jika anda memiliki pertanyaan, jangan ragu untuk menghubungi kami di <a href="mailto:info@lacak-mobil.com">info@lacak-mobil.com</a>.
	  <center></font>
    </p>

    <hr>

    <p>
      <center><small>Invoice ini hanya diperuntukan bagi nama tertuju diatas karena berifat khusus dan rahasia. Apabila anda bukan nama tertuju dilarang untuk 
	  mengumumkan, mengedarkan atau menggandakan Invoice ini.
      <br /><br />
      &copy; CV. Anugerah Cipta Karya
      </small></center>
    </p>
  </div><!--end content-->
</div><!--end page-->
</body>

</html>
