<style media="screen">
  #report-wim{
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

  // function getdetailinfo(id){
  //  $("#loadernya").show();
  //   var data = id.split(",");
  //   console.log("data detail 1 : ", data[0]);
  //   console.log("data detail 2 : ", data[1]);
  //   console.log("data detail 3 : ", data[2]);
  //   var imagenya =
  //     $("#modalinformationdetail").show();
  //     var imgfix = '<img src="<?php echo $data ?>" alt="">';
  //     $("#contentinformationdetail").html(response.html);
  //
  //   // $.post("<?php echo base_url() ?>securityevidence/getinfodetail", {alert_id : data[0], sdate : data[1]}, function(response){
  //   // $("#loadernya").hide();
  //   //   console.log("response getdetailinfo: ", response);
  //   //   $("#contentinformationdetail").html(response.html);
  //   //   $("#modalinformationdetail").show();
  //   // }, "json");
  // }

  function closemodallistofvehicle(){
    $("#modalinformationdetail").hide();
  }
</script>

<div class="row">
	<div class="col-md-12 col-sm-12">
		<div class="panel">
      <div id="modalinformationdetail" style="display: none;">
        <div id="mydivheader"></div>
        <div id="contentinformationdetail">

        </div>
      </div>
			<header class="panel-heading" id="report-wim">REPORT WIM</header>
				<div class="panel-body" id="bar-parent10">
					<div class="row">
					<?php if (count($data) == 0) {
							echo "<p>No Data</p>";
					}else{ ?>
						<div class="col-md-12 col-sm-12">

							<div class="col-lg-4 col-sm-4">
                <!-- <button type="button" name="button" id="showexportview" class="btn btn-danger btn-sm" onclick="showexportview();">Export View</button>
                <button type="button" name="button" id="hideexportview" class="btn btn-danger btn-sm" onclick="hideexportview();" style="display:none;">Show Detail</button> -->
								<a href="javascript:void(0);" id="export_xcel" type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect m-b-10 btn-circle btn-info"><small>Export to Excel</small></a>
							</div>

							<div id="isexport_xcel" style="overflow-x:auto;">
							<table class="table table-striped custom-table table-hover" style="font-size:10px; width:100%;overflow-x:auto;">
								<thead>
									<tr>
										<th width="3%" valign="top" style="text-align:center;">No</th>
										<th width="8%" valign="top" style="text-align:center;">WIM TransID</th>
										<th width="8%" valign="top" style="text-align:center;">WIM Mode</th>
										<th width="5%" valign="top" style="text-align:center;">Truck</th>
										<th width="9%" valign="top" style="text-align:center;">Driver</th>
										<th width="8%" valign="top" style="text-align:center;">Client</th>
										<th width="8%" valign="top" style="text-align:center;">Material</th>
										<th width="8%" valign="top" style="text-align:center;">Hauling</th>
										<th width="8%" valign="top" style="text-align:center;">Last ROM</th>
										<th width="8%" valign="top" style="text-align:center;">DateTimeGross</th>
										<th width="8%" valign="top" style="text-align:center;">DateTimeNetto</th>
										<th width="5%" valign="top" style="text-align:center;">Code</th>
										<th width="7%" valign="top" style="text-align:center;">Gross</th>
										<th width="7%" valign="top" style="text-align:center;">Tare</th>
										<th width="7%" valign="top" style="text-align:center;">Netto</th>
										<th width="5%" valign="top" style="text-align:center;">Coal</th>
										<th width="7%" valign="top" style="text-align:center;">Slip</th>
										<th width="7%" valign="top" style="text-align:center;">Doc</th>
										<th width="8%" valign="top" style="text-align:center;">Dumping</th>
										<th width="8%" valign="top" style="text-align:center;">CP</th>

										<th width="5%" valign="top" style="text-align:center;">RFID</th>
										<th width="7%" valign="top" style="text-align:center;">Total Gandar</th>
										<th width="8%" valign="top" style="text-align:center;">Berat Tiap Gandar</th>
										<th width="5%" valign="top" style="text-align:center;">Average Speed(km/h)</th>
										<th width="5%" valign="top" style="text-align:center;">Weight Balance(%)</th>
										<th width="8%" valign="top" style="text-align:center;">Remark</th>
										<th width="8%" valign="top" style="text-align:center;">Status</th>
										<!--<th width="3%" valign="top" style="text-align:center;" id="detaildata">Detail</th>-->
									</tr>
								</thead>
								<tbody>
                    <?php
                         if (isset($data)) {
                           for ($i=0; $i < sizeof($data); $i++) {?>
                      <tr>
                        <td valign="top" style="text-align:center;">
                          <?php echo $i+1; ?>
                        </td>
												<td valign="top" style="text-align:center;">
						                          <?php echo $data[$i]['integrationwim_transactionID']; ?>
						                        </td>
						                        <td valign="top" style="text-align:center;">
						                          <?php echo $data[$i]['integrationwim_status']; ?>
						                        </td>
						<td valign="top" style="text-align:center;">
                          <?php echo $data[$i]['integrationwim_truckID']; ?>
                        </td>
						<td valign="top" style="text-align:center;">
                          <?php echo $data[$i]['integrationwim_driver_id']; ?>
                        </td>
						<td valign="top" style="text-align:center;">
                          <?php echo $data[$i]['integrationwim_client_id']; ?>
                        </td>
						<td valign="top" style="text-align:center;">
                          <?php echo $data[$i]['integrationwim_material_id']; ?>
                        </td>
						<td valign="top" style="text-align:center;">
                          <?php echo $data[$i]['integrationwim_hauling_id']; ?>
                        </td>
						<td valign="top" style="text-align:center;">

						<!-- <?php
							if($data[$i]['integrationwim_itws_datetimetrans'] != ""){
								echo date("d-m-Y H:i:s", strtotime($data[$i]['integrationwim_itws_datetimetrans']));
							}
						?> -->
						<?php echo $data[$i]['integrationwim_last_rom']; ?>
                        </td>
						 <td valign="top" style="text-align:center;">
                          <?php echo date("d-m-Y H:i:s", strtotime($data[$i]['integrationwim_penimbanganStartLocal']));?>
                        </td>
                        <td valign="top" style="text-align:center;">
                          <?php echo date("d-m-Y H:i:s", strtotime($data[$i]['integrationwim_penimbanganFinishLocal']));?>
                        </td>
						<td valign="top" style="text-align:center;">
                          <?php echo $data[$i]['integrationwim_haulingContractor']; ?>
                        </td>
						<td valign="top" style="text-align:center;">
                          <?php echo $data[$i]['integrationwim_gross']; ?>
                        </td>
                        <td valign="top" style="text-align:center;">
                          <?php echo $data[$i]['integrationwim_tare']; ?>
                        </td>
                        <td valign="top" style="text-align:center;">
                          <?php echo $data[$i]['integrationwim_netto']; ?>
                        </td>
						<td valign="top" style="text-align:center;">
                          <?php echo $data[$i]['integrationwim_itws_coal']; ?>
                        </td>
						<td valign="top" style="text-align:center;">
                          <?php echo $data[$i]['integrationwim_itws_slip']; ?>
                        </td>
						<td valign="top" style="text-align:center;">
                          <?php echo $data[$i]['integrationwim_other_text1']; ?>
                        </td>
						<td valign="top" style="text-align:center;">
                          <?php echo $data[$i]['integrationwim_dumping_fms_port']; ?>
                        </td>
						<td valign="top" style="text-align:center;">
                          <?php echo $data[$i]['integrationwim_dumping_fms_cp']; ?>
                        </td>

						<td valign="top" style="text-align:center;">
						    <?php echo $data[$i]['integrationwim_rfid']; ?>
                        </td>
                        <td valign="top" style="text-align:center;">
                          <?php echo $data[$i]['integrationwim_totalGandar']; ?>
                        </td>
						<td valign="top" style="text-align:center;">
                          <?php echo $data[$i]['integrationwim_beratTiapGandar']; ?>
                        </td>
						<td valign="top" style="text-align:center;">
                          <?php echo $data[$i]['integrationwim_averageSpeed']; ?>
                        </td>
						<td valign="top" style="text-align:center;">
                          <?php echo $data[$i]['integrationwim_weightBalance']; ?>
                        </td>
						<td valign="top" style="text-align:center;">
                          <?php echo $data[$i]['integrationwim_distanceWB']; ?> m
                        </td>

												<td valign="top" style="text-align:center;">
													<?php
			                        if($data[$i]['integrationwim_operator_status'] == 0){
			                          echo "UNPROCESS";
			                        }else if($data[$i]['integrationwim_operator_status'] == 1){
			                          echo "UPDATED BY OPERATOR";
			                        }else if($data[$i]['integrationwim_operator_status'] == 2){
			                          echo "UPDATED BY ADMIN";
			                        }else if($data[$i]['integrationwim_operator_status'] == 3){
			                          echo "REJECTED";
			                        }else{
			                          echo "-";
			                        }
	                         ?>
                        </td>

                      </tr>
                      <?php    }?>

                        <?php }else{ ?>
                          <tr>
                            <td colspan="4">No Available Data</td>
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

<script type="text/javascript">
function showexportview(){
  $("#detaildata").hide();
  $("#showexportview").hide();
  $("#hideexportview").show();
  $("#export_xcel").show();
  var datareport = '<?php echo json_encode($data)?>';
  var obj        = JSON.parse(datareport);
  console.log("datareport report : ", datareport);
  console.log("obj report : ", obj);
  for (var i = 0; i < obj.length; i++) {
    $("#detaildatatd"+i).hide();
  }
}

function hideexportview(){
  $("#detaildata").show();
  $("#showexportview").show();
  $("#hideexportview").hide();
  $("#export_xcel").hide();
  var datareport = '<?php echo json_encode($data)?>';
  var obj        = JSON.parse(datareport);
  console.log("datareport report : ", datareport);
  console.log("obj report : ", obj);
  for (var i = 0; i < obj.length; i++) {
    $("#detaildatatd"+i).show();
  }
}


</script>
