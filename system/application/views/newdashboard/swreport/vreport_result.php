<script src="<?php echo base_url();?>assets/js/jsblong/jquery.table2excel.js"></script>
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
        link.download = "sw-report-" + textCompany + "-" + textVehicle + ".csv";
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
				<td style="text-align:center;"><small>Periode: <?php echo $startdate." - ".$enddate;?></small></td>
				
			</tr>

			<header class="panel-heading panel-heading-blue" style="background-color: #221f1f;">REPORT</header>
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
									
										<th style="text-align:center;" width="10%">IMEI</th>
										<th style="text-align:center;" width="8%">Date</th>
										<th style="text-align:center;" width="8%">Time</th>
										<th style="text-align:center;" width="5%">Heart Rate</th>
										<th style="text-align:center;" width="8%">Blood Pressure (mmHg)</th>
										<th style="text-align:center;" width="8%">Body Temperature (°C)</th>
										<th style="text-align:center;" width="7%">Blood Oxygen (%)</th>
										<th style="text-align:center;" width="8%">Step Counter</th>
										<th style="text-align:center;" width="5%">Sleep</th>
									</tr>
								</thead>
								<tbody>
								 <?php if(isset($data) && (count($data) > 0)){
											for ($i=0;$i<count($data);$i++){ 
												if($data[$i]->gps_bp_sys != "" && $data[$i]->gps_bp_dia != ""){
													$bp_status = $data[$i]->gps_bp_sys."/".$data[$i]->gps_bp_dia;
												}else{
													$bp_status = "";
													
												}
											
											
											?>
												
												
											
											<tr>
												<td style="text-align:center;font-size:12px;"><?php echo $i+1;?></td>
												<td style="text-align:center;font-size:12px;"><?php echo $data[$i]->gps_name;?></td>
												<td style="text-align:center;font-size:12px;"><?php echo date("d-m-Y", strtotime($data[$i]->gps_ht_time));?></td>
												<td style="text-align:center;font-size:12px;"><?php echo date("H:i:s", strtotime($data[$i]->gps_ht_time));?></td>
												<td style="text-align:center;font-size:12px;"><?php echo $data[$i]->gps_hr_rate;?></td>
												<td style="text-align:center;font-size:12px;"><?php echo $bp_status;?></td>
												<td style="text-align:center;font-size:12px;"><?php echo $data[$i]->gps_temp;?></td>
												<td style="text-align:center;font-size:12px;"><?php echo $data[$i]->gps_oxy;?></td>
												<td style="text-align:center;font-size:12px;"><?php echo $data[$i]->gps_step;?></td>
												<td style="text-align:center;font-size:12px;"><?php echo $data[$i]->gps_sleep;?></td>
										
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
