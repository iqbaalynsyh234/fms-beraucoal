<style media="screen">
#maintenance{
  background-color: #221f1f;
  color: white;
}
</style>

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
      <div class="col-md-12" id="tablevehicles">
        <div class="panel" id="panel_form">
          <header class="panel-heading panel-heading-red" id="maintenance">Maintenance History</header>
          <div class="panel-body" id="bar-parent10">
              <div class="block-content form" name="frmsearch" id="frmsearch">
                <table class="table">
                  <tr>
                    <td>Vehicle</td>
                    <td>
                      <select class="form-control select2" name="selectvehicle" id="selectvehicle">
                        <option value="all">All</option>
                        <?php for ($i=0; $i < sizeof($vehicle); $i++) {?>
                          <option value="<?php echo $vehicle[$i]['vehicle_no'];?>">
                            <?php echo $vehicle[$i]['vehicle_no'];?> - <?php echo $vehicle[$i]['vehicle_name'];?>
                          </option>
                          <?php } ?>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td>Servicess</td>
                    <td>
                      <select class="form-control select2" name="selectservicess" id="selectservicess" onchange="showhideservicestatus();">
                        <option value="all">All</option>
                        <?php for ($i=0; $i < sizeof($servicetype); $i++) {?>
                          <option value="<?php echo $servicetype[$i]['service_type_id'];?>">
                            <?php echo $servicetype[$i]['service_type'];?>
                          </option>
                          <?php } ?>
                      </select>
                    </td>
                  </tr>
                  <tr style="display:none;" id="servicestatusshowhide">
                    <td>Service Status</td>
                    <td>
                      <select class="form-control" name="servicestatus" id="servicestatus">
                        <option value="all">All</option>
                        <option value="0">Process</option>
                        <option value="1">Completed</option>
                      </select>
                    </td>
                  </tr>
                  <tr id="filterdatestartend">
                    <td width="10%">Date</td>
                    <td>
                      <div class="input-group date form_date col-md-6" data-date="" data-date-format="dd-mm-yyyy" data-link-format="yyyy-mm-dd">
                          <input class="form-control" type="text" name="sdate" id="sdate" value="<?php echo date("Y-m-d"); ?>"/>
                          <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                      </div>
                      s/d
                      <div class="input-group date form_date col-md-6" data-date="" data-date-format="dd-mm-yyyy" data-link-format="yyyy-mm-dd">
                          <input class="form-control" type="text" name="enddate" id="enddate" value="<?php echo date("Y-m-d"); ?>"/>
                          <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td></td>
                    <td class="text-right">
                      <button class="btn btn-primary" type="submit" name="btnsearchhistory" id="btnsearchhistory" onclick="frmsearch_onsubmit();">Search</button>
                      <img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
                    </td>
                  </tr>
                </table>
          		</div>
              <input class="btn_export btn btn-danger" type="button" name="excel" id="btnexcelreport" value="Export To Excel" onclick="exportthisexcel();" style="display: none;"/>
              <div id="exportthisexcel">
                <table class="table table-striped" style="display: none; font-size:12px;" id="tableexportexcel">
                <thead>
          				<tr>
          					<th>
                      <!-- <button type="button" class="btn btn-success btn-xs">
                        <span class="fa fa-plus"></span>
                      </button> -->
                      No
                    </th>
          					<th>Vehicle</th>
                    <th>Servicess Name</th>
                    <th>Number Of Letter</th>
                    <th>Executor</th>
                    <th>Cost</th>
                    <th>Agency</th>
                    <th>Note</th>
                    <th>Status</th>
          					<th>Date</th>
          				</tr>
          			</thead>
                <tbody id="historyvalue">

                </tbody>
  						</table>
              </div>
            </div>
      </div>
    </div>


</div>
</div>
</div>

<script type="text/javascript">
    // EXPORT TO EXCEL
    function exportthisexcel()
    {
      window.open('data:application/vnd.ms-excel,' + encodeURIComponent($('#exportthisexcel').html()));
    }

  function frmsearch_onsubmit(){
    $("#historyvalue").html("");
    $("#loader").show();
    $("#btnexcelreport").hide();
    $("#tableexportexcel").hide();
    var selectvehicle   = $("#selectvehicle").val();
    var selectservicess = $("#selectservicess").val();
    var servicestatus   = $("#servicestatus").val();
    var date            = $("#sdate").val();
    var enddate         = $("#enddate").val();
    var data            = {selectvehicle: selectvehicle, selectservicess: selectservicess, date: date, enddate: enddate, servicestatus:servicestatus};
    $.post("<?=base_url();?>vehicles/showmaintenancehistory/", data, function(r){
      $("#loader").hide();
        console.log("responsenya : ", r);
        var tipeservicess = r.tipeservices;
          if (tipeservicess == "all") {
            var no = 1;
            var html = "";
            for (var i = 0; i < r.data.length; i++) {
              var statusservice = r.data[i].servicess_status;
              var servicess_tipeservice = r.data[i].servicess_tipeservice
                if (servicess_tipeservice == 4) {
                  if (statusservice == 0) {
                    statusservice = "Process";
                  }else {
                    statusservice = "Completed";
                  }
                }else {
                  statusservice = "Completed";
                }

             html += '<tr>';
                html += '<td>'+no+'</td>';
                html += '<td>'+r.data[i].servicess_vehicle_no + ' ' +r.data[i].servicess_vehicle_name+'</td>';
                html += '<td>'+r.data[i].servicess_name+'</td>';
                html += '<td>'+r.data[i].servicess_nol+'</td>';
                html += '<td>'+r.data[i].servicess_pelaksana+'</td>';
                html += '<td>'+r.data[i].servicess_biaya+'</td>';
                html += '<td>'+r.data[i].workshop_name+'</td>';
                html += '<td>'+r.data[i].servicess_note+'</td>';
                html += '<td>'+statusservice+'</td>';
                html += '<td>'+r.data[i].servicess_date+'</td>';
              html += '</tr>';
              no++;
            }
          }else {
            var no = 1;
            var html = "";
            for (var i = 0; i < r.data.length; i++) {
              var statusservice = r.data[i].servicess_status;
              var servicess_tipeservice = r.data[i].servicess_tipeservice
                if (servicess_tipeservice == 4) {
                  if (statusservice == 0) {
                    statusservice = "Process";
                  }else {
                    statusservice = "Completed";
                  }
                }else {
                  statusservice = "Completed";
                }
             html += '<tr>';
                html += '<td>'+no+'</td>';
                html += '<td>'+r.data[i].servicess_vehicle_no+ ' ' +r.data[i].servicess_vehicle_name+'</td>';
                html += '<td>'+r.data[i].servicess_name+'</td>';
                html += '<td>'+r.data[i].servicess_nol+'</td>';
                html += '<td>'+r.data[i].servicess_pelaksana+'</td>';
                html += '<td>'+r.data[i].servicess_biaya+'</td>';
                html += '<td>'+r.data[i].workshop_name+'</td>';
                html += '<td>'+r.data[i].servicess_note+'</td>';
                html += '<td>'+statusservice+'</td>';
                html += '<td>'+r.data[i].servicess_date+'</td>';
              html += '</tr>';
              no++;
            }
          }
          $("#btnexcelreport").show();
          $("#tableexportexcel").show();
          $("#historyvalue").html(html);
      }, "json");
  }

  function showhideservicestatus(){
    var selectservicess = $("#selectservicess").val();
      if (selectservicess == 4) {
        $("#servicestatusshowhide").show();
      }else {
        $("#servicestatusshowhide").hide();
      }
  }
</script>
