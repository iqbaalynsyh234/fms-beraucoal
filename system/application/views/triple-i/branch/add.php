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
    <h2>Add Branch Office</h2>
<!--New Form -->
<form id="frmadd" name="frmadd" onsubmit="javascript: return frmadd_onsubmit()">
<table style="font-size: 12px;">
    <tr>
        <td>Name</td>
        <td>:</td>
        <td><input type="text" name="branch_name" id="branch_name" /></td>
    </tr>
    <tr>
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
    </tr>
    <tr>
        <td><input type="submit" id="submit" name="submit" value="Save" /></td>
        <td><input type="button" name="btncancel" value="Cancel" onclick="location='<?php echo base_url();?>transporter/branchoffice/index'" /></td>
        <td><img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;" /></td>
    </tr>
</table>
</form> 
</div>