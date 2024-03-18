<style media="screen">
#detail-truck{
  background-color: #221f1f;
  color: white;
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
    <div class="row">
      <div class="col-md-12">
        <div class="panel" id="panel_form">
          <header class="panel-heading panel-heading-red" id="detail-truck"> Detail Truck</header>
          <div class="panel-body" id="bar-parent10">
            <form class="block-content form" name="frmadd" id="frmadd" onsubmit="javascript: return frmadd_onsubmit()">
    				<table width="100%" class="table table-striped">
    					<input class="form-control" type="hidden" id="vehicleID" name="vehicleID" value="<?php echo $vehicledata[0]['vehicle_id']; ?>" />

              <tr>
      					<td colspan="2">Vehicle No</td>
                <td>
                  <input type="text" name="detail_vehicle_no" id="detail_vehicle_no" class="form-control col-md-12" value="<?php echo $vehicledata[0]['vehicle_no']; ?>" disabled>
                </td>
              </tr>

              <tr>
                <td colspan="2">Vehicle Name</td>
                <td>
                  <input type="text" name="detail_vehicle_name" id="detail_vehicle_name" class="form-control col-md-12" value="<?php echo $vehicledata[0]['vehicle_name']; ?>" disabled>
                </td>
              </tr>

      				<tr>
      					<td colspan="2">Type</td>
                <td>
                  <input type="text" name="detail_type" id="detail_type" class="form-control col-md-12" value="Dump Truck">
                </td>
              </tr>

              <tr>
                <td colspan="2">Merk</td>
                <td>
                  <input type="text" name="detail_merk" id="detail_merk" class="form-control col-md-12" value="Hino">
                </td>
              </tr>

              <tr>
      					<td colspan="2">Model</td>
                <td>
                  <input type="text" name="detail_model" id="detail_model" class="form-control col-md-12" value="Diesel">
                </td>
              </tr>

              <tr>
                <td colspan="2">Seri</td>
                <td>
                  <input type="text" name="detail_seri" id="detail_seri" class="form-control col-md-12" value="FM 260JD">
                </td>
              </tr>

              <tr>
      					<td colspan="2">Tahun Pembuatan</td>
                <td>
                  <input type="text" name="detail_tahunpembuatan" id="detail_tahunpembuatan" class="form-control col-md-12" value="2018">
                </td>
              </tr>

              <tr>
                <td colspan="2">Commissioning Expired</td>
                <td>
                  <input type="date" name="detail_commissioningexpired" id="detail_commissioningexpired" class="form-control col-md-12" value="<?php echo date("Y-m-d"); ?>">
                </td>
              </tr>

              <tr>
      					<td colspan="2">Lambung</td>
                <td>
                  <input type="text" name="detail_lambung" id="detail_lambung" class="form-control col-md-12" value="MKS 175">
                </td>
              </tr>

              <tr>
                <td colspan="2">No. RFID SPIP</td>
                <td>
                  <input type="text" name="detail_rfidspip" id="detail_rfidspip" class="form-control col-md-12" value="213434141421">
                </td>
              </tr>

              <tr>
      					<td colspan="2">No. Mesin</td>
                <td colspan="9">
                  <input type="text" name="detail_nomesin" id="detail_nomesin" class="form-control col-md-12" value="J08EUFJ-97112">
                </td>
              </tr>

              <tr>
      					<td colspan="2">No. Rangka</td>
                <td colspan="9">
                  <input type="text" name="detail_norangka" id="detail_norangka" class="form-control col-md-12" value="MJEFM8JN1JJE 23156">
                </td>
              </tr>

              <tr>
                <td colspan="2">Mitra Kerja</td>
                <td colspan="9">
                  <input type="text" name="detail_mitrakerja" id="detail_mitrakerja" class="form-control col-md-12" value="CV Mega Karya Sahabat">
                </td>
              </tr>

              <tr>
                <td colspan="2">Departemen</td>
                <td colspan="9">
                  <input type="text" name="detail_departemen" id="detail_departemen" class="form-control col-md-12" value="Coal Mining & Handling">
                </td>
              </tr>

              <tr>
                <td colspan="2">Tare</td>
                <td colspan="9">
                  <input type="number" name="detail_tare" id="detail_tare" class="form-control col-md-12">
                </td>
              </tr>

              <tr>
                <td colspan="2">Status</td>
                <td colspan="9">
                  <select class="form-control col-md-12" name="detail_status" id="detail_status">
                    <option value="Approve">Approve</option>
                    <option value="Reject">Reject</option>
                  </select>
                </td>
              </tr>

              <tr>
                <td colspan="2"></td>
                <td colspan="9">
                  <div class="text-right">
                    <a href="<?php echo base_url() ?>vehicles" class="btn btn-warning">Cancel</a>
                    <button type="button" name="button" class="btn btn-success" onclick="updateDetail();">Update</button>
                  </div>
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

<script type="text/javascript" src="js/script.js"></script>
<script src="<?php echo base_url()?>assets/dashboard/assets/js/jquery-1.7.1.min.js" type="text/javascript"></script>

<script type="text/javascript">
  // document.addEventListener('keypress', function (e) {
  //         if (e.keyCode === 13 || e.which === 13) {
  //             e.preventDefault();
  //             return false;
  //         }
  //
  //     });

  function updateDetail(){
    var vehicleID                   = $("#vehicleID").val();
    var detail_type                 = $("#detail_type").val();
    var detail_merk                 = $("#detail_merk").val();
    var detail_model                = $("#detail_model").val();
    var detail_seri                 = $("#detail_seri").val();
    var detail_tahunpembuatan       = $("#detail_tahunpembuatan").val();
    var detail_commissioningexpired = $("#detail_commissioningexpired").val();
    var detail_lambung              = $("#detail_lambung").val();
    var detail_rfidspip             = $("#detail_rfidspip").val();
    var detail_nomesin              = $("#detail_nomesin").val();
    var detail_norangka             = $("#detail_norangka").val();
    var detail_mitrakerja           = $("#detail_mitrakerja").val();
    var detail_departemen           = $("#detail_departemen").val();
    var detail_tare                 = $("#detail_tare").val();
    var detail_status               = $("#detail_status").val();

    var data = {
      vehicleID : vehicleID,
      detail_type : detail_type,
      detail_merk : detail_merk,
      detail_model : detail_model,
      detail_seri : detail_seri,
      detail_tahunpembuatan : detail_tahunpembuatan,
      detail_commissioningexpired : detail_commissioningexpired,
      detail_lambung : detail_lambung,
      detail_rfidspip : detail_rfidspip,
      detail_nomesin : detail_nomesin,
      detail_norangka : detail_norangka,
      detail_mitrakerja : detail_mitrakerja,
      detail_departemen : detail_departemen,
      detail_tare : detail_tare,
      detail_status : detail_status
    };

    // alert("Data : " + JSON.stringify(data));

    $.post('<?php echo base_url() ?>vehicles/updateDetailTruck', data, function(response){
      console.log("response : ", response);
    }, 'json');

  }
</script>
