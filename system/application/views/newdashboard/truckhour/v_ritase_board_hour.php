<script src="<?php echo base_url(); ?>assets/dashboard/assets/plugins/jquery/jquery.min.js"></script>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/data.js"></script>
<script src="https://code.highcharts.com/modules/drilldown.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<style type="text/css">
  /* edit style datepicker*/
  .datetimepicker {
    background: #4bb036;
  }

  .prev,
  .switch,
  .next,
  .today {
    background: #FFF;
  }

  #dashboard{
    background-color: #221f1f;
    color: white;
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



  /* edit Style graphic */
  .highcharts-data-label text {
    text-decoration: none;
  }

  .highcharts-drilldown-axis-label {
    pointer-events: none;
  }
</style>
<div class="sidebar-container">
  <?= $sidebar; ?>
</div>

<div class="page-content-wrapper">
  <div class="page-content">
    <br>
    <?php if ($this->session->flashdata('notif')) { ?>
      <div class="alert alert-success" id="notifnya" style="display: none;"><?php echo $this->session->flashdata('notif'); ?></div>
    <?php } ?>
    <!--<div class="alert alert-success" id="notifnya2" style="display: none;"></div>-->
    <div class="col-md-12">
      <div class="panel" id="panel_form">
        <header class="panel-heading panel-heading-red" id="dashboard">DASHBOARD RITASE</header>
        <div class="panel-body" id="bar-parent10">

          <div class="row">
            <div class="input-group date form_date col-md-2 col-sm-5" data-date="" data-date-format="dd-mm-yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
              <input class="form-control" type="text" readonly name="date" id="startdate" value="<?= date('d-m-Y') ?>" onchange="dtdatechange()">
              <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
            </div>
            <div class="col-md-2 col-sm-5">
              <input type="hidden" id="total_vehicle" name="total_vehicle" value="">
              <select id="shift" name="shift" class="form-control select2" onchange="dtdatechange()">
                <option value="0">-- All Shift --</option>
                <option value="1">Shift 1</option>
                <option value="2">Shift 2</option>
              </select>
            </div>

            <div class="col-md-2 col-sm-5">
              <select id="viewdata" name="viewdata" class="form-control select2" onchange="viewchange()">
                <option value="1">Graph view</option>
                <!--<option value="0">Table view</option>-->
              </select>
            </div>

            <div class="col-md-2 col-sm-5">
              <select class="form-control select2" id="company" name="company" onchange="dtdatechange()">
                <option value="0" selected>--All Contractor</option>
                <?php
                $ccompany = count($rcompany);
                for ($i = 0; $i < $ccompany; $i++) {

                  echo "<option value='" . $rcompany[$i]->company_id . "'>" . $rcompany[$i]->company_name . "</option>";
                }
                ?>
              </select>
            </div>

            <!--<div class="col-md-2 col-sm-5">
              <select id="location" name="location" class="form-control select2" onchange="dtdatechange()">
                <option value="0">-- All Port</option>
                <?php $nr = count($rlocation);

                for ($z = 0; $z < $nr; $z++) {
                  if ($rlocation[$z]->street_alias != "") {
                    if ($rlocation[$z]->street_alias != "PORT BBC") {
                      echo "<option value=\"" . $rlocation[$z]->street_alias . "\">" . $rlocation[$z]->street_alias . " </option>";
                    }
                  }
                } ?>
              </select>
            </div>-->
			
			<div class="col-md-2 col-sm-5">
              <select id="location" name="location" class="form-control select2" onchange="dtdatechange()">
                <option value="0">-- All Port</option>
				<option value="BIB_GROUP">PORT BIB</option>
				<option value="BIR_GROUP">PORT BIR</option>
                <option value="TIA_GROUP">PORT TIA</option>
              </select>
            </div>

            <?php
            //  echo "<pre>";
            // var_dump($rcompany);
            // var_dump($rlocation);
            // echo "</pre>"; 
            ?>

            <div class="col-md-1 col-sm-3">
              <button type="button" name="button" id="export_xcel" class="btn btn-primary btn-sm" style="display:none">Export Excel</button>
            </div>

          </div>
          <div class="row">
            <div class="col">
              <!-- loader -->
              <div id="loader" style="display: none;" class="mdl-progress mdl-js-progress mdl-progress__indeterminate is-upgraded" data-upgraded=",MaterialProgress">
                <div class="progressbar bar bar1" style="width: 0%;"></div>
                <div class="bufferbar bar bar2" style="width: 100%;"></div>
                <div class="auxbar bar bar3" style="width: 0%;"></div>
              </div>
              <!-- end loader -->

            </div>
          </div>

          <div class="row">
            <div id="valueHidden" style="display: none;"></div>
            <div class="col-md-12" id="viewtable" style="display:none;">
              <div id="isexport_xcel">
                <div class="row">
                  <div class="col-md-6">
                    <b id="exceltitle"></b>
                  </div>
                  <div class="col-md-6">
                    <p id="totalan" style="text-align:right;"></p>
                  </div>
                </div>
                <table class="table table-striped table-bordered" id="content" style="font-size: 14px; overflow-y:auto;">
                </table>
              </div>
            </div>
            <!-- start graphic -->
            <div class="col-lg-12 col-md-12 col-sm-12" id="viewgraphic" style="bottom:13px;">

              <div class="row">
                <div class="col-md" id="graphic_caption">

                </div>
              </div>
              <br>
              <figure class="highcharts-figure">
                <div id="container_graphic"></div>
              </figure>
            </div>
            <!-- end graphic -->


          </div>

        </div>
      </div>
    </div>
  </div>


  <script type="text/javascript" src="js/script.js"></script>
  <script src="<?php echo base_url() ?>assets/dashboard/assets/js/jquery-1.7.1.min.js" type="text/javascript"></script>

  <script type="text/javascript">
    function dtdatechange() {
      $("#loader").show();
      var company = $("#company").val();
      var location = $("#location").val();
      var table_loc_name = $("#location").val();

      var date = $("#startdate").val();
      var shift = $("#shift").val();
      var shiftname = "";
      if (shift == 0) {
        shiftname = "Semua Shift";
      } else if (shift == 1) {
        shiftname = "Shift 1";
      } else {
        shiftname = "Shift 2";
      }
      var data = {
        date: date,
        shift: shift,
        company: company,
        location: location
      };
      $("#totalan").html("");
      $("#content").html("");
      $("#container_graphic").html("");
      $("#exceltitle").html("");
      $("#valueHidden").html("");
      jQuery.post("<?php echo base_url() ?>ritasehour/search_board", data, function(response) {
       // console.log(response);
        $("#loader").hide();

        if (response.error) {
          console.log(response);
          alert(response.msg);
        } else {
          if (company == 0) {
			//utama
            charttrilevel(response.data_fix, response.data_company, response.data_hour, response.data_location, response.length_company, response.length_hour, response.total_unit_location, response.total_unit, response.total_unit_per_contractor, location, date, company, response.length_data);
          } else {
			  //filter company
            charttrilevelbycompany(response.data_fix, response.data_company, response.data_hour, response.data_location, response.length_company, response.length_hour, response.total_unit_location, response.total_unit, response.total_unit_per_contractor, location, date, company, response.length_data);

          }
          createTable(response, table_loc_name);
        }


      }, "json");

    }

    function viewchange() {
      var view = $("#viewdata").val();
      if (view == 0) {
        $("#viewtable").show();
        $("#export_xcel").show();
        $("#viewgraphic").hide();

      } else if (view == 1) {
        $("#viewtable").hide();
        $("#export_xcel").hide();
        $("#viewgraphic").show();

      }
    }

    $(document).ready(function() {
      //edit datepicker
      $(".glyphicon-arrow-right").html(">>");
      $(".glyphicon-arrow-left").html("<<");



      dtdatechange();
      jQuery("#export_xcel").click(function() {
        window.open('data:application/vnd.ms-excel,' + encodeURIComponent(jQuery('#isexport_xcel').html()));
      });

    });


    var chart;

    function charttrilevel(data_fix, data_company, data_hour, data_location, length_company, length_hour, total_unit_location, total_unit, total_unit_per_contractor, location, date, company, length_data) {
      var data = data_fix;
	  var length_data = length_data;
	  var data_company = data_company;
      var data_location = data_location;
      var data_hour = data_hour;
      var length_company = length_company;
      var length_hour = length_hour;
      var total_unit_location = total_unit_location;
      var total_unit = total_unit;
      var total_unit_per_contractor = total_unit_per_contractor;
      var location = location;
      var company_selected = company;
      var categories2 = [];
      var datalvl0 = [];
      var datalvl1 = [];
      var datalvl2 = [];
      var datalevel0 = [];
      var datalevel1 = [];
      var datalevel2 = [];
      var totallevel0 = 0;
      var totallevel1 = 0;
      var totallevel2 = 0;
      var y1 = 0;
      var a = 0;
      var b = 0;
      var c = 0;
      var n = 0;
      var k = 0;
      var dstat = 0;

		//console.log(data);
		
      var plotlin = -1;
      var maks = null;
      /* if (location == 0) {
        plotlin = 0.8 * total_unit;
        plotlin = Math.round(plotlin);
        maks = Math.round(total_unit / 100) * 100;
      } */


      for (i = 0; i < data_hour.length; i++) {
        n = 0;
        totallevel1 = 0;
        var pr = Object.keys(data[data_hour[i]]);
        var html = "";
        for (j = 0; j < pr.length; j++) {

          dtlvl2 = data[data_hour[i]][pr[j]]; 
		  y1 = Object.keys(dtlvl2).length;
		
          totallevel2 = 0;
          datalvl2 = [];
          for (k = 0; k < y1; k++) {
            datalvl2.push(dtlvl2[k]['vehicle']);
            totallevel2++;
          }
		 
          html += "<textarea id=\"" + data_hour[i] + pr[j] + "\">" + datalvl2.sort() + "</textarea>";

          datalvl1.push({
            y: totallevel2

          });
          n = totallevel1 + totallevel2;
          totallevel1 = n;
        }
        /* for (h = 0; h < pr.length; h++) {
          pr[h] += "<br>" + total_unit_per_contractor[pr[h]];
        } */

        datalvl0.push({
          y: totallevel1,
          z: data_hour[i],
          color: "#4bb036",
          drilldown: {
            //name: "Contractor <b style'display:none'>" + data_hour[i] + "</b>",
			name: "PORT <b style'display:none'>" + data_hour[i] + "</b>",
            color: "#4bb036",
            categories: pr,
            level: 1,
            data: datalvl1
          }
		  
		 
        });
        datalvl1 = [];
        k = totallevel0 + totallevel1;
        totallevel0 = k;
        html_old = $("#valueHidden").html();
        html_old += html;

        $("#valueHidden").html(html_old);

      }

      var plotLine = {
        id: 'y-axis-plot-line-0',
        color: '#4bb036',
        width: 3,
        value: plotlin,
        dashStyle: 'shortdash',
        label: {
          text: '30%'
        }
      }


      var colors = Highcharts.getOptions().colors,
        categories = data_hour,

        name = 'Hour',
        level = 0,
        data = datalvl0;
		
      chart = new Highcharts.Chart({
        chart: {
          renderTo: 'container_graphic',
          type: 'column',
          events: {
            drilldown: function() {
              this.yAxis[0].removePlotLine('y-axis-plot-line-0');
              var cs = this.series[0];
              if (cs != undefined) {
                this.yAxis[0].update({
                  max: null
                });
              }
              dstat = 0;
            },
            redraw: function() {
              var cs = this.series[0];
              if (cs != undefined) {
                lvl = this.series[0].options.level;
                if (lvl == 0) {
                  //this.yAxis[0].addPlotLine(plotLine);
				  this.yAxis[0].removePlotLine(plotLine);
				  
                  dstat++;
                  if (dstat == 2) {
                    this.yAxis[0].update({
                      max: maks
                    });
                  }
                }
              }
            }

          }
        },
        title: {
          useHTML: true,
          text: ''
        },
        subtitle: {
          text: 'Dashboard  Ritase ' + date
        },
        accessibility: {
          announceNewData: {
            enabled: true
          }
        },
        xAxis: {
          categories: categories,
          title: {
            text: 'Hour'
          }
        },
        yAxis: {
          title: {
            text: 'Total Ritase'
          },
          max: maks,
          plotLines: [plotLine]
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
                    setChart(drilldown.name, drilldown.categories, drilldown.data, drilldown.color, drilldown.level);
                  } else { // restore
                    setChart(name, categories, data, null, level);
                  }

                }
              }
            },
            dataLabels: {
              enabled: true,
              formatter: function() {
                var persen = 0;
                var outputlabel = "";
                switch (this.series.options.level) {
                  case 0:

                    if (location == 0) {
                      persen = (this.y / total_unit) * 100;
                      persen = Math.round(persen);
                      //title = "Total Unit All Contractor : " + total_unit;
					  title = "Total Ritase: " + length_data;

                      //outputlabel = this.y + " unit <br>" + persen + "%";
					  outputlabel = this.y + " Rit";
                    } else {
                      outputlabel = this.y + " Rit";
                     
                        title = "Total Ritase di " + location + " : " + length_data;
                      
                    }




                    $(".highcharts-xaxis text").html("Hour");
                    this.series.chart.setTitle({
                      text: title
                    });
                    break;
                  case 1:
                    var name = this.series.options.name;
                    h = name.split("e'>");
                    hour = h[1].split("</");
                    $(".highcharts-xaxis text").html(name);
                    this.series.chart.setTitle({
                      text: "Hour : " + hour[0]
                    });
                    outputlabel = this.y + " Rit";
                    xs = this.x;
                    xs = String(xs);
                    xslength = xs.search("<br>");
                    if (xslength > 1) {
                      tu = xs.split("<br>");
                      totalUnit = parseInt(tu[1]);
                      pcn = (this.y / totalUnit) * 100;
                      persen = Math.round(pcn);
                      //outputlabel += "<br>" + persen + "%";
                    }

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
            var point = this.point,
              s = '';

            switch (this.series.options.level) {
              case 0:
                s = 'Hour: ' + key + ' <br/>';
                s += y + ' Ritase';
                break;

              case 1:
                s += y + ' unit<br>';
                if (key.length > 2) {
                  exp = key.split("<br>");
                  key = exp[0];
                  name = this.series.name;
                  exp = name.split("e'>");
                  name = exp[1].split("<");

                  idn = name[0] + key;
                  units = $("#" + idn).html();
                  //newunits = units.replace(/,/g, " - ");
                  s += key;
                }
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

    function charttrilevelbycompany(data_fix, data_company, data_hour, data_location, length_company, length_hour, total_unit_location, total_unit, total_unit_per_contractor, location, date, company, length_data) {
      var data = data_fix;
	  var length_data = length_data;
	  var data_company = data_company;
      var data_location = data_location;
      var data_hour = data_hour;
      var length_company = length_company;
      var length_hour = length_hour;
      var total_unit_location = total_unit_location;
      var total_unit = total_unit;
      var total_unit_per_contractor = total_unit_per_contractor;
      var location = location;
      var company_selected = company;
      var categories2 = [];
      var datalvl0 = [];
      var datalvl1 = [];
      var datalvl2 = [];
      var datalevel0 = [];
      var datalevel1 = [];
      var datalevel2 = [];
      var totallevel0 = 0;
      var totallevel1 = 0;
      var totallevel2 = 0;
      var y1 = 0;
      var a = 0;
      var b = 0;
      var c = 0;
      var n = 0;
      var k = 0;
      var dstat = 0;
      var html = "";

      var plotlin = -1;
      var maks = null;
     /*  if (location == 0) {
        plotlin = 0.8 * total_unit;
        plotlin = Math.round(plotlin);
        maks = total_unit;
      } */


      for (i = 0; i < data_hour.length; i++) {
        n = 0;
        totallevel1 = 0;
        var pr = Object.keys(data[data_hour[i]]);

        for (j = 0; j < pr.length; j++) {
          dtlvl2 = data[data_hour[i]][pr[j]];
          y1 = Object.keys(dtlvl2).length;
          totallevel2 = 0;
          categories2 = [];
          datalvl2 = [];
          var lvllocation = "";
          if (location == 0) {
            lvllocation = "All Location";
          } else if (location == "STREET.0") {
            lvllocation = "Hauling Kosongan";
          } else if (location == "STREET.1") {
            lvllocation = "Hauling Muatan";
          }
          categories2[0] = lvllocation;
          for (k = 0; k < y1; k++) {
            datalvl2.push(dtlvl2[k]['vehicle']);
            totallevel2++;
          }
          datalevel2 = {
            level: 2,
            z: data_hour[i],
            color: "#4bb036",
            name: "Lokasi <b style'display:none'>" + data_hour[i] + "<b>",
            categories: categories2,
            data: datalvl2
          };

          datalvl1.push({
            y: totallevel2,
            z: data_hour[i],
          });
          n = totallevel1 + totallevel2;
          totallevel1 = n;

        }
        html = "<textarea id=\"" + data_hour[i] + "\">" + datalvl2.sort() + "</textarea>";
        for (h = 0; h < pr.length; h++) {
          pr[h] += "<br>" + total_unit;
        }

        datalvl0.push({
          y: totallevel1,
          color: "#4bb036",
        });
        datalvl1 = [];
        k = totallevel0 + totallevel1;
        totallevel0 = k;
        html_old = $("#valueHidden").html();
        html_old += html;
        $("#valueHidden").html(html_old);

      }


      var plotLine = {
        id: 'y-axis-plot-line-0',
        color: '#4bb036',
        width: 3,
        value: plotlin,
        dashStyle: 'shortdash',
        label: {
          text: '30%'
        }
      }


      var colors = Highcharts.getOptions().colors,
        categories = data_hour,
        name = 'Hour',
        level = 0,
        data = datalvl0;
      chart = new Highcharts.Chart({
        chart: {
          renderTo: 'container_graphic',
          type: 'column',
          events: {
            drilldown: function() {
              this.yAxis[0].removePlotLine('y-axis-plot-line-0');
              var cs = this.series[0];
              if (cs != undefined) {
                this.yAxis[0].update({
                  max: null
                });
              }
            },
            redraw: function() {
              var cs = this.series[0];
              if (cs != undefined) {
                lvl = this.series[0].options.level;
                if (lvl == 0) {
                 // this.yAxis[0].addPlotLine(plotLine);
				  this.yAxis[0].removePlotLine(plotLine);
                }
              }
            }

          }
        },
        title: {
          useHTML: true,
          text: ''
        },
        subtitle: {
          text: 'Dashboard Ritase ' + date
        },
        accessibility: {
          announceNewData: {
            enabled: true
          }
        },
        xAxis: {
          categories: categories,
          title: {
            text: 'Hour'
          }
        },
        yAxis: {
          title: {
            text: 'Total Ritase'
          },
          max: maks,
          plotLines: [plotLine]
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
                var persen = 0;
                var outputlabel = "";
                switch (this.series.options.level) {
                  case 0:
                    if (location == 0) {
                      persen = (this.y / total_unit) * 100;
                      persen = Math.round(persen);
                      //title = "Total Unit " + data_company[0] + " : " + total_unit;
					  title = "Total Ritase " + data_company[0] + " : " + length_data;
					  
                      //outputlabel = this.y + " unit <br>" + persen + "%";
					  outputlabel = this.y + " Rit";
                    } else {
                      outputlabel = this.y + " Rit";
                    
                        title = "Total Ritase " + data_company[0] + " di " + location + " : " + length_data;
                      
                    }



                    $(".highcharts-xaxis text").html(this.series.options.name);
                    this.series.chart.setTitle({
                      text: title
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
            var point = this.point,
              s = '';

            switch (this.series.options.level) {
              case 0:
                s = 'Hour: ' + key + ' <br/>';
                s += y + ' Ritase <br>';
                units = $("#" + key).html();
                newunits = units.replace(/,/g, " - ");
                s += newunits;
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
        }]
        // exporting: {
        //   enabled: false
        // }
      });
    }

    function setChart(name, categories, data, color, level) {
      chart.xAxis[0].setCategories(categories);
      chart.series[0].remove();


      chart.addSeries({
        name: name,
        data: data,
        level: level,
        color: "#4bb036"
      });


    }

    function createTable(response, table_loc_name) {
      var length_company = response.length_company;
      var length_hour = response.length_hour;
      var data_company = response.data_company;
      var data_hour = response.data_hour;
      var total_unit = response.total_unit;
      var total_unit_per_contractor = response.total_unit_per_contractor;
      var data = response.data_fix;
      var totalin = 0;
      var totalan = 0;
      var cek = "";
      var date = $("#startdate").val();
      var shift = $("#shift").val();
      var shiftname = "";
      if (shift == 0) {
        shiftname = "Semua Shift";
      } else if (shift == 1) {
        shiftname = "Shift 1";
      } else {
        shiftname = "Shift 2";
      }
      var html = "";
      var locname = table_loc_name;
      if (locname == "STREET.0") {
        table_loc_name = " HAULING Jalur Kosongan";
      } else if (locname == "STREET.1") {
        table_loc_name = " HAULING Jalur Muatan";
      } else if (locname == 0) {
        table_loc_name = " Semua Port";
      } else {
        table_loc_name = " " + locname;
      }
      html += `<thead><tr>
                      <th style="text-align:center;">Hour</th>`;
      if (length_company == 1) {
        //html += '<th style="text-align:center;">' + data_company[0] + '<br><span>' + total_unit + '</span></th>';
		html += '<th style="text-align:center;">' + data_company[0] + '</th>';
      } else {
        for (var i = 0; i < length_company; i++) {
          //html += '<th style="text-align:center;">' + data_company[i] + '<br><span>' + total_unit_per_contractor[data_company[i]] + '</span></th>';
		  html += '<th style="text-align:center;">' + data_company[i] + '</th>';
        }
      }
      html += `   <th style="text-align:center;">Total</th>
                  </tr></thead><tbody>`;
      for (var j = 0; j < length_hour; j++) {
        n = 0;
        total = 0;
        html += `<tr><th style="text-align:center;">` + data_hour[j] + `</th>`;

        for (var k = 0; k < length_company; k++) {
          cek = data[data_hour[j]][data_company[k]];
          if (cek == undefined) {

            html += `<td style="text-align:center;"></td>`;
          } else {
            pr = Object.keys(data[data_hour[j]][data_company[k]]).length;
            if (length_company == 1) {
              persen = (pr / total_unit) * 100;
            } else {
              persen = (pr / total_unit_per_contractor[data_company[k]]) * 100;

            }
            persen = Math.round(persen);
            if (persen < 80) {
              color = "red";
            } else {
              color = "blue";
            }
            //html += `<td style="text-align:center;">` + pr + ` <span style="color:` + color + `">(` + persen + `%)</span> </td>`;
			html += `<td style="text-align:center;">` + pr + `</td>`;
            x = n + pr;
            n = x;
          }
        }
        totalin = totalan + n;
        totalan = totalin;
        html += `<th style="text-align:center;">` + n + `</th></tr>`;
      }
      totalavg = totalan / length_hour;
      totalavg = Math.round(totalavg);
      html += `</tbody>`;
      $("#content").html(html);
      $("#totalan").html(`Total Unit Rata-rata ` + shiftname + ` : <b>` + totalavg + ` &nbsp; </b>`);
      $("#exceltitle").html("Total Ritase Per Jam pada tanggal " + date + " " + table_loc_name);
      return false;
    }
  </script>