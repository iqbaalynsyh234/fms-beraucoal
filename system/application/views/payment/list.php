<script>
	jQuery(document).ready(
		function()
		{
			showclock();
			
			jQuery("#sortby").val('<?=$sortby?>');
			jQuery("#orderby").val('<?=$orderby?>')
			
			field_onchange();
			page(0);			
		}
	);
	
	function page(p)
	{
		if (! p) p = 0;
		
		jQuery("#offset").val(p);
		jQuery("#result").html("<?=$this->lang->line("lwait_loading_data");?>");
		
		jQuery.post("<?=base_url();?>payment/search/"+p, jQuery("#frmsearch").serialize(),
			function(r)
			{
				jQuery("#result").html(r.html);		
				jQuery("#total").html(r.total);
			}
			, "json"
		);
	}
	
	function field_onchange()
	{
		var v = jQuery("#field").val();

		jQuery("#keyword").hide();
		jQuery("#transfermethod").hide();
		jQuery("#bankdest").hide();
		
		switch(v)
		{
			case "transfermethod":
				jQuery("#transfermethod").show();
				break;
			case "bankdest":
				jQuery("#bankdest").show();
				break;
			default:
				jQuery("#keyword").show();			
		}
	}
	
	function frmsearch_onsubmit()
	{
		page(0);
		return false;
	}

	function order(by)
	{						
		if (by == jQuery("#sortby").val())
		{
			if (jQuery("#orderby").val() == "asc")
			{
				jQuery("#orderby").val("desc");
			}
			else
			{
				jQuery("#orderby").val("asc");
			}
		}
		else
		{
			jQuery("#orderby").val('asc')
		}
		
		jQuery("#sortby").val(by);
		page(0);
	}		

</script>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
<?=$navigation;?>
	<div id="main" style="margin: 20px;">
		<br />&nbsp;
		<h1><?=$this->lang->line("lpayment_confirmation"); ?> (<span id="total"></span>)</h1>
		<h2><?=$this->lang->line("lsearch"); ?></h2>
		<form name="frmsearch" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
			<input type="hidden" name="offset" id="offset" value="" />
			<input type="hidden" id="sortby" name="sortby" value="" />
			<input type="hidden" id="orderby" name="orderby" value="" />			
			<table width="100%" cellpadding="3" class="tablelist">
				<tr>
					<td width="10%"><?=$this->lang->line("lsearchby");?></td>
					<td>
						<select id="field" name="field" onchange="javascript:field_onchange()">
							<option value="name"><?=$this->lang->line("lname");?></option>
							<option value="vehicle"><?=$this->lang->line("lvehicle");?></option>
							<option value="transfermethod"><?=$this->lang->line("ltransfer_method");?></option>
							<option value="bankdest"><?=$this->lang->line("ldestination_account");?></option>
						</select>
						<input type="text" name="keyword" id="keyword" value="" class="formdefault" />
						<select id="transfermethod" name="transfermethod" style="display: none;">
							<option value="cash"><?=$this->lang->line("lcash");?></option>
							<option value="atm"><?=$this->lang->line("latm");?></option>
							<option value="internet"><?=$this->lang->line("linet_banking");?></option>
							<option value="sms"><?=$this->lang->line("lsms_banking");?></option>
						</select>
						<select id="bankdest" name="bankdest" style="display: none;">
								<?php for($i=0; $i < count($banks); $i++) { ?>
								<option value="<?php echo $banks[$i]->bank_id; ?>">No Rek.<?php echo $banks[$i]->bank_branch; ?> <?php echo $banks[$i]->bank_acc; ?> a/n <?php echo $banks[$i]->bank_name; ?></option>
								<?php } ?>
						</select>
					</td>
				</tr>
				<tr>
					<td style="border: 0px;">&nbsp;</td>
					<td style="border: 0px;"><input type="submit" value="<?=$this->lang->line("lsearch");?>" /></td>
				</tr>
			</table>
		</form>
		<div id="result"></div>		
	</div>
</div>
