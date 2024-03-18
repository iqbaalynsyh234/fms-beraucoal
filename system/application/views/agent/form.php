<script>
	jQuery(document).ready(
		function()
		{
			showclock();
		}
	);
	
	function frmadd_onsubmit(frm)
	{
		jQuery.post("<?=base_url()?>agent/save", jQuery("#frmadd").serialize(),
			function(r)
			{
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
		<?php if (isset($row)) { ?>
		<h1><?=$this->lang->line("lagent_edit"); ?></h1>
		<?php } else { ?>
		<h1><?=$this->lang->line("lagent_add"); ?></h1>
		<?php } ?>
			<form id="frmadd" onsubmit="javascript: return frmadd_onsubmit(this)">				
				<table width="100%" cellpadding="3" class="tablelist">
					<?php if (isset($row)) { ?>					
					<input type="hidden" id="id" name="id" value="<?=$row->agent_id;?>" />
					<tr style="border: 0px;">
						<td style="border: 0px;">ID</td>
						<td style="border: 0px;">:</td>
						<td style="border: 0px;"><?=$row->agent_id;?></td>
					</tr>
					<?php } ?>
    			<tr style="border: 0px;">
						<td width="100" style="border: 0px;">Name</td>
						<td width="1" style="border: 0px;">:</td>
						<td style="border: 0px;"><input type="text" name="agent" id="agent" value="<?=isset($row) ? htmlspecialchars($row->agent_name, ENT_QUOTES) : "";?>" class="formdefault" /></td>
					</tr>
					<tr style="border: 0px;">
						<td style="border: 0px;" colspan="2">&nbsp;</td>
						<td style="border: 0px;"><input type="checkbox" name="canedit_vactive" id="canedit_vactive" value="1"<?=(isset($row) && ($row->agent_canedit_vactive == 1)) ? " checked": ""; ?> />&nbsp;<?php echo $this->lang->line("lcan_edit_vactivate"); ?></td>
					</tr>
					<tr style="border: 0px;">
						<td style="border: 0px;" colspan="2">&nbsp;</td>
						<td style="border: 0px;"><input type="checkbox" name="agent_alert_pulsa" id="agent_alert_pulsa" value="1"<?=(isset($row) && ($row->agent_alert_pulsa == 1)) ? " checked": ""; ?> />&nbsp;<?php echo $this->lang->line("lsend_alert_pulsa"); ?></td>
					</tr>
    			<tr style="border: 0px;">
						<td width="100" style="border: 0px;"><?php echo $this->lang->line("lsite"); ?></td>
						<td width="1" style="border: 0px;">:</td>
						<td style="border: 0px;">
							<select name="site" id="site">
								<option value="">-</option>
								<?php
									$sites = $this->config->item("sites");
									if (is_array($sites) && count($sites))
									{
										foreach($sites as $site)
										{
								?>
										<option value="<?php echo $site; ?>"<?php if (isset($row) && ($row->agent_site == $site)) { echo " selected"; } ?>><?php echo $site; ?></option>
								<?php
										}
									}
								?>
							</select>
						</td>
					</tr>
					<tr style="border: 0px;">
							<td style="border: 0px;"><?=$this->lang->line("lpayment_period");?></td>
							<td style="border: 0px;">:</td>
							<td style="border: 0px;"><input type="text" name="agent_payment_periode" id="agent_payment_periode" value="<?php if (isset($row) && $row->agent_payment_periode) { echo $row->agent_payment_periode; } ?>" class="formshort" style="text-align: right;" /> <?= strtolower($this->lang->line("lmonthlabel"));?></td>
					</tr>
					<tr style="border: 0px;">
							<td style="border: 0px;"><?=$this->lang->line("lpayment_total");?></td>
							<td style="border: 0px;">:</td>
							<td style="border: 0px;">Rp. <input type="text" name="agent_payment_amount" id="agent_payment_amount" value="<?php if (isset($row) && $row->agent_payment_amount) { echo number_format($row->agent_payment_amount, 0, "", ","); } ?>" class="formshort" style="text-align: right;" /></td>
					</tr>						
    			<tr style="border: 0px;">
						<td style="border: 0px;">&nbsp;</td>
						<td style="border: 0px;">&nbsp;</td>
						<td style="border: 0px;">
								<input type="submit" name="btnsave" id="btnsave" value=" Save " />
								<input type="button" name="btncancel" id="btncancel" value=" Cancel " onclick="location='<?=base_url()?>agent';" />
						</td>
					</tr>					
				</table>
			</form>		
	</div>
</div>
			
