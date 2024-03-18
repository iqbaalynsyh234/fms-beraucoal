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
						
						vehicle_image_onchange();
						loadgroup();	
					}

				);
		
        function loadgroup()
        {
                jQuery.post("<?php echo base_url(); ?>transporter/customer/options<?php if (isset($vehicle)) { echo "/".$vehicle->vehicle_group; } ?>", jQuery("#frmaddvehicle").serialize(),
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
					jQuery.post("<?=base_url();?>transporter/user/savevehicle", jQuery("#frmaddvehicle").serialize(),
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
				
				function vehicle_type_onchange()
				{
					jQuery("#fuel").hide();
					var vtype = jQuery("#vehicle_type_2").val();
					
					if(vtype == 'T5 Fuel'){
						jQuery("#fuel").show();
					}
							
				}
			</script>
            <div class="block-border">
			<form id="frmaddvehicle" onsubmit="javascript: return frmaddvehicle_onsubmit(this)">			
				<input type="hidden" name="vehicle_id" id="vehicle_id" value="<?php if (isset($vehicle)) { echo $vehicle->vehicle_id; } ?>" />
				<table width="100%" cellpadding="3" class="tablelist">
    			<tr>
						<td width="160" style="display:none;"><?=$this->lang->line("lvehicle_device");?></td>
						<td width="1" style="display:none;">:</td>
						<td style="display:none";><input type="text" name="vehicle_device" id="vehicle_device" value="<?php if (isset($vehicle)) { echo $vehicle->vehicle_device; } ?>" class="formdefault" /></td>
					</tr>
					<?php if ($this->config->item('vehicle_type_fixed')) { ?>
					<input type="hidden" name="vehicle_type" id="vehicle_type" value="<?php echo $this->config->item('vehicle_type_fixed'); ?>" />
				<?php } else if (isset($vehicle)) { ?>
					<input type="hidden" name="vehicle_type" id="vehicle_type" value="<?php echo $vehicle->vehicle_type; ?>" />
			<?php } else { ?>
    			<tr>
						<td style="display:none";><?=$this->lang->line("lvehicle_type");?></td>
						<td style="display:none";>:</td>
						<td style="display:none";>
							<select name='vehicle_type' id='vehicle_type_2' onchange="javascript:vehicle_type_onchange();">
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
					<?php } ?>
				<?php
				if (isset($vehicle) && $vehicle->vehicle_type == 'T5 Fuel'){
					$showfuel = "";
				}else{
					$showfuel = "style='display:none;'";
				}				
				?>
				
				<tr <?=$showfuel?> id="fuel">
						<td style="display:none";><?=$this->lang->line("lvehicle_fuel_capacity");?></td>
						<td style="display:none";>:</td>
						<td style="display:none";>
							<select name='vehicle_fuel_capacity' id='vehicle_fuel_capacity'>
								<option value="0">--Select Fuel Capacity--</option>
								<?php 
									
									foreach($fuel as $f){
										if (isset($vehicle) && $vehicle->vehicle_fuel_capacity == $f->fuel_tank_capacity){
											$selected = "selected"; 
										}else{
											$selected = "";
										}
										echo "<option value='" . $f->fuel_tank_capacity ."' " . $selected . ">" . $f->fuel_tank_capacity . "L</option>";
									}
									
								?>						
							</select>
						</td>
				</tr>
                                <?php if (count($companies)) { ?>
                        <tr>
                                                <td><?=$this->lang->line("lcompany");?></td>
                                                <td>:</td>
                                                <td>
                                                        <select name="usersite" id="usersite" onchange="javascript:loadgroup()">
															<option value="0"><?php echo $this->lang->line("lprivate"); ?></option>
                                                        <?php foreach($companies as $company) { ?>
                                                                <option value="<?php echo $company->company_id; ?>" <?php if (isset($vehicle) && ($vehicle->vehicle_company == $company->company_id)) { echo "selected"; } ?>><?php echo $company->company_name; ?></option>
                                                        <?php } ?>
                                                        </select>               
                                                </td>
                        </tr>
                        <tr id="trgroup" style="display: none;">
                                <td><?php echo "Customer"; ?></td>
                                <td>:</td>
                                <td><div id="usergroup"></div></td>
                        </tr>
                                <?php } ?>

			<tr id="trowner">
				<td><?=$this->lang->line("lusername");?></td>
				<td>:</td>
				<td>
				<select name="vehicle_user_id" id="vehicle_user_id">
					<?php for($i=0; $i < count($users); $i++) { ?>
					<?php if ($users[$i]->user_type == 1) continue; ?>
					<option value="<?php echo $users[$i]->user_id; ?>"<?php if (isset($owner) && ($owner == $users[$i]->user_id)) { echo " selected"; } ?>><?php echo $users[$i]->user_name; ?></option>
					<?php } ?>
				</select>
				</td>
			</tr>

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
				<?php if (($this->sess->user_type == 1) || (($this->sess->user_type == 3) && ($this->sess->agent_canedit_vactive == 1))) { ?>
    			<tr>
						<td ><?=$this->lang->line("lexpire_date");?></td>
						<td>:</td>
						<td>
								<table width="100%" cellpadding="3">
									<tr>
										<td><input type='text' name="vehicle_active_date1" id="vehicle_active_date1"  class="date-pick" value="<?php if (isset($vehicle)) { echo date('d/m/Y', $vehicle->vehicle_active_date1_t); } ?>"  maxlength='10'></td>
										<td><?=$this->lang->line("luntil");?></td>
										<td><input type='text' name="vehicle_active_date2" id="vehicle_active_date2"  class="date-pick" value="<?php if (isset($vehicle)) { echo date('d/m/Y', $vehicle->vehicle_active_date2_t); } ?>"  maxlength='10'></td>
									</tr>
								</table>
						</td>
					</tr>	
				<?php } ?>
    			<tr>
						<td style="display:none";><?=$this->lang->line("lexpire_card_no");?></td>
						<td style="display:none";>:</td>
						<td style="display:none";><input type="text" name="vehicle_card_no" id="vehicle_card_no" value="<?php if (isset($vehicle)) { echo $vehicle->vehicle_card_no; } ?>" class="formdefault" /></td>
					</tr>
    			<tr>
						<td style="display:none";><?=$this->lang->line("lexpire_card_op");?></td>
						<td style="display:none";>:</td>
						<td style="display:none";><input type="card_op" name="vehicle_operator" id="vehicle_operator" value="<?php if (isset($vehicle)) { echo $vehicle->vehicle_operator; } ?>" class="formdefault" /></td>
					</tr>
    			<tr>
						<td style="display:none";><?=$this->lang->line("lexpire_card_expired_date");?></td>
						<td style="display:none";>:</td>
						<td style="display:none";><input type='text' name="vehicle_active_date" id="vehicle_active_date"  class="date-pick" value="<?php if (isset($vehicle)) { echo date('d/m/Y', $vehicle->vehicle_active_date_t); } ?>"  maxlength='10'></td>
					</tr>
    			<tr>
						<td><?=$this->lang->line("lmobil_image");?></td>
						<td>:</td>
						<td>
							<select name='vehicle_image' id='vehicle_image' onchange="vehicle_image_onchange()">
							<?php foreach($this->config->item('vehicle_image') as $key=>$val) { ?>
							<option value="<?php echo $key; ?>"<?php if (isset($vehicle) && ($vehicle->vehicle_image == $key)) { echo " selected"; } ?>><?php echo $this->lang->line($val); ?></option>
							<?php } ?>
						</select><span id="dvvehicle_image"></span>
						</td>
					</tr>	
    			<tr>
						<td ><?=$this->lang->line("lodometer_init");?></td>
						<td>:</td>
						<td><input type='text' name="vehicle_odometer" id="vehicle_odometer"  class="formshort" value="<?php if (isset($vehicle)) { echo $vehicle->vehicle_odometer; } ?>"  maxlength='9'> <?php echo $this->lang->line('lkm'); ?></td>
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
						<td style="display:none";>Server</td>
						<td style="display:none";>:</td>
						<td style="display:none";>
							<select name="vehicle_ip" id="vehicle_ip">
						<?php foreach($this->config->item("SERVER_TRACKERS") as $key=>$val) { ?>
						<option value="<?php echo $key; ?>"<?php echo (isset($vehicle) && ($key==$vehicle->vehicle_ip)) ? " selected" : "";?>><?php echo $val; ?></option>
						<?php } ?>						
							</select>
						</td>
					</tr>

				<?php 
					if (count($drivers)) { 
					$appdosj = $this->config->item("app_dosj");
					if (!$appdosj)
					{
				?>
                        <tr>
							<td><?php echo "Driver"?></td>
							<td>:</td>
							<td>
								<?php 
									
									$app_route = $this->config->item("app_route");
									if (isset($app_route) && $app_route == 1)
									{
										foreach($drivers as $driver)
										{
											if ($vehicle->vehicle_id == $driver->driver_vehicle)
											{
												echo $driver->driver_name;
											}
										}
									}
									else 
									{
								?>
								<select name="driver" id="driver">
									<option value="0"><?php echo "NONE"; ?></option>
									<?php foreach($drivers as $driver) { ?>
									<option value="<?php echo $driver->driver_id; ?>" <?php if (isset($vehicle) && ($vehicle->vehicle_id == $driver->driver_vehicle)) { echo "selected"; } ?>><?php echo $driver->driver_name; ?></option>
									<?php } ?>
								</select>
								<?php } ?>
							</td>
                        </tr>
				<?php } } ?>
					
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
