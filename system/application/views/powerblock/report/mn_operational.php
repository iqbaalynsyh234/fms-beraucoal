<script src="<?php echo base_url()?>assets/js/assetsbaru/jquery.min.js"></script>
<!-- <link rel="stylesheet" href="<?php echo base_url()?>assets/js/assetsbaru/jquery-ui.css"> -->
<script src="<?php echo base_url()?>assets/js/assetsbaru/jquery-ui.min.js"></script>
<script src="<?php echo base_url()?>assets/js/assetsbaru/chosen.jquery.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url()?>assets/js/assetsbaru/chosen.min.css">

<script>
  var geodesicvalue;
  var repeated;
	var isgeofenceclicked = 0;
  var isgeofenceinpbi = 0;

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
  jQuery(document).ready(
    function() {
      jQuery(".thisgeofence").hide();
      showclock();
      jQuery("#date").datepicker({
        dateFormat: 'yy/mm/dd',
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
      jQuery("#startdate").datepicker({
        dateFormat: 'yy/mm/dd',
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

      jQuery("#enddate").datepicker({
        dateFormat: 'yy/mm/dd',
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
      jQuery("#histstartdate").datepicker({
        dateFormat: 'yy/mm/dd',
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

      jQuery("#histenddate").datepicker({
        dateFormat: 'yy/mm/dd',
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
    }
  );



  function page(p) {
    if (p == undefined) {
      p = 0;
    }
    jQuery("#offset").val(p);
    jQuery("#result").html('<img src="<?php echo base_url();?>assets/transporter/images/loader2.gif">');
    jQuery("#loader").show();
			if (isgeofenceclicked == 1) {
				jQuery.post("<?=base_url();?>pbi_report/getdataoperationalbygeofence/", jQuery("#frmsearch").serialize(),
					function(r) {
						// jQuery("#btngotohistorymaps").show();
						jQuery("#loader").hide();
						jQuery("#result").html(r.html);
					}, "json"
				);
			}else if (isgeofenceclicked == 2) {
				jQuery.post("<?=base_url();?>pbi_report/getdataoperationalbynotgeofence/", jQuery("#frmsearch").serialize(),
					function(r) {
						// jQuery("#btngotohistorymaps").show();
						jQuery("#loader").hide();
						jQuery("#result").html(r.html);
					}, "json"
				);
			}else if (isgeofenceclicked == 3) {
        jQuery.post("<?=base_url();?>pbi_report/getdatabygeofencinpbi/", jQuery("#frmsearch").serialize(),
					function(r) {
            console.log("responya : ", r);
						// jQuery("#btngotohistorymaps").show();
						jQuery("#loader").hide();
						jQuery("#result").html(r.html);
					}, "json"
				);
      }else if (isgeofenceclicked == 4) {
        alert(isgeofenceclicked +"notinpbi");
      }else {
				jQuery.post("<?=base_url();?>pbi_report/dataoperational/", jQuery("#frmsearch").serialize(),
					function(r) {
						jQuery("#btngotohistorymaps").show();
						jQuery("#loader").hide();
						jQuery("#result").html(r.html);
					}, "json"
				);
			}

  }



  function frmsearch_onsubmit() {
    jQuery("#loader").show();
    page(0);
    return false;
  }


  function excel_onsubmit() {
    jQuery("#loader2").show();

    jQuery.post("<?=base_url();?>pbi_report/dataoperational_excel/", jQuery("#frmsearch").serialize(),
      function(r) {
        jQuery("#loader2").hide();
        if (r.success == true) {
          jQuery("#frmreq").attr("src", r.filename);
        } else {
          alert(r.errMsg);
        }
      }, "json"
    );

    return false;
  }


  function order(by) {
    if (by == jQuery("#sortby").val()) {
      if (jQuery("#orderby").val() == "asc") {
        jQuery("#orderby").val("desc");
      } else {
        jQuery("#orderby").val("asc");
      }
    } else {
      jQuery("#orderby").val('asc')
    }

    jQuery("#sortby").val(by);
    page(0);
  }

  function option_type_location(v) {
    switch (v) {
      case "location_no":
        jQuery('#location').val("");
        jQuery("#location_view").hide();
        break;
      case "location_yes":
        jQuery("#location_view").show();
        break;
    }
  }

  function option_type_duration(v) {
    switch (v) {
      case "duration_no":
        jQuery('#s_minute').val("");
        jQuery('#e_minute').val("");
        jQuery("#duration_view").hide();
        break;
      case "duration_yes":
        jQuery("#duration_view").show();
        break;
    }
  }

  function option_type_km(v) {
    switch (v) {
      case "km_no":
        jQuery('#km_start').val("");
        jQuery('#km_end').val("");
        jQuery("#km_view").hide();
        break;
      case "km_yes":
        jQuery("#km_view").show();
        break;
    }
  }

  function option_type_geodesic(v) {
    switch (v) {
      case "geodesic_no":
        geodesicvalue = "false";
        repeated = '0px';
        break;
      case "geodesic_yes":
        geodesicvalue = "true";
        repeated = '250px';
        break;
    }
  }

  function option_type_geofence(v) {
    switch (v) {
      case "geofence_no":
			isgeofenceclicked = 0;
			jQuery('#thisgeofence').prop('disabled', true).trigger("chosen:updated");
			jQuery('#vehicle').prop('disabled', false).trigger("chosen:updated");
			jQuery("#btngotohistorymaps").show();
        break;
      case "geofence_yes":
			isgeofenceclicked = 1;
			jQuery('#thisgeofence').prop('disabled', false).trigger("chosen:updated");
			jQuery('#vehicle').prop('disabled', true).trigger("chosen:updated");
			jQuery('#notgeofenceno').prop("checked", true);
			jQuery("#btngotohistorymaps").hide();
        break;
      case "inpbi":
			isgeofenceclicked = 3;
        break;
      case "notinpbi":
			isgeofenceclicked = 4;
        break;
    }
  }

	function option_type_not_geofence(v) {
    switch (v) {
      case "not_geofence_no":
			isgeofenceclicked = 0;
			jQuery('#thisgeofence').prop('disabled', true).trigger("chosen:updated");
			jQuery('#vehicle').prop('disabled', false).trigger("chosen:updated");
			jQuery("#btngotohistorymaps").show();
        break;
      case "not_geofence_yes":
			isgeofenceclicked = 2;
			jQuery('#thisgeofence').prop('disabled', true).trigger("chosen:updated");
			jQuery('#vehicle').prop('disabled', true).trigger("chosen:updated");
			jQuery('#filterbygeofenceno').prop("checked", true);
			jQuery("#btngotohistorymaps").hide();
        break;
    }
  }





  // FUNGSI GOTO HISTORY MAP
  function gotohistorymaps() {
    jQuery("#loader2").show();
    jQuery.post("<?=base_url();?>pbi_report/newhistorymaps", jQuery("#frmsearch").serialize(),
      function(r) {
        jQuery("#loader2").hide();
        jQuery("#result").html(r.html);
      }, "json"
    );
  }
</script>

<style media="screen">
  .ui-datepicker {
    z-index: 9999 !important;
  }
</style>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;">
  <?=$navigation;?>
    <div id="main" style="margin: 20px;">
      <div class="block-border">
        <form class="block-content form" name="frmsearch" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
          <h1>Operational Report</h1>
          <input type="hidden" name="offset" id="offset" value="" />
          <input type="hidden" id="sortby" name="sortby" value="" />
          <input type="hidden" id="orderby" name="orderby" value="" />
					<fieldset>
          <table width="100%" cellpadding="3" class="tablelist" style="font-size: 12px;">
            <tr>
							<td>
								<b>Filter By Geofence </b>
							<input name="filterbygeofence" id="filterbygeofenceno" type="radio" value="" onClick="option_type_geofence('geofence_no')" checked>No</input>
							<input name="filterbygeofence" id="filterbygeofence" type="radio" value="1" onClick="option_type_geofence('geofence_yes')">Yes</input>
									<select name="thisgeofence" id="thisgeofence" style="width:30%;" disabled>
										<?php
									if (isset($geofence_name) && count($geofence_name)>0)
									{
										for ($i=0;$i<count($geofence_name);$i++)
										{
								?>
											<option value="<?php echo $geofence_name[$i]->geofence_name;?>">
												<?php echo $geofence_name[$i]->geofence_name;?>
											</option>
											<?php
										}
									}
								?>
									</select>

							<b>Vehicle</b>
                <select id="vehicle" name="vehicle" class="chosen" style="width: 30%;">
                  <?php for($i=0; $i < count($vehicles); $i++) { ?>
                    <option value="<?php echo $vehicles[$i]->vehicle_device; ?>">
                      <?php if ($this->sess->user_type != 2) { echo $vehicles[$i]->user_name." - "; } ?>
                        <?php echo $vehicles[$i]->vehicle_no; ?> -
                          <?php echo $vehicles[$i]->vehicle_name; ?>
                            <?php } ?>
                </select>
									<b>Not Geofence </b>
									<input name="notgeofence" id="notgeofenceno" type="radio" value="" onClick="option_type_not_geofence('not_geofence_no')" checked>No</input>
	                <input name="notgeofence" id="notgeofenceyes" type="radio" value="1" onClick="option_type_not_geofence('not_geofence_yes')">Yes</input>
								</td>
						</tr>
					</table><br>
          <tr>
            <td>
              <b>Filter By Geofence In PBI</b>
            </td>
            <td>
              <input name="geofenceinpbi" id="geofenceinpbi" type="radio" value="" onClick="option_type_geofence('notinpbi')" checked>No</input>
              <input name="geofenceinpbi" id="geofenceinpbi" type="radio" value="1" onClick="option_type_geofence('inpbi')">Yes</input>
            </td>
          </tr>
					</fieldset>
					<fieldset>
						<table>
	            <tr id="filterdatestartend">
	              <td width="10%">Date</td>
	              <td>
	                <input type='text' readonly name="startdate" id="startdate" class="date-pick" value="<?=date('Y/m/d')?>" maxlength='10'> ~
                  <select class="textgray" style="font-size: 11px; width: 65px;" id="shour" name="shour">
      						                    <option value="00:00">00:00</option>
      						                    <option value="01:00">01:00</option>
      						                    <option value="02:00">02:00</option>
      						                    <option value="03:00">03:00</option>
      						                    <option value="04:00">04:00</option>
      						                    <option value="05:00">05:00</option>
      						                    <option value="06:00">06:00</option>
      						                    <option value="07:00">07:00</option>
      						                    <option value="08:00">08:00</option>
      						                    <option value="09:00">09:00</option>
      						                    <option value="10:00">10:00</option>
      						                    <option value="11:00">11:00</option>
      						                    <option value="12:00">12:00</option>
      						                    <option value="13:00">13:00</option>
      						                    <option value="14:00">14:00</option>
      						                    <option value="15:00">15:00</option>
      						                    <option value="16:00">16:00</option>
      						                    <option value="17:00">17:00</option>
      						                    <option value="18:00">18:00</option>
      						                    <option value="19:00">19:00</option>
      						                    <option value="20:00">20:00</option>
      						                    <option value="21:00">21:00</option>
      						                    <option value="22:00">22:00</option>
      						                    <option value="23:00">23:00</option>
      						             </select>
	                <input type='text' readonly name="enddate" id="enddate" class="date-pick" value="<?=date('Y/m/d')?>" maxlength='10'>
                  <select class="textgray" style="font-size: 11px; width: 65px;" id="ehour" name="ehour">

      						                    <option value="00:59">00:59</option>
      						                    <option value="01:59">01:59</option>
      						                    <option value="02:59">02:59</option>
      						                    <option value="03:59">03:59</option>
      						                    <option value="04:59">04:59</option>
      						                    <option value="05:59">05:59</option>
      						                    <option value="06:59">06:59</option>
      						                    <option value="07:59">07:59</option>
      						                    <option value="08:59">08:59</option>
      						                    <option value="09:59">09:59</option>
      						                    <option value="10:59">10:59</option>
      						                    <option value="11:59">11:59</option>
      						                    <option value="12:59">12:59</option>
      						                    <option value="13:59">13:59</option>
      						                    <option value="14:59">14:59</option>
      						                    <option value="15:59">15:59</option>
      						                    <option value="16:59">16:59</option>
      						                    <option value="17:59">17:59</option>
      						                    <option value="18:59">18:59</option>
      						                    <option value="19:59">19:59</option>
      						                    <option value="20:59">20:59</option>
      						                    <option value="21:59">21:59</option>
      						                    <option value="22:59">22:59</option>
      						                    <option selected="" value="23:59">23:59</option>
      						                </select>
	              </td>
	            </tr>
	            <tr>
	              <td>Engine</td>
	              <td>
	                <select id="engine" name="engine">
	                  <option value="">All</option>
	                  <option value="1">ON</option>
	                  <option value="0">OFF</option>
	                </select>
	              </td>
	            </tr>

	            <tr>
	              <td>
	                <br />Location </td>
	              <td>
	                <br />
	                <input name="type_location" type="radio" value="" onClick="option_type_location('location_no')" checked>No</input>
	                <input name="type_location" type="radio" value="1" onClick="option_type_location('location_yes')">Yes</input>
	                <div id="location_view" style="display:none"> Location Name:
	                  <input type="text" name="location" id="location" value="" size="50" />
	                </div>
	              </td>
	            </tr>

	            <tr>
	              <td>
	                <br />Duration(minute)</td>
	              <td>
	                <br />
	                <input name="type_duration" type="radio" value="" onClick="option_type_duration('duration_no')" checked>No</input>
	                <input name="type_duration" type="radio" value="1" onClick="option_type_duration('duration_yes')">Yes</input>
	                <div id="duration_view" style="display:none"> From:
	                  <input type="text" name="s_minute" id="s_minute" value="" size="3" /> To:
	                  <input type="text" name="e_minute" id="e_minute" value="" size="3" />
	                </div>
	              </td>
	            </tr>
	            <tr>
	              <td>
	                <br />Filter KM</td>
	              <td>
	                <br />
	                <input name="type_km" type="radio" value="" onClick="option_type_km('km_no')" checked>No</input>
	                <input name="type_km" type="radio" value="1" onClick="option_type_km('km_yes')">Yes</input>
	                <div id="km_view" style="display:none">
	                  From:
	                  <input type="text" name="km_start" id="km_start" value="" size="3" placeholder="0~9" maxlength="3" checked/> KM To:
	                  <input type="text" name="km_end" id="km_end" value="" size="3" placeholder="0~9" maxlength="3" /> KM
	                </div>
	              </td>
	            </tr>
	            <tr>
	              <td>
	                <br />Arah Perjalanan
	              </td>
	              <td>
	                <br />
	                <input name="type_geodesic" type="radio" value="" onClick="option_type_geodesic('geodesic_no')" checked>No</input>
	                <input name="type_geodesic" type="radio" value="1" onClick="option_type_geodesic('geodesic_yes')">Yes</input>
	                <div id="km_view" style="display:none">
	                  From:
	                  <input type="text" name="km_start" id="km_start" value="" size="3" placeholder="0~9" maxlength="3" /> KM To:
	                  <input type="text" name="km_end" id="km_end" value="" size="3" placeholder="0~9" maxlength="3" /> KM
	                </div>
	              </td>
	            </tr>
	            <tr>
	              <td style="border: 0px;">&nbsp;</td>
	              <br />
	              <td style="border: 0px;">
	                <input class="btn_search2" id="btnsearchreport" type="submit" value="Search" />
	                <input class="btn_export" type="button" name="excel" id="btnexcelreport" value="Export To Excel" onclick="javascript:return excel_onsubmit()" />
	                <input class="btn_export" type="button" name="btngotohistorymaps" id="btngotohistorymaps" value="History Maps" onclick="javascript:return gotohistorymaps()"/>

	                <img id="loader2" style="display: none;" src="<?php echo base_url();?>assets/images/ajax-loader.gif" />
	                <!--input type="button" name="pdf" value="Export To PDF" onclick="javascript:return pdf_onsubmit()" /-->
	              </td>
	            </tr>

	          </table>
					</fieldset>
        </form>
        <br />
        <div id="result"></div>
        <iframe id="frmreq" style="display:none;"></iframe>
      </div>
    </div>
</div>
<script type="text/javascript">
  $(".chosen").chosen();
	$("#thisgeofence").chosen();
</script>
