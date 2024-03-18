<script src="<?php echo base_url();?>assets/js/jsblong/jquery.table2excel.js"></script>
<script>
jQuery.maxZIndex = jQuery.fn.maxZIndex = function(opt) {
	    var def = { inc: 10, group: "*" };
	    jQuery.extend(def, opt);
	    var zmax = 0;
	    jQuery(def.group).each(function() {
	        var cur = parseInt(jQuery(this).css('z-index'));
	        zmax = cur > zmax ? cur : zmax;
	    });
	    if (!this.jquery)
	        return zmax;
	
	    return this.each(function() {
	        zmax += def.inc;
	        jQuery(this).css("z-index", zmax);
	    });
	}
	
jQuery(document).ready(
		function()
		{
			jQuery("#export_xcel").click(function() 
			{ 
				window.open('data:application/vnd.ms-excel,' + encodeURIComponent(jQuery('#isexport_xcel').html()));
			});
		}
	);
	
function frmadd_onsubmit()
	{
		
		jQuery("#loader_save").show();
		jQuery("#button_save").hide();
		jQuery.post("<?=base_url()?>report_otl_otd/save", jQuery("#frmadd").serialize(),	
			function(r)
			{
				jQuery("#loader_save").hide();
				jQuery("#button_save").show();
				if (r.error)
				{
					alert(r.message);
					return false;
				}
				
				alert(r.message);
				page(0);
			}
			, "json"
		);
		return false;
	}
</script>
<p>
<a class="button" href="javascript:void(0);" id="export_xcel" align="right" style="text-align:right;">Export to Excel</a>
<a class="button" href="<?=base_url()?>balrich_report/mn_histdoorstatus" target="_blank">View Door Report</a>
<a class="button" href="<?=base_url()?>balrich_report/mn_dataoperasional" target="_blank">View Operasional Report</a>
</p>
<div id="isexport_xcel">
<h3>Periode <?php echo date('d-m-Y', strtotime($sdate));?> - <?php echo date('d-m-Y', strtotime($edate));?></h3>
<h3><?=$plant_name->plant_name;?></h3>

<?php 
$this->dbtransporter = $this->load->database("transporter", true);
?>
<form id="frmadd" onsubmit="javascript: return frmadd_onsubmit()">	
<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<thead>
				<tr>
					<th style="text-align:center">Tanggal</th>
					<th style="text-align:center">Plant</th>
					<th style="text-align:center">Transporter</th>
					<th style="text-align:center">LOT</th>
					<th style="text-align:center">Distrep</th>
					<th style="text-align:center">No.Pol</th>
					<th style="text-align:center">Driver</th>
					<th style="text-align:center">Target OTL</th>
					<th style="text-align:center">Actual OTL</th>
					<th style="text-align:center">Ach.OTL</th>
					<th style="text-align:center">Ach.OTL %</th>
					<th style="text-align:center">Start Loading</th>
					<th style="text-align:center">Finish Loading</th>
					
					<th style="text-align:center">Target OTD</th>
					<th style="text-align:center">Actual OTD</th>
					<th style="text-align:center">Ach.OTD</th>
					<th style="text-align:center">Ach.OTD %</th>
					<th style="text-align:center">Unloading Time</th>
					<th style="text-align:center">Start KM</th>
					<th style="text-align:center">Finish KM</th>
					<th style="text-align:center">Distance (KM)</th>
				</tr>
			</thead>
			<tbody>
			<?php
			if(count($data) > 0){
				
				$nowdate = date("Y-m-d");
				$m = date("m");
				$Y = date("Y");
				$d = "1";
				$firstdate = date("Y-m-d", strtotime($Y."-".$m."-".$d));
				
			?>
			<?php 
			for($i=0;$i<count($data);$i++)
			{
				$total_dis = 0;
				$distrep_report_type = "";
				$search_level = "";
				$on_search_level = 0;
			?>
				<?php //compare distrep dan config report //berdasarkan mobil yg disetting di distrep
					if($data[$i]->loading_otd_distrep > 0){
						$this->dbtransporter->select("distrep_name,distrep_report_status,distrep_vehicle_device");
						$this->dbtransporter->where("distrep_id",$data[$i]->loading_otd_distrep);
						$this->dbtransporter->where("distrep_vehicle_device",$data[$i]->loading_vehicle_device);
						$this->dbtransporter->where("distrep_flag",0);
						$qdis = $this->dbtransporter->get("droppoint_distrep");
						$row_dis = $qdis->row();
						$total_dis = count($row_dis);
						
						if(count($row_dis)> 0){
							$distrep_name = $row_dis->distrep_name;
							$distrep_report_type = $row_dis->distrep_report_status;
							$distrep_vehicle_device = $row_dis->distrep_vehicle_device;
							$search_level = "1";
						}else{
							$on_search_level = "2";
						}
						
						$this->dbtransporter->select("monthly_type");
						$this->dbtransporter->where("monthly_type",$distrep_report_type);
						$this->dbtransporter->where("monthly_date",$data[$i]->loading_report_date);
						$qconfig = $this->dbtransporter->get("config_monthly_report");
						$row_config = $qconfig->row();
						if(count($row_config)> 0){
							$report_date_status = $row_config->monthly_type;
						}else{
							$report_date_status = 0;
						}
					}?>
					
				<!-- Jika mobil dedicated ada datanya -->
				<?php if (($total_dis > 0) && ($search_level == 1) ){ 
					if($report_date_status == $distrep_report_type) {?>
					<tr>
						<td valign="top" align="center" style="text-align:center;"><?=date("d-m-Y", strtotime($data[$i]->loading_report_date));?></td>
						<td valign="top" align="center" style="text-align:center;"><?=$plant_name->plant_code;?></td>
						
						<td valign="top" align="center" style="text-align:center;">BALRICH</td>
						
						<td valign="top" align="center" style="text-align:center;"></td>
						
						<!-- distrep name-->
						<td valign="top" align="center" style="text-align:center;"><?php echo $distrep_name; ?></td>
						
						<!-- vehicle -->
						<td valign="top" align="center" style="text-align:center;"><?=$data[$i]->loading_vehicle_no;?></td>
						
						<!-- Driver -->
						<td valign="top" align="center" style="text-align:center;">
							 
						</td>
						
						<!-- Target OTL -->
						<td valign="top" align="center" style="text-align:center;">
						<?php
							$this->dbtransporter->select("target_loading_time");
							$this->dbtransporter->where("target_loading_type","OTL");
							$this->dbtransporter->where("target_loading_startdate >=",$firstdate);
							$this->dbtransporter->where("target_loading_enddate <=",date("Y-m-t"));
							$this->dbtransporter->where("target_loading_flag",0);
							$this->dbtransporter->where("target_loading_distrep",$data[$i]->loading_otd_distrep);
							$qotl = $this->dbtransporter->get("droppoint_target_loading");
							$row_otl = $qotl->row();
							if(count($row_otl)> 0){
								$otl_target = date("H:i",strtotime($row_otl->target_loading_time));
							}else{
								$otl_target = "";
							}
						?>
						
						<?=$otl_target;?>
						</td>
						
						<!-- actual OTL -->
						<td valign="top" align="center" style="text-align:center;">
							<?php $otl_actual = date("H:i", strtotime($data[$i]->loading_otl_arrival_time));?>
							<?php if($otl_actual > $otl_target){
								$otl_actual = "<font color ='red'>".$otl_actual."</font>";
							} ?>
							
							<?php if($data[$i]->loading_otl_base != ""){ 
								echo $otl_actual;
							}?> 
							
							<?php if($checkdetail == "1"){ ?>
								<br />
								<small>
									<b><?=$data[$i]->loading_otl_geofence_start;?> ~ <?=$data[$i]->loading_otl_geofence_end;?></b><br />
									(<?=$data[$i]->loading_otl_report_status;?>)<br />
									(Search Level: <?=$search_level;?>)
								</small>
							<?php }?>
						</td>
						
						<!-- Ach. OTL -->
						<td valign="top" align="center" style="text-align:center;">
							<?php if($otl_actual <= $otl_target){
									$ach_otl = 1;
							}else{
								$ach_otl = 0;
							} ?>
							<?php if($data[$i]->loading_otl_base != ""){ 
								echo $ach_otl;
							}?> 
						</td>
						
						<!-- Ach. OTL %-->
						<td valign="top" align="center" style="text-align:center;">
							<?php 
								$otl_actual_time = $otl_actual.":"."00";
								$otl_target_time = $otl_target.":"."00";
							
								$parsed_otl_actual = date_parse($otl_actual_time);
								$parsed_otl_target = date_parse($otl_target_time);
								$seconds_otl_actual = $parsed_otl_actual['hour'] * 3600 + $parsed_otl_actual['minute'] * 60 + $parsed_otl_actual['second'];
								$seconds_otl_target = $parsed_otl_target['hour'] * 3600 + $parsed_otl_target['minute'] * 60 + $parsed_otl_target['second'];
								
								if($seconds_otl_actual > 0){
									$ach_otl_persen = ($seconds_otl_target / $seconds_otl_actual) * 100;
								}else{
									$ach_otl_persen = "";
								}
							?>
								
							<?php if($data[$i]->loading_otl_base != ""){ 
								 $ach_otl_persen_round = round($ach_otl_persen, 0);
								 if($ach_otl_persen_round >= "100"){
									 echo "100";
								 }else{
									 echo $ach_otl_persen_round;
								 }
							}?> 
						</td>
						
						<!-- start finish loading -->
						<td valign="top" align="center" style="text-align:center;">
						<?php if($data[$i]->loading_otl_base != ""){ ?>
							<?=date("H:i", strtotime($data[$i]->loading_otl_start_time));?>
						<?php }?>
						</td>
						<td valign="top" align="center" style="text-align:center;">
						<?php if($data[$i]->loading_otl_base != ""){ ?>
							<?=date("H:i", strtotime($data[$i]->loading_otl_end_time));?>
						<?php } ?>
						</td>
						
						<!-- Target OTD -->
						<td valign="top" align="center" style="text-align:center;">
						<?php
							$this->dbtransporter->select("target_loading_time");
							$this->dbtransporter->where("target_loading_type","OTD");
							$this->dbtransporter->where("target_loading_startdate >=",$firstdate);
							$this->dbtransporter->where("target_loading_enddate <=",date("Y-m-t"));
							$this->dbtransporter->where("target_loading_flag",0);
							$this->dbtransporter->where("target_loading_distrep",$data[$i]->loading_otd_distrep);
							$qotd = $this->dbtransporter->get("droppoint_target_loading");
							$row_otd = $qotd->row();
							if(count($row_otd)> 0){
								$otd_target = date("H:i",strtotime($row_otd->target_loading_time));
							}else{
								$otd_target = "";
							}
						?>
						<?=$otd_target;?>
						</td>
						
						<!-- actual OTD -->
						<td valign="top" align="center" style="text-align:center;">
							<?php $otd_actual = date("H:i", strtotime($data[$i]->loading_otd_start_time));?>
							<?php if($otd_actual > $otd_target){
								$otd_actual = "<font color ='red'>".$otd_actual."</font>";
							} ?>
							<?php if($data[$i]->loading_otd_report_status != ""){ 
								echo $otd_actual;
							}?> 
							
							<?php if($checkdetail == "1"){ ?>
								<br /> <small>
								<b><?=$data[$i]->loading_otd_geofence_start;?> ~ <?=$data[$i]->loading_otd_geofence_end;?> </b><br />
								(<?=$data[$i]->loading_otd_report_status;?>)</small>
							<?php } ?>
						</td>
						
						<!-- Ach. OTD -->
						<td valign="top" align="center" style="text-align:center;">
							<?php if($otd_actual <= $otd_target){
									$ach_otd = 1;
							}else{
								$ach_otd = 0;
							} ?>
							<?=$ach_otd;?>
						</td>
						
						<!-- Ach. OTD %-->
						<td valign="top" align="center" style="text-align:center;">
							<?php 
								$otd_actual_time = $otd_actual.":"."00";
								$otd_target_time = $otd_target.":"."00";
							
								$parsed_otd_actual = date_parse($otd_actual_time);
								$parsed_otd_target = date_parse($otd_target_time);
								$seconds_otd_actual = $parsed_otd_actual['hour'] * 3600 + $parsed_otd_actual['minute'] * 60 + $parsed_otd_actual['second'];
								$seconds_otd_target = $parsed_otd_target['hour'] * 3600 + $parsed_otd_target['minute'] * 60 + $parsed_otd_target['second'];
								
								if($seconds_otd_actual > 0){
									$ach_otd_persen = ($seconds_otd_target / $seconds_otd_actual) * 100;
								}else{
									$ach_otd_persen = "";
								}
							?>
								
							<?php
								 $ach_otd_persen_round = round($ach_otd_persen, 0);
								 if($ach_otd_persen_round >= "100"){
									 echo "100";
								 }else{
									 echo $ach_otd_persen_round;
								 }
							?> 
							
						</td>
						
						<!-- unloading time-->
						<td valign="top" align="center" style="text-align:center;"></td>
						
						<!-- start km-->
						<td valign="top" align="center" style="text-align:center;">0</td>
						
						<!-- finish km-->
						<td valign="top" align="center" style="text-align:center;"><?=$data[$i]->loading_distance;?></td>
						
						<!-- distance-->
						<td valign="top" align="center" style="text-align:center;"><?=$data[$i]->loading_distance;?></td>
					</tr>
				<?php }} ?>
				
				<!-- mobil random jika kondisi pertama tidak ada -->
				<?php if (($total_dis == 0) && ($on_search_level == 2)){ ?>
					<?php if($data[$i]->loading_otd_distrep > 0){ 
					
						$this->dbtransporter->select("distrep_name,distrep_report_status,distrep_vehicle_device");
						$this->dbtransporter->where("distrep_id",$data[$i]->loading_otd_distrep);
						$this->dbtransporter->where("distrep_flag",0);
						$qdis = $this->dbtransporter->get("droppoint_distrep");
						$row_dis = $qdis->row();
						$total_dis = count($row_dis);
						
						if(count($row_dis)> 0){
							$distrep_name = $row_dis->distrep_name;
							$distrep_report_type = $row_dis->distrep_report_status;
							$search_level = "2";
						}
						
						$this->dbtransporter->select("monthly_type");
						$this->dbtransporter->where("monthly_type",$distrep_report_type);
						$this->dbtransporter->where("monthly_date",$data[$i]->loading_report_date);
						$qconfig = $this->dbtransporter->get("config_monthly_report");
						$row_config = $qconfig->row();
						if(count($row_config)> 0){
							$report_date_status = $row_config->monthly_type;
						}else{
							$report_date_status = 0;
						}
					} ?>
					<?php if($report_date_status == $distrep_report_type) { ?>
					<tr>
						<td valign="top" align="center" style="text-align:center;"><?=date("d-m-Y", strtotime($data[$i]->loading_report_date));?></td>
						<td valign="top" align="center" style="text-align:center;"><?=$plant_name->plant_code;?></td>
						
						<td valign="top" align="center" style="text-align:center;">BALRICH</td>
						
						<td valign="top" align="center" style="text-align:center;"></td>
						
						<!-- distrep name-->
						<td valign="top" align="center" style="text-align:center;"><?php echo $distrep_name; ?></td>
						
						<!-- vehicle -->
						<td valign="top" align="center" style="text-align:center;"><?=$data[$i]->loading_vehicle_no;?></td>
						
						<!-- Driver -->
						<td valign="top" align="center" style="text-align:center;">
							 
						</td>
						
						<!-- Target OTL -->
						<td valign="top" align="center" style="text-align:center;">
						<?php
							$this->dbtransporter->select("target_loading_time");
							$this->dbtransporter->where("target_loading_type","OTL");
							$this->dbtransporter->where("target_loading_startdate >=",$firstdate);
							$this->dbtransporter->where("target_loading_enddate <=",date("Y-m-t"));
							$this->dbtransporter->where("target_loading_flag",0);
							$this->dbtransporter->where("target_loading_distrep",$data[$i]->loading_otd_distrep);
							$qotl = $this->dbtransporter->get("droppoint_target_loading");
							$row_otl = $qotl->row();
							if(count($row_otl)> 0){
								$otl_target = date("H:i",strtotime($row_otl->target_loading_time));
							}else{
								$otl_target = "";
							}
						?>
						
						<?=$otl_target;?>
						</td>
						
						<!-- actual OTL -->
						<td valign="top" align="center" style="text-align:center;">
							<?php $otl_actual = date("H:i", strtotime($data[$i]->loading_otl_start_time));?>
							<?php if($otl_actual > $otl_target){
								$otl_actual = "<font color ='red'>".$otl_actual."</font>";
							} ?>
							
							<?php if($data[$i]->loading_otl_base != ""){ 
								echo $otl_actual;
							}?> 
							
							<?php if($checkdetail == "1"){ ?>
								<br />
								<small>
									<b><?=$data[$i]->loading_otl_geofence_start;?> ~ <?=$data[$i]->loading_otl_geofence_end;?> </b><br />
									(<?=$data[$i]->loading_otl_report_status;?>) <br />
									(Search Level: <?=$search_level;?>)
								</small>
							<?php }?>
						</td>
						
						<!-- Ach. OTL -->
						<td valign="top" align="center" style="text-align:center;">
							<?php if($otl_actual <= $otl_target){
									$ach_otl = 1;
							}else{
								$ach_otl = 0;
							} ?>
							<?php if($data[$i]->loading_otl_base != ""){ 
								echo $ach_otl;
							}?> 
						</td>
						
						<!-- Ach. OTL %-->
						<td valign="top" align="center" style="text-align:center;">
							<?php 
								$otl_actual_time = $otl_actual.":"."00";
								$otl_target_time = $otl_target.":"."00";
							
								$parsed_otl_actual = date_parse($otl_actual_time);
								$parsed_otl_target = date_parse($otl_target_time);
								$seconds_otl_actual = $parsed_otl_actual['hour'] * 3600 + $parsed_otl_actual['minute'] * 60 + $parsed_otl_actual['second'];
								$seconds_otl_target = $parsed_otl_target['hour'] * 3600 + $parsed_otl_target['minute'] * 60 + $parsed_otl_target['second'];
								
								if($seconds_otl_actual > 0){
									$ach_otl_persen = ($seconds_otl_target / $seconds_otl_actual) * 100;
								}else{
									$ach_otl_persen = "";
								}
							?>
							
							<?php if($data[$i]->loading_otl_base != ""){ 
								 $ach_otl_persen_round = round($ach_otl_persen, 0);
								 if($ach_otl_persen_round >= "100"){
									 echo "100";
								 }else{
									 echo $ach_otl_persen_round;
								 }
							}?>  
						</td>
						
						<!-- start finish loading -->
						<td valign="top" align="center" style="text-align:center;">
						<?php if($data[$i]->loading_otl_base != ""){ ?>
							<?=date("H:i", strtotime($data[$i]->loading_otl_start_time));?>
						<?php }?>
						</td>
						<td valign="top" align="center" style="text-align:center;">
						<?php if($data[$i]->loading_otl_base != ""){ ?>
							<?=date("H:i", strtotime($data[$i]->loading_otl_end_time));?>
						<?php } ?>
						</td>
						
						<!-- Target OTD -->
						<td valign="top" align="center" style="text-align:center;">
						<?php
							$this->dbtransporter->select("target_loading_time");
							$this->dbtransporter->where("target_loading_type","OTD");
							$this->dbtransporter->where("target_loading_startdate >=",$firstdate);
							$this->dbtransporter->where("target_loading_enddate <=",date("Y-m-t"));
							$this->dbtransporter->where("target_loading_flag",0);
							$this->dbtransporter->where("target_loading_distrep",$data[$i]->loading_otd_distrep);
							$qotd = $this->dbtransporter->get("droppoint_target_loading");
							$row_otd = $qotd->row();
							if(count($row_otd)> 0){
								$otd_target = date("H:i",strtotime($row_otd->target_loading_time));
							}else{
								$otd_target = "";
							}
						?>
						<?=$otd_target;?>
						</td>
						
						<!-- actual OTD -->
						<td valign="top" align="center" style="text-align:center;">
							<?php $otd_actual = date("H:i", strtotime($data[$i]->loading_otd_start_time));?>
							<?php if($otd_actual > $otd_target){
								$otd_actual = "<font color ='red'>".$otd_actual."</font>";
							} ?>
							<?php if($data[$i]->loading_otd_report_status != ""){ 
								echo $otd_actual;
							}?> 
							
							<?php if($checkdetail == "1"){ ?>
								<br /> <small>
								<b><?=$data[$i]->loading_otd_geofence_start;?> ~ <?=$data[$i]->loading_otd_geofence_end;?> </b><br />
								(<?=$data[$i]->loading_otd_report_status;?>)</small>
							<?php } ?>
						</td>
						
						<!-- Ach. OTD -->
						<td valign="top" align="center" style="text-align:center;">
							<?php if($otd_actual <= $otd_target){
									$ach_otd = 1;
							}else{
								$ach_otd = 0;
							} ?>
							<?=$ach_otd;?>
						</td>
						
						<!-- Ach. OTD %-->
						<td valign="top" align="center" style="text-align:center;">
							<?php 
								$otd_actual_time = $otd_actual.":"."00";
								$otd_target_time = $otd_target.":"."00";
							
								$parsed_otd_actual = date_parse($otd_actual_time);
								$parsed_otd_target = date_parse($otd_target_time);
								$seconds_otd_actual = $parsed_otd_actual['hour'] * 3600 + $parsed_otd_actual['minute'] * 60 + $parsed_otd_actual['second'];
								$seconds_otd_target = $parsed_otd_target['hour'] * 3600 + $parsed_otd_target['minute'] * 60 + $parsed_otd_target['second'];
								
								if($seconds_otd_actual > 0){
									$ach_otd_persen = ($seconds_otd_target / $seconds_otd_actual) * 100;
								}else{
									$ach_otd_persen = "";
								}
							?>
								
							<?php
								$ach_otd_persen_round = round($ach_otd_persen, 0);
								 if($ach_otd_persen_round >= "100"){
									 echo "100";
								 }else{
									 echo $ach_otd_persen_round;
								 }
							?> 
						</td>
						
						<!-- unloading time-->
						<td valign="top" align="center" style="text-align:center;"></td>
						
						<!-- start km-->
						<td valign="top" align="center" style="text-align:center;">0</td>
						
						<!-- finish km-->
						<td valign="top" align="center" style="text-align:center;"><?=$data[$i]->loading_distance;?></td>
						
						<!-- distance-->
						<td valign="top" align="center" style="text-align:center;"><?=$data[$i]->loading_distance;?></td>
					</tr>
				<?php }} ?>	
					
				
				
			<?php
			}
			}else{
			?>
			<tr><td colspan="14">No Available Data</td></tr>
			<?php
			}
			?>
			</tbody>
			<!--<td colspan="10" valign="top" align="center" style="text-align:center;">
			<?php if(count($data) > 0){ ?>
			<div id="button_save">
				<input type="submit" value="Save" name="submit" id="submit"/>
			</div>
				<img id="loader_save" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="loading" title="loading" style="display:none;">
			<?php }?>
			</td>-->
			<tfoot>
				
						
			</tfoot>
		</table>
</form>
</div>