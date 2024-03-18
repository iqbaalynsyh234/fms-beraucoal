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
		jQuery.post("<?=base_url()?>fib/saveuj", jQuery("#frmadd").serialize(),	
			function(r)
			{
				jQuery("#loader").hide();
				if (r.error)
				{
					alert(r.message);
					return false;
				}
				
				alert(r.message);
				jQuery("#uj_vehicle").val("");
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
		<h1>Add Uang Jalan</h1>
		<form name="frmadd" id="frmadd" onsubmit="javascript: return frmadd_onsubmit()">
			<input type="hidden" name="uj_id" id="uj_id" value="<?php if(isset($data)){ echo $data->uj_id; } ?>"/>
			<table width="100%" cellpadding="3" class="tablelist">
				<!--
				<tr>
					<td>SJ No : <input type="text" name="uj_no" id="uj_no"  size = "70" class="form-control"/></td>
				</tr>
				-->
				<tr>
					<td>
						Select Vehicle : 
						<select name="uj_vehicle" id="uj_vehicle" class="form-control" style="width: 400px"/>
							<option value="">Select Vehicle</option>
							<?php for($i=0;$i<count($vehicle);$i++) { ;?>
							<option value="<?php echo $vehicle[$i]->vehicle_device;?>" <?php if(isset($data)){ if($data->uj_vehicle == $vehicle[$i]->vehicle_device) { echo "selected"; } } ?> ><?php echo $vehicle[$i]->vehicle_no;?></option>
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
