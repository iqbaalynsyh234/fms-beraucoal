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

<?php

$totaldata = $this->dashboardmodel->gettotalengine($companydata->company_id);
$totalengine = explode("|", $totaldata);
$shour = "07:00:00";
$nowdate = date("Y-m-d");

$sdate = date("Y-m-d H:i:s", strtotime($nowdate." ".$shour));
$edate = date("Y-m-d H:i:s");
							
?>
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
                                <div class="page-title"><?=$companydata->company_name;?></div>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                                <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?=base_url();?>dashboard">Dashboard</a>&nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                                <li>Area &nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                                <li class="active"><a class="parent-item" href=""><?=$companydata->company_name;?></a></li>
                            </ol>
                        </div>
                    </div>
                     <div class="row">
                    	<div class="col-sm-12">
                             <div class="card-box">
                                 <div class="card-head">
                                     <header>Total Vehicle (<?=count($data);?>)</header>
                                 </div>
                                 <div class="card-body ">
                                 	<div class="state-overview">
										<div class="row">
											<div class="col-lg-4 col-sm-4">
												<div class="overview-panel red">
													<div class="symbol">
														<i class="fa fa-car"></i>
													</div>
													<div class="value white">
														<p class="sbold addr-font-h1" data-counter="counterup" data-value="<?=$totalengine[0];?>"><?=$totalengine[0];?></p>
														<p>Vehicle (OFF)</p>
													</div>
												</div>
											</div>
											<div class="col-lg-4 col-sm-4">
												<div class="overview-panel blue">
													<div class="symbol">
														<i class="fa fa-car"></i>
													</div>
													<div class="value white">
														<p class="sbold addr-font-h1" data-counter="counterup" data-value="<?=$totalengine[1];?>"><?=$totalengine[1];?></p>
														<p>Vehicle (ON)</p>
													</div>
												</div>
											</div>
											<div class="col-lg-4 col-sm-4">
												<div class="overview-panel yellow">
													<div class="symbol">
														<i class="fa fa-car"></i>
													</div>
													<div class="value white">
														<p class="sbold addr-font-h1" data-counter="counterup" data-value="<?=$totalengine[3];?>"><?=$totalengine[3];?></p>
														<p>NO DATA (Go to History)</p>
													</div>
												</div>
											</div>
											
										</div>
									</div>
										<!--<div class="row p-b-20">
											<div class="col-md-6 col-sm-6 col-6">
												<div class="btn-group">
													<button id="addRow1" class="btn btn-info">
														Add New <i class="fa fa-plus"></i>
													</button>
												</div>
											</div>
											<div class="col-md-6 col-sm-6 col-6">
												<div class="btn-group pull-right">
													<button class="btn deepPink-bgcolor  btn-outline dropdown-toggle" data-toggle="dropdown">Tools
														<i class="fa fa-angle-down"></i>
													</button>
													<ul class="dropdown-menu pull-right">
														<li>
															<a href="javascript:;">
																<i class="fa fa-file-excel-o"></i> Export to Excel </a>
														</li>
													</ul>
												</div>
											</div>
										</div>-->
										<div class="row">
										<div class="col-lg-6 col-sm-6">
											<td class="center">
												<a href="<?=base_url();?>dashboard/maparea/<?=$companydata->company_id;?>" type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect m-b-10 btn-circle btn-success" >SHOW MAP
												</a>
											</td>
										</div>	
										<div class="col-lg-6 col-sm-6">			
											<a href="javascript:void(0);" id="export_xcel" type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect m-b-10 btn-circle btn-info">Export to Excel</a>
										</div>	
										
									</div>
									
									<div id="isexport_xcel">
                                    <table class="table table-hover table-checkable order-column full-width" id="example4">
										<thead>
											<tr>
												<th style="text-align:left;" width="3%">No</td>
                                                <th style="text-align:left;" width="10%">Vehicle</th>
												<th style="text-align:left;" width="15%">Last Position</th>
                                                <th style="text-align:left;" width="5%">Last Information</th>
												<th style="text-align:left;" width="5%">Simcard</th>
												<th style="text-align:left;" width="10%">Status</th>
												<th style="text-align:left;" width="5%">Action</th>
											</tr>
										</thead>
										<tbody>
										<?php 
										if (count($data)>0){
											for ($i=0;$i<count($data);$i++){
												$datadevice = explode('@',$data[$i]->auto_vehicle_device);
												$vehicledevice = $datadevice[0];
												$vehicletype = $datadevice[1];
												//print_r("AWAL".$vehicledevice."   ".$vehicletype);exit();
												//$lastON = $this->dashboardmodel->GetLastInfoON($data[$i]->auto_vehicle_id,$sdate,$edate);
												//print_r("AKHIR".$vehicledevice."   ".$vehicletype);exit();
												
										?>
                                            <tr>
												<td><h5 class="text-medium full-width"><?php echo $i+1;?></h5></td>
                                                <td><h5 class="text-medium full-width">
													<?=$data[$i]->auto_vehicle_no;?> <br />
													<?=$data[$i]->auto_vehicle_name;?> <br />
													<b>
													<font color="green">
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
													</font>
													</h5>
												</td>
                                                <td><h5 class="text-medium full-width">
													<b><font color="green"><?=date("d-m-Y H:i:s", strtotime($data[$i]->auto_last_update));?></font></b><br />
													<a href="https://www.google.com/maps?q=<?=$data[$i]->auto_last_lat.",".$data[$i]->auto_last_lat;?>" target="_blank">
														<?=$data[$i]->auto_last_position;?>
													</a>
													</h5>
												</td>
												<td><h5 class="text-medium full-width">
													Engine : <?=$data[$i]->auto_last_engine;?> <br />
													Speed : <?=$data[$i]->auto_last_speed;?> kph <br />
													GPS Status : <?=$data[$i]->auto_last_gpsstatus;?> 
													
													</h5>
												</td>
												<td><h5 class="text-medium full-width"><?=$data[$i]->auto_simcard;?></h5></td>
												<td><h5 class="text-medium full-width">
												<?php if($data[$i]->auto_status == "P"){ ?>
														<span class="label label-sm label-success"> Online </span>
												<?php }else if($data[$i]->auto_status == "K"){ ?>
														<span class="label label-sm label-warning"> Online(delay) </span>
												<?php }else if ($data[$i]->auto_status == "M"){ ?>
														<span class="label label-sm label-danger"> Offline </span>
												<?php }else{ ?>
													 - 
												<?php } ?>
												</h5></td>
												<td class="center">
													<a href="<?=base_url();?>maps/realtime/<?=$vehicledevice."/".$vehicletype;?>" target="_blank" class="btn btn-tbl-delete btn-xs" title="Realtime Monitoring">
														<i class="fa fa-map-marker"></i>
													</a>
													
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