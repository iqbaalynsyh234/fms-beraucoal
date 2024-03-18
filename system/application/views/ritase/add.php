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
jQuery(document).ready(
		function()
		{
			showclock();
		}
	);
	
	function frmadd_onsubmit()
	{
		jQuery("#loader").show();
		jQuery.post("<?=base_url()?>transporter/ritase/save", jQuery("#frmadd").serialize(),	
			function(r)
			{
				jQuery("#loader").hide();
				if (r.error)
				{
					alert(r.message);
					return false;
				}
				
				alert(r.message);
				location = r.redirect;
			}
			, "json"	
		);
		return false;
	}
</script>

<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
	<?=$navigation;?>
	<div id="main" style="margin: 20px;">
        <div class="block-border">
		<br />&nbsp;
		<form  class="block-content form" name="frmadd" id="frmadd" onsubmit="javascript: return frmadd_onsubmit()">
        <h1>Add Ritase</h1>
		<h6>Make sure you made the first Geofence</h6>
		<br /><br />
			<table width="100%" cellpadding="3" class="table sortable no-margin">
				<tr>
					<input type="hidden" name="ritase_company" id="ritase_company"  value="<?php echo $this->sess->user_company;?>" /></td>
				</tr>
				<tr>
					<td width="15%">Set Ritase System For :</td>
					<td>
						<select id="ritase_name" name="ritase_name" >
						<?php
							if (isset($data) && (count($data) > 0))
							{
								for ($i=0;$i<count($data);$i++)
								{ 
									if ($data[$i]->geofence_name != $data[$i+1]->geofence_name) 
									{
						?>	
									<option value="<?php echo $data[$i]->geofence_name;?>" ><?php echo $data[$i]->geofence_name;?></option>
									
						<?php 		} 
								} 
							} 
						?>
						</select>
					</td>
				</tr>
				
				<tr>
					<td>&nbsp;</td>
						<td>
								<input type="submit" name="btnsave" id="btnsave" value=" Save " />
								<input type="button" name="btncancel" id="btncancel" value=" Cancel " onclick="location='<?=base_url()?>transporter/ritase';" />
								<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
						</td>
				</tr>
			</table>
		</form>
	</div>
</div>