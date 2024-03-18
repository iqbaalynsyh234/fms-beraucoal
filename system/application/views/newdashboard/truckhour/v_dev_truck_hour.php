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

    #truck{
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
                <header class="panel-heading panel-heading-red" id="truck">Truck On Duty</header>
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
                                <option value="0" selected>--All Contractor</option>
                                <?php
                                $ccompany = count($rcompany);
                                for ($i = 0; $i < $ccompany; $i++) {

                                    echo "<option value='" . $rcompany[$i]->company_id . "'>" . $rcompany[$i]->company_name . "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-2 col-sm-5">
                            <select id="location" name="location" class="form-control select2" onchange="dtdatechange()">
                                <option value="0">-- All Location</option>
                                <option value="STREET.0">HAULING (jalur kosongan)</option>
                                <option value="STREET.1">HAULING (jalur muatan)</option>
                                <?php $nr = count($rlocation);

                                for ($z = 0; $z < $nr; $z++) {
                                    if ($rlocation[$z]->street_alias != "") {
                                        if ($rlocation[$z]->street_alias != "PORT BBC") {
                                            echo "<option value=\"" . $rlocation[$z]->street_alias . "\">" . $rlocation[$z]->street_alias . " </option>";
                                        }
                                    }
                                } ?>
                            </select>
                        </div>

                        <?php
                        //  echo "<pre>";
                        // var_dump($rcompany);
                        // var_dump($rlocation);
                        // echo "</pre>"; 
                        ?>

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
            var location = $("#location").val();
            var table_loc_name = $("#location").val();

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
                company: company,
                location: location
            };
            $("#totalan").html("");
            $("#content").html("");
            $("#container_graphic").html("");
            $("#exceltitle").html("");
            $("#valueHidden").html("");
            jQuery.post("<?php echo base_url() ?>devtruck/search", data, function(response) {
                console.log(response);
                $("#loader").hide();

                if (response.error) {
                    console.log(response);
                    alert(response.msg);
                } else {
                    if (company == 0) {

                        charttrilevel(response.data_fix, response.data_company, response.data_hour, response.data_location, response.length_company, response.length_hour, response.total_unit_location, response.total_unit, response.total_unit_per_contractor, location, date, company);
                    } else {
                        charttrilevelbycompany(response.data_fix, response.data_company, response.data_hour, response.data_location, response.length_company, response.length_hour, response.total_unit_location, response.total_unit, response.total_unit_per_contractor, location, date, company);

                    }
                    createTable(response, table_loc_name);
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


            // charttrilevel();
        });


        var chart;

        function charttrilevel(data_fix, data_company, data_hour, data_location, length_company, length_hour, total_unit_location, total_unit, total_unit_per_contractor, location, date, company) {
            var data = data_fix;
            var data_company = data_company;
            var data_location = data_location;
            var data_hour = data_hour;
            var length_company = length_company;
            var length_hour = length_hour;
            var total_unit_location = total_unit_location;
            var total_unit = total_unit;
            var total_unit_per_contractor = total_unit_per_contractor;
            var location = location;
            var company_selected = company;
            var categories2 = [];
            var datalvl0 = [];
            var datalvl1 = [];
            var datalvl2 = [];
            var datalevel0 = [];
            var datalevel1 = [];
            var datalevel2 = [];
            var totallevel0 = 0;
            var totallevel1 = 0;
            var totallevel2 = 0;
            var y1 = 0;
            var a = 0;
            var b = 0;
            var c = 0;
            var n = 0;
            var k = 0;
            var dstat = 0;


            var plotlin = -1;
            var maks = null;
            if (location == 0) {
                plotlin = 0.84 * total_unit;
                plotlin = Math.round(plotlin);
                maks = total_unit;
            }


            for (i = 0; i < data_hour.length; i++) {
                //lvl 0
                n = 0;
                totallevel1 = 0;
                var pr = Object.keys(data[data_hour[i]]);
                var html = "";
                for (j = 0; j < pr.length; j++) {

                    // lvl 1
                    dtlvl2 = data[data_hour[i]][pr[j]];
                    y1 = Object.keys(dtlvl2).length;

                    // console.log(dtlvl2);
                    totallevel2 = 0;
                    // categories2 = [];
                    // vehiclelist = [];
                    datalvl2 = [];
                    // alert(location);
                    // return false;
                    // var lvllocation = "";
                    // if (location == 0) {
                    //     lvllocation = "All Location";
                    // } else if (location == "STREET.0") {
                    //     lvllocation = "Hauling Kosongan";
                    // } else if (location == "STREET.1") {
                    //     lvllocation = "Hauling Muatan";
                    // }
                    // categories2[0] = lvllocation;
                    for (k = 0; k < y1; k++) {
                        //lvl 2
                        // c = datalvl2.length;
                        // if (c > 0) {
                        //     b = 1 + datalvl2[0][1];
                        //     datalvl2[0][1] = b;
                        //     b = datalvl2[0][0] + ", " + dtlvl2[k]['vehicle'];
                        //     datalvl2[0][0] = b;
                        // } else {
                        // datalvl2.push([dtlvl2[k]['vehicle'], 1]);
                        datalvl2.push(dtlvl2[k]['vehicle']);
                        // }
                        totallevel2++;
                    }
                    html += "<textarea id=\"" + data_hour[i] + pr[j] + "\">" + datalvl2 + "</textarea>";

                    // datalevel2 = {
                    //     level: 2,
                    //     z: data_hour[i],
                    //     color: "#D32E36",
                    //     name: "Lokasi <b style'display:none'>" + data_hour[i] + "<b>",
                    //     categories: categories2,
                    //     data: datalvl2
                    // };
                    // if (company_selected == 0) {
                    //     nan = total_unit_per_contractor[pr[j]];
                    // } else {
                    //     nan = total_unit;
                    // }

                    datalvl1.push({
                        // name: pr[j] + " " + nan,
                        y: totallevel2
                        // z: data_hour[i],
                        // drilldown: datalevel2

                    });
                    n = totallevel1 + totallevel2;
                    totallevel1 = n;
                }
                for (h = 0; h < pr.length; h++) {
                    pr[h] += "<br>" + total_unit_per_contractor[pr[h]];
                }

                datalvl0.push({
                    y: totallevel1,
                    z: data_hour[i],
                    color: "#D32E36",
                    drilldown: {
                        name: "Contractor <b style'display:none'>" + data_hour[i] + "</b>",
                        color: "#D32E36",
                        categories: pr,
                        level: 1,
                        data: datalvl1
                    }
                });
                datalvl1 = [];
                k = totallevel0 + totallevel1;
                totallevel0 = k;
                html_old = $("#valueHidden").html();
                html_old += html;

                $("#valueHidden").html(html_old);

            }

            var plotLine = {
                id: 'y-axis-plot-line-0',
                color: '#D32E36',
                width: 3,
                value: plotlin,
                dashStyle: 'shortdash',
                label: {
                    text: '85%'
                }
            }


            var colors = Highcharts.getOptions().colors,
                categories = data_hour,
                // categories = ['MSIE', 'Firefox', 'Chrome', 'Safari', 'Opera'],

                name = 'Hour',
                level = 0,
                data = datalvl0;
            chart = new Highcharts.Chart({
                chart: {
                    renderTo: 'container_graphic',
                    type: 'column',
                    events: {
                        drilldown: function() {
                            this.yAxis[0].removePlotLine('y-axis-plot-line-0');
                            var cs = this.series[0];
                            if (cs != undefined) {
                                this.yAxis[0].update({
                                    max: null
                                });
                            }
                        },
                        redraw: function() {
                            var cs = this.series[0];
                            if (cs != undefined) {
                                lvl = this.series[0].options.level;
                                if (lvl == 0) {
                                    this.yAxis[0].addPlotLine(plotLine);
                                }
                            }
                        }

                    }
                },
                title: {
                    useHTML: true,
                    text: ''
                },
                subtitle: {
                    text: 'Truck On Duty ' + date
                },
                accessibility: {
                    announceNewData: {
                        enabled: true
                    }
                },
                xAxis: {
                    categories: categories,
                    title: {
                        text: 'Hour'
                    }
                },
                yAxis: {
                    title: {
                        text: 'Total Unit'
                    },
                    max: maks,
                    plotLines: [plotLine]
                },
                legend: {
                    enabled: false
                },
                plotOptions: {
                    column: {
                        cursor: 'pointer',
                        point: {
                            events: {
                                click: function() {
                                    // var yax = this.series.yAxis
                                    // console.log(yax);
                                    var drilldown = this.drilldown;
                                    if (drilldown) { // drill down

                                        // this.yAxis[0].removePlotLine('y-axis-plot-line-0');

                                        // switch (this.series.options.level) {
                                        //   case 0:
                                        //     var hour = this.series.options.data[0].z;
                                        //     this.series.chart.setTitle({
                                        //       text: "Hour : " + hour
                                        //     });
                                        //     break;
                                        //   case 1:
                                        //     this.series.chart.setTitle({
                                        //       text: "Hour : " + drilldown.z
                                        //     });
                                        //     break;
                                        // }

                                        setChart(drilldown.name, drilldown.categories, drilldown.data, drilldown.color, drilldown.level);
                                    } else { // restore
                                        setChart(name, categories, data, null, level);

                                    }
                                }
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            // color: colors[0],
                            // style: {
                            //   fontWeight: 'bold'
                            // },
                            formatter: function() {
                                // console.log(this);
                                // var key = " ";
                                var persen = 0;
                                var outputlabel = "";
                                switch (this.series.options.level) {
                                    case 0:
                                        persen = (this.y / total_unit) * 100;
                                        persen = Math.round(persen);
                                        if (length_company > 1) {
                                            if (location == 0) {
                                                title = "Total Unit All Contractor : " + total_unit;

                                                outputlabel = this.y + " unit <br>" + persen + "%";
                                            } else {
                                                outputlabel = this.y + " unit";
                                                if (location == "STREET.0") {
                                                    title = "Total Unit di HAULING Jalur Kosongan : " + total_unit_location;
                                                } else if (location == "STREET.1") {
                                                    title = "Total Unit di HAULING Jalur Muatan : " + total_unit_location;
                                                } else {
                                                    title = "Total Unit di " + location + " : " + total_unit_location;
                                                }
                                            }
                                        } else {
                                            if (location == 0) {
                                                title = "Total Unit " + data_company[0] + " : " + total_unit;

                                                outputlabel = this.y + " unit <br>" + persen + "%";
                                            } else {
                                                outputlabel = this.y + " unit";
                                                if (location == "STREET.0") {
                                                    title = "Total Unit di HAULING Jalur Kosongan : " + total_unit_location;
                                                } else if (location == "STREET.1") {
                                                    title = "Total Unit di HAULING Jalur Muatan : " + total_unit_location;
                                                } else {
                                                    title = "Total Unit di " + location + " : " + total_unit_location;
                                                }
                                            }
                                        }



                                        $(".highcharts-xaxis text").html("Hour");
                                        // $(".highcharts-xaxis text").html(this.series.options.name);
                                        this.series.chart.setTitle({
                                            text: title
                                        });
                                        break;
                                    case 1:
                                        var name = this.series.options.name;
                                        h = name.split("e'>");
                                        hour = h[1].split("</");
                                        $(".highcharts-xaxis text").html(name);
                                        this.series.chart.setTitle({
                                            text: "Hour : " + hour[0]
                                        });

                                        // if (length_company > 1) {
                                        // if (location == 0) {
                                        // var exp = this.key;
                                        // var key = exp.split("<br>");
                                        // // console.log(this.series.options);
                                        // if (key.length > 1) {
                                        //     persen = (this.y / total_unit_per_contractor[key[0]]) * 100;
                                        //     outputlabel = this.y + " unit <br>" + Math.round(persen) + "%";
                                        // } else {
                                        //     outputlabel = this.y + " unit";
                                        // }
                                        //     outputlabel = this.y + " unit";
                                        // } else {
                                        // outputlabel = this.y + " unit";

                                        // }
                                        // } else {
                                        outputlabel = this.y + " unit";
                                        // }
                                        break;
                                        // case 2:
                                        //     // key = this.key;
                                        //     $(".highcharts-xaxis text").html(this.series.options.name);

                                        //     var exp = this.series.name;
                                        //     var cont = this.series.options.data[0][0];
                                        //     expr = exp.split("e'>");
                                        //     h = expr[1].split("<");
                                        //     contr = cont.split(" ");
                                        //     this.series.chart.setTitle({
                                        //         text: contr[0] + " Hour : " + h[0]
                                        //     });
                                        //     outputlabel = this.y + " unit";
                                        //     break;
                                }

                                return outputlabel;
                            }
                        }
                    }
                },
                tooltip: {
                    useHTML: true,
                    style: {
                        color: "#000000"
                    },
                    backgroundColor: '#FCFFC5',
                    formatter: function() {
                        var key = this.key;
                        // console.log(key);
                        if (key == undefined) {
                            key = "";
                        }
                        var y = this.y;
                        var point = this.point,
                            s = '';

                        switch (this.series.options.level) {
                            case 0:
                                s = 'Hour: ' + key + ' <br/>';
                                s += y + ' unit';
                                break;

                            case 1:
                                s += y + ' unit<br>';
                                if (key.length > 2) {
                                    // console.log(key);
                                    exp = key.split("<br>");
                                    key = exp[0];
                                    name = this.series.name;
                                    exp = name.split("e'>");
                                    name = exp[1].split("<");

                                    idn = name[0] + key;
                                    s += $("#" + idn).html();
                                }
                                break;

                                // case 2:
                                //     s = y + ' unit <br/>';
                                //     s += key + ' ';
                                //     break;
                        }


                        return s;
                    }
                },
                series: [{
                    name: name,
                    level: level,
                    data: data,
                    color: "#D32E36"
                }]
                // exporting: {
                //   enabled: false
                // }
            });
        }

        function charttrilevelbycompany(data_fix, data_company, data_hour, data_location, length_company, length_hour, total_unit_location, total_unit, total_unit_per_contractor, location, date, company) {
            var data = data_fix;
            var data_company = data_company;
            var data_location = data_location;
            var data_hour = data_hour;
            var length_company = length_company;
            var length_hour = length_hour;
            var total_unit_location = total_unit_location;
            var total_unit = total_unit;
            var total_unit_per_contractor = total_unit_per_contractor;
            var location = location;
            var company_selected = company;
            var categories2 = [];
            var datalvl0 = [];
            var datalvl1 = [];
            var datalvl2 = [];
            var datalevel0 = [];
            var datalevel1 = [];
            var datalevel2 = [];
            var totallevel0 = 0;
            var totallevel1 = 0;
            var totallevel2 = 0;
            var y1 = 0;
            var a = 0;
            var b = 0;
            var c = 0;
            var n = 0;
            var k = 0;
            var dstat = 0;
            var html = "";

            var plotlin = -1;
            var maks = null;
            if (location == 0) {
                plotlin = 0.84 * total_unit;
                plotlin = Math.round(plotlin);
                maks = total_unit;
            }


            for (i = 0; i < data_hour.length; i++) {
                //lvl 0
                n = 0;
                totallevel1 = 0;
                var pr = Object.keys(data[data_hour[i]]);

                for (j = 0; j < pr.length; j++) {

                    // lvl 1
                    dtlvl2 = data[data_hour[i]][pr[j]];
                    y1 = Object.keys(dtlvl2).length;

                    // console.log(dtlvl2);
                    totallevel2 = 0;
                    categories2 = [];
                    datalvl2 = [];
                    // if (location == 0 || location == "STREET.0" || location == "STREET.1") {
                    // alert(location);
                    // return false;
                    var lvllocation = "";
                    if (location == 0) {
                        lvllocation = "All Location";
                    } else if (location == "STREET.0") {
                        lvllocation = "Hauling Kosongan";
                    } else if (location == "STREET.1") {
                        lvllocation = "Hauling Muatan";
                    }
                    categories2[0] = lvllocation;
                    for (k = 0; k < y1; k++) {
                        //lvl 2
                        // c = datalvl2.length;
                        // if (c > 0) {
                        //     b = 1 + datalvl2[0][1];
                        //     datalvl2[0][1] = b;
                        //     b = datalvl2[0][0] + ", " + dtlvl2[k]['vehicle'];
                        //     datalvl2[0][0] = b;
                        // } else {
                        // datalvl2.push([dtlvl2[k]['vehicle'], 1]);
                        datalvl2.push(dtlvl2[k]['vehicle']);
                        // }
                        totallevel2++;
                    }
                    // } else {
                    //     for (k = 0; k < y1; k++) {
                    //         //lvl 2
                    //         c = categories2.length;
                    //         if (c > 0) {
                    //             dstat = 0;
                    //             for (a = 0; a < c; a++) {
                    //                 if (categories2[a] == dtlvl2[k]['location']) {

                    //                     b = 1 + datalvl2[a][1];
                    //                     datalvl2[a][1] = b;
                    //                     b = datalvl2[a][0] + ", " + dtlvl2[k]['vehicle'];
                    //                     datalvl2[a][0] = b;
                    //                     dstat = 1;
                    //                 }
                    //             }
                    //             if (dstat == 0) {
                    //                 categories2.push(dtlvl2[k]['location']);
                    //                 datalvl2.push([dtlvl2[k]['vehicle'], 1]);
                    //             }
                    //         } else {
                    //             categories2.push(dtlvl2[k]['location']);
                    //             datalvl2.push([dtlvl2[k]['vehicle'], 1]);
                    //         }
                    //         totallevel2++;
                    //     }
                    // }
                    datalevel2 = {
                        level: 2,
                        z: data_hour[i],
                        color: "#D32E36",
                        name: "Lokasi <b style'display:none'>" + data_hour[i] + "<b>",
                        categories: categories2,
                        data: datalvl2
                    };
                    // if (company_selected == 0) {
                    //     nan = total_unit_per_contractor[pr[j]];
                    // } else {
                    //     nan = total_unit;
                    // }

                    datalvl1.push({
                        // name: pr[j] + " " + nan,
                        y: totallevel2,
                        z: data_hour[i],
                        // drilldown: datalevel2

                    });
                    n = totallevel1 + totallevel2;
                    totallevel1 = n;

                }
                html = "<textarea id=\"" + data_hour[i] + "\">" + datalvl2 + "</textarea>";
                for (h = 0; h < pr.length; h++) {
                    pr[h] += "<br>" + total_unit;
                }

                datalvl0.push({
                    y: totallevel1,
                    // z: data_hour[i],
                    color: "#D32E36",
                    // drilldown: datalevel2
                    // drilldown: {
                    //     name: "Contractor <b style'display:none'>" + data_hour[i] + "</b>",
                    //     color: "#D32E36",
                    //     categories: pr,
                    //     level: 1,
                    //     data: datalvl1
                    // }
                });
                datalvl1 = [];
                k = totallevel0 + totallevel1;
                totallevel0 = k;
                html_old = $("#valueHidden").html();
                html_old += html;
                $("#valueHidden").html(html_old);

            }


            var plotLine = {
                id: 'y-axis-plot-line-0',
                color: '#D32E36',
                width: 3,
                value: plotlin,
                dashStyle: 'shortdash',
                label: {
                    text: '85%'
                }
            }


            var colors = Highcharts.getOptions().colors,
                categories = data_hour,
                // categories = ['MSIE', 'Firefox', 'Chrome', 'Safari', 'Opera'],

                name = 'Hour',
                level = 0,
                data = datalvl0;
            chart = new Highcharts.Chart({
                chart: {
                    renderTo: 'container_graphic',
                    type: 'column',
                    events: {
                        drilldown: function() {
                            this.yAxis[0].removePlotLine('y-axis-plot-line-0');
                            var cs = this.series[0];
                            if (cs != undefined) {
                                this.yAxis[0].update({
                                    max: null
                                });
                            }
                        },
                        redraw: function() {
                            var cs = this.series[0];
                            if (cs != undefined) {
                                lvl = this.series[0].options.level;
                                if (lvl == 0) {
                                    this.yAxis[0].addPlotLine(plotLine);
                                }
                            }
                        }

                    }
                },
                title: {
                    useHTML: true,
                    text: ''
                },
                subtitle: {
                    text: 'Truck On Duty ' + date
                },
                accessibility: {
                    announceNewData: {
                        enabled: true
                    }
                },
                xAxis: {
                    categories: categories,
                    title: {
                        text: 'Hour'
                    }
                },
                yAxis: {
                    title: {
                        text: 'Total Unit'
                    },
                    max: maks,
                    plotLines: [plotLine]
                },
                legend: {
                    enabled: false
                },
                plotOptions: {
                    column: {
                        cursor: 'pointer',
                        point: {
                            events: {
                                click: function() {
                                    // var yax = this.series.yAxis
                                    // console.log(yax);
                                    var drilldown = this.drilldown;
                                    if (drilldown) { // drill down

                                        // this.yAxis[0].removePlotLine('y-axis-plot-line-0');

                                        // switch (this.series.options.level) {
                                        //   case 0:
                                        //     var hour = this.series.options.data[0].z;
                                        //     this.series.chart.setTitle({
                                        //       text: "Hour : " + hour
                                        //     });
                                        //     break;
                                        //   case 1:
                                        //     this.series.chart.setTitle({
                                        //       text: "Hour : " + drilldown.z
                                        //     });
                                        //     break;
                                        // }

                                        setChart(drilldown.name, drilldown.categories, drilldown.data, drilldown.color, drilldown.level);
                                    } else { // restore
                                        setChart(name, categories, data, null, level);

                                    }
                                }
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            // color: colors[0],
                            // style: {
                            //   fontWeight: 'bold'
                            // },
                            formatter: function() {
                                // console.log(this);
                                // var key = " ";
                                var persen = 0;
                                var outputlabel = "";
                                switch (this.series.options.level) {
                                    case 0:
                                        persen = (this.y / total_unit) * 100;
                                        persen = Math.round(persen);
                                        if (length_company > 1) {
                                            if (location == 0) {
                                                title = "Total Unit All Contractor : " + total_unit;

                                                outputlabel = this.y + " unit <br>" + persen + "%";
                                            } else {
                                                outputlabel = this.y + " unit";
                                                if (location == "STREET.0") {
                                                    title = "Total Unit di HAULING Jalur Kosongan : " + total_unit_location;
                                                } else if (location == "STREET.1") {
                                                    title = "Total Unit di HAULING Jalur Muatan : " + total_unit_location;
                                                } else {
                                                    title = "Total Unit di " + location + " : " + total_unit_location;
                                                }
                                            }
                                        } else {
                                            if (location == 0) {
                                                title = "Total Unit " + data_company[0] + " : " + total_unit;

                                                outputlabel = this.y + " unit <br>" + persen + "%";
                                            } else {
                                                outputlabel = this.y + " unit";
                                                if (location == "STREET.0") {
                                                    title = "Total Unit di HAULING Jalur Kosongan : " + total_unit_location;
                                                } else if (location == "STREET.1") {
                                                    title = "Total Unit di HAULING Jalur Muatan : " + total_unit_location;
                                                } else {
                                                    title = "Total Unit di " + location + " : " + total_unit_location;
                                                }
                                            }
                                        }



                                        $(".highcharts-xaxis text").html(this.series.options.name);
                                        this.series.chart.setTitle({
                                            text: title
                                        });
                                        break;
                                    case 1:
                                        var name = this.series.options.name;
                                        console.log(name);
                                        h = name.split("e'>");
                                        hour = h[1].split("</");
                                        $(".highcharts-xaxis text").html(name);
                                        this.series.chart.setTitle({
                                            text: "Hour : " + hour[0]
                                        });

                                        // if (length_company > 1) {
                                        // if (location == 0) {
                                        // var exp = this.key;
                                        // var key = exp.split("<br>");
                                        // // console.log(this.series.options);
                                        // if (key.length > 1) {
                                        //     persen = (this.y / total_unit_per_contractor[key[0]]) * 100;
                                        //     outputlabel = this.y + " unit <br>" + Math.round(persen) + "%";
                                        // } else {
                                        //     outputlabel = this.y + " unit";
                                        // }
                                        //     outputlabel = this.y + " unit";
                                        // } else {
                                        // outputlabel = this.y + " unit";

                                        // }
                                        // } else {
                                        outputlabel = this.y + " unit";
                                        // }
                                        break;
                                    case 2:
                                        // key = this.key;
                                        $(".highcharts-xaxis text").html(this.series.options.name);

                                        var exp = this.series.name;
                                        var cont = this.series.options.data[0][0];
                                        expr = exp.split("e'>");
                                        h = expr[1].split("<");
                                        contr = cont.split(" ");
                                        this.series.chart.setTitle({
                                            text: contr[0] + " Hour : " + h[0]
                                        });
                                        outputlabel = this.y + " unit";
                                        break;
                                }

                                return outputlabel;
                            }
                        }
                    }
                },
                tooltip: {
                    useHTML: true,
                    style: {
                        color: "#000000"
                    },
                    backgroundColor: '#FCFFC5',
                    formatter: function() {
                        var key = this.key;
                        // console.log(key);
                        if (key == undefined) {
                            key = "";
                        }
                        var y = this.y;
                        var point = this.point,
                            s = '';

                        switch (this.series.options.level) {
                            case 0:
                                s = 'Hour: ' + key + ' <br/>';
                                s += y + ' unit <br>';
                                s += $("#" + key).html();
                                break;

                            case 1:
                                // if (key != "") {
                                //     exp = key.split("<br>");
                                //     if (exp.length > 1) {

                                //         key = exp[0];
                                //     }
                                // }
                                // s = key + ' <br/>';
                                s += y + ' unit';
                                break;

                            case 2:
                                s = y + ' unit <br/>';
                                s += key + ' ';
                                break;
                        }


                        return s;
                    }
                },
                series: [{
                    name: name,
                    level: level,
                    data: data,
                    color: "#D32E36"
                }]
                // exporting: {
                //   enabled: false
                // }
            });
        }

        function setChart(name, categories, data, color, level) {
            chart.xAxis[0].setCategories(categories);
            chart.series[0].remove();


            chart.addSeries({
                name: name,
                data: data,
                level: level,
                color: "#D32E36"
            });


        }

        function createTable(response, table_loc_name) {
            var length_company = response.length_company;
            var length_hour = response.length_hour;
            var data_company = response.data_company;
            var data_hour = response.data_hour;
            var total_unit = response.total_unit;
            var total_unit_per_contractor = response.total_unit_per_contractor;
            var data = response.data_fix;
            var totalin = 0;
            var totalan = 0;
            var cek = "";
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
            var html = "";
            var locname = table_loc_name;
            if (locname == "STREET.0") {
                table_loc_name = " HAULING Jalur Kosongan";
            } else if (locname == "STREET.1") {
                table_loc_name = " HAULING Jalur Muatan";
            } else if (locname == 0) {
                table_loc_name = " Semua Lokasi";
            } else {
                table_loc_name = " " + locname;
            }
            html += `<thead><tr>
                      <th style="text-align:center;">Hour</th>`;
            if (length_company == 1) {
                html += '<th style="text-align:center;">' + data_company[0] + '<br><span>' + total_unit + '</span></th>';
            } else {
                for (var i = 0; i < length_company; i++) {
                    html += '<th style="text-align:center;">' + data_company[i] + '<br><span>' + total_unit_per_contractor[data_company[i]] + '</span></th>';
                }
            }
            html += `   <th style="text-align:center;">Total</th>
                  </tr></thead><tbody>`;
            for (var j = 0; j < length_hour; j++) {
                n = 0;
                total = 0;
                html += `<tr><th style="text-align:center;">` + data_hour[j] + `</th>`;

                for (var k = 0; k < length_company; k++) {
                    cek = data[data_hour[j]][data_company[k]];
                    if (cek == undefined) {

                        html += `<td style="text-align:center;"></td>`;
                    } else {
                        pr = Object.keys(data[data_hour[j]][data_company[k]]).length;
                        if (length_company == 1) {
                            persen = (pr / total_unit) * 100;
                        } else {
                            persen = (pr / total_unit_per_contractor[data_company[k]]) * 100;

                        }
                        persen = Math.round(persen);
                        html += `<td style="text-align:center;">` + pr + ` <span style="color:red">(` + persen + `%)</span> </td>`;
                        x = n + pr;
                        n = x;
                    }
                }
                totalin = totalan + n;
                totalan = totalin;
                html += `<th style="text-align:center;">` + n + `</th></tr>`;
            }
            totalavg = totalan / length_hour;
            totalavg = Math.round(totalavg);
            html += `</tbody>`;
            $("#content").html(html);
            $("#totalan").html(`Total Unit Rata-rata ` + shiftname + ` : <b>` + totalavg + ` &nbsp; </b>`);
            $("#exceltitle").html("Data DT Operasional tanggal " + date + " " + table_loc_name);
            return false;
        }
    </script>