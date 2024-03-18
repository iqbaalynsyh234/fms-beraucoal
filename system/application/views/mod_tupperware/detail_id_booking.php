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
			showclock();
		}
	);
</script>

<div id="main_data">
		<form id="frmidbooking">		
			<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
				<tr>
					<td style="text-align:center" colspan="2">
						<h2>ID Booking ( DETAIL )</h2>
						Create Date : <?php if (isset($data)) { echo date("d-m-Y H:i:s",strtotime($data->booking_create_date)); } ?>
					</td>
				</tr>
				<tr>
					<td style="text-align:center" colspan="2">
						<h2>
							Status : 
							
							<?php 
								if (isset($data)) 
								{ 
									if ($data->booking_loading != 1)
									{
										switch ($data->booking_delivery_status)
										{
											case 1 :
												echo "Active";
											break;
											case 2 :
												echo "<font color='green'>";
												echo "Delivered"." "."(".date("d-m-Y H:i:s",strtotime($data->booking_delivered_datetime)).")";
												echo "</font>";
											break;
											case 3:
												echo "Cancel";
											break;
										}
									}
									else
									{
										if ($data->booking_loading == 1)
										{
											echo "<font color='brown'>";
											echo "Loading"." "."(".date("d-m-Y H:i:s",strtotime($data->booking_loading_date)).")";
											echo "</font>";
										}
									}
									
								} 
							?>
						</h2>
					</td>
				</tr>
				<tr>
					<td style="text-align:left">ID Booking</td>
					<td><?php if (isset($data)) { echo $data->booking_id; } ?></td>
				</tr>
				<tr>
					<td style="text-align:left">Destination</td>
					<td><?php if (isset($data)) { echo $data->booking_destination; } ?></td>
				</tr>
				<tr>
					<td style="text-align:left">Type Armada</td>
					<td>
                                            <?php 
                                                if (isset($data)) 
                                                    { 
                                                        for($x=0;$x<count($typearmada);$x++)
                                                        {
                                                            if ($typearmada[$x]->typearmada_id == $data->booking_armada_type)
                                                            {
                                                                echo $typearmada[$x]->typearmada_name; 
                                                            }
                                                        }
                                                        
                                                        } 
                                            ?>
                                        </td>
				</tr>
				<tr>
					<td style="text-align:left">Vehicle</td>
					<td>
					<?php
						foreach($vehicle as $v)
						{
							if (isset($data) && $data->booking_vehicle == $v->vehicle_device)
							{
								echo $v->vehicle_name." ".$v->vehicle_no;
							}
						}
					?>
					</td>
				</tr>
				<tr>
					<td style="text-align:left">Driver</td>
					<td>
					<?php
						foreach($driver as $d)
						{
							if (isset($data) && $data->booking_driver == $d->driver_id)
							{
								echo $d->driver_name;
								echo "<br />";
								echo "("." ".$d->driver_mobile." ".")";
							}
						}
					?>
					</td>
				</tr>
				<tr>
					<td style="text-align:left">CBM Loading</td>
					<td><?php if (isset($data)) { echo $data->booking_cbm_loading; } ?></td>
				</tr>
				<tr>
					<td style="text-align:left">Tanggal Masuk Gudang</td>
					<td><?php if (isset($data)) { echo date("d-m-Y",strtotime($data->booking_date_in)); } ?></td>
				</tr>
				<tr>
					<td style="text-align:left">Jam Masuk Gudang</td>
					<td>
					<?php
						foreach($timecontrol as $t)
						{
							if (isset($data) && $data->booking_time_in == $t->time)
							{
								echo $t->time;
							}
						}
					?>
					</td>
				</tr>
				<tr>
					<td style="text-align:left">Tujuan Gudang</td>
					<td><?php if (isset($data)) { echo $data->booking_warehouse; } ?></td>
				</tr>
				<tr>
					<td style="text-align:left">Loading Date Time</td>
					<td><?php if (isset($data->booking_loading_date)) { echo date("d-m-Y H:i:s",strtotime($data->booking_loading_date)); } ?></td>
				</tr>
				<tr>
					<td style="text-align:left">Delivered Date Time</td>
					<td><?php if (isset($data->booking_delivered_datetime)) { echo date("d-m-Y H:i:s",strtotime($data->booking_delivered_datetime)); } ?></td>
				</tr>
				<tr>
					<td style="text-align:left">Note</td>
					<td><?php if (isset($data)) { echo $data->booking_notes; } ?></td>
				</tr>
			</table>
		</form>
</div>