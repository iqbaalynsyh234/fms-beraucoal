<script type="text/javascript" src="<?php echo base_url();?>assets/js/jsblong/jblong.min.js"></script>
<style>
form select{
	position : relative;
}
</style>
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
				jQuery("#ship_date").datepicker(
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
				jQuery("select").searchable();
				jQuery("select").change(function()
				{
					jQuery("#value").html(this.options[this.selectedIndex].text + " (VALUE: " + this.value + ")");
				});
				trhide();
		}
	);
	
	
	function frmdosj_onsubmit()
	{
		jQuery("#loader").show();
		jQuery("#vehicle").removeAttr("disabled");
		jQuery("#customer").removeAttr("disabled");
		jQuery("#driver").removeAttr("disabled");
		jQuery.post("<?=base_url()?>transporter/dosj/save", jQuery("#frmdosj").serialize(),
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
	
	function trhide()
	{
		jQuery("#trvehicle").hide();
		jQuery("#trdriver").hide();
		jQuery("#tritemdesc").hide();
		jQuery("#btneditcust").hide();
		jQuery("#btneditvehicle").hide();
		jQuery("#btneditdriver").hide();
	}
	
	function select_change(v)
	{
		if (v == "cust")
		{
			var z = jQuery("#customer").val();
			var y = jQuery("#vehicle").val();
			var x = jQuery("#driver").val();
			var w = jQuery("#item_desc").val();
			
			if (z!=0)
			{
				jQuery("#trvehicle").show();
				jQuery("#btneditcust").show();
				jQuery("#customer").attr("disabled", "disabled");
			}
			
			if (y!=0)
			{
				jQuery("#trvehicle").show();
			}
			
			if (x!=0)
			{
				jQuery("#trdriver").show();
			}
			
			if (w!=0)
			{
				jQuery("#tritemdesc").show();
			}
		}
		
		if (v == "vehicle")
		{
			var z = jQuery("#vehicle").val();
			var x = jQuery("#driver").val();
			var w = jQuery("#item_desc").val();
			
			if (z!=0)
			{
				jQuery("#trdriver").show();
				jQuery("#btneditvehicle").show();
				jQuery("#vehicle").attr("disabled", "disabled");
			}
			
			if (x!=0)
			{
				jQuery("#trdriver").show();
			}
			
			if (w!=0)
			{
				jQuery("#tritemdesc").show();
			}
		}
		
		if (v == "driver")
		{
			var z = jQuery("#driver").val();
			var w = jQuery("#item_desc").val();
			if (z!=0)
			{
				jQuery("#tritemdesc").show();
				jQuery("#btneditdriver").show();
				jQuery("#driver").attr("disabled", "disabled");
			}
			
			if (w!=0)
			{
				jQuery("#tritemdesc").show();
			}
		}
	}
	
	function editselect(v)
	{
		switch(v)
		{
			case "editcustomer":
				jQuery("#trvehicle").hide();
				jQuery("#trdriver").hide();
				jQuery("#tritemdesc").hide();
				jQuery("#btneditcust").hide();
				jQuery("#customer").removeAttr("disabled");
			break;
			case "editvehicle":
				jQuery("#trdriver").hide();
				jQuery("#tritemdesc").hide();
				jQuery("#btneditvehicle").hide();
				jQuery("#vehicle").removeAttr("disabled");
			break;
			case "editdriver":
				jQuery("#tritemdesc").hide();
				jQuery("#btneditdriver").hide();
				jQuery("#driver").removeAttr("disabled");
			break;
		}
	}
		
</script>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
<?=$navigation;?>
	<div id="main" style="margin: 20px;">
		<div class="block-border">
			<form class="block-content form" id="frmdosj" onsubmit="javascript: return frmdosj_onsubmit(this)">				
			<input type="hidden" name="dosj_no" id="dosj_no" class="formdefault" />
            <h1><?php echo "Add SO"; ?></h1>
			
			<table width="100%" cellpadding="3" class="tablelist">
			
				<!-- DO Type -->
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">
						SO Type
					</td>
					<td style="border: 0px;">
						<select name="dosj_type" id="dosj_type">
							<option value="SO Mortar + Block">SO Mortar + Block</option>
							<option value="SO Mortar">SO Mortar</option>
							<option value="SO Block">SO Block</option>
						</select>
						<font color="red">*</font>
					</td>
				</tr>
				
                <tr><td>&nbsp;</td></tr>
				<!-- DO Number -->
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">
						SO Block No.
					</td>
					<td style="border: 0px;">
						<input type="text" name="dosj_no_block" id="dosj_no_block" class="formdefault" />
					</td>
				</tr>
				
                <tr><td>&nbsp;</td></tr>
                
                <tr style="border: 0px;">
                    <td width="100" style="border: 0px;">
                        SO Mortar No.
                    </td>
                    <td style="border: 0px;">
                        <input type="text" name="dosj_no_mortar" id="dosj_no_mortar" class="formdefault" />
                    </td>
                </tr>
                
                <tr><td>&nbsp;</td></tr>
				
                <!-- SJP Block -->
                <tr style="border: 0px;">
                    <td width="100" style="border: 0px;">SJP Block</td>
                    <td style="border: 0px;">
                        <input type="text" name="block_no" id="block_no" class="formdefault" />
                        <br />
                    </td>
                </tr>
                
                <tr><td>&nbsp;</td></tr>
                
				<!-- SJP Mortar -->
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">SJP Mortar</td>
					<td style="border: 0px;">
						<input type="text" name="mortar_no" id="mortar_no" class="formdefault" />
						<br />
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				<tr><td colspan="3"><hr /></td></tr>
				
				<tr>
					<td><hr /></td>
					<td colspan="2"><small>This Select Option is Searchable, Use key ESCAPE to abort your searching</small></td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				<tr><td>&nbsp;</td></tr>
				
				<!-- Customer -->
				<tr style="border: 0px;" id="trcustomer">
					<td width="100" style="border: 0px;">Customer</td>
					<td>
						<select name="customer" id="customer" onchange="return select_change('cust')">
							<option value="0">--</option>
							<?php
								if ((isset($customer)) && count($customer)>0)
								{
								for($i=0;$i<count($customer);$i++)
								{
							?>
								<option value="<?php echo $customer[$i]->group_id;?>"><?php echo $customer[$i]->group_name;?></option>
							<?php
								}
								}
							?>
						</select>
						<input type="button" name="btneditcust" id="btneditcust" onclick="javascript: return editselect('editcustomer');" value="Edit" />
						<font color="red">*</font>
						<small>
							<strong>FIRST : Select Customer to View Vehicle Field</strong>
						</small>
					</td>
				</tr>
                
				<tr><td>&nbsp;</td></tr>
				
				<!-- Vehicle -->
				<tr style="border: 0px;" id="trvehicle">
					<td style="border: 0px;">Vehicle</td>
					<td>
						<select name="vehicle" id="vehicle" onchange="return select_change('vehicle')">
							<option value="0">--</option>
							<?php
								if ((isset($vehicle)) && count($vehicle)>0)
								{
								for($i=0;$i<count($vehicle);$i++)
								{
							?>
								<option value="<?php echo $vehicle[$i]->vehicle_id."#".$vehicle[$i]->vehicle_device."#".$vehicle[$i]->vehicle_name."#".$vehicle[$i]->vehicle_no;?>">
								<?php echo $vehicle[$i]->vehicle_name." ".$vehicle[$i]->vehicle_no;?></option>
							<?php
								}
								}
							?>
						</select>
						<input type="button" name="btneditvehicle" id="btneditvehicle" onclick="javascript: return editselect('editvehicle');" value="Edit" />
						<font color="red">*</font>
						<small><strong>SECOND : Select Vehicle to View Driver Field</strong></small>
					</td>
				</tr>
                
				<tr><td>&nbsp;</td></tr>
				
				<!-- Driver -->
				<tr style="border: 0px;" id="trdriver">
					<td width="100" style="border: 0px;">Driver</td>
					<td>
						<select name="driver" id="driver" onchange="return select_change('driver')">
							<option value="0">--</option>
							<?php
								if ((isset($driver)) && count($driver)>0)
								{
								for($i=0;$i<count($driver);$i++)
								{
							?>
								<option value="<?php echo $driver[$i]->driver_id;?>"><?php echo $driver[$i]->driver_name;?></option>
							<?php
								}
								}
							?>
						</select>
						<input type="button" name="btneditdriver" id="btneditdriver" onclick="javascript: return editselect('editdriver');" value="Edit" />
						<font color="red">*</font>
						<small><strong>THIRD : Select Driver to View Item Description Field</strong>
						</small>
					</td>
				</tr>
                
				<tr><td>&nbsp;</td></tr>
				
				<!-- Item Description -->
				<tr style="border: 0px;" id="tritemdesc">
					<td width="100" style="border: 0px;">Item Description</td>
					<td style="border: 0px;">
						<!--<input size="50" type="text" name="item_desc" id="item_desc" class="formdefault" />-->
						<select name="item_desc" id="item_desc" >
							<option value="0">--</option>
							<option value="Mortar + Block">Mortar + Block</option>
							<option value="Mortar">Mortar</option>
							<option value="Block">Block</option>
						</select>
						<font color="red">*</font>
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				
				<tr><td colspan="3"><hr /></td></tr>
				
				<tr><td>&nbsp;</td></tr>
				
				<!-- Size -->
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">Item Size</td>
					<td style="border: 0px;">
						PxLxT <input type="text" size="25" maxlength="25" id="item_size" name="item_size" class="formdefault" />
						<!--P <input type="text" size="5" maxlength="5" id="item_panjang" name="item_panjang" class="formdefault" onKeyPress="return numbersonly(this, event)" />-->
						<!--L <input type="text" size="5" maxlength="5" id="item_lebar" name="item_lebar"  class="formdefault" onKeyPress="return numbersonly(this, event)" />-->
						<!--T <input type="text" size="5" maxlength="5" id="item_tinggi" name="item_tinggi"  class="formdefault" onKeyPress="return numbersonly(this, event)" />-->
						<!-- mm -->
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				
				<!-- Quantity -->
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">Total Quantity</td>
					<td style="border: 0px;">
						<input size="10" maxlength="10" type="text" name="item_quantity" id="item_quantity" class="formdefault" />
                        <br />
                        <small>Total Block</small>
                        <br /><br />
                        <input size="10" maxlength="10" type="text" name="item_quantity_mortar" id="item_quantity_mortar" class="formdefault" />
						<br />
                        <small>Total Mortar</small>
                        <!--On Ship <input size="10" maxlength="10" type="text" name="item_onship" id="item_onship" class="formdefault" />-->
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				
				<!-- Item Unit -->
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">Unit</td>
					<td style="border: 0px;">
						<input size="10" maxlength="10" type="text" name="item_unit" id="item_unit" class="formdefault" />
						M3
                        <input size="10" maxlength="10" type="text" name="item_unit_mortar" id="item_unit_mortar" class="formdefault" />
                        Sak
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				
				<!-- Ship Date -->
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">Ship Date</td>
					<td style="border: 0px;">
						<input size="10" maxlength="10" type="text" name="ship_date" id="ship_date" class="date-pick" />
						<font color="red">*</font>
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				
				<!-- Cost On Delivery -->
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">Dest. ( Cost )</td>
					<td style="border: 0px;">
						<select name="cost" id="cost">
							<option value="0" >--Select Destination--</option>
							<?php 
								if (isset($cost))
								{
									for($i=0;$i<count($cost);$i++)
									{
							?>
								<option value="<?php echo $cost[$i]->cost_id;?>">
									<?php echo $cost[$i]->destination_name." - ".$cost[$i]->cost_vehicle_type;?>
								</option>
							<?php
									}
								}
							?>
						</select>
						<font color="red">*</font>
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				
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
								<input type="button" name="btncancel" id="btncancel" value=" Cancel " onclick="location='<?=base_url()?>transporter/dosj';" />
								<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
						</td>
				</tr>					
				
				
				</table>
			</form>
		</div>
	</div>
</div>
			

			