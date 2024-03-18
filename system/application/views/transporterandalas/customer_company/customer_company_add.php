<script>
	jQuery(document).ready(
		function()
		{
			showclock();
			
		}
	);
	
	function frmadd_onsubmit()
	{
		jQuery("#loader").show();
		jQuery.post("<?=base_url()?>andalas_customer_company/save", jQuery("#frmadd").serialize(),	
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
		<div class="block-border">
		<form class="block-content form" name="frmadd" id="frmadd" onsubmit="javascript: return frmadd_onsubmit()">				
		<?php if (isset($row)) { ?>			
		<h1>Edit Data Customer Company</h1>
		<?php } else { ?>
		<h1>Add Data Customer Company</h1>
		<?php } ?>
				<table width="90%" cellpadding="3" class="table sortable no-margin">
					<?php if (isset($row)) { ?>					
					<input type="hidden" id="id" name="id" value="<?=$row->customer_company_id;?>" />
					<tr style="display:none;">
						<td>ID</td>
						<td>:</td>
						<td><?=$row->customer_company_id;?></td>
					</tr>
					<?php } ?>
				<tr>
					<td colspan="3"><h2>Require Information</h2></td>
				</tr>
				
    			<input type="hidden" id="customer_company_usercompany" name="customer_company_usercompany" value="<?php echo $this->sess->user_company; ?>" />
				<tr>
					<td>Company Name</td>
					<td>:</td>
					<td><input type="text" size="35" name="customer_company_name" id="customer_company_name" value="<?=isset($row) ? htmlspecialchars($row->customer_company_name, ENT_QUOTES) : "";?>" class="formdefault" /></span></td>
				</tr>	
    			
				<tr>
					<td>Address</td>
					<td>:</td>
					<td><input type="text" size="50" name="customer_company_address" id="customer_company_address" value="<?=isset($row) ? htmlspecialchars($row->customer_company_address, ENT_QUOTES) : "";?>" class="formdefault" /></td>
				</tr>
				<tr>
					<td>Status Active</td>
					<td>:</td>
					<td>
						<input type="checkbox" name="customer_company_status" id="customer_company_status" value="1" checked /> Active 
					</td>
				</tr>
			
				<!--<tr>
					<td>Status Active</td>
					<td>:</td>
					<td>
						<input name="customer_company_config_status" type="checkbox" value="1" checked> Active </option>
					</td>
				</tr> -->
			
    			<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>
								<input type="submit" name="btnsave" id="btnsave" value=" Save " />
			
								<input type="button" name="btncancel" id="btncancel" value=" Cancel " onclick="location='<?=base_url()?>andalas_customer_company';" />
								<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
						</td>
					</tr>					
				</table>
			</form>		
		</div>
	</div>
</div>
			
