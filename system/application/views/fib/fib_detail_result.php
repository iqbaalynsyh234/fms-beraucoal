<?php
$height = "50";
$width = "50";
if($this->sess->user_company == 9999){
					$color_old = "red";
					$bold_open = "<b>";
					$bold_close = "</b>";
				}else{
					$color_old = "gray";
					$bold_open = "";
					$bold_close = "";
				}
 ?>

	<div id="blue_view">	
		<table id="table2" style='background:#DCDCDC' width="10%">
			<td width="10%">
				<img id="loader" src="<?=base_url();?>assets/kim/images/biru3.png" alt="" title="" height="<?=$height;?>" width=<?=$width;?>>
			</td>
			<td style="text-align:left;">
				<?=$bold_open;?><span id="total_blue"></span><?=$bold_close;?>
			</td>
		</table>
		<table width="30%" cellpadding="3" id="customers" style="font-family:Trebuchet MS, Arial, Helvetica, sans-serif; border-collapse: collapse; width: 100%; ">
			<thead>
				<tr style="text-align:left">
					<th style="text-align:left;" width="1%"><?=$bold_open;?>NO.<?=$bold_close;?></th>
					<th style="text-align:left;" width="10%"><?=$bold_open;?>NO.POL<?=$bold_close;?></th>
					<th style="text-align:left;" width="12%"><?=$bold_open;?>DRIVER<?=$bold_close;?></th>
				</tr>
			</thead>
			<?php $j=0;
			for($i=0;$i<$total; $i++){  ?>
			<?php if(isset($data[$i]->kondisi_biru) && ($data[$i]->kondisi_biru == 1)){ ?>
				<tbody>
					<tr> 
						<td><?=$bold_open;?><?=$j+1;?><?=$bold_close;?></td>
						<!-- VEHICLE -->
						<td style="text-align:left;">
							<?=$bold_open;?><?php if(isset($data[$i]->vehicle_no)) { echo $data[$i]->vehicle_no; } ?>
						</td>
						<!-- DRIVER -->
						<td style="text-align:left;"><?=$bold_open;?>
						<?php if(isset($driver)) 
								{ foreach($driver as $d)
								{ if($d->driver_vehicle == $data[$i]->vehicle_id)
								{ echo $d->driver_name;  }} } 
						?><?=$bold_close;?>
						</td>
					</tr>	
					<?php $j = $j+1; } ?>	
				</tbody>
			<?php } ?>
		</table>
	</div>
	
	<br />
	
	<div id="green_view">
		<table id="table2" style='background:#DCDCDC' width="10%">
			<td width="10%">
				<img id="loader" src="<?=base_url();?>assets/kim/images/hijau3.png" alt="" title="" height=<?=$height;?> width=<?=$width;?>>
			</td>
			<td>
				<?=$bold_open;?><span id="total_green"></span><?=$bold_close;?>
			</td>
		</table>
		<table width="30%" cellpadding="3" id="customers" style="font-family:Trebuchet MS, Arial, Helvetica, sans-serif; border-collapse: collapse; width: 100%; ">
			<thead>
				<tr style="text-align:left">
					<th style="text-align:left;" width="1%"><?=$bold_open;?>NO.<?=$bold_close;?></th>
					<th style="text-align:left;" width="10%"><?=$bold_open;?>NO.POL<?=$bold_close;?></th>
					<th style="text-align:left;" width="12%"><?=$bold_open;?>DRIVER<?=$bold_close;?></th>
				</tr>
			</thead>
			<?php $j=0;
			for($i=0;$i<$total; $i++){  ?>
			<?php if(isset($data[$i]->kondisi_hijau) && ($data[$i]->kondisi_hijau == 1)){ ?>
				<tbody>
					<tr> 
						<td><?=$bold_open;?><?=$j+1;?><?=$bold_close;?></td>
						<!-- VEHICLE -->
						<td style="text-align:left;">
							<?=$bold_open;?><?php if(isset($data[$i]->vehicle_no)) { echo $data[$i]->vehicle_no; } ?>
						</td>
						<!-- DRIVER -->
						<td style="text-align:left;"><?=$bold_open;?>
						<?php if(isset($driver)) 
								{ foreach($driver as $d)
								{ if($d->driver_vehicle == $data[$i]->vehicle_id)
								{ echo $d->driver_name;  }} } 
						?><?=$bold_close;?>
						</td>
					</tr>	
					<?php $j = $j+1; } ?>	
				</tbody>
			<?php } ?>
		</table>
	</div >
	
	<br />
	
	<div id="red_view">	
		<table id="table2" style='background:#DCDCDC' width="10%">
			<td width="10%">
				<img id="loader" src="<?=base_url();?>assets/kim/images/merah3.png" alt="" title="" height=<?=$height;?> width=<?=$width;?>>
			</td>
			<td>
				<?=$bold_open;?><span id="total_red"></span><?=$bold_close;?>
			</td>
		</table>
		
		<table width="30%" cellpadding="3" id="customers" style="font-family:Trebuchet MS, Arial, Helvetica, sans-serif; border-collapse: collapse; width: 100%; ">
			<thead>
				<tr style="text-align:left">
					<th style="text-align:left;" width="1%"><?=$bold_open;?>NO.<?=$bold_close;?></th>
					<th style="text-align:left;" width="10%"><?=$bold_open;?>NO.POL<?=$bold_close;?></th>
					<th style="text-align:left;" width="12%"><?=$bold_open;?>DRIVER<?=$bold_close;?></th>
				</tr>
			</thead>
			<?php $j=0;
			for($i=0;$i<$total; $i++){  ?>
			<?php if(isset($data[$i]->kondisi_merah) && ($data[$i]->kondisi_merah == 1)){ ?>
				<tbody>
					<tr> 
						<td><?=$bold_open;?><?=$j+1;?><?=$bold_close;?></td>
						<!-- VEHICLE -->
						<td style="text-align:left;">
							<?=$bold_open;?><?php if(isset($data[$i]->vehicle_no)) { echo $data[$i]->vehicle_no; } ?>
						</td>
						<!-- DRIVER -->
						<td style="text-align:left;"><?=$bold_open;?>
						<?php if(isset($driver)) 
								{ foreach($driver as $d)
								{ if($d->driver_vehicle == $data[$i]->vehicle_id)
								{ echo $d->driver_name;  }} } 
						?><?=$bold_close;?>
						</td>
					</tr>	
					<?php $j = $j+1; } ?>	
				</tbody>
			<?php } ?>
		</table>
	</div>
	
	<br />
	
	<div id="yellow_view">	
		<table id="table2" style='background:#DCDCDC' width="10%">
			<td width="10%">
				<img id="loader" src="<?=base_url();?>assets/kim/images/kuning4.png" alt="" title="" height=<?=$height;?> width=<?=$width;?>>
			</td>
			<td>
				<?=$bold_open;?><span id="total_yellow"></span><?=$bold_close;?>
			</td>
		</table>
		
		<table width="30%" cellpadding="3" id="customers" style="font-family:Trebuchet MS, Arial, Helvetica, sans-serif; border-collapse: collapse; width: 100%; ">
			<thead>
				<tr style="text-align:left">
					<th style="text-align:left;" width="1%"><?=$bold_open;?>NO.<?=$bold_close;?></th>
					<th style="text-align:left;" width="10%"><?=$bold_open;?>NO.POL<?=$bold_close;?></th>
					<th style="text-align:left;" width="12%"><?=$bold_open;?>DRIVER<?=$bold_close;?></th>
				</tr>
			</thead>
			<?php $j=0;
			for($i=0;$i<$total; $i++){  ?>
			<?php if(isset($data[$i]->kondisi_kuning) && ($data[$i]->kondisi_kuning == 1)){ ?>
				<tbody>
					<tr> 
						<td><?=$bold_open;?><?=$j+1;?><?=$bold_close;?></td>
						<!-- VEHICLE -->
						<td style="text-align:left;">
							<?=$bold_open;?><?php if(isset($data[$i]->vehicle_no)) { echo $data[$i]->vehicle_no; } ?>
						</td>
						<!-- DRIVER -->
						<td style="text-align:left;"><?=$bold_open;?>
						<?php if(isset($driver)) 
								{ foreach($driver as $d)
								{ if($d->driver_vehicle == $data[$i]->vehicle_id)
								{ echo $d->driver_name;  }} } 
						?><?=$bold_close;?>
						</td>
					</tr>	
					<?php $j = $j+1; } ?>	
				</tbody>
			<?php } ?>
		</table>
	</div>
	
	<br />
	
	<div id="white_view">	
		<table id="table2" style='background:#DCDCDC' width="10%">
			<td width="10%">
				<img id="loader" src="<?=base_url();?>assets/kim/images/putih2.png" alt="" title="" height=<?=$height;?> width=<?=$width;?>>
			</td>
			<td>
				<?=$bold_open;?><span id="total_white"></span><?=$bold_close;?>
			</td>
		</table>
	
		<table width="30%" cellpadding="3" id="customers" style="font-family:Trebuchet MS, Arial, Helvetica, sans-serif; border-collapse: collapse; width: 100%; ">
			<thead>
				<tr style="text-align:left">
					<th style="text-align:left;" width="1%"><?=$bold_open;?>NO.<?=$bold_close;?></th>
					<th style="text-align:left;" width="10%"><?=$bold_open;?>NO.POL<?=$bold_close;?></th>
					<th style="text-align:left;" width="12%"><?=$bold_open;?>DRIVER<?=$bold_close;?></th>
				</tr>
			</thead>
			<?php $j=0;
			for($i=0;$i<$total; $i++){  ?>
			<?php if(isset($data[$i]->kondisi_putih) && ($data[$i]->kondisi_putih == 1)){ ?>
				<tbody>
					<tr> 
						<td><?=$bold_open;?><?=$j+1;?><?=$bold_close;?></td>
						<!-- VEHICLE -->
						<td style="text-align:left;">
							<?=$bold_open;?><?php if(isset($data[$i]->vehicle_no)) { echo $data[$i]->vehicle_no; } ?>
						</td>
						<!-- DRIVER -->
						<td style="text-align:left;"><?=$bold_open;?>
						<?php if(isset($driver)) 
								{ foreach($driver as $d)
								{ if($d->driver_vehicle == $data[$i]->vehicle_id)
								{ echo $d->driver_name;  }} } 
						?><?=$bold_close;?>
						</td>
					</tr>	
					<?php $j = $j+1; } ?>	
				</tbody>
			<?php } ?>
		</table>
	</div>
	