
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
			
			/*jQuery("#export_xcel").click(function() 
			{ 
				window.open('data:application/vnd.ms-excel,' + encodeURIComponent(jQuery('#isexport_xcel').html()));
			});*/
		}
	);
	
	function page(p)
	{
		if(p==undefined){
			p=0;
		}
		jQuery("#offset").val(p);
		//jQuery("#result").html('<img src="<?php echo base_url();?>assets/transporter/images/loader2.gif">');
		jQuery("#loader2").show();
		jQuery("#btnsearchreport").hide();
		
		jQuery.post("<?=base_url();?>history_hour_report/search/", jQuery("#frmsearch").serialize(),
			function(r)
			{
				jQuery("#loader2").hide();
				jQuery("#btnsearchreport").show();
				if (r.error)
				{
					alert(r.message);
					return false;
				}
				jQuery("#isexport_xcel").show();
				jQuery("#result").html(r.html);				
			}
			, "json"
		);
		 return false;
	}
	
	
	
	function frmsearch_onsubmit()
	{
		jQuery("#loader2").show();
		jQuery("#isexport_xcel").hide();
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
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
<?=$navigation;?>
	<div id="main" style="margin: 20px;">
	<div class="block-border">
		<form class="block-content form" name="frmsearch" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
        <h1>History (Hour) Report</h1>
			<input type="hidden" name="offset" id="offset" value="" />
			<input type="hidden" id="sortby" name="sortby" value="" />
			<input type="hidden" id="orderby" name="orderby" value="" />			
			<table width="60%" cellpadding="3" class="tablelist" style="font-size: 12px;">
			
				<tr>
					<td>Vehicle</td>
					<td>
						<select id="vehicle" name="vehicle">
							<option value="" selected='selected'>--All Vehicle--</option>
							<?php 
								$cvehicle = count($vehicles);
									for($i=0;$i<$cvehicle;$i++){
										if (isset($vehicles)&&($row->parent_company == $vehicles[$i]->vehicle_device)){
												$selected = "selected"; 
											}else{
												$selected = "";
											}
										echo "<option value='" . $vehicles[$i]->vehicle_device ."' " . $selected . ">" . $vehicles[$i]->vehicle_no ." - ".$vehicles[$i]->vehicle_name. "</option>";
										}
							?>
						</select>
					</td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				
				<tr id="filterdatestartend">
					<td	>Periode</td>
					<td>
						<input type='text' readonly name="startdate" id="startdate" class="date-pick" value="<?=date('Y/m/d')?>"  maxlength='10'>
						~ <input type='text' readonly name="enddate" id="enddate"  class="date-pick" value="<?=date('Y/m/d')?>"  maxlength='10'>
					</td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr>
					<td style="border: 0px;"><input type="checkbox" name="checkdetail" id="checkdetail" value="1" /> View Detail</td>
					<td style="border: 0px;"><input class="btn_search2" id="btnsearchreport" name="btnsearchreport" type="submit" value="Search" />
						<img id="loader2" style="display: none;" src="<?php echo base_url();?>assets/images/ajax-loader.gif" />
					</td>
				</tr>
			</table>
		</form>		
		<br />
		<div id="result" style="width:100%;height:100%;line-height:3em;overflow:scroll;padding:5px;" /></div>
		<iframe id="frmreq" style="display:none;"></iframe>
        </div>
	</div>
</div>
