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
<?php 
if(isset($distrep_name) && ($distrep_name->distrep_type == 1)){
	$type_name = "COMBINE";
}else{
	$type_name = "REGULAR";
}

if(isset($distrep_name) && ($distrep_name->distrep_report_status == 1)){
	$report_status = "TDS - A";
}else if($distrep_name->distrep_report_status == 2){
	$report_status = "TDS - B";
}else{
	$report_status = "ODS";
}
?>
<a class="button" href="javascript:void(0);" id="export_xcel">Export to Excel</a> 
<a class="button" href="<?=base_url()?>balrich_report/mn_dataoperasional" target="_blank">View Operasional Report</a>
<a class="button" href="<?=base_url()?>newhistory" target="_blank">View History (Animasi)</a>


<div id="isexport_xcel">

<h3>Periode <?php echo date('d-m-Y', strtotime($sdate));?> - <?php echo date('d-m-Y', strtotime($edate));?></h3>
<h3><?=$company_name->company_name;?> - <?=$parent_name->parent_code;?> - <?=$distrep_name->distrep_vehicle_no;?></h3>
<h3>Kode Distrep: <?=$distrep_name->distrep_code;?></h3>
<h3>Tipe: <?=$type_name;?> (<?=$report_status;?>)</h3>
<!--<h3>DB TABLE: <?=$dbtable;?></h3>-->

<?php 
$this->dbreport = $this->load->database("balrich_report", true);
$this->dbtransporter = $this->load->database("transporter", true);
?>
<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<thead>
						
								<th width="2%" style="text-align:center"><?=$this->lang->line("lno"); ?></th>
								<th width="5%" style="text-align:center">Dist Rep</th>
								<th width="5%" style="text-align:center">Plant</th>
								<th width="5%" style="text-align:center">Month</th>
								<th width="5%" style="text-align:center">Transporter</th>
								<th width="5%" style="text-align:center">Drop Point</th>
								<th width="10%" style="text-align:center">Target OTA</th>
								
							<?php
								if(count($data) > 0){
								for($i=0; $i < count($data); $i++){ ?>
									<th style="text-align:center" width="15%"><?=$data[$i]->monthly_day;?>, <?=date("d", strtotime($data[$i]->monthly_date));?> <?=$data[$i]->monthly_name;?> <?=date("Y", strtotime($data[$i]->monthly_date));?></th>
								<?php }} ?>
								<th width="2%" style="text-align:center">Avg.</th>
								<th width="2%" style="text-align:center">Total Pengiriman</th>
								<th width="2%" style="text-align:center">OTA Achievement</th>
								<th width="2%" style="text-align:center">%</th>
						
							
								<?php
								if(count($data) > 0){
								for($i=0;$i<count($data); $i++){ ?>
								
									
									
									
								<?php } }?>
								
							
							
			</thead>
			<tbody>
			<?php
			if(count($droppoint) > 0){
			for($i=0;$i<count($droppoint);$i++)
			{
			?>
				
				<tr>
					<td valign="top" align="center" style="text-align:center;"><?=$i+1?></td>
					<td valign="top" align="center" style="text-align:center;"><?=$distrep_name->distrep_code;?></td>
					<td valign="top" align="center" style="text-align:center;"><?=$plant->plant_code;?></td>
					<td valign="top" align="center" style="text-align:center;"><?=$month_name;?></td>
					<td valign="top" align="center" style="text-align:center;">BALRICH</td>
					<td valign="top" align="center" style="text-align:center;">
						<?=$droppoint[$i]->droppoint_name;?> 
						<?php if($checkdetail == "1"){ ?>
							<br /> <small><?=$droppoint[$i]->droppoint_geofence;?></small>
						<?php  } ?>
					</td>
					<td valign="top" align="center" style="text-align:center;">
					<!-- Cek All Target in Droppoint Time -->
					<?php 
						$total_target = 0;
						$target_startdate = "";
						$target_enddate = "";
						$target_time = "";
						$total_target2 = 0;
						
						$this->dbtransporter->select("target_startdate,target_enddate,target_time");
						$this->dbtransporter->where("target_droppoint",$droppoint[$i]->droppoint_id);
						$this->dbtransporter->where("target_type",$distrep_name->distrep_type);
						$this->dbtransporter->where("target_startdate >=",$sdate);
						$this->dbtransporter->where("target_startdate <=",$edate);
						/*$this->dbtransporter->where("target_month",$month);
						$this->dbtransporter->where("target_year",$year);*/
						$this->dbtransporter->where("target_creator",1032);
						$this->dbtransporter->where("target_flag",0);
						$q_target = $this->dbtransporter->get("droppoint_target");
						$target = $q_target->result();
						$total_target = count($target);
					?>
					
					<?php if($total_target >= 0 && $total_target < 2){   //jika target hanya ada 1
						//cek target per tanggal
						$this->dbtransporter->limit(1);
						$this->dbtransporter->order_by("target_startdate", "asc");							
						$this->dbtransporter->select("target_startdate,target_enddate,target_time");
						$this->dbtransporter->where("target_droppoint",$droppoint[$i]->droppoint_id);
						$this->dbtransporter->where("target_type",$distrep_name->distrep_type);
						$this->dbtransporter->where("target_startdate >=",$sdate);
						$this->dbtransporter->where("target_creator",1032);
						$this->dbtransporter->where("target_flag",0);
						$q_target2 = $this->dbtransporter->get("droppoint_target");
						$target2 = $q_target2->row();
						$total_target2 = count($target2);
						
						if($total_target2 == 0){
							$this->dbtransporter->limit(1);
							$this->dbtransporter->order_by("target_startdate", "desc");
							$this->dbtransporter->select("target_time");
							$this->dbtransporter->where("target_droppoint",$droppoint[$i]->droppoint_id);
							$this->dbtransporter->where("target_type",$distrep_name->distrep_type);
							$this->dbtransporter->where("target_month",$month);
							$this->dbtransporter->where("target_year",$year);
							$this->dbtransporter->where("target_flag",0);
							$q_target2 = $this->dbtransporter->get("droppoint_target");
							$target2 = $q_target2->row();
						}	
						
						if(isset($target2) && (count($target2) > 0)){ ?>
							<?=date("H:i", strtotime($target2->target_time));?>
					<?php } } ?>
					
					<?php if($total_target >= 2){
						for($t=0;$t<$total_target; $t++){ 
									
									//untuk kondisi ada perubahan target 
									$this->dbtransporter->order_by("target_startdate", "desc");							
									$this->dbtransporter->select("target_startdate,target_enddate,target_time");
									$this->dbtransporter->where("target_droppoint",$droppoint[$i]->droppoint_id);
									$this->dbtransporter->where("target_type",$distrep_name->distrep_type);
									$this->dbtransporter->where("target_startdate >=",$sdate);
									$this->dbtransporter->where("target_creator",1032);
									$this->dbtransporter->where("target_flag",0);
									$q_target2 = $this->dbtransporter->get("droppoint_target");
									$target2 = $q_target2->row();
									
									if(isset($target2) && (count($target2) > 0)){ ?>
										<td colspan="14">
											<h3>Ada perubahan target pada tanggal <?=date("d-m-Y", strtotime($target2->target_startdate." ".$target2->target_time));?> !</h3> <br />
											Silahkan ubah pengambilan tanggal akhir (End Date) kurang dari : <b><?=date("d-m-Y", strtotime($target2->target_startdate." ".$target2->target_time));?></b>
										</td> 
									<?php  } return; ?>
						<?php } ?>
					<?php } ?>
						
						
					</td>
					
					<?php
						if(count($data) > 0){
							$total_pengiriman = 0;
							$total_red = 0;
							$total_persentase = 0;
							$total_target_time = 0;
							$total_detik = 0;
							$total_jam = 0;
							$total_rata = "";
							$total_rata_jam = 0;
						for($j=0; $j < count($data); $j++){ ?>
						
							<!-- search dari table balrich_report - inout_geofence_ (Date) -->
							<?php 
							$georeport_time_alert = "";
							$georeport_time_alert_print = "";
							$georeport_time_alert_vehicle = "";
							$georeport_status = "";
							$droppoint_target = "";
							$total_target_time = 0;
							$detik_perdata = 0;
							
							//matikan sementara
							$sdate_zone = $data[$j]->monthly_date;
							$sdate_only = date("d", strtotime($data[$j]->monthly_date));
							$field_time = "georeport_date_".$sdate_only;
							$field_vehicle = "georeport_vehicle_".$sdate_only;
							$field_status = "georeport_status_".$sdate_only;
							$sdate_type = $data[$j]->monthly_type; //ganjil //genap //ods
							
						//jika ods
						if($distrep_name->distrep_report_status == 0){
							$this->dbreport->where("georeport_droppoint ",$droppoint[$i]->droppoint_id);
							$q_report = $this->dbreport->get($dbtable);
							$row_report = $q_report->row();
							if(isset($row_report) && (count($row_report) > 0)){
								if($row_report->$field_time == "00:00:00"){
									$georeport_time_alert = "";
									$georeport_time_alert_print = "";
									$georeport_time_alert_vehicle = "";
									$georeport_status = "";
									$detik_perdata = "";
									
								}else{
									
									$georeport_time_alert = date("H:i:s", strtotime($row_report->$field_time));
									$georeport_time_alert_print = date("H:i", strtotime($row_report->$field_time));
									$georeport_time_alert_vehicle = $row_report->$field_vehicle;
									$georeport_status = $row_report->$field_status;
									//$georeport_time_alert_datetime = date("Y-m-d H:i:s", strtotime($sdate_zone." ".$georeport_time_alert));
									
									$this->dbtransporter->limit(1);
									$this->dbtransporter->order_by("target_startdate", "asc");							
									$this->dbtransporter->select("target_startdate,target_enddate,target_time");
									$this->dbtransporter->where("target_droppoint",$droppoint[$i]->droppoint_id);
									$this->dbtransporter->where("target_type",$distrep_name->distrep_type);
									$this->dbtransporter->where("target_startdate >=",$sdate);
									$this->dbtransporter->where("target_creator",1032);
									$this->dbtransporter->where("target_flag",0);
									$q_target_time = $this->dbtransporter->get("droppoint_target");
									$target_time = $q_target_time->row();
									$total_target_time = count($target_time);
									
									if($total_target_time == 0){
										//cek target per tanggal
										$this->dbtransporter->limit(1);
										$this->dbtransporter->order_by("target_startdate", "desc");
										$this->dbtransporter->select("target_time");
										$this->dbtransporter->where("target_droppoint",$droppoint[$i]->droppoint_id);
										$this->dbtransporter->where("target_type",$distrep_name->distrep_type);
										$this->dbtransporter->where("target_month",$month);
										$this->dbtransporter->where("target_year",$year);
										$this->dbtransporter->where("target_flag",0);
										$q_target_time = $this->dbtransporter->get("droppoint_target");
									}
									$target_time = $q_target_time->row();
									
									if(isset($target_time) && (count($target_time) > 0) ){
										$droppoint_target = $target_time->target_time;
										// cek jika lebih dari target
										if($georeport_time_alert > $droppoint_target){
											$georeport_time_alert_print = "<font color ='red'>".$georeport_time_alert_print."</font>";
											$total_red = $total_red + 1;
											
										}
									
									}else{
										$droppoint_target = "nodata";
									}
									$total_pengiriman = $total_pengiriman + 1;
									
									$jam_perdata = $georeport_time_alert_print.":"."00";
									$jam_konvert = date_parse($jam_perdata);
									$detik_perdata = $jam_konvert['hour'] * 3600 + $jam_konvert['minute'] * 60 + $jam_konvert['second'];
									$total_detik = $total_detik + $detik_perdata;
									
								}
							}
						}else{ //jika tds 
								if($sdate_type == $distrep_name->distrep_report_status){ //ganjil or genap
									$this->dbreport->where("georeport_droppoint ",$droppoint[$i]->droppoint_id);
									$q_report = $this->dbreport->get($dbtable);
									$row_report = $q_report->row();
									if(isset($row_report) && (count($row_report) > 0)){
										if($row_report->$field_time == "00:00:00"){
											$georeport_time_alert = "";
											$georeport_time_alert_print = "";
											$georeport_time_alert_vehicle = "";
											$georeport_status = "";
											
										}else{
											
											$georeport_time_alert = date("H:i:s", strtotime($row_report->$field_time));
											$georeport_time_alert_print = date("H:i", strtotime($row_report->$field_time));
											$georeport_time_alert_vehicle = $row_report->$field_vehicle;
											$georeport_status = $row_report->$field_status;
											//$georeport_time_alert_datetime = date("Y-m-d H:i:s", strtotime($sdate_zone." ".$georeport_time_alert));
											
											$this->dbtransporter->limit(1);
											$this->dbtransporter->order_by("target_startdate", "asc");							
											$this->dbtransporter->select("target_startdate,target_enddate,target_time");
											$this->dbtransporter->where("target_droppoint",$droppoint[$i]->droppoint_id);
											$this->dbtransporter->where("target_type",$distrep_name->distrep_type);
											$this->dbtransporter->where("target_startdate >=",$sdate);
											$this->dbtransporter->where("target_creator",1032);
											$this->dbtransporter->where("target_flag",0);
											$q_target_time = $this->dbtransporter->get("droppoint_target");
											$target_time = $q_target_time->row();
											$total_target_time = count($target_time);
											
											if($total_target_time == 0){
												//cek target per tanggal
												$this->dbtransporter->limit(1);
												$this->dbtransporter->order_by("target_startdate", "desc");
												$this->dbtransporter->select("target_time");
												$this->dbtransporter->where("target_droppoint",$droppoint[$i]->droppoint_id);
												$this->dbtransporter->where("target_type",$distrep_name->distrep_type);
												$this->dbtransporter->where("target_month",$month);
												$this->dbtransporter->where("target_year",$year);
												$this->dbtransporter->where("target_flag",0);
												$q_target_time = $this->dbtransporter->get("droppoint_target");
											}
											$target_time = $q_target_time->row();
											
											if(isset($target_time) && (count($target_time) > 0) ){
												$droppoint_target = $target_time->target_time;
												// cek jika lebih dari target
												if($georeport_time_alert > $droppoint_target){
													$georeport_time_alert_print = "<font color ='red'>".$georeport_time_alert_print."</font>";
													$total_red = $total_red + 1;
													
												}
											
											}else{
												$droppoint_target = "nodata";
											}
											$total_pengiriman = $total_pengiriman + 1;
											
											$jam_perdata = $georeport_time_alert_print.":"."00";
											$jam_konvert = date_parse($jam_perdata);
											$detik_perdata = $jam_konvert['hour'] * 3600 + $jam_konvert['minute'] * 60 + $jam_konvert['second'];
											$total_detik = $total_detik + $detik_perdata;
											
											
										}
									}
								}
							
						}
							
							?>
							
							<td valign="top" align="center" style="text-align:center;"><b>
								<?=$georeport_time_alert_print;?>
								<!--<br /> <small><?=$droppoint_target;?></small>-->
								<!--<br /> <small><?=$detik_perdata;?></small>-->
								<?php if($checkdetail == "1"){ ?>
									<br /> <small><?=$georeport_time_alert_vehicle;?></small>
									<?php 
										if(isset($georeport_status) && ($georeport_status == "DOOR")){ ?>
										<small> - <?=$georeport_status;?></small>
									<?php } ?>
									
								<?php } ?>
								</b>
							</td>
					<?php }} ?>
						<?php 
							$total_achive = $total_pengiriman-$total_red;
						
							if($total_pengiriman > 0){
								$total_persentase = ($total_achive/$total_pengiriman) * 100;
								$total_rata = $total_detik / $total_pengiriman;
								
								//konvert detik ke jam menit detik
								$jam = floor($total_rata/3600);

								//Untuk menghitung jumlah dalam satuan menit:
								$sisa = $total_rata%3600;
								$menit = floor($sisa/60);

								//Untuk menghitung jumlah dalam satuan detik:
								$sisa = $sisa % 60;
								$detik = floor($sisa/1);
								$total_rata_jam = date("H:i", strtotime($jam.':'.$menit));
								
							}
							
						?>
						<?php if($total_pengiriman > 0){ ?>
							<td valign="top" align="center" style="text-align:center;"><b><?=$total_rata_jam;?></b></td>
							<td valign="top" align="center" style="text-align:center;"><?=$total_pengiriman;?></td>
							<td valign="top" align="center" style="text-align:center;"><?=$total_achive;?></td>
							<td valign="top" align="center" style="text-align:center;"><?=round($total_persentase, 1, PHP_ROUND_HALF_UP);?></td>
						<?php }else{ ?>
							<td valign="top" align="center" style="text-align:center;"></td>
							<td valign="top" align="center" style="text-align:center;"></td>
							<td valign="top" align="center" style="text-align:center;"></td>
							<td valign="top" align="center" style="text-align:center;"></td>
						<?php } ?>
						
				</tr>
				
			<?php
			}
			}else{
			?>
			<tr><td colspan="14">No Available Data</td></tr>
			<?php
			}
			?>
			</tbody>
			<tfoot>
				
						
			</tfoot>
		</table>
</div>