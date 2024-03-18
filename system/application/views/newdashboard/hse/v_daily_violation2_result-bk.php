<script src="<?php echo base_url(); ?>assets/dashboard/assets/plugins/jquery/jquery.min.js"></script>

<div class="row">

    <div class="col-lg-7 col-md-7 col-sm-12">
        <figure class="highcharts-figure">
            <!-- <div id="container-data"></div> -->
            <div id="all_all_all"></div>
        </figure>
    </div>


    <div class="col-lg-5 col-md-5 col-sm-12">
        <figure class="highcharts-figure" style="background-color: white; padding:2px;">
            <div id="loader2" style="display: none;" class="mdl-progress mdl-js-progress mdl-progress__indeterminate is-upgraded" data-upgraded=",MaterialProgress">
                <div class="progressbar bar bar1" style="width: 0%;"></div>
                <div class="bufferbar bar bar2" style="width: 100%;"></div>
                <div class="auxbar bar bar3" style="width: 0%;"></div>
            </div>
            <div id="container-summary">
                <div id="viewtable" style="height: 95%;">
                    <div id="isexport_xcel">
                        <div style="margin: 3px; padding:3px;">
                            <b id="exceltitle" style="font-size: 12px;">Daily Violation Report (<?= $date; ?>)</b>
                            <button type="button" name="button" id="export_xcel" class="btn btn-primary btn-xs" style="font-size: 11px;">Export Excel</button>
                        </div>
                        <style>
                            table {
                                width: 100%;
                            }

                            .tablecont {
                                font-family: 'Trebuchet MS';
                                border-collapse: collapse;
                                color: #333333;
                                font-size: 10px;
                            }

                            .tablecont thead {
                                background-color: #dddddd;
                            }

                            .tablecont td,
                            th {
                                border-bottom: 1px solid #dfdadd;
                                text-align: left;
                                padding: 6px 4px 6px 4px;
                            }

                            .tablecont tr:nth-child(even) {
                                background-color: #dddddd;
                            }
                        </style>
                        <!-- <table class="table table-striped table-bordered" id="tablecontent" style="font-size: 12px;"> -->
                        <table id="tablecontent" class="tablecont">

                        </table>
                    </div>
                </div>
            </div>
        </figure>
    </div>

    <div class="col-lg-12 col-md-12 col-sm-12">
        <figure id="export_top_ten" class="highcharts-figure" style="background-color: white; padding:2px;">
            <p style="text-align: center;"><b>Top 10 Violation</b><br><?= $date; ?>
                <button type="button" name="button" id="export_xcel_top_ten" class="btn btn-primary btn-xs" style="font-size: 11px;">Export Excel</button>
            </p>
            <table class="table table-striped table-bordered table-hover table-sm" style="font-size: 12px; width: 100%; text-align:center;">
                <thead class="thead-light">
                    <tr>
                        <th>Rank</th>
                        <?php
                        $rank = array();
                        $rank_seleksi = array();

                        for ($i = 0; $i < count($master_violation); $i++) {
                            echo '<th>' . $master_violation[$i] . '</th>';
                            for ($j = 0; $j < 10; $j++) {
                                $rank[$master_violation[$i]][$j] = "";
                                $rank_seleksi[$master_violation[$i]][$j] = 0;
                            }
                        } ?>
                    </tr>
                </thead>
                <?php
                $arrkey = array_keys($top_violation);
                for ($j = 0; $j < count($arrkey); $j++) {
                    $keys = array_keys($top_violation[$arrkey[$j]]);
                    for ($k = 0; $k < count($keys); $k++) {
                        $val = $top_violation[$arrkey[$j]][$keys[$k]];
                        if ($val > $rank_seleksi[$arrkey[$j]][0]) {
                            $rank[$arrkey[$j]][9] = $rank[$arrkey[$j]][8];
                            $rank[$arrkey[$j]][8] = $rank[$arrkey[$j]][7];
                            $rank[$arrkey[$j]][7] = $rank[$arrkey[$j]][6];
                            $rank[$arrkey[$j]][6] = $rank[$arrkey[$j]][5];
                            $rank[$arrkey[$j]][5] = $rank[$arrkey[$j]][4];
                            $rank[$arrkey[$j]][4] = $rank[$arrkey[$j]][3];
                            $rank[$arrkey[$j]][3] = $rank[$arrkey[$j]][2];
                            $rank[$arrkey[$j]][2] = $rank[$arrkey[$j]][1];
                            $rank[$arrkey[$j]][1] = $rank[$arrkey[$j]][0];
                            $rank[$arrkey[$j]][0] = $keys[$k] . " (" . $val . ")";

                            $rank_seleksi[$arrkey[$j]][9] = $rank_seleksi[$arrkey[$j]][8];
                            $rank_seleksi[$arrkey[$j]][8] = $rank_seleksi[$arrkey[$j]][7];
                            $rank_seleksi[$arrkey[$j]][7] = $rank_seleksi[$arrkey[$j]][6];
                            $rank_seleksi[$arrkey[$j]][6] = $rank_seleksi[$arrkey[$j]][5];
                            $rank_seleksi[$arrkey[$j]][5] = $rank_seleksi[$arrkey[$j]][4];
                            $rank_seleksi[$arrkey[$j]][4] = $rank_seleksi[$arrkey[$j]][3];
                            $rank_seleksi[$arrkey[$j]][3] = $rank_seleksi[$arrkey[$j]][2];
                            $rank_seleksi[$arrkey[$j]][2] = $rank_seleksi[$arrkey[$j]][1];
                            $rank_seleksi[$arrkey[$j]][1] = $rank_seleksi[$arrkey[$j]][0];
                            $rank_seleksi[$arrkey[$j]][0] = $val;
                        } else {
                            if ($val > $rank_seleksi[$arrkey[$j]][1]) {
                                $rank[$arrkey[$j]][9] = $rank[$arrkey[$j]][8];
                                $rank[$arrkey[$j]][8] = $rank[$arrkey[$j]][7];
                                $rank[$arrkey[$j]][7] = $rank[$arrkey[$j]][6];
                                $rank[$arrkey[$j]][6] = $rank[$arrkey[$j]][5];
                                $rank[$arrkey[$j]][5] = $rank[$arrkey[$j]][4];
                                $rank[$arrkey[$j]][4] = $rank[$arrkey[$j]][3];
                                $rank[$arrkey[$j]][3] = $rank[$arrkey[$j]][2];
                                $rank[$arrkey[$j]][2] = $rank[$arrkey[$j]][1];
                                $rank[$arrkey[$j]][1] = $keys[$k] . " (" . $val . ")";

                                $rank_seleksi[$arrkey[$j]][9] = $rank_seleksi[$arrkey[$j]][8];
                                $rank_seleksi[$arrkey[$j]][8] = $rank_seleksi[$arrkey[$j]][7];
                                $rank_seleksi[$arrkey[$j]][7] = $rank_seleksi[$arrkey[$j]][6];
                                $rank_seleksi[$arrkey[$j]][6] = $rank_seleksi[$arrkey[$j]][5];
                                $rank_seleksi[$arrkey[$j]][5] = $rank_seleksi[$arrkey[$j]][4];
                                $rank_seleksi[$arrkey[$j]][4] = $rank_seleksi[$arrkey[$j]][3];
                                $rank_seleksi[$arrkey[$j]][3] = $rank_seleksi[$arrkey[$j]][2];
                                $rank_seleksi[$arrkey[$j]][2] = $rank_seleksi[$arrkey[$j]][1];
                                $rank_seleksi[$arrkey[$j]][1] = $val;
                            } else {
                                if ($val > $rank_seleksi[$arrkey[$j]][2]) {
                                    $rank[$arrkey[$j]][9] = $rank[$arrkey[$j]][8];
                                    $rank[$arrkey[$j]][8] = $rank[$arrkey[$j]][7];
                                    $rank[$arrkey[$j]][7] = $rank[$arrkey[$j]][6];
                                    $rank[$arrkey[$j]][6] = $rank[$arrkey[$j]][5];
                                    $rank[$arrkey[$j]][5] = $rank[$arrkey[$j]][4];
                                    $rank[$arrkey[$j]][4] = $rank[$arrkey[$j]][3];
                                    $rank[$arrkey[$j]][3] = $rank[$arrkey[$j]][2];
                                    $rank[$arrkey[$j]][2] = $keys[$k] . " (" . $val . ")";

                                    $rank_seleksi[$arrkey[$j]][9] = $rank_seleksi[$arrkey[$j]][8];
                                    $rank_seleksi[$arrkey[$j]][8] = $rank_seleksi[$arrkey[$j]][7];
                                    $rank_seleksi[$arrkey[$j]][7] = $rank_seleksi[$arrkey[$j]][6];
                                    $rank_seleksi[$arrkey[$j]][6] = $rank_seleksi[$arrkey[$j]][5];
                                    $rank_seleksi[$arrkey[$j]][5] = $rank_seleksi[$arrkey[$j]][4];
                                    $rank_seleksi[$arrkey[$j]][4] = $rank_seleksi[$arrkey[$j]][3];
                                    $rank_seleksi[$arrkey[$j]][3] = $rank_seleksi[$arrkey[$j]][2];
                                    $rank_seleksi[$arrkey[$j]][2] = $val;
                                } else {
                                    if ($val > $rank_seleksi[$arrkey[$j]][3]) {
                                        $rank[$arrkey[$j]][9] = $rank[$arrkey[$j]][8];
                                        $rank[$arrkey[$j]][8] = $rank[$arrkey[$j]][7];
                                        $rank[$arrkey[$j]][7] = $rank[$arrkey[$j]][6];
                                        $rank[$arrkey[$j]][6] = $rank[$arrkey[$j]][5];
                                        $rank[$arrkey[$j]][5] = $rank[$arrkey[$j]][4];
                                        $rank[$arrkey[$j]][4] = $rank[$arrkey[$j]][3];
                                        $rank[$arrkey[$j]][3] = $keys[$k] . " (" . $val . ")";

                                        $rank_seleksi[$arrkey[$j]][9] = $rank_seleksi[$arrkey[$j]][8];
                                        $rank_seleksi[$arrkey[$j]][8] = $rank_seleksi[$arrkey[$j]][7];
                                        $rank_seleksi[$arrkey[$j]][7] = $rank_seleksi[$arrkey[$j]][6];
                                        $rank_seleksi[$arrkey[$j]][6] = $rank_seleksi[$arrkey[$j]][5];
                                        $rank_seleksi[$arrkey[$j]][5] = $rank_seleksi[$arrkey[$j]][4];
                                        $rank_seleksi[$arrkey[$j]][4] = $rank_seleksi[$arrkey[$j]][3];
                                        $rank_seleksi[$arrkey[$j]][3] = $val;
                                    } else {
                                        if ($val > $rank_seleksi[$arrkey[$j]][4]) {
                                            $rank[$arrkey[$j]][9] = $rank[$arrkey[$j]][8];
                                            $rank[$arrkey[$j]][8] = $rank[$arrkey[$j]][7];
                                            $rank[$arrkey[$j]][7] = $rank[$arrkey[$j]][6];
                                            $rank[$arrkey[$j]][6] = $rank[$arrkey[$j]][5];
                                            $rank[$arrkey[$j]][5] = $rank[$arrkey[$j]][4];
                                            $rank[$arrkey[$j]][4] = $keys[$k] . " (" . $val . ")";

                                            $rank_seleksi[$arrkey[$j]][9] = $rank_seleksi[$arrkey[$j]][8];
                                            $rank_seleksi[$arrkey[$j]][8] = $rank_seleksi[$arrkey[$j]][7];
                                            $rank_seleksi[$arrkey[$j]][7] = $rank_seleksi[$arrkey[$j]][6];
                                            $rank_seleksi[$arrkey[$j]][6] = $rank_seleksi[$arrkey[$j]][5];
                                            $rank_seleksi[$arrkey[$j]][5] = $rank_seleksi[$arrkey[$j]][4];
                                            $rank_seleksi[$arrkey[$j]][4] = $val;
                                        } else {
                                            if ($val > $rank_seleksi[$arrkey[$j]][5]) {
                                                $rank[$arrkey[$j]][9] = $rank[$arrkey[$j]][8];
                                                $rank[$arrkey[$j]][8] = $rank[$arrkey[$j]][7];
                                                $rank[$arrkey[$j]][7] = $rank[$arrkey[$j]][6];
                                                $rank[$arrkey[$j]][6] = $rank[$arrkey[$j]][5];
                                                $rank[$arrkey[$j]][5] = $keys[$k] . " (" . $val . ")";

                                                $rank_seleksi[$arrkey[$j]][9] = $rank_seleksi[$arrkey[$j]][8];
                                                $rank_seleksi[$arrkey[$j]][8] = $rank_seleksi[$arrkey[$j]][7];
                                                $rank_seleksi[$arrkey[$j]][7] = $rank_seleksi[$arrkey[$j]][6];
                                                $rank_seleksi[$arrkey[$j]][6] = $rank_seleksi[$arrkey[$j]][5];
                                                $rank_seleksi[$arrkey[$j]][5] = $val;
                                            } else {
                                                if ($val > $rank_seleksi[$arrkey[$j]][6]) {
                                                    $rank[$arrkey[$j]][9] = $rank[$arrkey[$j]][8];
                                                    $rank[$arrkey[$j]][8] = $rank[$arrkey[$j]][7];
                                                    $rank[$arrkey[$j]][7] = $rank[$arrkey[$j]][6];
                                                    $rank[$arrkey[$j]][6] = $keys[$k] . " (" . $val . ")";

                                                    $rank_seleksi[$arrkey[$j]][9] = $rank_seleksi[$arrkey[$j]][8];
                                                    $rank_seleksi[$arrkey[$j]][8] = $rank_seleksi[$arrkey[$j]][7];
                                                    $rank_seleksi[$arrkey[$j]][7] = $rank_seleksi[$arrkey[$j]][6];
                                                    $rank_seleksi[$arrkey[$j]][6] = $val;
                                                } else {
                                                    if ($val > $rank_seleksi[$arrkey[$j]][7]) {
                                                        $rank[$arrkey[$j]][9] = $rank[$arrkey[$j]][8];
                                                        $rank[$arrkey[$j]][8] = $rank[$arrkey[$j]][7];
                                                        $rank[$arrkey[$j]][7] = $keys[$k] . " (" . $val . ")";

                                                        $rank_seleksi[$arrkey[$j]][9] = $rank_seleksi[$arrkey[$j]][8];
                                                        $rank_seleksi[$arrkey[$j]][8] = $rank_seleksi[$arrkey[$j]][7];
                                                        $rank_seleksi[$arrkey[$j]][7] = $val;
                                                    } else {
                                                        if ($val > $rank_seleksi[$arrkey[$j]][8]) {
                                                            $rank[$arrkey[$j]][9] = $rank[$arrkey[$j]][8];
                                                            $rank[$arrkey[$j]][8] = $keys[$k] . " (" . $val . ")";

                                                            $rank_seleksi[$arrkey[$j]][9] = $rank_seleksi[$arrkey[$j]][8];
                                                            $rank_seleksi[$arrkey[$j]][8] = $val;
                                                        } else {
                                                            if ($val > $rank_seleksi[$arrkey[$j]][9]) {
                                                                $rank[$arrkey[$j]][9] = $keys[$k] . " (" . $val . ")";

                                                                $rank_seleksi[$arrkey[$j]][9] = $val;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                // var_dump($rank);

                ?>
                <tbody>
                    <?php
                    for ($j = 0; $j < 10; $j++) {
                        $no = $j + 1;
                        echo '<tr><th>' . $no . '</th>';
                        for ($i = 0; $i < count($master_violation); $i++) {
                            echo '<th>' . $rank[$master_violation[$i]][$j] . '</th>';
                        }
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </figure>
    </div>
</div>
<div class="row">
    <br>
    <br>
</div>


<script>
    $(document).ready(function() {

        collect_data();
        jQuery("#export_xcel").click(function() {
            var myBlob = new Blob([jQuery('#isexport_xcel').html()], {
                type: 'application/vnd.ms-excel'
            });
            var url = window.URL.createObjectURL(myBlob);
            var a = document.createElement("a");
            document.body.appendChild(a);
            a.href = url;
            a.download = "daily_violation_report_<?= $date; ?>.xls";
            a.click();
            //adding some delay in removing the dynamically created link solved the problem in FireFox
            setTimeout(function() {
                window.URL.revokeObjectURL(url);
            }, 0);

        });

        jQuery("#export_xcel_top_ten").click(function() {
            var myBlob = new Blob([jQuery('#export_top_ten').html()], {
                type: 'application/vnd.ms-excel'
            });
            var url = window.URL.createObjectURL(myBlob);
            var a = document.createElement("a");
            document.body.appendChild(a);
            a.href = url;
            a.download = "top_ten_violation_<?= $date; ?>.xls";
            a.click();
            //adding some delay in removing the dynamically created link solved the problem in FireFox
            setTimeout(function() {
                window.URL.revokeObjectURL(url);
            }, 0);

        });

    });

    var chart;

    function collect_data() {
        var data_hour = '<?php echo json_encode($data_hour); ?>';
        var data_hour_fix = JSON.parse(data_hour);
        var data_company = '<?php echo json_encode($data_company); ?>';
        var data_company_fix = JSON.parse(data_company);
        var opposite_company = '<?php echo json_encode($opposite_company); ?>';
        var opposite_company_fix = JSON.parse(opposite_company);
        var data_violation = '<?php echo json_encode($data_alarm); ?>';
        var data_violation_fix = JSON.parse(data_violation);
        var total_data = <?= $total_data; ?>;
        var date = '<?= $date; ?>';
        var data = '<?php echo json_encode($data_fix); ?>';
        var data_fix = JSON.parse(data);

        var data_table = '<?php echo json_encode($data_table); ?>';
        var data_table_fix = JSON.parse(data_table);
        var master_company = '<?php echo json_encode($master_company); ?>';
        var master_company_fix = JSON.parse(master_company);
        var master_v = '<?php echo json_encode($master_vehicle); ?>';
        var master_vehicle = JSON.parse(master_v);
        var master_violation = '<?php echo json_encode($master_violation); ?>';
        var master_violation_fix = JSON.parse(master_violation);
        var master_v = '<?php echo json_encode($total_violation_units); ?>';
        var total_violation_units = JSON.parse(master_v);
        var master_v = '<?php echo json_encode($top_violation); ?>';
        var top_violation = JSON.parse(master_v);


        <?php if ($input['company'] == "all") {
            if ($input['violation'] == "all") { ?>
                setTimeout(function() {
                    charttrilevel(data_fix, data_hour_fix, data_company_fix, data_violation_fix, total_data, date);
                }, 0);
            <?php } else if ($input['violation'] == "6") { ?>
                setTimeout(function() {
                    charttrileveloverspeed(data_fix, data_hour_fix, data_company_fix, data_violation_fix, total_data, date);
                }, 0);

            <?php
            } else { ?>
                setTimeout(function() {
                    charttwolevel(data_fix, data_hour_fix, data_company_fix, data_violation_fix, total_data, date);
                }, 0);
            <?php
            }
        } else {
            if ($input['violation'] == "all") { ?>
                setTimeout(function() {
                    charttwolevel(data_fix, data_hour_fix, data_company_fix, data_violation_fix, total_data, date);
                }, 0);
            <?php } else { ?>
                setTimeout(function() {
                    chartonelevel(data_fix, data_hour_fix, data_company_fix, data_violation_fix, total_data, date);
                }, 0);
        <?php
            }
        } ?>
        setTimeout(function() {
            createTable(data_table_fix, data_hour_fix, data_company_fix, data_violation_fix, total_data, date, master_company_fix, master_violation_fix, opposite_company_fix, master_vehicle, total_violation_units);
        }, 0);


    }


    function charttrilevel(data_fix, data_hour_fix, data_company_fix, data_violation_fix, total_data, date) {

        var data = data_fix;
        var data_hour = data_hour_fix;
        var data_company = data_company_fix;
        var data_violation = data_violation_fix;
        var date = date;
        var total_data = total_data;
        // console.log(data);
        datalvl0 = [];
        totallevel0 = 0;
        for (i = 0; i < data_hour.length; i++) {
            //lvl 0
            n = 0;
            totallevel1 = 0;
            datalvl1 = [];
            html = '';
            var pr = Object.keys(data[data_hour[i]]);
            for (j = 0; j < pr.length; j++) {
                // lvl 1
                dtlvl2 = data[data_hour[i]][pr[j]];
                y1 = Object.keys(dtlvl2).length;
                totallevel2 = 0;
                categories2 = [];
                datalvl2 = [];
                info = [];
                for (k = 0; k < y1; k++) {
                    //lvl 2
                    c = categories2.length;
                    vlat = dtlvl2[k]['violation'];
                    if (c > 0) {
                        dstat = 0;
                        for (a = 0; a < c; a++) {
                            if (categories2[a] == vlat) {

                                b = 1 + datalvl2[a][1];
                                datalvl2[a][1] = b;
                                dstat = 1;
                            }
                        }
                        if (dstat == 0) {
                            categories2.push(vlat);
                            datalvl2.push([vlat, 1]);
                        }
                    } else {
                        categories2.push(vlat);
                        datalvl2.push([vlat, 1]);
                    }
                    totallevel2++;
                }
                datalevel2 = {
                    level: 2,
                    name: "Violation/" + dtlvl2[0]['hour'] + "/" + dtlvl2[0]['company'] + "/" + totallevel2,
                    categories: categories2,
                    data: datalvl2
                };
                datalvl1.push({
                    y: totallevel2,
                    z: data_hour[i],
                    drilldown: datalevel2
                });
                n = totallevel1 + totallevel2;
                totallevel1 = n;
            }
            datalvl0.push({
                y: totallevel1,
                drilldown: {
                    name: "Contractor",
                    categories: pr,
                    level: 1,
                    data: datalvl1
                }
            });
            datalvl1 = [];
            k = totallevel0 + totallevel1;
            totallevel0 = k;
        }
        var total_title_lvl1 = 0;

        var colors = Highcharts.getOptions().colors,

            categories = data_hour,

            name = 'Violation',
            level = 0,
            data = datalvl0;
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'all_all_all',
                type: 'column'
            },
            title: {
                useHTML: true,
                text: 'This Title'
            },
            subtitle: {
                text: 'Daily Violation Report<br>' + date
            },
            accessibility: {
                announceNewData: {
                    enabled: true
                }
            },
            xAxis: {
                categories: categories,
                title: {
                    text: 'Violation'
                }
            },
            yAxis: {
                title: {
                    text: 'Total Violation'
                }
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
                                var drilldown = this.drilldown;
                                if (drilldown) {
                                    setChart(drilldown.name, drilldown.categories, drilldown.data, drilldown.level);
                                } else { // restore
                                    setChart(name, categories, data, level);

                                }
                            }
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function() {
                            switch (this.series.options.level) {
                                case 0:
                                    outputlabel = this.y;
                                    $(".highcharts-xaxis text").html("Hour");

                                    this.series.chart.setTitle({
                                        text: "Total Violation : " + total_data
                                    }, {
                                        text: "Daily Violation Report<br>" + date
                                    });
                                    total_title_lvl1 = 0;
                                    break;
                                case 1:
                                    outputlabel = this.y;
                                    hr = this.point.z;
                                    total_title_lvl1 += outputlabel;
                                    var name = this.series.options.name;

                                    $(".highcharts-xaxis text").html(name);

                                    this.series.chart.setTitle({
                                        text: "Hour " + hr + " : " + total_title_lvl1
                                    }, {
                                        text: "Daily Violation Report<br>" + date
                                    });

                                    break;
                                case 2:
                                    exp = this.series.name.split("/");
                                    this.series.chart.setTitle({
                                        text: ""
                                    }, {
                                        text: "<b>Hour " + exp[1] + " > " + exp[2] + " > " + exp[3] + "</b><br>Daily Violation Report<br>" + date
                                    });
                                    return this.y;
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
                    if (key == undefined) {
                        key = "";
                    }
                    var y = this.y;
                    var s = '';
                    switch (this.series.options.level) {
                        case 0:
                            s = 'Hour ' + key + ': ';
                            s += y + ' pelanggaran';
                            break;
                        case 1:
                            s = key + ' <br/>';
                            s += y + ' pelanggaran';
                            break;
                        case 2:
                            s += '<b>' + y + ' ' + this.x + '</b><br/>';
                            break;
                    }
                    return s;
                }
            },
            series: [{
                name: name,
                level: level,
                data: data,
                color: "#4bb036"
            }],
            drilldown: {
                activeAxisLabelStyle: {
                    textDecoration: 'none',
                    color: '#000000'
                },
                activeDataLabelStyle: {
                    color: "#000000",
                    textDecoration: "none"
                }
            }
        });
    }

    function charttrileveloverspeed(data_fix, data_hour_fix, data_company_fix, data_violation_fix, total_data, date) {

        var data = data_fix;
        var data_hour = data_hour_fix;
        var data_company = data_company_fix;
        var data_violation = data_violation_fix;
        var date = date;
        var total_data = total_data;
        // console.log(data);
        datalvl0 = [];
        totallevel0 = 0;
        for (i = 0; i < data_hour.length; i++) {
            //lvl 0
            n = 0;
            totallevel1 = 0;
            datalvl1 = [];
            html = '';
            var pr = Object.keys(data[data_hour[i]]);
            for (j = 0; j < pr.length; j++) {
                // lvl 1
                dtlvl2 = data[data_hour[i]][pr[j]];
                y1 = Object.keys(dtlvl2).length;
                totallevel2 = 0;
                categories2 = [];
                datalvl2 = [];
                info = [];
                for (k = 0; k < y1; k++) {
                    //lvl 2
                    c = categories2.length;
                    vlat = "Level " + dtlvl2[k]['level'];
                    if (c > 0) {
                        dstat = 0;
                        for (a = 0; a < c; a++) {
                            if (categories2[a] == vlat) {
                                b = 1 + datalvl2[a][1];
                                datalvl2[a][1] = b;
                                dstat = 1;
                            }
                        }
                        if (dstat == 0) {
                            categories2.push(vlat);
                            datalvl2.push([vlat, 1]);
                        }
                    } else {
                        categories2.push(vlat);
                        datalvl2.push([vlat, 1]);
                    }
                    totallevel2++;
                }
                datalevel2 = {
                    level: 2,
                    name: "Violation/" + dtlvl2[0]['hour'] + "/" + dtlvl2[0]['company'] + "/" + totallevel2,
                    categories: categories2,
                    data: datalvl2
                };
                datalvl1.push({
                    y: totallevel2,
                    z: data_hour[i],
                    drilldown: datalevel2
                });
                n = totallevel1 + totallevel2;
                totallevel1 = n;
            }
            datalvl0.push({
                y: totallevel1,
                drilldown: {
                    name: "Contractor",
                    categories: pr,
                    level: 1,
                    data: datalvl1
                }
            });
            datalvl1 = [];
            k = totallevel0 + totallevel1;
            totallevel0 = k;
        }
        var total_title_lvl1 = 0;

        var colors = Highcharts.getOptions().colors,

            categories = data_hour,

            name = 'Violation',
            level = 0,
            data = datalvl0;
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'all_all_all',
                type: 'column'
            },
            title: {
                useHTML: true,
                text: 'This Title'
            },
            subtitle: {
                text: 'Daily Violation Report<br>' + date
            },
            accessibility: {
                announceNewData: {
                    enabled: true
                }
            },
            xAxis: {
                categories: categories,
                title: {
                    text: 'Violation'
                }
            },
            yAxis: {
                title: {
                    text: 'Total Violation'
                }
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
                                var drilldown = this.drilldown;
                                if (drilldown) {
                                    setChart(drilldown.name, drilldown.categories, drilldown.data, drilldown.level);
                                } else { // restore
                                    setChart(name, categories, data, level);

                                }
                            }
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function() {
                            switch (this.series.options.level) {
                                case 0:
                                    outputlabel = this.y;
                                    $(".highcharts-xaxis text").html("Hour");

                                    this.series.chart.setTitle({
                                        text: "Total Overspeed : " + total_data
                                    }, {
                                        text: "Daily Violation Report<br>" + date
                                    });
                                    total_title_lvl1 = 0;
                                    break;
                                case 1:
                                    outputlabel = this.y;
                                    hr = this.point.z;
                                    total_title_lvl1 += outputlabel;
                                    var name = this.series.options.name;

                                    $(".highcharts-xaxis text").html(name);

                                    this.series.chart.setTitle({
                                        text: "Hour " + hr + " : " + total_title_lvl1 + " Overspeed"
                                    }, {
                                        text: "Daily Violation Report<br>" + date
                                    });

                                    break;
                                case 2:
                                    exp = this.series.name.split("/");
                                    this.series.chart.setTitle({
                                        text: ""
                                    }, {
                                        text: "<b>Hour " + exp[1] + " > " + exp[2] + " > " + exp[3] + " Overspeed </b><br>Daily Violation Report<br>" + date
                                    });
                                    return this.y;
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
                    if (key == undefined) {
                        key = "";
                    }
                    var y = this.y;
                    var s = '';
                    switch (this.series.options.level) {
                        case 0:
                            s = 'Hour ' + key + ': ';
                            s += y + ' Overspeed';
                            break;
                        case 1:
                            s = key + ' <br/>';
                            s += y + ' Overspeed';
                            break;
                        case 2:
                            s += '<b>' + y + ' Overspeed Alarm ' + this.x + '</b><br/>';
                            break;
                    }
                    return s;
                }
            },
            series: [{
                name: name,
                level: level,
                data: data,
                color: "#4bb036"
            }],
            drilldown: {
                activeAxisLabelStyle: {
                    textDecoration: 'none',
                    color: '#000000'
                },
                activeDataLabelStyle: {
                    color: "#000000",
                    textDecoration: "none"
                }
            }
        });
    }

    function charttwolevel(data_fix, data_hour_fix, data_company_fix, data_violation_fix, total_data, date) {

        var data = data_fix;
        var data_hour = data_hour_fix;
        var data_company = data_company_fix;
        var data_violation = data_violation_fix;
        var date = date;
        var total_data = total_data;
        // console.log(data);
        datalvl0 = [];
        totallevel0 = 0;
        for (i = 0; i < data_hour.length; i++) {
            //lvl 0
            n = 0;
            totallevel1 = 0;
            datalvl1 = [];
            html = '';
            var pr = Object.keys(data[data_hour[i]]);
            for (j = 0; j < pr.length; j++) {
                // lvl 1
                dtlvl2 = data[data_hour[i]][pr[j]];
                y1 = dtlvl2.length;
                datalvl1.push({
                    y: y1,
                    z: data_hour[i]
                });
                n = totallevel1 + y1;
                totallevel1 = n;
            }
            datalvl0.push({
                y: totallevel1,
                drilldown: {
                    name: "Violation",
                    categories: pr,
                    level: 1,
                    data: datalvl1
                }
            });
            datalvl1 = [];
            k = totallevel0 + totallevel1;
            totallevel0 = k;
        }
        var total_title_lvl1 = 0;

        var colors = Highcharts.getOptions().colors,

            categories = data_hour,

            name = 'Violation',
            level = 0,
            data = datalvl0;
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'all_all_all',
                type: 'column'
            },
            title: {
                useHTML: true,
                text: 'This Title'
            },
            subtitle: {
                text: 'Daily Violation Report<br>' + date
            },
            accessibility: {
                announceNewData: {
                    enabled: true
                }
            },
            xAxis: {
                categories: categories,
                title: {
                    text: 'Violation'
                }
            },
            yAxis: {
                title: {
                    text: 'Total Violation'
                }
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
                                var drilldown = this.drilldown;
                                if (drilldown) {
                                    setChart(drilldown.name, drilldown.categories, drilldown.data, drilldown.level);
                                } else { // restore
                                    setChart(name, categories, data, level);

                                }
                            }
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function() {
                            switch (this.series.options.level) {
                                case 0:
                                    outputlabel = this.y;
                                    $(".highcharts-xaxis text").html("Hour");

                                    this.series.chart.setTitle({
                                        text: "Total Violation : " + total_data
                                    }, {
                                        text: "Daily Violation Report<br>" + date
                                    });
                                    total_title_lvl1 = 0;
                                    break;
                                case 1:
                                    outputlabel = this.y;
                                    hr = this.point.z;
                                    total_title_lvl1 += outputlabel;
                                    var name = this.series.options.name;

                                    $(".highcharts-xaxis text").html(name);

                                    this.series.chart.setTitle({
                                        text: "Hour " + hr + " : " + total_title_lvl1
                                    }, {
                                        text: "Daily Violation Report<br>" + date
                                    });

                                    break;
                                case 2:
                                    outputlabel = this.y;

                                    exp = this.series.name.split("/");
                                    this.series.chart.setTitle({
                                        text: ""
                                    }, {
                                        text: "<b>Hour " + exp[1] + " > " + exp[2] + " > " + exp[3] + "</b><br>Daily Violation Report<br>" + date
                                    });
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
                    if (key == undefined) {
                        key = "";
                    }
                    var y = this.y;
                    var s = '';
                    switch (this.series.options.level) {
                        case 0:
                            s = 'Hour ' + key + ': ';
                            s += y + ' pelanggaran';
                            break;
                        case 1:
                            s = key + ' <br/>';
                            s += y + ' pelanggaran';
                            break;
                        case 2:
                            s += '<b>' + y + ' ' + this.x + '</b><br/>';
                            break;
                    }
                    return s;
                }
            },
            series: [{
                name: name,
                level: level,
                data: data,
                color: "#4bb036"
            }],
            drilldown: {
                activeAxisLabelStyle: {
                    textDecoration: 'none',
                    color: '#000000'
                },
                activeDataLabelStyle: {
                    color: "#000000",
                    textDecoration: "none"
                }
            }
        });
    }

    function chartonelevel(data_fix, data_hour_fix, data_company_fix, data_violation_fix, total_data, date) {

        var data = data_fix;
        var data_hour = data_hour_fix;
        var data_company = data_company_fix;
        var data_violation = data_violation_fix;
        var date = date;
        var total_data = total_data;
        // console.log(data);
        datalvl0 = [];
        totallevel0 = 0;
        for (i = 0; i < data_hour.length; i++) {
            //lvl 0
            n = 0;
            totallevel1 = 0;
            html = '';
            var pr = Object.keys(data[data_hour[i]]);
            for (j = 0; j < pr.length; j++) {
                // lvl 1
                dtlvl2 = data[data_hour[i]][pr[j]];
                y1 = dtlvl2.length;

                n = totallevel1 + y1;
                totallevel1 = n;
            }
            datalvl0.push({
                y: totallevel1,
            });
            k = totallevel0 + totallevel1;
            totallevel0 = k;
        }
        var total_title_lvl1 = 0;

        var colors = Highcharts.getOptions().colors,

            categories = data_hour,

            name = 'Violation',
            level = 0,
            data = datalvl0;
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'all_all_all',
                type: 'column'
            },
            title: {
                useHTML: true,
                text: 'This Title'
            },
            subtitle: {
                text: 'Daily Violation Report<br>' + date
            },
            accessibility: {
                announceNewData: {
                    enabled: true
                }
            },
            xAxis: {
                categories: categories,
                title: {
                    text: 'Violation'
                }
            },
            yAxis: {
                title: {
                    text: 'Total Violation'
                }
            },
            legend: {
                enabled: false
            },
            plotOptions: {
                column: {
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        formatter: function() {
                            switch (this.series.options.level) {
                                case 0:
                                    outputlabel = this.y;
                                    $(".highcharts-xaxis text").html("Hour");

                                    this.series.chart.setTitle({
                                        text: "Total Violation : " + total_data
                                    }, {
                                        text: "Daily Violation Report<br>" + date
                                    });
                                    total_title_lvl1 = 0;
                                    break;
                                case 1:
                                    outputlabel = this.y;
                                    hr = this.point.z;
                                    total_title_lvl1 += outputlabel;
                                    var name = this.series.options.name;

                                    $(".highcharts-xaxis text").html(name);

                                    this.series.chart.setTitle({
                                        text: "Hour " + hr + " : " + total_title_lvl1
                                    }, {
                                        text: "Daily Violation Report<br>" + date
                                    });

                                    break;
                                case 2:
                                    outputlabel = this.y;

                                    exp = this.series.name.split("/");
                                    this.series.chart.setTitle({
                                        text: ""
                                    }, {
                                        text: "<b>Hour " + exp[1] + " > " + exp[2] + " > " + exp[3] + "</b><br>Daily Violation Report<br>" + date
                                    });
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
                    if (key == undefined) {
                        key = "";
                    }
                    var y = this.y;
                    var s = '';
                    switch (this.series.options.level) {
                        case 0:
                            s = 'Hour ' + key + ': ';
                            s += y + ' pelanggaran';
                            break;
                        case 1:
                            s = key + ' <br/>';
                            s += y + ' pelanggaran';
                            break;
                        case 2:
                            s += '<b>' + y + ' ' + this.x + '</b><br/>';
                            break;
                    }
                    return s;
                }
            },
            series: [{
                name: name,
                level: level,
                data: data,
                color: "#4bb036"
            }],
            drilldown: {
                activeAxisLabelStyle: {
                    textDecoration: 'none',
                    color: '#000000'
                },
                activeDataLabelStyle: {
                    color: "#000000",
                    textDecoration: "none"
                }
            }
        });
    }

    function setChart(name, categories, data, level) {
        chart.xAxis[0].setCategories(categories);
        chart.series[0].remove();


        chart.addSeries({
            name: name,
            data: data,
            level: level,
            color: "#4bb036"
        });


    }

    function createTable(data_fix, data_hour_fix, data_company_fix, data_violation_fix, total_data, date, master_company, master_violation, opposite_company, master_vehicle, total_violation_units, total_operational_units = 0) {
        $("#tablecontent").html(" ");

        var html = '';
        if (total_data > 0) {
            html += '<thead><tr><th></th>';
            var total_per_col = [];
            var ratio = [];
            var total_mc = master_company.length
            for (var i = 0; i < total_mc; i++) {
                // html += '<th>' + master_company[i] + '</th>'; //header
                html += '<th><a href="#" onclick="showinfo(\'<?= $sdate; ?>\', \'<?= $edate; ?>\', \'' + opposite_company[master_company[i]] + '\',\'' + master_company[i] + '\', \'<?= $input['violation']; ?>\')"><b>' + master_company[i] + '</b></a></th>'; //header
                total_per_col[i] = 0;
                ratio[i] = 0;
            }
            <?php if ($input['company'] == "all") { ?>
                html += '<th>Total</th>';
            <?php } ?>
            html += '</tr></thead><tbody>';

            for (i = 0; i < master_violation.length; i++) {
                html += '<tr><th>' + master_violation[i] + '</th>';
                var total_per_row = 0;
                for (var j = 0; j < total_mc; j++) {
                    if (data_fix[master_violation[i]] != undefined) {
                        if (data_fix[master_violation[i]][master_company[j]] != undefined) {
                            df = data_fix[master_violation[i]][master_company[j]];
                            nr = df.length;
                            total_per_row += nr;
                            total_per_col[j] += nr;
                            html += '<th>' + nr + '</th>';
                        } else {
                            html += '<td></td>';
                        }
                    } else {
                        html += '<td></td>';
                    }
                }
                <?php if ($input['company'] == "all") { ?>
                    html += '<th>' + total_per_row + '</th>';
                <?php } ?>
                html += '</tr>';

            }


            //total violation
            <?php if ($input['violation'] == "all") { ?>
                html += '<tr><th>Total</th>';
                for (var i = 0; i < total_mc; i++) {
                    html += '<th>' + total_per_col[i] + '</th>';
                }
                <?php if ($input['company'] == "all") { ?>
                    html += '<th>' + total_data + '</th>';
                <?php } ?>
                html += '</tr>';
            <?php } ?>


            colspan = total_mc + 1;
            total_dt = 0;
            html += '<tr><td colspan="' + colspan + '"><td></tr>';


            //total units
            html += '</tr><tr><th>Total DT Contractor</th>';
            for (var i = 0; i < total_mc; i++) {
                dt_company = master_vehicle[opposite_company[master_company[i]]];
                html += '<th>' + dt_company + '</th>';
                total_dt += dt_company;
            }
            <?php if ($input['company'] == "all") { ?>
                html += '<th>' + total_dt + '<th>';
            <?php } ?>
            html += '</tr>';


            //total violation units
            // total_dt = 0;
            // html += '<tr><th>Total DT Violation</th>';
            // for (var i = 0; i < total_mc; i++) {
            //     var tot_vunit = total_violation_units[master_company[i]];
            //     if (tot_vunit != undefined) {
            //         dt_company = Object.keys(total_violation_units[master_company[i]]);
            //         dt_company = dt_company.length;
            //         html += '<th>' + dt_company + '</th>';
            //         total_dt += dt_company;
            //     } else {
            //         html += '<td></td>';
            //     }
            // }
            // <?php //if ($input['company'] == "all") {                 
                ?>
            //     html += '<th>' + total_dt + '<th>';
            // <?php //}                 
                ?>
            // html += '</tr>';


            <?php if ($ratio) {               ?>
                //     //total Operational units
                //     // total_dt = 0;
                //     // html += '<tr><th>Total DT Operational</th>';
                //     // for (var i = 0; i < total_mc; i++) {
                //     //     var tot_vunit = total_operational_units[master_company[i]];
                //     //     if (tot_vunit != undefined) {
                //     //         dt_company = Object.keys(total_operational_units[master_company[i]]);
                //     //         dt_company = dt_company.length;
                //     //         html += '<th>' + dt_company + '</th>';
                //     //         total_dt += dt_company;
                //     //     } else {
                //     //         html += '<td></td>';
                //     //     }
                //     // }
                //     // <?php //if ($input['company'] == "all") { 
                            //         
                            ?>
                //     //     html += '<th>' + total_dt + '<th>';
                //     // <?php //} 
                            //         
                            ?>
                //     // html += '</tr>';
                total_dt = 0;
                html += '<tr><th>Total DT Active</th>';
                for (var i = 0; i < total_mc; i++) {
                    var tot_vunit = total_violation_units[master_company[i]];
                    if (tot_vunit != undefined) {
                        total_dt += tot_vunit;

                        html += '<th>' + tot_vunit + '</th>';
                        ratio[i] = total_per_col[i] / tot_vunit;
                    } else {
                        ratio[i] = 0;
                        html += '<th></th>';

                    }

                }
                <?php if ($input['company'] == "all") {                     ?>
                    html += '<th>' + total_dt + '<th>';
                <?php }                    ?>
                html += '</tr>';

                //Ration Violation
                html += '<tr><th>Ratio Per Unit</th>';
                for (var i = 0; i < total_mc; i++) {
                    html += '<th>' + ratio[i].toFixed(1) + '</th>';

                }

                <?php if ($input['company'] == "all") {                     ?>

                    html += '<th></th>'
                <?php }                    ?>
                html += '</tr>'

            <?php }                ?>
            html += '</tbody>';
            $("#tablecontent").html(html);
        }
    }
</script>