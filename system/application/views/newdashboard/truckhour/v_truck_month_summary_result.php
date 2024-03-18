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



			<header class="panel-heading panel-heading-red">REPORT NEW</header>
				<div class="panel-body" id="bar-parent10">
					<div class="row">
					<?php if (count($data) == 0) {
							echo "<p>No Data</p>";
					}else{
					?>
						<div class="col-md-12 col-sm-12">

							<div class="col-lg-4 col-sm-4">
								<a href="javascript:void(0);" id="export_xcel" type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect m-b-10 btn-circle btn-default"><small>Export to Excel</small></a>
							</div>

							<div id="isexport_xcel">
							<tr>
								<td style="text-align:center;"><b><?php echo $company_name;?> Total unit : <?php echo $total_unit;?></b> Periode: <?php echo $sdate." - ".$edate;?></td>
							</tr>
							<table class="table table-striped custom-table table-hover" style="font-size:12px;">
								<thead>
									<tr>
										<th style="text-align:center;" width="3%">No</th>
										<th style="text-align:center;" width="10%">Jam</th>
										<?php
										$totaldatahari = sizeof($datahari);
										$totalperulangan = 1;
											if ($totaldatahari > 1) {
												$totalperulangan = (sizeof($datahari)-1);
											}else {
												$totalperulangan = 1;
											}
											for ($j=0;$j<$totalperulangan;$j++)
											{
										?>

												<th style="text-align:center;" width="10%"><?=$datahari[$j];?></th>
										<?php
											}

										?>
										<th style="text-align:center;" width="10%">Total</th>
										<th style="text-align:center;" width="10%">%</th>
									</tr>
								</thead>
								<tbody>
									<?php if (isset($data)) {?>
										<?php
											if ($shift == 1) {
												$jam_array = array("06","07","08","09","10","11","12","13","14","15","16","17");
											}elseif ($shift == 2) {
												// $jam_array = array("18","19","20","21","22","23","00","01","02","03","04","05");
												$jam_array = array("00","01","02","03","04","05","18","19","20","21","22","23");
											}else {
												// $jam_array = array("00","01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23");
												$jam_array = array("06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","00","01","02","03","04","05");
											}

										 ?>
										 	<?php for ($j=0; $j < sizeof($jam_array); $j++) {?>
										 		<tr>
													<td style="text-align:center;"><?php echo ($j+1) ?></td>
													<td style="text-align:center;"><?php echo $jam_array[$j] ?></td>

													<?php
													$sum = 0;
													 for ($k=0; $k < $totalperulangan; $k++) {?>
														<td style="text-align:center;">
															<?php
																if ($jam_array[$j] == "00" || $jam_array[$j] == "01" || $jam_array[$j] == "02" || $jam_array[$j] == "03" || $jam_array[$j] == "04" || $jam_array[$j] == "05") {
																	if ($k < (sizeof($datahari)-1)) {
																		if (isset($data['tanggal'][$datahari[($k+1)]]['jam'][$jam_array[$j]])) {
																			echo sizeof($data['tanggal'][$datahari[($k+1)]]['jam'][$jam_array[$j]]);
																		}
																	}
																}else {
																	// if ($k == sizeof($datahari)) {
																	//
																	// }else {
																		if (isset($data['tanggal'][$datahari[$k]]['jam'][$jam_array[$j]])) {
																			echo sizeof($data['tanggal'][$datahari[$k]]['jam'][$jam_array[$j]]);
																		}
																	// }
																}
															?>
														</td>

														<?php
															if ($jam_array[$j] == "00" || $jam_array[$j] == "01" || $jam_array[$j] == "02" || $jam_array[$j] == "03" || $jam_array[$j] == "04" || $jam_array[$j] == "05") {
																if ($k < (sizeof($datahari)-1)) {
																	if (isset($data['tanggal'][$datahari[($k+1)]]['jam'][$jam_array[$j]])) {
																			$sum += sizeof($data['tanggal'][$datahari[$k+1]]['jam'][$jam_array[$j]]);
																	}
																}
															}else {
																// $sum = 0;
																if (isset($data['tanggal'][$datahari[$k]]['jam'][$jam_array[$j]])) {
																	$sum += sizeof($data['tanggal'][$datahari[$k]]['jam'][$jam_array[$j]]);
																}
															}
														?>


													<?php } ?>
														<td style="text-align:center;"><?php echo $sum ?></td>
														<td style="text-align:center;">
															<?php
															// $percent = round(($sum*100)/$total_unit) ;
															 	// if ($percent > 100) {
															 		$sum_awal = ($sum / sizeof($datahari));
																	echo $percent = round(($sum_awal*100)/$total_unit);
															 	// }else {
																// 	echo $percent;
															 	// }
															?>
														</td>
										 		</tr>
										 	<?php } ?>


											<!-- <?php for ($j=0; $j < sizeof($data['tanggal'][$datahari[0]]['jam']); $j++) {?>

												<tr>
													<td style="text-align:center;"><?php echo ($j+1) ?></td>
													<td style="text-align:center;">
														<?php echo $data['tanggal'][$datahari[0]]['jam'][$jam_array[$j]][0] ?>
													</td>

													<?php
													$sum = 0;
													 for ($k=0; $k < sizeof($datahari); $k++) {?>
														<td style="text-align:center;">
															<?php
															if (isset($data['tanggal'][$datahari[$k]]['jam'][$jam_array[$j]])) {
																echo sizeof($data['tanggal'][$datahari[$k]]['jam'][$jam_array[$j]]);
															} ?>
														</td>

															<?php
															if (isset($data['tanggal'][$datahari[$k]]['jam'][$jam_array[$j]])) {
																$sum += sizeof($data['tanggal'][$datahari[$k]]['jam'][$jam_array[$j]]);
															}
																?>
													<?php } ?>
														<td style="text-align:center;"><?php echo $sum ?></td>
														<td style="text-align:center;">
															<?php
															$percent = round(($sum*100)/$total_unit) ;
															 	if ($percent > 100) {
															 		$sum_awal = ($sum / sizeof($datahari));
																	echo $percent = round(($sum_awal*100)/$total_unit);
															 	}else {
																	echo $percent;
															 	}
															?>
														</td>
												</tr>
											<?php } ?> -->
									<?php }else {
										echo "Data is empty";
									} ?>
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
