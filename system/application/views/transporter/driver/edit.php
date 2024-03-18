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
		
		jQuery("#joint_date").datepicker(
							{
										dateFormat: 'dd-mm-yy'
									, 	startDate: '01-01-2000'
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
		
		jQuery("#driver_licence_expired").datepicker(
							{
										dateFormat: 'dd-mm-yy'
									, 	startDate: '01-01-2000'
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
		
		jQuery("#driver_siof_expired").datepicker(
							{
										dateFormat: 'dd-mm-yy'
									, 	startDate: '01-01-2000'
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
						
			showclock();
			
		}
	);
	
	function frmedit_onsubmit()
	{
		jQuery("#loader").show();
		jQuery.post("<?=base_url()?>transporter/driver/update", jQuery("#frmedit").serialize(),	
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
		<form class="block-content form" name="frmedit" id="frmedit" method="post" action="<?=base_url()?>transporter/driver/update">
        <h1>Edit Driver</h1>
			<table class="table sortable no-margin">
				<tr>
				<?php if (isset($row)) { ?>	
				<input type="hidden" name="driver_company" id="driver_company"  value="<?php echo $this->sess->user_company;?>" /></td>
				<input type="hidden" name="driver_id" id="driver_id"  value="<?php echo $row->driver_id;?>" /></td>
				<input type="hidden" name="driver_group" id="driver_group"  value="<?php echo $this->sess->user_group;?>" /></td>
				<?php } ?>
				</tr>
				<tr>
					<td>Name</td>
					<td>:</td>
					<td><input type="text" name="driver_name" value="<?=isset($row) ? htmlspecialchars($row->driver_name, ENT_QUOTES) : "";?>" /></td>
				</tr>
				<tr>
					<td>ID Card</td>
					<td>:</td>
					<td><input type="text" name="driver_idcard" value="<?=isset($row) ? htmlspecialchars($row->driver_idcard, ENT_QUOTES) : "";?>" /></td>
				</tr>
				<tr>
					<td>Address</td>
					<td>:</td>
					<td><input type="text" name="driver_address" value="<?=$row->driver_address;?>" >
					</td>
				</tr>
				<tr>
					<td>Phone</td>
					<td>:</td>
					<td><input type="text" name="driver_phone" value="<?=isset($row) ? htmlspecialchars($row->driver_phone, ENT_QUOTES) : "";?>" /></td>
				</tr>
				<tr>
					<td>Mobile</td>
					<td>:</td>
					<td>
					1.<input type="text" name="driver_mobile" value="<?=isset($row) ? htmlspecialchars($row->driver_mobile, ENT_QUOTES) : "";?>" />
					2.<input type="text" name="driver_mobile2" value="<?=isset($row) ? htmlspecialchars($row->driver_mobile2, ENT_QUOTES) : "";?>" />
					</td>
				</tr>
				<tr>
					<td>Licence</td>
					<td>:</td>
					<td><input size="5" type="text" maxlength="5" name="driver_licence" value="<?=isset($row) ? htmlspecialchars($row->driver_licence, ENT_QUOTES) : "";?>" />
					Licence No :
					<input type="text" name="driver_licence_no" value="<?=isset($row) ? htmlspecialchars($row->driver_licence_no, ENT_QUOTES) : "";?>" /></td>
				</tr>
				<tr>
					<td>Licence Expired</td>
					<td>:</td>
					<td>
						<input type="text" maxlength="15" name="driver_licence_expired" id="driver_licence_expired" class="date-pick" value="<?=isset($row) ? htmlspecialchars($row->driver_licence_expired, ENT_QUOTES) : "";?>" /><br />
					</td>
					</td>
				</tr>
				<tr>
					<td>Sex</td>
					<td>:</td>
					<td>
						<select name="driver_sex" >
							<option value="M" <? if ((! isset($row)) || ($row->driver_sex == 'M')) { ?>selected<?php } ?>>M</option>
							<option value="F" <? if ((! isset($row)) || ($row->driver_sex == 'F')) { ?>selected<?php } ?>>F</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>Joint Date</td>
					<td>:</td>
					<td><input type="text" maxlength="15" name="driver_joint_date" id="joint_date" class="date-pick"  value="<?=isset($row) ? htmlspecialchars($row->driver_joint_date, ENT_QUOTES) : "";?>"/></td>
					</td>
				</tr>
				<tr>
					<td>SIOF</td>
					<td>:</td>
					<td>
						<input type="text" maxlength="15" name="driver_siof" id="driver_siof" value="<?=isset($row) ? htmlspecialchars($row->driver_siof, ENT_QUOTES) : "";?>" /><br />
						<small>Surat Ijin Operation Forklip</small>
					</td>
					</td>
				</tr>
				<tr>
					<td>SIOF Expired</td>
					<td>:</td>
					<td>
						<input type="text" maxlength="15" name="driver_siof_expired" id="driver_siof_expired" class="date-pick" value="<?=isset($row) ? htmlspecialchars($row->driver_siof_expired, ENT_QUOTES) : "";?>" /><br />
						<small>Surat Ijin Operation Forklip</small>
					</td>
					</td>
				</tr>
				<tr>
					<td>Note</td>
					<td>:</td>
					<td><input type="text" name="driver_note" value="<?=isset($row) ? htmlspecialchars($row->driver_note, ENT_QUOTES) : "";?>" >
				</tr>
				<tr>
					<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>
								<input type="submit" name="btnsave" id="btnsave" value=" Save " />
								<input type="button" name="btncancel" id="btncancel" value=" Cancel " onclick="location='<?=base_url()?>transporter/driver';" />
								<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
						</td>
				</tr>
			</table>
		</form>
	</div>
    </div>
</div>