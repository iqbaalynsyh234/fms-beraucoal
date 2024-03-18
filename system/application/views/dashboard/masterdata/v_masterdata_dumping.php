<!-- start sidebar menu -->
<div class="sidebar-container">
  <?=$sidebar;?>
</div>
<!-- end sidebar menu -->

<!-- start page content -->
<div class="page-content-wrapper">
  <div class="page-content" style="width:160%;">
    <br>
    <?php if ($this->session->flashdata('notif')) {?>
      <div class="alert alert-success" id="notifnya" style="display: none;"><?php echo $this->session->flashdata('notif');?></div>
    <?php }?>
    <!--<div class="alert alert-success" id="notifnya2" style="display: none;"></div>-->
    <div class="row">
      <div class="col-md-12" id="tablecustomer">
        <div class="panel" id="panel_form">
          <header class="panel-heading panel-heading-blue">Master Data Dumping</header>
          <div class="panel-body" id="bar-parent10">
              <table id="example1" class="table table-striped">
                <thead>
          				<tr>
          					<th>
                      <a class="btn btn-success btn-xs" href="<?php echo base_url() ?>masterdata/addDumping" title="Add New">
                        <span class="fa fa-plus"></span>
                      </a>No
                    </th>
          					<th>ID Dumping</th>
                    <th>Dumping</th>
                    <th>Dumping Reg Date</th>
                    <th>Description</th>
          					<th>Control</th>
          				</tr>
          			</thead>
                <tbody>
                  <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                  </tr>
  							</tbody>
  						</table>
            </div>
      </div>
    </div>


</div>
</div>
</div>
