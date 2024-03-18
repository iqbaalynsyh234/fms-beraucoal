<style  media="screen">
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
                      <th style="text-align:center;" width="10%">Vehicle</th>
                      <th style="text-align:center;" width="10%">Breakdown Start (WITA)</th>
                      <th style="text-align:center;" width="10%">Coordinate Start</th>
                      <th style="text-align:center;" width="10%">Breakdown End (WITA)</th>
                      <th style="text-align:center;" width="10%">Coordinate End</th>
                      <th style="text-align:center;" width="10%">Duration</th>
					  <th style="text-align:center;" width="10%">Keterangan</th>
                      <th style="text-align:center;" width="10%">Status</th>
					  <th style="text-align:center;" width="10%"></th>
                    </tr>
                  </thead>
                  <tbody>


                    <?php
                  		if(isset($data) && (count($data) > 0)){
							
              				for ($i=0;$i<count($data);$i++)
              				{
								
							?>
                      <tr>
                        <td style="text-align:center;font-size:12px;">
                          <?php echo $i+1;?>
                        </td>
                        <td style="text-align:center;font-size:12px;">
                          <?php echo $data[$i]['breakdown_driver_name'] ?>
                        </td>
                        <td style="text-align:center;font-size:12px;">
                          <?php echo $data[$i]['breakdown_driver_idcard'] ?>
                        </td>
						<td style="text-align:center;font-size:12px;">
                          <?php echo $data[$i]['breakdown_vehicle_no'] ?>
                        </td>
                        <td style="text-align:center;font-size:12px;">
                          <?php echo date("d-m-Y H:i:s", strtotime($data[$i]['breakdown_start_time']));?>
                        </td>
                        <td style="text-align:center;font-size:12px;">
                          <a href="https://www.google.com/maps/search/?api=1&query=<?php echo $data[$i]['breakdown_start_coord'] ?>" target="_blank">
                            <?php echo $data[$i]['breakdown_start_coord'] ?>
                          </a>
                        </td>
                        <td style="text-align:center;font-size:12px;">
                          <?php
                            if (isset($data[$i]['breakdown_finish_time'])) {
                              echo date("d-m-Y H:i:s", strtotime($data[$i]['breakdown_finish_time']));
                            }
                           ?>
                        </td>
                        <td style="text-align:center;font-size:12px;">
                          <a href="https://www.google.com/maps/search/?api=1&query=<?php echo $data[$i]['breakdown_finish_coord'] ?>" target="_blank">
                            <?php echo $data[$i]['breakdown_finish_coord'] ?>
                          </a>
                        </td>
                        <td style="text-align:center;font-size:12px;">
                          <?php echo $data[$i]['breakdown_duration'];?>
                        </td>
						<td style="text-align:center;font-size:12px;">
                          <?php echo $data[$i]['breakdown_info'] ?>
                        </td>
					
                        <td style="text-align:center;font-size:12px;">
                          <?php
                            $statusbreakdown = $data[$i]['breakdown_status'];
                              if ($statusbreakdown == 1) {?>
                                <button type="button" class="btn btn-warning">
                                  Selesai
                                </button>
                              <?php }else {?>
                                <button type="button" class="btn btn-success">
                                  Belum Selesai
                                </button>
                              <?php } ?>
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

<div id="modalMapSetting" class="modal">
  <div class="modal-content" style="width:40%">
    <div class="row">
      <div class="col-md-10">
        <p class="modalCustomTitle">
          Foto Absensi Driver
        </p>
      </div>
      <div class="col-md-2">
        <div class="btn btn-danger btn-sm" onclick="closemodal()" style="margin-left: 10%;">X</div>
      </div>
    </div>
    <div id="imgdriver" class="text-center"></div>
  </div>
</div>

<script type="text/javascript">
  function imagepopup(img){
    // console.log("img : ", img);
    $("#imgdriver").html('<img src="'+img+'" width="auto" height="300">');
    $(".modal").show();
  }

  function closemodal(){
    $(".modal").hide();
  }
</script>
