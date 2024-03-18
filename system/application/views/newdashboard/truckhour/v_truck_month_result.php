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


<div class="row">
	<div class="col-md-12 col-sm-12">
		<div class="panel">

			

			<header class="panel-heading panel-heading-red">REPORT</header>
				<div class="panel-body" id="bar-parent10">
					<div class="row">
					<?php if (count($data) == 0) {
							echo "<p>No Data</p>";
					}else{
						$group_selected = array('STREET','ROM','PORT');
						$sdate_tgl = date('Y-m-d', strtotime($sdate));
						$edate_tgl = date('Y-m-d', strtotime($edate));
					

					?>
						<div class="col-md-12 col-sm-12">

							<div class="col-lg-4 col-sm-4">
								<a href="javascript:void(0);" id="export_xcel" type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect m-b-10 btn-circle btn-default"><small>Export to Excel</small></a>
							</div>

							<div id="isexport_xcel">
							<tr>
								<td style="text-align:center;"><b><?php echo $company_name;?> Total unit : <?php echo $total_unit;?></b> Periode: <?php echo $startdate." - ".$enddate;?></td>
							</tr>
							<table class="table table-striped custom-table table-hover">
								<thead>
									<tr>
										<th style="text-align:center;" width="3%">No</th>
										<th style="text-align:center;" width="10%">Jam</th>
										<?php 
												
											for ($j=0;$j<count($datadate);$j++)
											{
												$date_loop = date("d", strtotime($datadate[$j]->monthly_date)); 
										?>
												
												<th style="text-align:center;" width="10%"><?=$date_loop;?></th>
										<?php 
											}
												
										?>
									
									</tr>
								</thead>
								<tbody>


								 <?php
									if(isset($data) && (count($data) > 0)){

											for ($i=0;$i<count($data);$i++){

											?>
											<tr>
												<td style="text-align:center;font-size:12px;"><?= $i+1; ?></td>
												<td style="text-align:center;font-size:12px;"><?= $data[$i]->hour_time_show; ?></td>
												
											<?php 
												
												for ($j=0;$j<count($datadate);$j++)
												{
													$date_loop = date("d", strtotime($datadate[$j]->monthly_date));
													$hour_value = "hour_date_".$date_loop;
												?>
													
													<td style="text-align:center;font-size:12px;"><?= $data[$i]->$hour_value; ?></td>
											<?php } ?>		
												
											</tr>
									<?php
											}

									}else{
								?>
									<tr>
										<td colspan="10">No Available Data</td>
									</tr>
								<?php
									}
								?>
								</tbody>

							</table>
							</div>
						</div>

					<?php } ?>

					</div>
				</div>
		</div>
	</div>
</div>
