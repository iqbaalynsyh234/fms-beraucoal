<style media="screen">
  div#modalinformationdetail {
    max-height: 500px;
    width: 65%;
    overflow-x: auto;
    position: fixed;
    z-index: 9;
    background-color: #f1f1f1;
    text-align: left;
    border: 1px solid #d3d3d3;
  }
</style>
<div class="sidebar-container">
  <?= $sidebar; ?>
</div>

<div class="page-content-wrapper">
  <div class="page-content" id="page-content-new">
    <div class="row">
      <div class="col-md-12 col-sm-12" id="reportfilter">


        <div class="panel" id="panel_form">
          <header class="panel-heading panel-heading-red"><?php echo $title ?></header>
          <div class="panel-body" id="bar-parent10">
            <form class="form-horizontal" name="frmsearch" id="frmsearch" onsubmit="javascript: return frmsearch_onsubmit();">
              <div class="form-group row">
                <div class="col-md">
                  <?php
                  // $d1 = "2022-01-20";
                  // $d2 = "2022-01-21";
                  // $yesterday1  = mktime(0, 0, 0, date('n'), date('j', mktime()), date('Y')) - 7 * 3600;
                  // $yesterday2  = mktime(0, 0, 0, date('n'), date('j', mktime()), date('Y')) - 0 * 3600;
                  // echo $this->uri->segment(2) . "<br>";
                  // echo $yesterday1 . "<br>";
                  // echo $yesterday2 . "<br>";
                  // $yesterday = date('Y-m-d', strtotime("-1 days"));
                  // echo $yesterday . "<br>";
                  // if ($d2 > $d1) {
                  //   echo $d2 . " lebih besar dari " . $d1;
                  // } else {
                  //   echo $d2 . " lebih kecil dari " . $d1;
                  // } 
                  ?>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-lg-3 col-md-3 control-label">Contractor
                </label>
                <div class="col-lg-4 col-md-3">
                  <select class="form-control select2" id="company" name="company" onchange="javascript:company_onchange()">
                    <option value="0" selected>--Select Contractor--</option>
                    <?php
                    $ccompany = count($rcompany);
                    for ($i = 0; $i < $ccompany; $i++) {
                      if (isset($rcompany) && ($row->parent_company == $rcompany[$i]->company_id)) {
                        $selected = "selected";
                      } else {
                        $selected = "";
                      }
                      echo "<option value='" . $rcompany[$i]->company_id . "' " . $selected . ">" . $rcompany[$i]->company_name . "</option>";
                    }
                    ?>
                  </select>
                </div>
              </div>

              <div class="form-group row">
                <label class="col-lg-3 col-md-4 control-label">Vehicle
                </label>
                <div class="col-lg-4 col-md-3">
                  <select id="vehicle" name="vehicle" class="form-control select2">
                    <option value="0">--Select Vehicle--</option>
                    <?php
                    $i = 1;
                    foreach ($data as $rowvehicle) {
                    ?>
                      <option value="<?php echo $rowvehicle['vehicle_device'] ?>">
                        <?php echo $i . '. ' . $rowvehicle['vehicle_no'] . ' ' . $rowvehicle['vehicle_name'] ?>
                      </option>
                    <?php $i++;
                    } ?>
                  </select>
                </div>
              </div>

              <div class="form-group row" id="mn_sdate">
                <label class="col-lg-3 col-md-4 control-label">Date
                </label>
                <div class="input-group date form_date col-md-3" data-date-format="dd-mm-yyyy" data-link-format="yyyy-mm-dd">
                  <input type="text" class="form-control" size="5" type="text" readonly name="date" value="<?= date('d-m-Y', strtotime(" yesterday ")) ?>">
                  <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                </div>

                <div class="input-group date form_time col-md-2" data-date="" data-date-format="hh:ii" data-link-field="dtp_input2" data-link-format="hh:ii">
                  <input class="form-control" size="5" type="text" readonly id="shour" name="shour" value="<?= date("H:i", strtotime("00:00:00 ")) ?>">
                  <span class="input-group-addon"><span class="fa fa-clock-o"></span></span>
                </div>
                <input type="hidden" id="dtp_input2" value="" />
                <div class="input-group col-md-1">to</div>
                <div class="input-group date form_time col-md-2" data-date="" data-date-format="hh:ii" data-link-field="dtp_input3" data-link-format="hh:ii">
                  <input class="form-control" size="5" type="text" readonly id="ehour" name="ehour" value="<?= date("H:i", strtotime("23:59:59 ")) ?>">
                  <span class="input-group-addon"><span class="fa fa-clock-o"></span></span>
                </div>
                <input type="hidden" id="dtp_input3" value="" />
              </div>

              <div class="form-group row" id="mn_vehicle">
                <label class="col-lg-3 col-md-3 control-label">Data
                </label>
                <div class="col-lg-4 col-md-3">
                  <select id="data" name="data" class="form-control">
                    <option value="1">Detail</option>
                    <option value="2">Summary</option>
                  </select>
                </div>

              </div>

              <div class="form-group row">
                <label class="col-lg-3 col-md-4 control-label"></label>
                <div class="col-lg-3 col-md-3">
                  <button class="btn btn-circle btn-success" id="btnsearchreport" type="submit">Search</button>
                  <img src="<?php echo base_url(); ?>assets/transporter/images/loader2.gif" style="display: none;" id="loadernya">
                </div>
              </div>
            </form>

          </div>
        </div>
      </div>

      <div class="col-md-12" id="resultreport" style="display:none;">

      </div>

    </div>
    <div id="result" style="width:100%"></div>
  </div>
</div>

<script type="text/javascript" src="js/script.js"></script>
<script src="<?php echo base_url() ?>assets/dashboard/assets/js/jquery-1.7.1.min.js" type="text/javascript"></script>

<script type="text/javascript">
  $("#loadernya").hide();

  function frmsearch_onsubmit() {
    // console.log(jQuery("#frmsearch").serialize());
    var vehicle = jQuery("#vehicle").val();
    var shour = jQuery("#shour").val();
    var ehour = jQuery("#ehour").val();
    if (vehicle === "0") {
      alert("Please select vehicle!");
      return false;
    } else if (ehour < shour) {
      alert("Time invalid!");
      return false;
    } else {
      var data = jQuery("#frmsearch").serialize();

      // console.log(data);
      $("#resultreport").hide();
      $("#loadernya").show();
      $.post("<?php echo base_url() ?>triphistory/searchhistorytest", data, function(r) {
        if (r.error) {
          $("#loadernya").hide();
          $("#resultreport").html(r.html);
          $("#resultreport").show();
          alert("data is empty");
          console.log(r.report);
        } else {
          $("#loadernya").hide();
          $("#resultreport").html(r.html);
          $("#resultreport").show();
          console.log(r.report);
        }
      }, "json");
      return false;
    }
  }

  function option_form(v) {
    switch (v) {
      case "hide":
        jQuery("#btn_hide_form").hide();
        jQuery("#btn_show_form").show();
        jQuery("#panel_form").hide();

        break;
      case "show":
        jQuery("#btn_hide_form").show();
        jQuery("#btn_show_form").hide();
        jQuery("#panel_form").show();
        break;
    }
  }


  function company_onchange() {
    var data_company = jQuery("#company").val();
    if (data_company == 0) {

      jQuery("#vehicle").html("<option value='0' selected='selected' class='form-control'>--Select Vehicle--</option>");
    } else {
      jQuery("#mn_vehicle").show();

      var site = "<?= base_url() ?>triphistory/get_vehicle_by_company_with_numberorder/" + data_company;
      jQuery.ajax({
        url: site,
        success: function(response) {
          jQuery("#vehicle").html("");
          jQuery("#vehicle").html(response);
        },
        dataType: "html"
      });

    }

  }
</script>