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
    <?php
      if (isset($privilegecode)) {
        if ($privilegecode != 5) {?>
          <button type="button" class="btn btn-success" id="btnMaintenanceType" onclick="maintenanceType();">Maintenance Type</button>
          <button type="button" class="btn btn-success" id="btnMaintenanceCategory" onclick="maintenanceCategory();">Maintenance Category</button>
          <br><br>
        <?php } }?>

      <div class="row">
        <div class="col-md-12" id="tablevehicles">
          <div class="panel" id="panel_form">
            <header class="panel-heading panel-heading-red" id="maintenance">Maintenance</header>
            <div class="panel-body" id="bar-parent10">
              <table class="table table-striped" id="example1" style="font-size:12px; width:100%;">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Vehicle</th>
                    <th>Control</th>
                  </tr>
                </thead>
                <tbody>
                  <?php for ($i=0; $i < sizeof($datavehicle); $i++) {?>
                    <tr>
                      <td><?php echo $i+1; ?></td>
                      <td><?php echo $datavehicle[$i]['vehicle_no'].' '.$datavehicle[$i]['vehicle_name'] ?></td>
                      <td>
                        <button type="button" class="btn btn-primary" onclick="showMaintenanceForm('<?php echo $datavehicle[$i]['vehicle_device'] ?>');">
                          <span class="fa fa-cog"></span>
                        </button>
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
    </div>
  </div>
</div>

<div id="modalMaintenanceShow" class="modalformaintenance">
  <div class="modal-content-maintenance">
    <div class="row">
      <div class="col-md-10">
        <p class="modalTitleforAll" id="modalStateTitle"></p>
      </div>
      <div class="col-md-2">
        <div class="closethismodalmaintenance btn btn-danger btn-sm">X</div>
      </div>
    </div>
      <div class="row">
        <div class="col-md-12">
          <div id="modalStateMaintenance">
            <div id="maintenancetype_content">
              <!-- style="display:none;" -->
              <div id="thistableMType" style="display:none;">
                <form class="form-horizontal" id="frmmaintenancetype" onsubmit="javascript:return savemaintenancetype()">
                  <table class="table">
                    <tr>
                      <td>
                        <input type="text" name="type_name" id="type_name" class="form-control" placeholder="Maintenance Type">
                      </td>
                      <td>
                        <button type="submit" class="btn btn-success">Simpan</button>
                      </td>
                    </tr>
                  </table>
                </form>

                <div id="tablerowtype">
                  <table class="table table-striped" id="example1" style="font-size:12px;" width="100%">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Type</th>
                        <th>Control</th>
                      </tr>
                    </thead>
                    <tbody id="thisrow">

                    </tbody>
                  </table>
                </div>
              </div>

              <div id="thistableMCat" style="display:none;">
                <form class="form-horizontal" id="frmmaintenancecat" onsubmit="javascript:return savemaintenancecat()">
                  <table class="table">
                    <tr>
                      <td>
                        <div id="maintenance_type_in_cat_show" style="display:none;">

                        </div>
                      </td>
                      <td>
                        <input type="text" name="cat_name" id="cat_name" class="form-control" placeholder="Maintenance Category">
                      </td>
                      <td>
                        <button type="submit" id="btnSimpanMCat" class="btn btn-success">Simpan</button>
                      </td>
                    </tr>
                  </table>
                </form>

                <div id="tablerowcat">
                  <table class="table table-striped" id="example1" style="font-size:12px;" width="100%">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Type</th>
                        <th>Category</th>
                        <th>Control</th>
                      </tr>
                    </thead>
                    <tbody id="thisrowMCat">

                    </tbody>
                  </table>
                </div>
              </div>

              <div id="maintenanceformshowhide" style="display:none;">
                <form class="form-horizontal" id="save_maintenance" onsubmit="javascript:return frmaddmaintenance();">
                <input type="hidden" name="estimatedornot" id="estimatedornot" value="0">
                <input type="hidden" name="mVehicleDevice" id="mVehicleDevice">
                <input type="hidden" name="mVehicleNo" id="mVehicleNo">
                <input type="hidden" name="mvehicle_mv03" id="mvehicle_mv03">
                <input type="text"   name="isotherscategory" id="isotherscategory" value="0" hidden>
                <table class="table table-striped" width="100%" style="font-size:12px;">
                  <tr>
                    <td>Maintenance Type</td>
                    <td>
                      <div id="type_select_form"></div>
                    </td>
                    <td></td>
                  </tr>

                  <tr id="rowMCategory" style="display:none;">
                    <td>Maintenance Category</td>
                    <td>
                      <div id="cat_select_form"></div>
                    </td>
                    <td></td>
                  </tr>

                  <tr id="rowOthersCategory" style="display:none;">
                    <td>Lain - lain</td>
                    <td>
                      <div id="others_category">
                        <input type="text" name="maintenanceothers_category" id="maintenanceothers_category" class="form-control">
                      </div>
                    </td>
                    <td></td>
                  </tr>

                  <tr>
                    <td>Vehicle</td>
                    <td>
                      <input type="text" name="mVehicle_form" id="mVehicle_form" class="form-control" readonly>
                    </td>
                    <td>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      <input type="checkbox" name="showdate" id="showdate" onclick="showthisdate()" ><span style="color:blue;"> Completed Task</span>
                    </td>
                    <td></td>
                    <td></td>
                  </tr>

                  <tr id="starttime_form">
                    <td>Start Time</td>
                    <td>
                      <div class="input-group date form_date col-md-6" data-date="" data-date-format="dd-mm-yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
                        <input class="form-control" size="5" type="text" readonly name="startdate" id="startdate" value="<?=date('d-m-Y')?>">
                        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                      </div>
                      <input type="hidden" id="dtp_input2" value="" />
                      <div class="input-group date form_time col-md-6" data-date="" data-date-format="hh:ii" data-link-field="dtp_input3" data-link-format="hh:ii">
                        <input class="form-control switch" size="5" type="text" readonly id="shour" name="shour" value="06:00" onclick="houronclick();">
                        <!-- value="<?=date("H:i",strtotime("00:00:00"))?>" -->
                        <span class="input-group-addon"><span class="fa fa-clock-o"></span></span>
                      </div>
                      <input type="hidden" id="dtp_input3" value="" />
                    </td>
                    <td></td>
                  </tr>

                  <tr id="endtime_form" style="display:none;">
                    <td>End Time</td>
                    <td>
                      <div class="input-group date form_date col-md-6" data-date="" data-date-format="dd-mm-yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
                        <input class="form-control" size="5" type="text" readonly name="enddate" id="enddate" value="<?=date('d-m-Y')?>">
                        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                      </div>
                      <input type="hidden" id="dtp_input2" value="" />
                      <div class="input-group date form_time col-md-6" data-date="" data-date-format="hh:ii" data-link-field="dtp_input3" data-link-format="hh:ii">
                        <input class="form-control switch" size="5" type="text" readonly id="ehour" name="ehour" value="08:10" onclick="houronclick();">
                        <!-- value="<?=date("H:i",strtotime("23:59:59"))?>" -->
                        <span class="input-group-addon"><span class="fa fa-clock-o"></span></span>
                      </div>
                      <input type="hidden" id="dtp_input3" value="" />
                    </td>
                    <td></td>
                  </tr>

                  <tr>
                    <td>Detail Info</td>
                    <td>
                      <textarea name="mNotes_form" id="mNotes_form" rows="5" cols="30"></textarea>
                    </td>
                    <td></td>
                  </tr>

                  <tr>
                    <td></td>
                    <td></td>
                    <td>
                      <div class="text-right">
                        <button type="submit" class="btn btn-success">Save</button>
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
</div>

<script type="text/javascript" src="js/script.js"></script>
<script src="<?php echo base_url()?>assets/dashboard/assets/js/jquery-1.7.1.min.js" type="text/javascript"></script>

<script type="text/javascript">
  function houronclick() {
    $(".switch").html("<?php echo date("Y F d ")?>");
  }

  function maintenanceType(){
    $("#maintenanceform").hide();
    $("#thistableMType").show();
    $("#thistableMCat").hide();
    $("#maintenance_type_in_cat_show").hide();
    $("#maintenanceformshowhide").hide();
    $("#modalStateTitle").html("Maintenance Type");
    getMaintenanceType();
    modalformaintenance('modalMaintenanceShow');
  }

  function getMaintenanceType(){
    $("#thisrow").html("");
    $.post("<?php echo base_url() ?>maintenance/maintenance_type", {}, function(response){
      console.log("response : ", response);
        var data = response.data;
        var html = "";
          if (data.length > 0) {
            var data = response.data;
              for (var i = 0; i < data.length; i++) {
                    html += '<tr>';
                      html += '<td>'+(i+1)+'</td>';
                      html += '<td>'+data[i].maintenance_type_name+'</td>';
                      html += '<td><button type="button" class="btn btn-danger" onclick="deleteMaintenanceType('+data[i].maintenance_type_id+')">'+
                                '<span class="fa fa-trash"></span>'+
                               '</button></td>';
                    html += '</tr>';
              }
            $("#thisrow").html(html);
          }
    }, "json");
  }

  function savemaintenancetype(){
    $.post("<?php echo base_url() ?>maintenance/maintenance_type_save", $("#frmmaintenancetype").serialize(), function(response){
      console.log("response : ", response);
      $("#thisrow").html("");

      if (response.msg == "success") {
        var data = response.data;
        var html = "";

          for (var i = 0; i < data.length; i++) {
                html += '<tr>';
                  html += '<td>'+(i+1)+'</td>';
                  html += '<td>'+data[i].maintenance_type_name+'</td>';
                  html += '<td><button type="button" class="btn btn-danger" onclick="deleteMaintenanceType('+data[i].maintenance_type_id+')">'+
                            '<span class="fa fa-trash"></span>'+
                           '</button></td>';
                html += '</tr>';
          }
        $("#thisrow").html(html);
        $("#type_name").val("");
      }else {
        if (confirm("Failed Insert Maintenance Type")) {
          window.location = '<?php echo base_url()?>maintenance';
        }
      }
    }, "json");
    return false;
  }

  function deleteMaintenanceType(id){
    if (confirm("Anda yakin akan menghapus data ini ?")) {
      $.post("<?php echo base_url() ?>maintenance/deleteMaintenanceType", {id: id}, function(response){
        var msg = response.msg;
          if (msg == "success") {
            var data = response.data;
            var html = "";

              for (var i = 0; i < data.length; i++) {
                    html += '<tr>';
                      html += '<td>'+(i+1)+'</td>';
                      html += '<td>'+data[i].maintenance_type_name+'</td>';
                      html += '<td><button type="button" class="btn btn-danger" onclick="deleteMaintenanceType('+data[i].maintenance_type_id+')">'+
                                '<span class="fa fa-trash"></span>'+
                               '</button></td>';
                    html += '</tr>';
              }
            $("#thisrow").html(html);
          }
      }, "json");
    }
  }

  function maintenanceCategory(){
    $("#maintenanceform").hide();
    $("#thistableMType").hide();
    $("#maintenanceformshowhide").hide();
    $("#thistableMCat").show();
    $("#modalStateTitle").html("Maintenance Category");
    getMaintenanceCat();
    modalformaintenance('modalMaintenanceShow');
  }

  function getMaintenanceCat(){
    $("#thisrow").html("");
    $.post("<?php echo base_url() ?>maintenance/maintenance_cat", {}, function(response){
      console.log("response : ", response);
        var data     = response.data;
        var dataType = response.dataType;
        var html = "";
          if (data.length > 0) {
            var data = response.data;
              for (var i = 0; i < data.length; i++) {
                html += '<tr>';
                  html += '<td>'+(i+1)+'</td>';
                  for (var j = 0; j < dataType.length; j++) {
                    if (data[i].maintenance_cat_typeid == dataType[j].maintenance_type_id) {
                      html += '<td>'+dataType[j].maintenance_type_name+'</td>';
                    }
                  }
                  html += '<td>'+data[i].maintenance_cat_name+'</td>';
                  html += '<td><button type="button" class="btn btn-danger" onclick="deleteMaintenanceCat('+data[i].maintenance_cat_id+')">'+
                            '<span class="fa fa-trash"></span>'+
                           '</button></td>';
                html += '</tr>';
              }
            $("#thisrowMCat").html(html);
          }

          if (dataType.length > 0) {
            var htmlType = "";
              htmlType +='<select class="form-control select2" name="maintenance_type_in_cat" id="maintenance_type_in_cat">';
              for (var j = 0; j < dataType.length; j++) {
                htmlType += '<option value='+dataType[j].maintenance_type_id+'>'+dataType[j].maintenance_type_name+'</option>';
              }
              htmlType +='</select>';
              $("#cat_name").show();
              $("#btnSimpanMCat").show();
            $("#maintenance_type_in_cat_show").html(htmlType);
          }else {
            $("#cat_name").hide();
            $("#btnSimpanMCat").hide();
            $("#maintenance_type_in_cat_show").html("Please input Maintenance type First");
          }
          $("#maintenance_type_in_cat_show").show();
    }, "json");
  }

  function savemaintenancecat(){
    $.post("<?php echo base_url() ?>maintenance/maintenance_cat_save", $("#frmmaintenancecat").serialize(), function(response){
      console.log("response : ", response);
      $("#thisrowMCat").html("");

      if (response.msg == "success") {
        var data     = response.data;
        var dataType = response.dataType;
        var html = "";
          if (data.length > 0) {
            var data = response.data;
              for (var i = 0; i < data.length; i++) {
                    html += '<tr>';
                      html += '<td>'+(i+1)+'</td>';
                      for (var j = 0; j < dataType.length; j++) {
                        if (data[i].maintenance_cat_typeid == dataType[j].maintenance_type_id) {
                          html += '<td>'+dataType[j].maintenance_type_name+'</td>';
                        }
                      }
                      html += '<td>'+data[i].maintenance_cat_name+'</td>';
                      html += '<td><button type="button" class="btn btn-danger" onclick="deleteMaintenanceCat('+data[i].maintenance_cat_id+')">'+
                                '<span class="fa fa-trash"></span>'+
                               '</button></td>';
                    html += '</tr>';
              }
            $("#thisrowMCat").html(html);
          }
          $("#cat_name").val("");
          $("#maintenance_type_in_cat_show").show();
      }else {
        if (confirm("Failed Insert Maintenance Category")) {
          window.location = '<?php echo base_url()?>maintenance';
        }
      }
    }, "json");
    return false;
  }

  function deleteMaintenanceCat(id){
    if (confirm("Anda yakin akan menghapus data ini ?")) {
      $.post("<?php echo base_url() ?>maintenance/deleteMaintenanceCat", {id: id}, function(response){
        var msg = response.msg;
          if (msg == "success") {
            var data     = response.data;
            var dataType = response.dataType;
            var html     = "";

              for (var i = 0; i < data.length; i++) {
                    html += '<tr>';
                      html += '<td>'+(i+1)+'</td>';
                      for (var j = 0; j < dataType.length; j++) {
                        if (data[i].maintenance_cat_typeid == dataType[j].maintenance_type_id) {
                          html += '<td>'+dataType[j].maintenance_type_name+'</td>';
                        }
                      }
                      html += '<td>'+data[i].maintenance_cat_name+'</td>';
                      html += '<td><button type="button" class="btn btn-danger" onclick="deleteMaintenanceCat('+data[i].maintenance_cat_id+')">'+
                                '<span class="fa fa-trash"></span>'+
                               '</button></td>';
                    html += '</tr>';
              }
            $("#thisrowMCat").html(html);
          }
      }, "json");
    }
  }

  function showMaintenanceForm(vdevice){
    $("#maintenanceform").hide();
    $("#thistableMType").hide();
    $("#thistableMCat").hide();
    $("#maintenance_type_in_cat_show").hide();
    $("#modalStateTitle").html("Maintenance Form");
    $("#maintenanceform").show();
    console.log("vdevice : ", vdevice);
    $("#type_select_form").html("");
    $.post("<?php echo base_url() ?>maintenance/getTypeCatMaintenance", {vdevice : vdevice}, function(response){
      console.log("response mForm : ", response);
          var vehicle = response.datavehicle[0].vehicle_no+" "+response.datavehicle[0].vehicle_name;
                        $("#mVehicle_form").val(vehicle);
                        $("#mVehicleDevice").val(response.datavehicle[0].vehicle_device);
                        $("#mVehicleNo").val(response.datavehicle[0].vehicle_no);
                        $("#mvehicle_mv03").val(response.datavehicle[0].vehicle_mv03);

          var dataType = response.dataType;
          var htmlType = "";

            htmlType +='<select class="form-control select2" name="mType_form" id="mType_form" onchange="getMCategoryByType()">';
              htmlType += '<option value="0000">Choose Maintenance Type</option>';
              for (var i = 0; i < dataType.length; i++) {
                htmlType += '<option value='+dataType[i].maintenance_type_id+'|'+dataType[i].maintenance_type_name+'>'+dataType[i].maintenance_type_name+'</option>';
              }
            htmlType +='</select>';

          $("#type_select_form").html(htmlType);
          $("#maintenanceformshowhide").show();
          modalformaintenance('modalMaintenanceShow');
    }, "json");
  }

  function getMCategoryByType(){
    var mType_form = $("#mType_form").val();
      if (mType_form != "0000") {
        var value = mType_form.split("|");
        $.post("<?php echo base_url() ?>maintenance/categorybyType", {mTypeID : value[0]}, function(response){
          console.log("response : ", response);
          var dataCat = response.dataCat;
          var htmlCat = "";

            htmlCat +='<select class="form-control select2" name="mCat_form" id="mCat_form" onchange="getOthersCategory()">';
              htmlCat += '<option value="0000">Choose Maintenance Category</option>';
              for (var i = 0; i < dataCat.length; i++) {
                htmlCat += '<option value='+dataCat[i].maintenance_cat_typeid+'|'+dataCat[i].maintenance_cat_name+'>'+dataCat[i].maintenance_cat_name+'</option>';
              }
              htmlCat += '<option value="others">Lain - lain</option>';
            htmlCat +='</select>';
          $("#cat_select_form").html(htmlCat);
          $("#rowMCategory").show();
        }, "json");
      }
  }

  function getOthersCategory(){
    var value = $("#mCat_form").val();
      if (value == "others") {
        $("#isotherscategory").val(1);
        $("#rowOthersCategory").show();
      }else {
        $("#isotherscategory").val(0);
        $("#rowOthersCategory").hide();
      }
  }

  function showthisdate() {
    // console.log("show nih");
    var checkBox             = document.getElementById("showdate");

    // If the checkbox is checked, display the output text
    if (checkBox.checked == true){
      $("#estimatedornot").val(1);
      $("#starttime_form").show();
      $("#endtime_form").show();
    } else {
      $("#estimatedornot").val(0);
      $("#starttime_form").show();
      $("#endtime_form").hide();
    }
  }

  function frmaddmaintenance(){
    var mType_form                 = $("#mType_form").val();
    var mCat_form                  = $("#mCat_form").val();
    var isotherscategory           = $("#isotherscategory").val();
    var maintenanceothers_category = $("#maintenanceothers_category").val();
    var mNotes_form                = $("#mNotes_form").val();

    if (mType_form == "0000") {
      alert("Please select maintenance type first");
      return false;
    }

    if (mCat_form == "0000") {
      alert("Please select maintenance category first");
      return false;
    }

    if (isotherscategory == "1") {
      if (maintenanceothers_category == "") {
        alert("Please fill the others category field");
        return false;
      }
    }

    if (mNotes_form == "") {
      alert("Please fill the notes field");
      return false;
    }

    if (mType_form != "0000" && mCat_form != "0000") {
      $.post("<?php echo base_url() ?>maintenance/savemaintenance_form", $("#save_maintenance").serialize(), function(response){
        console.log("response : ", response);
          if (response.msg == "success") {
            if (confirm("Successfully saved maintenance")) {
              window.location = '<?php echo base_url() ?>maintenance';
            }else {
              if (confirm("Failed saved maintenance")) {
                window.location = '<?php echo base_url() ?>maintenance';
              }
            }
          }
      }, "json");
      return false;
    }
  }
</script>
