<script src="<?php echo base_url();?>assets/dashboard/assets/plugins/jquery/jquery.min.js"></script>
<script>
	  $(document).ready(function() { 
	 
	  getAllOverspeed();
	  getAllOverspeed_byCompany();
	  getAllOverspeed_byStreet();
	  getAllOverspeed_byHourly();
	  
	  
	  });
</script>



<script>

function add(accumulator, a) {
	  return accumulator + a;
}

function getAllOverspeed()
{
	
	var content_level1 =  '<?php echo json_encode($content_all_overspeed); ?>';
	var content_level1_fix = JSON.parse(content_level1);
	
	/* var content_level2 =  '<?php echo json_encode($content_all_overspeed_bycompany_level); ?>';
	var content_level2_fix = JSON.parse(content_level2); */
	//console.log(content_level1_fix);
	//console.log(content_level2_fix);
	
	// Create the chart
	Highcharts.chart('container_spd_all', {
    chart: {
        type: 'column'
    },
    title: {
        text: '<h4>Total</h4>'
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
            text: 'Total Overspeed'
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
            name: "",
            colorByPoint: true,
            data: [
                content_level1_fix
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
                name: "Overspeed",
                id: "level",
                data: 
					//content_level2_fix
					
				
				[
			
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
				
			
				
				
            }
			
		]
    }
});
	
}

function getAllOverspeed_byCompany()
{
	
	var content_contractor_level1 =  '<?php echo json_encode($content_all_overspeed_bycontractor); ?>';
	var content_contractor_level1_fix = JSON.parse(content_contractor_level1);
	
	//var a = 0;
	var totala = 0;
	
	for(let i = 0; i < content_contractor_level1_fix.length; i++){
		var a = content_contractor_level1_fix[i]['y'];
		
		totala = totala + a;
		
	  //console.log(content_contractor_level1_fix[i]['y']);
	  
	}
	//console.log(content_contractor_level1_fix);
	//console.log(totala);
	
	// Create the chart
	Highcharts.chart('container_spd_all_contractor', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'Contractor'
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
            text: 'Total : '+totala
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
            name: "",
            colorByPoint: true,
          
			data: 
			
                content_contractor_level1_fix
            
			
			
               
            
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
                name: "level2",
                id: "contractor2",
                data: 
					//content_level2_fix
				[
			
				
				
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
            }
			
		]
    }
});
	
}

function getAllOverspeed_byStreet()
{
	
	var content_street_level1 =  '<?php echo json_encode($content_all_overspeed_bystreet); ?>';
	var content_street_level1_fix = JSON.parse(content_street_level1);
	
	//var a = 0;
	var totala = 0;
	
	for(let i = 0; i < content_street_level1_fix.length; i++){
		var a = content_street_level1_fix[i]['y'];
		
		totala = totala + a;
		
	  //console.log(content_contractor_level1_fix[i]['y']);
	  
	}
	//console.log(content_street_level1_fix);
	//console.log(totala);
	
	// Create the chart
	Highcharts.chart('container_spd_all_street', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'Lokasi'
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
            text: 'Total : '+totala
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
            name: "",
            colorByPoint: true,
          
			data: 
			
                content_street_level1_fix
            
            
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
                name: "level2",
                id: "contractor2",
                data: 
					//content_level2_fix
				[
			
				
				
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
            }
			
		]
    }
});
	
}

function getAllOverspeed_byHourly()
{
	var content_all_overspeed_byhourly  = '<?php echo json_encode($content_all_overspeed_byhourly); ?>';
	var content_all_overspeed_byhourly_fix = JSON.parse(content_all_overspeed_byhourly);
	var content_all_overspeed_byhourly2 = '<?php echo json_encode($content_all_overspeed_byhourly2); ?>';
	var content_all_overspeed_byhourly2_fix = JSON.parse(content_all_overspeed_byhourly2);
	
	//console.log(content_all_overspeed_byhourly_fix);
	//console.log(content_all_overspeed_byhourly2_fix);
	
	const sum = content_all_overspeed_byhourly2_fix.reduce(add, 0); // with initial value to avoid when the array is empty

	
	//console.log(sum);
	
	Highcharts.chart('container_spd_all_hourly', {
    chart: {
        type: 'line'
    },
    title: {
        text: 'Jam Pelanggaran'
    },
    subtitle: {
        text: ''
    },
    xAxis: {
        //categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
		 categories: content_all_overspeed_byhourly_fix
    },
    yAxis: {
        title: {
            text: 'Total : '+sum
        }
    },
    plotOptions: {
        line: {
            dataLabels: {
                enabled: true
            },
            enableMouseTracking: false
        }
    },
    series: [{
        name: 'Overspeed',
        data: content_all_overspeed_byhourly2_fix
		//data: [7.0, 6.9, 9.5, 14.5, 18.4, 21.5, 25.2, 26.5, 23.3, 18.3, 13.9, 9.6]
    } 
	/*
	{
        name: 'Fatigue',
        data: [39, 6, 13, 13, 20, 17, 17, 32, 16, 13, 12, 12, 13, 16, 19, 17, 34, 23, 20, 18, 12, 22, 17, 18]
    }
	*/
	
	]
});
	

}

</script>


<div class="row" >						
				<!-- result -->
				
		
						<div class="col-lg-4 col-md-4 col-sm-3 col-3" style="bottom:10px">
							<figure class="highcharts-figure">
								<div id="container_spd_all"></div>
							</figure>
						</div>
						
			
						<div class="col-lg-4 col-md-4 col-sm-3 col-3" style="bottom:10px">
							<figure class="highcharts-figure">
								<div id="container_spd_all_contractor"></div>
							</figure>
						</div>		
	
						<div class="col-lg-4 col-md-4 col-sm-3 col-3" style="bottom:13px">
							<figure class="highcharts-figure">
								<div id="container_spd_all_hourly"></div>
							</figure>
						</div>
						<div class="col-lg-4 col-md-4 col-sm-3 col-3" style="bottom:13px">
							<figure class="highcharts-figure">
								<div id="container_spd_all_street"></div>
							</figure>
						</div>		