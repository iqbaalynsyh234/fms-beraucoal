<script>
	
	jQuery(document).ready(
		function()
		{
			showclock();
		}
	);
    
</script>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
<?=$navigation;?>
	<div id="main" style="margin: 20px;">
		<div class="block-border">
			<h1><center>TUPPERWARE</center></h1>
            <br />
            <small>Menu-Menu Dibawah ini adalah menu yang terintegrasi dengan sistem dari PT. Tupperware, TBK.
			Aplikasi yang berhubungan dengan PT. Tupperware, TBK dapat di akses hanya jika Perusahaan anda terdaftar sebagai
			Transporter PT. Tupperware, TBK	
			</small>
            <hr />
			<center>
				<h1><img src="<?php echo base_url();?>assets/tupperware/images/order.png" border="0" style="vertical-align:middle;" />
				<a href="<?php echo base_url();?>transporter/tupperware/booking_id"target="_blank">Booking ID</a></h1>
			</center>
		</div>
	</div>
</div>
			

			