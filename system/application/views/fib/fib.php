<style>
table {
    font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
    border-collapse: collapse;
    width: 100%;
}

table td, #customers th {
    border: 1px solid #ddd;
    padding: 8px;
}

table tr:nth-child(even){background-color: #f2f2f2;}

table tr:hover {background-color: #ddd;}

table th {
    padding-top: 12px;
    padding-bottom: 12px;
    text-align: left;
    background-color: #4CAF50;
    color: white;
}
</style>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;">
	
	<div id="main" style="margin: 20px;">
		<br />&nbsp;
		<center><h1>FLEET INFORMATION BOARD</h1></center>
		<p><br />
		<!--<table>
		<td style="background-color: #1E90FF" width="10%"></td><td>0</td><td></td>
		<td style="background-color: green" width="10%"></td><td>0</td><td></td>
		<td style="background-color: red" width="10%"></td><td>0</td><td></td>
		<td style="background-color: yellow" width="10%"></td><td>0</td><td></td>
		<td style="background-color: white" width="10%"></td><td>0</td>
		</table>-->
		
		<p><br />
		<table width="100%" cellpadding="3" id="customers">
			<thead>
				<tr style="text-align:center">
					<th style="text-align:center;" rowspan="2" >NO.</th>
					<th style="text-align:center;" rowspan="2" width="10%">NO.POL</th>
					<th style="text-align:center;" rowspan="2" width="12%">DRIVER</th>
					<th style="text-align:center;" rowspan="2">LOADING</th>
					<th style="text-align:center;" rowspan="2">SJ</th>
					<th style="text-align:center;" rowspan="2">UJ</th>
					<th style="text-align:center;" rowspan="2">CHECK OUT KIM</th>
					<th style="text-align:center;" rowspan="2">ETA</th>
					<th style="text-align:center;" colspan="2">DESTINATION</th>
					<th style="text-align:center;" rowspan="2">CUSTOMER</th>
					<th style="text-align:center;" rowspan="2" width="9%">NO.SJ</th>
					<th style="text-align:center;" rowspan="2">REMARK</th>
				</tr>
				<tr>
					<th style="text-align:center">CHECK IN</th>
					<th style="text-align:center">CHECK OUT</th>
				</tr>
			</thead>
			<tbody>
				<?php if(isset($data)) { for($i=0;$i<count($data);$i++) { 
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
				if(isset($data[$i]->fib_loaded) && ($data[$i]->fib_co_ischeck == 0) && ($data[$i]->fib_sj_status == 0) ){
					if($data[$i]->fib_loaded_status == 1){ 
						$fib_loaded_sec = strtotime($data[$i]->fib_loaded);
						//status >= 4 jam belum keluar dari KIM
						$delta_loading = $now_datetime_sec - $fib_loaded_sec;
						if(($delta_loading >= $muat_gantung_limit) && ($data[$i]->fib_co_status == 0)){
							$style = $muat_gantung_color;
						}
					}
				}
				
				//gps trouble
				if(isset($data[$i]->fib_remark) && ($data[$i]->fib_remark_status == 1) && (($data[$i]->fib_remark == "GPS TROUBLE") || ($data[$i]->fib_remark == "SERVICE"))  ){
					if($data[$i]->fib_remark_status == 1){ 
						$style = $gps_trouble_color;
					}
				}
				
				//di sudah sampai customer + lebih dari 4 jam di customer (out status = 0)
				if(isset($data[$i]->fib_arrival_time) && ($data[$i]->fib_arrival_time_status == 1) && ($data[$i]->fib_out_time_status == 0)){
					$fib_arrival_time_sec = strtotime($data[$i]->fib_arrival_time);
						$delta_customer = $now_datetime_sec - $fib_arrival_time_sec;
						//status >= 4 jam belum keluar dari arrival
						if(($delta_customer >= $customer_warning_limit) && ($data[$i]->fib_arrival_time_status == 1)){
							$style = $customer_warning_color; 
						}
				}
				?>
				
				<tr>
					<td><?=$i+1;?></td>
					<td style="text-align:center; background-color: <?=$style;?>;"><?php if(isset($data[$i]->vehicle_no)) { echo $data[$i]->vehicle_no; } ?><!--<?=$data[$i]->vehicle_device;?><br /><?=$data[$i]->fib_last_geofence;?>--></td>
					<td style="text-align:center;"><?php if(isset($driver)) { foreach($driver as $d){ if($d->driver_vehicle == $data[$i]->vehicle_id){ echo $d->driver_name;  }} } ?></td>
					<td style="text-align:center;">
						<?php if(isset($data[$i]->fib_loaded) && ($data[$i]->fib_loaded_status == 1)){ 
								$loading_date = date("Y-m-d",strtotime($data[$i]->fib_loaded));
								$font_color_loading = "";
						?>
							<?php if($loading_date < $now_date){ $font_color_loading = "gray";} ?>
							<font color="<?=$font_color_loading;?>"><?=date("H:i",strtotime($data[$i]->fib_loaded));?></font>
						<?php } ?> 
						<!--<?=$fib_loaded_sec;?>-->
					</td>
					<td style="text-align:center;">
						<?php if(isset($data[$i]->fib_sj) && ($data[$i]->fib_sj_status == 1)){ 
								$sj_date = date("Y-m-d",strtotime($data[$i]->fib_sj));
								$font_color_sj = "";
						?>
							<?php if($sj_date < $now_date){ $font_color_sj = "gray";} ?>
							<font color="<?=$font_color_sj;?>"><?=date("H:i",strtotime($data[$i]->fib_sj));?></font>
						<?php } ?> 
					</td>
					<td style="text-align:center;">
						<?php if(isset($data[$i]->fib_uj) && ($data[$i]->fib_uj_status == 1)){ 
								$uj_date = date("Y-m-d",strtotime($data[$i]->fib_uj));
								$font_color_uj = "";
						?>
							<?php if($uj_date < $now_date){ $font_color_uj = "gray";} ?>
							<font color="<?=$font_color_uj;?>"><?=date("H:i",strtotime($data[$i]->fib_uj));?></font>
						<?php } ?> 
					</td>
					<td style="text-align:center;">
						<?php if(isset($data[$i]->fib_co) && ($data[$i]->fib_co_status == 1)){ 
								$co_date = date("Y-m-d",strtotime($data[$i]->fib_co));
								$font_color_co = "";
						?>
							<?php if($co_date < $now_date){ $font_color_co = "gray";} ?>
							<font color="<?=$font_color_co;?>"><?=date("H:i",strtotime($data[$i]->fib_co));?></font>
						<?php } ?>
					</td>
					<td style="text-align:center;">
						<?php if(isset($data[$i]->fib_eta) && ($data[$i]->fib_eta_status == 1)){ 
								$eta_date = date("Y-m-d",strtotime($data[$i]->fib_eta));
								$font_color_eta = "";
						?>
							<?php if($eta_date < $now_date){ $font_color_eta = "gray";} ?>
							<font color="<?=$font_color_eta;?>"><?=date("H:i",strtotime($data[$i]->fib_eta));?></font>
						<?php } ?> 
					</td>
					<td style="text-align:center;">
						<?php if(isset($data[$i]->fib_arrival_time) && ($data[$i]->fib_arrival_time_status == 1)){ 
								$arive_date = date("Y-m-d",strtotime($data[$i]->fib_arrival_time));
								$font_color_arrive = "";
						?>
							<?php if($arive_date < $now_date){ $font_color_arrive = "gray";} ?>
							<font color="<?=$font_color_arrive;?>"><?=date("H:i",strtotime($data[$i]->fib_arrival_time));?></font>
							<br /><small><font color="green"><?=$data[$i]->fib_arrival_name;?></font></small>
						<?php } ?>
						
					</td>
					<td style="text-align:center;">
						<?php if(isset($data[$i]->fib_out_time) && ($data[$i]->fib_out_time_status == 1)){ 
								$out_date = date("Y-m-d",strtotime($data[$i]->fib_out_time));
								$font_color_out = "";
						?>
							<?php if($out_date < $now_date){ $font_color_out = "gray";} ?>
							<font color="<?=$font_color_out;?>"><?=date("H:i",strtotime($data[$i]->fib_out_time));?></font>
							<br /><small><font color="green"><?=$data[$i]->fib_out_name;?></font></small>
						<?php } ?>
						
					</td>
					<td style="text-align:center;">
						<?php if(isset($data[$i]->fib_customer) && ($data[$i]->fib_customer_status == 1)){ echo $data[$i]->fib_customer; }?> 
						<?php if(isset($data[$i]->fib_customer2) && ($data[$i]->fib_customer2_status == 1)){ echo " "."<font color='blue'>".$data[$i]->fib_customer2; }?>
						<?php if(isset($data[$i]->fib_customer3) && ($data[$i]->fib_customer3_status == 1)){ echo " "."<font color='green'>".$data[$i]->fib_customer3; }?>
					</td>
					<td style="text-align:center;"><?php if(isset($data[$i]->fib_noso) && ($data[$i]->fib_noso_status == 1)){ echo $data[$i]->fib_noso; }?></td>
					<td style="text-align:center;">
					<?php if(isset($data[$i]->fib_remark) && ($data[$i]->fib_remark_status == 1)){ echo $data[$i]->fib_remark; }?>
					<?php if(isset($data[$i]->fib_remark) && ($data[$i]->fib_remark == "SERVICE")) {
						$last_geofence = explode("#", $data[$i]->fib_last_geofence);
								if(count($last_geofence)>1){
									$last_geofence_name = $last_geofence[1];
								}else{
									$last_geofence_name =  $last_geofence->fib_last_geofence;
								}
					?>
						<br /><font size="1.75pt">(<?=$last_geofence_name;?>)</font>
					<?php } ?>
					</td>
				</tr>	
				<?php } } ?>
			</tbody>
		</table>
	</div>
</div>
