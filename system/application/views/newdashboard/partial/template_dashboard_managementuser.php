<!--<!DOCTYPE html>-->
<style media="screen">
  #floatthis{
    position: absolute;
     /* background-color: #d81b60 ; */
     right:0;
     bottom: -5px;
     margin:15px;
     padding:10px;
     font-size: large;
  }

  #contactsupport{
    position: absolute;
     right:0;
     bottom: 30px;
     margin:15px;
     padding:10px;
     font-size: large;
  }

</style>
<?php
  $devicealert = $this->config->item('device_alert');
 ?>

<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta content="width=device-width, initial-scale=1" name="viewport" />
  <meta name="Transporter Lacakmobil" content="Transporter Lacakmobil" />
  <meta name="Lacakmobil" content="Lacakmobil" />
  <title><?=$this->sess->user_name;?> | Dashboard Monitoring System</title>
  <!-- google font -->
  <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet" type="text/css" />
  <!-- icons -->
  <link href="<?php echo base_url();?>assets/dashboard/assets/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
  <!--bootstrap -->
  <link href="<?php echo base_url();?>assets/dashboard/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo base_url();?>assets/dashboard/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />

  <!-- timeline -->
	<!-- <link rel="stylesheet" href="<?php echo base_url();?>assets/dashboard/assets/css/pages/timeline.css"> -->

  <link href="<?php echo base_url();?>assets/dashboard/assets/plugins/summernote/summernote.css" rel="stylesheet">
  <!-- morris chart -->
  <link href="<?php echo base_url();?>assets/dashboard/assets/plugins/morris/morris.css" rel="stylesheet" type="text/css" />
  <!-- Material Design Lite CSS -->
  <link rel="stylesheet" href="<?php echo base_url();?>assets/dashboard/assets/plugins/material/material.min.css">
  <link rel="stylesheet" href="<?php echo base_url();?>assets/dashboard/assets/css/material_style.css">
  <!-- animation -->
  <link href="<?php echo base_url();?>assets/dashboard/assets/css/pages/animate_page.css" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo base_url();?>assets/dashboard/assets/plugins/jquery-toast/dist/jquery.toast.min.css">

  <!-- inbox style -->
  <link href="<?php echo base_url();?>assets/dashboard/assets/css/pages/inbox.min.css" rel="stylesheet" type="text/css" />
  <!-- Template Styles -->
  <link href="<?php echo base_url();?>assets/dashboard/assets/css/plugins.min.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo base_url();?>assets/dashboard/assets/css/style.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo base_url();?>assets/dashboard/assets/css/mystyle.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo base_url();?>assets/dashboard/assets/css/responsive.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo base_url();?>assets/dashboard/assets/css/theme-color.css" rel="stylesheet" type="text/css" />
  <!-- Owl Carousel Assets -->
  <link href="<?php echo base_url();?>assets/dashboard/assets/plugins/owl-carousel/owl.carousel.css" rel="stylesheet">
  <link href="<?php echo base_url();?>assets/dashboard/assets/plugins/owl-carousel/owl.theme.css" rel="stylesheet">

  <!-- for form -->
  <!--select2-->
  <link href="<?php echo base_url();?>assets/dashboard/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo base_url();?>assets/dashboard/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo base_url();?>assets/dashboard/assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">

  <!-- Date Time item CSS -->
  <link rel="stylesheet" href="<?php echo base_url();?>assets/dashboard/assets/plugins/material-datetimepicker/bootstrap-material-datetimepicker.css" />
  <!-- end for form -->

	<!-- favicon -->
  <link rel="shortcut icon" href="<?=base_url();?>assets/images/favicon_lacakmobil.ico" />
</head>
<body class="page-header-fixed sidemenu-closed-hidelogo page-content-white page-md header-red white-sidebar-color logo-white page-footer-fixed">
  <div class="page-wrapper">

    <!-- start header -->
    <div class="page-header navbar navbar-fixed-top">
      <?=$header;?>
    </div>
    <!-- end header -->

    <!-- start page container -->
    <!--<div class="page-container" style="border:0px solid black; min-height: calc(100vh - 226px); position: absolute;">-->
	<div class="page-container">
      <?=$content;?>
        <!-- <div class="scroll-to-top">
          <i class="icon-arrow-up"></i>
        </div> -->
    </div>
    <!-- end page container -->

    <!-- start footer -->
    <div class="page-footer">
      <div class="page-footer-inner"> Provided by Digitech - GEMS. &copy 2022
		<!--<a href="https://www.goldenenergymines.com/" target="_blank" class="makerCss">PT. Borneo Indobara</a>-->
		<a href="https://www.goldenenergymines.com/" target="_blank" class="makerCss">PT. Borneo Indobara </a> 
      </div>

      <div id="contactsupport" style="display:none;">
        <div class="col-sm-12">
          <div class="card card-topline-green">
            <div class="card-head">
                <header>Contact Support</header>
            </div>
            <div class="card-body">
              <p style="font-size:10px;">Click salah satu tim kami di bawah ini untuk chat di Whatsapp</p>

              <a href="https://api.whatsapp.com/send?phone=+6281119143655&text=Halo.%20Ada%20yang%20ingin%20saya%20tanyakan." class="btn btn-success btn-circle" style="margin-bottom:2%; text-transform: capitalize;" target="_blank">
                <span class="fa fa-whatsapp fa-lg"></span>
                Monitoring
              </a> <br>

              <!-- <a href="https://api.whatsapp.com/send?phone=+628111178162&text=Halo.%20Ada%20yang%20ingin%20Saya%20tanyakan." class="btn btn-success btn-circle" style="margin-bottom:2%; text-transform: capitalize;" target="_blank">
                <span class="fa fa-whatsapp fa-lg"></span>
                Monitoring 2
              </a> <br> -->
            </div>
          </div>
        </div>
      </div>

      <button id="floatthis" href="#" class="btn btn-success btn-circle" title="Contact Support" onclick="showCS();">
        <span class="fa fa-whatsapp fa-lg"></span>
      </button>
    </div>
    <!-- end footer -->
  </div>
  </body>
</html>
  <script src="<?php echo base_url();?>assets/dashboard/assets/plugins/jquery/jquery.min.js"></script>
  <!--<script src="<?php echo base_url();?>assets/dashboard/assets/plugins/popper/popper.min.js" ></script>-->
  <script src="<?php echo base_url();?>assets/dashboard/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
  <!-- <script src="<?php echo base_url();?>assets/js/pages/timeline/timeline.js"></script> -->
  <script src="<?php echo base_url();?>assets/dashboard/assets/plugins/jquery-blockui/jquery.blockui.min.js"></script>
  <script src="<?php echo base_url();?>assets/dashboard/assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js"></script>
  <script src="<?php echo base_url();?>assets/dashboard/assets/plugins/moment/moment.min.js"></script>
  <script src="<?php echo base_url();?>assets/dashboard/assets/plugins/counterup/jquery.waypoints.min.js"></script>
  <script src="<?php echo base_url();?>assets/dashboard/assets/plugins/counterup/jquery.counterup.min.js"></script>
  <!-- owl carousel -->
  <script src="<?php echo base_url();?>assets/dashboard/assets/plugins/owl-carousel/owl.carousel.js"></script>
  <script src="<?php echo base_url();?>assets/dashboard/assets/js/pages/owl-carousel/owl_data.js"></script>
  <!-- Common js-->
  <script src="<?php echo base_url();?>assets/dashboard/assets/js/app.js"></script>
  <script src="<?php echo base_url();?>assets/dashboard/assets/js/myjs.js"></script>
  <script src="<?php echo base_url();?>assets/dashboard/assets/js/layout.js"></script>
  <script src="<?php echo base_url();?>assets/dashboard/assets/js/theme-color.js"></script>
  <!-- Material -->
  <!--<script src="<?php echo base_url();?>assets/dashboard/assets/plugins/material/material.min.js"></script>-->
  <!-- animation -->
  <script src="<?php echo base_url();?>assets/dashboard/assets/js/pages/ui/animations.js"></script>
  <script src="<?php echo base_url();?>assets/dashboard/assets/plugins/jquery-toast/dist/jquery.toast.min.js" ></script>
  <script src="<?php echo base_url();?>assets/dashboard/assets/plugins/jquery-toast/dist/toast.js" ></script>
  <!-- sparkline -->
  <script src="<?php echo base_url();?>assets/dashboard/assets/plugins/sparkline/jquery.sparkline.min.js"></script>
  <script src="<?php echo base_url();?>assets/dashboard/assets/js/pages/sparkline/sparkline-data.js"></script>
  <!-- summernote -->
  <script src="<?php echo base_url();?>assets/dashboard/assets/plugins/summernote/summernote.min.js"></script>
  <script src="<?php echo base_url();?>assets/dashboard/assets/js/pages/summernote/summernote-data.js"></script>

  <!-- timeline -->
  <!-- <script src="<?php echo base_url();?>assets/dashboard/assets/js/pages/timeline/timeline.js"></script> -->


  <!-- echart -->
  <!--<script src="<?php echo base_url();?>assets/dashboard/assets/plugins/echarts/echarts.js" ></script>
    <script src="<?php echo base_url();?>assets/dashboard/assets/js/pages/chart/echart/echart-data.js" ></script>-->

  <!--Chart JS-->
  <script src="<?php echo base_url();?>assets/dashboard/assets/plugins/chart-js/Chart.bundle.js"></script>
  <script src="<?php echo base_url();?>assets/dashboard/assets/plugins/chart-js/utils.js"></script>
  <!--<script src="<?php echo base_url();?>assets/dashboard/assets/js/pages/chart/chartjs/chartjs-data.js" ></script>-->

  <!-- data tables -->
  <script src="<?php echo base_url();?>assets/dashboard/assets/plugins/datatables/jquery.dataTables.min.js"></script>
  <script src="<?php echo base_url();?>assets/dashboard/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.js"></script>
  <script src="<?php echo base_url();?>assets/dashboard/assets/js/pages/table/table_data.js"></script>

  <!-- for form -->
  <script src="<?php echo base_url();?>assets/dashboard/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
  <script src="<?php echo base_url();?>assets/dashboard/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker-init.js"></script>
  <!--select2-->
  <script src="<?php echo base_url();?>assets/dashboard/assets/plugins/select2/js/select2.js"></script>
  <script src="<?php echo base_url();?>assets/dashboard/assets/js/pages/select2/select2-init.js"></script>
  <!-- floating select -->
  <script src="<?php echo base_url();?>assets/dashboard/assets/js/pages/material_select/getmdl-select.js"></script>
  <script src="<?php echo base_url();?>assets/dashboard/assets/plugins/material-datetimepicker/moment-with-locales.min.js"></script>
  <script src="<?php echo base_url();?>assets/dashboard/assets/plugins/material-datetimepicker/bootstrap-material-datetimepicker.js"></script>
  <script src="<?php echo base_url();?>assets/dashboard/assets/plugins/material-datetimepicker/datetimepicker.js"></script>

	<script>
	  $(document).ready(function() { checkbrowser();});
	</script>

  <script type="text/javascript">
  function listEngine(status){
    if (status == 0) {
      // OFF
      var statusfix = "Engine Off";
      var urlfix = 'maps/getlistengineoff';
    }else {
      var statusfix = "Engine On";
      var urlfix = 'maps/getlistengineon';
    }
    $.post("<?php echo base_url() ?>"+urlfix, {}, function(response){
      console.log(statusfix + " List : ", response);
      var datafix           = response.data;
      var totalrow          = datafix.length;
      var datacontractor    = response.jumlah_contractor;
      var datacontractorfix = Object.entries(datacontractor);

      var htmlcontractor = "";
      htmlcontractor += '<table>';
        htmlcontractor += '<tr>';
          for (var i = 0; i < datacontractorfix.length; i++) {
            if (datacontractorfix[i][1] != 0) {
              // console.log("datacontractorfix : ", datacontractorfix[i][0]);
              htmlcontractor += '<td><b>'+datacontractorfix[i][0]+'</b><td>';
              htmlcontractor += '<td>:<td>';
              htmlcontractor += '<td><b>'+datacontractorfix[i][1]+'</b><td>';
            }
          }
          htmlcontractor += '<tr>';
        htmlcontractor += '</table>';
        $("#contractorinlocation").html(htmlcontractor);

      var htmlpool = "";
        if (datafix.length > 0) {
          var lastcheckpoolws = "Last Check : "+response.data[0].auto_last_update + " WITA";
          $("#modalStateTitle").html(statusfix + " (" + totalrow + ")");
          $("#lastcheckpoolws").html(lastcheckpoolws);
          htmlpool += '<table class="table table-striped">';
            htmlpool += '<thead>';
              htmlpool += '<tr>';
                htmlpool += '<th>No</th>';
                htmlpool += '<th>Vehicle</th>';
                htmlpool += '<th align="center">Engine</th>';
                htmlpool += '<th align="center">Speed (Kph)</th>';
                htmlpool += '<th>Coord</th>';
              htmlpool += '</tr>';
            htmlpool += '</thead>';
          for (var i = 0; i < datafix.length; i++) {
              htmlpool += '<tr>';
                htmlpool += '<td style="font-size:12px;color:black"><span style="color:black;">'+(i+1)+'</span>';
                htmlpool += '<td style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].vehicle_no+ " " +datafix[i].vehicle_name+'</span>';
                htmlpool += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].auto_last_engine+'</span>';
                htmlpool += '<td align="center" style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].auto_last_speed+'</span>';
                htmlpool += '<td style="font-size:12px;color:black"><span style="color:black;">'+datafix[i].auto_last_lat+ "," +datafix[i].auto_last_long+'</span>';
              htmlpool += '</tr>';
          }
          htmlpool += '</table>';
          $("#modalStateContent").html(htmlpool);
          modalPoolFromMasterData('modalState');
        }else {
          alert("Data Tidak Ada");
        }
      },"json");
  }
  // FLOATING BUTTON
  function showCS(){
    $("#contactsupport").fadeToggle('fast');
  }

  var devalertarray = {"dt" : "Cut Power Alert", "BO010" : "Cut Power Alert", "BO012" : "Panic Button (SOS)"};
  window.onload = function() {
    // $("#livestream").html("das");
    // setInterval(getalertalways, 3000);
    // FOR GET THE NOTIFICATION
    // var url = '<?php echo base_url()?>vehicles/getfornotif';
    // $.post(url, {}, function(response) {
    //   console.log("response header : ", response);
    //   // FOR KIR
    //   var totalkirexpdate = response.total_kirexpdate;
    //   if (totalkirexpdate > 0) {
    //     $("#tablekir").show();
    //     for (var i = 0; i < totalkirexpdate; i++) {
    //       var stnk = '<tr>';
    //       stnk += '<td>' + (i+1) + '. </td>';
    //       stnk += '<td> ' +response.data_notifkir[i].vehicle_no + ' <br> '+response.data_notifkir[i].vehicle_name+'</td>';
    //       stnk += '<td> ' +response.data_notifkir[i].vehicle_kirexpdate + '</td>';
    //       stnk += '</tr>';
    //       $("#kirexpdate").before(stnk);
    //     }
    //   }else {
    //     $("#tablekir").hide();
    //         var stnk = '<tr>';
    //         stnk += '<td>Data Not Available </td>';
    //         stnk += '<td>Data Not Available </td>';
    //         stnk += '<td>Data Not Available </td>';
    //         stnk += '</tr>';
    //         $("#kirexpdate").before(stnk);
    //   }
    //   // FOR STNK
    //   var totalstnkexp = response.total_stnkexpdate;
    //   if (totalstnkexp > 0) {
    //     $("#tablestnk").show();
    //     for (var i = 0; i < totalstnkexp; i++) {
    //       var stnk = '<tr>';
    //       stnk += '<td>' + (i+1) + '. </td>';
    //       stnk += '<td> ' +response.data_notifstnk[i].vehicle_no + ' <br> '+response.data_notifstnk[i].vehicle_name+'</td>';
    //       stnk += '<td> ' +response.data_notifstnk[i].vehicle_stnkexpdate + '</td>';
    //       stnk += '</tr>';
    //       $("#stnkexpdate").before(stnk);
    //     }
    //   }else {
    //     $("#tablestnk").hide();
    //         var stnk = '<tr>';
    //         stnk += '<td>Data Not Available </td>';
    //         stnk += '<td>Data Not Available </td>';
    //         stnk += '<td>Data Not Available </td>';
    //         stnk += '</tr>';
    //         $("#stnkexpdate").before(stnk);
    //   }
    //
    //   // FOR SERVICE PERKM
    //   var total_notifserviceperkm = response.total_notifserviceperkm;
    //   if (total_notifserviceperkm > 0) {
    //     $("#tableserviceperkm").show();
    //     for (var i = 0; i < total_notifserviceperkm; i++) {
    //       var serviceperkm = '<tr>';
    //       serviceperkm += '<td>' + (i+1) + '. </td>';
    //       serviceperkm += '<td> ' +response.data_notifserviceperkm[i].vehicle_no + ' <br> '+response.data_notifserviceperkm[i].vehicle_name+'</td>';
    //       serviceperkm += '<td> ' +response.data_notifserviceperkm[i].lastodometerfromgps + '</td>';
    //       serviceperkm += '<td> ' +response.data_notifserviceperkm[i].odometerforservice + '</td>';
    //       serviceperkm += '</tr>';
    //       $("#serviceperkm").before(serviceperkm);
    //     }
    //   }else {
    //     $("#tableserviceperkm").hide();
    //         var serviceperkm = '<tr>';
    //         serviceperkm += '<td>Data Not Available </td>';
    //         serviceperkm += '<td>Data Not Available </td>';
    //         serviceperkm += '<td>Data Not Available </td>';
    //         serviceperkm += '</tr>';
    //         $("#serviceperkm").before(serviceperkm);
    //   }
    //
    //   // FOR SERVICE PERMONTH
    //   var total_notifservicepermonth = response.total_notifservicepermonth;
    //   if (total_notifservicepermonth > 0) {
    //     $("#tableservicepermonth").show();
    //     for (var i = 0; i < total_notifservicepermonth; i++) {
    //       var servicepermonth = '<tr>';
    //       servicepermonth += '<td>' + (i+1) + '. </td>';
    //       servicepermonth += '<td> ' +response.data_notifservicepermonth[i].vehicle_no + ' <br> '+response.data_notifservicepermonth[i].vehicle_name+'</td>';
    //       servicepermonth += '<td> ' +response.data_notifservicepermonth[i].last_service + '</td>';
    //       servicepermonth += '<td> ' +response.data_notifservicepermonth[i].next_service + '</td>';
    //       servicepermonth += '</tr>';
    //       $("#servicepermonth").before(servicepermonth);
    //     }
    //   }else {
    //     $("#tableservicepermonth").hide();
    //         var servicepermonth = '<tr>';
    //         servicepermonth += '<td>Data Not Available </td>';
    //         servicepermonth += '<td>Data Not Available </td>';
    //         servicepermonth += '<td>Data Not Available </td>';
    //         servicepermonth += '</tr>';
    //         $("#servicepermonth").before(servicepermonth);
    //   }
    //
    //
    //   var totalallnotif = totalstnkexp + totalkirexpdate + total_notifserviceperkm + total_notifservicepermonth;
    //     console.log("total data Maintenance : ", totalallnotif);
    //     if (totalallnotif > 0) {
    //       $("#totalnotifmaintenance").html(totalallnotif);
    //       $("#totalnotifmaintenance").show();
    //     }
    // }, 'json');

    function getalertalways() {
        $.post("<?php echo base_url()?>devicealert/getallalert", {}, function(response){
          // console.log("response ready");
          // $("#devicenotif").html("");// AKTIFIN KALO MAU BUTTON DEVICE ALERT DIMUNCULIN
          var code          = response.code;
          var data          = response.data;
          console.log("data : ", data);
          var totaldata = response.data.length;
            if (response.data == "empty") {
              $("#totalnotifdevicealert").hide();
            }else {
              if (totaldata > 0) {
                $("#totalnotifdevicealert").html(totaldata);
                $("#totalnotifdevicealert").show();
              }
            }

          var htmlnotif = "";
            if (code == 200) {
              htmlnotif += '<li>';
              htmlnotif += '<ul class="dropdown-menu-list small-slimscroll-style" data-handle-color="#637283">';
                for (var i = 0; i < data.length; i++) {
                  var alert_name    = data[i].vehicle_alert;
                  var devicealertfix = alert_name in devalertarray ? devalertarray[alert_name] : alert_name;
                  // console.log("alert_name : ", alert_name);
                  // console.log("databasealert : ", devicealertfix);
                  // console.log("devalertarray : ", devalertarray[alert_name]);
                   htmlnotif += '<li>'+
                                  '<a href="<?php echo base_url()?>devicealert/listalert">'+
                                    '<span class="subject">'+
                                    '<span>  </span>'+
                                    '<span class="time"> '+ data[i].vehicle_alert_datetime +' </span>'+
                                    '</span>'+
                                    (1 + i) + '. <span style="font-size: 12px;">'+ data[i].vehicle_no +'</span> <span class="label label-sm label-warning"> '+ devicealertfix +' </span>'+
                                  '</a>'+
                                '</li>';
                  }
                  htmlnotif += '</ul>';
                  htmlnotif += '<div class="dropdown-menu-footer text-right">';
                    htmlnotif += '<a href="<?php echo base_url()?>devicealert/listalert" target="_blank" class="btn btn-success btn-sm" title="Detail Alert"><span class="fa fa-external-link"></span></a>';
                    htmlnotif += '<button onclick="btnClearNotif();" class="btn btn-danger btn-sm" title="Clear Notification"><span class="fa fa-trash"></span></button>';
                  htmlnotif += '</div>';
                htmlnotif += '</li>';
            }else {
              htmlnotif += '<li>'+
                            '<ul class="dropdown-menu-list small-slimscroll-style" data-handle-color="#637283">'+
                              '<li>'+
                                '<a href="#">'+
                                  '<span class="subject">'+
                                  '<span class="from"> </span>'+
                                  '<span class="time"> </span>'+
                                  '</span>'+
                                  '<span class="message">no data Alert </span>'+
                                '</a>'+
                              '</li>'+
                            '</ul>'+
                          '</li>';
            }
            // $("#devicenotif").html(htmlnotif);// AKTIFIN KALO MAU BUTTON DEVICE ALERT DIMUNCULIN
        }, 'json');
    }
    setInterval(getalertalways, 30000);

    // UNTUK KEBUTUHAN KALIMANTAN START
    // LOGIN
    // var sessionkalimantan;
    // $.post('<?php echo base_url() ?>safetyhandle/apilogin', { url: "http://47.91.108.9:8080/StandardApiAction_login.action?account=IND.lacakmobil&password=000000" }, function(response) {
    //   var obj = JSON.parse(response);
    //   sessionkalimantan = obj.jsession;
    //   localStorage.setItem("sessionkalimantan", sessionkalimantan);
    //   console.log("obj : ", obj);
    //   console.log("sessionkalimantan : ", localStorage.getItem("sessionkalimantan"));
    // }, "json");


    // sessionkalimantan = localStorage.getItem("sessionkalimantan");
    // console.log("sessionkalimantan : ", sessionkalimantan);
    // GET VEHICLE DATA BY SESSION
    // var vehicledata;
    // $.post('<?php echo base_url() ?>safetyhandle/apigetvehicledata', { url: "http://47.91.108.9:8080/StandardApiAction_queryUserVehicle.action?jsession="+sessionkalimantan }, function(response) {
    //   var obj = JSON.parse(response);
    //   vehicledata = obj.vehicles[1].id;
    //   // console.log("obj : ", obj);
    // }, "json");

    // GET JAVASCRIPT LIVE VIDEO
    // $.post('<?php echo base_url() ?>safetyhandle/vehiclelive', { url: "http://47.91.108.9:8080/808gps/open/player/RealPlayVideo.html?account=IND.LacakMobil&password=000000&PlateNum=020200360002&lang=en"}, function(response) {
    //   // var obj = JSON.parse(response);
    //   console.log("response : ", response);
    //   $("#cmsv6flash").html(response.html);
    // }, "json");
    // UNTUK KEBUTUHAN KALIMANTAN END
  }

  var isInitFinished = false;//Are video plug loading complete
//Init Video Plug
function initPlayerExample() {
      //Video plug-in init param
      var params = {
        allowFullscreen: "true",
        allowScriptAccess: "always",
        bgcolor: "#FFFFFF",
        wmode: "transparent"
        };
      //Init flash
      swfobject.embedSWF("player.swf", "cmsv6flash", 400, 400, "11.0.0", null, null, params, null);
      initFlash();
}
//Are video plug loading complete
function initFlash() {
      if (swfobject.getObjectById("cmsv6flash") == null ||
            typeof swfobject.getObjectById("cmsv6flash").setWindowNum == "undefined" ) {
            setTimeout(initFlash, 50);
      } else {
            //Setting the language video widget
            swfobject.getObjectById("cmsv6flash").setLanguage("cn.xml");
            //First of all windows created
            swfobject.getObjectById("cmsv6flash").setWindowNum(36);
            //Re-configure the current number of windows
            swfobject.getObjectById("cmsv6flash").setWindowNum(4);
            //Set the video plug-in server
            swfobject.getObjectById("cmsv6flash").setServerInfo("47.91.108.9", "6605");
            isInitFinished = true;
    }
}

  function btnClearNotif(){
    console.log("onclick ok");
    $.post("<?php echo base_url()?>devicealert/clearnotif", {}, function(response){
      console.log("response : ", response);
      var code = response.code;
      if (code == 200) {
        alert("Alert Successfully Cleared");
        // $("#notifnya").html("Alert Successfully Cleared");
        // $("#notifnya").fadeIn(1000);
        // $("#notifnya").fadeOut(5000);
      }else {
        alert("Alert Failed Cleared");
        // $("#notifnya").html("Alert Failed Cleared");
        // $("#notifnya").fadeIn(1000);
        // $("#notifnya").fadeOut(5000);
      }
    }, 'json');
  }

    function display_c() {
      var refresh = 1000; // Refresh rate in milli seconds
      mytime = setTimeout('display_ct()', refresh)
    }

    /*function display_ct() {
    	var x = new Date()
    	var x1=x.toUTCString();// changing the display to UTC string
    	document.getElementById('ct').innerHTML = x1;
    	tt=display_c();
    }*/
    function display_ct() {
      var x = new Date();
      // date part ///
      var month = x.getMonth() + 1;
      var day = x.getDate();
      var year = x.getFullYear();
      if (month < 10) {
        month = '0' + month;
      }
      if (day < 10) {
        day = '0' + day;
      }
      var x3 = month + '-' + day + '-' + year;

      // time part //
      var hour = x.getHours();
      var minute = x.getMinutes();
      var second = x.getSeconds();
      if (hour < 10) {
        hour = '0' + hour;
      }
      if (minute < 10) {
        minute = '0' + minute;
      }
      if (second < 10) {
        second = '0' + second;
      }
      var x3 = x3 + ' ' + hour + ':' + minute + ':' + second;
      display_c();
    }

    function monitoring_sidebar() {
      jQuery("#sidebar_monitoring").show();
      jQuery("#sidebar_config").hide();
      jQuery("#sidebar_report").hide();
      jQuery("#sidebar_billing").hide();
    }

    function config_sidebar() {
      jQuery("#sidebar_monitoring").hide();
      jQuery("#sidebar_config").show();
      jQuery("#sidebar_report").hide();
      jQuery("#sidebar_billing").hide();
    }

    function report_sidebar() {
      jQuery("#sidebar_monitoring").hide();
      jQuery("#sidebar_config").hide();
      jQuery("#sidebar_report").show();
      jQuery("#sidebar_billing").hide();
    }

    function billing_sidebar() {
      jQuery("#sidebar_monitoring").hide();
      jQuery("#sidebar_config").hide();
      jQuery("#sidebar_report").hide();
      jQuery("#sidebar_billing").show();
    }

    function showmodalfivereport(){
      $("#vehiclelistfivereport").html("");
      $.post("<?php echo base_url()?>maps/getallvehicle", {}, function(response){
        // console.log("response getallvehicle : ", response);
        var JSONString = JSON.parse(response);
        // console.log("response getallvehicle 2: ", JSONString);
        var htmlvehiclelistfivereport = "";
        var data          = JSONString.data;
        // console.log("vdevicearray : ", vdevicearray);
        for (var i = 0; i < data.length; i++) {
          var vdevice      = data[i].vehicle_device;
          var vdevicearray = vdevice.split("@");
          htmlvehiclelistfivereport += '<tr id="rowid_'+data[i].vehicle_device+'">'+
                                  '<td><a name="'+(i+1)+'"></a>'+
                                  '<td style="font-size:12px;">'+(i+1)+'</td>'+
                                  '<td style="font-size:12px;">'+data[i].vehicle_no + " - " + data[i].vehicle_name+'</td>'+
                                  '<td style="font-size:12px;">'+
                                  '<a title="History" href="<?php echo base_url()?>triphistory/history/'+vdevicearray[0]+"/"+vdevicearray[1]+'" target="_blank" class="btn btn-primary btn-sm"><span class="fa fa-car"></span></a>' + '<a title="Workhour" href="<?php echo base_url()?>triphistory/workhour/'+vdevicearray[0]+"/"+vdevicearray[1]+'" target="_blank" class="btn btn-success btn-sm"><span class="fa fa-clock-o"></span></a>' + '<a title="Overspeed" href="<?php echo base_url()?>triphistory/overspeed/'+vdevicearray[0]+"/"+vdevicearray[1]+'" target="_blank" class="btn btn-info btn-sm"><span class="fa fa-dashboard"></span></a>'+ '<a title="Geofence" href="<?php echo base_url()?>triphistory/geofence/'+vdevicearray[0]+"/"+vdevicearray[1]+'" target="_blank" class="btn btn-warning btn-sm"><span class="fa fa-globe"></span></a>' + '<a title="Parking Time" href="<?php echo base_url()?>triphistory/parkingtime/'+vdevicearray[0]+"/"+vdevicearray[1]+'" target="_blank" class="btn btn-danger btn-sm"><span class="fa fa-stop"></span></a>' +
                                  '</td>'+
                               '</tr>';
        }
        $("#vehiclelistfivereport").html(htmlvehiclelistfivereport);
        $("#modalfivereport").show();
      });
    }

    function closemodalfivereport(){
      $("#modalfivereport").hide();
    }

    function btnNotif() {
      $("#ultooltip").toggle('slow');
    }

    function showhidelogo(){
      var thislogovalue = $("#thislogovalue").val();
        if (thislogovalue == 0) {
          $("#thislogo").hide();
          $("#thislogo2").show();
          $("#thislogovalue").val(1);
        }else {
          $("#thislogo").show();
          $("#thislogo2").hide();
          $("#thislogovalue").val(0);
        }
    }
</script>

  <!-- end for form ->
