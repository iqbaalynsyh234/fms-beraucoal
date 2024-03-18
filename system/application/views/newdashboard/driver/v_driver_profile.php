<!-- start sidebar menu -->
<div class="sidebar-container">
  <?=$sidebar;?>
</div>
<!-- end sidebar menu -->

<!-- start page content -->
<div class="page-content-wrapper">
  <div class="page-content">
    <br>
    <?php if ($this->session->flashdata('notif')) {?>
      <div class="alert alert-success" id="notifnya" style="display: none;"><?php echo $this->session->flashdata('notif');?></div>
    <?php }?>
    <div class="alert alert-success" id="notifnya2" style="display: none;"></div>
    <div class="row">
      <div class="col-md-12">
                            <!-- BEGIN PROFILE SIDEBAR -->
                            <div class="profile-sidebar">
                                <div class="card card-topline-aqua">
                                    <div class="card-body no-padding height-9">
                                        <div class="row">
                                            <div class="profile-userpic">
                                                <!--<img src="<?=base_url();?>assets/bib/images/dp.jpg" class="img-responsive" alt=""> -->
												<?php if(isset($row->driver_image)){ ?>
													<!--<img src="<?php echo base_url().$this->config->item("dir_photo").$row->driver_image_raw_name.$row->driver_image_file_ext;?>" class="img-responsive" alt="">--> 
													<!--<img src="<?=$row->driver_image;?>" class="img-responsive" alt="" /> -->
													<img src="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" class="img-responsive" alt="" /> 
												<?php } ?>
											</div>
										</div>
                                        <div class="profile-usertitle">
                                            <div class="profile-usertitle-name"><?=$row->driver_name;?></div>
                                            <div class="profile-usertitle-job">Profesional Driver</div>
											<input type="hidden" name="now_coord" id="now_coord" class="form-control" value="" >
											
                                        </div>
                                        <ul class="list-group list-group-unbordered">
											
                                            <li class="list-group-item">
                                                <b>Ritase Hari Ini</b> <a class="pull-right">-</a>
                                            </li>
                                            <li class="list-group-item">
                                                <b>Pelanggaran Hari Ini</b> <a class="pull-right">-</a>
                                            </li>
                                           
											<?php if($total_absensi > 0){ ?>
												<?php if($datalast_absensi->absensi_status == 1){ ?>
													<li class="list-group-item">
														<b><font color="green">Anda Sudah Absen</font></b> <a class="pull-right" href="<?=base_url()?>driver/clockout">Klik Jam Keluar</a>
													</li>
													
													<li class="list-group-item">
														<b>Jam Masuk</b> <a class="pull-right"><?=date("d-m-Y H:i:s", strtotime($datalast_absensi->absensi_clock_in));?> WITA</a>
													</li>
													
													<li class="list-group-item">
														<b><font color="red">Breakdown Mulai</font></b> <a class="pull-right" href="<?=base_url()?>driver/breakdownstart">Klik Disini</a>
													</li>
													<li class="list-group-item">
														<b><font color="green">Breakdown Selesai</font></b> <a class="pull-right" href="<?=base_url()?>driver/breakdownfinish">Klik Disini</a>
													</li>
													
													<?php if($datalast_absensi->absensi_vehicle_no == ""){ ?>
														<li class="list-group-item">
															<input type="hidden" name="absensi_id" id="absensi_id" class="form-control" value="<?php echo $datalast_absensi->absensi_id; ?>" >
															<input type="hidden" name="absensi_id2" id="absensi_id2" class="form-control" value="<?php echo $datalast_absensi->absensi_id; ?>" >
															<input type="hidden" name="absensi_idcard" id="absensi_idcard" class="form-control" value="<?php echo $datalast_absensi->absensi_driver_idcard; ?>" >
															<input type="hidden" name="absensi_clock_in" id="absensi_clock_in" class="form-control" value="<?php echo $datalast_absensi->absensi_clock_in; ?>" >
															<input type="hidden" name="absensi_clock_in2" id="absensi_clock_in2" class="form-control" value="<?php echo $datalast_absensi->absensi_clock_in; ?>" >
															<input type="hidden" name="absensi_driver_name" id="absensi_driver_name" class="form-control" value="<?php echo $datalast_absensi->absensi_driver_name; ?>" >
															<input type="hidden" name="absensi_vehicle_no" id="absensi_vehicle_no" class="form-control" value="<?php echo $datalast_absensi->absensi_vehicle_no; ?>" >
															<input type="hidden" name="absensi_driver_id" id="absensi_driver_id" class="form-control" value="<?php echo $datalast_absensi->absensi_driver_id; ?>" >
															
															<span id="txt_belum_terhubung"><b><font style="font-size:12px;">Belum terhubung ke kendaraan</font></b></span> 
															<a class="pull-right" onclick="javascript:getsyncunit();">
																<font color="green">HUBUNGKAN</font>
																 <img id="loaderhb" style="display: none;" src="<?php echo base_url();?>assets/images/ajax-loader.gif" />
															</a>
														</li>
													
													
														<li class="list-group-item" id="sync_now_unit_show" style="display:none;">
															<b>Kendaraan</b> <a class="pull-right"><span id='sync_now_unit'><span></a>
														</li>
														
														<li class="list-group-item" id="available_unit" style="display:none;">
															<select class="form-control" id="vehicle_manual" name="vehicle_manual">
																<option value="0">--Pilih Kendaraan</option>
															<?php for ($i = 0; $i < sizeof($datavehicle_bycontractor); $i++) { ?>
																<option value="<?php echo $datavehicle_bycontractor[$i]['vehicle_id'] ?>"><?php echo $datavehicle_bycontractor[$i]['vehicle_no'] ?></option>
															<?php } ?>
																 
															
															 
															</select><br />
															<a class="pull-right" onclick="javascript:getsyncunit_manual();">
																<font color="green">HUBUNGKAN MANUAL</font>
																 <img id="loaderhbm" style="display: none;" src="<?php echo base_url();?>assets/images/ajax-loader.gif" />
															</a>
														</li>
														
														
						
						
														<li class="list-group-item" id="sync_now_time_show" style="display:none;">
															<b>Terdeteksi</b> <a class="pull-right"><span id='sync_now_time'></span> WITA</a>
														</li>
														
														 <div class="profile-userbuttons" id="sync_now_sos_show" style="display:none;">
														
															<a onclick="javascript:sospress();" ><img src="<?=base_url();?>assets/bib/images/sosbutton3.png" alt="TOMBOL SOS" width="100px" height="100px"></a>
															<br />
															<font style="font-size:10px;">dalam keadaan darurat, tekan tombol diatas</font><br />
															<span id="txt_sos_lastpress" style="display:none;" ><font style="font-size:10px;color='red';">terakhir ditekan <?=date("d-m-Y H:i:s");?> WITA</font></font></span>
														 </div>
														
													<?php } ?>
													
													
													<?php if($datalast_absensi->absensi_clock_out_status > 0){ ?>
													<li class="list-group-item">
														<b>Jam Keluar</b> <a class="pull-right"><?=date("d-m-Y H:i:s", strtotime($datalast_absensi->absensi_clock_out));?> WITA</a>
													</li>
													<?php } ?>
													
													<?php if($datalast_absensi->absensi_vehicle_no != ""){ ?>
														<li class="list-group-item">
															<b>Kendaraan</b> <a class="pull-right"><?=$datalast_absensi->absensi_vehicle_no;?></a>
														</li>
														<li class="list-group-item">
															<b>Terdeteksi</b><a class="pull-right"><?=date("d-m-Y H:i:s", strtotime($datalast_absensi->absensi_face_detected));?> WITA</a>
														</li>
													<?php } ?>
												
												
												<?php } ?>
											
											
												<?php if($datalast_absensi->absensi_status > 1){ ?>
													<li class="list-group-item">
														<b><font color="green">Absensi Terakhir</font></b> <a class="pull-right" href="<?=base_url()?>driver/clockin">Klik Jam Masuk</a>
													</li>
													
													<li class="list-group-item">
														<b>Jam Masuk</b> <a class="pull-right"><?=date("d-m-Y H:i:s", strtotime($datalast_absensi->absensi_clock_in));?> WITA</a>
													</li>
												
													<li class="list-group-item">
														<b>Jam Keluar</b><a class="pull-right"><?=date("d-m-Y H:i:s", strtotime($datalast_absensi->absensi_clock_out));?> WITA</a>
													</li>
													
												
												<?php } ?>
												
											<?php }else{ ?>
												
												<li class="list-group-item">
													<b><font color="red">Anda Belum Absen</font></b> <a class="pull-right" href="<?=base_url()?>driver/clockin">Klik Jam Masuk</a>
												</li>
												
											<?php } ?>
											
                                        </ul>
                                        <!-- END SIDEBAR USER TITLE -->
                                        <!-- SIDEBAR BUTTONS -->
										
										
										<?php 
										if(count($datalast_absensi) > 0){
										
										if($datalast_absensi->absensi_vehicle_no != "" && $datalast_absensi->absensi_status == 1){ ?>
											<div class="profile-userbuttons" id="sos_button_on_default">
												<a onclick="javascript:sospress();" ><img src="<?=base_url();?>assets/bib/images/sosbutton3.png" alt="TOMBOL SOS" width="100px" height="100px"></a>
												<br />
												<font style="font-size:10px;">dalam keadaan darurat, tekan tombol diatas</font> <br />
												<span id="txt_sos_lastpress" style="display:none;"><font style="font-size:10px;color='red';">terakhir ditekan <?=date("d-m-Y H:i:s");?> WITA</font></span>
											</div>
											<?php }else{ ?>
											<div class="profile-userbuttons" id="sos_button_off_default">
												<img src="<?=base_url();?>assets/bib/images/sosbutton3b.png" alt="TOMBOL SOS" width="100px" height="100px">
												<br />
												<font style="font-size:10px;">tombol akan aktif jika Anda sudah verifikasi wajah dan berada di dalam unit</font>
											</div>
										<?php } }?>
										 
                                        <!-- END SIDEBAR BUTTONS -->
                                    </div>
                                </div>
                               
                                
                            </div>
                            <!-- END BEGIN PROFILE SIDEBAR -->
							
							
                            <!-- BEGIN PROFILE CONTENT -->
                            <div class="profile-content">
                                <div class="row">
								
								<div class="card col-md-10">
                                    <div class="card-head card-topline-aqua">
                                        <header>Profile Saya <small>(iSafe)</header>
                                    </div>
                                    <div class="card-body no-padding height-9">
                                        <div class="profile-desc">
                                           
                                        </div>
                                        <ul class="list-group list-group-unbordered">
											<li class="list-group-item">
                                                <b>ID Simper</b>
                                                <div class="profile-desc-item pull-right"><?=$row->driver_idcard;?></div>
                                            </li>
											<li class="list-group-item">
                                                <b>Nama Pengemudi</b>
                                                <div class="profile-desc-item pull-right"><?=$row->driver_name;?></div>
                                            </li>
                                            <li class="list-group-item">
                                                <b>Nomor Handphone</b>
                                                <div class="profile-desc-item pull-right"><small><?=$row->driver_mobile;?></small></div>
                                            </li>
                                            <li class="list-group-item">
                                                <b>Alamat</b>
                                                <div class="profile-desc-item pull-right"><?=$row->driver_address;?></div>
                                            </li>
                                            
											<li class="list-group-item pull-justify">
												<div class="profile-userbuttons">
												Status Vaksinasi<br />
												<!--<small>Pindai kode QR berikut untuk melihat status vaksinasi Anda</small> <br />
													<a href="<?=$row->driver_vaksin_1;?>" target="_blank"><img src="<?=$row->driver_vaksin_1;?>" alt="<?=$row->driver_vaksin_status;?>" style="width:100px;height:100px;" ></a>
												-->
												
												<b><?=$row->driver_vaksin_status;?></b>
												</div>
											</li>
                                        </ul>
                                        <div class="row list-separated profile-stat" style="display:none;">
                                            <div class="col-md-4 col-sm-4 col-6">
                                                <div class="uppercase profile-stat-title"> 37 </div>
                                                <div class="uppercase profile-stat-text"> Projects </div>
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-6">
                                                <div class="uppercase profile-stat-title"> 51 </div>
                                                <div class="uppercase profile-stat-text"> Tasks </div>
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-6">
                                                <div class="uppercase profile-stat-title"> 61 </div>
                                                <div class="uppercase profile-stat-text"> Uploads </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>								
                                </div>
                            </div>
                                <!-- END PROFILE CONTENT -->
     </div>
	</div>
</div>
</div>

<script >
  function getsyncunit()
		{
			var absensi_id = document.getElementById("absensi_id").value;
			var absensi_idcard = document.getElementById("absensi_idcard").value;
			var absensi_clock_in = document.getElementById("absensi_clock_in").value;
			jQuery("#loaderhb").show();
			jQuery.post('<?php echo base_url()?>'+'driver/getsyncunit_byidcard/', {absensi_id:absensi_id, absensi_idcard: absensi_idcard,absensi_clock_in:absensi_clock_in },
			function(r)
			  {
				jQuery("#loaderhb").hide();
				if (r.error)
				{
				  alert(r.message);
				  document.getElementById("txt_belum_terhubung").innerHTML = '<font color='+'black'+'><b>Gunakan Hubungkan Manual</b></font>';
				  jQuery("#available_unit").show();
				  return false;
				}
				
				var nolambung = r.vehicle_no;
				var facedetect = r.face_detect;
				jQuery("#sync_now_unit_show").show();
				jQuery("#sync_now_time_show").show();
				jQuery("#sync_now_sos_show").show();
				jQuery("#sos_button_off_default").hide();
				
				
				document.getElementById("txt_belum_terhubung").innerHTML = '<font color='+'green'+'><b>Berhasil Terhubung</b></font>';
				document.getElementById("sync_now_unit").innerHTML = nolambung;
				document.getElementById("sync_now_time").innerHTML = facedetect;
			  }
			  , "json"
			);
			return false;
		}
		
		function getsyncunit_manual()
		{
			
			var absensi_id = document.getElementById("absensi_id").value;
			var absensi_idcard = document.getElementById("absensi_idcard").value;
			var absensi_clock_in = document.getElementById("absensi_clock_in").value;
			
			var absensi_vehicle_manual = document.getElementById("vehicle_manual").value;
		
			
			jQuery("#loaderhbm").show();
			jQuery.post('<?php echo base_url()?>'+'driver/getsyncunit_byidcard_manual/', {absensi_id:absensi_id, absensi_idcard: absensi_idcard,absensi_clock_in:absensi_clock_in,absensi_vehicle_manual:absensi_vehicle_manual},
			function(r)
			  {
				jQuery("#loaderhbm").hide();
				if (r.error)
				{
				  alert(r.message);
				  return false;
				}
				
				var nolambung = r.vehicle_no;
				var facedetect = r.face_detect;
				jQuery("#sync_now_unit_show").show();
				jQuery("#sync_now_time_show").show();
				jQuery("#sync_now_sos_show").show();
				jQuery("#sos_button_off_default").hide();
				jQuery("#available_unit").hide();
				
				
				
				document.getElementById("txt_belum_terhubung").innerHTML = '<font color='+'green'+'><b>Berhasil Terhubung</b></font>';
				document.getElementById("sync_now_unit").innerHTML = nolambung;
				document.getElementById("sync_now_time").innerHTML = facedetect;
			  }
			  , "json"
			);
			return false;
		}
		
		function sospress()
		{
			
			if (confirm('Apa Anda Yakin Menekan Tombol SOS ? #1')) {
			  // Save it!
			 if (confirm('Apa Anda Yakin Menekan Tombol SOS ? #2')) {
				// Save it!
					//sospress3();
					alert('Anda telah menekan tombol SOS');
					jQuery("#txt_sos_lastpress").show();
					
				} else {
				  // Do nothing!
				 
				 
				}
			} else {
			  // Do nothing!
			 
			}
			
		}
		
		function sospress2()
		{
					alert('sos 2');
					var absensi_driverid = document.getElementById("absensi_driverid").value;
					var absensi_driver_name = document.getElementById("absensi_driver_name").value;
					var absensi_idcard = document.getElementById("absensi_idcard").value;
					var absensi_clock_in = document.getElementById("absensi_clock_in").value;
					var now_coord = document.getElementById("now_coord").value;
					var absensi_vehicle_no = document.getElementById("absensi_vehicle_no").value;
					//jQuery("#loaderhb").show();
					jQuery.post('<?php echo base_url()?>'+'driver/sosdigital_save/', {absensi_driverid:absensi_driverid, absensi_vehicle_no:absensi_vehicle_no, now_coord:now_coord, absensi_idcard: absensi_idcard, absensi_driver_name:absensi_driver_name },
					function(r)
					  {
						//jQuery("#loaderhb").hide();
						if (r.error)
						{
						  alert(r.message);
						  location = r.redirect;
						  return false;
						}
						
						alert(r.message);
					  }
					  , "json"
					);
					return false;
		}
		
		function sospress3()
		{
					alert('sos');
					var absensi_driverid = document.getElementById("absensi_driver_id").value;
					var absensi_driver_name = document.getElementById("absensi_driver_name").value;
					var absensi_idcard = document.getElementById("absensi_idcard").value;
					var absensi_clock_in = document.getElementById("absensi_clock_in").value;
					var now_coord = document.getElementById("now_coord").value;
					var absensi_vehicle_no = document.getElementById("absensi_vehicle_no").value;
					
					alert(absensi_driverid);
					/* alert(absensi_driver_name);
					alert(absensi_idcard);
					alert(absensi_clock_in);
					alert(now_coord);
					alert(absensi_vehicle_no); */
					//jQuery("#loaderhb").show();
					jQuery.post('<?php echo base_url()?>'+'driver/sosdigital_save/', {absensi_driverid:absensi_driverid, absensi_vehicle_no:absensi_vehicle_no, now_coord:now_coord, absensi_idcard: absensi_idcard, absensi_driver_name:absensi_driver_name },
					function(r)
					  {
						if (r.error)
						{
						  //alert(r.message);
						  alert('ok');
						  /* document.getElementById("txt_belum_terhubung").innerHTML = '<font color='+'black'+'><b>Gunakan Hubungkan Manual</b></font>';
						  jQuery("#available_unit").show(); */
						  return false;
						}
						
						//alert(r.message);
					  }
					  , "json"
					);
					return false;
					
					
		}
</script>

<!-- if (r.row_image == "") {
img_driver = '<img src="<?php echo base_url().$this->config->item("dir_photo").$this->config->item("default_photo_driver");?>" width="256px" height="256px" />';
 }else {
   img_driver = '<img src="<?php echo base_url().$this->config->item("dir_photo").$row_image->driver_image_raw_name.$row_image->driver_image_file_ext;?>" width="256px" height="256px" />';
 } -->
