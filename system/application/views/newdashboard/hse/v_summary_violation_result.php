<script src="<?php echo base_url(); ?>assets/dashboard/assets/plugins/jquery/jquery.min.js"></script>

<div class="row">

    <div class="col-lg-12 col-md-12 col-sm-12">
        <figure class="highcharts-figure" style="background-color: white; padding:2px;">
            <div id="loader2" style="display: none;" class="mdl-progress mdl-js-progress mdl-progress__indeterminate is-upgraded" data-upgraded=",MaterialProgress">
                <div class="progressbar bar bar1" style="width: 0%;"></div>
                <div class="bufferbar bar bar2" style="width: 100%;"></div>
                <div class="auxbar bar bar3" style="width: 0%;"></div>
            </div>
            <div id="container-summary">
                <div id="viewtable" style="height: 100%;">
                    <div id="isexport_xcel">
                        <div style="margin: 3px; padding:3px;">
                            <center><b id="exceltitle" style="font-size: 16px;">Dashboard Summary Alert (<?= $date; ?>)</b><br/>
                            <button type="button" name="button" id="export_xcel" class="btn btn-primary btn-xs" style="font-size: 12px;">Export Excel</button></center>
                        </div>
                        <style>
                            table {
                                width: 100%;
                            }

                            .tablecont {
                                font-family: 'Trebuchet MS';
                                border-collapse: collapse;
                                color: #333333;
                                font-size: 12px;
                            }

                            .tablecont thead {
                                background-color: #dddddd;
                            }

                            .tablecont td,
                            th {
                                border-bottom: 1px solid #dfdadd;
                                text-align: left;
                                padding: 6px 4px 6px 4px;
                            }

                            .tablecont tr:nth-child(even) {
                                background-color: #dddddd;
                            }
                        </style>
                        <!-- <table class="table table-striped table-bordered" id="tablecontent" style="font-size: 12px;"> -->
                        <table id="tablecontent" class="tablecont">

                        </table>
                    </div>
                </div>
            </div>
        </figure>
    </div>

    
</div>
<div class="row">
    <br>
    <br>
</div>


<script>
    $(document).ready(function() {

        collect_data();
        jQuery("#export_xcel").click(function() {
            var myBlob = new Blob([jQuery('#isexport_xcel').html()], {
                type: 'application/vnd.ms-excel'
            });
            var url = window.URL.createObjectURL(myBlob);
            var a = document.createElement("a");
            document.body.appendChild(a);
            a.href = url;
            a.download = "daily_violation_report_<?= $date; ?>.xls";
            a.click();
            //adding some delay in removing the dynamically created link solved the problem in FireFox
            setTimeout(function() {
                window.URL.revokeObjectURL(url);
            }, 0);

        });

     

    });

    //var chart;

    function collect_data() {
        var data_hour = '<?php echo json_encode($data_hour); ?>';
        var data_hour_fix = JSON.parse(data_hour);
        var data_company = '<?php echo json_encode($data_company); ?>';
        var data_company_fix = JSON.parse(data_company);
        var opposite_company = '<?php echo json_encode($opposite_company); ?>';
        var opposite_company_fix = JSON.parse(opposite_company);
        var data_violation = '<?php echo json_encode($data_alarm); ?>';
        var data_violation_fix = JSON.parse(data_violation);
        var total_data = <?= $total_data; ?>;
        var date = '<?= $date; ?>';
        var data = '<?php echo json_encode($data_fix); ?>';
        var data_fix = JSON.parse(data);

        var data_table = '<?php echo json_encode($data_table); ?>';
        var data_table_fix = JSON.parse(data_table);
        var master_company = '<?php echo json_encode($master_company); ?>';
        var master_company_fix = JSON.parse(master_company);
        var master_v = '<?php echo json_encode($master_vehicle); ?>';
        var master_vehicle = JSON.parse(master_v);
        var master_violation = '<?php echo json_encode($master_violation); ?>';
        var master_violation_fix = JSON.parse(master_violation);
        var master_v = '<?php echo json_encode($total_violation_units); ?>';
        var total_violation_units = JSON.parse(master_v);
        var master_v = '<?php echo json_encode($top_violation); ?>';
        var top_violation = JSON.parse(master_v);
		
		var master_r = '<?php echo json_encode($master_ritase); ?>';
        var master_ritase = JSON.parse(master_r);
		  
        setTimeout(function() {
            createTable(data_table_fix, data_hour_fix, data_company_fix, data_violation_fix, total_data, date, master_company_fix, master_violation_fix, opposite_company_fix, master_vehicle, total_violation_units, master_ritase);
        }, 0);


    }

	//char start here
   
	//chart end here
    function createTable(data_fix, data_hour_fix, data_company_fix, data_violation_fix, total_data, date, master_company, master_violation, opposite_company, master_vehicle, total_violation_units, master_ritase, total_operational_units = 0) {
        $("#tablecontent").html(" ");

        var html = '';
        if (total_data > 0) {
            html += '<thead><tr><th></th>';
            var total_per_col = [];
            var ratio = [];
			var ratio_ritase = [];
			
            var total_mc = master_company.length
            for (var i = 0; i < total_mc; i++) {
                 html += '<th>' + master_company[i] + '</th>'; //header
                //html += '<th><a href="#" onclick="showinfo(\'<?= $sdate; ?>\', \'<?= $edate; ?>\', \'' + opposite_company[master_company[i]] + '\',\'' + master_company[i] + '\', \'<?= $input['violation']; ?>\')"><b>' + master_company[i] + '</b></a></th>'; //header
                total_per_col[i] = 0;
                ratio[i] = 0;
				ratio_ritase[i] = 0;
            }
            <?php if ($input['company'] == "all") { ?>
                html += '<th>Total</th>';
            <?php } ?>
            html += '</tr></thead><tbody>';

            for (i = 0; i < master_violation.length; i++) {
                html += '<tr><th>' + master_violation[i] + '</th>';
                var total_per_row = 0;
                for (var j = 0; j < total_mc; j++) {
                    if (data_fix[master_violation[i]] != undefined) {
                        if (data_fix[master_violation[i]][master_company[j]] != undefined) {
                            df = data_fix[master_violation[i]][master_company[j]];
                            nr = df.length;
                            total_per_row += nr;
                            total_per_col[j] += nr;
                            html += '<th>' + nr + '</th>';
                        } else {
                            html += '<td></td>';
                        }
                    } else {
                        html += '<td></td>';
                    }
                }
                <?php if ($input['company'] == "all") { ?>
                    html += '<th>' + total_per_row + '</th>';
                <?php } ?>
                html += '</tr>';

            }


            //total violation
            <?php if ($input['violation'] == "all") { ?>
                html += '<tr><th>Total Violation</th>';
                for (var i = 0; i < total_mc; i++) {
                    html += '<th>' + total_per_col[i] + '</th>';
                }
                <?php if ($input['company'] == "all") { ?>
                    html += '<th>' + total_data + '</th>';
                <?php } ?>
                html += '</tr>';
            <?php } ?>


            colspan = total_mc + 1;
            total_dt = 0;
            html += '<tr><td colspan="' + colspan + '"><td></tr>';


            //total units
            html += '</tr><tr><th>Total Unit Contractor</th>';
            for (var i = 0; i < total_mc; i++) {
                dt_company = master_vehicle[opposite_company[master_company[i]]];
                html += '<th>' + dt_company + '</th>';
                total_dt += dt_company;
            }
            <?php if ($input['company'] == "all") { ?>
                html += '<th>' + total_dt + '<th>';
            <?php } ?>
            html += '</tr>';


         


            <?php if ($ratio) {               ?>
                
               total_dt = 0;
                html += '<tr><th>Total Unit Active</th>';
                for (var i = 0; i < total_mc; i++) {
                    var tot_vunit = total_violation_units[master_company[i]];
                    if (tot_vunit != undefined) {
                        total_dt += tot_vunit;

                        html += '<th>' + tot_vunit + '</th>';
                        ratio[i] = total_per_col[i] / tot_vunit;
                    } else {
                        ratio[i] = 0;
                        html += '<th></th>';

                    }

                }
                <?php if ($input['company'] == "all") {                     ?>
                    html += '<th>' + total_dt + '<th>';
                <?php }                    ?>
                html += '</tr>';
				
				
				total_rit = 0;
				/*html += '<tr><th>Total Ritase</th>';
                for (var i = 0; i < total_mc; i++) {
                    var tot_ritase = master_ritase[opposite_company[master_company[i]]];
                    if (tot_ritase != undefined) {
                        total_rit += tot_ritase;

                        html += '<th>' + tot_ritase + '</th>';
                        ratio_ritase[i] = total_per_col[i] / tot_ritase;
                    } else {
                        ratio[i] = 0;
                        html += '<th></th>';

                    }

                }
                <?php if ($input['company'] == "all") {                     ?>
                    html += '<th>' + total_rit + '<th>';
                <?php }                    ?>
                html += '</tr>'; */
				
				
				//ratio ritase
				/*html += '</tr><tr><th>Violation Ratio Per Ritase</th>';
				for (var i = 0; i < total_mc; i++) {
					html += '<th>' + ratio_ritase[i].toFixed(1) + '</th>';
				}
				<?php if ($input['company'] == "all") { ?>
					html += '<th><th>';
				<?php } ?>
				html += '</tr>'; */
				
				
                //Ration Violation
                html += '<tr><th>Violation Ratio Per Unit</th>';
                for (var i = 0; i < total_mc; i++) {
                    html += '<th>' + ratio[i].toFixed(1) + '</th>';

                }

                <?php if ($input['company'] == "all") {                     ?>

                    html += '<th></th>'
                <?php }                    ?>
                html += '</tr>'

            <?php }                ?>
            html += '</tbody>';
            $("#tablecontent").html(html);
        }
    }
</script>