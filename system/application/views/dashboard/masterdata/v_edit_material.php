<script type="text/javascript">
function page(p)
{
  if(p==undefined){
    p=0;
  }
  jQuery("#offset").val(p);
  // jQuery("#result").html('<img src="<?php echo base_url();?>assets/transporter/images/loader2.gif">');
  jQuery("#loader2").show();
  jQuery.post("<?=base_url();?>masterdata/material_update", jQuery("#frmadd").serialize(),
    function(r)
    {
      jQuery("#loader2").hide();
      console.log("response : ", r);
      if (r.error) {
        alert(r.message);
      }else {
        if (confirm(r.message)) {
          window.location = '<?php echo base_url() ?>masterdata/material';
        }else {
          window.location = '<?php echo base_url() ?>masterdata/material';
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
  <div class="page-content" style="width:160%;">
    <br>
    <?php if ($this->session->flashdata('notif')) {?>
      <div class="alert alert-success" id="notifnya" style="display: none;"><?php echo $this->session->flashdata('notif');?></div>
    <?php }?>
    <!--<div class="alert alert-success" id="notifnya2" style="display: none;"></div>-->
    <div class="row">
      <div class="col-md-12" id="tablecustomer">
        <div class="panel" id="panel_form">
          <header class="panel-heading panel-heading-blue">Edit Data Material</header>
          <div class="panel-body" id="bar-parent10">
            <form class="block-content form" name="frmadd" id="frmadd" onsubmit="javascript:return frmsearch_onsubmit()">
              <input type="hidden" name="edit_material_no" id="edit_material_no" class="form-control col-md-12" value="<?php echo $data_material[0]['material_no']; ?>">
    				<table width="100%" class="table table-striped">
              <tr>
      					<td colspan="2">ID Material</td>
                <td>
                  <input type="text" name="edit_material_id" id="edit_material_id" class="form-control col-md-12" value="<?php echo $data_material[0]['material_id']; ?>">
                </td>
              </tr>

              <tr>
                <td colspan="2">Material</td>
                <td>
                  <input type="text" name="edit_material_name" id="edit_material_name" class="form-control col-md-12" value="<?php echo $data_material[0]['material_name']; ?>">
                </td>
              </tr>

              <tr>
                <td colspan="2">Hauling</td>
                <td>
                  <input type="text" name="edit_material_hauling" id="edit_material_hauling" class="form-control col-md-12" value="<?php echo $data_material[0]['material_hauling']; ?>">
                </td>
              </tr>

              <tr>
                <td colspan="2">Coal</td>
                <td>
                  <input type="text" name="edit_material_coal" id="edit_material_coal" class="form-control col-md-12" value="<?php echo $data_material[0]['material_coal']; ?>">
                </td>
              </tr>

              <tr>
                <td colspan="2">Material Reg Date</td>
                <td>
                  <input type="date" name="edit_material_reg_date" id="edit_material_reg_date" class="form-control col-md-12" value="<?php echo $data_material[0]['material_reg_date']; ?>">
                </td>
              </tr>

              <tr>
                <td colspan="2">Description</td>
                <td>
                  <input type="text" name="edit_material_description" id="edit_material_description" class="form-control col-md-12" value="<?php echo $data_material[0]['material_description']; ?>">
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
