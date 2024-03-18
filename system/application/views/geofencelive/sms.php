<script src="<?php echo base_url(); ?>assets/js/jquery-ui-1.7.2.custom/js/jquery-1.3.2.min.js" type="text/javascript"></script> 
<script>
	jQuery(document).ready(
		function()
		{
			provinsi_onchange();
		}
	);
	
	function provinsi_onchange()
	{
		jQuery("#kotakab").html("loading...");
		
		var prov = jQuery("#provinsi").val();
		jQuery.post("<?php echo base_url(); ?>geofence/loadkabkota", {provinsi: prov},
			function(r)
			{
				jQuery("#kotakab").html(r.html);
			}
			, "json"
		);
		
	}
	
	function frmgeofence_onsubmit()
	{
		jQuery.post("<?php echo base_url(); ?>geofence/smssave/<?php echo $vehicle->vehicle_id; ?>/<?php echo $nohp; ?>", jQuery("#frmgeofence").serialize(),
			function(r)
			{
				if (r.error)
				{
					alert(r.message);
					return;
				}
				
				alert(r.message);
			}
			, "json"
		);

		return false;
	}
	
	function notexist()
	{
		jQuery.post("<?php echo base_url(); ?>geofence/smsnotfound/<?php echo $vehicle->vehicle_id; ?>/<?php echo $nohp; ?>", jQuery("#frmgeofence").serialize(),
			function(r)
			{
				alert(r.message);
			}
			, "json"
		);		
	}
</script>
<form name="frmgeofence" id="frmgeofence" onsubmit="javascript: return frmgeofence_onsubmit()">
	<h1><?php echo $vehicle->vehicle_no; ?></h1>
	<input type="hidden" name="hp" id="hp" value="<?php echo $hp; ?>" />	
	<table width="100%" cellpadding="1" cellspacing="1" border="0">
		<tr>
			<td width="10%">Propinsi</td>
			<td>
				<select name="provinsi" id="provinsi" onchange="javascript: provinsi_onchange()">
					<?php for($i=0; $i < count($provinsies); $i++) { ?>
					<option value="<?php echo $provinsies[$i]->PROPINSI; ?>"><?php echo $provinsies[$i]->PROPINSI; ?></option>
					<?php } ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Kota/Kab</td>
			<td><div id="kotakab"></div></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<input type="submit" value=" Save " />
				<input type="button" value=" Kota tidak ada " onclick="javscript: notexist()" />
			</td>
		</tr>
	</table>
</form>
