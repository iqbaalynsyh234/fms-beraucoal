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
		}
	);
	
	function frmedit_onsubmit()
	{
		jQuery("#loader").show();
		jQuery.post("<?=base_url()?>transporter/dosj_all/saveedit_destination", jQuery("#frmedit").serialize(),
			function(r)
			{
				jQuery("#loaderupdate").hide();
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
<div id="main_data">
		<form id="frmedit" onsubmit="javascript: return frmedit_onsubmit()">		
		<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
				<input type="hidden" name="destination_id" id="destination_id" value="<?php echo $data->destination_id; ?>" />
				<tr>
					<td style="text-align:left" colspan="2"><h2>EDIT DESTINATION</h2></td>
				</tr>
				<tr>
					<td style="text-align:right">Destination</td>
					<td>
						<input type='text' name="destination_name" id="destination_name" size="30" value="<?=isset($data) ? htmlspecialchars($data->destination_name, ENT_QUOTES) : "";?>"></td>
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>
						<input type="submit" name="submit" id="submit" value="Save" />
						<input type="button" name="btn_cancel" id="btn_cancel" value="Cancel" onclick="javascript:jQuery('#dialog').dialog('close');" />
						<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
					</td>
				</tr>
		</table>
		</form>
</div>