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
		jQuery.post("<?=base_url()?>bgn_muatan/save_data", jQuery("#frmadd").serialize(),	
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
		<h1>Edit Data Muatan</h1>
		<?php } else { ?>
		<h1>Add Data Muatan</h1>
		<?php } ?>
				<table width="90%" cellpadding="3" class="table sortable no-margin">
					<?php if (isset($row)) { ?>					
					<input type="hidden" id="id" name="id" value="<?=$row->muatan_data_id;?>" />
					<tr style="display:none;">
						<td>ID</td>
						<td>:</td>
						<td><?=$row->muatan_data_id;?></td>
					</tr>
					<?php } ?>
				<tr>
					<td colspan="3"><h2>Require Information</h2></td>
				</tr>
				
    			<input type="hidden" id="company" name="company" value="<?php echo $this->sess->user_company; ?>" />
				<tr>
					<td>Name</td>
					<td>:</td>
					<td><input type="text" size="35" name="name" id="name" value="<?=isset($row) ? htmlspecialchars($row->muatan_data_name, ENT_QUOTES) : "";?>" class="formdefault" /></span></td>
				</tr>	
    			
				<tr>
					<td>Notes</td>
					<td>:</td>
					<td><input type="text" size="50" name="note" id="note" value="<?=isset($row) ? htmlspecialchars($row->muatan_data_note, ENT_QUOTES) : "";?>" class="formdefault" /></td>
				</tr>
				<tr>
					<td>Status Active</td>
					<td>:</td>
					<td>
						<input type="checkbox" name="status" id="status" value="1" checked /> Active 
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
			
								<input type="button" name="btncancel" id="btncancel" value=" Cancel " onclick="location='<?=base_url()?>bgn_muatan/data/';" />
								<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
						</td>
					</tr>					
				</table>
			</form>		
		</div>
	</div>
</div>
			
