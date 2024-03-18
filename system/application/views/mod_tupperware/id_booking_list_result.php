		<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<thead>
				<tr>
					<th width="2%" style="text-align:center;">No.</td>
					<?php 
						if ($this->config->item("app_tupperware"))
						{
					?>
						<th style="text-align:center;">Transporter</th>
						<th style="text-align:center;">SLCARS</th>
						<!--<th style="text-align:center;">DB Code</th>-->
					<?php
						}
					?>
					<th width="10%" style="text-align:center;">Booking ID</th>
					<th width="18%" style="text-align:center;">Destination</th>
					<th style="text-align:center;">Armada Type</th>
					<th style="text-align:center;">CBM Loading</th>
					<th style="text-align:center;">Vehicle</th>
					<th align="center" style="text-align:center;">Driver</th>
					<th align="center" style="text-align:center;">Date Time ( In )</th>					
					<th align="center" style="text-align:center;">Warehouse</th>
					<th style="text-align:center;">Control</th>					
				</tr>
			</thead>
			<tbody>
			<?php
			if(count($data) > 0){
			for($i=0; $i < count($data); $i++)
			{
			?>
				<tr <?=($i%2) ? "class='odd'" : "";?>>
					<td valign="top"><?=$i+1+$offset?></td>
					<?php 
						if ($this->config->item("app_tupperware"))
						{
					?>
						<td valign="top">
						<?php 
							if (isset($company))
							{
								foreach ($company as $c)
								{
									if ($c->company_id == $data[$i]->booking_company)
									{
										echo $c->company_name;
									}
								}
							}
						?>
						</td>
						<td valign="top">
							<?php if (isset($data[$i]->transporter_barcode_slcars)) { echo $data[$i]->transporter_barcode_slcars; }  ?>
						</td>
						<!--<td valign="top"></td>-->
					<?php
						}
					?>
					<td valign="top">
						<?=$data[$i]->booking_id?>
						<?php 
							if ($data[$i]->booking_delivery_status == 2)
							{
						?>
							<br />
							<font color="green"><b>( Delivered )</b></font>
						<?php
							}
							else if ($data[$i]->booking_loading == 1)
							{
						?>
							<br />
							<font color="brown"><b>( Loading )</b></font>
						<?php
							}
						?>
					</td>
					<td valign="top"><?=$data[$i]->booking_destination?></td>
					<td valign="top">
                                             <?php 
                                                if (isset($data)) 
                                                    { 
                                                        for($x=0;$x<count($typearmada);$x++)
                                                        {
                                                            if ($typearmada[$x]->typearmada_id == $data[$i]->booking_armada_type)
                                                            {
                                                                echo $typearmada[$x]->typearmada_name; 
                                                            }
                                                        }
                                                        
                                                        } ?>
                                        </td>
					<td valign="top"><?=$data[$i]->booking_cbm_loading?></td>
					<td valign="top">
						<?php 
							if (isset($vehicle))
							{
								foreach($vehicle as $v)
								{
									if ($v->vehicle_device == $data[$i]->booking_vehicle)
									{
										echo $v->vehicle_name." ".$v->vehicle_no;
									}
								}
							}
						?>
					</td>
					<td valign="top">
						<?php 
							if (isset($driver))
							{
								foreach($driver as $d)
								{
									if ($d->driver_id == $data[$i]->booking_driver)
									{
										echo $d->driver_name."<br />";
										echo $d->driver_mobile;
									}
								}
							}
							
						?>
					</td>
					<td valign="top"><?=date("d-m-Y H:i:s",strtotime($data[$i]->booking_datetime_in))?></td>
					<td valign="top"><?=$data[$i]->booking_warehouse?></td>
					<td>
						<?php 
							if (isset($data))
							{
								if ($data[$i]->booking_delivery_status == 1)
								{
									if ($data[$i]->booking_company == $this->sess->user_company) 
									{
						?>
								<a href="javascript: edit('<?php echo $data[$i]->id;?>')" title="Edit ID Booking"><img src="<?php echo base_url();?>assets/images/edit.gif" alt="Edit ID Booking" /></a>
						<?php
									}
								}
							}
						?>
						<a href="javascript: detail('<?php echo $data[$i]->id;?>')" title="Info Detail"><img src="<?php echo base_url();?>assets/images/postreq.png" alt="Info Detail" /></a>
						<?php 
							if ($this->config->item("app_tupperware") && isset($data[$i]->booking_loading) && $data[$i]->booking_delivery_status != 2)
							{
						?>
								<a href="javascript: set_delivered('<?php echo $data[$i]->id;?>')" title="Set To Delivered"><img src="<?php echo base_url();?>assets/transporter/images/delivered.png" width="16px" height="16px" alt="Set To Delivered" /></a>
						<?php
								if ($data[$i]->booking_loading != 1 && $data[$i]->booking_delivery_status == 1)
								{
						?>
								<a href="javascript: delete_data('<?php echo $data[$i]->id;?>')" title="Delete ID Booking"><img src="<?php echo base_url();?>assets/images/trash.gif" alt="Delete ID Booking" /></a>
								<a href="javascript: set_loading('<?php echo $data[$i]->id;?>')" title="Set Loading"><img src="<?php echo base_url();?>assets/transporter/images/loadingpackage.png" alt="Set Loading" /></a>
						<?php
								}
							}
						?>
					</td>
				</tr>
			<?php
			}
			}else{
				echo "<tr><td colspan='11'>No Data Available</td></tr>";
			}
			?>
			</tbody>
			<tfoot>
					<tr>
							<td colspan="12"><?=$paging?></td>
					</tr>
			</tfoot>
		</table>
