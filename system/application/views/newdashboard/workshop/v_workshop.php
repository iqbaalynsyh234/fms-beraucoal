
<style media="screen">
div#modaldeleteworkshop {
  margin-top: 1.5%;
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
    <!--<div class="alert alert-success" id="notifnya2" style="display: none;"></div>-->
    <div class="row">
      <div class="col-md-12" id="tableworkshop">
        <div class="panel" id="panel_form">
          <header class="panel-heading panel-heading-red">Workshop / Agency / Location</header>
          <div class="panel-body" id="bar-parent10">
              <table id="example1" class="table table-striped"style="width:100%;">
                <thead>
          				<tr>
          					<th>
                      <?php if ($privilegecode == 3) {?>

                      <?php }else {?>
                        <button type="button" class="btn btn-success btn-xs" onclick="showaddworkshop()" title="Add New Workhsop">
                          <span class="fa fa-plus"></span>
                        </button>
                      <?php } ?>
                      No
                    </th>
          					<th>Workshop Name</th>
                    <th>Phone</th>
                    <th>Fax</th>
                    <th>Address</th>
                    <?php if ($privilegecode == 3) {?>

                    <?php }else {?>
                      <th>Control</th>
                    <?php } ?>
          				</tr>
          			</thead>
                <tbody>
                  <?php $no = 1; for ($i=0; $i < sizeof($workshop); $i++) {?>
                    <tr>
                      <td><?php echo $no; ?></td>
                      <td><?php echo $workshop[$i]['workshop_name'] ?></td>
                      <td><?php echo $workshop[$i]['workshop_telp'] ?></td>
                      <td><?php echo $workshop[$i]['workshop_fax'] ?></td>
                      <td><?php echo $workshop[$i]['workshop_address'] ?></td>
                      <?php if ($privilegecode == 3) {?>

                      <?php }else {?>
                        <td>
                          <button type="button" class="btn btn-danger" onclick="btnDelete('<?php echo $workshop[$i]['workshop_id'];?>')" title="Delete Data">
                            <span class="fa fa-trash"></span>
                          </button>
                        </td>
                      <?php } ?>
                    </tr>
                  <?php $no++; } ?>
  							</tbody>
  						</table>
            </div>
      </div>
    </div>

    <div class="col-md-12" id="formaddworkshop" style="display: none; width:1000px;">
      <div class="panel" id="panel_form">
        <header class="panel-heading panel-heading-red">Add Workshop / Agency / Location</header>
        <div class="panel-body" id="bar-parent10">
          <form class="block-content form" id="frmadd" onsubmit="javascript: return frmadd_onsubmit(this)">
      			<table width="100%" cellpadding="3" class="table sortable no-margin">
              <tr>
      					<td>Workshop</td>
      					<td>
                  <input class="form-control" type="text" name="workshop_name" id="workshop_name" /></td>
                </td>
      				</tr>
      				<tr>
      					<td>Telp<br />
      					<td>
                  <input class="form-control" type="number" name="workshop_telp" id="workshop_telp"/></td>
                </td>
      				</tr>
      				<tr>
      					<td>Fax<br />
      					<td>
                  <input class="form-control" type="text" name="workshop_fax" id="workshop_fax"/></td>
                </td>
      				</tr>
      				<tr>
      					<td style="text-align:top">Address<br />
      					<td>
                  <textarea class="form-control" cols="30" rows="3" name="workshop_address" id="workshop_address" ></textarea></td>
                </td>
      				</tr>
      				<tr>
      					<td>&nbsp;</td>
      					<td class="text-right">
		       				<button type="button" class="btn btn-warning" onclick="btncancel();"/>Cancel</button>
                  <button type="submit" class="btn btn-success" name="btnsave" id="btnsave"/> Save</button>
  								<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
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

<div id="modaldeleteworkshop" style="display: none;">
  <div id="mydivheader"></div>
  <div class="row" >
    <div class="col-md-12">
        <div class="card card-topline-yellow">
            <div class="card-head">
                <header>Delete Form</header>
                <div class="tools">
                    <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
                  <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                  <button type="button" class="btn btn-danger" name="button" onclick="closemodallistofvehicle();">X</button>
                </div>
            </div>
            <div class="card-body">
                <form class="form-horizontal" action="<?php echo base_url()?>vehicles/deleteworkshop" method="post" enctype="multipart/form-data">
                  <input type="hidden" name="iddelete" id="iddelete">
                  Are you sure want to delete this data?<br><br>
                  <div class="text-right">
                    <button type="button" name="button" class="btn btn-warning" onclick="btnCloseModal();">Cancel</button>
                    <button type="submit" name="button" class="btn btn-danger">Delete</button>
                  </div>
                </form>
            </div>
        </div>
    </div>
  </div>
</div>

<script type="text/javascript">
function frmadd_onsubmit()
{
  jQuery("#loader").show();
  jQuery.post("<?=base_url()?>vehicles/save_workshop", jQuery("#frmadd").serialize(),
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

  function configthisvehicle(idnya){
    // console.log(idnya);
    jQuery.post('<?php echo base_url(); ?>transporter/maintenancemanagement/forconfigservicess/', {id: idnya},
      function(response)
      {
        console.log("response : ", response);
      }
      , "json"
    );
  }

  function setservicess(idnya){
		jQuery.post('<?php echo base_url(); ?>transporter/maintenancemanagement/forsetservicess/', {id: idnya},
			function(response)
			{
				console.log(response);
			}
			, "json"
		);
	}

  function showaddworkshop(){
    $("#formaddworkshop").show();
    $("#tableworkshop").hide();
  }

  function btncancel(){
    $("#formaddworkshop").hide();
    $("#tableworkshop").show();
  }

  function btnDelete(id){
    $("#iddelete").val(id);
    $("#modaldeleteworkshop").fadeIn(1000);
  }

  function closemodallistofvehicle(){
    $("#modaldeleteworkshop").fadeOut(1000);
  }

  function btnCloseModal(){
    $("#modaldeleteworkshop").fadeOut(1000);
  }
</script>
