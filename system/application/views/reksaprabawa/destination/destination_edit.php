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
		jQuery.post("<?=base_url()?>destination/update", jQuery("#frmedit").serialize(),	
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
<form id="frmedit" name="frmedit" method="post" action="<?=base_url()?>destination/update" class="wufoo topLabel page">
<header id="header" class="info"><h2>Edit Destination</h2></header>
<table width="100%" cellpadding="3" class="tablelist">
<tr>
	<td>Destination Name</td>
	<td>:</td>
	<td>
	<input name="destination_name1" type="text" value="<?=isset($row) ? htmlspecialchars($row->destination_name1, ENT_QUOTES) : "";?>" size="50" tabindex="1" />
	<input type="hidden" name="destination_id" id="destination_id"  value="<?php echo $row->destination_id?>" />
	</td>
	
	<tr>
	<td>Vehicle Name</td>
	<td>:</td>
	
	<td><?php echo $row->destination_vehicle;?></td>
	<!--<td><input name="destination_vehicle" type="text" value="<?=$row->destination_vehicle;?>" tabindex="2" /></td>-->
	</tr>
			
	
	<tr>
		<td colspan="3">
			<input id="saveForm" name="submit" class="btTxt submit" type="submit" value="Update" />
			<input id="btncancel" name="btncancel" class="btTxt submit" type="button" value="Cancel" onclick="location='<?=base_url()?>destination';" />
			<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
		</td>
	</tr>
</table>	
</form> 
	</div>
</div>