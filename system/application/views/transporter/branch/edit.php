<script>
	function frmedit_onsubmit()
	{
		jQuery("#loader").show();
		jQuery.post("<?=base_url()?>transporter/branchoffice/update", jQuery("#frmedit").serialize(),	
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
	<div id="main" style="margin: 20px;">
		<br />&nbsp;
        <div class="block-border">
        <form class="block-content form"  name="frmedit" id="frmedit" onsubmit="javascript:return frmedit_onsubmit()">
        <h1>Edit Branch Office</h1>
        <input type="hidden" name="company_id" id="company_id" value="<?php echo $data->company_id;?>" />
    <table class="table sortable no-margin">
        <tr>
            <td>Name</td>
            <td>:</td>
            <td><input type="text" size="35" name="company_name" id="company_name" value="<?php echo $data->company_name; ?>" /></td>
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
            <td><input type="text" size="35" name="company_telegram_sos" id="company_telegram_sos" value="<?php echo $data->company_telegram_sos; ?>" /></td>
        </tr>
		<tr>
            <td>Telegram Group ID (Parking Alert)</td>
            <td>:</td>
            <td><input type="text" size="35" name="company_telegram_parkir" id="company_telegram_parkir" value="<?php echo $data->company_telegram_parkir; ?>" /></td>
        </tr>
		<tr>
            <td>Telegram Group ID (Speed Alert)</td>
            <td>:</td>
            <td><input type="text" size="35" name="company_telegram_speed" id="company_telegram_speed" value="<?php echo $data->company_telegram_speed; ?>" /></td>
        </tr>
		<tr>
            <td>Telegram Group ID (Geofence Alert)</td>
            <td>:</td>
            <td><input type="text" size="35" name="company_telegram_geofence" id="company_telegram_geofence" value="<?php echo $data->company_telegram_geofence; ?>" /></td>
        </tr>
        <!--<tr>
            <td>Address</td>
            <td>:</td>
            <td><input type="text" size="35" name="company_address" id="company_address" value="<?php echo $data->company_address; ?>"  /></td>
        </tr>
         <tr>
            <td>City</td>
            <td>:</td>
            <td><input type="text" size="35" name="company_city" id="company_city" value="<?php echo $data->company_city; ?>"  /></td>
        </tr>
        <tr>
            <td>Phone</td>
            <td>:</td>
            <td><input type="text" size="35" name="company_telp" id="company_telp" value="<?php echo $data->company_telp;?>" /></td>
        </tr>
        <tr>
            <td>Fax</td>
            <td>:</td>
            <td><input type="text" size="35" name="company_fax" id="company_fax" value="<?php echo $data->company_fax;?>" /></td>
        </tr> -->
        <tr>
            <td>
            <input type="submit" name="submit" id="submit" value="Update" />
            </td>
            <td>
            <input type="button" name="btncancel" id="btncancel" value="Cancel" onclick="location='<?php echo base_url();?>transporter/branchoffice/index'" />
            </td>
            <td><img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;" /></td>
        </tr>
    </table>    
    </form>
</div>
</div>
</div>