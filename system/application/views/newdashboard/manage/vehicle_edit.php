<script type="text/javascript" src="js/script.js"></script>
<script src="<?php echo base_url()?>assets/dashboard/assets/js/jquery-1.7.1.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/js/jsblong/jquery.table2excel.js"></script>

<script>
	
	function frmadd_onsubmit()
	{
		jQuery("#loader2").show();
		jQuery("#buttonsubmit").hide();
		jQuery.post("<?=base_url()?>manage/savevehicle", jQuery("#frmadd").serialize(),
			function(r)
			{
				jQuery("#loader2").hide();
				jQuery("#buttonsubmit").show();
				
				if (r.error)
				{
					alert(r.message);
					return false;
				}
				
				alert(r.message);
				location = r.redirect;
			}
			, "json"	
		);
		return false;
	}
	
	function company_onchange(){
		var data_company = jQuery("#company").val();
		if(data_company == 0){
			alert('Silahkan Pilih Company!!');
			//jQuery("#mn_subcompany").hide();
			jQuery("#subcompany").html("<option value='0' selected='selected'>--Select Sub Company--</option>");
		}else{
			//jQuery("#mn_subcompany").show();
			var site = "<?=base_url()?>manage/company_onchange/" + data_company;
		    jQuery.ajax({
		        url: site,
		        success: function(response){
					jQuery("#subcompany").html("");
		            jQuery("#subcompany").html(response);
		        },
		    	dataType:"html"
		    });

		}

	}
	
	function subcompany_onchange(){
		var data_subcompany = jQuery("#subcompany").val();
		if(data_subcompany == 0){
			alert('Silahkan Pilih Cabang!!');
			jQuery("#group").html("<option value='0' selected='selected'>--Select Group--</option>");
		}else{
			var site = "<?=base_url()?>manage/subcompany_onchange/" + data_subcompany;
		    jQuery.ajax({
		        url: site,
		        success: function(response){
					jQuery("#group").html("");
		            jQuery("#group").html(response);
		        },
		    	dataType:"html"
		    });

		}

	}
	
	function group_onchange(){
		var data_group = jQuery("#group").val();
		if(data_group == 0){
			alert('Silahkan Pilih Cabang!!');
			jQuery("#subgroup").html("<option value='0' selected='selected'>--Select Sub Group--</option>");
		}else{
			
			var site = "<?=base_url()?>manage/group_onchange/" + data_group;
		    jQuery.ajax({
		        url: site,
		        success: function(response){
					jQuery("#subgroup").html("");
		            jQuery("#subgroup").html(response);
		        },
		    	dataType:"html"
		    });

		}

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
                                <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?=base_url();?>">Home</a>&nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                                <li>Manage&nbsp;<i class="fa fa-angle-right"></i>
                                </li>
								<li><a class="parent-item" href="<?=base_url();?>manage/vehicle">Vehicle</a>&nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                                <li class="active">Edit</li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">
						<div class="col-md-12 col-sm-12">
                            <div class="panel" id="panel_form">
                                <header class="panel-heading panel-heading-blue">FORM</header>
                                <div class="panel-body" id="bar-parent10">
                                    <form id="frmadd" onsubmit="javascript: return frmadd_onsubmit()">		
										<table class="table table-striped table-hover">
											<tr>
												<td colspan="4"><b>Edit Vehicle</b></td>
												<input type="hidden" name="id" id="id" value="<?=isset($row) ? htmlspecialchars($row->vehicle_id, ENT_QUOTES) : "";?>" />
											</tr>
											<tr>
												<td><small>Vehicle No</td>
												<td></td>
												<td><small><?=isset($row) ? htmlspecialchars($row->vehicle_no, ENT_QUOTES) : "";?></td>
											</tr>
											<tr>
												<td><small>Vehicle Name</td>
												<td></td>
												<td><input type="text" name="vehicle_name" id="vehicle_name" value="<?=isset($row) ? htmlspecialchars($row->vehicle_name, ENT_QUOTES) : "";?>" class="form-control"/></td>
											</tr>
											<tr>
												<td><small>Company</td>
												<td></td>
												<td>
													<select id="company" name="company" class="form-control select2" onchange="javascript:company_onchange()">
														<option value="" selected='selected'><small>--Select Company--</option>
														<?php 
															$ccompany = count($rcompany);
															for($i=0;$i<$ccompany;$i++){
																if (isset($row)&&($row->vehicle_company == $rcompany[$i]->company_id)){
																	$selected = "selected"; 
																	}else{
																		$selected = "";
																	}
																	echo "<option value='" . $rcompany[$i]->company_id ."' " . $selected . ">" . $rcompany[$i]->company_name . "</option>";
																}
														?>
													</select>
												</td>
											</tr>
											
											<tr>
												<td><small>Sub Company</td>
												<td></td>
												<td>
													<select id="subcompany" name="subcompany" class="form-control select2" onchange="javascript:subcompany_onchange()">
														<option value="">Select a Subcompany</option>
														<?php 
															$csubcompany = count($subcompanyall);
															for($i=0;$i<$csubcompany;$i++){
																if (isset($row)&&($row->vehicle_subcompany == $subcompanyall[$i]->subcompany_id)){
																	$selected = "selected"; 
																	}else{
																		$selected = "";
																	}
																	echo "<option value='" . $subcompanyall[$i]->subcompany_id ."' " . $selected . ">" . $subcompanyall[$i]->subcompany_name . "</option>";
																}
														?>
													</select>
												</td>
											</tr>
											
											<tr>
												<td><small>Group</td>
												<td></td>
												<td>
													<select id="group" name="group" class="form-control select2" onchange="javascript:group_onchange()">
														<option value="">Select a Group</option>
														<?php 
															$cgroup = count($groupall);
															for($i=0;$i<$cgroup;$i++){
																if (isset($row)&&($row->vehicle_group == $groupall[$i]->group_id)){
																	$selected = "selected"; 
																	}else{
																		$selected = "";
																	}
																	echo "<option value='" . $groupall[$i]->group_id ."' " . $selected . ">" . $groupall[$i]->group_name . "</option>";
																}
														?>
													</select>
												</td>
											</tr>
											
											<tr>
												<td><small>Sub Group</td>
												<td></td>
												<td>
													<select id="subgroup" name="subgroup" class="form-control select2" onchange="javascript:subgroup_onchange()">
														<option value="">Select a Sub Group</option>
														<?php 
															$csubgroup = count($subgroupall);
															for($i=0;$i<$csubgroup;$i++){
																if (isset($row)&&($row->vehicle_subgroup == $subgroupall[$i]->subgroup_id)){
																	$selected = "selected"; 
																	}else{
																		$selected = "";
																	}
																	echo "<option value='" . $subgroupall[$i]->subgroup_id ."' " . $selected . ">" . $subgroupall[$i]->subgroup_name . "</option>";
																}
														?>
													</select>
												</td>
											</tr>
												
											<tr>
												<td>
													<button id="buttonsubmit" type="submit" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect m-b-10 btn-circle btn-success" >Save
													</button>
													
													<a href="<?=base_url();?>manage/vehicle/" type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect m-b-10 btn-circle btn-info" >Cancel
													</a>
												</td>
											</tr>
										</table>
									</form>
                                </div>
								
							</div>
                           
                        </div>
                        
                    </div>
                    <div id="loader2" class="mdl-progress mdl-js-progress mdl-progress__indeterminate" style="display: none;"></div>
					
                </div>
                <!-- end page content -->
                
            </div>
            <!-- end page container -->