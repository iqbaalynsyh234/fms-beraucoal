<script type="text/javascript" src="js/script.js"></script>
<script src="<?php echo base_url()?>assets/dashboard/assets/js/jquery-1.7.1.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/js/jsblong/jquery.table2excel.js"></script>
<script>
jQuery(document).ready(
		function()
		{
			jQuery("#export_xcel").click(function() 
			{ 
				window.open('data:application/vnd.ms-excel,' + encodeURIComponent(jQuery('#isexport_xcel').html()));
			});
		}
	);
</script>
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
		jQuery.post("<?=base_url();?>manage/searchvehicle/"+p, jQuery("#frmsearch").serialize(),
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
		jQuery("#company").hide();
		jQuery("#subcompany").hide();
		jQuery("#group").hide();
		jQuery("#subgroup").hide();
		switch(v)
		{
			case "company" :
				jQuery("#company").show();
			break;
			case "subcompany" :
				jQuery("#subcompany").show();
			break;
			case "group" :
				jQuery("#group").show();
			break;
			case "subgroup" :
				jQuery("#subgroup").show();
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
	
	function frmadd_onsubmit()
	{
		jQuery("#loader2").show();
		jQuery.post("<?=base_url()?>manage/savevehicle", jQuery("#frmadd").serialize(),
		function(r)
		{
			jQuery("#loader2").hide();
			alert(r.message);
									
									if (r.error)
									{								
										return;									
									}								
									page();
								//	jQuery("#dialog").dialog("close");
								}
								, "json"
							);
							
							return false;
		
	}
	
</script>
<!-- start sidebar menu -->
 			<div class="sidebar-container">
 				<?=$sidebar;?>
            </div>
<!-- end sidebar menu -->

<!-- start page content -->
            <div class="page-content-wrapper">
                <div class="page-content">
                    <div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title">Manage</div>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                                <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?=base_url();?>dashboard">Dashboard</a>&nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                                <li>Manage &nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                                <li class="active"><a class="parent-item" href="">Vehicle</a></li>
                            </ol>
                        </div>
                    </div>
                     <div class="row">
                    	<div class="col-sm-12">
                             <div class="card-box">
							  
								<div class="panel" id="panel_form">
                                <header class="panel-heading panel-heading-blue">Total Data (<span id="total"></span>)</header>
								  <div class="card-body ">
									<form name="frmsearch" class="block-content form" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
										<input type="hidden" name="offset" id="offset" value="" />
										<input type="hidden" id="sortby" name="sortby" value="" />
										<input type="hidden" id="orderby" name="orderby" value="" />			
										<table>
											<tr><td>&nbsp;</td></tr>
											<tr>
											
												<td>
												<div class="row">
												<div class="col-lg-12 col-sm-12">
													<select id="field" name="field" onchange="javascript:field_onchange()">
														<option value="vehicle_no"><small>Vehicle No</option>
														<option value="vehicle_name"><small>Vehicle Name</option>
														<option value="company"><small>Area</option>
													</select>
													
													<select id="company" name="company" style="display: none;">
															<option value="" selected='selected'><small>--Select Company--</option>
															<?php 
																$ccompany = count($companyall);
																for($i=0;$i<$ccompany;$i++){
																	echo "<option value='" . $rcompany[$i]->company_id ."'><small>" . $rcompany[$i]->company_name . "</option>";
																}
															?>
													</select>
													
													<select id="subcompany" name="subcompany" style="display: none;">
															<option value="" selected='selected'><small>--Select Area--</option>
															<?php 
																$csubcompany = count($rsubcompany);
																for($i=0;$i<$ccompany;$i++){
																	echo "<option value='" . $rsubcompany[$i]->subcompany_id ."'><small>" . $rsubcompany[$i]->subcompany_name . "</option>";
																}
															?>
													</select>
													
													<select id="group" name="group" style="display: none;">
															<option value="" selected='selected'><small>--Select Area--</option>
															<?php 
																$cgroup = count($rgroup);
																for($i=0;$i<$cgroup;$i++){
																	echo "<option value='" . $cgroup[$i]->group_id ."'><small>" . $cgroup[$i]->group_name . "</option>";
																}
															?>
													</select>
													
													<select id="subgroup" name="subgroup" style="display: none;">
															<option value="" selected='selected'><small>--Select Area--</option>
															<?php 
																$csubgroup = count($rsubgroup);
																for($i=0;$i<$rsubgroup;$i++){
																	echo "<option value='" . $rsubgroup[$i]->subgroup_id ."'><small>" . $rsubgroup[$i]->subgroup_name . "</option>";
																}
															?>
													</select>
													<input type="text" name="keyword" id="keyword" value="" class="formdefault" /> 
													<input type="submit" value="<?=$this->lang->line("lsearch");?>" />
													
													<a class="button" href="javascript:void(0);" id="export_xcel">Export to Excel</a> 
													<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
												</div>
												</div>
												</td>
												
											</tr>
										</table>
									</legend>
									</form>
									
									<div id="isexport_xcel">
                                    	<div id="result"></div>
									</div>
									
								 </div>
                              </div>
							 </div>
                         </div>
                    </div>
                </div>
            </div>
            <!-- end page content -->
	

