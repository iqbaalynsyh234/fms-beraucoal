<script>
	jQuery.maxZIndex = jQuery.fn.maxZIndex = function(opt) {
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
				jQuery("#registration_date").datepicker(
				{
							dateFormat: 'dd-mm-yy'
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
				
				jQuery("#mobil_kir_active_date").datepicker(
				{
							dateFormat: 'dd-mm-yy'
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
				
				jQuery("#mobil_insurance_expired_date").datepicker(
				{
							dateFormat: 'dd-mm-yy'
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
				
				jQuery("#mobil_service_date").datepicker(
				{
							dateFormat: 'dd-mm-yy'
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
				
				jQuery("#mobil_warranty_service_bydate").datepicker(
				{
							dateFormat: 'dd-mm-yy'
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
				
				jQuery("#mobil_next_service_date").datepicker(
				{
							dateFormat: 'dd-mm-yy'
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
				
				jQuery("#mobil_stnk_expired").datepicker(
				{
							dateFormat: 'dd-mm-yy'
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
				
				jQuery("#mobil_sipa_expired").datepicker(
				{
							dateFormat: 'dd-mm-yy'
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
				
				jQuery("#mobil_ibm_expired").datepicker(
				{
							dateFormat: 'dd-mm-yy'
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
		jQuery.post("<?=base_url()?>transporter/mod_vehicle_maintenance/save", jQuery("#frmadd").serialize(),
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
	
	function numbersonly(myfield, e, dec)
	{
		var key;
		var keychar;
		
		if (window.event)
		key = window.event.keyCode;
		else if (e)
		key = e.which;
		else
		return true;
	
		keychar = String.fromCharCode(key);

		// control keys
		if ((key==null) || (key==0) || (key==8) || 
		(key==9) || (key==13) || (key==27) )
		return true;

		// numbers
		else if ((("0123456789").indexOf(keychar) > -1))
		return true;

		// decimal point jump
		else if (dec && (keychar == "."))
		{
			myfield.form.elements[dec].focus();
			return false;
		}
		else
		return false;
	}
		
</script>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
<?=$navigation;?>
	<div id="main" style="margin: 20px;">
		<div class="block-border">
			<form class="block-content form" id="frmadd" onsubmit="javascript: return frmadd_onsubmit(this)">				
			<h1><?php echo "Initializing Vehicle"; ?></h1>
			
			<table width="100%" cellpadding="3" class="tablelist">
			
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;" colspan="2">
						<b>Profile</b>
						<br />
						<hr />
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				
				<!-- Vehicle -->
				<!-- Hanya Vehicle yang sudah ter-registered ke dalam sistem lacak mobil -->
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">Vehicle</td>
					<td>
						<select name="vehicle" id="vehicle">
							<option value="0">--Select Vehicle--</option>
							<?php
								if ((isset($vehicle)) && count($vehicle)>0)
								{
								for($i=0;$i<count($vehicle);$i++)
								{
							?>
								<option value="<?php echo $vehicle[$i]->vehicle_device;?>"><?php echo $vehicle[$i]->vehicle_name." ".$vehicle[$i]->vehicle_no;?></option>
							<?php
								}
								}
							?>
						</select>
						<font color="red">*</font>
					</td>
				</tr>
                
				<tr><td>&nbsp;</td></tr>
				
				<!-- Kumis Logistics -->
				<!-- Route -->
				<!--
				<?php 
					//$app_route = $this->config->item("app_route");
					//if (isset($app_route) && ($app_route == 1))
					//{
				?>
					<tr style="border: 0px;">
					<td width="100" style="border: 0px;">Route</td>
					<td>
						<select name="route" id="route">
							<option value="0">--Select Route--</option>
							<?php
								//if ((isset($my_route)) && count($my_route)>0)
								//{
								//for($i=0;$i<count($my_route);$i++)
								//{
							?>
								<option value="<?php //echo $my_route[$i]->route_id;?>"><?php //echo $my_route[$i]->route_name;?></option>
							<?php
								//}
								//}
							?>
						</select>
					</td>
					</tr>
					<tr><td>&nbsp;</td></tr>
				<?php
					//}
				?>
				-->
				
				<!-- Vehicle Model -->
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">Type</td>
					<td style="border: 0px;">
						<input size="20" type="text" name="mobil_model" id="mobil_model" class="formdefault" />
						Year <input size="4" type="text" name="mobil_year" id="mobil_year" class="formdefault" onKeyPress="return numbersonly(this, event)" />
						
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				
				<!-- Vehicle Merk -->
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">Merk</td>
					<td style="border: 0px;">
						<input size="20" type="text" name="mobil_merk" id="mobil_merk" class="formdefault" />
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				
				<!-- Mobil Registration Date -->
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">Registration Date</td>
					<td style="border: 0px;">
						<input size="10" maxlength="10" type="text" name="registration_date" id="registration_date" class="date-pick" />
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				
				<!-- KIR -->
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">KIR No.</td>
					<td style="border: 0px;">
						<input size="10" type="text" name="mobil_no_kir" id="mobil_no_kir" class="formdefault" />
						Exp. Date 
						<input size="10" maxlength="10" type="text" name="mobil_kir_active_date" id="mobil_kir_active_date" class="date-pick" />
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				
				<!-- Engine Capacity -->
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">Engine Capacity</td>
					<td style="border: 0px;">
						<input size="10" type="text" name="mobil_engine_capacity" id="mobil_engine_capacity" class="formdefault" /> CC</font>
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				
				<!-- Fuel Type -->
				<!-- Select From DB -->
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">Fuel Type</td>
					<td>
						<select name="mobil_fuel_type" id="mobil_fuel_type">
							<option value="0">--Select Fuel Type--</option>
							<?php
								if ((isset($fuel_type)) && count($fuel_type)>0)
								{
								for($i=0;$i<count($fuel_type);$i++)
								{
							?>
								<option value="<?php echo $fuel_type[$i]->fuel_type_id;?>"><?php echo $fuel_type[$i]->fuel_type;?></option>
							<?php
								}
								}
							?>
						</select>
					</td>
				</tr>
                
				<tr><td>&nbsp;</td></tr>
				
				<!-- Fuel Consumption -->
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">Fuel Consumption</td>
					<td style="border: 0px;">
						<input size="5" type="text" name="mobil_fuel_consumption" id="mobil_fuel_consumption" class="formdefault" /> Lt
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				
				<!-- STNK -->
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">STNK No.</td>
					<td style="border: 0px;">
						<input size="10" type="text" name="mobil_stnk_no" id="mobil_stnk_no" class="formdefault" />
						Exp. Date 
						<input size="10" maxlength="10" type="text" name="mobil_stnk_expired" id="mobil_stnk_expired" class="date-pick" />
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				
				<!-- No Rangka -->
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">No. Rangka</td>
					<td style="border: 0px;">
						<input size="15" type="text" name="mobil_no_rangka" id="mobil_no_rangka" class="formdefault" />
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				
				<!-- No Mesin -->
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">No. Mesin</td>
					<td style="border: 0px;">
						<input size="15" type="text" name="mobil_no_mesin" id="mobil_no_mesin" class="formdefault" />
					</td>
				</tr>
				
				
				<tr><td>&nbsp;</td></tr>
				
				<!-- KIU / SIPA -->
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">KIU / SIPA</td>
					<td style="border: 0px;">
						<input size="15" type="text" name="mobil_no_sipa" id="mobil_no_sipa" class="formdefault" />
						Exp. Date 
						<input size="10" maxlength="10" type="text" name="mobil_sipa_expired" id="mobil_sipa_expired" class="date-pick" /><br />
						<small>KIU ( Kartu Ijin Usaha ) / SIPA ( Surat Ijin Pengusaha Angkutan )
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				
				<!-- IBM ( Ijin Bongkar Muat ) -->
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">IBM</td>
					<td style="border: 0px;">
						<input size="15" type="text" name="mobil_no_ibm" id="mobil_no_ibm" class="formdefault" />
						Exp. Date 
						<input size="10" maxlength="10" type="text" name="mobil_ibm_expired" id="mobil_ibm_expired" class="date-pick" /><br />
						<small>IBM (Ijin Bongkar Muat)
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr><td>&nbsp;</td></tr>
				
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;" colspan="2">
						<b>Insurance Information</b>
						<br />
						<hr />
					</td>
				</tr>
				
				<!-- Liability Insurance -->
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">Insurance No.</td>
					<td style="border: 0px;">
						<input type="text" name="mobil_insurance_no" id="mobil_insurance_no" class="formdefault" /> Exp. Date 
						<input size="10" maxlength="10" type="text" name="mobil_insurance_expired_date" id="mobil_insurance_expired_date" class="date-pick" />
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				
				<!-- Insurance Type -->
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">Insurance Type</td>
					<td style="border: 0px;">
						<select name="mobil_insurance_type" id="mobil_insurance_type">
							<option value="0">--Select Insurance Type--</option>
							<?php
								if (isset($insurance_type))
								{
									for ($i=0;$i<count($insurance_type);$i++)
									{
							?>
									<option value="<?php echo $insurance_type[$i]->insurance_type_id;?>">
									<?php echo $insurance_type[$i]->insurance_type;?>
									</option>
							<?		}
								}
							?>
						</select>
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr><td>&nbsp;</td></tr>
				
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;" colspan="2">
						<b>Services Information</b>
						<br />
						<hr />
					</td>
				</tr>

				<!-- Service Date -->
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">Service Date</td>
					<td style="border: 0px;">
						<input size="10" maxlength="10" type="text" name="mobil_service_date" id="mobil_service_date" class="date-pick" />
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				
				<!-- Warranty Service Date -->
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">Warranty Service Date</td>
					<td style="border: 0px;">
						<input size="10" maxlength="10" type="text" name="mobil_warranty_service_bydate" id="mobil_warranty_service_bydate" class="date-pick" />
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				
				<!-- Last Service By KM -->
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">Last Service (Inisialisasi Odo)</td>
					<td style="border: 0px;">
						<input size="10" maxlength="10" type="text" name="mobil_last_service_bykm" id="mobil_last_service_bykm" /> KM
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				
				<!-- Warranty Service By KM -->
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">Warranty Service by KM</td>
					<td style="border: 0px;">
						<input size="10" maxlength="10" type="text" name="mobil_warranty_service_bykm" id="mobil_warranty_service_bykm" /> KM
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				
				<!-- Service Date Schedule -->
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">Service Date Schedule</td>
					<td style="border: 0px;">
						<input size="10" maxlength="10" type="text" name="mobil_next_service_date" id="mobil_next_service_date" class="date-pick" />
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">Alert Service</td>
					<td style="border: 0px;">
						<input name="mobil_alert_service" type="radio" checked value="1">Yes</input>
						<input name="mobil_alert_service" type="radio" value="0">No</input>
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr><td colspan="2"><hr /></td></tr>
				
				<!-- Note -->
				<tr style="border: 0px;">
					<td width="100" valign="center" colspan="2">
						Note <br />
						<textarea name="note" id="note" cols="50" rows="5"></textarea>
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				
				<tr>
					<td colspan="2">
						<font color="red">*</font> ) Harus Di Isi
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				
    			<tr style="border: 0px;">
						<td style="border: 0px;" colspan="2">
								<input type="submit" name="btnsave" id="btnsave" value=" Save " />
								<input type="button" name="btncancel" id="btncancel" value=" Cancel " onclick="location='<?=base_url()?>transporter/mod_vehicle_maintenance';" />
								<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
						</td>
				</tr>					
				</table>
			</form>
		</div>
	</div>
</div>
			
