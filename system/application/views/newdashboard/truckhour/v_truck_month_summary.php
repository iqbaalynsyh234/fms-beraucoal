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

		jQuery.post("<?=base_url();?>truck/searchmonthsummarynew", jQuery("#frmsearch").serialize(),
			function(r)
			{
				if (r.error) {
					alert(r.message);
					jQuery("#loader2").hide();
					jQuery("#result").hide();
					return;
				}else{

					console.log(r);
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
								<center><font size="5px"><b>TRUCK ON DUTY (SUMMARY) NEW</font> </b><br /><small><span id="periode_show"></span></small></center>
							  </div>
                                <div class="panel-body" id="bar-parent10">
                                    	<div class="form-group row">
											<div class="col-lg-3 col-md-3">
												<select id="contractor" name="contractor" class="form-control select2" >
                                                    <!--<option value="">Select Contractor</option>-->
													<?php
														$ccompany = count($rcompany);
															for($i=0;$i<$ccompany;$i++){
																echo "<option value='" . $rcompany[$i]->company_id ."' " . $selected . ">" . $rcompany[$i]->company_name."</option>";
																}
													?>
												</select>
											</div>
											<!-- <div class="col-lg-3 col-md-3">
												<select id="shift" name="shift" class="form-control select2" >
                                                    <option value="all">All Shift</option>
													<option value="1">Shift 1</option>
													<option value="2">Shift 2</option>
												</select>
											</div> -->
											<div class="col-lg-3 col-md-3">
                                                <!--<select id="periode" name="periode" id="periode" class="form-control select2"  >-->
												<select name="periode" id="periode" class="form-control select2" onchange="javascript:periode_onchange()" >

													<option value="last7">Last 7 Days</option>
													<option value="last30">This Month</option>
													<option value="custom">Custom Date</option>
												</select>
                                            </div>

											<div class="col-lg-3 col-md-3">
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
