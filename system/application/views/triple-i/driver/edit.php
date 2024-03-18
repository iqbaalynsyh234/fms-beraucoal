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
		<br />&nbsp;
<!--New Form -->
<form id="frmedit" name="frmedit" method="post" action="<?=base_url()?>transporter/driver/update" class="wufoo topLabel page">
<header id="header" class="info">
	<h2>Edit Driver Registration</h2>
	<div></div>
</header>

<ul>
	<li id="foli0" class="notranslate">
		<label class="desc" id="title0" for="Field0">Name
			<span id="req_2" class="req">*</span>
		</label>
		<span>
			<input type="hidden" name="driver_company" id="driver_company"  value="<?php echo $this->sess->user_company;?>" />
			<input type="hidden" name="driver_id" id="driver_id"  value="<?php echo $row->driver_id?>" />
			<input id="Field0" name="driver_name" type="text" class="field text fn" value="<?=isset($row) ? htmlspecialchars($row->driver_name, ENT_QUOTES) : "";?>" size="50" tabindex="1" />
			<label for="Field0">Full Name</label>
		</span>
	</li>
	
	<li id="foli2" class="complex notranslate">
		<label class="desc" id="title2" for="Field2">Address</label>
		<div>
			<span class="full addr1">
				<input type="text" id="Field2" name="driver_address" type="text" class="field text addr" value="<?=$row->driver_address;?>" tabindex="2" />
				<label for="Field2">Street Address</label>
			</span>
		</div>
	</li>
	
	<li id="foli8" class="phone notranslate leftHalf">
		<label class="desc" id="title8" for="Field8">Phone</label>
		<span>
			<input id="Field8" name="driver_phone" type="tel" class="field text" value="<?=isset($row) ? htmlspecialchars($row->driver_phone, ENT_QUOTES) : "";?>" size="25" maxlength="25" tabindex="3" />
			<label for="Field8">########</label>
		</span>
	</li>
	
	<li id="foli8" class="phone notranslate leftHalf">
		<label class="desc" id="title8" for="Field8">Mobile</label>
		<span>
			<input id="Field8" name="driver_mobile" type="tel" class="field text" value="<?=isset($row) ? htmlspecialchars($row->driver_mobile, ENT_QUOTES) : "";?>" size="25" maxlength="25" tabindex="4" />
			<label for="Field8">1. ###</label>
		</span>
		<span class="symbol">/</span>
		<span>
			<input id="Field8-1" name="driver_mobile2" type="tel" class="field text" value="<?=isset($row) ? htmlspecialchars($row->driver_mobile2, ENT_QUOTES) : "";?>" size="25" maxlength="25" tabindex="5" />
			<label for="Field8-1">2. ###</label>
		</span>
	</li>
	
	<li id="foli8" class="phone notranslate leftHalf">
		<label class="desc" id="title8" for="Field8">Licence</label>
		<span>
			<input id="Field8" name="driver_licence" type="tel" class="field text" value="<?=isset($row) ? htmlspecialchars($row->driver_licence, ENT_QUOTES) : "";?>" size="3" maxlength="3" tabindex="6" />
			<label for="Field8">Licence</label>
		</span>
		<span class="symbol">-</span>
		<span>
			<input id="Field8-1" name="driver_licence_no" type="tel" class="field text" value="<?=isset($row) ? htmlspecialchars($row->driver_licence_no, ENT_QUOTES) : "";?>" size="25" maxlength="25" tabindex="7" />
			<label for="Field8-1">Licence Number</label>
		</span>
	</li>
	
	<li id="foli10" class="notranslate">
		<label class="desc" id="title10" for="Field10">Sex</label>
		<div>
			<select id="Field10" name="driver_sex" tabindex="8" > 
				<option value="M" <? if ((! isset($row)) || ($row->driver_sex == 'M')) { ?>selected<?php } ?>>Male</option>
				<option value="F" <? if ((! isset($row)) || ($row->driver_sex == 'F')) { ?>selected<?php } ?>>Female</option>
			</select>
		</div>
	</li>
	
	<li id="foli8" class="phone notranslate leftHalf">
		<label class="desc" id="title8" for="Field8">Joint Date</label>
		<span>
			<input id="joint_date" name="driver_joint_date" type="tel" class="field text" value="<?=isset($row) ? htmlspecialchars($row->driver_joint_date, ENT_QUOTES) : "";?>" tabindex="9" />
			<label for="Field8">dd-MM-YYYY</label>
		</span>
	</li>
	
	<li id="foli11" class="notranslate">
		<label class="desc" id="title11" for="Field11">Special Notes</label>
		<div>
			<input type="text" class="field text addr" id="Field11" name="driver_note" value="<?=isset($row) ? htmlspecialchars($row->driver_note, ENT_QUOTES) : "";?>" spellcheck="true" tabindex="9" onkeyup="" />
		</div>
	</li>
	
	<li class="buttons ">
	<div>
		<input id="saveForm" name="submit" class="btTxt submit" type="submit" value="Update" />
		<input id="btncancel" name="btncancel" class="btTxt submit" type="button" value="Cancel" onclick="location='<?=base_url()?>transporter/driver';" />
		<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
		</div>
	</li>
</ul>
</form> 

	</div><!--container-->
		<img id="bottom" src="<?php echo base_url();?>assets/transporter/images/form/bottom.png" alt="" />
		<!-- End New Form -->




	</div>
</div>