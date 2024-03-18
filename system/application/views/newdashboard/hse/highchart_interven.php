<?php 

Highcharts.chart('container', {
  chart: {
    type: 'spline'
  },
  title: {
    text: 'DASHBOARD INTERVENSI'
  },
  subtitle: {
    text: 'PERIODE 2023-07-01 s/d 2023-07-30'
  },
  xAxis: {
    type: 'datetime',
    dateTimeLabelFormats: { // don't display the year
      month: '%e. %b',
      year: '%b'
    },
    title: {
      text: 'Periode (day)'
    }
  },
  yAxis: {
    title: {
      text: 'Percentage (%)'
    },
    min: 0
  },
  tooltip: {
    headerFormat: '<b>{series.name}</b><br>',
    pointFormat: '{point.x:%e. %b}: {point.y:.2f} %'
  },

  plotOptions: {
    series: {
      marker: {
        enabled: true,
        radius: 5
      }
    }
  },

  colors: ['#221f1f', '#74bf43', '#06C', '#036', '#74bf43'],

  // Define the data points. All series have a year of 1970/71 in order
  // to be compared on the same x axis. Note that in JavaScript, months start
  // at 0 for January, 1 for February etc.
  series: [{
    name: '<b>Not Intervened</b>',
    data: [
     
	  [Date.UTC(2023, 6, 1), 81],
	  [Date.UTC(2023, 6, 2), 79],
      [Date.UTC(2023, 6, 3), 73],
      [Date.UTC(2023, 6, 4), 78],
	  [Date.UTC(2023, 6, 5), 75],
      [Date.UTC(2023, 6, 6), 83],
      [Date.UTC(2023, 6, 7), 78],
	  [Date.UTC(2023, 6, 8), 83],
	  [Date.UTC(2023, 6, 9), 80],
      [Date.UTC(2023, 6, 10), 77],
      [Date.UTC(2023, 6, 11), 76],
      [Date.UTC(2023, 6, 12), 75],
      [Date.UTC(2023, 6, 13), 82],
	  [Date.UTC(2023, 6, 14), 76],
      [Date.UTC(2023, 6, 15), 77],
	  [Date.UTC(2023, 6, 16), 84],
      [Date.UTC(2023, 6, 17), 83],
	  [Date.UTC(2023, 6, 18), 78],
      [Date.UTC(2023, 6, 19), 82],
      [Date.UTC(2023, 6, 20), 66],
      [Date.UTC(2023, 6, 21), 68],
      [Date.UTC(2023, 6, 22), 74],
      [Date.UTC(2023, 6, 23), 79],
	  [Date.UTC(2023, 6, 24), 78],
	  [Date.UTC(2023, 6, 25), 73],
      [Date.UTC(2023, 6, 26), 78],
      [Date.UTC(2023, 6, 27), 83],
      [Date.UTC(2023, 6, 28), 85],
	  [Date.UTC(2023, 6, 29), 78],
      [Date.UTC(2023, 6, 30), 86]
      
	  
      
    ]
  }, {
    name: '<b>Intervened</b>',
    data: [
	  [Date.UTC(2023, 6, 1), 100-81],
	  [Date.UTC(2023, 6, 2), 100-79],
      [Date.UTC(2023, 6, 3), 100-73],
      [Date.UTC(2023, 6, 4), 100-78],
	  [Date.UTC(2023, 6, 5), 100-75],
      [Date.UTC(2023, 6, 6), 100-83],
      [Date.UTC(2023, 6, 7), 100-78],
	  [Date.UTC(2023, 6, 8), 100-83],
	  [Date.UTC(2023, 6, 9), 100-80],
      [Date.UTC(2023, 6, 10), 100-77],
      [Date.UTC(2023, 6, 11), 100-76],
      [Date.UTC(2023, 6, 12), 100-75],
      [Date.UTC(2023, 6, 13), 100-82],
	  [Date.UTC(2023, 6, 14), 100-76],
      [Date.UTC(2023, 6, 15), 100-77],
	  [Date.UTC(2023, 6, 16), 100-84],
      [Date.UTC(2023, 6, 17), 100-83],
	  [Date.UTC(2023, 6, 18), 100-78],
      [Date.UTC(2023, 6, 19), 100-82],
      [Date.UTC(2023, 6, 20), 100-66],
      [Date.UTC(2023, 6, 21), 100-68],
      [Date.UTC(2023, 6, 22), 100-74],
      [Date.UTC(2023, 6, 23), 100-79],
	  [Date.UTC(2023, 6, 24), 100-78],
	  [Date.UTC(2023, 6, 25), 100-73],
      [Date.UTC(2023, 6, 26), 100-78],
      [Date.UTC(2023, 6, 27), 100-83],
      [Date.UTC(2023, 6, 28), 100-85],
	  [Date.UTC(2023, 6, 29), 100-78],
      [Date.UTC(2023, 6, 30), 100-86]
      
    ]
  }
  ]
});