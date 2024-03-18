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
		
		jQuery.post("<?=base_url();?>transporter/pgn_carpool/search_car_request/"+p, jQuery("#frmsearch").serialize(),
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
		switch(v)
		{
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
	
	function edit(v)
	{
		showdialog();
		jQuery.post('<?php echo base_url(); ?>transporter/pgn_carpool/request_edit/', {id: v},
		function(r)
		{
			showdialog(r.html, "Car Request Edit");
		}
		, "json"
		);
	}
	
	function detail(v)
	{
		showdialog();
		jQuery.post('<?php echo base_url(); ?>transporter/pgn_carpool/request_detail/', {id: v},
		function(r)
		{
			showdialog(r.html, "Car Request Detail");
		}
		, "json"
		);
	}
	
	function process_request(v)
	{
		showdialog();
		jQuery.post('<?php echo base_url(); ?>transporter/pgn_carpool/process_request/', {id: v},
		function(r)
		{
			showdialog(r.html, "Process Request");
		}
		, "json"
		);
	}
	
	function complete_request(id)
	{
		if (confirm("Are you sure Complete this Request Schedule ?")) {
				jQuery.post('<?=base_url()?>transporter/pgn_carpool/complete_request/' + id, {}, function(r){
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
	
	
	function delete_data(id)
		{
			if (confirm("Are you sure delete this data?")) {
				jQuery.post('<?=base_url()?>transporter/pgn_carpool/delete_request/' + id, {}, function(r){
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
		<h1>Car Request List (<span id="total"></span>)</h1>
		<fieldset class="grey-bg required">
		<legend><?=$this->lang->line("lsearchby");?></legend>
			<input type="hidden" name="offset" id="offset" value="" />
			<input type="hidden" id="sortby" name="sortby" value="" />
			<input type="hidden" id="orderby" name="orderby" value="" />			
			<table width="100%" cellpadding="3" class="tablelist">
				<tr>
					<td>
						<select id="field" name="field" onchange="javascript:field_onchange()">
							<option value="request_id">Request ID</option>
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
		[ <a href="<?=base_url();?>transporter/pgn_carpool/add_request"><font color="#0000ff">Add</font></a> ]
		</div>
		<div id="result"></div>		
	</div>
</div>
