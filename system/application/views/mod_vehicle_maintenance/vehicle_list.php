<script>
	
	jQuery(document).ready(
		function()
		{
			showclock();
			
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
		
		jQuery.post("<?=base_url();?>transporter/mod_vehicle_maintenance/search_vehicle/"+p, jQuery("#frmsearch").serialize(),
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
		jQuery("#status").hide();
		jQuery("#driver_additional").hide();
		jQuery("#lisence").hide();
		jQuery("#unit").hide();
		<?php 
			$app_route = $this->config->item("app_route");
			if (isset($app_route) && ($app_route == 1))
			{
		?>
			jQuery("#route").hide();
		<?
			}
		?>
		switch(v)
		{
			case "driver_status":
				jQuery("#status").show();
				break;
			case "driver_additional":
				jQuery("#driver_additional").show();
				break;
			case "driver_lisence_type":
				jQuery("#lisence").show();
				break;
			case "lisence_expired":
				jQuery("#keyword").hide();
				break;	
			case "driver_lisence_expired":
				jQuery("#keyword").hide();
				break;
			case "driver_working_unit" :
				jQuery("#unit").show();
				break;
			case "route" :
				jQuery("#route").show();
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
	
	function changephoto(id)
		{
			showdialog();
			jQuery.post('<?=base_url()?>fleet_driver/changephoto/' + id, {},
				function(r)
				{
					if (r.error)
					{
						alert("Retry");
						return;
					}
					
					showdialog(r.html, 'Change Photo');
				}
				, "json"
			);			
		}
	
	function vehicle_detail(id)
		{
			showdialog();
			jQuery.post('<?=base_url()?>transporter/mod_vehicle_maintenance/get_detail/' + id, {},
				function(r)
				{
					if (r.error)
					{
						alert(r.message);
						return;
					}
					
					showdialog(r.html, 'Vehicle - Detail Information');
				}
				, "json"
			);			
		}
	
	function vehicle_edit(id)
		{
			showdialog();
			jQuery.post('<?=base_url()?>transporter/mod_vehicle_maintenance/mn_vehicle_edit/' + id, {},
				function(r)
				{
					if (r.error)
					{
						alert(r.message);
						return;
					}
					
					showdialog(r.html, 'Vehicle - Edit Information');
				}
				, "json"
			);			
		}
		
	function delete_data(id)
		{
			if (confirm("Are you sure delete this data?")) {
				jQuery.post('<?=base_url()?>transporter/mod_vehicle_maintenance/delete_mobil/' + id, {}, function(r){
					if (r.error) {
						alert(r.message);
						return;
					}else{
						alert(r.message);
						page();
						return;
					}
				}, "json");
			}		
		}
		
	function excel_onsubmit(){
		jQuery("#loader").show();
		
		jQuery.post("<?=base_url();?>report/vehiclelist_excel/", jQuery("#frmsearch").serialize(),
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
	
</script>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
<?=$navigation;?>
	<div id="main" style="margin: 20px;">
		<div class="block-border">
		<form class="block-content form" name="frmsearch" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
		<h1>Vehicle List (<span id="total"></span>)</h1>
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
							<option value="all">All</option>
							<?php
								//$app_route = $this->config->item("app_route");
								//if (isset($app_route) && ($app_route == 1))
								//{
							?>
								<!--<option value="route">Route</option>-->
							<?php
								//}
							?>
							<option value="mobil_no">NoPol</option>
							<option value="mobil_name">Vehicle Name</option>
							<option value="mobil_model">Model</option>
							<option value="mobil_year">Year</option>
							<option value="mobil_insurance_no">Insurance</option>
							<option value="mobil_stnk_no">STNK</option>
							<option value="mobil_no_rangka">Rangka No.</option>
							<option value="mobil_no_mesin">Mesin No.</option>
							<option value="mobil_no_kir">KIR No.</option>
						</select>
						<?php
								$app_route = $this->config->item("app_route");
								if (isset($app_route) && ($app_route == 1))
								{
							?>
								<select name="route" id="route" style="display:none;">
								<?php
									if (isset($my_route) && (count($my_route)>0))
									{
									for ($j=0;$j<count($my_route);$j++)
									{
								?>
									<option value="<?php echo $my_route[$j]->route_id; ?>">
										<?php echo $my_route[$j]->route_name;?>
									</option>
								<?php
									}
									}
								?>
								</select>
						<?php
								}
						?>
						<input type="text" name="keyword" id="keyword" value="" class="formdefault" />
						<input type="text" name="cp" id="cp" value="" class="formdefault" style="display: none;" />
						<input type="submit" value="<?=$this->lang->line("lsearch");?>" />
						<input class="btn_export" type="button" name="excel" id="btnexcelreport" value="Export To Excel" onclick="javascript:return excel_onsubmit()" />
						<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
					</td>
				</tr>
					
			</table>
		</fieldset>
		</form>		
		<br />
		[ <a href="<?=base_url();?>transporter/mod_vehicle_maintenance/add_vehicle"><font color="#0000ff">Add Initializing Vehicle</font></a> ]
		[ <a href="<?=base_url();?>transporter/mod_vehicle_maintenance/service"><font color="#0000ff">Manage Service</font></a> ]
		[ <a href="<?=base_url();?>transporter/mod_vehicle_maintenance/workshop"><font color="#0000ff">Manage Workshop</font></a> ]
		[ <a href="<?=base_url();?>transporter/mod_vehicle_maintenance/mechanic"><font color="#0000ff">Manage Mechanic</font></a> ]
		</div>
		<div id="result"></div>		
		<iframe id="frmreq" style="display:none;"></iframe>
	</div>
</div>
