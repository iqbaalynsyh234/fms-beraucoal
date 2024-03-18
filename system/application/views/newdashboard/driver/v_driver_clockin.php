<script type="text/javascript">
  function frmedit_onsubmit(){
    jQuery("#loader").show();
    jQuery.post("<?=base_url()?>driver/clockin_save2", jQuery("#frmedit").serialize(),
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
  
  function save(){
	  
	 
	  var driver_id = document.getElementById("driver_id").value;
	  var driver_name = document.getElementById("driver_name").value;
	  var driver_idcard = document.getElementById("driver_idcard").value;
	  var driver_shift = document.getElementById("driver_shift").value;
	  var driver_photo_text = document.getElementById("driver_photo_text").value;
	  var driver_coord = document.getElementById("driver_coord").value;
			
			jQuery("#btnsave").hide();
			jQuery("#loader2").show();
			jQuery.post('<?php echo base_url()?>'+'driver/clockin_save/',  {driver_id:driver_id, driver_name:driver_name, driver_idcard:driver_idcard, driver_shift:driver_shift, driver_photo_text:driver_photo_text, driver_coord:driver_coord },
			function(r)
			{
				
				jQuery("#loader2").hide();
				if (r.error)
				{
				  alert(r.message);
				  jQuery("#btnsave").show();
				  return false;
				}
				jQuery("#btnsave").show();
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
          <header class="panel-heading panel-heading-red">JAM MASUK DRIVER</header>
          <div class="panel-body" id="bar-parent10">
          <form class="block-content form" name="frmedit" id="frmedit" onsubmit="javascript:return frmedit_onsubmit()">
			<table width="100%" class="table table-striped">
			<input type="hidden" name="driver_id" id="driver_id" class="form-control" value="<?php echo $row->driver_id; ?>" >
			<input type="hidden" name="driver_photo_text" id="driver_photo_text" class="form-control" value="" >
              <tr>
				<td>ID Simper</td>
                <td>
                  <input type="text" name="driver_idcard" id="driver_idcard" class="form-control" value="<?php echo $row->driver_idcard; ?>" disabled>
                </td>
              </tr>

              <tr>
                <td >Nama</td>
                <td>
					<input type="text" name="driver_name" id="driver_name" class="form-control" value="<?php echo $row->driver_name; ?>" disabled>
                </td>
              </tr>
			  
			  
			   <tr>
                <td>Shift</td>
                <td>
                  <select class="form-control select2" name="driver_shift" id="driver_shift">
				   <option value="">Pilih Shift</option>
                    <option value="Pagi 06:00 – 18:00">Pagi 06:00 – 18:00</option>
                    <option value="Pagi 07:00 – 19:00">Pagi 07:00 – 19:00</option>
          			<option value="Malam 18:00 – 06:00">Malam 18:00 – 06:00</option>
                    <option value="Malam 19:00 – 07:00">Malam 19:00 – 07:00</option>
          			
                  </select>
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
					<td colspan="2">Foto Selfie</td>
					
				  </tr>
				 <tr>
					<td colspan="2">
						<div class="form-control text-center" >
							AMBIL FOTO SELFIE DENGAN LATAR KENDARAAN DAN TERLIHAT NOMOR LAMBUNG
							<button id="start-camera" class="btn btn-default" >Buka Camera</button>
						</div>
					<td>
				 </tr>
			
				<tr>
					<td colspan="2">
						<div class="form-control text-center" >
							<video id="video"  width="100%" height="100%" autoplay></video> 
							<br />
							<button id="click-photo" class="btn btn-default">Foto</button>
						</div>
					</td>
					
				</tr>
			
			 <tr>
                <td colspan="2">Hasil Foto</td>
			 </tr>
			 <tr>
                <td colspan="2">
					<div class="form-control text-center" >
					
					<canvas id="canvas" height="452px" width="320px"  ></canvas>
					</div>
                </td>
              </tr>
			 
              <tr>
               <td>
				&nbsp
			   </td>
                <td >
                  <div class="text-left">
                    <a href="<?php echo base_url() ?>driver/profile" class="btn btn-warning">Kembali</a>
                 	 <!--<input class="btn btn-success" type="submit" name="btnsave" id="btnsave" value=" Simpan " />-->
					 <button class="btn btn-success" type="button" id="btnsave" name="btnsave" onclick="javascript:save()";/>Simpan</button>
					
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
