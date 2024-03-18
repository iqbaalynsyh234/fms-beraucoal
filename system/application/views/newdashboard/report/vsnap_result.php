<!-- gallery -->
<link href="<?php echo base_url();?>assets/dashboard/assets/plugins/light-gallery/css/lightgallery.css" rel="stylesheet">
<script src="<?php echo base_url();?>assets/dashboard/assets/plugins/light-gallery/js/lightgallery-all.js"></script>
<script src="<?php echo base_url();?>assets/dashboard/assets/plugins/light-gallery/js/image-gallery.js"></script>

							<div class="col-lg-6 col-sm-6">	
								<input id="btn_hide_form" class="btn btn-circle btn-danger" title="" type="button" value="Hide Form" onclick="javascript:return option_form('hide')" />
								<input id="btn_show_form" class="btn btn-circle btn-success" title="" type="button" value="Show Form" onClick="javascript:return option_form('show')" style="display:none"/>
							</div>
							<div class="col-lg-2 col-sm-2">	
							</div>
							<br />
							
<div class="row">
						<div class="col-sm-12">
							<div class="card-box">
								<div class="card-head">
									<header>SNAP PICTURE : <?=$vehicle->vehicle_no." ".$vehicle->vehicle_name;?></header>
									<!--<button id = "panel-button" 
			                           class = "mdl-button mdl-js-button mdl-button--icon pull-right" 
			                           data-upgraded = ",MaterialButton">
			                           <i class = "material-icons">more_vert</i>
			                        </button>
			                        <ul class = "mdl-menu mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect"
			                           data-mdl-for = "panel-button">
			                           <li class = "mdl-menu__item"><i class="material-icons">assistant_photo</i>Action</li>
			                           <li class = "mdl-menu__item"><i class="material-icons">print</i>Another action</li>
			                           <li class = "mdl-menu__item"><i class="material-icons">favorite</i>Something else here</li>
			                        </ul>
									-->
								</div>
								<div class="card-body row">
						            <div id="aniimated-thumbnials" class="list-unstyled row clearfix">
									
									<?php if(isset($data) && count($data)>0){ 
											for ($i=0;$i<count($data);$i++){?>
											 <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 m-b-20"> 
											 <a href="<?=$data[$i]->picture_url;?>" data-sub-html="<?=$vehicle->vehicle_no." ".$vehicle->vehicle_name;?> <?=date("d-m-Y H:i:s",strtotime($data[$i]->picture_datetime));?>"> 
												<img class="img-fluid img-thumbnail" src="<?=$data[$i]->picture_url;?>" alt="<?=$data[$i]->picture_imei."-".date("d-m-Y H:i:s",strtotime($data[$i]->picture_datetime));?>"> 
											 </a> <center><font style="align:center" size="2px"><?=$vehicle->vehicle_no." ".$vehicle->vehicle_name;?> <br /> (<?=date("d-m-Y H:i:s",strtotime($data[$i]->picture_datetime));?>)</font></center>
											 </div>
									<?php }}?>
									
			                        </div>
								</div>
							</div>
						</div>
					</div>
