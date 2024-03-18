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
		jQuery.post("<?=base_url()?>andalas_info_alert/info_alert_save", jQuery("#frmadd").serialize(),	
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
		<h1>Edit Info Alert</h1>
		<?php } else { ?>
		<h1>Add Info Alert</h1>
		<?php } ?>
				<table width="90%" cellpadding="3" class="table sortable no-margin">
					<?php if (isset($row)) { ?>					
					<input type="hidden" id="id" name="id" value="<?=$row->alert_dispatcher_id;?>" />
					<tr style="display:none;">
						<td>ID</td>
						<td>:</td>
						<td><?=$row->alert_dispatcher_id;?></td>
					</tr>
					<?php } ?>
				<tr>
					<td colspan="3"><h2>Require Information</h2></td>
				</tr>
				
    			<input type="hidden" id="alert_dispatcher_company" name="alert_dispatcher_company" value="<?php echo $this->sess->user_company; ?>" />
				<tr>
					<td>Name</td>
					<td>:</td>
					<td><input type="text" size="35" name="alert_dispatcher_name" id="alert_dispatcher_name" value="<?=isset($row) ? htmlspecialchars($row->alert_dispatcher_name, ENT_QUOTES) : "";?>" class="formdefault" /></span></td>
				</tr>	
    			
				<tr>
					<td>Mobile Phone</td>
					<td>:</td>
						<td><input type="text" size="20" maxlength = "13" name="alert_dispatcher_mobile" id="alert_dispatcher_mobile" value="<?=isset($row) ? htmlspecialchars($row->alert_dispatcher_mobile, ENT_QUOTES) : "";?>" class="formdefault" /></td>
				</tr>
				
				<tr>
					<td>Email</td>
					<td>:</td>
					<td><input type="text" size="35" name="alert_dispatcher_email" id="alert_dispatcher_email" value="<?=isset($row) ? htmlspecialchars($row->alert_dispatcher_email, ENT_QUOTES) : "";?>" class="formdefault" /></td>
				</tr>
				
				<tr>
					<td>Alert SMS</td>
					<td>:</td>
					<td>
						<input name="alert_dispatcher_config_mobile" type="radio" value="1"> Yes</input>
						<input name="alert_dispatcher_config_mobile" type="radio" value="0" checked > No</input>
					</td>
				</tr>
				
				<tr>
					<td>Alert Email</td>
					<td>:</td>
					<td>
						<input name="alert_dispatcher_config_email" type="radio" value="1"> Yes</input>
						<input name="alert_dispatcher_config_email" type="radio" value="0" checked > No</input>
					</td>
				</tr>
				
				<tr>
					<td>Status Active</td>
					<td>:</td>
					<td>
						<input name="alert_dispatcher_config_status" type="checkbox" value="1" checked> Active </option>
					</td>
				</tr>
			
    			<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>
								<input type="submit" name="btnsave" id="btnsave" value=" Save " />
			
								<input type="button" name="btncancel" id="btncancel" value=" Cancel " onclick="location='<?=base_url()?>andalas_info_alert';" />
								<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
						</td>
					</tr>					
				</table>
			</form>		
		</div>
	</div>
</div>
			
