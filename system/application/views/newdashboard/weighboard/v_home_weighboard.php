<style media="screen">
.material-icons{
  font-size: 50px;
  padding: 10px;
}
</style>
<div class="sidebar-container">
  <?=$sidebar;?>
</div>
<!-- end sidebar menu -->

<!-- start page content -->
<div class="page-content-wrapper">
  <div class="page-content">
		<div class="page-bar">
      <div class="page-title-breadcrumb">
				<div class=" pull-left">
           <div class="page-title">WB Board</div>
           <div style="color:red; font-size:16px;">Sample Data Tanggal : 17-11-2020 00:00:00 s/d 17-11-2020 23:59:59</div>
				</div>
          <!-- <ol class="breadcrumb page-breadcrumb pull-right">
              <li><i class="fa fa-home"></i>&nbsp;</li>
      					<li class="active"><a href="#">Yesterday</a> | </li>
      					<li><a href="#">Last 7</a> | </li>
               <li><a href="#">Last 30</a> </li>
          </ol> -->
       </div>
		</div>

      <div class="row">
        <div class="col-md-6 col-sm-12 col-12">
          <div class="white-box border-gray">
            <div class="info-box bg-danger">
              <span class="info-box-icon push-bottom"><i class="material-icons">folder</i></span>
              <div class="info-box-content">
                <span class="info-box-text">Total Gross (Kg)</span>
                <span class="info-box-number" id="totalgross" style="font-size:12px;">&nbsp</span>
                <!-- <div class="progress">
                  <div class="progress-bar width-60"></div>
                </div> -->
                <span class="progress-description" style="font-size:20px;">
                 <?php echo $totalgross ?>
                </span>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
              <table class="table table-bordered table-striped table-hover">
                <?php for ($i=0; $i < sizeof($getgrossbyvehicle); $i++) {?>
                    <tr>
                      <td><?php echo $i+1; ?></td>
                      <td><?php echo $getgrossbyvehicle[$i]['Truck']; ?></td>
                      <td><?php echo number_format($getgrossbyvehicle[$i]['totalgrosspervehicle'], "0", ",", "."); ?></td>
                    </tr>
                <?php } ?>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-sm-12 col-12">
        <div class="white-box border-gray">
          <div class="info-box bg-success">
            <span class="info-box-icon push-bottom"><i class="material-icons">folder_open</i></span>
            <div class="info-box-content">
              <span class="info-box-text">Total Netto (Kg)</span>
              <span class="info-box-number" id="totalgross" style="font-size:12px;">&nbsp</span>
              <!-- <div class="progress">
                <div class="progress-bar width-60"></div>
              </div> -->
              <span class="progress-description" style="font-size:20px;">
               <?php echo $totalnetto; ?>
              </span>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
            <table class="table table-bordered table-striped table-hover">
              <?php for ($i=0; $i < sizeof($getnettobyvehicle); $i++) {?>
                  <tr>
                    <td><?php echo $i+1; ?></td>
                    <td><?php echo $getnettobyvehicle[$i]['Truck']; ?></td>
                    <td><?php echo number_format($getnettobyvehicle[$i]['totalnettopervehicle'], "0", ",", "."); ?></td>
                  </tr>
              <?php } ?>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body" style="overflow-y:auto;">
          <table id="example1" class="table table-bordered table-striped table-hover" style="font-size:12px;">
            <thead>
              <tr>
                <th>No</th>
                <th>Trans</th>
                <th>Mode</th>
                <th>Truck</th>
                <th>Driver</th>
                <th>Client</th>
                <th>Material</th>
                <th>Hauling</th>
                <th>DateTimeTrans</th>
                <th>DateTimeGross</th>
                <th>DateTimeNetto</th>
                <th>Code</th>
                <th>Gross</th>
                <th>Tare</th>
                <th>Netto</th>
                <th>Coal</th>
              </tr>
            </thead>
            <tbody>
              <?php for ($i=0; $i < sizeof($totaldata); $i++) {?>
                <tr>
                  <td><?php echo $i+1; ?></td>
                  <td><?php echo $totaldata[$i]['Trans'] ?></td>
                  <td><?php echo $totaldata[$i]['Mode'] ?></td>
                  <td><?php echo $totaldata[$i]['Truck'] ?></td>
                  <td><?php echo $totaldata[$i]['Driver'] ?></td>
                  <td><?php echo $totaldata[$i]['Client'] ?></td>
                  <td><?php echo $totaldata[$i]['Material'] ?></td>
                  <td><?php echo $totaldata[$i]['Hauling'] ?></td>
                  <td><?php echo $totaldata[$i]['DateTimeTrans'] ?></td>
                  <td><?php echo $totaldata[$i]['DateTimeGross'] ?></td>
                  <td><?php echo $totaldata[$i]['DateTimeNetto'] ?></td>
                  <td><?php echo $totaldata[$i]['Code'] ?></td>
                  <td><?php echo $totaldata[$i]['Gross'] ?></td>
                  <td><?php echo $totaldata[$i]['Tare'] ?></td>
                  <td><?php echo $totaldata[$i]['Netto'] ?></td>
                  <td><?php echo $totaldata[$i]['Coal'] ?></td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
