
<script>
	jQuery.maxZIndex = jQuery.fn.maxZIndex = function(opt) {
	    /// <summary>
	    /// Returns the max zOrder in the document (no parameter)
	    /// Sets max zOrder by passing a non-zero number
	    /// which gets added to the highest zOrder.
	    /// </summary>    
	    /// <param name="opt" type="object">
	    /// inc: increment value, 
	    /// group: selector for zIndex elements to find max for
	    /// </param>
	    /// <returns type="jQuery" />
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
				
				
				
				function frmedit_onsubmit()
				{
					jQuery("#loader").show();
					jQuery.post("<?=base_url()?>destination/update_dest", jQuery("#frmedit").serialize(),	
						function(r)
						{
							jQuery("#loader").hide();
							if (r.error)
							{
								alert(r.message);
								return false;
							}
							
							alert(r.message);
							//location = r.redirect;
							jQuery("#dialog").dialog("close");
							
						}
						, "json"	
					);
					return false;
				}
				
				
			</script>

            <div class="block-border">
			<form id="frmedit" onsubmit="javascript: return frmedit_onsubmit()">			
				<table width="100%" cellpadding="3" class="tablelist">
						
					<td>No CO</td>
					<td>:</td>
					<td>
						<input name="destination_name1" type="text" value="<?=isset($row) ? htmlspecialchars($row->destination_name1, ENT_QUOTES) : "";?>" size="50" tabindex="1" />
						<input type="hidden" name="destination_id" id="destination_id"  value="<?php echo $row->destination_id?>" />
					</td>
					
					
					<tr>
						<td colspan="3">
							<input id="saveForm" name="submit" class="btTxt submit" type="submit" value="Update" />
							<!--<input id="btncancel" name="btncancel" class="btTxt submit" type="button" value="Cancel" onclick="location='<?=base_url()?>trackers';" />-->
							<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
						</td>
					</tr>
					
				</table>
			</form>
            </div>
