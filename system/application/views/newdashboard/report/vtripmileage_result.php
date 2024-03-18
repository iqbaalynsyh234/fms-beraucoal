<script src="<?php echo base_url();?>assets/js/jsblong/jquery.table2excel.js"></script>
<script>
jQuery(document).ready(
		function()
		{
			jQuery("#export_xcel").click(function()
			{
				window.open('data:application/vnd.ms-excel,' + encodeURIComponent(jQuery('#isexport_xcel').html()));
			});
		}
	);


</script>


							<div class="col-lg-6 col-sm-6">
								<input id="btn_hide_form" class="btn btn-circle btn-danger" title="" type="button" value="Hide Form" onclick="javascript:return option_form('hide')" />
								<input id="btn_show_form" class="btn btn-circle btn-success" title="" type="button" value="Show Form" onClick="javascript:return option_form('show')" style="display:none"/>
							</div>
							<div class="col-lg-2 col-sm-2">
							</div>
							<br />

<div class="row">
	<div class="col-md-12 col-sm-12">
		<div class="panel">
			<header class="panel-heading panel-heading-blue">REPORT</header>
				<div class="panel-body" id="bar-parent10">
					<div class="row">
					<?php if (count($data) == 0) {
							echo "<p>No Data</p>";
					}else{ ?>
						<div class="col-md-12 col-sm-12">

							<div class="col-lg-4 col-sm-4">
								<a href="javascript:void(0);" id="export_xcel" type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect m-b-10 btn-circle btn-info"><small>Export to Excel</small></a>
							</div>

							<div id="isexport_xcel">
							<table class="table table-striped custom-table table-hover">
								<thead>
									<tr>
										<th valign="top" style="text-align:center;"  width="2%" >No</th>
										<th valign="top" style="text-align:center;"  width="7%" >Vehicle</th>
										<th valign="top" style="text-align:center;"  width="2%">Trip No</td>
										<th valign="top" style="text-align:center;"  width="5%">Start Time</td>
										<th valign="top" style="text-align:center;"  width="5%">End Time</th>
										<th valign="top" style="text-align:center;"  width="5%">Duration</th>
										<th valign="top" style="text-align:center;"  width="5%">Trip Mileage</th>
										<th valign="top" style="text-align:left;"  width="5%">Cumulative Mileage</th>
										<th valign="top" style="text-align:left;"  width="10%">Location Start</th>
										<th valign="top" style="text-align:left;"  width="10%" >Location End</th>
									</tr>
								</thead>
								<tbody>
	<?php
		if(isset($data) && (count($data) > 0)){
				$j=1;
				$new = "";
				foreach($data as $vehicle_no=>$val)
				{
					if($new != $vehicle_no){
						$cumm = 0;
						$trip_no = 1;
					}
					foreach($val as $no=>$report){

						$mileage = $report['end_mileage']- $report['start_mileage'];
						if($mileage != 0){

						$duration = get_time_difference($report['start_time'], $report['end_time']);
						$show = "";
						if($duration[0]!=0){
							$show .= $duration[0] ." Day ";
							}
								if($duration[1]!=0){
								$show .= $duration[1] ." Hour ";
								}
									if($duration[2]!=0){
									$show .= $duration[2] ." Min ";
									}
										if($show == ""){
										$show .= "0 Min";
										}
									if ($show != "0 Min")
									{
				?>
			<table class="table table-striped custom-table table-hover">
            <tr>
                <td valign="top" width="2%"><?=$j?></td>
								<td valign="top" style="text-align:center; font-size: 12px;" width="7%">
									<?=$vehicle_no;?> <br>
									<?=$report['vehicle_name'];?>
								</td>
								<td valign="top" style="text-align:center; font-size: 12px;" width="2%">
									<?=$trip_no++;?>
								</td>
								<td valign="top" style="text-align:center; font-size: 12px;" width="5%">
									<?=$report['start_time'];?>
								</td>
								<td valign="top" style="text-align:center; font-size: 12px;" width="5%">
									<?=$report['end_time'];?>
								</td>
				<td valign="top" style="text-align:center; font-size: 12px;" width="5%">
				<?php
                $duration = get_time_difference($report['start_time'], $report['end_time']);
                //print_r($duration);
                $show = "";
                if($duration[0]!=0){
                    $show .= $duration[0] ." Day ";
                    }
                    if($duration[1]!=0){
                        $show .= $duration[1] ." Hour ";
                        }
                        if($duration[2]!=0){
                            $show .= $duration[2] ." Min ";
                            }
							if($show == ""){
								$show .= "0 Min";
							}
							echo $show;
							?>
                </td>
				<td valign="top" style="text-align:center; font-size: 12px;" width="5%">
                <?php
                $tm = $mileage/1000;
                echo round($tm,2);
                ?> km
                </td>
				<td valign="top" style="text-align:center; font-size: 12px;" width="5%">
				<?php
				$cumm += $tm;
				echo round($cumm,2);
				?> km
				</td>
				<td valign="top" style="text-align:center;font-size:10px;" width="10%">
				<?php
				if ($report['start_geofence_location']) {
					$arrGeo = explode("#", $report['start_geofence_location']);
					if(count($arrGeo)>1){
						$geoname = $arrGeo[1];
					}else{
						$geoname = $arrGeo[0];
					}
					echo "<b><font color='red'>Geofence : " . strtoupper($geoname) . "</font></b><br/>";
				}
				?>

				<p style="text-align:center;font-size:10px;">
					<a target="_blank" href="http://maps.google.com/maps?q=<?=$report['start_latitude'].",".$report['start_longitude'];?>">
						<strong><?=$report['start_position']->display_name;?></strong>
					</a>
				</p>
				</td>
				<td valign="top" style="text-align:center;font-size:10px;" width="10%">
				<?php
				if ($report['end_geofence_location']) {
					$arrGeoEnd = explode("#", $report['end_geofence_location']);
					if(count($arrGeoEnd)>1){
						$geonameend = $arrGeoEnd[1];
					}else{
						$geonameend = $arrGeoEnd[0];
					}
					echo "<b><font color='red'>Geofence : " . strtoupper($geonameend) . "</font></b><br/>";

				}
				?>

				<p style="text-align:center;font-size:10px;">
					<a target="_blank" href="http://maps.google.com/maps?q=<?=$report['end_latitude'].",".$report['end_longitude'];?>">
						<strong><?=$report['end_position']->display_name;?></strong>
					</a>
				</p>


				</td>
            </tr>
            <?php
            $j++;
            }}}}

		}else{
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
