<style media="screen">
div#modaladdserviceworks {
  margin-top: 1.5%;
  margin-left: 17.5%;
  max-height: 70%;
  position: absolute;
  background-color: #f1f1f1;
  text-align: left;
  border: 1px solid #d3d3d3;
  z-index: 1;
  overflow-y: auto;
  width: 60%;
}

#result {
  background-color: #1f50a2;
  color: white;
}

  div#modalforconfigservice {
    margin-top: 1%;
    margin-left: 17.5%;
    max-height: 70%;
    position: absolute;
    background-color: #f1f1f1;
    text-align: left;
    border: 1px solid #d3d3d3;
    z-index: 1;
    overflow-y: auto;
    width: 50%;
  }

  div#modalforsetservicess {
    margin-top: 1.5%;
    margin-left: 17.5%;
    max-height: 70%;
    position: absolute;
    background-color: #f1f1f1;
    text-align: left;
    border: 1px solid #d3d3d3;
    z-index: 1;
    overflow-y: auto;
    width: 60%;
  }

  div#modalvehiclesetting {
    margin-top: 3%;
    margin-left: 25%;
    width: 60%;
    /* max-height: 300px;
    max-width: 754px; */
    /* position: absolute; */
    max-height: 500px;
    max-width: 950px;
    overflow-x: auto;
    position: fixed;
    z-index: 9;
    background-color: #f1f1f1;
    text-align: left;
    border: 1px solid #d3d3d3;
  }

  div#modalvfuelcalibration {
    margin-top: 3%;
    margin-left: 25%;
    width: 60%;
    /* max-height: 300px;
    max-width: 754px; */
    /* position: absolute; */
    max-height: 500px;
    max-width: 950px;
    overflow-x: auto;
    position: fixed;
    z-index: 9;
    background-color: #f1f1f1;
    text-align: left;
    border: 1px solid #d3d3d3;
  }
</style>

<script src="<?php echo base_url();?>assets/js/jsblong/jquery.table2excel.js"></script>
<script>
  jQuery(document).ready(
      function()
      {
        jQuery("#export_xcel").click(function()
        {
          window.open('data:application/vnd.ms-excel,' + encodeURIComponent(jQuery('#isexport_xcel').html()));
        });
      }
    );
</script>

    <?php if ($this->session->flashdata('notif')) {?>
      <div class="alert alert-success" id="notifnya" style="display: none;"><?php echo $this->session->flashdata('notif');?></div>
    <?php }?>
    <!-- <a class="btn btn-success" href="javascript:addserviceworks()"><font>Service Works</font></a> -->
    <!-- <a class="btn btn-info" target="_blank" href="<?=base_url();?>vehicles/maintenanceshistory"><font>Maintenance History</font></a> -->
    <!-- <a class="btn btn-primary" target="_blank" href="<?=base_url();?>vehicles/workshop"><font>Manage Workshop / Agencies / Location</font></a> -->
    <!-- <br><br> -->
    <!--<div class="alert alert-success" id="notifnya2" style="display: none;"></div>-->
      <div class="row">
        <div class="col-md-12" id="tablevehicles">
          <div class="panel" id="panel_form">
            <header class="panel-heading panel-heading-red" id="result">RESULT</header>
            <div class="panel-body" id="bar-parent10">
              <!-- <h3>
                <b>Under Construction</b>
              </h3> -->
              <div class="col-lg-4 col-sm-4">
								<a href="javascript:void(0);" id="export_xcel" type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect m-b-10 btn-circle btn-default"><small>Export to Excel</small></a>
							</div>
              <div id="isexport_xcel">
  							<table class="table table-striped custom-table table-hover" style="font-size:12px;">
  								<thead>
  									<tr>
  										<th style="text-align:center;">No</th>
                      <th style="text-align:center;">Date</th>
                      <th style="text-align:center;">Time</th>
  										<th style="text-align:center;">Trans ID Awal</th>
                      <th style="text-align:center;">Trans ID Pengganti</th>
                      <th style="text-align:center;">Vehicle</th>
                      <th style="text-align:center;">Contractor</th>
  									</tr>
  								</thead>
  								<tbody>
  							    <?php if (sizeof($data) < 1) {
                      echo "Data is Empty";
                    }else {?>
                      <?php for ($i=0; $i < sizeof($data); $i++) {?>
                        <tr>
                          <td style="text-align:center;"><?php echo $i+1 ?></td>
                          <td style="text-align:center;"><?php echo date("d-m-Y", strtotime($data[$i]['hist_replacementwim_created_date'])) ?></td>
                          <td style="text-align:center;"><?php echo date("H:i:s", strtotime($data[$i]['hist_replacementwim_created_date'])) ?></td>
                          <td style="text-align:center;"><?php echo $data[$i]['hist_replacementwim_transIDawal'] ?></td>
                          <td style="text-align:center;"><?php echo $data[$i]['hist_replacementwim_transIDpengganti'] ?></td>
                          <td style="text-align:center;"><?php echo $data[$i]['hist_replacementwim_vehicleno_awal'] ?></td>
                          <td style="text-align:center;"><?php echo $data[$i]['hist_replacementwim_companyawal'] ?></td>
                        </tr>
                      <?php } ?>
                    <?php } ?>
  								</tbody>
  							</table>
							</div>
            </div>
      		</div>
        </div>
      </div>
