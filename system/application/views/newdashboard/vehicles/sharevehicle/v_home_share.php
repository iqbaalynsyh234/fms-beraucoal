<style media="screen">
#data-vehicle{
  background-color: #1f50a2;
  color: white;
}
</style>

<div class="sidebar-container">
  <?=$sidebar;?>
</div>

<div class="page-content-wrapper">
  <div class="page-content">
    <br>
      <div class="row">
        <div class="col-md-12" id="tablevehicles">
          <div class="panel" id="panel_form">
            <header class="panel-heading panel-heading-red" id="data-vehicle">Data Vehicle</header>
            <div class="panel-body" id="bar-parent10">
              <form class="form-horizontal" id="frmShareVehicle" onsubmit="javascript: return frm_onsubmit();">

                <table class="table">
                  <tr>
                    <td>Type</td>
                    <td>
                      <select class="form-control" name="share_type" id="share_type">
                        <option value="0">Select Type</option>
                        <option value="1">Start Share</option>
                        <option value="2">Stop Share</option>
                      </select>
                    </td>
                  </tr>

                  <tr>
                    <td>Shared To</td>
                    <td>
                      <select class="form-control select2" name="vehicle_sharedto" id="vehicle_sharedto">
                        <option value="0">Select Contractor</option>
                        <?php
                          for ($i=0; $i < sizeof($company); $i++) {?>
                            <?php if ($company[$i]->company_id != $this->sess->user_company) {?>
                              <option value="<?php echo $company[$i]->company_id ?>"><?php echo $company[$i]->company_name; ?></option>
                            <?php } ?>
                          <?php } ?>
                      </select>
                    </td>
                  </tr>
                </table>

                <table class="table table-striped" style="font-size:14px;">
                  <thead>
                    <tr>
                      <th>
                        No
                      </th>
                      <th>No. Lambung</th>
                      <th>Device IMEI</th>
                      <th>Status</th>
                      <th>Control</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php for($i=0;$i<count($datavehicle);$i++) { ?>
                      <tr>
                        <td width="2%"><?=$i+1?></td>
                        <td>
                          <?=$datavehicle[$i]['vehicle_no'] .' '. $datavehicle[$i]['vehicle_name'];?><br>
                        </td>
                        <td>
                            <?php
                                $deviceimei = explode("@", $datavehicle[$i]['vehicle_device']);
                                echo $deviceimei[0];
                            ?>
                        </td>
                        <td>
                          <?php
                          $isShare          = $datavehicle[$i]['vehicle_is_share'];
                          $vehicle_sharedto = $datavehicle[$i]['vehicle_id_shareto'];
                            if ($isShare == 0) {
                              echo "Available";
                            }else {
                              for ($j=0; $j < sizeof($company); $j++) {
                                if ($company[$j]->company_id == $vehicle_sharedto) {
                                  echo "Shared to " . $company[$j]->company_name;
                                }
                              }
                            }
                           ?>
                        </td>
                        <td>
                          <?php
                            if ($isShare == 0) {?>
                              <input type="checkbox" name="vehicle_for_share[]" value="<?php echo $datavehicle[$i]['vehicle_device'] ?>" class="form-control">
                            <?php }else {?>
                              <input type="checkbox" name="vehicle_for_share[]" value="<?php echo $datavehicle[$i]['vehicle_device'] ?>" class="form-control" checked>
                            <?php }
                           ?>
                        </td>
                      </tr>
                    <? } ?>
                    <tr>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td>
                        <div class="text-right">
                          <button type="submit" class="btn btn-success">Submit</button>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </form>
            </div>
      		</div>
        </div>
      </div>
</div>
</div>
</div>
</div>

<script type="text/javascript">
  function frm_onsubmit(){
    $.post("<?php echo base_url() ?>vehicles/share_process", $("#frmShareVehicle").serialize(), function(response){
      console.log("response : ", response);
        if (response.code == 400) {
          alert(response.msg);
        }else if (response.code == 200) {
          if (confirm("Request successfully send to BIB")) {
            window.location = '<?php echo base_url() ?>vehicles/share';
          }
        }
    }, "json");
    return false;
  }


</script>
