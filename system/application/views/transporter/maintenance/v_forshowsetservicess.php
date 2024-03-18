<script type="text/javascript">
  jQuery.maxZIndex = jQuery.fn.maxZIndex = function(opt) {
    var def = {
      inc: 10,
      group: "*"
    };
    jQuery.extend(def, opt);
    var zmax = 0;
    jQuery(def.group).each(function() {
      var cur = parseInt(jQuery(this).css('z-index'));
      zmax = cur > zmax ? cur : zmax;
    });
    if (!this.jquery)
      return zmax;

    return this.each(function() {
      zmax += def.inc;
      jQuery(this).css("z-index", zmax);
    });
  }

  jQuery(document).ready(function() {
    showclock();
    jQuery("#v_kirdate_setservicess").datepicker({
      dateFormat: 'yy-mm-dd',
      startDate: '1900/01/01',
      showOn: 'button'
        //, 	changeYear: true
        //,	changeMonth: true
        ,
      buttonImage: '<?=base_url()?>assets/images/calendar.gif',
      buttonImageOnly: true,
      beforeShow: function() {
        jQuery('#ui-datepicker-div').maxZIndex();
      }
    });

    jQuery("#kirexpdate").datepicker({
      dateFormat: 'yy-mm-dd',
      startDate: '1900/01/01',
      showOn: 'button'
        //, 	changeYear: true
        //,	changeMonth: true
        ,
      buttonImage: '<?=base_url()?>assets/images/calendar.gif',
      buttonImageOnly: true,
      beforeShow: function() {
        jQuery('#ui-datepicker-div').maxZIndex();
      }
    });

    jQuery("#v_perpstnk_date_setservicess").datepicker({
      dateFormat: 'yy-mm-dd',
      startDate: '1900/01/01',
      showOn: 'button'
        //, 	changeYear: true
        //,	changeMonth: true
        ,
      buttonImage: '<?=base_url()?>assets/images/calendar.gif',
      buttonImageOnly: true,
      beforeShow: function() {
        jQuery('#ui-datepicker-div').maxZIndex();
      }
    });


    jQuery("#v_service_date_setservicess").datepicker({
      dateFormat: 'yy-mm-dd',
      startDate: '1900/01/01',
      showOn: 'button'
        //, 	changeYear: true
        //,	changeMonth: true
        ,
      buttonImage: '<?=base_url()?>assets/images/calendar.gif',
      buttonImageOnly: true,
      beforeShow: function() {
        jQuery('#ui-datepicker-div').maxZIndex();
      }
    });

    jQuery("#v_kir_exp_date_setservicess").datepicker({
      dateFormat: 'yy-mm-dd',
      startDate: '1900/01/01',
      showOn: 'button'
        //, 	changeYear: true
        //,	changeMonth: true
        ,
      buttonImage: '<?=base_url()?>assets/images/calendar.gif',
      buttonImageOnly: true,
      beforeShow: function() {
        jQuery('#ui-datepicker-div').maxZIndex();
      }
    });

    jQuery("#v_perpstnk_expdate_setservicess").datepicker({
      dateFormat: 'yy-mm-dd',
      startDate: '1900/01/01',
      showOn: 'button'
        //, 	changeYear: true
        //,	changeMonth: true
        ,
      buttonImage: '<?=base_url()?>assets/images/calendar.gif',
      buttonImageOnly: true,
      beforeShow: function() {
        jQuery('#ui-datepicker-div').maxZIndex();
      }
    });
  });

  // FOR SELECT SERVICESS ON CHANGE
  jQuery("#selectservicess").change(function() {
    var servicess = jQuery("#selectservicess").val();
    jQuery("#service_type").val(servicess);
    if (servicess == 2) {
      // KIR
      jQuery("#perpanjangstnkview").hide();
      jQuery("#serviceview").hide();
      jQuery("#kirview").show();
    } else if (servicess == 3) {
      // PERPNANG STNK
      jQuery("#kirview").hide();
      jQuery("#serviceview").hide();
      jQuery("#perpanjangstnkview").show();
    } else {
      // SERVICE
      jQuery("#kirview").hide();
      jQuery("#perpanjangstnkview").hide();
      jQuery("#serviceview").show();
    }
    console.log("servicess : ", servicess);
  });

  // SAVE SERVICE TO SERVICE HISTORY
  function saveservicess() {
    var tipeservice = jQuery("#service_type").val();
    var vehicle_device = jQuery("#vehicle_device").val();

    var data;
    var url;
    if (tipeservice == 2) {
      // KIR
      var v_kirvehicle_no             = jQuery("#v_kirvehicle_no").val();
      var v_kirvehicle_name           = jQuery("#v_kirvehicle_name").val();
      var work_agenc_kir_setservicess = jQuery("#work_agenc_kir_setservicess").val();
      var v_kirno_setservicess        = jQuery("#v_kirno_setservicess").val();
      var v_kirdate_setservicess      = jQuery("#v_kirdate_setservicess").val();
      var v_kir_exp_date_setservicess = jQuery("#v_kir_exp_date_setservicess").val();
      var v_kir_pelaksana             = jQuery("#v_kir_pelaksana").val();
      var v_kir_biaya                 = jQuery("#v_kir_biaya").val();
      var v_kirnote_setservicess      = jQuery("#v_kirnote_setservicess").val();

      url                             = "<?php echo base_url();?>transporter/maintenancemanagement/savetomaintenancehistory";
      data = {
        v_kirno_setservicess: v_kirno_setservicess,
        v_kirdate_setservicess: v_kirdate_setservicess,
        v_kir_exp_date_setservicess: v_kir_exp_date_setservicess,
        v_kirnote_setservicess: v_kirnote_setservicess,
        v_kirvehicle_no: v_kirvehicle_no,
        v_kirvehicle_name: v_kirvehicle_name,
        v_kir_biaya: v_kir_biaya,
        v_kir_pelaksana: v_kir_pelaksana,
        work_agenc_kir_setservicess: work_agenc_kir_setservicess,
        tipeservice: tipeservice,
        vehicle_device: vehicle_device
      };
    } else if (tipeservice == 3) {
      // PERPANJANG STNK
      var v_perpstnk_vehicle_no           = jQuery("#v_perpstnk_vehicle_no").val();
      var v_perpstnk_vehicle_name         = jQuery("#v_perpstnk_vehicle_name").val();
      var work_agenc_stnk_setservicess    = jQuery("#work_agenc_stnk_setservicess").val();
      var v_perpstnk_no_setservicess      = jQuery("#v_perpstnk_no_setservicess").val();
      var v_perpstnk_date_setservicess    = jQuery("#v_perpstnk_date_setservicess").val();
      var v_perpstnk_expdate_setservicess = jQuery("#v_perpstnk_expdate_setservicess").val();
      var v_perpstnk_pelaksana            = jQuery("#v_perpstnk_pelaksana").val();
      var v_perpstnk_biaya                = jQuery("#v_perpstnk_biaya").val();
      var v_perpstnk_note_setservicess    = jQuery("#v_perpstnk_note_setservicess").val();


      url = "<?php echo base_url();?>transporter/maintenancemanagement/savetomaintenancehistory";
      data = {
        v_perpstnk_vehicle_no: v_perpstnk_vehicle_no,
        v_perpstnk_vehicle_name: v_perpstnk_vehicle_name,
        work_agenc_stnk_setservicess: work_agenc_stnk_setservicess,
        v_perpstnk_no_setservicess: v_perpstnk_no_setservicess,
        v_perpstnk_date_setservicess: v_perpstnk_date_setservicess,
        v_perpstnk_expdate_setservicess: v_perpstnk_expdate_setservicess,
        v_perpstnk_pelaksana: v_perpstnk_pelaksana,
        v_perpstnk_biaya: v_perpstnk_biaya,
        v_perpstnk_note_setservicess: v_perpstnk_note_setservicess,
        tipeservice: tipeservice,
        vehicle_device: vehicle_device
      };
    } else {
      // SERVICE
      var v_service_vehicle_no        = jQuery("#v_service_vehicle_no").val();
      var v_service_vehicle_name      = jQuery("#v_service_vehicle_name").val();
      var work_agenc_setservicess     = jQuery("#work_agenc_setservicess").val();
      var v_service_date_setservicess = jQuery("#v_service_date_setservicess").val();
      var v_service_pelaksana         = jQuery("#v_service_pelaksana").val();
      var v_service_biaya             = jQuery("#v_service_biaya").val();
      var v_service_lastodometer      = jQuery("#v_service_lastodometer").val();
      var v_service_note_setservicess = jQuery("#v_service_note_setservicess").val();

      url = "<?php echo base_url();?>transporter/maintenancemanagement/savetomaintenancehistory";
      data = {
        v_service_vehicle_no: v_service_vehicle_no,
        v_service_vehicle_name: v_service_vehicle_name,
        work_agenc_setservicess: work_agenc_setservicess,
        v_service_date_setservicess: v_service_date_setservicess,
        v_service_pelaksana: v_service_pelaksana,
        v_service_biaya: v_service_biaya,
        v_service_lastodometer: v_service_lastodometer,
        v_service_note_setservicess: v_service_note_setservicess,
        tipeservice: tipeservice,
        vehicle_device: vehicle_device
      };
    }
    console.log("url : ", url);
    console.log("data : ", data);
    console.log("tipeservice : ", tipeservice);
    jQuery.post(url, data, function(response) {
      console.log("response", response);
      if (response.status == "success") {
      	if (confirm(response.msg)) {
      		window.location = '<?php echo base_url()?>transporter/maintenancemanagement';
      	}
      }else {
      	alert("Process Failed");
      }
    }, 'json');
  }
</script>
<style media="screen">
  input[type=number]::-webkit-inner-spin-button,
  input[type=number]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
  }
</style>
<div id="main">
  <div class="block-border">
    <?php if ($sizeconfig != 0) {?>
      <tr>
        <td>Servicess</td>
        <td>
          <select class="formdefault" name="selectservicess" id="selectservicess">
            <option value="">--Choose Servicess--</option>
            <?php for ($i=0; $i < sizeof($data); $i++) {?>
              <option value="<?php echo $data[$i]['service_type_id'];?>">
                <?php echo $data[$i]['service_type'];?>
              </option>
              <?php } ?>
          </select>
        </td>
      </tr>
      <br>
      <br>
      <br>
      <input type="text" name="vehicle_device" id="vehicle_device" value="<?php echo $vehicledata[0]['vehicle_device'] ?>" hidden>
      <table width="100%" cellpadding="8" class="table sortable no-margin" id="kirview" style="display: none;">
        <!-- FOR KIR START -->
        <input type="text" name="service_type" id="service_type" hidden>
        <tr>
          <td>Vehicle No</td>
          <td>
            <input type="text" name="v_kirvehicle_no" id="v_kirvehicle_no" value="<?php echo $dataconfigmaintenance[0]['maintenance_conf_vehicle_no']?>" class="formdefault" readonly>
          </td>
          <td>Vehicle Name</td>
          <td>
            <input type="text" name="v_kirvehicle_name" id="v_kirvehicle_name" value="<?php echo $dataconfigmaintenance[0]['maintenance_conf_vehicle_name']?>" class="formdefault" readonly>
          </td>
        </tr>
        <tr>
          <td>Workshop / Agencies</td>
          <td>
            <select class="formdefault" name="work_agenc_kir_setservicess" id="work_agenc_kir_setservicess">
              <?php foreach ($workshop as $work) {?>
                <option value="<?php echo $work['workshop_id'] ?>">
                  <?php echo $work['workshop_name'] ?>
                </option>
                <?php } ?>
            </select>
          </td>
          <td>KIR. No</td>
          <td>
            <input type="text" name="v_kirno_setservicess" id="v_kirno_setservicess" value="<?php echo $dataconfigmaintenance[0]['maintenance_conf_kir_no']?>" class="formdefault" readonly>
          </td>
        </tr>
        <tr>
          <td>KIR Date</td>
          <td>
            <input type="text" name="v_kirdate_setservicess" id="v_kirdate_setservicess" class="date-pick" />
          </td>
          <td>KIR Exp Date</td>
          <td>
            <input type="text" name="v_kir_exp_date_setservicess" id="v_kir_exp_date_setservicess" class="date-pick" />
          </td>
        </tr>
        <tr>
          <td>Pelaksana</td>
          <td>
            <input type="text" name="v_kir_pelaksana" id="v_kir_pelaksana" class="formdefault">
          </td>
          <td>Biaya</td>
          <td>
            <input type="number" name="v_kir_biaya" id="v_kir_biaya" class="formdefault rupiah">
          </td>
        </tr>

        <tr>
          <td>Note</td>
          <td>
            <textarea name="v_kirnote_setservicess" name="v_kirnote_setservicess" id="v_kirnote_setservicess" rows="5" cols="50"></textarea>
          </td>
          <td></td>
          <td></td>
        </tr>
        <tr>
          <td></td>
          <td></td>
          <td></td>
          <td>
            <div>
              <input type="submit" name="submit" value="Save" onclick="saveservicess()">
              <input type="button" value="Cancel" onclick="javascript:jQuery('#dialog').dialog('close');" />
            </div>
          </td>
        </tr>
      </table>
      <!-- FOR KIR END -->

      <!-- FOR PERPANJANG STNK START -->
      <table width="100%" cellpadding="8" class="table sortable no-margin" id="perpanjangstnkview" style="display: none;">
        <!-- FOR KIR START -->
        <input type="text" name="service_type_stnk" id="service_type_stnk" hidden>
        <tr>
          <td>Vehicle No</td>
          <td>
            <input type="text" name="v_perpstnk_vehicle_no" id="v_perpstnk_vehicle_no" value="<?php echo $dataconfigmaintenance[0]['maintenance_conf_vehicle_no']?>" class="formdefault" readonly>
          </td>
          <td>Vehicle Name</td>
          <td>
            <input type="text" name="v_perpstnk_vehicle_name" id="v_perpstnk_vehicle_name" value="<?php echo $dataconfigmaintenance[0]['maintenance_conf_vehicle_name']?>" class="formdefault" readonly>
          </td>
        </tr>
        <tr>
          <td>Workshop / Agencies</td>
          <td>
            <select class="formdefault" name="work_agenc_stnk_setservicess" id="work_agenc_stnk_setservicess">
              <?php foreach ($workshop as $work) {?>
                <option value="<?php echo $work['workshop_id'] ?>">
                  <?php echo $work['workshop_name'] ?>
                </option>
                <?php } ?>
            </select>
          </td>
          <td>STNK. No</td>
          <td>
            <input type="text" name="v_perpstnk_no_setservicess" id="v_perpstnk_no_setservicess" value="<?php echo $dataconfigmaintenance[0]['maintenance_conf_stnk_no']?>" class="formdefault" readonly>
          </td>
        </tr>
        <tr>
          <td>Extend Date</td>
          <td>
            <input type="text" name="v_perpstnk_date_setservicess" id="v_perpstnk_date_setservicess" class="date-pick" />
          </td>
          <td>Exp Date</td>
          <td>
            <input type="text" name="v_perpstnk_expdate_setservicess" id="v_perpstnk_expdate_setservicess" class="date-pick" />
          </td>
        </tr>
        <tr>
          <td>Pelaksana</td>
          <td>
            <input type="text" name="v_perpstnk_pelaksana" id="v_perpstnk_pelaksana" class="formdefault">
          </td>
          <td>Biaya</td>
          <td>
            <input type="number" name="v_perpstnk_biaya" id="v_perpstnk_biaya" class="formdefault rupiah">
          </td>
        </tr>

        <tr>
          <td>Note</td>
          <td>
            <textarea name="v_perpstnk_note_setservicess" id="v_perpstnk_note_setservicess" rows="5" cols="50"></textarea>
          </td>
          <td></td>
          <td></td>
        </tr>
        <tr>
          <td></td>
          <td></td>
          <td></td>
          <td>
            <div>
              <input type="submit" name="submit" value="Save" onclick="saveservicess()">
              <input type="button" value="Cancel" onclick="javascript:jQuery('#dialog').dialog('close');" />
            </div>
          </td>
        </tr>
      </table>
      <!-- FOR PERPANJANG STNK END -->

      <!-- FOR SERVICE START -->
      <div id="serviceview" style="display: none;">
      <td>
        <h3>
          Serviced By :
          <?php echo strtoupper($dataconfigmaintenance[0]['maintenance_conf_servicedby']); ?>
        </h3>
      </td>
      <table width="100%" cellpadding="8" class="table sortable no-margin"  >
        <input type="text" name="service_type_stnk" id="service_type_stnk" hidden>
        <tr>
          <td>Vehicle No</td>
          <td>
            <input type="text" name="v_service_vehicle_no" id="v_service_vehicle_no" value="<?php echo $dataconfigmaintenance[0]['maintenance_conf_vehicle_no']?>" class="formdefault" readonly>
          </td>
          <td>Vehicle Name</td>
          <td>
            <input type="text" name="v_service_vehicle_name" id="v_service_vehicle_name" value="<?php echo $dataconfigmaintenance[0]['maintenance_conf_vehicle_name']?>" class="formdefault" readonly>
          </td>
        </tr>
        <tr>
          <td>Workshop / Agencies</td>
          <td>
            <select class="formdefault" name="work_agenc_setservicess" id="work_agenc_setservicess">
              <?php foreach ($workshop as $work) {?>
                <option value="<?php echo $work['workshop_id'] ?>">
                  <?php echo $work['workshop_name'] ?>
                </option>
                <?php } ?>
            </select>
          </td>
          <td>Service Date</td>
          <td>
            <input type="text" name="v_service_date_setservicess" id="v_service_date_setservicess" class="date-pick" />
          </td>
        </tr>
        <tr>
          <td></td>
          <td></td>
          <td>Last Odometer</td>
          <td>
            <input type="number" name="v_service_lastodometer" id="v_service_lastodometer" class="formdefault">
          </td>
        </tr>

        <tr>
          <td>Pelaksana</td>
          <td>
            <input type="text" name="v_service_pelaksana" id="v_service_pelaksana" class="formdefault">
          </td>
          <td>Biaya</td>
          <td>
            <input type="number" name="v_service_biaya" id="v_service_biaya" class="formdefault rupiah">
          </td>
        </tr>


        <tr>
          <td>Note</td>
          <td>
            <textarea name="v_service_note_setservicess" id="v_service_note_setservicess" rows="5" cols="50"></textarea>
          </td>
          <td></td>
          <td></td>
        </tr>
        <tr>
          <td></td>
          <td></td>
          <td></td>
          <td>
            <div>
              <input type="submit" name="submit" value="Save" onclick="saveservicess()">
              <input type="button" value="Cancel" onclick="javascript:jQuery('#dialog').dialog('close');" />
            </div>
          </td>
        </tr>
        <?php }else {?>
          <div style="color : red; font-size: 16px;">
            <?php echo "You dont have any configuration for this vehicle. Please set configuration first to complete this task." ?>
          </div>
          <?php } ?>
      </table>
    </div>

      <!-- FOR SERVICE END -->
  </div>
</div>
</div>
