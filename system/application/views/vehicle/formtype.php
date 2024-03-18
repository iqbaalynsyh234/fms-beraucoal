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
					}

				);
		
				function frmaddvehicle_onsubmit(frm)
				{
					jQuery.post("<?=base_url();?>vehicle/savetype", jQuery("#frmaddvehicle").serialize(),
						function(r)
						{
							if (r.error)
							{
								alert(r.message);
								return;
							}
														
							alert(r.message);
							jQuery("#dialog").dialog('close');
							page(0);
						}
						, "json"
					);
					return false;
				}
				
			</script>
			<form id="frmaddvehicle" onsubmit="javascript: return frmaddvehicle_onsubmit(this)">			
				<input type="hidden" name="vehicle_id" id="vehicle_id" value="<?php if (isset($vehicle)) { echo $vehicle->vehicle_id; } ?>" />
				<table width="100%" cellpadding="3" class="tablelist">
    			<tr>
						<td width="160"><?=$this->lang->line("lvehicle_device");?></td>
						<td width="1">:</td>
						<td><?php if (isset($vehicle)) { echo $vehicle->vehicle_device; } ?></td>
					</tr>
    			<tr>
						<td><?=$this->lang->line("lvehicle_no");?></td>
						<td>:</td>
						<td><?php if (isset($vehicle)) { echo $vehicle->vehicle_no; } ?></td>
					</tr>
    			<tr>
						<td><?=$this->lang->line("lvehicle_name");?></td>
						<td>:</td>
						<td><?php if (isset($vehicle)) { echo $vehicle->vehicle_name; } ?></td>
					</tr>					
    			<tr>
						<td><?=$this->lang->line("lvehicle_type");?></td>
						<td>:</td>
						<td>
							<select name='vehicle_type' id='vehicle_type'>
								<?php
									$vehicle_type_admin = $this->config->item("vehicle_type_admin");
									$vehicle_type_replace = $this->config->item("vehicle_type_replace");
								 
									foreach($this->config->item("vehicle_type") as $key=>$val) { ?>
									<?php 
										if ($this->sess->user_type != 1) 
										{ 
											if (is_array($vehicle_type_admin) && in_array($key, $vehicle_type_admin))
											{
												continue;
											}
										} 
										
										if (! in_array($key, $this->config->item('vehicle_type_visible'))) continue;
									?>
								<option value="<?php echo isset($vehicle_type_replace[$key]) ? $vehicle_type_replace[$key] : $key; ?>"<?php if (isset($vehicle) && (strtoupper($vehicle->vehicle_type) == strtoupper($key))) { echo " selected"; } ?>><?php echo $key; ?></option>
								<?php } ?>								
							</select>
						</td>
				</tr>					
    			<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>
								<input type="submit" name="btnsave" id="btnsave" value=" Save " />
						</td>
					</tr>					
				</table>
			</form>
