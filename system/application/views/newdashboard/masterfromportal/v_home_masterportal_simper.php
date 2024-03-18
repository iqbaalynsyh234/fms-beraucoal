<style media="screen">
#master-portal{
  background-color: #1f50a2;
  color: white;
}

</style>

<div class="sidebar-container">
  <?=$sidebar;?>
</div>

<div class="page-content-wrapper">
  <div class="page-content">
    <br>
    <?php if ($this->session->flashdata('notif')) {?>
      <div class="alert alert-success" id="notifnya" style="display: none;"><?php echo $this->session->flashdata('notif');?></div>
    <?php }?>
    <!--<div class="alert alert-success" id="notifnya2" style="display: none;"></div>-->
      <div class="col-md-12">
          <div class="panel" id="panel_form">
            <header class="panel-heading" id="master-portal">MASTER PORTAL 
				<!--<button type="button" name="button" id="export_xcel" class="btn btn-primary btn-sm">Export Excel</button>-->
			</header>
            <div id="loader" style="display: none;" class="mdl-progress mdl-js-progress mdl-progress__indeterminate is-upgraded" data-upgraded=",MaterialProgress">
                <div class="progressbar bar bar1" style="width: 0%;"></div>
                <div class="bufferbar bar bar2" style="width: 100%;"></div>
                <div class="auxbar bar bar3" style="width: 0%;"></div>
            </div>
            <div class="panel-body" id="bar-parent10">
			
			<div id="isexport_xcel" style="overflow-x: auto;">
			<!--<table id="example" class="table table-striped" style="font-size:14px;">-->
            <table id="example1" class="table table-striped" style="font-size: 12px; overflow-x:auto;">
              <thead>
                <tr>
                  <th>
                    No
                  </th>
                  <th>Image</th>
                  <th>Reg. Number</th>
                  <th>ID Number</th>
                  <th>Name</th>
                  <th>Position</th>
                  <th>ID Position</th>
                  <th>Departement</th>
                  <th>Company</th>
                  <th>Depkon ID</th>
                  <th>Date of Hire</th>
                  <th>Exp. Date</th>
                  <th>Blood Type</th>
                  <th>Gender</th>
                  <th>Religion</th>
                  <th>Birthdate</th>
                  <th>Place of Birthdate</th>
                  <th>Address</th>

                  <th>Tribe</th>
                  <th>Citizen</th>
                  <th>Emergency Contact</th>
                  <th>ID Card Type</th>
                  <th>Port Access</th>
                  <th>Access Zone</th>
                  <th>Counting Pengajuan</th>
                  <th>Counting Gagal</th>
                  <th>SIM Type</th>
                  <th>SIM Number</th>
                  <th>SIM Exp. Date</th>
                  <th>SIM Scan</th>
                  <th>Issued At</th>
                  <th>Status</th>
                  <th>VVIP</th>
                  <th>Verification Status</th>

                  <th>No KTP</th>
                  <th>KTP Scan</th>
                  <th>Atasan Langsung</th>
                  <th>Jabatan</th>
                  <th>Contact</th>
                  <th>E-mail</th>
                  <th>MCU Date</th>
                  <th>MCU Location</th>
                  <th>MCU File</th>
                  <th>MCU Description</th>
                  <th>MCU Status</th>
                  <th>Status Karyawan</th>
                  <th>Violation</th>
                  <th>Violation Date</th>
                  <th>License File</th>
                  <th>License Exp</th>

                  <th>Inspection Point Target</th>
                  <th>Observation Point Target</th>
                  <th>Safety Talk Point Target</th>
                  <th>Hazard Report Point Target</th>
                  <th>Commisioning Point Target</th>
                  <th>Created At</th>
                  <th>Updated At</th>
                  <th>Created By</th>
                  <th>Updated By </th>
                  <th>NIK</th>
                  <th>Deleted By</th>
                  <th>RFID Tag</th>
                  <th>Isafe NO</th>
                  <th>Isafe Password Default</th>
                  <th>Date Of Bithdate</th>
                  <th>Isafe Password</th>

                  <th>Special Notes</th>
                  <th>Role ID</th>
                  <th>Coaching Point Target</th>
                  <th>is ERT</th>
                  <th>Vaksinasi</th>
                  <th>Akun Peduli</th>
                  <th>Tgl. Vaksin</th>
                  <th>Status Vaksin</th>
                  <th>Jenis Vaksin</th>
                  <th>Tgl. V1</th>
                  <th>Tgl. V2</th>
                  <th>Verifikator Vaksin</th>
                  <th>Terakhir Verifikasi</th>
                  <th>Portal ID</th>
                  <th>Submitted At</th>
                  <th>Updated Date</th>
                </tr>
              </thead>
                <tbody>
                  <?php for ($i=0; $i < sizeof($datafromportal); $i++) {?>
                    <tr>
                      <td><?php echo $i+1; ?></td>
                      <td>
                        <?php
                          $image_status = $datafromportal[$i]['portal_image_status'];
                          $portal_id_number  = $datafromportal[$i]['portal_id_number'];
                          if ($image_status == 1) {?>
                            <button type="button" class="btn btn-primary btn-xs" onclick="modalImage('modalState', '<?php echo $portal_id_number; ?>');">
                              <span class="fa fa-camera"></span>
                            </button>
                        <?php } ?>
                      </td>
                      <td><?php echo $datafromportal[$i]['portal_register_number'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_id_number'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_name'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_position'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_id_position'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_departmen'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_company'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_depkon_id'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_date_of_hire'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_exp_date'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_blood_type'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_gender'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_religion'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_date_of_birth'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_place_of_birth'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_address'] ?></td>

                      <td><?php echo $datafromportal[$i]['portal_tribe'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_citizen'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_emergency_contact'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_id_card_type'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_port_access'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_zone_access'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_counting_pengajuan'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_counting_gagal'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_sim_type'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_sim_number'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_sim_exp_date'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_sim_scan'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_issued_at'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_status'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_is_vvip'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_verification_status'] ?></td>

                      <td><?php echo $datafromportal[$i]['portal_no_ktp'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_ktp_scan'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_atasan_langsung'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_jabatan'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_contact'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_email'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_mcu_date'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_mcu_location'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_mcu_file'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_mcu_description'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_mcu_status'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_status_karyawan'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_violation'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_violation_date'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_license_file'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_license_exp'] ?></td>

                      <td><?php echo $datafromportal[$i]['portal_inspection_point_target'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_observation_point_target'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_safety_talk_point_target'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_hazard_report_point_target'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_commisioning_point_target'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_created_at'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_updated_at'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_created_by'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_updated_by'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_nik'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_deleted_by'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_rfid_tag'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_isafe_no'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_default_isafe_password'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_date_of_birth_string'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_isafe_password'] ?></td>

                      <td><?php echo $datafromportal[$i]['portal_special_notes'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_roleId'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_coaching_point_target'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_isERT'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_vaksinasi'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_akun_peduli'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_tanggal_vaksin'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_status_vaksin'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_jenis_vaksin'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_tanggal_v1'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_tanggal_v2'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_verifikator_vaksin'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_terakhir_verifikasi'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_id'] ?></td>
                      <td><?php echo $datafromportal[$i]['portal_submited_at'] ?></td>
                      <td><?php echo $datafromportal[$i]['master_portal_updateddate_new'] ?></td>
                    </tr>
                  <?php } ?>
                </tbody>
  			</table>
			</div>
      </div>
    </div>
  </div>
</div>

<div id="modalState" class="modalbase64">
  <div class="modal-content-state-imagebase64">
    <div class="closethismodalimagebase64 btn btn-danger btn-sm">X</div>
    <hr>
    <div id="modalStateContentimagebase64">
      <div id="image64fix"></div>
    </div>
  </div>
</div>

<script src="<?php echo base_url(); ?>assets/js/jsblong/jquery.table2excel.js"></script>
<script type="text/javascript" src="js/script.js"></script>
<script src="<?php echo base_url()?>assets/dashboard/assets/js/jquery-1.7.1.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo base_url()?>assets/dashboard/assets/js/myjs.js"></script>
<script>
	  $(document).ready(
		function() {
		  $("#export_xcel").click(function() {
			window.open('data:application/vnd.ms-excel,' + encodeURIComponent(jQuery('#isexport_xcel').html()));
		  });
		}
	  );
	</script>
<script type="text/javascript">
  $("#notifnya").fadeIn(1000);
  $("#notifnya").fadeOut(5000);

  // GLOBAL VARIABLE FOR MODAL
  var modalpool, btnpool;

  function modalImage(modalid, portalidnumber) {
    $("#loader").show();
    $.post("<?php echo base_url() ?>masterportalsimper/getimage", {portal_id_number : portalidnumber}, function(response){
      console.log("response getimage : ", response);
      var status = response.code;
        if (status == 200) {
          $("#image64fix").html("");
          var imagefix = response.imagebase64[0].portal_image;
          var imageconverted = "<img src='"+imagefix+"' width='200px' height='auto' alt='Red dot' />";
          $("#image64fix").html(imageconverted);
          jQuery("#loader").hide();

          modalpool = document.getElementById(modalid);
          btnpool = document.getElementsByClassName("closethismodalimagebase64")[0];
          modalpool.style.display = "block";
          btnpool.onclick = function () {
            modalpool.style.display = "none";
          }
        }else {
          alert("Image is Empty");
        }
    }, "json");

  }
</script>
