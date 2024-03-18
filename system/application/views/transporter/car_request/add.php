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
		
		jQuery("#start_date").datepicker(
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
        
        jQuery("#end_date").datepicker(
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
		jQuery.post("<?=base_url()?>transporter/car_request/save_by_cust", jQuery("#frmadd").serialize(),	
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
		<h1>Request Car</h1>
        <hr /><br />
		<form name="frmadd" id="frmadd" onsubmit="javascript: return frmadd_onsubmit()">
			<table width="100%" cellpadding="3" class="tablelist">
				<tr>
				<input type="hidden" name="request_company" id="request_company"  value="<?php echo $this->sess->user_company;?>" /></td>
				<input type="hidden" name="request_group_company" id="request_group_company"  value="<?php echo $this->sess->user_group;?>" /></td>
                <input type="hidden" name="request_group_name" id="request_group_name"  value="<?php echo $group;?>" /></td>
                <input type="hidden" name="request_user_id" id="request_user_id"  value="<?php echo $this->sess->user_id;?>" /></td>
                </tr>
				<tr>
					<td>Start Date</td>
					<td>:</td>
                    <td><input type="text" maxlength="15" name="start_date" id="start_date" class="date-pick" /></td>
				</tr>
				<tr>
					<td>End Date</td>
					<td>:</td>
					<td><input type="text" maxlength="15" name="end_date" id="end_date" class="date-pick" /></td>
				</tr>
				<tr>
					<td>Trip Purpose</td>
					<td>:</td>
					<td>
                        <select name="request_purpose" id="request_purpose">
                        <?php
                        if (isset($trip_purpose) && $trip_purpose != "")
                        {
                            foreach($trip_purpose as $purpose)
                            {
                         ?>
                         <option value="<?php echo $purpose->trip_purpose_id;?>"><?php echo $purpose->trip_purpose_name; ?></option>
                         <?php } }?>
                        </select>
                    </td>
				</tr>
                
                <tr>
                <td colspan="3">
                <h2>PIC DATA</h2>
                </td>
                </tr>
				
                <tr>
					<td>Name</td>
					<td>:</td>
					<td><input type="text" name="request_pic_name" size="30" /></td>
				</tr>
				<tr>
                    <td>Mobile</td>
					<td>:</td>
					<td><input type="text" name="request_pic_mobile" size="30" /></td>
                </tr>
                <tr>
                    <td>Phone</td>
					<td>:</td>
					<td><input type="text" name="request_pic_phone" size="30" /></td>
                </tr>
				<tr>
					<td>Email</td>
					<td>:</td>
					<td><input type="text" name="request_pic_email" size="30" /></td>
				</tr>
				<tr>
				    <td>Address</td>
					<td>:</td>
					<td><textarea rows="5" cols="30" name="request_pic_address" ></textarea></td>
				</tr>
				<tr>
						<td colspan="3">
								<input type="submit" name="btnsave" id="btnsave" value=" Save " />
								<input type="button" name="btncancel" id="btncancel" value=" Cancel " onclick="location='<?=base_url()?>transporter/car_request/getlist';" />
								<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;" />
						</td>
				</tr>
			</table>
		</form>
	</div>
</div>