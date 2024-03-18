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
                                <div class="page-title">SUMMARY REPORT KM (This Month)</div>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                                <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?=base_url();?>dashboard">Dashboard</a>&nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                                <li>Report &nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                                <li class="active"><a class="parent-item" href="<?=base_url();?>dashboard/summary/<?=$companydata->company_id;?>"><?=$companydata->company_name;?></a></li>
                            </ol>
                        </div>
                    </div>
							<div class="row">
										
										<div class="col-lg-6 col-sm-6">			
											<a href="javascript:void(0);" id="export_xcel" type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect m-b-10 btn-circle btn-info"><small>Export to Excel</small></a>
										</div>	
										
									</div>
                     <div class="row">
                    	<div class="col-sm-12">
                             <div class="card-box">
                                 <div class="card-head">
                                     <header>Total Vehicle (<?=count($data);?>)</header>
                                 </div>
                                 <div class="card-body ">
                                 	<div id="isexport_xcel">
                                    <!--<table class="table table-hover table-checkable order-column full-width" id="example4">-->
									<table class="table table-striped custom-table table-hover">
										<thead>
											<tr>
												<th style="text-align:left;" width="3%">No</td>
                                                <th style="text-align:left;" width="10%">Vehicle</th>
												<th style="text-align:left;" width="10%">Area</th>
												<th style="text-align:left;" width="15%">Periode</th>
												<th style="text-align:left;" width="15%">Total Duration (ON)</th>
                                                <th style="text-align:left;" width="15%">Total KM</th>
											</tr>
										</thead>
										<tbody>
										<?php 
										if (count($data)>0){
											for ($i=0;$i<count($data);$i++){
												$datareport = $this->dashboardmodel->gettotalsummary_pervehicle($data[$i]->auto_vehicle_device,$dbtable,$sdate,$edate);
												$ex_datareport = explode('|',$datareport);
												$totaldur = $ex_datareport[0];
												$totalkm = $ex_datareport[1];
										?>
                                            <tr>
												<td><h5 class="text-medium full-width"><?php echo $i+1;?></h5></td>
                                                <td><h5 class="text-medium full-width">
													<?=$data[$i]->auto_vehicle_no." ".$data[$i]->auto_vehicle_name;?>
												</td>
												<td>
													<h5 class="text-medium full-width">
													<?php 
															if (isset($rcompany))
															{
																foreach ($rcompany as $com)
																{
																	if ($com->company_id == $data[$i]->auto_vehicle_company)
																	{
																		echo $com->company_name;
																	}
																}
															}
														?>
													</h5>
												</td>
                                                <td>
													<h5 class="text-medium full-width">
													<?=date("d-m-Y", strtotime($sdate));?> to <?=date("d-m-Y", strtotime($edate));?>
													</h5>
												</td>
												<td>
													<h5 class="text-medium full-width">
													<?php
													if (isset($totaldur))
													{
														$conval = $totaldur;
														$seconds = $conval;
														
														// extract hours
														$hours = floor($seconds / (60 * 60));
					 
														// extract minutes
														$divisor_for_minutes = $seconds % (60 * 60);
														$minutes = floor($divisor_for_minutes / 60);
					 
														// extract the remaining seconds
														$divisor_for_seconds = $divisor_for_minutes % 60;
														$seconds = ceil($divisor_for_seconds);
														
														if(isset($hours) && $hours > 0)
														{
															
															echo $hours."".":"."";
															
														}
														if(isset($minutes) && $minutes > 0)
														{
															echo $minutes;
														}
														
														
													}
													?>
													</h5>
												</td>
												<td>
													<h5 class="text-medium full-width">
														<?=$totalkm;?>
													</h5>
												</td>
											</tr>
										<?php }}?>
                                            
											
										</tbody>
									</table>
									</div>
								 </div>
                             </div>
                         </div>
                    </div>
                </div>
            </div>
            <!-- end page content -->