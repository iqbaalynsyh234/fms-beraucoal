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
			jQuery("#startdate").datepicker(
				{
							dateFormat: 'dd-mm-yy'
						//, 	showOn: 'button'
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
							dateFormat: 'dd-mm-yy'
						//, 	showOn: 'button'
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
			field_onchange();
			page(0);			
		}
	);
	
	function page(p)
	{
		if(p==undefined){
			p=0;
		}
		jQuery("#offset").val(p);
		jQuery("#loader").show();
		
		jQuery.post("<?=base_url();?>transporter/tupperware/search_dr/"+p, jQuery("#frmsearch").serialize(),
			function(r)
			{
				jQuery("#loader").hide();
				jQuery("#result").html(r.html);		
				jQuery("#total").html(r.total);				
			}
			, "json"
		);
	}
	
	function field_onchange()
	{
		var v = jQuery("#field").val();

		jQuery("#keyword").hide();
		jQuery("#startdate").hide();
		jQuery("#enddate").hide();
		jQuery("#lbldate").hide();
		switch(v)
		{
			case "delivered":
				jQuery("#keyword").hide();			
			break;
			case "booking_date_in":
				jQuery("#lbldate").show();
				jQuery("#startdate").show();
				jQuery("#enddate").show();
			break;
			default:
				jQuery("#keyword").show();			
		}
	}
	
	function frmsearch_onsubmit()
	{
		page(0);
		return false;
	}
	
	function excel_onsubmit(){
		jQuery("#loader").show();
		
		jQuery.post("<?=base_url();?>report/export_dr", jQuery("#frmsearch").serialize(),
			function(r)
			{
				jQuery("#loader").hide();
				if(r.success == true){
					jQuery("#frmreq").attr("src", r.filename);			
				}else{
					alert(r.errMsg);
				}	
			}
			, "json"
		);
		
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

<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
<?=$navigation;?>
	<div id="main" style="margin: 20px;">
		<div class="block-border">
		<form class="block-content form" name="frmsearch" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
		<h1>DR/SO List (<span id="total"></span>)</h1>
		<fieldset class="grey-bg required">
		<legend><?=$this->lang->line("lsearchby");?></legend>
			<input type="hidden" name="offset" id="offset" value="" />
			<input type="hidden" id="sortby" name="sortby" value="" />
			<input type="hidden" id="orderby" name="orderby" value="" />			
			<table width="100%" cellpadding="3" class="tablelist">
                            <tr>
                                <td>
                                    <select id="field" name="field" onchange="javascript:field_onchange()">
                                        <option value="transporter_dr_so">SO</option>
										<option value="transporter_dr_dr">DR</option>
										<option value="dist_code">DB CODE</option>
										<option value="delivered">DELIVERED</option>
										<option value="booking_date_in">DATE ( ID BOOKING )</option>
                                    </select>
                                    <input type="text" name="keyword" id="keyword" value="" class="formdefault" />
									<input type="text" name="startdate" id="startdate" class="date-pick" style="display:none;"/>
									<span id="lbldate" name="lbldate" style="display:none;">s/d</span>
									<input type="text" name="enddate" id="enddate" class="date-pick" style="display:none;" />
                                    <input type="submit" value="<?=$this->lang->line("lsearch");?>" />
									<input class="btn_export" type="button" name="excel" id="btnexcelreport" value="Export To Excel" onclick="javascript:return excel_onsubmit()" />
                                    <img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
                                </td>
                            </tr>	
			</table>
		</fieldset>
		</form>		
		<br />
		</div>
		<div id="result"></div>		
		<iframe id="frmreq" style="display:none;"></iframe>
	</div>
</div>
</div>