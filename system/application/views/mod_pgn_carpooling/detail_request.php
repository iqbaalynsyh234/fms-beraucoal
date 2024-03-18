<div id="main_data">    
<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
<tr>
	<td style="text-align:left" colspan="2">
		<h2>
			Request Detail 
			<?php 
				if(isset($data))
				{ 
					if($data->request_complete_status == 1)
					{
						echo " "."("." "."COMPLETE"." ".")";
					}
				}
			?>
		</h2>
	</td>
</tr>
<tr style="border: 0px;">
	<td width="100" style="border: 0px;">NIK</td>
	<td style="border: 0px;"><?php if(isset($data)) { echo $data->request_nik; }?></td>
</tr>
<tr style="border: 0px;">
<td width="100" style="border: 0px;">Name</td>
					<td style="border: 0px;"><?php if(isset($data)) { echo $data->request_name; };?></td>
				</tr>

				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">Date</td>
					<td style="border: 0px;"><?php if(isset($data)) { echo date("d-m-Y",strtotime($data->request_date)); }?></td>
				</tr>

				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">Time</td>
					<td style="border: 0px;">
						<?php
						if(isset($data->request_time))
						{
							echo $data->request_time;
						}
						?>
					</td>
				</tr>

				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">Passenger</td>
					<td style="border: 0px;"><?php if(isset($data)) { echo $data->request_passenger; }?></td>
				</tr>

				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">Destination</td>
					<td style="border: 0px;"><?php if(isset($data)) { echo $data->request_destination; }?></td>
				</tr>

				<tr style="border: 0px;">
					<td width="100"  style="border: 0px;vertical-align: middle;">Notes</td>
					<td style="border: 0px;" colspan="2"><?php if(isset($data)) { echo $data->request_note; }?></td>
				</tr>
				
				<tr style="border: 0px;">
					<td width="100"  style="border: 0px;vertical-align: middle;">Vehicle/Car</td>
					<td style="border: 0px;" colspan="2">
						<?php 
							if(isset($data)) 
							{ 
								if(isset($vehicle))
								{
									foreach($vehicle as $v)
									{
										if($v->pgn_vehicle_device == $data->request_car)
										{
											echo $v->pgn_vehicle_no." ".$v->pgn_vehicle_name;
										}
									}
								}
							}
						?>
					</td>
				</tr>
				
				<tr style="border: 0px;">
					<td width="100"  style="border: 0px;vertical-align: middle;">Driver</td>
					<td style="border: 0px;" colspan="2">
						<?php if(isset($data)) { echo $data->request_driver; }?>
					</td>
				</tr>
        </table>
</div>