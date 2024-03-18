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
						
			showclock();
			
		}
	);
	
	function frmadd_onsubmit()
	{
		jQuery("#loader").show();
		jQuery.post("<?=base_url()?>transporter/driver/save", jQuery("#frmadd").serialize(),	
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
		<br />&nbsp;
		<h1>Add Driver</h1>
		<form name="frmadd" id="frmadd" onsubmit="javascript: return frmadd_onsubmit()">
			<table width="100%" cellpadding="3" class="tablelist">
				<tr>
				<input type="hidden" name="driver_company" id="driver_company"  value="<?php echo $this->sess->user_company;?>" /></td>
				</tr>
				<tr>
					<td>Name</td>
					<td>:</td>
					<td><input type="text" name="driver_name" /></td>
				</tr>
				<tr>
					<td>Address</td>
					<td>:</td>
					<td><textarea rows="5" name="driver_address" ></textarea></td>
				</tr>
				<tr>
					<td>Phone</td>
					<td>:</td>
					<td><input type="text" name="driver_phone" /></td>
				</tr>
				<tr>
					<td>Mobile</td>
					<td>:</td>
					<td>
					1.<input type="text" name="driver_mobile" />
					2.<input type="text" name="driver_mobile2" />
					</td>
				</tr>
				<tr>
					<td>Licence</td>
					<td>:</td>
					<td><input size="5" type="text" maxlength="5" name="driver_licence" />
					Licence No :
					<input type="text" name="driver_licence_no" /></td>
				</tr>
				<tr>
					<td>Sex</td>
					<td>:</td>
					<td>
						<select name="driver_sex" >
							<option value="M">Male</option>
							<option value="F">Female</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>Joint Date</td>
					<td>:</td>
					<td><input type="text" maxlength="15" name="driver_joint_date" id="joint_date" class="date-pick" /></td>
					</td>
				</tr>
				<tr>
					<td>Note</td>
					<td>:</td>
					<td><textarea rows="5" name="driver_note" ></textarea></td>
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