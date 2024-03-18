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
			<header class="panel-heading panel-heading-blue">REPORT</header>
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

							<div id="isexport_xcel">
							<table class="table table-striped custom-table table-hover" style="font-size:14px; width:100%;">
								<thead>
									<tr>
										<th width="3%" valign="top" style="text-align:center;">No</td>
										<th width="10%" valign="top" style="text-align:center;">Trans ID</th>
										<th width="5%" valign="top" style="text-align:center;">Truck ID</th>
										<th width="7%" valign="top" style="text-align:center;">Penimbangan Start</th>
										<th width="11%" valign="top" style="text-align:center;">Penimbangan Finish</th>
                    <th width="11%" valign="top" style="text-align:center;">Berat Tiap Gandar</th>
                    <th width="11%" valign="top" style="text-align:center;">Gross</th>
                    <th width="11%" valign="top" style="text-align:center;">Tare</th>
                    <th width="11%" valign="top" style="text-align:center;">Netto</th>
                    <th width="11%" valign="top" style="text-align:center;">Total Gandar</th>
                    <th width="11%" valign="top" style="text-align:center;">Average Speed</th>
										<th width="3%" valign="top" style="text-align:center;" id="detaildata">Detail</td>
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
                          <?php echo $data[$i]['integrationwim_truckID']; ?>
                        </td>
                        <td valign="top" style="text-align:center;">
                          <?php echo date("d-m-Y H:i:s", strtotime($data[$i]['integrationwim_penimbanganStartLocal']));?>
                        </td>
                        <td valign="top" style="text-align:center;">
                          <?php echo date("d-m-Y H:i:s", strtotime($data[$i]['integrationwim_penimbanganFinishLocal']));?>
                        </td>
                        <td valign="top" style="text-align:center;">
                          <?php echo $data[$i]['integrationwim_beratTiapGandar']; ?>
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
                          <?php echo $data[$i]['integrationwim_totalGandar']; ?>
                        </td>
                        <td valign="top" style="text-align:center;">
                          <?php echo $data[$i]['integrationwim_averageSpeed']; ?>
                        </td>
												<td id="detaildatatd<?php echo $i?>">
                          <!-- <img src="<?php echo $data[$i]['integrationwim_TruckImage']; ?>" width="100" height="100"> -->
													<!-- onclick="getdetailinfo('<?php echo $data[$i]['integrationwim_TransactionID'].','.$data[$i]['integrationwim_PenimbanganStartLocal'].','.$i ?>');" -->
                          <button type="button" class="btn btn-primary" >
                            <span class="fa fa-list"></span>
                          </button>
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
