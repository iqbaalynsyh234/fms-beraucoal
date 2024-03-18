<!--<script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery.maskx.js"></script>-->
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
			//showclock();
			//jQuery('#amount').maskx({maskx: 'money'});
			jQuery("#startdate2").datepicker(
				{
							dateFormat: 'yy-mm-dd'
						, 	startDate: '1900-01-01'
						, 	showOn: 'button'
						//, 	changeYear: true
						//,	changeMonth: true
						, 	buttonImage: '<?=base_url()?>assets/images/calendar.gif'
						, 	buttonImageOnly: true
						,	beforeShow: 
								function() 
								{	
									jQuery('#ui-datepicker-div').maxZIndex();
								}
				}
			);
			jQuery("#enddate2").datepicker(
				{
							dateFormat: 'yy-mm-dd'
						, 	startDate: '1900-01-01'
						, 	showOn: 'button'
						//, 	changeYear: true
						//,	changeMonth: true
						, 	buttonImage: '<?=base_url()?>assets/images/calendar.gif'
						, 	buttonImageOnly: true
						,	beforeShow: 
								function() 
								{	
									jQuery('#ui-datepicker-div').maxZIndex();
								}
				}
			);
			
		}
		
	);
	
function frmadd_onsubmit()
{
	jQuery("#loader2").show();
	jQuery.post("<?=base_url()?>droppoint/save_target", jQuery("#frmadd").serialize(),
	function(r)
	{
		jQuery("#loader2").hide();
		alert(r.message);
								
								if (r.error)
								{								
									return;									
								}								
								page();
								jQuery("#dialog").dialog("close");
							}
							, "json"
						);
						
						return false;
	
}


</script>
<div class="block-border">
<form id="frmadd" onsubmit="javascript: return frmadd_onsubmit()">		

<table width="100%" cellpadding="3" class="table sortable no-margin">
	<tr>
		<td colspan="3"><h2>Edit Target Time</h2></td>
		<input type="hidden" name="id" id="id" value="<?=isset($row) ? htmlspecialchars($row->target_id, ENT_QUOTES) : "";?>" size="50" />
	</tr>
	<tr>
		<td colspan="2">Type</td>
		<td>:</td>
		<td>
			<input name="type" type="radio" value="0" <? if (( isset($row)) && ($row->target_type == '0')) { ?>checked<?php } ?>> REGULAR </option>
			<input name="type" type="radio" value="1" <? if (( isset($row)) && ($row->target_type == '1')) { ?>checked<?php } ?>> COMBINE </option>
		</td>
	</tr>
	<tr>
		<td colspan="2">Start Date</td>
		<td>:</td>
		<td><input type='text' name="startdate" id="startdate2"  class="date-pick" value="<?=isset($row) ? htmlspecialchars(date("Y-m-d", strtotime($row->target_startdate)), ENT_QUOTES) : "";?>"  maxlength='10'>
	</tr>
	<tr>
		<td colspan="2">End Date</td>
		<td>:</td>
		<td><input type='text' name="enddate" id="enddate2"  class="date-pick" value="<?=isset($row) ? htmlspecialchars(date("Y-m-d", strtotime($row->target_enddate)), ENT_QUOTES) : "";?>"  maxlength='10'>
	</tr>
	<tr>
		<td colspan="2">Droppoint</td>
		<td>:</td>
		<td>
			<input type="text" name="droppoint" id="droppoint" value="<?=isset($row) ? htmlspecialchars(date("i", strtotime($row->target_time)), ENT_QUOTES) : "";?>" size="10" placeholder="ex: 30"/>	
		</td>
	</tr>
	<tr>
		<td colspan="2">Target (Hour)</td>
		<td>:</td>
		<td>
			<input type="text" name="hour" id="hour" value="<?=isset($row) ? htmlspecialchars(date("H", strtotime($row->target_time)), ENT_QUOTES) : "";?>" size="10" placeholder="ex: 07"/>
		</td>
	</tr>
	<tr>
		<td colspan="2">Target (Minute)</td>
		<td>:</td>
		<td>
			<input type="text" name="minute" id="minute" value="<?=isset($row) ? htmlspecialchars(date("i", strtotime($row->target_time)), ENT_QUOTES) : "";?>" size="10" placeholder="ex: 30"/>	
		</td>
	</tr>
	<tr>
		<td colspan="5">
			<input type="submit" value="Save " name="submit" id="submit"/>
			<input type="button" value=" Close " name="close" id="close" onclick="javascript:jQuery('#dialog').dialog('close');" /> 
			<img id="loader2" src="<?=base_url();?>assets/images/anim_wait.gif" border="0" alt="loading" title="loading" style="display:none;">
		</td>
	</tr>
	
</table>
</form>
</div>