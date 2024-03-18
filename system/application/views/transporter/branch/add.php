<script>
	function frmadd_onsubmit()
	{
		jQuery("#loader").show();
		jQuery.post("<?=base_url()?>transporter/branchoffice/save", jQuery("#frmadd").serialize(),	
			function(r)
			{
				jQuery("#loader").hide();
				if (r.error)
				{
					alert(r.message);
					return false;
				}
				
				alert(r.message);
				location = r.redirect;
			}
			, "json"	
		);
		return false;
	}
</script>

<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
	<?=$navigation;?>
	<div id="main" style="margin: 20px;"><br />
    <br />
    <div class="block-border">
    <form class="block-content form" id="frmadd" name="frmadd" onsubmit="javascript: return frmadd_onsubmit()">
    <h1>Add Branch Office</h1>
    <table class="table sortable no-margin">
    <tr>
        <td>Name</td>
        <td>:</td>
        <td><input type="text" name="branch_name" id="branch_name" /></td>
    </tr>
	<tr>
            <td>Note</td>
            <td>:</td>
            <td>
				<small>
	Proses Add Telegram GROUP:  <br />
	1. Buka (https://web.telegram.org) dan LOGIN sebagai User yang membuat GROUP baru <br />
	2. Buat GROUP di Telegram Web<br />
	3. Invite bot_lacakmobil dalam GROUP Tersebut <br />
	4. Klik GROUP yang baru dibuat untuk mendapatkan CHAT ID nya (Contoh : https://web.telegram.org/#/im?p=g154513121) <br />
	5. Contoh 154513121 adalah CHAT ID dari GROUP Tersebut, untuk memasukkan Ke dalam System ditambahkan tanda - menjadi -154513121 
				</small>
			</td>
    </tr>
	 <tr>
        <td>Telegram Group ID (SOS Alert)</td>
        <td>:</td>
        <td><input type="text" name="company_telegram_sos" id="company_telegram_sos" /></td>
    </tr>
	 <tr>
        <td>Telegram Group ID (Parking Alert)</td>
        <td>:</td>
        <td><input type="text" name="company_telegram_parkir" id="company_telegram_parkir" /></td>
    </tr>
	 <tr>
        <td>Telegram Group ID (Speed Alert)</td>
        <td>:</td>
        <td><input type="text" name="company_telegram_speed" id="company_telegram_speed" /></td>
    </tr>
	 <tr>
        <td>Telegram Group ID (Geofence Alert)</td>
        <td>:</td>
        <td><input type="text" name="company_telegram_geofence" id="company_telegram_geofence" /></td>
    </tr>
   <!-- <tr>
        <td>Address</td>
        <td>:</td>
        <td><input type="text" name="branch_address" id="branch_address"/></td>
    </tr>
    <tr>
        <td>City</td>
        <td>:</td>
        <td><input type="text" name="branch_city" id="branch_city" /></td>
    </tr>
    <tr>
        <td>Telp</td>
        <td>:</td>
        <td><input type="text" name="branch_tlp" id="branch_tlp" /></td>
    </tr>
    <tr>
        <td>Fax</td>
        <td>:</td>
        <td><input type="text" name="branch_fax" id="branch_fax" /></td>
    </tr>-->
    <tr>
        <td><input type="submit" id="submit" name="submit" value="Save" /></td>
        <td><input type="button" name="btncancel" value="Cancel" onclick="location='<?php echo base_url();?>transporter/branchoffice/index'" /></td>
        <td><img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;" /></td>
    </tr>
</table>
</form>
</div>
</div>