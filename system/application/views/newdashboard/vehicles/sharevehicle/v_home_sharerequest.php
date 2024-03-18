<style media="screen">
#vehicle-share{
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
            <header class="panel-heading panel-heading-red" id="vehicle-share">Vehicle Share Request</header>
            <div class="panel-body" id="bar-parent10">
              <form class="form-horizontal" id="frmrequestsharevehicle" onsubmit="javascript: return frm_onsubmit();">
                <table class="table table-striped" style="font-size:14px;">
                  <thead>
                    <tr>
                      <th>
                        No
                      </th>
                      <th>Request Form</th>
                      <th>Shared To</th>
                      <th>Type</th>
                      <th>Status</th>
                      <th>Control</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php for($i=0;$i<count($requestshare);$i++) { ?>
                      <tr>
                        <td width="2%"><?=$i+1?></td>
                        <td>
                          <?php for ($j=0; $j < sizeof($company); $j++) {?>
                            <?php if ($company[$j]->company_id == $requestshare[$i]['vehicle_share_contractorid']) {
                              echo $company[$j]->company_name;
                            } ?>
                          <?php } ?>
                        </td>
                        <td>
                          <?php for ($j=0; $j < sizeof($company); $j++) {?>
                            <?php if ($company[$j]->company_id == $requestshare[$i]['vehicle_share_sharedto']) {
                              echo $company[$j]->company_name;
                            } ?>
                          <?php } ?>
                        </td>
                        <td>
                          <?php
                            if ($requestshare[$i]['vehicle_share_type'] == 1) {
                              echo "Start Share";
                            }else {
                              echo "Stop Share";
                            }
                           ?>
                        </td>
                        <td>
                          <?php if ($requestshare[$i]['vehicle_share_flag'] == 0) {?>
                            <button type="button" class="btn btn-warning">Waiting</button>
                          <?php }elseif ($requestshare[$i]['vehicle_share_flag'] == 2) {?>
                            <button type="button" class="btn btn-danger">Rejected</button>
                          <?php }else {?>
                            <button type="button" class="btn btn-success">Approved</button>
                          <?php } ?>
                        </td>
                        <td>
                          <button type="button" class="btn btn-info" onclick="getData('<?php echo $requestshare[$i]['vehicle_share_id'] ?>')">
                            <span class="fa fa-search"></span>
                            View
                          </button>
                        </td>
                      </tr>
                    <? } ?>
                  </tbody>
                </table>
              </form>
            </div>
      		</div>
        </div>
      </div>

      <div class="row" id="resultpanel" style="display: none">
        <div class="col-md-12">
          <div class="panel" id="panel_form">
            <header class="panel-heading panel-heading-red">Form Share Request</header>
            <div class="panel-body" id="bar-parent10">
              <form class="form-horizontal" id="frmRequestShareSubmit" onsubmit="javascript: return frm_onsubmitrequest();">
                <input type="text" name="share_to" id="share_to" hidden>
                <input type="text" name="id_request" id="id_request" hidden>
                <div id="notessharedto"></div> <br>

                <table class="row" style="width:50%">
                  <tr>
                    <td class="col-md-1">
                      Action
                    </td>
                    <td>
                      <select class="form-control select2" name="requestAction" id="requestAction" onchange="requestActionType();">
                        <option value="approved">Approved</option>
                        <option value="reject">Reject</option>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td></td>
                    <td>
                      <textarea name="reject_reason" id="reject_reason" class="form-control" style="display: none;" rows="5" cols="50"></textarea>
                    </td>
                  </tr>
                </table> <br>

                <div id="showRequestShare"></div>
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
  function getData(idrequest){
    $("#showRequestShare").hide();
    $("#resultpanel").hide();
    $.post("<?php echo base_url() ?>vehicles/getDetailData", {idrequest : idrequest}, function(response){
      console.log("response : ", response);
      var data     = response.data;
      var from     = response.requestfrom.company_name;
      var sharedto = response.sharedto.company_name
      var share_type = response.share_type;
      $("#share_to").val(response.sharedto.company_id);
      $("#id_request").val(response.idrequest);

        if (share_type == 1) {
          $("#notessharedto").html("Berikut adalah list kendaraan yang akan di share dari " + from + " Ke " + sharedto);
        }else {
          $("#notessharedto").html("Berikut adalah permintaan pembatalan share vehicle dari " + from + " Untuk " + sharedto);
        }

      $("#modalStateTitle").html("Form Request Share Vehicle");
      var htmlsharerequest = "";
      htmlsharerequest += '<table class="table table-striped">';
        htmlsharerequest += '<thead>';
          htmlsharerequest += '<tr>';
            htmlsharerequest += '<th>No</th>';
            htmlsharerequest += '<th>Vehicle</th>';
            htmlsharerequest += '<th>Control <input type="checkbox" onClick="toggle(this)" /> Pilih Semua</th>';
          htmlsharerequest += '</tr>';
        htmlsharerequest += '</thead>';
      for (var i = 0; i < data.length; i++) {
          htmlsharerequest += '<tr>';
            htmlsharerequest += '<td style="font-size:12px;color:black"><span style="color:black;">'+(i+1)+'</span></td>';
            htmlsharerequest += '<td style="font-size:12px;color:black"><span style="color:black;">'+data[i].vehicle_no+ " " +data[i].vehicle_name+'</span></td>';
            htmlsharerequest += '<td>';
              htmlsharerequest += '<input type="checkbox" name="vehicle_shared_approved[]" id="vehicle_shared_approved'+data[i].vehicle_device+'" value="'+data[i].vehicle_device+'" class="form-control" onClick="checkedonchange(this);">';
            htmlsharerequest += '</td>';
          htmlsharerequest += '</tr>';
      }
        htmlsharerequest += '<tr>';
          htmlsharerequest += '<td></td>';
          htmlsharerequest += '<td></td>';
          htmlsharerequest += '<td><div class="text-right" style="display: none;" id="btnsubmitform"><button class="btn btn-success">Submit</button></div></td>';
        htmlsharerequest += '<tr>';
      htmlsharerequest += '</table>';
      $("#showRequestShare").html(htmlsharerequest);
      $("#resultpanel").show();
      $("#showRequestShare").show();
    }, "json");
  }

  function requestActionType(){
    var value = $("#requestAction").val();
      if (value == "reject") {
        $("#reject_reason").show();
      }else {
        $("#reject_reason").hide();
      }
  }

  function toggle(source) {
    console.log("source : ", source);
      var checkboxes = document.querySelectorAll('input[type="checkbox"]');
      for (var i = 0; i < checkboxes.length; i++) {
          if (checkboxes[i] != source){
              checkboxes[i].checked = source.checked;
              $("#btnsubmitform").toggle();
            }
      }
  }

  function frm_onsubmitrequest(){
    if (confirm("Apakah anda yakin menyetujui permintaan share kendaraan berikut?")) {
      $.post("<?php echo base_url() ?>vehicles/bib_share_process", $("#frmRequestShareSubmit").serialize(), function(response){
        console.log("response : ", response);
          if (response.code == 400) {
            alert(response.msg);
          }else if (response.code == 200) {
            if (confirm("Request successfully send to BIB")) {
              window.location = '<?php echo base_url() ?>vehicles/requestshare';
            }
          }
      }, "json");
    }
    return false;
  }

  function checkedonchange(source) {
    var isChecked = $("input[type=checkbox]").is(":checked");

    if (isChecked == true) {
      $("#btnsubmitform").show();
    }else {
      $("#btnsubmitform").hide();
    }
  }
</script>
