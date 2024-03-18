<div class="sidebar-container">
  <?=$sidebar;?>
</div>

<div class="page-content-wrapper">
  <div class="page-content">
    <div class="col-sm-12 col-md-4 col-lg-3">
      <button class="btn btn-info" id="notifdevicestatus" style="display:none;"></button>
    </div>
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-head">
            <header>
              <!-- <h5>DEVELOPMENT</h5> -->
            </header>
            <div class="tools">
              <!-- <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a> -->
              <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
              <!-- <a class="t-close btn-color fa fa-times" href="javascript:;"></a> -->
            </div>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-3">
                <select class="form-control select2" name="contractor" id="contractor">
                  <option value="0">--All Contractor</option>
                  <?php for ($i=0; $i < sizeof($company); $i++) {?>
                    <option value="<?php echo $company[$i]->company_id ?>"><?php echo $company[$i]->company_name ?></option>
                  <?php } ?>
                </select>
              </div>

              <div class="col-md-3">
                <button type="button" class="btn btn-success btn-md" name="button" onclick="getVehicleByContractor()">
                  Search
                </button>
              </div>

              <div class="col-md-2">
                <img id="loader2" style="display:none;" src="<?php echo base_url();?>assets/images/anim_wait.gif" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div id="result" style="width:100%"></div>

  </div>
</div>

<script type="text/javascript" src="js/script.js"></script>
<script src="<?php echo base_url()?>assets/dashboard/assets/js/jquery-1.7.1.min.js" type="text/javascript"></script>

<script type="text/javascript">
  function getVehicleByContractor(){
    console.log("masuk");
    var companyid        = $("#contractor").val();
    $("#loader2").show();
    $("#result").hide();
    $("#result").html("");
    $.post("<?php echo base_url() ?>installedfms/search_vehiclebycontractor", {companyid : companyid}, function(response){
      $("#result").html("");
      $("#loader2").hide();
      console.log("response search_vehiclebycontractor : ", response);
      var data = response.data;
        if (response.html) {
          $("#result").html(response.html);
          $("#result").show();
        }
    },"json");
  }
</script>
