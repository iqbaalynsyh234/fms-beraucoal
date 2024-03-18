		 
		<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<tr>
				<td colspan="6">
					<b>Profile</b>
					<br />
					<hr />
				</td>
			</tr>
			
			<?php
				//$app_route = $this->config->item("app_route");
				//if (isset($app_route) && ($app_route == 1))
				//{
			?>
			<!--
				<tr>
					<td>Route</td>
					<td>:</td>
					<td style="text-align:left" colspan="4">
						<?php
							/*if (isset($my_route) && $my_route>0)
							{
								foreach($my_route as $myroute)
								{
									if (isset($row) && $row->mobil_route == $myroute->route_id)
									{
										echo $myroute->route_name;
									}
								}
							}*/		
						?>
					</td>
				</tr>
				-->
			<?php
				//}
			?>
			
			<tr>
				<td width="30%">Type</td>
				<td width="2%">:</td>
				<td><?php echo $row->mobil_model;?></td>
				
				
				<td>Year</td>
				<td>:</td>
				<td><?php echo $row->mobil_year;?></td>
			</tr>
			<tr>
				<td>Engine Capacity</td>
				<td>:</td>
				<td><?php echo $row->mobil_engine_capacity." "."CC";?></td>
				
				<td>Registration Date</td>
				<td>:</td>
				<td>
					<?php 
						$v = $row->mobil_year;
						$val = date("d-m-Y", strtotime($v));
						echo $val;
					?>
				</td>
			</tr>

			<tr>
				<td>Fuel Type</td>
				<td>:</td>
				<td>
					<?php 
						if(isset($fuel) && count($fuel)>0)
						{
							foreach ($fuel as $f)
							{
								if ($f->fuel_type_id == $row->mobil_fuel_type)
								{
									echo $f->fuel_type;
								}
							}
						}
						else
						{ echo "-"; }
					?>
				</td>
				
				<td>Fuel Consumption</td>
				<td>:</td>
				<td><?php echo $row->mobil_fuel_consumption." "."Lt";?></td>
			</tr>
			<tr>
				<td>KIR No</td>
				<td>:</td>
				<td>
					<?php echo $row->mobil_no_kir;?>
				</td>
				
				<td>KIR Exp. Date</td>
				<td>:</td>
				<td>
					<?php 
						$v = $row->mobil_kir_active_date;
						$val = date("d-m-Y", strtotime($v));
						echo $val;
					?>
				</td>
			</tr>
			<tr>
				<td>STNK No</td>
				<td>:</td>
				<td>
					<?php echo $row->mobil_stnk_no;?>
				</td>
				
				<td>Exp. Date</td>
				<td>:</td>
				<td>
					<?php 
						$v = $row->mobil_kir_active_date;
						$val = date("d-m-Y", strtotime($v));
						echo $val;
					?>
				</td>
			</tr>
			<tr>
				<td>Rangka No</td>
				<td>:</td>
				<td>
					<?php echo $row->mobil_no_rangka;?>
				</td>
				
				<td>Mesin No</td>
				<td>:</td>
				<td>
					<?php echo $row->mobil_no_mesin;?>
				</td>
			</tr>
			<tr>
				<td>Merk</td>
				<td>:</td>
				<td>
					<?php echo $row->mobil_merk;?>
				</td>
				
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr><td colspan="6">&nbsp;</td></tr>
			<tr>
				<td colspan="6">
					<b>Insurance Information</b>
					<br />
					<hr />
				</td>
			</tr>
			<tr>
				<td>Insurance No</td>
				<td>:</td>
				<td><?php echo $row->mobil_insurance_no;?></td>
				
				<td>Insurance Exp. Date</td>
				<td>:</td>
				<td>
					<?php 
						$v = $row->mobil_insurance_expired_date;
						$val = date("d-m-Y",strtotime($v));
						echo $val;
					?>
				</td>
			</tr>
			<tr><td colspan="6">&nbsp;</td></tr>
			<tr>
				<td colspan="6">
					<b>Services Information</b>
					<br />
					<hr />
				</td>
			</tr>
			<tr>
				<td>Warranty Service by Date</td>
				<td>:</td>
				<td>
					<?php 
						$v = $row->mobil_warranty_service_bydate;
						$value = date("d-m-Y",strtotime($row->mobil_warranty_service_bydate));
						echo $value;
					?>
				</td>
				
				<td>Last Service (Inisialisasi Odo)</td>
				<td>:</td>
				<td>
					<?php 
						echo $row->mobil_last_service_bykm." ";
						echo "( Realtime Odometer : "." ".$lastodo." "." ) ";
					?>
				</td>
			</tr>
			<tr>
				<td>Warranty Service by KM</td>
				<td>:</td>
				<td>
					<?php 
						echo $row->mobil_warranty_service_bykm;
					?>
				</td>
				
				<td>Schedule Service</td>
				<td>:</td>
				<td>
					<?php 
						$v = $row->mobil_next_service_date;
						$value = date("d-m-Y",strtotime($row->mobil_next_service_date));
						echo $value;
					?>
				</td>
			</tr>
			<tr>
			<td>Service Date</td>
				<td>:</td>
				<td>
					<?php 
						$v = $row->mobil_service_date;
						$value = date("d-m-Y",strtotime($row->mobil_service_date));
						echo $value;
					?>
				</td>
			</tr>
		</table>
		
