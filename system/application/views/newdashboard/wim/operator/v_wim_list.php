<style media="screen">
  div#modalinformationdetail {
    max-height: 500px;
    width: 65%;
    overflow-x: auto;
    position: fixed;
    z-index: 9;
    background-color: #f1f1f1;
    text-align: left;
    border: 1px solid #d3d3d3;
  }

  .modalClient{
    z-index: 9999;
    width: 100%;
    margin: auto;
  }

  #data-wim{
    background-color: #1f50a2;
    color: white;
  }

  .modalMaterial{
    z-index: 9999;
    width: 100%;
    margin: auto;
  }

/* AUTOCOMPLETE STYLE */
* {
  box-sizing: border-box;
}

body {
  font: 16px Arial;
}

/*the container must be positioned relative:*/
.autocompleteno_lambung {
  position: relative;
  display: inline-block;
}

.autocomplete_client {
  position: relative;
  display: inline-block;
}

.autocomplete_material {
  position: relative;
  display: inline-block;
}

.autocomplete_driveritws {
  position: relative;
  display: inline-block;
}

input {
  border: 1px solid transparent;
  background-color: #f1f1f1;
  padding: 10px;
  font-size: 16px;
}

input[type=text] {
  /* background-color: #f1f1f1; */
  width: 100%;
}

input[type=submit] {
  background-color: DodgerBlue;
  color: #fff;
  cursor: pointer;
}

/* AUTOCOMPLETE NO LAMBUNG */
.autocompleteno_lambung-items {
  position: absolute;
  border: 1px solid #d4d4d4;
  border-bottom: none;
  border-top: none;
  z-index: 99;
  /*position the autocomplete items to be the same width as the container:*/
  top: 100%;
  left: 0;
  right: 0;
}

.autocompleteno_lambung-items div {
  padding: 10px;
  cursor: pointer;
  background-color: #fff;
  border-bottom: 1px solid #d4d4d4;
}

/*when hovering an item:*/
.autocompleteno_lambung-items div:hover {
  background-color: #e9e9e9;
}

/*when navigating through the items using the arrow keys:*/
.autocompleteno_lambung-active {
  background-color: DodgerBlue !important;
  color: #ffffff;
}

/* AUTOCOMPLETE CLIENT */
.autocomplete_client-items {
  position: absolute;
  border: 1px solid #d4d4d4;
  border-bottom: none;
  border-top: none;
  z-index: 99;
  /*position the autocomplete items to be the same width as the container:*/
  top: 100%;
  left: 0;
  right: 0;
}

.autocomplete_client-items div {
  padding: 10px;
  cursor: pointer;
  background-color: #fff;
  border-bottom: 1px solid #d4d4d4;
}

/*when hovering an item:*/
.autocomplete_client-items div:hover {
  background-color: #e9e9e9;
}

/*when navigating through the items using the arrow keys:*/
.autocomplete_client-active {
  background-color: DodgerBlue !important;
  color: #ffffff;
}

/* AUTOCOMPLETE MATERIAL */
.autocomplete_material-items {
  position: absolute;
  border: 1px solid #d4d4d4;
  border-bottom: none;
  border-top: none;
  z-index: 99;
  /*position the autocomplete items to be the same width as the container:*/
  top: 100%;
  left: 0;
  right: 0;
}

.autocomplete_material-items div {
  padding: 10px;
  cursor: pointer;
  background-color: #fff;
  border-bottom: 1px solid #d4d4d4;
}

/*when hovering an item:*/
.autocomplete_material-items div:hover {
  background-color: #e9e9e9;
}

/*when navigating through the items using the arrow keys:*/
.autocomplete_material-active {
  background-color: DodgerBlue !important;
  color: #ffffff;
}

/* AUTOCOMPLETE DRIVER ITWS */
.autocomplete_driveritws-items {
  position: absolute;
  border: 1px solid #d4d4d4;
  border-bottom: none;
  border-top: none;
  z-index: 99;
  /*position the autocomplete items to be the same width as the container:*/
  top: 100%;
  left: 0;
  right: 0;
}

.autocomplete_driveritws-items div {
  padding: 10px;
  cursor: pointer;
  background-color: #fff;
  border-bottom: 1px solid #d4d4d4;
}

/*when hovering an item:*/
.autocomplete_driveritws-items div:hover {
  background-color: #e9e9e9;
}

/*when navigating through the items using the arrow keys:*/
.autocomplete_driveritws-active {
  background-color: DodgerBlue !important;
  color: #ffffff;
}
</style>

<div class="sidebar-container">
  <?=$sidebar;?>
</div>
<div class="page-content-wrapper">
  <div class="page-content" id="page-content-new">
    <div class="row">
      <div class="col-md-12 col-sm-12">
        <div class="panel" id="panel_form">
          <header class="panel-heading panel-heading-red" id="data-wim">DATA WIM</header>
          <div class="panel-body" id="bar-parent10">
            <form class="form-horizontal form" name="frmsearch" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
              <input type="hidden" name="offset" id="offset" value=""/>
              <input type="hidden" id="sortby" name="sortby" value=""/>
              <input type="hidden" id="orderby" name="orderby" value=""/>
              <input type="hidden" id="first_load" name="first_load" value="1"/>

            <div id="formsearch_new">
              <table class="table table-striped" style="width:100%;">
                <tr>
                  <td>
                     Trans. ID
                  </td>
                  <td>
                    <div class="col-lg-10 col-md-10">
                      <input type="text" name="itws_transID" id="itws_transID" class="form-control" readonly>
                    </div>
                  </td>

                  <td>
                     ROM
                  </td>
                  <td>
                    <div class="col-lg-12 col-md-12">
                      <!-- <input type="text" name="itws_rom" id="itws_rom" class="form-control" readonly> -->
                      <select class="form-control select2" name="itws_rom" id="itws_rom">
                        <option value="0"></option>
                        <?php
                        if (isset($data_rom)) {
                          for ($i=0; $i < sizeof($data_rom); $i++) {?>
                            <option value="<?php echo $data_rom[$i]['street_name']; ?>"><?php echo $data_rom[$i]['street_name']; ?></option>
                          <?php }
                        }
                         ?>
                      </select>
                    </div>
                  </td>

                  <td>
                     Driver ITWS
                  </td>
                  <td>
                    <!-- <div class="col-lg-10 col-md-10">
                      <div class="autocompletenolambung">
                      </div>
                    </div> -->

                    <div class="col-lg-10 col-md-10">
                      <div class="autocompleteclient" style="width:160px;">
                      <div class="input-group mb-3">
                        <input type="text" name="itws_driver" id="itws_driver" hidden>
                        <input type="text" name="itws_driver_search" id="itws_driver_search" class="form-control">
                        <div class="input-group-append">
                          <button class="btn btn-warning" type="button" data-toggle="modal" data-target="#clientitwsdriver">
                            <span class="fa fa-info"></span>
                          </button>
                        </div>
                      </div>
                      </div>
                    </div>

                    <!-- <div class="col-lg-12 col-md-12">
                      <select class="form-control select2" name="itws_driver" id="itws_driver" style="width:100%;">
                        <option value="0"></option>
                        <?php
                          for ($i=0; $i < sizeof($datadriver); $i++) {?>
                            <option value="<?php echo $datadriver[$i]['driveritws_id_driver'].'|'.$datadriver[$i]['driveritws_driver_name']; ?>"><?php echo $datadriver[$i]['driveritws_driver_name'].' '.$datadriver[$i]['driveritws_id_driver']; ?></option>
                          <?php } ?>
                      </select>
                    </div> -->
                  </td>
                </tr>

                <tr>
                  <td>
                     No. Lambung
                  </td>
                  <td>
                    <div class="col-lg-10 col-md-10">
                      <div class="autocompletenolambung">
                        <input type="text" name="itws_nolambung" id="itws_nolambung" class="form-control">
                      </div>
                    </div>
                  </td>

                  <td>
                     Code
                  </td>
                  <td>
                    <div class="col-lg-12 col-md-12">
                      <input type="text" name="itws_contractor" id="itws_contractor" class="form-control" readonly>
                    </div>
                  </td>

                  <td>
                     Driver ID Simper
                  </td>
                  <td>
                    <div class="col-lg-12 col-md-12">
                      <input type="text" name="driver_id_cron" id="driver_id_cron" class="form-control" readonly>
                    </div>
                  </td>

                  <td>
                    <div class="col-lg-12 col-md-12">
                      <input type="text" name="driver_name_cron" id="driver_name_cron" class="form-control" readonly>
                    </div>
                  </td>
                </tr>

                <tr>
                  <td>
                     Client
                  </td>
                  <td>
                    <div class="col-lg-10 col-md-10">
                      <div class="autocompleteclient" style="width:160px;">
                      <div class="input-group mb-3">
                          <input type="text" name="itws_client" id="itws_client" class="form-control">
                        <div class="input-group-append">
                          <button class="btn btn-warning" type="button" data-toggle="modal" data-target="#clientModal">
                            <span class="fa fa-info"></span>
                          </button>
                        </div>
                      </div>
                      </div>
                    </div>
                  </td>

                  <td>
                     Material
                  </td>
                  <td>
                    <div class="col-lg-12 col-md-12">
                      <div class="autocompletematerial" style="width:200px;">
                      <div class="input-group mb-3">
                          <input type="text" name="itws_material" id="itws_material" class="form-control">
                        <div class="input-group-append">
                          <button class="btn btn-warning" type="button" data-toggle="modal" data-target="#materialModal">
                            <span class="fa fa-info"></span>
                          </button>
                        </div>
                      </div>
                      </div>
                    </div>
                  </td>

                  <td></td>
                  <td>
                    <div class="col-lg-12 col-md-12">
                      <input type="text" name="itws_hauling" id="itws_hauling" class="form-control" readonly>
                    </div>
                  </td>

                  <td>
                    <div class="col-lg-10 col-md-10">
                      <input type="text" name="itws_coal" id="itws_coal" class="form-control" readonly>
                    </div>
                  </td>
                </tr>

                <tr>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>

                  <td>
                    <div class="text-right">
                      <button class="btn btn-success">Update</button>
                    </div>
                  </td>
                </tr>

              </table>
            </div>
          </form>
          </div>
        </div>
      </div>

    </div>
    <div id="loader2" class="mdl-progress mdl-js-progress mdl-progress__indeterminate" style="display:none;"></div>
    <div id="result" style="width:100%"></div>
  </div>
  <!-- end page content -->

</div>

<div class="modal fade modalClient" id="clientModal" tabindex="-1" role="dialog" aria-labelledby="clientModal" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content" style="width:150%; margin-left: -20%;">
      <div class="modal-header">
        <h5 class="modal-title" id="clientModal">
          <b>
            Data Client
          </b>
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div style="width: 100%; overflow-y:auto; max-height:450px;">
          <table id="table_1" class="table table-striped" style="width: 100%; font-size:11px;">
            <thead>
              <tr>
                <th>
                  No
                </th>
                <th>ID Client</th>
                <th>Client</th>
                <th>Description</th>
              </tr>
            </thead>
            <tbody>
              <?php if (sizeof($data_client) > 0) {
                for ($i=0; $i < sizeof($data_client); $i++) {?>
                  <tr>
                    <td><?php echo $i+1; ?></td>
                    <td><?php echo $data_client[$i]['client_shortcut'] ?></td>
                    <td><?php echo $data_client[$i]['client_id'] ?></td>
                    <td><?php echo $data_client[$i]['client_description'] ?></td>
                  </tr>
                <?php } } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade modalMaterial" id="materialModal" tabindex="-1" role="dialog" aria-labelledby="materialModal" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content" style="width:150%; margin-left: -20%;">
      <div class="modal-header">
        <h5 class="modal-title" id="materialModal">
          <b>
            Data Material
          </b>
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div style="width: 100%; overflow-y:auto; max-height:450px;">
          <table id="table_2" class="table table-striped" style="width: 100%; font-size:11px;">
            <thead>
              <tr>
                <th>
                  No
                </th>
                <th>ID Material</th>
                <th>Material</th>
                <th>Hauling</th>
                <th>Coal</th>
                <th>Desc</th>
              </tr>
            </thead>
            <tbody>
              <?php if (sizeof($data_material) > 0) {
                for ($i=0; $i < sizeof($data_material); $i++) {?>
                  <tr>
                    <td><?php echo $i+1; ?></td>
                    <td><?php echo $data_material[$i]['material_shortcut'] ?></td>
                    <td><?php echo $data_material[$i]['material_id'] ?></td>
                    <td><?php echo $data_material[$i]['material_hauling'] ?></td>
                    <td><?php echo $data_material[$i]['material_coal'] ?></td>
                    <td><?php echo $data_material[$i]['material_description'] ?></td>
                  </tr>
                <?php } } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade modalClient" id="clientitwsdriver" tabindex="-1" role="dialog" aria-labelledby="clientitwsdriver" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content" style="width:150%; margin-left: -20%;">
      <div class="modal-header">
        <h5 class="modal-title" id="clientitwsdriver">
          <b>
            Data Driver ITWS
          </b>
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div style="width: 100%; overflow-y:auto; max-height:450px;">
          <table id="table_3" class="table table-striped" style="width: 100%; font-size:11px;">
            <thead>
              <tr>
                <th>
                  No
                </th>
                <th>ID Driver ITWS</th>
                <th>ID SIMPER</th>
                <th>Driver Name</th>
                <th>Contractor</th>
              </tr>
            </thead>
            <tbody>
              <?php if (sizeof($datadriveritws) > 0) {
                for ($i=0; $i < sizeof($datadriveritws); $i++) {?>
                  <tr>
                    <td><?php echo $i+1; ?></td>
                    <td><?php echo $datadriveritws[$i]['driveritws_id_driver'] ?></td>
                    <td><?php echo $datadriveritws[$i]['driveritws_id_simper'] ?></td>
                    <td><?php echo $datadriveritws[$i]['driveritws_driver_name'] ?></td>
                    <td><?php echo $datadriveritws[$i]['driveritws_company_name'] ?></td>
                  </tr>
                <?php } } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>




<script type="text/javascript" src="js/script.js"></script>
<script src="<?php echo base_url()?>assets/dashboard/assets/js/jquery-1.7.1.min.js" type="text/javascript"></script>

<script>
	$(document).ready(function() {
		page(0);
    setInterval(function () {
      page(0);
    }, 15000);

    $("#table_1").DataTable( {
        // "scrollY": "200px",
        "scrollCollapse": true,
        "searching": true,
        "paging": false
    });

    $("#table_2").DataTable( {
        // "scrollY": "100px",
        "scrollCollapse": true,
        "searching": true,
        "paging": false
    });

    $("#table_3").DataTable( {
        // "scrollY": "100px",
        "scrollCollapse": true,
        "searching": true,
        "paging": false
    });
  });

	 function page(p) {
		if (p == undefined) {
			p = 0;
		}

		jQuery("#offset").val(p);
		jQuery("#loader2").show();
		// jQuery.post("<?= base_url(); ?>wim/search_report", jQuery("#frmsearch").serialize(),
    jQuery.post("<?= base_url(); ?>wim/search_report_operator", jQuery("#frmsearch").serialize(),
			function(r) {
        console.log("response search_report_operator : ", r);
				if (r.error) {
					alert(r.message);
					jQuery("#loader2").hide();
					jQuery("#result").hide();
				} else {
					jQuery("#loader2").hide();
					jQuery("#result").show();
					jQuery("#result").html(r.html);
					jQuery("#total").html(r.total);
				}
			}, "json"
		);
    return false;
	}

	function frmsearch_onsubmit()
	{
    $("#first_load").val(0);
		jQuery("#loader2").show();
		updateData();
		return false;
	}

  function updateData(){
    jQuery("#loader2").show();
		jQuery.post("<?= base_url(); ?>wim/itws_update_data", jQuery("#frmsearch").serialize(),
			function(response) {
        console.log("response itws_update_data : ", response);
        if (response.error) {
					alert(response.message);
					jQuery("#loader2").hide();
				} else {
					jQuery("#loader2").hide();
          alert(response.message);
          page(0);
          $("#itws_transID").val("");
          $("#itws_nolambung").val("");
          $("#itws_rom").val("");
          $("#itws_driverid").val("");
          $("#itws_contractor").val("");
          $("#itws_client").val("");
          $("#itws_material").val("");
          $("#itws_hauling").val("");
          $("#itws_coal").val("");
          $("#itws_driver").val("");
          $("#itws_driver_search").val("");
				}
			}, "json"
		);
    return false;
  }

	function company_onchange() {
		var data_company = jQuery("#company").val();
		if (data_company == 0) {
			// alert('Silahkan Pilih Cabang!!');
			// jQuery("#mn_vehicle").hide();

			jQuery("#vehicle").html("<option value='0' selected='selected' class='form-control'>--All Vehicle--</option>");
		} else {
			jQuery("#mn_vehicle").show();

			var site = "<?= base_url() ?>wim/get_vehicle_by_company_with_numberorder/" + data_company;
			jQuery.ajax({
				url: site,
				success: function(response) {
					jQuery("#vehicle").html("");
					jQuery("#vehicle").html(response);
				},
				dataType: "html"
			});
		}
	}

  function periode_onchange() {
    var data_periode = jQuery("#periode").val();
    if (data_periode == 'custom') {
      jQuery("#mn_sdate").show();
      jQuery("#mn_edate").show();
    } else {
      jQuery("#mn_sdate").hide();
      jQuery("#mn_edate").hide();
    }
  }

	function houronclick(){
		console.log("ok");
		$(".switch").html("<?php echo date("Y F d")?>");
	}

  // AUTOCOMPLETE NO LAMBUNG
  function autocompletenolambung(inp) {
  /*the autocomplete function takes two arguments,
  the text field element and an array of possible autocompleted values:*/
  var currentFocus;
    /*execute a function when someone writes in the text field:*/
    inp.addEventListener("input", function(e) {
        var a, b, i, val    = this.value;
        var keyword_vehicle = this.value;
        if (keyword_vehicle.length >= 3) {
          /*close any already open lists of autocompleted values*/
          closeAllListsno_lambung();
          if (!val) { return false;}
          currentFocus = -1;
          /*create a DIV element that will contain the items (values):*/
          a = document.createElement("DIV");
          a.setAttribute("id", this.id + "autocompleteno_lambung-list");
          a.setAttribute("class", "autocompleteno_lambung-items");
          /*append the DIV element as a child of the autocomplete container:*/
          this.parentNode.appendChild(a);

          // GET FROM DATABASE
          var data = {
            keyword : keyword_vehicle
          };
          $("#loader2").show();
          $.post("<?= base_url(); ?>wim/getVehicleByInput", data,
            function(r) {
              $("#loader2").hide();
              console.log("response getVehicleByInput : ", r);
              var arr = r.data;
              console.log("arr : ", arr);
              /*for each item in the array...*/
              for (i = 0; i < arr.length; i++) {
                /*check if the item starts with the same letters as the text field value:*/
                // if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
                  console.log("masuk bang");
                  /*create a DIV element for each matching element:*/
                  b = document.createElement("DIV");
                  /*make the matching letters bold:*/
                  b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
                  b.innerHTML += arr[i].substr(val.length);
                  /*insert a input field that will hold the current array item's value:*/
                  b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
                  /*execute a function when someone clicks on the item value (DIV element):*/
                  b.addEventListener("click", function(e) {

                      /*insert the value for the autocomplete text field:*/
                      inp.value = this.getElementsByTagName("input")[0].value;

                      var data2 = {
                        vehicleno :inp.value
                      };
                      $("#loader2").show();
                        $.post("<?= base_url(); ?>wim/recallThisVehicle", data2, function(response) {
                          $("#loader2").hide();
                          console.log("response recallThisVehicle : ", response);
                          var code = response.code;
                            if (code == 200) {
                                $("#itws_transID").val(response.data_forprocess.integrationwim_transactionID);
                                // CHANGE ROM SELECT
                                $('[name=itws_rom]').val(response.data_forprocess.integrationwim_last_rom);
                                $('#itws_rom').select2().trigger('change');
                                // CHANGE DRIVER ITWS SELECT
                                // $('[name=itws_driver]').html("");
                                // $('#itws_driver').select2().trigger('change');
                                // var data_driver_itws = response.data_driver_itws;
                                // var html_driver_itws = "";
                                //
                                // html_driver_itws += '<option value="0"></option>';
                                //   for (var i = 0; i < data_driver_itws.length; i++) {
                                //     html_driver_itws += '<option value="'+data_driver_itws[i].driveritws_id_driver + "|" + data_driver_itws[i].driveritws_driver_name +'">'+data_driver_itws[i].driveritws_driver_name + " " + data_driver_itws[i].driveritws_id_driver+'</option>';
                                //   }
                                  // APPEND SELECT2 WITH SELECTED COMPANY
                                  // $('[name=itws_driver]').html(html_driver_itws);

                                $("#driver_id_cron").val(response.data_forprocess.integrationwim_driver_id);
                                $("#driver_name_cron").val(response.data_forprocess.integrationwim_driver_name);
                                $("#itws_contractor").val(response.data_forprocess.integrationwim_haulingContractor);
                                $("#itws_client").val(response.data_forprocess.integrationwim_client_id);
                                $("#itws_material").val(response.data_forprocess.integrationwim_material_id);
                                $("#itws_hauling").val(response.data_forprocess.integrationwim_hauling_id);
                                $("#itws_coal").val(response.data_forprocess.integrationwim_itws_coal);
                            }else {
                              alert("Data not found");
                              $("#itws_transID").val("");
                              $("#itws_rom").val("");
                              $("#itws_driverid").val("");
                              $("#itws_contractor").val("");
                              $("#itws_client").val("");
                              $("#itws_material").val("");
                              $("#itws_hauling").val("");
                              $("#itws_coal").val("");
                            }
                        }, "json");

                      /*close the list of autocompleted values,
                      (or any other open lists of autocompleted values:*/
                      closeAllListsno_lambung();
                  });
                  a.appendChild(b);
                // }
              }
            }, "json"
          );
        }
    });

  /*execute a function presses a key on the keyboard:*/
  inp.addEventListener("keydown", function(e) {
      var x = document.getElementById(this.id + "autocompleteno_lambung-list");
      if (x) x = x.getElementsByTagName("div");
      if (e.keyCode == 40) {
        /*If the arrow DOWN key is pressed,
        increase the currentFocus variable:*/
        currentFocus++;
        /*and and make the current item more visible:*/
        addActiveno_lambung(x);
      } else if (e.keyCode == 38) { //up
        /*If the arrow UP key is pressed,
        decrease the currentFocus variable:*/
        currentFocus--;
        /*and and make the current item more visible:*/
        addActiveno_lambung(x);
      } else if (e.keyCode == 13) {
        /*If the ENTER key is pressed, prevent the form from being submitted,*/
        e.preventDefault();
        if (currentFocus > -1) {
          /*and simulate a click on the "active" item:*/
          if (x) x[currentFocus].click();
        }
      }
  });
  function addActiveno_lambung(x) {
    /*a function to classify an item as "active":*/
    if (!x) return false;
    /*start by removing the "active" class on all items:*/
    removeActiveno_lambung(x);
    if (currentFocus >= x.length) currentFocus = 0;
    if (currentFocus < 0) currentFocus = (x.length - 1);
    /*add class "autocomplete-active":*/
    x[currentFocus].classList.add("autocompleteno_lambung-active");
  }
  function removeActiveno_lambung(x) {
    /*a function to remove the "active" class from all autocomplete items:*/
    for (var i = 0; i < x.length; i++) {
      x[i].classList.remove("autocompleteno_lambung-active");
    }
  }
  function closeAllListsno_lambung(elmnt) {
    /*close all autocomplete lists in the document,
    except the one passed as an argument:*/
    var x = document.getElementsByClassName("autocompleteno_lambung-items");
    for (var i = 0; i < x.length; i++) {
      if (elmnt != x[i] && elmnt != inp) {
        x[i].parentNode.removeChild(x[i]);
      }
    }
  }
  /*execute a function when someone clicks in the document:*/
  document.addEventListener("click", function (e) {
      closeAllListsno_lambung(e.target);
  });
  }

  // AUTOCOMPLETE CLIENT
  function autocompleteclient(inp) {
  /*the autocomplete function takes two arguments,
  the text field element and an array of possible autocompleted values:*/
  var currentFocus;
  /*execute a function when someone writes in the text field:*/
  inp.addEventListener("input", function(e) {
      var a, b, i, val    = this.value;
      var keyword_vehicle = this.value;
      /*close any already open lists of autocompleted values*/
      closeAllLists_client();
      if (!val) { return false;}
      currentFocus = -1;
      /*create a DIV element that will contain the items (values):*/
      a = document.createElement("DIV");
      a.setAttribute("id", this.id + "autocomplete_client-list");
      a.setAttribute("class", "autocomplete_client-items");
      /*append the DIV element as a child of the autocomplete container:*/
      this.parentNode.appendChild(a);

      // GET FROM DATABASE
      var data = {
        keyword : keyword_vehicle
      };
      $("#loader2").show();
      $.post("<?= base_url(); ?>wim/getClientByInput", data,
  			function(r) {
          $("#loader2").hide();
          console.log("response getClientByInput : ", r);
          var arr = r.data;
          console.log("arr : ", arr);
          /*for each item in the array...*/
          for (i = 0; i < arr.length; i++) {
            /*check if the item starts with the same letters as the text field value:*/
            // if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
              console.log("masuk bang");
              /*create a DIV element for each matching element:*/
              b = document.createElement("DIV");
              /*make the matching letters bold:*/
              b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
              b.innerHTML += arr[i].substr(val.length);
              /*insert a input field that will hold the current array item's value:*/
              b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
              /*execute a function when someone clicks on the item value (DIV element):*/
              b.addEventListener("click", function(e) {

                  /*insert the value for the autocomplete text field:*/
                  inp.value = this.getElementsByTagName("input")[0].value;

                  /*close the list of autocompleted values,
                  (or any other open lists of autocompleted values:*/
                  closeAllLists_client();
              });
              a.appendChild(b);
            // }
          }
  			}, "json"
  		);
  });
  /*execute a function presses a key on the keyboard:*/
  inp.addEventListener("keydown", function(e) {
      var x = document.getElementById(this.id + "autocomplete_client-list");
      if (x) x = x.getElementsByTagName("div");
      if (e.keyCode == 40) {
        /*If the arrow DOWN key is pressed,
        increase the currentFocus variable:*/
        currentFocus++;
        /*and and make the current item more visible:*/
        addActive_client(x);
      } else if (e.keyCode == 38) { //up
        /*If the arrow UP key is pressed,
        decrease the currentFocus variable:*/
        currentFocus--;
        /*and and make the current item more visible:*/
        addActive_client(x);
      } else if (e.keyCode == 13) {
        /*If the ENTER key is pressed, prevent the form from being submitted,*/
        e.preventDefault();
        if (currentFocus > -1) {
          /*and simulate a click on the "active" item:*/
          if (x) x[currentFocus].click();
        }
      }
  });
  function addActive_client(x) {
    /*a function to classify an item as "active":*/
    if (!x) return false;
    /*start by removing the "active" class on all items:*/
    removeActive_client(x);
    if (currentFocus >= x.length) currentFocus = 0;
    if (currentFocus < 0) currentFocus = (x.length - 1);
    /*add class "autocomplete-active":*/
    x[currentFocus].classList.add("autocomplete_client-active");
  }
  function removeActive_client(x) {
    /*a function to remove the "active" class from all autocomplete items:*/
    for (var i = 0; i < x.length; i++) {
      x[i].classList.remove("autocomplete_client-active");
    }
  }
  function closeAllLists_client(elmnt) {
    /*close all autocomplete lists in the document,
    except the one passed as an argument:*/
    var x = document.getElementsByClassName("autocomplete_client-items");
    for (var i = 0; i < x.length; i++) {
      if (elmnt != x[i] && elmnt != inp) {
        x[i].parentNode.removeChild(x[i]);
      }
    }
  }
  /*execute a function when someone clicks in the document:*/
    document.addEventListener("click", function (e) {
        closeAllLists_client(e.target);
    });
  }

  // AUTOCOMPLETE MATERIAL
  function autocompletematerial(inp) {
  /*the autocomplete function takes two arguments,
  the text field element and an array of possible autocompleted values:*/
  var currentFocus;
  /*execute a function when someone writes in the text field:*/
  inp.addEventListener("input", function(e) {
      var a, b, i, val    = this.value;
      var keyword_vehicle = this.value;
      /*close any already open lists of autocompleted values*/
      closeAllLists_material();
      if (!val) { return false;}
      currentFocus = -1;
      /*create a DIV element that will contain the items (values):*/
      a = document.createElement("DIV");
      a.setAttribute("id", this.id + "autocomplete_material-list");
      a.setAttribute("class", "autocomplete_material-items");
      /*append the DIV element as a child of the autocomplete container:*/
      this.parentNode.appendChild(a);

      // GET FROM DATABASE
      var data = {
        keyword : keyword_vehicle
      };
      $("#loader2").show();
      $.post("<?= base_url(); ?>wim/getMaterialByInput", data,
  			function(r) {
          $("#loader2").hide();
          console.log("response getMaterialByInput : ", r);
          var arr = r.data;
          console.log("arr : ", arr);
          /*for each item in the array...*/
          for (i = 0; i < arr.length; i++) {
            /*check if the item starts with the same letters as the text field value:*/
            // if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
              console.log("masuk bang");
              /*create a DIV element for each matching element:*/
              b = document.createElement("DIV");
              /*make the matching letters bold:*/
              b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
              b.innerHTML += arr[i].substr(val.length);
              /*insert a input field that will hold the current array item's value:*/
              b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
              /*execute a function when someone clicks on the item value (DIV element):*/
              b.addEventListener("click", function(e) {

                  /*insert the value for the autocomplete text field:*/
                  inp.value = this.getElementsByTagName("input")[0].value;

                  var data3 = {
                    materialid :inp.value
                  };
                  $("#loader2").show();
                    $.post("<?= base_url(); ?>wim/getMaterialValue", data3, function(response) {
                      $("#loader2").hide();
                      console.log("response getMaterialValue : ", response);
                      $("#itws_hauling").val(response.data[0].material_hauling);
                      $("#itws_coal").val(response.data[0].material_coal);
                    }, "json");

                  /*close the list of autocompleted values,
                  (or any other open lists of autocompleted values:*/
                  closeAllLists_material();
              });
              a.appendChild(b);
            // }
          }
  			}, "json"
  		);
  });
  /*execute a function presses a key on the keyboard:*/
  inp.addEventListener("keydown", function(e) {
      var x = document.getElementById(this.id + "autocomplete_material-list");
      if (x) x = x.getElementsByTagName("div");
      if (e.keyCode == 40) {
        /*If the arrow DOWN key is pressed,
        increase the currentFocus variable:*/
        currentFocus++;
        /*and and make the current item more visible:*/
        addActive_material(x);
      } else if (e.keyCode == 38) { //up
        /*If the arrow UP key is pressed,
        decrease the currentFocus variable:*/
        currentFocus--;
        /*and and make the current item more visible:*/
        addActive_material(x);
      } else if (e.keyCode == 13) {
        /*If the ENTER key is pressed, prevent the form from being submitted,*/
        e.preventDefault();
        if (currentFocus > -1) {
          /*and simulate a click on the "active" item:*/
          if (x) x[currentFocus].click();
        }
      }
  });
  function addActive_material(x) {
    /*a function to classify an item as "active":*/
    if (!x) return false;
    /*start by removing the "active" class on all items:*/
    removeActive_material(x);
    if (currentFocus >= x.length) currentFocus = 0;
    if (currentFocus < 0) currentFocus = (x.length - 1);
    /*add class "autocomplete-active":*/
    x[currentFocus].classList.add("autocomplete_material-active");
  }
  function removeActive_material(x) {
    /*a function to remove the "active" class from all autocomplete items:*/
    for (var i = 0; i < x.length; i++) {
      x[i].classList.remove("autocomplete_material-active");
    }
  }
  function closeAllLists_material(elmnt) {
    /*close all autocomplete lists in the document,
    except the one passed as an argument:*/
    var x = document.getElementsByClassName("autocomplete_material-items");
    for (var i = 0; i < x.length; i++) {
      if (elmnt != x[i] && elmnt != inp) {
        x[i].parentNode.removeChild(x[i]);
      }
    }
  }
  /*execute a function when someone clicks in the document:*/
    document.addEventListener("click", function (e) {
        closeAllLists_material(e.target);
    });
  }

  // AUTOCOMPLETE DRIVER ITWS
  function autocompletedriveritws(inp) {
  /*the autocomplete function takes two arguments,
  the text field element and an array of possible autocompleted values:*/
  var currentFocus;
  /*execute a function when someone writes in the text field:*/
  inp.addEventListener("input", function(e) {
      var a, b, i, val    = this.value;
      var keyword_vehicle = this.value;
      /*close any already open lists of autocompleted values*/
      closeAllLists_client();
      if (!val) { return false;}
      currentFocus = -1;
      /*create a DIV element that will contain the items (values):*/
      a = document.createElement("DIV");
      a.setAttribute("id", this.id + "autocomplete_driveritws-list");
      a.setAttribute("class", "autocomplete_driveritws-items");
      /*append the DIV element as a child of the autocomplete container:*/
      this.parentNode.appendChild(a);

      // GET FROM DATABASE
      var data = {
        keyword : keyword_vehicle
      };
      $("#loader2").show();
      $.post("<?= base_url(); ?>wim/getDriverItws", data,
  			function(r) {
          $("#loader2").hide();
          console.log("response getDriverItws : ", r);
          var arr = r.data;
          console.log("arr : ", arr);
          /*for each item in the array...*/
          for (i = 0; i < arr.length; i++) {
            /*check if the item starts with the same letters as the text field value:*/
            // if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
              console.log("masuk bang");
              /*create a DIV element for each matching element:*/
              b = document.createElement("DIV");
              /*make the matching letters bold:*/
              b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
              b.innerHTML += arr[i].substr(val.length);
              /*insert a input field that will hold the current array item's value:*/
              b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
              /*execute a function when someone clicks on the item value (DIV element):*/
              b.addEventListener("click", function(e) {

                  /*insert the value for the autocomplete text field:*/
                  inp.value = this.getElementsByTagName("input")[0].value;

                  var data4 = {
                    driveritwsid :inp.value
                  };

                  console.log('Data4 : ', data4);

                  $("#loader2").show();
                    $.post("<?= base_url(); ?>wim/getDriverItwsValue", data4, function(response) {
                      $("#loader2").hide();
                      console.log("response getDriverItwsValue : ", response);
                      $("#itws_driver").val(response.data[0].driveritws_id_driver+'|'+response.data[0].driveritws_driver_name);
                    }, "json");

                  /*close the list of autocompleted values,
                  (or any other open lists of autocompleted values:*/
                  closeAllLists_client();
              });
              a.appendChild(b);
            // }
          }
  			}, "json"
  		);
  });
  /*execute a function presses a key on the keyboard:*/
  inp.addEventListener("keydown", function(e) {
      var x = document.getElementById(this.id + "autocomplete_driveritws-list");
      if (x) x = x.getElementsByTagName("div");
      if (e.keyCode == 40) {
        /*If the arrow DOWN key is pressed,
        increase the currentFocus variable:*/
        currentFocus++;
        /*and and make the current item more visible:*/
        addActive_client(x);
      } else if (e.keyCode == 38) { //up
        /*If the arrow UP key is pressed,
        decrease the currentFocus variable:*/
        currentFocus--;
        /*and and make the current item more visible:*/
        addActive_client(x);
      } else if (e.keyCode == 13) {
        /*If the ENTER key is pressed, prevent the form from being submitted,*/
        e.preventDefault();
        if (currentFocus > -1) {
          /*and simulate a click on the "active" item:*/
          if (x) x[currentFocus].click();
        }
      }
  });
  function addActive_client(x) {
    /*a function to classify an item as "active":*/
    if (!x) return false;
    /*start by removing the "active" class on all items:*/
    removeActive_client(x);
    if (currentFocus >= x.length) currentFocus = 0;
    if (currentFocus < 0) currentFocus = (x.length - 1);
    /*add class "autocomplete-active":*/
    x[currentFocus].classList.add("autocomplete_driveritws-active");
  }
  function removeActive_client(x) {
    /*a function to remove the "active" class from all autocomplete items:*/
    for (var i = 0; i < x.length; i++) {
      x[i].classList.remove("autocomplete_driveritws-active");
    }
  }
  function closeAllLists_client(elmnt) {
    /*close all autocomplete lists in the document,
    except the one passed as an argument:*/
    var x = document.getElementsByClassName("autocomplete_driveritws-items");
    for (var i = 0; i < x.length; i++) {
      if (elmnt != x[i] && elmnt != inp) {
        x[i].parentNode.removeChild(x[i]);
      }
    }
  }
  /*execute a function when someone clicks in the document:*/
    document.addEventListener("click", function (e) {
        closeAllLists_client(e.target);
    });
  }

  /*initiate the autocomplete function on the "myInput" element, and pass along the countries array as possible autocomplete values:*/
  // autocompletenolambung(document.getElementById("itws_nolambung"), countries);
  autocompletenolambung(document.getElementById("itws_nolambung"));
  autocompleteclient(document.getElementById("itws_client"));
  autocompletedriveritws(document.getElementById("itws_driver_search"));
  autocompletematerial(document.getElementById("itws_material"));
</script>
