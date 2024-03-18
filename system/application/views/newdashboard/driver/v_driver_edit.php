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
    <div class="alert alert-success" id="notifnya2" style="display: none;"></div>
    <div class="row">
      <div class="col-md-12" id="formeditdriver">
        <div class="panel" id="panel_form">
          <header class="panel-heading panel-heading-red">Edit Driver</header>
          <div class="panel-body" id="bar-parent10">
            <form class="form-horizontal" name="frmedit" id="frmedit" onsubmit="javascript:return frmedit_onsubmit()">
              <table class="table sortable no-margin">
                <tr>
                <?php if (isset($row)) { ?>
                <input type="hidden" name="driver_company" id="driver_company"  value="<?php echo $this->sess->user_company;?>" /></td>
                <input type="hidden" name="driver_id" id="driver_id"            value="<?php echo $row->driver_id;?>" /></td>
                <input type="hidden" name="driver_group" id="driver_group"      value="<?php echo $this->sess->user_group;?>" /></td>
                <?php } ?>
                </tr>
                <tr>
                  <td>Name</td>
                  <td>:</td>
                  <td><input type="text" class="form-control" name="driver_name" value="<?=isset($row) ? htmlspecialchars($row->driver_name, ENT_QUOTES) : "";?>" /></td>
                </tr>
                <tr>
                  <td>ID Driver</td>
                  <td>:</td>
                  <td><input type="text" class="form-control" name="driver_idcard" value="<?=isset($row) ? htmlspecialchars($row->driver_idcard, ENT_QUOTES) : "";?>" /></td>
                </tr>
                <tr>
                  <td>Address</td>
                  <td>:</td>
                  <td>
                    <!-- <input type="text" class="form-control" name="driver_address" value="<?=$row->driver_address;?>" > -->
                    <textarea rows="5" class="form-control" name="driver_address" ><?=$row->driver_address;?></textarea>
                  </td>
                </tr>
                <tr>
                  <td>Phone</td>
                  <td>:</td>
                  <td><input type="text" class="form-control" name="driver_phone" value="<?=isset($row) ? htmlspecialchars($row->driver_phone, ENT_QUOTES) : "";?>" /></td>
                </tr>
                <tr>
                  <td>Mobile</td>
                  <td>:</td>
                  <td>
                  1.<input type="text" class="form-control" name="driver_mobile" value="<?=isset($row) ? htmlspecialchars($row->driver_mobile, ENT_QUOTES) : "";?>" />
                  2.<input type="text" class="form-control" name="driver_mobile2" value="<?=isset($row) ? htmlspecialchars($row->driver_mobile2, ENT_QUOTES) : "";?>" />
                  </td>
                </tr>
                <tr>
                  <td>Licence</td>
                  <td>:</td>
                  <td><input size="5" type="text" class="form-control" maxlength="5" name="driver_licence" value="<?=isset($row) ? htmlspecialchars($row->driver_licence, ENT_QUOTES) : "";?>" />
                  Licence No :
                  <input type="text" class="form-control" name="driver_licence_no" value="<?=isset($row) ? htmlspecialchars($row->driver_licence_no, ENT_QUOTES) : "";?>" /></td>
                </tr>
                <tr>
                  <td>Licence Expired</td>
                  <td>:</td>
                  <td>
                    <div class="input-group date form_date col-md-4" data-date="" data-date-format="dd-mm-yyyy" data-link-format="yyyy-mm-dd">
                      <input type="text" class="form-control" maxlength="15" name="driver_licence_expired" id="driver_licence_expired" class="date-pick" value="<?php echo date("d-m-Y", strtotime($row->driver_licence_expired))?>"/>
                      <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                    </div>
                  </td>
                  </td>
                </tr>
                <tr>
                  <td>Sex</td>
                  <td>:</td>
                  <td>
                    <select name="driver_sex" class="form-control">
                      <option value="M" <? if ((! isset($row)) || ($row->driver_sex == 'M')) { ?>selected<?php } ?>>M</option>
                      <option value="F" <? if ((! isset($row)) || ($row->driver_sex == 'F')) { ?>selected<?php } ?>>F</option>
                    </select>
                  </td>
                </tr>
                <tr>
                  <td>Joint Date</td>
                  <td>:</td>
                  <td>
                    <div class="input-group date form_date col-md-4" data-date="" data-date-format="dd-mm-yyyy" data-link-format="yyyy-mm-dd">
                        <!-- <input class="form-control" size="5" type="text" readonly name="duedate" id="duedate" value="<?=date('d-m-Y')?>"> -->
                        <input type="text" class="form-control" maxlength="15" name="driver_joint_date" id="joint_date" class="date-pick"  value="<?php echo date("d-m-Y", strtotime($row->driver_joint_date))?>"/>
                        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td>SIOF</td>
                  <td>:</td>
                  <td>
                    <input type="text" class="form-control" maxlength="15" name="driver_siof" id="driver_siof" value="<?=isset($row) ? htmlspecialchars($row->driver_siof, ENT_QUOTES) : "";?>" /><br />
                    <small>Surat Ijin Operation Forklip</small>
                  </td>
                  </td>
                </tr>
                <tr>
                  <td>SIOF Expired</td>
                  <td>:</td>
                  <td>
                    <div class="input-group date form_date col-md-4" data-date="" data-date-format="dd-mm-yyyy" data-link-format="yyyy-mm-dd">
                        <!-- <input class="form-control" size="5" type="text" readonly name="duedate" id="duedate" value="<?=date('d-m-Y')?>"> -->
                        <input type="text" class="form-control" maxlength="15" name="driver_siof_expired" id="driver_siof_expired" class="date-pick" value="<?php echo date("d-m-Y", strtotime($row->driver_siof_expired))?>" /><br />
                        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                    </div>
                    <small>Surat Ijin Operation Forklip</small>
                  </td>
                  </td>
                </tr>
                <tr>
                  <td>Note</td>
                  <td>:</td>
                  <td><input type="text" class="form-control" name="driver_note" value="<?=isset($row) ? htmlspecialchars($row->driver_note, ENT_QUOTES) : "";?>" >
                </tr>
                <tr>
                  <td>RFID</td>
                  <td>:</td>
                  <td>
                    <input type="text" name="driver_rfid" id="driver_rfid" class="form-control" value="<?=isset($row) ? htmlspecialchars($row->driver_rfid, ENT_QUOTES) : "";?>">
                  </td>
                </tr>
              </table>
              <div class="text-right">
                <input class="btn btn-warning" type="button" name="btncancel" id="btncancel" value=" Cancel " onclick="location='<?=base_url()?>driver';" />
                <input class="btn btn-success" type="submit" name="btnsave" id="btnsave" value=" Save " />
                <img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
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
    jQuery.post("<?=base_url()?>driver/update", jQuery("#frmedit").serialize(),
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

<!-- if (r.row_image == "") {
img_driver = '<img src="<?php echo base_url().$this->config->item("dir_photo").$this->config->item("default_photo_driver");?>" width="256px" height="256px" />';
 }else {
   img_driver = '<img src="<?php echo base_url().$this->config->item("dir_photo").$row_image->driver_image_raw_name.$row_image->driver_image_file_ext;?>" width="256px" height="256px" />';
 } -->
