<?php 
if(isset($distrep_name) && ($distrep_name->distrep_type == 1)){
	$type_name = "COMBINE";
}else{
	$type_name = "REGULAR";
}
?>
<h3>Periode <?php echo date('d-m-Y', strtotime($sdate));?> - <?php echo date('d-m-Y', strtotime($edate));?></h3>
<h3><?=$company_name->company_name;?> - <?=$parent_name->parent_code;?></h3>
<h3>Kode Distep: <?=$distrep_name->distrep_code;?> Tipe: <?=$type_name;?> </h3>
<h3>Default ID Kendaraan: <?=$distrep_name->distrep_vehicle_device;?></h3>
<h3>DB TABLE: <?=$dbtable;?></h3>

<?php 
$this->dbreport = $this->load->database("balrich_report", true);
$this->dbtransporter = $this->load->database("transporter", true);
?>
<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<thead>
							<tr>
								<th rowspan="3" width="2%"style="text-align:center"><?=$this->lang->line("lno"); ?></td>
								<th rowspan="3" width="5%"  style="text-align:center">DROPPOINT</th>
								<th rowspan="3" width="10%" style="text-align:center">TARGET OTA</th>
								
							</tr>
							<tr>
							<?php
								if(count($data) > 0){
								for($i=0; $i < count($data); $i++){ ?>
									<th style="text-align:center" width="15%"><?=$data[$i]->monthly_day;?></th>
								<?php }} ?>
								<th rowspan="2" width="2%"style="text-align:center">AVERAGE</th>
								<th rowspan="2" width="2%"style="text-align:center">TOTAL PENGIRIMAN</th>
								<th rowspan="2" width="2%"style="text-align:center">OTA ACHIEVEMENT</th>
								<th rowspan="2" width="2%"style="text-align:center">PERSENTASE %</th>
							</tr>
							<tr>
								<?php
								if(count($data) > 0){
								for($i=0;$i<count($data); $i++){ ?>
								
									<th style="text-align:center" width="15%"><?=date("d-m-Y", strtotime($data[$i]->monthly_date));?></th>
									
									
								<?php } }?>
								
							</tr>
							
			</thead>
			<tbody>
			<?php
			if(count($droppoint) > 0){
			for($i=0;$i<count($droppoint);$i++)
			{
			?>
				
				<tr>
					<td valign="top" align="center" style="text-align:center;"><?=$i+1?></td>
					<td valign="top" align="center" style="text-align:center;">
						<?=$droppoint[$i]->droppoint_name;?> <br /> <small><?=$droppoint[$i]->droppoint_geofence;?></small>
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
							$mean_jam = "";
							$x = array();
							$total_rata = "";
							$total_rata_jam = 0;
						for($j=0; $j < count($data); $j++){ ?>
						
							<!-- search dari table balrich_report - operasional_report (Date) -->
							<?php 
							$georeport_time_alert = "";
							$georeport_time_alert_print = "";
							$georeport_time_alert_vehicle = "";
							$droppoint_target = "";
							$total_target_time = 0;
							$total_row_report = 0;
							
							$sdate_zone = $data[$j]->monthly_date;
							$sdate_only = date("d", strtotime($data[$j]->monthly_date));
							/*$field_time = "georeport_date_".$sdate_only;
							$field_vehicle = "georeport_vehicle_".$sdate_only;*/
							
							//$this->dbreport->where("georeport_date"."_".$sdate_only,$sdate_only);
							/*
							$this->dbreport->where("georeport_droppoint ",$droppoint[$i]->droppoint_id);
							$q_report = $this->dbreport->get($dbtable);
							$row_report = $q_report->row();
							*/
							
							//new search
							//select berdasarkan like nama droppoint di tanggal yg dipilih
							$this->dbreport->select("trip_mileage_vehicle_no,trip_mileage_end_time,");
							$this->dbreport->order_by("trip_mileage_start_time","asc");
							$this->dbreport->where("trip_mileage_vehicle_id",$distrep_name->distrep_vehicle_device);
							$this->dbreport->where("trip_mileage_start_time >=",date("Y-m-d H:i:s", strtotime($sdate_zone."00:00:00")));
							$this->dbreport->where("trip_mileage_start_time <=",date("Y-m-d H:i:s", strtotime($sdate_zone."23:59:59")));
							$this->dbreport->like("trip_mileage_geofence_end",$droppoint[$i]->droppoint_name);
							$this->dbreport->limit(1);
							$q_report = $this->dbreport->get($dbtable);
							$row_report = $q_report->row();
							$total_row_report = count($row_report);
							
							if((isset($row_report)) && ($total_row_report == 0)){
								//jika tidak ada pencarian dari default kendaraan
								$this->dbreport->select("trip_mileage_vehicle_no,trip_mileage_end_time");
								$this->dbreport->order_by("trip_mileage_start_time","asc");
								$this->dbreport->where("trip_mileage_start_time >=",date("Y-m-d H:i:s", strtotime($sdate_zone."00:00:00")));
								$this->dbreport->where("trip_mileage_start_time <=",date("Y-m-d H:i:s", strtotime($sdate_zone."23:59:59")));
								$this->dbreport->like("trip_mileage_geofence_end",$droppoint[$i]->droppoint_name);
								$this->dbreport->limit(1);
								$q_report = $this->dbreport->get($dbtable);
								$row_report = $q_report->row();
							}
						
							if(isset($row_report) && (count($row_report) > 0)){
								if($row_report->trip_mileage_end_time == "00:00:00"){
									$georeport_time_alert = "";
									$georeport_time_alert_print = "";
									$georeport_time_alert_vehicle = "";
									
								}else{
									
									$georeport_time_alert = date("H:i:s", strtotime($row_report->trip_mileage_end_time));
									$georeport_time_alert_print = date("H:i", strtotime($row_report->trip_mileage_end_time));
									$georeport_time_alert_vehicle = $row_report->trip_mileage_vehicle_no;
									
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
									
									$jam_perdata = date("H:i:s", strtotime($georeport_time_alert));
									$jam_konvert = explode(":", $jam_perdata);
									$detik_perdata = $jam_konvert[0]*3600 + $jam_konvert[1] * 60 + $jam_konvert[2];
									$total_detik = $total_detik + $detik_perdata;
									$x[] = $total_detik;
									
								}
							}
							?>
							<td valign="top" align="center" style="text-align:center;"><b>
								<?=$georeport_time_alert_print;?>
								<!--<br /> <small><?=$droppoint_target;?></small>-->
								<br /> <small><?=$georeport_time_alert_vehicle;?></small>
								</b>
							</td>
					<?php }} ?>
						<?php 
							$total_achive = $total_pengiriman-$total_red;
						
							if($total_pengiriman > 0){
								$total_persentase = ($total_achive/$total_pengiriman) * 100;
								
								$count = count($x);                 
								$sum = array_sum($x);               
								$total_rata = $sum / $count;
						 
								
								//konvert detik ke jam menit detik
								$jam = floor($total_rata/3600);

								//Untuk menghitung jumlah dalam satuan menit:
								$sisa = $total_rata%3600;
								$menit = floor($sisa/60);

								//Untuk menghitung jumlah dalam satuan detik:
								$sisa = $sisa % 60;
								$detik = floor($sisa/1);
								$total_rata_jam = date("H:i", strtotime($jam.':'.$menit));
								//$mean_jam = date("H:i:s", strtotime($total_jam/$total_pengiriman));
							}
							
						?>
						<td valign="top" align="center" style="text-align:center;">
						
						</td>
						<td valign="top" align="center" style="text-align:center;"><?=$total_pengiriman;?></td>
						<td valign="top" align="center" style="text-align:center;"><?=$total_achive;?></td>
						<td valign="top" align="center" style="text-align:center;"><?=round($total_persentase, 1, PHP_ROUND_HALF_UP);?> </td>
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