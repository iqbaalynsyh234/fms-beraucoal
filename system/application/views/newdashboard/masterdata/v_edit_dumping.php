<style media="screen">
#edit-data{
  background-color: #1f50a2;
  color: white;
}
</style>

<script type="text/javascript">
  function page(p)
  {
    if(p==undefined){
      p=0;
    }
    jQuery("#offset").val(p);
    // jQuery("#result").html('<img src="<?php echo base_url();?>assets/transporter/images/loader2.gif">');
    jQuery("#loader2").show();
    jQuery.post("<?=base_url();?>masterdata/dumping_update", jQuery("#frmadd").serialize(),
      function(r)
      {
        jQuery("#loader2").hide();
        console.log("response : ", r);
        if (r.error) {
          alert(r.message);
        }else {
          if (confirm(r.message)) {
            window.location = '<?php echo base_url() ?>masterdata/dumping';
          }else {
            window.location = '<?php echo base_url() ?>masterdata/dumping';
          }
        }
      }
      , "json"
    );
  }

  function frmsearch_onsubmit()
  {
    jQuery("#loader").show();
    page(0);
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
    <?php if ($this->session->flashdata('notif')) {?>
      <div class="alert alert-success" id="notifnya" style="display: none;"><?php echo $this->session->flashdata('notif');?></div>
    <?php }?>
    <!--<div class="alert alert-success" id="notifnya2" style="display: none;"></div>-->
    <div class="row">
      <div class="col-md-12" id="tablecustomer">
        <div class="panel" id="panel_form">
          <header class="panel-heading panel-heading-red" id="edit-data">Edit Data Client</header>
          <div class="panel-body" id="bar-parent10">
            <form class="block-content form" name="frmadd" id="frmadd" onsubmit="javascript:return frmsearch_onsubmit()">
              <input type="hidden" name="edit_dumping_no" id="edit_dumping_no" class="form-control col-md-12" value="<?php echo $data_dumping[0]['dumping_no']; ?>">
              <input type="hidden" name="edit_dumping_id_old" id="edit_dumping_id_old" class="form-control col-md-12" value="<?php echo $data_dumping[0]['dumping_id']; ?>">
              <input type="hidden" name="edit_dumping_name_old" id="edit_dumping_name_old" class="form-control col-md-12" value="<?php echo $data_dumping[0]['dumping_name']; ?>">
    				<table width="100%" class="table table-striped">
              <!-- <tr>
      					<td colspan="2">ID Dumping</td>
                <td>
                  <input type="text" name="edit_dumping_id" id="edit_dumping_id" class="form-control col-md-12" value="<?php echo $data_dumping[0]['dumping_id']; ?>">
                </td>
              </tr> -->

              <tr>
                <td colspan="2">Dumping</td>
                <td>
                  <input type="text" name="edit_dumping_name" id="edit_dumping_name" class="form-control col-md-12" value="<?php echo $data_dumping[0]['dumping_name']; ?>">
                </td>
              </tr>

              <tr>
                <td colspan="2">Dumping Geofence</td>
                <td>
                  <input type="text" name="edit_dumping_geofence" id="edit_dumping_geofence" class="form-control col-md-12" value="<?php echo $data_dumping[0]['dumping_geofence']; ?>">
                </td>
              </tr>

              <tr>
                <td colspan="2">Dumping Reg Date</td>
                <td>
                  <input type="date" name="edit_dumping_reg_date" id="edit_dumping_reg_date" class="form-control col-md-12" value="<?php echo $data_dumping[0]['dumping_reg_date']; ?>">
                </td>
              </tr>

              <tr>
                <td colspan="2">Description</td>
                <td>
                  <input type="text" name="edit_dumping_description" id="edit_dumping_description" class="form-control col-md-12" value="<?php echo $data_dumping[0]['dumping_description']; ?>">
                </td>
              </tr>

              <tr>
                <td colspan="2"></td>
                <td colspan="9">
                  <div class="text-right">
                    <a href="<?php echo base_url() ?>masterdata/dumping" class="btn btn-warning">Kembali</a>
                    <button type="submit" name="button" class="btn btn-success">Simpan</button>
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
