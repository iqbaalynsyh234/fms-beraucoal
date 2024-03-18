<?php 

Highcharts.chart('container', {
  chart: {
    type: 'spline'
  },
  title: {
    text: 'DASHBOARD TRUE-FALSE ALARM'
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

  colors: ['#74bf43', '#221f1f', '#06C', '#036', '#74bf43'],

  // Define the data points. All series have a year of 1970/71 in order
  // to be compared on the same x axis. Note that in JavaScript, months start
  // at 0 for January, 1 for February etc.
  series: [{
    name: '<b>True Alarm</b>',
    data: [
      [Date.UTC(2023, 6, 1), 66],
      [Date.UTC(2023, 6, 2), 68],
      [Date.UTC(2023, 6, 3), 74],
      [Date.UTC(2023, 6, 4), 79],
	  [Date.UTC(2023, 6, 5), 80],
      [Date.UTC(2023, 6, 6), 77],
      [Date.UTC(2023, 6, 7), 76],
      [Date.UTC(2023, 6, 8), 75],
	  [Date.UTC(2023, 6, 9), 78],
      [Date.UTC(2023, 6, 10), 73],
      [Date.UTC(2023, 6, 11), 79],
      [Date.UTC(2023, 6, 12), 82],
	  [Date.UTC(2023, 6, 13), 76],
      [Date.UTC(2023, 6, 14), 77],
      [Date.UTC(2023, 6, 15), 82],
      [Date.UTC(2023, 6, 16), 78],
	  [Date.UTC(2023, 6, 17), 73],
      [Date.UTC(2023, 6, 18), 78],
      [Date.UTC(2023, 6, 19), 83],
      [Date.UTC(2023, 6, 20), 85],
	  [Date.UTC(2023, 6, 21), 78],
      [Date.UTC(2023, 6, 22), 86],
      [Date.UTC(2023, 6, 23), 84],
      [Date.UTC(2023, 6, 24), 83],
	  [Date.UTC(2023, 6, 25), 78],
	  [Date.UTC(2023, 6, 26), 75],
      [Date.UTC(2023, 6, 27), 83],
      [Date.UTC(2023, 6, 28), 81],
      [Date.UTC(2023, 6, 29), 78],
	  [Date.UTC(2023, 6, 30), 83]
	  
      
    ]
  }, {
    name: '<b>False Alarm</b>',
    data: [
	  [Date.UTC(2023, 6, 1), 100-66],
      [Date.UTC(2023, 6, 2), 100-68],
      [Date.UTC(2023, 6, 3), 100-74],
      [Date.UTC(2023, 6, 4), 100-79],
	  [Date.UTC(2023, 6, 5), 100-80],
      [Date.UTC(2023, 6, 6), 100-77],
      [Date.UTC(2023, 6, 7), 100-76],
      [Date.UTC(2023, 6, 8), 100-75],
	  [Date.UTC(2023, 6, 9), 100-78],
      [Date.UTC(2023, 6, 10), 100-73],
      [Date.UTC(2023, 6, 11), 100-79],
      [Date.UTC(2023, 6, 12), 100-82],
	  [Date.UTC(2023, 6, 13), 100-76],
      [Date.UTC(2023, 6, 14), 100-77],
      [Date.UTC(2023, 6, 15), 100-82],
      [Date.UTC(2023, 6, 16), 100-78],
	  [Date.UTC(2023, 6, 17), 100-73],
      [Date.UTC(2023, 6, 18), 100-78],
      [Date.UTC(2023, 6, 19), 100-83],
      [Date.UTC(2023, 6, 20), 100-85],
	  [Date.UTC(2023, 6, 21), 100-78],
      [Date.UTC(2023, 6, 22), 100-86],
      [Date.UTC(2023, 6, 23), 100-84],
      [Date.UTC(2023, 6, 24), 100-83],
	  [Date.UTC(2023, 6, 25), 100-78],
	  [Date.UTC(2023, 6, 26), 100-75],
      [Date.UTC(2023, 6, 27), 100-83],
      [Date.UTC(2023, 6, 28), 100-81],
      [Date.UTC(2023, 6, 29), 100-78],
	  [Date.UTC(2023, 6, 30), 100-83]
      
    ]
  }
  ]
});