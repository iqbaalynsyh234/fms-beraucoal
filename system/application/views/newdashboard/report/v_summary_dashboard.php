<script src="<?php echo base_url(); ?>assets/dashboard/assets/plugins/jquery/jquery.min.js"></script>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/series-label.js"></script>
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
        background-color: #1f50a2;
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
                    <header class="panel-heading" id="fuel-report">Dashboard Mockup</header>
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

            $.post("<?php echo base_url() ?>summarydashboard/search", jQuery("#frmsearch").serialize(), function(response) {
                if (response.error) {
                    //     $("#result").hide();
                    $("#loadernya").hide();
                    $("#resultreport").hide();
                    alert(response.message)
                } else {

                    $("#loadernya").hide();
                    getChart();
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
            var site = "<?= base_url() ?>summarydashboard/get_vehicle_by_company_with_numberorder/" + data_company;
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

    function getChart() {

     // Data retrieved from https://www.vikjavev.no/ver/snjomengd
	Highcharts.chart('container', {
		chart: {
			type: 'spline'
		},
		title: {
			text: 'Snow depth at Vikjafjellet, Norway'
		},
		subtitle: {
			text: 'Irregular time data in Highcharts JS'
		},
		xAxis: {
			type: 'datetime',
			dateTimeLabelFormats: { // don't display the year
				month: '%e. %b',
				year: '%b'
			},
			title: {
				text: 'Date'
			}
		},
		yAxis: {
			title: {
				text: 'Snow depth (m)'
			},
			min: 0
		},
		tooltip: {
			headerFormat: '<b>{series.name}</b><br>',
			pointFormat: '{point.x:%e. %b}: {point.y:.2f} m'
		},

		plotOptions: {
			series: {
				marker: {
					enabled: true,
					radius: 2.5
				}
			}
		},

		colors: ['#6CF', '#39F', '#06C', '#036', '#000'],

		// Define the data points. All series have a year of 1970/71 in order
		// to be compared on the same x axis. Note that in JavaScript, months start
		// at 0 for January, 1 for February etc.
		series: [
			{
				name: 'Winter 2019-2020',
				data: [
					[Date.UTC(1970, 9, 24), 0],
					[Date.UTC(1970, 9, 27), 0.12],
					[Date.UTC(1970, 9, 30), 0.09],
					[Date.UTC(1970, 10,  3), 0.13],
					[Date.UTC(1970, 10,  6), 0.12],
					[Date.UTC(1970, 10,  9), 0.13],
					[Date.UTC(1970, 10, 12), 0.13],
					[Date.UTC(1970, 10, 15), 0.16],
					[Date.UTC(1970, 10, 18), 0.19],
					[Date.UTC(1970, 10, 21), 0.25],
					[Date.UTC(1970, 10, 24), 0.26],
					[Date.UTC(1970, 10, 27), 0.24],
					[Date.UTC(1970, 10, 30), 0.25],
					[Date.UTC(1970, 11,  3), 0.26],
					[Date.UTC(1970, 11,  6), 0.36],
					[Date.UTC(1970, 11,  9), 0.43],
					[Date.UTC(1970, 11, 12), 0.32],
					[Date.UTC(1970, 11, 15), 0.48],
					[Date.UTC(1970, 11, 18), 0.5],
					[Date.UTC(1970, 11, 21), 0.44],
					[Date.UTC(1970, 11, 24), 0.43],
					[Date.UTC(1970, 11, 27), 0.45],
					[Date.UTC(1970, 11, 30), 0.4],
					[Date.UTC(1971, 0,  3), 0.39],
					[Date.UTC(1971, 0,  6), 0.56],
					[Date.UTC(1971, 0,  9), 0.57],
					[Date.UTC(1971, 0, 12), 0.68],
					[Date.UTC(1971, 0, 15), 0.93],
					[Date.UTC(1971, 0, 18), 1.11],
					[Date.UTC(1971, 0, 21), 1.01],
					[Date.UTC(1971, 0, 24), 0.99],
					[Date.UTC(1971, 0, 27), 1.17],
					[Date.UTC(1971, 0, 30), 1.24],
					[Date.UTC(1971, 1,  3), 1.41],
					[Date.UTC(1971, 1,  6), 1.47],
					[Date.UTC(1971, 1,  9), 1.4],
					[Date.UTC(1971, 1, 12), 1.92],
					[Date.UTC(1971, 1, 15), 2.03],
					[Date.UTC(1971, 1, 18), 2.46],
					[Date.UTC(1971, 1, 21), 2.53],
					[Date.UTC(1971, 1, 24), 2.73],
					[Date.UTC(1971, 1, 27), 2.67],
					[Date.UTC(1971, 2,  3), 2.65],
					[Date.UTC(1971, 2,  6), 2.62],
					[Date.UTC(1971, 2,  9), 2.79],
					[Date.UTC(1971, 2, 13), 2.93],
					[Date.UTC(1971, 2, 20), 3.09],
					[Date.UTC(1971, 2, 27), 2.76],
					[Date.UTC(1971, 2, 30), 2.73],
					[Date.UTC(1971, 3,  4), 2.9],
					[Date.UTC(1971, 3,  9), 2.77],
					[Date.UTC(1971, 3, 12), 2.78],
					[Date.UTC(1971, 3, 15), 2.76],
					[Date.UTC(1971, 3, 18), 2.76],
					[Date.UTC(1971, 3, 21), 2.7],
					[Date.UTC(1971, 3, 24), 2.61],
					[Date.UTC(1971, 3, 27), 2.52],
					[Date.UTC(1971, 3, 30), 2.53],
					[Date.UTC(1971, 4,  3), 2.55],
					[Date.UTC(1971, 4,  6), 2.52],
					[Date.UTC(1971, 4,  9), 2.44],
					[Date.UTC(1971, 4, 12), 2.43],
					[Date.UTC(1971, 4, 15), 2.43],
					[Date.UTC(1971, 4, 18), 2.48],
					[Date.UTC(1971, 4, 21), 2.41],
					[Date.UTC(1971, 4, 24), 2.16],
					[Date.UTC(1971, 4, 27), 2.01],
					[Date.UTC(1971, 4, 30), 1.88],
					[Date.UTC(1971, 5,  2), 1.62],
					[Date.UTC(1971, 5,  6), 1.43],
					[Date.UTC(1971, 5,  9), 1.3],
					[Date.UTC(1971, 5, 12), 1.11],
					[Date.UTC(1971, 5, 15), 0.84],
					[Date.UTC(1971, 5, 18), 0.54],
					[Date.UTC(1971, 5, 21), 0.19],
					[Date.UTC(1971, 5, 23), 0]
				]
			}, {
				name: 'Winter 2020-2021',
				data: [
					[Date.UTC(1970, 10, 14), 0],
					[Date.UTC(1970, 11,  6), 0.35],
					[Date.UTC(1970, 11, 13), 0.35],
					[Date.UTC(1970, 11, 20), 0.33],
					[Date.UTC(1970, 11, 30), 0.53],
					[Date.UTC(1971, 0, 13), 0.62],
					[Date.UTC(1971, 0, 20), 0.6],
					[Date.UTC(1971, 1,  2), 0.69],
					[Date.UTC(1971, 1, 18), 0.67],
					[Date.UTC(1971, 1, 21), 0.65],
					[Date.UTC(1971, 1, 24), 0.66],
					[Date.UTC(1971, 1, 27), 0.66],
					[Date.UTC(1971, 2,  3), 0.61],
					[Date.UTC(1971, 2,  6), 0.6],
					[Date.UTC(1971, 2,  9), 0.69],
					[Date.UTC(1971, 2, 12), 0.66],
					[Date.UTC(1971, 2, 15), 0.75],
					[Date.UTC(1971, 2, 18), 0.76],
					[Date.UTC(1971, 2, 21), 0.75],
					[Date.UTC(1971, 2, 24), 0.69],
					[Date.UTC(1971, 2, 27), 0.82],
					[Date.UTC(1971, 2, 30), 0.86],
					[Date.UTC(1971, 3,  3), 0.81],
					[Date.UTC(1971, 3,  6), 1],
					[Date.UTC(1971, 3,  9), 1.15],
					[Date.UTC(1971, 3, 10), 1.35],
					[Date.UTC(1971, 3, 12), 1.26],
					[Date.UTC(1971, 3, 15), 1.18],
					[Date.UTC(1971, 3, 18), 1.14],
					[Date.UTC(1971, 3, 21), 1.04],
					[Date.UTC(1971, 3, 24), 1.06],
					[Date.UTC(1971, 3, 27), 1.05],
					[Date.UTC(1971, 3, 30), 1.03],
					[Date.UTC(1971, 4,  3), 1.01],
					[Date.UTC(1971, 4,  6), 0.98],
					[Date.UTC(1971, 4,  9), 0.94],
					[Date.UTC(1971, 4, 12), 0.8],
					[Date.UTC(1971, 4, 15), 0.61],
					[Date.UTC(1971, 4, 18), 0.43],
					[Date.UTC(1971, 4, 21), 0.29],
					[Date.UTC(1971, 4, 24), 0.1],
					[Date.UTC(1971, 4, 26), 0]
				]
			}, {
				name: 'Winter 2021-2022',
				data: [
					[Date.UTC(1970, 10,  5), 0],
					[Date.UTC(1970, 10, 12), 0.1],
					[Date.UTC(1970, 10, 21), 0.15],
					[Date.UTC(1970, 10, 22), 0.19],
					[Date.UTC(1970, 10, 27), 0.17],
					[Date.UTC(1970, 10, 30), 0.27],
					[Date.UTC(1970, 11,  2), 0.25],
					[Date.UTC(1970, 11,  4), 0.27],
					[Date.UTC(1970, 11,  5), 0.26],
					[Date.UTC(1970, 11,  6), 0.25],
					[Date.UTC(1970, 11,  7), 0.26],
					[Date.UTC(1970, 11,  8), 0.26],
					[Date.UTC(1970, 11,  9), 0.25],
					[Date.UTC(1970, 11, 10), 0.25],
					[Date.UTC(1970, 11, 11), 0.25],
					[Date.UTC(1970, 11, 12), 0.26],
					[Date.UTC(1970, 11, 22), 0.22],
					[Date.UTC(1970, 11, 23), 0.22],
					[Date.UTC(1970, 11, 24), 0.22],
					[Date.UTC(1970, 11, 25), 0.24],
					[Date.UTC(1970, 11, 26), 0.24],
					[Date.UTC(1970, 11, 27), 0.24],
					[Date.UTC(1970, 11, 28), 0.24],
					[Date.UTC(1970, 11, 29), 0.24],
					[Date.UTC(1970, 11, 30), 0.22],
					[Date.UTC(1970, 11, 31), 0.18],
					[Date.UTC(1971, 0,  1), 0.17],
					[Date.UTC(1971, 0,  2), 0.23],
					[Date.UTC(1971, 0,  9), 0.5],
					[Date.UTC(1971, 0, 10), 0.5],
					[Date.UTC(1971, 0, 11), 0.53],
					[Date.UTC(1971, 0, 12), 0.48],
					[Date.UTC(1971, 0, 13), 0.4],
					[Date.UTC(1971, 0, 17), 0.36],
					[Date.UTC(1971, 0, 22), 0.69],
					[Date.UTC(1971, 0, 23), 0.62],
					[Date.UTC(1971, 0, 29), 0.72],
					[Date.UTC(1971, 1,  2), 0.95],
					[Date.UTC(1971, 1, 10), 1.73],
					[Date.UTC(1971, 1, 15), 1.76],
					[Date.UTC(1971, 1, 26), 2.18],
					[Date.UTC(1971, 2,  2), 2.22],
					[Date.UTC(1971, 2,  6), 2.13],
					[Date.UTC(1971, 2,  8), 2.11],
					[Date.UTC(1971, 2,  9), 2.12],
					[Date.UTC(1971, 2, 10), 2.11],
					[Date.UTC(1971, 2, 11), 2.09],
					[Date.UTC(1971, 2, 12), 2.08],
					[Date.UTC(1971, 2, 13), 2.08],
					[Date.UTC(1971, 2, 14), 2.07],
					[Date.UTC(1971, 2, 15), 2.08],
					[Date.UTC(1971, 2, 17), 2.12],
					[Date.UTC(1971, 2, 18), 2.19],
					[Date.UTC(1971, 2, 21), 2.11],
					[Date.UTC(1971, 2, 24), 2.1],
					[Date.UTC(1971, 2, 27), 1.89],
					[Date.UTC(1971, 2, 30), 1.92],
					[Date.UTC(1971, 3,  3), 1.9],
					[Date.UTC(1971, 3,  6), 1.95],
					[Date.UTC(1971, 3,  9), 1.94],
					[Date.UTC(1971, 3, 12), 2],
					[Date.UTC(1971, 3, 15), 1.9],
					[Date.UTC(1971, 3, 18), 1.84],
					[Date.UTC(1971, 3, 21), 1.75],
					[Date.UTC(1971, 3, 24), 1.69],
					[Date.UTC(1971, 3, 27), 1.64],
					[Date.UTC(1971, 3, 30), 1.64],
					[Date.UTC(1971, 4,  3), 1.58],
					[Date.UTC(1971, 4,  6), 1.52],
					[Date.UTC(1971, 4,  9), 1.43],
					[Date.UTC(1971, 4, 12), 1.42],
					[Date.UTC(1971, 4, 15), 1.37],
					[Date.UTC(1971, 4, 18), 1.26],
					[Date.UTC(1971, 4, 21), 1.11],
					[Date.UTC(1971, 4, 24), 0.92],
					[Date.UTC(1971, 4, 27), 0.75],
					[Date.UTC(1971, 4, 30), 0.55],
					[Date.UTC(1971, 5,  3), 0.35],
					[Date.UTC(1971, 5,  6), 0.21],
					[Date.UTC(1971, 5,  9), 0]
				]
			}
		]
	});

    }
</script>