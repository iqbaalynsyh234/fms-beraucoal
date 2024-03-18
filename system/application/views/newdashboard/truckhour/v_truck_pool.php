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

    #truck-pool{
        background-color: #221f1f;
        color: white;
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
                <header class="panel-heading panel-heading-red" id="truck-pool">Truck On Pool</header>
                <div class="panel-body" id="bar-parent10">

                    <div class="row">
                        <div class="col-md-1 col-sm-2">
                            <p><b>Date : </b></p>
                        </div>
                        <div class="input-group date form_date col-md-2 col-sm-5" data-date="" data-date-format="dd-mm-yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
                            <input class="form-control" type="text" readonly name="date" id="startdate" value="<?= date('d-m-Y') ?>" onchange="dtdatechange()">
                            <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                        </div>
                        <div class="col-md-2 col-sm-5">
                            <input type="hidden" id="total_vehicle" name="total_vehicle" value="">
                            <select id="shift" name="shift" class="form-control select2" onchange="dtdatechange()">
                                <option value="0">-- All Shift --</option>
                                <option value="1">Shift 1</option>
                                <option value="2">Shift 2</option>
                            </select>
                        </div>

                        <div class="col-md-2 col-sm-5">
                            <select id="viewdata" name="viewdata" class="form-control select2" onchange="viewchange()">
                                <option value="1">Graph view</option>
                                <option value="0">Table view</option>
                            </select>
                        </div>

                        <div class="col-md-2 col-sm-5">
                            <select class="form-control select2" id="company" name="company" onchange="dtdatechange()">
                                <?php $privilege = $this->sess->user_id_role;
                                if (($privilege == 5) || ($privilege == 6)) { ?>
                                <?php
                                } else {
                                ?>
                                    <option value="0" selected>--All Contractor</option>

                                <?php
                                }
                                $ccompany = count($rcompany);
                                for ($i = 0; $i < $ccompany; $i++) {

                                    echo "<option value='" . $rcompany[$i]->company_id . "'>" . $rcompany[$i]->company_name . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-1 col-sm-3">
                            <button type="button" name="button" id="export_xcel" class="btn btn-primary btn-sm" style="display:none">Export Excel</button>

                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-12" id="viewtable" style="display:none;">
                            <br>
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
                            <br>
                            <div class="row">
                                <div class="col-md" id="graphic_caption">

                                </div>
                            </div>
                            <br>
                            <figure class="highcharts-figure">
                                <div id="container_graphic"></div>
                            </figure>
                        </div>
                        <div class="col-md-12">
                            <div id="test"></div>
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
        $("#notifnya").fadeIn(1000);
        $("#notifnya").fadeOut(5000);


        // function dtdatechange() {
        //     var company = $("#company").val();
        //     var date = $("#startdate").val();
        //     var shift = $("#shift").val();
        //     var shiftname = "";
        //     if (shift == 0) {
        //         shiftname = "Semua Shift";
        //     } else if (shift == 1) {
        //         shiftname = "Shift 1";
        //     } else {
        //         shiftname = "Shift 2";
        //     }
        //     var data = {
        //         date: date,
        //         shift: shift,
        //         company: company
        //     };
        //     $("#totalan").html("");
        //     $("#content").html("");
        //     $("#container_graphic").html("");
        //     $("#exceltitle").html("");
        //     jQuery.post("<?php echo base_url() ?>truck/search_on_pool", data, function(response) {
        //         console.log(response);
        //         var data = response.data;
        //         var total_unit_contractor = response.total_unit_contractor;
        //         var company = response.company;
        //         var hour = response.hour;
        //         var total = response.total;
        //         var clength = response.clength;
        //         var hlength = response.hlength;
        //         var x = 0;
        //         var n = 0;
        //         var totalin = 0;
        //         var totalan = 0;
        //         var totalavg = 0;
        //         var charttotal = [];
        //         var d = 0;
        //         var e = 0;
        //         if (total > 0) {
        //             var html = "";
        //             html += `<thead><tr>
        //               <th style="text-align:center;">Hour</th>`;
        //             for (var i = 0; i < clength; i++) {
        //                 // total_unit_contractor[company[i]] = 0;
        //                 html += '<th style="text-align:center;">' + company[i] + '<br><span>' + total_unit_contractor[company[i]] + '</span></th>';
        //             }
        //             html += `   <th style="text-align:center;">Total</th>
        //           </tr></thead><tbody>`;
        //             for (var j = 0; j < hlength; j++) {
        //                 n = 0;
        //                 total = 0;
        //                 html += `<tr><th style="text-align:center;">` + hour[j] + `</th>`;

        //                 for (var k = 0; k < clength; k++) {
        //                     html += `<td style="text-align:center;">` + data[hour[j]][company[k]] + `</td>`;
        //                     x = n + parseInt(data[hour[j]][company[k]]);
        //                     n = x;
        //                 }
        //                 totalin = totalan + n;
        //                 totalan = totalin;
        //                 charttotal[hour[j]] = n;
        //                 html += `<th style="text-align:center;">` + n + `</th></tr>`;
        //             }
        //             totalavg = totalan / hlength;
        //             totalavg = Math.round(totalavg);
        //             html += `</tbody>`;
        //             $("#content").html(html);
        //             $("#totalan").html(`Total Unit Rata-rata ` + shiftname + ` : <b>` + totalavg + ` &nbsp; </b>`);
        //             $("#exceltitle").html("Data DT On Pool tanggal " + date);
        //             creatchart(response, charttotal);
        //         } else {
        //             console.log(response);
        //             alert("data empty");
        //         }
        //     }, "json");

        // }

        function dtdatechange() {
            var company = $("#company").val();
            var date = $("#startdate").val();
            var shift = $("#shift").val();
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
            $("#totalan").html("");
            $("#content").html("");
            $("#container_graphic").html("");
            $("#exceltitle").html("");
            jQuery.post("<?php echo base_url() ?>truck/search_on_pool", data, function(response) {
                console.log(response);
                var data = response.data;
                var total_unit = response.total_unit;
                var total_unit_contractor = response.total_unit_contractor;
                var company = response.company;
                var hour = response.hour;
                var total = response.total;
                var clength = response.clength;
                var hlength = response.hlength;
                var x = 0;
                var n = 0;
                var totalin = 0;
                var totalan = 0;
                var totalavg = 0;
                var charttotal = [];
                var d = 0;
                var e = 0;
                if (total > 0) {
                    $("#total_vehicle").val(total_unit);
                    var html = "";
                    html += `<thead><tr>
                      <th style="text-align:center;">Hour</th>`;
                    if (clength == 1) {
                        html += '<th style="text-align:center;">' + company[0] + '<br><span>' + total_unit + '</span></th>';
                    } else {
                        for (var i = 0; i < clength; i++) {
                            html += '<th style="text-align:center;">' + company[i] + '<br><span>' + total_unit_contractor[company[i]] + '</span></th>';
                        }
                    }
                    html += `   <th style="text-align:center;">Total</th>
                  </tr></thead><tbody>`;
                    for (var j = 0; j < hlength; j++) {
                        n = 0;
                        total = 0;
                        html += `<tr><th style="text-align:center;">` + hour[j] + `</th>`;

                        for (var k = 0; k < clength; k++) {
                            html += `<td style="text-align:center;">` + data[hour[j]][company[k]] + `</td>`;
                            x = n + parseInt(data[hour[j]][company[k]]);
                            n = x;
                        }
                        totalin = totalan + n;
                        totalan = totalin;
                        charttotal[hour[j]] = n;
                        html += `<th style="text-align:center;">` + n + `</th></tr>`;
                    }
                    totalavg = totalan / hlength;
                    totalavg = Math.round(totalavg);
                    html += `</tbody>`;
                    $("#content").html(html);
                    $("#totalan").html(`Total Unit Rata-rata ` + shiftname + ` : <b>` + totalavg + ` &nbsp; </b>`);
                    $("#exceltitle").html("Data DT On Pool tanggal " + date);

                    creatchart(response, charttotal);
                } else {
                    console.log(response);
                    alert("data empty");
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



        function creatchart(response, charttotal) {
            var total_vehicle = $("#total_vehicle").val();
            var data = response.data;
            var company = response.company;
            var hour = response.hour;
            var total = response.total;
            var clength = response.clength;
            var hlength = response.hlength;
            var pcn = 0;
            var datalevel1 = [];
            var datalevel2 = [];

            if (clength > 1) {
                c_name = "All Contractor";
            } else {
                c_name = company[0];
            }


            if (total > 0) {
                // $("#graphic_caption").html("Total Vehicle : <b>" + total_vehicle + "</b>");
                for (i = 0; i < hlength; i++) {
                    pcn = (charttotal[hour[i]] / total_vehicle) * 100
                    pcn = Math.round(pcn);
                    if (clength == 1) {
                        datalevel1.push({
                            "name": hour[i],
                            "y": pcn,
                            "z": charttotal[hour[i]]
                        });
                    } else {
                        datalevel1.push({
                            "name": hour[i],
                            "y": pcn,
                            "z": charttotal[hour[i]],
                            "drilldown": "content_" + hour[i]
                        });
                    }
                    var dc = [];
                    for (j = 0; j < clength; j++) {
                        value = data[hour[i]][company[j]];
                        vl = value.split(" ");
                        pcn = vl[1].split("(");
                        pcn = pcn[1].split("%)");
                        dc.push([company[j] + " <br><b style=\"display:none\">" + vl[0] + " unit</b>", parseInt(pcn[0])]);
                    }

                    datalevel2.push({
                        "name": hour[i],
                        "id": "content_" + hour[i],
                        "z": charttotal[hour[i]],
                        "data": dc
                    });
                }

                // console.log(datalevel2);

                Highcharts.chart('container_graphic', {
                    chart: {
                        type: 'column',
                        events: {
                            drilldown: function(e) {
                                this.title.textSetter("Hour : <b>" + e.seriesOptions.name + "</b> | Total Unit : <b>" + e.seriesOptions.z + "</b>");

                                $(".highcharts-xaxis text").html("");

                            },
                            drillup: function() {
                                this.title.textSetter("Total Unit " + c_name + " : <b>" + total_vehicle + "</b>");

                                $(".highcharts-xaxis text").html("Hour");
                            }
                        }
                    },
                    title: {
                        useHTML: true,
                        text: "Total Unit " + c_name + " : <b>" + total_vehicle + "</b>"
                    },
                    subtitle: {
                        text: ''
                    },
                    accessibility: {
                        announceNewData: {
                            enabled: true
                        }
                    },
                    xAxis: {
                        type: 'category',
                        title: {
                            text: 'Hour'
                        }
                        // labels: {
                        //   formatter: function() {
                        //     return "test";
                        //   }
                        // }
                    },
                    yAxis: {
                        title: {
                            text: 'Percentage'
                        },
                        labels: {
                            format: '{value}%'
                        },
                        max: 100,
                        // plotLines: [{
                        //     color: '#D32E36',
                        //     width: 2,
                        //     value: 85,
                        //     dashStyle: 'shortdash',
                        //     label: {
                        //         text: '85%'
                        //     }
                        // }]

                    },
                    legend: {
                        enabled: false
                    },
                    plotOptions: {
                        series: {
                            // useHTML: true,
                            borderWidth: 0,
                            dataLabels: {
                                align: 'center',
                                position: 'center',
                                style: {
                                    textAlign: 'center'
                                },
                                enabled: true,
                                // format: '{point.z} {point.drilldown.series.z} unit <br> {point.y}%',
                                formatter: function() {
                                    var unitlvl1 = this.point.options.z;
                                    var ulvl2 = this.point.options.name.split("\">");
                                    var ustr = String(ulvl2[1]);
                                    var unitlevel2 = ustr.split(" ");
                                    var unitlvl2 = unitlevel2[0];
                                    if (unitlvl1 == undefined) {
                                        unitlvl1 = "";
                                        // console.log(this);
                                    } else {
                                        unitlvl2 = "";
                                    }
                                    return unitlvl1 + unitlvl2 + " unit <br>" + this.point.options.y + '%';
                                }
                            }
                        }
                    },

                    tooltip: {
                        enabled: false
                        // headerFormat: "",
                        // `<span style="font-size:11px">{series.name} : </span> <span style="color:{point.color}">{point.n}</span> <br>`,
                        // pointFormat: `<b>{point.y:.0f}%</b><br> <b>{point.z:.0f} {point.series.z:.0f} unit</b>`
                    },

                    series: [{
                        name: "Hour",
                        // colorByPoint: true,
                        color: "#D32E36",
                        data: datalevel1
                    }],
                    drilldown: {
                        breadcrumbs: {
                            position: {
                                align: 'right'
                            }
                        },
                        activeAxisLabelStyle: {
                            textDecoration: 'none',
                            color: '#000000'
                            // fontStyle: 'italic'
                        },
                        series: datalevel2
                    }
                });

                //edit style graphic
                $(".highcharts-data-label text").css("text-decoration", "none");

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