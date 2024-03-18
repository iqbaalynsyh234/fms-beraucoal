<script src="<?php echo base_url(); ?>assets/dashboard/assets/plugins/jquery/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        //edit datepicker
        $(".glyphicon-arrow-right").html(">>");
        $(".glyphicon-arrow-left").html("<<");
        // frmsearch_onsubmit();

        // $('#myTable').DataTable({
        // "scrollX": true
        // });

    });

    function frmsearch_onsubmit() {
        var company = $("#company").val();
        var periode = $("#periode").val();
        if (periode == "last30") {
            if (company == "all") {
                alert("last 30 days data only for specific contractors!");
                return false;
            }
        }
        jQuery("#loader").show();
        // var data = jQuery("#frmsearch").serialize();
        // console.log(data);
        jQuery.post("<?= base_url(); ?>georeport/search_georeport", jQuery("#frmsearch").serialize(),
            function(r) {
                if (r.error) {
                    console.log(r);
                    alert(r.message);
                    jQuery("#loader").hide();
                    return;
                } else {
                    console.log(r);
                    jQuery("#result").html(r.html);
                    jQuery("#result").show();
                    jQuery("#loader").hide();

                }
            }, "json"
        );
        return false;
    }

    function company_onchange() {
        var data_company = jQuery("#company").val();
        if (data_company == 0) {
            // alert('Silahkan Pilih Cabang!!');
            // jQuery("#mn_vehicle").hide();

            jQuery("#vehicle").html("<option value='0' selected='selected' class='form-control'>--All Vehicle--</option>");
        } else {
            jQuery("#mn_vehicle").show();

            var site = "<?= base_url() ?>ritasereport/get_vehicle_by_company_with_numberorder/" + data_company;
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

    function periode_onchange() {
        var data_periode = jQuery("#periode").val();
        if (data_periode == 'custom') {
            jQuery(".mn_date").show();
            // jQuery("#mn_edate").show();
        } else {
            jQuery(".mn_date").hide();
            // jQuery("#mn_edate").hide();
        }

    }

    // function option_form(v) {
    // 	switch (v) {
    // 		case "hide":
    // 			jQuery("#btn_hide_form").hide();
    // 			jQuery("#btn_show_form").show();
    // 			jQuery("#panel_form").hide();

    // 			break;
    // 		case "show":
    // 			jQuery("#btn_hide_form").show();
    // 			jQuery("#btn_show_form").hide();
    // 			jQuery("#panel_form").show();
    // 			break;
    // 	}
    // }
</script>

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
<!-- start sidebar menu -->
<div class="sidebar-container">
    <?= $sidebar; ?>
</div>
<!-- end sidebar menu -->
<!-- start page content -->
<div class="page-content-wrapper">
    <div class="page-content">
        <div class="row">
            <div class="col-md-12 col-sm-12">
                <div class="panel" id="panel_form">
                    <header class="panel-heading panel-heading-red">GEOFENCE REPORT</header>
                    <div class="panel-body" id="bar-parent10">
                        <form class="form-horizontal" name="frmsearch" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
                            <div class="form-group row">

                                <div class="col-lg-2 col-md-2">
                                    <!-- <select class="form-control select2" id="company" name="company" onchange="javascript:company_onchange()"> -->
                                    <select class="form-control select2" id="company" name="company">
                                        <?php
                                        $pc = $this->sess->user_id_role;
                                        if (($pc == 0) || ($pc == 1) || ($pc == 2) || ($pc == 3) || ($pc == 4)) {
                                            echo '<option value="all">--All Contractor</option>';
                                        }
                                        $dcompany = count($rows_company);
                                        for ($i = 0; $i < $dcompany; $i++) {

                                            echo "<option value='" . $rows_company[$i]->company_id . "'>" . $rows_company[$i]->company_name . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="col-lg-2 col-md-2">
                                    <select id="vehicle" name="vehicle" class="form-control select2">
                                        <option value="0">--All Vehicle</option>
                                        <?php
                                        $cvehicle = count($vehicles);
                                        for ($i = 0; $i < $cvehicle; $i++) {
                                            echo "<option value='" . $vehicles[$i]->vehicle_device . "' " . $selected . ">" . $vehicles[$i]->vehicle_no . " - " . $vehicles[$i]->vehicle_name . "</option>";
                                        }
                                        ?>

                                    </select>
                                </div>

                                <div class="col-lg-2 col-md-2">
                                    <select id="periode" name="periode" class="form-control select2" onchange="javascript:periode_onchange()">
                                        <!-- <option value="today">Today</option> -->
                                        <option value="yesterday">Yesterday</option>
                                        <option value="last7">Last 7 Days</option>
                                        <option value="last30">Last 30 Days</option>
                                        <option value="custom">Custom Date</option>
                                    </select>
                                </div>

                                <div class="col-lg-2 col-md-2">
                                    <select id="model" name="model" class="form-control select2" onchange="javascript:periode_onchange()">
                                        <!-- <option value="today">Today</option> -->
                                        <option value="all">PORT & ROM</option>
                                        <option value="PORT">PORT</option>
                                        <option value="ROM">ROM</option>
                                    </select>
                                </div>

                                <!-- <div class="col-lg-2 col-md-3">
								<select id="reporttype" name="reporttype" class="form-control select2">
									<option value="0">Reguler</option>
								</select>
							</div> -->
                                <!-- </div> -->

                                <div class="col-lg-2 col-md-2">
                                    <button class="btn btn-circle btn-success" id="btnsearchreport" type="submit">Search</button>
                                </div>
                            </div>

                            <div class="form-group row mn_date" style="display: none;">

                                <!-- <div id="mn_date" > -->
                                <div class="col-lg-2 col-md-2">
                                    <div class="input-group date form_date" data-date="" data-date-format="dd-mm-yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
                                        <input class="form-control" type="text" readonly name="sdate" id="startdate" value="<?= date("d-m-Y"); ?>">
                                        <span class=" input-group-addon"><span class="fa fa-calendar"></span></span>
                                    </div>
                                </div>
                                <div class="col-lg-1 col-md-1">
                                    s/d
                                </div>
                                <div class="col-lg-2 col-md-2">
                                    <div class="input-group date form_date" data-date="" data-date-format="dd-mm-yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
                                        <input class="form-control" type="text" readonly name="edate" id="enddate" value="<?= date("d-m-Y"); ?>">
                                        <span class=" input-group-addon"><span class="fa fa-calendar"></span></span>
                                    </div>
                                </div>
                            </div>



                            <div id="loader" style="display: none;" class="mdl-progress mdl-js-progress mdl-progress__indeterminate is-upgraded" data-upgraded=",MaterialProgress">
                                <div class="progressbar bar bar1" style="width: 0%;"></div>
                                <div class="bufferbar bar bar2" style="width: 100%;"></div>
                                <div class="auxbar bar bar3" style="width: 0%;"></div>
                            </div>
                        </form>


                        <!-- <div id="result" class="row" style="background-color: white;"> -->

                    </div>

                </div>

            </div>

        </div>
        <div class="row" id="result">

        </div>
        <div class="row">
            <div class="col">
                <br>
                <br>
            </div>
        </div>
    </div>
</div>
<!-- end page content -->

</div>
<!-- end page container -->