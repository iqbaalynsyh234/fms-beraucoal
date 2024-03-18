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

  // FOR CHANGE SERVICED BY
  jQuery("#servicedby").change(function(){
    var servicedby = jQuery("#servicedby").val();
    console.log("servicedby : ", servicedby);
      if (servicedby == "") {
        jQuery("#valueservicedby").hide();
        jQuery("#alertlimit").hide();
      }else {
        jQuery("#valueservicedby").show();
        jQuery("#alertlimit").show();
      }
  });



  jQuery(document).ready(function() {
    showclock();
    jQuery("#stnkexpdatenotempty").datepicker({
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


    jQuery("#kirexpdatenotempty").datepicker({
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

    jQuery("#stnkexpdateifempty").datepicker({
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

    jQuery("#kirexpdateifempty").datepicker({
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

  // FOR SAVE CONFIGURATION
  function saveconfiguration(){
    var vehicle_no          = jQuery("#vehicle_no").val();
    var vehicle_name        = jQuery("#vehicle_name").val();
    var vehicle_type        = jQuery("#vehicle_type").val();
    var vehicle_year        = jQuery("#vehicle_year").val();
    var no_rangka           = jQuery("#no_rangka").val();
    var no_mesin            = jQuery("#no_mesin").val();
    var stnk_no             = jQuery("#stnk_no").val();
    var stnkexpdatenotempty = jQuery("#stnkexpdatenotempty").val();
    var stnkexpdateifempty  = jQuery("#stnkexpdateifempty").val();
    var kir_no              = jQuery("#kir_no").val();
    var kirexpdatenotempty  = jQuery("#kirexpdatenotempty").val();
    var kirexpdateifempty   = jQuery("#kirexpdateifempty").val();
    var servicedby          = jQuery("#servicedby").val();
    var valueservicedby     = jQuery("#valueservicedby").val();
    var vehicle_device      = jQuery("#vehicle_device").val();
    var vehicle_type_gps    = jQuery("#vehicle_type_gps").val();
    var adaisinya           = jQuery("#adaisinya").val();
    var alertlimit           = jQuery("#alertlimit").val();
    var stnkexpdatefix;
    var kirexpdatefix;

    if (adaisinya == 1) {
      stnkexpdatefix = stnkexpdatenotempty;
      kirexpdatefix = kirexpdatenotempty;
    }else {
      stnkexpdatefix = stnkexpdateifempty;
      kirexpdatefix = kirexpdateifempty;
    }

    var data = {
      vehicle_no          : vehicle_no,
      vehicle_name        : vehicle_name,
      vehicle_type        : vehicle_type,
      vehicle_year        : vehicle_year,
      no_rangka           : no_rangka,
      no_mesin            : no_mesin,
      stnk_no             : stnk_no,
      stnkexpdatefix      : stnkexpdatefix,
      kir_no              : kir_no,
      kirexpdatefix       : kirexpdatefix,
      servicedby          : servicedby,
      valueservicedby     : valueservicedby,
      vehicle_device      : vehicle_device,
      vehicle_type_gps    : vehicle_type_gps,
      alertlimit          : alertlimit,
    };

    jQuery.post("<?php echo base_url()?>transporter/maintenancemanagement/savethisconfiguration", data,
    function(response)
      {
  				if (response.status == "success") {
            if (confirm(response.msg)) {
              window.location = '<?php echo base_url()?>transporter/maintenancemanagement';
            }
          }else {
            alert("Process Failed");
          }
			}
			, "json"
		);
  }
</script>

<style media="screen">
  input[type=number]::-webkit-inner-spin-button,
  input[type=number]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
  }
</style>

<?php if ($row > 0) {?>
  <!-- ADA ISINYA -->
  <div id="main">
    <div class="block-border">
      <table width="100%" cellpadding="8" class="table sortable no-margin">
        <input type="text" name="vehicle_device" id="vehicle_device" value="<?php echo $vehicle[0]['vehicle_device'] ?>" hidden>
        <input type="text" name="vehicle_type_gps" id="vehicle_type_gps" value="<?php echo $vehicle[0]['vehicle_type'] ?>" hidden>
        <input type="text" id="adaisinya" value="1" hidden>
        <tr>
          <td>
            <h3>Vehicle Detail Info</h3>
          </td>
          <td></td>
          <td></td>
          <td></td>
        </tr>

        <tr>
          <td>Vehicle No </td>
          <td>
            <input type="text" name="vehicle_no" id="vehicle_no" value="<?php echo $data[0]['maintenance_conf_vehicle_no']; ?>" readonly style="font-size : large;">
          </td>
          <td>Vehicle Name </td>
          <td>
            <input type="text" name="vehicle_name" id="vehicle_name" value="<?php echo $data[0]['maintenance_conf_vehicle_name']; ?>" readonly style="font-size : large;">
          </td>
        </tr>

        <tr>
          <td>Vehicle Type</td>
          <td>
            <input type="text" name="vehicle_type" id="vehicle_type" class="formdefault" value="<?php echo $data[0]['maintenance_conf_vehicle_type']; ?>">
          </td>
          <td>Year</td>
          <td>
            <input type="number" name="vehicle_year" id="vehicle_year" class="formdefault" size="4" value="<?php echo $data[0]['maintenance_conf_vehicle_year']; ?>">
          </td>
        </tr>

        <tr>
          <td>No. Rangka</td>
          <td>
            <input type="text" name="no_rangka" id="no_rangka" class="formdefault" value="<?php echo $data[0]['maintenance_conf_no_rangka']; ?>">
          </td>
          <td>No. Mesin</td>
          <td>
            <input type="text" name="no_mesin" id="no_mesin" class="formdefault" value="<?php echo $data[0]['maintenance_conf_no_mesin']; ?>">
          </td>
        </tr>

        <tr>
          <td>STNK No.</td>
          <td>
            <input type="text" name="stnk_no" id="stnk_no" class="formdefault" value="<?php echo $data[0]['maintenance_conf_stnk_no']; ?>">
          </td>
          <td>Exp. Date</td>
          <td>
            <input type="text" name="stnkexpdate" id="stnkexpdatenotempty" class="date-pick"  value="<?php echo $data[0]['maintenance_conf_stnkexpdate']; ?>"/>
          </td>
        </tr>

        <tr>
          <td>
            <h3>KIR Info</h3>
          </td>
          <td></td>
          <td></td>
          <td></td>
        </tr>

        <tr>
          <td>KIR No</td>
          <td>
            <input type="text" name="kir_no" id="kir_no" class="formdefault" value="<?php echo $data[0]['maintenance_conf_kir_no']; ?>">
          </td>
          <td>Exp. Date</td>
          <td>
            <input type="text" name="kirexpdate" id="kirexpdatenotempty" class="date-pick"  value="<?php echo $data[0]['maintenance_conf_kirexpdate']; ?>"/>
          </td>
        </tr>

        <tr>
          <td>
            <h3>Service Info</h3>
          </td>
          <td></td>
          <td></td>
          <td></td>
        </tr>

        <tr>
          <td>Serviced By </td>
          <td>
            <select name="servicedby" id="servicedby">
              <option value="<?php echo $data[0]['maintenance_conf_servicedby']; ?>"><?php echo $data[0]['maintenance_conf_servicedby']; ?></option>
              <option value="">--Choose Serviced By--</option>
              <option value="perkm">Per Km</option>
              <option value="permonth">Per Month</option>
            </select><br>
            <input type="number" name="valueservicedby" id="valueservicedby" class="formdefault" value="<?php echo $data[0]['maintenance_conf_valueservicedby']; ?>"><br>
            <input type="number" name="alertlimit" id="alertlimit" class="formdefault" value="<?php echo $data[0]['maintenance_conf_alertlimit']; ?>"><br>
            <small style="color: red;">
              <i>* Isi dengan periode bulan <br> atau dengan periode Kilometer.</i>
            </small>
          </td>
          <td></td>
          <td></td>
          <td></td>
        </tr>

        <tr>
          <td></td>
          <td></td>
          <td></td>
          <td>
            <div>
              <input type="submit" name="submit" value="Save Configuration" onclick="saveconfiguration()">
              <input type="button" value="Cancel" onclick="javascript:jQuery('#dialog').dialog('close');" />
            </div>
          </td>
        </tr>
      </table>
    </div>
  </div>
<?php }else { ?>
  <!-- GX ADA ISINYA -->
  <div id="main">
    <div class="block-border">
      <table width="100%" cellpadding="8" class="table sortable no-margin">
        <input type="text" name="vehicle_device" id="vehicle_device" value="<?php echo $vehicle[0]['vehicle_device'] ?>" hidden>
        <input type="text" name="vehicle_type_gps" id="vehicle_type_gps" value="<?php echo $vehicle[0]['vehicle_type'] ?>" hidden>
        <input type="text" id="adaisinya" value="0" hidden>
        <tr>
          <td>
            <h3>Vehicle Detail Info</h3>
          </td>
          <td></td>
          <td></td>
          <td></td>
        </tr>

        <tr>
          <td>Vehicle No </td>
          <td>
            <input type="text" name="vehicle_no" id="vehicle_no" value="<?php echo $vehicle[0]['vehicle_no']; ?>" readonly style="font-size : large;">
          </td>
          <td>Vehicle Name </td>
          <td>
            <input type="text" name="vehicle_name" id="vehicle_name" value="<?php echo $vehicle[0]['vehicle_name']; ?>" readonly style="font-size : large;">
          </td>
        </tr>

        <tr>
          <td>Vehicle Type</td>
          <td>
            <input type="text" name="vehicle_type" id="vehicle_type" class="formdefault">
          </td>
          <td>Year</td>
          <td>
            <input type="number" name="vehicle_year" id="vehicle_year" class="formdefault" size="4">
          </td>
        </tr>

        <tr>
          <td>No. Rangka</td>
          <td>
            <input type="text" name="no_rangka" id="no_rangka" class="formdefault">
          </td>
          <td>No. Mesin</td>
          <td>
            <input type="text" name="no_mesin" id="no_mesin" class="formdefault">
          </td>
        </tr>

        <tr>
          <td>STNK No.</td>
          <td>
            <input type="text" name="stnk_no" id="stnk_no" class="formdefault">
          </td>
          <td>Exp. Date</td>
          <td>
            <input type="text" name="stnkexpdate" id="stnkexpdateifempty" class="date-pick" />
          </td>
        </tr>

        <tr>
          <td>
            <h3>KIR Info</h3>
          </td>
          <td></td>
          <td></td>
          <td></td>
        </tr>

        <tr>
          <td>KIR No</td>
          <td>
            <input type="text" name="kir_no" id="kir_no" class="formdefault">
          </td>
          <td>Exp. Date</td>
          <td>
            <input type="text" name="kirexpdate" id="kirexpdateifempty" class="date-pick" />
          </td>
        </tr>

        <tr>
          <td>
            <h3>Service Info</h3>
          </td>
          <td></td>
          <td></td>
          <td></td>
        </tr>

        <tr>
          <td>Serviced By </td>
          <td>
            <select name="servicedby" id="servicedby">
              <option value="">--Choose Serviced By--</option>
              <option value="perkm">Per Km</option>
              <option value="permonth">Per Month</option>
            </select><br>
            <input type="number" name="valueservicedby" id="valueservicedby" class="formdefault" style="display: none;"><br>
            <input type="number" name="alertlimit" id="alertlimit" class="formdefault" style="display: none;" placeholder="Limit Notifikasi (KM/Bln)"><br>
            <small style="color: red;">
              <i>* Isi dengan periode bulan <br> atau dengan periode Kilometer.</i>
            </small>
          </td>
          <td></td>
          <td></td>
          <td></td>
        </tr>

        <tr>
          <td></td>
          <td></td>
          <td></td>
          <td>
            <div>
              <input type="submit" name="submit" value="Save Configuration" onclick="saveconfiguration()">
              <input type="button" value="Cancel" onclick="javascript:jQuery('#dialog').dialog('close');" />
            </div>
          </td>
        </tr>
      </table>
    </div>
  </div>
<?php } ?>
