<link href="<?php echo base_url();?>assets/dashboard/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url();?>assets/dashboard/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />

<div class="row" style="overflow-x:auto; height:516px;">

  <div class="col-md-12">
     <!-- style="height:410px" -->
    <p class="text-center">
      <b>Pelaksanaan Post Event DMS OVERSPEED -  DEVELOPMENT</b>
    </p>
    <div class="text-center" id="notif" style="display:none;"></div>
    <div class="form">
      <table class="table table-striped" style="font-size:12px;">
        <table class="table table-striped" style="font-size:12px;">
          <tr>
            <td>Vehicle</td>
            <td>
              <?php
                echo $content[0]['alarm_report_vehicle_no'] .' '. $content[0]['alarm_report_vehicle_name'];
               ?>
            </td>
            <td>Alert</td>
            <td>
              <?php
                echo "Overspeed - ". $content[0]['overspeed_report_level_alias'];
               ?>
            </td>
          </tr>

          <tr>
            <td>Speed Limit</td>
            <td>
              <?php
                echo $content[0]['alarm_report_speed_limit'].' Kph';
               ?>
            </td>
            <td>Speed</td>
            <td>
              <?php
                echo $content[0]['alarm_report_speed'].' Kph';
               ?>
            </td>
          </tr>

        <tr>
          <td>Tanggal</td>
          <td>
            <input type="text" name="postevent_date" id="postevent_date" class="form-control" value="<?php echo date("Y-m-d H:i:s", strtotime("+1 Hour")) ?>" readonly >
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
          <td>Name</td>
          <td>
            <?php
            if (isset($data_karyawan_bc[0]['karyawan_bc_name'])) {
              $postevent_name = $data_karyawan_bc[0]['karyawan_bc_name'];
            }else {
              $postevent_name = $this->sess->user_name;
            }
             ?>
            <input type="text" name="postevent_name" id="postevent_name" class="form-control" value="<?php echo $postevent_name ?>" readonly>
          </td>

          <td>SID</td>
          <td>
            <?php
              if ($content[0]['alarm_report_name_cr'] == "") {?>
                <select class="form-control select2" name="postevent_sid" id="postevent_sid" style="width:180px;">
                  <?php for ($i=0; $i < sizeof($data_karyawan_bc); $i++) {?>
                    <option value="<?php echo $data_karyawan_bc[$i]['karyawan_bc_sid'].'|'.$data_karyawan_bc[$i]['karyawan_bc_name']; ?>"><?php echo $data_karyawan_bc[$i]['karyawan_bc_sid'].' / '.$data_karyawan_bc[$i]['karyawan_bc_name']; ?></option>
                  <?php } ?>
                </select>
              <?php }else {?>
                <input type="text" name="postevent_sid" id="postevent_sid" class="form-control" value="<?php echo $content[0]['alarm_report_sid_cr'].'|'.$content[0]['alarm_report_name_cr'] ?>" hidden>
                <!-- <input type="text" name="itervention_sid_justshow" id="itervention_sid_justshow" class="form-control" value="" readonly> -->
                <p style="font-size:12px;">
                  <?php echo $content[0]['alarm_report_sid_cr'].' - '.$content[0]['alarm_report_name_cr'] ?>
                </p>
              <?php } ?>
          </td>
        </tr>

        <tr>
          <td>Status By CR</td>
          <td>
            <?php
              $intervention_status_by_cr = $content[0]['alarm_report_statusintervention_cr'];
                if ($intervention_status_by_cr == 1) {
                  echo "True";
                }elseif ($intervention_status_by_cr == 2) {
                  echo "False";
                }else {
                  echo "Belum Diintervensi";
                }
             ?>
          </td>
          <td></td>
          <td></td>
        </tr>

        <tr>
          <?php
            if ($content[0]['alarm_report_type'] == 618 || $content[0]['alarm_report_type'] == 619) {?>

            <?php }else {?>
              <td></td>
            <?php }?>
          <td>True / False Alarm</td>
          <td>Intervensi *Wajib Dipilih</td>
          <?php
            if ($content[0]['alarm_report_type'] == 618 || $content[0]['alarm_report_type'] == 619) {?>
              <td>
                Fatigue Category
              </td>

              <td>
                <?php
                if (isset($content[0]['alarm_report_intervention_category_cr'])) {
                  $type_intervention_fix = $content[0]['alarm_report_intervention_category_cr'];
                }else {
                  $type_intervention_fix = "";
                }

                if ($type_intervention_fix == "" || $type_intervention_fix == Null) {
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
                  $type_intervention_fix = $content[0]['alarm_report_intervention_category_cr'];
                }else {
                  $type_intervention_fix = "";
                }

                if ($type_intervention_fix == "" || $type_intervention_fix == Null) {
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
            <input type="radio" name="postevent_alarm" id="postevent_alarm_sesuai" value="1"> Sesuai
            <input type="radio" name="postevent_alarm" id="postevent_alarm_tidaksesuai" value="0"> Tidak Sesuai
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
                  $type_intervention_fix = explode("|", $content[0]['alarm_report_intervention_category_cr']);
                }else {
                  $type_intervention_fix = "";
                }

                if ($type_intervention_fix == "" || $type_intervention_fix == Null) {
                  echo "Belum Diintervensi";
                }else {?>

                <?php }
                 ?>
              </td>
            <?php }else {?>
                <input type="hidden" name="fatigue_category" id="fatigue_category" value="0">
              <td>
                <?php
                if (isset($content[0]['alarm_report_intervention_category_cr'])) {
                  $type_intervention_fix = explode("|", $content[0]['alarm_report_intervention_category_cr']);
                }else {
                  $type_intervention_fix = "";
                }

                if ($type_intervention_fix == "" || $type_intervention_fix == Null) {
                  echo "Belum Diintervensi";
                }else {?>
                  <select class="form-control select2" name="intervention_category" id="intervention_category" onchange="change_type_intervention();">
                    <?php for ($i=0; $i < sizeof($type_intervention); $i++) {?>
                      <option value="<?php echo $type_intervention[$i]['intervention_type_id'].'|'.$type_intervention[$i]['intervention_type_name']; ?>"><?php echo $type_intervention[$i]['intervention_type_name']; ?></option>
                    <?php } ?>
                  </select>
                <?php }
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
    var user_name         = $('#postevent_name').val();
    var postevent_date    = $('#postevent_date').val();
    var postevent_sid     = $('#postevent_sid').val();
    var alarmtype         = '<?php echo $alarmtype ?>';
    var fatigue_category  = $('#fatigue_category').val();
    var postevent_date = $('#postevent_date').val();
    var postevent_name  = $('#postevent_name').val();
    var postevent_sid   = $('#postevent_sid').val();
    var intervention_note = $('#intervention_note').val();
    var alarm_true_false  = $("input[type='radio'][name='alarm_true_false']:checked").val();
    var postevent_alarm = $("input[type='radio'][name='postevent_alarm']:checked").val();

    if (alarm_true_false == undefined || postevent_alarm == undefined) {
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
        postevent_date:postevent_date,
        postevent_name:postevent_name,
        postevent_sid:postevent_sid,
        alarm_true_false:alarm_true_false,
        postevent_alarm:postevent_alarm,
        intervention_note:intervention_note,
        postevent_date:postevent_date,
      };

      console.log("data : ", data);
      $.post("<?php echo base_url() ?>development/submit_postevent_overspeed", data, function(response){
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
          const postevent_alarm_sesuai        = document.getElementById('postevent_alarm_sesuai');
          const postevent_alarm_tidaksesuai   = document.getElementById('postevent_alarm_tidaksesuai');
          const intervention_note               = document.getElementById('intervention_note');
          alarm_true.checked                    = false;
          alarm_false.checked                   = false;
          postevent_alarm_sesuai.checked      = false;
          postevent_alarm_tidaksesuai.checked = false;
          $("#postevent_sid").val("");
          $("#intervention_note").val("");
          frmsearch_onsubmit();
        }
        return false;
      }, "json");
    }
  }

  function btnReset(){
    $("#postevent_sid").val("");
    $("#intervention_note").val("");
  }

  function change_type_intervention(){
    var intervention_category = $("#intervention_category").val();
    var interv_cat            = intervention_category.split("|");
    var data = {
      interv_type_id : interv_cat[0]
    };
    // console.log("interv_cat : ", interv_cat);
    $.post("<?php echo base_url() ?>development/data_intervention_note", data, function(response){
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
