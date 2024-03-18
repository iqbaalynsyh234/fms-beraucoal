<script src="<?php echo base_url();?>assets/dashboard/assets/plugins/jquery/jquery.min.js"></script>
<script>
	  $(document).ready(function() { 
	 
	  //getSelViolation();
	  getAllViolation_byType();
	
	  });
</script>



<script>

function add(accumulator, a) {
	  return accumulator + a;
}

function getSelViolation()
{
	
	var content_level1 =  '<?php echo json_encode($content_selected_violation); ?>';
	var content_level1_fix = JSON.parse(content_level1);
	
	/* var content_level2 =  '<?php echo json_encode($content_all_overspeed_bycompany_level); ?>';
	var content_level2_fix = JSON.parse(content_level2); */
	//console.log(content_level1_fix);
	//console.log(content_level2_fix);
	
	// Create the chart
	Highcharts.chart('container_select_violation', {
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
            text: 'Total'
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
                name: "violation",
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

function getAllViolation_byType()
{
	
	var content_level1 =  '<?php echo json_encode($content_all_violation_bytype); ?>';
	var content_level1_fix = JSON.parse(content_level1);
	
	//var a = 0;
	var totala = 0;
	
	for(let i = 0; i < content_level1_fix.length; i++){
		var a = content_level1_fix[i]['y'];
		
		totala = totala + a;
		
	  //console.log(content_contractor_level1_fix[i]['y']);
	  
	}
	//console.log(content_contractor_level1_fix);
	//console.log(totala);
	
	// Create the chart
	Highcharts.chart('content_all_violation_bytype', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'Violation'
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
			
                content_level1_fix
            
			
			
               
            
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

</script>


<div class="row" >						
				<!-- result -->
				
		
						<div class="col-lg-12 col-md-12 col-sm-3 col-3" style="bottom:10px">
							<figure class="highcharts-figure">
								<div id="content_all_violation_bytype"></div>
							</figure>
						</div>
						
			
						
							