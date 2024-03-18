<script>
	jQuery.maxZIndex = jQuery.fn.maxZIndex = function(opt) {
	    var def = { inc: 10, group: "*" };
	    jQuery.extend(def, opt);
	    var zmax = 0;
	    jQuery(def.group).each(function() {
	        var cur = parseInt(jQuery(this).css('z-index'));
	        zmax = cur > zmax ? cur : zmax;
	    });
	    if (!this.jquery)
	        return zmax;
	
	    return this.each(function() {
	        zmax += def.inc;
	        jQuery(this).css("z-index", zmax);
	    });
	}
	
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
	
	function field_onchange()
	{
		var v = jQuery("#field").val();

		jQuery("#keyword").hide();
		jQuery("#cost_destination").hide();		

		switch(v)
		{
			
			case "cost_destination":
				jQuery("#cost_destination").show();
			break;			
			default:
				jQuery("#keyword").show();			
		}
	}
	
	function page(p)
	{		
		if(p==undefined){
			p=0;
		}
		jQuery("#offset").val(p);
		jQuery("#listresult").html("<?=$this->lang->line('lwait_loading_data');?>");
		jQuery.post("<?=base_url()?>transporter/dosj/search_cost/"+p, jQuery("#frmsearch").serialize(), 
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
	
	function cost_edit(v)
	{
		showdialog();
		jQuery.post('<?php echo base_url(); ?>/transporter/dosj/cost_edit/', {id: v},
		function(r)
		{
			showdialog(r.html, "Delivery Order Edit");
		}
		, "json"
		);
	}
	
	function cost_delete_data(v)
	{
		if (confirm("Are you sure delete this data.?")) {
				jQuery.post('<?=base_url()?>transporter/dosj/cost_delete/', {id: v}, 
				function(r){
					if (r.error) {
						alert(r.message);
						return;
					}else{
						alert(r.message);
						page();
						return;
					}
				}, "json");
			}
	}
	
	
	
	
</script>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
	<?=$navigation;?>
	<div id="main" style="margin: 20px;">
		<div class="block-border">
			<form class="block-content form" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
				<h1><?php echo "Cost List :";?> (<span id="total"></span>)</h1>
				<h2><?=$this->lang->line("lsearch"); ?></h2>		
				<table cellpadding="10" class="tablelist">
					<tr>
						<td><?php echo "Search By"; ?></td>
						<td>&nbsp;</td>
						<td>
							<select name="field" id="field" onchange="javascript:field_onchange()">
								<option value="all">ALL</option>
								<option value="cost_destination">Destination</option>
							</select>
						</td>
						<td>&nbsp;</td>
						<td>
						<select name="cost_destination" id="cost_destination" style="display:none">
							<?php 
								if (isset($destination))
								{
									for ($i=0;$i<count($destination);$i++)
									{
							?>
									<option value="<?php echo $destination[$i]->destination_id;?>">
									<?php echo $destination[$i]->destination_name;?>
									</option>
							<?php
									}
								}
							?>
						</select>
						</td>
						<td><input type="text" name="keyword" id="keyword" value="" class="formdefault" /></td>
						<td><input type="submit" value="<?=$this->lang->line("lsearch");?>" /></td>
					</tr>				
				</table>
				<input type="hidden" id="sortby" name="sortby" value="" />
				<input type="hidden" id="orderby" name="orderby" value="" />
				<input type="hidden" id="offset" name="offset" value="" />
			</form>
			<br />
			<a href="<?=base_url();?>transporter/dosj/cost_add"><font color="#0000ff">[ Add ]</font></a>	
			<a href="<?=base_url();?>transporter/dosj/cost_add_destination"><font color="#0000ff">[ Add Destination ]</font></a>	
		</div>
		<div id="listresult"></div>			
	</div>
</div>
