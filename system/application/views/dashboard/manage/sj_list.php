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
	
	function voperational(v)
			{
				jQuery("#loader2").show();
				jQuery("#result").hide();
				jQuery.post('<?php echo base_url(); ?>pbimanage/dataoperational/'+v, {id: v},
					function(r)
					{
						if (r.error) {
							alert(r.message);
							jQuery("#loader2").hide();
							jQuery("#result").hide();
							return;
						}else{
							jQuery("#loader2").hide();
							jQuery("#result").show();
							jQuery("#result").html(r.html);		
							//jQuery("#total").html(r.total);	
							
						}
					}
					, "json"
				);
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
                                <div class="page-title">Manage SJ</div>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                                <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?=base_url();?>dashboard">Dashboard</a>&nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                                <li>FIB &nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                                <li class="active"><a class="parent-item" href="">SJ List</a></li>
                            </ol>
                        </div>
                    </div>
                     <div class="row">
                    	<div class="col-sm-12">
                             <div class="card-box">
                                 <div class="card-head">
                                     <header>Total Data (<?=count($data);?>)</header>
                                 </div>
                                 <div class="card-body ">
                                 	
									<div class="row">
										<div class="col-lg-6 col-sm-6">
											<td class="center">
												<a href="<?=base_url()?>fib" type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect m-b-10 btn-circle btn-success" >SHOW FIB
												</a>
											</td>
										</div>	
										<div class="col-lg-6 col-sm-6">			
											<a href="javascript:void(0);" id="export_xcel" type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect m-b-10 btn-circle btn-info">Export to Excel</a>
										</div>	
										
									</div>
									
									<div id="isexport_xcel">
                                    <!--<table class="table table-hover table-checkable order-column full-width" id="example4">-->
									<table id="example2" class="display" class="full-width">
										<thead>
											<tr>
												<th style="text-align:center;" width="3%">No</td>
												<th style="text-align:center;" width="10%">SJ No</th>
												<th style="text-align:center;" width="10%">DI No</th>
												<th style="text-align:center;" width="10%">Vehicle</th>
												<th style="text-align:center;" width="10%">Driver</th>
												<th style="text-align:center;" width="10%">SJ Date</th>
												<th style="text-align:center;" width="10%">Uang Jalan</th>
												<th style="text-align:center;" width="10%">Category</th>
												<th style="text-align:center;" width="15%">Item</th>
                                                <th style="text-align:center;" width="7%">Status</th>
											</tr>
										</thead>
										<tbody>
										<?php 
										if (count($data)>0){
											for ($i=0;$i<count($data);$i++){
										?>
                                            <tr>
												<td style="text-align:center;"><h5 class="text-medium full-width"><?php echo $i+1;?></h5></td>
                                                <td style="text-align:center;"><h5 class="text-medium full-width">
													<?=$data[$i]->sj_sj_no;?>
													</h5>
												</td>
												<td style="text-align:center;"><h5 class="text-medium full-width">
													<?=$data[$i]->sj_di_no;?>
													</h5>
												</td>
												<td style="text-align:center;"><h5 class="text-medium full-width">
													<?=$data[$i]->sj_vehicle_no;?>
													</h5>
												</td>
												<td style="text-align:center;"><h5 class="text-medium full-width">
														<?=$data[$i]->sj_driver;?>
													</h5>
												</td>
												<td style="text-align:center;"><h5 class="text-medium full-width">
													<?=date("d-m-Y", strtotime($data[$i]->sj_sj_date));?>
													</h5>
												</td>
												<td style="text-align:center;"><h5 class="text-medium full-width">
													<?=date("d-m-Y H:i:s", strtotime($data[$i]->sj_uj_date));?> 
													</h5>
												</td>
												
												<td style="text-align:center;"><h5 class="text-medium full-width">
													<?=$data[$i]->sj_category;?> 
													</h5>
												</td>
												<td style="text-align:center;"><h5 class="text-medium full-width">
													<?=$data[$i]->sj_item;?> 
													</h5>
												</td>
												<td style="text-align:center;"><h5 class="text-medium full-width">
												<?php if($data[$i]->sj_status == "2"){ ?>
														<!--<span class="label label-sm label-success"> Completed </span> -->
														<a href="javascript:voperational(<?=$data[$i]->sj_id;?>)" type="input" class="btn btn-circle btn-success" type="button" title="View Operational Report" />Completed</a>
														<br /><br />
														<?=date("d-m-Y H:i:s", strtotime($data[$i]->sj_api_completed));?>
														
												<?php }else if($data[$i]->sj_status == "1"){ ?>
														<span class="label label-sm label-warning"> On Process </span>
												<?php }else{ ?>
													 - 
												<?php } ?>
												</h5></td>
												
                                               
                                            </tr>
										<?php }}?>
                                            
											
										</tbody>
									</table>
									</div>
								 </div>
                             </div>
                         </div>
                    </div>
					<div id="loader2" class="mdl-progress mdl-js-progress mdl-progress__indeterminate" style="display: none;"></div>
					<div id="result"></div>	
                </div>
            </div>
            <!-- end page content -->