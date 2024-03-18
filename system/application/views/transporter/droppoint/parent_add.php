<script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery.maskx.js"></script>
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
	jQuery.post("<?=base_url()?>droppoint/save_dataparent", jQuery("#frmadd").serialize(),
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
		<td colspan="3"><h2>Add Group Droppoint</h2></td>
	</tr>
	<tr>
		<td colspan="2">Name</td>
		<td>:</td>
		<td><input type="text" name="name" id="name" value="" />
	</tr>
	<tr>
		<td colspan="2">Code</td>
		<td>:</td>
		<td><input type="text" name="code" id="code" value="" />
	</tr>
	<tr>
		<td colspan="2">Area</td>
		<td>:</td>
		<td>
			<select id="company" name="company">
				<option value="0" selected='selected'>--Select Area--</option>
				<?php 
					$ccompany = count($rcompany);
					for($i=0;$i<$ccompany;$i++){
						if (isset($row)&&($row->parent_company == $rcompany[$i]->company_id)){
							$selected = "selected"; 
							}else{
								$selected = "";
							}
							echo "<option value='" . $rcompany[$i]->company_id ."' " . $selected . ">" . $rcompany[$i]->company_name . "</option>";
						}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="3">
			<input type="submit" value="Save " name="submit" id="submit"/>
			<input type="button" value=" Close " name="close" id="close" onclick="javascript:jQuery('#dialog').dialog('close');" /> 
			<img id="loader2" src="<?=base_url();?>assets/images/anim_wait.gif" border="0" alt="loading" title="loading" style="display:none;">
		</td>
	</tr>
	
</table>
</form>
</div>