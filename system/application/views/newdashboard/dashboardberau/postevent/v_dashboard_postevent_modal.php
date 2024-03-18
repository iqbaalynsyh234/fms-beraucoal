
<div class="row">

  <div class="col-md-12">
    <p class="text-center">
      <b>Pelaksanaan Post Event DMS</b>
    </p>
    <div class="text-center" id="notif" style="display:none;"></div>
    <table class="table table-striped" style="font-size:12px;">
        <tr>
          <td>Tanggal</td>
          <td>
            <input type="text" name="intervention_date" id="intervention_date" class="form-control" value="<?php echo date("Y-m-d H:i:s") ?>" readonly >
            <!-- <div class="form-group row" id="mn_sdate">
              <div class="input-group date form_date col-md-12" data-date="" data-date-format="dd-mm-yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
                  <input class="form-control" size="5" type="text" readonly name="startdate" id="startdate" value="<?=date('d-m-Y',strtotime("yesterday") )?>">
                  <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
              </div>
              <input type="hidden" id="dtp_input2" value="" />
            </div> -->
          </td>
          <td>No Alert / ID</td>
          <td>
            <input type="text" name="alert_id" id="alert_id" value="<?php echo $alert_id; ?>" class="form-control" readonly>
          </td>
        </tr>
        <tr>
          <td>Control Room</td>
          <td>
            <?php
            $control_room = $content[0]['alarm_report_spvname_cr'];
              if ($control_room == "") {
                $control_room = $content[0]['alarm_report_name_up'];
              }
             ?>
            <input type="text" name="itervention_name" id="itervention_name" class="form-control" value="<?php echo $control_room; ?>" readonly>
          </td>

          <td>Supervisor</td>
          <!-- <td>
            <select class="form-control select2" name="itervention_sid" id="itervention_sid" style="width:180px;">
              <?php for ($i=0; $i < sizeof($data_karyawan_bc); $i++) {?>
                <option value="<?php echo $data_karyawan_bc[$i]['karyawan_bc_sid'].'|'.$data_karyawan_bc[$i]['karyawan_bc_name']; ?>"><?php echo $data_karyawan_bc[$i]['karyawan_bc_sid'].' / '.$data_karyawan_bc[$i]['karyawan_bc_name']; ?></option>
              <?php } ?>
            </select>
          </td> -->

          <td>
            <?php
              $sid_pengawas = sizeof($sid_pengawas_from_config);
                if ($sid_pengawas > 0) {?>
                  <input type="text" name="itervention_sid" id="itervention_sid" value="<?php echo $sid_pengawas_from_config[0]['config_supervisor_id'].'|'.$sid_pengawas_from_config[0]['config_supervisor_name']; ?>" class="form-control" hidden>
                  <input type="text" value="<?php echo $sid_pengawas_from_config[0]['config_supervisor_id'].' / '.$sid_pengawas_from_config[0]['config_supervisor_name']; ?>" class="form-control" readonly>
                <?php }else {?>
                  <select class="form-control select2" name="itervention_sid" id="itervention_sid" style="width:180px;">
                    <?php for ($i=0; $i < sizeof($data_karyawan_bc); $i++) {?>
                      <option value="<?php echo $data_karyawan_bc[$i]['karyawan_bc_sid'].'|'.$data_karyawan_bc[$i]['karyawan_bc_name']; ?>"><?php echo $data_karyawan_bc[$i]['karyawan_bc_sid'].' / '.$data_karyawan_bc[$i]['karyawan_bc_name']; ?></option>
                    <?php } ?>
                  </select>
                <?php }
             ?>
          </td>
        </tr>

        <tr>
          <?php
            if ($content[0]['alarm_report_type'] == 618 || $content[0]['alarm_report_type'] == 619) {?>

            <?php }else {?>
              <td></td>
            <?php }?>
          <td>
            Status By CR :
            <?php
              $alarm_report_truefalse_up = $content[0]['alarm_report_truefalse_up'];
                if ($alarm_report_truefalse_up == 1) {
                  echo "True";
                }elseif ($alarm_report_truefalse_up == 2) {
                  echo "False";
                }else {
                  echo "Belum Diintervensi";
                }
             ?>
          </td>
          <td>Intervensi *Wajib Dipilih</td>
          <?php
            if ($content[0]['alarm_report_type'] == 618 || $content[0]['alarm_report_type'] == 619) {?>
              <td>
                Fatigue Category : <?php echo $content[0]['alarm_report_fatiguecategory_cr'] ?>
              </td>

              <td>
                <?php
                if (isset($content[0]['alarm_report_intervention_category_cr'])) {
                  $type_intervention = $content[0]['alarm_report_intervention_category_cr'];
                }else {
                  $type_intervention = "";
                }

                if ($type_intervention == "" || $type_intervention == Null) {
                  echo "Intervention Status";
                }else {
                  echo "Intervention Type";
                }
                 ?>
              </td>
            <?php }else {?>
              <td>
                <?php
                if (isset($content[0]['alarm_report_intervention_category_cr'])) {
                  $type_intervention = $content[0]['alarm_report_intervention_category_cr'];
                }else {
                  $type_intervention = "";
                }

                if ($type_intervention == "" || $type_intervention == Null) {
                  echo "Intervention Status";
                }else {
                  echo "Intervention Type";
                }
                 ?>
              </td>
            <?php } ?>
        </tr>
        <tr>
          <?php
            if ($content[0]['alarm_report_type'] == 618 || $content[0]['alarm_report_type'] == 619) {?>

            <?php }else {?>
              <td></td>
            <?php }?>
          <td>
            <input type="radio" class="alarm" name="alarm_true_false" id="alarm_true" value="1"> Sesuai
            <input type="radio" class="alarm" name="alarm_true_false" id="alarm_false" value="0"> Tidak Sesuai
          </td>
          <td>
            <input type="radio" name="itervention_alarm" id="itervention_alarm_sesuai" value="1"> Sesuai
            <input type="radio" name="itervention_alarm" id="itervention_alarm_tidaksesuai" value="0"> Tidak Sesuai
          </td>
          <?php
            if ($content[0]['alarm_report_type'] == 618 || $content[0]['alarm_report_type'] == 619) {?>
              <td>
                <select class="form-control select2" name="fatigue_category" id="fatigue_category">
                  <option value="Mata Memejam">Mata Memejam</option>
                  <option value="Menguap">Menguap</option>
                  <option value="Kepala Menunduk">Kepala Menunduk</option>
                </select>
              </td>

              <td>
                <?php
                if (isset($content[0]['alarm_report_intervention_category_cr'])) {
                  $type_intervention = explode("|", $content[0]['alarm_report_intervention_category_cr']);
                }else {
                  $type_intervention = "";
                }

                if ($type_intervention == "" || $type_intervention == Null) {
                  echo "Belum Diintervensi";
                }else {
                  echo $type_intervention[1];
                }
                 ?>
              </td>
            <?php }else {?>
                <input type="hidden" name="fatigue_category" id="fatigue_category" value="0">
              <td>
                <?php
                if (isset($content[0]['alarm_report_intervention_category_cr'])) {
                  $type_intervention = explode("|", $content[0]['alarm_report_intervention_category_cr']);
                }else {
                  $type_intervention = "";
                }

                if ($type_intervention == "" || $type_intervention == Null) {
                  echo "Belum Diintervensi";
                }else {
                  echo $type_intervention[1];
                }
                 ?>
              </td>
            <?php } ?>
        </tr>

        <tr>
          <td>
            Catatan
          </td>
          <td>
            <textarea name="intervention_note" id="intervention_note" rows="1" cols="20" class="form-control"></textarea>
          </td>
          <td>
            Catatan Control Room
          </td>
          <td>
            <textarea name="intervention_note_cr" id="intervention_note_cr" rows="1" cols="20" class="form-control" readonly><?php echo $content[0]['alarm_report_note_cr'] ?></textarea>
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
    // $("#resultreport").hide();
    // $("#loadernya").show();
    var alert_id          = $('#alert_id').val();
    var tablenya          = '<?php echo $tablenya ?>';
    var user_id           = '<?php echo $this->sess->user_id ?>';
    var user_name         = '<?php echo $this->sess->user_name ?>';
    var isfatiguetype     = '<?php echo $content[0]['alarm_report_type']; ?>';
    var alarmtype         = '<?php echo $alarmtype ?>';
    var fatigue_category  = $('#fatigue_category').val();
    var intervention_date = $('#intervention_date').val();
    var itervention_name  = $('#itervention_name').val();
    var itervention_sid   = $('#itervention_sid').val();
    var intervention_note = $('#intervention_note').val();
    var alarm_true_false  = $("input[type='radio'][name='alarm_true_false']:checked").val();
    var itervention_alarm = $("input[type='radio'][name='itervention_alarm']:checked").val();

    if (alarm_true_false == undefined || itervention_alarm == undefined) {
      console.log('masuk und');
      var alert = "<p style='color:red;'>Harap mengisi seluruh form dengan benar</p>";
      $("#notif").html(alert);
      $("#notif").fadeIn(1000);
      $("#notif").fadeOut(3000);
    }else {
      var data = {
        alarmtype:alarmtype,
        user_id:user_id,
        user_name:user_name,
        alert_id:alert_id,
        tablenya:tablenya,
        fatigue_category:fatigue_category,
        intervention_date:intervention_date,
        itervention_name:itervention_name,
        itervention_sid:itervention_sid,
        alarm_true_false:alarm_true_false,
        itervention_alarm:itervention_alarm,
        intervention_note:intervention_note,
      };

      console.log("data : ", data);
      $.post("<?php echo base_url() ?>dashboardberau/submit_intervention", data, function(response){
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
          const alarm_true                      = document.getElementById('alarm_true');
          const alarm_false                     = document.getElementById('alarm_false');
          const itervention_alarm_sesuai        = document.getElementById('itervention_alarm_sesuai');
          const itervention_alarm_tidaksesuai   = document.getElementById('itervention_alarm_tidaksesuai');
          const intervention_note               = document.getElementById('intervention_note');
          alarm_true.checked                    = false;
          alarm_false.checked                   = false;
          itervention_alarm_sesuai.checked      = false;
          itervention_alarm_tidaksesuai.checked = false;
          $("#itervention_sid").val("");
          $("#intervention_note").val("");
          frmsearch_onsubmit();
        }
        return false;
      }, "json");
    }
  }

  function btnReset(){
    const alarm_true                      = document.getElementById('alarm_true');
    const alarm_false                     = document.getElementById('alarm_false');
    const itervention_alarm_sesuai        = document.getElementById('itervention_alarm_sesuai');
    const itervention_alarm_tidaksesuai   = document.getElementById('itervention_alarm_tidaksesuai');
    const intervention_note               = document.getElementById('intervention_note');
    alarm_true.checked                    = false;
    alarm_false.checked                   = false;
    itervention_alarm_sesuai.checked      = false;
    itervention_alarm_tidaksesuai.checked = false;
    $("#itervention_sid").val("");
    $("#intervention_note").val("");
  }


</script>
