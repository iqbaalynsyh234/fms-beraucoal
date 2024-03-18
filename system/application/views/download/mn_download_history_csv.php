<script src="<?php echo base_url()?>assets/js/assetsbaru/jquery.min.js"></script>
<!-- <link rel="stylesheet" href="<?php echo base_url()?>assets/js/assetsbaru/jquery-ui.css"> -->
<script src="<?php echo base_url()?>assets/js/assetsbaru/jquery-ui.min.js"></script>
<script src="<?php echo base_url()?>assets/js/assetsbaru/chosen.jquery.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url()?>assets/js/assetsbaru/chosen.min.css">
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
			jQuery("#date").datepicker(
				{
							dateFormat: 'yy/mm/dd'
						, 	startDate: '1900/01/01'
						, 	showOn: 'button'
						//, 	changeYear: true
						//,	changeMonth: true
						, 	buttonImage: '<?=base_url()?>assets/images/calendar.gif'
						, 	buttonImageOnly: true
						,	beforeShow: 
								function() 
								{	
									jQuery('#ui-datepicker-div').maxZIndex();
								}
				}
			);
			jQuery("#startdate").datepicker(
				{
							dateFormat: 'yy/mm/dd'
						, 	startDate: '1900/01/01'
						, 	showOn: 'button'
						//, 	changeYear: true
						//,	changeMonth: true
						, 	buttonImage: '<?=base_url()?>assets/images/calendar.gif'
						, 	buttonImageOnly: true
						,	beforeShow: 
								function() 
								{	
									jQuery('#ui-datepicker-div').maxZIndex();
								}
				}
			);	

			jQuery("#enddate").datepicker(
				{
							dateFormat: 'yy/mm/dd'
						, 	startDate: '1900/01/01'
						, 	showOn: 'button'
						//, 	changeYear: true
						//,	changeMonth: true
						, 	buttonImage: '<?=base_url()?>assets/images/calendar.gif'
						, 	buttonImageOnly: true
						,	beforeShow: 
								function() 
								{	
									jQuery('#ui-datepicker-div').maxZIndex();
								}
				}
			);
			jQuery("#histstartdate").datepicker(
				{
							dateFormat: 'yy/mm/dd'
						, 	startDate: '1900/01/01'
						, 	showOn: 'button'
						//, 	changeYear: true
						//,	changeMonth: true
						, 	buttonImage: '<?=base_url()?>assets/images/calendar.gif'
						, 	buttonImageOnly: true
						,	beforeShow: 
								function() 
								{	
									jQuery('#ui-datepicker-div').maxZIndex();
								}
				}
			);	

			jQuery("#histenddate").datepicker(
				{
							dateFormat: 'yy/mm/dd'
						, 	startDate: '1900/01/01'
						, 	showOn: 'button'
						//, 	changeYear: true
						//,	changeMonth: true
						, 	buttonImage: '<?=base_url()?>assets/images/calendar.gif'
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
	
	function page(p)
	{
		if(p==undefined){
			p=0;
		}
		jQuery("#offset").val(p);
		jQuery("#result").html('<img src="<?php echo base_url();?>assets/transporter/images/loader2.gif">');
		jQuery("#loader").show();
		jQuery.post("<?=base_url();?>download/download_historycsv/", jQuery("#frmsearch").serialize(),
			function(r)
			{
				jQuery("#loader").hide();
				jQuery("#result").html(r.html);				
			}
			, "json"
		);
	}
	
	function frmsearch_onsubmit()
	{
		jQuery("#loader").show();
		page(0);
		return false;
	}
	
	
	function order(by)
	{						
		if (by == jQuery("#sortby").val())
		{
			if (jQuery("#orderby").val() == "asc")
			{
				jQuery("#orderby").val("desc");
			}
			else
			{
				jQuery("#orderby").val("asc");
			}
		}
		else
		{
			jQuery("#orderby").val('asc')
		}
		
		jQuery("#sortby").val(by);
		page(0);
	}
	
</script>
<style media="screen">
	.ui-datepicker{
		z-index: 9999 !important;
	}

</style>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
<?=$navigation;?>
	<div id="main" style="margin: 20px;">
	<div class="block-border">
	
		<form class="block-content form" name="frmsearch" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
			
			<h1>Download History Report (CSV)</h1>
				<input type="hidden" name="offset" id="offset" value="" />
				<input type="hidden" id="sortby" name="sortby" value="" />
				<input type="hidden" id="orderby" name="orderby" value="" />
				<input type="hidden" id="userid" name="userid" value="<?=$this->sess->user_id;?>" />					
				<input type="hidden" id="usercompany" name="usercompany" value="<?=$this->sess->user_company;?>" />			
				<table width="100%" cellpadding="3" class="tablelist" style="font-size: 12px;">
					<tr>
		
						<td>Vehicle</td>
						<td>
							<select id="vehicle" name="vehicle" class="chosen" style="width:30%;">
								<option value="all">All</option>		
								<?php for($i=0; $i < count($vehicles); $i++) { ?>									
								<option value="<?php echo $vehicles[$i]->vehicle_device; ?>"><?php if ($this->sess->user_type != 2) { echo $vehicles[$i]->user_name." - "; } ?><?php echo $vehicles[$i]->vehicle_no; ?> - <?php echo $vehicles[$i]->vehicle_name; ?></option>
								<?php } ?>							
							</select>
						</td>
					</tr>
					<tr id="filterdatestartend">
						<td width="10%">Date</td>
						<td>
							<input type='text' readonly name="startdate" id="startdate" class="date-pick" value="<?=date('Y/m/d',strtotime('yesterday'));?>"  maxlength='10'>
							~ <input type='text' readonly name="enddate" id="enddate"  class="date-pick" value="<?=date('Y/m/d',strtotime('yesterday'));?>"  maxlength='10'>
						</td>
					</tr>
					
					<tr>
						<td style="border: 0px;">&nbsp;</td>
						<td style="border: 0px;"><input class="btn_search2" id="btnsearchreport" type="submit" value="Search" />
						<img id="loader2" style="display: none;" src="<?php echo base_url();?>assets/images/ajax-loader.gif" />
						</td>
					</tr>
					
				</table>
				
		</form>		
		
		<br />
		<div id="result"></div>		
		<iframe id="frmreq" style="display:none;"></iframe>	
	</div>
    </div>
</div>
<script type="text/javascript">
	$(".chosen").chosen();
</script>