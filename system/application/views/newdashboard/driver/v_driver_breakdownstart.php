<script type="text/javascript">
  function frmedit_onsubmit(){
    jQuery("#loader").show();
    jQuery.post("<?=base_url()?>driver/breakdownstart_save", jQuery("#frmedit").serialize(),
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
	  
	 alert("BREAKDOWN START!!");
	 
	  /* var driver_id = document.getElementById("driver_id").value;
	  var driver_name = document.getElementById("driver_name").value;
	  var driver_idcard = document.getElementById("driver_idcard").value;
	  var driver_coord = document.getElementById("driver_coord").value;
			
			jQuery("#btnsave").hide();
			jQuery("#loader2").show();
			jQuery.post('<?php echo base_url()?>'+'driver/breakdownstart_save/',  {driver_id:driver_id, driver_name:driver_name, driver_idcard:driver_idcard, driver_coord:driver_coord },
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
			return false; */
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
          <header class="panel-heading panel-heading-red">BREAKDOWN FORM</header>
          <div class="panel-body" id="bar-parent10">
          <form class="block-content form" name="frmedit" id="frmedit" onsubmit="javascript:return frmedit_onsubmit()">
			<table width="100%" class="table table-striped">
			<input type="hidden" name="driver_id" id="driver_id" class="form-control" value="<?php echo $row->driver_id; ?>" >
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
                <td>Keterangan</td>
                <td>
					<input type="text" name="driver_keterangan" id="driver_keterangan" class="form-control" placeholder="silahkan isi keterangan" value="">
                </td>
              </tr>
			  
			   <tr style="display:none;">
				   <td>
					Lokasi
				   </td>
				   <td>
						<input type="text" name="driver_coord" id="driver_coord" class="form-control" value="" disabled>
				   </td>
				  
				</tr>
				 <tr>
					<td colspan="2" align="center">
						<a onclick="javascript:save();" ><img src="<?=base_url();?>assets/bib/images/startactive.png" alt="TOMBOL START" width="100px" height="100px"></a>
						<br />
						<font style="font-size:10px;">Tekan tombol start jika terkendala operasional dan sebagai notifikasi ke Command Center</font>
				   </td>
				  
				</tr>
			
			
              <tr>
              
                <td colspan="2">
                  <div class="text-center">
                    <a href="<?php echo base_url() ?>driver/profile" class="btn btn-warning">Kembali</a>
                 	 <!--<input class="btn btn-success" type="submit" name="btnsave" id="btnsave" value=" Simpan " />-->
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
