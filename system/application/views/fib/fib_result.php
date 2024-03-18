<?php
$height = "50";
$width = "50";
if($this->sess->user_company == 9999){
					$color_old = "blue";
					$bold_open = "<b>";
					$bold_close = "</b>";
				}else{
					$color_old = "gray";
					$bold_open = "";
					$bold_close = "";
				}
 ?>
		<table id="table2" style='background:#DCDCDC' width="30%">
			<td width="10%">
				<img id="loader" src="<?=base_url();?>assets/fib/images/biru3.png" alt="" title="" height="<?=$height;?>" width=<?=$width;?>>
			</td>
			<td style="text-align:left;">
				<?=$bold_open;?><span id="total_blue"></span><?=$bold_close;?>
			</td>
			<td width="10%">
				<img id="loader" src="<?=base_url();?>assets/fib/images/hijau3.png" alt="" title="" height=<?=$height;?> width=<?=$width;?>>
			</td>
			<td>
				<?=$bold_open;?><span id="total_green"></span><?=$bold_close;?>
			</td>
			<td width="10%">
				<img id="loader" src="<?=base_url();?>assets/fib/images/merah3.png" alt="" title="" height=<?=$height;?> width=<?=$width;?>>
			</td>
			<td>
				<?=$bold_open;?><span id="total_red"></span><?=$bold_close;?>
			</td>
			<td width="10%">
				<img id="loader" src="<?=base_url();?>assets/fib/images/kuning4.png" alt="" title="" height=<?=$height;?> width=<?=$width;?>>
			</td>
			<td>
				<?=$bold_open;?><span id="total_yellow"></span><?=$bold_close;?>
			</td>
			<td width="10%">
				<img id="loader" src="<?=base_url();?>assets/fib/images/putih2.png" alt="" title="" height=<?=$height;?> width=<?=$width;?>>
			</td>
			<td>
				<?=$bold_open;?><span id="total_white"></span><?=$bold_close;?>
			</td>
		</table>
		<br />
		<table width="100%" cellpadding="3" id="customers" style="font-family:Trebuchet MS, Arial, Helvetica, sans-serif; border-collapse: collapse; width: 100%; ">
			<thead>
				<tr  style="text-align:center">
					<th style="text-align:center;" rowspan="2"><?=$bold_open;?>NO.<?=$bold_close;?></th>
					<th style="text-align:center;" rowspan="2" width="10%"><?=$bold_open;?>NO.POL<?=$bold_close;?></th>
					<th style="text-align:center;" rowspan="2" width="12%"><?=$bold_open;?>DRIVER<?=$bold_close;?></th>
					<th style="text-align:center;" rowspan="2"><?=$bold_open;?>LDG<?=$bold_close;?></th>
					<th style="text-align:center;" rowspan="2"><?=$bold_open;?>SJ<?=$bold_close;?></th>
					<th style="text-align:center;" rowspan="2"><?=$bold_open;?>UJ<?=$bold_close;?></th>
					<th style="text-align:center;" rowspan="2"><?=$bold_open;?>CO OFFICE<?=$bold_close;?></th>
					<th style="text-align:center;" rowspan="2"><?=$bold_open;?>ETA<?=$bold_close;?></th>
					<th style="text-align:center;" colspan="2"><?=$bold_open;?>DESTINATION<?=$bold_close;?></th>
					<th style="text-align:center;" rowspan="2"><?=$bold_open;?>CUST<?=$bold_close;?></th>
					<th style="text-align:center;" rowspan="2" width="9%"><?=$bold_open;?>NO.SJ<?=$bold_close;?></th>
					<th style="text-align:center;" rowspan="2"><?=$bold_open;?>REMARK<?=$bold_close;?></th>
				</tr>
				<tr>
					<th style="text-align:center"><?=$bold_open;?>CI<?=$bold_close;?></th>
					<th style="text-align:center"><?=$bold_open;?>CO<?=$bold_close;?></th>
				</tr>
			</thead>
			<?php $k = 0;  
				 $n = 1;
			$ishow = true;
			$start_plus = 0;
			$end_plus = $per_slice;
			
			for($j=0;$j<$total_slide_real; $j++){ ?>
			<tbody id="slide_<?php echo $n; ?>" <?php if(!$ishow){ ?> style="display:none" <?php } ?> >
			<!--<tbody id="slide_<?php echo $n; ?>" > -->
				
			<?php
				
				//loop array slice
				$data_slice1 = array_slice($data,$start_plus,$end_plus);
				
				$start_plus = $start_plus + $per_slice;
				$end_plus = $end_plus + $per_slice;
				
				//limit row view
				$limit_row = $per_slice;
				
				//jika total slide terkahir maka
				if($n == $total_slide_real ){
					$limit_row = count($data_slice1);
				}
				if(isset($data_slice1)) { for($i=0;$i<$limit_row;$i++) { 
				
				$now_date = date("Y-m-d");
				$now_datetime = date("Y-m-d H:i:s");
				$now_datetime_sec = strtotime($now_datetime);
				$style = "";
				$fib_loaded_sec = 0;
				$muat_gantung_limit = 4*3600; //4 jam
				$muat_gantung_color = "#1E90FF"; //DodgerBlue
				$loading_color = "green";
				$gps_trouble_color = "red";
				$customer_warning_color = "yellow";
				$customer_warning_limit = 4*3600; //4jam di customer
				
				//muat gantung
				if(isset($data_slice1[$i]->fib_loaded) && ($data_slice1[$i]->fib_co_ischeck == 0) && ($data_slice1[$i]->fib_sj_status == 0) ){
					if($data_slice1[$i]->fib_loaded_status == 1){ 
						$fib_loaded_sec = strtotime($data_slice1[$i]->fib_loaded);
						//status >= 4 jam belum keluar dari fib
						$delta_loading = $now_datetime_sec - $fib_loaded_sec;
						if(($delta_loading >= $muat_gantung_limit) && ($data_slice1[$i]->fib_co_status == 0)){
							$style = $muat_gantung_color;
						}
					}
				}
				
				//gps trouble
				if(isset($data_slice1[$i]->fib_remark) && ($data_slice1[$i]->fib_remark_status == 1) && (($data_slice1[$i]->fib_remark == "GPS TROUBLE") || ($data_slice1[$i]->fib_remark == "SERVICE AREA"))  ){
					if($data_slice1[$i]->fib_remark_status == 1){ 
						$style = $gps_trouble_color;
					}
				}
				
				//di sudah sampai customer + lebih dari 4 jam di customer (out status = 0)
				if(isset($data_slice1[$i]->fib_arrival_time) && ($data_slice1[$i]->fib_arrival_time_status == 1) && ($data_slice1[$i]->fib_out_time_status == 0)){
					$fib_arrival_time_sec = strtotime($data_slice1[$i]->fib_arrival_time);
						$delta_customer = $now_datetime_sec - $fib_arrival_time_sec;
						//status >= 4 jam belum keluar dari arrival
						if(($delta_customer >= $customer_warning_limit) && ($data_slice1[$i]->fib_arrival_time_status == 1)){
							$style = $customer_warning_color; 
						}
				}
				$k = $k+1;
				
				?>
				
				<tr> 
					<td><?=$bold_open;?><?=$k;?><?=$bold_close;?></td>
					
					<!-- VEHICLE -->
					<td style="text-align:center; background-color: <?=$style;?>;">
						<?=$bold_open;?><?php if(isset($data_slice1[$i]->fib_vehicle_no)) { echo $data_slice1[$i]->fib_vehicle_no; } ?>
						<!--<?=$data_slice1[$i]->fib_vehicle;?><br /><?=$data_slice1[$i]->fib_last_geofence;?>--><?=$bold_close;?>
					</td>
					
					<!-- DRIVER -->
					<td style="text-align:center;"><?=$bold_open;?>
						<?=$data_slice1[$i]->fib_driver;?><?=$bold_close;?>
					</td>
					
					<!-- LDG -->
					<td style="text-align:center;"><?=$bold_open;?>
						<?php if(isset($data_slice1[$i]->fib_loaded) && ($data_slice1[$i]->fib_loaded_status == 1)){ 
								$loading_date = date("Y-m-d",strtotime($data_slice1[$i]->fib_loaded));
								$font_color_loading = "";
						?>
							<?php if($loading_date < $now_date){ $font_color_loading = $color_old;} ?>
							<font color="<?=$font_color_loading;?>"><?=date("H:i",strtotime($data_slice1[$i]->fib_loaded));?></font>
						<?php } ?> 
						<!--<?=$fib_loaded_sec;?>--><?=$bold_close;?>
					</td>
					
					<!-- SJ -->
					<td style="text-align:center;"><?=$bold_open;?>
						<?php if(isset($data_slice1[$i]->fib_sj) && ($data_slice1[$i]->fib_sj_status == 1)){ 
								$sj_date = date("Y-m-d",strtotime($data_slice1[$i]->fib_sj));
								$font_color_sj = "";
						?>
							<?php if($sj_date < $now_date){ $font_color_sj = $color_old;} ?>
							<font color="<?=$font_color_sj;?>"><?=date("d-m-Y",strtotime($data_slice1[$i]->fib_sj));?></font>
						<?php } ?> <?=$bold_close;?>
					</td>
					
					<!-- UJ -->
					<td style="text-align:center;"><?=$bold_open;?>
						<?php if(isset($data_slice1[$i]->fib_uj) && ($data_slice1[$i]->fib_uj_status == 1)){ 
								$uj_date = date("Y-m-d",strtotime($data_slice1[$i]->fib_uj));
								$font_color_uj = "";
						?>
							<?php if($uj_date < $now_date){ $font_color_uj = $color_old;} ?>
							<font color="<?=$font_color_uj;?>"><?=date("H:i",strtotime($data_slice1[$i]->fib_uj));?></font>
						<?php } ?> <?=$bold_close;?>
					</td>
					
					<!-- CO -->
					<td style="text-align:center;"><?=$bold_open;?>
						<?php if(isset($data_slice1[$i]->fib_co) && ($data_slice1[$i]->fib_co_status == 1)){ 
								$co_date = date("Y-m-d",strtotime($data_slice1[$i]->fib_co));
								$font_color_co = "";
						?>
							<?php if($co_date < $now_date){ $font_color_co = $color_old;} ?>
							<font color="<?=$font_color_co;?>"><?=date("H:i",strtotime($data_slice1[$i]->fib_co));?></font>
						<?php } ?><?=$bold_close;?>
					</td>
					
					<!-- ETA -->
					<td style="text-align:center;"><?=$bold_open;?>
						<?php if(isset($data_slice1[$i]->fib_eta) && ($data_slice1[$i]->fib_eta_status == 1)){ 
								$eta_date = date("Y-m-d",strtotime($data_slice1[$i]->fib_eta));
								$font_color_eta = "";
						?>
							<?php if($eta_date < $now_date){ $font_color_eta = $color_old;} ?>
							<font color="<?=$font_color_eta;?>"><?=date("H:i",strtotime($data_slice1[$i]->fib_eta));?></font>
						<?php } ?> <?=$bold_close;?>
					</td>
					
					<!-- CI -->
					<td style="text-align:center;"><?=$bold_open;?>
						<?php if(isset($data_slice1[$i]->fib_arrival_time) && ($data_slice1[$i]->fib_arrival_time_status == 1)){
								$arive_date = date("Y-m-d",strtotime($data_slice1[$i]->fib_arrival_time));
								$font_color_arrive = "";
						?>
							<?php if($arive_date < $now_date){ $font_color_arrive = $color_old;} ?>
								<?php if($this->sess->user_company == 9999){ 
									$arrival_time = date("H:i",strtotime($data_slice1[$i]->fib_arrival_time));
									$arrival_name = substr($data_slice1[$i]->fib_arrival_name,0,10);
								}else{ 
									$arrival_time = date("d-m-Y H:i",strtotime($data_slice1[$i]->fib_arrival_time));
									$arrival_name = $data_slice1[$i]->fib_arrival_name;
								} ?>
							<font color="<?=$font_color_arrive;?>"><?=$arrival_time;?></font><br />
							<small><font color="green"><?=$arrival_name;?></font></small>
						<?php } ?><?=$bold_close;?>
					</td>
					
					<!-- CO -->
					<td style="text-align:center;"><?=$bold_open;?>
						<?php if(isset($data_slice1[$i]->fib_out_time) && ($data_slice1[$i]->fib_out_time_status == 1)){ 
								$out_date = date("Y-m-d",strtotime($data_slice1[$i]->fib_out_time));
								$font_color_out = "";
						?>
							<?php if($out_date < $now_date){ $font_color_out = $color_old;} ?>
								<?php if($this->sess->user_company == 9999){ 
									$out_time = date("H:i",strtotime($data_slice1[$i]->fib_out_time));
									$out_name = substr($data_slice1[$i]->fib_out_name,0,10);
								}else{ 
									$out_time = date("d-m-Y H:i",strtotime($data_slice1[$i]->fib_out_time));
									$out_name = $data_slice1[$i]->fib_out_name;
								} ?>
							<font color="<?=$font_color_out;?>"><?=$out_time;?></font><br />
							<small><font color="green"><?=$out_name;?></font></small>
						<?php } ?><?=$bold_close;?>
					</td>
					
					<!-- CUST -->
					<td style="text-align:center;"><?=$bold_open;?>
						<?php if($this->sess->user_company == 9999){ ?>
							<?php if(isset($data_slice1[$i]->fib_customer) && ($data_slice1[$i]->fib_customer_status == 1)){ echo substr($data_slice1[$i]->fib_customer,0,10); }?> 
							<?php if(isset($data_slice1[$i]->fib_customer2) && ($data_slice1[$i]->fib_customer2_status == 1)){ echo "<font color='blue'>".substr($data_slice1[$i]->fib_customer2,0,10); }?>
							<?php if(isset($data_slice1[$i]->fib_customer3) && ($data_slice1[$i]->fib_customer3_status == 1)){ echo "<font color='green'>".substr($data_slice1[$i]->fib_customer3,0,10); }?>
						<?php }else{ ?>
							<?php if(isset($data_slice1[$i]->fib_customer) && ($data_slice1[$i]->fib_customer_status == 1)){ echo $data_slice1[$i]->fib_customer_unique." ".$data_slice1[$i]->fib_customer; }?> 
							<?php if(isset($data_slice1[$i]->fib_customer2) && ($data_slice1[$i]->fib_customer2_status == 1)){ echo " "."<font color='blue'>".$data_slice1[$i]->fib_customer2_unique." ".$data_slice1[$i]->fib_customer2; }?>
							<?php if(isset($data_slice1[$i]->fib_customer3) && ($data_slice1[$i]->fib_customer3_status == 1)){ echo " "."<font color='green'>".$data_slice1[$i]->fib_customer3_unique." ".$data_slice1[$i]->fib_customer3; }?>
						<?php } ?><?=$bold_close;?>
					</td>
					
					<!-- NO SO -->
					<td style="text-align:center;"><?=$bold_open;?>
						<?php if(isset($data_slice1[$i]->fib_noso) && ($data_slice1[$i]->fib_noso_status == 1)){ echo $data_slice1[$i]->fib_noso; }?>
						<?=$bold_close;?>
					</td>
					
					<!-- REMARK -->
					<td style="text-align:center;"><?=$bold_open;?>
						<?php if(isset($data_slice1[$i]->fib_note) && ($data_slice1[$i]->fib_note != "")){ ?>
							<font size="2pt"><?=$data_slice1[$i]->fib_note;?></font><br />
						<?php } ?>
					<?php if($this->sess->user_company == 9999){ ?>
						<font size="2pt">
							<?php if(isset($data_slice1[$i]->fib_av_plus) && ($data_slice1[$i]->fib_av_plus == 1)){ echo "AV+"."<br />"; }?>
							<?php if(isset($data_slice1[$i]->fib_remark) && ($data_slice1[$i]->fib_remark_status == 1)){
								$last_geofence_name = "";
								$remark = $data_slice1[$i]->fib_remark; 
								if(isset($data_slice1[$i]->fib_remark) && ($data_slice1[$i]->fib_remark == "SERVICE AREA")) {
										if($data_slice1[$i]->fib_last_geofence == "bengkel#BENGKEL PT fib"){
											$remark = "SERVICE @fib";
										}else{
											$last_geofence = explode("#", $data_slice1[$i]->fib_last_geofence);
											if(count($last_geofence)>1){
												$last_geofence_name = $last_geofence[1];
											}else{
												$last_geofence_name =  $last_geofence->fib_last_geofence;
											}
										}
								}
							}
							?>
							<?=$remark;?>
							<?php if($last_geofence_name != ""){ ?>
								<br /><font size="1.75pt">(<?=$last_geofence_name;?>)</font>
							<?php } ?>
								
						</font>		
					<?php }else{ ?>
						<font size="2pt">
							<?php if(isset($data_slice1[$i]->fib_av_plus) && ($data_slice1[$i]->fib_av_plus == 1)){ echo "AV+"."<br />"; }?>
							
							<?php if(isset($data_slice1[$i]->fib_remark) && ($data_slice1[$i]->fib_remark_status == 1)){ 
								echo $data_slice1[$i]->fib_remark; 
							}?>
							
							<?php if(isset($data_slice1[$i]->fib_remark) && ($data_slice1[$i]->fib_remark == "SERVICE AREA")) {
								$last_geofence = explode("#", $data_slice1[$i]->fib_last_geofence);
										if(count($last_geofence)>1){
											$last_geofence_name = $last_geofence[1];
										}else{
											$last_geofence_name =  $last_geofence->fib_last_geofence;
										}
							?>
						</font>
							<br />
							<font size="1.75pt">(<?=$last_geofence_name;?>)</font>
							<?php } ?>
					<?php } ?>
						<?=$bold_close;?>
					</td>
					
					<!-- REMARK -->
					
				</tr>	
				<?php }} ?>	
				
			</tbody>
				<?php $n++; $ishow = false; ?>
				
			<?php } ?>
				
		</table>