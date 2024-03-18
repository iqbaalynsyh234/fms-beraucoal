<style media="screen">
.timeline {
  list-style: none;
  padding: 20px 0 20px;
  position: relative;
}

.timeline:before {
  top: 30px;
  bottom: 30px;
  position: absolute;
  content: " ";
  width: 5px;
  background-color: #000000;
  left: 65%;
  /* margin-left: -1.5px; */
}

.timeline > li {
  margin-bottom: 2px;
  position: relative;
}

.timeline > li:before,
.timeline > li:after {
  content: " ";
  display: table;
}

.timeline > li:after {
  clear: both;
}

.timeline > li:before,
.timeline > li:after {
  content: " ";
  display: table;
}

.timeline > li:after {
  clear: both;
}

.timeline > li > .timeline-panel {
  width: 46%;
  float: left;
  /* border: 1px solid #d4d4d4; */
  /* border-radius: 2px; */
  padding: 20px;
  position: relative;
  /* -webkit-box-shadow: 0 1px 6px rgba(0, 0, 0, 0.175); */
  /* box-shadow: 0 1px 6px rgba(0, 0, 0, 0.175); */
}

.timeline > li > .timeline-badge {
  color: #fff;
  width: 100px;
  height: 25px;
  line-height: 2px;
  font-size: 14px;
  text-align: center;
  position: absolute;
  left: 50%;
  margin-left: -25px;
  background-color: green;
  z-index: 100;
  /* border-top-right-radius: 50%;
  border-top-left-radius: 50%;
  border-bottom-right-radius: 50%;
  border-bottom-left-radius: 50%; */
}

.timeline > li > .timeline-badge2 {
  color: #fff;
  width: 40px;
  height: 40px;
  line-height: 25px;
  font-size: 14px;
  text-align: center;
  position: absolute;
  left: 50%;
  margin-left: -25px;
  top: -20%;
  padding: 7px;
  background-color: green;
  z-index: 100;
  border-top-right-radius: 50%;
  border-top-left-radius: 50%;
  border-bottom-right-radius: 50%;
  border-bottom-left-radius: 50%;
}

.timeline > li > .timeline-badge3 {
  color: #fff;
  width: 40px;
  height: 40px;
  line-height: 25px;
  font-size: 14px;
  text-align: center;
  position: absolute;
  left: 90%;
  margin-left: -25px;
  top: -20%;
  padding: 7px;
  background-color: green;
  z-index: 100;
  border-top-right-radius: 50%;
  border-top-left-radius: 50%;
  border-bottom-right-radius: 50%;
  border-bottom-left-radius: 50%;
}

.timeline > li.timeline-inverted > .timeline-panel {
  float: center;
}

.timeline > li.timeline-inverted > .timeline-panel:before {
  border-left-width: 0;
  border-right-width: 15px;
  left: -15px;
  right: auto;
}

.timeline > li.timeline-inverted > .timeline-panel:after {
  border-left-width: 0;
  border-right-width: 14px;
  left: -14px;
  right: auto;
}

.timeline-heading {
  width: 70%;
}
.timeline-clock {
  width: 25%;
}

.timeline-title {
  margin-top: 0;
  color: inherit;
}

.timeline-body > p,
.timeline-body > ul {
  margin-bottom: 0;
}

.timeline-body > p + p {
  margin-top: 5px;
}


  #valueTitle{
    margin: 5%;
    margin-left: 25%;
    font-weight: 400;
    font-size: 14px;
    color: white;
  }

  #valueonsite{
    margin: 5%;
    margin-left: 25%;
    font-weight: 400;
    font-size: 14px;
    color: white;
  }

  .custom-map-control-button {
    margin : 10px;
    height: 40px;
    cursor: pointer;
    direction: ltr;
    overflow: hidden;
    text-align: center;
    position: relative;
    color: rgb(0, 0, 0);
    font-family: "Roboto", Arial, sans-serif;
    -webkit-user-select: none;
    font-size: 18px !important;
    background-color: rgb(255, 255, 255);
    padding: 1px 6px;
    border-bottom-left-radius: 2px;
    border-top-left-radius: 2px;
    -webkit-background-clip: padding-box;
    background-clip: padding-box;
    border: 1px solid rgba(0, 0, 0, 0.14902);
    -webkit-box-shadow: rgba(0, 0, 0, 0.298039) 0px 1px 4px -1px;
    box-shadow: rgba(0, 0, 0, 0.298039) 0px 1px 4px -1px;
    min-width: 100px;
    font-weight: 500;
  }
</style>

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
          <div class="card-header">
            <!-- <h5>Maps</h5> -->
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-3">
                <select class="form-control select2" name="contractor" id="contractor" onchange="changevehiclelist()">
                </select>
              </div>

              <div class="col-md-3">
                <select class="form-control select2" name="searchnopol" id="searchnopol" onchange="forsearchinput()">
                </select>
              </div>

              <div class="col-md-3">
                <select class="form-control select2" name="mapsOptions" id="mapsOptions" onchange="mapsOptions()">
                  <option value="0">--Maps Option</option>
                  <option value="showHeatmap">Maps</option>
                  <option value="showTableMuatan1">Quick Count Data</option>
                  <option value="showTableMuatan2">Consolidated Data</option>
                  <option value="showTableRom">ROM</option>
                  <option value="showTablePort">PORT</option>
                </select>
              </div>

              <div class="col-md-3">
                <button type="button" name="button" class="btn btn-danger btn-sm" id="mapSetting" style="margin-left:2%;" onclick="customMymodal('modalMapSetting');">Pengaturan Map</button>
              </div>

            </div> <br>

            <div id="modalMapSetting" class="modal">
              <div class="modal-content">
                <div class="row">
                  <div class="col-md-10">
                    <p class="modalCustomTitle">
                      Pengaturan Map
                    </p>
                  </div>
                  <div class="col-md-2">
                    <div class="closethismodal btn btn-danger btn-sm">X</div>
                  </div>
                </div>
                <form class="form-horizontal" name="frmadd" id="frmadd" onsubmit="javascript:return saveMapSetting()">
                  <table class="table table-striped">
                    <tr>
                      <td>Batas Tengah (Kuning)</td>
                      <td>
                        <input type="number" name="middle_limit" id="middle_limit" class="form-control" value="<?php echo $mapsetting[0]['mapsetting_middle_limit'] ?>">
                      </td>
                    </tr>
                    <tr>
                      <td>Batas Atas (Merah)</td>
                      <td>
                        <input type="number" name="top_limit" id="top_limit" class="form-control" value="<?php echo $mapsetting[0]['mapsetting_top_limit'] ?>">
                      </td>
                    </tr>
                    <tr>
                      <td></td>
                      <td class="text-right">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                      </td>
                    </tr>
                  </table>
                </form>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <div id="tableShowMuatan" style="width: 100%; max-height: 450px; display:none;">
                  <!-- display:none; -->

                  <div class="row">
                    <div class="col-md-6">
                      <p style="margin-left: 9%;">K || M</p>
                    </div>

                    <div class="col-md-6">
                      <p style="position: absolute;
                        right: 0px;
                        padding: 3px;"
                        id="lastupdateconsolidated">
                      </p>
                    </div>
                  </div>

                  <div class="row" style="margin-top:-3%; margin-left: -5%; overflow-y:hidden; overflow-x:auto;">
                    <div class="col-md-2">
                      <ul class="timeline">
                        <?php for ($i=30; $i > 25; $i--) {?>
                          <li class="timeline-inverted">
                            <div class="" id="labelvehicleonmuatan<?php echo $i ?>">KM <?php echo $i; ?></div>
                            <div class="timeline-panel"></div>
                          </li>
                          <li class="timeline-inverted">
                            <div class="" id="vehicleonkosongan<?php echo $i ?>"></div>
                            <div class="" id="vehicleonmuatan<?php echo $i ?>"></div>
                            <div class="timeline-panel"></div>
                          </li>
                        <?php } ?>
                      </ul>
                    </div>

                    <div class="col-md-2">
                      <ul class="timeline">
                        <?php for ($i=25; $i > 20; $i--) {?>
                          <li class="timeline-inverted">
                            <div class="" id="labelvehicleonmuatan<?php echo $i ?>">KM <?php echo $i; ?></div>
                            <div class="timeline-panel"></div>
                          </li>
                          <li class="timeline-inverted">
                            <div class="" id="vehicleonkosongan<?php echo $i ?>"></div>
                            <div class="" id="vehicleonmuatan<?php echo $i ?>"></div>
                            <div class="timeline-panel"></div>
                          </li>
                        <?php } ?>
                      </ul>
                    </div>

                    <div class="col-md-2">
                      <ul class="timeline">
                        <?php for ($i=20; $i > 15; $i--) {?>
                          <li class="timeline-inverted">
                            <div class="" id="labelvehicleonmuatan<?php echo $i ?>">KM <?php echo $i; ?></div>
                            <div class="timeline-panel"></div>
                          </li>
                          <li class="timeline-inverted">
                            <div class="" id="vehicleonkosongan<?php echo $i ?>"></div>
                            <div class="" id="vehicleonmuatan<?php echo $i ?>"></div>
                            <div class="timeline-panel"></div>
                          </li>
                        <?php } ?>
                      </ul>
                    </div>

                    <div class="col-md-2">
                      <ul class="timeline">
                        <?php for ($i=15; $i > 10; $i--) {?>
                          <li class="timeline-inverted">
                            <div class="" id="labelvehicleonmuatan<?php echo $i ?>">KM <?php echo $i; ?></div>
                            <div class="timeline-panel"></div>
                          </li>
                          <li class="timeline-inverted">
                            <div class="" id="vehicleonkosongan<?php echo $i ?>"></div>
                            <div class="" id="vehicleonmuatan<?php echo $i ?>"></div>
                            <div class="timeline-panel"></div>
                          </li>
                        <?php } ?>
                      </ul>
                    </div>

                    <div class="col-md-2">
                      <ul class="timeline">
                        <?php for ($i=10; $i > 5; $i--) {?>
                          <li class="timeline-inverted">
                            <div class="" id="labelvehicleonmuatan<?php echo $i ?>">KM <?php echo $i; ?></div>
                            <div class="timeline-panel"></div>
                          </li>
                          <li class="timeline-inverted">
                            <div class="" id="vehicleonkosongan<?php echo $i ?>"></div>
                            <div class="" id="vehicleonmuatan<?php echo $i ?>"></div>
                            <div class="timeline-panel"></div>
                          </li>
                        <?php } ?>
                      </ul>
                    </div>

                    <div class="col-md-2">
                      <ul class="timeline">
                        <?php for ($i=5; $i > 0; $i--) {?>
                          <li class="timeline-inverted">
                            <div class="" id="labelvehicleonmuatan<?php echo $i ?>">KM <?php echo $i; ?></div>
                            <div class="timeline-panel"></div>
                          </li>
                          <li class="timeline-inverted">
                            <div class="" id="vehicleonkosongan<?php echo $i ?>"></div>
                            <div class="" id="vehicleonmuatan<?php echo $i ?>"></div>
                            <div class="timeline-panel"></div>
                          </li>
                        <?php } ?>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-12">
                <div id="tableShowRom" style="width: 100%; max-height: 400px; display:none;">
                  <div class="row">
                    <div class="col-md-2">
                      <table class="table table-bordered" style="font-size:12px;">
                          <tr>
                            <td>
                              <div class="btn-group">
                                <div type="button" class="" id="labelvehicleinrom_1"></div>
                                <div type="button" class="" id="vehicleinRom_1"></div>
                              </div>
                            </td>

                            <td>
                              <div class="btn-group">
                                <div type="button" class="btn btn-primary btn-lg" id="labelvehicleinrom_1_2_road"></div>
                                <div type="button" class="btn btn-primary btn-lg dropdown-toggle m-r-20" id="vehicleinRom_1_2_road"></div>
                              </div>
                            </td>

                            <td>
                              <div class="btn-group">
                                <div type="button" class="btn btn-primary btn-lg" id="labelvehicleinrom_2"></div>
                                <div type="button" class="btn btn-primary btn-lg dropdown-toggle m-r-20" id="vehicleinRom_2"></div>
                              </div>
                            </td>

                            <td>
                              <div class="btn-group">
                                <div type="button" class="btn btn-primary btn-lg" id="labelvehicleinrom_3"></div>
                                <div type="button" class="btn btn-primary btn-lg dropdown-toggle m-r-20" id="vehicleinRom_3"></div>
                              </div>
                            </td>
                          </tr>

                          <tr>
                            <td>
                              <div class="btn-group">
                                <div type="button" class="btn btn-primary btn-lg" id="labelvehicleinrom_3_4_road"></div>
                                <div type="button" class="btn btn-primary btn-lg dropdown-toggle m-r-20" id="vehicleinRom_3_4_road"></div>
                              </div>
                            </td>

                            <td>
                              <div class="btn-group">
                                <div type="button" class="btn btn-primary btn-lg" id="labelvehicleinrom_4"></div>
                                <div type="button" class="btn btn-primary btn-lg dropdown-toggle m-r-20" id="vehicleinRom_4"></div>
                              </div>
                            </td>

                            <td>
                              <div class="btn-group">
                                <div type="button" class="btn btn-primary btn-lg" id="labelvehicleinrom_6_road"></div>
                                <div type="button" class="btn btn-primary btn-lg dropdown-toggle m-r-20" id="vehicleinRom_6_road"></div>
                              </div>
                            </td>

                            <td>
                              <div class="btn-group">
                                <div type="button" class="btn btn-primary btn-lg" id="labelvehicleinrom_6"></div>
                                <div type="button" class="btn btn-primary btn-lg dropdown-toggle m-r-20" id="vehicleinRom_6"></div>
                              </div>
                            </td>
                          </tr>

                          <tr>
                            <td>
                              <div class="btn-group">
                                <div type="button" class="btn btn-primary btn-lg" id="labelvehicleinrom_7"></div>
                                <div type="button" class="btn btn-primary btn-lg dropdown-toggle m-r-20" id="vehicleinRom_7"></div>
                              </div>
                            </td>

                            <td>
                              <div class="btn-group">
                                <div type="button" class="btn btn-primary btn-lg" id="labelvehicleinrom_7_8_road"></div>
                                <div type="button" class="btn btn-primary btn-lg dropdown-toggle m-r-20" id="vehicleinRom_7_8_road"></div>
                              </div>
                            </td>

                            <td>
                              <div class="btn-group">
                                <div type="button" class="btn btn-primary btn-lg" id="labelvehicleinrom_8"></div>
                                <div type="button" class="btn btn-primary btn-lg dropdown-toggle m-r-20" id="vehicleinRom_8"></div>
                              </div>
                            </td>
                          </tr>
                      </table>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-12">
                <div id="tableShowPort" style="width: 100%; max-height: 400px; display:none;">
                  <div class="row">
                    <div class="col-md-2">
                      <table class="table table-bordered" style="font-size:12px;">
                          <tr>
                            <td>
                              <div class="btn-group">
                                <div type="button" class="btn btn-primary" id="labelvehicleinport_bbc"></div>
                                <div type="button" class="btn btn-primary dropdown-toggle m-r-20" id="vehicleinPort_bbc"></div>
                              </div>
                            </td>

                            <td>
                              <div class="btn-group">
                                <div type="button" class="btn btn-primary" id="labelvehicleinport_bib"></div>
                                <div type="button" class="btn btn-primary dropdown-toggle m-r-20" id="vehicleinPort_bib"></div>
                              </div>
                            </td>

                            <td>
                              <div class="btn-group">
                                <div type="button" class="btn btn-primary" id="labelvehicleinport_bir"></div>
                                <div type="button" class="btn btn-primary dropdown-toggle m-r-20" id="vehicleinPort_bir"></div>
                              </div>
                            </td>

                            <td>
                              <div class="btn-group">
                                <div type="button" class="btn btn-primary" id="labelvehicleinport_tia"></div>
                                <div type="button" class="btn btn-primary dropdown-toggle m-r-20" id="vehicleinPort_tia"></div>
                              </div>
                            </td>
                          </tr>

                      </table>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-8">
                <input type="hidden" id="valueMode" value="0">
                  <div id="mapShow">
                     <!-- display:none; -->
                     <div id="mapsnya" style="width: 100%; height: 400px;"></div>
                  </div>
              </div>

              <div class="col-md-4" id="realtimealertshowhide">
                 <!-- style="display:none;" -->
                <div class="panel" id="panel_form">
                  <header class="panel-heading panel-heading-red">
                    Realtime Alert
                    Show :
                    <select class="select" name="changelimitrealtimalert" id="changelimitrealtimalert" onchange="changelimit();">
                      <option value="10">10</option>
                      <option value="20">20</option>
                      <option value="30">30</option>
                    </select>

                    <button type="button" name="button" id="activatesound" class="btn btn-flat btn-sm" title="Sound" onclick="activatesound();">
                      <span class="fa fa-volume-up"></span>
                    </button>

                    <button type="button" name="button" id="activatesound2" class="btn btn-flat btn-sm" title="Sound" onclick="activatesound2();" style="display:none;">
                      <span class="fa fa-volume-off"></span>
                    </button>
                  </header>
                  <div class="panel-body" id="bar-parent10">
                    <div id="realtimealertcontent"></div>
                      <table class="table">
                        <div id="summaryalertcontent" style="margin-top: 1%; overflow-x: auto; height: 270px; max-height: 270px;"></div>
                      </table>
                  </div>
                  <div class="panel-footer">
                    <div class="row">
                      <div class="col-md-12">
                        <a href="<?php echo base_url() ?>overspeedreport" type="button" class="btn btn-flat form-control">View More</a>
                      </div>
                    </div>
                  </div>
              </div>
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
  $(document).ready(function() {
    setTimeout(function(){
      appendthevehiclelist();
      appendthecontractorlist();
    }, 3000);

    function appendthecontractorlist(){
      $.post("<?php echo base_url() ?>maps/getdatacontractor", {}, function(response){
        console.log("response : ", response);
        var data = response.data;
        var html = "";

            html += '<option value="0">--Contractor List</option>';
            for (var i = 0; i < data.length; i++) {
              html += '<option value="'+data[i].company_id+'">'+data[i].company_name+'</option>';
            }
          $("#contractor").html(html);
      },"json");
    }

    function appendthevehiclelist(){
      // console.log("masuk");
      var html = "";

          html += '<option value="0">--Vehicle List</option>';
          html += '<?php for ($i=0; $i < sizeof($vehicledata); $i++) {?>';
            html += '<option value="<?php echo $vehicledata[$i]['vehicle_no'] ?>"><?php echo $vehicledata[$i]['vehicle_no'] ?></option>';
          html += '<?php } ?>';

        $("#searchnopol").html(html);
    }
  });

  function changevehiclelist(){
    console.log("masuk gan");
    var companyid = $("#contractor").val();
    $.post("<?php echo base_url() ?>maps/getvehiclebycontractor", {companyid : companyid}, function(response){
      console.log("response : ", response);
      var data = response.data;
      var html = "";

          html += '<option value="0">--Vehicle List</option>';
          for (var i = 0; i < data.length; i++) {
            html += '<option value="'+data[i].vehicle_no+'">'+data[i].vehicle_no+'</option>';
          }
        $("#searchnopol").html(html);
    },"json");
  }

  function mapsOptions(){
    var mapsOptionsValue = $("#mapsOptions").val();
    console.log("mapsOptionsValue : ", mapsOptionsValue);
      if (mapsOptionsValue == "showHeatmap") {
        showHeatmap();
      }else if (mapsOptionsValue == "showTableMuatan1") {
        showTableMuatan(0);
      }else if (mapsOptionsValue == "showTableMuatan2") {
        showTableMuatan(1);
      }else if (mapsOptionsValue == "showTableKosongan") {
        showTableKosongan();
      }else if (mapsOptionsValue == "showTableRom") {
        showTableRom();
      }else if (mapsOptionsValue == "showTablePort") {
        showTablePort();
      }else {
        showHeatmap();
      }
  }

  function showHeatmap(){
    $("#heatmapbutton").hide();
    $("#mapShow").show();
    $("#realtimealertshowhide").show();
    $("#tableShowMuatan").hide();
    $("#tableShowKosongan").hide();
    $("#tableShowPort").hide();
    $("#tableShowRom").hide();
    soundisactive = 1;
  }

  function showTableMuatan(sikon){
    $("#heatmapbutton").show();
    $("#kosonganbutton").show();
    $("#mapShow").hide();
    $("#realtimealertshowhide").hide();
    $("#tableShowKosongan").hide();
    $("#tableShowPort").hide();
    $("#tableShowRom").hide();
    $("#tableShowMuatan").show();
    soundisactive = 0;

    $.post("<?php echo base_url() ?>maps/dataconsolidated", {}, function(response){
      console.log("response : ", response);
      var lastupdate        = response.datamuatan[0].minidashboard_created_date;
      if (sikon == 1) {
        $("#lastupdateconsolidated").html("Last Update : "+lastupdate);
        $("#lastupdateconsolidated").show();
      }else {
        $("#lastupdateconsolidated").hide();
      }

      var datamuatan        = JSON.parse(response.datamuatan[0].minidashboard_json);
      var datakosongan      = JSON.parse(response.datakosongan[0].minidashboard_json);
      var arraydatamuatan   = Object.keys(datamuatan).map((key) => [String(key), datamuatan[key]]);
      var arraydatakosongan = Object.keys(datakosongan).map((key) => [String(key), datakosongan[key]]);
      var sizemuatan        = arraydatamuatan.length;
      var sizekosongan      = arraydatakosongan.length;
      // console.log("datamuatan : ", datamuatan);
      // console.log("arraydatamuatan : ", arraydatamuatan[0][1]);
        for (var i = 0; i < sizekosongan; i++) {
          var jumlahfix = arraydatakosongan[i][1]; //arraydatamuatan[i][1]; //30+i; //arraydatamuatan[i][1];
          if (jumlahfix >= middle_limit && jumlahfix < top_limit) {
            $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
            $("#vehicleonkosongan"+i).addClass('timeline-badge2 btn btn-warning btn-md btn-circle');
          }else if(jumlahfix >= top_limit){
            $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
            $("#vehicleonkosongan"+i).addClass('timeline-badge2 btn btn-danger btn-md btn-circle');
          }else {
            $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
            $("#vehicleonkosongan"+i).addClass('timeline-badge2 btn btn-success btn-md btn-circle');
          }
          $("#vehicleonkosongan"+i).html(jumlahfix);
        }

        for (var i = 0; i < sizemuatan; i++) {
          var jumlahfix = arraydatamuatan[i][1]; //arraydatamuatan[i][1]; //30+i; //arraydatamuatan[i][1];
          if (jumlahfix >= middle_limit && jumlahfix < top_limit) {
            $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
            $("#vehicleonmuatan"+i).addClass('timeline-badge3 btn btn-warning btn-md btn-circle');
          }else if(jumlahfix >= top_limit){
            $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
            $("#vehicleonmuatan"+i).addClass('timeline-badge3 btn btn-danger btn-md btn-circle');
          }else {
            $("#labelvehicleonmuatan"+i).addClass('timeline-badge btn btn-primary btn-lg');
            $("#vehicleonmuatan"+i).addClass('timeline-badge3 btn btn-success btn-md btn-circle');
          }
          $("#vehicleonmuatan"+i).html(jumlahfix);
        }
    },"json");
  }

  function showTableRom(){
    $("#heatmapbutton").show();
    $("#mapShow").hide();
    $("#realtimealertshowhide").hide();
    $("#tableShowMuatan").hide();
    $("#tableShowKosongan").hide();
    $("#tableShowPort").hide();
    $("#tableShowRom").show();
    soundisactive = 0;

    $.post("<?php echo base_url() ?>maps/vehicleonrom", {}, function(response){
      console.log("response : ", response);
      var datainrom      = JSON.parse(response.data[0].minidashboard_json);
      var arraydatainrom = Object.keys(datainrom).map((key) => [String(key), datainrom[key]]);
      var size           = arraydatainrom.length;

      var labelvehicleinrom_1        = "ROM 01";
      var vehicleinRom_1             = arraydatainrom[0][1];
        $("#labelvehicleinrom_1").html(labelvehicleinrom_1);
        $("#vehicleinRom_1").html(vehicleinRom_1);
          if (vehicleinRom_1 > 30 && vehicleinRom_1 < 51) {
            $("#labelvehicleinrom_1").addClass('btn btn-warning btn-lg');
            $("#vehicleinRom_1").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
          }else if(vehicleinRom_1 > 50){
            $("#labelvehicleinrom_1").addClass('btn btn-danger btn-lg');
            $("#vehicleinRom_1").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
          }else {
            $("#labelvehicleinrom_1").addClass('btn btn-primary btn-lg');
            $("#vehicleinRom_1").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
          }

      var labelvehicleinrom_1_2_road = "ROM 01/02 ROAD";
      var vehicleinRom_1_2_road      = arraydatainrom[1][1];
        $("#labelvehicleinrom_1_2_road").html(labelvehicleinrom_1_2_road);
        $("#vehicleinRom_1_2_road").html(vehicleinRom_1_2_road);
          if (vehicleinRom_1_2_road > 30 && vehicleinRom_1_2_road < 51) {
            $("#labelvehicleinrom_1_2_road").addClass('btn btn-warning btn-lg');
            $("#vehicleinRom_1_2_road").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
          }else if(vehicleinRom_1_2_road > 50){
            $("#labelvehicleinrom_1_2_road").addClass('btn btn-danger btn-lg');
            $("#vehicleinRom_1_2_road").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
          }else {
            $("#labelvehicleinrom_1_2_road").addClass('btn btn-primary btn-lg');
            $("#vehicleinRom_1_2_road").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
          }

      var labelvehicleinrom_2        = "ROM 02";
      var vehicleinRom_2             = arraydatainrom[2][1];
        $("#labelvehicleinrom_2").html(labelvehicleinrom_2);
        $("#vehicleinRom_2").html(vehicleinRom_2);
          if (vehicleinRom_2 > 30 && vehicleinRom_2 < 51) {
            $("#labelvehicleinrom_2").addClass('btn btn-warning btn-lg');
            $("#vehicleinRom_2").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
          }else if(vehicleinRom_2 > 50){
            $("#labelvehicleinrom_2").addClass('btn btn-danger btn-lg');
            $("#vehicleinRom_2").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
          }else {
            $("#labelvehicleinrom_2").addClass('btn btn-primary btn-lg');
            $("#vehicleinRom_2").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
          }

      var labelvehicleinrom_3        = "ROM 03";
      var vehicleinRom_3             = arraydatainrom[3][1];
        $("#labelvehicleinrom_3").html(labelvehicleinrom_3);
        $("#vehicleinRom_3").html(vehicleinRom_3);
          if (vehicleinRom_3 > 30 && vehicleinRom_3 < 51) {
            $("#labelvehicleinrom_3").addClass('btn btn-warning btn-lg');
            $("#vehicleinRom_3").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
          }else if(vehicleinRom_3 > 50){
            $("#labelvehicleinrom_3").addClass('btn btn-danger btn-lg');
            $("#vehicleinRom_3").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
          }else {
            $("#labelvehicleinrom_3").addClass('btn btn-primary btn-lg');
            $("#vehicleinRom_3").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
          }

      var labelvehicleinrom_3_4_road = "ROM 03/04 ROAD";
      var vehicleinRom_3_4_road      = arraydatainrom[4][1];
        $("#labelvehicleinrom_3_4_road").html(labelvehicleinrom_3_4_road);
        $("#vehicleinRom_3_4_road").html(vehicleinRom_3_4_road);
          if (vehicleinRom_3_4_road > 30 && vehicleinRom_3_4_road < 51) {
            $("#labelvehicleinrom_3_4_road").addClass('btn btn-warning btn-lg');
            $("#vehicleinRom_3_4_road").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
          }else if(vehicleinRom_3_4_road > 50){
            $("#labelvehicleinrom_3_4_road").addClass('btn btn-danger btn-lg');
            $("#vehicleinRom_3_4_road").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
          }else {
            $("#labelvehicleinrom_3_4_road").addClass('btn btn-primary btn-lg');
            $("#vehicleinRom_3_4_road").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
          }

      var labelvehicleinrom_4        = "ROM 04";
      var vehicleinRom_4             = arraydatainrom[5][1];
        $("#labelvehicleinrom_4").html(labelvehicleinrom_4);
        $("#vehicleinRom_4").html(vehicleinRom_4);
          if (vehicleinRom_4 > 30 && vehicleinRom_4 < 51) {
            $("#labelvehicleinrom_4").addClass('btn btn-warning btn-lg');
            $("#vehicleinRom_4").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
          }else if(vehicleinRom_4 > 50){
            $("#labelvehicleinrom_4").addClass('btn btn-danger btn-lg');
            $("#vehicleinRom_4").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
          }else {
            $("#labelvehicleinrom_4").addClass('btn btn-primary btn-lg');
            $("#vehicleinRom_4").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
          }

      var labelvehicleinrom_6_road   = "ROM 06 ROAD";
      var vehicleinRom_6_road        = arraydatainrom[6][1];
        $("#labelvehicleinrom_6_road").html(labelvehicleinrom_6_road);
        $("#vehicleinRom_6_road").html(vehicleinRom_6_road);
          if (vehicleinRom_6_road > 30 && vehicleinRom_6_road < 51) {
            $("#labelvehicleinrom_6_road").addClass('btn btn-warning btn-lg');
            $("#vehicleinRom_6_road").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
          }else if(vehicleinRom_6_road > 50){
            $("#labelvehicleinrom_6_road").addClass('btn btn-danger btn-lg');
            $("#vehicleinRom_6_road").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
          }else {
            $("#labelvehicleinrom_6_road").addClass('btn btn-primary btn-lg');
            $("#vehicleinRom_6_road").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
          }

      var labelvehicleinrom_6        = "ROM 06";
      var vehicleinRom_6             = arraydatainrom[7][1];
        $("#labelvehicleinrom_6").html(labelvehicleinrom_6);
        $("#vehicleinRom_6").html(vehicleinRom_6);
          if (vehicleinRom_6 > 30 && vehicleinRom_6 < 51) {
            $("#labelvehicleinrom_6").addClass('btn btn-warning btn-lg');
            $("#vehicleinRom_6").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
          }else if(vehicleinRom_6 > 50){
            $("#labelvehicleinrom_6").addClass('btn btn-danger btn-lg');
            $("#vehicleinRom_6").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
          }else {
            $("#labelvehicleinrom_6").addClass('btn btn-primary btn-lg');
            $("#vehicleinRom_6").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
          }

      var labelvehicleinrom_7        = "ROM 07";
      var vehicleinRom_7             = arraydatainrom[8][1];
        $("#labelvehicleinrom_7").html(labelvehicleinrom_7);
        $("#vehicleinRom_7").html(vehicleinRom_7);
          if (vehicleinRom_7 > 30 && vehicleinRom_7 < 51) {
            $("#labelvehicleinrom_7").addClass('btn btn-warning btn-lg');
            $("#vehicleinRom_7").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
          }else if(vehicleinRom_7 > 50){
            $("#labelvehicleinrom_7").addClass('btn btn-danger btn-lg');
            $("#vehicleinRom_7").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
          }else {
            $("#labelvehicleinrom_7").addClass('btn btn-primary btn-lg');
            $("#vehicleinRom_7").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
          }

      var labelvehicleinrom_7_8_road = "ROM 07/08 ROAD";
      var vehicleinRom_7_8_road      = arraydatainrom[9][1];
        $("#labelvehicleinrom_7_8_road").html(labelvehicleinrom_7_8_road);
        $("#vehicleinRom_7_8_road").html(vehicleinRom_7_8_road);
          if (vehicleinRom_7_8_road > 30 && vehicleinRom_7_8_road < 51) {
            $("#labelvehicleinrom_7_8_road").addClass('btn btn-warning btn-lg');
            $("#vehicleinRom_7_8_road").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
          }else if(vehicleinRom_7_8_road > 50){
            $("#labelvehicleinrom_7_8_road").addClass('btn btn-danger btn-lg');
            $("#vehicleinRom_7_8_road").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
          }else {
            $("#labelvehicleinrom_7_8_road").addClass('btn btn-primary btn-lg');
            $("#vehicleinRom_7_8_road").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
          }

      var labelvehicleinrom_8        = "ROM 08";
      var vehicleinRom_8             = arraydatainrom[10][1];
      $("#labelvehicleinrom_8").html(labelvehicleinrom_8);
      $("#vehicleinRom_8").html(vehicleinRom_8);
        if (vehicleinRom_8 > 30 && vehicleinRom_8 < 51) {
          $("#labelvehicleinrom_8").addClass('btn btn-warning btn-lg');
          $("#vehicleinRom_8").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
        }else if(vehicleinRom_8 > 50){
          $("#labelvehicleinrom_8").addClass('btn btn-danger btn-lg');
          $("#vehicleinRom_8").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
        }else {
          $("#labelvehicleinrom_8").addClass('btn btn-primary btn-lg');
          $("#vehicleinRom_8").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
        }

    },"json");
  }

  function showTablePort(){
    $("#heatmapbutton").show();
    $("#mapShow").hide();
    $("#realtimealertshowhide").hide();
    $("#tableShowMuatan").hide();
    $("#tableShowKosongan").hide();
    $("#tableShowRom").hide();
    $("#tableShowPort").show();
    soundisactive = 0;

    $.post("<?php echo base_url() ?>maps/vehicleonport", {}, function(response){
      console.log("response : ", response);
      var datainport      = JSON.parse(response.data[0].minidashboard_json);
      var arraydatainport = Object.keys(datainport).map((key) => [String(key), datainport[key]]);
      var size            = arraydatainport.length;

      var labelvehicleinport_bbc        = "PORT BBC";
      var vehicleinPort_bbc             = arraydatainport[0][1];
        $("#labelvehicleinport_bbc").html(labelvehicleinport_bbc);
        $("#vehicleinPort_bbc").html(vehicleinPort_bbc);
          if (vehicleinPort_bbc > 30 && vehicleinPort_bbc < 51) {
            $("#labelvehicleinport_bbc").addClass('btn btn-warning btn-lg');
            $("#vehicleinPort_bbc").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
          }else if(vehicleinPort_bbc > 50){
            $("#labelvehicleinport_bbc").addClass('btn btn-danger btn-lg');
            $("#vehicleinPort_bbc").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
          }else {
            $("#labelvehicleinport_bbc").addClass('btn btn-primary btn-lg');
            $("#vehicleinPort_bbc").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
          }

      var labelvehicleinport_bib       = "PORT BIB";
      var vehicleinPort_bib             = arraydatainport[1][1];
        $("#labelvehicleinport_bib").html(labelvehicleinport_bib);
        $("#vehicleinPort_bib").html(vehicleinPort_bib);
          if (vehicleinPort_bib > 30 && vehicleinPort_bib < 51) {
            $("#labelvehicleinport_bib").addClass('btn btn-warning btn-lg');
            $("#vehicleinPort_bib").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
          }else if(vehicleinPort_bib > 50){
            $("#labelvehicleinport_bib").addClass('btn btn-danger btn-lg');
            $("#vehicleinPort_bib").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
          }else {
            $("#labelvehicleinport_bib").addClass('btn btn-primary btn-lg');
            $("#vehicleinPort_bib").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
          }

      var labelvehicleinport_bir = "PORT BIR";
      var vehicleinPort_bir      = arraydatainport[2][1];
        $("#labelvehicleinport_bir").html(labelvehicleinport_bir);
        $("#vehicleinPort_bir").html(vehicleinPort_bir);
          if (vehicleinPort_bir > 30 && vehicleinPort_bir < 51) {
            $("#labelvehicleinport_bir").addClass('btn btn-warning btn-lg');
            $("#vehicleinPort_bir").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
          }else if(vehicleinPort_bir > 50){
            $("#labelvehicleinport_bir").addClass('btn btn-danger btn-lg');
            $("#vehicleinPort_bir").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
          }else {
            $("#labelvehicleinport_bir").addClass('btn btn-primary btn-lg');
            $("#vehicleinPort_bir").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
          }

      var labelvehicleinport_tia = "PORT TIA";
      var vehicleinPort_tia      = arraydatainport[3][1];
        $("#labelvehicleinport_tia").html(labelvehicleinport_tia);
        $("#vehicleinPort_tia").html(vehicleinPort_tia);
          if (vehicleinPort_tia > 30 && vehicleinPort_tia < 51) {
            $("#labelvehicleinport_tia").addClass('btn btn-warning btn-lg');
            $("#vehicleinPort_tia").addClass('btn btn-warning btn-lg dropdown-toggle m-r-20');
          }else if(vehicleinPort_tia > 50){
            $("#labelvehicleinport_tia").addClass('btn btn-danger btn-lg');
            $("#vehicleinPort_tia").addClass('btn btn-danger btn-lg dropdown-toggle m-r-20');
          }else {
            $("#labelvehicleinport_tia").addClass('btn btn-primary btn-lg');
            $("#vehicleinPort_tia").addClass('btn btn-primary btn-lg dropdown-toggle m-r-20');
          }

    },"json");
  }

  setTimeout(function(){
    getmapsdata();
  }, 1000);

  var datafixnya        = "";
  var dataposition      = [];
  var overlaystatus     = 0;
  var overlaysarray     = [];
  var arraypointheatmap = [];
  var marker            = [];
  var markernya         = [];
  var markers           = [];
  var markerss          = [];
  var markerpools       = [];
  var intervalstart;
  var camdevices        = ["TK510CAMDOOR", "TK510CAM", "GT08", "GT08DOOR", "GT08CAM", "GT08CAMDOOR"];
  var bibarea           = ["KM", "POOL", "ST", "ROM", "PIT", "PORT", "POOl", "WS", "WB", "PT.BIB"];
  var car = "M17.402,0H5.643C2.526,0,0,3.467,0,6.584v34.804c0,3.116,2.526,5.644,5.643,5.644h11.759c3.116,0,5.644-2.527,5.644-5.644 V6.584C23.044,3.467,20.518,0,17.402,0z M22.057,14.188v11.665l-2.729,0.351v-4.806L22.057,14.188z M20.625,10.773 c-1.016,3.9-2.219,8.51-2.219,8.51H4.638l-2.222-8.51C2.417,10.773,11.3,7.755,20.625,10.773z M3.748,21.713v4.492l-2.73-0.349 V14.502L3.748,21.713z M1.018,37.938V27.579l2.73,0.343v8.196L1.018,37.938z M2.575,40.882l2.218-3.336h13.771l2.219,3.336H2.575z M19.328,35.805v-7.872l2.729-0.355v10.048L19.328,35.805z";
  var middle_limit = '<?php echo $mapsetting[0]['mapsetting_middle_limit'] ?>';
  var top_limit    = '<?php echo $mapsetting[0]['mapsetting_top_limit'] ?>';
  var vehicle;

  function initMap() {
    var vehicle           = '<?php echo json_encode($vehicledata); ?>';

    if (datafixnya == "") {
      try {
        var datacode  = JSON.parse(vehicle);
        // console.log("disini objpoolmaster: ", objpoolmaster);
      } catch (e) {
        // console.log("e : ", e);
      }
    } else {
      var datacode  = vehicle;
    }

    obj              = datacode;
    console.log("obj : ", obj);

    for (var i = 0; i < obj.length; i++) {
      arraypointheatmap.push(new google.maps.LatLng(obj[i].auto_last_lat, obj[i].auto_last_long));
    }

    // console.log("arraypointheatmap : ", arraypointheatmap);

    // for (var i = 0; i < obj.length; i++) {
    //   var vehiclePosition = new google.maps.LatLng(parseFloat(obj[i].auto_last_lat), parseFloat(obj[i].auto_last_long));
    //   dataposition.push(vehiclePosition);
    // }

    var gradientdefault = ["rgba(102, 255, 0, 0)",
                          "rgba(102, 255, 0, 1)",
                          "rgba(147, 255, 0, 1)",
                          "rgba(193, 255, 0, 1)",
                          "rgba(238, 255, 0, 1)",
                          "rgba(244, 227, 0, 1)",
                          "rgba(249, 198, 0, 1)",
                          "rgba(255, 170, 0, 1)",
                          "rgba(255, 113, 0, 1)",
                          "rgba(255, 57, 0, 1)",
                          "rgba(255, 0, 0, 1)"];

  map = new google.maps.Map(document.getElementById("mapsnya"), {
    zoom: 14,
    center: { lat: parseFloat(-3.7288), lng: parseFloat(115.6452)},
    mapTypeId: "satellite",
    options: {
      gestureHandling: 'greedy'
    }
  });
  heatmap = new google.maps.visualization.HeatmapLayer({
    data: arraypointheatmap,
    // gradient : gradientdefault
    // dissipating: true,
    // radius: 20,
    opacity: 50,
    maxIntensity: 8
    // data: dataposition,
  });
  heatmap.setMap(map);

  // TOOGEL BUTTON BIB MAP
    var toggleButton = document.createElement("button");
    toggleButton.textContent = "BIB Maps";
    toggleButton.classList.add("custom-map-control-button");
    map.controls[google.maps.ControlPosition.TOP_CENTER].push(toggleButton);

     toggleButton.addEventListener("click", () => {
      addoverlay(map);
    });

    var changegradient = document.createElement("button");
    changegradient.textContent = "Change Gradient";
    changegradient.classList.add("custom-map-control-button");
    map.controls[google.maps.ControlPosition.TOP_CENTER].push(changegradient);

     changegradient.addEventListener("click", () => {
      changeGradient();
    });

    // intervalstart = setInterval(simultango, 10000);
}

function changeGradient() {
  const gradient = [
    "rgba(0, 255, 255, 0)",
    "rgba(0, 255, 255, 1)",
    "rgba(0, 191, 255, 1)",
    "rgba(0, 127, 255, 1)",
    "rgba(0, 63, 255, 1)",
    "rgba(0, 0, 255, 1)",
    "rgba(0, 0, 223, 1)",
    "rgba(0, 0, 191, 1)",
    "rgba(0, 0, 159, 1)",
    "rgba(0, 0, 127, 1)",
    "rgba(63, 0, 91, 1)",
    "rgba(127, 0, 63, 1)",
    "rgba(191, 0, 31, 1)",
    "rgba(255, 0, 0, 1)",
  ];

  heatmap.set("gradient", heatmap.get("gradient") ? null : gradient);
  // heatmap.set("gradient", gradient);
}
</script>

<?php
$key = $this->config->item("GOOGLE_MAP_API_KEY");
//$key = "AIzaSyAYe-6_UE3rUgSHelcU1piLI7DIBnZMid4";

if(isset($key) && $key != "") { ?>
  <script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo $key;?>&libraries=visualization&callback=initMap" type="text/javascript" async></script>
  <?php } else { ?>
    <script src="http://maps.google.com/maps/api/js?sensor=false"></script>
    <?php } ?>
