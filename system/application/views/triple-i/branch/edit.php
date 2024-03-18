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
        <h2>Edit Branch Office</h2>
    <form name="frmedit" id="frmedit" onsubmit="javascript:return frmedit_onsubmit()">
        <input type="hidden" name="company_id" id="company_id" value="<?php echo $data->company_id;?>" />
    <table style="font-size: 12px;">
        <tr>
            <td>Name</td>
            <td>:</td>
            <td><input type="text" size="35" name="company_name" id="company_name" value="<?php echo $data->company_name; ?>" /></td>
        </tr>
        <tr>
            <td>Address</td>
            <td>:</td>
            <td><input type="text" size="35" name="branch_address" id="branch_address" value="<?php echo $data_branch->branch_address; ?>"  /></td>
        </tr>
         <tr>
            <td>City</td>
            <td>:</td>
            <td><input type="text" size="35" name="branch_city" id="branch_city" value="<?php echo $data_branch->branch_city; ?>"  /></td>
        </tr>
        <tr>
            <td>Phone</td>
            <td>:</td>
            <td><input type="text" size="35" name="branch_telp" id="branch_telp" value="<?php echo $data_branch->branch_telp;?>" /></td>
        </tr>
        <tr>
            <td>Fax</td>
            <td>:</td>
            <td><input type="text" size="35" name="branch_fax" id="branch_fax" value="<?php echo $data_branch->branch_fax;?>" /></td>
        </tr>
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