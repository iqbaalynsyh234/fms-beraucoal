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
		
		jQuery.post("<?=base_url();?>transporter/mod_vehicle_maintenance/search_workshop/"+p, jQuery("#frmsearch").serialize(),
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
	
	function workshop_edit(id)
		{
			showdialog();
			jQuery.post('<?=base_url()?>transporter/mod_vehicle_maintenance/mn_workshop_edit/' + id, {},
				function(r)
				{
					if (r.error)
					{
						alert(r.message);
						return;
					}
					
					showdialog(r.html, 'Workshop Edit');
				}
				, "json"
			);			
		}
		
	function delete_data(id)
		{
			if (confirm("Are you sure delete this data?")) {
				jQuery.post('<?=base_url()?>transporter/mod_vehicle_maintenance/delete_workshop/' + id, {}, function(r){
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
		<h1>Workshop List (<span id="total"></span>)</h1>
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
							<option value="vehicle_name">Workshop</option>
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
		[ <a href="<?=base_url();?>transporter/mod_vehicle_maintenance/add_workshop"><font color="#0000ff">Add Workshop</font></a> ]
		</div>
		<div id="result"></div>		
	</div>
</div>
