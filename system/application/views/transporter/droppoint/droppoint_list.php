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
			jQuery("#displayperiode").hide();
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
		jQuery.post("<?=base_url();?>droppoint/search/"+p, jQuery("#frmsearch").serialize(),
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
		jQuery("#unit").hide();
		switch(v)
		{
			case "budget_hist_working_unit" :
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
	function add()
		{
			showdialog();
			jQuery.post('<?=base_url()?>droppoint/add/', {},
				function(r)
				{
					if (r.error)
					{
						alert("Retry");
						return;
					}
					
					showdialog(r.html, 'Form Add Droppoint');
				}
				, "json"
			);			
		}
	
	function edit(id)
		{
			showdialog();
			jQuery.post('<?=base_url()?>droppoint/edit/' + id, {},
				function(r)
				{
					if (r.error)
					{
						alert("Retry");
						return;
					}
					
					showdialog(r.html, 'Form Edit Droppoint');
				}
				, "json"
			);			
		}
	function delete_data(id)
		{
			if (confirm("Are you sure delete this data?")) {
				jQuery.post('<?=base_url()?>droppoint/delete/' + id, {}, function(r){
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
		<h1>Droppoint List</h1>
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
							<option value="droppoint_name">Name</option>
							<option value="droppoint_code">Code</option>
						</select>
						
						<input type="text" name="keyword" id="keyword" value="" class="formdefault" /> 
						<input type="text" name="cp" id="cp" value="" class="formdefault" style="display: none;" />
					
						<!--<input type="radio" name="searchdate" id="alldate" value="all" checked="checked"/> All
						<input type="radio" name="searchdate" id="periodedate" value="periode" /> Periode
						
						<span id="displayperiode" style="display:none;">
						From <input type='text' name="startdate" id="startdate" class="date-pick" value=""  maxlength='10'>
						To <input type='text' name="enddate" id="enddate"  class="date-pick" value=""  maxlength='10'>
						</span>-->
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
	<?php if ($this->sess->user_group == 0) { ?>
		[ <a href="#" onclick="javascript:add()"><strong>ADD NEW</strong></font></a> ]
		<?php }?>
		<br />
		<div id="result"></div>
		<iframe id="frmexpense" style="display:none;"></iframe>		
	</div>
</div>
