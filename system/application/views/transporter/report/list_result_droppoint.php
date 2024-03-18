<h3>Periode <?php echo date('d-m-Y', strtotime($sdate));?> - <?php echo date('d-m-Y', strtotime($edate));?></h3>
<h3><?=$company_name->company_name;?> - <?=$parent_name->parent_code;?></h3>
<h3>Kode Distep: <?=$distrep_name->distrep_code;?></h3>
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
					<td valign="top" align="center" style="text-align:center;"><?=date('H:i',strtotime($droppoint[$i]->droppoint_target_time));?></td>
					<?php
						if(count($data) > 0){
							$total_pengiriman = 0;
							$total_red = 0;
							$total_persentase = 0;
							
						for($j=0; $j < count($data); $j++){ ?>
						
							<!-- search dari table webtracking_geofence_alert_balrich -->
							<?php 
							$geo_time_alert_print = "";
							$sdate_zone = date("Y-m-d H:i:s", strtotime($data[$j]->monthly_date." "."00:00:00"));
							$edate_zone = date("Y-m-d H:i:s", strtotime($data[$j]->monthly_date." "."23:59:59"));
							//$datetime_zone = date("Y-m-d H:i:s", strtotime($data[$j]->monthly_date));
							
							
							
							/*$this->db->select("geoalert_vehicle,geoalert_direction,geoalert_time,geoalert_geofence,
											   geofence_id,geofence_name,
							");*/
							$this->db->select("geoalert_vehicle,geoalert_time");
							
							$this->db->order_by("geoalert_time","asc");
							$this->db->where("geoalert_time >=",$sdate_zone);
							$this->db->where("geoalert_time <=",$edate_zone);
							$this->db->where("geoalert_direction", 1); //masuk area
							$this->db->where("geoalert_geofence_name ",$droppoint[$i]->droppoint_geofence);
							$qgeo = $this->db->get("geofence_alert_balrich");
							$rgeo = $qgeo->row();
							if((count($rgeo)) > 0){
								$geo_time_alert = date("H:i:s", strtotime($rgeo->geoalert_time));
								$geo_time_alert_print = date("H:i:s", strtotime($rgeo->geoalert_time));
								$geo_time_alert_vehicle = $rgeo->geoalert_vehicle;
								// cek jika lebih dari target
								if($geo_time_alert > $droppoint[$i]->droppoint_target_time){
									$geo_time_alert_print = "<font color ='red'>".$geo_time_alert_print."</font>";
									$total_red = $total_red + 1;
								}
								
								$total_pengiriman = $total_pengiriman + 1;
							}else{
								$geo_time_alert = "";
								$geo_time_alert_print = "";
								$geo_time_alert_vehicle = "";
							}
							
							?>
							<td valign="top" align="center" style="text-align:center;"><?=$geo_time_alert_print;?><!--<br /> <small><?=$geo_time_alert_vehicle;?></small>--></td>
					<?php }} ?>
						<?php 
							$total_achive = $total_pengiriman-$total_red;
							
							if($total_pengiriman > 0){
								$total_persentase = ($total_achive/$total_pengiriman) * 100;
							}
							
						?>
						<td valign="top" align="center" style="text-align:center;">-</td>
						<td valign="top" align="center" style="text-align:center;"><?=$total_pengiriman;?></td>
						<td valign="top" align="center" style="text-align:center;"><?=$total_achive;?></td>
						<td valign="top" align="center" style="text-align:center;"><?=$total_persentase;?></td>
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