<script src="<?php echo base_url(); ?>assets/dashboard/assets/plugins/jquery/jquery.min.js"></script>
<script src="<?php echo base_url(); ?>assets/dashboard/assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url(); ?>assets/dashboard/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.js"></script>
<link rel="stylesheet" href="<?php base_url(); ?>assets/dashboard/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css">


<div class="col-12">
    <div class="panel">
        <header class="panel-heading panel-heading-red">Result <?= $periode; ?>
            <button type="button" name="button" id="export_xcel" class="btn btn-primary btn-xs" style="font-size: 11px;">Export Excel</button>
        </header>
        <div class="panel-body row">
            <div class="col-12" id="is_export_excel">
                <table class="table table-striped table-bordered table-hover table-sm" style="font-size: 12px; width: 100%; text-align:center;">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Vehicle</th>
                            <th>Start time</th>
                            <th>End time</th>
                            <th>Duration</th>
                            <th>Geofence</th>
                        </tr>
                    </thead>
                    <tbody id="table_list">
                        <?php for ($i = 0; $i < count($data); $i++) { ?>
                            <tr>
                                <td><?= $i + 1; ?></td>
                                <td><?= $data[$i]['georeport_vehicle_no']; ?></td>
                                <td><?= $data[$i]['georeport_starttime']; ?></td>
                                <td><?= $data[$i]['georeport_endtime']; ?></td>
                                <td><?= $data[$i]['georeport_duration_text']; ?></td>
                                <td><?= $data[$i]['georeport_geofence']; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <!-- <p>Total <b id="total_detail"></b> entries</p> -->
            </div>
        </div>
    </div>
</div>
<style>
    /* .paginate_button {
        width: 10px;
        height: 10px;
        margin-right: 24px;
    } */
</style>
<script>
    $(document).ready(function() {

        // collect_data();
        $('#myTable').DataTable({
            // "scrollX": true
            // order: [
            //     [0, "desc"]
            // ],
            // info: false,
            aoColumnDefs: [{
                    "bSortable": false,
                    "aTargets": [0, 1, 2, 3]
                }
                // {
                //     "bSearchable": false,
                //     "aTargets": [0, 1, 2, 3]
                // }
            ],
            language: {
                paginate: {
                    previous: "<b><</b>",
                    next: "<b>></b>"
                },
                lengthMenu: "Show _MENU_"
            }
        });
        // $('#tablecontent').DataTable({
        //     // "scrollX": true
        //     // info: false,
        //     // searching: false,
        //     // order: [
        //     //     [0, "desc"]
        //     // ],
        //     aoColumnDefs: [{
        //             "bSortable": false,
        //             "aTargets": [0, 1, 2]
        //         }
        //         // {
        //         //     "bSearchable": false,
        //         //     "aTargets": [0, 1, 2]
        //         // }
        //     ],
        //     language: {
        //         paginate: {
        //             previous: "<b><</b>",
        //             next: "<b>></b>"
        //         },
        //         lengthMenu: "Show _MENU_"
        //     }
        // });
        // $(".col-md-5").addClass("col-md-12");
        // $(".col-md-5").removeClass("col-md-5");
        // $(".col-md-5").hide();
        jQuery("#export_xcel").click(function() {
            var myBlob = new Blob([jQuery('#is_export_excel').html()], {
                type: 'application/vnd.ms-excel'
            });
            var url = window.URL.createObjectURL(myBlob);
            var a = document.createElement("a");
            document.body.appendChild(a);
            a.href = url;
            a.download = "geo_report.xls";
            a.click();
            //adding some delay in removing the dynamically created link solved the problem in FireFox
            setTimeout(function() {
                window.URL.revokeObjectURL(url);
            }, 0);

        });

    });

    // function collect_data() {
    //     var check = '<?php //echo json_encode($data); 
                        ?>';
    //     var data = JSON.parse(check);
    //     check = '<?php //echo json_encode($master_company); 
                    ?>';
    //     var data_company = JSON.parse(check);
    //     var table_list = " ";

    //     var table_summary = " ";
    //     var data_summary = [];
    //     var dt = [];
    //     var max_rit = [];
    //     var dates = [];
    //     var lastdate = " ";
    //     var lastdt = " ";
    //     for (var i = 0; i < data.length; i++) {
    //         value = data[i]['kepmen_total_rit'];
    //         daten = data[i]['kepmen_date'];
    //         split = daten.split("-");
    //         date = split[2] + "-" + split[1] + "-" + split[0];
    //         table_list += `<tr>
    //                         <td>` + date + `</td>
    //                         <td>` + data_company[data[i]['kepmen_company_id']] + `</td>
    //                         <td>` + data[i]['kepmen_vehicle_no'] + `</td>
    //                         <td>` + value + `</td>
    //                         </tr>`;
    //         st = value.toString();
    //         if (i == 0) {
    //             dt[st] = 1;
    //             data_summary[date] = dt;
    //             lastdate = date;
    //             // lastdt = st;
    //         } else {
    //             // if (lastdate == date) {
    //             check = data_summary[date];
    //             if (check == undefined) {
    //                 dt = [];
    //                 dt[st] = 1;
    //                 data_summary[date] = dt;
    //                 // data_summary[date][st] = 1;
    //             } else {
    //                 check = data_summary[date][st];
    //                 if (check == undefined) {
    //                     data_summary[date][st] = 1;
    //                 } else {
    //                     data_summary[date][st] += 1;
    //                 }
    //             }
    //         }


    //     }
    //     // console.log(data_summary);
    //     sr = Object.keys(data_summary);
    //     // console.log(sr);
    //     for (i = 0; i < sr.length; i++) {
    //         rr = Object.keys(data_summary[sr[i]]);
    //         for (j = 0; j < rr.length; j++) {
    //             table_summary += `<tr>
    //                         <td>` + sr[i] + `</td>
    //                         <td>` + rr[j] + `</td>
    //                         <td>` + data_summary[sr[i]][rr[j]] + `</td>
    //                     </tr>`;
    //             // total_summary++;
    //         }
    //     }
    //     $("#table_list").html(table_list);
    //     $("#table_list_export").html(table_list);
    //     $("#table_summary").html(table_summary);
    //     $("#table_summary_export").html(table_summary);
    //     // $("#total_detail").html(total_detail);
    //     // $("#total_summary").html(total_summary);

    // }
</script>