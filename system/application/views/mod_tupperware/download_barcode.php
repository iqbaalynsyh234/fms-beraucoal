<script type="text/javascript" src="<?=base_url();?>assets/kopindosat/js/ajaxfileupload.js"></script>
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
			jQuery("#sdate").datepicker(
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
			jQuery("#edate").datepicker(
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
	
	function downloadbarcode()
	{
		jQuery("#loader").show();
		jQuery.post("<?=base_url();?>transporter/tupperware/downloadbarcode", jQuery("#frmsearch").serialize());
	}
</script>

<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
<?=$navigation;?>
	<div id="main" style="margin: 20px;">
		<div class="block-border">
		<form class="block-content form" method="post" name="frmsearch" id="frmsearch" action="<?php echo base_url();?>transporter/tupperware/downloadbarcode">
		<h1>Barcode List</h1>
		<fieldset class="grey-bg required">
		<legend><?=$this->lang->line("lsearchby");?></legend>
			<input type="hidden" name="offset" id="offset" value="" />
			<input type="hidden" id="sortby" name="sortby" value="" />
			<input type="hidden" id="orderby" name="orderby" value="" />			
			<table width="100%" cellpadding="3" class="tablelist">
				<tr>
					<td>
						<select id="field" name="field" onchange="javascript:field_onchange()">
							<option value="barcode">Barcode No</option>
						</select>
						<select name="barcode" id="barcode">
							<option value="">Select Barcode</option>
							<?php 
								if (isset($barcode))
								{
									for ($i=0;$i<count($barcode);$i++)
									{
							?>
								<option value="<?php echo $barcode[$i]->booking_id;?>">
									<?php echo $barcode[$i]->booking_id; ?>
								</option>
							<?php
									}
								}
							?>
						</select>
						<br /><br />
						Start Date : <input type="text" name="sdate" id="sdate" class="date-pick" />
						<br /><br />
						Start Date : <input type="text" name="edate" id="edate" class="date-pick" />
						<br /><br />
						<input type="submit" value="<?="Download";?>" />
						<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
                    </td>
                </tr>	
			</table>
		</fieldset>
		</form>	
		<iframe id="frmreq" style="display:none;"></iframe>		
		</div>
	</div>
</div>
