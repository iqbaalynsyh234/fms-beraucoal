<script src="<?php echo base_url()?>assets/js/assetsbaru/jquery.min.js"></script>
<script src="<?php echo base_url()?>assets/js/assetsbaru/jquery-ui.min.js"></script>
<script src="<?php echo base_url()?>assets/js/assetsbaru/chosen.jquery.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url()?>assets/js/assetsbaru/chosen.min.css">
<style>
     /* Set the size of the div element that contains the map */
    #mapview {
      height: 400px;  /* The height is 400 pixels */
      width: 100%;  /* The width is the width of the web page */
     }

     #description {
      font-family: Roboto;
      font-size: 15px;
      font-weight: 300;
    }

    #infowindow-content .title {
      font-weight: bold;
    }

    #infowindow-content {
      display: none;
    }

    #mapview #infowindow-content {
      display: inline;
    }

    .pac-card {
      margin: 10px 10px 0 0;
      border-radius: 2px 0 0 2px;
      box-sizing: border-box;
      -moz-box-sizing: border-box;
      outline: none;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
      background-color: #fff;
      font-family: Roboto;
    }

    #pac-container {
      padding-bottom: 12px;
      margin-right: 12px;
    }

    .pac-controls {
      display: inline-block;
      padding: 5px 11px;
    }

    .pac-controls label {
      font-family: Roboto;
      font-size: 13px;
      font-weight: 300;
    }

    #pac-input {
      background-color: #fff;
      font-family: Roboto;
      font-size: 15px;
      font-weight: 300;
      margin-left: 12px;
      padding: 0 11px 0 13px;
      text-overflow: ellipsis;
      width: 400px;
    }

    #pac-input:focus {
      border-color: #4d90fe;
    }

    #title {
      color: #fff;
      background-color: #4d90fe;
      font-size: 25px;
      font-weight: 500;
      padding: 6px 12px;
    }

    /* MODAL STYLE */
    div#modalDeleteGardu {
      margin-top: 5%;
      margin-left: 45%;
      max-height: 300px;
      max-width: 400px;
      position: absolute;
      background-color: #f1f1f1;
      text-align: left;
      border: 1px solid #d3d3d3;
    }

    #formcreateticket{
      visibility: hidden;
    }

    #error_msg{
      color: red;
    }

    div#modalchangestatus {
      margin-top: 5%;
      margin-left: 45%;
      max-height: 300px;
      max-width: 400px;
      position: absolute;
      background-color: #f1f1f1;
      text-align: left;
      border: 1px solid #d3d3d3;
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
      <div class="col-md-12" id="formtablegardumaster">
          <div class="card-box">
            <!-- <div class="card-header">
              <button type="button" class="btn btn-success btn-xs" onclick="showaddpool()">
                <span class="fa fa-plus"></span>
              </button>
            </div> -->
            <div class="card-body">
                <table id="example1" class="table table-striped" class="display" class="full-width" style="width: 100%; font-size: 14px;">
                    <thead>
                        <tr>
                            <th>
                              <button type="button" class="btn btn-success btn-xs" onclick="showcreateticketing()" title="Create Ticket">
                                <span class="fa fa-plus"></span>
                              </button>No
                            </th>
                            <th>Ticketing Number</th>
                            <th>Destination Info</th>
                            <th>Vehicle</th>
                            <th>Technician</th>
                            <th>Status</th>
                            <th>Option</th>
                        </tr>
                    </thead>
                    <tbody>
                      <?php $no = 1; foreach ($dataticket as $rowticket) {?>
                        <tr>
                          <td><?php echo $no; ?></td>
                          <td><?php echo $rowticket['ticket_name_number'] ?></td>
                          <?php
                            $type = $rowticket['ticket_type'];
                              if ($type == 0) {
                                $type = "Customer";
                              }else {
                                $type = "Substation";
                              }
                           ?>
                           <td>
                             Type : <?php echo $type; ?> <br>
                             Destination : <br> <?php echo $rowticket['ticket_customer_substation_name'] ?> <br>
                             Distance :  <?php echo $rowticket['ticket_dist_to_destination'].' Km' ?> <br>
                             Address : <a href="https://maps.google.com/?q=<?php echo $rowticket['ticket_customer_substation_lat'].','.$rowticket['ticket_customer_substation_lng'] ?>" target="_blank"> <?php echo $rowticket['ticket_customer_substation_address'] ?></a>
                         </td>
                           <td><?php echo $rowticket['ticket_vehicle_no'].' - '.$rowticket['ticket_vehicle_name'] ?></td>
                           <td><?php echo $rowticket['ticket_technician_name'] ?></td>
                           <?php
                             $ticket_status = $rowticket['ticket_status'];
                               if ($ticket_status == 0) {
                                 $ticket_status = "Scheduled";?>
                                 <td>
                                   <button type="button" name="button" class="btn btn-warning"><?php echo $ticket_status; ?></button>
                                 </td>
                               <?php }elseif ($ticket_status == 1){
                                 $ticket_status = "On Duty";?>
                                 <td>
                                   <button type="button" name="button" class="btn btn-info"><?php echo $ticket_status; ?></button>
                                 </td>
                               <?php }elseif ($ticket_status == 2) {
                                 $ticket_status = "On Process";?>
                                 <td>
                                   <button type="button" name="button" class="btn btn-primary"><?php echo $ticket_status; ?></button>
                                 </td>
                               <?php }else {
                                 $ticket_status = "Completed";?>
                                 <td>
                                   <button type="button" name="button" class="btn btn-success"><?php echo $ticket_status; ?></button>
                                 </td>
                               <?php } ?>

                           <td>
                             <button type="button" class="btn btn-primary" name="button" title="Change Status Manually" onclick="changestatus('<?php echo $rowticket['ticket_id'];?>','<?php echo $ticket_status; ?>')">
                               <span class="fa fa-list"></span>
                             </button>
                           </td>
                        </tr>
                      <?php $no++; } ?>
                    </tbody>
                </table>
            </div>
          </div>
      </div>

       <div class="col-md-12" id="formcreateticket">
        <div class="card-box">
          <div class="card-header">
            Create Ticket
           <div id="error_msg" style="display: none;"></div>
          </div>

          <div class="card-body">
            <div class="form-horizontal" method="post" enctype="multipart/form-data">
              <table class="table table-striped">
                <tr>
                  <td>Ticket Name</td>
                  <td>
                    <input type="text" class="form-control" id="ticket_name" name="ticket_name" placeholder="Ticket Name">
                  </td>
                </tr>

                <tr>
                  <td>Due Date</td>
                  <td>
                    <div class="input-group date form_date col-md-4" data-date="" data-date-format="dd-mm-yyyy" data-link-format="yyyy-mm-dd">
                        <input class="form-control" size="5" type="text" readonly name="duedate" id="duedate" value="<?=date('d-m-Y')?>">
                        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                    </div>
                  </td>
                </tr>

                <tr>
                  <td>Type</td>
                  <td>
                    <input type="radio" name="ticketingtype" id="ticketingtype0" value="0"> Customer
                    <input type="radio" name="ticketingtype" id="ticketingtype1" value="1"> Substation
                  </td>
                </tr>

                <tr id="ticketingtypesearchcustomer">
                  <td>Customer</td>
                  <td>
                    <div class="col-sm-4">
                        <select class="form-control" name="customer_name" id="customer_name">
                          <option value="">--Choose Customer--</option>
                          <?php foreach ($datacustomer as $rowcustomer) {?>
                            <option value="<?php echo $rowcustomer['webtracking_tms_customer_id']; ?>"><?php echo $rowcustomer['webtracking_tms_customer_name']; ?></option>
                          <?php } ?>
                        </select>
                    </div>
                  </td>
                </tr>

                <tr id="ticketingtypesearchsubstation">
                  <td>Substation</td>
                  <td>
                    <div class="col-sm-4">
                      <select class="form-control" name="substation_name" id="substation_name">
                        <option value="">--Choose Substation--</option>
                        <?php foreach ($datagardu as $rowgardu) {?>
                          <option value="<?php echo $rowgardu['gardu_id']; ?>"><?php echo $rowgardu['gardu_name']; ?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </td>
                </tr>

                <tr>
                  <td>Ticket Note</td>
                  <td>
                    <textarea name="ticket_keterangan" id="ticket_keterangan" rows="8" cols="80"></textarea>
                  </td>
                </tr>

                <tr>
                  <td></td>
                  <td>
                    <button type="button" class="btn btn-default" onclick="btncancel()">Cancel</button>
                    <button type="button" id="btnsearch" class="btn btn-success" onclick="btnSearchTech();">Search Tenhnician</button>
                    <img src="<?php echo base_url()?>assets/images/ajax-loader.gif" id="loader" style="display: none;">
                  </td>
                </tr>
              </table>

              <div id="map_canvas" style="height:350px; width:100%; margin-left: 0%; margin-top: 0%; position:relative; "></div>

              <table class="table table-striped table-hover" id="tablesearchtechnician" style="display : none;">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Technisian</th>
                    <th>Km to Destination</th>
                    <th>Option</th>
                    <th>Confirm</th>
                  </tr>
                </thead>
                <tbody id="technicianresponse">

                </tbody>
              </table>
          </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<div id="modalchangestatus" style="display: none;">
  <div id="mydivheader"></div>
  <div class="row" >
    <div class="col-md-12">
        <div class="card card-topline-yellow">
            <div class="card-head">
                <header>Change Status</header>
                <div class="tools">
                    <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
                  <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                  <button type="button" class="btn btn-danger" name="button" onclick="closemodallistchangestatus();">X</button>
                </div>
            </div>
            <div class="card-body">
                <form class="form-horizontal" action="<?php echo base_url()?>tms/changestatus" method="post" enctype="multipart/form-data">
                  <input type="hidden" name="ticketid" id="ticketid">
                  <table>
                    <tr>
                      <td>Current Status</td>
                      <td>
                        <input type="text" id="current_status" class="form-control" readonly>
                      </td>
                    </tr>

                    <tr>
                      <td>Status</td>
                      <td>
                        <select class="form-control" name="statusnya" id="statusnya">
                          <option value="">--Choose Status--</option>
                          <option value="0">Scheduled</option>
                          <option value="1">On Duty</option>
                          <option value="2">On Process</option>
                          <option value="3">Completed</option>
                        </select>
                      </td>
                    </tr>
                  </table>
                  <br>
                  <div class="text-right">
                    <button type="button" name="button" onclick="btnCloseModal2();">Cancel</button>
                    <button type="submit" name="button" class="btn btn-danger">Change</button>
                  </div>
                </form>
            </div>
        </div>
    </div>
  </div>
</div>

<!-- <script type="text/javascript" src="js/script.js"></script>
<script src="<?php echo base_url()?>assets/dashboard/assets/js/jquery-1.7.1.min.js" type="text/javascript"></script> -->

<script type="text/javascript">
  var ticket_type = "";
  var center;
  var markernya, markerss = [];
  var markerpools = [];
  var infowindow;
  var bounds, boundspool;
  var datadikirim;
  var car = "M17.402,0H5.643C2.526,0,0,3.467,0,6.584v34.804c0,3.116,2.526,5.644,5.643,5.644h11.759c3.116,0,5.644-2.527,5.644-5.644 V6.584C23.044,3.467,20.518,0,17.402,0z M22.057,14.188v11.665l-2.729,0.351v-4.806L22.057,14.188z M20.625,10.773 c-1.016,3.9-2.219,8.51-2.219,8.51H4.638l-2.222-8.51C2.417,10.773,11.3,7.755,20.625,10.773z M3.748,21.713v4.492l-2.73-0.349 V14.502L3.748,21.713z M1.018,37.938V27.579l2.73,0.343v8.196L1.018,37.938z M2.575,40.882l2.218-3.336h13.771l2.219,3.336H2.575z M19.328,35.805v-7.872l2.729-0.355v10.048L19.328,35.805z";
  var iconpool = {
    url: "http://transporter.lacak-mobil.com/assets/images/markergif.gif", // url JIKA GIF
    // path: 'assets/images/iconpulsemarker.gif',
    // scale: .5,
    // anchor: new google.maps.Point(25,10),
    // scaledSize: new google.maps.Size(15,15)
  };

  $("#notifnya").fadeIn(1000);
  $("#notifnya").fadeOut(5000);
  $("#substation_name").chosen();
  $("#customer_name").chosen();
  $("#ticketingtypesearchcustomer").hide();
  $("#ticketingtypesearchsubstation").hide();

  // RADIO BUTTON DIPILIH
  $("#ticketingtype0").on("click", function(){
    ticket_type = 0;
    $("#ticketingtypesearchcustomer").show();
    $("#ticketingtypesearchsubstation").hide();
  });

  $("#ticketingtype1").on("click", function(){
    ticket_type = 1;
    $("#ticketingtypesearchcustomer").hide();
    $("#ticketingtypesearchsubstation").show();
  });

  function showcreateticketing(){
    $("#technicianresponse").html("");
    $("#ticket_name").val("");
    $("#customer_name").val("");
    $("#substation_name").val("");
    $("#formtablegardumaster").hide();
    // $("#formcreateticket").show();
    document.getElementById('formcreateticket').style.visibility = "visible";
  }

  function btncancel(){
    window.location = '<?php echo base_url()?>tms/ticketing';
  }

  function initialize() {
     bounds = new google.maps.LatLngBounds();
     boundspool = new google.maps.LatLngBounds();
    map = new google.maps.Map(
      document.getElementById("map_canvas"), {
        center: new google.maps.LatLng(-6.1753871, 106.8249641),
        zoom: 8,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        options: {
          gestureHandling: 'greedy'
        }
      });
  }


  function btnSearchTech(){
    if (ticket_type == 0) {
      var sendId = $("#customer_name").val();
    }else {
      var sendId = $("#substation_name").val();
    }

    var data = {ticket_type : ticket_type, sendId: sendId};
    $("#loader").show();
    $.post("<?php echo base_url()?>tms/searchforticket", data, function(response){
      $("#loader").hide();
        console.log("response : ", response);
        var latcompare = parseFloat(response.dataforcompare[0].lat);
        var lngcompare = parseFloat(response.dataforcompare[0].lng);
        var name       = response.dataforcompare[0].name;
        var total      = response.datadblive.length;
        var searchnearby = [];
        var arraylatlng = [];
        var kmnya = 20;
        var kmtomiles = kmnya / 1.6;

        for (var y = 0; y < 1; y++) {
          deletemarkerpool();
          var positionpool = new google.maps.LatLng(response.dataforcompare[0].lat, response.dataforcompare[0].lng);
          boundspool.extend(positionpool);

          markerpool = new google.maps.Marker({
            position: positionpool,
            map: map,
            icon: iconpool,
            title: name + "\n" + response.dataforcompare[0].address,
            id: response.dataforcompare[0].id,
            optimized: false
          });
          markerpools.push(markerpool);
        }

          for (var i = 0; i < total; i++) {
            // kondisi if : Math.ceil(distance(latcompare, lngcompare, parseFloat(response.datadblive[i].auto_last_lat), parseFloat(response.datadblive[i].auto_last_long), "K")) <= kmtomiles
            if (response.datadblive[i].technician_id != "Not Assign Yet") {
              arraylatlng.push({
                dist : Math.ceil(distance(latcompare, lngcompare, parseFloat(response.datadblive[i].auto_last_lat), parseFloat(response.datadblive[i].auto_last_long), "K") * 1.6),
                technician_id : response.datadblive[i].technician_id,
                technician_name : response.datadblive[i].technician_name,
                technician_phone : response.datadblive[i].technician_phone,
                device: response.datadblive[i].vehicle_device,
                vehicle : response.datadblive[i].vehicle_no + ' - ' + response.datadblive[i].vehicle_name,
                loc: response.datadblive[i].auto_last_position,
                lat : response.datadblive[i].auto_last_lat,
                lng: response.datadblive[i].auto_last_long,
                course : response.datadblive[i].auto_last_course,
                auto_last_update : response.datadblive[i].auto_last_update,
                auto_last_lat : response.datadblive[i].auto_last_lat,
                auto_last_long : response.datadblive[i].auto_last_long,
                auto_last_engine : response.datadblive[i].auto_last_engine,
                auto_last_speed : response.datadblive[i].auto_last_speed,
                vehicle_card_no : response.datadblive[i].vehicle_card_no,
                vehicle_id : response.datadblive[i].vehicle_id
              });
            }else {
              arraylatlng.push({
                dist : Math.ceil(distance(latcompare, lngcompare, parseFloat(response.datadblive[i].auto_last_lat), parseFloat(response.datadblive[i].auto_last_long), "K") * 1.6),
                technician_id : response.datadblive[i].technician_id,
                technician_name : response.datadblive[i].technician_name,
                technician_phone : response.datadblive[i].technician_phone,
                device: response.datadblive[i].vehicle_device,
                vehicle : response.datadblive[i].vehicle_no + ' - ' + response.datadblive[i].vehicle_name,
                loc: response.datadblive[i].auto_last_position,
                lat : response.datadblive[i].auto_last_lat,
                lng: response.datadblive[i].auto_last_long,
                course : response.datadblive[i].auto_last_course,
                auto_last_update : response.datadblive[i].auto_last_update,
                auto_last_lat : response.datadblive[i].auto_last_lat,
                auto_last_long : response.datadblive[i].auto_last_long,
                auto_last_engine : response.datadblive[i].auto_last_engine,
                auto_last_speed : response.datadblive[i].auto_last_speed,
                vehicle_card_no : response.datadblive[i].vehicle_card_no,
                vehicle_id : response.datadblive[i].vehicle_id
              });
            }
          }
          console.log("arraylatlng <= "+kmnya+" km : ", arraylatlng.sort(compareValues('dist')));
          console.log("latcompare : ", latcompare);
          console.log("lngcompare : ", lngcompare);
          console.log("name : ", name);
          var fixarray = arraylatlng.sort(compareValues('dist'));
          var no = 0;
          infowindow      = new google.maps.InfoWindow();
          var htmltechnician = ""
            for (var x = 0; x < fixarray.length; x++) {
              no = x + 1;
               htmltechnician += '<tr>'+
                                   '<td>'+no+'</td>'+
                                   '<td>'+fixarray[x].technician_name+'</td>'+
                                   '<td>'+fixarray[x].dist+' Km</td>'+
                                   '<td><button onclick="assigntothistechnician('+fixarray[x].technician_id+ ',' +fixarray[x].vehicle_id+ ',' +fixarray[x].dist+ ',' +no+ ');" type="button" class="btn btn-primary" title="Assign job to this Technician"><span class="fa fa-list"></span></button></td>'+
                                   '<td>'+
                                   '<div id="confirmassignjob_'+no+'" style="display: none;">'+
                                     '<button type="button" name="button" class="btn btn-danger" onclick="btnCloseModal('+no+');">X</button> | '+
                                     '<button type="submit" name="button" class="btn btn-success" onclick="confirmassignthis('+no+');"><span class="fa fa-check"></span></button><img src="<?php echo base_url()?>assets/images/ajax-loader.gif" id="loader2" style="display: none;">'+
                                   '</div></td>'+
                                 '</tr>';

             DeleteMarkers(fixarray[x].device);
             var icon = {
               // url: "<?php echo base_url()?>assets/images/car_biru/car1.png", // url
               path: car,
               scale: .5,
               // anchor: new google.maps.Point(25,10),
               // scaledSize: new google.maps.Size(30,20),
               strokeColor: 'white',
               strokeWeight: .10,
               fillOpacity: 1,
               fillColor: '#00b300',
               offset: '5%'
             };
             markernya = new google.maps.Marker({
               map: map,
               icon: icon,
               position: new google.maps.LatLng(parseFloat(fixarray[x].lat), parseFloat(fixarray[x].lng)),
               title: fixarray[x].vehicle + " - " + fixarray[x].technician_name,
               // + ' - ' + value.vehicle_name + value.driver + "\n" +
               //   "GPS Time : " + value.gps.gps_date_fmt + " " + value.gps.gps_time_fmt + "\n" + value.gps.georeverse.display_name + "\n" + value.gps.gps_latitude_real_fmt + ", " + value.gps.gps_longitude_real_fmt + "\n" +
               //   "Speed : " + value.gps.gps_speed + " kph",
               id: fixarray[x].device
             });
             markerss.push(markernya);
             icon.rotation = Math.ceil(fixarray[x].course);
             markernya.setIcon(icon);

             google.maps.event.addListener(markernya, 'click', (function(markernya, x) {
               return function() {
                 var data = {device : markernya.id};
                 jQuery.post("<?php echo base_url() ?>tms/getlastinfonya", data, function(response){
                   console.log("response saat mobil diklik nih : ", response);
                   infowindow.close();
                   var gps_status = response.gps_status;
                     if (gps_status == "A") {
                       gps_status = "Good";
                     }else {
                       gps_status = "Lost Signal";
                     }

                     var string = "<div style='z-index: 1;'>" +
                       fixarray[x].vehicle + ' - ' + fixarray[x].technician_name + "<br>" +
                       "GPS Time : " + fixarray[x].auto_last_update + "<br>" + "Position : " + response.georeverse.display_name + "<br>" + "Coord : " + fixarray[x].auto_last_lat + ", " + fixarray[x].auto_last_long + "<br>" +
                       "Engine : " + fixarray[x].auto_last_engine + "<br>" +
                       "Speed : " + fixarray[x].auto_last_speed + " kph" + "<br>" +
                       "Odometer : " + response.gps_odometer + "<br>" +
                       "GPS Status : " + gps_status + "<br>" +
                       "Card No : " + fixarray[x].vehicle_card_no + "<br>" +
                       // "<a href='<?php echo base_url()?>maps/tracking/" + fixarray[x].vehicle_id + "' target='_blank'>Tracking</a>" +
                       "</div>";

                     infowindow = new google.maps.InfoWindow({
                       content: string,
                       maxWidth: 300
                     });

                     infowindow.setContent(string);
                     map.setCenter(new google.maps.LatLng(parseFloat(fixarray[x].auto_last_lat), parseFloat(fixarray[x].auto_last_long)));
                     infowindow.open(map, markernya);
                 }, "json");
               };
             })(markernya, x));
            }
          // console.log("searchnearby : ", searchnearby.sort());
        $("#technicianresponse").html(htmltechnician);
        $("#tablesearchtechnician").show();
    }, 'json');
  }

  // CEK IF NAME IS DUPLICATE
  $('#ticket_name').on('blur', function(){
 	var ticket_name = $('#ticket_name').val();
 	if (ticket_name == '') {
 		ticket_name_state = false;
 		return;
 	}
 	$.ajax({
      url: '<?php echo base_url()?>tms/cekifduplicate',
      type: 'post',
      dataType:"json",
      data: {
      	'ticket_name_check' : 1,
      	'ticket_name' : ticket_name,
      },
      success: function(response){
        console.log('responya : ', response.msg);
      	if (response.msg == 'taken' ) {
          ticket_name_state = false;
          $('#error_msg').html('Ticket Name already exist, please change ticket');
          $("#error_msg").fadeIn(1000);
          $("#error_msg").fadeOut(5000);
          document.getElementById('btnsearch').style.visibility = "hidden";
      	}else{
      	  ticket_name_state = true;
          $('#error_msg').html('Ticket Name available');
          document.getElementById('btnsearch').style.visibility = "visible";
          $("#error_msg").fadeIn(1000);
          $("#error_msg").fadeOut(5000);
      	}
      }
 	});
 });

 function assigntothistechnician(techid, devid, distance, x){
   var ticket_name       = $("#ticket_name").val();
   var customer_name     = $("#customer_name").val();
   var substation_name   = $("#substation_name").val();
   var duedate           = $("#duedate").val();
   var ticket_keterangan = $("#ticket_keterangan").val();

    datadikirim = {
     ticket_type : ticket_type, techid : techid, devid : devid, distance : distance, ticket_name : ticket_name, customer_name : customer_name, substation_name : substation_name, duedate : duedate, ticket_keterangan : ticket_keterangan
   };
   $("#confirmassignjob_"+x).show();
 }

  function confirmassignthis(x){
    console.log("data dikirim : ", datadikirim);
      dataString = JSON.stringify(datadikirim);
      // console.log("data dataString : ", dataString);
      $("#loader2").show();
      $.post("<?php echo base_url()?>tms/saveticket", dataString, function(response){
        console.log("responya : ", response);
          if(response == "200") {
            // document.getElementById('formcreateticket').style.visibility = "hidden";
            // $("#formtablegardumaster").show();
            // $("#notifnya2").html("Ticket Successfully Created");
            // $("#notifnya2").fadeIn(1000);
            // $("#notifnya2").fadeOut(5000);
            // $('html, body').animate({ scrollTop: 0 }, 'fast');
            window.location = '<?php echo base_url()?>tms/ticketing';
            return false;
          } else {
            document.getElementById('formcreateticket').style.visibility = "hidden";
            $("#formtablegardumaster").show();
            $("#notifnya2").html("Failed Creating Ticket, please refresh this page and try again.");
            $("#notifnya2").fadeIn(1000);
            $('html, body').animate({ scrollTop: 0 }, 'fast');
          }
      }, "json");
  }

  function DeleteMarkers(id) {
    //Loop through all the markers and remove
    for (var i = 0; i < markerss.length; i++) {
      if (markerss[i].id == id) {
        //Remove the om Map
        markerss[i].setMap(null);

        //Remove the marker from array.
        markerss.splice(i, 1);
        return;
      }
    }
  }

  function deletemarkerpool(){
    //Loop through all the markers and remove
    for (var i = 0; i < markerpools.length; i++) {
        //Remove the om Map
        markerpools[i].setMap(null);
        //Remove the marker from array.
        markerpools.splice(i, 1);
        return;
    }
  }

  function changestatus(ticketid, curstatus){
    console.log("ticketid : ", ticketid);
    console.log("curstatus : ", curstatus);
    $("#ticketid").val(ticketid);
    $("#current_status").val(curstatus);
    $("#modalchangestatus").show();
  }

  function closemodallistchangestatus(){
    $("#modalchangestatus").hide();
  }

  function btnCloseModal2(){
    $("#modalchangestatus").hide();
  }

  function compareValues(key, order = 'asc') {
  return function innerSort(a, b) {
    if (!a.hasOwnProperty(key) || !b.hasOwnProperty(key)) {
      // property doesn't exist on either object
      return 0;
    }

    const varA = (typeof a[key] === 'string')
      ? a[key].toUpperCase() : a[key];
    const varB = (typeof b[key] === 'string')
      ? b[key].toUpperCase() : b[key];

    let comparison = 0;
    if (varA > varB) {
      comparison = 1;
    } else if (varA < varB) {
      comparison = -1;
    }
    return (
      (order === 'desc') ? (comparison * -1) : comparison
    );
  };
}

  function distance(lat1, lon1, lat2, lon2, unit) {
    	var radlat1 = Math.PI * lat1/180;
    	var radlat2 = Math.PI * lat2/180;
    	var theta = lon1-lon2;
    	var radtheta = Math.PI * theta/180;
    	var dist = Math.sin(radlat1) * Math.sin(radlat2) + Math.cos(radlat1) * Math.cos(radlat2) * Math.cos(radtheta);
    	if (dist > 1) {
    		dist = 1;
    	}
    	dist = Math.acos(dist)
    	dist = dist * 180/Math.PI;
    	dist = dist * 60 * 1.1515;
    	if (unit=="K") { dist = dist * 1.609344; }
    	if (unit=="N") { dist = dist * 0.8684; }
    	return dist;
    }

    function btnCloseModal(x){
      $("#confirmassignjob_"+x).hide();
    }

  // FOR DISABLE SUBMIT FORM
  jQuery(window).keydown(function(event){
    if(event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });
</script>

<?php
$key = $this->config->item("GOOGLE_MAP_API_KEY");
//$key = "AIzaSyAYe-6_UE3rUgSHelcU1piLI7DIBnZMid4";

if(isset($key) && $key != "") { ?>
  <script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo $key;?>&callback=initialize" type="text/javascript"></script>
  <?php } else { ?>
    <script src="http://maps.google.com/maps/api/js?sensor=false"></script>
    <?php } ?>
