<style media="screen">
	#report{
		background-color: #1f50a2;
  		color: white;
	}
</style>


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


							<div class="col-lg-6 col-sm-6">
								<input id="btn_hide_form" class="btn btn-circle btn-danger" title="" type="button" value="Hide Form" onclick="javascript:return option_form('hide')" />
								<input id="btn_show_form" class="btn btn-circle btn-success" title="" type="button" value="Show Form" onClick="javascript:return option_form('show')" style="display:none"/>
							</div>
							<div class="col-lg-2 col-sm-2">
							</div>
							<br />

<div class="row">
	<div class="col-md-12 col-sm-12">
		<div class="panel">

			<tr>
				<td style="text-align:center;"><small>Periode: <?php echo $startdate." - ".$enddate;?></small></td>
			</tr>

			<header class="panel-heading" id="report">REPORT</header>
				<div class="panel-body" id="bar-parent10">
					<div class="row">
						<div class="col-md-12 col-sm-12">

							<div class="col-lg-4 col-sm-4">
								<a href="javascript:void(0);" id="export_xcel" type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect m-b-10 btn-circle btn-default"><small>Export to Excel</small></a>
							</div>

							<div id="isexport_xcel">
							<table class="table table-striped custom-table table-hover">
								<thead>
									<tr>
										<th style="text-align:center;" width="3%">No</th>
										<th style="text-align:center;" width="3%">Code Material</th>
										<th style="text-align:center;" width="10%">Ritase</th>
										<th style="text-align:center;" width="8%">Tonase</th>
									</tr>
								</thead>
								<tbody>
								 <?php if(isset($data) && (count($data) > 0)){
												$total_rit = 0;
												$total_ton = 0;
											for ($i=0;$i<count($data);$i++){
												
												$total_rit = $total_rit + $data[$i]->stockpile_report_rit;
												$total_ton = $total_ton + $data[$i]->stockpile_report_tonase;
											?>
											<tr>
												<td style="text-align:center;font-size:12px;"><?php echo $i+1;?></td>
												<td style="text-align:center;font-size:12px;"><?php echo $data[$i]->stockpile_report_material_name;?></td>
												<!--<td style="text-align:center;font-size:12px;"><?php echo date("d-m-Y", strtotime($data[$i]->stockpile_report_start_time));?></td>
												<td style="text-align:center;font-size:12px;"><?php echo date("H:i:s", strtotime($data[$i]->stockpile_report_start_time));?></td>
												<td style="text-align:center;font-size:12px;"><?php echo date("d-m-Y", strtotime($data[$i]->stockpile_report_end_time));?></td>
												<td style="text-align:center;font-size:12px;"><?php echo date("H:i:s", strtotime($data[$i]->stockpile_report_end_time));?></td>-->
												<td style="text-align:center;font-size:12px;"><?php echo $data[$i]->stockpile_report_rit;?></td>
												<td style="text-align:center;font-size:12px;"><?php echo $data[$i]->stockpile_report_tonase;?></td>
												  
											</tr>
									<?php } ?>
									<tr>
										<td style="text-align:center;font-size:14px;" colspan='2'>SDT TRAILER</td>
										<td style="text-align:center;font-size:14px;">-</td>
										<td style="text-align:center;font-size:14px;">-</td>
									</tr>
									<tr>
										<td style="text-align:center;font-size:14px;" colspan='2'>DDT TRAILER</td>
										<td style="text-align:center;font-size:14px;">-</td>
										<td style="text-align:center;font-size:14px;">-</td>
									</tr>
									<tr>
										<td style="text-align:center;font-size:14px;" colspan='2'>HAULING DT</td>
										<td style="text-align:center;font-size:14px;">-</td>
										<td style="text-align:center;font-size:14px;">-</td>
									</tr>
									<tr>
										<td style="text-align:center;font-size:14px;" colspan='2'>TOTAL ALL HAULING</td>
										<td style="text-align:center;font-size:14px;"><?php echo number_format($total_rit,2);?></td>
										<td style="text-align:center;font-size:14px;"><?php echo number_format($total_ton,2);?></td>
									</tr>
									</td>
								<?php }else{ ?>
						        <tr>
						        	<td colspan="10">No Available Data</td>
										</tr>
								<?php } ?>
							</tbody>
						</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
