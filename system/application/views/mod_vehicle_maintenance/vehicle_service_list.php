<script>
	
	jQuery(document).ready(
		function()
		{
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
		
		jQuery.post("<?=base_url();?>transporter/mod_vehicle_maintenance/search_service/"+p, jQuery("#frmsearch").serialize(),
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
		jQuery("#bymobil").hide();
		jQuery("#bydriver").hide();
		jQuery("#byworkshop").hide();
		jQuery("#byservicetype").hide();
		
		switch(v)
		{
			case "bymobil":
				jQuery("#bymobil").show();
				break;
			case "bydriver":
				jQuery("#bydriver").show();
				break;
			case "byworkshop":
				jQuery("#byworkshop").show();
				break;
			case "byservicetype":
				jQuery("#byservicetype").show();
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
	
	
	function service_edit(id)
		{
			showdialog();
			jQuery.post('<?=base_url()?>transporter/mod_vehicle_maintenance/mn_service_edit/' + id, {},
				function(r)
				{
					if (r.error)
					{
						alert(r.message);
						return;
					}
					
					showdialog(r.html, 'Service - Edit Information');
				}
				, "json"
			);			
		}
	
	function service_model(id)
		{
			showdialog();
			jQuery.post('<?=base_url()?>transporter/mod_vehicle_maintenance/service_model_detail/' + id, {},
				function(r)
				{
					if (r.error)
					{
						alert(r.message);
						return;
					}
					
					showdialog(r.html, 'Service Model - Detail Information');
				}
				, "json"
			);			
		}
		
	function delete_data(id)
		{
			if (confirm("Are you sure delete this data?")) {
				jQuery.post('<?=base_url()?>transporter/mod_vehicle_maintenance/delete_service/' + id, {}, function(r){
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
	
</script>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
<?=$navigation;?>
	<div id="main" style="margin: 20px;">
		<div class="block-border">
		<form class="block-content form" name="frmsearch" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
		<h1>Service List (<span id="total"></span>)</h1>
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
							<option value="service_invoice">Invoice</option>
							<option value="bymobil">Vehicle</option>
							<option value="bydriver">Driver</option>
							<option value="byworkshop">Workshop</option>
							<option value="byservicetype">Service Type</option>
						</select>
						<select id="bymobil" name="bymobil" style="display:none">
							<?php
								if (isset($vehicle)&&count($vehicle)>0)
								{
									for($i=0;$i<count($vehicle);$i++)
									{
							?>
								<option value="<?php echo $vehicle[$i]->mobil_id;?>" >
									<?php echo $vehicle[$i]->mobil_name." ".$vehicle[$i]->mobil_no;?>
								</option>
							<?php
									}
								}
							?>
						</select>
						<select id="bydriver" name="bydriver" style="display:none">
							<?php
								if (isset($driver)&&count($driver)>0)
								{
									for($i=0;$i<count($driver);$i++)
									{
							?>
								<option value="<?php echo $driver[$i]->driver_id;?>" >
									<?php echo $driver[$i]->driver_name;?>
								</option>
							<?php
									}
								}
							?>
						</select>
						<select id="byworkshop" name="byworkshop" style="display:none">
							<?php
								if (isset($workshop)&&count($workshop)>0)
								{
									for($i=0;$i<count($workshop);$i++)
									{
							?>
								<option value="<?php echo $workshop[$i]->workshop_id;?>" >
									<?php echo $workshop[$i]->workshop_name;?>
								</option>
							<?php
									}
								}
							?>
						</select>
						<select id="byservicetype" name="byservicetype" style="display:none">
							<?php
								if (isset($service_model)&&count($service_model)>0)
								{
									for($i=0;$i<count($service_model);$i++)
									{
							?>
								<option value="<?php echo $service_model[$i]->service_model_id;?>" >
									<?php echo $service_model[$i]->service_model;?>
								</option>
							<?php
									}
								}
							?>
						</select>
						<input type="text" name="keyword" id="keyword" value="" class="formdefault" />
						<input type="submit" value="<?=$this->lang->line("lsearch");?>" />
						<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
					</td>
				</tr>
			</table>
		</fieldset>
		</form>		
		<br />
		[ <a href="<?=base_url();?>transporter/mod_vehicle_maintenance/add_service"><font color="#0000ff">Add Service</font></a> ]
		</div>
		<div id="result"></div>		
	</div>
</div>
