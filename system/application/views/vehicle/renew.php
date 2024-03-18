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
						jQuery("#expire_date1").datepicker(
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
						
						jQuery("#expire_date2").datepicker(
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
					}
				);
				
				function frmvehicle_onsubmit(frm)
				{										
					var dev = jQuery("#device").val();
					var exp1 = jQuery("#expire_date1").val();
					var exp2 = jQuery("#expire_date2").val();
					
					jQuery.post("<?php echo base_url(); ?>vehicle/activate", {dev: dev, exp1: exp1, exp2: exp2},
						function(r)
						{
							if (r.error)
							{
								alert(r.message);
								return;
							}
							
							alert(r.message);
							document.frmsearch.submit();
						}
						, "json"
					);
					
					return false;
				}
				
			</script>
			<form id="frmvehicle" onsubmit="javascript: return frmvehicle_onsubmit(this)">			
				<table width="100%" cellpadding="3" class="tablelist">
    			<tr>
						<td width="160"><?=$this->lang->line("lvehicle_device");?></td>
						<td width="1">:</td>
						<td><?php echo $vehicle->vehicle_device; ?><input type="hidden" name="device" id="device" value="<?php echo $vehicle->vehicle_device; ?>" /></td>
					</tr>
				<?php if (($this->sess->user_type == 1) || (($this->sess->user_type == 3) && ($this->sess->agent_canedit_vactive == 1))) { ?>
    			<tr>
						<td><?=$this->lang->line("lexpire_date");?></td>
						<td>:</td>
						<td>
								<table width="100%" cellpadding="3">
									<tr>
										<td><input type='text' name="expire_date1" id="expire_date1"  class="date-pick" value="<?php echo $vehicle->vehicle_active_date1_fmt; ?>"  maxlength='10'></td>
										<td><?=$this->lang->line("luntil");?></td>
										<td><input type='text' name="expire_date2" id="expire_date2"  class="date-pick" value="<?php echo $vehicle->vehicle_active_date2_fmt; ?>"  maxlength='10'></td>
									</tr>
								</table>
						</td>
					</tr>	
				<?php } ?>
    			<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>
								<input type="submit" name="btnsave" id="btnsave" value=" Save " />
						</td>
					</tr>					
				</table>
			</form>
