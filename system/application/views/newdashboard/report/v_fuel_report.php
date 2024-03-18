<script src="<?php echo base_url(); ?>assets/dashboard/assets/plugins/jquery/jquery.min.js"></script>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/data.js"></script>
<script src="https://code.highcharts.com/modules/drilldown.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

<div class="sidebar-container">
    <?= $sidebar; ?>
</div>

<style>
    /* edit style datepicker*/
    .datetimepicker {
        background: #D32E36;
    }

    #fuel-report{
        background-color: #221f1f;
        color: white;
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


    /* End edit style date picker*/
</style>

<div class="page-content-wrapper">
    <div class="page-content" id="page-content-new">
        <div class="row">
            <div class="col-md">
                <div class="panel" id="panel_form">
                    <header class="panel-heading" id="fuel-report">Fuel Report</header>
                    <div class="panel-body" id="bar-parent10">
                        <div class="row">
                            <div class="col">
                                <form class="form-horizontal" name="frmsearch" id="frmsearch" onsubmit="javascript: return frmsearch_onsubmit();">

                                    <div class="row">
                                        <div class="col-md-3 col-sm-9">
                                            <select class="form-control select2" id="company" name="company" onchange="javascript:company_onchange()">
                                                <option value="0" selected>--Select Contractor--</option>
                                                <?php
                                                $ccompany = count($rcompany);
                                                for ($i = 0; $i < $ccompany; $i++) {

                                                    echo "<option value='" . $rcompany[$i]->company_id . "'>" . $rcompany[$i]->company_name . "</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3 col-sm-9">
                                            <select id="vehicle" name="vehicle" class="form-control select2">
                                                <option value="0">--Select Vehicle--</option>
                                                <?php
                                                $i = 1;
                                                foreach ($vehicles as $rowvehicle) {
                                                ?>
                                                    <option value="<?php echo $rowvehicle['vehicle_device'] ?>" class="vehiclename">
                                                        <?php echo $i . '. ' . $rowvehicle['vehicle_no'] . ' ' . $rowvehicle['vehicle_name'] ?>
                                                    </option>
                                                <?php $i++;
                                                } ?>
                                            </select>
                                        </div>
                                        <div class="input-group date form_date col-md-3 col-sm-9" data-date-format="dd-mm-yyyy" data-link-format="yyyy-mm-dd">
                                            <input type="text" class="form-control" type="text" readonly name="date" value="<?= date('d-m-Y') ?>">
                                            <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                        </div>
                                        <div class="input-group col-md-3 col-sm-9">
                                            <select id="shift" name="shift" class="form-control select2">
                                                <option value="0">-- All Shift --</option>
                                                <option value="1">Shift 1</option>
                                                <option value="2">Shift 2</option>
                                            </select>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-3 col-sm-9">
                                            <select id="interval" name="interval" class="form-control select2">
                                                <option value="1">Interval 1 Hour</option>
                                                <option value="2">Interval 30 Minute</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 col-sm-9">
                                            <!--offset-sm-3-->
                                            <button class="btn btn-circle btn-success" id="btnsearchreport" type="submit">Search</button>
                                            <img src="<?php echo base_url(); ?>assets/transporter/images/loader2.gif" style="display: none;" id="loadernya">
                                        </div>
                                    </div>


                                    <!-- <div class="form-group row">
                                        <label class="col-md-2 col-sm-3 control-label"> Contractor </label>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-2 col-sm-3 control-label"> Vehicle </label>
                                        
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-2 col-sm-3 control-label"> Date </label>
                                        
                                    </div>
                                    <div class="form-group row">
                                        
                                    </div>
                                    <div class="form-group row">
                                        
                                    </div> -->

                                </form>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <figure class="highcharts-figure">
                                    <div id="resultreport" style="display:none;">
                                    </div>
                                </figure>
                            </div>
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
    function frmsearch_onsubmit() {
        var vehicle = jQuery("#vehicle").val();
        var vehiclename = jQuery("#vehicle").find(":selected").html();
        var interval = jQuery("#interval").val();
        var date = document.frmsearch.date.value;
        var shift = jQuery("#shift").val();
        $("#resultreport").hide();
        $("#loadernya").show();
        if (vehicle == 0) {
            $("#loadernya").hide();
            alert("Please select vehicle!");
            return false;
        } else {
            vehiclename = vehiclename.split(".");
            if (shift == 1) {
                shiftname = "Shift 1";
            } else if (shift == 2) {
                shiftname = "Shift 2";
            } else {
                shiftname = "All Shift";
            }

            $.post("<?php echo base_url() ?>fuelreport/search", jQuery("#frmsearch").serialize(), function(response) {
                if (response.error) {
                    //     $("#result").hide();
                    $("#loadernya").hide();
                    $("#resultreport").hide();
                    alert(response.message)
                } else {

                    $("#loadernya").hide();
                    getFuelConsumption(response.data, vehiclename[1], date, interval, shift, shiftname);
                    $("#resultreport").show();
                    console.log(response);
                }
            }, "json");
            return false;
        }
    }

    function company_onchange() {
        var data_company = jQuery("#company").val();
        if (data_company == 0) {
            jQuery("#vehicle").html("<option value='0' selected='selected' class='form-control'>--Select Vehicle--</option>");
        } else {
            jQuery("#mn_vehicle").show();
            var site = "<?= base_url() ?>fuelreport/get_vehicle_by_company_with_numberorder/" + data_company;
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

    $(document).ready(function() {
        //edit datepicker
        $(".glyphicon-arrow-right").html(">>");
        $(".glyphicon-arrow-left").html("<<");

    });

    function getFuelConsumption(data, vehicle, date, interval, shift, shiftname) {

        const hour = [];
        const fuel = [];

        if (interval == 1) {
            if (shift == 1) {
                for (i = 6; i < 18; i++) {
                    var t = String(i);
                    if (t.length == 1) {
                        t = "0" + t;
                    }
                    if (typeof data[t] !== 'undefined') {
                        hour.push(t);
                        fuel.push(parseInt(data[t]));
                    }
                }

            } else if (shift == 2) {
                for (i = 18; i < 24; i++) {
                    var t = String(i);
                    if (typeof data[t] !== 'undefined') {
                        hour.push(t);
                        fuel.push(parseInt(data[t]));
                    }
                }
                for (i = 0; i < 6; i++) {
                    var t = String(i);
                    t = "0" + t;
                    if (typeof data[t] !== 'undefined') {
                        hour.push(t);
                        fuel.push(parseInt(data[t]));
                    }
                }

            } else {
                for (i = 6; i < 24; i++) {
                    var t = String(i);
                    if (t.length == 1) {
                        t = "0" + t;
                    }
                    if (typeof data[t] !== 'undefined') {
                        hour.push(t);
                        fuel.push(parseInt(data[t]));
                    }
                }
                for (i = 0; i < 6; i++) {
                    var t = String(i);
                    t = "0" + t;
                    if (typeof data[t] !== 'undefined') {
                        hour.push(t);
                        fuel.push(parseInt(data[t]));
                    }
                }

            }
        } else {
            var jml = data.length;
            var lasthour = "";
            var lastminute = 0;
            var lastfuel = 0;
            var fuelin = 0;
            var delta_cons = 0;
            for (i = 0; i < jml; i++) {
                if (i == 0) {
                    fuelin = parseInt(data[i]['fuel']);
                    hour.push(data[i]["hour"] + "." + data[i]["minute"]);
                    fuel.push(fuelin);
                    lasthour = data[i]["hour"];
                    min = parseInt(data[i]['minute']);
                    if (min > 29) {
                        lastminute = parseInt(data[i]["minute"]);
                    } else {
                        lastminute = 0;
                    }
                    lastfuel = fuelin;
                } else {
                    if (lasthour == data[i]["hour"]) {
                        if (lastminute == 0) {
                            min = parseInt(data[i]['minute']);
                            if (min > 29) {
                                fuelin = parseInt(data[i]['fuel']);
                                delta_cons = fuelin - lastfuel;
                                if (delta_cons > 15) {
                                    //asumsi isi bbm selalu lebih dari 15 liter
                                } else if ((delta_cons > 0) && (delta_cons < 15)) {
                                    //asumsi data invalid maka data disamakan dengan data sebelumnya
                                    fuelin = lastfuel;
                                }

                                hour.push(data[i]["hour"] + "." + data[i]["minute"]);
                                fuel.push(fuelin);
                                lastminute = data[i]["minute"];
                                lasthour = data[i]["hour"];
                                lastfuel = fuelin;
                            }
                        }
                    } else {
                        fuelin = parseInt(data[i]['fuel']);
                        delta_cons = fuelin - lastfuel;
                        if (delta_cons > 15) {
                            //asumsi isi bbm selalu lebih dari 15 liter
                        } else if ((delta_cons > 0) && (delta_cons < 15)) {
                            //asumsi data invalid maka data disamakan dengan data sebelumnya
                            fuelin = lastfuel;
                        }

                        hour.push(data[i]["hour"] + "." + data[i]["minute"]);
                        fuel.push(fuelin);
                        min = parseInt(data[i]['minute']);
                        if (min > 29) {
                            lastminute = parseInt(data[i]["minute"]);
                        } else {
                            lastminute = 0;
                        }
                        lasthour = data[i]["hour"];
                        lastfuel = fuelin;
                    }
                }
            }
        }

        console.log(hour);
        console.log(fuel);
        // return false;
        Highcharts.chart('resultreport', {
            chart: {
                // type: 'line'
                type: 'area'
            },
            title: {
                text: 'Fuel Report'
            },
            subtitle: {
                useHTML: true,
                text: vehicle + " <span style=\"color:red\"><b>(" + date + " " + shiftname + ")</b></span>"
            },
            xAxis: {
                categories: hour,
                title: {
                    text: "Hour"
                }
            },
            yAxis: {
                title: {
                    text: 'Liter'
                }
            },
            legend: {
                enabled: false
            },
            plotOptions: {
                // line: {
                //     dataLabels: {
                //         enabled: true
                //     },
                //     enableMouseTracking: false
                // }
                area: {
                    // pointStart: 1940,
                    dataLabels: {
                        enabled: true
                    },
                    marker: {
                        enabled: false,
                        symbol: 'circle',
                        radius: 2,
                        states: {
                            hover: {
                                enabled: true
                            }
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
                pointFormat: "{point.y} Liter"

            },
            series: [{
                name: 'Hour',
                data: fuel
                // data: [11.4, 10.2, 9.7, 8.5, 7.4, null, 7, 6.3, 6, null, null, 16, 15.7, 15.6, 15, 14.7, 14.2, 14, 13.9, 13, 12.9, 12.8, 12.2, 12]
            }]
        });
    }
</script>