<script>
	jQuery(document).ready(
		function()
		{
			showclock();
			
		}
	);
	
	function frmedit_onsubmit()
	{
		jQuery("#loader2").show();
		jQuery.post("<?=base_url()?>transporter/mod_vehicle_maintenance/vehicle_update", jQuery("#frmedit").serialize(),	
			function(r)
			{
				jQuery("#loader2").hide();
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
		<h2><?php echo "Vehicle"." ".$row->mobil_name." ".$row->mobil_no;?></h2>
		<form class="block-content form" name="frmedit" id="frmedit" method="post" onsubmit="javascript: return frmedit_onsubmit()">	
		<input type="hidden" name="mobil_id" id="mobil_id" value="<?php if(isset($row->mobil_id)) { echo $row->mobil_id; };?>" />		
		<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<tr>
				<td colspan="6">
					<b>Profile</b>
					<br />
					<hr />
				</td>
			</tr>
			
			<?php
			//Kumis Logistics
			//$app_route = $this->config->item("app_route");
			//if (isset($app_route) && ($app_route == 1))
			//{
			?>
			<!--
			<tr>
				<td>Route</td>
				<td>:</td>
				<td style="text-align:left;" colspan="4">
				<select name="mobil_route" id="mobil_route">
				<option value="0">--Select Route--</option>
				<?php 
				/*if (isset($my_route) && $my_route>0)
				{
					foreach($my_route as $myroute)
					{
						if (isset($row) && $row->mobil_route == $myroute->route_id)
						{
							$selected = "selected"; 
						}
						else
						{
							$selected = "";
						}
						echo "<option value='" . $myroute->route_id ."' " . $selected . ">" . $myroute->route_name . "</option>";
					}
				}*/
				?>
				</select>
				</td>
			</tr>
			-->
			<?php
			//}
			?>
			
			<tr>
				<td width="25%">Type</td>
				<td width="2%">:</td>
				<td>
					<input name="mobil_model" id="mobil_model" value="<?php if(isset($row->mobil_model)) { echo $row->mobil_model; };?>" />
				</td>
				
				<td style="text-align:right" width="25%">Year</td>
				<td width="2%">:</td>
				<td>
					<input name="mobil_year" id="mobil_year" value="<?php if(isset($row->mobil_year)) { echo $row->mobil_year; };?>" />
				</td>
			</tr>
			
			<tr>
				<td>Engine Capacity</td>
				<td>:</td>
				<td>
					<input size="5" name="mobil_engine_capacity" id="mobil_engine_capacity" value="<?php if(isset($row->mobil_engine_capacity)) { echo $row->mobil_engine_capacity; };?>" /> CC
				</td>
				
				<td style="text-align:right">Registration Date</td>
				<td width="2%">:</td>
				<td>
					<?php 
						if (isset($row->mobil_registration_date))
						{
							$v = $row->mobil_registration_date;
							$val = date("d-m-Y", strtotime($v));
						}
					?>
					<input name="mobil_registration_date" id="mobil_registration_date" value="<?php if(isset($val)) { echo $val; };?>" />
				</td>
			</tr>
			
			<tr>
				<td>Fuel Type</td>
				<td>:</td>
				<td>
					<select id="mobil_fuel_type" name="mobil_fuel_type">
					<?php 
						if(isset($fuel) && count($fuel)>0)
						{
							for($i=0;$i<count($fuel);$i++)
							{
								if(isset($row->mobil_fuel_type) &&  ($fuel[$i]->fuel_type_id == $row->mobil_fuel_type))
								{
									$mselected = "selected='selected'";
								}
								else
								{
									$mselected = "";
								}
								echo "<option value='" . $fuel[$i]->fuel_type_id ."' ". $mselected.">" . $fuel[$i]->fuel_type . "</option>";
							}
						}
					?>
					</select>
				</td>
				
				<td style="text-align:right">Fuel Consumption</td>
				<td>:</td>
				<td>
					<input type="text" size="10" name="mobil_fuel_consumption" id="mobil_fuel_consumption" value="<?php if(isset($row->mobil_fuel_consumption)) { echo $row->mobil_fuel_consumption; };?>" />
					Lt
				</td>
			</tr>
			<tr>
				<td>KIR No</td>
				<td>:</td>
				<td>
					<input name="mobil_no_kir" id="mobil_no_kir" value="<?php if(isset($row->mobil_no_kir)) { echo $row->mobil_no_kir; };?>" />
				</td>
				
				<td style="text-align:right">KIR Exp. Date</td>
				<td width="2%">:</td>
				<td>
					<?php 
						if (isset($row->mobil_kir_active_date))
						{
							$v = $row->mobil_kir_active_date;
							$val = date("d-m-Y", strtotime($v));
						}
					?>
					<input name="mobil_kir_active_date" id="mobil_kir_active_date" value="<?php if(isset($val)) { echo $val; };?>" />
				</td>
			</tr>
			<tr>
				<td>STNK No</td>
				<td>:</td>
				<td>
					<input name="mobil_stnk_no" id="mobil_stnk_no" value="<?php if(isset($row->mobil_stnk_no)) { echo $row->mobil_stnk_no; };?>" />
				</td>
				
				<td style="text-align:right">STNK Exp. Date</td>
				<td width="2%">:</td>
				<td>
					<?php 
						if (isset($row->mobil_stnk_expired))
						{
							$v = $row->mobil_stnk_expired;
							$val = date("d-m-Y", strtotime($v));
						}
					?>
					<input name="mobil_stnk_expired" id="mobil_stnk_expired" value="<?php if(isset($val)) { echo $val; };?>" />
				</td>
			</tr>
			<tr>
				<td>Rangka No</td>
				<td>:</td>
				<td>
					<input name="mobil_no_rangka" id="mobil_no_rangka" value="<?php if(isset($row->mobil_no_rangka)) { echo $row->mobil_no_rangka; };?>" />
				</td>
				
				<td style="text-align:right">Mesin No</td>
				<td width="2%">:</td>
				<td>
					<input name="mobil_no_mesin" id="mobil_no_mesin" value="<?php if(isset($row->mobil_no_mesin)) { echo $row->mobil_no_mesin; };?>" />
				</td>
			</tr>
			<tr>
				<td>KIU/SIPA No</td>
				<td>:</td>
				<td>
					<input name="mobil_no_sipa" id="mobil_no_sipa" value="<?php if(isset($row->mobil_no_sipa)) { echo $row->mobil_no_sipa; };?>" />
				</td>
				
				<td style="text-align:right">KIU/SIPA Expired</td>
				<td width="2%">:</td>
				<td>
					<input name="mobil_sipa_expired" id="mobil_sipa_expired" value="<?php if(isset($row->mobil_sipa_expired)) { echo $row->mobil_sipa_expired; };?>" />
				</td>
			</tr>
			<tr>
				<td>IBM No</td>
				<td>:</td>
				<td>
					<input name="mobil_no_ibm" id="mobil_no_ibm" value="<?php if(isset($row->mobil_no_ibm)) { echo $row->mobil_no_ibm; };?>" />
				</td>
				
				<td style="text-align:right">IBM Expired</td>
				<td width="2%">:</td>
				<td>
					<input name="mobil_ibm_expired" id="mobil_ibm_expired" value="<?php if(isset($row->mobil_ibm_expired)) { echo $row->mobil_ibm_expired; };?>" />
				</td>
			</tr>
			<tr>
				<td>Merk</td>
				<td>:</td>
				<td>
					<input name="mobil_merk" id="mobil_merk" value="<?php if(isset($row->mobil_merk)) { echo $row->mobil_merk; };?>" />
				</td>
				
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td></td>
			</tr>
			<tr><td colspan="6">&nbsp;</td></tr>
			<tr>
				<td colspan="6">
					<b>Insurance Information</b>
					<br />
					<hr />
				</td>
			</tr>
			<tr>
				<td>Insurance No</td>
				<td>:</td>
				<td>
					<input name="mobil_insurance_no" id="mobil_insurance_no" value="<?php if(isset($row->mobil_insurance_no)) { echo $row->mobil_insurance_no; };?>" />
				</td>
				
				<td style="text-align:right">Insurance Exp. Date</td>
				<td>:</td>
				<td>
					<?php 
						$v = $row->mobil_insurance_expired_date;
						$val = date("d-m-Y",strtotime($v));
					?>
					<input name="mobil_insurance_expired_date" id="mobil_insurance_expired_date" value="<?php if(isset($val)) { echo $val; };?>" />
				</td>
			</tr>
			<tr>
				<td>Insurance Type</td>
				<td>:</td>
				<td>
					<select id="mobil_insurance_type" name="mobil_insurance_type">
					<option value="0" >--Select Insurance Type--</option>
					<?php 
						if(isset($insurance_type) && count($insurance_type)>0)
						{
							for($i=0;$i<count($insurance_type);$i++)
							{
								if(isset($row->mobil_insurance_type) &&  ($insurance_type[$i]->insurance_type_id == $row->mobil_insurance_type))
								{
									$mselected = "selected='selected'";
								}
								else
								{
									$mselected = "";
								}
								echo "<option value='" . $insurance_type[$i]->insurance_type_id ."' ". $mselected.">" . $insurance_type[$i]->insurance_type . "</option>";
							}
						}
					?>
					</select>
				</td>
				
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr><td colspan="6">&nbsp;</td></tr>
			<tr>
				<td colspan="6">
					<b>Services Information</b>
					<br />
					<hr />
				</td>
			</tr>
			<tr>
				<td>Service Date</td>
				<td>:</td>
				<td>
					<?php 
						$v = $row->mobil_service_date;
						$value = date("d-m-Y",strtotime($row->mobil_service_date));
					?>
					<input name="mobil_service_date" id="mobil_service_date" value="<?php if(isset($val)) { echo $val; };?>" />
				</td>
				
				<td style="text-align:right">Warranty Service by Date</td>
				<td>:</td>
				<td>
					<?php 
						$v = $row->mobil_warranty_service_bydate;
						$value = date("d-m-Y",strtotime($row->mobil_warranty_service_bydate));
					?>
					<input name="mobil_warranty_service_bydate" id="mobil_warranty_service_bydate" value="<?php if(isset($val)) { echo $val; };?>" />
				</td>
			</tr>

			<tr>
				<td>Warranty Service by KM</td>
				<td>:</td>
				<td>
					<input name="mobil_warranty_service_bykm" id="mobil_warranty_service_bykm" value="<?php if(isset($row->mobil_warranty_service_bykm)) { echo $row->mobil_warranty_service_bykm; };?>" />
				</td>
				
				<td style="text-align:right">Schedule Service</td>
				<td>:</td>
				<td>
					<?php 
						$v = $row->mobil_next_service_date;
						$value = date("d-m-Y",strtotime($row->mobil_next_service_date));
					?>
					<input name="mobil_next_service_date" id="mobil_next_service_date" value="<?php if(isset($val)) { echo $val; };?>" />
				</td>
			</tr>
			<tr>
				<td>Last Service (Inisialisasi Odo)</td>
				<td>:</td>
				<td>
					<input name="mobil_last_service_bykm" id="mobil_last_service_bykm" value="<?php if(isset($row->mobil_last_service_bykm)) { echo $row->mobil_last_service_bykm; };?>" />
				</td>
				
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td colspan="6">
					<input type="submit" name="submit" id="submit" value="Update" />
					<img id="loader2" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" style="display:none;">
				</td>
			</tr>
		</table>
		</form>
		
