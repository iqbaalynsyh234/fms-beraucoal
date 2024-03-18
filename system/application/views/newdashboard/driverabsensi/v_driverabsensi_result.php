<style media="screen">
  #report{
    background-color: #221f1f;
    color: white;
  }
</style>


<script src="<?php echo base_url();?>assets/js/jsblong/jquery.table2excel.js"></script>
<script>
  jQuery(document).ready(
    function() {
      jQuery("#export_xcel").click(function() {
        window.open('data:application/vnd.ms-excel,' + encodeURIComponent(jQuery('#isexport_xcel').html()));
      });
    }
  );
</script>


<div class="col-lg-6 col-sm-6">
  <input id="btn_hide_form" class="btn btn-circle btn-danger" title="" type="button" value="Hide Form" onclick="javascript:return option_form('hide')" />
  <input id="btn_show_form" class="btn btn-circle btn-success" title="" type="button" value="Show Form" onClick="javascript:return option_form('show')" style="display:none" />
</div>
<div class="col-lg-2 col-sm-2"></div>
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
          <?php if (count($data) == 0) {
							echo "<p>No Data</p>";
					}else{ ?>
            <div class="col-md-12 col-sm-12">

              <div class="col-lg-4 col-sm-4">
                <a href="javascript:void(0);" id="export_xcel" type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect m-b-10 btn-circle btn-default"><small>Export to Excel</small></a>
              </div>

              <div id="isexport_xcel" style="overflow-x:auto;">
                <table class="table table-striped custom-table table-hover">
                  <thead>
                    <tr>
                      <th style="text-align:center;" width="3%">No</th>
                      <th style="text-align:center;" width="10%">Driver</th>
                      <th style="text-align:center;" width="10%">ID Card</th>
                      <th style="text-align:center;" width="10%">Shift</th>
                      <th style="text-align:center;" width="10%">Time</th>
                      <th style="text-align:center;" width="10%">Vehicle</th>
                      <th style="text-align:center;" width="10%">Face Detected</th>
					  <th style="text-align:center;" width="10%">Status Sync</th>
                      <th style="text-align:center;" width="10%">Clock In (WITA)</th>
                      <th style="text-align:center;" width="10%">Clock In Coord</th>
                      <th style="text-align:center;" width="10%">Clock Out (WITA)</th>
                      <th style="text-align:center;" width="10%">Clock Out Coord</th>
                      <th style="text-align:center;" width="10%">Duration</th>
					  <th style="text-align:center;" width="10%">Duration on Board</th>
					  <th style="text-align:center;" width="10%">Vaksinasi</th>
                      <th style="text-align:center;" width="10%">Status</th>
					  <th style="text-align:center;" width="10%"></th>
                    </tr>
                  </thead>
                  <tbody>


                    <?php
                  		if(isset($data) && (count($data) > 0)){

              				for ($i=0;$i<count($data);$i++)
              				{
								$start_time = $data[$i]['absensi_face_detected'];
								$end_time = $data[$i]['absensi_clock_out'];

								$duration = get_time_difference($start_time, $end_time);

									$start_1 = dbmaketime($start_time);
									$end_1 = dbmaketime($end_time);
									$duration_sec = $end_1 - $start_1;

                                    $show = "";
                                    if($duration[0]!=0)
                                    {
                                        $show .= $duration[0] ." Day ";
                                    }
                                    if($duration[1]!=0)
                                    {
                                        $show .= $duration[1] ." Hour ";
                                    }
                                    if($duration[2]!=0)
                                    {
                                        $show .= $duration[2] ." Min ";
                                    }
                                    if($show == "")
                                    {
                                        $show .= "0 Min";
                                    }


            				?>
                      <tr>
                        <td style="text-align:center;font-size:12px;">
                          <?php echo $i+1;?>
                        </td>
                        <td style="text-align:center;font-size:12px;">
                          <?php echo $data[$i]['absensi_driver_name'] ?>
                        </td>
                        <td style="text-align:center;font-size:12px;">
                          <?php echo $data[$i]['absensi_driver_idcard'] ?>
                        </td>
                        <td style="text-align:center;font-size:12px;">
                          <?php echo $data[$i]['absensi_shift_type'] ?>
                        </td>
                        <td style="text-align:center;font-size:12px;">
                          <?php echo $data[$i]['absensi_shift_time'] ?>
                        </td>
                        <td style="text-align:center;font-size:12px;">
                          <?php echo $data[$i]['absensi_vehicle_no'] ?>
                        </td>
                        <td style="text-align:center;font-size:12px;">
                           <?php echo date("d-m-Y H:i:s", strtotime($data[$i]['absensi_face_detected']));?>
                        </td>

						<td style="text-align:center;font-size:12px;">
						<?php
							if($data[$i]['absensi_vehicle_manual'] == 1){

								echo "Manual";
							}else{

								echo "Sync";
							}
						?>
                        </td>

                        <td style="text-align:center;font-size:12px;">
                          <?php echo date("d-m-Y H:i:s", strtotime($data[$i]['absensi_clock_in']));?>
                        </td>
                        <td style="text-align:center;font-size:12px;">
                          <a href="https://www.google.com/maps/search/?api=1&query=<?php echo $data[$i]['absensi_clock_in_coord'] ?>" target="_blank">
                            <?php echo $data[$i]['absensi_clock_in_coord'] ?>
                          </a>
                        </td>
                        <td style="text-align:center;font-size:12px;">
                          <?php
                            if (isset($data[$i]['absensi_clock_out'])) {
                              echo date("d-m-Y H:i:s", strtotime($data[$i]['absensi_clock_out']));
                            }
                           ?>
                        </td>
                        <td style="text-align:center;font-size:12px;">
                          <a href="https://www.google.com/maps/search/?api=1&query=<?php echo $data[$i]['absensi_clock_out_coord'] ?>" target="_blank">
                            <?php echo $data[$i]['absensi_clock_out_coord'] ?>
                          </a>
                        </td>
                        <td style="text-align:center;font-size:12px;">
                          <?php echo $data[$i]['absensi_duration'];?>
                        </td>
						<td style="text-align:center;font-size:12px;">
                          <?php echo $show; ?>
                        </td>
						<td style="text-align:center;font-size:12px;">
                          <?php echo "Sudah Vaksin Kedua"?>
                        </td>
                        <td style="text-align:center;font-size:12px;">
                          <?php
                            $statusabsensi = $data[$i]['absensi_status'];
                              if ($statusabsensi == 1) {?>
                                <button type="button" class="btn btn-warning">
                                  Sedang Beroperasi
                                </button>
                              <?php }else {?>
                                <button type="button" class="btn btn-success">
                                  Selesai
                                </button>
                              <?php } ?>
                        </td>

                        <td style="text-align:center;font-size:12px;">
                          <button type = "button" class="btn btn-primary btn-sm" onclick="imagepopup('<?php echo $data[$i]['absensi_photo_txt']; ?>')">
                              <span class="fa fa-image"></span>
                          </button>
                        </td>
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

<div id="modalState" class="modal">
  <div class="modal-content-state" style="width:40%;">
    <div class="row">
      <div class="col-md-10">
        <p class="modalTitleforAll" id="modalStateTitle">
          Foto Absensi Driver
        </p>
        <div id="contractorinlocation" style="font-size:14px; color:black"></div>
        <div id="lastcheckpoolws" style="font-size:12px; color:black"></div>
      </div>
      <div class="col-md-2">
        <div class="closethismodalall btn btn-danger btn-sm" onclick="closemodal();" style="margin-left:28%;">X</div>
      </div>
    </div>
    <div id="imgdriver" class="text-center"></div>
  </div>
</div>

<script type="text/javascript">
  function imagepopup(img){
    // console.log("img : ", img);
    $("#imgdriver").html('<img src="'+img+'" width="auto" height="300">');
    $("#modalState").show();
  }

  function closemodal(){
    console.log("closethismodal");
    $("#modalState").hide();
  }
</script>
