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
		
		jQuery.post("<?=base_url();?>geofence_label/search/"+p, jQuery("#frmsearch").serialize(),
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
		jQuery("#type").hide();
		switch(v)
		{
			case "type":
				jQuery("#type").show();
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
				jQuery.post('<?=base_url()?>geofence_label/delete/' + id, {}, function(r){
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
		
	function edit(id)
		{
			showdialog();
			jQuery.post('<?=base_url()?>geofence_label/edit/' + id, {},
				function(r)
				{
					if (r.error)
					{
						alert("Retry");
						return;
					}
					
					showdialog(r.html, 'Edit Geofence Label');
				}
				, "json"
			);			
		}
	
</script>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
<?=$navigation;?>
	<div id="main" style="margin: 20px;">
		<div class="block-border">
		<form class="block-content form" name="frmsearch" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
		<h1>Goefence List (<span id="total"></span>)</h1>
		<fieldset class="grey-bg required">
		<legend><?=$this->lang->line("lsearchby");?></legend>
			<input type="hidden" name="offset" id="offset" value="" />
			<input type="hidden" id="sortby" name="sortby" value="" />
			<input type="hidden" id="orderby" name="orderby" value="" />			
			<table width="100%" cellpadding="3" class="tablelist">
				<tr>
					<td>
						<select id="field" name="field" onchange="javascript:field_onchange()">
							<option value="All">All</option>
							<option value="name">Geofence Name</option>
							<option value="type">Geofence Type</option>
						</select>
						<!--sales-->
						<select id="type" name="type" style="display: none;">
						<!--<option value="">--Select Sales--</option>-->
						<?php foreach($this->config->item("geofencetype") as $key=>$val) { ?>
						<option value="<?php echo $key; ?>"<?php echo (isset($_POST['geofencetype']) && ($key==$_POST['geofencetype'])) ? " selected" : "";?>><?php echo $val; ?></option>
						<?php } ?>						
						</select>
						
						<input type="text" name="keyword" id="keyword" value="" class="formdefault" />
						<input type="text" name="cp" id="cp" value="" class="formdefault" style="display: none;" />
						<input type="submit" value="<?=$this->lang->line("lsearch");?>" />
						<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
					</td>
				</tr>
			</table>
		</fieldset>
		</form>
		</div>
		<div id="result"></div>		
	</div>
</div>
