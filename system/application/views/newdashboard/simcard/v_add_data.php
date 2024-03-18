<script type="text/javascript">
  function page(p)
  {
    if(p==undefined){
      p=0;
    }
    jQuery("#offset").val(p);
    // jQuery("#result").html('<img src="<?php echo base_url();?>assets/transporter/images/loader2.gif">');
    jQuery("#loader2").show();
    jQuery.post("<?=base_url();?>simcard/save_data_simcard", jQuery("#frmadd").serialize(),
      function(r)
      {
        jQuery("#loader2").hide();
        console.log("response : ", r);
        if (r.error) {
          alert(r.message);
        }else {
          if (confirm(r.message)) {
            $("#simcard_number").val("");
            $("#simcard_type").val("");
            $("#simcard_last_topup").val("");
            $("#simcard_aps").val("");
            $("#simcard_remark").val("");
          }else {
            $("#simcard_number").val("");
            $("#simcard_type").val("");
            $("#simcard_last_topup").val("");
            $("#simcard_aps").val("");
            $("#simcard_remark").val("");
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
          <header class="panel-heading panel-heading-red">Add Data Simcard</header>
          <div class="panel-body" id="bar-parent10">
            <form class="block-content form" name="frmadd" id="frmadd" onsubmit="javascript:return frmsearch_onsubmit()">
    				<table width="100%" class="table table-striped">
              <tr>
      					<td colspan="2">Vehicle</td>
                <td>
                  <select class="form-control select2" name="simcard_vehicle_no" id="simcard_vehicle_no">
                    <option value="0">--Choose Vehicle</option>
                    <?php for ($i=0; $i < sizeof($vehicles); $i++) {?>
                      <option value="<?php echo $vehicles[$i]['vehicle_id'].'|'.$vehicles[$i]['vehicle_device'].'|'.$vehicles[$i]['vehicle_no'].'|'.$vehicles[$i]['vehicle_name'] ?>"><?php echo $vehicles[$i]['vehicle_no'].' '.$vehicles[$i]['vehicle_name'] ?></option>
                    <?php } ?>
                  </select>
                </td>
              </tr>

              <tr>
                <td colspan="2">Number</td>
                <td>
                  <input type="number" name="simcard_number" id="simcard_number" class="form-control">
                </td>
              </tr>

              <tr>
                <td colspan="2">Type</td>
                <td>
                  <input type="text" name="simcard_type" id="simcard_type" class="form-control">
                </td>
              </tr>

              <tr>
                <td colspan="2">Last Top Up Date</td>
                <td>
                  <input type="date" name="simcard_last_topup" id="simcard_last_topup" class="form-control">
                </td>
              </tr>

              <tr>
                <td colspan="2">Simcard Aps</td>
                <td>
                  <input type="text" name="simcard_aps" id="simcard_aps" class="form-control">
                </td>
              </tr>

              <tr>
                <td colspan="2">Remark</td>
                <td>
                  <input type="text" name="simcard_remark" id="simcard_remark" class="form-control">
                </td>
              </tr>

              <tr>
                <td colspan="2"></td>
                <td colspan="9">
                  <div class="text-right">
                    <a href="<?php echo base_url() ?>simcard" class="btn btn-warning">Kembali</a>
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
