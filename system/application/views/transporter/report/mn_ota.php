
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
		
		jQuery.post("<?=base_url();?>report_ota/search/", jQuery("#frmsearch").serialize(),
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
	
	
	function excel_onsubmit(){
		jQuery("#loader2").show();
		
		jQuery.post("<?=base_url();?>report_ota/export/", jQuery("#frmsearch").serialize(),
			function(r)
			{
				jQuery("#loader2").hide();
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
	
	function plant_onchange(){
		var data_plant = jQuery("#plant").val();
		if(data_plant == 0){
			alert('Please Select Plant!!');
			jQuery("#mn_company").hide();
			jQuery("#mn_parent").hide();
			jQuery("#mn_distrep").hide();
			jQuery("#company").html("<option value='0' selected='selected'>--Select Plant--</option>");
		}else{
			jQuery("#mn_company").show();
			jQuery("#mn_parent").hide();
			jQuery("#mn_distrep").hide();
			var site = "<?=base_url()?>report_ota/get_company_by_plant/" + data_plant;
		    jQuery.ajax({
		        url: site,
		        success: function(response){
					jQuery("#company").html("");
		            jQuery("#company").html(response);
		        },
		    	dataType:"html"
		    });

		}

	}
	
	function company_onchange(){
		var data_company = jQuery("#company").val();
		if(data_company == 0){
			alert('Please Select Group!!');
			jQuery("#mn_parent").hide();
			jQuery("#mn_distrep").hide();
			jQuery("#parent").html("<option value='0' selected='selected'>--Select Group--</option>");
		}else{
			jQuery("#mn_parent").show();
			jQuery("#mn_distrep").hide();
			var site = "<?=base_url()?>report_ota/get_parent_by_company/" + data_company;
		    jQuery.ajax({
		        url: site,
		        success: function(response){
					jQuery("#parent").html("");
		            jQuery("#parent").html(response);
		        },
		    	dataType:"html"
		    });

		}

	}
	
	function parent_onchange(){
		var data_parent = jQuery("#parent").val();
		if(data_parent == 0){
			alert('Please Select Group!!');
			jQuery("#mn_distrep").hide();
			jQuery("#distrep").html("<option value='0' selected='selected'>--Select Distrep--</option>");
		}else{
			jQuery("#mn_distrep").show();
			var site = "<?=base_url()?>report_ota/get_distrep_by_parent/" + data_parent;
		    jQuery.ajax({
		        url: site,
		        success: function(response){
					jQuery("#distrep").html("");
		            jQuery("#distrep").html(response);
		        },
		    	dataType:"html"
		    });

		}

	}
	
	function option_date(v)
		{
			switch(v)
			{
				case "bulan_ini":
					jQuery("#option_bulanini").show();
					jQuery("#option_bulanini_tr").show();
					jQuery("#option_bulanini_td").show();
					jQuery("#option_bulansemua").hide();
					jQuery("#option_bulansemua_tr").hide();
					jQuery("#option_bulansemua_td").hide();
				break;
				case "bulan_semua":
					jQuery("#option_bulanini").hide();
					jQuery("#option_bulanini_tr").hide();
					jQuery("#option_bulanini_td").hide();
					jQuery("#option_bulansemua").show();
					jQuery("#option_bulansemua_tr").show();
					jQuery("#option_bulansemua_td").show();
					
				break;
			}
		}
	

</script>
<?php 
	$nowdate = date("Y-m-d");
	$m = date("m");
	$Y = date("Y");
	$d = "1";
	$firstdate = date("Y-m-d", strtotime($Y."-".$m."-".$d));
?>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
<?=$navigation;?>
	<div id="main" style="margin: 20px;">
	<div class="block-border">
		<form class="block-content form" name="frmsearch" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
        <h1>OTA Report</h1>
			<input type="hidden" name="offset" id="offset" value="" />
			<input type="hidden" id="sortby" name="sortby" value="" />
			<input type="hidden" id="orderby" name="orderby" value="" />			
			<table width="70%" cellpadding="3" class="tablelist" style="font-size: 12px;">
				<tr>
					<td>Plant</td>
					<td>
						<select id="plant" name="plant" onchange="javascript:plant_onchange()">
							<option value="" selected='selected'>--Select Plant--</option>
							<?php 
								$cplant = count($rplant);
									for($i=0;$i<$cplant;$i++){
										if (isset($rplant)&&($row->plant_company == $rplant[$i]->plant_id)){
												$selected = "selected"; 
											}else{
												$selected = "";
											}
										echo "<option value='" . $rplant[$i]->plant_id ."' " . $selected . ">" . $rplant[$i]->plant_name . "</option>";
										}
							?>
						</select>
					</td>
				</tr>
			
				<tr id="mn_company" style="display:none">
					<td>Pool</td>
					<td>
						<select id="company" name="company" onchange="javascript:company_onchange()">
							<option value="" selected='selected'>--Select Pool--</option>
							<?php 
								$ccompany = count($rcompany);
									for($i=0;$i<$ccompany;$i++){
										if (isset($rcompany)&&($row->parent_company == $rcompany[$i]->company_id)){
												$selected = "selected"; 
											}else{
												$selected = "";
											}
										echo "<option value='" . $rcompany[$i]->company_id ."' " . $selected . ">" . $rcompany[$i]->company_name . "</option>";
										}
							?>
						</select>
					</td>
				</tr>
				
				<tr id="mn_parent" style="display:none">
					<td>Group</td>
					<td>
						<select id="parent" name="parent" onchange="javascript:parent_onchange()">
							<option value="" selected='selected'>--Select Group--</option>
							<?php 
								$cparent = count($rparent);
									for($i=0;$i<$cparent;$i++){
										if (isset($row)&&($row->distrep_parent == $rparent[$i]->parent_id)){
											$selected = "selected"; 
										}else{
											$selected = "";
										}
										echo "<option value='" . $rparent[$i]->parent_id ."' " . $selected . ">" . $rparent[$i]->parent_name . "</option>";
									}
							?>
						</select>
					</td>
				</tr>
				<tr id="mn_distrep" style="display:none">
					<td>Distrep</td>
					<td>
						<select id="distrep" name="distrep">
							<option value="" selected='selected'>--Select Distrep--</option>
							<?php 
								$cdistrep= count($rdistrep);
									for($i=0;$i<$cdistrep;$i++){
										if (isset($row)&&($row->distrep_parent == $rdistrep[$i]->distrep_id)){
											$selected = "selected"; 
										}else{
											$selected = "";
										}
										echo "<option value='" . $rdistrep[$i]->distrep_id ."' " . $selected . ">" . $rdistrep[$i]->distrep_name . "</option>";
									}
							?>
						</select>
					</td>
				</tr>
				<tr id="filterdatestartend">
					<td	>Periode</td>
					<td>
						<input type='text' readonly name="startdate" id="startdate" class="date-pick" value="<?=date('Y/m/d', strtotime("yesterday"));?>"  maxlength='10'>
						~ <input type='text' readonly name="enddate" id="enddate"  class="date-pick" value="<?=date('Y/m/d', strtotime("yesterday"));?>"  maxlength='10'>
					</td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr>
					<td style="border: 0px;"><input type="checkbox" name="checkdetail" id="checkdetail" value="1" checked /> View Detail</td>
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
