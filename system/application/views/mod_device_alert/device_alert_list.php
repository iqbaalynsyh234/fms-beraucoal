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
			jQuery("#alldate").attr("checked", true);
			jQuery("#displayperiode").show();
			jQuery("#startdate").datepicker(
				{
							dateFormat: 'yy-mm-dd'
						, 	startDate: '1900-01-01'
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
							dateFormat: 'yy-mm-dd'
						, 	startDate: '1900-01-01'
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
			jQuery("#periodedate").click(function(){
				jQuery("#startdate").attr("value", "");
				jQuery("#enddate").attr("value", "");
				jQuery("#displayperiode").show();
			});	
			jQuery("#alldate").click(function(){
				jQuery("#displayperiode").hide();
			});
			
			jQuery("#sortby").val('<?=$sortby?>');
			jQuery("#orderby").val('<?=$orderby?>')
			
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
		jQuery.post("<?=base_url();?>mod_device_alert/search/"+p, jQuery("#frmsearch").serialize(),
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
		jQuery("#distrep2").hide();
		switch(v)
		{
			case "mod_device_alert_distrep" :
				jQuery("#distrep2").show();
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
	
	function delete_data(id)
		{
			if (confirm("Are you sure delete this data?")) {
				jQuery.post('<?=base_url()?>mod_device_alert/delete/' + id, {}, function(r){
					if (r.error) {
						alert(r.message);
						return;
					}else{
						alert(r.message);
						page(0);
						return;
					}
				}, "json");
			}		
		}
</script>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
<?=$navigation;?>
	<div id="main" style="margin: 20px;">
		<div class="block-border">
		<form name="frmsearch" class="block-content form" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
		<h1>Device Alert List <!--(<span id="total"></span>)--></h1>
		<fieldset class="grey-bg required">
		<legend><?=$this->lang->line("lsearchby");?></legend>
			<input type="hidden" name="offset" id="offset" value="" />
			<input type="hidden" id="sortby" name="sortby" value="" />
			<input type="hidden" id="orderby" name="orderby" value="" />			
			<table width="100%" cellpadding="3" class="tablelist">
				<tr><td>&nbsp;</td></tr>
				<tr>
					<td>
						<select id="field" name="field" onchange="javascript:field_onchange()">
							<option value="alert_vehicle_no">Vehicle No</option>
							<option value="alert_vehicle_name">Vehicle Name</option>
						</select>
						
						<!--<select id="distrep2" name="distrep2" style="display: none;">
								<option value="" selected='selected'>--Select Distrep--</option>
								<?php 
									$cdistrep = count($rdistrep);

									for($i=0;$i<$cdistrep;$i++){										
										echo "<option value='" . $rdistrep[$i]->distrep_id ."'>" . $rdistrep[$i]->distrep_name . "</option>";
									}
								?>
						</select>-->
						
						<input type="text" name="keyword" id="keyword" value="" class="formdefault" /> 
						<input type="text" name="cp" id="cp" value="" class="formdefault" style="display: none;" />
						
						<!--<input type="radio" name="searchdate" id="alldate" value="all" checked="checked"/> This Month -->
						<input type="radio" name="searchdate" id="periodedate" value="periode" checked="checked" /> Periode
						
						<span id="displayperiode">
						From <input type='text' name="startdate" id="startdate" class="date-pick" value="<?php echo date("Y-m-d");?>"  maxlength='10'>
						To <input type='text' name="enddate" id="enddate"  class="date-pick" value="<?php echo date("Y-m-d");?>"  maxlength='10'>
						</span>
						<input type="submit" value="<?=$this->lang->line("lsearch");?>" />
						<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
					</td>
					
				</tr>
				
			</table>
		</legend>
		</form>
		<br />
	</div>
	<br />
	
		<div id="result"></div>
		<iframe id="frmexpense" style="display:none;"></iframe>
		<br />		
	</div>
</div>
