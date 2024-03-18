									<div id="listtable">	
										<span id='tblrealtime' style='display:none;'>
											<table id="boxtable" width="30%" style='display:none;'> 
												<thead>
														<?php
														
														$vehiclewithpulse = $this->config->item("vehicle_pulse");
														for($i=0; $i < count($data); $i++)
														{
															?>
															<!--no-->
															<tr class="toprow" <?=($i%2) ? "class='odd'" : "";?> id="tr<?=$data[$i]->vehicle_id;?>">
																<th width="2%" align="right"><?=$this->lang->line("lno"); ?></th>
																	<td><?=$i+1?></td>
															</tr>
															
															<!--owner-->
															<tr class="toprow" class='odd' id="tr<?=$data[$i]->vehicle_id;?>">
																
																<?php if (($this->sess->user_type != 2) || $this->sess->user_company) { ?>
																	<th width="8%" align="right"><a href="#" onclick="javascript:order('user_name')"><?if ($sortby == 'user_name') { echo '<u>'; }?><?=$this->lang->line("lusername"); ?><?if ($sortby == 'user_name') { echo '</u>'; }?></a></th>
																<?php } ?>
																<?php if (($this->sess->user_type == 1) || $this->sess->user_group == 0) { ?>
																	<td>
																		<a href="<?php echo base_url(); ?>user/add/<?php echo $data[$i]->user_id; ?>" target="_blank"><font color='#0000FF'><?=$data[$i]->user_name;?><br /><?php if (($this->sess->user_type == 1) && $data[$i]->user_payment_period) { echo ($data[$i]->user_payment_period >= 12) ? " (T)" : " (B)"; } ?></font></a>
																		<span id="branch<?=$data[$i]->vehicle_id;?>"></span>
																	</td>
															
																<?php } else { ?>
															
																	<td><font color='#0000FF'><?=$data[$i]->user_name;?><br /></font></td>
															
																<?php } ?>
															</tr>
															
															<!--vehicle name-->
															<tr class="toprow" class='odd' id="tr<?=$data[$i]->vehicle_id;?>">
																<th align="right" ><a href="#" onclick="javascript:order('vehicle_name')"><?if ($sortby == 'vehicle_name') { echo '<u>'; }?><?=$this->lang->line("lvehicle"); ?><?if ($sortby == 'vehicle_name') { echo '</u>'; }?></a></th>
																<?php if ($this->sess->user_group == 0) { ?>
																	<td><a href="javascript:vform(<?php echo $data[$i]->vehicle_id; ?>)"><font color='#0000FF'><?=$data[$i]->vehicle_name;?></font><br />
																		</a><?=$data[$i]->vehicle_no;?></td>
																	<td><!--<a href="javascript:vform(<?php echo $data[$i]->vehicle_id; ?>)"><font color='#0000FF'><?=$data[$i]->vehicle_no;?>--></td>
																<?php if ($this->sess->user_type == 1) { printf("<br />(%s)", $data[$i]->vehicle_type); } ?></font></a>
															</tr>	
																
															<!--engine-->
															<tr class="toprow" class='odd' id="tr<?=$data[$i]->vehicle_id;?>">
																<th align="right"><?php echo "Engine"; ?></th>
																<?php if (in_array(strtoupper($data[$i]->vehicle_type), $this->config->item("vehicle_gtp"))) { ?>
																<td>
																	<div><?php echo $this->lang->line("lengine"); ?>
																		<span id="engine<?=$data[$i]->vehicle_id;?>">-</span>
																		<span id="startoff<?=$data[$i]->vehicle_id;?>"></span>
																		<span id="starton<?=$data[$i]->vehicle_id;?>"></span>
																	</div>
																<?php } ?>
																	<span id="speed<?=$data[$i]->vehicle_id;?>" style=""></span>
																</td>
																<?php } else { ?>
																<td width="1px;"><font color='#0000FF'><?=$data[$i]->vehicle_name;?></font></td>			
																<td width="1">
																		<font color='#0000FF'><?=$data[$i]->vehicle_no;?><br />
																		<?php if (in_array(strtoupper($data[$i]->vehicle_type), $this->config->item("vehicle_gtp"))) { ?>
																		<div><?php echo $this->lang->line("lengine"); ?>
																			<span id="engine<?=$data[$i]->vehicle_id;?>">-</span>
																		</div>
																	<?php } ?>
																	
																	<span id="speed<?=$data[$i]->vehicle_id;?>" style=""></span>
																	
																	<?php if ($this->sess->user_type == 1) { printf("<br />(%s)", $data[$i]->vehicle_type); } ?></font>
																	<?php } ?>
																	<?php if ($this->sess->user_type == 1) { printf("<br />(%s)", $data[$i]->vehicle_type); } ?>
																</td>
															</tr>
															
															<!-- driver -->
															<tr class="toprow" class='odd' id="tr<?=$data[$i]->vehicle_id;?>">
																<th align="right"><?php echo "Driver"; ?></th>
																<td>
																	<span id="driver<?=$data[$i]->vehicle_id;?>" style=""></span>
																</td>
															</tr>
															
															<!--datetime-->
															<tr class="toprow" class='odd' id="tr<?=$data[$i]->vehicle_id;?>">
																<th width="10%" align="right"><?=$this->lang->line("ldatetime"); ?></th>
																<td id="datetime<?=$data[$i]->vehicle_id;?>"></td>
															</tr>
															
															<!--position-->
															<tr class="toprow" class='odd' id="tr<?=$data[$i]->vehicle_id;?>">
																<th align="right"><?=$this->lang->line("lposition"); ?></th>
																<td>
																	<span id="geofence_location<?php echo $data[$i]->vehicle_id; ?>"></span>
																	<span id="position<?=$data[$i]->vehicle_id;?>" style=""></span><br />
																	<span id="coord<?=$data[$i]->vehicle_id;?>"></span>
																</td>
															</tr>
															
															<!--customer-->
															<tr class="toprow" class='odd' id="tr<?=$data[$i]->vehicle_id;?>">
															<?php if ($this->sess->user_group == 0) { ?>
																<th align="right">Customer</th>
															<?php } ;?>
																<!-- Cutomer Groups -->
																<?php if ($this->sess->user_group == 0) { ?>
																<td>
																	<span id="customer_groups<?=$data[$i]->vehicle_id;?>" ></span>
																</td>
																<? } ?>
																<!-- End Customer Groups -->
															</tr>
															
															<!--device -->
															<tr class="toprow" class='odd' id="tr<?=$data[$i]->vehicle_id;?>">
																<!--<th width="10%"><?=$this->lang->line("lcoordinate"); ?></th>-->
																<th width="12%" align="right"><?php echo "Device"; ?></th>
															
																<!--<td id="coord<?=$data[$i]->vehicle_id;?>" style="text-align: center;"></td>-->
																<td style="text-align: left;" valign="top">
																	<div style="position: relative; ">GPS</div>
																	<div style="position: relative; left : 20%;" id="signal<?=$data[$i]->vehicle_id;?>">-</div>
																	<div  id="fan_stt<?=$data[$i]->vehicle_id;?>" style="position: absolute;"></div> 
																	<div style="position: relative; left : 50%;" id="fan<?=$data[$i]->vehicle_id;?>"></div>
																	<?php if (in_array(strtoupper($data[$i]->vehicle_type), $this->config->item("vehicle_gtp"))) { ?>
																	<!--<div  style="position: absolute;"><?php echo $this->lang->line("lengine"); ?></div> <div style="position: relative; left : 50%;" id="engine<?=$data[$i]->vehicle_id;?>">-</div> -->
																		<?php 
																		if ($this->sess->user_type != 2 && $this->sess->user_agent != "1") {
																		if (in_array(strtoupper($data[$i]->vehicle_type), $this->config->item("vehicle_gtp_door"))) { ?>
																	<div style="position: absolute;"><?php echo $this->lang->line("ldoor"); ?></div>
																	<div style="position: relative; left : 50%;" id="door<?=$data[$i]->vehicle_id;?>">-</div>
																		<?php } } ?>
																		<?php if (in_array(strtoupper($data[$i]->vehicle_type), $this->config->item("vehicle_fuel"))) { ?>
																	<div style="position: absolute;"><?php echo $this->lang->line("lfuel"); ?></div>
																	<div style="position: relative; left : 50%;" id="fuel<?=$data[$i]->vehicle_id;?>">-</div>
																		<?php } ?>
																	<?php } ?>
																	<?php if ((($this->sess->user_type == 1) || (($this->sess->user_type == 3) && ($this->sess->user_agent_admin == 1)) || $this->sess->user_payment_pulsa)) { ?>
																	<div id="pulsadiv<?=$data[$i]->vehicle_id;?>" style="width: 100%; display: none;">
																		<div style="position: absolute;"><?php echo $this->lang->line("lpulse_remain"); ?> </div> <div style="position: relative; left : 50%;" id="pulsa<?=$data[$i]->vehicle_id;?>">-</div>
																	</div>
																	<div id="masaktifdiv<?=$data[$i]->vehicle_id;?>" style="width: 100%; display: none;">
																		<div style="position: absolute;"><?php echo $this->lang->line("lmasa_aktif"); ?> </div> <div style="position: relative; left : 50%;" id="masaktif<?=$data[$i]->vehicle_id;?>">-</div>
																	</div>
																	<?php } ?>

																	<?php if ($this->sess->user_group == 0) { ?>
																	<?php echo "Card :"." ".$data[$i]->vehicle_card_no;?>
																	<?php if ($this->sess->user_type == 1) { ?>
																	<br />
																	<span id="restart<?=$data[$i]->vehicle_id;?>"></span>
																	<?php } ?>
																	
																	<?php if (($this->sess->user_type == 2) || ($this->sess->user_type == 3)) { ?>
																	<br />
																	<span id="restart_member<?=$data[$i]->vehicle_id;?>" style="display: none;" ></span>
																	<?php } } ?>
																</td>
															</tr>
															
															<!--icon follow /show map-->
															<tr class="toprow" class='odd' id="tr<?=$data[$i]->vehicle_id;?>">
																<!--<th width="4%"><?=$this->lang->line("lspeed"); ?></th>-->
																<th width="70px;" align="right">Follow</th>
																<!--<td style="text-align: right;">-->
																<!--<span id="speed<?= $data[$i]->vehicle_id;?>" style=""></span>-->
																<!--</td>-->
																<td>
																	<span id="map<?=$data[$i]->vehicle_id;?>" style="display: none;">
																		<a href="<?=base_url(); ?>map/realtime/<?=$data[$i]->vehicle_device_name;?>/<?=$data[$i]->vehicle_device_host;?>"><img src="<?=base_url();?>assets/images/realtime.png" width="20" border="0" alt="<?=$this->lang->line("lshow_map"); ?>" title="<?=$this->lang->line("lshow_map"); ?>"></a>
																	</span>
																</td>
															</tr>
															<span id="timestamp<?=$data[$i]->vehicle_device_name;?>_<?=$data[$i]->vehicle_device_host;?>" style="display: none;">0</span>
														<?php
														}
														?>

												</head>
											</table>
										</span>
										</div>