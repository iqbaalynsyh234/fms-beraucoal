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
			page(0);			
		}
	);
	
	function page(p)
	{		
		if(p==undefined){
			p=0;
		}
		jQuery("#offset").val(p);
		jQuery("#listresult").html("<?=$this->lang->line('lwait_loading_data');?>");
		jQuery.post("<?=base_url()?>transporter/route/search/"+p, jQuery("#frmsearch").serialize(), 
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
	
	function route_edit(v)
	{
		showdialog();
		jQuery.post('<?php echo base_url(); ?>/transporter/route/edit/', {id: v},
		function(r)
		{
			showdialog(r.html, "Route Edit");
		}
		, "json"
		);
	}
	
	function route_delete(v)
	{
		showdialog();
		jQuery.post('<?php echo base_url(); ?>/transporter/route/delete/', {id: v},
		function(r)
		{
			showdialog(r.html, "Delete Route !");
		}
		, "json"
		);
	}

		
</script>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
	<?=$navigation;?>
	<div id="main" style="margin: 20px;">
		<div class="block-border">
			<form class="block-content form" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
				<h1><?php echo "Route List";?> (<span id="total"></span>)</h1>
				<h2><?=$this->lang->line("lsearch"); ?></h2>		
				<table cellpadding="10" class="tablelist">
					<tr>
						<td><?php echo "Search By"; ?></td>
						<td>&nbsp;</td>
						<td>
							<select name="field" id="field">
								<option value="all">ALL</option>
								<option value="route_name">Route Name</option>
							</select>
						</td>
						<td>&nbsp;</td>
						<td><input type="text" name="keyword" id="keyword" value="" class="formdefault" /></td>
						<td>&nbsp;</td>
						<td><input type="submit" value="<?=$this->lang->line("lsearch");?>" /></td>
					</tr>				
				</table>
				<input type="hidden" id="sortby" name="sortby" value="" />
				<input type="hidden" id="orderby" name="orderby" value="" />
			</form>
			<br />
			<a href="<?=base_url();?>transporter/route/add"><font color="#0000ff">[ Add ]</font></a>	
		</div>
		<div id="listresult"></div>			
	</div>
</div>
