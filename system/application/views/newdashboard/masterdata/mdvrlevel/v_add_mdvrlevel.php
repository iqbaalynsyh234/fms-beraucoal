<style media="screen">
#add-data{
  background-color: #221f1f;
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
  jQuery.post("<?=base_url();?>masterdata/speedlevel_save", jQuery("#frmadd").serialize(),
    function(r)
    {
      jQuery("#loader2").hide();
      console.log("response : ", r);
      if (r.error) {
        alert(r.message);
      }else {
        if (confirm(r.message)) {
          $("#level_name").val("");
          $("#level_alias").val("");
          $("#level_value").val("");
          $("#level_value_min").val("");
          $("#level_value_max").val("");
          $("#level_sanksi_lubang").val("");
          $("#level_sanksi_skors").val("");
        }else {
          $("#level_name").val("");
          $("#level_alias").val("");
          $("#level_value").val("");
          $("#level_value_min").val("");
          $("#level_value_max").val("");
          $("#level_sanksi_lubang").val("");
          $("#level_sanksi_skors").val("");
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
          <header class="panel-heading panel-heading-red" id="add-data">Add Data Overspeed Level</header>
          <div class="panel-body" id="bar-parent10">
            <form class="block-content form" name="frmadd" id="frmadd" onsubmit="javascript:return frmsearch_onsubmit()">
    				<table width="100%" class="table table-striped">
              <tr>
      					<td colspan="2">Level Name</td>
                <td>
                  <input type="text" name="level_name" id="level_name" class="form-control col-md-12">
                </td>
              </tr>

              <tr>
                <td colspan="2">Level Alias</td>
                <td>
                  <input type="text" name="level_alias" id="level_alias" class="form-control col-md-12">
                </td>
              </tr>

              <tr>
                <td colspan="2">Level Value</td>
                <td>
                  <input type="number" name="level_value" id="level_value" class="form-control col-md-2">
                </td>
              </tr>

              <tr>
                <td colspan="2">Level Value Min</td>
                <td>
                  <input type="number" name="level_value_min" id="level_value_min" class="form-control col-md-2">
                </td>
              </tr>

              <tr>
                <td colspan="2">Level Value Max</td>
                <td>
                  <input type="number" name="level_value_max" id="level_value_max" class="form-control col-md-2">
                </td>
              </tr>

              <tr>
                <td colspan="2">Level Sanksi Lubang</td>
                <td>
                  <input type="number" name="level_sanksi_lubang" id="level_sanksi_lubang" class="form-control col-md-2">
                </td>
              </tr>

              <tr>
                <td colspan="2">Level Sanksi Skors</td>
                <td>
                  <input type="number" name="level_sanksi_skors" id="level_sanksi_skors" class="form-control col-md-2">
                </td>
              </tr>

              <tr>
                <td colspan="2"></td>
                <td colspan="9">
                  <div class="text-right">
                    <a href="<?php echo base_url() ?>masterdata/speedlevel" class="btn btn-warning">Kembali</a>
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
