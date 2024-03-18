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
      <div class="col-md-12" id="tablebranchoffice">
        <div class="panel" id="panel_form">
          <header class="panel-heading" style="background-color:#221f1f;color:white;">Branch Office</header>
          <div class="panel-body" id="bar-parent10">
              <table id="example1" class="table table-striped">
                <thead>
          				<tr>
          					<th>
                      <?php
                      if ($privilegecode == 3) {?>

                      <?php }else {?>
                        <button type="button" class="btn btn-success btn-xs" onclick="showaddbranchoffice()" title="Add New Branch Office">
                          <span class="fa fa-plus"></span>
                        </button>
                      <?php }?>
                      No
                    </th>
          					<th><?php echo "Name" ?></th>
							<th><?php echo "SID" ?></th>
          					<th><?php echo "Telegram Group ID (SOS Alert)" ?></th>
          					<th><?php echo "Telegram Group ID (Parking Alert)" ?></th>
          					<th><?php echo "Telegram Group ID (Speed Alert)" ?></th>
          					<th><?php echo "Telegram Group ID (Geofence Alert)" ?></th>
          					<th>
                      <?php
                      if ($privilegecode == 3) {?>

                      <?php }else {?>
                        <?php echo "Option" ?>
                      <?php }?>
                    </th>
          				</tr>
          			</thead>
                <tbody>
                  <?php for($i=0;$i<count($data);$i++) { ?>
          				  <tr>
            					<td width="2%"><?=$i+1?></td>
                      <td><?php echo $data[$i]->company_name;?></td>
					  <td><?php echo $data[$i]->company_sid;?></td>
                      <td><?php echo $data[$i]->company_telegram_sos;?></td>
            					<td><?php echo $data[$i]->company_telegram_parkir;?></td>
            					<td><?php echo $data[$i]->company_telegram_speed;?></td>
            					<td><?php echo $data[$i]->company_telegram_geofence;?></td>
                      <td>
                        <?php
                        if ($privilegecode == 3) {?>

                        <?php }else {?>
                          <a href="<?php echo base_url();?>account/editbranchoffice/<?php echo $data[$i]->company_id;?>">
                            <img src="<?php echo base_url();?>assets/images/edit.gif" />
                          </a>
                        <?php }?>
                      </td>
                    </tr>
                  <? } ?>
  							</tbody>
  						</table>
            </div>
      </div>
    </div>
    <div class="col-md-12" id="formaddbranchoffice" style="display: none;">
      <div class="panel" id="panel_form">
        <header class="panel-heading" style="background-color:#221f1f;color:white;">Add Branch Office</header>
        <div class="panel-body" id="bar-parent10">
          <form class="form-horizontal" id="frmadd" name="frmadd" onsubmit="javascript: return frmadd_onsubmit()">
                <table class="table sortable no-margin">
                <tr>
                    <td>Name</td>
                    <td>:</td>
                    <td><input type="text" name="branch_name" id="branch_name" class="form-control"/></td>
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
                    <td><input type="text" name="company_telegram_sos" id="company_telegram_sos" class="form-control"/></td>
                </tr>
            	  <tr>
                    <td>Telegram Group ID (Parking Alert)</td>
                    <td>:</td>
                    <td><input type="text" name="company_telegram_parkir" id="company_telegram_parkir" class="form-control"/></td>
                </tr>
            	  <tr>
                    <td>Telegram Group ID (Speed Alert)</td>
                    <td>:</td>
                    <td><input type="text" name="company_telegram_speed" id="company_telegram_speed" class="form-control"/></td>
                </tr>
            	  <tr>
                    <td>Telegram Group ID (Geofence Alert)</td>
                    <td>:</td>
                    <td><input type="text" name="company_telegram_geofence" id="company_telegram_geofence" class="form-control"/></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td>
                      <button type="button" class="btn btn-warning" onclick="btncancel()"/> Cancel</button>
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
  // $("#notifnya").fadeIn(1000);
  // $("#notifnya").fadeOut(5000);

  function showaddbranchoffice(){
    $("#formaddbranchoffice").show();
    $("#tablebranchoffice").hide();
  }

  function btncancel(){
    $("#formaddbranchoffice").hide();
    $("#tablebranchoffice").show();
  }

  function btnDelete(id){
    $("#iddelete").val(id);
    $("#modalDeletedest").show();
  }

  function closemodallistofvehicle(){
    $("#modalDeletedest").hide();
  }

  function frmadd_onsubmit()
	{
		jQuery("#loader").show();
		jQuery.post("<?=base_url()?>account/savebranchoffice", jQuery("#frmadd").serialize(),
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
