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
			jQuery("#startdate").datepicker(
				{
							dateFormat: 'dd-mm-yy'
						//, 	showOn: 'button'
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
							dateFormat: 'dd-mm-yy'
						//, 	showOn: 'button'
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
		
		jQuery.post("<?=base_url();?>mod_co/search_co/"+p, jQuery("#frmsearch").serialize(),
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
		jQuery("#destination_vehicle").hide();
		jQuery("#destination_driver").hide();
		jQuery("#startdate").hide();
		jQuery("#enddate").hide();
		jQuery("#lbldate").hide();
		
		switch(v)
		{
			case "destination_date":
				jQuery("#lbldate").show();
				jQuery("#startdate").show();
				jQuery("#enddate").show();
			break;
			case "destination_vehicle":
				jQuery("#destination_vehicle").show();
			break;
			case "destination_driver":
				jQuery("#destination_driver").show();
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
	
	function excel_onsubmit(){
		jQuery("#loader").show();
		
		jQuery.post("<?=base_url();?>mod_co/export_co", jQuery("#frmsearch").serialize(),
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
				jQuery.post('<?=base_url()?>mod_co/delete_co/' + id, {}, function(r){
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
		
	function edit(v)
	{
		showdialog();
		jQuery.post('<?php echo base_url(); ?>mod_co/edit_co/', {id: v},
		function(r)
		{
			showdialog(r.html, "Edit No. CO");
		}
		, "json"
		);
	}
	
	function show_upload()
	{
		jQuery("#upload").show("slow");
	}
</script>

<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
<?=$navigation;?>
	<div id="main" style="margin: 20px;">
		<div class="block-border">
		<form class="block-content form" name="frmsearch" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
		<h1>Manage CO Number (<span id="total"></span>)</h1>
		<fieldset class="grey-bg required">
		<legend><?=$this->lang->line("lsearchby");?></legend>
			<input type="hidden" name="offset" id="offset" value="" />
			<input type="hidden" id="sortby" name="sortby" value="" />
			<input type="hidden" id="orderby" name="orderby" value="" />			
			<table width="100%" cellpadding="3" class="tablelist">
                            <tr>
                                <td>
                                    <select id="field" name="field" onchange="javascript:field_onchange()">
                                        <option value="destination_name1">No. CO</option>
										<option value="destination_vehicle">Vehicle</option>
										<option value="destination_driver">Driver</option>
										<option value="destination_date">Date</option>
                                    </select>
									<select id="destination_vehicle" name="destination_vehicle" style="display:none;">
										<?php 
											if (isset($vehicle))
											{
												for($i=0;$i<count($vehicle);$i++)
												{
										?>
												<option value="<?php echo $vehicle[$i]->vehicle_device;?>">
													<?php echo $vehicle[$i]->vehicle_no;?>
												</option>
										<?php
												}
											}
										?>
									</select>
									<select id="destination_driver" name="destination_driver" style="display:none;">
										<?php 
											if (isset($driver))
											{
												for ($i=0;$i<count($driver);$i++)
												{
										?>
												<option value="<?php echo $driver[$i]->driver_id;?>">
													<?php echo $driver[$i]->driver_name;?>
												</option>
										<?php
												}
											}
										?>
									</select>
                                    <input type="text" name="keyword" id="keyword" value="" class="formdefault" />
									<input type="text" name="startdate" id="startdate" class="date-pick" style="display:none;"/>
									<span id="lbldate" name="lbldate" style="display:none;">s/d</span>
									<input type="text" name="enddate" id="enddate" class="date-pick" style="display:none;" />
                                    <input type="submit" value="<?=$this->lang->line("lsearch");?>" />
									<input class="btn_export" type="button" name="excel" id="btnexcelreport" value="Export To Excel" onclick="javascript:return excel_onsubmit()" />
                                    <img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
                                </td>
                            </tr>	
			</table>
		</fieldset>
		</form>		
		<br />
		[<a href="<?php echo base_url();?>mod_co/add_co">Add CO Number</a>]
		[<a href="javascript:show_upload();">Upload File</a>]
		<br /><br />
		<!-- Upload Menu -->
		<div id="upload" style="display:none;">
			<div class="block-border">
			<h2>Upload Data CO Number (*.txt)</h2>
			<hr />
			<?php echo form_open_multipart('mod_co/upload_co');?>
			<input type="file" id="file_upload" name="userfile" size="30" />
			<br /><br />
			<input type="submit" value="Upload" />
			<?php echo form_close();?>
		</div>
		</div>
		<!-- End Upload Menu -->
		</div>
		<div id="result"></div>		
		<iframe id="frmreq" style="display:none;"></iframe>
	</div>
</div>
</div>