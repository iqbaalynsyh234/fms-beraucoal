<style media="screen">
  div#modalalert {
    margin-top: 2%;
    margin-left: 26%;
    overflow-x: auto;
    position: absolute;
    background-color: #f1f1f1;
    text-align: left;
    border: 1px solid #d3d3d3;
    width: 55%;
  }

  div#modaldetailpopup {
    margin-top: 2%;
    margin-left: 36%;
    overflow-x: auto;
    position: absolute;
    background-color: #f1f1f1;
    text-align: left;
    border: 1px solid #d3d3d3;
    width: 30%;
  }

.timeline-wrap{
  margin: 11% 7%;
  top: 100px;
  position: relative;
  margin-left: 4%;
  margin-top: -6%;
}

.timeline{
  height:1px;
  width: 100%;
  background-color:#aabbc4;
  position:relative;
  height:5px;
}

 .marker{
   /* z-index:1000; */
   color: #fff;
  width: 50px;
  height: 50px;
  line-height: 50px;
  font-size: 1.4em;
  text-align: center;
  position: absolute;
  margin-left: -25px;
  background-color: #999999;
  border-radius: 50%;
        }

 .marker:hover{
   -moz-transform: scale(1.2);
-webkit-transform: scale(1.2);
-o-transform: scale(1.2);
-ms-transform: scale(1.2);
transform: scale(1.2);

   -webkit-transition: all 300ms ease;
-moz-transition: all 300ms ease;
-ms-transition: all 300ms ease;
-o-transition: all 300ms ease;
transition: all 300ms ease;
 }


.timeline-icon.one {
    background-color: blue !important;
}

.timeline-icon.two {
    background-color: #536295 !important;
}

.timeline-icon.three{
    background-color: #6976a2 !important;
}

.timeline-icon.plus{
    background-color: #6976a2 !important;
}

.timeline-icon.plus1{
    background-color: #6976a2 !important;
}

.timeline-icon.four {
    background-color: #6976a2 !important;
}


.mfirst{
     top:-25px;
}

.m2{
     top:-25px;
     left:15%
}

.m3{
     top:-25px;
      left:32.5%
}

.m4{
     top:-25px;
    left:50%
}

.m5{
     top:-25px;
    left:66%
}

.m6{
     top:-25px;
    left:83%
}

.m7{
     top:-25px;
    left:100%
}

.mlast{
     top:-25px;
    left:100%
}

.timeline-panel {
  margin-top: 20%;
	width: 500px;
  height: 200px;
  background-color: #cbd0df;
  border-radius:2px;
	position:relative;
	text-align:left;
  padding:10px;
	font-size:20px;
	font-weight:bold;
	line-height:20px;
  float:left;
}

.timeline-panel:after {
	content:'';
	position:absolute;
  margin-top: -12%;
	left:10%;
	width:0;
	height:0;
	border:12px solid transparent;
	border-bottom: 15px solid #cbd0df;
}

#icontemplate{
  margin-top: 30%;
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
    <div class="alert alert-success" id="notifnya2" style="display: none;"></div>
    <div class="row">
      <div class="col-md-12" id="tablecustomer">
        <div class="panel" id="panel_form">
          <header class="panel-heading panel-heading-blue">Timeline Tracking</header>
          <div class="panel-body" id="bar-parent10">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th style="text-align: center; vertical-align: middle; width:2%;">No</th>
                  <th width="20%;">Vehicle</th>
                  <th>Tracking</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $totaldata = 5;
                $totalroute = array('4', '3', '2', '3', '4');
                $no = 1;
                for ($i=0; $i < $totaldata; $i++) {?>
                  <tr>
                    <td>
                      <?php echo $i+1; ?>
                    </td>
                    <td>
                        B162<?php echo $i+1?>KRM
                        <!-- <span class="badge headerBadgeColor1" id="totalnotif<?php echo $i?>" style=" cursor: pointer; display:none;" onclick="notifonclick(<?php echo $i?>)"> </span> -->
                        <span class="material-icons mdl-badge mdl-badge--overlap material-warning" id="totalnotif<?php echo $i?>" onclick="notifonclick(<?php echo $i?>)" style="cursor: pointer; display:none;" title="Device Alert">error_outline</span>
                    </td>
                    <td>
                      <div class="timeline-wrap" id="timeline-wrap<?php echo $i?>">
                          <div class="timeline" id="timeline<?php echo $i?>"></div>
                          <div class="marker mfirst timeline-icon one" id="marker1">
                              <i class="fa fa-home" id="icontemplate"></i>
                          </div>
                          <p style="margin-top: 3%; margin-left: -2%;">
                            <b>Start</b>
                          </p>
                      <?php $nummarkerplus = 2; for ($j=0; $j < $totalroute[$i]; $j++) {?>
                        <div class="marker m<?php echo $nummarkerplus;?> timeline-icon plus" id="marker<?php echo $i.$nummarkerplus;?>" style="cursor: pointer;" onclick="detailpopup('<?php echo $nummarkerplus;?>')" title="Geofence : Geofence <?php echo ($j+1) ?>">
                            <i class="fa fa-map-marker" id="icontemplate"></i>
                        </div>
                      <?php $nummarkerplus++; } ?>
                      <div class="marker mlast timeline-icon four" id="marker<?php echo $i?>6">
                        <i class="fa fa-check" id="icontemplate"></i>
                      </div>
                      <p style="margin-top: -5.7%; margin-left: 97%;">
                        <b>Finish</b>
                      </p>
                    </div>

                      <!-- <div id="timeline-wrap">
                        <div id="timeline"></div>
                        <div class="marker mfirst timeline-icon four" id="marker1">
                            <i class="fa fa-check" id="icontemplate"></i>
                        </div>
                        <div class="marker m2 timeline-icon two" id="marker2">
                            <i class="fa fa-map-marker" id="icontemplate"></i>
                        </div>
                        <div class="marker m5 timeline-icon plus1" id="marker3">
                          <i class="fa fa-map-marker" id="icontemplate"></i>
                        </div>
                        <div class="marker m3 timeline-icon three" id="marker4">
                          <i class="fa fa-map-marker" id="icontemplate"></i>
                        </div>
                        <div class="marker m4 timeline-icon plus" id="marker5">
                          <i class="fa fa-map-marker" id="icontemplate"></i>
                        </div>
                        <div class="marker mlast timeline-icon one" id="marker6" style="background-color: blue !important;">
                          <i class="fa fa-home" id="icontemplate"></i>
                        </div>
                      </div> -->
                    </td>
                  </tr>
                <?php $no++; } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="modalalert" style="display: none;">
  <div id="modalalert1"></div>
  <div class="row">
    <div class="col-md-12">
      <div class="card card-topline-yellow">
        <div class="card-head">
          <header>Device Alert</header>
          <div class="tools">
            <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
            <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
            <button type="button" class="btn btn-danger" name="button" onclick="closemodalalert();">X</button>
          </div>
        </div>
        <div class="card-body" id="alertbody">

        </div>
      </div>
    </div>
  </div>
</div>

<div id="modaldetailpopup" style="display: none;">
  <div id="modaldetailpopup1"></div>
  <div class="row">
    <div class="col-md-12">
      <div class="card card-topline-yellow">
        <div class="card-head">
          <header>Detail Information</header>
          <div class="tools">
            <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
            <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
            <button type="button" class="btn btn-danger" name="button" onclick="closemodaldetailpopup();">X</button>
          </div>
        </div>
        <div class="card-body" id="detailpopupbody">

        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  var datadummy = {
  "0" : [{
    "0" : {
      "geofence" : "Lacak Mobil",
      "status" : "1",
      "kmtonextgeofence" : "12",
      "currentkm" : "0",
      "totalroute" : "6",
      "currentpositioncoord" : "-6.26288,106.9850633",
      "currentpositionaddress" : "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
      "persentbar" : "100%"
    },
    "1" : {
      "geofence" : "Naga Pekayon",
      "status" : "1",
      "kmtonextgeofence" : "80",
      "currentkm" : "0",
      "totalroute" : "6",
      "currentpositioncoord" : "-6.2494045,106.9901368",
      "currentpositionaddress" : "Jl. Jend. Ahmad Yani, RT.004/RW.001, Marga Jaya, Kec. Bekasi Bar., Kota Bks, Jawa Barat 17141",
      "persentbar" : "100%"
    },
    "2" : {
      "geofence" : "Mega Mall Bekasi",
      "status" : "1",
      "kmtonextgeofence" : "4",
      "currentkm" : "0",
      "totalroute" : "6",
      "currentpositioncoord" : "-6.2494045,106.9901368",
      "currentpositionaddress" : "Bks Cyber Park Kayuringin Jaya Kec. Bekasi Sel. Kota Bks Jawa Barat",
      "persentbar" : "100%"
    },
    "3" : {
      "geofence" : "Bekasi Cyber Park",
      "status" : "1",
      "kmtonextgeofence" : "2",
      "currentkm" : "0",
      "totalroute" : "6",
      "currentpositioncoord" : "-6.2259813,106.9988616",
      "currentpositionaddress" : "Jl. Bulevar Ahmad Yani, RT.006/RW.002, Marga Mulya, Kec. Bekasi Utara, Kota Bks, Jawa Barat 17142",
      "persentbar" : "100%"
    },
    "4" : {
      "geofence" : "Summarecon Mall Bekasi",
      "status" : "1",
      "kmtonextgeofence" : "2",
      "currentkm" : "0",
      "totalroute" : "6",
      "currentpositioncoord" : "-6.26288,106.9850633",
      "currentpositionaddress" : "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
      "persentbar" : "100%"
    },
    "5" : {
      "geofence" : "Lacak Mobil",
      "status" : "1",
      "kmtonextgeofence" : "12",
      "currentkm" : "0",
      "totalroute" : "6",
      "currentpositioncoord" : "-6.2915165,106.9638034",
      "currentpositionaddress" : "Jalan Raya Swatantra Kav.4 No.71 Pekayon, RT.004/RW.004, Jatirasa, Kec. Jatiasih, Kota Bks, Jawa Barat 17424",
      "persentbar" : "100%"
    }
  }],
  "1" : [{
    "0" : {
      "geofence" : "Lacak Mobil",
      "status" : "1",
      "kmtonextgeofence" : "12",
      "currentkm" : "0",
      "totalroute" : "6",
      "currentpositioncoord" : "-6.26288,106.9850633",
      "currentpositionaddress" : "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
      "persentbar" : "100%"
    },
    "1" : {
      "geofence" : "Naga Pekayon",
      "status" : "1",
      "kmtonextgeofence" : "80",
      "currentkm" : "0",
      "totalroute" : "6",
      "currentpositioncoord" : "-6.2494045,106.9901368",
      "currentpositionaddress" : "Jl. Jend. Ahmad Yani, RT.004/RW.001, Marga Jaya, Kec. Bekasi Bar., Kota Bks, Jawa Barat 17141",
      "persentbar" : "100%"
    },
    "2" : {
      "geofence" : "Mega Mall Bekasi",
      "status" : "1",
      "kmtonextgeofence" : "4",
      "currentkm" : "0",
      "totalroute" : "6",
      "currentpositioncoord" : "-6.2494045,106.9901368",
      "currentpositionaddress" : "Bks Cyber Park Kayuringin Jaya Kec. Bekasi Sel. Kota Bks Jawa Barat",
      "persentbar" : "100%"
    },
    "3" : {
      "geofence" : "Bekasi Cyber Park",
      "status" : "1",
      "kmtonextgeofence" : "2",
      "currentkm" : "0",
      "totalroute" : "6",
      "currentpositioncoord" : "-6.2259813,106.9988616",
      "currentpositionaddress" : "Jl. Bulevar Ahmad Yani, RT.006/RW.002, Marga Mulya, Kec. Bekasi Utara, Kota Bks, Jawa Barat 17142",
      "persentbar" : "100%"
    },
    "4" : {
      "geofence" : "Summarecon Mall Bekasi",
      "status" : "1",
      "kmtonextgeofence" : "2",
      "currentkm" : "0",
      "totalroute" : "6",
      "currentpositioncoord" : "-6.26288,106.9850633",
      "currentpositionaddress" : "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
      "persentbar" : "100%"
    }
  }],
  "2" : [{
    "0" : {
      "geofence" : "Lacak Mobil",
      "status" : "1",
      "kmtonextgeofence" : "12",
      "currentkm" : "0",
      "totalroute" : "6",
      "currentpositioncoord" : "-6.26288,106.9850633",
      "currentpositionaddress" : "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
      "persentbar" : "100%"
    },
    "1" : {
      "geofence" : "Naga Pekayon",
      "status" : "1",
      "kmtonextgeofence" : "80",
      "currentkm" : "0",
      "totalroute" : "6",
      "currentpositioncoord" : "-6.2494045,106.9901368",
      "currentpositionaddress" : "Jl. Jend. Ahmad Yani, RT.004/RW.001, Marga Jaya, Kec. Bekasi Bar., Kota Bks, Jawa Barat 17141",
      "persentbar" : "100%"
    },
    "2" : {
      "geofence" : "Mega Mall Bekasi",
      "status" : "1",
      "kmtonextgeofence" : "4",
      "currentkm" : "0",
      "totalroute" : "6",
      "currentpositioncoord" : "-6.2494045,106.9901368",
      "currentpositionaddress" : "Bks Cyber Park Kayuringin Jaya Kec. Bekasi Sel. Kota Bks Jawa Barat",
      "persentbar" : "100%"
    },
    "3" : {
      "geofence" : "Bekasi Cyber Park",
      "status" : "1",
      "kmtonextgeofence" : "2",
      "currentkm" : "0",
      "totalroute" : "6",
      "currentpositioncoord" : "-6.2259813,106.9988616",
      "currentpositionaddress" : "Jl. Bulevar Ahmad Yani, RT.006/RW.002, Marga Mulya, Kec. Bekasi Utara, Kota Bks, Jawa Barat 17142",
      "persentbar" : "100%"
    }
  }],
  "3" : [{
    "0" : {
      "geofence" : "Lacak Mobil",
      "status" : "1",
      "kmtonextgeofence" : "12",
      "currentkm" : "0",
      "totalroute" : "6",
      "currentpositioncoord" : "-6.26288,106.9850633",
      "currentpositionaddress" : "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
      "persentbar" : "100%"
    },
    "1" : {
      "geofence" : "Naga Pekayon",
      "status" : "1",
      "kmtonextgeofence" : "80",
      "currentkm" : "0",
      "totalroute" : "6",
      "currentpositioncoord" : "-6.2494045,106.9901368",
      "currentpositionaddress" : "Jl. Jend. Ahmad Yani, RT.004/RW.001, Marga Jaya, Kec. Bekasi Bar., Kota Bks, Jawa Barat 17141",
      "persentbar" : "100%"
    },
    "2" : {
      "geofence" : "Mega Mall Bekasi",
      "status" : "1",
      "kmtonextgeofence" : "4",
      "currentkm" : "0",
      "totalroute" : "6",
      "currentpositioncoord" : "-6.2494045,106.9901368",
      "currentpositionaddress" : "Bks Cyber Park Kayuringin Jaya Kec. Bekasi Sel. Kota Bks Jawa Barat",
      "persentbar" : "100%"
    }
  }],
  "4" : [{
    "0" : {
      "geofence" : "Lacak Mobil",
      "status" : "1",
      "kmtonextgeofence" : "12",
      "currentkm" : "0",
      "totalroute" : "6",
      "currentpositioncoord" : "-6.26288,106.9850633",
      "currentpositionaddress" : "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
      "persentbar" : "100%"
    },
    "1" : {
      "geofence" : "Naga Pekayon",
      "status" : "1",
      "kmtonextgeofence" : "80",
      "currentkm" : "0",
      "totalroute" : "6",
      "currentpositioncoord" : "-6.2494045,106.9901368",
      "currentpositionaddress" : "Jl. Jend. Ahmad Yani, RT.004/RW.001, Marga Jaya, Kec. Bekasi Bar., Kota Bks, Jawa Barat 17141",
      "persentbar" : "100%"
    }
  }]
};
  console.log("datadummy : ", datadummy);
    var totalrow      = 5;
    // var totalroute    = ['6', '6', '6', '6', '6'];
    var totalroute    = ['6', '3', '5', '4', '6'];
    var currentmarker = 1;
    var i             = 0;
    var loopnya       = "red";

    function getcurrent(){
      console.log("currentmarker : ", "marker"+i+currentmarker);
      if (currentmarker == totalroute[i]) {
        // clearInterval(intervalthis);
        console.log("udah sama");
        if (i == (totalrow-1)) {
          console.log("loop");
          loopnya = "red";
          i = 0;
          currentmarker = 1;
          for (var j = 0; j < totalroute[i]; j++) {
            console.log("marker when loop : ", "marker"+i+currentmarker);
            document.getElementById("marker"+i+currentmarker).setAttribute("style", "background-color: "+loopnya+" !important; cursor: pointer;");
          }
        }else {
          loopnya = "lightgreen";
          i = i + 1;
          currentmarker = 1;
          for (var j = 0; j < totalroute[i]; j++) {
            console.log("marker simultan : ", "marker"+i+(currentmarker+1));
            document.getElementById("marker"+i+(currentmarker+1)).setAttribute("style", "background-color: "+loopnya+" !important; cursor: pointer;");
          }
        }
      }else {
        currentmarker = currentmarker + 1;
        loopnya = "lightgreen";
        for (var j = 0; j < totalroute[i]; j++) {
          console.log("marker awal : ", "marker"+i+currentmarker);
          document.getElementById("marker"+i+currentmarker).setAttribute("style", "background-color: "+loopnya+" !important; cursor: pointer;");
        }
      }
    }
    var intervalthis = setInterval(getcurrent, 2000);

    function getalerttimeline(){
      // $("#totalnotif0").html("3").show();
      // $("#totalnotif2").html("2").show();
      // $("#totalnotif3").html("1").show();
      $("#totalnotif0").attr('data-badge', '3').show();
      $("#totalnotif2").attr('data-badge', '2').show();
      $("#totalnotif3").attr('data-badge', '1').show();
    }
    var intervaalert = setInterval(getalerttimeline, 5000);

    function notifonclick(id){
      var stringelement = "#totalnotif";
      var string = "";
        if (id == 0) {
          console.log("0", id);
          string += '<table class="table" style="font-size:12px;">';
          string += '<tr>';
          string += '<th>No</th>';
          string += '<th>Information</th>';
          string += '</tr>';
            string += '<tr>';
              string += '<td>1</td>';
              string += '<td>';
                string += '<p>';
                  string += 'Alert Name : Overspeed <br>';
                  string += 'Date : 05-08-2020 14:04:07 <br>';
                  string += 'Location : Jl. Raya Jatiwaringin Jatiwaringin Kec. Pondokgede Kota Bks Jawa Barat </br>';
                  string += '<a href="https://maps.google.com/?q=-6.2756953,106.9105901" target="_blank">-6.2756953,106.9105901</a>';
                string += '</p>';
              string += '</td>';
            string += '</tr>';
            string += '<tr>';
              string += '<td>2</td>';
              string += '<td>';
                string += '<p>';
                  string += 'Alert Name : Overspeed <br>';
                  string += 'Date : 05-08-2020 14:34:02 <br>';
                  string += 'Location : Jl. Raya Pekayon RT.004/RW.001, Pekayon Jaya Kec. Bekasi Sel. Kota Bks Jawa Barat </br>';
                  string += '<a href="https://maps.google.com/?q=-6.2603427,106.9855399" target="_blank">-6.2603427,106.9855399</a>';
                string += '</p>';
              string += '</td>';
            string += '</tr>';
            string += '<tr>';
              string += '<td>3</td>';
              string += '<td>';
                string += '<p>';
                  string += 'Alert Name : Overspeed <br>';
                  string += 'Date : 05-08-2020 15:08:07 <br>';
                  string += 'Location : Flyover K. H. Noer Ali Summarecon Bekasi </br>';
                  string += '<a href="https://maps.google.com/?q=-6.232619,106.9913641" target="_blank">-6.232619,106.9913641</a>';
                string += '</p>';
              string += '</td>';
            string += '</tr>';
          string += '</table>';
        }else if (id == 2) {
          console.log("2", id);
          string += '<table class="table" style="font-size:12px;">';
          string += '<tr>';
          string += '<th>No</th>';
          string += '<th>Information</th>';
          string += '</tr>';
            string += '<tr>';
              string += '<td>1</td>';
              string += '<td>';
                string += '<p>';
                  string += 'Alert Name : Overspeed <br>';
                  string += 'Date : 05-08-2020 08:04:07 <br>';
                  string += 'Location : Jl. Raya Jatiwaringin Jatiwaringin Kec. Pondokgede Kota Bks Jawa Barat </br>';
                  string += '<a href="https://maps.google.com/?q=-6.2756953,106.9105901" target="_blank">-6.2756953,106.9105901</a>';
                string += '</p>';
              string += '</td>';
            string += '</tr>';
            string += '<tr>';
              string += '<td>2</td>';
              string += '<td>';
                string += '<p>';
                  string += 'Alert Name : Overspeed <br>';
                  string += 'Date : 05-08-2020 11:34:02 <br>';
                  string += 'Location : Jl. Raya Pekayon RT.004/RW.001, Pekayon Jaya Kec. Bekasi Sel. Kota Bks Jawa Barat </br>';
                  string += '<a href="https://maps.google.com/?q=-6.2603427,106.9855399" target="_blank">-6.2603427,106.9855399</a>';
                string += '</p>';
              string += '</td>';
            string += '</tr>';
          string += '</table>';
        }else {
          console.log("3", id);
          string += '<table class="table" style="font-size:12px;">';
          string += '<tr>';
          string += '<th>No</th>';
          string += '<th>Information</th>';
          string += '</tr>';
            string += '<tr>';
              string += '<td>1</td>';
              string += '<td>';
                string += '<p>';
                  string += 'Alert Name : Overspeed <br>';
                  string += 'Date : 05-08-2020 12:04:07 <br>';
                  string += 'Location : Jl. Raya Jatiwaringin Jatiwaringin Kec. Pondokgede Kota Bks Jawa Barat </br>';
                  string += '<a href="https://maps.google.com/?q=-6.2756953,106.9105901" target="_blank">-6.2756953,106.9105901</a>';
                string += '</p>';
              string += '</td>';
            string += '</tr>';
          string += '</table>';
        }
        $("#alertbody").html(string);
        $("#modalalert").fadeIn(1000);
    }

    function closemodalalert(){
      $("#modalalert").fadeOut(1000);
    }

    function detailpopup(id){
      // alert(id);
      var string = "";
          string += '<table style="font-size:12px;">';
          string += '<tr>';
            string += '<td>B162'+id+'KRM </td>';
            string += '</tr>';
            string += '<tr>';
            string += '<td>GPS Time : 5 Agustus 2020 15:34:06</td>';
            string += '</tr>';
            string += '<tr>';
            string += '<td>Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148</td>';
            string += '</tr>';
            string += '<tr>';
            string += '<td>';
            string += '<a href="https://maps.google.com/?q=-6.26288,106.9850633" target="_blank">-6.26288,106.9850633</a>';
            string += '</td>';
            string += '</tr>';
            string += '<tr>';
            string += '<td>Speed : 0 kph</td>';
            string += '</tr>';
            string += '<tr>';
            string += '<td>Engine : ON</td>';
            string += '</tr>';
            string += '<tr>';
            string += '<td>Odometer : 491196</td>';
            string += '</tr>';
            string += '<tr>';
            string += '<td>Card No : 081112320010</td>';
            string += '</tr>';
          string += '</table>';
      $("#detailpopupbody").html(string);
      $("#modaldetailpopup").fadeIn(1000);
    }

    function closemodaldetailpopup(){
      $("#modaldetailpopup").fadeOut(1000);
    }
</script>
