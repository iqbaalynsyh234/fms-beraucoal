<style media="screen">
  div#modalchangepass {
    margin-top: 5%;
    margin-left: 45%;
    max-height: 300px;
    max-width: 400px;
    position: absolute;
    background-color: #f1f1f1;
    text-align: left;
    border: 1px solid #d3d3d3;
  }
</style>
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
          <header class="panel-heading panel-heading-blue">Edit Branch Office</header>
          <div class="panel-body" id="bar-parent10">
            <form class="form-horizontal" name="frmedit" id="frmedit" onsubmit="javascript:return frmedit_onsubmit()">
              <input type="hidden" name="company_id" id="company_id" value="<?php echo $data->company_id;?>" />
                <table class="table sortable no-margin">
                  <tr>
                      <td>Name</td>
                      <td>:</td>
                      <td><input class="form-control" type="text" size="35" name="company_name" id="company_name" value="<?php echo $data->company_name; ?>" /></td>
                  </tr>

                  <tr>
                    <td>Note</td>
                    <td>:</td>
                    <td>
                      <small>
                        Proses Add Telegram GROUP:  <br />
                        1. Buka (https://web.telegram.org) dan LOGIN sebagai User yang membuat GROUP baru <br />
                        2. Buat GROUP di Telegram Web<br />
                        3. Invite bot_lacakmobil dalam GROUP Tersebut <br />
                        4. Klik GROUP yang baru dibuat untuk mendapatkan CHAT ID nya (Contoh : https://web.telegram.org/#/im?p=g154513121) <br />
                        5. Contoh 154513121 adalah CHAT ID dari GROUP Tersebut, untuk memasukkan Ke dalam System ditambahkan tanda - menjadi -154513121
                      </small>
                    </td>
                  </tr>


                  <tr>
                    <td>Telegram Group ID (SOS Alert)</td>
                    <td>:</td>
                    <td><input class="form-control" type="text" size="35" name="company_telegram_sos" id="company_telegram_sos" value="<?php echo $data->company_telegram_sos; ?>" /></td>
                  </tr>
                  <tr>
                    <td>Telegram Group ID (Parking Alert)</td>
                    <td>:</td>
                    <td><input class="form-control" type="text" size="35" name="company_telegram_parkir" id="company_telegram_parkir" value="<?php echo $data->company_telegram_parkir; ?>" /></td>
                  </tr>
                  <tr>
                    <td>Telegram Group ID (Speed Alert)</td>
                    <td>:</td>
                    <td><input class="form-control" type="text" size="35" name="company_telegram_speed" id="company_telegram_speed" value="<?php echo $data->company_telegram_speed; ?>" /></td>
                  </tr>
                  <tr>
                    <td>Telegram Group ID (Geofence Alert)</td>
                    <td>:</td>
                    <td><input class="form-control" type="text" size="35" name="company_telegram_geofence" id="company_telegram_geofence" value="<?php echo $data->company_telegram_geofence; ?>" /></td>
                  </tr>
                  <tr>
                      <td></td>
                      <td></td>
                      <td>
                        <a type="button" class="btn btn-warning" href="<?php echo base_url()?>account/branch"/> Cancel</a>
                        <button type="submit" id="submit" name="submit" class="btn btn-success"/> Save</button>
                        <img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" style="display:none;"/>
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

<script type="text/javascript">
  function frmedit_onsubmit(){
    jQuery("#loader").show();
    jQuery.post("<?=base_url()?>account/updatebranchoffice", jQuery("#frmedit").serialize(),
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
