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
		
		jQuery.post("<?=base_url();?>transporter/mod_vehicle_maintenance/search_alert_service/"+p, jQuery("#frmsearch").serialize(),
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
	
	
	function service_delete_data(v)
	{
		if (confirm("Are you sure delete this data.?")) {
				jQuery.post('<?=base_url()?>transporter/mod_vehicle_maintenance/service_delete/', {id: v}, 
				function(r){
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
		<h1>Alert Service List (<span id="total"></span>)</h1>
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
							<option value="bymobil">Vehicle</option>
						</select>
						<select id="bymobil" name="bymobil" style="display:none">
							<?php
								if (isset($vehicle)&&count($vehicle)>0)
								{
									for($i=0;$i<count($vehicle);$i++)
									{
							?>
								<option value="<?php echo $vehicle[$i]->mobil_device;?>" >
									<?php echo $vehicle[$i]->mobil_name." ".$vehicle[$i]->mobil_no;?>
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
