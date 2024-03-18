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
			showclock();
			jQuery("#datetime").datepicker(
				{
							dateFormat: 'yy-mm-dd'
						, 	startDate: '1900-01-01'
						, 	showOn: 'button'
						, 	changeYear: true
						,	changeMonth: true
						, 	buttonImage: '<?php echo base_url();?>assets/images/calendar.gif'
						, 	buttonImageOnly: true
						,	beforeShow: 
								function() 
								{	
									jQuery('#ui-datepicker-div').maxZIndex();
								}
				}
			);
			
		}
	);
	
function frmsave_onsubmit()
	{
		jQuery("#loader").show();
		jQuery.post("<?=base_url()?>transporter/ppi_hourmeter/save_config", jQuery("#frmsave").serialize(),	
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
		<form class="block-content form" name="frmsave" id="frmsave" onsubmit="javascript: return frmsave_onsubmit()">		
			
		<h1>Add Config Hourmeter</h1>		
			
		<table width="100%" cellpadding="3" class="table sortable no-margin">
		<tr>
         <td colspan="4"><h2>Form Config Hourmeter</h2></td>
		 <!-- <td>
		    <input type="hidden" name="settenant_id" id="settenant_id" value="<?php echo $row->settenant_id?>" />
			
		  </td>-->
	
		</tr>
		<tr>
			  <td>Vehicle</td>
			  <td>:</td>
			  <td>
                <select id="vehicle_id" name="vehicle_id" >
                <option value="" selected="selected">--Select Vehicle--</option>
								<?php 
									$cvehicle = count($rvehicle);
									for($i=0;$i<$cvehicle;$i++){
										if(isset($row) && $rvehicle[$i]->vehicle_id == $row->data_hm_config_vehicle_id){
											
											$mselected = "selected='selected'";
										}else{
											$mselected = "";
										}
										
										echo "<option value='" . $rvehicle[$i]->vehicle_id ."' ". $mselected.">" . $rvehicle[$i]->vehicle_name ." - ".$rvehicle[$i]->vehicle_no . "</option>";
						
									} ?>
									
							</select></td>
		</tr>
		<tr>
          <td>Config Hourmeter ( per Hour )</td>
          <td>:</td>
		  <td>
			<input type="text" name="value" id="value" size="30" value= "" /></input>
		  </td>
        </tr>
		
    			<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>
								<input type="submit" name="btnsave" id="btnsave" value=" Save " />
								<input type="button" name="btncancel" id="btncancel" value=" Cancel " onclick="location='<?=base_url()?>transporter/ppi_hourmeter';" />
								<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
						</td>
					</tr>					
				</table>
			</form>		
		</div>
	</div>
</div>
			
