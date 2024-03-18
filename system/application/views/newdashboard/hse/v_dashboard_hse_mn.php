<script src="<?php echo base_url();?>assets/dashboard/assets/plugins/jquery/jquery.min.js"></script>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/data.js"></script>
<script src="https://code.highcharts.com/modules/drilldown.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

<script>
	$(document).ready(function() { 
	  
		page(0);
	
	  });
function frmsearch_onsubmit()
	{
		jQuery("#loader2").show();
		page(0);
		return false;
	}
	
function page(p)
	{
		if(p==undefined){
			p=0;
		}
		jQuery("#offset").val(p);
		jQuery("#result").hide();
		jQuery("#loader2").show();
		
		jQuery.post("<?=base_url();?>hse/search", jQuery("#frmsearch").serialize(),
			function(r)
			{
				if (r.error) {
					alert(r.message);
					jQuery("#loader2").hide();
					jQuery("#result").hide();
					return;
				}else{
					
					console.log(r.periode_show);
					jQuery("#loader2").hide();
					jQuery("#result").show();
					jQuery("#result").html(r.html);		
					jQuery("#periode_show").html(r.periode_show);	
				
					jQuery("#btn_hide_form").hide();
					jQuery("#btn_show_form").show();
					
					
				}		
			}
			, "json"
		);
	}
	
function periode_onchange(){
		var data_periode = jQuery("#periode").val();
		if(data_periode == 'custom'){
			jQuery("#mn_sdate").show();
			jQuery("#mn_edate").show();
		}else{
			jQuery("#mn_sdate").hide();
			jQuery("#mn_edate").hide();

		}

	}
	
function company_onchange() {
    var data_company = jQuery("#company").val();
    if (data_company == 0) {
      alert('Please Select Contractor!!');
      jQuery("#mn_vehicle").hide();

      jQuery("#vehicle").html("<option value='0' selected='selected'>--Select Vehicle--</option>");
    } else {
      jQuery("#mn_vehicle").show();

      var site = "<?=base_url()?>hse/get_vehicle_by_company/" + data_company;
      jQuery.ajax({
        url: site,
        success: function(response) {
          jQuery("#vehicle").html("");
          jQuery("#vehicle").html(response);
        },
        dataType: "html"
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
                   <form class="form-horizontal" name="frmsearch" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
                   <div class="row">
					<div class="col-md-12 col-sm-12">
                            <div class="panel" id="panel_form">
                              <div class="card-header">
								<center><font size="5px"><b>OHS HAULING DASHBOARD (DRAFT)</font> </b><br /><small><span id="periode_show"></span></small></center>
							  </div>
                                <div class="panel-body" id="bar-parent10">
                                    	<div class="form-group row">
											<div class="col-lg-2 col-md-2">
												<!--<select id="contractor" name="contractor" class="form-control select2" >-->
												<select class="form-control select2" id="company" name="company" onchange="javascript:company_onchange()">
                                                    <option value="all">All Contractor</option>
													<?php
														$ccompany = count($rcompany);
															for($i=0;$i<$ccompany;$i++){
																echo "<option value='" . $rcompany[$i]->company_id ."' " . $selected . ">" . $rcompany[$i]->company_name."</option>";
																}
													?>
												</select>
											</div>
											<div class="col-lg-3 col-md-3">
												<select id="vehicle" name="vehicle" class="form-control select2" >
                                                    <option value="all">All Vehicle</option>
													<?php
														$cvehicle = count($rvehicle);
															for($i=0;$i<$cvehicle;$i++){
																echo "<option value='" . $rvehicle[$i]->vehicle_id ."' " . $selected . ">" . $rvehicle[$i]->vehicle_no."</option>";
																}
													?>
												</select>
											</div>
											<div class="col-lg-3 col-md-3">
                                                <select id="violation" name="violation" class="form-control select2"  >
                                                    <option value="all">All Violation</option>
													<option value="Call">Call</option>
													<option value="Car Distance">Car Distance</option>
													<option value="Distracted">Distracted</option>
													<option value="Fatigue">Fatigue</option>
													<option value="Smoking">Smoking</option>
													<option value="Driver Abnormal">Driver Abnormal</option>
													<option value="overspeed" selected>Overspeed</option>
												</select>
                                            </div>
											<div class="col-lg-2 col-md-2">
                                                <!--<select id="periode" name="periode" id="periode" class="form-control select2"  >-->
												<select name="periode" id="periode" class="form-control select2" onchange="javascript:periode_onchange()" >
                                                    <option value="yesterday">Yesterday</option>
													<option value="last7">Last 7 Days</option>
													<option value="last30">This Month</option>
													<option value="custom">Custom Date</option>
												</select>
                                            </div>
											<div class="col-lg-2 col-md-2">
												<button class="btn btn-circle btn-success" id="btnsearchreport" type="submit" />Search</button>
												<img id="loader2" style="display:none;" src="<?php echo base_url();?>assets/images/ajax-loader.gif" />
											</div>
										</div>
										<div class="form-group row" id="mn_sdate" style="display:none;" >
											
											<div class="col-lg-6 col-md-1">
												<div class="input-group date form_date col-md-6" data-date="" data-date-format="dd-mm-yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
														<input class="form-control" size="5" type="text" readonly name="startdate" id="startdate" value="<?=date('d-m-Y',strtotime("yesterday") )?>">
														<span class="input-group-addon"><span class="fa fa-calendar"></span></span>
												</div>
												<input type="hidden" id="dtp_input2" value="" />
												
											</div>
											<div class="col-lg-6 col-md-1">
												<div class="input-group date form_date col-md-6" data-date="" data-date-format="dd-mm-yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
														<input class="form-control" size="5" type="text" readonly name="enddate" id="enddate" value="<?=date('d-m-Y',strtotime("yesterday") )?>">
														<span class="input-group-addon"><span class="fa fa-calendar"></span></span>
												</div>
												<input type="hidden" id="dtp_input2" value="" />
											</div>
											
											
											
										</div>
										
								</div>
								
							</div>
							
					</div>		
			
				   
					</div>
					</form>
					
                    <div id="result"></div>	
                </div>
                <!-- end page content -->
                <!--<div id="loader2" class="mdl-progress mdl-js-progress mdl-progress__indeterminate" ></div>-->
				
            </div>
            <!-- end page container -->