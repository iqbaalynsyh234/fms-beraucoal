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
		jQuery("#offset").val(p);
		jQuery("#result").html("<?=$this->lang->line("lwait_loading_data");?>");
		
		jQuery.post("<?=base_url();?>contactus/search/"+p, jQuery("#frmsearch").serialize(),
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
		jQuery("#dest").hide();
		jQuery("#status").hide();
		
		switch(v)
		{
			case "dest":
				jQuery("#dest").show();
				break;
			case "status":
				jQuery("#status").show();
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
		<h1><?=$this->lang->line("lcontact_us"); ?> (<span id="total"></span>)</h1>
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
							<option value="email"><?=$this->lang->line("lsender");?></option>
							<option value="dest"><?=$this->lang->line("lcategory");?></option>
							<option value="status"><?=$this->lang->line("lstatus");?></option>
						</select>
						<input type="text" name="keyword" id="keyword" value="" class="formdefault" />
						<select id="dest" name="dest" style="display: none;">
							<option value=""><?php echo $this->lang->line("lall"); ?></option>
							<?php for($i=0; $i < count($categories); $i++) { ?>
							<option value="<?php echo $categories[$i]->id; ?>"><?php echo $categories[$i]->title; ?></option>
							<?php } ?>
						</select>
						<select id="status" name="status" style="display: none;">
							<option value=""><?php echo $this->lang->line("lall"); ?></option>
							<option value="1"><?=$this->lang->line("lnew");?></option>
							<option value="2"><?=$this->lang->line("lreplied");?></option>
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
