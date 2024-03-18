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
		jQuery.post("<?=base_url()?>transporter/trackrecord/save", jQuery("#frmadd").serialize(),	
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
		<h2>Add Track Record Driver</h2>
        <hr />
		<form name="frmadd" id="frmadd" onsubmit="javascript: return frmadd_onsubmit()">
			<table width="100%" cellpadding="3" class="tablelist">
				<tr>
                    <td>Driver</td>
                    <td>:</td>
                    <td>
                        <select name="driver" id="driver" >
                        <?php
                            for ($i=0;$i<count($driver);$i++)
                            {
                        ?>
                            <option value="<?php echo $driver[$i]->driver_id;?>">
                            <?php echo $driver[$i]->driver_name; ?>
                            </option>
                        <?php
                            }
                         ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Vehicle</td>
                    <td>:</td>
                    
                    <td>
                        <select name="vehicle" id="vehicle">
                        <?php
                            for($i=0;$i<count($vehicle);$i++)
                            {
                        ?>
                        <option value="<?php echo $vehicle[$i]->vehicle_id;?>">
                        <?php echo $vehicle[$i]->vehicle_name." ".$vehicle[$i]->vehicle_no; ?>
                        </option>
                        <?php 
                        }
                        ?>
                        </select>
                        <small></small>Note: Vehicles to choose form, is a vehicle that is not being used</small>
                    </td>
                </tr>
                <tr>
                    <td><input type="submit" name="submit" id="submit" value="Save" /></td>
                    <td><input type="button" name="btncancel" id="btncancel" value="Cancel" onclick="location='<?php echo base_url();?>transporter/driver'" /></td>
                    <td><img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;" /></td>
                </tr>
			</table>
		</form>
	</div>
</div>