<style media="screen">
#add-data{
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
  jQuery.post("<?=base_url();?>masterdata/material_save", jQuery("#frmadd").serialize(),
    function(r)
    {
      jQuery("#loader2").hide();
      console.log("response : ", r);
      if (r.error) {
        alert(r.message);
      }else {
        if (confirm(r.message)) {
          $("#add_material_id").val("");
          // $("#add_material_name").val("");
          $("#add_material_hauling").val("");
          $("#add_material_coal").val("");
          $("#add_material_reg_date").val("");
          $("#add_material_description").val("");
        }else {
          $("#add_material_id").val("");
          // $("#add_material_name").val("");
          $("#add_material_hauling").val("");
          $("#add_material_coal").val("");
          $("#add_material_reg_date").val("");
          $("#add_material_description").val("");
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
          <header class="panel-heading panel-heading-red" id="add-data">Add Data Material</header>
          <div class="panel-body" id="bar-parent10">
            <form class="block-content form" name="frmadd" id="frmadd" onsubmit="javascript:return frmsearch_onsubmit()">
    				<table width="100%" class="table table-striped">
              <tr>
      					<td colspan="2">Material</td>
                <td>
                  <input type="text" name="add_material_id" id="add_material_id" class="form-control col-md-12">
                </td>
              </tr>

              <!-- <tr>
                <td colspan="2">Material</td>
                <td>
                  <input type="text" name="add_material_name" id="add_material_name" class="form-control col-md-12">
                </td>
              </tr> -->

              <tr>
                <td colspan="2">Hauling</td>
                <td>
                  <input type="text" name="add_material_hauling" id="add_material_hauling" class="form-control col-md-12">
                </td>
              </tr>

              <tr>
                <td colspan="2">Coal</td>
                <td>
                  <input type="text" name="add_material_coal" id="add_material_coal" class="form-control col-md-12">
                </td>
              </tr>

              <tr>
                <td colspan="2">Material Reg Date</td>
                <td>
                  <input type="date" name="add_material_reg_date" id="add_material_reg_date" class="form-control col-md-12">
                </td>
              </tr>

              <tr>
                <td colspan="2">Description</td>
                <td>
                  <input type="text" name="add_material_description" id="add_material_description" class="form-control col-md-12">
                </td>
              </tr>

              <tr>
                <td colspan="2"></td>
                <td colspan="9">
                  <div class="text-right">
                    <a href="<?php echo base_url() ?>masterdata/material" class="btn btn-warning">Kembali</a>
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
