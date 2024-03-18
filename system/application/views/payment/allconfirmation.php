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
					
				function frmpayment_onsubmit()
				{
					jQuery.post("<?php echo base_url(); ?>payment/saveconfirmation", jQuery("#frmpayment").serialize(),
						function (r)
						{
							if (r.error)
							{
								alert(r.message);
								return;
							}
							
							alert(r.message);
							jQuery("#dialog").dialog("close");
						}
						, "json"
					);				
					
					return false;
				}		
				
				jQuery(document).ready(
					function()
					{
						jQuery("#paymentdate").datepicker(
							{
										dateFormat: 'dd/mm/yy'
									, 	startDate: '01/01/1900'
									, 	endDate: '<?php echo date("d/m/Y"); ?>'
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
					}						
				);
			</script>
			<form name="frmpayment" id="frmpayment" onsubmit="javascript: return frmpayment_onsubmit()">				
				<table width="100%" cellpadding="3" class="tablelist">
    			<tr>
						<td width="40%"><?=$this->lang->line("lvehicle");?></td>
						<td width="1">:</td>
						<td>
							<select id="id" name="id">
								<?php for($i=0; $i < count($vehicles); $i++) { ?>
								<?php $ds = inttodate($vehicles[$i]->vehicle_active_date2); ?>
								<option value="<?php echo $vehicles[$i]->vehicle_id; ?>"><?php echo $vehicles[$i]->vehicle_name; ?> - <?php echo $vehicles[$i]->vehicle_no; ?> - <?php echo $ds; ?></option>
								<?php } ?>
							</select>							
						</td>
					</tr>
    			<tr>
    			<tr>
						<td><?=$this->lang->line("ltransfer_method");?></td>
						<td>:</td>
						<td>
							<select id="transfermethod" name="transfermethod">
								<option value="cash"><?=$this->lang->line("lcash");?></option>
								<option value="atm"><?=$this->lang->line("latm");?></option>
								<option value="internet"><?=$this->lang->line("linet_banking");?></option>
								<option value="sms"><?=$this->lang->line("lsms_banking");?></option>
							</select>
						</td>
					</tr>
    			<tr>
						<td><?=$this->lang->line("ldestination_account");?></td>
						<td width="1">:</td>
						<td>
							<select id="bankdest" name="bankdest">
								<?php for($i=0; $i < count($banks); $i++) { ?>
								<option value="<?php echo $banks[$i]->bank_id; ?>">No Rek.<?php echo $banks[$i]->bank_branch; ?> <?php echo $banks[$i]->bank_acc; ?> a/n <?php echo $banks[$i]->bank_name; ?></option>
								<?php } ?>
							</select>
						</td>
				</tr>
    			<tr>
						<td><?=$this->lang->line("lpayment_amount_desc");?></td>
						<td>:</td>
						<td><input type="text" name="amount" id="amount" value="X<?php echo $this->config->item("serviceamount"); ?>" class="formshort" /></td>
				</tr>
    			<tr>
						<td><?=$this->lang->line("lpayment_date");?></td>
						<td>:</td>
						<td><input type='text' name="paymentdate" id="paymentdate"  class="date-pick" value=""  maxlength='10'></td>
				</tr>
    			<tr>
						<td><?=$this->lang->line("ltransfer_code_desc");?></td>
						<td>:</td>
						<td><input type='text' name="transfercode" id="transfercode"  class="formshort" value=""></td>
				</tr>
    			<tr>
						<td><?=$this->lang->line("lsendername_desc");?></td>
						<td>:</td>
						<td><input type='text' name="sendername" id="sendername"  class="formdefault" value=""></td>
				</tr>				
    			<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>
							<input type="submit" name="btnsave" id="btnsave" value=" Send " />
						</td>
					</tr>					
				</table>
			</form>		
