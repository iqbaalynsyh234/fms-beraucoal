<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/plugins/select2/select2.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/plugins/select2/select2-metronic.css"/>
<script src="<?php echo base_url();?>assets/plugins/jquery-1.10.2.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/plugins/select2/select2.min.js"></script>
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
			$('select').select2();
			showclock();
			
		}
	);
	
	function frmadd_onsubmit()
	{
		jQuery("#loader").show();
		jQuery.post("<?=base_url()?>fib/savesj", jQuery("#frmadd").serialize(),	
			function(r)
			{
				jQuery("#loader").hide();
				if (r.error)
				{
					alert(r.message);
					return false;
				}
				
				alert(r.message);
				jQuery("#sj_no").val("");
				jQuery("#sj_vehicle").val("");
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
		<h1>Add Surat Jalan</h1>
		<form name="frmadd" id="frmadd" onsubmit="javascript: return frmadd_onsubmit()">
			<input type="hidden" name="sj_id" id="sj_id" value="<?php if(isset($data)){ echo $data->sj_id; } ?>"/>
			<table width="100%" cellpadding="3" class="tablelist">
				<tr>
					<td>SJ No : <input type="text" name="sj_no" id="sj_no"  size = "70" class="form-control"/></td>
				</tr>
				<tr>
					<td>
						Vehicle : 
						<select name="sj_vehicle" id="sj_vehicle" class="form-control" style="width: 400px"/>
							<option value="">Select Vehicle</option>
							<?php for($i=0;$i<count($vehicle);$i++) { ;?>
							<option value="<?php echo $vehicle[$i]->vehicle_device;?>" <?php if(isset($data)){ if($data->sj_vehicle == $vehicle[$i]->vehicle_device) { echo "selected"; } } ?> ><?php echo $vehicle[$i]->vehicle_no;?></option>
							<?php }?>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						Catatan : Customer pertama yang dipilih adalah urutan kiriman pertama dan seterusnya.
					</td>
				</tr>
				<tr>
					<td>
						Customer 1 : 
						<select name="sj_cust_1_code" id="sj_cust_1_code" class="form-control" style="width: 400px"/>
							<option value="">Select Customer</option>
							<?php for($i=0;$i<count($customer);$i++) { ;?>
							<option value="<?php echo $customer[$i]->customer_code;?>" <?php if(isset($data)){ if($data->sj_cust_1_code == $customer[$i]->customer_code) { echo "selected"; } } ?> ><?php echo $customer[$i]->customer_name." (".$customer[$i]->customer_code.")";?></option>
							<?php }?>
						</select>
					</td>
				</tr>
				
				<tr>
					<td>
						Customer 2 : 
						<select name="sj_cust_2_code" id="sj_cust_2_code" class="form-control" style="width: 400px"/>
							<option value="">Select Customer</option>
							<?php for($i=0;$i<count($customer);$i++) { ;?>
							<option value="<?php echo $customer[$i]->customer_code;?>" <?php if(isset($data)){ if($data->sj_cust_2_code == $customer[$i]->customer_code) { echo "selected"; } } ?> ><?php echo $customer[$i]->customer_name." (".$customer[$i]->customer_code.")";?></option>
							<?php }?>
						</select>
					</td>
				</tr>
				
				<tr>
					<td>
						Customer 3 : 
						<select name="sj_cust_3_code" id="sj_cust_3_code" class="form-control" style="width: 400px"/>
							<option value="">Select Customer</option>
							<?php for($i=0;$i<count($customer);$i++) { ;?>
							<option value="<?php echo $customer[$i]->customer_code;?>" <?php if(isset($data)){ if($data->sj_cust_3_code == $customer[$i]->customer_code) { echo "selected"; } } ?> ><?php echo $customer[$i]->customer_name." (".$customer[$i]->customer_code.")";?></option>
							<?php }?>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<input type="submit" name="btnsave" id="btnsave" value=" Save " />
						<button type="reset">Reset</reset>
						<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>
