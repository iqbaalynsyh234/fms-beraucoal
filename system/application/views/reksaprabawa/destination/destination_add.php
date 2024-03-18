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
	
	function frmadd_onsubmit()
	{
		jQuery("#loader").show();
		jQuery.post("<?=base_url()?>destination/save", jQuery("#frmadd").serialize(),	
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
		<h1>Add New Destination</h1>
		<form name="frmadd" id="frmadd" onsubmit="javascript: return frmadd_onsubmit()">
			<table width="100%" cellpadding="3" class="tablelist">
				<tr>
					<td>Destination Name : <input type="text" name="destination_name1"  size = "70" /></td>
				</tr>
			
				<tr>
					<td>
						<input type="submit" name="btnsave" id="btnsave" value=" Save " />
						<input type="button" name="btncancel" id="btncancel" value=" Cancel " onclick="location='<?=base_url()?>destination';" />
						<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>