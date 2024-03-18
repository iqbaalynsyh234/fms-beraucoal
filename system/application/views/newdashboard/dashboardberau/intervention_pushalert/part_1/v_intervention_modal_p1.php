<link href="<?php echo base_url();?>assets/dashboard/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url();?>assets/dashboard/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />

<div class="row" style="overflow-x:auto; height:516px;">

  <div class="col-md-12">
     <!-- style="height:410px" -->
    <p class="text-center">
      <!-- <b>Pelaksanaan Intervensi DMS NON Overspeed - Part I DEVELOPMENT</b> -->
      <b>Pelaksanaan Intervensi DMS</b>
    </p>
    <div class="text-center" id="notif" style="display:none;"></div>
    <div class="form">
      <div class="row">
        <div class="col-md-6">
          <table class="table table-striped" style="font-size:12px;">
            <input type="text" name="alert_id" id="alert_id" value="<?php echo $alert_id; ?>" class="form-control" hidden>
            <!-- <?php
            if (isset($data_karyawan[0]['karyawan_bc_sid'])) {?>
              <input type="text" name="intervention_sid" id="intervention_sid" value="<?php echo $data_karyawan[0]['karyawan_bc_sid']; ?>" class="form-control" hidden>
            <?php }else {?>
              <input type="text" name="intervention_sid" id="intervention_sid" value="" class="form-control" hidden>
            <?php } ?> -->
            <input type="text" name="intervention_date" id="intervention_date" class="form-control" value="<?php echo date("Y-m-d H:i:s", strtotime("+1 Hour")) ?>" hidden>
            <tr>
              <td>
                <video width="320" height="240" controls>
                  <?php
                    $not_our_device = $content[0]['alarm_report_insert_type'];
                      if ($not_our_device == "pushalert") {?>
                        <source src="<?php echo $content[0]['alarm_report_video_link'] ?>" type="video/mp4">
                      <?php }else {?>
                        <source src="<?php echo $urlvideo ?>" type="video/mp4">
                      <?php }
                   ?>
                </video>
              </td>
              <td>
                <?php
                  $not_our_device = $content[0]['alarm_report_insert_type'];
                    if ($not_our_device == "pushalert") {?>
                      <img src="<?php echo $content[0]['alarm_report_image_link'] ?>" width="300px" height="auto">
                    <?php }else {?>
                      <img src="<?php echo $content[0]['alarm_report_fileurl'] ?>" width="300px" height="auto">
                    <?php }
                 ?>
              </td>
            </tr>
          </table>
        </div>

        <div class="col-md-6">
          <table class="table table-striped" style="font-size:12px">
            <!-- <tr>
              <td>Alert ID</td>
              <td>
                <p style="font-size:12px;"><?php echo $content[0]['alarm_report_id'] ?></p>
              </td>
              <td></td>
              <td></td>
            </tr> -->

            <tr>
              <td>
                Alert ID <br> <br>
                Vehicle
              </td>
              <td>
                <input type="text" class="form-control" value="<?php echo $content[0]['alarm_report_id'] ?>" readonly> <br>
                <?php echo $content[0]['alarm_report_vehicle_no'] ?>
              </td>
              <td>Alarm</td>
              <td>
                <p style="font-size:12px; margin-top:6%; color:red;">
                  <?php echo $alarm_name; ?>
                </p>
              </td>
            </tr>

            <tr>
              <td>SID Operator</td>
              <td>
                <select class="form-control select2" name="intervention_sid" id="intervention_sid" style="width:180px;">
                  <?php for ($i=0; $i < sizeof($data_karyawan_all); $i++) {?>
                    <option value="<?php echo $data_karyawan_all[$i]['karyawan_bc_sid']; ?>"><?php echo $data_karyawan_all[$i]['karyawan_bc_sid'].' / '.$data_karyawan_all[$i]['karyawan_bc_name']; ?></option>
                  <?php } ?>
                </select>
              </td>

              <td>SID CR</td>
              <td>
                <?php if (isset($data_karyawan[0]['karyawan_bc_sid'])) {?>
                  <input type="text" name="intervention_sid_cr" id="intervention_sid_cr" value="<?php echo $data_karyawan[0]['karyawan_bc_sid']; ?>" class="form-control" readonly>
                <?php }else {?>
                  <select class="form-control select2" name="intervention_sid_cr" id="intervention_sid_cr" style="width:180px;">
                    <?php for ($i=0; $i < sizeof($data_karyawan_layer2up); $i++) {?>
                      <option value="<?php echo $data_karyawan_layer2up[$i]['karyawan_bc_sid']; ?>"><?php echo $data_karyawan_layer2up[$i]['karyawan_bc_sid'].' / '.$data_karyawan_layer2up[$i]['karyawan_bc_name']; ?></option>
                    <?php } ?>
                  </select>

                <?php } ?>
              </td>
            </tr>

            <!-- DATA FATIGUE FROM ANOTHER VENDOR  -->
            <?php
              $fatigue_category_array = array(
                "Look Down",
                "Head Down",
                "Yawning",
                "Yawning Detection",
                "Head Down",
                "Closed Eye"
              );
             ?>

            <tr>
              <?php
                if (in_array($content[0]['alarm_report_name'], $fatigue_category_array)) {?>
                  <td>
                    Fatigue Category
                  </td>
                <?php }else {?>

                <?php } ?>
                <?php
                  if (in_array($content[0]['alarm_report_name'], $fatigue_category_array)) {?>
                    <td>
                      <select class="form-control select2" name="fatigue_category" id="fatigue_category" style="width:180px; font-size:10px;">
                        <option value="Mata Memejam">Mata Memejam</option>
                        <option value="Menguap">Menguap</option>
                        <option value="Kepala Menunduk">Kepala Menunduk</option>
                      </select>
                    </td>
                  <?php }else {?>
                      <input type="hidden" name="fatigue_category" id="fatigue_category" value="0">
                  <?php } ?>
                  <td>Judgement</td>
                  <td>
                    <select class="form-control select2" name="intervention_judgement" id="intervention_judgement" style="width:180px;">
                      <option value="Low Risk">Low Risk</option>
                      <option value="Medium Risk">Medium Risk</option>
                      <option value="High Risk">High Risk</option>
                    </select>
                  </td>
            </tr>

            <tr>
              <td>Working Type</td>
              <td>
                <select class="form-control select2" name="intervention_working_type" id="intervention_working_type" style="width:180px;">
                  <option value="34">PEKERJA - OPERATOR / DRIVER - A2B</option>
                  <option value="35">PEKERJA - OPERATOR / DRIVER - HAULER</option>
                  <option value="36">PEKERJA - OPERATOR / DRIVER - SARANA & KENDARAAN</option>
                </select>
              </td>
              <td>Location</td>
              <td>
                <!-- <input type="text" name="intervention_location" id="intervention_location" value="<?php echo $data_location[0]['master_location_id_sync']; ?>" class="form-control" hidden>
                <input type="text" value="<?php echo $data_location[0]['master_location_parent_short_name']; ?>" class="form-control" readonly> -->

                <select class="form-control select2" name="intervention_location" id="intervention_location" style="width:180px;">
                  <?php for ($i=0; $i < sizeof($data_site); $i++) {?>
                    <option value="<?php echo $data_site[$i]['master_site_veryshortname_tambahan'] ?>"><?php echo $data_site[$i]['master_site_shortname']; ?></option>
                  <?php } ?>
                </select>
              </td>
            </tr>

            <tr>
              <td>Supervisor</td>
              <td>
                <input type="text" name="intervention_supervisor" id="intervention_supervisor" value="<?php echo $sid_pengawas_from_config[0]['config_supervisor_id'].'|'.$sid_pengawas_from_config[0]['config_supervisor_name']; ?>" class="form-control" hidden>
                <input type="text" value="<?php echo $sid_pengawas_from_config[0]['config_supervisor_id'].' / '.$sid_pengawas_from_config[0]['config_supervisor_name']; ?>" class="form-control" readonly>

                <!-- <select class="form-control select2" name="intervention_supervisor" id="intervention_supervisor" style="width:180px;">
                  <?php for ($i=0; $i < sizeof($data_karyawan_all); $i++) {?>
                    <option value="<?php echo $data_karyawan_all[$i]['karyawan_bc_company_id'].'|'.$data_karyawan_all[$i]['karyawan_bc_sid'].'|'.$data_karyawan_all[$i]['karyawan_bc_name']; ?>"><?php echo $data_karyawan_all[$i]['karyawan_bc_sid'].' / '.$data_karyawan_all[$i]['karyawan_bc_name']; ?></option>
                  <?php } ?>
                </select> -->
              </td>

              <td>Tanggal</td>
              <td>
                <input type="text" name="intervention_date" id="intervention_date" class="form-control" value="<?php echo date("Y-m-d H:i:s", strtotime("+1 Hour")) ?>" readonly>
              </td>
            </tr>

            <tr>
              <td>True / False Alarm</td>
              <td>
                <input type="radio" class="alarm" name="alarm_true_false" id="alarm_true" value="1"> True
                <input type="radio" class="alarm" name="alarm_true_false" id="alarm_false" value="2"> False
              </td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <!-- <td>True / False Alarm</td> -->
              <td>Intervensi *Wajib Dipilih</td>
              <td>
                <select class="form-control select2" name="intervention_category" id="intervention_category" style="width:180px;" onchange="change_type_intervention();">
                  <?php for ($i=0; $i < sizeof($type_intervention); $i++) {?>
                    <option value="<?php echo $type_intervention[$i]['intervention_type_id'].'|'.$type_intervention[$i]['intervention_type_name'] ?>"><?php echo $type_intervention[$i]['intervention_type_name']; ?></option>
                  <?php } ?>
                </select>
              </td>

              <td>Notes</td>
              <td>
                <select class="form-control select2" name="intervention_note" id="intervention_note" style="width:180px;">
                  <?php for ($i=0; $i < sizeof($type_note); $i++) {?>
                    <option value="<?php echo $type_note[$i]['type_note_name'] ?>"><?php echo $type_note[$i]['type_note_name']; ?></option>
                  <?php } ?>
                </select>
              </td>
            </tr>

              <tr>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-right">
                  <button type="button" class="btn btn-small btn-default" name="button" onclick="btnReset();">Reset</button>
                  <div class="btn btn-small btn-primary" name="button" onclick="btnSubmitIntervention();">Submit</div>
                </td>
              </tr>
          </table>
        </div>

      </div>


    </div>


  </div>

  <!-- <div class="row justify-content-center" style="margin-left:4px; margin-right:4px; margin-bottom:12px; background-color:#f2f2f2; padding-top:10px; padding-bottom:10px;">
    <div class="col-lg-5 col-md-6 col-sm-10 col-xs-12" style="padding-bottom: 2px; margin-bottom:2px;">
      <video controls style="padding-bottom: 2px; margin-bottom:2px; width:100%; height:auto; ">
        <source src='<?= $urlvideo; ?>' type='video/mp4'>
      </video>
    </div>
  </div> -->
</div>

<script src="<?php echo base_url();?>assets/dashboard/assets/plugins/select2/js/select2.js"></script>
<script src="<?php echo base_url();?>assets/dashboard/assets/js/pages/select2/select2-init.js"></script>

<script type="text/javascript">

$('.select2').each(function() {
    $(this).select2({ dropdownParent: $(this).parent()});
})

  function btnSubmitIntervention(){
    var alert_id                    = $('#alert_id').val();
    var tablenya                    = '<?php echo $tablenya ?>';
    var user_id                     = '<?php echo $this->sess->user_id ?>';
    var user_name                   = '<?php echo $this->sess->user_name ?>';
    var intervention_date           = $('#intervention_date').val();
    var alarm_name                  = "<?php echo $content[0]['alarm_report_name']; ?>";
    var image_url                   = '<?php echo $content[0]['alarm_report_fileurl']; ?>';
    var alarmtype                   = '<?php echo $content[0]['alarm_report_name']; ?>';
    var fatigue_category_array      = '<?php echo json_encode($fatigue_category_array); ?>';
    var alarm_start_time            = '<?php echo $content[0]['alarm_report_start_time']; ?>';
    var alarm_report_vehicle_no     = '<?php echo $content[0]['alarm_report_vehicle_no']; ?>';
    var alarm_report_vehicle_device = '<?php echo $content[0]['alarm_report_vehicle_id'].'@'.$content[0]['alarm_report_vehicle_type']; ?>';
    var alarm_true_false            = $("input[type='radio'][name='alarm_true_false']:checked").val();
    var intervention_category       = $("#intervention_category").val();
    var intervention_note           = $("#intervention_note").val();
    var intervention_sid            = $("#intervention_sid").val();
    var intervention_sid_cr         = $("#intervention_sid_cr").val();

    var intervention_judgement      = $('#intervention_judgement').val();
    var intervention_supervisor     = $('#intervention_supervisor').val();
    var intervention_working_type   = $('#intervention_working_type').val();
    var intervention_location       = $('#intervention_location').val();

    console.log("fat cat : ", fatigue_category_array);

    if (fatigue_category_array.includes(alarmtype)) {
      var fatigue_category  = $('#fatigue_category').val();
    }else {
      var fatigue_category  = 0;
    }

    if (alarm_true_false == undefined) {
      var alert = "<p style='color:red;'>Harap mengisi seluruh form dengan benar</p>";
      $("#notif").html(alert);
      $("#notif").fadeIn(1000);
      $("#notif").fadeOut(3000);
    }else {
      var data = {
        user_id:user_id,
        alarm_name:alarm_name,
        user_name:user_name,
        alert_id:alert_id,
        alarm_start_time:alarm_start_time,
        alarm_report_vehicle_no:alarm_report_vehicle_no,
        alarm_report_vehicle_device:alarm_report_vehicle_device,
        tablenya:tablenya,
        intervention_date:intervention_date,
        alarm_true_false:alarm_true_false,
        intervention_location:intervention_location,
        intervention_category:intervention_category,
        intervention_sid:intervention_sid,
        intervention_sid_cr:intervention_sid_cr,
        fatigue_category:fatigue_category,
        // itervention_alarm:itervention_alarm,
        intervention_note:intervention_note,
        intervention_judgement: intervention_judgement,
        intervention_supervisor: intervention_supervisor,
        intervention_working_type: intervention_working_type,
        image_url:image_url
      };

      console.log("data : ", data);
      $.post("<?php echo base_url() ?>dashboard_intervensi_postevent_pushalert/submit_intervention_controlroom_p1", data, function(response){
        console.log("response : ", response);
        if (response.error) {
          $("#loader2").hide();
          var alert = response.message;
          $("#notif").html(alert);
          $("#notif").fadeIn(1000);
          $("#notif").fadeOut(3000);
        }else {
          $("#loader2").hide();
          var alert = response.message;
          $("#notif").html(alert);
          $("#notif").fadeIn(1000);
          $("#notif").fadeOut(3000);
          $("#intervention_sid").val("");
          $("#intervention_note").val("");
          frmsearch_onsubmit();
        }
        return false;
      }, "json");
    }
  }

  function btnReset(){
    $("#intervention_sid").val("");
    $("#intervention_note").val("");
  }

  function change_type_intervention(){
    var intervention_category = $("#intervention_category").val();
    var interv_cat            = intervention_category.split("|");
    var data = {
      interv_type_id : interv_cat[0]
    };
    // console.log("interv_cat : ", interv_cat);
    $.post("<?php echo base_url() ?>dashboard_intervensi_postevent_pushalert/data_intervention_note", data, function(response){
      // console.log("response data_intervention_note : ", response);
        var data = response.data;
        $("#intervention_note").html("");

        var html = "";
        for (var i = 0; i < data.length; i++) {
          html += '<option value="'+data[i].type_note_name+'">'+data[i].type_note_name+'</option>';
        }
        $("#intervention_note").html(html);
    }, "json");
  }


</script>
