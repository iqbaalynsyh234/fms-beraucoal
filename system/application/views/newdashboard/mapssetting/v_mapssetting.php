<style media="screen">
#maps-setting{
  background-color: #221f1f;
  color: white;
}
</style>

<div class="sidebar-container">
  <?=$sidebar;?>
</div>

<div class="page-content-wrapper">
  <div class="page-content">
    <br>
    <?php if ($this->session->flashdata('notif')) {?>
      <div class="alert alert-success" id="notifnya" style="display: none;"><?php echo $this->session->flashdata('notif');?></div>
    <?php }?>
    <!--<div class="alert alert-success" id="notifnya2" style="display: none;"></div>-->
      <div class="col-md-12">
          <div class="panel" id="panel_form">
            <header class="panel-heading panel-heading-red" id="maps-setting">Maps Setting</header>
            <div class="panel-body" id="bar-parent10">

              <div class="row">
                <div class="col-md-2">
                  <p>Type Of Setting</p>
                </div>
                <div class="col-md-1">
                  <p> : </p>
                </div>
                <div class="col-md-4">
                  <select class="form-horizontal select2" name="mapssettingoption" id="mapssettingoption" onchange="mapssettingoptionohange();" style="width:100%;">
                    <option value="mapssettingdefault">--Maps Setting</option>
                    <option value="mapssettinginKM1Muatan">KM 1 Muatan</option>
                    <option value="mapssettinginAllKMKosongan">All KM Kosongan</option>
                    <option value="mapssettinginAllKMMuatan">All KM Muatan</option>
                    <option value="mapssettinginrom">ROM</option>
                    <option value="mapssettinginport">PORT</option>
                    <option value="mapssettingintiakosongan">Jalur TIA Kosongan</option>
                    <option value="mapssettingintiamuatan">Jalur TIA Muatan</option>
                    <!-- <option value="mapssettinginpoolws">In POOL / WS</option> -->
                  </select>
                </div>
                <div class="col-md-2">
                  <img id="loader2" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" style="display:none;"/>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12">
                    <form class="block-content form" name="frmadd" id="frmadd" onsubmit="javascript: return frmadd_onsubmit()">
                      <input type="text" name="mapsetting_type" id="mapsetting_type" class="form-control" hidden>
                      <input type="text" name="valueMapsOption" id="valueMapsOption" class="form-control" hidden>

                      <table class="table" id="mapssetting_content"></table>

                      <?php
                        $privilegecode = $this->sess->user_id_role;
                          if ($privilegecode != 3) { ?>
                            <div class="row" id="mapsetting_btnsave" style="display:none;">
                              <div class="col-md-12">
                                <div class="text-right">
                                  <input class="btn btn-warning" type="button" name="btncancel" id="btncancel" value=" Cancel " onclick="location='<?=base_url()?>maps/heatmap'" />
                  								<input class="btn btn-success" type="submit" name="btnsave" id="btnsave" value=" Save " />
                                  <img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" style="display:none;"/>
                                </div>
                              </div>
                            </div>
                          <?php }
                       ?>
                    </form>
                </div>
              </div>

          </div>
        </div>
      </div>
</div>


<script type="text/javascript" src="js/script.js"></script>
<script src="<?php echo base_url()?>assets/dashboard/assets/js/jquery-1.7.1.min.js" type="text/javascript"></script>

<script type="text/javascript">
  $("#notifnya").fadeIn(1000);
  $("#notifnya").fadeOut(5000);

  function mapssettingoptionohange(){
    var mapsoptionvalue = $("#mapssettingoption").val();
      if (mapsoptionvalue == "mapssettingdefault") {
        alert("Silahkan pilih pengaturan map yang diinginkan");
      }else {
        // console.log("mapsoptionvalue : ", mapsoptionvalue);
        // alert("Silahkan pilih2 pengaturan map yang diinginkan");
        mapsoptionvalue = mapsoptionvalue;
        var data = {
          mapsoptionvalue : mapsoptionvalue
        };
        $("#mapssetting_content").html("");
        $("#loader2").show();
        jQuery.post("<?php echo base_url() ?>mapssetting/searchbytype", data, function(response){
          $("#loader2").hide();
          console.log("response searchbytype : ", response);
          var data = response.data;
          $("#mapsetting_type").val(response.street_type);
          $("#valueMapsOption").val(response.mapsoptionvalue);
          var html = "";
          html += '<tr>';
              html += '<th>Name</th>';
              if (response.mapsoptionvalue == "mapssettinginKM1Muatan") {
                html += '<th>Batas Bawah</th>';
                html += '<th>Batas Tengah</th>';
                html += '<th>Batas Atas</th>';
              }else {
                html += '<th>Batas Tengah</th>';
                html += '<th>Batas Atas</th>';
              }
          html += '</tr>';
          for (var i = 0; i < data.length; i++) {
              html += '<tr>';
              html += '<td>'+data[i].mapsetting_name_alias+'</td>';
                if (response.mapsoptionvalue == "mapssettinginAllKMMuatan") {
                  html += '<td><input type="text" class="form-control" name="'+data[i].mapsetting_name+'_middle_limit_allkmmuatan" id="'+data[i].mapsetting_name+'_middle_limit_allkmmuatan" value="'+data[i].mapsetting_middle_limit+'"></td>';
                  html += '<td><input type="text" class="form-control" name="'+data[i].mapsetting_name+'_top_limit_allkmmuatan" id="'+data[i].mapsetting_name+'_top_limit_allkmmuatan" value="'+data[i].mapsetting_top_limit+'"></td>';
                }else if (response.mapsoptionvalue == "mapssettinginAllKMKosongan") {
                  html += '<td><input type="text" class="form-control" name="'+data[i].mapsetting_name+'_middle_limit_allkmkosongan" id="'+data[i].mapsetting_name+'_middle_limit_allkmkosongan" value="'+data[i].mapsetting_middle_limit+'"></td>';
                  html += '<td><input type="text" class="form-control" name="'+data[i].mapsetting_name+'_top_limit_allkmkosongan" id="'+data[i].mapsetting_name+'_top_limit_allkmkosongan" value="'+data[i].mapsetting_top_limit+'"></td>';
                }else if (response.mapsoptionvalue == "mapssettinginKM1Muatan") {
                  html += '<td><input type="text" class="form-control" name="'+data[i].mapsetting_name+'_bottom_limit_km1muatan" id="'+data[i].mapsetting_name+'_bottom_limit_km1muatan" value="'+data[i].mapsetting_bottom_limit+'"></td>';
                  html += '<td><input type="text" class="form-control" name="'+data[i].mapsetting_name+'_middle_limit_km1muatan" id="'+data[i].mapsetting_name+'_middle_limit_km1muatan" value="'+data[i].mapsetting_middle_limit+'"></td>';
                  html += '<td><input type="text" class="form-control" name="'+data[i].mapsetting_name+'_top_limit_km1muatan" id="'+data[i].mapsetting_name+'_top_limit_km1muatan" value="'+data[i].mapsetting_top_limit+'"></td>';
                }else if (response.mapsoptionvalue == "mapssettingintiakosongan") {
                  html += '<td><input type="text" class="form-control" name="'+data[i].mapsetting_name+'_middle_limit_alltiakmkosongan" id="'+data[i].mapsetting_name+'_middle_limit_alltiakmkosongan" value="'+data[i].mapsetting_middle_limit+'"></td>';
                  html += '<td><input type="text" class="form-control" name="'+data[i].mapsetting_name+'_top_limit_alltiakmkosongan" id="'+data[i].mapsetting_name+'_top_limit_alltiakmkosongan" value="'+data[i].mapsetting_top_limit+'"></td>';
                }else if (response.mapsoptionvalue == "mapssettingintiamuatan") {
                  html += '<td><input type="text" class="form-control" name="'+data[i].mapsetting_name+'_middle_limit_alltiakmmuatan" id="'+data[i].mapsetting_name+'_middle_limit_alltiakmmuatan" value="'+data[i].mapsetting_middle_limit+'"></td>';
                  html += '<td><input type="text" class="form-control" name="'+data[i].mapsetting_name+'_top_limit_alltiakmmuatan" id="'+data[i].mapsetting_name+'_top_limit_alltiakmmuatan" value="'+data[i].mapsetting_top_limit+'"></td>';
                }else {
                  html += '<td><input type="text" class="form-control" name="'+data[i].mapsetting_name+'_middle_limit" id="'+data[i].mapsetting_name+'_middle_limit" value="'+data[i].mapsetting_middle_limit+'"></td>';
                  html += '<td><input type="text" class="form-control" name="'+data[i].mapsetting_name+'_top_limit" id="'+data[i].mapsetting_name+'_top_limit" value="'+data[i].mapsetting_top_limit+'"></td>';
                }
              html += '</tr>';
          }
          $("#mapssetting_content").html(html);
          $("#mapsetting_btnsave").show();
        }, "json");
      }
  }

  function frmadd_onsubmit(){
    $("#loader").show();
    // jQuery("#frmadd").serialize()
			jQuery.post("<?php echo base_url();?>mapssetting/savethismapsetting", JSON.stringify($("#frmadd").serializeArray()), function(response){
        console.log("response savethismapsetting : ", response);
        $("#loader").hide();
          if (response.code == 200) {
            if (confirm("Pengaturan limit berhasil disimpan")) {
              window.location = '<?php echo base_url() ?>mapssetting';
            }
          }
			}, "json");
		return false;
	}

  // FOR DISABLE SUBMIT FORM
  $(window).keydown(function(event){
    if(event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });
</script>
