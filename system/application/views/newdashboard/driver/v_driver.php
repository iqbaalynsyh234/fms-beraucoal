<style media="screen">
div#modalchangepass {
  margin-top: 2%;
  margin-left: 18%;
  max-height: 300px;
  max-width: 400px;
  position: absolute;
  background-color: #f1f1f1;
  text-align: left;
  border: 1px solid #d3d3d3;
}

div#modalassignvehicle {
  margin-top: 2%;
  margin-left: 18%;
  max-height: 500px;
  max-width: 900px;
  overflow-x: auto;
  position: absolute;
  z-index: 9;
  background-color: #f1f1f1;
  text-align: left;
  border: 1px solid #d3d3d3;
}

div#modaluploadvehicle {
  margin-top: 2%;
  margin-left: 18%;
  position: absolute;
  z-index: 9;
  background-color: #f1f1f1;
  text-align: left;
  border: 1px solid #d3d3d3;
  width: 38%;
}
</style>
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
    <!--<div class="alert alert-success" id="notifnya2" style="display: none;"></div>-->
    <div class="row">
      <div class="col-md-12" id="tablecustomer">
        <div class="panel" id="panel_form">
          <header class="panel-heading panel-heading-red">Driver Data</header>
          <div class="panel-body" id="bar-parent10">
              <table id="example1" class="table table-striped" style="font-size:12px;">
                <thead>
          				<tr>
							<th>
							  <button type="button" class="btn btn-success btn-xs" onclick="showaddcustomer()" title="Add New Driver">
								<span class="fa fa-plus"></span>
							  </button>No
							</th>
          					<th style="text-align:center;">Name</th>
							<th style="text-align:center;">IDSimper</th>
							<th style="text-align:center;">Phone</th>
							<th style="text-align:center;">Register Date</th>
							<th style="text-align:center;">Akun</th>
							<th style="text-align:center;">Status Vaksinasi</th>
							<th style="text-align:center;">Sertifikat Vaksin</th>

							<!--<th>Assigned Vehicle</th>-->
          					<th style="text-align:center;">Control</th>
          				</tr>
          			</thead>
                <tbody>
                  <?php for($i=0;$i<count($data);$i++) { ?>
          				  <tr>
            					<td width="2%" style="text-align:center;"><?=$i+1?></td>
								<td width="10%" style="text-align:center;"><?=$data[$i]->driver_name;?></td>
								<td width="15%" style="text-align:center;"><?=$data[$i]->driver_idcard;?></td>
            					<td width="10%" style="text-align:center;"><?=$data[$i]->driver_mobile;?></td>
            					<td width="15%" style="text-align:center;"><?=$data[$i]->driver_joint_date;?></td>
            					<td width="10%" style="text-align:center;"><?=$data[$i]->driver_idcard;?></td>
								<td width="15%" style="text-align:center;">
								<?php
									if($data[$i]->driver_vaksin_status == "Sudah Vaksin Kedua"){
										$vaksin_color = "success";
									}else if($data[$i]->driver_vaksin_status == "Sudah Vaksin Pertama"){
										$vaksin_color = "warning";
									}else if($data[$i]->driver_vaksin_status == "Sudah Vaksin Ketiga"){
										$vaksin_color = "success";
									}else{
										$vaksin_status = "Belum Vaksin";
										$vaksin_color = "danger";
									}

								?>

									<button type="button" class="btn btn-circle btn-<?=$vaksin_color;?> btn-sm m-b-10"><?=$data[$i]->driver_vaksin_status;?></button>

								</td>


								<td width="20%" style="text-align:center;">
									<div class="btn-group btn-group-solid  btn-sm m-b-10">
										<a href="<?=$data[$i]->driver_vaksin_1;?>" target="_blank" type="button" class="btn btn-default">Lihat QRCode</a>
									</div>
								</td>


            					<!--<td width="10%">
            						<?php
              						if (isset($row2)) {
              							foreach ($row2 as $vehicle) {
              								if ($vehicle['vehicle_id'] == $data[$i]->driver_vehicle) {
              									echo $vehicle['vehicle_no'] . " - ". $vehicle['vehicle_name'];
              								}
              							}
              						}
          							?>
            					</td>-->
      							<td width="20%" style="text-align:left;">
            						<!--<a href="javascript:assignvehicle(<?php echo $data[$i]->driver_id;?>)">
            							<img src="<?=base_url();?>assets/images/update.png" width="16px" height="16px" border="0" alt="<?php echo "Assign Vehicle"; ?>" title="<?php echo "Assign Vehicle"; ?>">
            						</a>-->
									<a href="<?=base_url();?>driver/profile/<?=$data[$i]->driver_id;?>">
										<img src="assets/transporter/images/driver_photo.png" width="18px" height="18px" border="0" border="0" alt="<?=$this->lang->line("ledit_data"); ?>" title="<?=$this->lang->line("ledit_data"); ?>">
									</a>
									<a href="<?=base_url();?>driver/edit/<?=$data[$i]->driver_id;?>">
										<img src="<?=base_url();?>assets/images/edit.gif" border="0" alt="<?=$this->lang->line("ledit_data"); ?>" title="<?=$this->lang->line("ledit_data"); ?>">
									</a>

									<!--<a href="javascript:driver_image(<?php echo $data[$i]->driver_id;?>)">
										<img src="<?=base_url();?>assets/transporter/images/driver_photo.png" width="16px" height="16px" border="0" alt="<?php echo "Upload Photo"; ?>" title="<?php echo "Upload Photo"; ?>">
									</a>-->

									<a href="javascript:delete_data(<?php echo $data[$i]->driver_id;?>)">
										<img src="<?=base_url();?>assets/images/trash.gif" border="0" alt="<?=$this->lang->line("lremove_data"); ?>" title="<?=$this->lang->line("lremove_data"); ?>">
									</a>
      							 </td>
                    </tr>
                  <? } ?>
  							</tbody>
  						</table>
            </div>
      </div>
    </div>

  <div class="col-md-12" id="formaddcustomer" style="display: none; width:1000px;">
    <div class="panel" id="panel_form">
      <header class="panel-heading panel-heading-red">Add Driver</header>
      <div class="panel-body" id="bar-parent10">
        <form  class="block-content form" name="frmadd" id="frmadd" onsubmit="javascript: return frmadd_onsubmit()">
    			<table width="100%" cellpadding="3" class="table sortable no-margin">
    				<tr>
    				<input type="hidden" name="driver_company" id="driver_company"  value="<?php echo $this->sess->user_company;?>" /></td>
    				<input type="hidden" name="driver_group" id="driver_group"  value="<?php echo $this->sess->user_group;?>" /></td>
    				</tr>
    				<tr>
    					<td>Name</td>
    					<td>:</td>
    					<td><input type="text" class="form-control" name="driver_name" /></td>
    				</tr>
    				<tr>
    					<td>ID Driver</td>
    					<td>:</td>
    					<td><input type="text" class="form-control" name="driver_idcard" /></td>
    				</tr>
            <tr>
              <td>RFID</td>
              <td>:</td>
              <td>
                <input type="text" name="driver_rfid" id="driver_rfid" class="form-control">
              </td>
            </tr>
    				<tr>
    					<td>Address</td>
    					<td>:</td>
    					<td><textarea rows="5" class="form-control" name="driver_address" ></textarea></td>
    				</tr>
    				<tr>
    					<td>Phone</td>
    					<td>:</td>
    					<td><input type="text" class="form-control" name="driver_phone" /></td>
    				</tr>
    				<tr>
    					<td>Mobile</td>
    					<td>:</td>
    					<td>
    					1.<input type="text" class="form-control" name="driver_mobile" />
    					2.<input type="text" class="form-control" name="driver_mobile2" />
    					</td>
    				</tr>
    				<tr>
    					<td>ID Card</td>
    					<td>:</td>
    					<td><textarea rows="5" class="form-control" name="driver_address" ></textarea></td>
    				</tr>
    				<tr>
    					<td>Licence</td>
    					<td>:</td>
    					<td>
                <input size="5" class="form-control" type="text" maxlength="5" name="driver_licence" />
    					</td>
    				</tr>
            <tr>
    					<td>Licence No</td>
    					<td>:</td>
    					<td>
					       <input type="text" class="form-control" name="driver_licence_no" />
              </td>
    				</tr>
    				<tr>
    					<td>Licence Expired</td>
    					<td>:</td>
    					<td>
                <div class="input-group date form_date col-md-4" data-date="" data-date-format="dd-mm-yyyy" data-link-format="yyyy-mm-dd">
                    <!-- <input class="form-control" size="5" type="text" readonly name="duedate" id="duedate" value="<?=date('d-m-Y')?>"> -->
                    <input type="text" class="form-control" maxlength="15" name="driver_licence_expired" id="driver_licence_expired" value="<?=date('d-m-Y')?>" /><br />
                    <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                </div>
    					</td>
    					</td>
    				</tr>
    				<tr>
    					<td>Sex</td>
    					<td>:</td>
    					<td>
    						<select name="driver_sex" class="form-control">
    							<option value="M">Male</option>
    							<option value="F">Female</option>
    						</select>
    					</td>
    				</tr>
    				<tr>
    					<td>Joint Date</td>
    					<td>:</td>
    					<td>
                <div class="input-group date form_date col-md-4" data-date="" data-date-format="dd-mm-yyyy" data-link-format="yyyy-mm-dd">
                    <!-- <input class="form-control" size="5" type="text" readonly name="duedate" id="duedate" value="<?=date('d-m-Y')?>"> -->
                    <input type="text" class="form-control" maxlength="15" name="driver_joint_date" id="joint_date" value="<?=date('d-m-Y')?>" /><br />
                    <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                </div>
                <!-- <input type="text" class="form-control" maxlength="15" name="driver_joint_date" id="joint_date" class="date-pick" /> -->
              </td>
    					</td>
    				</tr>
    				<!-- <tr>
    					<td>
                SIOF <br>
                <small style="color:red;">Surat Ijin Operation Forklip</small>
              </td>
    					<td>:</td>
    					<td>
    						<input type="text" class="form-control" maxlength="15" name="driver_siof" id="driver_siof" /><br />
    					</td>
    					</td>
    				</tr> -->
    				<!-- <tr>
    					<td>
                SIOF Expired <br>
                <small style="color:red;">Surat Ijin Operation Forklip</small>
              </td>
    					<td>:</td>
    					<td>
                <div class="input-group date form_date col-md-4" data-date="" data-date-format="dd-mm-yyyy" data-link-format="yyyy-mm-dd">
                    <input type="text" class="form-control" maxlength="15" name="driver_siof_expired" id="driver_siof_expired" value="<?=date('d-m-Y')?>" /><br />
                    <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                </div>
    					</td>
    				</tr> -->
    				<!-- <tr>
    					<td>Note</td>
    					<td>:</td>
    					<td><textarea rows="5" class="form-control" name="driver_note" ></textarea></td>
    				</tr> -->
    				<tr>
    					<td>&nbsp;</td>
    						<td>&nbsp;</td>
    						<td>
    								<input type="button" class="btn btn-warning" name="btncancel" id="btncancel" value=" Cancel " onclick="location='<?=base_url()?>driver';" />
                    <input type="submit" class="btn btn-success" name="btnsave" id="btnsave" value=" Save " />
    								<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
    						</td>
    				</tr>
    			</table>
    		</form>
      </div>
    </div>
  </div>
</div>
</div>
</div>

<div id="modalassignvehicle" style="display: none;">
  <div id="mydivheader"></div>
  <div class="row" >
    <div class="col-md-12">
        <div class="card card-topline-yellow">
            <div class="card-head">
                <header>Assign Vehicle</header>
                <div class="tools">
                    <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
                  <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                  <button type="button" class="btn btn-danger" name="button" onclick="closemodalassignvehicle();">X</button>
                </div>
            </div>
            <div class="card-body">
              <table width="100%" cellpadding="8" class="table sortable no-margin">
                <tr>
                  <td>Driver ID</td>
                  <td>
                    <div id="driveridnya"></div>
                  </td>
                </tr>

                <tr>
                  <td>Driver Name</td>
                  <td>
                    <div id="drivernamenya"></div>
                  </td>
                </tr>

                <tr>
                  <td>Status</td>
                  <td>
                    <div id="driverstatusnya"></div>
                  </td>
                </tr>

                <tr>
                  <td>Choose Vehicle</td>
                  <td>
                    <select name="vehicle_choosed" id="vehicle_choosed" class="select2">
                      <option value="">--Choose Vehicle--</option>
                      <option value="makeavailable">Make Available</option>
                      <?php foreach ($row2 as $key => $value) {?>
                        <option value="<?php echo $value['vehicle_id']; ?>"><?php echo $value['vehicle_no']; ?> - <?php echo $value['vehicle_name']; ?></option>
                      <? } ?>
                    </select>
                  </td>
                </tr>
              </table>
              <div class="text-right">
                <input type="button" class="btn btn-warning" value="Cancel" onclick="closemodalassignvehicle();" />
                <input type="submit" class="btn btn-success" name="submit" value="Assign" onclick="gotoassignvehicle()">
              </div>
            </div>
        </div>
    </div>
  </div>
</div>

<div id="modaluploadvehicle" style="display: none;">
  <div id="modaluploadvehicleheader"></div>
  <div class="row" >
    <div class="col-md-12">
        <div class="card card-topline-yellow">
            <div class="card-head">
                <header>Upload Image</header>
                <div class="tools">
                    <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
                  <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                  <button type="button" class="btn btn-danger" name="button" onclick="closemodaluploadvehicle();">X</button>
                </div>
            </div>
            <div class="card-body">
              <form method="post" enctype="multipart/form-data" action="<?php echo base_url();?>driver/save_image">
              	<table class="table">
              		<tr>
              			<td width="30%">
                      <img id="driver_image_preview" src="<?php echo base_url().$this->config->item("dir_photo").$this->config->item("default_photo_driver");?>" width="100px" height="100px"/>
              			</td>
                    <td>
                    </td>
                  </tr>
                  <tr>
                    <td>Name</td>
              			<td>
                      <span id="driver_name_formupload"></span>
                    </td>
                  </tr>
                  <tr>
                    <td>ID Card</td>
                    <td>
                      <span id="id_card_formupload"></span>
                    </td>
                  </tr>
                  <tr>
                    <td>Mobile</td>
                    <td>
                      <span id="mobile_formupload"></span>
                    </td>
                  </tr>
                  <tr>
                    <td>Sex</td>
                    <td>
                      <span id="sex_formupload"></span>
                    </td>
                  </tr>

                  <tr>
                    <td></td>
                    <td>
              				<?php if ($this->sess->user_group == 0) { ?>
            				      <small><span>Change Picture Driver</span></small>
              				<div id="error_upload"></div>
                				<input type="hidden" name="driver_id" id="driver_id_formupload" />
                				<input type="file" name="userfile" size="20" class="form-control"/>
                        <small style="color:red;">Note : File images will be resize to 256 x 256 px</small>
              				<br />
                				<div class="text-right">
                          <button type="button" onclick="closemodaluploadvehicle();" class="btn btn-warning"/>Cancel</button>
                          <button type="submit" class="btn btn-success"/>Upload</button>
                        </div>
              				<?php } ?>
              			</td>
              		</tr>
              	</table>
            	</form>
            </div>
        </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  // $("#notifnya").fadeIn(1000);
  // $("#notifnya").fadeOut(5000);
  var driver_name, driver_id;

  function showaddcustomer(){
    $("#formaddcustomer").show();
    $("#tablecustomer").hide();
  }

  function btncancel(){
    $("#formaddcustomer").hide();
    $("#tablecustomer").show();
  }

  function btnDelete(id){
    $("#iddelete").val(id);
    $("#modalDeletedest").fadeIn(1000);
  }

  function closemodalassignvehicle(){
    $("#modalDeletedest").fadeOut(1000);
  }

  function frmadd_onsubmit()
	{
		jQuery("#loader").show();
		jQuery.post("<?=base_url()?>driver/save", jQuery("#frmadd").serialize(),
			function(r)
			{
				jQuery("#loader").hide();
				if (r.error)
				{
					alert(r.message);
					return false;
				}

				alert(r.message);
				location = r.redirect;
			}
			, "json"
		);
		return false;
	}

  function assignvehicle(idnya){
    console.log("idnya : ", idnya);
    $.post('<?php echo base_url(); ?>driver/getVehicle/', {id: idnya},
      function(response)
      {
        driver_id   = response.driver_id;
        driver_name = response.row.driver_name;
        $("#driveridnya").html(driver_id);
        $("#drivernamenya").html(driver_name);
        $("#driverstatusnya").html(response.row2);
        $("#modalassignvehicle").fadeIn(1000);
        console.log("response assignvehicle : ", response);
      }
      , "json"
    );
  }

  function gotoassignvehicle(){
    var vehicle = jQuery("#vehicle_choosed").val();
    if (vehicle == "") {
      alert("Vehicle Can Not Empty");
    }else {
      driverid   = driver_id;
      drivername = driver_name;
      var user_id     = '<?php echo $this->sess->user_id; ?>';

      var data = {driver_id : driverid, driver_name: drivername, user_id : user_id, vehicle_id : vehicle};
      // console.log("data : ", data);
      $.post('<?=base_url()?>driver/assignnow/', data, function(response){
        if (response.msg == "error") {
          if (confirm("Update Data Failed")) {
          }
        }else if (response.msg == "already") {
					alert("Vehicle Already Set To Another Driver. Please Select Another Vehicle");
        }else if (response.msg == "notalready") {
					alert("Please Refresh This Page");
        }else{
          console.log("Berhasil terima data : ", response);
          if (confirm("Update Data Success")) {
            window.location = '<?=base_url()?>driver';
          }
        }
      }, "json");
    }
  }

  function closemodalassignvehicle(){
    $("#modalassignvehicle").fadeOut(1000);
  }

  function driver_image(v){
		jQuery.post('<?php echo base_url(); ?>driver/upload_image/', {id: v},
			function(r)
			{
        console.log("response : ", r);
        var img_driver;
        if (r.row_image.driver_image_raw_name != "") {
          var img_driver = "<?php echo base_url().$this->config->item("dir_photo");?>" + r.row_image.driver_image_raw_name + r.row_image.driver_image_file_ext;
          // console.log("img_driver : ", img_driver);
          document.getElementById("driver_image_preview").src = img_driver;
        }

        var driver_id_formupload   = r.row.driver_id;
        var driver_name_formupload = r.row.driver_name;
        var id_card_formupload     = r.row.driver_idcard;
        var mobile_formupload      = r.row.driver_mobile;
        var sex_formupload         = r.row.driver_sex;
        // driver_image_preview
        $("#img_driver").html();
        $("#driver_name_formupload").html(driver_name_formupload);
        $("#id_card_formupload").html(id_card_formupload);
        $("#mobile_formupload").html(mobile_formupload);
        $("#sex_formupload").html(sex_formupload);
        $("#driver_id_formupload").val(driver_id_formupload);
        $("#modaluploadvehicle").fadeIn(1000);
			}
			, "json"
		);
	}

  function closemodaluploadvehicle(){
    $("#modaluploadvehicle").fadeOut(1000);
  }

  function delete_data(id)
  		{
  			if (confirm("Are you sure delete this data?")) {
  				jQuery.post('<?=base_url()?>driver/delete_driver/' + id, {}, function(r){
  					if (r.error) {
  						alert(r.message);
              window.location = "<?php echo base_url()?>driver";
  					}else{
  						alert(r.message);
              window.location = "<?php echo base_url()?>driver";
  					}
  				}, "json");
  			}
  		}
</script>
