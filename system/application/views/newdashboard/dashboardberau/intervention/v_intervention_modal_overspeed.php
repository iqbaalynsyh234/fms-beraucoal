<link href="<?php echo base_url();?>assets/dashboard/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url();?>assets/dashboard/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />

<div class="row" style="overflow-x:auto; height:516px;">

  <div class="col-md-12">
     <!-- style="height:410px" -->
    <p class="text-center">
      <b>Pelaksanaan Intervensi DMS OVERSPEED -  DEVELOPMENT</b>
    </p>
    <div class="text-center" id="notif" style="display:none;"></div>
    <div class="form">
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
            <input type="text" name="intervention_date" id="intervention_date" class="form-control" value="<?php echo date("Y-m-d H:i:s", strtotime("+1 Hour")) ?>" readonly >
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
          <!-- <td>Name</td>
          <td>
            <?php
            if (isset($data_karyawan_bc[0]['karyawan_bc_name'])) {
              $intervention_name = $data_karyawan_bc[0]['karyawan_bc_name'];
            }else {
              $intervention_name = $this->sess->user_name;
            }
             ?> -->
            <input type="text" name="intervention_name" id="intervention_name" class="form-control" value="<?php echo $intervention_name ?>" hidden>
          <!-- </td> -->

          <td>SID</td>
          <td>
            <select class="form-control select2" name="intervention_sid" id="intervention_sid" style="width:180px;">
              <?php for ($i=0; $i < sizeof($data_karyawan_bc); $i++) {?>
                <option value="<?php echo $data_karyawan_bc[$i]['karyawan_bc_sid'].'|'.$data_karyawan_bc[$i]['karyawan_bc_name']; ?>"><?php echo $data_karyawan_bc[$i]['karyawan_bc_sid'].' / '.$data_karyawan_bc[$i]['karyawan_bc_name']; ?></option>
              <?php } ?>
            </select>
          </td>
          <td>Supervisor</td>
          <td>
            <select class="form-control select2" name="intervention_supervisor" id="intervention_supervisor" style="width:180px;">
              <?php for ($i=0; $i < sizeof($data_karyawan_bc); $i++) {?>
                <option value="<?php echo $data_karyawan_bc[$i]['karyawan_bc_sid'].'|'.$data_karyawan_bc[$i]['karyawan_bc_name']; ?>"><?php echo $data_karyawan_bc[$i]['karyawan_bc_sid'].' / '.$data_karyawan_bc[$i]['karyawan_bc_name']; ?></option>
              <?php } ?>
            </select>
          </td>
          <td></td>
          <td></td>
        </tr>

        <tr>
            <input type="hidden" name="fatigue_category" id="fatigue_category" value="0">
            <td>
              Intervention Type
            </td>
              <td>
                <select class="form-control select2" name="intervention_category" id="intervention_category" onchange="change_type_intervention();">
                  <?php for ($i=0; $i < sizeof($type_intervention); $i++) {?>
                    <option value="<?php echo $type_intervention[$i]['intervention_type_id'].'|'.$type_intervention[$i]['intervention_type_name']; ?>"><?php echo $type_intervention[$i]['intervention_type_name']; ?></option>
                  <?php } ?>
                </select>
              </td>
              <td>
                Catatan
              </td>
              <td>
                <!-- <textarea name="intervention_note" id="intervention_note" rows="1" cols="20" class="form-control"></textarea> -->
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
    var alert_id                = $('#alert_id').val();
    var vehicle_no              = '<?php $content[0]['alarm_report_vehicle_no'] ?>';
    var vehicle_id              = '<?php $content[0]['alarm_report_vehicle_id'] ?>';
    // var alert_date              = '<?php $content[0]['alarm_report_start_time'] ?>';
    var tablenya                = '<?php echo $tablenya ?>';
    var user_id                 = '<?php echo $this->sess->user_id ?>';
    var user_name               = $('#intervention_name').val();
    var intervention_date       = $('#intervention_date').val();
    var intervention_sid        = $('#intervention_sid').val();
    var alarmtype               = '<?php echo $alarmtype ?>';
    var fatigue_category        = $('#fatigue_category').val();
    var intervention_date       = $('#intervention_date').val();
    var intervention_name       = $('#intervention_name').val();
    var intervention_sid        = $('#intervention_sid').val();
    var intervention_supervisor = $('#intervention_supervisor').val();
    var intervention_category   = $('#intervention_category').val();
    var intervention_note       = $('#intervention_note').val();

      var data = {
        vehicle_no:vehicle_no,
        vehicle_id:vehicle_id,
        // alert_date:alert_date,
        alarmtype:alarmtype,
        user_id:user_id,
        user_name:user_name,
        alert_id:alert_id,
        tablenya:tablenya,
        fatigue_category:fatigue_category,
        intervention_date:intervention_date,
        intervention_name:intervention_name,
        intervention_sid:intervention_sid,
        intervention_supervisor:intervention_supervisor,
        intervention_category:intervention_category,
        intervention_note:intervention_note,
        intervention_date:intervention_date,
      };

      console.log("data : ", data);
      $.post("<?php echo base_url() ?>dashboardberau/submit_intervention_controlroom_overspeed", data, function(response){
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
          frmsearch_onsubmit();
        }
        return false;
      }, "json");
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
