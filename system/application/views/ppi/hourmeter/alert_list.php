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
			/* jQuery("#alldate").attr("checked", true);
			jQuery("#startdate").datepicker(
				{
							dateFormat: 'yy/mm/dd'
						, 	startDate: '1900/01/01'
						, 	showOn: 'button'
						//, changeYear: true
						//,	changeMonth: true
						, 	buttonImage: '<?php echo base_url(); ?>assets/images/calendar.gif'
						, 	buttonImageOnly: true
						,	beforeShow: 
								function() 
								{	
									jQuery('#ui-datepicker-div').maxZIndex();
								}
				}
			);	

			
			jQuery("#sortby").val('settenant_id');
			jQuery("#orderby").val('desc') */
			
			//field_onchange();
			page(0);			
		}
	);
	
	function page(p)
	{
		if(p==undefined){
			p=0;
		}
		jQuery("#offset").val(p);
		jQuery("#loader2").show();
		jQuery.post("<?=base_url();?>transporter/ppi_hourmeter/search_alert/"+p, jQuery("#frmsearch").serialize(),
			function(r)
			{
				jQuery("#loader2").hide();
				jQuery("#result").html(r.html);		
				jQuery("#total").html(r.total);				
			}
			, "json"
		);
	}
	
	function field_onchange()
	{
		var v = jQuery("#field").val();

		jQuery("#keyword").show();
		
		/* switch(v)
		{
			default:
				jQuery("#keyword").show();			
		} */
	}
	
	function frmsearch_onsubmit()
	{
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
	
	/* function excel_onsubmit(){
		jQuery("#loader").show();
		jQuery.post("<?=base_url();?>transporter/ppi_hourmeter/export_to_excel_total_hourmeter/", jQuery("#frmsearch").serialize(),
			function(r)
			{
				if(r.success == true){
					jQuery("#loader").hide();
					jQuery("#frmexcel").attr("src", r.filename);			
				}else{
					alert(r.errMsg);
				}	
			}
			, "json"
		);
		
		return false;
	} */
</script>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
<?=$navigation;?>

	<div id="main" style="margin: 20px;">
		<div class="block-border">
		<form class="block-content form" name="frmsearch" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
		<h1>Alert Hourmeter List (<span id="total"></span>)</h1>
		<fieldset class="grey-bg required">
		<legend><?=$this->lang->line("lsearchby");?></legend>
			<input type="hidden" name="offset" id="offset" value="" />
			<input type="hidden" id="sortby" name="sortby" value="" />
			<input type="hidden" id="orderby" name="orderby" value="" />			
			<table width="100%" cellpadding="3" class="tablelist">
				<tr>
					<td width="10%"><?=$this->lang->line("lsearchby");?></td>
					<td>
						<select id="field" name="field" onchange="javascript:field_onchange()">
							<option value="hm_alert_vehicle_no">Vehicle No</option>
							<option value="hm_alert_vehicle_name">Vehicle Name</option>
						</select>
						
						<input type="text" name="keyword" id="keyword" value="" class="formdefault" />
						<input type="text" name="cp" id="cp" value="" class="formdefault" style="display: none;" />
						<input class="btn_search2" id="btnsearchreport" type="submit" value="Search" />
						<!--<input class="btn_export" type="button" name="excel" id="btnexcelreport" value="Export To Excel" onclick="javascript:return excel_onsubmit()" />-->
						<img id="loader2" style="display: none;" src="<?php echo base_url();?>assets/images/ajax-loader.gif" />
					
					</td>
				</tr>
			</table>
		</fieldset>
		</form>		
		<br />
	<!--	[ <a href="<?=base_url();?>transporter/ppi_hourmeter/add_hourmeter"><font color="#0000ff"><?php echo "Add Hourmeter" ?></font></a> ]-->
		</div>
		<div id="result"></div>
		<iframe id="frmexcel" style="display:none;"></iframe>		
	</div>
</div>
