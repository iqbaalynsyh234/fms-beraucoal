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
        <div class="col-md-12" id="tablesubcustomer">
          <div class="panel" id="panel_form">
            <header class="panel-heading panel-heading-blue">Vehicle Data</header>
            <div class="panel-body" id="bar-parent10">
                <table id="example1" class="table table-hover">
                  <thead>
            				<tr>
            					<th>
                        <button type="button" class="btn btn-success btn-xs" onclick="showaddcustomer()" title="Add New Sub Customer">
                          <span class="fa fa-plus"></span>
                        </button>No
                      </th>
                      <th>Company</th>
                      <th>Sub Company</th>
                      <th>Customer</th>
            					<th>Sub Customer Name</th>
            					<th>Control</th>
            				</tr>
            			</thead>
                  <tbody>
                    <?php
                    for ($i=0; $i < sizeof($subcustomer); $i++) { ?>
                      <tr>
                        <td><?php echo ($i+1) ?></td>
                        <td>
                          <?php if (isset($company)) {?>
                            <?php for ($j=0; $j < sizeof($company); $j++) {?>
                              <?php if ($subcustomer[$i]->subgroup_company == $company[$j]->company_id) {
                                echo $company[$j]->company_name;
                              } ?>
                            <?} ?>
                          <?php } ?>
                        </td>
                        <td>
                          <?php if (isset($subcompany)) {?>
                            <?php for ($k=0; $k < sizeof($subcompany); $k++) {?>
                              <?php if ($subcustomer[$i]->subgroup_subcompany == $subcompany[$k]->subcompany_id) {
                                echo $subcompany[$k]->subcompany_name;
                              } ?>
                            <?} ?>
                          <?php } ?>
                        </td>
                        <td>
                          <?php if (isset($customer)) {?>
                            <?php for ($l=0; $l < sizeof($customer); $l++) {?>
                              <?php if ($subcustomer[$i]->subgroup_customer == $customer[$l]['group_id']) {
                                echo $customer[$l]['group_name'];
                              } ?>
                            <?} ?>
                          <?php } ?>
                        </td>
                        <td><?php echo $subcustomer[$i]->subgroup_name ?></td>
                        <td>
                          <a href="<?php echo base_url();?>account/editsubcustomer/<?php echo $subcustomer[$i]->subgroup_id;?>">
                            <img src="<?php echo base_url();?>assets/images/edit.gif" />
                          </a>
                        </td>
                      </tr>
                    <?php } ?>
    							</tbody>
    						</table>
              </div>
        </div>
      </div>

      <div class="col-md-12" id="formaddbranchoffice" style="display: none;">
            <div class="panel" id="panel_form">
              <header class="panel-heading panel-heading-blue">Add Sub Customer</header>
              <div class="panel-body" id="bar-parent10">
            <form class="form-horizontal" id="frmadd" name="frmadd" onsubmit="javascript: return frmadd_onsubmit()">
              <table class="table">
                <tr>
                  <td>Branch Office</td>
                  <td>
                    <select class="select2" name="company" id="company" onchange="getsubcompanybyid();">
                      <option value="">--Choose Branch Office--</option>
                      <?php for ($i=0; $i < sizeof($company); $i++) {?>
                        <option value="<?php echo $company[$i]->company_id;?>"><?php echo $company[$i]->company_name;?></option>
                      <?php } ?>
                    </select>
                  </td>
                </tr>

                <tr id="showthissubcompany" style="display: none;">

                </tr>

                <tr id="showthiscustomer" style="display: none;">

                </tr>

                <tr>
                  <td>Sub Customer Name</td>
                  <td>
                    <input class="form-control" type="text" name="subcustomername" id="subcustomername">
                  </td>
                </tr>
              </table>

              <div class="text-right">
                <button class="btn btn-warning" type="button" onclick="btncancel()"/> Cancel</button>
                <button type="submit" id="submit" name="submit" class="btn btn-success"/> Save</button>
                <img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" style="display:none;"/>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  function showaddcustomer(){
    $("#formaddbranchoffice").show();
    $("#tablesubcustomer").hide();
  }

  function btncancel(){
    $("#showthissubcompany").hide();
    $("#showthiscustomer").hide();
    $("#formaddbranchoffice").hide();
    $("#tablesubcustomer").show();
  }

  function btnDelete(id){
    $("#iddelete").val(id);
    $("#modalDeletedest").show();
  }

  function closemodallistofvehicle(){
    $("#modalDeletedest").hide();
  }

  function frmadd_onsubmit()
	{
		jQuery("#loader").show();
		jQuery.post("<?=base_url()?>account/savesubcustomer", jQuery("#frmadd").serialize(),
			function(r)
			{
				jQuery("#loader").hide();
				if (r.error)
				{
					alert(r.message);
					return false;
				}

				alert(r.message);
				location = r.redirect;
			}
			, "json"
		);
		return false;
	}

  function getsubcompanybyid(){
    // GET SUBCOMPANY BY COMPANY ID
    var companyid = $("#company").val();
    console.log("data : ", companyid);
    jQuery.post("<?=base_url()?>account/getsubcompanybyid", {id : companyid}, function(r){
				jQuery("#loader").hide();
        $("#showthissubcompany").show();
				console.log("r : ", r);
        var size = r.data.length;
        var html = "";
              html += '<td>Sub Branch Office</td>';
                html += '<td>';
                  html += '<select class="form-control" name="subcompany" id="subcompany" onchange="getcustomerbysubcompanyid();">';
                      html += '<option value="">--Select Subcompany--</option>';
                      html += '<option value="empty">Empty</option>';
                      for (var i = 0; i < size; i++) {
                        html += '<option value="'+r.data[i].subcompany_id+'">'+r.data[i].subcompany_name+'</option>';
                      }
                html += '</select>';
              html += '</td>';
        $("#showthissubcompany").html(html);
			}, "json");
  }

  function getcustomerbysubcompanyid(){
    // GET CUSTOMER BY SUBCOMPANY ID
    var subcompany = $("#subcompany").val();
    console.log("data : ", subcompany);
    jQuery.post("<?=base_url()?>account/getcustomerbysubcompanyid", {id : subcompany}, function(r){
				jQuery("#loader").hide();
        $("#showthiscustomer").show();
				console.log("r : ", r);
        var size = r.data.length;
        var html = "";
              html += '<td>Customer</td>';
                html += '<td>';
                  html += '<select class="form-control" name="customer" id="customer">';
                    html += '<option value="">--Select Customer--</option>';
                    html += '<option value="empty">Empty</option>';
                    for (var i = 0; i < size; i++) {
                      html += '<option value="'+r.data[i].group_id+'">'+r.data[i].group_name+'</option>';
                    }
              html += '</select>';
            html += '</td>';
        $("#showthiscustomer").html(html);
			}, "json");
  }
</script>
