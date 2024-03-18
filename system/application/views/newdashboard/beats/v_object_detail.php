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
<div class="page-content-wrapper" style="width: 100%;">
 <div class="page-content">
    <br>
    <?php if ($this->session->flashdata('notif')) {?>
      <div class="alert alert-success" id="notifnya" style="display: none;"><?php echo $this->session->flashdata('notif');?></div>
    <?php }?>
    <div class="alert alert-success" id="notifnya2" style="display: none;"></div>
    <div class="row">
      <div class="col-md-12" id="tablebranchoffice">
        <div class="panel" id="panel_form">
          <header class="panel-heading panel-heading" style="background-color: #221f1f"><font color="white">Master Data - Object Detail</font></header>
          <div class="panel-body" id="bar-parent10">
              <table id="example1" class="table table-striped">
                <thead>
          				<tr>
							<th><?php echo "No"?></th>
          					<th><?php echo "ID Sync"?></th>
							<th><?php echo "Name"?></th>
          					<th><?php echo "Active"?></th>
							<th><?php echo "Parent ID"?></th>
							<th><?php echo "Parent Name"?></th>
						</tr>
          			</thead>
                <tbody>
                  <?php for($i=0;$i<count($data);$i++) { ?>
					<tr>
            					<td width="2%"><small><?=$i+1?></td>
								<td><small><?php echo $data[$i]->master_object_detail_id_sync;?></td>
								<td><small><?php echo $data[$i]->master_object_detail_name;?></td>
            					<td><small>
									<?php 
										if($data[$i]->master_object_detail_active == 1){
											echo "Yes";
										}else{
											echo "No";
										}
									?>
								</td>
								<td><small><?php echo $data[$i]->master_object_detail_parent_id;?></td>
            					<td><small><?php echo $data[$i]->master_object_detail_parent_name;?></td>
					</tr>
                  <? } ?>
  					</tbody>
				</table>
            </div>
      </div>
    </div>
    
  </div>
</div>
</div>

<script type="text/javascript">
  // $("#notifnya").fadeIn(1000);
  // $("#notifnya").fadeOut(5000);

  function showaddbranchoffice(){
    $("#formaddbranchoffice").show();
    $("#tablebranchoffice").hide();
  }

  function btncancel(){
    $("#formaddbranchoffice").hide();
    $("#tablebranchoffice").show();
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
		jQuery.post("<?=base_url()?>account/savebranchoffice", jQuery("#frmadd").serialize(),
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
