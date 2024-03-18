<script src="<?php echo base_url();?>assets/js/jsblong/jquery.table2excel.js"></script>
<!--<script>
jQuery(document).ready(
		function()
		{
			jQuery("#export_xcel").click(function()
			{
				window.open('data:application/vnd.ms-excel,' + encodeURIComponent(jQuery('#isexport_xcel').html()));
			});
		}
	);


</script> -->

<script>
  jQuery(document).ready(

    function() {
      jQuery("#export_xcel").click(function() {

        var $table = $('#isexport_xcel');
        $rows = $table.find('tr');
        var csvData = "";
        for(var i=0;i<$rows.length;i++){
                        var $cells = $($rows[i]).children('th,td'); //header or content cells

                        for(var y=0;y<$cells.length;y++){
                            if(y>0){
                              csvData += ",";
                            }
                            var txt = ($($cells[y]).text()).toString().trim();
                            if(txt.indexOf(',')>=0 || txt.indexOf('\"')>=0 || txt.indexOf('\n')>=0){
                                txt = "\"" + txt.replace(/\"/g, "\"\"") + "\"";
                            }
                            csvData += txt;
                        }
                        csvData += '\n';
        }

        var e = document.getElementById("company");
        var textCompany = e.options[e.selectedIndex].text;
        e = document.getElementById("vehicle");
        var textVehicle = e.options[e.selectedIndex].text;



        var link = document.createElement("a");
        link.href = 'data:text/csv,' + encodeURIComponent("sep=,\n"+csvData);
        link.download = "ritase-report-" + textCompany + "-" + textVehicle + ".csv";
        link.click();
        //window.open('data:application/csv;charset=utf-8,' + encodeURIComponent(csvData));
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
				<!--<td style="text-align:center;"><small>Periode: <?php echo $startdate." - ".$enddate;?></small></td>-->
				<td style="text-align:center;"><small>Periode: <?php echo $startdate." - ".$enddate." (Today:".$data_nowdate." yesterday:".$data_yesterday." )";?></small></td>
			</tr>

			<header class="panel-heading" style="background-color:#221f1f;color:white;">REPORT</header>
				<div class="panel-body" id="bar-parent10">
					<div class="row">
						<div class="col-md-12 col-sm-12">

							<!--<div class="col-lg-4 col-sm-4">
								<a href="javascript:void(0);" id="export_xcel" type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect m-b-10 btn-circle btn-default"><small>Export to Excel</small></a>
							</div>-->
							<div class="col-lg-4 col-sm-4">
								<a href="javascript:void(0);" id="export_xcel" type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect m-b-10 btn-circle btn-default"><small>Export to CSV</small></a>
							</div>

							<div id="isexport_xcel">
							<table class="table table-striped custom-table table-hover">
								<thead>
									<tr>
										<th style="text-align:center;" width="3%">No</th>

										<th style="text-align:center;" width="10%">Start Time</th>
										<th style="text-align:center;" width="10%">End Time</th>
										<th style="text-align:center;" width="8%">Duration</th>
										<th style="text-align:center;" width="7%">Vehicle No</th>
										<th style="text-align:center;" width="7%">Location Start</th>
										<th style="text-align:center;" width="7%">Location End</th>
										<th style="text-align:center;" width="15%">Beaching Point Start Time</th>
										<th style="text-align:center;" width="15%">Beaching Point End Time</th>
										<th style="text-align:center;" width="15%">Beaching Point Duration</th>
										<th style="text-align:center;" width="15%">Beaching Point Start Location</th>
										<th style="text-align:center;" width="15%">Beaching Point End Location</th>
										<th style="text-align:center;" width="5%">Shift</th>
										<!-- <th style="text-align:center;" width="10%">Jarak Tempuh (KM)</th>
										<th style="text-align:center;" width="7%">Fuel Start (L)</th>
								<th style="text-align:center;" width="7%">Fuel End (L)</th> -->
									</tr>
								</thead>
								<tbody>
								 <?php if(isset($data) && (count($data) > 0)){
											for ($i=0;$i<count($data);$i++){

												if($data[$i]->ritase_report_beach_start_time == "" || $data[$i]->ritase_report_beach_end_time == "0000-00-00 00:00:00"){
													$startdate = "";
													$enddate = "";
												}else{
													$startdate = date("d-m-Y H:i:s", strtotime($data[$i]->ritase_report_beach_start_time));
													$enddate = date("d-m-Y H:i:s", strtotime($data[$i]->ritase_report_beach_end_time));
												}

											?>
											<tr>
												<td style="text-align:center;font-size:12px;"><?php echo $i+1;?></td>
												<td style="text-align:center;font-size:12px;"><?php echo date("d-m-Y H:i:s", strtotime($data[$i]->ritase_report_start_time));?></td>
												<td style="text-align:center;font-size:12px;"><?php echo date("d-m-Y H:i:s", strtotime($data[$i]->ritase_report_end_time));?></td>
												<td style="text-align:center;font-size:12px;"><?php echo $data[$i]->ritase_report_duration;?></td>
												<td style="text-align:center;font-size:12px;"><?php echo $data[$i]->ritase_report_vehicle_no;?></td>
												<td style="text-align:center;font-size:12px;"><?php echo $data[$i]->ritase_report_start_location;?></td>
												<td style="text-align:center;font-size:12px;"><?php echo $data[$i]->ritase_report_end_location;?></td>
												<td style="text-align:center;font-size:12px;"><?php echo $startdate;?></td>
												<td style="text-align:center;font-size:12px;"><?php echo $enddate;?></td>
												<td style="text-align:center;font-size:12px;"><?php echo $data[$i]->ritase_report_beach_duration;?></td>
												<td style="text-align:center;font-size:12px;"><?php echo $data[$i]->ritase_report_beach_start_location;?></td>
												<td style="text-align:center;font-size:12px;"><?php echo $data[$i]->ritase_report_beach_end_location;?></td>
												
												<td style="text-align:center;font-size:12px;"><?php echo $data[$i]->ritase_report_shift_name;?></td>
											</tr>
									<?php }?>
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
