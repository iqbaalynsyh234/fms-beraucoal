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
			jQuery("#date").datepicker(
				{
							dateFormat: 'yy/mm/dd'
						, 	startDate: '1900/01/01'
						, 	showOn: 'button'
						, 	changeYear: true
						,	changeMonth: true
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
	jQuery.post("<?=base_url()?>geofence_label/save", jQuery("#frmadd").serialize(),
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
<form  id="frmadd" onsubmit="javascript: return frmadd_onsubmit()">		
<input type="hidden" name="id" id="id" value="<?=isset($row) ? $row->geofence_id : 0;?>"/>
<input type="hidden" name="oldname" id="oldname" value="<?=isset($row) ? $row->geofence_name : 0;?>"/>
<table width="100%" cellpadding="3" class="table sortable no-margin">
	<tr>
		<td colspan="3"><h2> <?php echo $row->geofence_name;?></h2></td>
	</tr>
	
	<tr>
		<td colspan="3"><h2>Geofence Info</h2></td>
	</tr>
	<tr>
		<td>Geofence Name</td>
		<td>:</td>
		<td><input type="text" name="name" id="name" size="85" value="<?=isset($row) ? htmlspecialchars($row->geofence_name, ENT_QUOTES) : "";?>" class="formdefault" /></td>
	</tr>

	<tr>
		<td>Geofence Type</td>
		<td>:</td>
		<td>
		<select name="type" id="type">
			<option value="0">--Select Type--</option>
				<?php foreach($this->config->item("geofencetype") as $key=>$val) { ?>
					<option value="<?php echo $key; ?>"<?php echo (isset($row) && ($key==$row->geofence_type)) ? " selected" : "";?>><?php echo $val; ?></option>
				<?php } ?>						
		</select>
		</td>
	</tr>
	<tr>
		<td colspan="3">
			<input type="submit" value="Save">
			<input type="button" value=" Close " name="close" id="close" onclick="javascript:jQuery('#dialog').dialog('close');" /> 
			<img id="loader2" src="<?=base_url();?>assets/images/anim_wait.gif" border="0" alt="loading" title="loading" style="display:none;">
		</td>
	</tr>
	
</table>
</form>
</div>