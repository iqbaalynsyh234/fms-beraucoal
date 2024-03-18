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
			field_onchange()
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
		jQuery.post("<?=base_url()?>transporter/timesheet/search/"+p, jQuery("#frmsearch").serialize(), 
			function(r)
			{
				jQuery("#listresult").html(r.html);
				jQuery("#total").html(r.total);				
			}
			, "json"
		);
	}
	
	function field_onchange()
	{
		var v = jQuery("#field").val();

		jQuery("#keyword").hide();
		jQuery("#timesheet_route").hide();
		jQuery("#vehicle").hide();
		
		switch(v)
		{
			case "timesheet_route":
				jQuery("#timesheet_route").show();
			break;
			case "vehicle":
				jQuery("#vehicle").show();
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
	
	function timesheet_edit(v)
	{
		showdialog();
		jQuery.post('<?php echo base_url(); ?>/transporter/timesheet/edit/', {id: v},
		function(r)
		{
			showdialog(r.html, "Edit Timesheet");
		}
		, "json"
		);
	}
	
	function timesheet_delete(v)
	{
		showdialog();
		jQuery.post('<?php echo base_url(); ?>/transporter/timesheet/delete/', {id: v},
		function(r)
		{
			showdialog(r.html, "Delete Timesheet !");
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
				<input type="hidden" name="offset" id="offset" value="" />
				<input type="hidden" id="sortby" name="sortby" value="" />
				<input type="hidden" id="orderby" name="orderby" value="" />		
				<h1><?php echo "Timesheet List";?> (<span id="total"></span>)</h1>
				<h2><?=$this->lang->line("lsearch"); ?></h2>		
				<table cellpadding="10" class="tablelist">
					<tr>
						<td><?php echo "Search By"; ?></td>
						<td>&nbsp;</td>
						<td>
							<select name="field" id="field" onchange="javascript: return field_onchange();">
								<option value="all">ALL</option>
								<option value="timesheet_name">Timesheet</option>
								<option value="timesheet_route">Route</option>
								<option value="vehicle">Vehicle</option>
							</select>
						</td>
						<td>&nbsp;</td>
						<td>
							<input type="text" name="keyword" id="keyword" value="" class="formdefault" />
							<select name='timesheet_route' id='timesheet_route' style="display: none;">
								<?php 
									if (isset($route) && count($route)>0)
									{
										for($i=0;$i<count($route);$i++)
										{
								?>
									<option value="<?php echo $route[$i]->route_id;?>">
										<?php echo $route[$i]->route_name;?>
									</option>
								<?
										}
									}
								?>
							</select>
							<select name='vehicle' id='vehicle' style="display: none;">
								<?php 
									if (isset($vehicle) && count($vehicle)>0)
									{
										for($i=0;$i<count($vehicle);$i++)
										{
								?>
									<option value="<?php echo $vehicle[$i]->vehicle_device;?>">
										<?php echo $vehicle[$i]->vehicle_name." ".$vehicle[$i]->vehicle_no;?>
									</option>
								<?
										}
									}
								?>
							</select>
						</td>
						<td>&nbsp;</td>
						<td><input type="submit" value="<?=$this->lang->line("lsearch");?>" /></td>
					</tr>				
				</table>
				<input type="hidden" id="sortby" name="sortby" value="" />
				<input type="hidden" id="orderby" name="orderby" value="" />
			</form>
			<br />
			<a href="<?=base_url();?>transporter/timesheet/add"><font color="#0000ff">[ Add ]</font></a>	
		</div>
		<div id="listresult"></div>			
	</div>
</div>
