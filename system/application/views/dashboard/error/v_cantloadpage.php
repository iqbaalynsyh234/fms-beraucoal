<style media="screen">
  #page-content-new{
    width : 82.6%;
  }
</style>
<!-- start sidebar menu -->
<div class="sidebar-container">
  <?=$sidebar;?>
</div>
<!-- end sidebar menu -->

<!-- start page content -->
<div class="page-content-wrapper">
  <div class="page-content" id="page-content-new">
    <br>
    <?php if ($this->session->flashdata('notif')) {?>
      <div class="alert alert-success" id="notifnya" style="display: none;">
        <?php echo $this->session->flashdata('notif');?>
      </div>
      <?php }?>
        <!--<div class="alert alert-success" id="notifnya2" style="display: none;"></div>-->
        <div class="row">
          <div class="col-md-12" id="tabletriphistoryreport">
            <div class="card-box">
              <div class="card card-topline-green">
                <div class="card-head">
                  <header id="headernya1"></header>
                </div>
                <div class="card-body">
                  <b>
                    <h2>This page can't loaded. Please press other menu.</h2>
                  </b>
                </div>
              </div>
            </div>
          </div>
        </div>
  </div>
</div>
