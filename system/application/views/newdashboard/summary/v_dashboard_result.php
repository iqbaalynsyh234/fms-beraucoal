<script src="<?php echo base_url(); ?>assets/dashboard/assets/plugins/jquery/jquery.min.js"></script>
<script>
    $(document).ready(function() {

        var company_type = '<?php echo $company_type; ?>';

        if (company_type == "all") {
            normalchart();
        } else {
            stockchart();
        }

    });
</script>

<script>
    <?php if ($company_type != 'all') { ?>

        function stockchart() {
            var content_pa = '<?php echo json_encode($content_pa);                                 ?>';
            var content_pa_fix = JSON.parse(content_pa);

            var content_ma = '<?php echo json_encode($content_ma);                                 ?>';
            var content_ma_fix = JSON.parse(content_ma);

            var content_ua = '<?php echo json_encode($content_ua);                             ?>';
            var content_ua_fix = JSON.parse(content_ua);

            var content_eu = '<?php echo json_encode($content_eu);                                 ?>';
            var content_eu_fix = JSON.parse(content_eu);

            var chart = Highcharts.stockChart('testing', {
                scrollbar: {
                    barBackgroundColor: 'gray',
                    barBorderRadius: 7,
                    barBorderWidth: 0,
                    buttonBackgroundColor: 'gray',
                    buttonBorderWidth: 0,
                    buttonArrowColor: 'yellow',
                    buttonBorderRadius: 7,
                    rifleColor: 'yellow',
                    trackBackgroundColor: 'white',
                    trackBorderWidth: 1,
                    trackBorderColor: 'silver',
                    trackBorderRadius: 7,
                    minWidth: 25
                    // showFull: true,
                    // zIndex: 


                },

                // rangeSelector: {
                //     selected: 20 / 100
                // },
                chart: {
                    type: 'column',
                },
                title: {
                    text: '<?php echo $periode_show; ?>'
                },
                // subtitle: {
                //     text: ''
                // },
                xAxis: {
                    labels: {
                        // enabled: false
                        formatter: function() {
                            console.log(this);
                            i = this.value;
                            name = this.axis.series[0].userOptions.data[i][0];

                            // console.log(this.options.name);
                            return name;
                        }
                    }
                },
                yAxis: {
                    min: 0,
                    max: 100,
                    title: {
                        text: 'Nilai'
                    },
                    opposite: false //yaxis sebelah kiri
                },
                tooltip: {
                    headerFormat: '<b style="font-size:15px">{point.key}</b>',
                    pointFormat: '<b style="color:{series.color};"><span style="font-size:13px">{series.name}: </span></b>' +
                        '<b><span style="font-size:13px">{point.y:.0f}%</span></b>',

                    shared: true,
                    useHTML: true,
                    style: {
                        color: "#000000"
                    },
                    backgroundColor: '#FCFFC5',
                },
                plotOptions: {
                    column: {
                        dataLabels: {
                            enabled: true,
                            format: '{y}%'
                        }
                    }
                },
                legend: {
                    enabled: true,
                    align: 'right',
                    x: -30,
                    verticalAlign: 'top',
                    y: 0,
                    floating: true,
                    backgroundColor: Highcharts.defaultOptions.legend.backgroundColor || 'white',
                    borderColor: '#CCC',
                    borderWidth: 1,
                    shadow: false
                },
                series: [content_pa_fix, content_ma_fix, content_ua_fix, content_eu_fix]

            });
            chart.update({
                navigator: {
                    // enabled: false
                    // xAxis: {
                    // width: 300
                    // }

                }

            })

            $(".highcharts-range-selector-group").hide();
            $(".highcharts-navigator-xaxis").hide();

        }
    <?php } else { ?>

        function normalchart() {

            var data_company = '<?php echo json_encode($data_company);
                                ?>';
            var data_company_fix = JSON.parse(data_company);
            // console.log(data_company);

            var content_pa = '<?php echo json_encode($content_pa);
                                ?>';
            var content_pa_fix = JSON.parse(content_pa);

            var content_ma = '<?php echo json_encode($content_ma);
                                ?>';
            var content_ma_fix = JSON.parse(content_ma);

            var content_ua = '<?php echo json_encode($content_ua);
                                ?>';
            var content_ua_fix = JSON.parse(content_ua);

            var content_eu = '<?php echo json_encode($content_eu);
                                ?>';
            var content_eu_fix = JSON.parse(content_eu);


            Highcharts.chart('testing', {

                chart: {
                    type: 'column'
                },
                title: {
                    text: '<?php echo $periode_show; ?>'
                },
                subtitle: {
                    text: ''
                },
                xAxis: {
                    categories: data_company_fix,
                    // [
                    //     'BBS',
                    //     'BKAE',
                    //     'EST',
                    //     'GECL',
                    //     'KMB',
                    //     'MKS',
                    //     'MMS',
                    //     'RAMB',
                    //     'RBT',
                    //     'STLI'
                    // ],
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    max: 100,
                    title: {
                        text: 'Nilai'
                    }
                },
                tooltip: {
                    headerFormat: '<span style="font-size:15px">{point.key}</span><table>',
                    pointFormat: '<tr><td style="color:{series.color};padding:0"><span style="font-size:13px">{series.name}: </span></td>' +
                        '<td style="padding:0"><b><span style="font-size:13px">{point.y:.0f}%</span></b></td></tr>',
                    footerFormat: '</table>',
                    shared: true,
                    useHTML: true,
                    style: {
                        color: "#000000"
                    },
                    backgroundColor: '#FCFFC5',
                },
                plotOptions: {
                    column: {
                        // pointPadding: 0.2,
                        // borderWidth: 0,
                        dataLabels: {
                            enabled: true,
                            format: '{y}%'
                        }
                    }
                },

                series: [content_pa_fix, content_ma_fix, content_ua_fix, content_eu_fix]
                // [{
                //     name: 'MA',
                //     color: '#434348',
                //     data: [98.5, 93.4, 106.0, 106.4, 129.2, 144.0, 84.5, 83.5, 106.6, 92.3]
                //     // data: content_rit_fix

                // }, {
                //     name: 'UA',
                //     color: '#035405',
                //     data: [106.4, 129.2, 144.0, 176.0, 135.6, 148.5, 216.4, 194.1, 95.6, 54.4]
                //     // data: content_ton_fix

                // }, {
                //     name: 'EU',
                //     color: '#0c0fad',
                //     data: [98.5, 93.4, 106.0, 84.5, 106.4, 129.2, 144.0, 83.5, 106.6, 92.3]
                //     // data: content_rit_fix

                // }]
            });

        }
    <?php } ?>
</script>


<div class="row">
    <!-- result -->


    <div class="col-12" style="margin-bottom:13px">
        <figure class="highcharts-figure">
            <div id="testing"></div>
        </figure>
    </div>

    <div class="col-12">
        <br>
    </div>