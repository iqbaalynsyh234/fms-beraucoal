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
		//jQuery("#btnsearchreport").hide();
		jQuery("#result").hide();
		jQuery.post("<?=base_url();?>report_otl_otd/search/", jQuery("#frmsearch").serialize(),
			function(r)
			{
				jQuery("#loader2").hide();
				//jQuery("#btnsearchreport").show();
				if (r.error)
				{
					alert(r.message);
					return false;
				}
				jQuery("#result").show();
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
	
	function plant_onchange(){
		var data_plant = jQuery("#plant").val();
		if(data_plant == 0){
			alert('Please Select Plant!!');
			jQuery("#company").html("<option value='0' selected='selected'>--Select Pool--</option>");
		}else{
			var site = "<?=base_url()?>report_otl_otd/get_company_by_plant/" + data_plant;
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
			jQuery("#parent").html("<option value='0' selected='selected'>--Select Group--</option>");
		}else{
			var site = "<?=base_url()?>target_pergroup/get_parent_by_company/" + data_company;
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
			jQuery("#distrep").html("<option value='0' selected='selected'>--Select Distrep--</option>");
		}else{
			var site = "<?=base_url()?>target_pergroup/get_distrep_by_parent/" + data_parent;
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


	

</script>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
<?=$navigation;?>
	<div id="main" style="margin: 20px;">
	<div class="block-border">
		<form class="block-content form" name="frmsearch" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
        <h1>OTL & OTD Report</h1>
			<input type="hidden" name="offset" id="offset" value="" />
			<input type="hidden" id="sortby" name="sortby" value="" />
			<input type="hidden" id="orderby" name="orderby" value="" />			
			<table width="60%" cellpadding="3" class="tablelist" style="font-size: 12px;">
			<?php 
				$nowdate = date("Y-m-d");
				$m = date("m");
				$Y = date("Y");
				$d = "1";
				$firstdate = date("Y-m-d", strtotime($Y."-".$m."-".$d));
			?>
				<tr>
					<td>Plant</td>
					<td>
						<select id="plant" name="plant">
							<option value="" selected='selected'>--Select Plant--</option>
							<?php 
								$cplant = count($rplant);
									for($i=0;$i<$cplant;$i++){
										if (isset($rplant)&&($row->parent_company == $rplant[$i]->plant_id)){
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
				<tr id="filterdatestartend">
					<td	>Periode</td>
					<td>
						<input type='text' readonly name="startdate" id="startdate" class="date-pick" value="<?=date("Y/m/d", (strtotime($firstdate)));?>"  maxlength='10'>
						~ <input type='text' readonly name="enddate" id="enddate"  class="date-pick" value="<?=date('Y/m/t')?>"  maxlength='10'>
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
		
			
		<div id="result" /></div>
		
		<iframe id="frmreq" style="display:none;"></iframe>
       </div>
	</div>
</div>
