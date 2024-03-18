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
						<?php if (($this->sess->user_type == 1) || (($this->sess->user_type == 3) && ($this->sess->agent_canedit_vactive == 1))) { ?>
						jQuery("#vehicle_active_date1").datepicker(
							{
										dateFormat: 'dd/mm/yy'
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
						
						jQuery("#vehicle_active_date2").datepicker(
							{
										dateFormat: 'dd/mm/yy'
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
						<?php } ?>
						
						jQuery("#vehicle_active_date").datepicker(
							{
										dateFormat: 'dd/mm/yy'
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
						
						//vehicle_image_onchange();
						//loadgroup();	
					}

				);
		
        function loadgroup()
        {
                jQuery.post("<?php echo base_url(); ?>group/options<?php if (isset($vehicle)) { echo "/".$vehicle->vehicle_group; } ?>", jQuery("#frmaddvehicle").serialize(),
                        function(r)
                        {
                                if (r.empty)
                                {
                                        jQuery("#trgroup").hide();
 					jQuery("#trowner").show();
	                                 return;
                                }

                                jQuery("#trgroup").show();
                                jQuery("#usergroup").html(r.html);
				jQuery("#trowner").hide();
                        }
                        , "json"
                );
        }		
				function frmaddvehicle_onsubmit(frm)
				{
					jQuery.post("<?=base_url();?>user/savevehicle", jQuery("#frmaddvehicle").serialize(),
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
				
				function vehicle_image_onchange()
				{
					jQuery.post("<?=base_url();?>vehicle/getimage", {vimage: jQuery("#vehicle_image").val()},
						function(r)
						{
							if (r.error)
							{
								alert(r);
								return;
							}
							
							jQuery("#dvvehicle_image").html(r.html);
						}
						, "json"
					);
					
				}
				
			</script>
            <div class="block-border">
			<form id="frmaddvehicle" onsubmit="javascript: return frmaddvehicle_onsubmit(this)">			
				<input type="hidden" name="vehicle_id" id="vehicle_id" value="<?php if (isset($vehicle)) { echo $vehicle->vehicle_id; } ?>" />
				<table width="100%" cellpadding="3" class="tablelist">
    			<tr>
						<td><?=$this->lang->line("lvehicle_no");?></td>
						<td>:</td>
						<td><input type="text" name="vehicle_no" id="vehicle_no" value="<?php if (isset($vehicle)) { echo $vehicle->vehicle_no; } ?>" class="formdefault" /></td>
					</tr>
    			<tr>
						<td><?=$this->lang->line("lvehicle_name");?></td>
						<td>:</td>
						<td><input type="text" name="vehicle_name" id="vehicle_name" value="<?php if (isset($vehicle)) { echo $vehicle->vehicle_name; } ?>" class="formdefault" /></td>
					</tr>					
    			<tr>
						<td><?=$this->lang->line("lmax_speed");?></td>
						<td>:</td>
						<td><input type='text' name="vehicle_maxspeed" id="vehicle_maxspeed"  class="formshort" value="<?php if (isset($vehicle)) { echo $vehicle->vehicle_maxspeed; } ?>"  maxlength='4'> <?php echo $this->lang->line('lkph'); ?></td>
					</tr>
    			<tr>
						<td><?=$this->lang->line("lmax_parking_time");?></td>
						<td>:</td>
						<td><input type='text' name="vehicle_maxparking" id="vehicle_maxparking"  class="formshort" value="<?php if (isset($vehicle)) { echo $vehicle->vehicle_maxparking; } ?>"  maxlength='4'> <?php echo $this->lang->line('lminute'); ?></td>
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
            </div>
