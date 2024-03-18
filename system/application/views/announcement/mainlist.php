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
		
		jQuery.post("<?=base_url();?>announcement/search/"+p, jQuery("#frmsearch").serialize(),
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
		<h1><?=$this->lang->line("lannouncement"); ?> (<span id="total"></span>)</h1>
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
							<option value="announcement_message"><?=$this->lang->line("lmessage");?></option>
						</select>
						<input type="text" name="keyword" id="keyword" value="" class="formdefault" />
					</td>
				</tr>
				<tr>
					<td style="border: 0px;">&nbsp;</td>
					<td style="border: 0px;"><input type="submit" value="<?=$this->lang->line("lsearch");?>" /></td>
				</tr>
			</table>
		</form>
		<?php if ($canedit) { ?>
		<a href="<?=base_url();?>announcement/add"><font color="#0000ff">[ Add ]</font></a>
		<?php } ?>
		<div id="result"></div>		
	</div>
</div>
