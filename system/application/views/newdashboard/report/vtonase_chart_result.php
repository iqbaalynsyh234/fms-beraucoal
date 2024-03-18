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

			<header class="panel-heading panel-heading-red">REPORT</header>
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
										<th style="text-align:center;" width="3%">Jam</th>
										<th style="text-align:center;" width="10%">Plan Per Jam</th>
										<th style="text-align:center;" width="10%">BIB</th>
										<th style="text-align:center;" width="10%">BIR</th>
										<th style="text-align:center;" width="10%">Total</th>
									</tr>
								</thead>
								<tbody>
								 <?php if(isset($data) && (count($data) > 0)){
												$total_rit = 0;
												$total_ton = 0;
												$total_plan = 0;
											for ($i=0;$i<count($data);$i++){
											
												$total_ton = $total_ton + $data[$i]->tonase_chart_total;
												$total_plan = $total_plan + $data[$i]->tonase_chart_plan;
											?>
											<tr>
												<td style="text-align:center;font-size:12px;"><?php echo $i+1;?></td>
												<td style="text-align:center;font-size:12px;"><?php echo $data[$i]->tonase_chart_jam;?></td>
												<td style="text-align:center;font-size:12px;"><?php echo number_format($data[$i]->tonase_chart_plan,0);?></td>
												
												<td style="text-align:center;font-size:12px;"><?php echo number_format($data[$i]->tonase_chart_total,0);?></td>
												<td style="text-align:center;font-size:12px;"></td>
												<td style="text-align:center;font-size:12px;"><?php echo number_format($data[$i]->tonase_chart_total,0);?></td>
												  
											</tr>
									<?php } ?>
								
									<tr>
										<td style="text-align:center;font-size:14px;" colspan="2">Total</td>
										<td style="text-align:center;font-size:14px;"><?php echo number_format($total_plan,0);?></td>
										<td style="text-align:center;font-size:14px;"><?php echo number_format($total_ton,0);?></td>
										<td style="text-align:center;font-size:14px;">-</td>
										<td style="text-align:center;font-size:14px;"><?php echo number_format($total_ton,0);?></td>
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
