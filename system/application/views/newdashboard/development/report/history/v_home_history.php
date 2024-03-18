<script>
  function frmsearch_onsubmit() {
    jQuery("#loader").show();
    page(0);
    return false;
  }

  function page(p) {
    if (p == undefined) {
      p = 0;
    }
    jQuery("#offset").val(p);
    jQuery("#loader2").show();
    // jQuery("#result").html('<img src="<?php echo base_url(); ?>assets/transporter/images/loader2.gif">');
    jQuery("#result").show();
    // jQuery("#btnsearchreport").hide();
    jQuery.post("<?= base_url(); ?>development/search_history_withplayback/", jQuery("#frmsearch").serialize(),
      function(r) {
        if (r.error) {
          alert(r.message);
          jQuery("#loader2").hide();
          jQuery("#result").hide();
          jQuery("#btnsearchreport").show();
          return;
        } else {
          console.log(r);
          jQuery("#loader2").hide();
          jQuery("#btnsearchreport").show();
          jQuery("#result").show();
          jQuery("#result").html(r.html);
          jQuery("#total").html(r.total);

        }
      }, "json"
    );
  }

  function company_onchange() {
    var data_company = jQuery("#company").val();
    if (data_company == 0) {
      alert('Silahkan Pilih Cabang!!');
      jQuery("#mn_vehicle").hide();

      jQuery("#vehicle").html("<option value='0' selected='selected' class='form-control'>--Select Vehicle--</option>");
    } else {
      jQuery("#mn_vehicle").show();

      var site = "<?= base_url() ?>dashboard/get_vehicle_by_company/" + data_company;
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


  // FUNGSI GOTO HISTORY MAP
  function historymap() {
    jQuery("#loader2").show();
    jQuery.post("<?= base_url(); ?>tripreportdev/search_history", jQuery("#frmsearch").serialize(),
      function(r) {
        jQuery("#loader2").hide();
        jQuery("#panel_form").hide();
        jQuery("#result").html(r.html);
      }, "json"
    );
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
</script>
<!-- start sidebar menu -->
<div class="sidebar-container">
  <?= $sidebar; ?>
</div>
<!-- end sidebar menu -->
<!-- start page content -->
<div class="page-content-wrapper">
  <div class="page-content">
    <!--<div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title">History Map</div>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                                <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?= base_url(); ?>">Home</a>&nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                                <li><a class="parent-item" href="">Report</a>&nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                                <li class="active">History Map</li>
                            </ol>
                        </div>
                    </div>-->
    <div class="row">
      <div class="col-md-12 col-sm-12">
        <div class="panel" id="panel_form">
          <header class="panel-heading panel-heading-red">HISTORY WITH RADIUS - DEVELOPMENT</header>
          <div class="panel-body" id="bar-parent10">
            <form class="form-horizontal" name="frmsearch" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">

              <div class="form-group row">
                <div class="col-md-6">
                  <select class="form-control select2" name="unit_1" id="unit_1">
                  <option value="0">--Vehicle List</option>
                  <?php for ($j=0; $j < sizeof($vehicles); $j++) {?>
                    <option value="<?php echo $vehicles[$j]['vehicle_device']; ?>"><?php echo $vehicles[$j]['vehicle_no']; ?></option>
                <?php } ?>
                </select>
                </div>

                <div class="col-md-6">
                  <select class="form-control select2" name="unit_2" id="unit_2">
                  <option value="0">--Vehicle List</option>
                  <?php for ($j=0; $j < sizeof($vehicles); $j++) {?>
                    <option value="<?php echo $vehicles[$j]['vehicle_device']; ?>"><?php echo $vehicles[$j]['vehicle_no']; ?></option>
                <?php } ?>
                </select>
                </div>
              </div>

              <div class="form-group row">
                <div class="col-md-2">
                  <div class="input-group date form_date col-md-12" data-date="" data-date-format="dd-mm-yyyy" data-link-field="dtp_input1" data-link-format="yyyy-mm-dd">
                    <input class="form-control" size="5" type="text" readonly name="unit_1_starttime" id="unit_1_starttime" value="<?= date('d-m-Y') ?>">
                    <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                    <input type="hidden" id="dtp_input1" value="" />
                  </div>
                </div>

                <div class="col-md-2">
                  <select class="form-control select2" name="unit_1_hour" id="unit_1_hour">
                    <?php
                    $string = 0;
                    $time_unit_1 = "";
                    for ($i=0; $i < 24; $i++) {
                      if ($i < 10) {
                        $time_unit_1 = $string.$i;
                       }else {
                         $time_unit_1 = $i;
                       }?>
                       <option value="<?php echo $time_unit_1 ?>"><?php echo $time_unit_1; ?></option>
                    <?php } ?>
                  </select>
                </div>

                <div class="col-md-2">
                  <select class="form-control select2" name="unit_1_minutes" id="unit_1_minutes">
                    <?php
                    $string = 0;
                    $time_unit_1 = "";
                    for ($i=0; $i < 60; $i++) {
                      if ($i < 10) {
                        $time_unit_1 = $string.$i;
                       }else {
                         $time_unit_1 = $i;
                       }?>
                       <option value="<?php echo $time_unit_1 ?>"><?php echo $time_unit_1; ?></option>
                    <?php } ?>
                  </select>
                </div>

                <div class="col-md-2">
                  <div class="input-group date form_date col-md-12" data-date="" data-date-format="dd-mm-yyyy" data-link-field="dtp_input1" data-link-format="yyyy-mm-dd">
                    <input class="form-control" size="5" type="text" readonly name="unit_2_starttime" id="unit_2_starttime" value="<?= date('d-m-Y') ?>">
                    <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                    <input type="hidden" id="dtp_input1" value="" />
                  </div>
                </div>

                <div class="col-md-2">
                  <select class="form-control select2" name="unit_2_hour" id="unit_2_hour">
                    <?php
                    $string = 0;
                    $time_unit_2 = "";
                    for ($i=0; $i < 24; $i++) {
                      if ($i < 10) {
                        $time_unit_2 = $string.$i;
                       }else {
                         $time_unit_2 = $i;
                       }?>
                       <option value="<?php echo $time_unit_2 ?>"><?php echo $time_unit_2; ?></option>
                    <?php } ?>
                  </select>
                </div>

                <div class="col-md-2">
                  <select class="form-control select2" name="unit_2_minutes" id="unit_2_minutes">
                    <?php
                    $string = 0;
                    $time_unit_2 = "";
                    for ($i=0; $i < 60; $i++) {
                      if ($i < 10) {
                        $time_unit_2 = $string.$i;
                       }else {
                         $time_unit_2 = $i;
                       }?>
                       <option value="<?php echo $time_unit_2 ?>"><?php echo $time_unit_2; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12 text-right" id="btnsearchradiusshowhide">
                  <button type="submit" class="btn btn-md btn-primary" name="btnsearchradius" id="btnsearchradius">Process</button>
                  <img id="loader3" style="display:none;" src="<?php echo base_url();?>assets/images/anim_wait.gif" />
                </div>
              </div>

            </form>
          </div>

        </div>

      </div>

    </div>
    <div id="loader2" class="mdl-progress mdl-js-progress mdl-progress__indeterminate" style="display:none;"></div>
    <div id="result"></div>
  </div>
  <!-- end page content -->

</div>
<!-- end page container -->
<script type="text/javascript">
  function houronclick() {
    $(".switch").html("<?php echo date("Y F d ") ?>");
  }
</script>
