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
		
		jQuery("#period1").datepicker(
				{
							dateFormat: 'dd-mm-yy'
						, 	startDate: '01/01/1900'
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
			
			jQuery("#period2").datepicker(
				{
							dateFormat: 'dd-mm-yy'
						, 	startDate: '01/01/1900'
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
			//page(0);	
		}
	);
	
	function field_onchange()
	{
		var v = jQuery("#field").val();

		jQuery("#driver").hide();
	
		switch(v)
		{
			
			case "driver":
				jQuery("#driver").show();
			break;
			default:
				jQuery("#driver").show();			
		}
	}
	
	function page(n)
	{		
		jQuery("#listresult").html("<?=$this->lang->line('lwait_loading_data');?>");
		jQuery.post("<?=base_url()?>transporter/dosj/driver_searchoverspeed/"+n, jQuery("#frmsearch").serialize(), 
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
				<h1><?php echo "Driver Performance :";?> </h1>
				<h2><?=$this->lang->line("lsearch"); ?></h2>		
				<table cellpadding="10" class="tablelist">
					<tr>
						<td><?php echo "Search By"; ?></td>
						<td>&nbsp;</td>
						<td>
							<select name="field" id="field" onchange="javascript:field_onchange()">
								<option value="driver">Driver</option>
							</select>
							<select name="driver" id="driver">
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
					<tr><td>&nbsp;</td></tr>
					<tr>
						<td>Speed Limit</td>
						<td>&nbsp;</td>
						<td><input type="text" size="7" name="speed_limit" id="speed_limit" /></td>
					</tr>
					<tr><td>&nbsp;</td></tr>
					<tr>
						<td id="tdsdate">Date</td>
						<td>&nbsp;</td>
						<td><input size="10" maxlength="10" type="text" name="period1" id="period1"  class="date-pick" /></td>
					</tr>
					<tr><td>&nbsp;</td></tr>
					<tr><td  colspan="6"><input type="submit" value="<?=$this->lang->line("lsearch");?>" /></td></tr>
				</table>
				<br />
				<input type="hidden" id="sortby" name="sortby" value="" />
				<input type="hidden" id="orderby" name="orderby" value="" />
			</form>
			<br />
		</div>
		<div id="listresult"></div>			
	</div>
</div>
