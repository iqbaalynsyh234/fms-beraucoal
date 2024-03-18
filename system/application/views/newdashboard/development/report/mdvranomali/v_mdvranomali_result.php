<style media="screen">
#security-evidence{
	background-color: #221f1f;
	color: white;
}
</style>
<script src="<?php echo base_url();?>assets/js/jsblong/jquery.table2excel.js"></script>
<script>
jQuery(document).ready(
		function()
		{
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
        link.download = "MDVR-Anomali.csv";
        link.click();
        //window.open('data:application/csv;charset=utf-8,' + encodeURIComponent(csvData));
      });
		}
	);


</script>


							<!-- <div class="col-lg-6 col-sm-6">
								<input id="btn_hide_form" class="btn btn-circle btn-danger" title="" type="button" value="Hide Form" onclick="javascript:return option_form('hide')" />
								<input id="btn_show_form" class="btn btn-circle btn-success" title="" type="button" value="Show Form" onClick="javascript:return option_form('show')" style="display:none"/>
							</div> -->
							<div class="col-lg-2 col-sm-2">
							</div>
							<br />

<div class="row">
	<div class="col-md-12 col-sm-12">
		<div class="panel" >
			<header class="panel-heading" id="security-evidence">RESULT</header>
				<div class="panel-body" id="bar-parent10">
					<div class="row">
					<?php if (count($data) == 0) {
							echo "<p>NO DATA AVAILABLE</p>";
					}else{ ?>
						<div class="col-md-12 col-sm-12">

							<div class="col-lg-4 col-sm-4">
                <a href="javascript:void(0);" id="export_xcel" type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect m-b-10 btn-circle btn-default"><small>Export to CSV</small></a>
              </div>

							<div id="isexport_xcel" style="overflow-y:auto;">
							<table class="table table-striped custom-table table-hover" style="font-size:12px;">
								<thead>
                  <tr>
                    <th>No</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Vehicle No</th>
                    <th>Company</th>
                    <th>Violation</th>
                    <th>Location</th>
                    <th>Date Opr</th>
                    <th>Shift</th>
                    <th>Jalur</th>
                    <th>Week</th>
                    <th>Month</th>
                    <th>Coordinate</th>
                    <!-- <th>Speed (Kph)</th>
                    <th>Speed Limit</th>
                    <th>Geofence</th> -->
                  </tr>
								</thead>
								<tbody>
									<?php
									if (count($data)>0)
									{
                    $no = 1;
                    for ($i=0; $i < sizeof($data); $i++) {?>
                      <tr>
                        <td><?php echo $no ?></td>
                        <td><?php echo date("Y-m-d", strtotime($data[$i]['datetime'])) ?></td>
                        <td><?php echo date("H:i:s", strtotime($data[$i]['datetime'])) ?></td>
                        <td><?php echo $data[$i]['vehicle_no'] ?></td>
                        <td><?php echo $data[$i]['vehicle_company_name'] ?></td>
                        <td><?php echo $data[$i]['gps_alert'] ?></td>
                        <td><?php echo $data[$i]['position'] ?></td>
                        <td>
                          <?php
                            $jam = date("H:i:s", strtotime($data[$i]['datetime']));
                              if ($jam >= "06:00:00" && $jam <= "23:59:59") {
                                // $shift_opr = "Shift 1";
                                echo date("Y-m-d", strtotime($data[$i]['datetime']));
                              }else {
                                // $shift_opr = "Shift 2";
                                echo date("Y-m-d", strtotime($data[$i]['datetime'] . '-1 Day'));
                              }
                            ?>
                        </td>
                        <td>
                          <?php
                            $jam = date("H:i:s", strtotime($data[$i]['datetime']));
                              if ($jam >= "06:00:00" && $jam <= "18:00:00") {
                                echo "Shift 1";
                              }else {
                                echo " Shift 2";
                              }
                           ?>
                        </td>
                        <td><?php echo $data[$i]['jalur_name'] ?></td>
                        <td>
                          <?php
                          $ddate = date("Y-m-d", strtotime($data[$i]['datetime']));
                          $duedt = explode("-", $ddate);
                            $date  = mktime(0, 0, 0, $duedt[1], $duedt[2], $duedt[0]);
                            $week  = (int)date('W', $date);
                            echo $week;
                           ?>
                        </td>
                        <td><?php echo date("F", strtotime($data[$i]['datetime'])) ?></td>
                        <td>
                          <a href="https://maps.google.com/?q=<?php echo $data[$i]['gps_latitude_real'].','.$data[$i]['gps_longitude_real'] ?>" target="_blank"><?php echo $data[$i]['gps_latitude_real'].','.$data[$i]['gps_longitude_real'] ?></a>
                        </td>
                      </tr>
                    <?php $no++; }
									}else{
										echo "<tr><td colspan='12'><small>No Data Available</td></tr>";
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
