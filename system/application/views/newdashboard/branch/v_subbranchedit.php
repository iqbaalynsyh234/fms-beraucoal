<!-- start sidebar menu -->
<div class="sidebar-container">
  <?=$sidebar;?>
</div>
<!-- end sidebar menu -->

<!-- start page content -->
<div class="page-content-wrapper" style="width: 100%;">
  <div class="page-content">
    <br>
    <?php if ($this->session->flashdata('notif')) {?>
      <div class="alert alert-success" id="notifnya" style="display: none;"><?php echo $this->session->flashdata('notif');?></div>
    <?php }?>
    <div class="alert alert-success" id="notifnya2" style="display: none;"></div>
    <div class="row">
      <div class="col-md-12" id="formaddbranchoffice">
        <div class="panel" id="panel_form">
          <header class="panel-heading" style="background-color:#221f1f;color:white;">Edit Sub Branch Office</header>
          <div class="panel-body" id="bar-parent10">
            <form class="form-horizontal" name="frmedit" id="frmedit" onsubmit="javascript:return frmedit_onsubmit()">
              <input type="hidden" name="subcompany_id" id="subcompany_id" value="<?php echo $data_subcompany[0]['subcompany_id'];?>" />
                <table class="table sortable no-margin">
                  <tr>
                    <td>Current Branch Office</td>
                    <td>
                        <?php for ($i=0; $i < sizeof($data_subcompany); $i++) {?>
                          <?php
                            for ($x=0; $x < sizeof($data_company); $x++) {
                              if ($data_subcompany[0]['subcompany_parent'] == $data_company[$x]['company_id']) {?>
                                <input class="form-control" type="text" name="curbranchoffice" id="curbranchoffice" value="<?php echo $data_company[$x]['company_name'] ?>" readonly>
                              <?php } } ?>
                        <?php } ?>
                    </td>
                  </tr>
                  <tr>
                    <td>Branch Office</td>
                    <td>
                      <select class="select2" name="subcompany_parent" id="subcompany_parent">
                        <?php for ($i=0; $i < sizeof($data_company); $i++) {?>
                          <option value="<?php echo $data_company[$i]['company_id'] ?>"><?php echo $data_company[$i]['company_name'] ?></option>
                        <?php } ?>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td>Sub Branch Office Name</td>
                    <td><input class="form-control" type="text" size="35" name="subcompany_name" id="subcompany_name" value="<?php echo $data_subcompany[0]['subcompany_name'];?>"/></td>
                  </tr>
                </table>
              <div class="text-right">
                <a type="button" class="btn btn-warning" href="<?php echo base_url()?>account/subbranchoffice"/> Cancel</a>
                <button type="submit" id="submit" name="submit" class="btn btn-success"/> Save</button>
                <img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" style="display:none;"/>
              </div>
            </form>
          </div>
        </div>
      </div>
  </div>
</div>
</div>

<script type="text/javascript">
  function frmedit_onsubmit(){
    jQuery("#loader").show();
    jQuery.post("<?=base_url()?>account/updatesubbranchoffice", jQuery("#frmedit").serialize(),
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
</script>
