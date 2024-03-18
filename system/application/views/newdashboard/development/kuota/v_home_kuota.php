<script src="<?php echo base_url(); ?>assets/dashboard/assets/plugins/jquery/jquery.min.js"></script>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/data.js"></script>
<script src="https://code.highcharts.com/modules/drilldown.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<style type="text/css">
  /* edit style datepicker*/
  .datetimepicker {
    background: #D32E36;
  }

  .prev,
  .switch,
  .next,
  .today {
    background: #FFF;
  }

  .dow {
    color: #FFF;
    padding: 6px;
  }

  .table-condensed tbody tr td {
    color: #FFF;
  }

  .datetimepicker .datetimepicker-days table tbody tr td:hover {
    background-color: #000;
  }

  .datetimepicker .datetimepicker-years table tbody tr td span:hover {
    background-color: #000;
  }

  .datetimepicker .datetimepicker-months table tbody tr td span:hover {
    background-color: #000;
  }



  /* edit Style graphic */
  .highcharts-data-label text {
    text-decoration: none;
  }

  .highcharts-drilldown-axis-label {
    pointer-events: none;
  }
</style>
<div class="sidebar-container">
  <?= $sidebar; ?>
</div>

<div class="page-content-wrapper">
  <div class="page-content">
    <br>
    <?php if ($this->session->flashdata('notif')) { ?>
      <div class="alert alert-success" id="notifnya" style="display: none;"><?php echo $this->session->flashdata('notif'); ?></div>
    <?php } ?>
    <!--<div class="alert alert-success" id="notifnya2" style="display: none;"></div>-->
    <div class="col-md-12">
      <div class="panel" id="panel_form">
        <header class="panel-heading panel-heading-red">Dashboard Kuota</header>
        <div class="panel-body" id="bar-parent10">

          <div class="row">
            <div class="input-group date form_date col-md-2 col-sm-5" data-date="" data-date-format="dd-mm-yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
              <input class="form-control" type="text" readonly name="enddate" id="enddate" value="<?= date('d-m-Y') ?>" onchange="dtdatechange()">
              <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
            </div>

            <div class="col-md-2 col-sm-5">
              <select class="form-control select2" id="company" name="company" onchange="dtdatechange()">
                <option value="0" selected>--All Contractor</option>
                <?php
                $ccompany = count($rcompany);
                for ($i = 0; $i < $ccompany; $i++) {

                  echo "<option value='" . $rcompany[$i]->company_id . "'>" . $rcompany[$i]->company_name . "</option>";
                }
                ?>
              </select>
            </div>

              <div class="col-lg-4 col-md-3">
                  <select id="vehicle" name="vehicle" class="form-control select2">
                    <option value="all">--All Vehicle</option>
                  <?php
                  $i = 1;
                   foreach ($data as $rowvehicle) {
                     ?>
                    <option value="<?php echo $rowvehicle['vehicle_mv03'] ?>"><?php echo $i.'. '.$rowvehicle['vehicle_no'].' '.$rowvehicle['vehicle_name'] ?></option>
                  <?php $i++; } ?>
                </select>
              </div>

            <div class="col-md-1 col-sm-3">
              <button type="button" name="button" id="export_xcel" class="btn btn-primary btn-sm" style="display:none">Export Excel</button>
            </div>

          </div>
          <div class="row">
            <div class="col">
              <!-- loader -->
              <div id="loader" style="display: none;" class="mdl-progress mdl-js-progress mdl-progress__indeterminate is-upgraded" data-upgraded=",MaterialProgress">
                <div class="progressbar bar bar1" style="width: 0%;"></div>
                <div class="bufferbar bar bar2" style="width: 100%;"></div>
                <div class="auxbar bar bar3" style="width: 0%;"></div>
              </div>
              <!-- end loader -->

            </div>
          </div>

          <div class="row">
            <div id="valueHidden" style="display: none;"></div>
            <div class="col-md-12" id="viewtable" style="display:none;">
              <div id="isexport_xcel">
                <div class="row">
                  <div class="col-md-6">
                    <b id="exceltitle"></b>
                  </div>
                  <div class="col-md-6">
                    <p id="totalan" style="text-align:right;"></p>
                  </div>
                </div>
                <table class="table table-striped table-bordered" id="content" style="font-size: 14px; overflow-y:auto;">
                </table>
              </div>
            </div>
            <!-- start graphic -->
            <div class="col-lg-12 col-md-12 col-sm-12" id="viewgraphic" style="bottom:13px;">

              <div class="row">
                <div class="col-md" id="graphic_caption">

                </div>
              </div>
              <br>
              <figure class="highcharts-figure">
                <div id="container_graphic"></div>
              </figure>
            </div>
            <!-- end graphic -->


          </div>

        </div>
      </div>
    </div>
  </div>


  <script type="text/javascript" src="js/script.js"></script>
  <script src="<?php echo base_url() ?>assets/dashboard/assets/js/jquery-1.7.1.min.js" type="text/javascript"></script>

  <script type="text/javascript">
    function dtdatechange() {
      $("#loader").show();
      var company = $("#company").val();
      var date    = $("#enddate").val();

      var data = {
        date: date,
        company: company,
      };
      
      $("#totalan").html("");
      $("#content").html("");
      $("#container_graphic").html("");
      $("#exceltitle").html("");
      $("#valueHidden").html("");
      jQuery.post("<?php echo base_url() ?>development/searchforkuota", data, function(response) {
        console.log(response);
        $("#loader").hide();

        if (response.error) {
          console.log(response);
          alert(response.msg);
        } else {

        }


      }, "json");

    }

    function viewchange() {
      var view = $("#viewdata").val();
      if (view == 0) {
        $("#viewtable").show();
        $("#export_xcel").show();
        $("#viewgraphic").hide();

      } else if (view == 1) {
        $("#viewtable").hide();
        $("#export_xcel").hide();
        $("#viewgraphic").show();

      }
    }

    $(document).ready(function() {
      //edit datepicker
      $(".glyphicon-arrow-right").html(">>");
      $(".glyphicon-arrow-left").html("<<");

      dtdatechange();
      jQuery("#export_xcel").click(function() {
        window.open('data:application/vnd.ms-excel,' + encodeURIComponent(jQuery('#isexport_xcel').html()));
      });

    });


  </script>
