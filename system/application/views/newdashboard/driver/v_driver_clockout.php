<script type="text/javascript">
 function save(){
	  
	  
	  var absensi_clock_in = document.getElementById("absensi_clock_in").value;
	  var absensi_id = document.getElementById("absensi_id").value;
	  var driver_coord = document.getElementById("driver_coord").value;
	  var absensi_vehicle_id = document.getElementById("absensi_vehicle_id").value;
	  
	  jQuery("#loader2").show();
			jQuery.post('<?php echo base_url()?>'+'driver/clockout_save/',  {absensi_clock_in:absensi_clock_in, absensi_id:absensi_id, driver_coord:driver_coord, absensi_vehicle_id:absensi_vehicle_id},
			function(r)
			{
				jQuery("#loader2").hide();
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
  
</script>


<!-- start sidebar menu -->
<div class="sidebar-container">
  <?=$sidebar;?>
</div>
<!-- end sidebar menu -->

<!-- start page content -->
<div class="page-content-wrapper">
  <div class="page-content">
    <br>
   
    <!--<div class="alert alert-success" id="notifnya2" style="display: none;"></div>-->
    <div class="row">
      <div class="col-md-12" id="tablecustomer">
        <div class="panel" id="panel_form">
          <header class="panel-heading panel-heading-red">JAM KELUAR DRIVER</header>
          <div class="panel-body" id="bar-parent10">
            <form class="block-content form" name="frmadd" id="frmadd">
			<table width="100%" class="table table-striped">
			 <input type="hidden" name="absensi_id" id="absensi_id" class="form-control" value="<?php echo $row->absensi_id; ?>" disabled>
			 <input type="hidden" name="absensi_driver_id" id="absensi_driver_id" class="form-control" value="<?php echo $row->absensi_driver_id; ?>" disabled>
			  <input type="hidden" name="absensi_clock_in" id="absensi_clock_in" class="form-control" value="<?php echo $row->absensi_clock_in; ?>" disabled>
			  <input type="hidden" name="absensi_vehicle_id" id="absensi_vehicle_id" class="form-control" value="<?php echo $row->absensi_vehicle_id; ?>" disabled>
			
			  <tr>
				<td>Jam Masuk</td>
                <td>
                  <input type="text" name="absensi_clock_in_txt" id="absensi_clock_in_txt" class="form-control" value="<?php echo date("d-m-Y H:i:s", strtotime($row->absensi_clock_in)); ?>" disabled>
                </td>
              </tr>
              <tr>
				<td>ID Simper</td>
                <td>
                  <input type="text" name="absensi_driver_idcard" id="absensi_driver_idcard" class="form-control" value="<?php echo $row->absensi_driver_idcard; ?>" disabled>
                </td>
              </tr>

              <tr>
                <td >Nama</td>
                <td>
					<input type="text" name="absensi_driver_name" id="absensi_driver_name" class="form-control" value="<?php echo $row->absensi_driver_name; ?>" disabled>
                </td>
              </tr>
			  
			  <tr>
                <td >Unit</td>
                <td>
					<input type="text" name="absensi_vehicle_no" id="absensi_vehicle_no" class="form-control" value="<?php echo $row->absensi_vehicle_no; ?>" disabled>
                </td>
              </tr>
			  
			   <tr>
				   <td>
					Lokasi
				   </td>
				   <td>
						<input type="text" name="driver_coord" id="driver_coord" class="form-control" value="" disabled>
				   </td>
				  
				</tr>
			   <tr>
					<td colspan="2">
						Klik SIMPAN <br /><small>jika sudah selesai Shift Anda.</small>
					</td>
				</tr>
				
				<tr>
					<td colspan="2">
					  <div class="form-control text-center">
						<a href="<?php echo base_url() ?>driver" class="btn btn-warning">Kembali</a>
						<!--<button type="button" name="button" class="btn btn-success" onclick="updateDetail();">Update</button>-->
						 <button type="button" name="button" class="btn btn-success" onclick="javascript:save()">Simpan</button>
						 <img id="loader2" style="display: none;" src="<?php echo base_url();?>assets/images/ajax-loader.gif" />
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
