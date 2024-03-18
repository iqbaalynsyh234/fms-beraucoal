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
		jQuery.post("<?=base_url()?>andalas_customer/save", jQuery("#frmadd").serialize(),	
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
		<h1>Edit Data Customer</h1>
		<?php } else { ?>
		<h1>Add Data Customer</h1>
		<?php } ?>
				<table width="90%" cellpadding="3" class="table sortable no-margin">
					<?php if (isset($row)) { ?>					
					<input type="hidden" id="id" name="id" value="<?=$row->config_id;?>" />
					<tr style="display:none;">
						<td>ID</td>
						<td>:</td>
						<td><?=$row->config_id;?></td>
					</tr>
					<?php } ?>
				<tr>
					<td colspan="3"><h2>Require Information</h2></td>
				</tr>
				
				<input type="hidden" id="customer_company" name="customer_company" value="<?php echo $this->sess->user_company; ?>" />
				<tr>
					<td>Customer Name</td>
					<td>:</td>
					<td><input type="text" size="35" name="customer_name" id="customer_name" value="<?=isset($row) ? htmlspecialchars($row->customer_name, ENT_QUOTES) : "";?>" class="formdefault" /></span></td>
				</tr>
				<tr>
					<td>Customer Email</td>
					<td>:</td>
					<td><input type="text" size="35" name="customer_email" id="customer_email" value="<?=isset($row) ? htmlspecialchars($row->customer_email, ENT_QUOTES) : "";?>" class="formdefault" /></span></td>
				</tr>	
				<tr>
					<td>Customer Phone</td>
					<td>:</td>
					<td><input type="text" size="20" maxlength="13" name="customer_phone" id="customer_phone" value="<?=isset($row) ? htmlspecialchars($row->customer_phone, ENT_QUOTES) : "";?>" class="formdefault" /></span></td>
				</tr>	
				<tr>
					<td>Customer Mobile Phone</td>
					<td>:</td>
					<td><input type="text" size="20" maxlength="13" name="customer_mobile" id="customer_mobile" value="<?=isset($row) ? htmlspecialchars($row->customer_mobile, ENT_QUOTES) : "";?>" class="formdefault" /></span></td>
				</tr>	
				<tr>
					<td>Sex</td>
					<td>:</td>
					<td>
						<input name="customer_sex" type="radio" value="M" <? if (( isset($row)) && ($row->customer_sex == 'M')) { ?>checked<?php } ?>> Male </option>
						<input name="customer_sex" type="radio" value="F" <? if (( isset($row)) && ($row->customer_sex == 'F')) { ?>checked<?php } ?>> Female</option>
					</td>
				</tr>	
				
				<tr>
					<td>Company</td>
					  <td>:</td>
					  <td>
						<select id="customer_company_group" name="customer_company_group">
							<option value="" selected='selected'>--Select Company--</option>
							<?php 
								$cccompany = count($rccompany);
									for($i=0;$i<$cccompany;$i++){
										if (isset($row)&&($row->customer_company_group == $rccompany[$i]->customer_company_id)){
											$selected = "selected"; 
											}else{
												$selected = "";
											}
											echo "<option value='" . $rccompany[$i]->customer_company_id ."' " . $selected . ">" . $rccompany[$i]->customer_company_name . "</option>";
										}
							?>
						</select></td>
				</tr>				
    			
				<tr>
					<td>Alert Email</td>
					<td>:</td>
					<td>
						<input name="customer_alert_email" type="radio" value="1" <? if (( isset($row)) && ($row->customer_alert_email == '1')) { ?>checked<?php } ?>> Yes</option>
						<input name="customer_alert_email" type="radio" value="0" <? if (( isset($row)) && ($row->customer_alert_email == '0')) { ?>checked<?php } ?>> No </option>
					</td>
				</tr>
				<tr>
					<td>Alert SMS</td>
					<td>:</td>
					<td>
						<input name="customer_alert_sms" type="radio" value="1" <? if (( isset($row)) && ($row->customer_alert_sms == '1')) { ?>checked<?php } ?>> Yes</option>
						<input name="customer_alert_sms" type="radio" value="0" <? if (( isset($row)) && ($row->customer_alert_sms == '0')) { ?>checked<?php } ?>> No </option>
					</td>
				</tr>
				
				<tr>
					<td>Status Active</td>
					<td>:</td>
					<td>
						<input type="checkbox" name="customer_status" id="customer_status" value="1" checked /> Active 
					</td>
				</tr>
			
    			<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>
								<input type="submit" name="btnsave" id="btnsave" value=" Save " />
			
								<input type="button" name="btncancel" id="btncancel" value=" Cancel " onclick="location='<?=base_url()?>andalas_customer';" />
								<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
						</td>
					</tr>					
				</table>
			</form>		
		</div>
	</div>
</div>
			
