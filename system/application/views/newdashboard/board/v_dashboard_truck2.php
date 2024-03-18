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

function getTruckBoard(){
	Highcharts.chart('container', {
    chart: {
        type: 'bar'
    },
    title: {
        text: 'DASHBOARD TRUCK (<?php echo date("d-m-Y H:i:s");?>)'
    },
    subtitle: {
        text: ''
    },
    xAxis: {
        categories: ['RBT', 'BKA', 'GECL', 'MKS', 'MMS','BBS','EST','KMB','RAM','STLI'],
        title: {
            text: null
        }
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Total Unit',
            align: 'high'
        },
        labels: {
            overflow: 'justify'
        }
    },
    tooltip: {
        valueSuffix: ' unit'
    },
    plotOptions: {
        bar: {
            dataLabels: {
                enabled: true
            }
        }
    },
    legend: {
        layout: 'vertical',
        align: 'right',
        verticalAlign: 'top',
        x: -40,
        y: 80,
        floating: true,
        borderWidth: 1,
        backgroundColor:
            Highcharts.defaultOptions.legend.backgroundColor || '#FFFFFF',
        shadow: true
    },
    credits: {
        enabled: false
    },
    series: [{
        name: 'Hauling',
        data: [30, 15, 23, 15, 13, 18, 21, 22, 17, 20]
    }, {
        name: 'WS/Pool',
        data: [10, 4, 6, 8, 0, 7, 9, 3, 1, 0]
    }, {
        name: 'Others',
        data: [7, 3, 3, 4, 5, 7, 8, 4, 1, 4]
    }
	
	
	]
});

}

function getTruckRit(){
	Highcharts.chart('container2', {
    chart: {
        type: 'bar'
    },
    title: {
        text: 'RITASE BOARD (<?php echo date("d-m-Y");?>)'
    },
    subtitle: {
        text: ''
    },
    xAxis: {
        categories: ['RBT', 'BKA', 'GECL', 'MKS', 'MMS','BBS','EST','KMB','RAM','STLI'],
        title: {
            text: null
        }
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Total Unit',
            align: 'high'
        },
        labels: {
            overflow: 'justify'
        }
    },
    tooltip: {
        valueSuffix: ' Total'
    },
    plotOptions: {
        bar: {
            dataLabels: {
                enabled: true
            }
        }
    },
    legend: {
        layout: 'vertical',
        align: 'right',
        verticalAlign: 'top',
        x: -40,
        y: 80,
        floating: true,
        borderWidth: 1,
        backgroundColor:
            Highcharts.defaultOptions.legend.backgroundColor || '#FFFFFF',
        shadow: true
    },
    credits: {
        enabled: false
    },
    series: [{
        name: 'Tonnage',
        data: [48*30, 27*30, 36*30, 28*30, 15*30, 40*30, 13*30, 26*30, 8*30, 23*30]
    }, {
        name: 'Ritase',
        data: [48, 27, 36, 28, 15, 40, 13, 26, 8, 23]
    }
	
	
	]
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
	
						<div class="col-lg-12 col-md-12 col-sm-12 col-12">
							<div class="card card-box" >
									<div class="card-head">
	                                  <header>DASHBOARD TRUCK</header>
	                                  <div class="tools">
	                                    <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
										<a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
										<a class="t-close btn-color fa fa-times" href="javascript:;"></a>
	                                 </div>
									</div>
									
									<div class="card-body no-padding height-9">
										<figure class="highcharts-figure">
											<div id="container"></div>
											
										</figure>
									</div>
								
	                          </div>
				        </div>
						<div class="col-lg-12 col-md-12 col-sm-12 col-12">
							<div class="card card-box" >
									<div class="card-head">
	                                  <header>TONNAGE/RITASE</header>
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

