<!--<script type="text/javascript" src="js/script.js"></script>
<script src="<?php echo base_url()?>assets/dashboard/assets/js/jquery-1.7.1.min.js" type="text/javascript"></script>

-->
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/data.js"></script>
<script src="https://code.highcharts.com/modules/drilldown.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

<script type="text/javascript">

function gethighchartProductionDaily(){
	
	// Create the chart
Highcharts.chart('container', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'MTD HAULING PRODUCTION'
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
        type: 'category'
    },
    yAxis: {
        title: {
            text: 'Tonnage'
        }

    },
    legend: {
        enabled: false
    },
    plotOptions: {
        series: {
            borderWidth: 0,
            dataLabels: {
                enabled: true,
                format: '{point.y:.0f}'
            }
        }
    },

    tooltip: {
        headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
        pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.0f}</b><br/>'
    },

    series: [
        {
            name: "Tonnage",
            colorByPoint: true,
            data: [
                {
                    name: "01-Des-21",
                    y: 99000,
                    drilldown: "01"
                },
                {
                    name: "02-Des-21",
                    y: 58500,
                    drilldown: "02"
                },
                {
                    name: "03-Des-21",
                    y: 49500,
                    drilldown: "03"
                },
                {
                    name: "04-Des-21",
                    y: 58500,
                    drilldown: "04"
                },
                {
                    name: "05-Des-21",
                    y: 90000,
                    drilldown: "05"
                },
                {
                    name: "06-Des-21",
                    y: 54000,
                    drilldown: "06"
                },
                {
                    name: "07-Des-21",
                    y: 67500,
                    drilldown: "07"
                }
            ]
        }
    ],
    drilldown: {
        breadcrumbs: {
            position: {
                align: 'right'
            }
        },
        series: [
            {
                name: "01-Des-21",
                id: "01",
                data: [
                    [
                        "PORT BIB",
						67320
                    ],
                    [
                        "PORT BIR",
                        31680
                    ]
                    
                ]
            },
			{
                name: "02-Des-21",
                id: "02",
                data: [
                    [
                        "PORT BIB",
						39780
                    ],
                    [
                        "PORT BIR",
                        18720
                    ]
                    
                ]
            },
			{
                name: "03-Des-21",
                id: "03",
                data: [
                    [
                        "PORT BIB",
						39780
                    ],
                    [
                        "PORT BIR",
                        18720
                    ]
                    
                ]
            },
			{
                name: "04-Des-21",
                id: "04",
                data: [
                    [
                        "PORT BIB",
						39780
                    ],
                    [
                        "PORT BIR",
                        18720
                    ]
                    
                ]
            },
			{
                name: "05-Des-21",
                id: "05",
                data: [
                    [
                        "PORT BIB",
						39780
                    ],
                    [
                        "PORT BIR",
                        18720
                    ]
                    
                ]
            },
			{
                name: "06-Des-21",
                id: "06",
                data: [
                    [
                        "PORT BIB",
						39780
                    ],
                    [
                        "PORT BIR",
                        18720
                    ]
                    
                ]
            },
			{
                name: "07-Des-21",
                id: "07",
                data: [
                    [
                        "PORT BIB",
						39780
                    ],
                    [
                        "PORT BIR",
                        18720
                    ]
                    
                ]
            }
			
			
		]
    }
});
	
}

function gethighchartContractor(){
	
	// Create the chart
Highcharts.chart('container2', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'MTD HAULING CONTRACTOR'
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
        type: 'category'
    },
    yAxis: {
        title: {
            text: 'Tonnage'
        }

    },
    legend: {
        enabled: false
    },
    plotOptions: {
        series: {
            borderWidth: 0,
            dataLabels: {
                enabled: true,
                format: '{point.y:.0f}'
            }
        }
    },

    tooltip: {
        headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
        pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.0f}</b><br/>'
    },

    series: [
        {
            name: "Tonnage",
            colorByPoint: true,
            data: [
                {
                    name: "01-Des-21",
                    y: 99000,
                    drilldown: "01"
                },
                {
                    name: "02-Des-21",
                    y: 58500,
                    drilldown: "02"
                },
                {
                    name: "03-Des-21",
                    y: 49500,
                    drilldown: "03"
                },
                {
                    name: "04-Des-21",
                    y: 58500,
                    drilldown: "04"
                },
                {
                    name: "05-Des-21",
                    y: 90000,
                    drilldown: "05"
                },
                {
                    name: "06-Des-21",
                    y: 54000,
                    drilldown: "06"
                },
                {
                    name: "07-Des-21",
                    y: 67500,
                    drilldown: "07"
                }
            ]
        }
    ],
    drilldown: {
        breadcrumbs: {
            position: {
                align: 'right'
            }
        },
        series: [
            {
                name: "01-Des-21",
                id: "01",
                data: [
                    [
                        "RBT",
						12000
                    ],
                    [
                        "BKA",
                        15000
                    ],
					[
                        "GECL",
						17000
                    ],
                    [
                        "MKS",
                        16000
                    ],
					[
                        "MMS",
						11000
                    ],
                    [
                        "BBS",
                        19000
                    ],
					[
                        "EST",
						9000
                    ],
                    [
                        "KMB",
                        0
                    ],
					[
                        "RAM",
						0
                    ],
                    [
                        "STLI",
                        0
                    ]
                    
                ]
            },
			{
                name: "02-Des-21",
                id: "02",
                data: [
                    [
                        "RBT",
						18000
                    ],
                    [
                        "BKA",
                        14000
                    ],
					[
                        "GECL",
						11500
                    ],
                    [
                        "MKS",
                        0
                    ],
					[
                        "MMS",
						15000
                    ],
                    [
                        "BBS",
                        0
                    ],
					[
                        "EST",
						0
                    ],
                    [
                        "KMB",
                        0
                    ],
					[
                        "RAM",
						0
                    ],
                    [
                        "STLI",
                        0
                    ]
                    
                ]
            },
			{
                name: "03-Des-21",
                id: "03",
                data: [
                    [
                        "RBT",
						18000
                    ],
                    [
                        "BKA",
                        8000
                    ],
					[
                        "GECL",
						6700
                    ],
                    [
                        "MKS",
                        4500
                    ],
					[
                        "MMS",
						0
                    ],
                    [
                        "BBS",
                        0
                    ],
					[
                        "EST",
						0
                    ],
                    [
                        "KMB",
                        12300
                    ],
					[
                        "RAM",
						0
                    ],
                    [
                        "STLI",
                        0
                    ]
                    
                ]
            },
			{
                name: "04-Des-21",
                id: "04",
                data: [
                    [
                        "RBT",
						9000
                    ],
                    [
                        "BKA",
                        0
                    ],
					[
                        "GECL",
						11000
                    ],
                    [
                        "MKS",
                        0
                    ],
					[
                        "MMS",
						7000
                    ],
                    [
                        "BBS",
                        11000
                    ],
					[
                        "EST",
						5000
                    ],
                    [
                        "KMB",
                        0
                    ],
					[
                        "RAM",
						0
                    ],
                    [
                        "STLI",
                        10000
                    ]
                    
                ]
            },
			{
                name: "05-Des-21",
                id: "05",
                data: [
                    [
                        "RBT",
						13000
                    ],
                    [
                        "BKA",
                        9000
                    ],
					[
                        "GECL",
						12000
                    ],
                    [
                        "MKS",
                        12500
                    ],
					[
                        "MMS",
						7500
                    ],
                    [
                        "BBS",
                        9300
                    ],
					[
                        "EST",
						0
                    ],
                    [
                        "KMB",
                        6700
                    ],
					[
                        "RAM",
						8000
                    ],
                    [
                        "STLI",
                        12000
                    ]
                    
                ]
            },
			{
                name: "06-Des-21",
                id: "06",
                data: [
                    [
                        "RBT",
						8500
                    ],
                    [
                        "BKA",
                        4000
                    ],
					[
                        "GECL",
						8000
                    ],
                    [
                        "MKS",
                        6000
                    ],
					[
                        "MMS",
						8000
                    ],
                    [
                        "BBS",
                        0
                    ],
					[
                        "EST",
						7500
                    ],
                    [
                        "KMB",
                        12000
                    ],
					[
                        "RAM",
						0
                    ],
                    [
                        "STLI",
                        0
                    ]
                    
                ]
            },
			{
                name: "07-Des-21",
                id: "07",
                data: [
                    [
                        "RBT",
						9000
                    ],
                    [
                        "BKA",
                        10000
                    ],
					[
                        "GECL",
						0
                    ],
                    [
                        "MKS",
                        7500
                    ],
					[
                        "MMS",
						4500
                    ],
                    [
                        "BBS",
                        8000
                    ],
					[
                        "EST",
						13000
                    ],
                    [
                        "KMB",
                        0
                    ],
					[
                        "RAM",
						8000
                    ],
                    [
                        "STLI",
                        7500
                    ]
                    
                ]
            }
			
			
		]
    }
});
	
}

function gethighchartProductionToday(){
	
	// Create the chart
Highcharts.chart('container3', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'TOTAL HAULING PRODUCTION'
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
        type: 'category'
    },
    yAxis: {
        title: {
            text: 'Tonnage Hourly'
        }

    },
    legend: {
        enabled: false
    },
    plotOptions: {
        series: {
            borderWidth: 0,
            dataLabels: {
                enabled: true,
                format: '{point.y:.0f}'
            }
        }
    },

    tooltip: {
        headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
        pointFormat: '<span style="color:{point.color}">Hour {point.name}</span>: <b>{point.y:.0f}</b><br/>'
    },

    series: [
        {
            name: "Tonnage",
            colorByPoint: true,
            data: [
                {
                    name: "06",
                    y: 4000,
                    drilldown: "06"
                },
                {
                    name: "07",
                    y: 5000,
                    drilldown: "07"
                },
                {
                    name: "08",
                    y: 3500,
                    drilldown: "08"
                },
                {
                    name: "09",
                    y: 4500,
                    drilldown: "09"
                },
                {
                    name: "10",
                    y: 5000,
                    drilldown: "10"
                },
                {
                    name: "11",
                    y: 6000,
                    drilldown: "11"
                },
                {
                    name: "12",
                    y: 7000,
                    drilldown: "12"
                }
            ]
        }
    ],
    drilldown: {
        breadcrumbs: {
            position: {
                align: 'right'
            }
        },
        series: [
            {
                name: "06",
                id: "06",
                data: [
                    [
                        "PORT BIB",
						3000
                    ],
                    [
                        "PORT BIR",
                        1000
                    ]
                    
                ]
            },
			{
                name: "07",
                id: "07",
                data: [
                    [
                        "PORT BIB",
						2000
                    ],
                    [
                        "PORT BIR",
                        3000
                    ]
                    
                ]
            },
			{
                name: "08",
                id: "08",
                data: [
                    [
                        "PORT BIB",
						1500
                    ],
                    [
                        "PORT BIR",
                        2000
                    ]
                    
                ]
            },
			{
                name: "09",
                id: "09",
                data: [
                    [
                        "PORT BIB",
						2500
                    ],
                    [
                        "PORT BIR",
                        2000
                    ]
                    
                ]
            },
			{
                name: "10",
                id: "10",
                data: [
                    [
                        "PORT BIB",
						4000
                    ],
                    [
                        "PORT BIR",
                        1000
                    ]
                    
                ]
            },
			{
                name: "11",
                id: "11",
                data: [
                    [
                        "PORT BIB",
						3500
                    ],
                    [
                        "PORT BIR",
                        1500
                    ]
                    
                ]
            },
			{
                name: "12",
                id: "12",
                data: [
                    [
                        "PORT BIB",
						2000
                    ],
                    [
                        "PORT BIR",
                        5000
                    ]
                    
                ]
            }
			
			
		]
    }
});
	
}

function gethighchartContractorToday(){
	
	// Create the chart
Highcharts.chart('container4', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'HAULING CONTRACTOR TODAY'
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
        type: 'category'
    },
    yAxis: {
        title: {
            text: 'Tonnage Hourly'
        }

    },
    legend: {
        enabled: false
    },
    plotOptions: {
        series: {
            borderWidth: 0,
            dataLabels: {
                enabled: true,
                format: '{point.y:.0f}'
            }
        }
    },

    tooltip: {
        headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
        pointFormat: '<span style="color:{point.color}">Hour {point.name}</span>: <b>{point.y:.0f}</b><br/>'
    },

    series: [
        {
            name: "Tonnage",
            colorByPoint: true,
            data: [
                {
                    name: "06",
                    y: 4000,
                    drilldown: "06"
                },
                {
                    name: "07",
                    y: 5000,
                    drilldown: "07"
                },
                {
                    name: "08",
                    y: 3500,
                    drilldown: "08"
                },
                {
                    name: "09",
                    y: 4500,
                    drilldown: "09"
                },
                {
                    name: "10",
                    y: 5000,
                    drilldown: "10"
                },
                {
                    name: "11",
                    y: 6000,
                    drilldown: "11"
                },
                {
                    name: "12",
                    y: 7000,
                    drilldown: "12"
                }
            ]
        }
    ],
    drilldown: {
        breadcrumbs: {
            position: {
                align: 'right'
            }
        },
        series: [
            {
                name: "06",
                id: "06",
                data: [
                    [
                        "RBT",
						1000
                    ],
                    [
                        "BKA",
                        1000
                    ],
					[
                        "GECL",
						0
                    ],
                    [
                        "MKS",
                        0
                    ],
					[
                        "MMS",
						1000
                    ],
                    [
                        "BBS",
                        0
                    ],
					[
                        "EST",
						1000
                    ],
                    [
                        "KMB",
                        0
                    ],
					[
                        "RAM",
						0
                    ],
                    [
                        "STLI",
                        0
                    ]
                    
                ]
            },
			{
                name: "07",
                id: "07",
                data: [
                    [
                        "RBT",
						2000
                    ],
                    [
                        "BKA",
                        1000
                    ],
					[
                        "GECL",
						0
                    ],
                    [
                        "MKS",
                        0
                    ],
					[
                        "MMS",
						1000
                    ],
                    [
                        "BBS",
                        0
                    ],
					[
                        "EST",
						0
                    ],
                    [
                        "KMB",
                        0
                    ],
					[
                        "RAM",
						0
                    ],
                    [
                        "STLI",
                        0
                    ]
                    
                ]
            },
			{
                name: "08",
                id: "08",
                data: [
                    [
                        "RBT",
						1000
                    ],
                    [
                        "BKA",
                        0
                    ],
					[
                        "GECL",
						500
                    ],
                    [
                        "MKS",
                        2000
                    ],
					[
                        "MMS",
						0
                    ],
                    [
                        "BBS",
                        0
                    ],
					[
                        "EST",
						0
                    ],
                    [
                        "KMB",
                        0
                    ],
					[
                        "RAM",
						0
                    ],
                    [
                        "STLI",
                        0
                    ]
                    
                ]
            },
			{
                name: "09",
                id: "09",
                data: [
                    [
                        "RBT",
						0
                    ],
                    [
                        "BKA",
                        2000
                    ],
					[
                        "GECL",
						1000
                    ],
                    [
                        "MKS",
                        0
                    ],
					[
                        "MMS",
						0
                    ],
                    [
                        "BBS",
                        1000
                    ],
					[
                        "EST",
						500
                    ],
                    [
                        "KMB",
                        0
                    ],
					[
                        "RAM",
						0
                    ],
                    [
                        "STLI",
                        0
                    ]
                    
                ]
            },
			{
                name: "10",
                id: "10",
                data: [
                    [
                        "RBT",
						0
                    ],
                    [
                        "BKA",
                        0
                    ],
					[
                        "GECL",
						0
                    ],
                    [
                        "MKS",
                        2000
                    ],
					[
                        "MMS",
						2000
                    ],
                    [
                        "BBS",
                        0
                    ],
					[
                        "EST",
						0
                    ],
                    [
                        "KMB",
                        0
                    ],
					[
                        "RAM",
						0
                    ],
                    [
                        "STLI",
                        1000
                    ]
                    
                ]
            },
			{
                name: "11",
                id: "11",
                data: [
                    [
                        "RBT",
						0
                    ],
                    [
                        "BKA",
                        0
                    ],
					[
                        "GECL",
						1000
                    ],
                    [
                        "MKS",
                        1000
                    ],
					[
                        "MMS",
						500
                    ],
                    [
                        "BBS",
                        0
                    ],
					[
                        "EST",
						0
                    ],
                    [
                        "KMB",
                        0
                    ],
					[
                        "RAM",
						1500
                    ],
                    [
                        "STLI",
                        2000
                    ]
                    
                ]
            },
			{
                name: "12",
                id: "12",
                data: [
                    [
                        "RBT",
						1000
                    ],
                    [
                        "BKA",
                        1000
                    ],
					[
                        "GECL",
						0
                    ],
                    [
                        "MKS",
                        0
                    ],
					[
                        "MMS",
						0
                    ],
                    [
                        "BBS",
                        2000
                    ],
					[
                        "EST",
						0
                    ],
                    [
                        "KMB",
                        0
                    ],
					[
                        "RAM",
						0
                    ],
                    [
                        "STLI",
                        3000
                    ]
                    
                ]
            }
			
			
		]
    }
});
	
}



</script>

<!-- start sidebar menu -->
<div class="sidebar-container">
  <?=$sidebar;?>
</div>
<!-- end sidebar menu -->
<!-- start page content -->
<div class="page-content-wrapper">
  <div class="page-content">
 
	<div class="row">
	
						<div class="col-lg-6 col-md-6 col-sm-6 col-6">
							<div class="card card-box" >
									<div class="card-head">
	                                  <header>PRODUCTION <?php echo date("d-m-Y");?></header>
	                                  <div class="tools">
	                                    <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
										<a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
										<a class="t-close btn-color fa fa-times" href="javascript:;"></a>
	                                 </div>
									</div>
									
									<div class="card-body no-padding height-9">
										<figure class="highcharts-figure">
											<div id="container3"></div>
											
										</figure>
									</div>
								
	                          </div>
				        </div>
						
						<div class="col-lg-6 col-md-6 col-sm-6 col-6">
							<div class="card card-box" >
									<div class="card-head">
	                                  <header>CONTRACTOR <?php echo date("d-m-Y");?></header>
	                                  <div class="tools">
	                                    <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
										<a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
										<a class="t-close btn-color fa fa-times" href="javascript:;"></a>
	                                 </div>
									</div>
									
									<div class="card-body no-padding height-9">
										<figure class="highcharts-figure">
											<div id="container4"></div>
											
										</figure>
									</div>
								
	                          </div>
				        </div>
						
						<div class="col-lg-6 col-md-6 col-sm-8 col-8">
							<div class="card card-box" >
									<div class="card-head">
	                                  <header>PRODUCTION DAILY</header>
	                                  <div class="tools">
	                                    <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
										<a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
										<a class="t-close btn-color fa fa-times" href="javascript:;"></a>
	                                 </div>
									</div>
									
									<div class="card-body no-padding height-9">
										<figure class="highcharts-figure">
											<div id="container"></div>
											<p class="highcharts-description">
												
											</p>
										</figure>
									</div>
								
	                          </div>
				        </div>
						
						
						<div class="col-lg-6 col-md-6 col-sm-8 col-8">
							<div class="card card-box" >
									<div class="card-head">
	                                  <header>CONTRACTOR DAILY</header>
	                                  <div class="tools">
	                                    <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
										<a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
										<a class="t-close btn-color fa fa-times" href="javascript:;"></a>
	                                 </div>
									</div>
									
									<div class="card-body no-padding height-9">
										<figure class="highcharts-figure">
											<div id="container2"></div>
											
										</figure>
									</div>
								
	                          </div>
				        </div>
						
					
	</div>				
  <!-- end page content -->

</div>
<!-- end page container -->

