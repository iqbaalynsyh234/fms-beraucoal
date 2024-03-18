<style media="screen">
  .table thead tr th {
    font-size: 11px;
    font-weight: 600;
  }
</style>

    <?php if ($this->session->flashdata('notif')) {?>
      <div class="alert alert-success" id="notifnya" style="display: none;"><?php echo $this->session->flashdata('notif');?></div>
    <?php }?>
      <div class="row">
        <div class="col-md-12" id="tablevehicles">
          <div class="panel" id="panel_form">
            <header class="panel-heading panel-heading-red">LIVE TRANSACTION - DEVELOPMENT</header>
            <div class="panel-body" id="bar-parent10" style="overflow-x:auto;">

                  <table id="example1" class="table table-striped" style="font-size:10px;" >
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>TransID</th>
                        <th>Last Rom</th>
                        <th>TruckID</th>
                        <th>Driver ID</th>
                        <th>Driver Name</th>
                        <th>StartTime</th>
                        <th>Endtime</th>
                        <th>Gross</th>
                        <th>Tare</th>
                        <th>Netto</th>
                        <th>Client</th>
                        <th>Material</th>
                        <th>Hauling</th>
                        <th>Coal</th>
                        <th>Mode</th>
                        <th>Dumping</th>
                        <th>CP</th>
                        <th>Remark (M)</th>
                        <th>Status</th>
                        <!-- <th>Control</th> -->
                      </tr>
                    </thead>
                    <tbody>
                      <?php if (sizeof($data) < 1) {?>
                        <tr>
                          <td>Data is empty</td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <!-- <td></td> -->
                        </tr>
                      <?php }else {?>
                        <?php for($i=0;$i<count($data);$i++) { ?>
                          <tr>
                            <td width="2%"><?=$i+1?></td>
                            <td><?=$data[$i]['integrationwim_transactionID'];?></td>
                            <td><?=$data[$i]['integrationwim_last_rom'];?></td>
                            <td><?=$data[$i]['integrationwim_truckID'];?></td>
                              <?=
                              $driverid_column = "";
                              $drivername_column = "";
                               $driverid_itws = $data[$i]['integrationwim_driver_iditws'];
                                if ($driverid_itws == "" || $driverid_itws == 0) {
                                  $driverid_column   = $data[$i]['integrationwim_driver_id'];
                                  $drivername_column = $data[$i]['integrationwim_driver_name'];
                                }else {
                                  $driverid_column   = $data[$i]['integrationwim_driver_iditws'];
                                  $drivername_column = $data[$i]['integrationwim_driver_nameitws'];
                                }
                              ?>
                            <td>
                              <?php echo $driverid_column ?>
                            </td>
                            <td>
                              <?php echo $drivername_column ?>
                            </td>
                            <!-- <td></td> -->
                            <td><?=$data[$i]['integrationwim_penimbanganStartLocal'];?></td>
                            <td><?=$data[$i]['integrationwim_penimbanganFinishLocal'];?></td>
                            <td><?=$data[$i]['integrationwim_gross'];?></td>
                            <td><?=$data[$i]['integrationwim_tare'];?></td>
                            <td><?=$data[$i]['integrationwim_netto'];?></td>
                            <td><?=$data[$i]['integrationwim_client_id'];?></td>
                            <td><?=$data[$i]['integrationwim_material_id'];?></td>
                            <td><?=$data[$i]['integrationwim_hauling_id'];?></td>
                            <td><?=$data[$i]['integrationwim_itws_coal'];?></td>
                            <td><?=$data[$i]['integrationwim_status'];?></td>
                            <td><?=$data[$i]['integrationwim_dumping_fms_port'];?></td>
                            <td><?=$data[$i]['integrationwim_dumping_fms_cp'];?></td>
                            <td><?=$data[$i]['integrationwim_distanceWB'];?></td>
                            <?php
                            if ($data[$i]['integrationwim_operator_status'] == 1 || $data[$i]['integrationwim_operator_status'] == 2) {?>
                              <td>
                            <?php }elseif ($data[$i]['integrationwim_operator_status'] == 3) {?>
                              <td>
                            <?php }else {?>
                              <td style="background-color:red; color: white;">
                            <?php } ?>
                            <?php
                            if($data[$i]['integrationwim_operator_status'] == 0){
                              echo "UNPROCESS";
                              $edit = 1;
                            }else if($data[$i]['integrationwim_operator_status'] == 1){
                              echo "UPDATED";
                              $edit = 0;
                            }else if($data[$i]['integrationwim_operator_status'] == 2){
                              echo "UPDATED BY ADMIN";
                              $edit = 0;
                            }else if($data[$i]['integrationwim_operator_status'] == 3){
                              echo "REJECTED";
                              $edit = 0;
                            }else{
                              echo "-";
                              $edit = 0;
                            }
                             ?>
                            </td>
                            <!-- <td>
                            <?php if($edit == 1){ ?>
                              <a onclick="getDetail('<?php echo $data[$i]['integrationwim_id'];?>')">
                                <button class="btn btn-success btn-sm" title="Detail">
                                <span class="fa fa-edit"></span>
                                </button>
                              </a>

                               <a href="<?php echo base_url() ?>wim/show/<?php echo $data[$i]['integrationwim_id'];?>" target="_blank">
                                <button class="btn btn-info btn-sm" title="Detail">
                                  <span class="fa fa-photo"></span>
                                </button>
                               </a>
                            <?php }  ?>

                            </td> -->
                          </tr>
                        <?php } ?>
                      <?php } ?>
                    </tbody>
                  </table>
                  <div id="datadetail" style="display:none;"></div>
            </div>
      		</div>
        </div>
      </div>

<!-- <script src="https://apis.google.com/js/platform.js?onload=init" async defer></script> -->
<script src="https://apis.google.com/js/api.js"  async defer></script>
<script type="text/javascript">
  function getDetail(id){
    $("#loader2").show();
    $.post("<?php echo base_url() ?>wim/detail/"+id, {}, function(response){
      $("#loader2").hide();
      $("#example1").hide();
      $("#datadetail").show();
      $("#datadetail").html(response.datafix);
    }, "json");
  }

  function btnCancel(){
      $("#example1").show();
      $("#datadetail").hide();
  }

  function getFromDrive2() {
    var request = gapi.client.drive.files.get({
      'fileId': '18LnBe3byOs7TUFiAXAgxt0vd4T8qGOB8'
    });
    request.execute(function(resp) {
      console.log('Title: ' + resp.title);
      console.log('Description: ' + resp.description);
      console.log('MIME type: ' + resp.mimeType);
    });
  }

  function handleClientLoad() {

    var SCOPES = ['https://www.googleapis.com/auth/drive', 'profile'];
	var CLIENT_ID = '807678138767-kq65vpunpkfc6ppfdhiufa6601ckidff.apps.googleusercontent.com';
	var FOLDER_NAME = "";
	var FOLDER_ID = "root";
	var FOLDER_PERMISSION = true;
	var FOLDER_LEVEL = 0;
	var NO_OF_FILES = 100;
	var DRIVE_FILES = [];
	var FILE_COUNTER = 0;
	var FOLDER_ARRAY = [];
	var DELETE_FROM_TRASH = false;


    //gapi is client library, it used for Load the API client and auth2 library
    gapi.load('client:auth2', initClient);
}

  function initClient() {
    gapi.client.init({
        clientId: '807678138767-kq65vpunpkfc6ppfdhiufa6601ckidff.apps.googleusercontent.com',
        scope: SCOPES.join('https://www.googleapis.com/auth/drive.file https://www.googleapis.com/auth/drive.metadata https://www.googleapis.com/auth/drive')
    }).then(function () {
        // Listen for sign-in state changes.
        gapi.auth2.getAuthInstance().isSignedIn.listen(updateSigninStatus);
        // Handle the initial sign-in state.
        updateSigninStatus(gapi.auth2.getAuthInstance().isSignedIn.get());
    });
}

  function getFromDrive(id){
	 handleClientLoad();
    console.log("id : ", id);
    gapi.load('client', function () {
      gapi.client.load('drive', 'v2', function () {
        var file = gapi.client.drive.files.get({ fileId: '1rawdj6Q2tHvlCYfmKLAipylxMNC-RAXj' });
		//var file = gapi.client.drive.files.get({ fileId: '18LnBe3byOs7TUFiAXAgxt0vd4T8qGOB8' });
        file.execute(function (resp) {
          console.log("resp : ", resp);
        });
      });
    });
  }

  function base64ArrayBuffer(arrayBuffer) {
  var base64    = ''
  var encodings = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/'

  var bytes         = new Uint8Array(arrayBuffer)
  var byteLength    = bytes.byteLength
  var byteRemainder = byteLength % 3
  var mainLength    = byteLength - byteRemainder

  var a, b, c, d
  var chunk

  // Main loop deals with bytes in chunks of 3
  for (var i = 0; i < mainLength; i = i + 3) {
    // Combine the three bytes into a single integer
    chunk = (bytes[i] << 16) | (bytes[i + 1] << 8) | bytes[i + 2]

    // Use bitmasks to extract 6-bit segments from the triplet
    a = (chunk & 16515072) >> 18 // 16515072 = (2^6 - 1) << 18
    b = (chunk & 258048)   >> 12 // 258048   = (2^6 - 1) << 12
    c = (chunk & 4032)     >>  6 // 4032     = (2^6 - 1) << 6
    d = chunk & 63               // 63       = 2^6 - 1

    // Convert the raw binary segments to the appropriate ASCII encoding
    base64 += encodings[a] + encodings[b] + encodings[c] + encodings[d]
  }

  // Deal with the remaining bytes and padding
  if (byteRemainder == 1) {
    chunk = bytes[mainLength]

    a = (chunk & 252) >> 2 // 252 = (2^6 - 1) << 2

    // Set the 4 least significant bits to zero
    b = (chunk & 3)   << 4 // 3   = 2^2 - 1

    base64 += encodings[a] + encodings[b] + '=='
  } else if (byteRemainder == 2) {
    chunk = (bytes[mainLength] << 8) | bytes[mainLength + 1]

    a = (chunk & 64512) >> 10 // 64512 = (2^6 - 1) << 10
    b = (chunk & 1008)  >>  4 // 1008  = (2^6 - 1) << 4

    // Set the 2 least significant bits to zero
    c = (chunk & 15)    <<  2 // 15    = 2^4 - 1

    base64 += encodings[a] + encodings[b] + encodings[c] + '='
  }

  return base64
}
</script>
