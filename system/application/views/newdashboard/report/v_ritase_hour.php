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

    /* style table */
    th,
    td {
        text-align: center;
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
                <header class="panel-heading panel-heading-red">Hourly Ritase Report</header>

                <div class="panel-body" id="bar-parent10">

                    <div class="row">
                        <div class="col-md-1 col-sm-2">
                            <p><b>Date : </b></p>
                        </div>
                        <div class="input-group date form_date col-md-2 col-sm-5" data-date="" data-date-format="dd-mm-yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
                            <input class="form-control" type="text" readonly name="date" id="startdate" value="<?= date('d-m-Y') ?>">
                            <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                        </div>
                        <div class="col-md-2 col-sm-5">
                            <input type="hidden" id="total_vehicle" name="total_vehicle" value="">
                            <select id="shift" name="shift" class="form-control select2">
                                <option value="0">-- All Shift --</option>
                                <option value="1">Shift 1</option>
                                <option value="2">Shift 2</option>
                            </select>
                        </div>

                        <div class="col-md-2 col-sm-5">
                            <select class="form-control select2" id="company" name="company">
                                <option value="0" selected>--All Contractor</option>
                                <?php
                                $ccompany = count($rcompany);
                                for ($i = 0; $i < $ccompany; $i++) {

                                    echo "<option value='" . $rcompany[$i]->company_id . "'>" . $rcompany[$i]->company_name . "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-5 col-sm-5">
                            <button class="btn btn-circle btn-success" onclick="dtdatechange()">Search</button>
                            <img src="<?php echo base_url(); ?>assets/transporter/images/loader2.gif" style="display: none;" id="loadernya">
                        </div>

                        <div class="col-md-1 col-sm-3">
                        </div>
                    </div>

                    <div class="row">
                        <table id="table"></table>
                        <div class="col-md-12" id="viewtable">
                            <br>
                            <button type="button" name="button" id="export_xcel" class="btn btn-primary btn-sm">Export Excel</button>

                            <div id="isexport_xcel">
                                <div class="row">
                                    <div class="col-md">
                                        <b id="exceltitle">Laporan Lokasi Unit <span id="titlecontractor"></span> setiap Jam di <span id="titleshift"></span> tanggal <span id="titledate"></span></b>
                                    </div>
                                    <!-- <div class="col-md-6">
                                        <p id="totalan" style="text-align:right;"></p>
                                    </div> -->
                                </div>
                                <table class="table table-striped table-bordered" id="content" style="font-size: 12px;">



                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script type="text/javascript" src="js/script.js"></script>
    <script src="<?php echo base_url() ?>assets/dashboard/assets/js/jquery-1.7.1.min.js" type="text/javascript"></script>

    <script type="text/javascript">
        function setdate(date) {
            var exp = date.split("-");
            return exp[2] + "-" + exp[1] + "-" + exp[0];
        }

        function dtdatechange() {
            $("#loadernya").show();
            $("#viewtable").hide();
            var company = $("#company").val();
            var date = $("#startdate").val();
            var shift = $("#shift").val();
            var titlecompany = $("#company").val();
            var shiftname = "";
            if (shift == 0) {
                shiftname = "Semua Shift";
            } else if (shift == 1) {
                shiftname = "Shift 1";
            } else {
                shiftname = "Shift 2";
            }
            var data = {
                date: date,
                shift: shift,
                company: company
            };
            jQuery.post("<?php echo base_url() ?>ritasehour/search", data, function(response) {
                console.log(response);
                data = response.data;
                var no = 0;
                if (response.error) {
                    alert(response.msg);
                    $("#loadernya").hide();
                    return false;
                } else {
                    $("#loadernya").hide();
                    if (titlecompany == 0) {
                        $("#titlecontractor").html("Semua Kontraktor");
                    } else {
                        $("#titlecontractor").html(data[0]['ritase_report_company_name']);

                    }
                    $("#titleshift").html(shiftname);
                    $("#titledate").html(date);
                    var html = "";
                    html += `<tr>
                                        <th>No</th>
                                        <th>Vehicle No</th>
                                        <th>Contractor</th>
                                        <th>ROM</th>
										<th>PORT</th>
										<th>Duration</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                    </tr>`;
                    for (i = 0; i < response.total; i++) {
                        no = i + 1;
                        html += `<tr>
                                        <td>` + no + `</td>
                                        <td>` + data[i]['ritase_report_vehicle_no'] + `</td>
                                        <td>` + data[i]['ritase_report_company_name'] + `</td>
										<td>` + data[i]['ritase_report_from'] + `</td>
										<td>` + data[i]['ritase_report_to'] + `</td>
										<td>` + data[i]['ritase_report_duration'] + `</td>
                                        <td>` + setdate(data[i]['ritase_report_gps_date']) + `</td>
                                        <td>` + data[i]['ritase_report_gps_hour'] + `</td>
                                
                                    </tr>`;
                    }

                    $("#content").html(html);

                    $("#viewtable").show();
                }
            }, "json");

        }


        $(document).ready(function() {
            //edit datepicker
            $(".glyphicon-arrow-right").html(">>");
            $(".glyphicon-arrow-left").html("<<");



            dtdatechange();
            jQuery("#export_xcel").click(function() {
                var myBlob = new Blob([jQuery('#isexport_xcel').html()], {
                    type: 'application/vnd.ms-excel'
                });
                var url = window.URL.createObjectURL(myBlob);
                var a = document.createElement("a");
                document.body.appendChild(a);
                a.href = url;
                a.download = "ritase_hour.xls";
                a.click();
                //adding some delay in removing the dynamically created link solved the problem in FireFox
                setTimeout(function() {
                    window.URL.revokeObjectURL(url);
                }, 0);
            });
        });
    </script>