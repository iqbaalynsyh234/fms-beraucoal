<style media="screen">
div#modalchangepass {
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
      <div class="col-md-12" id="tablecustomer">
        <div class="panel" id="panel_form">
          <header class="panel-heading panel-heading-blue">Customer</header>
          <div class="panel-body" id="bar-parent10">
              <table id="example1" class="table table-striped">
                <thead>
          				<tr>
          					<th>
                      <button type="button" class="btn btn-success btn-xs" onclick="showaddcustomer()" title="Add New Customer">
                        <span class="fa fa-plus"></span>
                      </button>No
                    </th>
          					<th><?php echo "Customer Name" ?></th>
          					<th><?php echo "Option" ?></th>
          				</tr>
          			</thead>
                <tbody>
                  <?php for($i=0;$i<count($datacustomer);$i++) { ?>
          				  <tr>
            					<td width="2%"><?=$i+1?></td>
								<td><?php echo $datacustomer[$i]['group_name'];?></td>
							  <td>
								<a href="<?php echo base_url();?>account/customeredit/<?php echo $datacustomer[$i]['group_id'];?>">
								  <img src="<?php echo base_url();?>assets/images/edit.gif" />
								</a>
								<a href="<?php echo base_url();?>account/customerdelete/<?php echo $datacustomer[$i]['group_id'];?>" onclick="javascript: return confirm('<?=$this->lang->line("lconfirm_delete"); ?>')">
								  <img src="<?php echo base_url();?>assets/images/trash.gif" />
								</a>
							  </td>
                    </tr>
                  <? } ?>
  							</tbody>
  						</table>
            </div>
      </div>
    </div>
    <div class="col-md-12" id="formaddcustomer" style="display: none;">
      <div class="panel" id="panel_form">
        <header class="panel-heading panel-heading-blue">Add Customer</header>
        <div class="panel-body" id="bar-parent10">
          <form class="form-horizontal" id="frmadd" name="frmadd" onsubmit="javascript: return frmadd_onsubmit()">
                <table class="table sortable no-margin">
                  <tr>
                    <td>Branch Office (Parent)</td>
                    <td>
                      <select class="select2" name="branchoffice" id="branchoffice" onchange="getsubcompanybyid();">
                        <option value="">--Select Branch Office--</option>
                        <option value="empty">Empty</option>
                        <?php for ($i=0; $i < sizeof($databranch); $i++) {?>
                          <option value="<?php echo $databranch[$i]->company_id; ?>"><?php echo $databranch[$i]->company_name; ?></option>
                        <?php } ?>
                      </select>
                    </td>
                  </tr>

                  <tr id="showthissubcompany">

                  </tr>

                  <tr>
                      <td>Customer Name</td>
                      <td><input type="text" name="groupname" id="groupname" class="form-control"/></td>
                  </tr>

                  <tr>
                      <td></td>
                      <td>
                        <button type="button" class="btn btn-warning" onclick="btncancel()"/> Cancel</button>
                        <button type="submit" id="submit" name="submit" class="btn btn-success"/> Save</button>
                        <img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" style="display:none;"/>
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

<script type="text/javascript">
  // $("#notifnya").fadeIn(1000);
  // $("#notifnya").fadeOut(5000);

  function showaddcustomer(){
    $("#formaddcustomer").show();
    $("#tablecustomer").hide();
  }

  function btncancel(){
    $("#showthissubcompany").hide();
    $("#formaddcustomer").hide();
    $("#tablecustomer").show();
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
		jQuery.post("<?=base_url()?>account/customersave", jQuery("#frmadd").serialize(),
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
    var companyid = $("#branchoffice").val();
    console.log("data : ", companyid);
    jQuery.post("<?=base_url()?>account/getsubcompanybyid", {id : companyid}, function(r){
				jQuery("#loader").hide();
				console.log("r : ", r);
        var size = r.data.length;
        var html = "";
              html += '<td>Subcompany</td>';
                html += '<td>';
                  html += '<select class="form-control" name="subbranchoffice" id="subbranchoffice">';
                      html += '<option value="">--Select Sub Branch Office--</option>';
                      html += '<option value="empty">Empty</option>';
                      for (var i = 0; i < size; i++) {
                        html += '<option value="'+r.data[i].subcompany_id+'">'+r.data[i].subcompany_name+'</option>';
                      }
                html += '</select>';
              html += '</td>';
        $("#showthissubcompany").html(html);
        $("#showthissubcompany").show();
			}, "json");
  }
</script>
