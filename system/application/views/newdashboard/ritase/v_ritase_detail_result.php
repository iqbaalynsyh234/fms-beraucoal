<style media="screen">
    #result{
        background-color: #221f1f;
        color: white;
    }
</style>


<script src="<?php echo base_url(); ?>assets/js/jsblong/jquery.table2excel.js"></script>
<script>
    jQuery(document).ready(
        function() {
            jQuery("#export_xcel").click(function() {
                window.open('data:application/vnd.ms-excel,' + encodeURIComponent(jQuery('#isexport_xcel').html()));
            });
        }
    );
</script>


<div class="col-lg-6 col-sm-6">
    <input id="btn_hide_form" class="btn btn-circle btn-danger" title="" type="button" value="Hide Form" onclick="javascript:return option_form('hide')" />
    <input id="btn_show_form" class="btn btn-circle btn-success" title="" type="button" value="Show Form" onClick="javascript:return option_form('show')" style="display:none" />
</div>
<div class="col-lg-2 col-sm-2">
</div>
<br />

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="panel">

            <tr>
                <td style="text-align:center;"><small>Periode: <?php echo $startdate . " - " . $enddate; ?> </small></td>
            </tr>

            <header class="panel-heading" id="result">RESULT</header>
            <div class="panel-body" id="bar-parent10">
                <div class="row">
                    <?php if (count($data) == 0) {
                        echo "<p>No Data</p>";
                    } else { ?>
                        <div class="col-md-12 col-sm-12">

                            <div class="col-lg-4 col-sm-4">
                                <a href="javascript:void(0);" id="export_xcel" type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect m-b-10 btn-circle btn-default"><small>Export to Excel</small></a>
                            </div>

                            <div id="isexport_xcel">
                                <table class="table table-striped custom-table table-hover">
                                    <thead>
                                        <tr>
                                            <th style="text-align:center;" width="3%">No</th>
                                            <th style="text-align:center;" width="7%">Truck Name</th>
                                            <th style="text-align:center;" width="5%">IDSimper</th>
                                            <th style="text-align:center;" width="5%">Driver Name</th>
                                            <th style="text-align:center;" width="10%">Location Start</th>
                                            <th style="text-align:center;" width="15%">Start Date</th>
                                            <th style="text-align:center;" width="10%">Start Time</th>
                                            <th style="text-align:center;" width="10%">Location End</th>
                                            <th style="text-align:center;" width="15%">End Date</th>
                                            <th style="text-align:center;" width="10%">End Time</th>
                                            <th style="text-align:center;" width="10%">Duration</th>
                                            <th style="text-align:center;" width="10%">Tonase</th>
                                            <!--<th style="text-align:center;" width="5%">Duration in Geofence</th>
										<th style="text-align:center;" width="5%">Total Fuel Cons(L)</th>
										<th style="text-align:center;" width="5%">Total OFF in Road</th>
										<th style="text-align:center;" width="5%">Total ON in Road</th>
										<th style="text-align:center;" width="5%">Total IDLE in Road</th>-->
                                            <th style="text-align:center;" width="5%">Total Distance GPS</th>
                                            <th style="text-align:center;" width="5%">Total Distance BIB</th>
                                        </tr>
                                    </thead>
                                    <tbody>


                                        <?php
                                        if (isset($data) && (count($data) > 0)) {

                                            for ($i = 0; $i < count($data); $i++) {
                                                if ($data[$i]->ritase_report_type == "muatan") {

                                                    $tonase_value = 30;
                                                } else {
                                                    $tonase_value = 0;
                                                }
                                        ?>
                                                <tr>
                                                    <td style="text-align:center;font-size:12px;"><?php echo $i + 1; ?></td>
                                                    <td style="text-align:center;font-size:12px;"><?php echo $data[$i]->ritase_report_vehicle_no; ?></td>
                                                    <td style="text-align:center;font-size:12px;"><?php echo $data[$i]->ritase_report_driver; ?></td>
                                                    <td style="text-align:center;font-size:12px;"><?php echo $data[$i]->ritase_report_driver_name; ?></td>
                                                    <td style="text-align:center;font-size:12px;"><?php echo strtoupper($data[$i]->ritase_report_start_location); ?></td>
                                                    <td style="text-align:center;font-size:12px;"><?php echo date("d-m-Y", strtotime($data[$i]->ritase_report_start_time)); ?></td>
                                                    <td style="text-align:center;font-size:12px;"><?php echo date("H:i:s", strtotime($data[$i]->ritase_report_start_time)); ?></td>
                                                    <td style="text-align:center;font-size:12px;"><?php echo strtoupper($data[$i]->ritase_report_end_location); ?></td>
                                                    <td style="text-align:center;font-size:12px;"><?php echo date("d-m-Y", strtotime($data[$i]->ritase_report_end_time)); ?></td>
                                                    <td style="text-align:center;font-size:12px;"><?php echo date("H:i:s", strtotime($data[$i]->ritase_report_end_time)); ?></td>
                                                    <!--<td style="text-align:center;font-size:12px;"><?php echo round($data[$i]->ritase_report_geofence_in_duration_sec / 60); ?></td>
					<td style="text-align:center;font-size:12px;"><?php echo $data[$i]->ritase_report_total_fuel; ?></td>
					<td style="text-align:center;font-size:12px;"><?php echo round($data[$i]->ritase_report_off_in_road_sec / 60); ?></td>
					<td style="text-align:center;font-size:12px;"><?php echo round($data[$i]->ritase_report_on_in_road_sec / 60); ?></td>
					<td style="text-align:center;font-size:12px;"><?php echo round($data[$i]->ritase_report_idle_in_road_sec / 60); ?></td>-->
                                                    <td style="text-align:center;font-size:12px;"><?php echo $data[$i]->ritase_report_duration_all_text; ?></td>
                                                    <td style="text-align:center;font-size:12px;"><?php echo $tonase_value; ?></td>
                                                    <td style="text-align:center;font-size:12px;"><?php echo round($data[$i]->ritase_report_odometer / 1000, 3); ?></td>
                                                    <td style="text-align:center;font-size:12px;"><?php echo round($data[$i]->ritase_report_odometer_master / 1000, 3); ?></td>
                                                </tr>
                                            <?php
                                            }
                                        } else {
                                            ?>
                                            <tr>
                                                <td colspan="10">No Available Data</td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>

                                </table>
                            </div>
                        </div>

                    <?php } ?>

                </div>
            </div>
        </div>
    </div>
</div>