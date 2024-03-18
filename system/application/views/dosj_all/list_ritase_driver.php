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
			jQuery("#sdate").datepicker(
				{
							dateFormat: 'dd-mm-yy'
						, 	showOn: 'button'
						, 	buttonImage: '<?=base_url()?>assets/images/calendar.gif'
						, 	buttonImageOnly: true
						,	beforeShow: 
								function() 
								{	
									jQuery('#ui-datepicker-div').maxZIndex();
								}
				}
				);	
			
			jQuery("#edate").datepicker(
				{
							dateFormat: 'dd-mm-yy'
						, 	showOn: 'button'
						, 	buttonImage: '<?=base_url()?>assets/images/calendar.gif'
						, 	buttonImageOnly: true
						,	beforeShow: 
								function() 
								{	
									jQuery('#ui-datepicker-div').maxZIndex();
								}
				}
				);	
				
			showclock();
			field_onchange();
			jQuery("#sortby").val('<?=$sortby?>');
			jQuery("#orderby").val('<?=$orderby?>')
			page(0);	
		}
	);
	
	function field_onchange()
	{
		var v = jQuery("#field").val();

		jQuery("#keyword").hide();
		jQuery("#driver").hide();
		
		switch(v)
		{
			
			case "driver":
				jQuery("#driver").show();
			break;
			default:
				jQuery("#keyword").show();			
		}
	}
	
	function page(n)
	{		
		jQuery("#listresult").html("<?=$this->lang->line('lwait_loading_data');?>");
		jQuery.post("<?=base_url()?>transporter/dosj_all/search_ritase_driver/"+n, jQuery("#frmsearch").serialize(), 
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
				<h1><?php echo "Ritase Driver";?></h1>
				<h2><?=$this->lang->line("lsearch"); ?></h2>		
				<table cellpadding="10" class="tablelist">
					<tr>
						<td><?php echo "Search By"; ?></td>
						<td>&nbsp;</td>
						<td>
							<select name="field" id="field" onchange="javascript:field_onchange()">
								<option value="all">ALL</option>
								<option value="driver">Driver</option>
							</select>
						</td>
						<td>&nbsp;</td>
						<td><input type="text" name="keyword" id="keyword" value="" class="formdefault" /></td>
						<td>
							<select name="driver" id="driver" style="display:none" >
								<?php
									if (isset($driver) && (count($driver)>0))
									{
										for ($j=0;$j<count($driver);$j++)
										{
								?>
										<option value="<?php echo $driver[$j]->driver_id;?>">
											<?php echo $driver[$j]->driver_name;?>
										</option>
								<?php
										}
									}
								?>
							</select>
						</td>
					</tr>	
				</table>
				<br />
				<table>
					<tr>
					<td id="tdsdate" colspan="2">Start <input size="10" maxlength="10" type="text" name="sdate" id="sdate" class="date-pick" /></td>
					<td>&nbsp;</td>
					<td id="tdedate">End <input size="10" maxlength="10" type="text" name="edate" id="edate" class="date-pick" /></td>
					<td>&nbsp;</td>
					<td><input type="submit" value="<?=$this->lang->line("lsearch");?>" /></td>
					</tr>
				</table>
				<input type="hidden" id="sortby" name="sortby" value="" />
				<input type="hidden" id="orderby" name="orderby" value="" />
			</form>
		</div>
		<div id="listresult"></div>			
	</div>
</div>
