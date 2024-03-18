<script>
	jQuery(document).ready(
		function()
		{
			showclock();
			jQuery("#sortby").val('<?=$sortby?>');
			jQuery("#orderby").val('<?=$orderby?>')
			
			page(0);			
		}
	);
	
	function page(n)
	{		
		jQuery("#listresult").html("<?=$this->lang->line('lwait_loading_data');?>");
		jQuery.post("<?=base_url()?>transporter/customer_powerblock/search/"+n, jQuery("#frmsearch").serialize(), 
			function(r)
			{
				jQuery("#listresult").html(r.html);
				jQuery("#total").html(r.total);				
			}
			, "json"
		);
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
		<div class="block-border">
			<form class="block-content form" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
			<h1><?php echo "Customer List :";?> (<span id="total"></span>)</h1>
			<h2><?=$this->lang->line("lsearch"); ?></h2>		
				<table width="100%" cellpadding="3" class="tablelist">
					<tr>
						<td width="10%"><?php echo "Customer"; ?></td>
						<td><input type="text" name="keyword" id="keyword" value="" class="formdefault" /></td>
					</tr>				
					<tr>
						<td style="border: 0px;">&nbsp;</td>
						<td style="border: 0px;"><input type="submit" value="<?=$this->lang->line("lsearch");?>" /></td>
					</tr>				
				</table>
				<input type="hidden" id="sortby" name="sortby" value="" />
				<input type="hidden" id="orderby" name="orderby" value="" />
			</form>
		<a href="<?=base_url();?>transporter/customer_powerblock/add"><font color="#0000ff">[ Add ]</font></a>	
		</div>
		<div id="listresult"></div>			
	</div>
</div>
