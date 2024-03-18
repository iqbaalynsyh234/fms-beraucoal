<!-- start sidebar menu -->
<div class="sidebar-container">
  <?=$sidebar;?>
</div>
<!-- end sidebar menu -->

<!-- start page content -->
<div class="page-content-wrapper" style="width: 100%;">
  <div class="page-content">
    <br>
    <?php if ($this->session->flashdata('notif')) {?>
      <div class="alert alert-success" id="notifnya" style="display: none;"><?php echo $this->session->flashdata('notif');?></div>
    <?php }?>
    <div class="alert alert-success" id="notifnya2" style="display: none;"></div>
    <div class="row">
      <div class="col-md-12" id="tablesubbranchoffice">
        <div class="panel" id="panel_form">
          <header class="panel-heading panel-heading-blue">Sub Branch Office</header>
          <div class="panel-body" id="bar-parent10">
              <table id="example1" class="table table-striped">
                <thead>
          				<tr>
          					<th>
                      <button type="button" class="btn btn-success btn-xs" onclick="showaddsubbranchoffice()" title="Add New Sub branch Office">
                        <span class="fa fa-plus"></span>
                      </button>No
                    </th>
          					<th>Sub Branch Office Name</th>
                    <th>Branch Office (Parent)</th>
                    <th>Control</th>
          				</tr>
          			</thead>
                <tbody>
                  <?php for($i=0;$i<count($subcompany);$i++) { ?>
          				  <tr>
            					<td width="2%"><?=$i+1?></td>
                      <td><?php echo $subcompany[$i]->subcompany_name;?></td>
                      <td>
                        <?php for($x=0;$x<count($company);$x++) { ?>
                          <?php if ($subcompany[$i]->subcompany_parent == $company[$x]->company_id) {?>
                            <?php echo $company[$x]->company_name ?>
                          <?php  } }  ?>
                      </td>
                      <td>
                        <a href="<?php echo base_url();?>account/editsubbranchoffice/<?php echo $subcompany[$i]->subcompany_id;?>">
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
        <header class="panel-heading panel-heading-blue">Add Sub Branch Office</header>
        <div class="panel-body" id="bar-parent10">
          <form class="form-horizontal" id="frmadd" name="frmadd" onsubmit="javascript: return frmadd_onsubmit()">
            <table class="table sortable no-margin">
                <tr>
                  <td>Branch Office (Parent)</td>
                  <td>:</td>
                  <td>
                    <select class="select2" name="subcompany_parent" id="subcompany_parent">
                      <?php for($i=0; $i < count($company); $i++) {?>
                        <option value="<?php echo $company[$i]->company_id; ?>"><?php echo $company[$i]->company_name; ?></option>
                      <?php } ?>
                    </select>
                  </td>
                </tr>
                <tr>
                  <td>Sub Branch Office Name</td>
                  <td>:</td>
                  <td><input type="text" name="subcompany_name" id="subcompany_name" class="form-control"/></td>
                </tr>
                <tr>
                  <td></td>
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

  function showaddsubbranchoffice(){
    $("#formaddbranchoffice").show();
    $("#tablesubbranchoffice").hide();
  }

  function btncancel(){
    $("#formaddbranchoffice").hide();
    $("#tablesubbranchoffice").show();
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
		jQuery.post("<?=base_url()?>account/savesubbranchoffice", jQuery("#frmadd").serialize(),
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
</script>
