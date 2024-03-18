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
			jQuery("#sdate").datepicker(
				{
							dateFormat: 'dd-mm-yy'
						, 	showOn: 'button'
						, 	buttonImage: '<?=base_url()?>assets/images/calendar.gif'
						, 	buttonImageOnly: true
						,	beforeShow: 
								function() 
								{	
									jQuery('#ui-datepicker-div').maxZIndex();
								}
				}
				);	
			
			jQuery("#edate").datepicker(
				{
							dateFormat: 'dd-mm-yy'
						, 	showOn: 'button'
						, 	buttonImage: '<?=base_url()?>assets/images/calendar.gif'
						, 	buttonImageOnly: true
						,	beforeShow: 
								function() 
								{	
									jQuery('#ui-datepicker-div').maxZIndex();
								}
				}
				);	
				
			showclock();
			jQuery("#sortby").val('<?=$sortby?>');
			jQuery("#orderby").val('<?=$orderby?>')
			field_onchange();
			page(0);			
		}
	);
	
	function field_onchange()
	{
		var v = jQuery("#field").val();

		jQuery("#keyword").hide();
		jQuery("#customer").hide();		

		switch(v)
		{
			
			case "customer":
				jQuery("#customer").show();
			break;			
			default:
				jQuery("#keyword").show();			
		}
	}
	
	function page(p)
	{		
		if(p==undefined){
			p=0;
		}
		jQuery("#offset").val(p);
		jQuery("#listresult").html("<?=$this->lang->line('lwait_loading_data');?>");
		jQuery.post("<?=base_url()?>transporter/dosj_all/search/"+p, jQuery("#frmsearch").serialize(), 
			function(r)
			{
				jQuery("#listresult").html(r.html);
				jQuery("#total").html(r.total);				
			}
			, "json"
		);
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
	
	function dosj_history(v)
	{
		showdialog();
		jQuery.post('<?php echo base_url(); ?>/transporter/dosj_all/dosj_history/', {id: v},
		function(r)
		{
			showdialog(r.html, "Sales Order");
		}
		, "json"
		);
	}
	
	function dosj_edit(v)
	{
		showdialog();
		jQuery.post('<?php echo base_url(); ?>/transporter/dosj_all/dosj_edit/', {id: v},
		function(r)
		{
			showdialog(r.html, "Sales Order Edit");
		}
		, "json"
		);
	}
	
	function dosj_delete(v)
	{
		showdialog();
		jQuery.post('<?php echo base_url(); ?>/transporter/dosj_all/dosj_delete/', {id: v},
		function(r)
		{
			showdialog(r.html, "Delete SO/SJ !");
		}
		, "json"
		);
	}
        
        function excel_onsubmit(){
		jQuery("#loader2").show();
		
		jQuery.post("<?=base_url();?>export_dosj_all/dosj", jQuery("#frmsearch").serialize(),
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

		
</script>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
	<?=$navigation;?>
	<div id="main" style="margin: 20px;">
		<div class="block-border">
			<form class="block-content form" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
				<h1><?php echo "SO List :";?> (<span id="total"></span>)</h1>
				<h2><?=$this->lang->line("lsearch"); ?></h2>		
				<table cellpadding="10" class="tablelist">
					<tr>
						<td><?php echo "Search By"; ?></td>
						<td>&nbsp;</td>
						<td>
							<select name="field" id="field" onchange="javascript:field_onchange()">
								<option value="all">ALL</option>
								<option value="dosj_no">SO Number</option>
								<option value="customer">Customer</option>
							</select>
						</td>
						<td>&nbsp;</td>
						<td><input type="text" name="keyword" id="keyword" value="" class="formdefault" /></td>
						<td>
							<select name="customer" id="customer" style="display:none" >
								<?php
									if (isset($customer) && (count($customer)>0))
									{
										for ($j=0;$j<count($customer);$j++)
										{
								?>
										<option value="<?php echo $customer[$j]->group_id;?>">
											<?php echo $customer[$j]->group_name;?>
										</option>
								<?php
										}
									}
								?>
							</select>
						</td>
						<td>&nbsp;</td>
						<td>
                            <input type="submit" value="<?=$this->lang->line("lsearch");?>" />
                            <input class="btn_export" type="button" name="excel" id="btnexcelreport" value="Export To Excel" onclick="javascript:return excel_onsubmit()" />
                            <img id="loader2" style="display: none;" src="<?php echo base_url();?>assets/images/ajax-loader.gif" />
                        </td>
					</tr>				
				</table>
				<input type="hidden" id="sortby" name="sortby" value="" />
				<input type="hidden" id="orderby" name="orderby" value="" />
                <input type="hidden" id="offset" name="offset" value="" />
			</form>
			<br />
			<a href="<?=base_url();?>transporter/dosj_all/add"><font color="#0000ff">[ Add ]</font></a>	
			<!--<a href="<?=base_url();?>transporter/dosj_all/mn_manage_do"><font color="#0000ff">[ Manage SO ]</font></a>-->
			<!--<a href="<?=base_url();?>transporter/dosj_all/mn_upload_dosj"><font color="#0000ff">[ Import From Excel ]</font></a>-->
			<!--<a href="<?=base_url();?>transporter/dosj_all/mn_download_dosj_template"><font color="#0000ff">[ Download Sample/Template ]</font></a>-->
		</div>
		<div id="listresult"></div>			
        <iframe id="frmreq" style="display:none;"></iframe>
	</div>
</div>
